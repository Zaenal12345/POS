<x-admin-layout title="Dashboard">
    <div class="stat-grid" style="margin-bottom: 32px;">
        <div class="card stat-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #ede9fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ri-money-dollar-circle-line" style="font-size: 22px; color: #7c3aed;"></i>
                </div>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500; text-transform: uppercase;">Total Penjualan</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px;">Rp {{ number_format($totalPenjualan) }}</div>
                </div>
            </div>
        </div>
        <div class="card stat-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ri-arrow-down-line" style="font-size: 22px; color: #3b82f6;"></i>
                </div>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500; text-transform: uppercase;">Total Pembelian</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px;">Rp {{ number_format($totalPembelian) }}</div>
                </div>
            </div>
        </div>
        <div class="card stat-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ri-shopping-bag-3-line" style="font-size: 22px; color: #10b981;"></i>
                </div>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500; text-transform: uppercase;">Total Produk</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ $totalProduk }}</div>
                </div>
            </div>
        </div>
        <div class="card stat-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ri-folder-line" style="font-size: 22px; color: #f59e0b;"></i>
                </div>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500; text-transform: uppercase;">Total Kategori</div>
                    <div style="font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ $totalKategori }}</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (min-width: 640px) {
            .stat-grid { gap: 16px; }
        }
        @media (min-width: 1024px) {
            .stat-grid { grid-template-columns: repeat(4, 1fr); gap: 24px; }
            .stat-card { padding: 24px !important; }
            .stat-card [style*="font-size: 20px"] { font-size: 24px !important; }
        }
        /* Responsive font untuk mobile */
        @media (max-width: 480px) {
            .stat-card { padding: 14px !important; }
            .stat-card [style*="font-size: 20px"] { font-size: 15px !important; }
            .stat-card [style*="font-size: 11px"] { font-size: 10px !important; }
            .stat-card i { font-size: 18px !important; }
            .stat-card > div > div:first-child { width: 36px !important; height: 36px !important; }
            .card-header h3 { font-size: 14px !important; }
        }
    </style>

    <div class="card">
        <div class="card-header">
            <h3>Transaksi Terbaru</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $t)
                        <tr>
                            <td>#{{ $t->id }}</td>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $t->produk->kategori->nama ?? '-' }}</td>
                            <td>{{ $t->produk->nama ?? '-' }}</td>
                            <td>{{ $t->jumlah }}</td>
                            <td>Rp {{ number_format($t->total_harga) }}</td>
                            <td><span class="status-badge status-{{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-state"><div><i class="ri-inbox-2-line"></i><p>Belum ada transaksi</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
