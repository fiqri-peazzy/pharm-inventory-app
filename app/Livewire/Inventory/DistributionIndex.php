<?php

namespace App\Livewire\Inventory;

use App\Models\Distribution;
use Livewire\Component;
use Livewire\WithPagination;

class DistributionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $warehouseId;

    public function mount()
    {
        // If user is from a specific warehouse, they only see their own warehouse as origin OR destination
        $this->warehouseId = auth()->user()->warehouse_id;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Distribution::with(['origin', 'destination', 'creator', 'sender', 'receiver'])
            ->latest();

        if ($this->warehouseId) {
            $query->where(function ($q) {
                $q->where('origin_warehouse_id', $this->warehouseId)
                  ->orWhere('destination_warehouse_id', $this->warehouseId);
            });
        }

        if ($this->search) {
            $query->where('distribution_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('origin', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('destination', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.inventory.distribution-index', [
            'distributions' => $query->paginate(15)
        ]);
    }
}
