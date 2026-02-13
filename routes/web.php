<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.request');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.email');
});

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Master Data Routes
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/categories', [App\Http\Controllers\Master\ItemCategoryController::class, 'index'])->name('categories.index');
        Route::get('/units', [App\Http\Controllers\Master\ItemUnitController::class, 'index'])->name('units.index');
        Route::get('/items', [App\Http\Controllers\Master\ItemController::class, 'index'])->name('items.index');
        Route::get('/suppliers', [App\Http\Controllers\Master\SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/warehouses', [App\Http\Controllers\Master\WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('/service-units', [App\Http\Controllers\Master\ServiceUnitController::class, 'index'])->name('service-units.index');
        
        // Settings/Users
        Route::get('/users', [App\Http\Controllers\MasterController::class, 'users'])->name('users.index');
        Route::get('/roles', [App\Http\Controllers\MasterController::class, 'roles'])->name('roles.index');
    });

    // Procurement Routes
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::get('/prices', [App\Http\Controllers\Procurement\PurchaseController::class, 'prices'])->name('prices.index');
        Route::get('/requests', [App\Http\Controllers\Procurement\PurchaseController::class, 'requests'])->name('requests.index');
        Route::get('/requests/create', [App\Http\Controllers\Procurement\PurchaseController::class, 'createRequest'])->name('requests.create');
        Route::get('/requests/{id}/edit', [App\Http\Controllers\Procurement\PurchaseController::class, 'editRequest'])->name('requests.edit');
        Route::get('/approvals', [App\Http\Controllers\Procurement\PurchaseController::class, 'approvals'])->name('approvals.index');
        Route::get('/orders', [App\Http\Controllers\Procurement\PurchaseController::class, 'orders'])->name('orders.index');
        Route::get('/orders/create', [App\Http\Controllers\Procurement\PurchaseController::class, 'createOrder'])->name('orders.create');
        Route::get('/orders/{id}/edit', [App\Http\Controllers\Procurement\PurchaseController::class, 'editOrder'])->name('orders.edit');
        Route::get('/orders/{id}/print', [App\Http\Controllers\Procurement\PurchaseController::class, 'print'])->name('orders.print');

        // Receivings
        Route::get('/receivings', [App\Http\Controllers\Procurement\ReceivingController::class, 'index'])->name('receivings.index');
        Route::get('/receivings/create', [App\Http\Controllers\Procurement\ReceivingController::class, 'create'])->name('receivings.create');
        Route::get('/receivings/{id}/edit', [App\Http\Controllers\Procurement\ReceivingController::class, 'edit'])->name('receivings.edit');
        Route::get('/receivings/{id}/print', [\App\Http\Controllers\Procurement\ReceivingPrintController::class, 'print'])->name('receivings.print');
    });

    // Inventory Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/dashboard/{warehouse?}', [App\Http\Controllers\Inventory\InventoryController::class, 'dashboard'])->name('dashboard');
        Route::get('/thresholds', [App\Http\Controllers\Inventory\InventoryController::class, 'thresholds'])->name('thresholds');
        Route::get('/disposals', [App\Http\Controllers\Inventory\InventoryController::class, 'disposals'])->name('disposals.index');
        Route::get('/disposals/create', [App\Http\Controllers\Inventory\InventoryController::class, 'createDisposal'])->name('disposals.create');
        Route::get('/disposals/{id}/edit', [App\Http\Controllers\Inventory\InventoryController::class, 'editDisposal'])->name('disposals.edit');
        
        // Stock Opnames
        Route::get('/stock-opnames', [App\Http\Controllers\Inventory\InventoryController::class, 'stockOpnames'])->name('stock-opnames.index');
        Route::get('/stock-opnames/create', [App\Http\Controllers\Inventory\InventoryController::class, 'createStockOpname'])->name('stock-opnames.create');
        Route::get('/stock-opnames/{id}/edit', [App\Http\Controllers\Inventory\InventoryController::class, 'editStockOpname'])->name('stock-opnames.edit');

        // Adjustments
        Route::get('/adjustments', [App\Http\Controllers\Inventory\InventoryController::class, 'adjustments'])->name('adjustments.index');
        Route::get('/adjustments/create', [App\Http\Controllers\Inventory\InventoryController::class, 'createAdjustment'])->name('adjustments.create');
        Route::get('/adjustments/{id}/edit', [App\Http\Controllers\Inventory\InventoryController::class, 'editAdjustment'])->name('adjustments.edit');
        
        // Returns
        Route::get('/returns', [\App\Http\Controllers\Inventory\InventoryController::class, 'returns'])->name('returns.index');
        Route::get('/returns/create', [\App\Http\Controllers\Inventory\InventoryController::class, 'createReturn'])->name('returns.create');
        Route::get('/returns/{id}/edit', [\App\Http\Controllers\Inventory\InventoryController::class, 'editReturn'])->name('returns.edit');

        // Distributions
        Route::get('/distributions', [App\Http\Controllers\Inventory\InventoryController::class, 'distributions'])->name('distributions.index');
        Route::get('/distributions/request', [App\Http\Controllers\Inventory\InventoryController::class, 'createDistributionRequest'])->name('distributions.request');
        Route::get('/distributions/{id}/process', [App\Http\Controllers\Inventory\InventoryController::class, 'processDistribution'])->name('distributions.process');
        Route::get('/distributions/{id}/receive', [App\Http\Controllers\Inventory\InventoryController::class, 'receiveDistribution'])->name('distributions.receive');

        // Stock Cards & Batches
        Route::get('/stocks/cards', [App\Http\Controllers\Inventory\InventoryController::class, 'stockCards'])->name('stocks.cards');
        Route::get('/stocks/batches', [App\Http\Controllers\Inventory\InventoryController::class, 'batches'])->name('stocks.batches');
    });

    // Clinical Routes
    Route::prefix('clinical')->name('clinical.')->group(function () {
        Route::get('/prescriptions', [App\Http\Controllers\Clinical\ClinicalController::class, 'prescriptions'])->name('prescriptions.index');
        Route::get('/prescriptions/create', [App\Http\Controllers\Clinical\ClinicalController::class, 'createPrescription'])->name('prescriptions.create');
        Route::get('/prescriptions/{id}/dispense', [App\Http\Controllers\Clinical\ClinicalController::class, 'dispensePrescription'])->name('prescriptions.dispense');
        Route::get('/prescriptions/{id}/edit', [App\Http\Controllers\Clinical\ClinicalController::class, 'editPrescription'])->name('prescriptions.edit');

        Route::get('/ward-requests', [App\Http\Controllers\Clinical\ClinicalController::class, 'wardRequests'])->name('ward-requests.index');
        Route::get('/ward-requests/create', [App\Http\Controllers\Clinical\ClinicalController::class, 'createWardRequest'])->name('ward-requests.create');
        Route::get('/ward-requests/{id}/edit', [App\Http\Controllers\Clinical\ClinicalController::class, 'editWardRequest'])->name('ward-requests.edit');
    });

    // Accounting Routes
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/journals', [App\Http\Controllers\Accounting\AccountingController::class, 'journals'])->name('journals.index');
        Route::get('/journals/create', [App\Http\Controllers\Accounting\AccountingController::class, 'createJournal'])->name('journals.create');
        Route::get('/journals/{id}/edit', [App\Http\Controllers\Accounting\AccountingController::class, 'editJournal'])->name('journals.edit');
        Route::get('/journals/{id}', [App\Http\Controllers\Accounting\AccountingController::class, 'showJournal'])->name('journals.show');
        
        Route::get('/coa', [App\Http\Controllers\Accounting\AccountingController::class, 'coa'])->name('coa.index');
        
        // Reports
        Route::get('/reports/general-ledger', [App\Http\Controllers\Accounting\AccountingController::class, 'generalLedger'])->name('reports.general-ledger');
        Route::get('/reports/trial-balance', [App\Http\Controllers\Accounting\AccountingController::class, 'trialBalance'])->name('reports.trial-balance');
    });

    // Settings & Activity Log
    Route::get('/settings', function() { return view('pages.settings.index'); })->name('settings');
    Route::get('/activity-logs', function() { return view('pages.settings.activity-log'); })->name('activity-logs');
});