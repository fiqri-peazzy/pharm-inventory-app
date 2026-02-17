<?php

namespace App\Services\Reports;

use App\Models\Distribution;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DistributionAnalysisService
{
    /**
     * Analyze distributions with efficiency metrics
     */
    public function analyzeDistributions($filters = [])
    {
        $dateFrom = isset($filters['date_from']) ? Carbon::parse($filters['date_from']) : Carbon::now()->subDays(30);
        $dateTo = isset($filters['date_to']) ? Carbon::parse($filters['date_to']) : Carbon::now();
        
        $query = Distribution::with(['origin', 'destination', 'details.item'])
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);
        
        if (isset($filters['origin_warehouse_id'])) {
            $query->where('origin_warehouse_id', $filters['origin_warehouse_id']);
        }
        
        if (isset($filters['destination_warehouse_id'])) {
            $query->where('destination_warehouse_id', $filters['destination_warehouse_id']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $distributions = $query->get();
        
        return [
            'summary' => $this->getSummary($distributions),
            'efficiency_metrics' => $this->calculateMetrics($distributions),
            'route_analysis' => $this->analyzeRoutes($distributions),
            'recommendations' => $this->generateInsights($distributions),
        ];
    }

    /**
     * Get summary statistics
     */
    private function getSummary($distributions)
    {
        $totalItems = 0;
        $totalValue = 0;
        
        foreach ($distributions as $dist) {
            $totalItems += $dist->details->sum('qty_sent');
            foreach ($dist->details as $detail) {
                $totalValue += ($detail->qty_sent ?? 0) * ($detail->unit_price ?? 0);
            }
        }
        
        return [
            'total_distributions' => $distributions->count(),
            'total_items_moved' => $totalItems,
            'total_value' => $totalValue,
            'by_status' => [
                'requested' => $distributions->where('status', 'requested')->count(),
                'sent' => $distributions->where('status', 'sent')->count(),
                'received' => $distributions->where('status', 'received')->count(),
            ]
        ];
    }

    /**
     * Calculate efficiency metrics
     */
    private function calculateMetrics($distributions)
    {
        $completedDistributions = $distributions->where('status', 'received');
        
        if ($completedDistributions->isEmpty()) {
            return [
                'avg_lead_time' => 0,
                'fill_rate' => 0,
                'on_time_rate' => 0,
            ];
        }
        
        // Average Lead Time (days from sent to received)
        $leadTimes = [];
        foreach ($completedDistributions as $dist) {
            if ($dist->sent_at && $dist->received_at) {
                $leadTimes[] = Carbon::parse($dist->sent_at)->diffInDays($dist->received_at);
            }
        }
        $avgLeadTime = count($leadTimes) > 0 ? array_sum($leadTimes) / count($leadTimes) : 0;
        
        // Fill Rate (% of requested qty actually received)
        $totalRequested = 0;
        $totalReceived = 0;
        foreach ($completedDistributions as $dist) {
            foreach ($dist->details as $detail) {
                $totalRequested += $detail->qty_requested ?? 0;
                $totalReceived += $detail->qty_received ?? 0;
            }
        }
        $fillRate = $totalRequested > 0 ? ($totalReceived / $totalRequested) * 100 : 0;
        
        // On-Time Delivery (assuming 3 days is expected)
        $onTimeCount = 0;
        foreach ($completedDistributions as $dist) {
            if ($dist->sent_at && $dist->received_at) {
                $leadTime = Carbon::parse($dist->sent_at)->diffInDays($dist->received_at);
                if ($leadTime <= 3) {
                    $onTimeCount++;
                }
            }
        }
        $onTimeRate = $completedDistributions->count() > 0 
            ? ($onTimeCount / $completedDistributions->count()) * 100 
            : 0;
        
        return [
            'avg_lead_time' => round($avgLeadTime, 1),
            'fill_rate' => round($fillRate, 1),
            'on_time_rate' => round($onTimeRate, 1),
        ];
    }

    /**
     * Analyze distribution routes
     */
    private function analyzeRoutes($distributions)
    {
        $routes = [];
        
        foreach ($distributions as $dist) {
            $routeKey = $dist->origin_warehouse_id . '-' . $dist->destination_warehouse_id;
            
            if (!isset($routes[$routeKey])) {
                $routes[$routeKey] = [
                    'origin' => $dist->origin->name,
                    'destination' => $dist->destination->name,
                    'count' => 0,
                    'total_items' => 0,
                    'avg_lead_time' => 0,
                    'lead_times' => []
                ];
            }
            
            $routes[$routeKey]['count']++;
            $routes[$routeKey]['total_items'] += $dist->details->sum('qty_sent');
            
            if ($dist->sent_at && $dist->received_at) {
                $routes[$routeKey]['lead_times'][] = Carbon::parse($dist->sent_at)->diffInDays($dist->received_at);
            }
        }
        
        // Calculate average lead time per route
        foreach ($routes as $key => $route) {
            if (count($route['lead_times']) > 0) {
                $routes[$key]['avg_lead_time'] = round(array_sum($route['lead_times']) / count($route['lead_times']), 1);
            }
            unset($routes[$key]['lead_times']);
        }
        
        // Sort by count (most active routes first)
        usort($routes, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return $routes;
    }

    /**
     * Generate intelligent insights
     */
    private function generateInsights($distributions)
    {
        $insights = [];
        
        // Check for pending requests >5 days
        $oldPending = $distributions->filter(function($dist) {
            return $dist->status === 'requested' && 
                   Carbon::parse($dist->requested_at)->diffInDays(Carbon::now()) > 5;
        });
        
        if ($oldPending->count() > 0) {
            $insights[] = [
                'type' => 'pending',
                'priority' => 'high',
                'icon' => 'alert-triangle',
                'message' => "{$oldPending->count()} pending requests >5 days old - Follow up needed",
            ];
        }
        
        // Check for frequent requesters
        $destinationCounts = [];
        foreach ($distributions as $dist) {
            $destId = $dist->destination_warehouse_id;
            if (!isset($destinationCounts[$destId])) {
                $destinationCounts[$destId] = [
                    'name' => $dist->destination->name,
                    'count' => 0
                ];
            }
            $destinationCounts[$destId]['count']++;
        }
        
        foreach ($destinationCounts as $dest) {
            if ($dest['count'] >= 10) {
                $insights[] = [
                    'type' => 'frequent',
                    'priority' => 'medium',
                    'icon' => 'trending-up',
                    'message' => "{$dest['name']} requests frequently ({$dest['count']}x) - Consider increasing par level",
                ];
            }
        }
        
        // Check for distribution patterns by day of week
        $dayPattern = [];
        foreach ($distributions as $dist) {
            $day = Carbon::parse($dist->created_at)->dayOfWeek;
            if (!isset($dayPattern[$day])) {
                $dayPattern[$day] = 0;
            }
            $dayPattern[$day]++;
        }
        
        if (count($dayPattern) > 0) {
            arsort($dayPattern);
            $peakDay = array_key_first($dayPattern);
            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            
            if ($dayPattern[$peakDay] > count($distributions) * 0.3) {
                $insights[] = [
                    'type' => 'pattern',
                    'priority' => 'low',
                    'icon' => 'calendar',
                    'message' => "Distribution peaks on {$dayNames[$peakDay]}s - Plan resources accordingly",
                ];
            }
        }
        
        return $insights;
    }
}
