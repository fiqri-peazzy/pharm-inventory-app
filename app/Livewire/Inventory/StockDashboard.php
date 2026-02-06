<?php

namespace App\Livewire\Inventory;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Receiving;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockDashboard extends Component
{
    public $summary = [];
    public $warehouseStock = [];
    public $nearExpiredItems = [];
    public $lowStockItems = [];
    public $recentReceivings = [];

    public function mount()
    {
        $this->loadSummary();
        $this->loadWarehouseStock();
        $this->loadAlerts();
        $this->loadRecentActivity();
    }

    public function loadSummary()
    {
        $totalValue = ItemBatch::where('is_active', true)
            ->where('current_qty', '>', 0)
            ->select(DB::raw('SUM(current_qty * purchase_price) as total_value'))
            ->first()->total_value ?? 0;

        $nearExpiredCount = ItemBatch::where('is_active', true)
            ->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addMonths(6)])
            ->count();

        $lowStockCount = Item::where('is_active', true)
            ->whereHas('batches', function($query) {
                $query->where('is_active', true);
            }, '<', DB::raw('items.min_stock')) // This is a bit tricky in Eloquent
            ->count();
        
        // Let's refine low stock count logic
        $lowStockCount = Item::where('is_active', true)
            ->where('min_stock', '>', 0)
            ->where(function($q) {
                $q->whereRaw('(SELECT SUM(current_qty) FROM item_batches WHERE item_batches.item_id = items.id AND is_active = 1) < min_stock');
            })
            ->count();

        $this->summary = [
            'total_value' => $totalValue,
            'near_expired_count' => $nearExpiredCount,
            'low_stock_count' => $lowStockCount,
            'total_items' => Item::count(),
        ];
    }

    public function loadWarehouseStock()
    {
        $this->warehouseStock = Warehouse::with(['batches' => function($q) {
                $q->where('is_active', true)->where('current_qty', '>', 0);
            }])
            ->get()
            ->map(function($warehouse) {
                return [
                    'name' => $warehouse->name,
                    'total_qty' => $warehouse->batches->sum('current_qty'),
                    'total_value' => $warehouse->batches->sum(fn($b) => $b->current_qty * $b->purchase_price),
                ];
            });
    }

    public function loadAlerts()
    {
        // Near Expired Details (Top 10)
        $this->nearExpiredItems = ItemBatch::with(['item', 'warehouse'])
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addMonths(6)])
            ->orderBy('expired_date', 'asc')
            ->limit(10)
            ->get();

        // Low Stock Details (Top 10)
        $this->lowStockItems = Item::where('is_active', true)
            ->where('min_stock', '>', 0)
            ->where(function($q) {
                $q->whereRaw('(SELECT SUM(current_qty) FROM item_batches WHERE item_batches.item_id = items.id AND is_active = 1) < min_stock');
            })
            ->with(['unit', 'category'])
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->current_total_stock = ItemBatch::where('item_id', $item->id)->where('is_active', true)->sum('current_qty');
                return $item;
            });
    }

    public function loadRecentActivity()
    {
        $this->recentReceivings = Receiving::with(['supplier', 'warehouse'])
            ->where('status', 'posted')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.inventory.stock-dashboard');
    }
}
