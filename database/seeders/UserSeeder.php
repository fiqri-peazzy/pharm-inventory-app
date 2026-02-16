<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $mainWarehouse = Warehouse::where('is_main', true)->first();
        
        $users = [
            [
                'name' => 'Super Administrator',
                'username' => 'admin',
                'email' => 'admin@rsud.go.id',
                'employee_id' => 'ADM001',
                'phone' => '081234567890',
                'warehouse_id' => $mainWarehouse->id ?? 1,
                'role' => 'super-admin',
            ],
            [
                'name' => 'Kepala Farmasi',
                'username' => 'farmasi',
                'email' => 'farmasi@rsud.go.id',
                'employee_id' => 'FRM001',
                'phone' => '081234567891',
                'warehouse_id' => $mainWarehouse->id ?? 1,
                'role' => 'kepala-farmasi',
            ],
            [
                'name' => 'Petugas Gudang Utama',
                'username' => 'gudang',
                'email' => 'gudang@rsud.go.id',
                'employee_id' => 'GDN001',
                'phone' => '081234567892',
                'warehouse_id' => $mainWarehouse->id ?? 1,
                'role' => 'petugas-gudang',
            ],
            [
                'name' => 'Direktur RS',
                'username' => 'direktur',
                'email' => 'direktur@rsud.go.id',
                'employee_id' => 'DIR001',
                'phone' => '081234567893',
                'warehouse_id' => null,
                'role' => 'direktur',
            ],
            [
                'name' => 'Bupati',
                'username' => 'bupati',
                'email' => 'bupati@pemda.go.id',
                'employee_id' => 'BPT001',
                'phone' => '081234567894',
                'warehouse_id' => null,
                'role' => 'bupati',
            ],
            [
                'name' => 'dr. Andi Spesialis',
                'username' => 'dokter',
                'email' => 'dokter@rsud.go.id',
                'employee_id' => 'DOC001',
                'phone' => '081234567895',
                'warehouse_id' => null,
                'role' => 'doctor',
            ],
            [
                'name' => 'Auditor Farmasi',
                'username' => 'auditor',
                'email' => 'auditor@rsud.go.id',
                'employee_id' => 'AUD001',
                'phone' => '081234567896',
                'warehouse_id' => null,
                'role' => 'auditor',
            ],
        ];

        // Add Depot Users
        $depots = Warehouse::where('is_main', false)->get();
        foreach ($depots as $depot) {
            $username = strtolower(str_replace([' ', '-'], '_', $depot->code));
            $users[] = [
                'name' => 'Apoteker ' . $depot->name,
                'username' => $username,
                'email' => $username . '@rsud.go.id',
                'employee_id' => 'APT_' . $depot->code,
                'sipa_number' => 'SIPA/APT/' . rand(1000, 9999) . '/' . date('Y'),
                'phone' => '081' . rand(100000000, 999999999),
                'warehouse_id' => $depot->id,
                'role' => 'apoteker',
            ];
        }

        // Add special SIPA for Kepala Farmasi
        foreach ($users as &$u) {
            if ($u['role'] === 'kepala-farmasi') {
                $u['sipa_number'] = 'SIPA/KPL/' . rand(1000, 9999) . '/' . date('Y');
            }
        }
        unset($u);

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );

            $user->syncRoles([$role]);
        }
    }
}