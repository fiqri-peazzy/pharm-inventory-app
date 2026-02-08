<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemBatch;
use App\Models\StockCard;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySampleSeeder extends Seeder
{
    public function run()
    {
        // Clear existing inventory data for a fresh start
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ItemBatch::truncate();
        StockCard::truncate();
        DB::table('item_warehouse_settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = Item::all();
        $warehouses = Warehouse::all();

        if ($items->isEmpty() || $warehouses->isEmpty()) {
            $this->command->warn('Items or Warehouses are empty. Please seed Master Data first.');
            return;
        }

        foreach ($items as $index => $item) {
            foreach ($warehouses as $warehouse) {
                // Varying inventory levels
                // Some items are fast-moving, some slow, some dead
                $usageType = ($index % 3); // 0: fast, 1: slow, 2: dead

                $initialQty = match($usageType) {
                    0 => rand(500, 2000),
                    1 => rand(50, 200),
                    2 => rand(10, 50),
                };

                // 1. Create Batch
                $batch = ItemBatch::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'batch_number' => 'BCH-' . strtoupper(substr(md5($item->id . $warehouse->id), 0, 8)),
                    'expired_date' => Carbon::now()->addMonths(rand(6, 24)),
                    'initial_qty' => $initialQty,
                    'current_qty' => $initialQty,
                    'purchase_price' => rand(1000, 50000),
                    'is_active' => true,
                ]);

                // 2. Create Stock In (Initial)
                StockCard::create([
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'item_batch_id' => $batch->id,
                    'transaction_date' => Carbon::now()->subDays(31),
                    'reference_type' => 'Migration',
                    'reference_id' => 0,
                    'qty_in' => $initialQty,
                    'qty_out' => 0,
                    'last_stock' => $initialQty,
                    'notes' => 'Initial migration stock',
                ]);

                // 3. Create Daily Usage (Qty Out)
                $currentStock = $initialQty;
                for ($d = 30; $d >= 1; $d--) {
                    $dailyUsage = 0;
                    if ($usageType == 0) { // Fast
                        $dailyUsage = rand(10, 50);
                    } elseif ($usageType == 1) { // Slow
                        $dailyUsage = (rand(1, 10) > 8) ? rand(1, 5) : 0;
                    }
                    // Dead stock has 0 daily usage

                    if ($dailyUsage > 0 && $currentStock >= $dailyUsage) {
                        $currentStock -= $dailyUsage;
                        StockCard::create([
                            'item_id' => $item->id,
                            'warehouse_id' => $warehouse->id,
                            'item_batch_id' => $batch->id,
                            'transaction_date' => Carbon::now()->subDays($d),
                            'reference_type' => 'Sale/Dispense',
                            'reference_id' => rand(100, 999),
                            'qty_in' => 0,
                            'qty_out' => $dailyUsage,
                            'last_stock' => $currentStock,
                            'notes' => 'Patient consumption',
                        ]);
                    }
                }

                // Update batch current_qty
                $batch->update(['current_qty' => $currentStock]);
            }
        }

        $this->command->info('Inventory Sample Data seeded with varied patterns (Fast/Slow/Dead stock).');
    }
}
