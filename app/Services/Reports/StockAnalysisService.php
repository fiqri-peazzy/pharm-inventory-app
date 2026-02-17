<?php

namespace App\Services\Reports;

use App\Models\Item;
use App\Models\StockCard;
use App\Models\ItemBatch;
use App\Models\ItemWarehouseSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockAnalysisService
{
    /**
     * Analyze item stock with intelligent insights
     */
    public function analyzeItem($itemId, $warehouseId, $dateFrom = null, $dateTo = null)
    {
        $item = Item::with(['unit', 'category'])->findOrFail($itemId);
        
        $dateFrom = $dateFrom ? Carbon::parse($dateFrom) : Carbon::now()->subDays(90);
        $dateTo = $dateTo ? Carbon::parse($dateTo) : Carbon::now();
        
        return [
            'item' => $item,
            'abc_class' => $this->calculateABCClass($itemId, $warehouseId),
            'movement_pattern' => $this->analyzeMovement($itemId, $warehouseId, $dateFrom, $dateTo),
            'health_score' => $this->calculateHealthScore($itemId, $warehouseId),
            'recommendations' => $this->generateRecommendations($itemId, $warehouseId),
            'trend_data' => $this->getTrendData($itemId, $warehouseId, $dateFrom, $dateTo),
            'current_stock' => $this->getCurrentStock($itemId, $warehouseId),
        ];
    }

    /**
     * Calculate ABC Classification based on value
     */
    private function calculateABCClass($itemId, $warehouseId)
    {
        // Get all items with their total value
        $items = ItemBatch::select('item_id', DB::raw('SUM(current_qty * purchase_price) as total_value'))
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->groupBy('item_id')
            ->orderByDesc('total_value')
            ->get();
        
        $totalValue = $items->sum('total_value');
        $cumulativeValue = 0;
        $rank = 0;
        
        foreach ($items as $index => $item) {
            $rank++;
            $cumulativeValue += $item->total_value;
            $percentage = ($cumulativeValue / $totalValue) * 100;
            
            if ($item->item_id == $itemId) {
                if ($percentage <= 80) {
                    return ['class' => 'A', 'description' => 'High Value Item (Top 20%)', 'color' => 'red'];
                } elseif ($percentage <= 95) {
                    return ['class' => 'B', 'description' => 'Medium Value Item (Middle 30%)', 'color' => 'amber'];
                } else {
                    return ['class' => 'C', 'description' => 'Low Value Item (Bottom 50%)', 'color' => 'green'];
                }
            }
        }
        
        return ['class' => 'C', 'description' => 'Low Value Item', 'color' => 'green'];
    }

    /**
     * Analyze stock movement pattern
     */
    private function analyzeMovement($itemId, $warehouseId, $dateFrom, $dateTo)
    {
        $days = $dateFrom->diffInDays($dateTo);
        
        $totalOut = StockCard::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->sum('qty_out');
        
        $adu = $days > 0 ? $totalOut / $days : 0;
        
        // Check for dead stock (no movement in 90+ days)
        $lastMovement = StockCard::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('qty_out', '>', 0)
            ->latest('transaction_date')
            ->first();
        
        $daysSinceLastMovement = $lastMovement 
            ? Carbon::now()->diffInDays($lastMovement->transaction_date)
            : 999;
        
        if ($daysSinceLastMovement >= 90) {
            return [
                'pattern' => 'Dead Stock',
                'adu' => round($adu, 2),
                'description' => 'No movement in 90+ days - Consider disposal',
                'color' => 'gray',
                'priority' => 'low'
            ];
        } elseif ($adu >= 10) {
            return [
                'pattern' => 'Fast Moving',
                'adu' => round($adu, 2),
                'description' => 'High demand - Frequent reorder needed',
                'color' => 'green',
                'priority' => 'high'
            ];
        } elseif ($adu >= 3) {
            return [
                'pattern' => 'Medium Moving',
                'adu' => round($adu, 2),
                'description' => 'Moderate demand - Regular monitoring',
                'color' => 'blue',
                'priority' => 'medium'
            ];
        } else {
            return [
                'pattern' => 'Slow Moving',
                'adu' => round($adu, 2),
                'description' => 'Low demand - Risk of expiry',
                'color' => 'amber',
                'priority' => 'low'
            ];
        }
    }

    /**
     * Calculate stock health score
     */
    private function calculateHealthScore($itemId, $warehouseId)
    {
        $currentStock = $this->getCurrentStock($itemId, $warehouseId);
        
        $setting = ItemWarehouseSetting::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$setting || !$setting->reorder_point) {
            return [
                'score' => 0,
                'status' => 'Unknown',
                'description' => 'No threshold configured',
                'color' => 'gray'
            ];
        }
        
        $optimalStock = $setting->reorder_point;
        $score = $optimalStock > 0 ? ($currentStock / $optimalStock) * 100 : 0;
        
        if ($score >= 100) {
            return [
                'score' => round($score, 1),
                'status' => 'Overstocked',
                'description' => 'Risk: Expiry & capital tied up',
                'color' => 'purple'
            ];
        } elseif ($score >= 70) {
            return [
                'score' => round($score, 1),
                'status' => 'Healthy',
                'description' => 'Stock level optimal',
                'color' => 'green'
            ];
        } elseif ($score >= 50) {
            return [
                'score' => round($score, 1),
                'status' => 'Warning',
                'description' => 'Approaching reorder point',
                'color' => 'amber'
            ];
        } else {
            return [
                'score' => round($score, 1),
                'status' => 'Critical',
                'description' => 'Urgent reorder needed',
                'color' => 'red'
            ];
        }
    }

    /**
     * Generate intelligent recommendations
     */
    private function generateRecommendations($itemId, $warehouseId)
    {
        $recommendations = [];
        $currentStock = $this->getCurrentStock($itemId, $warehouseId);
        
        $setting = ItemWarehouseSetting::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        // Check reorder point
        if ($setting && $setting->reorder_point > 0 && $currentStock < $setting->reorder_point) {
            $orderQty = $setting->max_stock - $currentStock;
            $recommendations[] = [
                'type' => 'reorder',
                'priority' => 'high',
                'icon' => 'alert-circle',
                'message' => "URGENT: Reorder needed - Stock {$currentStock} below RP {$setting->reorder_point}",
                'action' => "Order {$orderQty} units to reach max stock"
            ];
        }
        
        // Check expiry
        $nearExpiry = ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addDays(60)])
            ->get();
        
        foreach ($nearExpiry as $batch) {
            $daysToExpiry = Carbon::now()->diffInDays($batch->expired_date);
            $recommendations[] = [
                'type' => 'expiry',
                'priority' => $daysToExpiry < 30 ? 'high' : 'medium',
                'icon' => 'clock',
                'message' => "Batch {$batch->batch_number} expiring in {$daysToExpiry} days - {$batch->current_qty} units",
                'action' => 'Use FEFO - Dispense this batch first'
            ];
        }
        
        // Check overstock
        if ($setting && $setting->max_stock > 0 && $currentStock > $setting->max_stock * 1.2) {
            $excess = $currentStock - $setting->max_stock;
            $value = $excess * ItemBatch::where('item_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->where('is_active', true)
                ->avg('purchase_price');
            
            $recommendations[] = [
                'type' => 'overstock',
                'priority' => 'medium',
                'icon' => 'trending-up',
                'message' => "Overstocked - {$excess} units excess (Rp " . number_format($value, 0, ',', '.') . " tied up)",
                'action' => 'Use existing stock before ordering more'
            ];
        }
        
        // Check slow moving
        $movement = $this->analyzeMovement($itemId, $warehouseId, Carbon::now()->subDays(90), Carbon::now());
        if ($movement['pattern'] === 'Slow Moving' && $currentStock > 100) {
            $recommendations[] = [
                'type' => 'slow_moving',
                'priority' => 'low',
                'icon' => 'trending-down',
                'message' => "Slow moving item (ADU: {$movement['adu']}/day) with high stock",
                'action' => 'Consider reducing future order quantities'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Get trend data for charts
     */
    private function getTrendData($itemId, $warehouseId, $dateFrom, $dateTo)
    {
        $cards = StockCard::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->orderBy('transaction_date')
            ->get();
        
        $trendData = [];
        foreach ($cards as $card) {
            $date = $card->transaction_date->format('Y-m-d');
            if (!isset($trendData[$date])) {
                $trendData[$date] = [
                    'date' => $card->transaction_date->format('d M'),
                    'in' => 0,
                    'out' => 0,
                    'stock' => $card->last_stock
                ];
            }
            $trendData[$date]['in'] += $card->qty_in;
            $trendData[$date]['out'] += $card->qty_out;
            $trendData[$date]['stock'] = $card->last_stock;
        }
        
        return array_values($trendData);
    }

    /**
     * Get current stock
     */
    private function getCurrentStock($itemId, $warehouseId)
    {
        return ItemBatch::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->sum('current_qty');
    }
}
