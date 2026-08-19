<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'app_name' => 'Sistem Farmasi',
                'hospital_name' => 'Sistem Farmasi',
                'address' => null,
                'phone' => null,
                'email' => null,
                'logo_path' => null,
            ]
        );

        $this->call([
            WarehouseSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            MasterDataSeeder::class,
            ItemPriceSeeder::class,
            ServiceUnitSeeder::class,
            DosageInstructionSeeder::class, // Seeder untuk aturan pakai standar
        ]);
    }
}
