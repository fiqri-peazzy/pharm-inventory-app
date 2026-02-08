<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouseSetting;
use App\Services\StockSuggestionService;
use Livewire\WithPagination;

class StockThresholdOptimization extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouseId = '';
    public $lookbackDays = 30;

    protected $listeners = ['refreshSuggestions' => '$refresh'];

    public function mount()
    {
        // Default to main warehouse if exists
        $mainWarehouse = Warehouse::where('is_main', true)->first();
        if ($mainWarehouse) {
            $this->warehouseId = $mainWarehouse->id;
        } else {
            $this->warehouseId = Warehouse::first()?->id;
        }
    }

    public function applyThreshold($itemId, $suggestedMin, $suggestedMax, $adu)
    {
        $service = new StockSuggestionService();
        $service->applySuggestion($itemId, $this->warehouseId, [
            'suggested_min' => $suggestedMin,
            'suggested_max' => $suggestedMax,
            'adu' => $adu,
        ]);

        session()->flash('success', 'Batas stok diperbarui.');
    }

    public function applyAll()
    {
        $service = new StockSuggestionService();
        $items = Item::search($this->search)->get();

        foreach ($items as $item) {
            $suggestions = $service->getSuggestions($item->id, $this->warehouseId, $this->lookbackDays);
            $service->applySuggestion($item->id, $this->warehouseId, $suggestions);
        }

        session()->flash('success', 'Semua saran stok telah diterapkan.');
    }

    public function render()
    {
        $items = Item::search($this->search)
            ->with(['warehouseSettings' => function($query) {
                $query->where('warehouse_id', $this->warehouseId);
            }])
            ->paginate(10);

        $warehouses = Warehouse::all();
        $service = new StockSuggestionService();

        $displayData = $items->getCollection()->map(function($item) use ($service) {
            $currentSetting = $item->warehouseSettings->first();
            $suggestions = $service->getSuggestions($item->id, $this->warehouseId, $this->lookbackDays);
            
            // Current stock in this warehouse
            $currentStock = \App\Models\ItemBatch::where('item_id', $item->id)
                ->where('warehouse_id', $this->warehouseId)
                ->sum('current_qty');

            return [
                'item' => $item,
                'current_stock' => $currentStock,
                'current_min' => $currentSetting?->min_stock ?? '-',
                'current_max' => $currentSetting?->max_stock ?? '-',
                'suggested_min' => $suggestions['suggested_min'],
                'suggested_max' => $suggestions['suggested_max'],
                'suggested_min_20percent' => $suggestions['suggested_min_20percent'],
                'adu' => $suggestions['adu'],
            ];
        });

        return view('livewire.inventory.stock-threshold-optimization', [
            'items' => $items,
            'displayData' => $displayData,
            'warehouses' => $warehouses
        ]);
    }
}
