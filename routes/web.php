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
})->name('welcome');

// PNBP public landing (non-auth) - shows external PNBP dashboard/intro
Route::get('/pnbp', function () {
    return view('PNBP.dashboard');
})->name('pnbp.landing');

// ===================================================================
// INDUSTRI (SIDI-HUT) - Public (Read Only)
// ===================================================================
Route::prefix('industri')->group(function () {
    // Dashboard Publik Industri
    Route::get('/dashboard', [DashboardController::class, 'publicIndex'])->name('industri.dashboard');

    // Resource Routes (Public Reading Index)
    Route::get('/primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
    Route::get('/sekunder', [IndustriSekunderController::class, 'index'])->name('industri-sekunder.index');
    Route::get('/tptkb', [TptkbController::class, 'index'])->name('tptkb.index');
    Route::get('/perajin', [PerajinController::class, 'index'])->name('perajin.index');

    // Public document download
    Route::get('/primer/{id}/dokumen', [IndustriPrimerController::class, 'downloadDokumen'])->name('industri-primer.download-dokumen');
});

// ===================================================================
// LEGACY URLS (Redirect to new system prefixes)
// ===================================================================
Route::get('/public/dashboard', fn () => redirect()->route('public.dashboard'));
Route::get('/industri-primer', fn () => redirect()->route('industri-primer.index'));
Route::get('/industri-sekunder', fn () => redirect()->route('industri-sekunder.index'));
Route::get('/tptkb', fn () => redirect()->route('tptkb.index'));
Route::get('/perajin', fn () => redirect()->route('perajin.index'));
Route::get('/industri-primer/{id}/dokumen', fn ($id) => redirect()->route('industri-primer.download-dokumen', ['id' => $id]));

// ===================================================================
// AUTH ROUTES
// ===================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===================================================================
// PNBP (Pelaporan PNBP) - Rekonsiliasi
// ===================================================================
Route::prefix('pnbp')->group(function () {
    // USER ROUTES (Role: user) - hanya PNBP
    Route::middleware(['auth', 'session.timeout', 'role:user'])->group(function () {
        Route::get('/upload', [UserDashboardController::class, 'upload'])->name('user.upload');
        Route::get('/history', [UserDashboardController::class, 'history'])->name('user.history');
        Route::get('/home', function () {
            return redirect()->route('user.upload');
        })->name('user.dashboard');
    });

    // Upload endpoint (admin + user) - REKONSILIASI
    Route::middleware(['auth', 'session.timeout', 'role:admin,user'])->group(function () {
        Route::post('/reconciliations', [ReconciliationController::class, 'store'])->name('reconciliations.store');
    });

    // ADMIN ROUTES (Role: admin) - PNBP
    Route::middleware(['auth', 'session.timeout', 'role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export');

        // User Management
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::post('/admin/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');

        // Reconciliations (Admin CRUD)
        Route::get('/reconciliations/create', [ReconciliationController::class, 'create'])->name('reconciliations.create');
        Route::resource('reconciliations', ReconciliationController::class)
            ->except(['create', 'store']);
        Route::get('/reconciliations/{reconciliation}/file', [ReconciliationController::class, 'downloadFile'])->name('reconciliations.file');
        Route::get('/reconciliations/{reconciliation}/raw', [ReconciliationController::class, 'rawExcel'])->name('reconciliations.raw');
        Route::get('/reconciliations/{reconciliation}/export-pdf', [ReconciliationController::class, 'exportPdf'])->name('reconciliations.export-pdf');
        Route::post('/reconciliations/{reconciliation}/summary-overrides', [ReconciliationController::class, 'updateSummaryOverrides'])->name('reconciliations.summary-overrides');
    });
});

// Legacy PNBP URLs
Route::get('/dashboard', fn () => redirect()->route('dashboard.index'));
Route::get('/user/upload', fn () => redirect()->route('user.upload'));
Route::get('/user/history', fn () => redirect()->route('user.history'));
Route::get('/user/dashboard', fn () => redirect()->route('user.dashboard'));

