<?php

namespace App\Livewire\Inventory;

use App\Models\StockAdjustment;
use App\Models\Warehouse;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StockAdjustmentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouse_filter = '';
    public $status_filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('do-delete-adjustment')]
    public function deleteAdjustment($id)
    {
        if (!auth()->user()->can('stock-adjustments.delete')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menghapus penyesuaian.']);
            return;
        }

        $adjustment = StockAdjustment::findOrFail($id);

        if ($adjustment->status === 'posted') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Dokumen yang sudah terbit (posted) tidak dapat dihapus.']);
            return;
        }

        $adjustment->details()->delete();
        $adjustment->delete();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Dokumen Penyesuaian berhasil dihapus.']);
    }

    public function render()
    {
        $query = StockAdjustment::with(['warehouse', 'creator'])
            ->when($this->search, function ($q) {
                $q->where('adjustment_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->warehouse_filter, function ($q) {
                $q->where('warehouse_id', $this->warehouse_filter);
            })
            ->when($this->status_filter, function ($q) {
                $q->where('status', $this->status_filter);
            })
            ->latest();

        $stats = [
            'draft' => StockAdjustment::where('status', 'draft')->count(),
            'submitted' => StockAdjustment::where('status', 'submitted')->count(),
            'posted' => StockAdjustment::where('status', 'posted')->count(),
        ];

        return view('livewire.inventory.stock-adjustment-index', [
            'adjustments' => $query->paginate(10),
            'warehouses' => Warehouse::all(),
            'stats' => $stats
        ]);
    }
}
