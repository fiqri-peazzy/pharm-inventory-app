<?php

namespace App\Livewire\Inventory;

use App\Models\Disposal;
use App\Models\DisposalDetail;
use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Warehouse;
use App\Models\StockCard;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use DB;

class DisposalForm extends Component
{
    public $disposalId;
    public $isEdit = false;

    // Header
    public $disposal_number;
    public $warehouse_id;
    public $type = 'disposal';
    public $disposal_date;
    public $notes;

    // Items
    public $rows = []; // {item_id, item_name, item_code, batch_id, batch_number, expiry_date, available_qty, qty, reason}

    // Search state
    public $showItemModal = false;
    public $itemSearch = '';
    public $searchResults = [];
    public $selectedItemForBatch;
    public $itemBatches = [];

    protected function rules()
    {
        return [
            'warehouse_id' => 'required',
            'type' => 'required',
            'disposal_date' => 'required|date',
            'rows.*.item_id' => 'required',
            'rows.*.item_batch_id' => 'required',
            'rows.*.qty' => 'required|numeric|min:0.01',
        ];
    }

    public function mount($disposalId = null)
    {
        $this->disposal_date = date('Y-m-d');
        
        if ($disposalId) {
            $this->disposalId = $disposalId;
            $this->isEdit = true;
            $this->loadDisposal();
        } else {
            $this->generateDisposalNumber();
            $mainWh = Warehouse::where('is_main', true)->first();
            if ($mainWh) $this->warehouse_id = $mainWh->id;
        }
    }

    public function generateDisposalNumber()
    {
        $prefix = $this->type === 'disposal' ? 'DSP' : 'RTS';
        $date = date('Y/m');
        $last = Disposal::where('disposal_number', 'like', "$prefix/$date/%")->latest()->first();
        
        $number = 1;
        if ($last) {
            $parts = explode('/', $last->disposal_number);
            $number = (int)end($parts) + 1;
        }
        
        $this->disposal_number = "$prefix/$date/" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function updatedType()
    {
        $this->generateDisposalNumber();
    }

    public function loadDisposal()
    {
        $d = Disposal::with('details.item', 'details.batch')->findOrFail($this->disposalId);
        
        if ($d->status !== 'draft') {
            return redirect()->route('inventory.disposals.index')->with('error', 'Hanya Draft yang dapat diubah.');
        }

        $this->disposal_number = $d->disposal_number;
        $this->warehouse_id = $d->warehouse_id;
        $this->type = $d->type;
        $this->disposal_date = $d->disposal_date->format('Y-m-d');
        $this->notes = $d->notes;

        foreach ($d->details as $detail) {
            $this->rows[] = [
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'item_code' => $detail->item->code,
                'item_batch_id' => $detail->item_batch_id,
                'batch_number' => $detail->batch->batch_number,
                'expiry_date' => $detail->batch->expiry_date->format('d/m/Y'),
                'available_qty' => $detail->batch->current_qty,
                'qty' => $detail->qty,
                'reason' => $detail->reason,
            ];
        }
    }

    public function updatedItemSearch()
    {
        if (strlen($this->itemSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Item::where('name', 'like', '%' . $this->itemSearch . '%')
            ->orWhere('code', 'like', '%' . $this->itemSearch . '%')
            ->limit(10)
            ->get();
    }

    public function selectItem($itemId)
    {
        $this->selectedItemForBatch = Item::find($itemId);
        $this->itemBatches = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $this->warehouse_id)
            ->where('current_qty', '>', 0)
            ->get();
    }

    public function addBatchRow($batchId)
    {
        $batch = ItemBatch::with('item')->findOrFail($batchId);
        
        // Check duplicate batch in rows
        foreach ($this->rows as $row) {
            if ($row['item_batch_id'] == $batchId) {
                $this->dispatch('notify', ['type' => 'warning', 'message' => 'Batch ini sudah ada di daftar.']);
                return;
            }
        }

        $this->rows[] = [
            'item_id' => $batch->item_id,
            'item_name' => $batch->item->name,
            'item_code' => $batch->item->code,
            'item_batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'expiry_date' => $batch->expiry_date->format('d/m/Y'),
            'available_qty' => $batch->current_qty,
            'qty' => 1,
            'reason' => 'Rusak/ED',
        ];

        $this->showItemModal = false;
        $this->itemSearch = '';
        $this->searchResults = [];
        $this->selectedItemForBatch = null;
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function save($status = 'draft')
    {
        try {
            $this->validate();

            if (empty($this->rows)) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Minimal harus ada 1 item.']);
                return;
            }

            DB::transaction(function () use ($status) {
                $data = [
                    'disposal_number' => $this->disposal_number,
                    'warehouse_id' => $this->warehouse_id,
                    'type' => $this->type,
                    'disposal_date' => $this->disposal_date,
                    'notes' => $this->notes,
                    'status' => $status,
                ];

                if (!$this->isEdit) {
                    $data['created_by'] = Auth::id();
                    $disposal = Disposal::create($data);
                } else {
                    $disposal = Disposal::findOrFail($this->disposalId);
                    $disposal->update($data);
                    $disposal->details()->delete();
                }

                foreach ($this->rows as $row) {
                    $disposal->details()->create([
                        'item_id' => $row['item_id'],
                        'item_batch_id' => $row['item_batch_id'],
                        'qty' => $row['qty'],
                        'reason' => $row['reason'],
                    ]);

                    if ($status === 'posted') {
                        // DECREASE STOCK
                        $batch = ItemBatch::findOrFail($row['item_batch_id']);
                        $oldQty = $batch->current_qty;
                        $batch->decrement('current_qty', $row['qty']);

                        // STOCK CARD
                        StockCard::create([
                            'item_id' => $row['item_id'],
                            'warehouse_id' => $this->warehouse_id,
                            'item_batch_id' => $row['item_batch_id'],
                            'transaction_date' => $this->disposal_date,
                            'transaction_type' => $this->type === 'disposal' ? 'disposal' : 'return_to_supplier',
                            'reference_type' => Disposal::class,
                            'reference_id' => $disposal->id,
                            'qty_in' => 0,
                            'qty_out' => $row['qty'],
                            'last_stock' => $oldQty - $row['qty'],
                            'notes' => $row['reason'],
                        ]);
                    }
                }

                if ($status === 'posted') {
                    $disposal->update([
                        'posted_by' => Auth::id(),
                        'posted_at' => now(),
                    ]);
                }
            });

            session()->flash('notify', ['type' => 'success', 'message' => 'Data berhasil disimpan.']);
            return redirect()->route('inventory.disposals.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.inventory.disposal-form', [
            'warehouses' => Warehouse::all()
        ]);
    }
}
