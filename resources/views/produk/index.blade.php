<x-admin-layout title="Produk">
    <div class="card">
        <div class="card-header">
            <h3>Master Data Produk</h3>
            <button class="btn btn-primary" onclick="openModal('add')">
                <i class="ri-add-line"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            {{-- Tabs --}}
            <div style="display:flex; gap:8px; margin-bottom:16px; border-bottom:2px solid #e2e8f0; padding-bottom:12px;">
                <button class="tab-btn active" id="tab-all" onclick="switchTab('all')" style="padding:8px 16px; border:none; border-radius:8px 8px 0 0; font-size:13px; font-weight:600; cursor:pointer; background:#4f46e5; color:#fff;">
                    Semua Produk <span id="count-all" style="background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:10px; font-size:11px; margin-left:6px;">0</span>
                </button>
                <button class="tab-btn" id="tab-low" onclick="switchTab('low')" style="padding:8px 16px; border:none; border-radius:8px 8px 0 0; font-size:13px; font-weight:600; cursor:pointer; background:#fee2e2; color:#991b1b;">
                    Hampir Habis <span id="count-low" style="background:#ef4444; color:#fff; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:6px;">0</span>
                </button>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <input class="search-input" id="searchInput" type="text" placeholder="Cari produk..." value="{{ request('search') }}">
                    <button class="btn btn-secondary" onclick="loadData(1)"><i class="ri-search-line"></i></button>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="table-scroll desktop-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>Kategori</th>
                            <th>Nama</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Tanpa Stok</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="produkTableBody"></tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div id="produkCardList" class="mobile-cards"></div>

            <div id="emptyState" class="hidden">
                <div class="empty-state"><div><i class="ri-shopping-bag-3-line"></i><p>Belum ada produk</p></div></div>
            </div>

            <div id="pagination" class="pagination-wrap" style="display:none;">
                <div class="pagination-info" id="paginationInfo"></div>
                <div id="paginationButtons"></div>
            </div>
        </div>
    </div>

    {{-- Modal Add --}}
    <div class="modal-overlay" id="modal-add">
        <div class="modal">
            <div class="modal-header">
                <h3>Tambah Produk</h3>
                <button class="modal-close" onclick="closeModal('add')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-add" onsubmit="submitForm(event, 'add')">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori_id" id="add-kategori_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-kategori_id-error"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Produk</label>
                        <input class="form-input" name="nama" id="add-nama" required placeholder="Contoh: Pulsa 10rb, Token Listrik">
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-nama-error"></span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Harga Beli</label>
                            <input class="form-input" name="harga_beli" id="add-harga_beli" type="number" min="0" required placeholder="0">
                            <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-harga_beli-error"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Jual</label>
                            <input class="form-input" name="harga_jual" id="add-harga_jual" type="number" min="0" required placeholder="0">
                            <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-harga_jual-error"></span>
                        </div>
                    </div>
                    <div class="form-group" id="stok-group-add">
                        <label class="form-label">Stok</label>
                        <input class="form-input" name="stok" id="add-stok" type="number" min="0" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="tanpa_stok" id="add-tanpa_stok" onchange="toggleStok('add')" style="width:18px; height:18px; accent-color:#4f46e5;">
                            <span style="font-size:14px; font-weight:500; color:#374151;">
                                <i class="ri-checkbox-circle-line"></i> Produk Tanpa Stok
                            </span>
                        </label>
                        <div style="font-size:12px; color:#94a3b8; margin-top:4px; margin-left:26px;">
                            Centang jika produk tidak memerlukan stok (contoh: pulsa, kuota, voucher, token)
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

    {{-- Modal Edit --}}
    <div class="modal-overlay" id="modal-edit">
        <div class="modal">
            <div class="modal-header">
                <h3>Edit Produk</h3>
                <button class="modal-close" onclick="closeModal('edit')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-edit" onsubmit="submitForm(event, 'edit')">
                    <input type="hidden" id="edit-id">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori_id" id="edit-kategori_id" required>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="edit-kategori_id-error"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Produk</label>
                        <input class="form-input" name="nama" id="edit-nama" required>
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="edit-nama-error"></span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Harga Beli</label>
                            <input class="form-input" name="harga_beli" id="edit-harga_beli" type="number" min="0" required>
                            <span class="field-error text-xs text-red-500 mt-1 hidden" id="edit-harga_beli-error"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Jual</label>
                            <input class="form-input" name="harga_jual" id="edit-harga_jual" type="number" min="0" required>
                            <span class="field-error text-xs text-red-500 mt-1 hidden" id="edit-harga_jual-error"></span>
                        </div>
                    </div>
                    <div class="form-group" id="stok-group-edit">
                        <label class="form-label">Stok</label>
                        <input class="form-input" name="stok" id="edit-stok" type="number" min="0">
                    </div>
                    <div class="form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="tanpa_stok" id="edit-tanpa_stok" onchange="toggleStok('edit')" style="width:18px; height:18px; accent-color:#4f46e5;">
                            <span style="font-size:14px; font-weight:500; color:#374151;">
                                <i class="ri-checkbox-circle-line"></i> Produk Tanpa Stok
                            </span>
                        </label>
                        <div style="font-size:12px; color:#94a3b8; margin-top:4px; margin-left:26px;">
                            Centang jika produk tidak memerlukan stok (contoh: pulsa, kuota, voucher, token)
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('edit')">Batal</button>
                        <button type="submit" id="btn-edit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const csrf = '{{ csrf_token() }}';
        let currentPage = 1;
        let searchQuery = '';
        let currentTab = 'all';

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function switchTab(tab) {
            currentTab = tab;
            const tabAll = document.getElementById('tab-all');
            const tabLow = document.getElementById('tab-low');

            if (tab === 'all') {
                tabAll.style.background = '#4f46e5';
                tabAll.style.color = '#fff';
                tabLow.style.background = '#fee2e2';
                tabLow.style.color = '#991b1b';
            } else {
                tabAll.style.background = '#f1f5f9';
                tabAll.style.color = '#475569';
                tabLow.style.background = '#ef4444';
                tabLow.style.color = '#fff';
            }
            loadData(1);
        }

        function renderTable(items) {
            const tbody = document.getElementById('produkTableBody');
            const cardList = document.getElementById('produkCardList');
            const empty = document.getElementById('emptyState');

            if (!items.length) {
                tbody.innerHTML = '';
                cardList.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            tbody.innerHTML = items.map(p => `
                <tr>
                    <td>#${p.id}</td>
                    <td><span style="background:#ede9fe; color:#7c3aed; padding:3px 10px; border-radius:20px; font-size:12px;">${p.kategori ? escapeHtml(p.kategori.nama) : '-'}</span></td>
                    <td>${escapeHtml(p.nama)}</td>
                    <td>Rp ${parseInt(p.harga_beli).toLocaleString('id-ID')}</td>
                    <td>Rp ${parseInt(p.harga_jual).toLocaleString('id-ID')}</td>
                    <td>${p.tanpa_stok ? '<span style="color:#94a3b8;">-</span>' : (p.stok < 5 ? `<span style="color:#ef4444; font-weight:600;">${p.stok}</span>` : `<span style="color:#10b981; font-weight:600;">${p.stok}</span>`)}</td>
                    <td>${p.tanpa_stok ? '<span style="background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:20px; font-size:12px;"><i class="ri-checkbox-circle-fill"></i> Ya</span>' : '<span style="color:#94a3b8; font-size:12px;">Tidak</span>'}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-warning btn-sm" onclick='openModal("edit", ${p.id})'>
                                <i class="ri-edit-2-line"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduk(${p.id}, &quot;${escapeHtml(p.nama).replace(/"/g, '&quot;')}&quot;)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            cardList.innerHTML = items.map(p => `
                <div class="mobile-card">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Nama</span>
                        <span class="mobile-card-value" style="font-weight:700;">${escapeHtml(p.nama)}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Kategori</span>
                        <span class="mobile-card-value"><span style="background:#ede9fe; color:#7c3aed; padding:2px 8px; border-radius:20px; font-size:11px;">${p.kategori ? escapeHtml(p.kategori.nama) : '-'}</span></span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Harga Beli</span>
                        <span class="mobile-card-value">Rp ${parseInt(p.harga_beli).toLocaleString('id-ID')}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Harga Jual</span>
                        <span class="mobile-card-value">Rp ${parseInt(p.harga_jual).toLocaleString('id-ID')}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Stok</span>
                        <span class="mobile-card-value">${p.tanpa_stok ? '<span style="color:#94a3b8; font-size:12px;">Tanpa Stok</span>' : (p.stok < 5 ? `<span style="color:#ef4444; font-weight:600;">${p.stok}</span>` : `<span style="color:#10b981; font-weight:600;">${p.stok}</span>`)}</span>
                    </div>
                    <div class="mobile-card-actions">
                        <button class="btn btn-warning btn-sm" onclick='openModal("edit", ${p.id})'><i class="ri-edit-2-line"></i> Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduk(${p.id}, &quot;${escapeHtml(p.nama).replace(/"/g, '&quot;')}&quot;)"><i class="ri-delete-bin-line"></i> Hapus</button>
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

        function toggleStok(type) {
            const checkbox = document.getElementById(type + '-tanpa_stok');
            const stokGroup = document.getElementById('stok-group-' + type);
            const stokInput = document.getElementById(type + '-stok');
            if (checkbox.checked) {
                stokGroup.style.opacity = '0.4';
                stokInput.value = '0';
            } else {
                stokGroup.style.opacity = '1';
            }
        }

        let produkCache = {};

        function openModal(type, id = null) {
            clearErrors();
            if (type === 'add') {
                document.getElementById('form-add').reset();
                document.getElementById('stok-group-add').style.opacity = '1';
                document.getElementById('add-tanpa_stok').checked = false;
                document.getElementById('modal-add').classList.add('show');
            } else {
                const p = produkCache[id];
                if (!p) return;
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-kategori_id').value = p.kategori_id;
                document.getElementById('edit-nama').value = p.nama;
                document.getElementById('edit-harga_beli').value = p.harga_beli;
                document.getElementById('edit-harga_jual').value = p.harga_jual;
                document.getElementById('edit-stok').value = p.stok;
                document.getElementById('edit-tanpa_stok').checked = !!p.tanpa_stok;
                const stokGroup = document.getElementById('stok-group-edit');
                stokGroup.style.opacity = p.tanpa_stok ? '0.4' : '1';
                document.getElementById('modal-edit').classList.add('show');
            }
        }

        function closeModal(type) {
            document.getElementById('modal-' + type).classList.remove('show');
        }

        function clearErrors() {
            document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
        }

        function setLoading(btn, on, label) {
            if (!btn) return;
            btn.disabled = on;
            if (on) btn.innerHTML = '<i class="ri-loader-2-line" style="display:inline-block;animation:spin 1s linear infinite"></i> Menyimpan...';
            else btn.textContent = label || btn.dataset.original || 'Simpan';
        }

        async function submitForm(e, type) {
            e.preventDefault();
            clearErrors();
            const btn = document.getElementById('btn-' + type);
            if (!btn.dataset.original) btn.dataset.original = btn.textContent;
            setLoading(btn, true);

            const id = type === 'edit' ? document.getElementById('edit-id').value : null;
            const payload = {
                kategori_id: document.getElementById(type + '-kategori_id').value,
                nama: document.getElementById(type + '-nama').value.trim(),
                harga_beli: parseInt(document.getElementById(type + '-harga_beli').value) || 0,
                harga_jual: parseInt(document.getElementById(type + '-harga_jual').value) || 0,
                stok: parseInt(document.getElementById(type + '-stok').value) || 0,
                tanpa_stok: document.getElementById(type + '-tanpa_stok').checked,
            };

            if (!payload.kategori_id) {
                showFieldError(type, 'kategori_id', 'Kategori wajib dipilih');
                setLoading(btn, false); return;
            }
            if (!payload.nama) {
                showFieldError(type, 'nama', 'Nama produk wajib diisi');
                setLoading(btn, false); return;
            }

            try {
                const res = await fetch(id ? `/produk/${id}` : '/produk', {
                    method: id ? 'PUT' : 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (data.success) {
                    closeModal(type);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    await loadData(currentPage);
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(key => showFieldError(type, key, data.errors[key][0]));
                } else {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Terjadi kesalahan', showConfirmButton: false, timer: 3000 });
                }
            } catch (err) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal terhubung ke server', showConfirmButton: false, timer: 3000 });
            } finally {
                setLoading(btn, false);
            }
        }

        function showFieldError(type, field, msg) {
            const el = document.getElementById(type + '-' + field + '-error');
            if (el) { el.textContent = msg; el.classList.remove('hidden'); }
        }

        async function deleteProduk(id, nama) {
            const result = await Swal.fire({
                title: 'Hapus Produk?', text: `"${nama}" akan dihapus permanen.`,
                icon: 'warning', toast: true, position: 'top-end',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/produk/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    await loadData(currentPage);
                } else {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Gagal menghapus', showConfirmButton: false, timer: 3000 });
                }
            } catch (err) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal terhubung ke server', showConfirmButton: false, timer: 3000 });
            }
        }

        async function loadData(page = 1) {
            currentPage = page;
            searchQuery = document.getElementById('searchInput')?.value || '';
            try {
                const params = new URLSearchParams({ page, per_page: 10, search: searchQuery });
                if (currentTab === 'low') params.set('filter', 'low');
                const res = await fetch(`/produk/data?${params}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                produkCache = {};
                (data.data || []).forEach(p => produkCache[p.id] = p);
                renderTable(data.data || []);
                renderPagination(data);

                // Refresh counts after data load
                loadCounts();
            } catch (err) {
                location.reload();
            }
        }

        // Load counts for tabs
        async function loadCounts() {
            try {
                const res = await fetch(`/produk/data?per_page=1`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                document.getElementById('count-all').textContent = data.meta?.total || 0;

                const resLow = await fetch(`/produk/data?per_page=1&filter=low`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const dataLow = await resLow.json();
                document.getElementById('count-low').textContent = dataLow.meta?.total || 0;
            } catch (err) {}
        }

        document.getElementById('searchInput')?.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); loadData(1); } });

        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', e => { if (e.target === el) el.classList.remove('show'); });
        });

        loadCounts();
        loadData(1);
    </script>
</x-admin-layout>
