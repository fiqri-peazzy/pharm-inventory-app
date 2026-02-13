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
            'inventory-dashboard' => ['view-all', 'view-own', 'export'],
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
            'ward-requests' => ['view', 'create', 'approve', 'process'],
            'stock-opnames' => ['view', 'view-all', 'create', 'update', 'input-physical', 'submit', 'review', 'approve', 'reject', 'request-recount', 'export', 'delete'],
            'stock-adjustments' => ['view', 'view-all', 'create', 'update', 'submit', 'approve', 'reject', 'export', 'delete', 'upload-evidence'],
            'returns' => ['view', 'view-all', 'create', 'update', 'submit', 'approve', 'reject', 'track-credit', 'input-credit-note', 'track-refund', 'export', 'delete'],
            'disposals' => ['view', 'view-all', 'create', 'update', 'submit', 'approve', 'reject', 'mark-executed', 'load-expired-items', 'upload-evidence', 'export', 'delete'],
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
        $superAdmin->syncPermissions(Permission::all());

        // Kepala Farmasi - Full Procurement Oversight
        $kepalaFarmasi = Role::findByName('kepala-farmasi');
        $kepalaFarmasi->syncPermissions([
            'dashboard.view',
            'inventory-dashboard.view-all',
            'master-items.view', 'master-categories.view', 'master-suppliers.view', 'master-warehouses.view',
            'purchase-requests.view', 'purchase-requests.approve',
            'purchase-orders.view', 'purchase-orders.approve',
            'receivings.view', 'receivings.approve',
            'stocks.view',
            'distributions.view', 'distributions.approve',
            'prescriptions.view',
            'ward-requests.view', 'ward-requests.approve',
            'stock-opnames.view-all', 'stock-opnames.approve', 'stock-opnames.review',
            'stock-adjustments.view-all', 'stock-adjustments.approve', 'stock-adjustments.reject',
            'returns.view-all', 'returns.approve', 'returns.reject', 'returns.track-credit',
            'disposals.view-all', 'disposals.approve',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
        ]);

        // Apoteker (Depot PJ) - Daily Clinic Operations
        $apoteker = Role::findByName('apoteker');
        $apoteker->syncPermissions([
            'dashboard.view',
            'inventory-dashboard.view-own',
            'master-items.view',
            'purchase-requests.view', 'purchase-requests.create',
            'stocks.view',
            'distributions.view',
            'prescriptions.view', 'prescriptions.create', 'prescriptions.process',
            'ward-requests.view', 'ward-requests.create', 'ward-requests.process',
            'stock-opnames.view', 'stock-opnames.create', 'stock-opnames.input-physical',
            'stock-adjustments.view', 'stock-adjustments.create', 'stock-adjustments.update', 'stock-adjustments.submit',
            'returns.view', 'returns.create', 'returns.update', 'returns.submit',
            'disposals.view', 'disposals.create',
            'reports-stock.view',
        ]);

        // Petugas Gudang - Logistics & Storage
        $petugasGudang = Role::findByName('petugas-gudang');
        $petugasGudang->syncPermissions([
            'dashboard.view',
            'master-items.view', 'master-suppliers.view',
            'purchase-requests.view',
            'purchase-orders.view',
            'receivings.view', 'receivings.create', 'receivings.update',
            'stocks.view',
            'distributions.view', 'distributions.create', 'distributions.update',
            'stock-opnames.view', 'stock-opnames.create', 'stock-opnames.input-physical',
            'stock-adjustments.view', 'stock-adjustments.create', 'stock-adjustments.update', 'stock-adjustments.submit',
            'returns.view', 'returns.create', 'returns.update', 'returns.submit',
            'disposals.view', 'disposals.create', 'disposals.load-expired-items',
            'reports-stock.view',
        ]);

        // Keuangan BLUD - Financial Audit
        $keuangan = Role::findByName('keuangan-blud');
        $keuangan->syncPermissions([
            'dashboard.view',
            'receivings.view',
            'stocks.view',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
            'returns.view-all', 'returns.input-credit-note', 'returns.track-refund',
            'journals.view', 'journals.create', 'journals.post',
        ]);

        // Auditor - Audit & Oversight
        $auditor = Role::findByName('auditor');
        $auditor->syncPermissions([
            'dashboard.view',
            'inventory-dashboard.view-all',
            'master-items.view', 'master-categories.view', 'master-suppliers.view', 'master-warehouses.view',
            'purchase-requests.view',
            'purchase-orders.view',
            'receivings.view',
            'stocks.view',
            'distributions.view',
            'prescriptions.view',
            'stock-opnames.view', 'stock-opnames.view-all', 'stock-opnames.review', 'stock-opnames.approve',
            'stock-adjustments.view', 'stock-adjustments.view-all', 'stock-adjustments.approve', 'stock-adjustments.reject',
            'returns.view', 'returns.view-all', 'returns.approve', 'returns.export',
            'disposals.view', 'disposals.view-all', 'disposals.approve',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
            'journals.view',
        ]);

        // Bupati - Monitoring & Transparency
        $bupati = Role::findByName('bupati');
        $bupati->syncPermissions([
            'dashboard.view',
            'reports-stock.view', 'reports-stock.export',
            'reports-accounting.view', 'reports-accounting.export',
            'reports-transparency.view', 'reports-transparency.export',
        ]);

        // Direktur - high level approval
        $direktur = Role::findByName('direktur');
        $direktur->syncPermissions([
            'dashboard.view',
            'inventory-dashboard.view-all',
            'purchase-orders.view', 'purchase-orders.direktur-approve',
            'reports-stock.view', 'reports-accounting.view',
        ]);

        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $doctor->syncPermissions(['dashboard.view', 'master-items.view', 'prescriptions.view', 'prescriptions.create']);
    }
}