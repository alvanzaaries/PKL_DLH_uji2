<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers from HEAD (Admin/Internal)
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;

// Controllers from Incoming (Public/Visualisasi)
use App\Http\Controllers\TptkbController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PerajinController;
use App\Http\Controllers\IndustriController;
use App\Http\Controllers\IndustriPrimerController;
use App\Http\Controllers\IndustriSekunderController;

// ===================================================================
// PUBLIC ROUTES - Accessible without login
// ===================================================================

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard Publik (Visualisasi Industri - untuk SIDI-HUT)
Route::get('/public/dashboard', [DashboardController::class, 'publicIndex'])->name('public.dashboard');

// Resource Routes (Public Reading Index)
// Digunakan oleh Dashboard Publik (Visualisasi)
Route::get('/industri-primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
Route::get('/industri-sekunder', [IndustriSekunderController::class, 'index'])->name('industri-sekunder.index');
Route::get('/tptkb', [TptkbController::class, 'index'])->name('tptkb.index');
Route::get('/perajin', [PerajinController::class, 'index'])->name('perajin.index');

// SIMPEL-HUT (Sistem Monitoring dan Pelaporan Kehutanan)
// Entry point utama untuk modul pelaporan (halaman dashboard laporan)
Route::get('/pelaporan', [IndustriController::class, 'index'])->name('pelaporan.index');

// Public document download
Route::get('/industri-primer/{id}/dokumen', [IndustriPrimerController::class, 'downloadDokumen'])->name('industri-primer.download-dokumen');

// ===================================================================
// AUTH ROUTES
// ===================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===================================================================
// USER ROUTES (Role: user) - MODUL REKONSILIASI (PNBP)
// ===================================================================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/upload', [UserDashboardController::class, 'upload'])->name('user.upload');
    Route::get('/user/history', [UserDashboardController::class, 'history'])->name('user.history');
    Route::get('/user/dashboard', function () {
        return redirect()->route('user.upload');
    })->name('user.dashboard');
});

// Upload endpoint (admin + user) - REKONSILIASI
Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::post('reconciliations', [ReconciliationController::class, 'store'])->name('reconciliations.store');
});

// ===================================================================
// ADMIN ROUTES (Role: admin) - MODUL REKONSILIASI (PNBP)
// ===================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard (SIP-JATENG)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // User Management
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');

    // Reconciliations (Admin CRUD)
    Route::get('reconciliations/create', [ReconciliationController::class, 'create'])->name('reconciliations.create');
    Route::resource('reconciliations', ReconciliationController::class)
        ->except(['create', 'store']);
    Route::get('reconciliations/{reconciliation}/file', [ReconciliationController::class, 'downloadFile'])->name('reconciliations.file');
    Route::get('reconciliations/{reconciliation}/raw', [ReconciliationController::class, 'rawExcel'])->name('reconciliations.raw');
    Route::post('reconciliations/{reconciliation}/summary-overrides', [ReconciliationController::class, 'updateSummaryOverrides'])->name('reconciliations.summary-overrides');
});

// ===================================================================
// MODUL PELAPORAN & CRUD DATA INDUSTRI (SIMPEL-HUT / SIDI-HUT Auth)
// ===================================================================
Route::middleware(['auth'])->group(function () {
    
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

    // Fitur Pelaporan (SIMPEL-HUT)
    Route::prefix('pelaporan')->name('laporan.')->group(function() {
        Route::get('/upload', [LaporanController::class, 'showUploadForm'])->name('upload.form');
        Route::post('/upload/preview', [LaporanController::class, 'preview'])->name('preview');
        Route::post('/upload/store', [LaporanController::class, 'store'])->name('store');
        Route::get('/rekap', [LaporanController::class, 'rekapLaporan'])->name('rekap');
        Route::get('/rekap/export', [LaporanController::class, 'exportRekapLaporan'])->name('rekap.export');
        
        // Route parameter di tengah
        Route::get('/{industri}/upload', [LaporanController::class, 'showByIndustri'])->name('industri');
        Route::get('/{industri}/detail/{id}', [LaporanController::class, 'detailLaporan'])->name('detail');
    });
});

