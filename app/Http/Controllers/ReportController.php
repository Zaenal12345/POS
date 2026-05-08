<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Keuntungan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $pembelians = Pembelian::with('produk.kategori')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $penjualans = Penjualan::with('produk.kategori')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $keuntuangans = Keuntungan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $totalPembelian = $pembelians->sum('total_harga');
        $totalPenjualan = $penjualans->sum('total_harga');
        $totalProduk = Produk::count();
        $totalKategori = Kategori::count();

        $keuntunganMasuk = $keuntuangans->where('tipe', 'masuk')->sum('jumlah');
        $keuntunganKeluar = $keuntuangans->where('tipe', 'keluar')->sum('jumlah');
        $keuntunganManual = $keuntunganMasuk - $keuntunganKeluar;

        $laba = $totalPenjualan - $totalPembelian + $keuntunganManual;

        return view('report.index', compact(
            'pembelians', 'penjualans', 'totalPembelian',
            'totalPenjualan', 'totalProduk', 'totalKategori', 'laba', 'bulan', 'tahun',
            'keuntunganMasuk', 'keuntunganKeluar', 'keuntunganManual'
        ));
    }
}
