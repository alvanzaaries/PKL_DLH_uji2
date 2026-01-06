<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReconciliationController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('reconciliations', ReconciliationController::class);
