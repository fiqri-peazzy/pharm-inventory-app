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
        
        // Settings/Users
        Route::get('/users', [App\Http\Controllers\Master\WarehouseController::class, 'index'])->name('users.index'); // Placeholder
    });

    // Procurement Routes
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::get('/prices', [App\Http\Controllers\Procurement\PurchaseController::class, 'prices'])->name('prices.index');
        Route::get('/requests', [App\Http\Controllers\Procurement\PurchaseController::class, 'requests'])->name('requests.index');
        Route::get('/approvals', [App\Http\Controllers\Procurement\PurchaseController::class, 'approvals'])->name('approvals.index');
        Route::get('/orders', [App\Http\Controllers\Procurement\PurchaseController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{id}/print', [App\Http\Controllers\Procurement\PurchaseController::class, 'print'])->name('orders.print');
    });
});