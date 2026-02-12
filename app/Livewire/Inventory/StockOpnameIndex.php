<?php

namespace App\Livewire\Inventory;

use App\Models\StockOpname;
use App\Models\Warehouse;
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

        return view('livewire.inventory.stock-opname-index', [
            'opnames' => $query->paginate(10),
            'warehouses' => Warehouse::all()
        ]);
    }
}
