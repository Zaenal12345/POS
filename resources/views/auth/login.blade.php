<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Cashier Application</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; min-height: 100vh; display: flex; }
        .left-panel {
            flex: 1; background: linear-gradient(160deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
            display: flex; align-items: center; justify-content: center; color: white; padding: 40px;
        }
        .left-content { text-align: center; max-width: 400px; }
        .left-content i { font-size: 56px; margin-bottom: 20px; opacity: 0.9; }
        .left-content h1 { font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .left-content p { font-size: 14px; opacity: 0.8; line-height: 1.6; }
        .right-panel {
            width: 440px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: #fff; padding: 32px 24px;
        }
        .login-card { width: 100%; max-width: 340px; }
        .login-card h2 { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
        .login-card p { font-size: 13px; color: #64748b; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 11px 14px; border: 1px solid #e2e8f0;
            border-radius: 10px; font-size: 14px; outline: none;
            transition: border 0.2s, box-shadow 0.2s; font-family: 'Figtree', sans-serif;
        }
        .form-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .form-input.error { border-color: #ef4444; }
        .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; display: none; }
        .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; font-size: 13px; color: #64748b; }
        .remember-row input { width: 16px; height: 16px; accent-color: #4f46e5; }
        .btn-login {
            width: 100%; padding: 12px; background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 600; cursor: pointer;
            transition: opacity 0.2s, transform 0.1s; font-family: 'Figtree', sans-serif;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { opacity: 0.9; }
        .btn-login:active { transform: scale(0.98); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
        .brand-mobile { display: none; background: linear-gradient(160deg, #4f46e5 0%, #7c3aed 100%); padding: 20px 24px; align-items: center; gap: 12px; }
        .brand-mobile i { font-size: 28px; color: white; }
        .brand-mobile span { font-size: 18px; font-weight: 700; color: white; }

        @media (max-width: 767px) {
            body { flex-direction: column; }
            .left-panel { display: none; }
            .brand-mobile { display: flex; }
            .right-panel {
                width: 100%; flex: 1;
                padding: 24px 20px;
                align-items: flex-start;
            }
            .login-card { max-width: 100%; }
            .login-card h2 { font-size: 20px; }
        }
    </style>
</head>
<body>
    {{-- Mobile only header --}}
    <div class="brand-mobile">
        <i class="ri-shopping-cart-2-fill"></i>
        <span>Cashier App</span>
    </div>

    <div class="left-panel">
        <div class="left-content">
            <i class="ri-shopping-cart-2-fill"></i>
            <h1>Cashier Application</h1>
            <p>Kelola toko anda dengan mudah dan efisien dengan sistem kasir modern</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-card">
            <h2>Sign In</h2>
            <p>Masuk ke akun anda untuk melanjutkan</p>

            <div id="error-global" class="error-msg" style="display:none; background:#fee2e2; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px;"></div>

            <form id="login-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input class="form-input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com" autocomplete="email">
                    <div class="error-text" id="email-error"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••" autocomplete="current-password">
                    <div class="error-text" id="password-error"></div>
                </div>
                <div class="remember-row">
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                        <input type="checkbox" name="remember" id="remember"> Remember me
                    </label>
                </div>
                <button type="submit" class="btn-login" id="btn-login">
                    <span id="btn-text">Sign In</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const btn = document.getElementById('btn-login');
        const btnText = document.getElementById('btn-text');
        const errorGlobal = document.getElementById('error-global');

        function clearErrors() {
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
            document.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
            errorGlobal.style.display = 'none';
        }

        function showFieldError(field, msg) {
            const input = document.getElementById(field);
            const error = document.getElementById(field + '-error');
            if (input) input.classList.add('error');
            if (error) { error.textContent = msg; error.style.display = 'block'; }
        }

        function setLoading(on) {
            btn.disabled = on;
            btnText.innerHTML = on
                ? '<i class="ri-loader-2-line" style="display:inline-block;animation:spin 1s linear infinite"></i> Memproses...'
                : 'Sign In';
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();
            setLoading(true);

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;

            try {
                const res = await fetch('{{ route('login') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email, password, remember, _token: '{{ csrf_token() }}' })
                });

                const data = await res.json();

                if (res.status === 422 && data.errors) {
                    Object.keys(data.errors).forEach(key => showFieldError(key, data.errors[key][0]));
                    setLoading(false);
                    return;
                }

                if (data.success === false || data.status === 401) {
                    errorGlobal.textContent = data.message || 'Email atau password salah';
                    errorGlobal.style.display = 'block';
                    setLoading(false);
                    return;
                }

                if (data.success || res.redirected || data.url) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Login berhasil!',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = data.url || '{{ route('dashboard') }}';
                    });
                } else {
                    errorGlobal.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                    errorGlobal.style.display = 'block';
                    setLoading(false);
                }
            } catch (err) {
                errorGlobal.textContent = 'Gagal terhubung ke server. Periksa koneksi internet anda.';
                errorGlobal.style.display = 'block';
                setLoading(false);
            }
        });
    </script>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</body>
</html>
