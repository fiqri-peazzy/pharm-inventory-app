<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Exports\InitialStockTemplateExport;
use App\Exports\ImportResultReportExport;
use App\Imports\InitialStockReceivingImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class InitialStockImport extends Component
{
    use WithFileUploads;

    public $file;
    public $isImporting = false;
    public $isValidating = false;
    public $importResults = null;

    /**
     * Result of the dry-run validation pass, shown to the user as a preview
     * table before anything is actually written to the database.
     */
    public $previewResults = null;

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls|max:10240',
    ];

    public function updatedFile()
    {
        // A newly chosen file invalidates any previous preview/result.
        $this->previewResults = null;
        $this->importResults = null;
    }

    public function downloadTemplate()
    {
        return Excel::download(new InitialStockTemplateExport, 'Template_Import_Stok_Awal_' . date('Ymd') . '.xlsx');
    }

    public function validateData()
    {
        $this->validate();
        $this->isValidating = true;
        $this->previewResults = null;
        $this->importResults = null;

        try {
            $importer = new InitialStockReceivingImport(dryRun: true);
            Excel::import($importer, $this->file->getRealPath());

            $results = $importer->getResults();
            $this->previewResults = [
                'status' => 'success',
                'valid' => $results['imported'],
                'skipped' => $results['skipped'],
                'errors' => $results['errors'],
                'rowResults' => $results['rowResults'],
            ];
        } catch (\Exception $e) {
            Log::error('InitialStockImport validate error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->previewResults = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        } finally {
            $this->isValidating = false;
        }
    }

    public function import()
    {
        $this->validate();
        $this->isImporting = true;
        $this->importResults = null;

        try {
            $importer = new InitialStockReceivingImport(dryRun: false);
            Excel::import($importer, $this->file->getRealPath());

            $results = $importer->getResults();
            $this->importResults = [
                'status'    => 'success',
                'imported'  => $results['imported'],
                'skipped'   => $results['skipped'],
                'receivings' => $results['receivings'],
                'errors'    => $results['errors'],
                'rowResults' => $results['rowResults'],
            ];
            $this->previewResults = null;

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

    public function downloadReport()
    {
        $rowResults = $this->importResults['rowResults'] ?? $this->previewResults['rowResults'] ?? [];

        if (empty($rowResults)) {
            return;
        }

        return Excel::download(new ImportResultReportExport($rowResults), 'Laporan_Import_Stok_Awal_' . date('Ymd_His') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.inventory.initial-stock-import');
    }
}
