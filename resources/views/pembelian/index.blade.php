<x-admin-layout title="Pembelian">
    <div class="card">
        <div class="card-header">
            <h3>Data Pembelian</h3>
            <button class="btn btn-primary" onclick="openModal('add')">
                <i class="ri-add-line"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div style="margin-bottom:16px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <input class="search-input" id="searchInput" type="text" placeholder="Cari...">
                <input type="date" id="filterDateFrom" class="form-input" style="width:140px;" onchange="loadData(1)">
                <span style="color:#94a3b8;">s/d</span>
                <input type="date" id="filterDateTo" class="form-input" style="width:140px;" onchange="loadData(1)">
                <select class="form-select" id="filterType" style="width:auto; min-width:130px;" onchange="loadData(1)">
                    <option value="">Semua Tipe</option>
                    <option value="stok">Pembelian Stok</option>
                    <option value="topup">Top Up</option>
                </select>
                <select class="form-select" id="filterStatus" style="width:auto; min-width:130px;" onchange="loadData(1)">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button class="btn btn-secondary" onclick="loadData(1)"><i class="ri-search-line"></i></button>
            </div>

            {{-- Desktop Table --}}
            <div class="table-scroll desktop-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pembelianTableBody"></tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div id="pembelianCardList" class="mobile-cards"></div>

            <div id="emptyState" class="hidden">
                <div class="empty-state"><div><i class="ri-arrow-down-circle-line"></i><p>Belum ada data</p></div></div>
            </div>

            <div id="pagination" class="pagination-wrap" style="display:none;">
                <div class="pagination-info" id="paginationInfo"></div>
                <div id="paginationButtons"></div>
            </div>
        </div>
    </div>

    {{-- Modal Add --}}
    <div class="modal-overlay" id="modal-add">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div id="modal-add-icon" style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#6366f1);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-shopping-cart-2-line" style="color:#fff;font-size:18px;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;" id="modal-add-title">Pembelian Stok</h3>
                        <span style="font-size:11px;color:#94a3b8;" id="modal-add-subtitle">Tambah stok produk</span>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('add')">&times;</button>
            </div>
            <div class="modal-body" style="padding:0 20px 20px;">

                {{-- Type Toggle --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;margin-top:16px;">
                    <button type="button" class="type-btn active" id="btn-type-stok" onclick="setType('stok')" style="border:2px solid #e2e8f0;border-radius:12px;padding:12px;text-align:center;background:#fff;cursor:pointer;transition:all 0.15s;">
                        <div style="font-size:20px;margin-bottom:4px;">📦</div>
                        <div style="font-size:12px;font-weight:700;color:#1e293b;">Pembelian Stok</div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Tambah inventori</div>
                    </button>
                    <button type="button" class="type-btn" id="btn-type-topup" onclick="setType('topup')" style="border:2px solid #e2e8f0;border-radius:12px;padding:12px;text-align:center;background:#fff;cursor:pointer;transition:all 0.15s;">
                        <div style="font-size:20px;margin-bottom:4px;">📱</div>
                        <div style="font-size:12px;font-weight:700;color:#1e293b;">Top Up</div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Pengeluaran</div>
                    </button>
                </div>
                <input type="hidden" id="add-type" value="stok">

                <form id="form-add" onsubmit="submitForm(event, 'add')">
                    <div class="form-group" id="produk-group">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="produk_id" id="add-produk">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produks as $pr)
                            <option value="{{ $pr->id }}" data-tanpa-stok="{{ $pr->tanpa_stok }}">
                                {{ $pr->nama }}
                                ({{ $pr->kategori->nama ?? '-' }})
                                @if(!$pr->tanpa_stok) - Stok: {{ $pr->stok }} @else - Tanpa Stok @endif
                            </option>
                            @endforeach
                        </select>
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-produk-error"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" id="label-jumlah">Jumlah</label>
                        <input class="form-input" name="jumlah" type="number" min="1" required id="add-jumlah" placeholder="Masukkan jumlah" value="1">
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-jumlah-error"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input class="form-input" name="keterangan" id="add-keterangan" type="text" placeholder="cth: Via transfer, Supplier X">
                    </div>
                    <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 14px;margin-bottom:16px;" id="total-preview">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:#64748b;">Estimasi Total</span>
                            <span style="font-size:18px;font-weight:800;color:#1e293b;" id="total-preview-value">Rp 0</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add')">Batal</button>
                        <button type="submit" id="btn-add" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Status --}}
    <div class="modal-overlay" id="modal-status">
        <div class="modal">
            <div class="modal-header">
                <h3>Edit Status</h3>
                <button class="modal-close" onclick="closeModal('status')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-status" onsubmit="submitForm(event, 'status')">
                    <input type="hidden" id="edit-id">
                    <div id="edit-detail" style="background:#f8fafc;border-radius:12px;padding:12px;margin-bottom:14px;font-size:12px;color:#64748b;"></div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit-status-val">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('status')">Batal</button>
                        <button type="submit" id="btn-status" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const csrf = '{{ csrf_token() }}';
        let currentPage = 1;
        let pembelianCache = {};
        const produkListRaw = @json($produks);

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str || '';
            return div.innerHTML;
        }
        function formatRupiah(n) { return 'Rp ' + (parseInt(n||0)).toLocaleString('id-ID'); }
        function typeBadge(type) {
            if (type === 'topup') return '<span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">📱 Top Up</span>';
            return '<span style="background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">📦 Stok</span>';
        }

        function setType(type) {
            document.getElementById('add-type').value = type;
            const btnStok = document.getElementById('btn-type-stok');
            const btnTopup = document.getElementById('btn-type-topup');
            const produkGroup = document.getElementById('produk-group');

            if (type === 'topup') {
                btnTopup.style.borderColor = '#f59e0b'; btnTopup.style.background = '#fffbeb';
                btnStok.style.borderColor = '#e2e8f0'; btnStok.style.background = '#fff';
                produkGroup.style.display = 'none';
                document.getElementById('add-keterangan').placeholder = 'cth: Top up ovo, Transfer BCA';
                document.getElementById('add-produk').value = '';
            } else {
                btnStok.style.borderColor = '#3b82f6'; btnStok.style.background = '#eff6ff';
                btnTopup.style.borderColor = '#e2e8f0'; btnTopup.style.background = '#fff';
                produkGroup.style.display = 'block';
                document.getElementById('add-keterangan').placeholder = 'cth: Via transfer, Supplier X';
            }
            calcPreview();
        }

        function calcPreview() {
            const typeVal = document.getElementById('add-type').value;
            const jumlah = parseInt(document.getElementById('add-jumlah').value) || 0;
            let harga = 0;

            if (typeVal === 'stok') {
                const sel = document.getElementById('add-produk');
                const opt = sel.options[sel.selectedIndex];
                if (opt && opt.value) {
                    const p = produkListRaw.find(x => x.id == opt.value);
                    if (p) harga = p.harga_beli;
                }
                document.getElementById('total-preview-value').textContent = formatRupiah(harga * jumlah);
            } else {
                document.getElementById('total-preview-value').textContent = formatRupiah(jumlah);
            }
        }

        document.getElementById('add-produk')?.addEventListener('change', calcPreview);
        document.getElementById('add-jumlah')?.addEventListener('input', calcPreview);

        function renderTable(items) {
            const tbody = document.getElementById('pembelianTableBody');
            const cardList = document.getElementById('pembelianCardList');
            const empty = document.getElementById('emptyState');
            if (!items.length) { tbody.innerHTML = ''; cardList.innerHTML = ''; empty.classList.remove('hidden'); return; }
            empty.classList.add('hidden');

            tbody.innerHTML = items.map(p => `
                <tr>
                    <td>#${p.id}</td>
                    <td>${p.created_at ? new Date(p.created_at).toLocaleString('id-ID', {dateStyle:'short', timeStyle:'short'}) : '-'}</td>
                    <td>${typeBadge(p.type)}</td>
                    <td>${escapeHtml(p.produk?.nama || '-')}</td>
                    <td>${parseInt(p.jumlah).toLocaleString('id-ID')}</td>
                    <td style="font-weight:700">${formatRupiah(p.total_harga||0)}</td>
                    <td><span class="status-badge status-${p.status}">${p.status === 'completed' ? 'Selesai' : p.status === 'pending' ? 'Pending' : 'Batal'}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-warning btn-sm" onclick='openEditModal(${p.id})'><i class="ri-edit-2-line"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deletePembelian(${p.id})"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');

            cardList.innerHTML = items.map(p => `
                <div class="mobile-card">
                    <div class="mobile-card-row"><span class="mobile-card-label">ID</span><span class="mobile-card-value">#${p.id}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Tipe</span><span class="mobile-card-value">${typeBadge(p.type)}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Produk</span><span class="mobile-card-value">${escapeHtml(p.produk?.nama || '-')}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Jumlah</span><span class="mobile-card-value">${parseInt(p.jumlah).toLocaleString('id-ID')}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Total</span><span class="mobile-card-value" style="font-weight:700;">${formatRupiah(p.total_harga||0)}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Status</span><span class="status-badge status-${p.status}">${p.status === 'completed' ? 'Selesai' : p.status === 'pending' ? 'Pending' : 'Batal'}</span></div>
                    <div class="mobile-card-actions">
                        <button class="btn btn-warning btn-sm" onclick='openEditModal(${p.id})'><i class="ri-edit-2-line"></i> Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deletePembelian(${p.id})"><i class="ri-delete-bin-line"></i></button>
                    </div>
                </div>
            `).join('');
        }

        function renderPagination(data) {
            const pagDiv = document.getElementById('pagination');
            const info = document.getElementById('paginationInfo');
            const btns = document.getElementById('paginationButtons');
            if (!pagDiv) return;
            if (!data.last_page || data.last_page <= 1) { pagDiv.style.display = 'none'; return; }
            pagDiv.style.display = 'flex';
            info.textContent = `Menampilkan ${data.data.length} dari ${data.total} data`;
            btns.innerHTML = `<button onclick="loadData(${data.current_page - 1})" class="btn btn-sm btn-secondary" ${data.current_page === 1 ? 'disabled' : ''}>&laquo;</button>`;
            for (let i = 1; i <= data.last_page; i++) {
                btns.innerHTML += `<button onclick="loadData(${i})" class="btn btn-sm ${i === data.current_page ? 'btn-primary' : 'btn-secondary'}">${i}</button>`;
            }
            btns.innerHTML += `<button onclick="loadData(${data.current_page + 1})" class="btn btn-sm btn-secondary" ${data.current_page === data.last_page ? 'disabled' : ''}>&raquo;</button>`;
        }

        function openModal(type) {
            if (type === 'add') {
                document.getElementById('form-add').reset();
                document.getElementById('add-jumlah').value = 1;
                document.getElementById('add-type').value = 'stok';
                setType('stok');
                document.getElementById('modal-add').classList.add('show');
            }
        }

        function openEditModal(id) {
            const p = pembelianCache[id];
            if (!p) return;
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-status-val').value = p.status || 'pending';
            document.getElementById('edit-detail').innerHTML = `
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span>${typeBadge(p.type)}</span>
                    <span>#${p.id}</span>
                </div>
                <div style="font-weight:600;color:#1e293b;">${escapeHtml(p.produk?.nama || '-')}</div>
                <div>Jumlah: ${parseInt(p.jumlah).toLocaleString('id-ID')} × ${formatRupiah(p.produk?.harga_beli || 0)}</div>
                <div style="font-weight:700;color:#1e293b;margin-top:4px;">Total: ${formatRupiah(p.total_harga||0)}</div>
                ${p.pelanggan ? '<div style="margin-top:4px;">Pelanggan: '+escapeHtml(p.pelanggan)+'</div>' : ''}
            `;
            document.getElementById('modal-status').classList.add('show');
        }

        function closeModal(type) { document.getElementById('modal-' + type).classList.remove('show'); }
        function clearErrors() { document.querySelectorAll('.field-error').forEach(el => { el.textContent=''; el.classList.add('hidden'); }); }

        function setLoading(btn, on) {
            if (!btn) return;
            btn.disabled = on;
            btn.innerHTML = on ? '<i class="ri-loader-2-line" style="display:inline-block;animation:spin 1s linear infinite"></i> Menyimpan...' : (btn.dataset.original || 'Simpan');
        }

        async function submitForm(e, type) {
            e.preventDefault();
            clearErrors();
            const btn = document.getElementById('btn-' + type);
            if (!btn.dataset.original) btn.dataset.original = btn.textContent;
            setLoading(btn, true);

            if (type === 'add') {
                const typeVal = document.getElementById('add-type').value;
                const jumlah = parseInt(document.getElementById('add-jumlah').value) || 0;

                if (!jumlah || jumlah < 1) {
                    document.getElementById('add-jumlah-error').textContent = 'Jumlah minimal 1';
                    document.getElementById('add-jumlah-error').classList.remove('hidden');
                    setLoading(btn, false); return;
                }

                const payload = {
                    jumlah,
                    type: typeVal,
                    keterangan: document.getElementById('add-keterangan').value,
                };

                if (typeVal === 'stok') {
                    const produkId = document.getElementById('add-produk').value;
                    if (!produkId) {
                        document.getElementById('add-produk-error').textContent = 'Pilih produk';
                        document.getElementById('add-produk-error').classList.remove('hidden');
                        setLoading(btn, false); return;
                    }
                    payload.produk_id = produkId;
                }
                try {
                    const res = await fetch('/pembelian', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (data.success) {
                        closeModal('add');
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                        await loadData(currentPage);
                    } else {
                        if (data.errors?.produk_id) { document.getElementById('add-produk-error').textContent = data.errors.produk_id[0]; document.getElementById('add-produk-error').classList.remove('hidden'); }
                        if (data.errors?.jumlah) { document.getElementById('add-jumlah-error').textContent = data.errors.jumlah[0]; document.getElementById('add-jumlah-error').classList.remove('hidden'); }
                        if (data.message && !data.errors) Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
                    }
                } catch (err) { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal terhubung ke server', showConfirmButton: false, timer: 3000 }); }
                finally { setLoading(btn, false); }
            } else {
                const id = document.getElementById('edit-id').value;
                try {
                    const res = await fetch(`/pembelian/${id}`, {
                        method: 'PUT',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ status: document.getElementById('edit-status-val').value })
                    });
                    const data = await res.json();
                    if (data.success) { closeModal('status'); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 }); await loadData(currentPage); }
                    else { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message||'Gagal', showConfirmButton: false, timer: 3000 }); }
                } catch (err) { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal', showConfirmButton: false, timer: 3000 }); }
                finally { setLoading(btn, false); }
            }
        }

        async function deletePembelian(id) {
            const result = await Swal.fire({
                title: 'Hapus Pembelian?', text: `Transaksi #${id} akan dihapus.`, icon: 'warning', toast: true, position: 'top-end',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/pembelian/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (data.success) { Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 }); await loadData(currentPage); }
                else { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message||'Gagal', showConfirmButton: false, timer: 3000 }); }
            } catch (err) { Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal', showConfirmButton: false, timer: 3000 }); }
        }

        async function loadData(page = 1) {
            currentPage = page;
            const params = new URLSearchParams({
                page, per_page: 10,
                search: document.getElementById('searchInput')?.value || '',
                date_from: document.getElementById('filterDateFrom')?.value || '',
                date_to: document.getElementById('filterDateTo')?.value || '',
                status: document.getElementById('filterStatus')?.value || '',
                type: document.getElementById('filterType')?.value || ''
            });
            try {
                const res = await fetch(`/pembelian/data?${params}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                pembelianCache = {};
                (data.data || []).forEach(p => { pembelianCache[p.id] = p; });
                renderTable(data.data || []);
                renderPagination(data);
            } catch (err) { location.reload(); }
        }

        // Set default date to today and load data
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('filterDateFrom').value = today;
            document.getElementById('filterDateTo').value = today;
            loadData(1);
        });

        document.getElementById('searchInput')?.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); loadData(1); } });
        document.querySelectorAll('.modal-overlay').forEach(el => { el.addEventListener('click', e => { if (e.target === el) el.classList.remove('show'); }); });
    </script>

    <style>
        .type-btn:hover { border-color: #94a3b8 !important; }
    </style>
</x-admin-layout>