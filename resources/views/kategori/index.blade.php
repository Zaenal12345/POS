<x-admin-layout title="Kategori">
    <div class="card">
        <div class="card-header">
            <h3>Master Data Kategori</h3>
            <button class="btn btn-primary" onclick="openModal('add')">
                <i class="ri-add-line"></i> Tambah
            </button>
        </div>
        <div class="card-body">

            {{-- Desktop Table --}}
            <div class="table-scroll desktop-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kategoriTableBody"></tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div id="kategoriCardList" class="mobile-cards"></div>

            <div id="emptyState" class="hidden">
                <div class="empty-state"><div><i class="ri-folder-3-line"></i><p>Belum ada kategori</p></div></div>
            </div>

            <div id="pagination" class="pagination-wrap" style="display:none;">
                <div class="pagination-info" id="paginationInfo"></div>
                <div id="paginationButtons"></div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal-overlay" id="modal-add">
        <div class="modal">
            <div class="modal-header">
                <h3>Tambah Kategori</h3>
                <button class="modal-close" onclick="closeModal('add')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-add" onsubmit="submitForm(event, 'add')">
                    <div class="form-group">
                        <label class="form-label">Nama Kategori</label>
                        <input class="form-input" name="nama" id="add-nama" required placeholder="Contoh: Makanan">
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="add-nama-error"></span>
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
                <h3>Edit Kategori</h3>
                <button class="modal-close" onclick="closeModal('edit')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-edit" onsubmit="submitForm(event, 'edit')">
                    <input type="hidden" id="edit-id">
                    <div class="form-group">
                        <label class="form-label">Nama Kategori</label>
                        <input class="form-input" name="nama" id="edit-nama" required>
                        <span class="field-error text-xs text-red-500 mt-1 hidden" id="edit-nama-error"></span>
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
        let allKategori = [];
        let currentPage = 1;

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function renderTable(items) {
            const tbody = document.getElementById('kategoriTableBody');
            const cardList = document.getElementById('kategoriCardList');
            const empty = document.getElementById('emptyState');

            if (!items.length) {
                tbody.innerHTML = '';
                cardList.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            tbody.innerHTML = items.map(k => `
                <tr>
                    <td>#${k.id}</td>
                    <td>${escapeHtml(k.nama)}</td>
                    <td>${escapeHtml(k.slug)}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-warning btn-sm" onclick='openModal("edit", ${k.id}, "${escapeHtml(k.nama).replace(/"/g, '&quot;')}")'>
                                <i class="ri-edit-2-line"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteKategori(${k.id}, &quot;${escapeHtml(k.nama).replace(/"/g, '&quot;')}&quot;)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            cardList.innerHTML = items.map(k => `
                <div class="mobile-card">
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Nama</span>
                        <span class="mobile-card-value" style="font-weight:700;">${escapeHtml(k.nama)}</span>
                    </div>
                    <div class="mobile-card-row">
                        <span class="mobile-card-label">Slug</span>
                        <span class="mobile-card-value">${escapeHtml(k.slug)}</span>
                    </div>
                    <div class="mobile-card-actions">
                        <button class="btn btn-warning btn-sm" onclick='openModal("edit", ${k.id}, "${escapeHtml(k.nama).replace(/"/g, '&quot;')}")'>
                            <i class="ri-edit-2-line"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteKategori(${k.id}, &quot;${escapeHtml(k.nama).replace(/"/g, '&quot;')}&quot;)">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function openModal(type, id = null, nama = '') {
            clearErrors();
            if (type === 'add') {
                document.getElementById('form-add').reset();
                document.getElementById('modal-add').classList.add('show');
            } else {
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-nama').value = nama;
                document.getElementById('modal-edit').classList.add('show');
            }
        }

        function closeModal(type) {
            document.getElementById('modal-' + type).classList.remove('show');
        }

        function clearErrors() {
            document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
        }

        function setLoading(btn, on) {
            if (!btn) return;
            btn.disabled = on;
            btn.innerHTML = on
                ? '<i class="ri-loader-2-line" style="display:inline-block;animation:spin 1s linear infinite"></i> Menyimpan...'
                : btn.dataset.original || btn.textContent;
        }

        async function submitForm(e, type) {
            e.preventDefault();
            clearErrors();
            const btn = document.getElementById('btn-' + type);
            if (!btn.dataset.original) btn.dataset.original = btn.textContent;
            setLoading(btn, true);

            const id = type === 'edit' ? document.getElementById('edit-id').value : null;
            const nama = document.getElementById(type + '-nama').value.trim();
            const url = id ? `/kategori/${id}` : '/kategori';
            const method = id ? 'PUT' : 'POST';

            if (!nama) {
                document.getElementById(type + '-nama-error').textContent = 'Nama kategori wajib diisi';
                document.getElementById(type + '-nama-error').classList.remove('hidden');
                setLoading(btn, false);
                return;
            }

            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ nama })
                });
                const data = await res.json();

                if (data.success) {
                    closeModal(type);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    await loadData(currentPage);
                } else if (data.errors?.nama) {
                    document.getElementById(type + '-nama-error').textContent = data.errors.nama[0];
                    document.getElementById(type + '-nama-error').classList.remove('hidden');
                } else {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Terjadi kesalahan', showConfirmButton: false, timer: 3000 });
                }
            } catch (err) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal terhubung ke server', showConfirmButton: false, timer: 3000 });
            } finally {
                setLoading(btn, false);
            }
        }

        async function deleteKategori(id, nama) {
            const result = await Swal.fire({
                title: 'Hapus Kategori?', text: `"${nama}" akan dihapus permanen.`,
                icon: 'warning', toast: true, position: 'top-end',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
            });
            if (!result.isConfirmed) return;

            try {
                const res = await fetch(`/kategori/${id}`, {
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
            try {
                const res = await fetch(`/kategori/data?page=${page}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                allKategori = data.data || [];
                renderTable(allKategori);
                renderPagination(data);
            } catch (err) {
                location.reload();
            }
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

        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', e => { if (e.target === el) el.classList.remove('show'); });
        });

        loadData(1);
    </script>
</x-admin-layout>
