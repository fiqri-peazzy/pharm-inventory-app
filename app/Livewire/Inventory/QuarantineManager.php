<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\ItemBatch;
use App\Models\Warehouse;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class QuarantineManager extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouseId = '';
    public $filterStatus = 'quarantine';

    protected $queryString = ['search', 'warehouseId', 'filterStatus'];

    public function mount()
    {
        $mainWh = Warehouse::where('is_main', true)->first();
        if ($mainWh) {
            $this->warehouseId = $mainWh->id;
        }
    }

    public function releaseBatch($batchId)
    {
        $batch = ItemBatch::findOrFail($batchId);
        
        // Logical check: only quarantine can be released
        if ($batch->status !== 'quarantine') {
            session()->flash('error', 'Hanya batch karantina yang dapat dirilis.');
            return;
        }

        $batch->update(['status' => 'available']);
        
        session()->flash('success', "Batch {$batch->batch_number} telah dirilis ke stok tersedia.");
    }

    public function rejectBatch($batchId, $reason = '')
    {
        $batch = ItemBatch::findOrFail($batchId);
        $batch->update([
            'status' => 'expired', // Using expired as a proxy for 'rejected/not-for-use'
            'is_active' => false,
            'notes' => $batch->notes . "\nRejected during quarantine. Reason: " . $reason
        ]);

        session()->flash('warning', "Batch {$batch->batch_number} telah ditolak dan dinonaktifkan.");
    }

    public function render()
    {
        $query = ItemBatch::with(['item', 'warehouse'])
            ->when($this->search, function($q) {
                $q->whereHas('item', function($iq) {
                    $iq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                })->orWhere('batch_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->warehouseId, function($q) {
                $q->where('warehouse_id', $this->warehouseId);
            })
            ->when($this->filterStatus, function($q) {
                $q->where('status', $this->filterStatus);
            });

        return view('livewire.inventory.quarantine-manager', [
            'batches' => $query->latest()->paginate(10),
            'warehouses' => Warehouse::all()
        ]);
    }
}
