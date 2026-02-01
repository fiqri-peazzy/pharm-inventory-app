<?php

namespace App\Imports;

use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemCategoryImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new ItemCategory([
            'code'       => $row['kode'],
            'name'       => $row['nama'],
            'type'       => $row['tipe'] ?? 'obat',
            'is_active'  => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|unique:item_categories,code',
            'nama' => 'required',
        ];
    }
}
