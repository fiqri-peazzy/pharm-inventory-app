<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'username' => 'admin',
            'email' => 'admin@rsud.go.id',
            'employee_id' => 'ADM001',
            'phone' => '081234567890',
            'warehouse_id' => 1,
            'is_active' => true,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('super-admin');

        // Demo Bupati Account
        // $bupati = User::create([
        //     'name' => 'Bupati',
        //     'username' => 'bupati',
        //     'email' => 'bupati@pemda.go.id',
        //     'employee_id' => 'BPT001',
        //     'phone' => '081234567891',
        //     'warehouse_id' => null,
        //     'is_active' => true,
        //     'password' => Hash::make('password'),
        //     'email_verified_at' => now(),
        // ]);

        // $bupati->assignRole('bupati');
    }
}