<?php

namespace App\Imports\Sheets;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PbfSheetImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 4; // Data starts at Row 4
    }

    public function model(array $row)
    {
        if (empty($row[1])) { // Column B: KODE
            return null;
        }

        return Supplier::updateOrCreate(
            ['code' => $row[1]],
            [
                'name'    => $row[2] ?? $row[1], // Column C: NAMA PBF
                'address' => $row[3] ?? null,    // Column D: LOKASI
                'is_active' => true,
            ]
        );
    }
}
