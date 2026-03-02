<?php

namespace App\Exports\Sheets;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateSupplierRefSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'REF_SUPPLIER';
    }

    public function headings(): array
    {
        return ['ID', 'Kode PBF', 'Nama PBF'];
    }

    public function collection()
    {
        return Supplier::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [$s->id, $s->code, $s->name]);
    }

    public function columnWidths(): array
    {
        return ['A' => 8, 'B' => 15, 'C' => 50];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
        ]);
        return [];
    }
}
