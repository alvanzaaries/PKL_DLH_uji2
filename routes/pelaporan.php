<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndustriController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Pelaporan Routes (SIMPEL-HUT)
|--------------------------------------------------------------------------
|
| System: Sistem Monitoring dan Pelaporan Kehutanan
| Features: Portal Pelaporan, Upload Laporan, Rekapitulasi
|
*/

// Entry point utama (Public Access for Dashboard/Index)
Route::get('/pelaporan', [IndustriController::class, 'index'])->name('pelaporan.index');

// Admin Operations (Restricted)
Route::middleware(['auth', 'role:admin'])->prefix('pelaporan')->name('laporan.')->group(function() {
    Route::get('/upload', [LaporanController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/upload/preview', [LaporanController::class, 'preview'])->name('preview');
    Route::post('/upload/store', [LaporanController::class, 'store'])->name('store');
    Route::get('/rekap', [LaporanController::class, 'rekapLaporan'])->name('rekap');
    Route::get('/rekap/export', [LaporanController::class, 'exportRekapLaporan'])->name('rekap.export');
    
    // Route parameter di tengah
    Route::get('/{industri}/upload', [LaporanController::class, 'showByIndustri'])->name('industri');
    Route::get('/{industri}/detail/{id}', [LaporanController::class, 'detailLaporan'])->name('detail');
});
