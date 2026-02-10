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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Master Data Routes
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/categories', [App\Http\Controllers\Master\ItemCategoryController::class, 'index'])->name('categories.index');
        Route::get('/units', [App\Http\Controllers\Master\ItemUnitController::class, 'index'])->name('units.index');
        Route::get('/items', [App\Http\Controllers\Master\ItemController::class, 'index'])->name('items.index');
        Route::get('/suppliers', [App\Http\Controllers\Master\SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/warehouses', [App\Http\Controllers\Master\WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('/service-units', [App\Http\Controllers\Master\ServiceUnitController::class, 'index'])->name('service-units.index');
        
        // Settings/Users
        Route::get('/users', [App\Http\Controllers\Master\WarehouseController::class, 'index'])->name('users.index'); // Placeholder
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

        // Distributions
        Route::get('/distributions', [App\Http\Controllers\Inventory\InventoryController::class, 'distributions'])->name('distributions.index');
        Route::get('/distributions/request', [App\Http\Controllers\Inventory\InventoryController::class, 'createDistributionRequest'])->name('distributions.request');
        Route::get('/distributions/{id}/process', [App\Http\Controllers\Inventory\InventoryController::class, 'processDistribution'])->name('distributions.process');
        Route::get('/distributions/{id}/receive', [App\Http\Controllers\Inventory\InventoryController::class, 'receiveDistribution'])->name('distributions.receive');

        // Stock Cards & Batches
        Route::get('/stocks/cards', [App\Http\Controllers\Inventory\InventoryController::class, 'stockCards'])->name('stocks.cards');
        Route::get('/stocks/batches', [App\Http\Controllers\Inventory\InventoryController::class, 'batches'])->name('stocks.batches');
    });
});