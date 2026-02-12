<?php

namespace App\Livewire\Inventory;

use App\Models\StockCard;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Warehouse;
use App\Models\ItemBatch;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameForm extends Component
{
    public $opnameId;
    public $isEdit = false;
    public $isViewOnly = false;

    // Header
    public $opname_number;
    public $warehouse_id;
    public $opname_date;
    public $type = 'full';
    public $pic_id;
    public $notes;
    public $status = 'draft';

    // Items
    public $rows = []; // {batch_id, item_name, batch_number, system_qty, physical_qty, difference, notes}

    public function mount($opnameId = null)
    {
        $this->opname_date = date('Y-m-d');
        $this->pic_id = Auth::id();
        
        // Check for read-only view parameter
        $this->isViewOnly = request()->query('view') == 1;

        if ($opnameId) {
            $this->opnameId = $opnameId;
            $this->isEdit = true;
            $this->loadOpname();
        } else {
            $this->generateNumber();
        }
    }

    public function generateNumber()
    {
        $date = date('Y/m');
        $count = StockOpname::where('opname_number', 'like', "OPN/$date/%")->count() + 1;
        $this->opname_number = "OPN/$date/" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function loadOpname()
    {
        $opname = StockOpname::with('details.item', 'details.batch')->findOrFail($this->opnameId);
        $this->opname_number = $opname->opname_number;
        $this->warehouse_id = $opname->warehouse_id;
        $this->opname_date = $opname->opname_date->format('Y-m-d');
        $this->type = $opname->type;
        $this->pic_id = $opname->pic_id;
        $this->notes = $opname->notes;
        $this->status = $opname->status;

        foreach ($opname->details as $detail) {
            $this->rows[] = [
                'batch_id' => $detail->item_batch_id,
                'item_name' => $detail->item->name,
                'batch_number' => $detail->batch->batch_number,
                'system_qty' => $detail->system_qty,
                'physical_qty' => $detail->physical_qty,
                'difference' => $detail->difference,
                'notes' => $detail->notes,
            ];
        }
    }

    public function loadItems()
    {
        if (!$this->warehouse_id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih gudang terlebih dahulu.']);
            return;
        }

        $batches = ItemBatch::with('item')
            ->where('warehouse_id', $this->warehouse_id)
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->get();

        $this->rows = [];
        foreach ($batches as $batch) {
            $this->rows[] = [
                'batch_id' => $batch->id,
                'item_name' => $batch->item->name,
                'batch_number' => $batch->batch_number,
                'system_qty' => $batch->current_qty,
                'physical_qty' => null,
                'difference' => null,
                'notes' => '',
            ];
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => count($batches) . ' item berhasil dimuat.']);
    }

    public function updatePhysical($index)
    {
        $row = $this->rows[$index];
        if ($row['physical_qty'] !== null && $row['physical_qty'] !== '') {
            $this->rows[$index]['difference'] = (float)$row['physical_qty'] - (float)$row['system_qty'];
        } else {
            $this->rows[$index]['difference'] = null;
        }
    }

    public function save()
    {
        $this->saveToDb('draft');
        session()->flash('notify', ['type' => 'success', 'message' => 'Stock Opname berhasil disimpan sebagai Draft.']);
        return redirect()->route('inventory.stock-opnames.index');
    }

    public function submitForReview()
    {
        // Validate all items have physical_qty
        foreach ($this->rows as $index => $row) {
            if ($row['physical_qty'] === null || $row['physical_qty'] === '') {
                $this->dispatch('notify', ['type' => 'error', 'message' => "Item pada baris " . ($index + 1) . " belum diisi jumlah fisiknya."]);
                return;
            }
        }

        $this->saveToDb('submitted');
        session()->flash('notify', ['type' => 'success', 'message' => 'Stock Opname berhasil diajukan untuk direview.']);
        return redirect()->route('inventory.stock-opnames.index');
    }

    public function approve()
    {
        if (!Auth::user()->can('stock-opnames.approve')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menyetujui opname.']);
            return;
        }

        try {
            DB::transaction(function () {
                $opname = StockOpname::findOrFail($this->opnameId);
                
                // 1. Update Status & Approver
                $opname->update([
                    'status' => 'posted', // We go straight to posted if simple, or 'approved' then 'posted'
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                // 2. Post to Inventory (Stock Card & ItemBatch)
                foreach ($opname->details as $detail) {
                    // Only post if there is a difference
                    if ($detail->difference != 0) {
                        $batch = ItemBatch::findOrFail($detail->item_batch_id);
                        
                        // Actual adjustment
                        $batch->update([
                            'current_qty' => $detail->physical_qty
                        ]);

                        // Create Stock Card
                        StockCard::create([
                            'item_id' => $detail->item_id,
                            'warehouse_id' => $opname->warehouse_id,
                            'item_batch_id' => $detail->item_batch_id,
                            'transaction_date' => now(),
                            'transaction_type' => 'stock_opname',
                            'reference_type' => StockOpname::class,
                            'reference_id' => $opname->id,
                            'qty_in' => $detail->difference > 0 ? $detail->difference : 0,
                            'qty_out' => $detail->difference < 0 ? abs($detail->difference) : 0,
                            'last_stock' => $detail->physical_qty,
                            'notes' => 'Stock Opname Adjustment: ' . ($detail->notes ?: 'No notes'),
                        ]);
                    }
                }
            });

            session()->flash('notify', ['type' => 'success', 'message' => 'Stock Opname berhasil disetujui dan stok telah diperbarui.']);
            return redirect()->route('inventory.stock-opnames.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal approve: ' . $e->getMessage()]);
        }
    }

    private function saveToDb($targetStatus)
    {
        $this->validate([
            'warehouse_id' => 'required',
            'opname_date' => 'required|date',
            'pic_id' => 'required',
        ]);

        DB::transaction(function () use ($targetStatus) {
            $opname = StockOpname::updateOrCreate(['id' => $this->opnameId], [
                'opname_number' => $this->opname_number,
                'warehouse_id' => $this->warehouse_id,
                'opname_date' => $this->opname_date,
                'type' => $this->type,
                'pic_id' => $this->pic_id,
                'status' => $targetStatus,
                'notes' => $this->notes,
                'created_by' => $this->isEdit ? StockOpname::find($this->opnameId)->created_by : Auth::id(),
            ]);

            $opname->details()->delete();
            foreach ($this->rows as $row) {
                $opname->details()->create([
                    'item_id' => ItemBatch::find($row['batch_id'])->item_id,
                    'item_batch_id' => $row['batch_id'],
                    'system_qty' => $row['system_qty'],
                    'physical_qty' => $row['physical_qty'],
                    'difference' => $row['difference'],
                    'notes' => $row['notes'],
                ]);
            }
        });
    }

    public function render()
    {
        return view('livewire.inventory.stock-opname-form', [
            'warehouses' => Warehouse::all(),
            'employees' => \App\Models\User::all(),
        ]);
    }
}
