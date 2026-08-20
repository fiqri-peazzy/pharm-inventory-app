<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.request');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showNewPassword'])->name('password.reset');
    Route::post('/reset-password/update', [AuthController::class, 'updatePassword'])->name('password.update');
});

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // Master Data Routes
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/categories', [App\Http\Controllers\Master\ItemCategoryController::class, 'index'])
            ->middleware('permission:master-categories.view')
            ->name('categories.index');

        // NOTE: "units" and "service-units" have no dedicated permission in PermissionSeeder
        // (only master-items/categories/suppliers/warehouses/users exist). Left auth-only
        // pending a human decision on which permission should gate them.
        Route::get('/units', [App\Http\Controllers\Master\ItemUnitController::class, 'index'])
            ->name('units.index');

        Route::get('/items', [App\Http\Controllers\Master\ItemController::class, 'index'])
            ->middleware('permission:master-items.view')
            ->name('items.index');
        Route::get('/suppliers', [App\Http\Controllers\Master\SupplierController::class, 'index'])
            ->middleware('permission:master-suppliers.view')
            ->name('suppliers.index');
        Route::get('/warehouses', [App\Http\Controllers\Master\WarehouseController::class, 'index'])
            ->middleware('permission:master-warehouses.view')
            ->name('warehouses.index');

        Route::get('/service-units', [App\Http\Controllers\Master\ServiceUnitController::class, 'index'])
            ->name('service-units.index');

        // Settings/Users
        Route::get('/users', [App\Http\Controllers\MasterController::class, 'users'])
            ->middleware('permission:master-users.view')
            ->name('users.index');
        // Role management reuses master-users.view (no separate "roles" permission exists;
        // role assignment is inherently a user-management action).
        Route::get('/roles', [App\Http\Controllers\MasterController::class, 'roles'])
            ->middleware('permission:master-users.view')
            ->name('roles.index');
    });

    // Procurement Routes
    Route::prefix('procurement')->name('procurement.')->group(function () {
        // "rko" (rencana kebutuhan obat) is procurement planning, closest match is purchase-requests.
        Route::get('/rko', [App\Http\Controllers\Procurement\PurchaseController::class, 'rko'])
            ->middleware('permission:purchase-requests.view')
            ->name('rko.index');
        // "prices" has no clear permission mapping in the seeder. Left auth-only pending human decision.
        Route::get('/prices', [App\Http\Controllers\Procurement\PurchaseController::class, 'prices'])
            ->name('prices.index');

        Route::get('/requests', [App\Http\Controllers\Procurement\PurchaseController::class, 'requests'])
            ->middleware('permission:purchase-requests.view')
            ->name('requests.index');
        Route::get('/requests/create', [App\Http\Controllers\Procurement\PurchaseController::class, 'createRequest'])
            ->middleware('permission:purchase-requests.create')
            ->name('requests.create');
        Route::get('/requests/{id}/edit', [App\Http\Controllers\Procurement\PurchaseController::class, 'editRequest'])
            ->middleware('permission:purchase-requests.update')
            ->name('requests.edit');
        Route::get('/approvals', [App\Http\Controllers\Procurement\PurchaseController::class, 'approvals'])
            ->middleware('permission:purchase-requests.approve|purchase-orders.approve|receivings.approve')
            ->name('approvals.index');
        Route::get('/orders', [App\Http\Controllers\Procurement\PurchaseController::class, 'orders'])
            ->middleware('permission:purchase-orders.view')
            ->name('orders.index');
        Route::get('/orders/create', [App\Http\Controllers\Procurement\PurchaseController::class, 'createOrder'])
            ->middleware('permission:purchase-orders.create')
            ->name('orders.create');
        Route::get('/orders/{id}/edit', [App\Http\Controllers\Procurement\PurchaseController::class, 'editOrder'])
            ->middleware('permission:purchase-orders.update')
            ->name('orders.edit');
        Route::get('/orders/{id}/print', [App\Http\Controllers\Procurement\PurchaseController::class, 'print'])
            ->middleware('permission:purchase-orders.view')
            ->name('orders.print');
        Route::get('/rko/print', [App\Http\Controllers\Procurement\PurchaseController::class, 'printRko'])
            ->middleware('permission:purchase-requests.view')
            ->name('rko.print');

        // Receivings
        Route::get('/receivings', [App\Http\Controllers\Procurement\ReceivingController::class, 'index'])
            ->middleware('permission:receivings.view')
            ->name('receivings.index');
        Route::get('/receivings/create', [App\Http\Controllers\Procurement\ReceivingController::class, 'create'])
            ->middleware('permission:receivings.create')
            ->name('receivings.create');
        Route::get('/receivings/{id}/edit', [App\Http\Controllers\Procurement\ReceivingController::class, 'edit'])
            ->middleware('permission:receivings.update')
            ->name('receivings.edit');
        Route::get('/receivings/{id}/print', [\App\Http\Controllers\Procurement\ReceivingPrintController::class, 'print'])
            ->middleware('permission:receivings.view')
            ->name('receivings.print');
    });

    // Inventory Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/dashboard/{warehouse?}', [App\Http\Controllers\Inventory\InventoryController::class, 'dashboard'])
            ->middleware('permission:inventory-dashboard.view-all|inventory-dashboard.view-own')
            ->name('dashboard');
        // "quarantine" and "thresholds" have no dedicated permission; reuse stocks.view (broadly held,
        // low risk) as the closest match. Flagged as a judgment call.
        Route::get('/quarantine', [App\Http\Controllers\Inventory\InventoryController::class, 'quarantine'])
            ->middleware('permission:stocks.view')
            ->name('quarantine.index');
        Route::get('/thresholds', [App\Http\Controllers\Inventory\InventoryController::class, 'thresholds'])
            ->middleware('permission:stocks.view')
            ->name('thresholds');

        Route::get('/disposals', [App\Http\Controllers\Inventory\InventoryController::class, 'disposals'])
            ->middleware('permission:disposals.view')
            ->name('disposals.index');
        Route::get('/disposals/create', [App\Http\Controllers\Inventory\InventoryController::class, 'createDisposal'])
            ->middleware('permission:disposals.create')
            ->name('disposals.create');
        Route::get('/disposals/{id}/edit', [App\Http\Controllers\Inventory\InventoryController::class, 'editDisposal'])
            ->middleware('permission:disposals.update')
            ->name('disposals.edit');

        // Stock Opnames
        Route::get('/stock-opnames', [App\Http\Controllers\Inventory\InventoryController::class, 'stockOpnames'])
            ->middleware('permission:stock-opnames.view|stock-opnames.view-all')
            ->name('stock-opnames.index');
        Route::get('/stock-opnames/create', [App\Http\Controllers\Inventory\InventoryController::class, 'createStockOpname'])
            ->middleware('permission:stock-opnames.create')
            ->name('stock-opnames.create');
        Route::get('/stock-opnames/{id}/edit', [App\Http\Controllers\Inventory\InventoryController::class, 'editStockOpname'])
            ->middleware('permission:stock-opnames.update|stock-opnames.input-physical')
            ->name('stock-opnames.edit');

        // Adjustments
        Route::get('/adjustments', [App\Http\Controllers\Inventory\InventoryController::class, 'adjustments'])
            ->middleware('permission:stock-adjustments.view|stock-adjustments.view-all')
            ->name('adjustments.index');
        Route::get('/adjustments/create', [App\Http\Controllers\Inventory\InventoryController::class, 'createAdjustment'])
            ->middleware('permission:stock-adjustments.create')
            ->name('adjustments.create');
        Route::get('/adjustments/{id}/edit', [App\Http\Controllers\Inventory\InventoryController::class, 'editAdjustment'])
            ->middleware('permission:stock-adjustments.update')
            ->name('adjustments.edit');

        // Returns
        Route::get('/returns', [\App\Http\Controllers\Inventory\InventoryController::class, 'returns'])
            ->middleware('permission:returns.view|returns.view-all')
            ->name('returns.index');
        Route::get('/returns/create', [\App\Http\Controllers\Inventory\InventoryController::class, 'createReturn'])
            ->middleware('permission:returns.create')
            ->name('returns.create');
        Route::get('/returns/{id}/edit', [\App\Http\Controllers\Inventory\InventoryController::class, 'editReturn'])
            ->middleware('permission:returns.update')
            ->name('returns.edit');

        // Distributions
        Route::get('/distributions', [App\Http\Controllers\Inventory\InventoryController::class, 'distributions'])
            ->middleware('permission:distributions.view')
            ->name('distributions.index');
        Route::get('/distributions/request', [App\Http\Controllers\Inventory\InventoryController::class, 'createDistributionRequest'])
            ->middleware('permission:distributions.create')
            ->name('distributions.request');
        Route::get('/distributions/{id}/process', [App\Http\Controllers\Inventory\InventoryController::class, 'processDistribution'])
            ->middleware('permission:distributions.process|distributions.update')
            ->name('distributions.process');
        Route::get('/distributions/{id}/receive', [App\Http\Controllers\Inventory\InventoryController::class, 'receiveDistribution'])
            ->middleware('permission:distributions.receive|distributions.update')
            ->name('distributions.receive');

        // Stock Cards & Batches
        Route::get('/stocks/cards', [App\Http\Controllers\Inventory\InventoryController::class, 'stockCards'])
            ->middleware('permission:stocks.view')
            ->name('stocks.cards');
        Route::get('/stocks/batches', [App\Http\Controllers\Inventory\InventoryController::class, 'batches'])
            ->middleware('permission:stocks.view')
            ->name('stocks.batches');

        // Initial Import - treated as a stock-adjust action (loads opening balances into stock).
        Route::get('/initial-import', [App\Http\Controllers\Inventory\InventoryController::class, 'initialImport'])
            ->middleware('permission:stocks.adjust')
            ->name('initial-import');
    });

    // Clinical Routes
    Route::prefix('clinical')->name('clinical.')->group(function () {
        Route::get('/prescriptions', [App\Http\Controllers\Clinical\ClinicalController::class, 'prescriptions'])
            ->middleware('permission:prescriptions.view')
            ->name('prescriptions.index');
        Route::get('/prescriptions/create', [App\Http\Controllers\Clinical\ClinicalController::class, 'createPrescription'])
            ->middleware('permission:prescriptions.create')
            ->name('prescriptions.create');
        Route::get('/prescriptions/{id}/dispense', [App\Http\Controllers\Clinical\ClinicalController::class, 'dispensePrescription'])
            ->middleware('permission:prescriptions.process')
            ->name('prescriptions.dispense');
        // No "update" permission exists for prescriptions; reusing .process/.create (closest
        // available actions) since editing happens as part of the processing/dispensing workflow.
        Route::get('/prescriptions/{id}/edit', [App\Http\Controllers\Clinical\ClinicalController::class, 'editPrescription'])
            ->middleware('permission:prescriptions.process|prescriptions.create')
            ->name('prescriptions.edit');

        Route::get('/ward-requests', [App\Http\Controllers\Clinical\ClinicalController::class, 'wardRequests'])
            ->middleware('permission:ward-requests.view')
            ->name('ward-requests.index');
        Route::get('/ward-requests/create', [App\Http\Controllers\Clinical\ClinicalController::class, 'createWardRequest'])
            ->middleware('permission:ward-requests.create')
            ->name('ward-requests.create');
        // No "update" permission for ward-requests; reusing .process|.create (closest available actions).
        Route::get('/ward-requests/{id}/edit', [App\Http\Controllers\Clinical\ClinicalController::class, 'editWardRequest'])
            ->middleware('permission:ward-requests.process|ward-requests.create')
            ->name('ward-requests.edit');
    });

    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        // Stock Reports
        Route::get('/stock', [App\Http\Controllers\Reports\StockReportController::class, 'index'])
            ->middleware('permission:reports-stock.view')
            ->name('stock.index');
        Route::get('/stock/pdf', [App\Http\Controllers\Reports\StockReportController::class, 'exportPdf'])
            ->middleware('permission:reports-stock.export')
            ->name('stock.pdf');

        // Distribution Reports - no dedicated permission exists; reuses reports-stock since
        // distribution reporting is a stock-movement report. Judgment call.
        Route::get('/distribution', [App\Http\Controllers\Reports\DistributionReportController::class, 'index'])
            ->middleware('permission:reports-stock.view')
            ->name('distribution.index');
        Route::get('/distribution/pdf', [App\Http\Controllers\Reports\DistributionReportController::class, 'exportPdf'])
            ->middleware('permission:reports-stock.export')
            ->name('distribution.pdf');
    });

    // Accounting Routes
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/journals', [App\Http\Controllers\Accounting\AccountingController::class, 'journals'])
            ->middleware('permission:journals.view')
            ->name('journals.index');
        // No "update" permission for journals; reusing .create (drafting/editing an entry).
        Route::get('/journals/create', [App\Http\Controllers\Accounting\AccountingController::class, 'createJournal'])
            ->middleware('permission:journals.create')
            ->name('journals.create');
        Route::get('/journals/{id}/edit', [App\Http\Controllers\Accounting\AccountingController::class, 'editJournal'])
            ->middleware('permission:journals.create')
            ->name('journals.edit');
        Route::get('/journals/{id}', [App\Http\Controllers\Accounting\AccountingController::class, 'showJournal'])
            ->middleware('permission:journals.view')
            ->name('journals.show');

        Route::get('/coa', [App\Http\Controllers\Accounting\AccountingController::class, 'coa'])
            ->middleware('permission:master-accounts.view')
            ->name('coa.index');

        // Reports
        Route::get('/reports/general-ledger', [App\Http\Controllers\Accounting\AccountingController::class, 'generalLedger'])
            ->middleware('permission:reports-accounting.view')
            ->name('reports.general-ledger');
        Route::get('/reports/trial-balance', [App\Http\Controllers\Accounting\AccountingController::class, 'trialBalance'])
            ->middleware('permission:reports-accounting.view')
            ->name('reports.trial-balance');
    });

    // Personal account — every authenticated user manages their own profile,
    // no special permission required beyond being logged in.
    Route::get('/profile', function () {
        return view('pages.profile');
    })->name('profile');

    // Settings & Activity Log
    Route::get('/settings', function () {
        return view('pages.settings.index');
    })->middleware('permission:settings.view')->name('settings');
    Route::get('/activity-logs', function () {
        return view('pages.settings.activity-log');
    })->middleware('permission:audit-logs.view')->name('activity-logs');

    // Manual Book - reference documentation, no permission fits; left open to any
    // authenticated active user (unrestricted; not a data/security-sensitive resource).
    Route::get('/manual-book',          [App\Http\Controllers\ManualBookController::class, 'view'])->name('manual-book.view');
    Route::get('/manual-book/download', [App\Http\Controllers\ManualBookController::class, 'download'])->name('manual-book.download');
});
