<?php

namespace App\Livewire\Inventory;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockCard;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class StockAdjustmentForm extends Component
{
    use WithFileUploads;

    public $adjustmentId;
    public $isEdit = false;
    public $isViewOnly = false;

    // Header Data
    public $adjustment_number;
    public $warehouse_id;
    public $adjustment_date;
    public $type = 'plus'; // plus, minus
    public $status = 'draft';
    public $reason_category;
    public $notes;
    public $total_value = 0;
    public $evidence_file;
    public $existing_evidence;
    public $investigation_report;
    public $corrective_action;

    // Item Selection
    public $search = '';
    public $searchResults = [];
    public $selectedItem;
    public $itemBatches = [];
    public $selectedBatchId;

    // List of Adjustment Items
    public $items = [];

    protected function rules()
    {
        return [
            'warehouse_id' => 'required',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:plus,minus',
            'reason_category' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.batch_id' => 'required',
            'items.*.adjusted_qty' => 'required|numeric|min:0',
            'evidence_file' => $this->total_value > 1000000 ? 'required|file|max:2048' : 'nullable|file|max:2048',
            'investigation_report' => $this->total_value > 5000000 ? 'required' : 'nullable',
            'corrective_action' => $this->total_value > 5000000 ? 'required' : 'nullable',
        ];
    }

    public function mount($adjustmentId = null)
    {
        $this->adjustmentId = $adjustmentId;
        $this->adjustment_date = now()->format('Y-m-d');
        
        if (request()->query('view') == 1) {
            $this->isViewOnly = true;
        }

        if ($adjustmentId) {
            $this->isEdit = true;
            $this->loadAdjustment();
        } else {
            $this->adjustment_number = $this->generateAdjustmentNumber();
        }
    }

    public function generateAdjustmentNumber()
    {
        $count = StockAdjustment::whereYear('created_at', now()->year)->count();
        return 'ADJ/' . now()->format('Ymd') . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function loadAdjustment()
    {
        $adjustment = StockAdjustment::with('details.item', 'details.batch')->findOrFail($this->adjustmentId);
        
        $this->adjustment_number = $adjustment->adjustment_number;
        $this->warehouse_id = $adjustment->warehouse_id;
        $this->adjustment_date = $adjustment->adjustment_date->format('Y-m-d');
        $this->type = $adjustment->type;
        $this->status = $adjustment->status;
        $this->reason_category = $adjustment->reason_category;
        $this->notes = $adjustment->notes;
        $this->total_value = $adjustment->total_value;
        $this->existing_evidence = $adjustment->evidence_file;
        $this->investigation_report = $adjustment->investigation_report;
        $this->corrective_action = $adjustment->corrective_action;

        if ($this->status !== 'draft') {
            $this->isViewOnly = true;
        }

        foreach ($adjustment->details as $detail) {
            $this->items[] = [
                'id' => $detail->id,
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'batch_id' => $detail->item_batch_id,
                'batch_number' => $detail->batch->batch_number,
                'expired_date' => $detail->batch->expired_date->format('d/m/y'),
                'system_qty' => $detail->system_qty,
                'adjusted_qty' => $detail->adjusted_qty,
                'difference' => $detail->difference,
                'unit_price' => $detail->unit_price,
                'total_value' => $detail->total_value,
                'notes' => $detail->notes,
            ];
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Item::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->active()
            ->limit(10)
            ->get();
    }

    public function selectItem($itemId)
    {
        $this->selectedItem = Item::find($itemId);
        $this->search = $this->selectedItem->name;
        $this->searchResults = [];

        if (!$this->warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang terlebih dahulu!']);
            $this->resetItemSelection();
            return;
        }

        $this->itemBatches = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $this->warehouse_id)
            ->where('is_active', true)
            ->get();
    }

    public function addItem()
    {
        if (!$this->selectedBatchId) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih batch terlebih dahulu!']);
            return;
        }

        $batch = ItemBatch::find($this->selectedBatchId);
        
        foreach ($this->items as $item) {
            if ($item['batch_id'] == $this->selectedBatchId) {
                $this->dispatch('notify', ['type' => 'info', 'message' => 'Batch ini sudah ada di daftar.']);
                return;
            }
        }

        $this->items[] = [
            'item_id' => $this->selectedItem->id,
            'item_name' => $this->selectedItem->name,
            'batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'expired_date' => $batch->expired_date->format('d/m/y'),
            'system_qty' => $batch->current_qty,
            'adjusted_qty' => $batch->current_qty,
            'difference' => 0,
            'unit_price' => $batch->purchase_price,
            'total_value' => 0,
            'notes' => '',
        ];

        $this->resetItemSelection();
        $this->calculateGrandTotal();
    }

