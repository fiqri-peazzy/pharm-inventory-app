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
        }

        try {
            DB::transaction(function () {
                foreach ($this->details as $row) {
                    $batch = ItemBatch::lockForUpdate()->find($row['batch_id']);
                    $oldQty = $batch->current_qty;
                    
                    // Decrease stock
                    $batch->decrement('current_qty', $row['qty_prescribed']);

                    // Update detail with batch info
                    PrescriptionDetail::where('id', $row['detail_id'])->update([
                        'item_batch_id' => $batch->id,
                        'price_per_unit' => $batch->purchase_price, // Assuming selling price logic later
                        'subtotal' => $batch->purchase_price * $row['qty_prescribed']
                    ]);

                    // Stock Card
                    StockCard::create([
                        'item_id' => $row['item_id'],
                        'warehouse_id' => $this->prescription->warehouse_id,
                        'item_batch_id' => $batch->id,
                        'transaction_date' => now(),
                        'transaction_type' => 'prescription',
                        'reference_type' => Prescription::class,
                        'reference_id' => $this->prescription->id,
                        'qty_out' => $row['qty_prescribed'],
                        'last_stock' => $oldQty - $row['qty_prescribed'],
                        'notes' => 'Dispensed for Rx: ' . $this->prescription->prescription_number,
                    ]);
                }

                $this->prescription->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'processed_by' => Auth::id()
                ]);

                // 4. Accounting Integration (Auto-Posting COGS)
                try {
                    $accountingService = app(\App\Services\AccountingService::class);
                    $entries = [];
                    $summaryByAccount = [];

                    foreach ($this->prescription->details as $detail) {
                        $inventoryAccount = $accountingService->getInventoryAccountByCategory($detail->item->category?->type);
                        $cogsAccount = $accountingService->getCOGSAccountByCategory($detail->item->category?->type);
                        
                        $costAmount = $detail->subtotal; // Using subtotal which is cost-based in this system

                        // Group by Inventory Account (Credit)
                        if (!isset($summaryByAccount['inv_' . $inventoryAccount->id])) {
                            $summaryByAccount['inv_' . $inventoryAccount->id] = ['account' => $inventoryAccount, 'amount' => 0];
                        }
                        $summaryByAccount['inv_' . $inventoryAccount->id]['amount'] += $costAmount;

                        // Group by COGS Account (Debit)
                        if (!isset($summaryByAccount['cogs_' . $cogsAccount->id])) {
                            $summaryByAccount['cogs_' . $cogsAccount->id] = ['account' => $cogsAccount, 'amount' => 0];
                        }
                        $summaryByAccount['cogs_' . $cogsAccount->id]['amount'] += $costAmount;
                    }

                    foreach ($summaryByAccount as $key => $data) {
                        if ($data['amount'] > 0) {
                            $isDebit = str_starts_with($key, 'cogs_');
                            $entries[] = [
                                'account_id' => $data['account']->id,
                                'debit' => $isDebit ? $data['amount'] : 0,
                                'credit' => $isDebit ? 0 : $data['amount'],
                                'description' => ($isDebit ? 'Beban: ' : 'Persediaan: ') . $this->prescription->prescription_number
                            ];
                        }
                    }

                    if (count($entries) > 0) {
                        $accountingService->createJournalEntry([
                            'journal_number' => $this->prescription->prescription_number,
                            'journal_date' => now(),
                            'type' => 'standard',
                            'transaction_type' => 'prescription',
                            'transaction_id' => $this->prescription->id,
                            'description' => 'Auto-journal (COGS) for prescription ' . $this->prescription->prescription_number,
                            'status' => 'posted',
                            'entries' => $entries
                        ]);
                    }

                } catch (\Exception $e) {
                    \Log::error('Accounting auto-posting failed for prescription ' . $this->prescription->prescription_number . ': ' . $e->getMessage());
                }
            });

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
