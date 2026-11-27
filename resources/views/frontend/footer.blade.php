@php
    $footerSocialLinks = collect($globalSosmed ?? [])
        ->filter(fn ($sm) => filled($sm->url ?? $sm->link ?? null))
        ->values();
@endphp

<footer class="jl-footer">

    <div class="jl-footer-container">

        {{-- MAIN FOOTER CONTENT --}}
        <div class="jl-footer-main">

            {{-- BRAND IDENTITY --}}
            <a
                href="{{ url('/') }}"
                aria-label="Beranda Jumala Living"
                class="jl-footer-brand"
            >

                <div class="jl-footer-logo">
                    <img
                        src="{{ asset('logo.jpeg') }}"
                        alt="Jumala Living Logo"
                        loading="lazy"
                    >
                </div>

                <div class="jl-footer-brand-info">
                    <span class="jl-footer-brand-name">
                        JUMALA <span>LIVING</span>
                    </span>

                    <span class="jl-footer-brand-subtitle">
                        PREMIUM FEMALE RESIDENCE
                    </span>

                    <span class="jl-footer-brand-description">
                        A Comfortable Place to Call Home
                    </span>
                </div>

            </a>

            <div class="jl-footer-brand-info" style="max-width:330px;">
                <span class="jl-footer-brand-description">
                    Hunian kos khusus putri di Lowokwaru, Malang.
                </span>
                <span class="jl-footer-brand-description" style="margin-top:8px;">
                    Jl. Kenanga No. 3, Lowokwaru, Malang, Jawa Timur 65141
                </span>
                <div style="display:flex;gap:14px;margin-top:12px;font-size:12px;">
                    <a href="{{ url('/#tentang') }}" style="color:inherit;">Tentang</a>
                    <a href="{{ url('/#pilihan-kamar') }}" style="color:inherit;">Pilihan Kamar</a>
                    <a href="{{ url('/#lokasi') }}" style="color:inherit;">Lokasi</a>
                </div>
            </div>


            {{-- SOCIAL MEDIA --}}
            <div class="jl-footer-social">

                <div class="jl-footer-social-heading">
                    <span class="jl-footer-social-title">
                        Ikuti Kami
                    </span>

                    <span class="jl-footer-social-subtitle">
                        Connect With Us
                    </span>
                </div>

                <div class="jl-footer-social-list">

                    @php
                        $sosmedIconMap = [
                            'instagram' => 'fa-instagram',
                            'ig'        => 'fa-instagram',
                            'facebook'  => 'fa-facebook-f',
                            'fb'        => 'fa-facebook-f',
                            'twitter'   => 'fa-twitter',
                            'x'         => 'fa-x-twitter',
                            'youtube'   => 'fa-youtube',
                            'tiktok'    => 'fa-tiktok',
                            'linkedin'  => 'fa-linkedin-in',
                            'whatsapp'  => 'fa-whatsapp',
                            'wa'        => 'fa-whatsapp',
                            'telegram'  => 'fa-telegram',
                            'threads'   => 'fa-threads',
                            'discord'   => 'fa-discord',
                            'pinterest' => 'fa-pinterest',
                        ];
                    @endphp

                    @if($footerSocialLinks->isNotEmpty())

                        @foreach($footerSocialLinks as $sm)

                            @php
                                $title = (string) ($sm->title ?? $sm->name ?? $sm->platform ?? 'Situs web');
                                $titleLower = strtolower(trim($title));

                                $iconClass = 'fa-globe';
                                $iconPrefix = 'fa-solid';

                                foreach ($sosmedIconMap as $key => $icon) {
                                    if (str_contains($titleLower, $key)) {
                                        $iconClass = $icon;
                                        $iconPrefix = 'fa-brands';
                                        break;
                                    }
                                }
                            @endphp

                            <a
                                href="{{ $sm->url ?? $sm->link ?? '#' }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $title }}"
                                title="{{ $title }}"
                                class="jl-social-link"
                            >
                                <i class="{{ $iconPrefix }} {{ $iconClass }}"></i>
                            </a>

                        @endforeach

                    @else
                        <span style="font-size:12px;color:var(--jl-footer-muted);">Belum ada tautan sosial</span>
                    @endif

                </div>

            </div>

        </div>


        {{-- DECORATIVE DIVIDER --}}
        <div class="jl-footer-divider">
            <span></span>
            <i class="fa-solid fa-star"></i>
            <span></span>
        </div>


        {{-- COPYRIGHT --}}
        <div class="jl-footer-bottom">

            <p class="jl-footer-copyright">
                &copy; {{ date('Y') }}
                <span>Jumala Living</span>.
                Seluruh hak cipta dilindungi.
            </p>

            <p class="jl-footer-made">
                Made with <i class="fa-solid fa-heart"></i>
                for your comfort.
            </p>

        </div>

    </div>

</footer>


{{-- =========================================================
     FOOTER STYLING
========================================================= --}}

