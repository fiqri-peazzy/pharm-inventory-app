<?php

namespace App\Exports\Sheets;

use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateCategoryRefSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'REF_KATEGORI';
    }

    public function headings(): array
    {
        return ['Kode', 'Nama Kategori', 'Tipe', 'Keterangan'];
    }

    public function collection()
    {
        $descriptions = [
            'OB'   => 'Obat Bebas (Over The Counter)',
            'OBT'  => 'Obat Bebas Terbatas',
            'OK'   => 'Obat Keras (resep dokter)',
            'PSI'  => 'Psikotropika (perlu izin khusus)',
            'NAR'  => 'Narkotika (perlu izin khusus)',
            'BMHP' => 'Bahan Medis Habis Pakai (selang, kateter, dll)',
            'ALKES' => 'Alat Kesehatan non-habis pakai',
        ];

        return ItemCategory::orderBy('type')->orderBy('code')
            ->get()
            ->map(fn($c) => [
                $c->code,
                $c->name,
                strtoupper($c->type),
                $descriptions[$c->code] ?? '-',
            ]);
    }

    public function columnWidths(): array
    {
        return ['A' => 10, 'B' => 35, 'C' => 10, 'D' => 50];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
        ]);
        return [];
    }
}
