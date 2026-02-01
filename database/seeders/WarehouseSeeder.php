<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'code' => 'GD-UTAMA',
                'name' => 'Gudang Farmasi Utama',
                'type' => 'gudang_utama',
                'is_main' => true,
                'is_active' => true,
                'pic_name' => 'Kepala Gudang Farmasi',
                'pic_phone' => '081234567890',
                'address' => 'Gedung Farmasi Lt. 1, RSUD',
            ],
            [
                'code' => 'APT-IGD',
                'name' => 'Apotek IGD',
                'type' => 'depo_igd',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Apoteker IGD',
                'pic_phone' => '081234567891',
                'address' => 'Instalasi Gawat Darurat',
            ],
            [
                'code' => 'APT-RANAP',
                'name' => 'Apotek Rawat Inap',
                'type' => 'depo_ranap',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Apoteker Rawat Inap',
                'pic_phone' => '081234567892',
                'address' => 'Gedung Rawat Inap Lt. 1',
            ],
            [
                'code' => 'APT-RAJAL',
                'name' => 'Apotek Rawat Jalan',
                'type' => 'depo_rajal',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Apoteker Rawat Jalan',
                'pic_phone' => '081234567893',
                'address' => 'Gedung Poliklinik Lt. 1',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }
    }
}
