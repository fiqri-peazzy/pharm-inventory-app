<?php

namespace App\Livewire\Inventory;

use App\Models\Item;
use App\Models\StockCard;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class StockCardIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouseId;
    public $itemId;
    public $startDate;
    public $endDate;
    public $transactionType = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        // RBAC: Restricted warehouse access
        if (!auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'direktur', 'bupati', 'auditor'])) {
            $this->warehouseId = auth()->user()->warehouse_id;
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'warehouseId', 'itemId', 'startDate', 'endDate', 'transactionType'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = StockCard::with(['item', 'warehouse', 'batch', 'reference'])
            ->when($this->search, function ($q) {
                $q->whereHas('item', function ($iq) {
                    $iq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->when($this->itemId, fn($q) => $q->where('item_id', $this->itemId))
            ->when($this->transactionType, fn($q) => $q->where('transaction_type', $this->transactionType))
            ->when($this->startDate, fn($q) => $q->whereDate('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('transaction_date', '<=', $this->endDate))
            ->where(function ($q) {
                $q->where('transaction_type', '!=', 'stock_opname')
                  ->orWhere('qty_in', '>', 0)
                  ->orWhere('qty_out', '>', 0);
            })
            ->orderBy('id', 'desc');

        return view('livewire.inventory.stock-card-index', [
            'stockCards' => $query->paginate(20),
            'warehouses' => Warehouse::all(),
            'items' => Item::active()->orderBy('name')->take(100)->get(), // Limited for performance, better use search
        ]);
    }
}
