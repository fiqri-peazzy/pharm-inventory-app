<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ItemPriceSeeder extends Seeder
{
    public function run(): void
    {
        // Pemetaan Harga dari Data BLUD
        $prices = [
            // OBAT
            ['code' => 'OB-ACY', 'pbf' => 'KF', 'price' => 4273.50],
            ['code' => 'OB-ALB', 'pbf' => 'KF', 'price' => 1068375.00],
            ['code' => 'OB-ALL100', 'pbf' => 'MUP', 'price' => 18315.00],
            ['code' => 'OB-ALL300', 'pbf' => 'TSJ', 'price' => 38000.85],
            ['code' => 'OB-ALP05', 'pbf' => 'KF', 'price' => 40816.92],
            ['code' => 'OB-ALP1', 'pbf' => 'KF', 'price' => 64141.35],
            ['code' => 'OB-AMB-SYR', 'pbf' => 'KF', 'price' => 7215.00],
            ['code' => 'OB-AMB-KAP', 'pbf' => 'NAJ', 'price' => 15007.20],
            ['code' => 'OB-AMN-INJ', 'pbf' => 'APL', 'price' => 302280.75],
            ['code' => 'OB-AMD10', 'pbf' => 'NAJ', 'price' => 25008.30],
            ['code' => 'OB-AMD5', 'pbf' => 'NAJ', 'price' => 22499.70],
            ['code' => 'OB-AMX500', 'pbf' => 'NAJ', 'price' => 60006.60],
            ['code' => 'OB-ANT-TAB', 'pbf' => 'KF', 'price' => 35400.13],
            ['code' => 'OB-ANT-SUS', 'pbf' => 'NAJ', 'price' => 5550.00],
            ['code' => 'OB-ATR20', 'pbf' => 'PPG', 'price' => 152647.20],
            ['code' => 'OB-ATR40', 'pbf' => 'PPG', 'price' => 302497.22],
            ['code' => 'OB-ASE', 'pbf' => 'MUP', 'price' => 8750.13],
            ['code' => 'OB-BIS5', 'pbf' => 'AAM', 'price' => 7200.01],
            ['code' => 'OB-CEF1-INJ', 'pbf' => 'KF', 'price' => 99900.00],
            
            // BHP
            ['code' => 'BHP-SLI', 'pbf' => 'PKM', 'price' => 127650.00],
            ['code' => 'BHP-APP', 'pbf' => 'PKM', 'price' => 194250.00],
            ['code' => 'BHP-KAP1000', 'pbf' => 'GMS', 'price' => 88800.00],
            ['code' => 'BHP-FOL10', 'pbf' => 'KF', 'price' => 15984.00],
            ['code' => 'BHP-IV16', 'pbf' => 'MBS', 'price' => 7284.84],
            ['code' => 'BHP-COL', 'pbf' => 'BDM', 'price' => 90000.00],
            ['code' => 'BHP-SP3', 'pbf' => 'MUP', 'price' => 1050.00], // Estimasi spuit
            ['code' => 'BHP-INF-DW', 'pbf' => 'MMS', 'price' => 12500.00], // Estimasi
        ];

        foreach ($prices as $p) {
            $item = Item::where('code', $p['code'])->first();
            $supplier = Supplier::where('code', $p['pbf'])->first();

            if ($item && $supplier) {
                ItemPrice::updateOrCreate(
                    [
                        'item_id' => $item->id,
                        'supplier_id' => $supplier->id,
                        'price_type' => 'contract',
                    ],
                    [
                        'price' => $p['price'],
                        'ppn_percentage' => 11,
                        'effective_date' => '2024-01-01',
                        'end_date' => '2025-12-31',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
