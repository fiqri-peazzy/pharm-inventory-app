<?php

namespace App\Exports\Sheets;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateWarehouseRefSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'REF_GUDANG';
    }

    public function headings(): array
    {
        return ['ID', 'Kode Gudang', 'Nama Gudang', 'Jenis'];
    }

    public function collection()
    {
        return Warehouse::where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->get()
            ->map(fn($w) => [
                $w->id,
                $w->code,
                $w->name,
                $w->is_main ? '⭐ Gudang Utama' : 'Depo/Satelit'
            ]);
    }

    public function columnWidths(): array
    {
        return ['A' => 8, 'B' => 18, 'C' => 40, 'D' => 20];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D97706']],
        ]);
        return [];
    }
}
