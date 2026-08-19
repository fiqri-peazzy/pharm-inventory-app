<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ImportResultReportExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $rowResults)
    {
    }

    public function title(): string
    {
        return 'HASIL_IMPORT';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Baris Excel', 'Nama Item', 'Status', 'Keterangan'];

        foreach ($this->rowResults as $r) {
            $rows[] = [
                $r['row'] ?? '-',
                $r['item_name'] ?? '-',
                ($r['status'] ?? '') === 'success' ? 'BERHASIL' : 'DILEWATI',
                $r['reason'] ?? '',
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 45, 'C' => 14, 'D' => 70];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->freezePane('A2');

        $rowCount = count($this->rowResults);
        foreach ($this->rowResults as $i => $r) {
            $rowNum = $i + 2;
            if (($r['status'] ?? '') === 'skipped') {
                $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                ]);
            }
        }

        return [];
    }
}
