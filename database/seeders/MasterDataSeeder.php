<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Units
        $units = [
            ['code' => 'TAB', 'name' => 'Tablet', 'is_active' => true],
            ['code' => 'CAP', 'name' => 'Kapsul', 'is_active' => true],
            ['code' => 'AMP', 'name' => 'Ampul', 'is_active' => true],
            ['code' => 'VIAL', 'name' => 'Vial', 'is_active' => true],
            ['code' => 'BTL', 'name' => 'Botol', 'is_active' => true],
            ['code' => 'PCS', 'name' => 'Pcs', 'is_active' => true],
            ['code' => 'STR', 'name' => 'Strip', 'is_active' => true],
            ['code' => 'BOX', 'name' => 'Box', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            ItemUnit::firstOrCreate(['code' => $unit['code']], $unit);
        }

        // 2. Seed Categories
        $categories = [
            ['code' => 'OB', 'name' => 'Obat Bebas', 'type' => 'obat', 'is_active' => true],
            ['code' => 'OBT', 'name' => 'Obat Bebas Terbatas', 'type' => 'obat', 'is_active' => true],
            ['code' => 'OK', 'name' => 'Obat Keras', 'type' => 'obat', 'is_active' => true],
            ['code' => 'PSI', 'name' => 'Psikotropika', 'type' => 'obat', 'is_active' => true],
            ['code' => 'NAR', 'name' => 'Narkotika', 'type' => 'obat', 'is_active' => true],
            ['code' => 'BMHP', 'name' => 'Bahan Medis Habis Pakai', 'type' => 'bmhp', 'is_active' => true],
            ['code' => 'ALKES', 'name' => 'Alat Kesehatan', 'type' => 'alkes', 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            ItemCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Seed Suppliers
        $suppliers = [
            [
                'code' => 'PBF-001',
                'name' => 'Kimia Farma Trading & Distribution',
                'type' => 'pbf',
                'address' => 'Jl. Budi Utomo No. 1, Jakarta',
                'phone' => '021-1234567',
                'email' => 'contact@kftd.co.id',
                'contact_person' => 'Bpk. Ahmad',
                'tax_status' => 'pkp',
                'payment_term' => 30,
            ],
            [
                'code' => 'PBF-002',
                'name' => 'Enseval Putera Megatrading',
                'type' => 'pbf',
                'address' => 'Kawasan Industri Pulo Gadung, Jakarta',
                'phone' => '021-7654321',
                'email' => 'sales@enseval.com',
                'contact_person' => 'Ibu Maria',
                'tax_status' => 'pkp',
                'payment_term' => 45,
            ],
            [
                'code' => 'PBF-003',
                'name' => 'Parit Padang Global',
                'type' => 'pbf',
                'address' => 'Jl. Palmerah Barat, Jakarta',
                'phone' => '021-5556667',
                'email' => 'info@ppg.co.id',
                'contact_person' => 'Bpk. Hendra',
                'tax_status' => 'pkp',
                'payment_term' => 30,
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['code' => $sup['code']], $sup);
        }

        // 4. Seed Items
        $catOk = ItemCategory::where('code', 'OK')->first()->id;
        $catOb = ItemCategory::where('code', 'OB')->first()->id;
        $unitTab = ItemUnit::where('code', 'TAB')->first()->id;
        $unitAmp = ItemUnit::where('code', 'AMP')->first()->id;

        $items = [
            [
                'code' => 'ITM-001',
                'name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin',
                'item_category_id' => $catOk,
                'item_unit_id' => $unitTab,
                'nie_number' => 'GKL12345678901',
                'manufacturer' => 'Kimia Farma',
                'min_stock' => 100,
                'max_stock' => 1000,
                'is_prescription' => true,
                'storage_condition' => 'suhu_ruang',
            ],
            [
                'code' => 'ITM-002',
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'item_category_id' => $catOb,
                'item_unit_id' => $unitTab,
                'nie_number' => 'GBL09876543210',
                'manufacturer' => 'Indofarma',
                'min_stock' => 500,
                'max_stock' => 5000,
                'is_prescription' => false,
                'storage_condition' => 'suhu_ruang',
            ],
            [
                'code' => 'ITM-003',
                'name' => 'Ceftriaxone 1g Inj',
                'generic_name' => 'Ceftriaxone',
                'item_category_id' => $catOk,
                'item_unit_id' => $unitAmp,
                'nie_number' => 'GKL55544433322',
                'manufacturer' => 'Dexa Medica',
                'min_stock' => 50,
                'max_stock' => 500,
                'is_prescription' => true,
                'storage_condition' => 'suhu_ruang',
            ],
            [
                'code' => 'ITM-004',
                'name' => 'Insulin Novorapid Flexpen',
                'generic_name' => 'Insulin Aspart',
                'item_category_id' => $catOk,
                'item_unit_id' => $unitTab, // Flexpen is usually treated as unit or PCS
                'nie_number' => 'DKI11122233344',
                'manufacturer' => 'Novo Nordisk',
                'min_stock' => 10,
                'max_stock' => 100,
                'is_prescription' => true,
                'storage_condition' => 'kulkas', // Cold storage
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['code' => $item['code']], $item);
        }
    }
}
