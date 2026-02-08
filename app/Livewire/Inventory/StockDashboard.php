<?php

namespace App\Livewire\Inventory;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Receiving;
use App\Models\Warehouse;
use App\Models\ItemWarehouseSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockDashboard extends Component
{
    public $warehouseId;
    public $isGlobal = true;
    public $warehouse;

    public $summary = [];
    public $warehouseStock = [];
    public $nearExpiredItems = [];
    public $lowStockItems = [];
    public $recentActivities = [];

    // Chart Data
    public $distributionChart = [];
    public $trendChart = [];

    public function mount($warehouseId = null)
    {
        $this->warehouseId = $warehouseId;
        $this->isGlobal = empty($warehouseId);
        
        if (!$this->isGlobal) {
            $this->warehouse = Warehouse::find($warehouseId);
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->loadSummary();
        $this->loadAlerts();
        $this->loadRecentActivity();
        
        if ($this->isGlobal) {
            $this->loadWarehouseStock();
        }
        
        $this->loadCharts();
    }

    public function loadSummary()
    {
        $query = ItemBatch::where('is_active', true)->where('current_qty', '>', 0);
        if (!$this->isGlobal) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        $stats = $query->select(
            DB::raw('SUM(current_qty * purchase_price) as total_value'),
            DB::raw('SUM(current_qty) as total_qty'),
            DB::raw('COUNT(DISTINCT item_id) as total_items')
        )->first();

        // Near Expired Count
        $expQuery = ItemBatch::where('is_active', true)->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addMonths(6)]);
        if (!$this->isGlobal) {
            $expQuery->where('warehouse_id', $this->warehouseId);
        }

        // Low Stock Count
        $lowQuery = ItemWarehouseSetting::where('min_stock', '>', 0);
        if (!$this->isGlobal) {
            $lowQuery->where('warehouse_id', $this->warehouseId);
        }
        $lowCount = $lowQuery->where(function($q) {
                $q->whereRaw('(SELECT SUM(current_qty) FROM item_batches WHERE item_batches.item_id = item_warehouse_settings.item_id AND item_batches.warehouse_id = item_warehouse_settings.warehouse_id AND is_active = 1) < min_stock');
            })
            ->count();

        // Barang Kadaluarsa & Mendekati Kadaluarsa
        $this->summary = [
            'total_value' => $stats->total_value ?? 0,
            'total_qty' => $stats->total_qty ?? 0,
            'total_items' => $stats->total_items ?? 0,
            'near_expired_count' => $expQuery->count(),
            'low_stock_count' => $lowCount,
            'expired_count' => ItemBatch::where('is_active', true)->where('current_qty', '>', 0)
                ->where('expired_date', '<', Carbon::now())
                ->when(!$this->isGlobal, fn($q) => $q->where('warehouse_id', $this->warehouseId))
                ->count(),
        ];
    }

    public function loadWarehouseStock()
    {
        $this->warehouseStock = Warehouse::with(['batches' => function($q) {
                $q->where('is_active', true)->where('current_qty', '>', 0);
            }])
            ->get()
            ->map(function($wh) {
                return [
                    'id' => $wh->id,
                    'name' => $wh->name,
                    'total_qty' => $wh->batches->sum('current_qty'),
                    'total_value' => $wh->batches->sum(fn($b) => $b->current_qty * $b->purchase_price),
                    'low_stock_alerts' => ItemWarehouseSetting::where('warehouse_id', $wh->id)
                        ->where('min_stock', '>', 0)
                        ->whereRaw('(SELECT SUM(current_qty) FROM item_batches WHERE item_batches.item_id = item_warehouse_settings.item_id AND item_batches.warehouse_id = item_warehouse_settings.warehouse_id AND is_active = 1) < min_stock')
                        ->count()
                ];
            });
    }

    public function loadAlerts()
    {
        // Detail Barang Hampir Kadaluarsa (Top 15)
        $expQuery = ItemBatch::with(['item', 'warehouse'])
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now()->subYears(1), Carbon::now()->addMonths(6)])
            ->orderBy('expired_date', 'asc');
        
        if (!$this->isGlobal) {
            $expQuery->where('warehouse_id', $this->warehouseId);
        }
        $this->nearExpiredItems = $expQuery->limit(15)->get();

        // Detail Stok Kritis (Top 15)
        $lowQuery = ItemWarehouseSetting::with(['item.unit', 'item.category', 'warehouse'])
            ->where('min_stock', '>', 0);
        
        if (!$this->isGlobal) {
            $lowQuery->where('warehouse_id', $this->warehouseId);
        }

        $this->lowStockItems = $lowQuery->where(function($q) {
                $q->whereRaw('(SELECT SUM(current_qty) FROM item_batches WHERE item_batches.item_id = item_warehouse_settings.item_id AND item_batches.warehouse_id = item_warehouse_settings.warehouse_id AND is_active = 1) < min_stock');
            })
            ->limit(15)
            ->get()
            ->map(function($setting) {
                $item = $setting->item;
                $item->alert_warehouse = $setting->warehouse->name;
                $item->alert_min_stock = $setting->min_stock;
                $item->current_stock = ItemBatch::where('item_id', $item->id)
                    ->where('warehouse_id', $setting->warehouse_id)
                    ->where('is_active', true)
                    ->sum('current_qty');
                return $item;
            });
    }

    public function loadRecentActivity()
    {
        $query = \App\Models\StockCard::with(['item', 'warehouse', 'batch'])
            ->latest('transaction_date')
            ->latest('id')
            ->limit(15);
        
        if (!$this->isGlobal) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        $this->recentActivities = $query->get();
    }

    public function loadCharts()
    {
        // Distribution Chart (Categories)
        $distQuery = ItemBatch::join('items', 'item_batches.item_id', '=', 'items.id')
            ->join('item_categories', 'items.item_category_id', '=', 'item_categories.id')
            ->where('item_batches.is_active', true)
            ->where('item_batches.current_qty', '>', 0);
        
        if (!$this->isGlobal) {
            $distQuery->where('item_batches.warehouse_id', $this->warehouseId);
        }

        $this->distributionChart = $distQuery->select('item_categories.name', DB::raw('SUM(current_qty * purchase_price) as value'))
            ->groupBy('item_categories.name')
            ->get()
            ->toArray();
            
        // Trend Chart (Example for last 7 days)
        // Implementation for line chart data...
    }

    public function render()
    {
        return view('livewire.inventory.stock-dashboard');
    }
}
