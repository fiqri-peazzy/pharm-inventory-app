<?php

namespace App\Livewire\Procurement;

use App\Models\PurchaseRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestApproval extends Component
{
    use WithPagination;

    public $search = '';
    public $showApprovalModal = false;
    public $selectedPr;
    public $rejectionReason = '';

    public function render()
    {
        $query = PurchaseRequest::with(['warehouse', 'creator', 'details.item'])
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->latest();

        if ($this->search) {
            $query->where('request_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.procurement.purchase-request-approval', [
            'requests' => $query->paginate(10),
            'pendingCount' => PurchaseRequest::where('status', 'submitted')->count()
        ]);
    }

    public function selectPr($id)
    {
        $this->selectedPr = PurchaseRequest::with('details.item')->findOrFail($id);
        $this->showApprovalModal = true;
    }

    public function approve()
    {
        if ($this->selectedPr->status !== 'submitted') return;

        $this->selectedPr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->showApprovalModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Purchase Request telah disetujui.']);
    }

    public function reject()
    {
        if ($this->selectedPr->status !== 'submitted') return;

        $this->validate([
            'rejectionReason' => 'required|min:5'
        ]);

        $this->selectedPr->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $this->rejectionReason,
        ]);

        $this->showApprovalModal = false;
        $this->rejectionReason = '';
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Purchase Request ditolak.']);
    }
}
