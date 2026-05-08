<x-admin-layout title="Pengaturan Toko">
    <div class="card">
        <div class="card-header">
            <h3>Pengaturan Toko</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.store') }}" method="POST">
                @csrf
                <div style="max-width:600px;">
                    <div class="form-group">
                        <label class="form-label">Nama Toko</label>
                        <input class="form-input" type="text" name="nama_toko" value="{{ $settings['nama_toko'] ?? '' }}" required placeholder="cth: Toko Kasir Maju">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Toko</label>
                        <textarea class="form-input" name="alamat" rows="3" placeholder="cth: Jl. Raya No. 123, Jakarta">{{ $settings['alamat'] ?? '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input class="form-input" type="text" name="no_telp" value="{{ $settings['no_telp'] ?? '' }}" placeholder="cth: 0812-3456-7890">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Footer Nota</label>
                        <input class="form-input" type="text" name="footer_nota" value="{{ $settings['footer_nota'] ?? '' }}" placeholder="cth: Terima kasih atas kunjungan Anda!">
                        <span style="font-size:12px;color:#94a3b8;">Teks yang muncul di bagian bawah struk/nota</span>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
