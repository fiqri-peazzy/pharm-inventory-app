<?php

namespace App\Livewire\Inventory;

use App\Models\Disposal;
use App\Models\DisposalDetail;
use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockOpname;
use App\Models\StockCard;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DisposalForm extends Component
{
    public $disposalId;
    public $isEdit = false;
    public $isViewOnly = false;

    // Header
    public $disposal_number;
    public $warehouse_id;
    public $type = 'disposal'; // disposal, return_to_supplier
    public $disposal_date;
    public $notes;
    public $status = 'draft';
    
    // Execution Details (BA)
    public $ba_number;
    public $method; // Incineration, Buried, etc.
    public $location;
    public $witness_1;
    public $witness_2;
    public $witness_3;

    // Items
    public $rows = []; // {item_id, item_name, item_code, item_batch_id, batch_number, expiry_date, available_qty, qty, reason}

    // Search state
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
        
        // Handle View Mode (?view=1)
        if (request()->query('view') == 1) {
            $this->isViewOnly = true;
        }

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
        $date = date('Ymd');
        $count = Disposal::whereYear('created_at', now()->year)->count();
        $this->disposal_number = "$prefix/$date/" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function updatedType()
    {
        $this->generateDisposalNumber();
    }

    public function loadDisposal()
    {
        $d = Disposal::with('details.item', 'details.batch')->findOrFail($this->disposalId);
        
        $this->status = $d->status;
        if ($this->status !== 'draft') {
            $this->isViewOnly = true;
        }

        $this->disposal_number = $d->disposal_number;
        $this->warehouse_id = $d->warehouse_id;
        $this->type = $d->type;
        $this->disposal_date = $d->disposal_date->format('Y-m-d');
        $this->notes = $d->notes;
        $this->ba_number = $d->ba_number;
        $this->method = $d->method;
        $this->location = $d->location;
        $this->witness_1 = $d->witness_1;
        $this->witness_2 = $d->witness_2;
        $this->witness_3 = $d->witness_3;

        foreach ($d->details as $detail) {
            $this->rows[] = [
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'item_code' => $detail->item->code,
                'item_batch_id' => $detail->item_batch_id,
                'batch_number' => $detail->batch->batch_number,
                'expiry_date' => $detail->batch->expired_date->format('d/m/Y'),
                'available_qty' => $detail->batch->current_qty,
                'qty' => $detail->qty,
                'reason' => $detail->reason,
            ];
        }
    }

    // --- INTERCONNECTION: Smart Load ---

    public function loadExpiredItems()
    {
        if (!$this->warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang terlebih dahulu.']);
            return;
        }

        $expiredBatches = ItemBatch::with('item')
            ->where('warehouse_id', $this->warehouse_id)
            ->where('expired_date', '<=', now())
            ->where('current_qty', '>', 0)
            ->get();

        if ($expiredBatches->isEmpty()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Tidak ada barang kadaluarsa ditemukan di gudang ini.']);
            return;
        }

        $addedCount = 0;
        foreach ($expiredBatches as $batch) {
            if ($this->addBatchToRows($batch, 'Kadaluarsa')) {
                $addedCount++;
            }
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => "$addedCount barang kadaluarsa berhasil ditarik."]);
    }

    public function loadDamagedFromAdjustments()
    {
        if (!$this->warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang terlebih dahulu.']);
            return;
        }

        // Get posted adjustments with "Barang Rusak" category in this warehouse
        $damagedDetails = StockAdjustmentDetail::whereHas('adjustment', function($q) {
            $q->where('warehouse_id', $this->warehouse_id)
              ->where('status', 'posted')
              ->where('reason_category', 'Damaged Item');
        })->with('item', 'batch')->get();

        if ($damagedDetails->isEmpty()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Tidak ada history adjustment "Barang Rusak" di gudang ini.']);
            return;
        }

        $addedCount = 0;
        foreach ($damagedDetails as $detail) {
            // Check if quantity is still > 0
            if ($detail->batch->current_qty > 0) {
                if ($this->addBatchToRows($detail->batch, 'Rusak (dari Adjustment)')) {
                    $addedCount++;
                }
            }
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => "$addedCount barang rusak berhasil ditarik."]);
    }

    private function addBatchToRows($batch, $reason)
    {
        // Check duplicate
        foreach ($this->rows as $row) {
            if ($row['item_batch_id'] == $batch->id) {
                return false;
            }
        }

        $this->rows[] = [
            'item_id' => $batch->item_id,
            'item_name' => $batch->item->name,
            'item_code' => $batch->item->code,
            'item_batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'expiry_date' => $batch->expired_date->format('d/m/Y'),
            'available_qty' => $batch->current_qty,
            'qty' => $batch->current_qty,
            'reason' => $reason,
        ];

        return true;
    }

    // --- SEARCH LOGIC ---

    public function updatedItemSearch()
    {
        if (strlen($this->itemSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Item::where('name', 'like', '%' . $this->itemSearch . '%')
            ->orWhere('code', 'like', '%' . $this->itemSearch . '%')
            ->active()
            ->limit(10)
            ->get();
    }

    public function selectItem($itemId)
    {
        $this->selectedItemForBatch = Item::find($itemId);
        $this->itemBatches = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $this->warehouse_id)
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->get();
    }

    public function addBatchRow($batchId)
    {
        $batch = ItemBatch::with('item')->findOrFail($batchId);
        if ($this->addBatchToRows($batch, 'Manual')) {
             $this->dispatch('notify', ['type' => 'success', 'message' => 'Barang ditambahkan.']);
        } else {
             $this->dispatch('notify', ['type' => 'warning', 'message' => 'Batch sudah ada.']);
        }

        $this->itemSearch = '';
        $this->searchResults = [];
        $this->selectedItemForBatch = null;
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    // --- ACTIONS ---

    public function saveDraft()
    {
        $this->save('draft');
    }

    public function submitForReview()
    {
        $this->save('submitted');
    }

    public function approve()
    {
        if (!auth()->user()->can('disposals.approve')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        try {
            DB::beginTransaction();

            $disposal = Disposal::with('details')->findOrFail($this->disposalId);
            
            foreach ($disposal->details as $detail) {
                $batch = ItemBatch::findOrFail($detail->item_batch_id);
                
                if ($batch->current_qty < $detail->qty) {
                    throw new \Exception("Stok tidak cukup untuk item: {$detail->item->name}");
                }

                $batch->decrement('current_qty', $detail->qty);

                StockCard::create([
                    'item_id' => $detail->item_id,
                    'item_batch_id' => $detail->item_batch_id,
                    'warehouse_id' => $disposal->warehouse_id,
                    'transaction_type' => $disposal->type === 'disposal' ? 'disposal' : 'return_to_supplier',
                    'transaction_date' => now(),
                    'reference_type' => Disposal::class,
                    'reference_id' => $disposal->id,
                    'qty_in' => 0,
                    'qty_out' => $detail->qty,
                    'last_stock' => $batch->current_qty,
                    'notes' => strtoupper($disposal->type) . " - " . $disposal->disposal_number . " ({$detail->reason})",
                ]);
            }

            $disposal->update([
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            DB::commit();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data berhasil disetujui & stok diperbarui.']);
            return redirect()->route('inventory.disposals.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function reject()
    {
        $disposal = Disposal::findOrFail($this->disposalId);
        $disposal->update(['status' => 'draft']);
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Dikembalikan ke draft.']);
        return redirect()->route('inventory.disposals.index');
    }

    private function save($status)
    {
        $this->validate();

        if (empty($this->rows)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Daftar item kosong.']);
            return;
        }

        try {
            DB::beginTransaction();

            $data = [
                'disposal_number' => $this->disposal_number,
                'warehouse_id' => $this->warehouse_id,
                'type' => $this->type,
                'disposal_date' => $this->disposal_date,
                'notes' => $this->notes,
                'status' => $status,
                'ba_number' => $this->ba_number,
                'method' => $this->method,
                'location' => $this->location,
                'witness_1' => $this->witness_1,
                'witness_2' => $this->witness_2,
                'witness_3' => $this->witness_3,
            ];

            if ($this->isEdit) {
                $disposal = Disposal::findOrFail($this->disposalId);
                $disposal->update($data);
                $disposal->details()->delete();
            } else {
                $data['created_by'] = Auth::id();
                $disposal = Disposal::create($data);
            }

            foreach ($this->rows as $row) {
                $disposal->details()->create([
                    'item_id' => $row['item_id'],
                    'item_batch_id' => $row['item_batch_id'],
                    'qty' => $row['qty'],
                    'reason' => $row['reason'],
                ]);
            }

            DB::commit();
            $msg = $status === 'draft' ? 'Draft berhasil disimpan.' : 'Berhasil diajukan.';
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
            return redirect()->route('inventory.disposals.index');
        } catch (\Exception $e) {
            DB::rollBack();
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
