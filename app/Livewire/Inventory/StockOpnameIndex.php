<?php

namespace App\Livewire\Inventory;

use App\Models\StockOpname;
use App\Models\Warehouse;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpnameIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouse_filter = '';
    public $status_filter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('do-delete-opname')]
    public function deleteOpname($id)
    {
        if (!auth()->user()->can('stock-opnames.delete')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menghapus opname.']);
            return;
        }

        $opname = StockOpname::findOrFail($id);

        if ($opname->status === 'posted') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Dokumen yang sudah terbit (posted) tidak dapat dihapus.']);
            return;
        }

        $opname->details()->delete();
        $opname->delete();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Dokumen Stock Opname berhasil dihapus.']);
    }

    public function render()
    {
        $query = StockOpname::with(['warehouse', 'pic', 'creator'])
            ->when($this->search, function($q) {
                $q->where('opname_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->warehouse_filter, function($q) {
                $q->where('warehouse_id', $this->warehouse_filter);
            })
            ->when($this->status_filter, function($q) {
                $q->where('status', $this->status_filter);
            })
            ->latest();

        // Calculate Stats
        $stats = [
            'draft' => StockOpname::where('status', 'draft')->count(),
            'submitted' => StockOpname::where('status', 'submitted')->count(),
            'posted' => StockOpname::where('status', 'posted')->count(),
            'significant' => \App\Models\StockOpnameDetail::where('difference', '!=', 0)
                ->whereRaw('ABS(difference) / NULLIF(system_qty, 0) > 0.1') // More than 10% variance
                ->count()
        ];

        return view('livewire.inventory.stock-opname-index', [
            'opnames' => $query->paginate(10),
            'warehouses' => Warehouse::all(),
            'stats' => $stats
        ]);
    }
}
