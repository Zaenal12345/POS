<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CMS - Backend' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background: #f1f5f9; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: 260px; height: 100vh;
            background: linear-gradient(180deg, #4f46e5 0%, #7c3aed 100%);
            color: white; z-index: 1000; overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .sidebar.open { transform: translateX(0); }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between; }
        .sidebar-header h1 { font-size: 18px; font-weight: 700; }
        .sidebar-header span { font-size: 12px; opacity: 0.7; }
        .sidebar-close { background: none; border: none; color: rgba(255,255,255,0.7); font-size: 22px; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; }
        .sidebar-close:hover { background: rgba(255,255,255,0.15); color: white; }
        .sidebar-nav { padding: 16px 0; }
        .nav-label { padding: 8px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; }
        .nav-item a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; color: rgba(255,255,255,0.8);
            text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all 0.2s;
        }
        .nav-item a:hover, .nav-item a.active {
            background: rgba(255,255,255,0.15); color: white;
            border-left: 3px solid white;
        }
        .nav-item a i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item span { flex: 1; }
        .sidebar-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 999; display: none;
        }
        .sidebar-overlay.show { display: block; }
        .main { margin-left: 0; min-height: 100vh; transition: margin-left 0.3s ease; }
        @media (min-width: 768px) {
            .sidebar { transform: translateX(0); }
            .sidebar-close { display: none; }
            .main { margin-left: 260px; }
        }
        .topbar {
            background: white; padding: 0 16px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100;
        }
        @media (min-width: 768px) { .topbar { padding: 0 32px; } }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-title { font-size: 16px; font-weight: 600; color: #1e293b; }
        @media (min-width: 768px) { .topbar-title { font-size: 18px; } }
        .hamburger {
            background: none; border: none; cursor: pointer;
            padding: 6px; display: flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: background 0.2s; color: #475569;
        }
        .hamburger:hover { background: #f1f5f9; }
        .hamburger i { font-size: 22px; }
        @media (min-width: 768px) { .hamburger { display: none; } }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        @media (min-width: 768px) { .topbar-right { gap: 16px; } }
        .topbar-right a { color: #64748b; font-size: 20px; text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 14px; }
        .topbar-right .user-name { font-size: 13px; font-weight: 600; color: #1e293b; display: none; }
        @media (min-width: 640px) { .topbar-right .user-name { display: block; } }
        .topbar-right .user-role { font-size: 11px; color: #94a3b8; display: none; }
        @media (min-width: 768px) { .topbar-right .user-role { display: block; } }
        .page-content { padding: 20px 16px; }
        @media (min-width: 768px) { .page-content { padding: 32px; } }
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        @media (min-width: 768px) { .card-header { padding: 20px 24px; } }
        .card-header h3 { font-size: 15px; font-weight: 600; color: #1e293b; }
        @media (min-width: 768px) { .card-header h3 { font-size: 16px; } }
        .card-body { padding: 16px; }
        @media (min-width: 768px) { .card-body { padding: 24px; } }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn:active { transform: scale(0.97); }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-secondary:hover { background: #cbd5e1; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8fafc; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        @media (min-width: 768px) { thead th { padding: 12px 16px; font-size: 12px; } }
        tbody td { padding: 12px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        @media (min-width: 768px) { tbody td { padding: 14px 16px; font-size: 14px; } }
        tbody tr:hover { background: #f8fafc; }

        /* Mobile Cards (hide table on mobile, show cards) */
        .mobile-cards { display: block; }
        .mobile-card { background: #f8fafc; border-radius: 10px; padding: 14px; margin-bottom: 10px; border: 1px solid #e2e8f0; }
        .mobile-card:last-child { margin-bottom: 0; }
        .mobile-card-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .mobile-card-row:last-child { margin-bottom: 0; }
        .mobile-card-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
        .mobile-card-value { font-size: 13px; color: #334155; font-weight: 500; text-align: right; }
        .mobile-card-actions { display: flex; gap: 6px; margin-top: 10px; }
        @media (min-width: 768px) {
            .mobile-cards { display: none; }
            .desktop-table { display: block; }
        }
        @media (max-width: 767px) {
            .desktop-table { display: none; }
        }

        .search-bar { display: flex; align-items: center; gap: 8px; }
        .search-input { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 160px; outline: none; }
        @media (min-width: 640px) { .search-input { width: 260px; } }
        .search-input:focus { border-color: #4f46e5; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
        .form-input:focus { border-color: #4f46e5; }
        .form-select { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: white; }
        .form-select:focus { border-color: #4f46e5; }
        .form-actions { display: flex; gap: 8px; justify-content: flex-end; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 16px; }
        .modal-overlay.show { display: flex; }
        .modal { background: white; border-radius: 16px; width: 480px; max-width: 100%; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .modal-header h3 { font-size: 16px; font-weight: 600; }
        .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; }
        .modal-body { padding: 24px; }
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .action-btns { display: flex; gap: 6px; }
        .empty-state { text-align: center; padding: 32px; color: #94a3b8; }
        @media (min-width: 768px) { .empty-state { padding: 48px; } }
        .empty-state i { font-size: 36px; margin-bottom: 12px; }
        @media (min-width: 768px) { .empty-state i { font-size: 48px; } }
        .hidden { display: none !important; }

        /* Pagination Styles */
        .pagination-wrap { display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; gap: 12px; }
        .pagination-info { font-size: 12px; color: #64748b; }
        @media (min-width: 768px) { .pagination-info { font-size: 13px; } }
        .pagination { display: flex; align-items: center; gap: 4px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; }
        .pagination li { display: inline-flex; }
        .pagination li a, .pagination li span { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 10px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; color: #64748b; background: white; margin: 0 2px; }
        .pagination li a:hover { background: #f8fafc; border-color: #cbd5e1; color: #4f46e5; }
        .pagination li.active span { background: #4f46e5; border-color: #4f46e5; color: white; font-weight: 600; }
        .pagination li.disabled span { opacity: 0.4; cursor: not-allowed; background: #f8fafc; border-color: #e2e8f0; color: #94a3b8; }
        .pagination li .page-link i { font-size: 18px; line-height: 1; }

        /* Scrollable table container */
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 10px; border: 1px solid #e2e8f0; }
        .table-scroll table { min-width: 600px; }

        /* Global Responsive Card Fonts */
        @media (max-width: 640px) {
            .card { border-radius: 10px; }
            .card-header { padding: 12px 14px !important; }
            .card-header h3 { font-size: 13px !important; }
            .card-body { padding: 14px !important; }
            .btn { padding: 6px 12px !important; font-size: 12px !important; }
            .form-input, .form-select { padding: 8px 12px !important; font-size: 13px !important; }
            .form-label { font-size: 12px !important; }
            tbody td { font-size: 12px !important; padding: 10px !important; }
            thead th { font-size: 10px !important; padding: 8px 10px !important; }
            .pagination li a, .pagination li span { min-width: 32px !important; height: 32px !important; font-size: 12px !important; }
        }
        @media (max-width: 480px) {
            .card-header { padding: 10px 12px !important; }
            .card-header h3 { font-size: 12px !important; }
            .card-body { padding: 12px !important; }
        }
    </style>
</head>
<body<?php if(session('success')): ?> data-flash-success="<?= htmlspecialchars(session('success'), ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?><?php if(session('error')): ?> data-flash-error="<?= htmlspecialchars(session('error'), ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?><?php if($errors->any()): ?> data-flash-errors="1"<?php endif; ?>>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div>
                <h1>Cashier App</h1>
                <span>Backend CMS</span>
            </div>
            <button class="sidebar-close" onclick="closeSidebar()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ri-dashboard-line"></i><span>Dashboard</span>
                </a>
            </div>
            <div class="nav-label">Master Data</div>
            <div class="nav-item">
                <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <i class="ri-folder-line"></i><span>Kategori</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('produk.index') }}" class="{{ request()->routeIs('produk.*') ? 'active' : '' }}">
                    <i class="ri-shopping-bag-line"></i><span>Produk</span>
                </a>
            </div>
            <div class="nav-label">Transaksi</div>
            <div class="nav-item">
                <a href="{{ route('pembelian.index') }}" class="{{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
                    <i class="ri-arrow-down-line"></i><span>Pembelian</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('penjualan.index') }}" class="{{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                    <i class="ri-arrow-up-line"></i><span>Penjualan</span>
                </a>
            </div>
            <div class="nav-label">Laporan</div>
            <div class="nav-item">
                <a href="{{ route('report.index') }}" class="{{ request()->routeIs('report.*') ? 'active' : '' }}">
                    <i class="ri-bar-chart-line"></i><span>Report</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('keuntungan.index') }}" class="{{ request()->routeIs('keuntungan.*') ? 'active' : '' }}">
                    <i class="ri-coins-line"></i><span>Keuntungan</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('wallet.index') }}" class="{{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                    <i class="ri-wallet-line"></i><span>Dompet</span>
                </a>
            </div>
            <div class="nav-label">Pengaturan</div>
            <div class="nav-item">
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="ri-settings-3-line"></i><span>Pengaturan Toko</span>
                </a>
            </div>
            <div class="nav-label">Akun</div>
            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" onclick="this.parentElement.submit(); return false;">
                        <i class="ri-logout-box-line"></i><span>Logout</span>
                    </a>
                </form>
            </div>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="hamburger" onclick="openSidebar()">
                    <i class="ri-menu-line"></i>
                </button>
                <div>
                    <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
                </div>
            </div>
            <div class="topbar-right">
                <div style="text-align:right;">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
                <a href="#"><i class="ri-notification-3-line"></i></a>
            </div>
        </div>

        <div class="page-content">
            {{ $slot }}
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var body = document.body;
            var successMsg = body.getAttribute('data-flash-success');
            var errorMsg = body.getAttribute('data-flash-error');
            var hasErrors = body.getAttribute('data-flash-errors');

            if (successMsg) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: successMsg,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: function(toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }
            if (errorMsg) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: errorMsg,
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            }
            if (hasErrors) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Terjadi kesalahan pada input',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            }
        });
    </script>
    </main>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Global delete confirmation with SweetAlert
        function confirmDelete(e, nama) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                title: 'Hapus Data?',
                text: nama ? `"${nama}" akan dihapus permanen.` : 'Data ini akan dihapus permanen.',
                icon: 'warning',
                toast: true,
                position: 'top-end',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showClass: { popup: 'animate__animated animate__shakeX' }
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }

        // Global form submit feedback
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('[type="submit"]');
            if (!submitBtn || submitBtn.dataset.ajax === 'true') return;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-loader-2-line" style="display:inline-block;animation:spin 1s linear infinite"></i> Menyimpan...';
            const style = document.createElement('style');
            style.textContent = '@keyframes spin{to{transform:rotate(360deg)}}';
            document.head.appendChild(style);
        });
    </script>
</body>
</html>
