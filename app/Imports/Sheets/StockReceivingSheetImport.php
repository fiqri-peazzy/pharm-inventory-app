<?php

namespace App\Imports\Sheets;

use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Receiving;
use App\Models\ReceivingDetail;
use App\Models\StockCard;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StockReceivingSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public int $receivingCount = 0;
    public array $errors = [];

    // Cache for performance
    private array $categoryCache = [];
    private array $unitCache = [];
    private array $supplierCache = [];
    private array $itemCache = [];
    private ?int $mainWarehouseId = null;

    public function collection(Collection $rows)
    {
        // Preload caches
        $this->preloadCaches();

        // Filter rows that have qty_received > 0
        $validRows = $rows->filter(function ($row) {
            return !empty($row['item_name'])
                && !empty($row['invoice_number'])
                && intval($row['qty_received'] ?? 0) > 0;
        });

        if ($validRows->isEmpty()) {
            $this->errors[] = 'Tidak ada data valid ditemukan. Pastikan kolom item_name dan qty_received terisi.';
            return;
        }

        // Group by invoice_number
        $grouped = $validRows->groupBy('invoice_number');

        foreach ($grouped as $invoiceNumber => $lines) {
            try {
                DB::transaction(function () use ($invoiceNumber, $lines) {
                    $firstLine   = $lines->first();
                    $supplierId  = $this->resolveSupplier($firstLine['supplier_code'] ?? '');
                    $warehouseId = $this->mainWarehouseId;
                    $invoiceDate = $this->parseDate($firstLine['invoice_date'] ?? null, now()->format('Y-m-d'));

                    if (!$supplierId) {
                        $code = $firstLine['supplier_code'] ?? 'N/A';
                        $this->errors[] = "Invoice '$invoiceNumber': Kode supplier '$code' tidak ditemukan. Baris diabaikan.";
                        $this->skippedCount += $lines->count();
                        return;
                    }

                    $totalAmount = $lines->sum(fn($l) => floatval($l['purchase_price'] ?? 0) * intval($l['qty_received'] ?? 0));

                    // Create Receiving document
                    $receiving = Receiving::create([
                        'receiving_number' => 'INIT-' . date('Ymd') . '-' . str_pad($this->receivingCount + 1, 4, '0', STR_PAD_LEFT),
                        'supplier_id'      => $supplierId,
                        'warehouse_id'     => $warehouseId,
                        'receiving_date'   => $invoiceDate,
                        'invoice_number'   => $invoiceNumber,
                        'invoice_date'     => $invoiceDate,
                        'total_amount'     => $totalAmount,
                        'ppn_amount'       => 0,
                        'grand_total'      => $totalAmount,
                        'notes'            => 'Import saldo awal stok (sistem lama)',
                        'status'           => 'posted',
                        'created_by'       => Auth::id() ?? 1,
                        'approved_by'      => Auth::id() ?? 1,
                        'approved_at'      => now(),
                    ]);

                    $this->receivingCount++;

                    foreach ($lines as $lineNum => $line) {
                        $itemName    = trim($line['item_name'] ?? '');
                        $categoryCode = strtoupper(trim($line['category_code'] ?? 'OK'));
                        $unitCode    = strtoupper(trim($line['unit_code'] ?? 'PCS'));
                        $batchNum    = trim($line['batch_number'] ?? ('INIT-' . Str::random(6)));
                        $expiredDate = $this->parseDate($line['expired_date'] ?? null, now()->addYears(2)->format('Y-m-d'));
                        $qtyIn       = intval($line['qty_received'] ?? 0);
                        $qtyUsed     = intval($line['qty_used'] ?? 0);
                        $price       = floatval($line['purchase_price'] ?? 0);
                        $currentQty  = max(0, $qtyIn - $qtyUsed);

                        if (empty($itemName)) {
                            $this->skippedCount++;
                            continue;
                        }

                        // Resolve or auto-create item
                        $item = $this->resolveItem($itemName, $categoryCode, $unitCode);

                        if (!$item) {
                            $this->errors[] = "Baris '{$itemName}': Gagal buat item baru.";
                            $this->skippedCount++;
                            continue;
                        }

                        // Create Receiving Detail
                        ReceivingDetail::create([
                            'receiving_id'   => $receiving->id,
                            'item_id'        => $item->id,
                            'batch_number'   => $batchNum,
                            'expired_date'   => $expiredDate,
                            'qty_received'   => $qtyIn,
                            'purchase_price' => $price,
                            'ppn_percentage' => 0,
                            'ppn_amount'     => 0,
                            'subtotal'       => $price * $qtyIn,
                        ]);

                        // Create or update Item Batch
                        $batch = ItemBatch::updateOrCreate(
                            ['item_id' => $item->id, 'batch_number' => $batchNum, 'warehouse_id' => $warehouseId],
                            [
                                'supplier_id'    => $supplierId,
                                'expired_date'   => $expiredDate,
                                'initial_qty'    => $qtyIn,
                                'current_qty'    => $currentQty,
                                'purchase_price' => $price,
                                'status'         => 'available',
                                'is_active'      => true,
                            ]
                        );

                        // Stock IN
                        StockCard::create([
                            'item_id'          => $item->id,
                            'warehouse_id'     => $warehouseId,
                            'item_batch_id'    => $batch->id,
                            'transaction_date' => $invoiceDate,
                            'transaction_type' => 'receiving',
                            'reference_type'   => \App\Models\Receiving::class,
                            'reference_id'     => $receiving->id,
                            'qty_in'           => $qtyIn,
                            'qty_out'          => 0,
                            'last_stock'       => $qtyIn,
                            'notes'            => 'Saldo awal - penerimaan: ' . $invoiceNumber,
                        ]);

                        // Stock OUT (usage) if any
                        if ($qtyUsed > 0) {
                            StockCard::create([
                                'item_id'          => $item->id,
                                'warehouse_id'     => $warehouseId,
                                'item_batch_id'    => $batch->id,
                                'transaction_date' => $invoiceDate,
                                'transaction_type' => 'adjustment',
                                'reference_type'   => \App\Models\Receiving::class,
                                'reference_id'     => $receiving->id,
                                'qty_in'           => 0,
                                'qty_out'          => $qtyUsed,
                                'last_stock'       => $currentQty,
                                'notes'            => 'Pemakaian periode lama (saldo awal)',
                            ]);
                        }

                        $this->importedCount++;
                    }
                });
            } catch (\Exception $e) {
                Log::error('InitialStockImport error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                $this->errors[] = "Error pada invoice '$invoiceNumber': " . $e->getMessage();
                $this->skippedCount += $lines->count();
            }
        }
    }

    private function preloadCaches(): void
    {
        foreach (ItemCategory::all() as $cat) {
            $this->categoryCache[strtoupper($cat->code)] = $cat->id;
        }
        foreach (ItemUnit::all() as $unit) {
            $this->unitCache[strtoupper($unit->code)] = $unit->id;
        }
        foreach (Supplier::all() as $sup) {
            $this->supplierCache[strtoupper($sup->code)] = $sup->id;
        }

        $mainWarehouse = Warehouse::where('is_main', true)->first() ?? Warehouse::first();
        $this->mainWarehouseId = $mainWarehouse?->id ?? 1;
    }

    private function resolveSupplier(string $code): ?int
    {
        $code = strtoupper(trim($code));
        return $this->supplierCache[$code] ?? null;
    }

    private function resolveItem(string $name, string $categoryCode, string $unitCode): ?Item
    {
        $cacheKey = strtoupper($name);

        if (isset($this->itemCache[$cacheKey])) {
            return $this->itemCache[$cacheKey];
        }

        // Try to find existing item (by name, case-insensitive)
        $item = Item::whereRaw('UPPER(name) = ?', [strtoupper($name)])->first();

        if (!$item) {
            // Resolve or auto-create category
            $categoryId = $this->categoryCache[$categoryCode] ?? null;
            if (!$categoryId) {
                // Default to OK (Obat Keras) if not found
                $category = ItemCategory::firstOrCreate(
                    ['code' => $categoryCode],
                    ['name' => $categoryCode, 'type' => 'obat', 'is_active' => true]
                );
                $this->categoryCache[$categoryCode] = $category->id;
                $categoryId = $category->id;
            }

            // Resolve or auto-create unit
            $unitId = $this->unitCache[$unitCode] ?? null;
            if (!$unitId) {
                $unit = ItemUnit::firstOrCreate(
                    ['code' => $unitCode],
                    ['name' => $unitCode, 'is_active' => true]
                );
                $this->unitCache[$unitCode] = $unit->id;
                $unitId = $unit->id;
            }

            // Auto-create item
            $item = Item::create([
                'code'             => 'ITEM-' . Str::upper(Str::random(8)),
                'name'             => $name,
                'item_category_id' => $categoryId,
                'item_unit_id'     => $unitId,
                'is_active'        => true,
                'is_prescription'  => in_array($categoryCode, ['OK', 'PSI', 'NAR']),
                'storage_condition' => 'suhu_ruang',
                'created_by'       => Auth::id() ?? 1,
            ]);
        }

        $this->itemCache[$cacheKey] = $item;
        return $item;
    }

    protected function parseDate($value, string $fallback): string
    {
        if (empty($value)) return $fallback;

        if (is_numeric($value)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
            } catch (\Exception $e) {
                return $fallback;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return $fallback;
        }
    }
}
