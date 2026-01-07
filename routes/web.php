<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndustriPrimerController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Routes untuk Industri Primer
Route::get('/industri-primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
Route::get('/industri-primer/create', [IndustriPrimerController::class, 'create'])->name('industri-primer.create');
Route::post('/industri-primer', [IndustriPrimerController::class, 'store'])->name('industri-primer.store');
