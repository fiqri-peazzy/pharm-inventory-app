<?php

namespace App\Exports\Sheets;

use App\Models\ItemUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateUnitRefSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'REF_SATUAN';
    }

    public function headings(): array
    {
        return ['Kode', 'Nama Satuan'];
    }

    public function collection()
    {
        return ItemUnit::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn($u) => [$u->code, $u->name]);
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 30];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DB2777']],
        ]);
        return [];
    }
}
