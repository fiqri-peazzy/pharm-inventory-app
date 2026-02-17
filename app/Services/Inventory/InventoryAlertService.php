<?php

namespace App\Services\Inventory;

use App\Models\ItemBatch;
use App\Models\StockCard;
use App\Models\PrescriptionDetail;
use App\Models\DistributionDetail;
use App\Models\ItemWarehouseSetting;
use Illuminate\Support\Facades\DB;

class InventoryAlertService
{
    /**
     * Get items that are near expiry (standard < 6 months).
     */
    public function getNearExpiredItems($months = 6)
    {
        return ItemBatch::with(['item', 'warehouse'])
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->where('expired_date', '<=', now()->addMonths($months))
            ->where('expired_date', '>', now())
            ->orderBy('expired_date', 'asc')
            ->get();
    }

    /**
     * Get items that are below their reorder point.
     */
    public function getLowStockItems()
    {
        $settings = ItemWarehouseSetting::with(['item', 'warehouse'])->get();
        $lowStock = [];

        foreach ($settings as $setting) {
            $currentStock = ItemBatch::where('item_id', $setting->item_id)
                ->where('warehouse_id', $setting->warehouse_id)
                ->active()
                ->sum('current_qty');

            if ($currentStock <= $setting->reorder_point) {
                $lowStock[] = [
                    'item' => $setting->item,
                    'warehouse' => $setting->warehouse,
                    'current_stock' => $currentStock,
                    'reorder_point' => $setting->reorder_point,
                    'status' => $currentStock <= $setting->min_stock ? 'CRITICAL' : 'WARNING'
                ];
            }
        }

        return $lowStock;
    }

    /**
     * Traceability: Find every transaction and patient/destination for a specific Batch Number.
     */
    public function traceBatch($batchNumber)
    {
        $trace = [
            'batches' => ItemBatch::with(['warehouse', 'item'])->where('batch_number', $batchNumber)->get(),
            'distributions' => DistributionDetail::with(['distribution.destination', 'item'])
                ->whereHas('batch', function($q) use ($batchNumber) {
                    $q->where('batch_number', $batchNumber);
                })->get(),
            'prescriptions' => PrescriptionDetail::with(['prescription.patient', 'item'])
                ->whereHas('batch', function($q) use ($batchNumber) {
                    $q->where('batch_number', $batchNumber);
                })->get(),
        ];

        return $trace;
    }
}
