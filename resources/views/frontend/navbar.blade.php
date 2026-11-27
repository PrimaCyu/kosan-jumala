@php
    $waNumber = '6282146138847';
    $socialLinks = collect($globalSosmed ?? []);

    $waSosmed = $socialLinks->first(function ($s) {
        $title = strtolower((string) ($s->title ?? $s->name ?? $s->platform ?? ''));
        $url = strtolower((string) ($s->url ?? $s->link ?? ''));

        return str_contains($title, 'whatsapp')
            || $title === 'wa'
            || str_contains($title, 'wa ')
            || str_contains($url, 'wa.me');
    });

    if ($waSosmed) {
        $link = (string) ($waSosmed->url ?? $waSosmed->link ?? '');
        $extracted = preg_replace('/[^0-9]/', '', $link);

        if (!empty($extracted)) {
            if (str_starts_with($extracted, '0')) {
                $extracted = '62' . substr($extracted, 1);
            } elseif (str_starts_with($extracted, '8')) {
                $extracted = '62' . $extracted;
            }

            $waNumber = $extracted;
        }
    }
@endphp

<style>
    /* =====================================================
       JUMALA LIVING COLOR SYSTEM
    ===================================================== */

    :root {
        --jl-brown: #3B2314;
        --jl-brown-dark: #3B2314;
        --jl-gold: #C9A45C;
        --jl-gold-light: #C9A45C;
        --jl-ivory: #FFF8F1;
        --jl-beige: #E9DDD2;
        --jl-muted: #8E7B6D;
    }

    /* =====================================================
       NAVBAR BASE
    ===================================================== */

    .jl-navbar {
        position: sticky;
        top: 0;
        z-index: 999;
        width: 100%;
        font-family: "DM Sans", "Plus Jakarta Sans", sans-serif;
        background: var(--jl-ivory);
        border-bottom: 1px solid var(--jl-beige);
        box-shadow: 0 4px 20px rgba(59,35,20,0.07);
    }

    .jl-navbar-inner {
        max-width: 1280px;
        margin: auto;
        padding: 0 24px;
    }

    .jl-navbar-content {
        min-height: 88px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    /* =====================================================
       BRAND / LOGO
    ===================================================== */

    .jl-brand {
        display: inline-flex;
        align-items: center;
        gap: 13px;
        text-decoration: none;
        flex-shrink: 0;
        transition: transform .25s ease;
    }

    .jl-brand:hover {
        transform: translateY(-1px);
    }

    .jl-logo-wrapper {
        width: 62px;
        height: 62px;
        padding: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--jl-brown);
        border: 1px solid var(--jl-gold);
        border-radius: 13px;
        box-shadow: 0 3px 10px rgba(59,35,20,.13);
    }

    .jl-logo-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        border-radius: 9px;
    }

    .jl-brand-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .jl-brand-name {
        font-family: "Playfair Display", serif;
        font-size: 23px;
        font-weight: 700;
        letter-spacing: .5px;
        color: var(--jl-brown);
        white-space: nowrap;
    }

    .jl-brand-name span {
        color: var(--jl-gold);
    }

    .jl-brand-subtitle {
        margin-top: 5px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: var(--jl-muted);
        white-space: nowrap;
    }

    /* =====================================================
       DESKTOP NAVIGATION
    ===================================================== */

    .jl-desktop-nav {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
    }

    .jl-nav-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 17px;
        border-radius: 9px;
        color: var(--jl-brown);
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .2px;
        transition: all .25s ease;
    }

    .jl-nav-link:hover {
        background: #E9DDD2;
        color: #C9A45C;
    }

    .jl-nav-link.active {
        background: var(--jl-brown);
        color: var(--jl-gold-light);
        box-shadow: 0 3px 9px rgba(59,35,20,.13);
    }

    /* =====================================================
       WHATSAPP BUTTON
    ===================================================== */

    .jl-contact-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 12px 19px;
        margin-left: 8px;
        border: 1px solid var(--jl-gold);
        border-radius: 9px;
        background: var(--jl-brown);
        color: var(--jl-ivory);
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .4px;
        white-space: nowrap;
        box-shadow: 0 4px 10px rgba(59,35,20,.12);
        transition: all .25s ease;
    }

    .jl-contact-btn i {
        color: var(--jl-gold-light);
        font-size: 17px;
    }

    .jl-contact-btn:hover {
        background: var(--jl-brown-dark);
        border-color: var(--jl-gold-light);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(59,35,20,.19);
        color: var(--jl-ivory);
    }

    /* =====================================================
       MOBILE HAMBURGER
    ===================================================== */

    .jl-mobile-toggle {
        display: none;
        width: 43px;
        height: 43px;
        align-items: center;
        justify-content: center;
        background: var(--jl-brown);
        border: 1px solid var(--jl-gold);
        border-radius: 10px;
        color: var(--jl-gold-light);
        font-size: 18px;
        cursor: pointer;
        transition: all .2s ease;
    }

    .jl-mobile-toggle:hover {
        background: var(--jl-brown-dark);
    }

    /* =====================================================
       MOBILE DRAWER
    ===================================================== */

    .jl-mobile-menu {
        display: none;
        padding: 15px 0 20px;
        border-top: 1px solid var(--jl-beige);
    }

    .jl-mobile-menu.open {
        display: block;
        animation: jlMenuAppear .22s ease;
    }

    @keyframes jlMenuAppear {
        from {
            opacity: 0;
            transform: translateY(-7px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .jl-mobile-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 16px;
        margin-bottom: 7px;
        border-radius: 9px;
        color: var(--jl-brown);
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .jl-mobile-link i {
        width: 19px;
        text-align: center;
        color: var(--jl-gold);
        font-size: 15px;
    }

    .jl-mobile-link:hover {
        background: #E9DDD2;
    }

    .jl-mobile-link.active {
        background: var(--jl-brown);
        color: var(--jl-gold-light);
    }

    .jl-mobile-link.active i {
        color: var(--jl-gold-light);
    }

    .jl-mobile-contact {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        margin-top: 12px;
        padding: 14px;
        border: 1px solid var(--jl-gold);
        border-radius: 9px;
        background: var(--jl-brown);
        color: var(--jl-ivory);
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        transition: all .2s ease;
    }

    .jl-mobile-contact i {
        color: var(--jl-gold-light);
        font-size: 17px;
    }

    .jl-mobile-contact:hover {
        background: var(--jl-brown-dark);
        color: var(--jl-ivory);
    }

    /* =====================================================
       RESPONSIVE TABLET & MOBILE
    ===================================================== */

    @media (min-width: 768px) {
        .jl-desktop-nav {
            display: flex;
        }

        .jl-mobile-toggle,
        .jl-mobile-menu {
            display: none !important;
        }
    }

    @media (max-width: 767.98px) {
        .jl-navbar-inner {
            padding: 0 16px;
        }

        .jl-navbar-content {
            min-height: 74px;
            gap: 12px;
        }

        .jl-desktop-nav {
            display: none !important;
        }

        .jl-mobile-toggle {
            display: flex;
            flex-shrink: 0;
        }

        .jl-logo-wrapper {
            width: 53px;
            height: 53px;
            border-radius: 11px;
        }

        .jl-brand {
            gap: 10px;
        }

        .jl-brand-name {
            font-size: 19px;
        }

        .jl-brand-subtitle {
            font-size: 8px;
            letter-spacing: 1.1px;
        }
    }

    @media (max-width: 380px) {
        .jl-navbar-inner {
            padding: 0 12px;
        }

        .jl-logo-wrapper {
            width: 46px;
            height: 46px;
        }

        .jl-brand-name {
            font-size: 17px;
        }

        .jl-brand-subtitle {
            font-size: 7px;
            letter-spacing: .7px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .jl-navbar *,
        .jl-navbar *::before,
        .jl-navbar *::after {
            animation-duration: .01ms !important;
            transition-duration: .01ms !important;
        }
    }

    /* FIX STICKY NAVBAR */
.jl-navbar {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 9999 !important;
    width: 100%;
    align-self: flex-start;
}

/* Clip horizontal overflow without creating a scroll container for sticky. */
body {
    overflow-x: clip;
}
</style>


{{-- =========================================================
     NAVBAR HTML
========================================================= --}}

<header id="jl-navbar-wrapper" class="jl-navbar-wrapper jl-navbar">
    <div class="jl-navbar-inner">

        <div class="jl-navbar-content">

            {{-- BRAND LOGO --}}
            <a href="{{ url('/') }}"
               class="jl-brand"
               title="Jumala Living - Premium Female Residence">

                <div class="jl-logo-wrapper">
                    <img
                        src="{{ asset('logo.jpeg') }}"
                        alt="Jumala Living Logo"
                        loading="eager"
                    >
                </div>

                <div class="jl-brand-info">
                    <span class="jl-brand-name">
                        Jumala <span>Living</span>
                    </span>

                    <span class="jl-brand-subtitle">
                        Premium Female Residence
                    </span>
                </div>
            </a>


            {{-- DESKTOP NAVIGATION --}}
            <nav class="jl-desktop-nav" aria-label="Navigasi utama">

                <a href="{{ url('/') }}"
                   class="jl-nav-link {{ Request::is('/') ? 'active' : '' }}">
                    Beranda
                </a>

                <a href="{{ url('/#tentang') }}" class="jl-nav-link">
                    Tentang Kami
                </a>

                <a href="{{ url('/#pilihan-kamar') }}"
                   class="jl-nav-link {{ Request::is('kosan*') || Request::is('kamar*') ? 'active' : '' }}">
                    Pilihan Kamar
                </a>

                <a href="{{ url('/#fasilitas') }}" class="jl-nav-link">
                    Fasilitas
                </a>

                <a href="{{ url('/#lokasi') }}" class="jl-nav-link">
                    Lokasi
                </a>

                <a href="https://wa.me/{{ $waNumber }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="jl-contact-btn">

                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Hubungi Kami</span>

                </a>

            </nav>


            {{-- MOBILE HAMBURGER --}}
            <button
                type="button"
                id="jl-hamburger"
                class="jl-mobile-toggle"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                aria-controls="jl-mobile-menu"
            >
                <i id="jl-hamburger-icon" class="fa-solid fa-bars"></i>
            </button>

        </div>


        {{-- MOBILE NAVIGATION --}}
        <div id="jl-mobile-menu" class="jl-mobile-menu">

            <a href="{{ url('/') }}"
               class="jl-mobile-link {{ Request::is('/') ? 'active' : '' }}">

                <i class="fa-solid fa-house"></i>
                <span>Beranda</span>

            </a>

            <a href="{{ url('/#tentang') }}" class="jl-mobile-link">
                <i class="fa-solid fa-circle-info"></i>
                <span>Tentang Kami</span>
            </a>

            <a href="{{ url('/#pilihan-kamar') }}"
               class="jl-mobile-link {{ Request::is('kosan*') || Request::is('kamar*') ? 'active' : '' }}">

                <i class="fa-solid fa-building"></i>
                <span>Pilihan Kamar</span>

            </a>

            <a href="{{ url('/#fasilitas') }}" class="jl-mobile-link">
                <i class="fa-solid fa-list-check"></i>
                <span>Fasilitas</span>
            </a>

            <a href="{{ url('/#lokasi') }}" class="jl-mobile-link">
                <i class="fa-solid fa-location-dot"></i>
                <span>Lokasi</span>
            </a>

            <a href="https://wa.me/{{ $waNumber }}"
               target="_blank"
               rel="noopener noreferrer"
               class="jl-mobile-contact">

                <i class="fa-brands fa-whatsapp"></i>
                <span>Hubungi Kami</span>

            </a>

        </div>

    </div>
</header>


{{-- =========================================================
     MOBILE NAVIGATION SCRIPT
========================================================= --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const button = document.getElementById('jl-hamburger');
        const menu = document.getElementById('jl-mobile-menu');
        const icon = document.getElementById('jl-hamburger-icon');

        if (!button || !menu || !icon) return;

        function openMenu() {
            menu.classList.add('open');
            button.setAttribute('aria-expanded', 'true');
            button.setAttribute('aria-label', 'Tutup menu navigasi');

            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        }

        function closeMenu() {
            menu.classList.remove('open');
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Buka menu navigasi');

            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }

        button.addEventListener('click', function () {
            const isOpen = menu.classList.contains('open');

            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (
                menu.classList.contains('open') &&
                !menu.contains(event.target) &&
                !button.contains(event.target)
            ) {
                closeMenu();
            }
        });

        // Close menu using Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });

        // Close menu when navigating to desktop viewport
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                closeMenu();
            }
        });

        // Close menu after clicking navigation links
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

    });
</script>