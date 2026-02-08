<?php

namespace App\Services;

use App\Models\Item;
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
        $leadTime = 7; // days
        $safetyFactor = 1.5; // multiplier for safety stock (50% buffer)

        // Min Stock = (ADU * Lead Time) * Safety Factor
        $suggestedMin = ceil(($adu * $leadTime) * $safetyFactor);
        
        // Max Stock = Min Stock * 3 (Example multiplier)
        $suggestedMax = $suggestedMin > 0 ? $suggestedMin * 3 : 100;

        // Alternative: 20% of Max Stock (Safety Buffer)
        $min20Percent = ceil($suggestedMax * 0.2);

        return [
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'adu' => round($adu, 2),
            'suggested_min' => (int) $suggestedMin,
            'suggested_max' => (int) $suggestedMax,
            'suggested_min_20percent' => (int) $min20Percent,
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
                'average_daily_usage' => $thresholds['adu'],
                'usage_rate_per_day' => $thresholds['adu'],
                'last_suggested_at' => now(),
            ]
        );
    }
}
