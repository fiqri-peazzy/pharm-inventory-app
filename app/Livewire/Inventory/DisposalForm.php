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
    public $disposal_type; // Expired, Damaged, Lost, etc.
    public $method; // Incineration, Buried, etc.
    public $disposal_method; // redundant but in migration? using 'method' as primary
    public $ba_number;
    public $location;
    public $disposal_date;
    public $execution_date;
    public $notes;
    public $status = 'draft';
    public $total_value = 0;

    // Witnesses (Mini-form)
    public $witnesses = []; // {name, role}
    public $new_witness_name;
    public $new_witness_role;

    // Evidence
    public $evidences = []; // {file, type, notes}

    // Items
    public $rows = []; // {item_id, item_name, item_code, item_batch_id, batch_number, expiry_date, available_qty, qty, unit_price, total_value, reason}

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
            'disposal_type' => 'required',
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
        
        $this->rows = [];
        $this->witnesses = [];
        
        $this->status = $d->status;
        if ($this->status !== 'draft') {
            $this->isViewOnly = true;
        }

        $this->disposal_number = $d->disposal_number;
        $this->warehouse_id = $d->warehouse_id;
        $this->type = $d->type;
        $this->disposal_type = $d->disposal_type;
        $this->method = $d->method;
        $this->location = $d->location;
        $this->disposal_date = $d->disposal_date->format('Y-m-d');
        $this->execution_date = $d->execution_date ? $d->execution_date->format('Y-m-d') : null;
        $this->notes = $d->notes;
        $this->ba_number = $d->ba_number;
        $this->total_value = $d->total_value;

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
                'unit_price' => $detail->batch->purchase_price,
                'total_value' => $detail->qty * $detail->batch->purchase_price,
                'reason' => $detail->reason,
                'source_type' => $detail->source_type,
                'source_id' => $detail->source_id,
            ];
        }

        foreach ($d->witnesses as $w) {
            $this->witnesses[] = [
                'name' => $w->name,
                'role' => $w->role,
            ];
        }
    }

    public function calculateTotal()
    {
        $this->total_value = collect($this->rows)->sum('total_value');
    }

    public function addWitness()
    {
        $this->validate([
            'new_witness_name' => 'required',
            'new_witness_role' => 'required',
        ]);

        $this->witnesses[] = [
            'name' => $this->new_witness_name,
            'role' => $this->new_witness_role,
        ];

        $this->new_witness_name = '';
        $this->new_witness_role = '';
    }

    public function removeWitness($index)
    {
        unset($this->witnesses[$index]);
        $this->witnesses = array_values($this->witnesses);
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

        // Get IDs that are already used in disposals OR returns
        $usedAdjustmentIds = \App\Models\DisposalDetail::where('source_type', 'adjustment')
            ->pluck('source_id')
            ->toArray();
        
        $usedInReturns = \App\Models\ReturnDetail::where('source_type', 'adjustment')
            ->pluck('source_id')
            ->toArray();
            
        $allUsedIds = array_unique(array_merge($usedAdjustmentIds, $usedInReturns));

        // Get posted adjustments with negative qty (minus) in this warehouse
        $damagedDetails = StockAdjustmentDetail::whereHas('adjustment', function($q) {
            $q->where('warehouse_id', $this->warehouse_id)
              ->where('status', 'posted');
        })
        ->where('difference', '<', 0)
        ->whereNotIn('id', $allUsedIds)
        ->with('item', 'batch')->get();

        if ($damagedDetails->isEmpty()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Tidak ada history adjustment (Minus) di gudang ini.']);
            return;
        }

        $addedCount = 0;
        foreach ($damagedDetails as $detail) {
            // Pull the exact quantity that was adjusted out (absolute value)
            $qtyToDispose = abs($detail->difference);
            
            if ($this->addBatchToRows($detail->batch, "Penyesuaian (ADJ-{$detail->id})", $qtyToDispose, 'adjustment', $detail->id)) {
                $addedCount++;
            }
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => "$addedCount barang dari Adjustment berhasil ditarik."]);
    }

    public function loadDamagedFromOpname()
    {
        if (!$this->warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang terlebih dahulu.']);
            return;
        }

        // Get posted opname details with negative difference
        // Get IDs that are already used in disposals OR returns
        $usedOpnameIds = \App\Models\DisposalDetail::where('source_type', 'opname')
            ->pluck('source_id')
            ->toArray();
            
        $usedInReturns = \App\Models\ReturnDetail::where('source_type', 'opname')
            ->pluck('source_id')
            ->toArray();
            
        $allUsedOpnameIds = array_unique(array_merge($usedOpnameIds, $usedInReturns));

        $opnameDetails = \App\Models\StockOpnameDetail::whereHas('opname', function($q) {
            $q->where('warehouse_id', $this->warehouse_id)
              ->where('status', 'posted');
        })
        ->where('difference', '<', 0)
        ->whereNotIn('id', $allUsedOpnameIds)
        ->with('item', 'batch')->get();

        if ($opnameDetails->isEmpty()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Tidak ada hasil opname (Selisih Kurang) di gudang ini.']);
            return;
        }

        $addedCount = 0;
        foreach ($opnameDetails as $detail) {
            $qtyToDispose = abs($detail->difference);
            
            if ($this->addBatchToRows($detail->batch, "Selisih Opname (SO-{$detail->id})", $qtyToDispose, 'opname', $detail->id)) {
                $addedCount++;
            }
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => "$addedCount barang dari Opname berhasil ditarik."]);
    }

    private function addBatchToRows($batch, $reason, $qty = null, $sourceType = null, $sourceId = null)
    {
        // Check duplicate
        foreach ($this->rows as $row) {
            if ($row['item_batch_id'] == $batch->id && ($row['source_type'] ?? null) == $sourceType && ($row['source_id'] ?? null) == $sourceId) {
                return false;
            }
        }

        $qtyFinal = $qty ?? $batch->current_qty;

        $this->rows[] = [
            'item_id' => $batch->item_id,
            'item_name' => $batch->item->name,
            'item_code' => $batch->item->code,
            'item_batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'expiry_date' => $batch->expired_date->format('d/m/Y'),
            'available_qty' => $batch->current_qty,
            'qty' => $qtyFinal,
            'unit_price' => $batch->purchase_price,
            'total_value' => $qtyFinal * $batch->purchase_price,
            'reason' => $reason,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ];

        $this->calculateTotal();
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
            ->orderBy('expired_date', 'asc')
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
        $this->calculateTotal();
    }

    public function updateQty($index)
    {
        $this->rows[$index]['qty'] = (float) ($this->rows[$index]['qty'] ?: 0);
        $this->rows[$index]['total_value'] = $this->rows[$index]['qty'] * $this->rows[$index]['unit_price'];
        $this->calculateTotal();
    }

    public function updatedRows($value, $key)
    {
        if (str_contains($key, '.qty')) {
            $index = explode('.', $key)[0];
            $this->updateQty($index);
        }
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
        $adjustment = Disposal::findOrFail($this->disposalId);
        $user = Auth::user();

        // Approval Threshold Logic
        $canApprove = false;
        if ($user->hasRole('super-admin') || $user->hasRole('direktur')) {
            $canApprove = true;
        } elseif ($user->hasRole('kepala-farmasi') && $this->total_value <= 10000000) {
            $canApprove = true;
        }

        if (!$canApprove) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki wewenang untuk menyetujui disposal dengan nominal ini.']);
            return;
        }

        try {
            DB::beginTransaction();
            $disposal = Disposal::findOrFail($this->disposalId);
            $disposal->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            DB::commit();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Disposal disetujui.']);
            $this->loadDisposal();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function markExecuted()
    {
        // Implementation for execution phase
        $disposal = Disposal::findOrFail($this->disposalId);
        $disposal->update([
            'status' => 'executed',
            'executed_by' => Auth::id(),
            'executed_at' => now(),
        ]);
        $this->loadDisposal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Pemusnahan fisik telah selesai.']);
    }

    public function post()
    {
        // final posting, reduce stock
        try {
            DB::beginTransaction();

            $disposal = Disposal::with('details')->findOrFail($this->disposalId);
            
            foreach ($disposal->details as $detail) {
                $batch = ItemBatch::findOrFail($detail->item_batch_id);
                
                // CRITICAL: Only decrement if not from source (Adjustment/Opname)
                // because source transactions already deducted stock.
                if (empty($detail->source_type)) {
                    if ($batch->current_qty < $detail->qty) {
                        throw new \Exception("Stok tidak cukup untuk item: {$detail->item->name}");
                    }
                    $batch->decrement('current_qty', $detail->qty);
                    $qtyOut = $detail->qty;
                    $notes = "DSP - " . $disposal->disposal_number . " ({$detail->reason})";
                } else {
                    // Just a documentary record, stock change is 0
                    $qtyOut = 0;
                    $notes = "DSP DOC - " . $disposal->disposal_number . " (Ref: " . strtoupper($detail->source_type) . " #{$detail->source_id})";
                }

                if ($qtyOut > 0) {
                    StockCard::create([
                        'item_id' => $detail->item_id,
                        'item_batch_id' => $detail->item_batch_id,
                        'warehouse_id' => $disposal->warehouse_id,
                        'transaction_type' => 'disposal',
                        'transaction_date' => now(),
                        'reference_type' => Disposal::class,
                        'reference_id' => $disposal->id,
                        'qty_in' => 0,
                        'qty_out' => $qtyOut,
                        'last_stock' => $batch->current_qty,
                        'notes' => $notes,
                    ]);
                }
            }

            $disposal->update([
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            DB::commit();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data berhasil di-posting & stok diperbarui.']);
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

            $this->calculateTotal();

            $data = [
                'disposal_number' => $this->disposal_number,
                'warehouse_id' => $this->warehouse_id,
                'type' => $this->type,
                'disposal_type' => $this->disposal_type,
                'method' => $this->method,
                'location' => $this->location,
                'disposal_date' => $this->disposal_date,
                'execution_date' => $this->execution_date,
                'total_value' => $this->total_value,
                'notes' => $this->notes,
                'ba_number' => $this->ba_number,
                'status' => $status,
            ];

            if ($this->isEdit) {
                $disposal = Disposal::findOrFail($this->disposalId);
                $disposal->update($data);
                $disposal->details()->delete();
                $disposal->witnesses()->delete();
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
                    'source_type' => $row['source_type'] ?? null,
                    'source_id' => $row['source_id'] ?? null,
                ]);
            }

            foreach ($this->witnesses as $w) {
                $disposal->witnesses()->create([
                    'name' => $w['name'],
                    'role' => $w['role'],
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
