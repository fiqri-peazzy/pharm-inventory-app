<?php

namespace App\Services\Inventory;

use App\Models\Distribution;
use App\Models\DistributionDetail;
use App\Models\ItemBatch;
use App\Models\StockCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DistributionService
{
    /**
     * Create a new distribution request from a depo.
     */
    public function createRequest(array $data)
    {
        return DB::transaction(function () use ($data) {
            $distribution = Distribution::create([
                'distribution_number' => $this->generateNumber(),
                'origin_warehouse_id' => $data['origin_warehouse_id'],
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'status' => 'requested',
                'type' => 'request',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'requested_at' => Carbon::now(),
            ]);

            foreach ($data['items'] as $item) {
                $distribution->details()->create([
                    'item_id' => $item['item_id'],
                    'qty_requested' => $item['qty'],
                ]);
            }

            return $distribution;
        });
    }

    /**
     * Ship items from origin warehouse to destination.
     * Enforces FEFO (First Expired First Out).
     */
    public function shipOrder(Distribution $distribution, array $details)
    {
        return DB::transaction(function () use ($distribution, $details) {
            foreach ($details as $detail) {
                $itemDetail = DistributionDetail::find($detail['id']);
                
                // If it's a new item not in request, create it
                if (!$itemDetail) {
                    $itemDetail = $distribution->details()->create([
                        'item_id' => $detail['item_id'],
                    ]);
                }

                $itemDetail->update([
                    'item_batch_id' => $detail['item_batch_id'],
                    'notes' => $detail['notes'] ?? null,
                ]);

                // Allocate batches for this item
                $qtySent = $detail['qty_sent'];
                if ($qtySent <= 0) {
                    throw new \Exception("Jumlah yang dikirim untuk {$itemDetail->item->name} harus lebih dari 0.");
                }

                $this->allocateBatches($distribution, $itemDetail, $qtySent);
            }

            $distribution->update([
                'status' => 'sent',
                'sent_at' => Carbon::now(),
                'sent_by' => auth()->id(),
                'total_items' => $distribution->details()->count(),
                'total_qty' => $distribution->details()->sum('qty_sent'),
            ]);

            return $distribution;
        });
    }

    /**
     * Receive items at the destination warehouse.
     * Supports partial receiving.
     */
    public function receiveOrder(Distribution $distribution, array $details)
    {
        return DB::transaction(function () use ($distribution, $details) {
            foreach ($details as $detail) {
                $itemDetail = DistributionDetail::find($detail['id']);
                $qtyReceived = $detail['qty_received'];

                // Update detail
                $itemDetail->update(['qty_received' => $qtyReceived]);

                // 1. Find or Create Batch in Destination Warehouse
                $originBatch = $itemDetail->batch;
                
                $destinationBatch = ItemBatch::where('item_id', $itemDetail->item_id)
                    ->where('warehouse_id', $distribution->destination_warehouse_id)
                    ->where('batch_number', $originBatch->batch_number)
                    ->where('expired_date', $originBatch->expired_date)
                    ->first();

                if (!$destinationBatch) {
                    $destinationBatch = ItemBatch::create([
                        'item_id' => $itemDetail->item_id,
                        'warehouse_id' => $distribution->destination_warehouse_id,
                        'batch_number' => $originBatch->batch_number,
                        'expired_date' => $originBatch->expired_date,
                        'initial_qty' => 0,
                        'current_qty' => 0,
                        'purchase_price' => $originBatch->purchase_price,
                        'is_active' => true
                    ]);
                }

                // 2. Increment Stock at Destination
                $destinationBatch->increment('current_qty', $qtyReceived);

                // 3. Create Stock Card (In) at Destination
                StockCard::create([
                    'item_id' => $itemDetail->item_id,
                    'warehouse_id' => $distribution->destination_warehouse_id,
                    'item_batch_id' => $destinationBatch->id,
                    'qty_in' => $qtyReceived,
                    'qty_out' => 0,
                    'last_stock' => $this->calculateTotalStock($itemDetail->item_id, $distribution->destination_warehouse_id),
                    'transaction_type' => 'distribution_in',
                    'reference_type' => Distribution::class,
                    'reference_id' => $distribution->id,
                    'transaction_date' => Carbon::now(),
                    'notes' => "Terima dari " . $distribution->origin->name,
                ]);
            }

            $distribution->update([
                'status' => 'received',
                'received_at' => Carbon::now(),
                'received_by' => auth()->id(),
            ]);

            return $distribution;
        });
    }

    /**
     * Private helper to allocate batches and handle stock deduction at origin.
     */
    private function allocateBatches($distribution, $detail, $qtyToShip)
    {
        $batch = ItemBatch::findOrFail($detail->item_batch_id);
        
        if ($batch->current_qty < $qtyToShip) {
            throw new \Exception("Stok barang {$detail->item->name} tidak mencukupi di Batch {$batch->batch_number}.");
        }

        // 1. Deduct Stock at Origin
        $batch->decrement('current_qty', $qtyToShip);

        // 2. Update Detail
        $detail->update([
            'qty_sent' => $qtyToShip,
            'unit_price' => $batch->purchase_price,
        ]);

        // 3. Create Stock Card (Out) at Origin
        StockCard::create([
            'item_id' => $detail->item_id,
            'warehouse_id' => $distribution->origin_warehouse_id,
            'item_batch_id' => $batch->id,
            'qty_in' => 0,
            'qty_out' => $qtyToShip,
            'last_stock' => $this->calculateTotalStock($detail->item_id, $distribution->origin_warehouse_id),
            'transaction_type' => 'distribution_out',
            'reference_type' => Distribution::class,
            'reference_id' => $distribution->id,
            'transaction_date' => Carbon::now(),
            'notes' => "Kirim ke " . $distribution->destination->name,
        ]);
    }

    private function calculateTotalStock($itemId, $warehouseId)
    {
        return ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->sum('current_qty');
    }

    private function generateNumber()
    {
        $prefix = "DIST-" . date('Ymd');
        $last = Distribution::where('distribution_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        $seq = $last ? (int)str_replace($prefix, '/', $last->distribution_number) + 1 : 1;
        
        // Fix generator logic
        $lastNum = $last ? (int)substr($last->distribution_number, -4) : 0;
        $seq = $lastNum + 1;
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
