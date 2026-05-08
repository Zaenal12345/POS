<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $penjualan->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }

        .receipt-container { background: white; width: 300px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; }

        .receipt-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 20px; text-align: center; }
        .receipt-header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .receipt-header p { font-size: 10px; opacity: 0.9; }

        .receipt-body { padding: 16px; }
        .receipt-info { border-bottom: 1px dashed #ddd; padding-bottom: 10px; margin-bottom: 10px; }
        .receipt-info p { font-size: 11px; color: #666; margin-bottom: 3px; }

        .receipt-items { margin-bottom: 10px; }
        .item { padding: 8px 0; border-bottom: 1px dotted #eee; }
        .item:last-child { border-bottom: none; }
        .item-name { font-weight: bold; margin-bottom: 4px; }
        .item-detail { display: flex; justify-content: space-between; font-size: 11px; color: #666; }
        .item-qty { flex: 1; }
        .item-price { text-align: right; }

        .receipt-totals { border-top: 1px dashed #333; padding-top: 10px; margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
        .total-row.grand { font-weight: bold; font-size: 14px; margin-top: 8px; padding-top: 8px; border-top: 2px solid #333; }
        .total-row.paid { color: #16a34a; }
        .total-row.change { color: #f59e0b; }

        .receipt-footer { background: #f8fafc; padding: 16px; text-align: center; border-top: 1px solid #eee; }
        .receipt-footer p { font-size: 10px; color: #666; }

        .actions { padding: 16px; display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-print { background: #4f46e5; color: white; }
        .btn-back { background: #e2e8f0; color: #475569; text-decoration: none; }

        @media print {
            body { background: white; padding: 0; }
            .actions { display: none; }
            .receipt-container { box-shadow: none; border-radius: 0; width: 100%; max-width: 300px; margin: 0 auto; }
        }
    </style>
</head>
<body>
    <div class="receipt-container" id="receipt">
        <div class="receipt-header">
            <h1>{{ $settings['nama_toko'] ?? 'Toko Kasir' }}</h1>
            @if($settings['alamat'])<p>{{ $settings['alamat'] }}</p>@endif
            @if($settings['no_telp'])<p>{{ $settings['no_telp'] }}</p>@endif
        </div>

        <div class="receipt-body">
            <div class="receipt-info">
                <p>No. Transaksi: <strong>#{{ $penjualan->id }}</strong></p>
                <p>Tanggal: {{ $penjualan->created_at->format('d/m/Y H:i') }}</p>
                <p>Kasir: {{ Auth::user()->name ?? 'Admin' }}</p>
                @if($penjualan->pelanggan_nama)
                <p>Pelanggan: {{ $penjualan->pelanggan_nama }}</p>
                @endif
            </div>

            <div class="receipt-items">
                @foreach($penjualan->items as $item)
                <div class="item">
                    <div class="item-name">{{ $item->produk?->nama ?? 'Produk' }}</div>
                    <div class="item-detail">
                        <span class="item-qty">{{ $item->jumlah }} x Rp {{ number_format($item->harga_jual) }}</span>
                        <span class="item-price">Rp {{ number_format($item->subtotal) }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="receipt-totals">
                <div class="total-row">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($penjualan->total_harga) }}</span>
                </div>
                <div class="total-row paid">
                    <span>DIBAYAR</span>
                    <span>Rp {{ number_format($penjualan->total_bayar) }}</span>
                </div>
                <div class="total-row change">
                    <span>KEMBALIAN</span>
                    <span>Rp {{ number_format($penjualan->kembalian) }}</span>
                </div>
                @if($penjualan->tipe_pembayaran)
                <div class="total-row" style="margin-top:8px; font-size:10px; color:#666;">
                    <span>Metode:</span>
                    <span>{{ strtoupper($penjualan->tipe_pembayaran) }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="receipt-footer">
            <p>{{ $settings['footer_nota'] ?? 'Terima kasih atas kunjungan Anda!' }}</p>
        </div>
    </div>

    <div class="actions">
        <a href="{{ url()->previous() }}" class="btn btn-back">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <button onclick="printReceipt()" class="btn btn-print">
            <i class="ri-printer-line"></i> Cetak
        </button>
    </div>

    <script>
        function printReceipt() {
            window.print();
        }

        // Show success toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Struk siap dicetak!',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
</body>
</html>
