<?php

namespace App\Livewire\Procurement;

use Livewire\Component;
use App\Models\Warehouse;
use App\Services\Procurement\RkoService;
use Livewire\WithPagination;

class RkoIndex extends Component
{
    use WithPagination;

    public $warehouseId = 'all'; // Default to Global
    public $projectionDays = 30;
    public $search = '';
    public $filterVen = '';
    public $filterAbc = '';

    protected $queryString = ['search', 'filterVen', 'filterAbc', 'warehouseId'];

    public function mount()
    {
        // Default to Global
    }

    public function syncUsage()
    {
        $service = new RkoService();
        if ($this->warehouseId === 'all') {
            $warehouses = Warehouse::all();
            foreach ($warehouses as $wh) {
                $service->syncUsage($wh);
            }
        } else {
            $warehouse = Warehouse::find($this->warehouseId);
            if ($warehouse) {
                $service->syncUsage($warehouse);
            }
        }

        session()->flash('success', 'Data pemakaian berhasil disinkronisasi.');
    }

    public function generateSp($itemId, $qty, $catCode = null)
    {
        $spType = 'reguler';
        if ($catCode === 'NAR') $spType = 'narkotika';
        if ($catCode === 'PSI') $spType = 'psikotropika';

        return redirect()->route('procurement.orders.create', [
            'items' => json_encode([['item_id' => $itemId, 'qty' => $qty]]),
            'sp_type' => $spType
        ]);
    }

    public function generateBatchSp($classification)
    {
        $service = new RkoService();
        if ($this->warehouseId === 'all') {
            $suggestions = $service->calculateGlobalRko($this->projectionDays);
        } else {
            $warehouse = Warehouse::find($this->warehouseId);
            $suggestions = $warehouse ? $service->calculateRko($warehouse, $this->projectionDays) : [];
        }

        $items = collect($suggestions)->filter(function($item) use ($classification) {
            $cat = $item['item_category_code'];
            if ($classification === 'narkotika') return $cat === 'NAR';
            if ($classification === 'psikotropika') return $cat === 'PSI';
            return !in_array($cat, ['NAR', 'PSI']);
        })->map(function($item) {
            return [
                'item_id' => $item['item_id'],
                'qty' => $item['suggested_qty']
            ];
        })->values()->toArray();

        if (empty($items)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => "Tidak ada item untuk kategori $classification."]);
            return;
        }

        return redirect()->route('procurement.orders.create', [
            'items' => json_encode($items),
            'sp_type' => $classification
        ]);
    }

    public function render()
    {
        $service = new RkoService();
        
        if ($this->warehouseId === 'all') {
            $suggestions = $service->calculateGlobalRko($this->projectionDays);
        } else {
            $warehouse = Warehouse::find($this->warehouseId);
            $suggestions = $warehouse ? $service->calculateRko($warehouse, $this->projectionDays) : [];
        }

        // Apply local filtering
        $displayData = collect($suggestions)->filter(function($item) {
            $matchesSearch = empty($this->search) || 
                            stripos($item['item_name'], $this->search) !== false || 
                            stripos($item['code'], $this->search) !== false;
            
            $matchesVen = empty($this->filterVen) || $item['ven'] === $this->filterVen;
            $matchesAbc = empty($this->filterAbc) || $item['abc'] === $this->filterAbc;

            return $matchesSearch && $matchesVen && $matchesAbc;
        });

        $warehouses = Warehouse::all();

        return view('livewire.procurement.rko-index', [
            'displayData' => $displayData,
            'warehouses' => $warehouses
        ]);
    }
}
