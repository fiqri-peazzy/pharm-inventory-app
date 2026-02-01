<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemCategoryExport implements FromCollection, WithHeadings
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
            'tipe'
        ];
    }
}
