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

    protected $updatesQueryString = ['search', 'status'];

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
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Hanya PR berstatus Draft yang dapat dihapus.'
            ]);
            return;
        }

        $pr->delete();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Purchase Request berhasil dihapus.'
        ]);
    }
}