// ===================================================================
// INDUSTRI (SIDI-HUT) - Admin CRUD
// ===================================================================
Route::prefix('industri')->middleware(['auth', 'session.timeout', 'role:admin'])->group(function () {
    // Industri Primer - CRUD
    Route::get('/primer/create', [IndustriPrimerController::class, 'create'])->name('industri-primer.create');
    Route::post('/primer', [IndustriPrimerController::class, 'store'])->name('industri-primer.store');
    Route::get('/primer/{id}/edit', [IndustriPrimerController::class, 'edit'])->name('industri-primer.edit');
    Route::put('/primer/{id}', [IndustriPrimerController::class, 'update'])->name('industri-primer.update');
    Route::delete('/primer/{id}', [IndustriPrimerController::class, 'destroy'])->name('industri-primer.destroy');

    // Industri Sekunder - CRUD
    Route::get('/sekunder/create', [IndustriSekunderController::class, 'create'])->name('industri-sekunder.create');
    Route::post('/sekunder', [IndustriSekunderController::class, 'store'])->name('industri-sekunder.store');
    Route::get('/sekunder/{id}/edit', [IndustriSekunderController::class, 'edit'])->name('industri-sekunder.edit');
    Route::put('/sekunder/{id}', [IndustriSekunderController::class, 'update'])->name('industri-sekunder.update');
    Route::delete('/sekunder/{id}', [IndustriSekunderController::class, 'destroy'])->name('industri-sekunder.destroy');

    // TPTKB - CRUD
    Route::get('/tptkb/create', [TptkbController::class, 'create'])->name('tptkb.create');
    Route::post('/tptkb', [TptkbController::class, 'store'])->name('tptkb.store');
    Route::get('/tptkb/{id}/edit', [TptkbController::class, 'edit'])->name('tptkb.edit');
    Route::put('/tptkb/{id}', [TptkbController::class, 'update'])->name('tptkb.update');
    Route::delete('/tptkb/{id}', [TptkbController::class, 'destroy'])->name('tptkb.destroy');

    // Perajin - CRUD
    Route::get('/perajin/create', [PerajinController::class, 'create'])->name('perajin.create');
    Route::post('/perajin', [PerajinController::class, 'store'])->name('perajin.store');
    Route::get('/perajin/{id}/edit', [PerajinController::class, 'edit'])->name('perajin.edit');
    Route::put('/perajin/{id}', [PerajinController::class, 'update'])->name('perajin.update');
    Route::delete('/perajin/{id}', [PerajinController::class, 'destroy'])->name('perajin.destroy');
});

// ===================================================================
// PELAPORAN (SIMPEL-HUT)
// ===================================================================
// Entry point (Dashboard Pelaporan) - public read access
Route::get('/laporan', [IndustriController::class, 'index'])->name('laporan.index');

// Admin operations (upload/rekap/detail) tetap khusus admin
Route::prefix('laporan')->middleware(['auth', 'session.timeout', 'role:admin'])->group(function () {

    // Upload + proses pelaporan
    Route::get('/upload', [LaporanController::class, 'showUploadForm'])->name('laporan.upload.form');
    Route::post('/upload/preview', [LaporanController::class, 'preview'])->name('laporan.preview');
    Route::post('/upload/store', [LaporanController::class, 'store'])->name('laporan.store');

    // Rekap
    Route::get('/rekap', [LaporanController::class, 'rekapLaporan'])->name('laporan.rekap');
    Route::get('/rekap/export', [LaporanController::class, 'exportRekapLaporan'])->name('laporan.rekap.export');

    // Per industri
    Route::get('/{industri}/upload', [LaporanController::class, 'showByIndustri'])->name('laporan.industri');
    Route::get('/{industri}/detail/{id}', [LaporanController::class, 'detailLaporan'])->name('laporan.detail');
});

