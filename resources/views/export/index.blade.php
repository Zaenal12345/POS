<x-admin-layout title="Export Laporan">
    <div class="card">
        <div class="card-header">
            <h3>Export Laporan</h3>
        </div>
        <div class="card-body">
            <p style="color:#64748b; margin-bottom:20px;">Export laporan dalam format PDF atau Excel untuk arsip bulanan.</p>

            <form action="{{ route('export.pdf') }}" method="GET" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:16px;">
                <h4 style="margin:0 0 16px; font-size:15px; font-weight:600; color:#1e293b;">Export PDF</h4>
                <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label" style="font-size:12px;">Bulan</label>
                        <select class="form-select" name="bulan" style="width:auto; min-width:130px;">
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
                    <button type="submit" class="btn btn-primary" style="margin-top:18px;">
                        <i class="ri-file-pdf-line"></i> Download PDF
                    </button>
                </div>
            </form>

            <form action="{{ route('export.excel') }}" method="GET" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
                <h4 style="margin:0 0 16px; font-size:15px; font-weight:600; color:#1e293b;">Export Excel (CSV)</h4>
                <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label" style="font-size:12px;">Bulan</label>
                        <select class="form-select" name="bulan" style="width:auto; min-width:130px;">
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
                    <button type="submit" class="btn btn-success" style="margin-top:18px; background:#16a34a;">
                        <i class="ri-file-excel-line"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
