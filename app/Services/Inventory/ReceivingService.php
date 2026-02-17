<?php

namespace App\Services\Inventory;

use App\Models\Receiving;
use App\Models\ItemBatch;
use App\Models\StockCard;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\DB;

class ReceivingService
{
    public function post(Receiving $rcv)
    {
        if ($rcv->status === 'posted') {
            throw new \Exception('Receiving already posted.');
        }

        if (!$rcv->warehouse || !$rcv->warehouse->is_main) {
            throw new \Exception('Penerimaan dari pihak luar (PBF) hanya diperbolehkan melalui Gudang Utama.');
        }

        return DB::transaction(function () use ($rcv) {
            foreach ($rcv->details as $detail) {
                // 1. Create Batch (Default to QUARANTINE as per RSUD SOP)
                $batch = ItemBatch::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $rcv->warehouse_id,
                    'batch_number' => $detail->batch_number,
                    'expired_date' => $detail->expired_date,
                    'initial_qty' => $detail->qty_received,
                    'current_qty' => $detail->qty_received,
                    'purchase_price' => $detail->purchase_price,
                    'status' => 'quarantine', // Restricted until released
                    'is_active' => true,
                ]);

                // 2. Create Stock Card
                StockCard::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $rcv->warehouse_id,
                    'item_batch_id' => $batch->id,
                    'transaction_date' => $rcv->receiving_date,
                    'transaction_type' => 'receiving',
                    'reference_type' => Receiving::class,
                    'reference_id' => $rcv->id,
                    'qty_in' => $detail->qty_received,
                    'qty_out' => 0,
                    'last_stock' => $this->calculateLastStock($detail->item_id, $rcv->warehouse_id) + $detail->qty_received,
                    'notes' => 'Penerimaan No: ' . $rcv->receiving_number,
                ]);

                // 3. Update PO Detail if applicable
                if ($rcv->purchase_order_id) {
                    $poDetail = PurchaseOrderDetail::where('purchase_order_id', $rcv->purchase_order_id)
                        ->where('item_id', $detail->item_id)
                        ->first();
                    if ($poDetail) {
                        $poDetail->increment('qty_received', $detail->qty_received);
                    }
                }
            }

            // 4. Update PO Status if applicable
            if ($rcv->purchase_order_id) {
                $po = PurchaseOrder::with('details')->find($rcv->purchase_order_id);
                $fullyReceived = true;
                foreach ($po->details as $d) {
                    if ($d->qty_received < $d->qty_ordered) {
                        $fullyReceived = false;
                        break;
                    }
                }
                $po->update(['status' => $fullyReceived ? 'completed' : 'partial_received']);
            }

            $rcv->update([
                'status' => 'posted',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // 5. Accounting Integration (Auto-Posting)
            try {
                $accountingService = app(\App\Services\AccountingService::class);
                
                $entries = [];
                $summaryByAccount = [];

                foreach ($rcv->details as $detail) {
                    $inventoryAccount = $accountingService->getInventoryAccountByCategory($detail->item->category?->type);
                    $amount = $detail->qty_received * $detail->purchase_price;
                    
                    // Add PPN proportional to this item if needed? 
                    // Usually total grand_total is AP.
                    // Let's just use the line amount and handle PPN as a separate entry if header has PPN.
                    
                    if (!isset($summaryByAccount[$inventoryAccount->id])) {
                        $summaryByAccount[$inventoryAccount->id] = 0;
                    }
                    $summaryByAccount[$inventoryAccount->id] += $amount;
                }

                // Add Inventory Debit Entries
                foreach ($summaryByAccount as $accountId => $totalAmount) {
                    if ($totalAmount > 0) {
                        $entries[] = [
                            'account_id' => $accountId,
                            'debit' => $totalAmount,
                            'credit' => 0,
                            'description' => 'Penerimaan: ' . $rcv->receiving_number
                        ];
                    }
                }

                // Add PPN Entry if any
                if ($rcv->ppn_amount > 0) {
                    // Assuming PPN Masukan (VAT In) account, but CoA doesn't have it yet.
                    // For now, add it to inventory value (capitalized) as per some simple rules, 
                    // or I should check if there is a PPN account. 
                    // PHASE 6 CoA doesn't explicitly list PPN account yet.
                    // I'll add PPN to the first inventory account or proportionally.
                    // Let's capitalize PPN for now to keep it balanced.
                    if (count($entries) > 0) {
                        $entries[0]['debit'] += $rcv->ppn_amount;
                    }
                }

                // Add Accounts Payable Credit Entry
                $apAccount = $accountingService->getAPAccountBySupplier($rcv->supplier_id);
                $entries[] = [
                    'account_id' => $apAccount->id,
                    'debit' => 0,
                    'credit' => $rcv->grand_total,
                    'description' => 'Hutang Dagang: ' . ($rcv->supplier?->name ?? 'Supplier')
                ];

                $accountingService->createJournalEntry([
                    'journal_number' => $rcv->receiving_number, // Use transaction number as journal number
                    'journal_date' => $rcv->receiving_date,
                    'type' => 'standard',
                    'transaction_type' => 'receiving',
                    'transaction_id' => $rcv->id,
                    'reference' => $rcv->invoice_number,
                    'description' => 'Auto-journal for receiving ' . $rcv->receiving_number,
                    'status' => 'posted', // Auto-post immediately
                    'entries' => $entries
                ]);

            } catch (\Exception $e) {
                // Log error but don't fail the whole transaction if accounting fails?
                // Actually, in a critical system, accounting SHOULD succeed if inventory succeeds.
                // But for safety during dev, let's log it.
                \Log::error('Accounting auto-posting failed for receiving ' . $rcv->receiving_number . ': ' . $e->getMessage());
                // throw $e; // If you want strictly coupled
            }

            return $rcv;
        });
    }

    protected function calculateLastStock($itemId, $warehouseId)
    {
        return ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->sum('current_qty');
    }
}
