<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouseSetting;
use App\Services\StockSuggestionService;
use App\Services\AI\RestockAdvisorService;
use Livewire\WithPagination;

class StockThresholdOptimization extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouseId = '';
    public $lookbackDays = 30;
    public $editingItemId = null;
    public $editMin = '';
    public $editMax = '';
    public $editRP = '';

    /** @var array<int, array{text:string, ai_generated:bool}> */
    public array $aiRecommendations = [];
    public ?int $aiLoadingItemId = null;

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

    public function applyThreshold($itemId, $suggestedMin, $suggestedMax, $suggestedRP, $adu)
    {
        $service = new StockSuggestionService();
        $service->applySuggestion($itemId, $this->warehouseId, [
            'suggested_min' => $suggestedMin,
            'suggested_max' => $suggestedMax,
            'suggested_reorder_point' => $suggestedRP,
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

    public function startEdit($itemId, $currentMin, $currentMax, $currentRP)
    {
        $this->editingItemId = $itemId;
        $this->editMin = $currentMin === '-' ? 0 : $currentMin;
        $this->editMax = $currentMax === '-' ? 0 : $currentMax;
        $this->editRP = $currentRP === '-' ? 0 : $currentRP;
    }

    public function askAi($itemId, $currentStock, $currentMin, $adu, $suggestedRp, $suggestedMax)
    {
        $this->aiLoadingItemId = $itemId;

        $item = Item::with(['category', 'unit'])->find($itemId);
        if (!$item) {
            $this->aiLoadingItemId = null;
            return;
        }

        $service = app(RestockAdvisorService::class);
        $result = $service->advise($item, $this->warehouseId, [
            'current_stock' => $currentStock,
            'current_min' => $currentMin,
            'adu' => $adu,
            'suggested_rp' => $suggestedRp,
            'suggested_max' => $suggestedMax,
        ]);

        $this->aiRecommendations[$itemId] = $result;
        $this->aiLoadingItemId = null;
    }

    public function cancelEdit()
    {
        $this->editingItemId = null;
        $this->editMin = '';
        $this->editMax = '';
        $this->editRP = '';
    }

    public function updateThreshold($itemId)
    {
        $this->validate([
            'editMin' => 'required|integer|min:0',
            'editMax' => 'required|integer|min:0',
            'editRP' => 'required|integer|min:0',
        ]);

        ItemWarehouseSetting::updateOrCreate(
            ['item_id' => $itemId, 'warehouse_id' => $this->warehouseId],
            [
                'min_stock' => $this->editMin,
                'max_stock' => $this->editMax,
                'reorder_point' => $this->editRP,
            ]
        );

        $this->cancelEdit();
        session()->flash('success', 'Batas stok berhasil diperbarui.');
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
                'current_rp' => $currentSetting?->reorder_point ?? '-',
                'suggested_min' => $suggestions['suggested_min'],
                'suggested_max' => $suggestions['suggested_max'],
                'suggested_rp' => $suggestions['suggested_reorder_point'],
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
