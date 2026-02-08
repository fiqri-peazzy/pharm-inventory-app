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
                'address' => 'Gedung Farmasi Lt. 1',
            ],
            // STATION OBAT
            [
                'code' => 'DEPO-UGD',
                'name' => 'DEPO UGD',
                'type' => 'depo_igd',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Petugas Depo UGD',
                'address' => 'Gedung UGD Lt. 1',
            ],
            [
                'code' => 'APT-CENTRA',
                'name' => 'APOTEK CENTRA',
                'type' => 'depo_farmasi',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Petugas Apotek Centra',
                'address' => 'Gedung Utama Lt. 1',
            ],
            [
                'code' => 'DEPO-IBC',
                'name' => 'DEPO INSTALASI BEDAH CENTRAL',
                'type' => 'depo_ok',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Petugas Depo IBC',
                'address' => 'Gedung OK Lt. 2',
            ],
            // STATION BMHP
            [
                'code' => 'HEMO-DIALISA',
                'name' => 'HEMODIALISA',
                'type' => 'depo_bmhp',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Petugas Hemodialisa',
                'address' => 'Gedung B Lt. 1',
            ],
            [
                'code' => 'LABORATORIUM',
                'name' => 'LABORATORIUM',
                'type' => 'depo_bmhp',
                'is_main' => false,
                'is_active' => true,
                'pic_name' => 'Petugas Lab',
                'address' => 'Gedung A Lt. 1',
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
