<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\Warehouse;
use App\Services\Reports\DistributionAnalysisService;
use App\Services\Reports\PdfExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DistributionReportController extends Controller
{
    protected $analysisService;
    protected $pdfService;

    public function __construct(DistributionAnalysisService $analysisService, PdfExportService $pdfService)
    {
        $this->analysisService = $analysisService;
        $this->pdfService = $pdfService;
    }

    /**
     * Display distribution report with analytics
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::all();
        
        $filters = [
            'origin_warehouse_id' => $request->input('origin_warehouse_id'),
            'destination_warehouse_id' => $request->input('destination_warehouse_id'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from', Carbon::now()->subDays(90)->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
        ];
        
        // Get analysis
        $analysis = $this->analysisService->analyzeDistributions($filters);
        

        
        // Get distributions for table
        $query = Distribution::with(['origin', 'destination', 'details.item'])
            ->whereBetween('created_at', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay()
            ]);
        
        if ($filters['origin_warehouse_id']) {
            $query->where('origin_warehouse_id', $filters['origin_warehouse_id']);
        }
        
        if ($filters['destination_warehouse_id']) {
            $query->where('destination_warehouse_id', $filters['destination_warehouse_id']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        $distributions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('reports.distribution.index', compact('warehouses', 'filters', 'analysis', 'distributions'));
    }

    /**
     * Export distribution report to PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = [
            'origin_warehouse_id' => $request->input('origin_warehouse_id'),
            'destination_warehouse_id' => $request->input('destination_warehouse_id'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];
        
        $analysis = $this->analysisService->analyzeDistributions($filters);
        
        $query = Distribution::with(['origin', 'destination', 'details.item'])
            ->whereBetween('created_at', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay()
            ]);
        
        if ($filters['origin_warehouse_id']) {
            $query->where('origin_warehouse_id', $filters['origin_warehouse_id']);
        }
        
        if ($filters['destination_warehouse_id']) {
            $query->where('destination_warehouse_id', $filters['destination_warehouse_id']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        $data = [
            'analysis' => $analysis,
            'distributions' => $query->orderBy('created_at', 'desc')->get(),
        ];
        
        return $this->pdfService->generateDistributionReport($data, $filters);
    }
}
