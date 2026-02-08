<?php

namespace App\Livewire\Inventory;

use App\Models\Distribution;
use App\Models\ItemBatch;
use App\Services\Inventory\DistributionService;
use Livewire\Component;

class DistributionProcess extends Component
{
    public $distributionId;
    public $distribution;
    public $items = []; // [[id (detail_id), item_id, name, qty_requested, qty_sent, item_batch_id, available_batches]]

    public function mount($distributionId)
    {
        $this->distributionId = $distributionId;
        $this->loadDistribution();
    }

    public function loadDistribution()
    {
        $this->distribution = Distribution::with(['origin', 'destination', 'details.item.category'])->findOrFail($this->distributionId);
        
        $this->items = [];
        foreach ($this->distribution->details as $detail) {
            // Fetch available batches in origin warehouse for this item
            $availableBatches = ItemBatch::where('item_id', $detail->item_id)
                ->where('warehouse_id', $this->distribution->origin_warehouse_id)
                ->where('current_qty', '>', 0)
                ->where('is_active', true)
                ->orderBy('expired_date', 'asc') // FEFO
                ->get();

            $this->items[] = [
                'id' => $detail->id,
                'item_id' => $detail->item_id,
                'name' => $detail->item->name,
                'code' => $detail->item->code,
                'qty_requested' => $detail->qty_requested,
                'qty_sent' => $detail->qty_requested, // Default to same as requested
                'item_batch_id' => $availableBatches->first()?->id, // Default to earliest expiry
                'available_batches' => $availableBatches->toArray(),
                'destination_stock' => $this->getDestinationStock($detail->item_id, $availableBatches->first()),
                'notes' => ''
            ];
        }
    }

    public function updatedItems($value, $key)
    {
        // If batch changed, update destination stock
        if (str_contains($key, '.item_batch_id')) {
            $parts = explode('.', $key);
            $index = $parts[0];
            $batchId = $value;
            $batch = ItemBatch::find($batchId);
            $this->items[$index]['destination_stock'] = $this->getDestinationStock($this->items[$index]['item_id'], $batch);
        }
    }

    private function getDestinationStock($itemId, $originBatch)
    {
        if (!$originBatch) return 0;

        // Try to find the same batch in destination
        $destBatch = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $this->distribution->destination_warehouse_id)
            ->where('batch_number', is_array($originBatch) ? $originBatch['batch_number'] : $originBatch->batch_number)
            ->where('expired_date', is_array($originBatch) ? $originBatch['expired_date'] : $originBatch->expired_date)
            ->first();

        return $destBatch ? $destBatch->current_qty : 0;
    }

    public function save(DistributionService $service)
    {
        $this->validate([
            'items.*.item_batch_id' => 'required',
            'items.*.qty_sent' => 'required|numeric|min:0.01',
        ]);

        try {
            $service->shipOrder($this->distribution, $this->items);

            session()->flash('message', 'Pengiriman distribusi berhasil diproses.');
            return redirect()->route('inventory.distributions.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pengiriman: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory.distribution-process');
    }
}
