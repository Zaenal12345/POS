<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\KeuntunganController;
use App\Http\Controllers\StoreSettingController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'))->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('kategori', KategoriController::class)->names([
        'index' => 'kategori.index',
        'store' => 'kategori.store',
        'update' => 'kategori.update',
        'destroy' => 'kategori.destroy',
    ])->except(['show']);
    Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');

    Route::resource('produk', ProdukController::class)->names([
        'index' => 'produk.index',
        'store' => 'produk.store',
        'update' => 'produk.update',
        'destroy' => 'produk.destroy',
    ])->except(['show']);
    Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');

    Route::resource('pembelian', PembelianController::class)->names([
        'index' => 'pembelian.index',
        'store' => 'pembelian.store',
        'update' => 'pembelian.update',
        'destroy' => 'pembelian.destroy',
    ])->except(['show']);
    Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');

    Route::resource('penjualan', PenjualanController::class)->names([
        'index' => 'penjualan.index',
        'store' => 'penjualan.store',
        'update' => 'penjualan.update',
        'destroy' => 'penjualan.destroy',
    ])->except(['show']);
    Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');

    // Receipt
    Route::get('/penjualan/{id}/receipt', [ReceiptController::class, 'show'])->name('receipt.show');
    Route::get('/penjualan/{id}/print', [ReceiptController::class, 'print'])->name('receipt.print');

    Route::resource('report', ReportController::class)->only('index')->names([
        'index' => 'report.index',
    ]);

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');

    Route::resource('keuntungan', KeuntunganController::class)->names([
        'index' => 'keuntungan.index',
        'store' => 'keuntungan.store',
        'destroy' => 'keuntungan.destroy',
    ])->except(['show']);

    // Settings
    Route::get('/settings', [StoreSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [StoreSettingController::class, 'store'])->name('settings.store');

    // Export
    Route::get('/export/pdf', [ExportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
});

require __DIR__.'/auth.php';
