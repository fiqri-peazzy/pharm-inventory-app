<?php

namespace App\Exports\Sheets;

use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateStockSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'TEMPLATE_STOK';
    }

    public function array(): array
    {
        $rows = [];

        // Header Row
        $rows[] = [
            'item_name',       // A - Nama item (auto-create jika belum ada)
            'category_code',   // B - Kode kategori (lihat REF_KATEGORI)
            'unit_code',       // C - Kode satuan (TAB, CAP, BOX, AMP, VIAL, dst)
            'batch_number',    // D - Nomor batch / No. SP / Nomor faktur
            'expired_date',    // E - Tanggal kadaluarsa format YYYY-MM-DD
            'supplier_code',   // F - Kode PBF (lihat REF_SUPPLIER)
            'qty_received',    // G - Jumlah stok yang diterima/masuk
            'qty_used',        // H - Jumlah stok yang sudah keluar (dari periode lalu)
            'purchase_price',  // I - Harga beli per satuan (opsional)
            'invoice_number',  // J - Nomor faktur / SP (per invoice = 1 dok penerimaan)
            'invoice_date',    // K - Tanggal faktur YYYY-MM-DD
        ];

        // Leave 50 empty rows for staff to fill. Nothing is pre-filled here on
        // purpose — a pre-filled invoice_date used to get dragged/copied down
        // by spreadsheet apps into blank rows, making the sheet confusing.
        for ($i = 0; $i < 50; $i++) {
            $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 55, // item_name
            'B' => 15, // category_code
            'C' => 12, // unit_code
            'D' => 22, // batch_number
            'E' => 16, // expired_date
            'F' => 14, // supplier_code
            'G' => 15, // qty_received
            'H' => 15, // qty_used
            'I' => 18, // purchase_price
            'J' => 28, // invoice_number
            'K' => 16, // invoice_date
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Light yellow background for data rows
        $sheet->getStyle('A2:K51')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
        ]);

        // Freeze header
        $sheet->freezePane('A2');

        return [];
    }
}
