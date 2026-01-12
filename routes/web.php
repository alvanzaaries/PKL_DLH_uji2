<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndustriPrimerController;
use App\Http\Controllers\IndustriSekunderController;
use App\Http\Controllers\TptkbController;
use App\Http\Controllers\PerajinController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Routes untuk Industri Primer
Route::get('/industri-primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
Route::get('/industri-primer/create', [IndustriPrimerController::class, 'create'])->name('industri-primer.create');
Route::post('/industri-primer', [IndustriPrimerController::class, 'store'])->name('industri-primer.store');
Route::get('/industri-primer/{id}/edit', [IndustriPrimerController::class, 'edit'])->name('industri-primer.edit');
Route::put('/industri-primer/{id}', [IndustriPrimerController::class, 'update'])->name('industri-primer.update');
Route::delete('/industri-primer/{id}', [IndustriPrimerController::class, 'destroy'])->name('industri-primer.destroy');

// Routes untuk Industri Sekunder
Route::get('/industri-sekunder', [IndustriSekunderController::class, 'index'])->name('industri-sekunder.index');
Route::get('/industri-sekunder/create', [IndustriSekunderController::class, 'create'])->name('industri-sekunder.create');
Route::post('/industri-sekunder', [IndustriSekunderController::class, 'store'])->name('industri-sekunder.store');
Route::get('/industri-sekunder/{id}/edit', [IndustriSekunderController::class, 'edit'])->name('industri-sekunder.edit');
Route::put('/industri-sekunder/{id}', [IndustriSekunderController::class, 'update'])->name('industri-sekunder.update');
Route::delete('/industri-sekunder/{id}', [IndustriSekunderController::class, 'destroy'])->name('industri-sekunder.destroy');

// TPTKB Routes
Route::get('/tptkb', [TptkbController::class, 'index'])->name('tptkb.index');
Route::get('/tptkb/create', [TptkbController::class, 'create'])->name('tptkb.create');
Route::post('/tptkb', [TptkbController::class, 'store'])->name('tptkb.store');
Route::get('/tptkb/{id}/edit', [TptkbController::class, 'edit'])->name('tptkb.edit');
Route::put('/tptkb/{id}', [TptkbController::class, 'update'])->name('tptkb.update');
Route::delete('/tptkb/{id}', [TptkbController::class, 'destroy'])->name('tptkb.destroy');

// Perajin Routes
Route::resource('perajin', PerajinController::class);

