<?php

namespace App\Livewire\Inventory;

use App\Models\Item;
use App\Models\Warehouse;
use App\Services\Inventory\DistributionService;
use Livewire\Component;

class DistributionRequest extends Component
{
    public $origin_warehouse_id;
    public $destination_warehouse_id;
    public $notes;
    public $items = []; // [[item_id, name, qty]]
    
    // Search
    public $showItemModal = false;
    public $search = '';
    public $searchResults = [];

    public function mount()
    {
        // Default destination is current user's warehouse
        $this->destination_warehouse_id = auth()->user()->warehouse_id;
        
        // Default origin is Gudang Utama (first warehouse usually)
        $gudangUtama = Warehouse::where('name', 'like', '%Gudang Utama%')->first();
        $this->origin_warehouse_id = $gudangUtama?->id;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Item::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->limit(5)
            ->get();
    }

    public function addItem($itemId)
    {
        $item = Item::find($itemId);
        
        // Check if item already in list
        foreach ($this->items as $i) {
            if ($i['item_id'] == $itemId) {
                $this->search = '';
                $this->searchResults = [];
                return;
            }
        }

        $this->items[] = [
            'item_id' => $item->id,
            'name' => $item->name,
            'code' => $item->code,
            'qty' => 1
        ];

        $this->dispatch('close-item-modal');
        $this->search = '';
        $this->searchResults = [];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(DistributionService $service)
    {
        $this->validate([
            'origin_warehouse_id' => 'required',
            'destination_warehouse_id' => 'required|different:origin_warehouse_id',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        try {
            $service->createRequest([
                'origin_warehouse_id' => $this->origin_warehouse_id,
                'destination_warehouse_id' => $this->destination_warehouse_id,
                'notes' => $this->notes,
                'items' => $this->items,
            ]);

            session()->flash('message', 'Permintaan distribusi berhasil dibuat.');
            return redirect()->route('inventory.distributions.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat permintaan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory.distribution-request', [
            'warehouses' => Warehouse::all()
        ]);
    }
}
