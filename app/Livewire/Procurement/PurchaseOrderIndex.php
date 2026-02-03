<?php

namespace App\Livewire\Procurement;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $supplier_id = '';
    public $warehouse_id = '';

    protected $queryString = ['search', 'status', 'supplier_id', 'warehouse_id'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])
            ->latest();

        if ($this->search) {
            $query->where('po_number', 'like', '%' . $this->search . '%');
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->supplier_id) {
            $query->where('supplier_id', $this->supplier_id);
        }

        if ($this->warehouse_id) {
            $query->where('warehouse_id', $this->warehouse_id);
        }

        return view('livewire.procurement.purchase-order-index', [
            'orders' => $query->paginate(10)
        ]);
    }

    public function cancelOrder($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        
        if ($po->status === 'completed') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'PO yang sudah Completed tidak bisa dibatalkan.']);
            return;
        }

        $po->update(['status' => 'cancelled']);
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Purchase Order telah dibatalkan.']);
    }
}
