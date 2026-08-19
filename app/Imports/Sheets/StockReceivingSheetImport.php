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

    /**
     * Per-row outcome, used to render the preview table and the
     * downloadable result report. Each entry:
     * ['row' => int, 'item_name' => string, 'status' => 'success'|'skipped', 'reason' => string]
     */
    public array $rowResults = [];

    /**
     * When true, no database writes happen — only validation.
     * Used to power the "Validasi Data" preview step in the UI.
     */
    public bool $dryRun = false;

    private const FALLBACK_INVOICE_GROUP = '__TANPA_FAKTUR__';

    // Cache for performance
    private array $categoryCache = [];
    private array $unitCache = [];
    private array $supplierCache = [];
    private array $itemCache = [];
    private array $existingBatchKeys = [];
    private ?int $mainWarehouseId = null;

    public function collection(Collection $rows)
    {
        $this->preloadCaches();

        // Excel row number: heading row is row 1, so first data row is row 2.
        $rows = $rows->values()->map(function ($row, $index) {
            $row['__excel_row__'] = $index + 2;
            return $row;
        });

        $consideredRows = $rows->filter(function ($row) {
            // A fully blank row (common at the end of a sheet) is silently ignored,
            // it is not a validation failure worth reporting to the user.
            return !empty(trim((string) ($row['item_name'] ?? '')))
                || intval($row['qty_received'] ?? 0) > 0;
        });

        if ($consideredRows->isEmpty()) {
            $this->errors[] = 'Tidak ada baris data ditemukan di sheet TEMPLATE_STOK. Pastikan Anda mengisi mulai dari baris ke-2.';
            return;
        }

        $validRows = collect();

        foreach ($consideredRows as $row) {
            $excelRow = $row['__excel_row__'];
            $itemName = trim((string) ($row['item_name'] ?? ''));
            $qtyReceived = intval($row['qty_received'] ?? 0);

            $problems = [];
            if ($itemName === '') {
                $problems[] = 'kolom item_name kosong';
            }
            if ($qtyReceived <= 0) {
                $problems[] = 'kolom qty_received harus diisi angka lebih dari 0';
            }

            if (!empty($problems)) {
                $this->rowResults[] = [
                    'row' => $excelRow,
                    'item_name' => $itemName ?: '(kosong)',
                    'status' => 'skipped',
                    'reason' => 'Baris dilewati: ' . implode(', ', $problems) . '.',
                ];
                $this->skippedCount++;
                continue;
            }

            $validRows->push($row);
        }

        if ($validRows->isEmpty()) {
            $this->errors[] = 'Tidak ada baris valid. Pastikan kolom item_name dan qty_received terisi dengan benar (lihat detail per baris di bawah).';
            return;
        }

        // Group by invoice_number; rows without an invoice number are grouped
        // together under one auto-generated "saldo awal tanpa faktur" document
        // instead of being rejected outright.
        $grouped = $validRows->groupBy(function ($row) {
            $invoice = trim((string) ($row['invoice_number'] ?? ''));
            return $invoice !== '' ? $invoice : self::FALLBACK_INVOICE_GROUP;
        });

        foreach ($grouped as $invoiceKey => $lines) {
            $isFallbackGroup = $invoiceKey === self::FALLBACK_INVOICE_GROUP;
            $invoiceNumber = $isFallbackGroup
                ? 'TANPA-FAKTUR-' . date('YmdHis') . '-' . Str::upper(Str::random(4))
                : $invoiceKey;

            try {
                DB::transaction(function () use ($invoiceNumber, $lines, $isFallbackGroup) {
                    $this->processInvoiceGroup($invoiceNumber, $lines, $isFallbackGroup);
                });
            } catch (\Exception $e) {
                Log::error('InitialStockImport error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                $this->errors[] = "Error pada invoice '$invoiceNumber': " . $e->getMessage();
                foreach ($lines as $line) {
                    $this->rowResults[] = [
                        'row' => $line['__excel_row__'],
                        'item_name' => trim((string) ($line['item_name'] ?? '')),
                        'status' => 'skipped',
                        'reason' => 'Gagal diproses karena error sistem pada dokumen ini: ' . $e->getMessage(),
                    ];
                }
                $this->skippedCount += $lines->count();
            }
        }
    }

    private function processInvoiceGroup(string $invoiceNumber, Collection $lines, bool $isFallbackGroup): void
    {
        $firstLine = $lines->first();
        $warehouseId = $this->mainWarehouseId;
        $invoiceDate = $this->parseDate($firstLine['invoice_date'] ?? null, now()->format('Y-m-d'));

        $supplierCode = trim((string) ($firstLine['supplier_code'] ?? ''));
        $supplierId = $this->resolveSupplier($supplierCode);

        if (!$supplierId && $supplierCode !== '') {
            foreach ($lines as $line) {
                $this->rowResults[] = [
                    'row' => $line['__excel_row__'],
                    'item_name' => trim((string) ($line['item_name'] ?? '')),
                    'status' => 'skipped',
                    'reason' => "Kode supplier '{$supplierCode}' tidak ditemukan di REF_SUPPLIER. Baris diabaikan.",
                ];
            }
            $this->errors[] = "Invoice '$invoiceNumber': kode supplier '$supplierCode' tidak ditemukan. Seluruh baris pada dokumen ini diabaikan.";
            $this->skippedCount += $lines->count();
            return;
        }

        $totalAmount = $lines->sum(fn($l) => floatval($l['purchase_price'] ?? 0) * intval($l['qty_received'] ?? 0));

        $receiving = null;
        $anyLineImported = false;

        foreach ($lines as $line) {
            $excelRow = $line['__excel_row__'];
            $itemName = trim((string) ($line['item_name'] ?? ''));
            $categoryCode = strtoupper(trim((string) ($line['category_code'] ?? 'OK')));
            $unitCode = strtoupper(trim((string) ($line['unit_code'] ?? 'PCS')));
            $batchNum = trim((string) ($line['batch_number'] ?? '')) ?: ('INIT-' . Str::upper(Str::random(6)));
            $expiredDate = $this->parseDate($line['expired_date'] ?? null, now()->addYears(2)->format('Y-m-d'));
            $qtyIn = intval($line['qty_received'] ?? 0);
            $qtyUsed = intval($line['qty_used'] ?? 0);
            $price = floatval($line['purchase_price'] ?? 0);
            $currentQty = max(0, $qtyIn - $qtyUsed);

            $item = $this->resolveItem($itemName, $categoryCode, $unitCode);

            if (!$item) {
                $this->rowResults[] = [
                    'row' => $excelRow,
                    'item_name' => $itemName,
                    'status' => 'skipped',
                    'reason' => 'Gagal membuat/menemukan item di master data.',
                ];
                $this->skippedCount++;
                continue;
            }

            $duplicateKey = $item->id . '|' . strtoupper($batchNum) . '|' . $warehouseId;
            if (isset($this->existingBatchKeys[$duplicateKey])) {
                $this->rowResults[] = [
                    'row' => $excelRow,
                    'item_name' => $itemName,
                    'status' => 'skipped',
                    'reason' => "Duplikat: batch '{$batchNum}' untuk item ini sudah pernah diimport sebelumnya. Baris dilewati agar stok tidak dobel.",
                ];
                $this->skippedCount++;
                continue;
            }

            if ($this->dryRun) {
                $this->rowResults[] = [
                    'row' => $excelRow,
                    'item_name' => $itemName,
                    'status' => 'success',
                    'reason' => 'Valid, siap diimport (' . number_format($qtyIn) . ' masuk).',
                ];
                $this->existingBatchKeys[$duplicateKey] = true;
                $this->importedCount++;
                continue;
            }

            if (!$receiving) {
                $receiving = $this->createReceivingWithRetry($supplierId, $warehouseId, $invoiceDate, $invoiceNumber, $totalAmount, $isFallbackGroup);
            }

            ReceivingDetail::create([
                'receiving_id' => $receiving->id,
                'item_id' => $item->id,
                'batch_number' => $batchNum,
                'expired_date' => $expiredDate,
                'qty_received' => $qtyIn,
                'purchase_price' => $price,
                'ppn_percentage' => 0,
                'ppn_amount' => 0,
                'subtotal' => $price * $qtyIn,
            ]);

            $batch = ItemBatch::updateOrCreate(
                ['item_id' => $item->id, 'batch_number' => $batchNum, 'warehouse_id' => $warehouseId],
                [
                    'supplier_id' => $supplierId,
                    'expired_date' => $expiredDate,
                    'initial_qty' => $qtyIn,
                    'current_qty' => $currentQty,
                    'purchase_price' => $price,
                    'status' => 'available',
                    'is_active' => true,
                ]
            );

            StockCard::create([
                'item_id' => $item->id,
                'warehouse_id' => $warehouseId,
                'item_batch_id' => $batch->id,
                'transaction_date' => $invoiceDate,
                'transaction_type' => 'receiving',
                'reference_type' => Receiving::class,
                'reference_id' => $receiving->id,
                'qty_in' => $qtyIn,
                'qty_out' => 0,
                'last_stock' => $qtyIn,
                'notes' => 'Saldo awal - penerimaan: ' . $invoiceNumber,
            ]);

            if ($qtyUsed > 0) {
                StockCard::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouseId,
                    'item_batch_id' => $batch->id,
                    'transaction_date' => $invoiceDate,
                    'transaction_type' => 'adjustment',
                    'reference_type' => Receiving::class,
                    'reference_id' => $receiving->id,
                    'qty_in' => 0,
                    'qty_out' => $qtyUsed,
                    'last_stock' => $currentQty,
                    'notes' => 'Pemakaian periode lama (saldo awal)',
                ]);
            }

            $this->existingBatchKeys[$duplicateKey] = true;
            $this->rowResults[] = [
                'row' => $excelRow,
                'item_name' => $itemName,
                'status' => 'success',
                'reason' => 'Berhasil diimport (' . number_format($qtyIn) . ' masuk, sisa ' . number_format($currentQty) . ').',
            ];
            $this->importedCount++;
            $anyLineImported = true;
        }

        if (!$anyLineImported && $receiving) {
            // Should not normally happen, but guard against an orphaned empty
            // Receiving document if every line in the group turned out invalid.
            $receiving->delete();
            $this->receivingCount--;
        }
    }

    /**
     * Create a Receiving document with an auto-generated document number,
     * retrying with the next sequence number if a collision occurs (e.g. a
     * previous run today, a retried/duplicated request, or a race with
     * another import happening at the same time).
     */
    private function createReceivingWithRetry(?int $supplierId, ?int $warehouseId, string $invoiceDate, string $invoiceNumber, float $totalAmount, bool $isFallbackGroup): Receiving
    {
        $maxAttempts = 20;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $this->receivingCount++;
            $receivingNumber = 'INIT-' . date('Ymd') . '-' . str_pad($this->receivingCount, 4, '0', STR_PAD_LEFT);

            try {
                return Receiving::create([
                    'receiving_number' => $receivingNumber,
                    'supplier_id' => $supplierId,
                    'warehouse_id' => $warehouseId,
                    'receiving_date' => $invoiceDate,
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => $invoiceDate,
                    'total_amount' => $totalAmount,
                    'ppn_amount' => 0,
                    'grand_total' => $totalAmount,
                    'notes' => $isFallbackGroup
                        ? 'Import saldo awal stok (sistem lama) - tanpa nomor faktur'
                        : 'Import saldo awal stok (sistem lama)',
                    'status' => 'posted',
                    'created_by' => Auth::id() ?? 1,
                    'approved_by' => Auth::id() ?? 1,
                    'approved_at' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // 23000 = integrity constraint violation (duplicate receiving_number).
                // Bump the counter and try the next number instead of failing the
                // whole import over a stale in-memory sequence.
                if ($e->getCode() !== '23000' || $attempt === $maxAttempts - 1) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Gagal membuat nomor dokumen penerimaan setelah beberapa percobaan.');
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

        // Continue today's receiving-number sequence from whatever is already
        // in the database instead of always starting at 0001 — a previous
        // import run today (or a partially-completed one) would otherwise
        // cause an immediate duplicate-key collision on the very first row.
        $prefix = 'INIT-' . date('Ymd') . '-';
        $lastNumber = Receiving::where('receiving_number', 'like', $prefix . '%')
            ->orderByDesc('receiving_number')
            ->value('receiving_number');
        $this->receivingCount = $lastNumber ? (int) substr($lastNumber, strlen($prefix)) : 0;

        // Preload existing item+batch+warehouse combinations so re-imports of
        // the same file (or overlapping data) are flagged as duplicates
        // instead of silently overwriting previously imported stock.
        ItemBatch::where('warehouse_id', $this->mainWarehouseId)
            ->select('item_id', 'batch_number')
            ->get()
            ->each(function ($batch) {
                $key = $batch->item_id . '|' . strtoupper($batch->batch_number) . '|' . $this->mainWarehouseId;
                $this->existingBatchKeys[$key] = true;
            });
    }

    private function resolveSupplier(string $code): ?int
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }
        return $this->supplierCache[$code] ?? null;
    }

    private function resolveItem(string $name, string $categoryCode, string $unitCode): ?Item
    {
        $cacheKey = strtoupper($name);

        if (isset($this->itemCache[$cacheKey])) {
            return $this->itemCache[$cacheKey];
        }

        $item = Item::whereRaw('UPPER(name) = ?', [strtoupper($name)])->first();

        if (!$item) {
            if ($this->dryRun) {
                // Don't create master data during a preview — just fabricate
                // an in-memory placeholder so the preview can still report
                // the row as valid without touching the database.
                $item = new Item(['name' => $name]);
                $this->itemCache[$cacheKey] = $item;
                return $item;
            }

            $categoryId = $this->categoryCache[$categoryCode] ?? null;
            if (!$categoryId) {
                $category = ItemCategory::firstOrCreate(
                    ['code' => $categoryCode],
                    ['name' => $categoryCode, 'type' => 'obat', 'is_active' => true]
                );
                $this->categoryCache[$categoryCode] = $category->id;
                $categoryId = $category->id;
            }

            $unitId = $this->unitCache[$unitCode] ?? null;
            if (!$unitId) {
                $unit = ItemUnit::firstOrCreate(
                    ['code' => $unitCode],
                    ['name' => $unitCode, 'is_active' => true]
                );
                $this->unitCache[$unitCode] = $unit->id;
                $unitId = $unit->id;
            }

            $item = Item::create([
                'code' => 'ITEM-' . Str::upper(Str::random(8)),
                'name' => $name,
                'item_category_id' => $categoryId,
                'item_unit_id' => $unitId,
                'is_active' => true,
                'is_prescription' => in_array($categoryCode, ['OK', 'PSI', 'NAR']),
                'storage_condition' => 'suhu_ruang',
                'created_by' => Auth::id() ?? 1,
            ]);
        }

        $this->itemCache[$cacheKey] = $item;
        return $item;
    }

    protected function parseDate($value, string $fallback): string
    {
        if (empty($value)) {
            return $fallback;
        }

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
