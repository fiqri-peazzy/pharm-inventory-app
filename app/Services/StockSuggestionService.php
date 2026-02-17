<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockCard;
use App\Models\ItemWarehouseSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockSuggestionService
{
    /**
     * Calculate suggested stock thresholds for a specific item in a warehouse.
     * 
     * @param int $itemId
     * @param int $warehouseId
     * @param int $days Lookback period for usage history
     * @return array
     */
    public function getSuggestions($itemId, $warehouseId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);

        // Calculate Average Daily Usage (ADU)
        // We look for 'qty_out' in StockCard for this item and warehouse
        $totalUsage = StockCard::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('transaction_date', '>=', $startDate)
            ->sum('qty_out');

        $adu = $totalUsage / $days;

        // Default parameters (can be moved to config or settings table later)
        $leadTime = 7; // days (supplier delivery time)
        $safetyDays = 3; // days (buffer for uncertainty)
        $reviewPeriod = 30; // days (reorder review cycle)

        // Calculate Safety Stock = ADU × Safety Days
        $safetyStock = ceil($adu * $safetyDays);
        
        // Calculate Reorder Point = (ADU × Lead Time) + Safety Stock
        $reorderPoint = ceil(($adu * $leadTime) + $safetyStock);
        
        // Min Stock = Safety Stock (minimum buffer to maintain)
        $suggestedMin = $safetyStock;
        
        // Max Stock = Reorder Point + (ADU × Review Period)
        $suggestedMax = $reorderPoint + ceil($adu * $reviewPeriod);
        
        // Ensure minimum values for items with usage
        if ($adu > 0 && $suggestedMax < 100) {
            $suggestedMax = 100;
        }

        return [
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'adu' => round($adu, 2),
            'suggested_min' => (int) $suggestedMin,
            'suggested_max' => (int) $suggestedMax,
            'suggested_reorder_point' => (int) $reorderPoint,
            'suggested_min_20percent' => (int) ceil($suggestedMax * 0.2), // Alternative: 20% of max
            'safety_stock' => (int) $safetyStock,
            'lead_time_days' => $leadTime,
            'safety_days' => $safetyDays,
            'calculation_period_days' => $days,
            'last_usage_sum' => $totalUsage
        ];
    }

    /**
     * Apply suggested thresholds to the ItemWarehouseSetting record.
     */
    public function applySuggestion($itemId, $warehouseId, $thresholds)
    {
        return ItemWarehouseSetting::updateOrCreate(
            ['item_id' => $itemId, 'warehouse_id' => $warehouseId],
            [
                'min_stock' => $thresholds['suggested_min'],
                'max_stock' => $thresholds['suggested_max'],
                'reorder_point' => $thresholds['suggested_reorder_point'] ?? 0,
                'average_daily_usage' => $thresholds['adu'],
                'usage_rate_per_day' => $thresholds['adu'],
                'last_suggested_at' => now(),
            ]
        );
    }

    /**
     * Calculate and apply thresholds for all items in a warehouse or all warehouses
     * 
     * @param int|null $warehouseId Specific warehouse or null for all
     * @param int $days Lookback period for usage history
     * @return int Number of updated settings
     */
    public function calculateAllThresholds($warehouseId = null, $days = 90)
    {
        // Get all items that have stock movement in the period
        $items = Item::whereHas('stockCards', function($q) use ($days) {
            $q->where('transaction_date', '>=', Carbon::now()->subDays($days));
        })->get();
        
        $warehouses = $warehouseId 
            ? [Warehouse::find($warehouseId)]
            : Warehouse::all();
        
        $updated = 0;
        
        foreach ($warehouses as $warehouse) {
            if (!$warehouse) continue;
            
            foreach ($items as $item) {
                $suggestions = $this->getSuggestions($item->id, $warehouse->id, $days);
                
                // Only apply if there's actual usage
                if ($suggestions['adu'] > 0) {
                    $this->applySuggestion($item->id, $warehouse->id, $suggestions);
                    $updated++;
                }
            }
        }
        
        return $updated;
    }
}
