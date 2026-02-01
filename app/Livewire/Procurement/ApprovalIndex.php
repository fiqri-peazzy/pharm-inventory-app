<?php

namespace App\Livewire\Procurement;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $selected_request;
    public $details = []; // Array of {id, item_name, requested_qty, approved_qty}

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function show($id)
    {
        $this->selected_request = PurchaseRequest::with(['warehouse', 'details.item', 'creator'])->findOrFail($id);
        $this->details = [];
        foreach ($this->selected_request->details as $detail) {
            $this->details[] = [
                'id' => $detail->id,
                'item_name' => $detail->item->name,
                'requested_qty' => $detail->requested_qty,
                'approved_qty' => $detail->approved_qty ?? $detail->requested_qty,
            ];
        }
        $this->isOpen = true;
    }

    public function approve()
    {
        foreach ($this->details as $item) {
            PurchaseRequestDetail::find($item['id'])->update([
                'approved_qty' => $item['approved_qty'],
            ]);
        }

        $this->selected_request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Permintaan Barang disetujui']);
        $this->closeModal();
    }

    public function reject()
    {
        $this->selected_request->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Permintaan Barang ditolak']);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->selected_request = null;
        $this->details = [];
    }

    public function render()
    {
        $requests = PurchaseRequest::with(['warehouse', 'creator'])
            ->where('status', 'pending')
            ->where('request_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.procurement.approval-index', [
            'requests' => $requests,
        ]);
    }
}
