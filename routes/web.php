<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReconciliationController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('reconciliations', ReconciliationController::class);

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

