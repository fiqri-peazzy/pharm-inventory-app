<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryReturn;
use App\Models\Warehouse;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';
    public $warehouse_filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('do-delete-return')]
    public function deleteReturn($id)
    {
        if (!auth()->user()->can('returns.delete')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menghapus retur.']);
            return;
        }

        $return = InventoryReturn::findOrFail($id);

        if ($return->status === 'approved' || $return->status === 'completed' || $return->status === 'picked_up') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Dokumen yang sudah diproses tidak dapat dihapus.']);
            return;
        }

        $return->details()->delete();
        $return->delete();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Dokumen Retur berhasil dihapus.']);
    }

    public function render()
    {
        $query = InventoryReturn::with(['fromWarehouse', 'toWarehouse', 'supplier', 'creator'])
            ->when($this->search, function ($q) {
                $q->where('return_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->type_filter, function ($q) {
                $q->where('type', $this->type_filter);
            })
            ->when($this->status_filter, function ($q) {
                $q->where('status', $this->status_filter);
            })
            ->when($this->warehouse_filter, function ($q) {
                $q->where('from_warehouse_id', $this->warehouse_filter);
            })
            ->latest();

        $stats = [
            'draft' => InventoryReturn::where('status', 'draft')->count(),
            'submitted' => InventoryReturn::where('status', 'submitted')->count(),
            'supplier_return' => InventoryReturn::where('type', 'supplier')->count(),
            'internal_return' => InventoryReturn::where('type', 'internal')->count(),
        ];

        return view('livewire.inventory.return-index', [
            'returns' => $query->paginate(10),
            'warehouses' => Warehouse::all(),
            'stats' => $stats
        ]);
    }
}
