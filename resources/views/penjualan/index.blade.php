<x-admin-layout title="Penjualan">
    <div class="card">
        <div class="card-header">
            <h3>Data Penjualan</h3>
            <button class="btn btn-primary" onclick="openModal('add')">
                <i class="ri-add-line"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div style="margin-bottom:16px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <input class="search-input" id="searchInput" type="text" placeholder="Cari pelanggan...">
                <input type="date" id="filterDateFrom" class="form-input" style="width:140px;" onchange="loadData(1)">
                <span style="color:#94a3b8;">s/d</span>
                <input type="date" id="filterDateTo" class="form-input" style="width:140px;" onchange="loadData(1)">
                <select class="form-select" id="filterStatus" style="width:auto; min-width:130px;" onchange="loadData(1)">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select class="form-select" id="filterLunas" style="width:auto; min-width:130px;" onchange="loadData(1)">
                    <option value="">Semua Bayar</option>
                    <option value="1">Lunas</option>
                    <option value="0">Belum Lunas</option>
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
                            <th>Pelanggan</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th>Tipe Bayar</th>
                            <th>Lunas</th>
                            <th>Status</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="penjualanTableBody"></tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div id="penjualanCardList" class="mobile-cards"></div>

            <div id="emptyState" class="hidden">
                <div class="empty-state"><div><i class="ri-arrow-up-circle-line"></i><p>Belum ada data penjualan</p></div></div>
            </div>

            <div id="pagination" class="pagination-wrap" style="display:none;">
                <div class="pagination-info" id="paginationInfo"></div>
                <div id="paginationButtons"></div>
            </div>
        </div>
    </div>

    {{-- Modal Add Multi-Item --}}
    <div class="modal-overlay" id="modal-add">
        <div class="modal" style="max-width:560px; width:96vw;">
            <div class="modal-header">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#6366f1);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-shopping-cart-2-line" style="color:#fff;font-size:18px;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1e293b;">Transaksi Penjualan</h3>
                        <span style="font-size:11px;color:#94a3b8;">Multi-item dalam satu transaksi</span>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('add')">&times;</button>
            </div>
            <div class="modal-body" style="padding:0 20px 20px;">

                {{-- Produk Picker --}}
                <div style="margin-bottom:16px;">
                    <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Pilih Produk</label>
                    <select class="form-select" id="produk-select" onchange="onProdukSelect()" style="font-size:13px;margin-bottom:8px;">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produks as $pr)
                        <option value="{{ $pr->id }}"
                            data-harga-beli="{{ $pr->harga_beli }}"
                            data-harga-jual="{{ $pr->harga_jual }}"
                            data-tanpa-stok="{{ $pr->tanpa_stok }}"
                            data-stok="{{ $pr->stok }}">
                            {{ $pr->nama }} {{ !$pr->tanpa_stok ? '| Stok: '.$pr->stok : '| Tanpa Stok' }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Custom Price Inputs (shown after select) --}}
                    <div id="price-custom-panel" style="display:none; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:10px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:4px;">Harga Beli (Rp)</label>
                                <input class="form-input" id="custom-harga-beli" type="number" min="0" placeholder="0" style="font-size:14px;font-weight:700;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:4px;">Harga Jual (Rp)</label>
                                <input class="form-input" id="custom-harga-jual" type="number" min="0" placeholder="0" style="font-size:14px;font-weight:700;">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:4px;">Jumlah</label>
                                <input class="form-input" id="custom-jumlah" type="number" min="1" value="1" placeholder="1" style="font-size:14px;font-weight:700;">
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <button type="button" id="btn-tambah-item" onclick="addItemToCart()" style="width:100%;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border:none;border-radius:10px;padding:10px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <i class="ri-add-circle-line"></i> Tambah
                                </button>
                            </div>
                        </div>
                        <div id="price-hint" style="font-size:11px;color:#94a3b8;margin-top:6px;"></div>
                    </div>
                </div>

                <div style="border-top:2px solid #f1f5f9;margin-bottom:16px;"></div>

                {{-- Cart Items --}}
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">
                            Item Terpilih <span id="cart-count" style="background:#3b82f6;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;vertical-align:middle;">0</span>
                        </label>
                    </div>
                    <div id="cart-list" style="display:flex;flex-direction:column;gap:8px;max-height:220px;overflow-y:auto;">
                        <div id="cart-empty" style="text-align:center;padding:24px;color:#cbd5e1;font-size:12px;border:2px dashed #e2e8f0;border-radius:12px;background:#fafafa;">
                            <i class="ri-add-circle-line" style="font-size:26px;margin-bottom:6px;display:block;"></i>
                            Klik produk di atas untuk menambahkan
                        </div>
                    </div>
                </div>

                {{-- Summary Box --}}
                <div style="background:linear-gradient(135deg,#f0f9ff,#eff6ff);border:1.5px solid #bfdbfe;border-radius:14px;padding:14px 16px;margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <span style="font-size:13px;color:#475569;">Subtotal</span>
                        <span style="font-size:13px;font-weight:600;color:#475569;" id="cart-subtotal">Rp 0</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <span style="font-size:14px;font-weight:700;color:#1e293b;">Total</span>
                        <span style="font-size:22px;font-weight:800;color:#1d4ed8;" id="cart-total">Rp 0</span>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:4px;">Jumlah Bayar</label>
                            <input class="form-input" id="add-total-bayar" type="number" min="0" value="0" placeholder="0" style="font-weight:700;color:#1d4ed8;font-size:14px;" oninput="calcKembalian()">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:4px;">Kembalian</label>
                            <div style="background:#ecfdf5;border:1.5px solid #a7f3d0;border-radius:8px;padding:8px 10px;display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:16px;font-weight:800;color:#059669;" id="cart-kembalian">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:12px;font-weight:600;color:#64748b;">Nama Pelanggan</span>
                        <input class="form-input" id="add-pelanggan" type="text" placeholder="Umum" style="width:180px;font-size:13px;text-align:right;">
                    </div>
                </div>

                {{-- Payment Type --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:8px;">Metode Bayar</label>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                        @php $tipes = [['tunai','💵','Tunai'],['transfer','🏦','Transfer'],['qris','📱','QRIS'],['cicilan','📊','Cicilan']]; @endphp
                        @foreach($tipes as $t)
                        <label style="cursor:pointer;">
                            <input type="radio" name="tipe_pembayaran" value="{{ $t[0] }}" {{ $t[0]==='tunai'?'checked':'' }} style="display:none;">
                            <div class="tipe-opt" data-val="{{ $t[0] }}" style="border:2px solid #e2e8f0;border-radius:12px;padding:10px 4px;text-align:center;transition:all 0.15s;background:#fff;">
                                <span style="font-size:18px;display:block;margin-bottom:2px;">{{ $t[1] }}</span>
                                <span style="font-size:11px;font-weight:700;color:#475569;">{{ $t[2] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Lunas Toggle --}}
                <div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 16px;margin-bottom:16px;">
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b;">Status Pembayaran</div>
                        <div style="font-size:11px;color:#94a3b8;">Otomatis是根据 jumlah bayar</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span id="lunas-label" style="font-size:11px;font-weight:800;color:#059669;">✓ LUNAS</span>
                        <label style="position:relative;display:inline-block;width:48px;height:26px;cursor:pointer;">
                            <input type="checkbox" id="add-lunas" checked style="opacity:0;width:0;height:0;">
                            <span class="lunas-slider" onclick="toggleLunas(this)" style="position:absolute;top:0;left:0;right:0;bottom:0;background-color:#22c55e;border-radius:24px;transition:0.3s;"></span>
                            <span class="lunas-thumb" onclick="toggleLunas(this)" style="position:absolute;top:3px;left:26px;width:20px;height:20px;background:white;border-radius:50%;transition:0.3s;box-shadow:0 1px 3px rgba(0,0,0,0.2);"></span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('add')" style="flex:1;">Batal</button>
                    <button type="button" id="btn-add" onclick="submitPenjualan()" style="flex:2;background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;border:none;border-radius:12px;padding:13px;font-weight:700;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                        <i class="ri-check-line"></i> Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal-overlay" id="modal-edit">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <h3>Edit Transaksi</h3>
                <button class="modal-close" onclick="closeModal('edit')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-edit" onsubmit="submitEdit(event)">
                <input type="hidden" id="edit-id">
                <div style="background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:14px;">
                    <div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Item Dalam Transaksi</div>
                    <div id="edit-items-list" style="display:flex;flex-direction:column;gap:6px;"></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="edit-status-val">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Total Bayar</label>
                        <input class="form-input" type="number" min="0" id="edit-total-bayar">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Metode Bayar</label>
                    <select class="form-select" id="edit-tipe-bayar">
                        <option value="tunai">💵 Tunai</option>
                        <option value="transfer">🏦 Transfer</option>
                        <option value="qris">📱 QRIS</option>
                        <option value="cicilan">📊 Cicilan</option>
                        <option value="piutang">📋 Piutang</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Status Lunas</label>
                    <select class="form-select" id="edit-lunas-val">
                        <option value="1">✓ Lunas</option>
                        <option value="0">○ Belum Lunas</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Keterangan</label>
                    <input class="form-input" type="text" id="edit-keterangan" placeholder="cth: pelanggan langganan">
                </div>
                <div class="form-actions" style="margin-top:14px;">
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
        let penjualanCache = {};
        let cart = [];
        let produkData = [];

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str || '';
            return div.innerHTML;
        }

        function formatRupiah(n) { return 'Rp ' + (parseInt(n||0)).toLocaleString('id-ID'); }

        function tipeIcon(t) { return {tunai:'💵',transfer:'🏦',qris:'📱',cicilan:'📊',piutang:'📋'}[t]||'💵'; }
        function tipeLabel(t) { return {tunai:'Tunai',transfer:'Transfer',qris:'QRIS',cicilan:'Cicilan',piutang:'Piutang'}[t]||'Tunai'; }

        function onProdukSelect() {
            const sel = document.getElementById('produk-select');
            const opt = sel.options[sel.selectedIndex];
            const panel = document.getElementById('price-custom-panel');
            const hint = document.getElementById('price-hint');
            if (!opt || !opt.value) {
                panel.style.display = 'none';
                return;
            }
            const hb = parseInt(opt.dataset.hargaBeli) || 0;
            const hj = parseInt(opt.dataset.hargaJual) || 0;
            const stok = parseInt(opt.dataset.stok) || 0;
            const tanpaStok = opt.dataset.tanpaStok == '1';
            document.getElementById('custom-harga-beli').value = hb;
            document.getElementById('custom-harga-jual').value = hj;
            document.getElementById('custom-jumlah').value = 1;
            hint.textContent = tanpaStok
                ? `Tanpa Stok | Beli: Rp ${hb.toLocaleString('id-ID')} | Jual: Rp ${hj.toLocaleString('id-ID')}`
                : `Stok: ${stok} | Beli: Rp ${hb.toLocaleString('id-ID')} | Jual: Rp ${hj.toLocaleString('id-ID')}`;
            panel.style.display = 'block';
            document.getElementById('custom-jumlah').focus();
        }

        function addItemToCart() {
            const sel = document.getElementById('produk-select');
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) return;
            const produkId = parseInt(opt.value);
            const nama = opt.text.replace(/\|.*/, '').trim();
            const jumlah = parseInt(document.getElementById('custom-jumlah').value) || 1;
            const hargaBeli = parseInt(document.getElementById('custom-harga-beli').value) || 0;
            const hargaJual = parseInt(document.getElementById('custom-harga-jual').value) || 0;
            const tanpaStok = opt.dataset.tanpaStok == '1';
            const stok = parseInt(opt.dataset.stok) || 0;

            if (!tanpaStok && jumlah > stok) {
                Swal.fire({ toast:true, position:'top-end', icon:'warning', title:`Stok tidak cukup. Tersedia: ${stok}`, showConfirmButton:false, timer:3000 });
                return;
            }
            if (hargaJual < 1) {
                Swal.fire({ toast:true, position:'top-end', icon:'warning', title:'Harga jual harus lebih dari 0', showConfirmButton:false, timer:3000 });
                return;
            }

            const existing = cart.find(c => c.produk_id == produkId);
            if (existing) {
                existing.jumlah += jumlah;
                existing.harga_beli = hargaBeli;
                existing.harga_jual = hargaJual;
                existing.subtotal = existing.jumlah * existing.harga_jual;
            } else {
                cart.push({ produk_id: produkId, produk_nama: nama, jumlah, harga_beli: hargaBeli, harga_jual: hargaJual, subtotal: jumlah * hargaJual });
            }

            sel.value = '';
            document.getElementById('price-custom-panel').style.display = 'none';
            renderCart();
            calcKembalian();
        }

        function removeFromCart(produkId) {
            cart = cart.filter(c => c.produk_id != produkId);
            renderCart();
            calcKembalian();
        }

        function updateCartQty(produkId, delta) {
            const item = cart.find(c => c.produk_id == produkId);
            if (!item) return;
            item.jumlah += delta;
            if (item.jumlah < 1) { removeFromCart(produkId); return; }
            item.subtotal = item.jumlah * item.harga_jual;
            renderCart();
            calcKembalian();
        }

        function renderCart() {
            const list = document.getElementById('cart-list');
            const count = document.getElementById('cart-count');
            const subtotalEl = document.getElementById('cart-subtotal');
            const totalEl = document.getElementById('cart-total');

            count.textContent = cart.length;
            if (!cart.length) {
                list.innerHTML = `<div id="cart-empty" style="text-align:center;padding:20px;color:#cbd5e1;font-size:12px;border:2px dashed #e2e8f0;border-radius:10px;"><i class="ri-add-circle-line" style="font-size:22px;margin-bottom:4px;display:block;"></i>Klik produk untuk menambahkan</div>`;
                subtotalEl.textContent = formatRupiah(0);
                totalEl.textContent = formatRupiah(0);
                document.getElementById('add-total-bayar').value = 0;
                calcKembalian();
                return;
            }

            const total = cart.reduce((s, c) => s + c.subtotal, 0);
            subtotalEl.textContent = formatRupiah(total);
            totalEl.textContent = formatRupiah(total);

            list.innerHTML = cart.map(c => `
                <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px;">
                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px;">
                        <div style="font-size:12px;font-weight:600;color:#1e293b;flex:1;">${escapeHtml(c.produk_nama)}</div>
                        <button onclick="removeFromCart(${c.produk_id})" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:0;font-size:14px;line-height:1;"><i class="ri-close-line"></i></button>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div style="display:flex;align-items:center;gap:4px;">
                            <button onclick="updateCartQty(${c.produk_id},-1)" style="width:26px;height:26px;border-radius:6px;border:1.5px solid #e2e8f0;background:#fff;color:#475569;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:700;">−</button>
                            <span style="font-size:13px;font-weight:700;color:#1e293b;min-width:28px;text-align:center;">${c.jumlah}</span>
                            <button onclick="updateCartQty(${c.produk_id},1)" style="width:26px;height:26px;border-radius:6px;border:1.5px solid #e2e8f0;background:#fff;color:#475569;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:700;">+</button>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:12px;font-weight:700;color:#3b82f6;">${formatRupiah(c.subtotal)}</div>
                            <div style="font-size:10px;color:#94a3b8;">${formatRupiah(c.harga_jual)}/pcs</div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function calcKembalian() {
            const total = cart.reduce((s, c) => s + c.subtotal, 0);
            const bayar = parseInt(document.getElementById('add-total-bayar').value) || 0;
            const kmbl = Math.max(0, bayar - total);
            document.getElementById('cart-kembalian').textContent = formatRupiah(kmbl);
            const lunasLabel = document.getElementById('lunas-label');
            const slider = document.querySelector('.lunas-slider');
            const thumb = document.querySelector('.lunas-thumb');
            const isLunas = document.getElementById('add-lunas').checked;
            if (isLunas) {
                lunasLabel.textContent = '✓ LUNAS';
                lunasLabel.style.color = '#059669';
                if (slider) slider.style.backgroundColor = '#22c55e';
                if (thumb) thumb.style.left = '24px';
            } else {
                lunasLabel.textContent = '✗ BELUM LUNAS';
                lunasLabel.style.color = '#dc2626';
                if (slider) slider.style.backgroundColor = '#ef4444';
                if (thumb) thumb.style.left = '2px';
            }
        }

        function toggleLunas(el) {
            const checked = document.getElementById('add-lunas').checked;
            document.getElementById('add-lunas').checked = !checked;
            const slider = document.querySelector('.lunas-slider');
            const thumb = document.querySelector('.lunas-thumb');
            if (!checked) {
                slider.style.backgroundColor = '#22c55e';
                thumb.style.left = '24px';
                document.getElementById('lunas-label').textContent = '✓ LUNAS';
                document.getElementById('lunas-label').style.color = '#059669';
            } else {
                slider.style.backgroundColor = '#ef4444';
                thumb.style.left = '2px';
                document.getElementById('lunas-label').textContent = '✗ BELUM LUNAS';
                document.getElementById('lunas-label').style.color = '#dc2626';
            }
        }

        // =====================
        // Table rendering
        // =====================
        function renderTable(items) {
            const tbody = document.getElementById('penjualanTableBody');
            const cardList = document.getElementById('penjualanCardList');
            const empty = document.getElementById('emptyState');
            if (!items.length) { tbody.innerHTML = ''; cardList.innerHTML = ''; empty.classList.remove('hidden'); return; }
            empty.classList.add('hidden');

            tbody.innerHTML = items.map(p => `
                <tr>
                    <td>#${p.id}</td>
                    <td>${p.created_at ? new Date(p.created_at).toLocaleString('id-ID', {dateStyle:'short', timeStyle:'short'}) : '-'}</td>
                    <td>${p.pelanggan_nama ? escapeHtml(p.pelanggan_nama) : '<span style="color:#94a3b8;font-style:italic;">Umum</span>'}</td>
                    <td>
                        <div style="max-width:200px;">
                            ${(p.items||[]).map(i => `
                                <div style="font-size:11px;padding:2px 0;border-bottom:1px solid #f1f5f9;">
                                    <span style="font-weight:600;">${escapeHtml(i.produk?.nama||'-')}</span>
                                    <span style="color:#64748b;"> ×${i.jumlah}</span>
                                    <span style="color:#3b82f6;font-weight:600;">${formatRupiah(i.subtotal)}</span>
                                </div>
                            `).join('')}
                        </div>
                    </td>
                    <td style="font-weight:700">${formatRupiah(p.total_harga)}</td>
                    <td style="font-weight:600;color:#22c55e">${formatRupiah(p.total_bayar)}</td>
                    <td style="color:#f59e0b;font-weight:600">${formatRupiah(p.kembalian||0)}</td>
                    <td><span style="font-size:11px;background:#f1f5f9;padding:3px 8px;border-radius:6px;">${tipeIcon(p.tipe_pembayaran)} ${tipeLabel(p.tipe_pembayaran||'tunai')}</span></td>
                    <td><span class="status-badge status-${p.lunas ? 'completed' : 'pending'}">${p.lunas ? '✓ Lunas' : '○ Belum'}</span></td>
                    <td><span class="status-badge status-${p.status}">${p.status === 'completed' ? 'Selesai' : p.status === 'pending' ? 'Pending' : 'Batal'}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-secondary btn-sm" onclick="printReceipt(${p.id})" title="Cetak Struk"><i class="ri-printer-line"></i></button>
                            <button class="btn btn-warning btn-sm" onclick='openEditModal(${p.id})'><i class="ri-edit-2-line"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deletePenjualan(${p.id})"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');

            cardList.innerHTML = items.map(p => `
                <div class="mobile-card">
                    <div class="mobile-card-row"><span class="mobile-card-label">ID</span><span class="mobile-card-value">#${p.id}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Pelanggan</span><span class="mobile-card-value">${p.pelanggan_nama ? escapeHtml(p.pelanggan_nama) : '<span style="color:#94a3b8;">Umum</span>'}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Total</span><span class="mobile-card-value" style="font-weight:700;color:#22c55e;">${formatRupiah(p.total_harga)}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Bayar</span><span class="mobile-card-value">${formatRupiah(p.total_bayar)}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Kembalian</span><span class="mobile-card-value" style="color:#f59e0b;font-weight:600;">${formatRupiah(p.kembalian||0)}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Tipe</span><span class="mobile-card-value">${tipeIcon(p.tipe_pembayaran)} ${tipeLabel(p.tipe_pembayaran||'tunai')}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Lunas</span><span class="status-badge status-${p.lunas ? 'completed' : 'pending'}">${p.lunas ? '✓ Lunas' : '○ Belum'}</span></div>
                    <div class="mobile-card-row"><span class="mobile-card-label">Status</span><span class="status-badge status-${p.status}">${p.status === 'completed' ? 'Selesai' : p.status === 'pending' ? 'Pending' : 'Batal'}</span></div>
                    <div class="mobile-card-actions">
                        <button class="btn btn-secondary btn-sm" onclick="printReceipt(${p.id})"><i class="ri-printer-line"></i> Cetak</button>
                        <button class="btn btn-warning btn-sm" onclick='openEditModal(${p.id})'><i class="ri-edit-2-line"></i> Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deletePenjualan(${p.id})"><i class="ri-delete-bin-line"></i></button>
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

        // =====================
        // Modal control
        // =====================
        function openModal(type) {
            if (type === 'add') {
                cart = [];
                renderCart();
                document.getElementById('produk-select').value = '';
                document.getElementById('price-custom-panel').style.display = 'none';
                document.getElementById('add-pelanggan').value = '';
                document.getElementById('add-total-bayar').value = 0;
                document.getElementById('add-lunas').checked = true;
                document.querySelectorAll('input[name="tipe_pembayaran"]').forEach(r => r.checked = r.value === 'tunai');
                document.querySelectorAll('.tipe-opt').forEach(el => {
                    const isActive = el.dataset.val === 'tunai';
                    el.style.background = isActive ? '#3b82f6' : '#fff';
                    el.style.borderColor = isActive ? '#3b82f6' : '#e2e8f0';
                    el.querySelector('span:last-child').style.color = isActive ? '#fff' : '#475569';
                });
                document.getElementById('modal-add').classList.add('show');
            }
        }

        function openEditModal(id) {
            const p = penjualanCache[id];
            if (!p) return;
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-status-val').value = p.status || 'pending';
            document.getElementById('edit-total-bayar').value = p.total_bayar || 0;
            document.getElementById('edit-tipe-bayar').value = p.tipe_pembayaran || 'tunai';
            document.getElementById('edit-lunas-val').value = p.lunas ? '1' : '0';
            document.getElementById('edit-keterangan').value = p.keterangan || '';
            const itemsList = document.getElementById('edit-items-list');
            itemsList.innerHTML = (p.items||[]).map(i => `
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #f1f5f9;">
                    <div><div style="font-size:12px;font-weight:600;color:#1e293b;">${escapeHtml(i.produk?.nama||'-')}</div><div style="font-size:11px;color:#64748b;">×${i.jumlah}</div></div>
                    <div style="font-size:12px;font-weight:700;color:#3b82f6;">${formatRupiah(i.subtotal)}</div>
                </div>
            `).join('');
            document.getElementById('modal-edit').classList.add('show');
        }

        function closeModal(type) { document.getElementById('modal-' + type).classList.remove('show'); }

        function setLoading(btn, on) {
            if (!btn) return;
            btn.disabled = on;
            btn.innerHTML = on ? '<i class="ri-loader-2-line" style="display:inline-block;animation:spin 1s linear infinite"></i> Menyimpan...' : (btn.dataset.original || btn.innerHTML);
        }

        // =====================
        // Submit penjualan
        // =====================
        async function submitPenjualan() {
            if (!cart.length) {
                Swal.fire({ toast:true, position:'top-end', icon:'warning', title:'Pilih minimal satu produk', showConfirmButton:false, timer:3000 });
                return;
            }
            const btn = document.getElementById('btn-add');
            btn.dataset.original = btn.innerHTML;
            setLoading(btn, true);
            const payload = {
                pelanggan_nama: document.getElementById('add-pelanggan').value,
                items: cart.map(c => ({
                    produk_id: c.produk_id,
                    jumlah: c.jumlah,
                    harga_beli: c.harga_beli,
                    harga_jual: c.harga_jual,
                })),
                total_bayar: parseInt(document.getElementById('add-total-bayar').value) || 0,
                tipe_pembayaran: document.querySelector('input[name="tipe_pembayaran"]:checked')?.value || 'tunai',
                lunas: document.getElementById('add-lunas').checked,
            };
            try {
                const res = await fetch('/penjualan', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    closeModal('add');
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title: data.message, showConfirmButton:false, timer:3000 });
                    await loadData(currentPage);
                } else {
                    Swal.fire({ toast:true, position:'top-end', icon:'error', title: data.message || 'Gagal', showConfirmButton:false, timer:3000 });
                }
            } catch (err) { Swal.fire({ toast:true, position:'top-end', icon:'error', title: 'Gagal terhubung ke server', showConfirmButton:false, timer:3000 }); }
            finally { setLoading(btn, false); }
        }

        // =====================
        // Submit edit
        // =====================
        async function submitEdit(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-edit');
            btn.dataset.original = btn.textContent;
            setLoading(btn, true);
            const id = document.getElementById('edit-id').value;
            const payload = {
                status: document.getElementById('edit-status-val').value,
                total_bayar: parseInt(document.getElementById('edit-total-bayar').value) || 0,
                tipe_pembayaran: document.getElementById('edit-tipe-bayar').value,
                lunas: document.getElementById('edit-lunas-val').value === '1',
                keterangan: document.getElementById('edit-keterangan').value,
            };
            try {
                const res = await fetch(`/penjualan/${id}`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    closeModal('edit');
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title: data.message, showConfirmButton:false, timer:3000 });
                    await loadData(currentPage);
                } else {
                    Swal.fire({ toast:true, position:'top-end', icon:'error', title: data.message||'Gagal', showConfirmButton:false, timer:3000 });
                }
            } catch (err) {
                Swal.fire({ toast:true, position:'top-end', icon:'error', title: 'Gagal terhubung ke server', showConfirmButton:false, timer:3000 });
            } finally {
                setLoading(btn, false);
            }
        }

        async function deletePenjualan(id) {
            const result = await Swal.fire({
                title: 'Hapus Penjualan?', text: `Transaksi #${id} akan dihapus.`, icon: 'warning', toast: true, position: 'top-end',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/penjualan/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (data.success) { Swal.fire({ toast:true, position:'top-end', icon:'success', title: data.message, showConfirmButton:false, timer:3000 }); await loadData(currentPage); }
                else { Swal.fire({ toast:true, position:'top-end', icon:'error', title: data.message||'Gagal', showConfirmButton:false, timer:3000 }); }
            } catch (err) { Swal.fire({ toast:true, position:'top-end', icon:'error', title: 'Gagal', showConfirmButton:false, timer:3000 }); }
        }

        function printReceipt(id) {
            window.open('/penjualan/' + id + '/receipt', '_blank');
        }

        async function loadData(page = 1) {
            currentPage = page;
            const params = new URLSearchParams({
                page, per_page: 10,
                search: document.getElementById('searchInput')?.value || '',
                date_from: document.getElementById('filterDateFrom')?.value || '',
                date_to: document.getElementById('filterDateTo')?.value || '',
                status: document.getElementById('filterStatus')?.value || '',
                lunas: document.getElementById('filterLunas')?.value || ''
            });
            try {
                const res = await fetch(`/penjualan/data?${params}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                penjualanCache = {};
                (data.data||[]).forEach(p => { penjualanCache[p.id] = p; });
                renderTable(data.data||[]);
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

        // Payment type selector
        document.querySelectorAll('input[name="tipe_pembayaran"]').forEach(r => {
            r.addEventListener('change', () => {
                document.querySelectorAll('.tipe-opt').forEach(el => {
                    const isActive = el.dataset.val === r.value;
                    el.style.background = isActive ? '#3b82f6' : '#fff';
                    el.style.borderColor = isActive ? '#3b82f6' : '#e2e8f0';
                    el.querySelector('span:last-child').style.color = isActive ? '#fff' : '#475569';
                });
            });
        });

        document.getElementById('searchInput')?.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); loadData(1); } });

        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', e => { if (e.target === el) el.classList.remove('show'); });
        });
    </script>

    <style>
        .produk-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s;
            background: #fff;
            min-height: 72px;
        }
        .produk-card:hover { border-color: #3b82f6; background: #eff6ff; }
        .produk-card:active { transform: scale(0.97); }
        .produk-card.no-stock { opacity: 0.55; }

        .lunas-slider { box-shadow: none !important; }
        .lunas-thumb { box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important; }

        @media (max-width: 640px) {
            .modal[data-v-xxx] { max-width: 100% !important; }
        }
    </style>
</x-admin-layout>
