<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $penjualan->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 300px; margin: 0 auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { border-bottom: 1px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 14px; }
        .header p { font-size: 10px; }
        .info p { font-size: 10px; margin-bottom: 2px; }
        .items { border-bottom: 1px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .item { margin-bottom: 6px; }
        .item-name { font-weight: bold; }
        .item-detail { display: flex; justify-content: space-between; font-size: 10px; }
        .totals { margin-bottom: 10px; }
        .total-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 3px; }
        .total-row.grand { font-weight: bold; font-size: 13px; margin-top: 6px; padding-top: 6px; border-top: 1px solid #333; }
        .footer { text-align: center; font-size: 10px; padding-top: 10px; border-top: 1px dashed #333; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header text-center">
        <h1>{{ $settings['nama_toko'] ?? 'Toko Kasir' }}</h1>
        @if($settings['alamat'])<p>{{ $settings['alamat'] }}</p>@endif
        @if($settings['no_telp'])<p>{{ $settings['no_telp'] }}</p>@endif
    </div>

    <div class="info">
        <p>No: <strong>#{{ $penjualan->id }}</strong></p>
        <p>Tanggal: {{ $penjualan->created_at->format('d/m/Y H:i') }}</p>
        @if($penjualan->pelanggan_nama)<p>Pelanggan: {{ $penjualan->pelanggan_nama }}</p>@endif
    </div>

    <div class="items">
        @foreach($penjualan->items as $item)
        <div class="item">
            <div class="item-name">{{ $item->produk?->nama ?? 'Produk' }}</div>
            <div class="item-detail">
                <span>{{ $item->jumlah }} x Rp {{ number_format($item->harga_jual) }}</span>
                <span>Rp {{ number_format($item->subtotal) }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-row">
            <span>TOTAL</span>
            <span>Rp {{ number_format($penjualan->total_harga) }}</span>
        </div>
        <div class="total-row">
            <span>BAYAR</span>
            <span>Rp {{ number_format($penjualan->total_bayar) }}</span>
        </div>
        <div class="total-row">
            <span>KEMBALIAN</span>
            <span>Rp {{ number_format($penjualan->kembalian) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>{{ $settings['footer_nota'] ?? 'Terima kasih!' }}</p>
    </div>
</body>
</html>
