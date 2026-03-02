<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InitialStockReceivingImport implements WithMultipleSheets
{
    public $importedCount = 0;
    public $skippedCount = 0;
    public $receivingCount = 0;
    public $errors = [];

    protected $sheetImporter;

    public function __construct()
    {
        $this->sheetImporter = new \App\Imports\Sheets\StockReceivingSheetImport();
    }

    public function sheets(): array
    {
        return [
            'TEMPLATE_STOK' => $this->sheetImporter,
        ];
    }

    public function getResults(): array
    {
        return [
            'imported' => $this->sheetImporter->importedCount,
            'skipped' => $this->sheetImporter->skippedCount,
            'receivings' => $this->sheetImporter->receivingCount,
            'errors' => $this->sheetImporter->errors,
        ];
    }
}
