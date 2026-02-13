<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryReturn;
use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\ReturnDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\StockCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReturnNotification;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReturnForm extends Component
{
    use WithFileUploads;

    public $returnId;
    public $isEdit = false;
    public $isViewOnly = false;

    // Header Data
    public $return_number;
    public $type = 'supplier'; // supplier, internal
    public $from_warehouse_id;
    public $to_warehouse_id;
    public $supplier_id;
    public $return_date;
    public $reason_category;
    public $reason;
    public $status = 'draft';
    public $receiving_number;
    public $po_number;
    public $invoice_number;
    public $supplier_do_number;
    public $evidence_file;
    public $existing_evidence;
    public $total_value = 0;
    public $notes;

    // Item Selection
    public $search = '';
    public $searchResults = [];
    public $selectedItem;
    public $itemBatches = [];
    public $selectedBatchId;

    // List of Items in the Return
    public $items = [];

    // Credit Note Management
    public $credit_notes = [];
    public $showCreditNoteForm = false;
    public $editingCreditNoteId = null;
    public $cn_number, $cn_amount, $cn_type = 'credit_memo', $cn_date, $cn_status = 'pending';

    protected function rules()
    {
        $rules = [
            'type' => 'required|in:supplier,internal',
            'from_warehouse_id' => 'required',
            'return_date' => 'required|date',
            'reason_category' => 'required',
            'reason' => 'required|min:5',
            'items' => 'required|array|min:1',
        ];

        if ($this->type === 'supplier') {
            $rules['supplier_id'] = 'required';
        } else {
            $rules['to_warehouse_id'] = 'required|different:from_warehouse_id';
        }

        return $rules;
    }

    public function mount($returnId = null)
    {
        $this->returnId = $returnId;
        $this->return_date = now()->format('Y-m-d');
        
        if (request()->query('view') == 1) {
            $this->isViewOnly = true;
        }

        if ($returnId) {
            $this->isEdit = true;
            $this->loadReturn();
        } else {
            $this->generateReturnNumber();
        }
    }

    public function updatedType()
    {
        $this->generateReturnNumber();
        $this->items = [];
        $this->total_value = 0;
    }

    public function generateReturnNumber()
    {
        $prefix = $this->type === 'supplier' ? 'RTN/SUP/' : 'RTN/INT/';
        $count = InventoryReturn::where('type', $this->type)
            ->whereYear('created_at', now()->year)
            ->count();
        $this->return_number = $prefix . now()->format('Ymd') . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function loadReturn()
    {
        $return = InventoryReturn::with('details.item', 'details.batch')->findOrFail($this->returnId);
        
        $this->items = [];
        $this->credit_notes = [];
        
        $this->return_number = $return->return_number;
        $this->type = $return->type;
        $this->from_warehouse_id = $return->from_warehouse_id;
        $this->to_warehouse_id = $return->to_warehouse_id;
        $this->supplier_id = $return->supplier_id;
        $this->return_date = $return->return_date->format('Y-m-d');
        $this->reason_category = $return->reason_category;
        $this->reason = $return->reason;
        $this->status = $return->status;
        $this->receiving_number = $return->receiving_number;
        $this->po_number = $return->po_number;
        $this->invoice_number = $return->invoice_number;
        $this->supplier_do_number = $return->supplier_do_number;
        $this->existing_evidence = $return->evidence_file;
        $this->total_value = $return->total_value;
        $this->notes = $return->notes;

        // Load Credit Notes
        $this->credit_notes = $return->creditNotes->toArray();

        if ($this->status !== 'draft') {
            $this->isViewOnly = true;
        }

        foreach ($return->details as $detail) {
            $this->items[] = [
                'id' => $detail->id,
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'batch_id' => $detail->item_batch_id,
                'batch_number' => $detail->batch->batch_number,
                'expired_date' => $detail->batch->expired_date->format('d/m/y'),
                'available_qty' => $detail->batch->current_qty,
                'qty' => $detail->qty,
                'price' => $detail->price,
                'total_value' => $detail->total_value,
                'notes' => $detail->notes,
                'source_type' => $detail->source_type,
                'source_id' => $detail->source_id,
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

        if (!$this->from_warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang asal terlebih dahulu!']);
            $this->resetItemSelection();
            return;
        }

        $this->itemBatches = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $this->from_warehouse_id)
            ->where('current_qty', '>', 0)
            ->get();
    }

    public function addItem($batchId = null, $reason = '', $qty = 1, $sourceType = null, $sourceId = null)
    {
        $batchId = $batchId ?? $this->selectedBatchId;
        if (!$batchId) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih batch terlebih dahulu!']);
            return;
        }

        $batch = ItemBatch::with('item')->find($batchId);
        
        foreach ($this->items as $item) {
            if ($item['batch_id'] == $batchId && ($item['source_type'] ?? null) == $sourceType && ($item['source_id'] ?? null) == $sourceId) {
                $this->dispatch('notify', ['type' => 'info', 'message' => 'Batch ini sudah ada di daftar.']);
                return;
            }
        }

        $this->items[] = [
            'item_id' => $batch->item_id,
            'item_name' => $batch->item->name,
            'batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'expired_date' => $batch->expired_date->format('d/m/y'),
            'available_qty' => $batch->current_qty,
            'qty' => $qty,
            'price' => $batch->purchase_price,
            'total_value' => $qty * floatval($batch->purchase_price),
            'notes' => $reason,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ];

        $this->resetItemSelection();
        $this->calculateGrandTotal();
    }

    // --- SMART LOAD ---

    public function loadExpiredItems()
    {
        if (!$this->from_warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang asal terlebih dahulu!']);
            return;
        }

        $expiredBatches = ItemBatch::where('warehouse_id', $this->from_warehouse_id)
            ->where('expired_date', '<=', now())
            ->where('current_qty', '>', 0)
            ->get();

        if ($expiredBatches->isEmpty()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Tidak ada barang kadaluarsa ditemukan.']);
            return;
        }

        $count = 0;
        foreach ($expiredBatches as $batch) {
            $this->addItem($batch->id, 'Expired', $batch->current_qty);
            $count++;
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => "$count item kadaluarsa ditarik."]);
    }

    public function loadDamagedFromAdjustments()
    {
        if (!$this->from_warehouse_id) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih gudang asal terlebih dahulu!']);
            return;
        }

        // Get IDs that are already used in returns OR disposals
        $usedAdjustmentIds = \App\Models\ReturnDetail::where('source_type', 'adjustment')
            ->pluck('source_id')
            ->toArray();
            
        $usedInDisposals = \App\Models\DisposalDetail::where('source_type', 'adjustment')
            ->pluck('source_id')
            ->toArray();
            
        $allUsedAdjustmentIds = array_unique(array_merge($usedAdjustmentIds, $usedInDisposals));

        $damagedDetails = \App\Models\StockAdjustmentDetail::whereHas('adjustment', function($q) {
            $q->where('warehouse_id', $this->from_warehouse_id)
              ->where('status', 'posted');
        })
        ->where('difference', '<', 0)
        ->whereNotIn('id', $allUsedAdjustmentIds)
        ->with('item', 'batch')->get();

        if ($damagedDetails->isEmpty()) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Tidak ada history adjustment (Minus) ditemukan.']);
            return;
        }

        $count = 0;
        foreach ($damagedDetails as $detail) {
            $qtyToReturn = abs($detail->difference);
            $this->addItem($detail->item_batch_id, "ADJ-{$detail->id}", $qtyToReturn, 'adjustment', $detail->id);
            $count++;
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => "$count item dari Adjustment ditarik."]);
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

    public function updateReturnQty($index)
    {
        $this->items[$index]['qty'] = (float) ($this->items[$index]['qty'] ?: 0);
        $this->items[$index]['total_value'] = $this->items[$index]['qty'] * $this->items[$index]['price'];
        $this->calculateGrandTotal();
    }

    public function updatedItems($value, $key)
    {
        if (str_contains($key, '.qty')) {
            $index = explode('.', $key)[0];
            $this->updateReturnQty($index);
        }
    }

    public function updateQty($index)
    {
        $item = &$this->items[$index];
        if ($item['qty'] > $item['available_qty']) {
            $item['qty'] = $item['available_qty'];
        }
        $item['total_value'] = floatval($item['qty']) * floatval($item['price']);
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
                $evidencePath = $this->evidence_file->store('returns/evidence', 'public');
            }

            $data = [
                'type' => $this->type,
                'from_warehouse_id' => $this->from_warehouse_id,
                'to_warehouse_id' => $this->type === 'internal' ? $this->to_warehouse_id : null,
                'supplier_id' => $this->type === 'supplier' ? $this->supplier_id : null,
                'return_date' => $this->return_date,
                'reason_category' => $this->reason_category,
                'reason' => $this->reason,
                'notes' => $this->notes,
                'status' => $status,
                'receiving_number' => $this->receiving_number,
                'po_number' => $this->po_number,
                'invoice_number' => $this->invoice_number,
                'supplier_do_number' => $this->supplier_do_number,
                'evidence_file' => $evidencePath,
                'total_value' => $this->total_value,
            ];

            if ($this->isEdit) {
                $returnDoc = InventoryReturn::findOrFail($this->returnId);
                $returnDoc->update($data);
                $returnDoc->details()->delete();
            } else {
                $data['return_number'] = $this->return_number;
                $data['created_by'] = Auth::id();
                $returnDoc = InventoryReturn::create($data);
            }

            foreach ($this->items as $item) {
                $returnDoc->details()->create([
                    'item_id' => $item['item_id'],
                    'item_batch_id' => $item['batch_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total_value' => $item['total_value'],
                    'notes' => $item['notes'],
                    'source_type' => $item['source_type'] ?? null,
                    'source_id' => $item['source_id'] ?? null,
                ]);
            }

            DB::commit();
            $msg = $status === 'draft' ? 'Draft Retur berhasil disimpan.' : 'Retur berhasil diajukan untuk review.';
            $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
            return redirect()->route('inventory.returns.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Status Transitions for Supplier Return
    public function approve()
    {
        if (!auth()->user()->can('returns.approve')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        try {
            DB::beginTransaction();

            $returnDoc = InventoryReturn::with('details')->findOrFail($this->returnId);

            foreach ($returnDoc->details as $detail) {
                $batch = ItemBatch::findOrFail($detail->item_batch_id);
                
                // CRITICAL: Only decrement if not from source (Adjustment/Opname)
                if (empty($detail->source_type)) {
                    $batch->current_qty -= $detail->qty;
                    $batch->save();
                    $qtyOut = $detail->qty;
                    $notes = "RTN - {$returnDoc->return_number}";
                } else {
                    $qtyOut = 0;
                    $notes = "RTN DOC - {$returnDoc->return_number} (Ref: ".strtoupper($detail->source_type)." #{$detail->source_id})";
                }

                // Stock Card Out
                if ($qtyOut > 0) {
                    StockCard::create([
                        'item_id' => $detail->item_id,
                        'item_batch_id' => $detail->item_batch_id,
                        'warehouse_id' => $returnDoc->from_warehouse_id,
                        'transaction_type' => $returnDoc->type === 'supplier' ? 'return_to_supplier' : 'internal_return_out',
                        'transaction_date' => now(),
                        'reference_type' => InventoryReturn::class,
                        'reference_id' => $returnDoc->id,
                        'qty_in' => 0,
                        'qty_out' => $qtyOut,
                        'last_stock' => $batch->current_qty,
                        'notes' => $notes,
                    ]);
                }

                // If Internal, increment destination
                if ($returnDoc->type === 'internal') {
                    // Find or create batch in destination
                    $destBatch = ItemBatch::where('warehouse_id', $returnDoc->to_warehouse_id)
                        ->where('item_id', $detail->item_id)
                        ->where('batch_number', $batch->batch_number)
                        ->first();

                    if (!$destBatch) {
                        $destBatch = $batch->replicate();
                        $destBatch->warehouse_id = $returnDoc->to_warehouse_id;
                        $destBatch->current_qty = $detail->qty;
                        $destBatch->save();
                    } else {
                        $destBatch->current_qty += $detail->qty;
                        $destBatch->save();
                    }

                    // Stock Card In for destination
                    StockCard::create([
                        'item_id' => $detail->item_id,
                        'item_batch_id' => $destBatch->id,
                        'warehouse_id' => $returnDoc->to_warehouse_id,
                        'transaction_type' => 'internal_return_in',
                        'transaction_date' => now(),
                        'reference_type' => InventoryReturn::class,
                        'reference_id' => $returnDoc->id,
                        'qty_in' => $detail->qty,
                        'qty_out' => 0,
                        'last_stock' => $destBatch->current_qty,
                        'notes' => "RTN - {$returnDoc->return_number}",
                    ]);
                }
            }

            $returnDoc->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Retur disetujui & Stok diperbarui.']);
            $this->loadReturn();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function notifySupplier()
    {
        $return = InventoryReturn::with('supplier', 'details.item', 'details.batch')->findOrFail($this->returnId);
        
        if (!$return->supplier || !$return->supplier->email) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Email supplier tidak ditemukan.']);
            return;
        }

        try {
            Mail::to($return->supplier->email)->send(new ReturnNotification($return));
            
            $return->update(['status' => 'supplier_notified']);
            $this->loadReturn();
            
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Notifikasi telah dikirim ke supplier.']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }

    public function markPickedUp()
    {
        $return = InventoryReturn::findOrFail($this->returnId);
        $return->update(['status' => 'picked_up']);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Barang telah diambil kurir/supplier.']);
        $this->loadReturn();
    }

    public function complete()
    {
        $return = InventoryReturn::findOrFail($this->returnId);
        $return->update(['status' => 'completed']);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Proses retur selesai.']);
        $this->loadReturn();
    }

    public function reject()
    {
        $return = InventoryReturn::findOrFail($this->returnId);
        $return->update(['status' => 'draft']);
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Dikembalikan ke draft.']);
        return redirect()->route('inventory.returns.index');
    }

    // --- Credit Note Logic ---
    public function saveCreditNote()
    {
        $this->validate([
            'cn_number' => 'required',
            'cn_amount' => 'required|numeric|min:0',
            'cn_type' => 'required',
            'cn_date' => 'required|date',
        ]);

        $return = InventoryReturn::findOrFail($this->returnId);
        $return->creditNotes()->create([
            'credit_note_number' => $this->cn_number,
            'amount' => $this->cn_amount,
            'type' => $this->cn_type,
            'note_date' => $this->cn_date,
            'status' => $this->cn_status,
        ]);

        $this->resetCreditNoteForm();
        $this->loadReturn();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Credit Note berhasil ditambahkan.']);
    }

    public function updateCNStatus($id, $status)
    {
        $cn = \App\Models\ReturnCreditNote::findOrFail($id);
        $cn->update(['status' => $status]);
        $this->loadReturn();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status Credit Note diperbarui.']);
    }

    public function deleteCreditNote($id)
    {
        \App\Models\ReturnCreditNote::destroy($id);
        $this->loadReturn();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Credit Note dihapus.']);
    }

    public function resetCreditNoteForm()
    {
        $this->cn_number = '';
        $this->cn_amount = '';
        $this->cn_type = 'credit_memo';
        $this->cn_date = now()->format('Y-m-d');
        $this->cn_status = 'pending';
        $this->showCreditNoteForm = false;
    }

    public function loadFromReceiving()
    {
        if (!$this->receiving_number) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Masukkan nomor receiving terlebih dahulu.']);
            return;
        }

        $receiving = \App\Models\Receiving::with('details.item') // Removed .batch
            ->where('receiving_number', $this->receiving_number)
            ->first();

        if (!$receiving) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Dokumen receiving tidak ditemukan.']);
            return;
        }

        $this->supplier_id = $receiving->supplier_id;
        $this->po_number = $receiving->purchase_order_number;
        $this->invoice_number = $receiving->invoice_number;
        $this->from_warehouse_id = $receiving->warehouse_id;

        $this->items = [];
        foreach ($receiving->details as $detail) {
            // Manually find the batch that corresponds to this receiving detail
            $batch = \App\Models\ItemBatch::where('warehouse_id', $receiving->warehouse_id)
                ->where('item_id', $detail->item_id)
                ->where('batch_number', $detail->batch_number)
                ->where('expired_date', $detail->expired_date)
                ->first();

            // We show the item even if $batch is 0 current_qty, 
            // but we only add it if the batch record actually exists in that warehouse
            if ($batch) {
                $canReturnQty = $batch->current_qty;
                
                $this->items[] = [
                    'item_id' => $detail->item_id,
                    'item_name' => $detail->item->name,
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expired_date' => $batch->expired_date->format('d/m/y'),
                    'available_qty' => $batch->current_qty,
                    'qty' => min($detail->qty_received, $canReturnQty), // Default to received unless stock is lower
                    'price' => $detail->purchase_price,
                    'total_value' => min($detail->qty_received, $canReturnQty) * floatval($detail->purchase_price),
                    'notes' => $batch->current_qty <= 0 ? 'Peringatan: Stok sistem 0' : '',
                ];
            }
        }
        $this->calculateGrandTotal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data dari Receiving berhasil ditarik.']);
    }

    public function render()
    {
        return view('livewire.inventory.return-form', [
            'warehouses' => Warehouse::all(),
            'suppliers' => Supplier::active()->get(),
        ]);
    }
}
