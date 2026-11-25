<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Login Pengelola - Sinar Citra Lestari</title>
    <link rel="icon" href="{{ asset('logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <style>
        :root {
            --navy-950: #071D4E;
            --navy-900: #0B2E7A;
            --primary: #1D4ED8;
            --primary-hover: #1E40AF;
            --canvas: #F4F7FC;
            --surface: #FFFFFF;
            --border: #E3EAF4;
            --border-strong: #CBD6E7;
            --text-main: #0F1E3A;
            --text-secondary: #3B4C6B;
            --text-muted: #64748B;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--canvas);
            background-image:
                radial-gradient(circle at 12% 10%, rgba(29, 78, 216, 0.08) 0%, transparent 42%),
                radial-gradient(circle at 88% 90%, rgba(11, 46, 122, 0.07) 0%, transparent 42%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1.25rem;
            -webkit-font-smoothing: antialiased;
        }

        .login-shell {
            width: 100%;
            max-width: 860px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 24px 48px -16px rgba(11, 46, 122, 0.14), 0 4px 12px rgba(11, 46, 122, 0.05);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .login-shell {
                grid-template-columns: 340px 1fr;
            }
        }

        /* Left Corporate Navy Brand Panel */
        .login-brand-panel {
            background: linear-gradient(165deg, #0E378F 0%, #0B2E7A 55%, #071D4E 100%);
            color: #FFFFFF;
            padding: 2.25rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-brand-panel::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            right: -90px;
            bottom: -90px;
            pointer-events: none;
        }

        .login-brand-panel::before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.18) 0%, transparent 70%);
            left: -40px;
            top: -40px;
            pointer-events: none;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .brand-mark-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .brand-mark-name {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .brand-mark-name span {
            color: #93C5FD;
        }

        .brand-mark-sub {
            font-size: 0.72rem;
            color: rgba(219, 234, 254, 0.75);
            font-weight: 500;
        }

        .brand-copy {
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 767.98px) {
            .login-brand-panel {
                padding: 1.5rem 1.5rem;
            }
            .brand-copy {
                margin: 1rem 0 0;
            }
            .brand-highlights,
            .brand-footer-meta {
                display: none;
            }
        }

        .brand-copy h2 {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.35;
            margin-bottom: 0.65rem;
        }

        .brand-copy p {
            font-size: 0.825rem;
            color: rgba(219, 234, 254, 0.82);
            line-height: 1.6;
        }

        .brand-highlights {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-top: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .brand-highlights li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.79rem;
            color: rgba(239, 246, 255, 0.88);
        }

        .brand-highlights i {
            color: #93C5FD;
            font-size: 0.9rem;
        }

        .brand-footer-meta {
            font-size: 0.72rem;
            color: rgba(191, 219, 254, 0.65);
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        /* Right White Form Panel */
        .login-card {
            padding: 2.25rem 2.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 575.98px) {
            .login-card {
                padding: 1.75rem 1.35rem;
            }
        }

        .login-header {
            margin-bottom: 1.5rem;
        }

        .login-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
            margin-bottom: 0.3rem;
        }

        .login-subtitle {
            font-size: 0.825rem;
            color: var(--text-muted);
            line-height: 1.45;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            font-size: 0.8rem;
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.9rem;
            color: #94A3B8;
            font-size: 0.92rem;
            pointer-events: none;
            transition: color 0.15s ease;
        }

        .form-input {
            width: 100%;
            background: #F8FAFD;
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0.68rem 2.65rem 0.68rem 2.45rem;
            font-family: inherit;
            font-size: 0.86rem;
            color: var(--text-main);
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .form-input::placeholder {
            color: #94A3B8;
        }

        .form-input:focus {
            background: #FFFFFF;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .btn-toggle-eye {
            position: absolute;
            right: 0.2rem;
            width: 40px;
            height: 40px;
            background: transparent;
            border: none;
            color: #64748B;
            cursor: pointer;
            padding: 0;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .btn-toggle-eye:hover {
            color: var(--navy-900);
        }

        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            margin-bottom: 1.35rem;
            color: var(--text-muted);
        }

        .remember-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
            user-select: none;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .remember-checkbox input[type="checkbox"] {
            accent-color: var(--primary);
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: var(--navy-900);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #FFFFFF;
            border: 1px solid var(--primary);
            border-radius: 9px;
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.24);
            transition: background-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            box-shadow: 0 6px 16px rgba(29, 78, 216, 0.3);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .btn-submit:disabled {
            opacity: 0.72;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            display: inline-block;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .turnstile-box {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.25rem;
            min-height: 65px;
            width: 100%;
            border-radius: 8px;
        }

        .turnstile-box iframe {
            max-width: 100% !important;
        }

        .footer-back {
            text-align: center;
            margin-top: 1.4rem;
            padding-top: 1.15rem;
            border-top: 1px solid var(--border);
        }

        .footer-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.15s ease;
        }

        .footer-link:hover {
            color: var(--navy-900);
        }

        /* MODAL BANTUAN LUPA SANDI */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(7, 29, 78, 0.55);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 9999;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 14px;
            max-width: 390px;
            width: 100%;
            padding: 1.5rem;
            box-shadow: 0 24px 48px -12px rgba(7, 29, 78, 0.25);
            animation: fadeIn 0.18s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-box-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .modal-box-title i {
            color: var(--primary);
        }

        .modal-box-text {
            font-size: 0.825rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .modal-box-text strong {
            color: var(--text-main);
        }

        .modal-box-btn {
            width: 100%;
            background: var(--navy-900);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 0.62rem;
            font-family: inherit;
            font-size: 0.825rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .modal-box-btn:hover {
            background: var(--primary);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- HEADER -->
        <div class="login-header">
            <div class="login-logo">
                <img src="{{ asset('logo.jpeg') }}" alt="Jumala Living Logo">
            </div>
            <h1 class="login-title">Jumala Living</h1>
            <p class="login-subtitle">Portal Masuk Pengelola Kos</p>
        </div>

            <!-- ERROR ALERT -->
            @if ($errors->any())
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle" style="margin-top: 1px;"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- FORM -->
            <form action="{{ route('login.post') }}" method="POST" id="formLogin" onsubmit="handleLoginSubmit(event)">
                @csrf

                <!-- EMAIL -->
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="email"
                               autocapitalize="none"
                               spellcheck="false"
                               placeholder="admin@kosanjumala.com"
                               class="form-input">
                    </div>
                </div>

                <!-- PASSWORD WITH EYE TOGGLE -->
                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               autocomplete="current-password"
                               placeholder="Masukkan kata sandi"
                               class="form-input">
                        <button type="button"
                                class="btn-toggle-eye"
                                id="togglePassword"
                                onclick="togglePasswordVisibility()"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                title="Tampilkan / Sembunyikan sandi">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- REMEMBER ME & FORGOT PASSWORD -->
                <div class="options-row">
                    <label class="remember-checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="javascript:void(0)" class="forgot-link" onclick="openForgotModal()">Lupa kata sandi?</a>
                </div>

                <!-- CLOUDFLARE TURNSTILE CAPTCHA -->
                @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
                    <div class="turnstile-box">
                        <div class="cf-turnstile"
                             data-sitekey="{{ config('services.turnstile.site_key') }}"
                             data-theme="light"
                             data-size="flexible"></div>
                    </div>
                @endif

                <!-- SUBMIT BUTTON WITH SPINNER STATE -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span id="btnText">Masuk ke Dashboard</span>
                    <i class="bi bi-arrow-right" id="btnArrow"></i>
                </button>
            </form>

            <!-- BACK TO HOME -->
            <div class="footer-back">
                <a href="{{ route('home') }}" class="footer-link">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Beranda Publik</span>
                </a>
            </div>
        </div>
    </div>

    <!-- MODAL BANTUAN LUPA KATA SANDI -->
    <div class="modal-overlay" id="forgotModal" onclick="closeForgotModalOnBackdrop(event)">
        <div class="modal-box">
            <div class="modal-box-title">
                <i class="bi bi-shield-lock"></i> Bantuan Kata Sandi
            </div>
            <p class="modal-box-text">
                Untuk menjaga keamanan operasional kos, pengaturan ulang kata sandi dikelola langsung oleh <strong>Super Admin</strong>.
                <br><br>
                Silakan hubungi administrator pusat untuk mendapatkan kata sandi baru atau bantuan pemulihan akses akun Anda.
            </p>
            <button type="button" class="modal-box-btn" onclick="closeForgotModal()">Mengerti</button>
        </div>
    </div>

    <script>
        // Toggle Show/Hide Password
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput && eyeIcon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                }
            }
        }

        // Handle Submit Loading State
        function handleLoginSubmit(e) {
            const btn = document.getElementById('btnSubmit');
            const btnText = document.getElementById('btnText');
            const btnArrow = document.getElementById('btnArrow');

            if (btn && btnText) {
                setTimeout(() => {
                    btn.disabled = true;
                    btnText.textContent = 'Memverifikasi...';
                    if (btnArrow) {
                        btnArrow.className = 'bi bi-arrow-repeat spinner';
                    }
                }, 10);
            }
        }

        // Modal Forgot Password
        function openForgotModal() {
            const modal = document.getElementById('forgotModal');
            if (modal) modal.classList.add('active');
        }

        function closeForgotModal() {
            const modal = document.getElementById('forgotModal');
            if (modal) modal.classList.remove('active');
        }

        function closeForgotModalOnBackdrop(e) {
            if (e.target.id === 'forgotModal') {
                closeForgotModal();
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeForgotModal();
            }
        });
    </script>
</body>
</html>
