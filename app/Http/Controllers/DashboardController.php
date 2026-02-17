<?php

namespace App\Http\Controllers;

use App\Models\ItemBatch;
use App\Models\StockCard;
use App\Models\Prescription;
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

        // 4. Prescription Statistics
        $prescriptionStats = [
            'total' => Prescription::count(),
            'rj' => Prescription::where('patient_type', 'rj')->count(),
            'ri' => Prescription::where('patient_type', 'ri')->count(),
            'queued' => Prescription::where('status', 'submitted')->count(),
            'processing' => Prescription::where('status', 'processing')->count(),
            'completed' => Prescription::where('status', 'completed')->count(),
            'umum' => Prescription::where('payer_type', 'umum')->count(),
            'bpjs' => Prescription::where('payer_type', 'bpjs')->count(),
            'asuransi' => Prescription::where('payer_type', 'asuransi_lain')->count(),
            'unpaid' => Prescription::where('payment_status', 'unpaid')->count(),
            'paid' => Prescription::where('payment_status', 'paid')->count(),
        ];

        return view('dashboard', [
            'totalStock' => $totalStock,
            'nearExpiredCount' => $nearExpiredCount,
            'recentActivities' => $recentActivities,
            'prescriptionStats' => $prescriptionStats
        ]);
    }
}
