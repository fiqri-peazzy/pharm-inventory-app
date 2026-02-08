<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find or create category/unit if they don't exist (flexible)
        $category = ItemCategory::where('code', $row['kode_kategori'])->first();
        $unit = ItemUnit::where('code', $row['kode_satuan'])->first();

        if (!$category || !$unit) {
            return null; // Skip if invalid ref
        }

        return new Item([
            'code'             => $row['kode'],
            'name'             => $row['nama'],
            'generic_name'     => $row['nama_generik'] ?? null,
            'item_category_id' => $category->id,
            'item_unit_id'     => $unit->id,
            'nie_number'       => $row['nie'] ?? null,
            'manufacturer'     => $row['pabrikan'] ?? null,
            'is_prescription'  => ($row['resep'] ?? 'tidak') === 'ya',
            'storage_condition'=> $row['penyimpanan'] ?? 'suhu_ruang',
            'is_active'        => true,
            'created_by'       => auth()->id(),
            'updated_by'       => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'kode'          => 'required|unique:items,code',
            'nama'          => 'required',
            'kode_kategori' => 'required|exists:item_categories,code',
            'kode_satuan'   => 'required|exists:item_units,code',
        ];
    }
}
