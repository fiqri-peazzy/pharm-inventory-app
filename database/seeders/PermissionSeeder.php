<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => ['view'],
            'master-items' => ['view', 'create', 'update', 'delete'],
            'master-categories' => ['view', 'create', 'update', 'delete'],
            'master-suppliers' => ['view', 'create', 'update', 'delete'],
            'master-warehouses' => ['view', 'create', 'update', 'delete'],
            'master-users' => ['view', 'create', 'update', 'delete'],
            'purchase-requests' => ['view', 'create', 'update', 'delete', 'approve'],
            'purchase-orders' => ['view', 'create', 'update', 'delete', 'approve', 'direktur-approve'],
            'receivings' => ['view', 'create', 'update', 'delete', 'approve'],
            'stocks' => ['view', 'adjust'],
            'distributions' => ['view', 'create', 'update', 'delete', 'approve'],
            'prescriptions' => ['view', 'create', 'process'],
            'stock-opnames' => ['view', 'create', 'update', 'delete', 'approve'],
            'adjustments' => ['view', 'create', 'approve'],
            'returns' => ['view', 'create', 'update', 'delete', 'approve'],
            'disposals' => ['view', 'create', 'update', 'delete', 'approve'],
            'reports-stock' => ['view', 'export'],
            'reports-accounting' => ['view', 'export'],
            'reports-transparency' => ['view', 'export'],
            'journals' => ['view', 'create', 'post'],
            'settings' => ['view', 'update'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web'
                ]);
            }
        }

        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->givePermissionTo(Permission::all());

        // Kepala Farmasi
        $kepalaFarmasi = Role::findByName('kepala-farmasi');
        $kepalaFarmasi->givePermissionTo([
            'dashboard.view',
            'master-items.view', 'master-categories.view', 'master-suppliers.view', 'master-warehouses.view',
            'purchase-requests.view', 'purchase-requests.approve',
            'purchase-orders.view', 'purchase-orders.approve',
            'receivings.view', 'receivings.approve',
            'stocks.view',
            'distributions.view', 'distributions.approve',
            'prescriptions.view',
            'stock-opnames.view', 'stock-opnames.approve',
            'adjustments.view', 'adjustments.approve',
            'returns.view', 'returns.approve',
            'disposals.view', 'disposals.approve',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
        ]);

        // Apoteker
        $apoteker = Role::findByName('apoteker');
        $apoteker->givePermissionTo([
            'dashboard.view',
            'master-items.view',
            'stocks.view',
            'distributions.view', 'distributions.create',
            'prescriptions.view', 'prescriptions.create', 'prescriptions.process',
            'returns.view', 'returns.create',
            'reports-stock.view',
        ]);

        // Petugas Gudang
        $petugasGudang = Role::findByName('petugas-gudang');
        $petugasGudang->givePermissionTo([
            'dashboard.view',
            'master-items.view', 'master-suppliers.view',
            'receivings.view', 'receivings.create', 'receivings.update',
            'stocks.view',
            'distributions.view', 'distributions.create',
            'stock-opnames.view', 'stock-opnames.create',
            'returns.view', 'returns.create',
            'disposals.view', 'disposals.create',
            'reports-stock.view',
        ]);

        // Keuangan BLUD
        $keuangan = Role::findByName('keuangan-blud');
        $keuangan->givePermissionTo([
            'dashboard.view',
            'receivings.view',
            'stocks.view',
            'distributions.view',
            'prescriptions.view',
            'stock-opnames.view',
            'returns.view',
            'disposals.view',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
            'journals.view', 'journals.create', 'journals.post',
        ]);

        // Auditor
        $auditor = Role::findByName('auditor');
        $auditor->givePermissionTo([
            'dashboard.view',
            'master-items.view', 'master-categories.view', 'master-suppliers.view', 'master-warehouses.view',
            'purchase-requests.view',
            'purchase-orders.view',
            'receivings.view',
            'stocks.view',
            'distributions.view',
            'prescriptions.view',
            'stock-opnames.view',
            'adjustments.view',
            'returns.view',
            'disposals.view',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
            'journals.view',
        ]);

        // Bupati - Transparansi
        $bupati = Role::findByName('bupati');
        $bupati->givePermissionTo([
            'dashboard.view',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
            'reports-transparency.view', 'reports-transparency.export',
        ]);

        // Direktur
        $direktur = Role::findByName('direktur');
        $direktur->givePermissionTo([
            'dashboard.view',
            'purchase-orders.view', 'purchase-orders.direktur-approve',
            'reports-stock.view', 'reports-accounting.view',
        ]);
    }
}