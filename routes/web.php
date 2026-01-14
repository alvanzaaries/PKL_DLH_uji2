<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndustriPrimerController;
use App\Http\Controllers\IndustriSekunderController;
use App\Http\Controllers\TptkbController;
use App\Http\Controllers\PerajinController;

// Public routes - accessible without login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Public viewing routes - accessible without login
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/test-layout', function () {
    return view('test-layout');
})->name('test.layout');
Route::get('/industri-primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
Route::get('/industri-sekunder', [IndustriSekunderController::class, 'index'])->name('industri-sekunder.index');
Route::get('/tptkb', [TptkbController::class, 'index'])->name('tptkb.index');
Route::get('/perajin', [PerajinController::class, 'index'])->name('perajin.index');

// Public document download routes
Route::get('/industri-primer/{id}/dokumen', [IndustriPrimerController::class, 'downloadDokumen'])->name('industri-primer.download-dokumen');

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Industri Primer - Create, Update, Delete only
    Route::get('/industri-primer/create', [IndustriPrimerController::class, 'create'])->name('industri-primer.create');
    Route::post('/industri-primer', [IndustriPrimerController::class, 'store'])->name('industri-primer.store');
    Route::get('/industri-primer/{id}/edit', [IndustriPrimerController::class, 'edit'])->name('industri-primer.edit');
    Route::put('/industri-primer/{id}', [IndustriPrimerController::class, 'update'])->name('industri-primer.update');
    Route::delete('/industri-primer/{id}', [IndustriPrimerController::class, 'destroy'])->name('industri-primer.destroy');

    // Industri Sekunder - Create, Update, Delete only
    Route::get('/industri-sekunder/create', [IndustriSekunderController::class, 'create'])->name('industri-sekunder.create');
    Route::post('/industri-sekunder', [IndustriSekunderController::class, 'store'])->name('industri-sekunder.store');
    Route::get('/industri-sekunder/{id}/edit', [IndustriSekunderController::class, 'edit'])->name('industri-sekunder.edit');
    Route::put('/industri-sekunder/{id}', [IndustriSekunderController::class, 'update'])->name('industri-sekunder.update');
    Route::delete('/industri-sekunder/{id}', [IndustriSekunderController::class, 'destroy'])->name('industri-sekunder.destroy');

    // TPTKB - Create, Update, Delete only
    Route::get('/tptkb/create', [TptkbController::class, 'create'])->name('tptkb.create');
    Route::post('/tptkb', [TptkbController::class, 'store'])->name('tptkb.store');
    Route::get('/tptkb/{id}/edit', [TptkbController::class, 'edit'])->name('tptkb.edit');
    Route::put('/tptkb/{id}', [TptkbController::class, 'update'])->name('tptkb.update');
    Route::delete('/tptkb/{id}', [TptkbController::class, 'destroy'])->name('tptkb.destroy');

    // Perajin - Create, Update, Delete only
    Route::get('/perajin/create', [PerajinController::class, 'create'])->name('perajin.create');
    Route::post('/perajin', [PerajinController::class, 'store'])->name('perajin.store');
    Route::get('/perajin/{id}/edit', [PerajinController::class, 'edit'])->name('perajin.edit');
    Route::put('/perajin/{id}', [PerajinController::class, 'update'])->name('perajin.update');
    Route::delete('/perajin/{id}', [PerajinController::class, 'destroy'])->name('perajin.destroy');
});