<style>

    :root {
        --jl-footer-brown: #3B2314;
        --jl-footer-brown-light: #3B2314;
        --jl-footer-gold: #C9A45C;
        --jl-footer-gold-light: #C9A45C;
        --jl-footer-ivory: #FFF8F1;
        --jl-footer-muted: #8E7B6D;
        --jl-footer-border: rgba(201,164,92,.25);
    }


    /* FOOTER BASE */

    .jl-footer {
        position: relative;
        width: 100%;
        font-family: "DM Sans", "Plus Jakarta Sans", sans-serif;
        background: var(--jl-footer-brown);
        color: var(--jl-footer-ivory);
        border-top: 1px solid var(--jl-footer-border);
    }

    .jl-footer-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 35px 24px 19px;
    }


    /* MAIN CONTENT */

    .jl-footer-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
    }


    /* BRAND */

    .jl-footer-brand {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        transition: transform .25s ease;
    }

    .jl-footer-brand:hover {
        transform: translateY(-2px);
    }

    .jl-footer-logo {
        width: 68px;
        height: 68px;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        background: var(--jl-footer-ivory);
        border: 1px solid var(--jl-footer-gold);
        border-radius: 13px;
        box-shadow: 0 4px 14px rgba(59,35,20,.2);
    }

    .jl-footer-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
    }

    .jl-footer-brand-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .jl-footer-brand-name {
        font-family: "Playfair Display", serif;
        font-size: 23px;
        font-weight: 700;
        letter-spacing: 1.3px;
        color: var(--jl-footer-ivory);
    }

    .jl-footer-brand-name span {
        color: var(--jl-footer-gold);
    }

    .jl-footer-brand-subtitle {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.7px;
        color: var(--jl-footer-gold-light);
    }

    .jl-footer-brand-description {
        margin-top: 3px;
        font-size: 10px;
        letter-spacing: .3px;
        color: var(--jl-footer-muted);
    }


    /* SOCIAL MEDIA */

    .jl-footer-social {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .jl-footer-social-heading {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 3px;
    }

    .jl-footer-social-title {
        color: var(--jl-footer-gold-light);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .jl-footer-social-subtitle {
        color: var(--jl-footer-muted);
        font-size: 10px;
        letter-spacing: .5px;
    }

    .jl-footer-social-list {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-wrap: wrap;
    }

    .jl-social-link {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--jl-footer-border);
        border-radius: 11px;
        background: rgba(255,248,241,.05);
        color: var(--jl-footer-gold-light);
        font-size: 16px;
        text-decoration: none;
        transition: all .25s ease;
    }

    .jl-social-link:hover {
        background: var(--jl-footer-gold);
        border-color: var(--jl-footer-gold);
        color: var(--jl-footer-brown);
        transform: translateY(-4px);
        box-shadow: 0 5px 14px rgba(201,164,92,.18);
    }

    .jl-social-link:active {
        transform: scale(.94);
    }


    /* DIVIDER */

    .jl-footer-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 13px;
        margin: 28px 0 19px;
    }

    .jl-footer-divider span {
        width: 65px;
        height: 1px;
        background: var(--jl-footer-border);
    }

    .jl-footer-divider i {
        color: var(--jl-footer-gold);
        font-size: 9px;
    }


    /* COPYRIGHT */

    .jl-footer-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .jl-footer-copyright {
        margin: 0;
        font-size: 11px;
        color: var(--jl-footer-muted);
        line-height: 1.7;
    }

    .jl-footer-copyright span {
        color: var(--jl-footer-gold-light);
        font-weight: 800;
    }

    .jl-footer-made {
        margin: 0;
        font-size: 10px;
        color: var(--jl-footer-muted);
        letter-spacing: .3px;
    }

    .jl-footer-made i {
        color: var(--jl-footer-gold);
        font-size: 10px;
        margin: 0 2px;
    }


    /* RESPONSIVE */

    @media (max-width: 767.98px) {

        .jl-footer-container {
            padding: 30px 18px 18px;
        }

        .jl-footer-main {
            flex-direction: column;
            align-items: center;
            gap: 28px;
        }

        .jl-footer-brand {
            flex-direction: column;
            text-align: center;
            gap: 11px;
        }

        .jl-footer-logo {
            width: 76px;
            height: 76px;
        }

        .jl-footer-brand-name {
            font-size: 22px;
        }

        .jl-footer-brand-subtitle {
            font-size: 8px;
            letter-spacing: 1.5px;
        }

        .jl-footer-social {
            align-items: center;
        }

        .jl-footer-social-heading {
            align-items: center;
        }

        .jl-footer-social-list {
            justify-content: center;
        }

        .jl-footer-divider {
            margin: 25px 0 17px;
        }

        .jl-footer-bottom {
            flex-direction: column;
            text-align: center;
            gap: 7px;
        }

        .jl-footer-copyright {
            font-size: 10px;
        }

    }


    @media (max-width: 380px) {

        .jl-footer-container {
            padding-left: 14px;
            padding-right: 14px;
        }

        .jl-footer-logo {
            width: 70px;
            height: 70px;
        }

        .jl-footer-brand-name {
            font-size: 20px;
        }

        .jl-social-link {
            width: 39px;
            height: 39px;
        }

    }


    /* ACCESSIBILITY */

    @media (prefers-reduced-motion: reduce) {

        .jl-footer *,
        .jl-footer *::before,
        .jl-footer *::after {
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
        }

    }

</style>