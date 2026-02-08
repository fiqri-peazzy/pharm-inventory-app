<?php

namespace App\Livewire\Inventory;

use App\Models\Distribution;
use App\Models\DistributionDetail;
use App\Services\Inventory\DistributionService;
use Livewire\Component;

class DistributionReceive extends Component
{
    public $distributionId;
    public $distribution;
    public $items = []; // [[id (detail_id), item_id, name, batch_number, qty_sent, qty_received]]

    public function mount($distributionId)
    {
        $this->distributionId = $distributionId;
        $this->loadDistribution();
    }

    public function loadDistribution()
    {
        $this->distribution = Distribution::with(['origin', 'destination', 'details.item', 'details.batch'])->findOrFail($this->distributionId);
        
        if ($this->distribution->status !== 'sent') {
            session()->flash('error', 'Status transaksi ini tidak valid untuk penerimaan.');
            return redirect()->route('inventory.distributions.index');
        }

        $this->items = [];
        foreach ($this->distribution->details as $detail) {
            $this->items[] = [
                'id' => $detail->id,
                'item_id' => $detail->item_id,
                'name' => $detail->item->name,
                'code' => $detail->item->code,
                'batch_number' => $detail->batch->batch_number ?? '-',
                'expired_date' => $detail->batch->expired_date?->format('d/m/Y') ?? '-',
                'qty_sent' => $detail->qty_sent,
                'qty_received' => $detail->qty_sent, // Default to same as sent
            ];
        }
    }

    public function save(DistributionService $service)
    {
        $this->validate([
            'items.*.qty_received' => 'required|numeric|min:0',
        ]);

        try {
            $service->receiveOrder($this->distribution, $this->items);

            session()->flash('message', 'Penerimaan distribusi berhasil dikonfirmasi.');
            return redirect()->route('inventory.distributions.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inventory.distribution-receive');
    }
}
