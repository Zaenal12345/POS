<x-admin-layout title="Keuntungan Manual">
    <div class="card">
        <div class="card-header">
            <h3>Input Keuntungan Manual</h3>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="ri-add-line"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div style="margin-bottom:16px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <form method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <select class="form-select" name="bulan" style="width:auto; min-width:110px;">
                        <option value="1" {{ ($bulan ?? 5) == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ ($bulan ?? 5) == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ ($bulan ?? 5) == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ ($bulan ?? 5) == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ ($bulan ?? 5) == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ ($bulan ?? 5) == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ ($bulan ?? 5) == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ ($bulan ?? 5) == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ ($bulan ?? 5) == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ ($bulan ?? 5) == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ ($bulan ?? 5) == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ ($bulan ?? 5) == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                    <select class="form-select" name="tahun" style="width:auto; min-width:90px;">
                        @for($y = date('Y') - 5; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button class="btn btn-secondary" type="submit"><i class="ri-filter-2-line"></i></button>
                </form>
            </div>

            <div class="summary-cards" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:20px;">
                <div class="card" style="padding:16px; text-align:center;">
                    <div style="font-size:11px; color:#94a3b8; font-weight:500;">Total Masuk</div>
                    <div style="font-size:18px; font-weight:700; color:#10b981; margin-top:6px;">Rp {{ number_format($totalMasuk) }}</div>
                </div>
                <div class="card" style="padding:16px; text-align:center;">
                    <div style="font-size:11px; color:#94a3b8; font-weight:500;">Total Keluar</div>
                    <div style="font-size:18px; font-weight:700; color:#ef4444; margin-top:6px;">Rp {{ number_format($totalKeluar) }}</div>
                </div>
                <div class="card" style="padding:16px; text-align:center;">
                    <div style="font-size:11px; color:#94a3b8; font-weight:500;">Saldo</div>
                    <div style="font-size:18px; font-weight:700; color:{{ $total >= 0 ? '#10b981' : '#ef4444' }}; margin-top:6px;">Rp {{ number_format($total) }}</div>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Keterangan</th>
                            <th style="text-align:right;">Jumlah</th>
                            <th style="width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keuntungans as $k)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                @if($k->tipe === 'masuk')
                                    <span class="status-badge" style="background:#d1fae5;color:#065f46;">Masuk</span>
                                @else
                                    <span class="status-badge" style="background:#fee2e2;color:#991b1b;">Keluar</span>
                                @endif
                            </td>
                            <td>{{ $k->keterangan ?? '-' }}</td>
                            <td style="text-align:right; font-weight:600;">Rp {{ number_format($k->jumlah) }}</td>
                            <td>
                                <form action="{{ route('keuntungan.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty-state"><div><i class="ri-inbox-2-line"></i><p>Belum ada data</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-add">
        <div class="modal" style="max-width:400px;">
            <div class="modal-header">
                <h3>Tambah Keuntungan</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('keuntungan.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" name="tipe" required id="tipe-select" onchange="toggleTipe()">
                            <option value="masuk">Keuntungan Masuk (+)</option>
                            <option value="keluar">Pengeluaran (-)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input class="form-input" name="jumlah" type="number" min="1" required placeholder="cth: 50000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input class="form-input" name="tanggal" type="date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input class="form-input" name="keterangan" type="text" placeholder="cth: Bonus bulan ini">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('modal-add').classList.add('show'); }
        function closeModal() { document.getElementById('modal-add').classList.remove('show'); }
        function toggleTipe() {
            var tipe = document.getElementById('tipe-select').value;
            var title = document.querySelector('.modal-header h3');
            title.textContent = tipe === 'masuk' ? 'Tambah Keuntungan' : 'Tambah Pengeluaran';
        }
        var modals = document.querySelectorAll('.modal-overlay');
        for (var i = 0; i < modals.length; i++) {
            modals[i].addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('show');
            });
        }
    </script>

    <style>
        @media (max-width: 640px) {
            .summary-cards { grid-template-columns: 1fr !important; }
            .summary-cards .card { padding: 12px !important; }
            .summary-cards [style*="font-size:18px"] { font-size: 14px !important; }
            .summary-cards [style*="font-size:11px"] { font-size: 10px !important; }
            .card-header h3 { font-size: 14px !important; }
            .btn { font-size: 11px !important; padding: 6px 10px !important; }
        }
    </style>
</x-admin-layout>
