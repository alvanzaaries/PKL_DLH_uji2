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
// PUBLIC ROUTES - Accessible without login (From Incoming Project)
// ===================================================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public viewing routes from Incoming (visualisasi publik)
Route::get('/public/dashboard', [DashboardController::class, 'publicIndex'])->name('public.dashboard');
Route::get('/industri-primer', [IndustriPrimerController::class, 'index'])->name('industri-primer.index');
Route::get('/industri-sekunder', [IndustriSekunderController::class, 'index'])->name('industri-sekunder.index');
Route::get('/tptkb', [TptkbController::class, 'index'])->name('tptkb.index');
Route::get('/perajin', [PerajinController::class, 'index'])->name('perajin.index');
Route::get('/laporan', [IndustriController::class, 'index'])->name('data.industri');

// Public document download routes
Route::get('/industri-primer/{id}/dokumen', [IndustriPrimerController::class, 'downloadDokumen'])->name('industri-primer.download-dokumen');

// ===================================================================
// AUTH ROUTES
// ===================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===================================================================
// USER ROUTES (Role: user) - From HEAD
// ===================================================================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/upload', [UserDashboardController::class, 'upload'])->name('user.upload');
    Route::get('/user/history', [UserDashboardController::class, 'history'])->name('user.history');
    Route::get('/user/dashboard', function () {
        return redirect()->route('user.upload');
    })->name('user.dashboard');
});

// Upload endpoint (admin + user)
Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::post('reconciliations', [ReconciliationController::class, 'store'])->name('reconciliations.store');
});

// ===================================================================
// ADMIN ROUTES (Role: admin) - From HEAD
// ===================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // User Management
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');

    Route::get('reconciliations/create', [ReconciliationController::class, 'create'])->name('reconciliations.create');

    Route::resource('reconciliations', ReconciliationController::class)
        ->except(['create', 'store']);

    // File download for stored Excel (verification)
    Route::get('reconciliations/{reconciliation}/file', [ReconciliationController::class, 'downloadFile'])->name('reconciliations.file');

    // Raw view for uploaded Excel (unprocessed)
    Route::get('reconciliations/{reconciliation}/raw', [ReconciliationController::class, 'rawExcel'])->name('reconciliations.raw');

    // Summary overrides (edit top totals)
    Route::post('reconciliations/{reconciliation}/summary-overrides', [ReconciliationController::class, 'updateSummaryOverrides'])->name('reconciliations.summary-overrides');
});

// ===================================================================
// PROTECTED ROUTES FROM INCOMING (Auth only, no role check)
// These are for the public/visualisasi project CRUD operations
// ===================================================================
Route::middleware(['auth'])->group(function () {
    // Industri Primer - Create, Update, Delete
    Route::get('/industri-primer/create', [IndustriPrimerController::class, 'create'])->name('industri-primer.create');
    Route::post('/industri-primer', [IndustriPrimerController::class, 'store'])->name('industri-primer.store');
    Route::get('/industri-primer/{id}/edit', [IndustriPrimerController::class, 'edit'])->name('industri-primer.edit');
    Route::put('/industri-primer/{id}', [IndustriPrimerController::class, 'update'])->name('industri-primer.update');
    Route::delete('/industri-primer/{id}', [IndustriPrimerController::class, 'destroy'])->name('industri-primer.destroy');

    // Industri Sekunder - Create, Update, Delete
    Route::get('/industri-sekunder/create', [IndustriSekunderController::class, 'create'])->name('industri-sekunder.create');
    Route::post('/industri-sekunder', [IndustriSekunderController::class, 'store'])->name('industri-sekunder.store');
    Route::get('/industri-sekunder/{id}/edit', [IndustriSekunderController::class, 'edit'])->name('industri-sekunder.edit');
    Route::put('/industri-sekunder/{id}', [IndustriSekunderController::class, 'update'])->name('industri-sekunder.update');
    Route::delete('/industri-sekunder/{id}', [IndustriSekunderController::class, 'destroy'])->name('industri-sekunder.destroy');

    // TPTKB - Create, Update, Delete
    Route::get('/tptkb/create', [TptkbController::class, 'create'])->name('tptkb.create');
    Route::post('/tptkb', [TptkbController::class, 'store'])->name('tptkb.store');
    Route::get('/tptkb/{id}/edit', [TptkbController::class, 'edit'])->name('tptkb.edit');
    Route::put('/tptkb/{id}', [TptkbController::class, 'update'])->name('tptkb.update');
    Route::delete('/tptkb/{id}', [TptkbController::class, 'destroy'])->name('tptkb.destroy');

    // Perajin - Create, Update, Delete
    Route::get('/perajin/create', [PerajinController::class, 'create'])->name('perajin.create');
    Route::post('/perajin', [PerajinController::class, 'store'])->name('perajin.store');
    Route::get('/perajin/{id}/edit', [PerajinController::class, 'edit'])->name('perajin.edit');
    Route::put('/perajin/{id}', [PerajinController::class, 'update'])->name('perajin.update');
    Route::delete('/perajin/{id}', [PerajinController::class, 'destroy'])->name('perajin.destroy');

    // Laporan Routes
    Route::get('/laporan/upload', [LaporanController::class, 'showUploadForm'])->name('laporan.upload.form');
    Route::post('/laporan/upload/preview', [LaporanController::class, 'preview'])->name('laporan.preview');
    Route::post('/laporan/upload/store', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{industri}/upload', [LaporanController::class, 'showByIndustri'])->name('industri.laporan');
    Route::get('/laporan/rekap', [LaporanController::class, 'rekapLaporan'])->name('laporan.rekap');
    Route::get('/laporan/rekap/export', [LaporanController::class, 'exportRekapLaporan'])->name('laporan.rekap.export');
    Route::get('/laporan/{industri}/detail/{id}', [LaporanController::class, 'detailLaporan'])->name('laporan.detail');
});

