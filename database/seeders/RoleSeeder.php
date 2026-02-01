<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super-admin',
                'guard_name' => 'web',
                'description' => 'Full system access'
            ],
            [
                'name' => 'kepala-farmasi',
                'guard_name' => 'web',
                'description' => 'Kepala Instalasi Farmasi'
            ],
            [
                'name' => 'apoteker',
                'guard_name' => 'web',
                'description' => 'Apoteker pelayanan'
            ],
            [
                'name' => 'petugas-gudang',
                'guard_name' => 'web',
                'description' => 'Petugas penerimaan dan penyimpanan'
            ],
            [
                'name' => 'keuangan-blud',
                'guard_name' => 'web',
                'description' => 'Tim Keuangan BLUD'
            ],
            [
                'name' => 'auditor',
                'guard_name' => 'web',
                'description' => 'Auditor Internal'
            ],
            [
                'name' => 'bupati',
                'guard_name' => 'web',
                'description' => 'Bupati - Transparansi Pemerintah'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']]
            );
        }
    }
}