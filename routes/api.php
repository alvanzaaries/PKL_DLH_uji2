<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiLaporanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Protected API endpoint untuk upload laporan
// Requires API key authentication via X-API-Key header
// Rate limited to 60 requests per minute
Route::middleware(['api.key', 'throttle:60,1'])->group(function () {
    Route::post('/laporan/upload', [ApiLaporanController::class, 'upload']);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
});
