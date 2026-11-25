<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Login Pengelola — Jumala Living</title>
    <link rel="icon" href="{{ asset('logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <style>
        :root {
            --espresso-950: #210F08;
            --espresso-900: #2D150D;
            --espresso-800: #3B1D12;
            --espresso-700: #4F2A1B;
            --gold-500: #C6A15B;
            --gold-400: #E1C98E;
            --gold-600: #9E7831;
            --canvas: #FAF6F0;
            --surface: #FFFFFF;
            --beige: #E9DDCD;
            --beige-strong: #D8C6B0;
            --text-main: #29130C;
            --text-secondary: #5C463A;
            --text-muted: #846E60;
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
                radial-gradient(circle at 10% 10%, rgba(198, 161, 91, 0.14) 0%, transparent 42%),
                radial-gradient(circle at 90% 90%, rgba(59, 29, 18, 0.08) 0%, transparent 42%);
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
            max-width: 880px;
            background: var(--surface);
            border: 1px solid var(--beige);
            border-radius: 20px;
            box-shadow: 0 28px 56px -18px rgba(41, 19, 12, 0.18), 0 6px 16px rgba(41, 19, 12, 0.06);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .login-shell {
                grid-template-columns: 360px 1fr;
            }
        }

        /* Left Luxury Dark Espresso Panel */
        .login-brand-panel {
            background: linear-gradient(165deg, #432215 0%, #3B1D12 52%, #231009 100%);
            color: #FFF9F2;
            padding: 2.35rem 2.1rem;
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
            border: 1px solid rgba(198, 161, 91, 0.18);
            right: -90px;
            bottom: -90px;
            pointer-events: none;
        }

        .login-brand-panel::before {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(198, 161, 91, 0.16) 0%, transparent 70%);
            left: -50px;
            top: -50px;
            pointer-events: none;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            position: relative;
            z-index: 1;
        }

        .brand-mark-logo {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            border: 1.5px solid rgba(198, 161, 91, 0.72);
            overflow: hidden;
            background: #2B140C;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .brand-mark-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .brand-mark-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            line-height: 1.2;
            color: #FFFDF9;
        }

        .brand-mark-name span {
            color: var(--gold-400);
        }

        .brand-mark-sub {
            font-size: 0.68rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(225, 201, 142, 0.82);
            font-weight: 600;
            margin-top: 3px;
        }

        .brand-copy {
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        .brand-copy h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.48rem;
            font-weight: 700;
            line-height: 1.32;
            margin-bottom: 0.7rem;
            color: #FFFDF9;
        }

        .brand-copy p {
            font-size: 0.84rem;
            line-height: 1.65;
            color: rgba(248, 240, 230, 0.78);
        }

        .brand-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            position: relative;
            z-index: 1;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.8rem;
            color: rgba(255, 249, 242, 0.9);
            font-weight: 500;
        }

        .brand-features i {
            color: var(--gold-400);
            font-size: 0.95rem;
        }

        .brand-location {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(198, 161, 91, 0.2);
            font-size: 0.74rem;
            color: rgba(225, 201, 142, 0.78);
            display: flex;
            align-items: center;
            gap: 0.45rem;
            position: relative;
            z-index: 1;
        }

        /* Right Form Panel */
        .login-form-panel {
            padding: 2.5rem 2.35rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        @media (max-width: 575.98px) {
            .login-brand-panel,
            .login-form-panel {
                padding: 1.85rem 1.5rem;
            }
        }

        .form-eyebrow {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gold-600);
            margin-bottom: 0.35rem;
        }

        .form-heading {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            margin-bottom: 0.3rem;
        }

        .form-subheading {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.55rem;
        }

        .alert-box {
            padding: 0.75rem 0.95rem;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: 1.15rem;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }

        .alert-success {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
        }

        .field-group {
            margin-bottom: 1.15rem;
        }

        .field-label {
            display: block;
            font-size: 0.81rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.42rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.88rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            height: 44px;
            padding: 0.5rem 2.6rem 0.5rem 2.55rem;
            border: 1px solid var(--beige-strong);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.88rem;
            color: var(--text-main);
            background: #FFFFFF;
            transition: border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .field-input:focus {
            outline: none;
            border-color: var(--gold-500);
            box-shadow: 0 0 0 3px rgba(198, 161, 91, 0.18);
        }

        .toggle-pw-btn {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.3rem;
            font-size: 0.95rem;
        }

        .toggle-pw-btn:hover {
            color: var(--espresso-800);
        }

        .turnstile-wrap {
            margin: 1rem 0 1.25rem;
        }

        .btn-submit {
            width: 100%;
            height: 45px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #3B1D12 0%, #4F2A1B 100%);
            color: #FFFDF9;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 6px 18px rgba(59, 29, 18, 0.22);
            transition: transform 0.15s ease, filter 0.15s ease;
        }

        .btn-submit:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .form-footer-links {
            margin-top: 1.5rem;
            padding-top: 1.1rem;
            border-top: 1px solid var(--beige);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .back-home-link {
            color: var(--espresso-800);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .back-home-link:hover {
            color: var(--gold-600);
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <aside class="login-brand-panel">
            <div>
                <div class="brand-mark">
                    <div class="brand-mark-logo">
                        <img src="{{ asset('logo.jpeg') }}" alt="Jumala Living">
                    </div>
                    <div>
                        <div class="brand-mark-name">Jumala <span>Living</span></div>
                        <div class="brand-mark-sub">Premium Female Residence</div>
                    </div>
                </div>

                <div class="brand-copy">
                    <h2>Panel Pengelola Hunian Eksklusif</h2>
                    <p>Kelola ketersediaan kamar, tarif sewa, galeri foto, serta verifikasi pemesanan penghuni secara terpadu.</p>
                </div>

                <ul class="brand-features">
                    <li><i class="bi bi-door-open"></i> Manajemen Unit & Status Kamar Real-Time</li>
                    <li><i class="bi bi-calendar2-check"></i> Verifikasi Booking & Kwitansi Otomatis</li>
                    <li><i class="bi bi-images"></i> Sinkronisasi Langsung ke Website Publik</li>
                </ul>
            </div>

            <div class="brand-location">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Jl. Kenanga No. 3, Lowokwaru, Malang</span>
            </div>
        </aside>

        <section class="login-form-panel">
            <div class="form-eyebrow">Admin Portal</div>
            <h1 class="form-heading">Masuk ke Dashboard</h1>
            <p class="form-subheading">Gunakan akun pengelola Jumala Living Anda untuk melanjutkan.</p>

            @if(session('error') || session('failed'))
                <div class="alert-box alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') ?? session('failed') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-box alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="field-group">
                    <label for="email" class="field-label">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="field-input"
                            placeholder="admin@jumalaliving.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="field-group">
                    <label for="password" class="field-label">Kata Sandi</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input"
                            placeholder="Masukkan kata sandi"
                            required
                        >
                        <button type="button" class="toggle-pw-btn" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan kata sandi">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
                    <div class="turnstile-wrap">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
                    </div>
                @endif

                <button type="submit" class="btn-submit">
                    <span>Masuk Dashboard</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="form-footer-links">
                <a href="{{ route('home') }}" class="back-home-link">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Website</span>
                </a>
                <span>&copy; {{ date('Y') }} Jumala Living</span>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('togglePasswordBtn');
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (!btn || !input || !icon) return;

            btn.addEventListener('click', function () {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        });
    </script>
</body>
</html>
