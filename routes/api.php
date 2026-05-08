<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

// Handle OPTIONS preflight
Route::options('/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Origin');
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| API Routes - SaleApp Mobile
|--------------------------------------------------------------------------
| Endpoint base: /api
*/

// Auth API
Route::post('/login', [AuthApiController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    // Dashboard summary
    Route::get('/dashboard', [ApiController::class, 'dashboard']);

    // Produk
    Route::get('/produk', [ProdukController::class, 'apiIndex']);
    Route::post('/produk', [ProdukController::class, 'apiStore']);
    Route::get('/produk/{id}', [ProdukController::class, 'apiShow']);
    Route::put('/produk/{id}', [ProdukController::class, 'apiUpdate']);
    Route::delete('/produk/{id}', [ProdukController::class, 'apiDestroy']);

    // Penjualan
    Route::get('/penjualan', [PenjualanController::class, 'apiIndex']);
    Route::post('/penjualan', [PenjualanController::class, 'apiStore']);
    Route::put('/penjualan/{id}', [PenjualanController::class, 'apiUpdate']);
    Route::delete('/penjualan/{id}', [PenjualanController::class, 'apiDestroy']);

    // Pembelian
    Route::get('/pembelian', [PembelianController::class, 'apiIndex']);
    Route::post('/pembelian', [PembelianController::class, 'apiStore']);
    Route::put('/pembelian/{id}', [PembelianController::class, 'apiUpdate']);
    Route::delete('/pembelian/{id}', [PembelianController::class, 'apiDestroy']);
});