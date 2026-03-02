<?php

namespace App\Exports\Sheets;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateItemRefSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'REF_ITEM';
    }

    public function headings(): array
    {
        return ['ID', 'Kode Item', 'Nama Item', 'Satuan', 'Kategori'];
    }

    public function collection()
    {
        return Item::with(['unit', 'category'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($item) => [
                $item->id,
                $item->code,
                $item->name,
                $item->unit?->code ?? '-',
                $item->category?->name ?? '-',
            ]);
    }

    public function columnWidths(): array
    {
        return ['A' => 8, 'B' => 18, 'C' => 55, 'D' => 12, 'E' => 25];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
        ]);
        return [];
    }
}
