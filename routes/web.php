<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Routes cho quản lý Distributor
Route::prefix('distributors')->name('distributors.')->group(function () {
    Route::get('/', [DistributorController::class, 'index'])->name('index');
    Route::get('/create', [DistributorController::class, 'create'])->name('create');
    Route::post('/store', [DistributorController::class, 'store'])->name('store');
    Route::get('/statistics', [DistributorController::class, 'showStatistics'])->name('statistics');
    Route::get('/search', [DistributorController::class, 'search'])->name('search');
    Route::get('/level/{level}', [DistributorController::class, 'showByLevel'])->name('by-level');
    Route::get('/{id}/edit', [DistributorController::class, 'edit'])->name('edit');
    Route::put('/{id}/update', [DistributorController::class, 'update'])->name('update');
    Route::delete('/{id}/delete', [DistributorController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/tree', [DistributorController::class, 'showTree'])->name('tree');
    Route::get('/{id}/income-details', [DistributorController::class, 'showIncomeDetails'])->name('income-details');
    Route::get('/{id}', [DistributorController::class, 'show'])->name('show');
});

// Routes cho quản lý Orders
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    Route::post('/store', [OrderController::class, 'store'])->name('store');
    Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{id}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
    Route::get('/search', [OrderController::class, 'search'])->name('search');
});
