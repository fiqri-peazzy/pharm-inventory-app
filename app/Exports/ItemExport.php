<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Return empty collection for template
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'kode',
            'nama',
            'nama_generik',
            'kode_kategori',
            'kode_satuan',
            'nie',
            'pabrikan',
            'stok_min',
            'stok_max',
            'resep',
            'penyimpanan'
        ];
    }
}
