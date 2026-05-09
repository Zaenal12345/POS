<x-admin-layout title="Dompet">
    <div style="margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h2 style="margin:0;font-size:20px;font-weight:700;color:#1e293b;">Laporan Dompet</h2>
            <p style="margin:4px 0 0;font-size:13px;color:#94a3b8;">Ringkasan keuangan bulan ini</p>
        </div>
        <form method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <select name="bulan" class="form-select" style="width:120px;" onchange="this.form.submit()">
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $n)
                <option value="{{ $i+1 }}" {{ $bulan == $i+1 ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
            <select name="tahun" class="form-select" style="width:100px;" onchange="this.form.submit()">
                @for($y = date('Y')-5; $y <= date('Y'); $y++)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
        <div style="background:linear-gradient(135deg,#fef2f2,#fef0f0);border:1px solid #fecaca;border-radius:16px;padding:20px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:40px;height:40px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:20px;">📉</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:0.5px;">Pengeluaran</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:#dc2626;margin-bottom:2px;">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</div>
            <div style="font-size:11px;color:#f87171;">Dari proses pembelian</div>
        </div>

        <div style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);border:1px solid #bbf7d0;border-radius:16px;padding:20px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:40px;height:40px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:20px;">📈</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:0.5px;">Pemasukkan</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:#16a34a;margin-bottom:2px;">Rp {{ number_format($penjualanTotal, 0, ',', '.') }}</div>
            <div style="font-size:11px;color:#4ade80;">Total penjualan bulan ini</div>
        </div>

        <div style="background:linear-gradient(135deg,#eff6ff,#f8faff);border:1px solid #bfdbfe;border-radius:16px;padding:20px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:20px;">💰</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:#1e40af;text-transform:uppercase;letter-spacing:0.5px;">Keuntungan</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:#2563eb;margin-bottom:2px;">Rp {{ number_format($keuntungan, 0, ',', '.') }}</div>
            <div style="font-size:11px;color:#60a5fa;">
                @if($keuntunganManual != 0)
                    + Manual: Rp {{ number_format($keuntunganManual > 0 ? $keuntunganManual : $keuntunganManual, 0, ',', '.') }}
                @else
                    Selisih jual - beli
                @endif
            </div>
        </div>

        <div style="background:linear-gradient(135deg,#faf5ff,#f5f3ff);border:1px solid #e9d5ff;border-radius:16px;padding:20px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:40px;height:40px;background:#faf5ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:20px;">🏦</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:#6b21a8;text-transform:uppercase;letter-spacing:0.5px;">Saldo Bersih</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:{{ $saldo >= 0 ? '#9333ea' : '#dc2626' }};margin-bottom:2px;">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
            <div style="font-size:11px;color:{{ $saldo >= 0 ? '#c084fc' : '#f87171' }};">Pemasukkan - Pengeluaran</div>
        </div>
    </div>

    {{-- Chart --}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="margin:0;font-size:15px;font-weight:700;color:#1e293b;">Grafik Keuangan Harian</h3>
            <div style="display:flex;gap:16px;font-size:11px;font-weight:600;">
                <span style="display:flex;align-items:center;gap:4px;color:#dc2626;">● Pengeluaran</span>
                <span style="display:flex;align-items:center;gap:4px;color:#16a34a;">● Pemasukkan</span>
                <span style="display:flex;align-items:center;gap:4px;color:#2563eb;">● Keuntungan</span>
            </div>
        </div>
        <div style="height:260px; position:relative;">
            <canvas id="walletChart"></canvas>
        </div>
    </div>

    {{-- Detail per Kategori --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;">
            <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;color:#1e293b;">Keuntungan Per Kategori</h3>
            @if(empty($kategoriKeuntungan))
                <div style="text-align:center;padding:30px;color:#94a3b8;font-size:13px;">
                    <i class="ri-bar-chart-line" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                    Belum ada data
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:10px; max-height:300px;overflow-y:auto;">
                    @foreach($kategoriKeuntungan as $kat => $data)
                    <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#1e293b;">{{ $kat }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $data['count'] }} transaksi</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:14px;font-weight:700;color:#16a34a;">+Rp {{ number_format($data['keuntungan'], 0, ',', '.') }}</div>
                            <div style="font-size:10px;color:#94a3b8;">Beli: Rp {{ number_format($data['total_beli'], 0, ',', '.') }} / Jual: Rp {{ number_format($data['total_jual'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;">
            <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;color:#1e293b;">Ringkasan Bulan</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                @php
                    $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    $grossProfit = $penjualanTotal - $pengeluaran;
                    $profitMargin = $penjualanTotal > 0 ? round(($keuntungan / $penjualanTotal) * 100, 1) : 0;
                @endphp
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#64748b;">Periode</span>
                    <span style="font-size:13px;font-weight:700;color:#1e293b;">{{ $months[$bulan-1] }} {{ $tahun }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#64748b;">Total Transaksi</span>
                    <span style="font-size:13px;font-weight:700;color:#1e293b;">{{ array_sum(array_column($dataPemasukkan, 0) ?: [0]) > 0 ? count(array_filter($dataPemasukkan)) : count(array_filter($dataPengeluaran)) }} Hari Aktif</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#64748b;">Gross Profit</span>
                    <span style="font-size:13px;font-weight:700;color:{{ $grossProfit >= 0 ? '#16a34a' : '#dc2626' }};">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:13px;color:#64748b;">Margin Keuntungan</span>
                    <span style="font-size:13px;font-weight:700;color:#2563eb;">{{ $profitMargin }}%</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                    <span style="font-size:13px;color:#64748b;">Total Item Terjual</span>
                    <span style="font-size:13px;font-weight:700;color:#1e293b;">
                        @php
                            $totalItems = 0;
                            foreach($kategoriKeuntungan as $d) $totalItems += $d['count'];
                            echo $totalItems . ' item';
                        @endphp
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($labels);
        const pengeluaran = @json($dataPengeluaran);
        const pemasukkan = @json($dataPemasukkan);
        const keuntungan = @json($dataKeuntungan);

        const ctx = document.getElementById('walletChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pengeluaran',
                        data: pengeluaran,
                        backgroundColor: 'rgba(220, 38, 38, 0.6)',
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Pemasukkan',
                        data: pemasukkan,
                        backgroundColor: 'rgba(22, 163, 74, 0.7)',
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Keuntungan',
                        data: keuntungan,
                        backgroundColor: 'rgba(37, 99, 235, 0.7)',
                        borderRadius: 4,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8', maxTicksLimit: 15 }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11 },
                            color: '#94a3b8',
                            callback: function(value) { return 'Rp ' + (value/1000).toFixed(0) + 'k'; }
                        }
                    }
                }
            }
        });
    </script>

    <style>
        @media (max-width: 900px) {
            .card-body { padding: 16px !important; }
        }
        @media (max-width: 768px) {
            div[style*="grid-template-columns:repeat(4"] {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            div[style*="grid-template-columns:1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
        @media (max-width: 480px) {
            div[style*="grid-template-columns:repeat(4"] {
                grid-template-columns: 1fr !important;
            }
            /* Responsive font untuk wallet cards */
            .wallet-card {
                padding: 14px !important;
            }
            .wallet-card [style*="font-size:26px"] {
                font-size: 16px !important;
            }
            .wallet-card [style*="font-size:12px"] {
                font-size: 10px !important;
            }
            .wallet-card [style*="font-size:20px"] {
                font-size: 16px !important;
            }
            .wallet-card [style*="font-size:11px"] {
                font-size: 9px !important;
            }
            .card-header h3 { font-size: 14px !important; }
            h2 { font-size: 16px !important; }
            p[style*="font-size:13px"] { font-size: 11px !important; }
        }
    </style>
</x-admin-layout>