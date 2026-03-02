<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InitialStockTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'PANDUAN'       => new Sheets\TemplateGuideSheet(),
            'TEMPLATE_STOK' => new Sheets\TemplateStockSheet(),
            'REF_KATEGORI'  => new Sheets\TemplateCategoryRefSheet(),
            'REF_SATUAN'    => new Sheets\TemplateUnitRefSheet(),
            'REF_SUPPLIER'  => new Sheets\TemplateSupplierRefSheet(),
            'REF_GUDANG'    => new Sheets\TemplateWarehouseRefSheet(),
        ];
    }
}
