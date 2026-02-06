<?php

namespace App\Livewire\Procurement;

use App\Models\PurchaseRequest;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $showDeleteModal = false;
    public $selectedId;
    
    // Multi-select
    public $selectedPRs = [];
    public $selectAll = false;

    protected $queryString = ['search', 'status'];
    
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPRs = PurchaseRequest::where('status', 'approved')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedPRs = [];
        }
    }

    public function combineSelected()
    {
        if (empty($this->selectedPRs)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih minimal satu PR.']);
            return;
        }

        $prs = PurchaseRequest::whereIn('id', $this->selectedPRs)->get();

        // Validate all status are approved
        if ($prs->where('status', '!=', 'approved')->count() > 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya PR berstatus Approved yang dapat digabung.']);
            return;
        }

        // Validate supplier is the same
        $supplierIds = $prs->pluck('supplier_id')->unique();
        if ($supplierIds->count() > 1) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya PR dengan supplier yang sama yang dapat digabung.']);
            return;
        }

        if ($supplierIds->first() === null) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'PR yang dipilih belum memiliki supplier. Silakan tentukan supplier di edit PR terlebih dahulu.']);
            return;
        }

        // Redirect to PO Create with PR IDs
        return redirect()->route('procurement.orders.create', [
            'from_prs' => implode(',', $this->selectedPRs)
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PurchaseRequest::with(['warehouse', 'creator'])
            ->latest();

        if ($this->search) {
            $query->where('request_number', 'like', '%' . $this->search . '%');
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.procurement.purchase-request-index', [
            'requests' => $query->paginate(10)
        ]);
    }

    public function delete($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        
        if ($pr->status !== 'draft') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya PR berstatus Draft yang dapat dihapus.']);
            return;
        }

        $pr->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Purchase Request berhasil dihapus.']);
    }

    public function approve($id)
    {
        if (!auth()->user()->hasPermissionTo('purchase-requests.approve')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki otoritas untuk menyetujui PR.']);
            return;
        }

        $pr = PurchaseRequest::findOrFail($id);
        if ($pr->status !== 'submitted') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya PR berstatus Submitted yang dapat disetujui.']);
            return;
        }

        $pr->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Purchase Request telah disetujui.']);
    }

    public function reject($id, $reason)
    {
        if (!auth()->user()->hasPermissionTo('purchase-requests.approve')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki otoritas untuk menolak PR.']);
            return;
        }

        $pr = PurchaseRequest::findOrFail($id);
        $pr->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Purchase Request telah ditolak.']);
    }
}
