<x-admin-layout title="Report">
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3>Filter & Export Laporan</h3>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('export.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-primary">
                    <i class="ri-file-pdf-line"></i> Export PDF
                </a>
                <a href="{{ route('export.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success" style="background:#16a34a;">
                    <i class="ri-file-excel-line"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:12px;">Bulan</label>
                    <select class="form-select" name="bulan" style="width:auto; min-width:120px;">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $n)
                            <option value="{{ $i+1 }}" {{ $bulan == $i+1 ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label" style="font-size:12px;">Tahun</label>
                    <select class="form-select" name="tahun" style="width:auto; min-width:100px;">
                        @for($y = date('Y')-5; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:18px;">
                    <button class="btn btn-primary" type="submit"><i class="ri-filter-2-line"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="report-grid" style="margin-bottom: 24px;">
        <div class="card report-card" style="padding: 20px;">
            <div style="font-size:11px; color:#94a3b8; font-weight:500; text-transform:uppercase;">Total Penjualan</div>
            <div style="font-size:20px; font-weight:700; color:#10b981; margin-top:8px;">Rp {{ number_format($totalPenjualan) }}</div>
        </div>
        <div class="card report-card" style="padding: 20px;">
            <div style="font-size:11px; color:#94a3b8; font-weight:500; text-transform:uppercase;">Total Pembelian</div>
            <div style="font-size:20px; font-weight:700; color:#3b82f6; margin-top:8px;">Rp {{ number_format($totalPembelian) }}</div>
        </div>
        <div class="card report-card" style="padding: 20px;">
            <div style="font-size:11px; color:#94a3b8; font-weight:500; text-transform:uppercase;">Keuntungan Manual</div>
            <div style="font-size:16px; font-weight:600; margin-top:6px;">
                <span style="color:#10b981;">+Rp {{ number_format($keuntunganMasuk) }}</span>
                <span style="color:#94a3b8;margin:0 4px;">/</span>
                <span style="color:#ef4444;">-Rp {{ number_format($keuntunganKeluar) }}</span>
            </div>
        </div>
        <div class="card report-card" style="padding: 20px;">
            <div style="font-size:11px; color:#94a3b8; font-weight:500; text-transform:uppercase;">Total Laba</div>
            <div style="font-size:20px; font-weight:700; color: {{ $laba >= 0 ? '#10b981' : '#ef4444' }}; margin-top:8px;">Rp {{ number_format($laba) }}</div>
        </div>
    </div>

    <style>
        .report-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (min-width: 640px) { .report-grid { gap: 16px; } }
        @media (min-width: 1024px) {
            .report-grid { grid-template-columns: repeat(4, 1fr); gap: 24px; }
            .report-card { padding: 24px !important; }
            .report-card [style*="font-size: 20px"] { font-size: 24px !important; }
        }
        .report-table-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        @media (min-width: 768px) {
            .report-table-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
        }
        /* Responsive font untuk mobile */
        @media (max-width: 640px) {
            .report-card { padding: 14px !important; }
            .report-card [style*="font-size: 20px"] { font-size: 15px !important; }
            .report-card [style*="font-size: 16px"] { font-size: 13px !important; }
            .report-card [style*="font-size: 11px"] { font-size: 10px !important; }
            .card-header h3 { font-size: 14px !important; }
            .btn { font-size: 11px !important; padding: 6px 10px !important; }
        }
    </style>

    <div class="report-table-grid">
        <div class="card">
            <div class="card-header"><h3>Riwayat Pembelian</h3></div>
            <div class="card-body" style="padding:0;">
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembelians as $p)
                            <tr>
                                <td>#{{ $p->id }}</td>
                                <td>{{ $p->produk->nama ?? '-' }}</td>
                                <td>{{ $p->jumlah }}</td>
                                <td>Rp {{ number_format($p->total_harga) }}</td>
                                <td><span class="status-badge status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty-state"><div><i class="ri-inbox-2-line"></i><p>Tidak ada data</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Riwayat Penjualan</h3></div>
            <div class="card-body" style="padding:0;">
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penjualans as $p)
                            <tr>
                                <td>#{{ $p->id }}</td>
                                <td>{{ $p->produk->nama ?? '-' }}</td>
                                <td>{{ $p->jumlah }}</td>
                                <td>Rp {{ number_format($p->total_harga) }}</td>
                                <td><span class="status-badge status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty-state"><div><i class="ri-inbox-2-line"></i><p>Tidak ada data</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
