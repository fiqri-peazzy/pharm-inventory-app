<?php

namespace App\Livewire\Clinical;

use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\ItemBatch;
use App\Models\StockCard;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use DB;

class PrescriptionDispense extends Component
{
    public $prescriptionId;
    public $prescription;
    public $details = []; // {detail_id, item_id, item_name, qty_prescribed, batch_id, available_batches, selected_batch_info}

    public function mount($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->loadPrescription();
    }

    public function loadPrescription()
    {
        $this->prescription = Prescription::with(['details.item', 'warehouse'])->findOrFail($this->prescriptionId);
        
        if ($this->prescription->status === 'completed') {
            return redirect()->route('clinical.prescriptions.index')->with('error', 'Resep ini sudah selesai diproses.');
        }

        foreach ($this->prescription->details as $detail) {
            // FEFO Suggestion: Get batches sorted by expiry
            $batches = ItemBatch::where('item_id', $detail->item_id)
                ->where('warehouse_id', $this->prescription->warehouse_id)
                ->where('is_active', true)
                ->where('current_qty', '>', 0)
                ->where('expired_date', '>', now())
                ->orderBy('expired_date', 'asc')
                ->get();

            $suggestedBatch = $batches->first();

            $this->details[] = [
                'detail_id' => $detail->id,
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'qty_prescribed' => $detail->qty,
                'instruction' => $detail->instruction,
                'batch_id' => $suggestedBatch?->id,
                // Immutable reference to the FEFO-recommended batch, so the UI
                // can warn if the user later picks a different (non-FEFO) one.
                'fefo_batch_id' => $suggestedBatch?->id,
                'override_reason' => '',
                'available_batches' => $batches->toArray(),
                'selected_batch_qty' => $suggestedBatch?->current_qty ?? 0
            ];
        }
    }

    public function updatedDetails($value, $key)
    {
        // Handle batch selection change
        if (str_contains($key, 'batch_id')) {
            $index = explode('.', $key)[0];
            $batchId = $value;
            $batch = ItemBatch::find($batchId);
            $this->details[$index]['selected_batch_qty'] = $batch?->current_qty ?? 0;
        }
    }

    public function processDispense()
    {
        // Validate all batches selected and stock sufficient
        foreach ($this->details as $row) {
            if (!$row['batch_id']) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih Batch untuk ' . $row['item_name']]);
                return;
            }
            if ($row['qty_prescribed'] > $row['selected_batch_qty']) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Stok Batch tidak mencukupi untuk ' . $row['item_name']]);
                return;
            }
            // FEFO deviation guard: dispensing a batch other than the one
            // with the earliest expiry is allowed (e.g. the FEFO batch is
            // reserved/damaged), but must be justified for audit purposes.
            if ($row['batch_id'] != $row['fefo_batch_id'] && trim($row['override_reason'] ?? '') === '') {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Beri alasan untuk ' . $row['item_name'] . ' karena batch yang dipilih bukan urutan FEFO (kadaluarsa terdekat).']);
                return;
            }
        }

        try {
            $service = app(\App\Services\Clinical\PrescriptionService::class);
            $items = array_map(function($row) {
                return [
                    'id' => $row['detail_id'],
                    'qty_dispensed' => $row['qty_prescribed'],
                    'item_batch_id' => $row['batch_id'],
                    'fefo_override_reason' => $row['batch_id'] != $row['fefo_batch_id'] ? $row['override_reason'] : null,
                ];
            }, $this->details);

            $service->dispense($this->prescription, $items);

            session()->flash('notify', ['type' => 'success', 'message' => 'Resep berhasil diproses (Dispensed).']);
            return redirect()->route('clinical.prescriptions.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal memproses resep: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.clinical.prescription-dispense');
    }
}
