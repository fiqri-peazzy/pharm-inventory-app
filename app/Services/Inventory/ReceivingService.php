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

        return DB::transaction(function () use ($rcv) {
            foreach ($rcv->details as $detail) {
                // 1. Create Batch
                $batch = ItemBatch::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $rcv->warehouse_id,
                    'batch_number' => $detail->batch_number,
                    'expired_date' => $detail->expired_date,
                    'initial_qty' => $detail->qty_received,
                    'current_qty' => $detail->qty_received,
                    'purchase_price' => $detail->purchase_price,
                    'is_active' => true,
                ]);

                // 2. Create Stock Card
                StockCard::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $rcv->warehouse_id,
                    'item_batch_id' => $batch->id,
                    'transaction_date' => $rcv->receiving_date,
                    'transaction_type' => 'receiving',
                    'reference_type' => 'receiving',
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
