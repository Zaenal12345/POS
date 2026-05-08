<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPenjualan = Penjualan::where('status', 'completed')->sum('total_harga');
        $totalPembelian = Pembelian::where('status', 'completed')->sum('total_harga');
        $totalProduk = Produk::count();
        $totalKategori = Kategori::count();

        $pembelians = Pembelian::with('produk.kategori')->latest()->limit(10)->get();
        $penjualans = Penjualan::with('produk.kategori')->latest()->limit(10)->get();

        $transaksis = $pembelians->merge($penjualans)
            ->sortByDesc('created_at')
            ->take(10);

        return view('dashboard', compact(
            'totalPenjualan', 'totalPembelian', 'totalProduk', 'totalKategori', 'transaksis'
        ));
    }
}
