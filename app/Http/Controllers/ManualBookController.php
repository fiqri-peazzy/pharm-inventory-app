<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ManualBookController extends Controller
{
    /**
     * Preview manual book PDF in browser (inline).
     */
    public function view(): Response
    {
        $pdf = $this->generatePdf();

        return $pdf->stream('manual-book.pdf');
    }

    /**
     * Download manual book PDF.
     */
    public function download(): Response
    {
        $pdf = $this->generatePdf();

        return $pdf->download('Manual-Book-Medivault.pdf');
    }

    /**
     * Generate the PDF using DomPDF.
     */
    private function generatePdf()
    {
        $data = [
            'generated_at' => now()->translatedFormat('d F Y'),
            'hospital_name' => 'RSUD Bumi Panua',
            'app_name' => 'Medivault',
            'version' => '1.0',
        ];

        $pdf = Pdf::loadView('pdf.manual-book', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'sans-serif',
                'dpi' => 96,
            ]);

        return $pdf;
    }
}
