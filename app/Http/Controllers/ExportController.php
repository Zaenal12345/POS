<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Keuntungan;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        return view('export.index', compact('bulan', 'tahun'));
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$bulan - 1];

        $pembelians = Pembelian::with('produk.kategori')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'completed')
            ->get();

        $penjualans = Penjualan::with('items.produk')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'completed')
            ->get();

        $keuntungans = Keuntungan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $totalPembelian = $pembelians->sum('total_harga');
        $totalPenjualan = $penjualans->sum('total_harga');
        $keuntunganMasuk = $keuntungans->where('tipe', 'masuk')->sum('jumlah');
        $keuntunganKeluar = $keuntungans->where('tipe', 'keluar')->sum('jumlah');
        $keuntunganManual = $keuntunganMasuk - $keuntunganKeluar;

        $items = PenjualanItem::whereHas('penjualan', function($q) use ($bulan, $tahun) {
            $q->whereMonth('created_at', $bulan)
              ->whereYear('created_at', $tahun)
              ->where('status', 'completed');
        })->get();

        $totalHargaBeli = $items->sum(fn($i) => $i->harga_beli * $i->jumlah);
        $labaKotor = $totalPenjualan - $totalHargaBeli;
        $labaBersih = $labaKotor + $keuntunganManual;

        $settings = StoreSetting::allSettings();

        $data = [
            'settings' => $settings,
            'bulan' => $namaBulan,
            'tahun' => $tahun,
            'pembelians' => $pembelians,
            'penjualans' => $penjualans,
            'totalPembelian' => $totalPembelian,
            'totalPenjualan' => $totalPenjualan,
            'keuntunganMasuk' => $keuntunganMasuk,
            'keuntunganKeluar' => $keuntunganKeluar,
            'keuntunganManual' => $keuntunganManual,
            'labaKotor' => $labaKotor,
            'labaBersih' => $labaBersih,
            'tanggal' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('export.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan_' . $namaBulan . '_' . $tahun . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$bulan - 1];

        $pembelians = Pembelian::with('produk')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'completed')
            ->get();

        $penjualans = Penjualan::with('items.produk')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'completed')
            ->get();

        $keuntungans = Keuntungan::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $settings = StoreSetting::allSettings();

        // Create CSV content
        $csv = [];

        // Header Toko
        $csv[] = [$settings['nama_toko'] ?? 'Toko Kasir'];
        $csv[] = ['Laporan Bulanan: ' . $namaBulan . ' ' . $tahun];
        $csv[] = ['Dicetak: ' . Carbon::now()->format('d/m/Y H:i')];
        $csv[] = [];

        // Ringkasan
        $csv[] = ['RINGKASAN'];
        $csv[] = ['Total Penjualan', 'Rp ' . number_format($penjualans->sum('total_harga'))];
        $csv[] = ['Total Pembelian', 'Rp ' . number_format($pembelians->sum('total_harga'))];
        $csv[] = ['Keuntungan Manual (+)', 'Rp ' . number_format($keuntungans->where('tipe', 'masuk')->sum('jumlah'))];
        $csv[] = ['Pengeluaran Manual (-)', 'Rp ' . number_format($keuntungans->where('tipe', 'keluar')->sum('jumlah'))];
        $csv[] = [];

        // Detail Penjualan
        $csv[] = ['DETAIL PENJUALAN'];
        $csv[] = ['ID', 'Tanggal', 'Pelanggan', 'Total', 'Status'];
        foreach ($penjualans as $p) {
            $csv[] = [
                '#' . $p->id,
                $p->created_at->format('d/m/Y H:i'),
                $p->pelanggan_nama ?? 'Umum',
                'Rp ' . number_format($p->total_harga),
                $p->status
            ];
        }
        $csv[] = [];

        // Detail Pembelian
        $csv[] = ['DETAIL PEMBELIAN'];
        $csv[] = ['ID', 'Tanggal', 'Produk', 'Jumlah', 'Total'];
        foreach ($pembelians as $p) {
            $csv[] = [
                '#' . $p->id,
                $p->created_at->format('d/m/Y H:i'),
                $p->produk?->nama ?? '-',
                $p->jumlah,
                'Rp ' . number_format($p->total_harga)
            ];
        }
        $csv[] = [];

        // Detail Keuntungan Manual
        $csv[] = ['DETAIL KEUNTUNGAN MANUAL'];
        $csv[] = ['Tanggal', 'Tipe', 'Jumlah', 'Keterangan'];
        foreach ($keuntungans as $k) {
            $csv[] = [
                Carbon::parse($k->tanggal)->format('d/m/Y'),
                $k->tipe === 'masuk' ? 'Masuk' : 'Keluar',
                'Rp ' . number_format($k->jumlah),
                $k->keterangan ?? '-'
            ];
        }

        // Generate CSV content
        $content = '';
        foreach ($csv as $row) {
            $content .= "\"" . implode('","', array_map(function($item) {
                return str_replace('"', '""', $item);
            }, $row)) . "\"\n";
        }

        $filename = 'Laporan_' . $namaBulan . '_' . $tahun . '.csv';

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
