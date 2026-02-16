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
    public function calculateRko(Warehouse $warehouse)
    {
        $items = Item::active()->with(['warehouseSettings' => function($q) use ($warehouse) {
            $q->where('warehouse_id', $warehouse->id);
        }])->get();

        $rkoResults = [];

        foreach ($items as $item) {
            $setting = $item->warehouseSettings->first();
            
            if (!$setting) continue;

            $currentStock = ItemBatch::where('item_id', $item->id)
                ->where('warehouse_id', $warehouse->id)
                ->active()
                ->sum('current_qty');

            $avgUsage = $setting->average_daily_usage ?? 0;
            $leadTime = $setting->lead_time_days ?? 7;
            $safetyStock = $setting->safety_stock ?? 0;

            // Projection for 30 days (standard RKO usually for a month or more)
            $needs = ($avgUsage * 30) + ($avgUsage * $leadTime) + $safetyStock;
            $toOrder = max(0, $needs - $currentStock);

            if ($toOrder > 0) {
                $rkoResults[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'code' => $item->code,
                    'abc' => $item->abc_classification,
                    'ven' => $item->ven_classification,
                    'current_stock' => (float)$currentStock,
                    'avg_usage' => (float)$avgUsage,
                    'suggested_qty' => (float)$toOrder,
                    'priority' => $this->calculatePriority($item),
                ];
            }
        }

        // Sort by priority (VEN then ABC)
        usort($rkoResults, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return $rkoResults;
    }

    /**
     * Priority Logic: VEN (V=3, E=2, N=1) and ABC (A=3, B=2, C=1)
     */
    private function calculatePriority($item)
    {
        $venMap = ['V' => 30, 'E' => 20, 'N' => 10];
        $abcMap = ['A' => 3, 'B' => 2, 'C' => 1];

        $venScore = $venMap[$item->ven_classification] ?? 0;
        $abcScore = $abcMap[$item->abc_classification] ?? 0;

        return $venScore + $abcScore;
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
