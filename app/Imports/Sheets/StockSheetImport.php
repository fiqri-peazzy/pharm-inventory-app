<?php

namespace App\Imports\Sheets;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Supplier;
use App\Models\StockCard;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class StockSheetImport implements ToModel, WithStartRow
{
    protected $categoryType;
    protected $warehouseId;

    public function __construct($categoryType)
    {
        $this->categoryType = $categoryType; // 'bhp' or 'obat'
        // Default to first warehouse
        $this->warehouseId = Warehouse::first()?->id ?? 1;
    }

    public function startRow(): int
    {
        return 11; // Data starts at Row 11
    }

    public function model(array $row)
    {
        if (empty($row[3])) { // Column D: Uraian/Item Name
            return null;
        }

        $itemName = trim($row[3]);
        $unitName = trim($row[5] ?? 'PCS');
        $batchNumber = $row[1] ?? 'INIT-' . Str::random(5);
        $pbfCode = $row[2] ?? null;
        $qtyIn = floatval($row[4] ?? 0);

        // Sum usage from Jan to Sept (Columns G to O / Indexes 6 to 14)
        $totalUsage = 0;
        for ($i = 6; $i <= 14; $i++) {
            if (isset($row[$i])) {
                $totalUsage += floatval($row[$i]);
            }
        }

        $currentQty = $qtyIn - $totalUsage;
        if ($currentQty < 0) $currentQty = 0;

        // --- NEW: Parse Expiry Date from Column Y (index 24) ---
        $expiredDate = now()->addYears(2); // Default fallback
        if (!empty($row[24])) {
            $val = $row[24];
            if (is_numeric($val)) {
                // Handle Excel Serial Date
                try {
                    $expiredDate = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val));
                } catch (\Exception $e) {
                    $expiredDate = now()->addYears(2);
                }
            } else {
                // Handle String format "Apr-27"
                try {
                    $dateStr = trim($val);
                    $expiredDate = \Carbon\Carbon::createFromFormat('M-y', $dateStr)->endOfMonth();
                } catch (\Exception $e) {
                    try {
                        $expiredDate = \Carbon\Carbon::parse($val);
                    } catch (\Exception $e2) {
                        $expiredDate = now()->addYears(2);
                    }
                }
            }
        }

        // 1. Ensure Category
        $categoryName = $this->categoryType === 'obat' ? 'Obat' : 'BMHP';
        $category = ItemCategory::firstOrCreate(
            ['code' => strtoupper($this->categoryType)],
            ['name' => $categoryName]
        );

        // 2. Ensure Unit
        $unit = ItemUnit::firstOrCreate(
            ['code' => strtoupper($unitName)],
            ['name' => $unitName]
        );

        // 3. Ensure Item
        $item = Item::firstOrCreate(
            ['name' => $itemName],
            [
                'code' => 'ITEM-' . Str::upper(Str::random(8)),
                'item_category_id' => $category->getKey(),
                'item_unit_id' => $unit->getKey(),
                'is_prescription' => ($this->categoryType === 'obat'),
                'is_active' => true,
                'created_by' => Auth::id() ?? 1,
            ]
        );

        // 4. Find Supplier
        $supplier = null;
        if ($pbfCode) {
            $supplier = Supplier::where('code', $pbfCode)->first();
        }

        // 5. Ensure Main Warehouse
        $mainWarehouse = Warehouse::where('name', 'like', '%Gudang Utama%')->first()
            ?? Warehouse::first()
            ?? (object)['id' => 1];
        $targetWarehouseId = $mainWarehouse->id;

        // 6. Create Batch
        $batch = ItemBatch::updateOrCreate(
            [
                'item_id' => $item->getKey(),
                'batch_number' => $batchNumber,
                'warehouse_id' => $targetWarehouseId,
            ],
            [
                'supplier_id' => $supplier?->id,
                'expired_date' => $expiredDate->format('Y-m-d'),
                'initial_qty' => $qtyIn,
                'current_qty' => $currentQty,
                'purchase_price' => 0.00,
                'status' => 'available',
                'is_active' => true,
            ]
        );

        // 7. Record Initial Stock Card
        StockCard::create([
            'item_id' => $item->getKey(),
            'warehouse_id' => $targetWarehouseId,
            'item_batch_id' => $batch->getKey(),
            'transaction_date' => now()->format('Y-m-d'),
            'transaction_type' => 'adjustment',
            'reference_type' => 'initial_import',
            'reference_id' => 0,
            'qty_in' => $currentQty,
            'qty_out' => 0,
            'last_stock' => $currentQty,
            'notes' => 'Import saldo awal dari Excel hospital data',
        ]);

        return null; // Return null because we handled multiple related models
    }
}
