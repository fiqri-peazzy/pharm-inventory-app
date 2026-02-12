<?php

namespace App\Http\Controllers;

use App\Models\ItemBatch;
use App\Models\StockCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Stock Quantity
        $totalStock = ItemBatch::where('is_active', true)->sum('current_qty');

        // 2. Near Expired (Next 6 months)
        $nearExpiredCount = ItemBatch::where('is_active', true)
            ->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addMonths(6)])
            ->count();

        // 3. Recent Activities (excluding zero-variance opnames)
        $recentActivities = StockCard::with(['item', 'warehouse'])
            ->where(function ($q) {
                $q->where('transaction_type', '!=', 'stock_opname')
                  ->orWhere('qty_in', '>', 0)
                  ->orWhere('qty_out', '>', 0);
            })
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalStock' => $totalStock,
            'nearExpiredCount' => $nearExpiredCount,
            'recentActivities' => $recentActivities
        ]);
    }
}
