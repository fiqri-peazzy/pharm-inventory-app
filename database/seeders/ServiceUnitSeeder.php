<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $depoRajal = \App\Models\Warehouse::where('code', 'APT-RAJAL')->first();
        $depoRanap = \App\Models\Warehouse::where('code', 'APT-RANAP')->first();
        $depoIgd = \App\Models\Warehouse::where('code', 'APT-IGD')->first();

        $units = [
            // POLIKLINIK -> Map ke Depo Rajal
            ['code' => 'POLI-UMM', 'name' => 'Poli Umum', 'type' => 'poli', 'category' => 'poli_umum', 'building' => 'Gedung A', 'floor' => '1', 'default_warehouse_id' => $depoRajal?->id],
            ['code' => 'POLI-GGI', 'name' => 'Poli Gigi', 'type' => 'poli', 'category' => 'poli_gigi', 'building' => 'Gedung A', 'floor' => '1', 'default_warehouse_id' => $depoRajal?->id],
            ['code' => 'POLI-INT', 'name' => 'Poli Penyakit Dalam', 'type' => 'poli', 'category' => 'poli_spesialis', 'building' => 'Gedung A', 'floor' => '2', 'default_warehouse_id' => $depoRajal?->id],
            
            // RUANGAN -> Map ke Depo Ranap
            ['code' => 'RW-VIP', 'name' => 'Ruangan VIP Mawar', 'type' => 'ruangan', 'category' => 'ruang_vip', 'building' => 'Gedung B', 'floor' => '3', 'default_warehouse_id' => $depoRanap?->id],
            ['code' => 'RW-KL3-A', 'name' => 'Ruangan Kelas 3 Melati', 'type' => 'ruangan', 'category' => 'ruang_kelas3', 'building' => 'Gedung C', 'floor' => '1', 'default_warehouse_id' => $depoRanap?->id],
            
            // INSTALASI -> Map ke Depo IGD
            ['code' => 'IGD-001', 'name' => 'Instalasi Gawat Darurat', 'type' => 'instalasi', 'category' => 'igd', 'building' => 'Gedung Emergency', 'floor' => '1', 'default_warehouse_id' => $depoIgd?->id],
            ['code' => 'OK-001', 'name' => 'Kamar Operasi (OK)', 'type' => 'instalasi', 'category' => 'ok', 'building' => 'Gedung Bedah', 'floor' => '2', 'default_warehouse_id' => $depoIgd?->id],
        ];

        foreach ($units as $unit) {
            \App\Models\ServiceUnit::updateOrCreate(['code' => $unit['code']], $unit);
        }
    }
}
