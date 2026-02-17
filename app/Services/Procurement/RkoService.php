<?php

namespace App\Services\Procurement;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\ItemWarehouseSetting;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RkoService
{
    /**
     * Calculate RKO (Rencana Kebutuhan Obat) for a specific warehouse.
     * Logic: RKO = (Average Usage x Lead Time) + Safety Stock - Current Stock
     */
    public function calculateRko(Warehouse $warehouse, $projectionDays = 30)
    {
        $items = Item::active()->with(['category', 'warehouseSettings' => function ($q) use ($warehouse) {
            $q->where('warehouse_id', $warehouse->id);
        }])->get();

        $rkoResults = [];

        foreach ($items as $item) {
            $setting = $item->warehouseSettings->first();

            if (!$setting) continue;

            $currentStock = ItemBatch::where('item_id', $item->id)
                ->where('warehouse_id', $warehouse->id)
                ->active()
                ->where('status', 'available') // Only consider available stock for RKO
                ->sum('current_qty');

            $avgUsage = $setting->average_daily_usage ?? 0;
            $leadTime = $setting->lead_time_days ?? 7;
            $safetyStock = $setting->safety_stock ?? ($avgUsage * 3); // Default safety stock 3 days if not set

            // Formula: RKO = (Usage during lead time) + (Usage for projection period) + Safety Stock - Stock On Hand
            $usageDuringLeadTime = $avgUsage * $leadTime;
            $usageDuringProjection = $avgUsage * $projectionDays;
            
            $totalNeed = $usageDuringLeadTime + $usageDuringProjection + $safetyStock;
            $suggestedQty = max(0, $totalNeed - $currentStock);

            if ($suggestedQty > 0 || $item->ven_classification === 'V') {
                $rkoResults[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'code' => $item->code,
                    'item_category_code' => $item->category->code ?? '',
                    'abc' => $item->abc_classification ?? 'C',
                    'ven' => $item->ven_classification ?? 'N',
                    'current_stock' => (float)$currentStock,
                    'avg_usage' => (float)$avgUsage,
                    'suggested_qty' => (float)ceil($suggestedQty),
                    'priority_score' => $this->calculatePriorityScore($item),
                    'urgency_level' => $this->getUrgencyLevel($item, $currentStock, $avgUsage),
                ];
            }
        }

        // Sort by priority score (VEN weight 10x ABC)
        usort($rkoResults, function ($a, $b) {
            return $b['priority_score'] <=> $a['priority_score'];
        });

        return $rkoResults;
    }

    private function getUrgencyLevel($item, $currentStock, $avgUsage)
    {
        if ($currentStock <= 0) return 'OUT_OF_STOCK';
        
        $daysOfStock = $avgUsage > 0 ? ($currentStock / $avgUsage) : 999;
        
        if ($daysOfStock < 3) return 'CRITICAL';
        if ($daysOfStock < 7) return 'WARNING';
        
        return 'NORMAL';
    }

    private function calculatePriorityScore($item)
    {
        $venWeights = ['V' => 100, 'E' => 50, 'N' => 10];
        $abcWeights = ['A' => 30, 'B' => 20, 'C' => 10];

        $venScore = $venWeights[$item->ven_classification] ?? 0;
        $abcScore = $abcWeights[$item->abc_classification] ?? 0;

        return $venScore + $abcScore;
    }

    /**
     * Calculate Consolidated RKO across all warehouses.
     * Useful for GD-UTAMA to plan procurement for the entire facility.
     */
    public function calculateGlobalRko($projectionDays = 30)
    {
        $items = Item::active()->with('category')->get();
        $globalResults = [];

        foreach ($items as $item) {
            $warehouses = Warehouse::all();
            $totalCurrentStock = 0;
            $totalAvgUsage = 0;
            $maxLeadTime = 0;
            $totalSafetyStock = 0;

            foreach ($warehouses as $wh) {
                $setting = ItemWarehouseSetting::where('item_id', $item->id)
                    ->where('warehouse_id', $wh->id)
                    ->first();

                if ($setting) {
                    $totalAvgUsage += $setting->average_daily_usage ?? 0;
                    $maxLeadTime = max($maxLeadTime, $setting->lead_time_days ?? 7);
                    $totalSafetyStock += $setting->safety_stock ?? (($setting->average_daily_usage ?? 0) * 3);
                }

                $totalCurrentStock += ItemBatch::where('item_id', $item->id)
                    ->where('warehouse_id', $wh->id)
                    ->active()
                    ->where('status', 'available')
                    ->sum('current_qty');
            }

            if ($totalAvgUsage <= 0 && $item->ven_classification !== 'V') continue;

            $usageDuringLeadTime = $totalAvgUsage * $maxLeadTime;
            $usageDuringProjection = $totalAvgUsage * $projectionDays;
            $totalNeed = $usageDuringLeadTime + $usageDuringProjection + $totalSafetyStock;
            
            $suggestedQty = max(0, $totalNeed - $totalCurrentStock);

            if ($suggestedQty > 0 || $item->ven_classification === 'V') {
                $globalResults[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'code' => $item->code,
                    'item_category_code' => $item->category->code ?? '',
                    'abc' => $item->abc_classification ?? 'C',
                    'ven' => $item->ven_classification ?? 'N',
                    'total_stock' => (float)$totalCurrentStock,
                    'total_avg_usage' => (float)$totalAvgUsage,
                    'suggested_qty' => (float)ceil($suggestedQty),
                    'priority_score' => $this->calculatePriorityScore($item),
                    'urgency_level' => $this->getUrgencyLevel($item, $totalCurrentStock, $totalAvgUsage),
                ];
            }
        }

        usort($globalResults, function ($a, $b) {
            return $b['priority_score'] <=> $a['priority_score'];
        });

        return $globalResults;
    }

    /**
     * Sync historical usage data for all items in a warehouse.
     */
    public function syncUsage(Warehouse $warehouse, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        // Calculate usage from StockCard (In the real system, this would be from 'prescription' or 'distribution_out' type cards)
        $usageData = \App\Models\StockCard::where('warehouse_id', $warehouse->id)
            ->where('transaction_date', '>=', $startDate)
            ->where('qty_out', '>', 0)
            ->select('item_id', DB::raw('SUM(qty_out) as total_out'))
            ->groupBy('item_id')
            ->get();

        foreach ($usageData as $usage) {
            $avgDaily = $usage->total_out / $days;
            
            ItemWarehouseSetting::updateOrCreate(
                ['item_id' => $usage->item_id, 'warehouse_id' => $warehouse->id],
                ['average_daily_usage' => $avgDaily, 'last_suggested_at' => now()]
            );
        }
    }
}
