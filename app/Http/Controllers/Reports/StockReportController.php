<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockCard;
use App\Services\Reports\StockAnalysisService;
use App\Services\Reports\PdfExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockReportController extends Controller
{
    protected $analysisService;
    protected $pdfService;

    public function __construct(StockAnalysisService $analysisService, PdfExportService $pdfService)
    {
        $this->analysisService = $analysisService;
        $this->pdfService = $pdfService;
    }

    /**
     * Display stock report with filters and analysis
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::all();
        $items = Item::with('unit')->orderBy('name')->get();
        
        // Default filters
        $filters = [
            'item_id' => $request->input('item_id'),
            'warehouse_id' => $request->input('warehouse_id', $warehouses->first()->id ?? null),
            'date_from' => $request->input('date_from', Carbon::now()->subDays(30)->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'transaction_type' => $request->input('transaction_type'),
        ];
        
        $data = null;
        $stockCards = collect();
        
        // If item is selected, get analysis and stock cards
        if ($filters['item_id'] && $filters['warehouse_id']) {
            $data = $this->analysisService->analyzeItem(
                $filters['item_id'],
                $filters['warehouse_id'],
                $filters['date_from'],
                $filters['date_to']
            );
            
            // Get stock card history
            $query = StockCard::with(['item.unit', 'warehouse', 'batch'])
                ->where('item_id', $filters['item_id'])
                ->where('warehouse_id', $filters['warehouse_id'])
                ->whereBetween('transaction_date', [
                    Carbon::parse($filters['date_from']),
                    Carbon::parse($filters['date_to'])
                ]);
            
            if ($filters['transaction_type']) {
                $query->where('transaction_type', $filters['transaction_type']);
            }
            
            $stockCards = $query->orderBy('transaction_date', 'desc')->paginate(50);
        }
        
        return view('reports.stock.index', compact('warehouses', 'items', 'filters', 'data', 'stockCards'));
    }

    /**
     * Export stock report to PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = [
            'item_id' => $request->input('item_id'),
            'warehouse_id' => $request->input('warehouse_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'transaction_type' => $request->input('transaction_type'),
        ];
        
        if (!$filters['item_id'] || !$filters['warehouse_id']) {
            return back()->with('error', 'Pilih barang dan gudang terlebih dahulu');
        }
        
        $data = $this->analysisService->analyzeItem(
            $filters['item_id'],
            $filters['warehouse_id'],
            $filters['date_from'],
            $filters['date_to']
        );
        
        $query = StockCard::with(['item.unit', 'warehouse', 'batch'])
            ->where('item_id', $filters['item_id'])
            ->where('warehouse_id', $filters['warehouse_id'])
            ->whereBetween('transaction_date', [
                Carbon::parse($filters['date_from']),
                Carbon::parse($filters['date_to'])
            ]);
        
        if ($filters['transaction_type']) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        
        $data['stockCards'] = $query->orderBy('transaction_date')->get();
        $data['warehouse'] = Warehouse::find($filters['warehouse_id']);
        
        return $this->pdfService->generateStockReport($data, $filters);
    }
}
