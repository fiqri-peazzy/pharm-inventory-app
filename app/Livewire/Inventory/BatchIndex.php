<?php

namespace App\Livewire\Inventory;

use App\Models\ItemBatch;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class BatchIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouseId;
    public $status = ''; // expired, near_expired, active

    public function mount()
    {
        // RBAC: Restricted warehouse access
        if (!auth()->user()->hasAnyRole(['super-admin', 'kepala-farmasi', 'direktur', 'bupati', 'auditor'])) {
            $this->warehouseId = auth()->user()->warehouse_id;
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'warehouseId', 'status'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = ItemBatch::with(['item', 'warehouse'])
            ->when($this->search, function ($q) {
                $q->whereHas('item', function ($iq) {
                    $iq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('code', 'like', '%' . $this->search . '%');
                })->orWhere('batch_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->warehouseId, fn($q) => $q->where('warehouse_id', $this->warehouseId))
            ->when($this->status == 'expired', fn($q) => $q->expired())
            ->when($this->status == 'near_expired', fn($q) => $q->nearExpired())
            ->when($this->status == 'active', fn($q) => $q->active())
            ->when($this->status == 'depleted', fn($q) => $q->where('current_qty', '<=', 0))
            // Default view: only batches that still have physical stock and
            // therefore still need monitoring. Depleted batches remain
            // accessible via the explicit "Habis/Dimusnahkan" filter for
            // audit purposes, but shouldn't clutter the default list.
            ->when($this->status == '', fn($q) => $q->where('current_qty', '>', 0))
            ->orderBy('expired_date', 'asc');

        return view('livewire.inventory.batch-index', [
            'batches' => $query->paginate(20),
            'warehouses' => Warehouse::all(),
        ]);
    }
}
