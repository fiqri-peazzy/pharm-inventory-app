<?php

namespace App\Livewire\Clinical;

use App\Models\WardRequest;
use App\Models\Warehouse;
use App\Models\ServiceUnit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockCard;
use App\Models\ItemBatch;

class WardRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $warehouse_id = ''; // Source Pharmacy

    // Modal states
    public $showDetailModal = false;
    public $showConfirmModal = false;
    public $selectedRequest = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function processRequest($id)
    {
        $this->selectedRequest = WardRequest::with(['details.item', 'serviceUnit', 'warehouse', 'requestedBy'])->findOrFail($id);
        $this->showConfirmModal = true;
    }

    public function confirmFulfillment()
    {
        if (!$this->selectedRequest) return;

        try {
            DB::transaction(function () {
                $fulfilledCount = 0;
                foreach ($this->selectedRequest->details as $detail) {
                    $qtyToFulfill = $detail->qty_requested;
                    
                    // Find batches in source warehouse using FEFO
                    $batches = ItemBatch::where('item_id', $detail->item_id)
                        ->where('warehouse_id', $this->selectedRequest->warehouse_id)
                        ->where('is_active', true)
                        ->where('current_qty', '>', 0)
                        ->where('expired_date', '>', now())
                        ->orderBy('expired_date', 'asc')
                        ->get();

                    $totalAvailable = $batches->sum('current_qty');
                    $actualFulfilled = min($qtyToFulfill, $totalAvailable);

                    if ($actualFulfilled <= 0) continue;
                    
                    $fulfilledCount++;
                    $remaining = $actualFulfilled;
                    foreach ($batches as $batch) {
                        if ($remaining <= 0) break;

                        $deduct = min($batch->current_qty, $remaining);
                        // Use the model instance to ensure decrement works
                        $batchModel = ItemBatch::find($batch->id);
                        $batchModel->decrement('current_qty', $deduct);

                        // Create Stock Card
                        StockCard::create([
                            'item_id' => $detail->item_id,
                            'warehouse_id' => $this->selectedRequest->warehouse_id,
                            'item_batch_id' => $batch->id,
                            'qty_in' => 0,
                            'qty_out' => $deduct,
                            'last_stock' => ItemBatch::where('item_id', $detail->item_id)
                                ->where('warehouse_id', $this->selectedRequest->warehouse_id)
                                ->sum('current_qty'),
                            'transaction_type' => 'distribution_out',
                            'reference_type' => WardRequest::class,
                            'reference_id' => $this->selectedRequest->id,
                            'transaction_date' => now(),
                            'notes' => "Pemenuhan permintaan unit " . $this->selectedRequest->serviceUnit->name,
                        ]);

                        $remaining -= $deduct;
                    }

                    $detail->update([
                        'qty_fulfilled' => $actualFulfilled
                    ]);
                }

                if ($fulfilledCount > 0) {
                    $this->selectedRequest->update([
                        'status' => 'fulfilled',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);
                } else {
                    throw new \Exception("Tidak ada stok yang tersedia untuk semua item di Depo Farmasi asal.");
                }
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Permintaan berhasil diproses dan stok telah dipotong otomatis.']);
            $this->closeModals();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal memproses permintaan: ' . $e->getMessage()]);
        }
    }

    public function viewDetails($id)
    {
        $this->selectedRequest = WardRequest::with(['details.item', 'serviceUnit', 'warehouse', 'requestedBy'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeModals()
    {
        $this->showDetailModal = false;
        $this->showConfirmModal = false;
        $this->selectedRequest = null;
    }

    public function editRequest($id)
    {
        return redirect()->route('clinical.ward-requests.edit', $id);
    }

    public function render()
    {
        $query = WardRequest::with(['serviceUnit', 'warehouse', 'requestedBy'])
            ->when($this->search, function ($q) {
                $q->where('request_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->warehouse_id, fn($q) => $q->where('warehouse_id', $this->warehouse_id))
            ->latest();

        return view('livewire.clinical.ward-request-index', [
            'requests' => $query->paginate(10),
            'pharmacies' => Warehouse::whereIn('type', ['gudang_utama', 'depo_farmasi', 'depo_igd'])->get()
        ]);
    }
}
