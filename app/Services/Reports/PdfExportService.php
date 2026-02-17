<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportService
{
    /**
     * Generate Stock Report PDF
     */
    public function generateStockReport($data, $filters)
    {
        $pdf = PDF::loadView('reports.stock.pdf', compact('data', 'filters'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'laporan-stok-' . ($data['item']->code ?? 'all') . '-' . date('Ymd-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate Distribution Report PDF
     */
    public function generateDistributionReport($data, $filters)
    {
        $pdf = PDF::loadView('reports.distribution.pdf', compact('data', 'filters'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'laporan-distribusi-' . date('Ymd-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