    public function resetItemSelection()
    {
        $this->selectedItem = null;
        $this->search = '';
        $this->searchResults = [];
        $this->itemBatches = [];
        $this->selectedBatchId = null;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateGrandTotal();
    }

    public function updateQty($index)
    {
        $item = &$this->items[$index];
        $item['difference'] = floatval($item['adjusted_qty']) - floatval($item['system_qty']);
        $item['total_value'] = abs($item['difference']) * floatval($item['unit_price']);
        
        $this->calculateGrandTotal();
    }

    public function calculateGrandTotal()
    {
        $this->total_value = collect($this->items)->sum('total_value');
    }

    public function saveDraft()
    {
        $this->save('draft');
    }

    public function submitForReview()
    {
        $this->save('submitted');
    }

    private function save($status)
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $evidencePath = $this->existing_evidence;
            if ($this->evidence_file) {
                $evidencePath = $this->evidence_file->store('adjustments/evidence', 'public');
            }

            $data = [
                'warehouse_id' => $this->warehouse_id,
                'adjustment_date' => $this->adjustment_date,
                'type' => $this->type,
                'reason_category' => $this->reason_category,
                'notes' => $this->notes,
                'status' => $status,
                'total_value' => $this->total_value,
                'evidence_file' => $evidencePath,
                'investigation_report' => $this->investigation_report,
                'corrective_action' => $this->corrective_action,
            ];

            if ($this->isEdit) {
                $adjustment = StockAdjustment::findOrFail($this->adjustmentId);
                $adjustment->update($data);
                $adjustment->details()->delete();
            } else {
                $data['adjustment_number'] = $this->adjustment_number;
                $data['created_by'] = Auth::id();
                $adjustment = StockAdjustment::create($data);
            }

            foreach ($this->items as $item) {
                $adjustment->details()->create([
                    'item_id' => $item['item_id'],
                    'item_batch_id' => $item['batch_id'],
                    'system_qty' => $item['system_qty'],
                    'adjusted_qty' => $item['adjusted_qty'],
                    'difference' => $item['difference'],
                    'unit_price' => $item['unit_price'],
                    'total_value' => $item['total_value'],
                    'notes' => $item['notes'],
                ]);
            }

            DB::commit();
            $msg = $status === 'draft' ? 'Draft berhasil disimpan.' : 'Adjustment berhasil diajukan untuk review.';
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
            return redirect()->route('inventory.adjustments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function approve()
    {
        $adjustment = StockAdjustment::findOrFail($this->adjustmentId);
        $user = Auth::user();

        // Approval Threshold Logic
        $canApprove = false;
        if ($user->hasRole('super-admin') || $user->hasRole('direktur')) {
            $canApprove = true;
        } elseif ($user->hasRole('kepala-farmasi') && $adjustment->total_value <= 5000000) {
            $canApprove = true;
        } elseif ($user->hasRole('kepala-gudang') && $adjustment->total_value <= 500000) {
            $canApprove = true;
        }

        if (!$canApprove) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki wewenang untuk menyetujui adjustment dengan nominal ini.']);
            return;
        }

        try {
            DB::beginTransaction();

            $adjustment->load('details');

            foreach ($adjustment->details as $detail) {
                $batch = ItemBatch::findOrFail($detail->item_batch_id);
                $batch->current_qty = $detail->adjusted_qty;
                $batch->save();

                StockCard::create([
                    'item_id' => $detail->item_id,
                    'item_batch_id' => $detail->item_batch_id,
                    'warehouse_id' => $adjustment->warehouse_id,
                    'transaction_type' => 'stock_adjustment',
                    'transaction_date' => now(),
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'qty_in' => $detail->difference > 0 ? $detail->difference : 0,
                    'qty_out' => $detail->difference < 0 ? abs($detail->difference) : 0,
                    'last_stock' => $batch->current_qty,
                    'notes' => "ADJ - {$adjustment->adjustment_number} ({$adjustment->reason_category})",
                ]);
            }

            $adjustment->update([
                'status' => 'posted',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Adjustment berhasil disetujui & diposting.']);
            return redirect()->route('inventory.adjustments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function reject()
    {
        $adjustment = StockAdjustment::findOrFail($this->adjustmentId);
        $adjustment->update(['status' => 'draft']);
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Dikembalikan ke draft.']);
        return redirect()->route('inventory.adjustments.index');
    }

    public function render()
    {
        return view('livewire.inventory.stock-adjustment-form', [
            'warehouses' => Warehouse::all()
        ]);
    }
}
