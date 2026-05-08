<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Dashboard summary untuk SaleApp mobile
     */
    public function dashboard(): JsonResponse
    {
        $totalProduk = Produk::count();
        $totalStok = Produk::sum('stok');
        $produkRendahStok = Produk::where('stok', '<=', 10)->where('tanpa_stok', false)->count();
        $totalKategori = Kategori::count();

        // Penjualan bulan ini
        $penjualanBulanIni = Penjualan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');

        $penjualanCount = Penjualan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Pembelian bulan ini
        $pembelianBulanIni = Pembelian::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');

        // Produk terlaris (top 5 by total sold this month)
        $produkTerlaris = Produk::with('kategori')
            ->withSum(['penjualans' => fn($q) => $q->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)], 'jumlah')
            ->orderByDesc('penjualans_sum_jumlah')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'sku' => 'SKU-' . str_pad($p->id, 5, '0', STR_PAD_LEFT),
                'stok' => $p->stok,
                'harga_jual' => $p->harga_jual,
                'kategori' => $p->kategori?->nama,
                'total_terjual' => $p->penjualans_sum_jumlah ?? 0,
            ]);

        // Ringkasan stok per kategori
        $stokPerKategori = Kategori::all()->map(fn($k) => [
            'kategori' => $k->nama,
            'jumlah_produk' => $k->produks()->count(),
            'total_stok' => $k->produks()->sum('stok'),
        ]);

        // Data grafik tren 7 hari terakhir
        $trenHarian = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i);
            $masuk = Pembelian::whereDate('created_at', $date)->sum('jumlah');
            $keluar = Penjualan::whereDate('created_at', $date)->sum('jumlah');
            return [
                'tanggal' => $date->format('d M'),
                'stok_masuk' => $masuk,
                'stok_keluar' => $keluar,
            ];
        })->reverse()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'total_produk' => $totalProduk,
                'total_stok' => $totalStok,
                'produk_rendah_stok' => $produkRendahStok,
                'total_kategori' => $totalKategori,
                'penjualan_bulan_ini' => $penjualanBulanIni,
                'penjualan_count' => $penjualanCount,
                'pembelian_bulan_ini' => $pembelianBulanIni,
                'produk_terlaris' => $produkTerlaris,
                'stok_per_kategori' => $stokPerKategori,
                'tren_harian' => $trenHarian,
            ],
        ]);
    }
}