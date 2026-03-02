<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Exports\InitialStockTemplateExport;
use App\Imports\InitialStockReceivingImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class InitialStockImport extends Component
{
    use WithFileUploads;

    public $file;
    public $isImporting = false;
    public $importResults = null;

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls|max:10240',
    ];

    public function downloadTemplate()
    {
        return Excel::download(new InitialStockTemplateExport, 'Template_Import_Stok_Awal_' . date('Ymd') . '.xlsx');
    }

    public function import()
    {
        $this->validate();
        $this->isImporting = true;
        $this->importResults = null;

        try {
            $importer = new InitialStockReceivingImport();
            Excel::import($importer, $this->file->getRealPath());

            $results = $importer->getResults();
            $this->importResults = [
                'status'    => 'success',
                'imported'  => $results['imported'],
                'skipped'   => $results['skipped'],
                'receivings' => $results['receivings'],
                'errors'    => $results['errors'],
            ];

            session()->flash('success', 'Import stok awal berhasil. ' . $results['imported'] . ' item tercatat.');
        } catch (\Exception $e) {
            Log::error('InitialStockImport error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->importResults = [
                'status'  => 'error',
                'message' => $e->getMessage(),
                'errors'  => [],
            ];
        } finally {
            $this->isImporting = false;
        }
    }

    public function render()
    {
        return view('livewire.inventory.initial-stock-import');
    }
}
