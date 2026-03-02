<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InitialStockImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'KODE PBF' => new \App\Imports\Sheets\PbfSheetImport(),
            'BLUD BHP FIX' => new \App\Imports\Sheets\StockSheetImport('bhp'),
            'BLUD OBAT' => new \App\Imports\Sheets\StockSheetImport('obat'),
        ];
    }
}
