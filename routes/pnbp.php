<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| PNBP Routes (SIP-JATENG)
|--------------------------------------------------------------------------
|
| System: Sistem Pengelolaan PNBP Jawa Tengah
| Features: Reconciliation, User Upload, Admin Dashboard, User Management
|
*/

// USER ROUTES (Role: user)
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

// ADMIN ROUTES (Role: admin)
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
