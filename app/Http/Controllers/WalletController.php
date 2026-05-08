<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Keuntungan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        $bulan = (int) $bulan;
        $tahun = (int) $tahun;

        // Pengeluaran: total pembelian bulan ini
        $pengeluaran = Pembelian::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'completed')
            ->sum('total_harga');

        // Penjualan: total penjualan bulan ini
        $penjualanTotal = Penjualan::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'completed')
            ->sum('total_harga');

        // Keuntungan manual dari input user
        $keuntunganMasuk = Keuntungan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('tipe', 'masuk')
            ->sum('jumlah');

        $keuntunganKeluar = Keuntungan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('tipe', 'keluar')
            ->sum('jumlah');

        $keuntunganManual = $keuntunganMasuk - $keuntunganKeluar;

        // Keuntungan: sum(subtotal - (harga_beli * jumlah)) untuk semua items di bulan ini
        $items = PenjualanItem::whereHas('penjualan', function($q) use ($bulan, $tahun) {
            $q->whereMonth('created_at', $bulan)
              ->whereYear('created_at', $tahun)
              ->where('status', 'completed');
        })->get();

        $totalHargaBeliItems = 0;
        foreach ($items as $item) {
            $totalHargaBeliItems += $item->harga_beli * $item->jumlah;
        }
        $keuntungan = ($penjualanTotal - $totalHargaBeliItems) + $keuntunganManual;

        // Saldo bersih + keuntungan manual
        $saldo = $penjualanTotal - $pengeluaran + $keuntunganManual;

        // Data grafik per hari dalam bulan
        $tanggalMulai = Carbon::create($tahun, $bulan, 1);
        $tanggalAkhir = $tanggalMulai->copy()->endOfMonth();
        $hariTotal = $tanggalAkhir->day;

        $labels = [];
        $dataPengeluaran = [];
        $dataPemasukkan = [];
        $dataKeuntungan = [];

        for ($d = 1; $d <= $hariTotal; $d++) {
            $tgl = Carbon::create($tahun, $bulan, $d);
            $labels[] = $d;

            $p = Pembelian::whereDate('created_at', $tgl->format('Y-m-d'))
                ->where('status', 'completed')
                ->sum('total_harga');
            $dataPengeluaran[] = (int) $p;

            $j = Penjualan::whereDate('created_at', $tgl->format('Y-m-d'))
                ->where('status', 'completed')
                ->sum('total_harga');
            $dataPemasukkan[] = (int) $j;

            // Keuntungan harian + manual keuntungan harian
            $dailyItems = PenjualanItem::whereHas('penjualan', function($q) use ($tgl) {
                $q->whereDate('created_at', $tgl->format('Y-m-d'))
                  ->where('status', 'completed');
            })->get();
            $dailyBeli = 0;
            foreach ($dailyItems as $it) {
                $dailyBeli += $it->harga_beli * $it->jumlah;
            }
            $dailyJual = $j;
            $dailyKeuntunganManual = Keuntungan::whereDate('tanggal', $tgl->format('Y-m-d'))
                ->where('tipe', 'masuk')
                ->sum('jumlah');
            $dailyKeuntunganKeluar = Keuntungan::whereDate('tanggal', $tgl->format('Y-m-d'))
                ->where('tipe', 'keluar')
                ->sum('jumlah');
            $dataKeuntungan[] = max(0, $dailyJual - $dailyBeli) + ($dailyKeuntunganManual - $dailyKeuntunganKeluar);
        }

        // Ringkasan per kategori (keuntungan)
        $kategoriKeuntungan = [];
        $kategoriItems = PenjualanItem::with('produk.kategori')
            ->whereHas('penjualan', function($q) use ($bulan, $tahun) {
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun)
                  ->where('status', 'completed');
            })->get();

        foreach ($kategoriItems as $item) {
            $kat = $item->produk?->kategori?->nama ?? 'Tanpa Kategori';
            if (!isset($kategoriKeuntungan[$kat])) {
                $kategoriKeuntungan[$kat] = ['total_beli' => 0, 'total_jual' => 0, 'count' => 0];
            }
            $kategoriKeuntungan[$kat]['total_beli'] += $item->harga_beli * $item->jumlah;
            $kategoriKeuntungan[$kat]['total_jual'] += $item->subtotal;
            $kategoriKeuntungan[$kat]['count'] += 1;
        }
        foreach ($kategoriKeuntungan as $kat => &$data) {
            $data['keuntungan'] = $data['total_jual'] - $data['total_beli'];
        }
        unset($data);

        return view('wallet.index', compact(
            'pengeluaran', 'penjualanTotal', 'keuntungan', 'saldo',
            'bulan', 'tahun',
            'labels', 'dataPengeluaran', 'dataPemasukkan', 'dataKeuntungan',
            'kategoriKeuntungan', 'keuntunganMasuk', 'keuntunganKeluar', 'keuntunganManual'
        ));
    }
}