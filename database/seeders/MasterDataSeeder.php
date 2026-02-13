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
            ['code' => 'TAB', 'name' => 'Tablet'],
            ['code' => 'CAP', 'name' => 'Kapsul'],
            ['code' => 'AMP', 'name' => 'Ampul'],
            ['code' => 'VIAL', 'name' => 'Vial'],
            ['code' => 'BTL', 'name' => 'Botol'],
            ['code' => 'PCS', 'name' => 'Pcs'],
            ['code' => 'PC', 'name' => 'Pc'],
            ['code' => 'STR', 'name' => 'Strip'],
            ['code' => 'BOX', 'name' => 'Box'],
            ['code' => 'TUBE', 'name' => 'Tube'],
            ['code' => 'ROLL', 'name' => 'Roll'],
            ['code' => 'TPLES', 'name' => 'Toples'],
            ['code' => 'PAK', 'name' => 'Pak'],
            ['code' => 'POT', 'name' => 'Pot'],
            ['code' => 'TP', 'name' => 'Tp'],
            ['code' => 'BAG', 'name' => 'Bag'],
        ];

        foreach ($units as $unit) {
            ItemUnit::updateOrCreate(['code' => $unit['code']], array_merge($unit, ['is_active' => true]));
        }

        // 2. Seed Categories
        $categories = [
            ['code' => 'OB', 'name' => 'Obat Bebas', 'type' => 'obat'],
            ['code' => 'OBT', 'name' => 'Obat Bebas Terbatas', 'type' => 'obat'],
            ['code' => 'OK', 'name' => 'Obat Keras', 'type' => 'obat'],
            ['code' => 'PSI', 'name' => 'Psikotropika', 'type' => 'obat'],
            ['code' => 'NAR', 'name' => 'Narkotika', 'type' => 'obat'],
            ['code' => 'BMHP', 'name' => 'Bahan Medis Habis Pakai', 'type' => 'bmhp'],
            ['code' => 'ALKES', 'name' => 'Alat Kesehatan', 'type' => 'alkes'],
        ];

        foreach ($categories as $cat) {
            ItemCategory::updateOrCreate(['code' => $cat['code']], array_merge($cat, ['is_active' => true]));
        }

        // 3. Seed Suppliers (PBF) dari kodepbf.md
        $suppliers = [
            ['code' => 'BSP', 'name' => 'PT. BINA SAN PRIMA'],
            ['code' => 'KF', 'name' => 'PT. KIMIA FARMA TRADING & DISTRIBUTOR'],
            ['code' => 'MBS', 'name' => 'PT. MENSA BINASUKSES'],
            ['code' => 'MUP', 'name' => 'PT. MERAPI UTAMA PHARMA'],
            ['code' => 'MMS', 'name' => 'PT. MITRAMEDIKA SEJAHTERABERSAMA'],
            ['code' => 'MMM', 'name' => 'PT. MULIA MULTI MEDIKA'],
            ['code' => 'APM', 'name' => 'PT. AOMA PRIMA MEDIKA'],
            ['code' => 'PV', 'name' => 'PT. PENTA VALENT'],
            ['code' => 'PPG', 'name' => 'PT. PARIT PADANG GLOBAL'],
            ['code' => 'RN', 'name' => 'PT. RAJAWALI NUSINDO'],
            ['code' => 'SSJ', 'name' => 'PT. SIKOLA SARANA JAYA'],
            ['code' => 'SST', 'name' => 'PT. SAPTA SARI TAMA'],
            ['code' => 'ST', 'name' => 'PT. SETIA THENOCH'],
            ['code' => 'TSJ', 'name' => 'PT. TRI SAPTA JAYA'],
            ['code' => 'MPI', 'name' => 'PT. MILLENIUM PHAMACON INTERNATIONAL'],
            ['code' => 'SA', 'name' => 'PT. SURGIKA ALKESINDO'],
            ['code' => 'PJM', 'name' => 'PT. PERDANA JAYA MEDIKA'],
            ['code' => 'GHF', 'name' => 'PT. GALOEH HUSADA FARMA'],
            ['code' => 'BDM', 'name' => 'PT. BERKAT DWI MITRA'],
            ['code' => 'AAM', 'name' => 'PT. ANUGRAH ARGON MEDICA'],
            ['code' => 'AAA', 'name' => 'PT. ADYA ARTHA ABADI'],
            ['code' => 'PML', 'name' => 'PT. PRIMA META LESTARI'],
            ['code' => 'LMS', 'name' => 'PT. LESTARI MULIA SEMPURNA'],
            ['code' => 'GMS', 'name' => 'CV. GORONTALO MUARA SUKSES'],
            ['code' => 'CDI', 'name' => 'PT. COBRA DENTAL INDONESIA'],
            ['code' => 'EI', 'name' => 'PT. ENDO INDONESIA'],
            ['code' => 'APF', 'name' => 'APOTEK PRIMA FARMA'],
            ['code' => 'NAJ', 'name' => 'PT. NAJ (NASIONAL)' ], // Tambahan dari data obat
            ['code' => 'APL', 'name' => 'PT. APL' ], // Tambahan dari data obat
            ['code' => 'PKM', 'name' => 'PT. PKM' ], // Tambahan dari data BHP
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(
                ['code' => $sup['code']],
                [
                    'name' => $sup['name'],
                    'type' => 'pbf',
                    'email' => strtolower($sup['code']) . '@mailtrap.io', // Added for testing
                    'is_active' => true,
                    'tax_status' => 'pkp',
                    'payment_term' => 30
                ]
            );
        }

        // 4. Seed Items (Sampling dari datablud-obat.md & datablud-bhp.md)
        $catOk = ItemCategory::where('code', 'OK')->first()->id;
        $catOb = ItemCategory::where('code', 'OB')->first()->id;
        $catBmh = ItemCategory::where('code', 'BMHP')->first()->id;
        $catAlk = ItemCategory::where('code', 'ALKES')->first()->id;

        $items = [
            // OBAT-OBATAN
            ['code' => 'OB-ACY', 'name' => 'ACYCLOVIR 5% CREAM 5 GR', 'item_category_id' => $catOb, 'unit' => 'TUBE'],
            ['code' => 'OB-ALB', 'name' => 'ALBUNORM 25% 50 ML', 'item_category_id' => $catOk, 'unit' => 'BTL'],
            ['code' => 'OB-ALL100', 'name' => 'ALLOPURINOL 100MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-ALL300', 'name' => 'ALLOPURINOL 300MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-ALP05', 'name' => 'ALPRAZOLAM 0,5 MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-ALP1', 'name' => 'ALPRAZOLAM 1 MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-AMB-SYR', 'name' => 'AMBROXOL HCL 15 MG/5 ML SYRUP', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-AMB-KAP', 'name' => 'AMBROXOL KAP 30 MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-AMN-INJ', 'name' => 'AMINOPHYLLINE INJ 24 MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-AMD10', 'name' => 'AMLODIPINE 10 MG TAB', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-AMD5', 'name' => 'AMLODIPINE 5 MG TAB', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-AMX500', 'name' => 'AMOXICILLIN 500 MG KAP', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-ANT-TAB', 'name' => 'ANTASIDA DOEN TAB', 'item_category_id' => $catOb, 'unit' => 'BOX'],
            ['code' => 'OB-ANT-SUS', 'name' => 'ANTASIDA DOEN SUSPENSI 60 ML', 'item_category_id' => $catOb, 'unit' => 'BTL'],
            ['code' => 'OB-ATR20', 'name' => 'ATORVASTATIN 20MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-ATR40', 'name' => 'ATORVASTATIN 40 MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-ASE', 'name' => 'ASERING 500 ML', 'item_category_id' => $catOk, 'unit' => 'BAG'],
            ['code' => 'OB-BIS5', 'name' => 'BISOPROLOL 5 MG', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-CEF1-INJ', 'name' => 'CEFTRIAXON SODIUM INJ 1 G', 'item_category_id' => $catOk, 'unit' => 'BOX'],
            ['code' => 'OB-INS-NOV', 'name' => 'INSULIN NOVORAPID FLEXPEN', 'item_category_id' => $catOk, 'unit' => 'PCS'],
            
            // BHP
            ['code' => 'BHP-SLI', 'name' => 'SLIT BLADE', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-APP', 'name' => 'APPALENS PLUS SQUARE EDGE MODEL 10L (22.5 D)', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-KAP1000', 'name' => 'KAPAS 1000 GR', 'item_category_id' => $catBmh, 'unit' => 'ROLL'],
            ['code' => 'BHP-FOL10', 'name' => 'FOLEYUCATH 2 WAY NO.10', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-IV16', 'name' => 'SURFLO IV CATH 16G', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-COL', 'name' => 'COLOSTOMY BAG', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-SP3', 'name' => 'SPUIT 3CC ONEMED', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-INF-DW', 'name' => 'INFUSET DEWASA', 'item_category_id' => $catBmh, 'unit' => 'PCS'],
            ['code' => 'BHP-KAS', 'name' => 'KASA STERIL 16X16', 'item_category_id' => $catAlk, 'unit' => 'BOX'],
        ];

        foreach ($items as $item) {
            $unitId = ItemUnit::where('code', $item['unit'])->first()->id;
            Item::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'item_category_id' => $item['item_category_id'],
                    'item_unit_id' => $unitId,
                    'is_active' => true,
                    'storage_condition' => 'suhu_ruang'
                ]
            );
        }
    }
}
