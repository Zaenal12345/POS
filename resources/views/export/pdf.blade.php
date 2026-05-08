<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $bulan }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .period { text-align: center; background: #f0f0f0; padding: 8px; margin-bottom: 20px; font-weight: bold; }
        .summary { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .summary-box { border: 1px solid #ddd; padding: 12px; border-radius: 8px; text-align: center; width: 30%; }
        .summary-box h3 { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        .summary-box .value { font-size: 14px; font-weight: bold; }
        .summary-box .income { color: #16a34a; }
        .summary-box .expense { color: #dc2626; }
        .summary-box .profit { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; font-size: 11px; }
        th { background: #4f46e5; color: white; text-align: left; }
        tr:nth-child(even) { background: #f9f9f9; }
        .section-title { font-size: 14px; font-weight: bold; margin: 20px 0 10px; padding: 5px 10px; background: #f0f0f0; border-left: 4px solid #4f46e5; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #666; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $settings['nama_toko'] ?? 'Toko Kasir' }}</h1>
        @if($settings['alamat'])<p>{{ $settings['alamat'] }}</p>@endif
        @if($settings['no_telp'])<p>{{ $settings['no_telp'] }}</p>@endif
    </div>

    <div class="period">LAPORAN BULANAN: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="summary">
        <div class="summary-box">
            <h3>Total Penjualan</h3>
            <div class="value income">Rp {{ number_format($totalPenjualan) }}</div>
        </div>
        <div class="summary-box">
            <h3>Total Pembelian</h3>
            <div class="value expense">Rp {{ number_format($totalPembelian) }}</div>
        </div>
        <div class="summary-box">
            <h3>Laba Bersih</h3>
            <div class="value profit">Rp {{ number_format($labaBersih) }}</div>
        </div>
    </div>

    <div class="summary" style="margin-top:-10px;">
        <div class="summary-box" style="width:45%;">
            <h3>Keuntungan Manual (+)</h3>
            <div class="value income">Rp {{ number_format($keuntunganMasuk) }}</div>
        </div>
        <div class="summary-box" style="width:45%;">
            <h3>Pengeluaran Manual (-)</h3>
            <div class="value expense">Rp {{ number_format($keuntunganKeluar) }}</div>
        </div>
    </div>

    <div class="section-title">DETAIL PENJUALAN ({{ count($penjualans) }} transaksi)</div>
    <table>
        <thead>
            <tr>
                <th style="width:8%">ID</th>
                <th style="width:15%">Tanggal</th>
                <th style="width:20%">Pelanggan</th>
                <th style="width:10%">Items</th>
                <th style="width:12%">Total</th>
                <th style="width:10%">Bayar</th>
                <th style="width:10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualans as $p)
            <tr>
                <td>#{{ $p->id }}</td>
                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $p->pelanggan_nama ?? 'Umum' }}</td>
                <td class="text-center">{{ $p->items->count() }}</td>
                <td class="text-right">Rp {{ number_format($p->total_harga) }}</td>
                <td class="text-right">Rp {{ number_format($p->total_bayar) }}</td>
                <td>{{ ucfirst($p->status) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">DETAIL PEMBELIAN ({{ count($pembelians) }} transaksi)</div>
    <table>
        <thead>
            <tr>
                <th style="width:8%">ID</th>
                <th style="width:15%">Tanggal</th>
                <th style="width:25%">Produk</th>
                <th style="width:10%">Jumlah</th>
                <th style="width:12%">Total</th>
                <th style="width:10%">Tipe</th>
                <th style="width:10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembelians as $p)
            <tr>
                <td>#{{ $p->id }}</td>
                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $p->produk?->nama ?? '-' }}</td>
                <td class="text-center">{{ $p->jumlah }}</td>
                <td class="text-right">Rp {{ number_format($p->total_harga) }}</td>
                <td>{{ $p->type === 'topup' ? 'Top Up' : 'Stok' }}</td>
                <td>{{ ucfirst($p->status) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    @if(count($keuntungans) > 0)
    <div class="section-title">DETAIL KEUNTUNGAN MANUAL ({{ count($keuntungans) }} transaksi)</div>
    <table>
        <thead>
            <tr>
                <th style="width:15%">Tanggal</th>
                <th style="width:15%">Tipe</th>
                <th style="width:15%">Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($keuntungans as $k)
            <tr>
                <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $k->tipe === 'masuk' ? 'Masuk (+)' : 'Keluar (-)' }}</td>
                <td class="text-right" style="color:{{ $k->tipe === 'masuk' ? '#16a34a' : '#dc2626' }}">Rp {{ number_format($k->jumlah) }}</td>
                <td>{{ $k->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>{{ $settings['footer_nota'] ?? 'Terima kasih atas kunjungan Anda!' }}</p>
        <p>Dicetak pada: {{ $tanggal }}</p>
    </div>
</body>
</html>
