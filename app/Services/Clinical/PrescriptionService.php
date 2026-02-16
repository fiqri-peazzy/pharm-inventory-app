<?php

namespace App\Services\Clinical;

use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\ItemBatch;
use App\Models\StockCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    /**
     * Dispense a prescription and deduct stock from the warehouse.
     * Enforces strict batch tracking and real-time deduction.
     */
    public function dispense(Prescription $prescription, array $dispensedItems)
    {
        return DB::transaction(function () use ($prescription, $dispensedItems) {
            foreach ($dispensedItems as $item) {
                $detail = PrescriptionDetail::find($item['id']);
                $qtyToDispense = $item['qty_dispensed'];
                $batchId = $item['item_batch_id'];

                if ($qtyToDispense <= 0) continue;

                $batch = ItemBatch::findOrFail($batchId);
                
                if ($batch->current_qty < $qtyToDispense) {
                    throw new \Exception("Stok tidak mencukupi untuk item {$detail->item->name} di Batch {$batch->batch_number}.");
                }

                // 1. Deduct Stock
                $batch->decrement('current_qty', $qtyToDispense);

                // 2. Update Detail
                $detail->update([
                    'qty_dispensed' => $qtyToDispense,
                    'item_batch_id' => $batchId,
                    'price_per_unit' => $batch->purchase_price,
                    'subtotal' => $batch->purchase_price * $qtyToDispense,
                    'dispensed_at' => Carbon::now(),
                ]);

                // 3. Create Stock Card
                StockCard::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $prescription->warehouse_id,
                    'item_batch_id' => $batch->id,
                    'qty_in' => 0,
                    'qty_out' => $qtyToDispense,
                    'last_stock' => $this->calculateTotalStock($detail->item_id, $prescription->warehouse_id),
                    'transaction_type' => 'dispensing',
                    'reference_type' => Prescription::class,
                    'reference_id' => $prescription->id,
                    'transaction_date' => Carbon::now(),
                    'notes' => "Dispensing Resep No: " . $prescription->prescription_number,
                ]);
            }

            $prescription->update([
                'status' => 'completed',
                'dispensed_at' => Carbon::now(),
                'dispensed_by' => auth()->id(),
            ]);

            // 4. Accounting Integration (Auto-Posting COGS)
            try {
                $accountingService = app(\App\Services\AccountingService::class);
                $entries = [];
                $summaryByAccount = [];

                foreach ($prescription->details as $detail) {
                    $inventoryAccount = $accountingService->getInventoryAccountByCategory($detail->item->category?->type);
                    $cogsAccount = $accountingService->getCOGSAccountByCategory($detail->item->category?->type);
                    
                    // subtotal was updated in step 2
                    $costAmount = $detail->subtotal; 

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
                            'description' => ($isDebit ? 'Beban: ' : 'Persediaan: ') . $prescription->prescription_number
                        ];
                    }
                }

                if (count($entries) > 0) {
                    $accountingService->createJournalEntry([
                        'journal_number' => $prescription->prescription_number,
                        'journal_date' => now(),
                        'type' => 'standard',
                        'transaction_type' => 'prescription',
                        'transaction_id' => $prescription->id,
                        'description' => 'Auto-journal (COGS) for prescription ' . $prescription->prescription_number,
                        'status' => 'posted',
                        'entries' => $entries
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error('Accounting auto-posting failed for prescription ' . $prescription->prescription_number . ': ' . $e->getMessage());
            }

            return $prescription;
        });
    }

    private function calculateTotalStock($itemId, $warehouseId)
    {
        return ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->sum('current_qty');
    }
}
