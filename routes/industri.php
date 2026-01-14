<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TptkbController;
use App\Http\Controllers\PerajinController;
use App\Http\Controllers\IndustriPrimerController;
use App\Http\Controllers\IndustriSekunderController;

/*
|--------------------------------------------------------------------------
| Industri Routes (SIDI-HUT)
|--------------------------------------------------------------------------
|
| System: Sistem Informasi Database Industri Kehutanan
| Features: Public Dashboard, Industri Data (Primer, Sekunder, TPTKB, Perajin)
|
*/

// Dashboard Publik (Visualisasi Industri)
Route::get('/public/dashboard', [DashboardController::class, 'publicIndex'])->name('public.dashboard');

// Public Resource Indices (Read Only)
Route::get('/industri-primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
Route::get('/industri-sekunder', [IndustriSekunderController::class, 'index'])->name('industri-sekunder.index');
Route::get('/tptkb', [TptkbController::class, 'index'])->name('tptkb.index');
Route::get('/perajin', [PerajinController::class, 'index'])->name('perajin.index');

// Public document download
Route::get('/industri-primer/{id}/dokumen', [IndustriPrimerController::class, 'downloadDokumen'])->name('industri-primer.download-dokumen');

// Admin CRUD Operations (Restricted)
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // Industri Primer - CRUD
    Route::prefix('industri-primer')->name('industri-primer.')->group(function() {
        Route::get('/create', [IndustriPrimerController::class, 'create'])->name('create');
        Route::post('/', [IndustriPrimerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [IndustriPrimerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IndustriPrimerController::class, 'update'])->name('update');
        Route::delete('/{id}', [IndustriPrimerController::class, 'destroy'])->name('destroy');
    });

    // Industri Sekunder - CRUD
    Route::prefix('industri-sekunder')->name('industri-sekunder.')->group(function() {
        Route::get('/create', [IndustriSekunderController::class, 'create'])->name('create');
        Route::post('/', [IndustriSekunderController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [IndustriSekunderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IndustriSekunderController::class, 'update'])->name('update');
        Route::delete('/{id}', [IndustriSekunderController::class, 'destroy'])->name('destroy');
    });

    // TPTKB - CRUD
    Route::prefix('tptkb')->name('tptkb.')->group(function() {
        Route::get('/create', [TptkbController::class, 'create'])->name('create');
        Route::post('/', [TptkbController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TptkbController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TptkbController::class, 'update'])->name('update');
        Route::delete('/{id}', [TptkbController::class, 'destroy'])->name('destroy');
    });

    // Perajin - CRUD
    Route::prefix('perajin')->name('perajin.')->group(function() {
        Route::get('/create', [PerajinController::class, 'create'])->name('create');
        Route::post('/', [PerajinController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PerajinController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PerajinController::class, 'update'])->name('update');
        Route::delete('/{id}', [PerajinController::class, 'destroy'])->name('destroy');
    });
});
