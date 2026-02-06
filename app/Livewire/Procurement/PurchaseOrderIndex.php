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

    public function approve($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $user = auth()->user();

        // 1. Logic for Kepala Farmasi
        if ($po->status === 'submitted' && $user->hasPermissionTo('purchase-orders.approve')) {
            if ($po->grand_total > 10000000) {
                $po->update([
                    'status' => 'pending_director',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);
                $this->dispatch('notify', ['type' => 'info', 'message' => 'PO > 10Jt. Menunggu persetujuan Direktur.']);
            } else {
                $po->update([
                    'status' => 'approved',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Purchase Order telah disetujui.']);
            }
            return;
        }

        // 2. Logic for Direktur
        if ($po->status === 'pending_director' && $user->hasPermissionTo('purchase-orders.direktur-approve')) {
            $po->update([
                'status' => 'approved',
                // Maybe maintain logs for multiple approvals if needed, 
                // but for now we just update status
            ]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Purchase Order telah disetujui oleh Direktur.']);
            return;
        }

        $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki otoritas atau status PO tidak sesuai.']);
    }

    public function reject($id, $reason)
    {
        $po = PurchaseOrder::findOrFail($id);
        $user = auth()->user();

        if (!$user->hasPermissionTo('purchase-orders.approve') && !$user->hasPermissionTo('purchase-orders.direktur-approve')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Otoritas ditolak.']);
            return;
        }

        $po->update([
            'status' => 'rejected',
            'notes' => ($po->notes ? $po->notes . ' | ' : '') . "Ditolak: " . $reason
        ]);

        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Purchase Order telah ditolak.']);
    }
}
