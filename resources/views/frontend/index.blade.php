
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3B2314">

    <title>Jumala Living | Premium Female Residence Malang</title>

    <meta name="description" content="Jumala Living merupakan hunian kos khusus putri di Lowokwaru, Malang, dengan pilihan kamar dan fasilitas untuk kenyamanan tinggal.">
    <meta name="keywords" content="kos putri Malang, hunian putri Lowokwaru, Jumala Living, kost Malang">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Jumala Living | Premium Female Residence Malang">
    <meta property="og:description" content="Hunian kos khusus putri di Lowokwaru, Malang.">
    <meta property="og:image" content="{{ asset('logo.jpeg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="{{ asset('logo.jpeg') }}">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --jl-brown: #3B2314;
            --jl-gold: #C9A45C;
            --jl-ivory: #FFF8F1;
            --jl-rose: #D99AA8;

            --jl-white: #FFFCF8;
            --jl-muted: #8E7B6D;
            --jl-border: #E9DDD2;

            --jl-serif: "Playfair Display", serif;
            --jl-sans: "DM Sans", sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        body {
            background: var(--jl-ivory);
            color: var(--jl-brown);
            font-family: var(--jl-sans);
        }

        ::selection {
            background: var(--jl-rose);
            color: var(--jl-brown);
        }

        .jl-serif {
            font-family: var(--jl-serif);
        }

        .jl-sans {
            font-family: var(--jl-sans);
        }

        /* NAVBAR */

        .jl-navbar-wrapper {
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
        }

        .jl-navbar-wrapper nav {
            transition: box-shadow .3s ease, background .3s ease;
        }

        .jl-navbar-wrapper.scrolled nav {
            box-shadow: 0 8px 25px rgba(59,35,20,.12);
        }

        /* HERO */

        .jl-hero {
            position: relative;
            isolation: isolate;
            overflow: clip;
            background: var(--jl-brown);
        }

        .jl-hero::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border: 1px solid rgba(201,164,92,.25);
            border-radius: 50%;
            top: -180px;
            right: -100px;
            z-index: -1;
        }

        .jl-hero::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border: 1px solid rgba(217,154,168,.2);
            border-radius: 50%;
            bottom: -180px;
            left: -100px;
            z-index: -1;
        }

        .jl-hero-title {
            letter-spacing: -.045em;
            line-height: 1.12;
        }

        .jl-hero-image {
            position: relative;
            overflow: clip;
            border-radius: 180px 180px 24px 24px;
            border: 5px solid rgba(201,164,92,.65);
            box-shadow: 0 25px 65px rgba(59,35,20,.25);
        }

        .jl-hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .7s ease;
        }

        .jl-hero-image:hover img {
            transform: scale(1.04);
        }

        .jl-hero-label {
            backdrop-filter: blur(12px);
            background: rgba(255,248,241,.94);
        }

        /* GENERAL */

        .jl-reveal {
            opacity: 0;
            transform: translateY(25px);
            transition: opacity .65s ease, transform .65s ease;
        }

        .jl-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .jl-card {
            transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
        }

        .jl-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 38px rgba(59,35,20,.10);
            border-color: var(--jl-gold);
        }

        .jl-button {
            transition: all .25s ease;
        }

        .jl-button:hover {
            transform: translateY(-2px);
        }

        .jl-section-title {
            letter-spacing: -.035em;
            line-height: 1.2;
        }

        .jl-accent-line {
            width: 45px;
            height: 2px;
            background: var(--jl-gold);
        }

        .jl-facility-icon {
            color: var(--jl-gold);
        }

        .jl-room-image img {
            transition: transform .5s ease;
        }

        .jl-room-image:hover img {
            transform: scale(1.06);
        }

        .jl-link {
            transition: color .2s ease;
        }

        .jl-link:hover {
            color: var(--jl-gold);
        }

        /* FILTER */

        .jl-filter {
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .jl-filter:focus {
            border-color: var(--jl-gold);
            box-shadow: 0 0 0 3px rgba(201,164,92,.15);
        }

        /* RESPONSIVE */

        @media (max-width: 768px) {
            html {
                scroll-padding-top: 80px;
            }

            .jl-hero-image {
                max-width: 340px;
                margin: 0 auto;
                border-radius: 150px 150px 20px 20px;
            }

            .jl-hero-title {
                letter-spacing: -.035em;
            }
        }

        @media (max-width: 480px) {
            .jl-hero-title {
                font-size: 2.35rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }

            .jl-reveal {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>

<body class="jl-sans antialiased overflow-x-clip min-h-screen flex flex-col">

    {{-- SUCCESS NOTIFICATION --}}
    @if (session('success'))
        <div id="flash-success-modal" class="fixed inset-0 bg-[#3B2314]/75 z-[999] flex items-center justify-center p-4">
            <div class="bg-[#FFF8F1] w-full max-w-md rounded-3xl p-7 shadow-2xl relative border border-[#C9A45C]">
                <button type="button"
                    onclick="this.closest('#flash-success-modal').remove()"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full border border-[#E9DDD2]"
                    aria-label="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center">
                    <i class="fa-solid fa-circle-check text-4xl text-[#C9A45C]"></i>
                    <h3 class="jl-serif text-2xl mt-3">Booking Berhasil</h3>
                    <p class="text-sm text-[#8E7B6D] mt-2">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- FAILED NOTIFICATION --}}
    @if (session('failed'))
        <div id="flash-failed-modal" class="fixed inset-0 bg-[#3B2314]/75 z-[999] flex items-center justify-center p-4">
            <div class="bg-[#FFF8F1] w-full max-w-md rounded-3xl p-7 shadow-2xl relative border border-[#D99AA8]">
                <button type="button"
                    onclick="this.closest('#flash-failed-modal').remove()"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full border border-[#E9DDD2]"
                    aria-label="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center">
                    <i class="fa-solid fa-circle-exclamation text-4xl text-[#D99AA8]"></i>
                    <h3 class="jl-serif text-2xl mt-3">Pemesanan Belum Berhasil</h3>
                    <p class="text-sm text-[#8E7B6D] mt-2">{{ session('failed') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- STICKY NAVBAR --}}
        @include('frontend.navbar')

    @php
            $kamarList = collect($kamarList ?? []);
            $kosanList = collect($kosanList ?? []);
            $testimonis = collect($testimonis ?? []);
            $property = $kosanList->first();

        $propertyImage = null;

            $propertyImageModel = $property?->productImageKosan?->first();
            $propertyImagePath = trim((string) ($propertyImageModel?->image ?? ''));
            if ($propertyImagePath !== '') {
                $propertyImage = asset('storage/' . $propertyImagePath);
        }

        $heroImage = $propertyImage ?: asset('logo.jpeg');

        $waNumber = '6282146138847';

        $waSosmed = collect($globalSosmed ?? [])->first(function ($s) {
            $title = strtolower((string) ($s->title ?? $s->name ?? $s->platform ?? ''));
            $url = strtolower((string) ($s->url ?? $s->link ?? ''));

            return str_contains($title, 'whatsapp')
                || $title === 'wa'
                || str_contains($url, 'wa.me');
        });

        if ($waSosmed) {
            $digits = preg_replace('/[^0-9]/', '', (string) ($waSosmed->url ?? $waSosmed->link ?? ''));

            if ($digits) {
                if (str_starts_with($digits, '0')) {
                    $digits = '62' . substr($digits, 1);
                } elseif (str_starts_with($digits, '8')) {
                    $digits = '62' . $digits;
                }

                $waNumber = $digits;
            }
        }

        $waUrl = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode(
            'Halo Admin Jumala Living, saya tertarik untuk menanyakan pilihan kamar dan reservasi.'
        );

        $propertyMapsUrl = (string) ($property?->gmaps ?? '');
        $mapsUrl = ($propertyMapsUrl !== '' && filter_var($propertyMapsUrl, FILTER_VALIDATE_URL))
            ? $propertyMapsUrl
            : ('https://www.google.com/maps/search/?api=1&query=' . rawurlencode(
                'Jumala Living Jl. Kenanga No. 3, Lowokwaru, Malang, Jawa Timur 65141'
            ));

        $roomFacilities = collect();
        $propertyFacilities = collect();

        if ($property) {
            $propertyFacilities = collect(
                array_filter(array_map('trim', explode(',', (string) ($property->fasilitas ?? ''))))
            );
        }

        foreach ($kamarList as $room) {
            $roomFacilities = $roomFacilities->merge(
                array_filter(array_map('trim', explode(',', (string) ($room->fasilitas ?? ''))))
            );
        }

        $roomFacilities = $roomFacilities->filter()->unique()->take(12);
        $propertyFacilities = $propertyFacilities->filter()->unique()->take(12);

        $getFacilityIcon = function ($facility) {
            $value = strtolower($facility);

            if (str_contains($value, 'ac')) return 'fa-snowflake';
            if (str_contains($value, 'mandi') || str_contains($value, 'toilet')) return 'fa-bath';
            if (str_contains($value, 'wifi') || str_contains($value, 'internet')) return 'fa-wifi';
            if (str_contains($value, 'kasur') || str_contains($value, 'bed')) return 'fa-bed';
            if (str_contains($value, 'lemari')) return 'fa-door-closed';
            if (str_contains($value, 'meja') || str_contains($value, 'kursi')) return 'fa-chair';
            if (str_contains($value, 'dapur') || str_contains($value, 'kitchen')) return 'fa-kitchen-set';
            if (str_contains($value, 'parkir')) return 'fa-car';
            if (str_contains($value, 'laundry') || str_contains($value, 'cuci')) return 'fa-soap';

            return 'fa-check';
        };
    @endphp

    <main class="flex-grow">

        {{-- =====================================================
             HERO SECTION
        ====================================================== --}}
        <section class="jl-hero text-[#FFF8F1]">

            <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24">

                <div class="grid lg:grid-cols-[1.1fr_.9fr] gap-12 lg:gap-16 items-center">

                    {{-- HERO TEXT --}}
                    <div class="relative z-10 text-center lg:text-left">

                        <div class="inline-flex items-center gap-3 text-[#C9A45C] text-[10px] sm:text-xs font-bold uppercase tracking-[.22em] mb-6">
                            <span class="jl-accent-line"></span>
                            Exclusive Female Residence
                            <span class="jl-accent-line"></span>
                        </div>

                        <h1 class="jl-serif jl-hero-title text-4xl sm:text-5xl lg:text-[64px] font-semibold max-w-3xl mx-auto lg:mx-0">
                            Ruang Nyaman,
                            <span class="block text-[#C9A45C] italic mt-2">
                                Cerita Istimewa.
                            </span>
                        </h1>

                        <p class="mt-7 max-w-xl mx-auto lg:mx-0 text-sm sm:text-base leading-8 text-[#FFF8F1]/75">
                            Lebih dari sekadar tempat tinggal. Temukan hunian khusus putri yang menghadirkan kenyamanan, privasi, dan suasana hangat untuk setiap perjalanan hidupmu.
                        </p>

                        <div class="flex flex-wrap justify-center lg:justify-start gap-x-6 gap-y-3 mt-7 text-xs sm:text-sm text-[#FFF8F1]/85">

                            <span>
                                <i class="fa-solid fa-female text-[#C9A45C] mr-2"></i>
                                Khusus Perempuan
                            </span>

                            <span>
                                <i class="fa-solid fa-location-dot text-[#C9A45C] mr-2"></i>
                                Lowokwaru, Malang
                            </span>

                            <span>
                                <i class="fa-solid fa-door-open text-[#C9A45C] mr-2"></i>
                                Berbagai Tipe Kamar
                            </span>

                        </div>

                        <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-3 mt-9">

                            <a href="#pilihan-kamar"
                                class="jl-button inline-flex items-center justify-center gap-3 px-7 py-4 rounded-xl bg-[#C9A45C] text-[#3B2314] text-sm font-bold hover:bg-[#D99AA8]">

                                Jelajahi Pilihan Kamar
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>

                            <a href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="jl-button inline-flex items-center justify-center gap-3 px-7 py-4 rounded-xl border border-[#C9A45C]/60 text-[#FFF8F1] text-sm font-bold hover:bg-[#FFF8F1]/10">

                                <i class="fa-brands fa-whatsapp text-[#C9A45C]"></i>
                                Konsultasi WhatsApp
                            </a>

                        </div>

                        <div class="flex items-center justify-center lg:justify-start gap-4 mt-10 pt-7 border-t border-[#FFF8F1]/15">

                            <div class="flex -space-x-2">
                                <span class="w-9 h-9 rounded-full bg-[#C9A45C] border-2 border-[#3B2314] flex items-center justify-center text-[#3B2314]">
                                    <i class="fa-solid fa-house text-xs"></i>
                                </span>

                                <span class="w-9 h-9 rounded-full bg-[#D99AA8] border-2 border-[#3B2314] flex items-center justify-center text-[#3B2314]">
                                    <i class="fa-solid fa-heart text-xs"></i>
                                </span>

                                <span class="w-9 h-9 rounded-full bg-[#FFF8F1] border-2 border-[#3B2314] flex items-center justify-center text-[#3B2314]">
                                    <i class="fa-solid fa-star text-xs"></i>
                                </span>
                            </div>

                            <p class="text-xs sm:text-sm text-[#FFF8F1]/65">
                                Hunian yang dirancang untuk kenyamanan sehari-hari.
                            </p>

                        </div>

                    </div>

                    {{-- HERO IMAGE --}}
                    <div class="relative max-w-md mx-auto w-full">

                        <div class="jl-hero-image aspect-[4/5] max-h-[550px]">

                            <img
                                src="{{ $heroImage }}"
                                alt="{{ $property?->title ?? 'Jumala Living' }}"
                                fetchpriority="high">

                        </div>

                        <div class="jl-hero-label absolute -bottom-5 -left-4 sm:-left-8 rounded-2xl p-4 sm:p-5 shadow-xl border border-[#C9A45C]/40 max-w-[230px]">

                            <div class="flex items-center gap-3">

                                <div class="w-11 h-11 rounded-xl bg-[#D99AA8]/30 flex items-center justify-center text-[#3B2314]">
                                    <i class="fa-solid fa-house text-lg"></i>
                                </div>

                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-[#8E7B6D] font-bold">
                                        Your New Space
                                    </p>

                                    <p class="text-sm font-bold text-[#3B2314]">
                                        Jumala Living
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div class="absolute -top-5 -right-3 sm:-right-8 bg-[#D99AA8] text-[#3B2314] rounded-full w-20 h-20 sm:w-24 sm:h-24 flex flex-col items-center justify-center shadow-lg rotate-12">

                            <i class="fa-solid fa-heart text-lg mb-1"></i>

                            <span class="text-[9px] font-bold uppercase tracking-wider">
                                Feel at Home
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             QUICK SEARCH
        ====================================================== --}}
        <section class="relative z-20 max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 -mt-7">

            <form action="{{ route('home') }}#pilihan-kamar"
                method="GET"
                class="bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl shadow-lg p-4 sm:p-5">

                <div class="grid grid-cols-1 md:grid-cols-[minmax(180px,.8fr)_minmax(220px,1.2fr)_auto] gap-3">

                    <div class="rounded-xl border border-[#E9DDD2] px-4 py-3">
                        <label for="hero-price" class="block text-[10px] uppercase tracking-wider font-bold text-[#8E7B6D]">
                            Budget Bulanan
                        </label>

                        <select id="hero-price"
                            name="price_range"
                            class="jl-filter w-full mt-1 bg-transparent text-sm font-semibold">

                            <option value="">Semua Budget</option>
                            <option value="under-500" @selected(request('price_range') === 'under-500')>Di bawah Rp500.000</option>
                            <option value="500-1000" @selected(request('price_range') === '500-1000')>Rp500.000–Rp1.000.000</option>
                            <option value="1000-1500" @selected(request('price_range') === '1000-1500')>Rp1.000.000–Rp1.500.000</option>
                            <option value="1500-2500" @selected(request('price_range') === '1500-2500')>Rp1.500.000–Rp2.500.000</option>
                            <option value="over-2500" @selected(request('price_range') === 'over-2500')>Di atas Rp2.500.000</option>

                        </select>
                    </div>

                    <div class="rounded-xl border border-[#E9DDD2] px-4 py-3">

                        <label for="hero-search" class="block text-[10px] uppercase tracking-wider font-bold text-[#8E7B6D]">
                            Nama Kamar atau Fasilitas
                        </label>

                        <input id="hero-search"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="Contoh: AC, WiFi, kamar mandi"
                            class="jl-filter w-full mt-1 bg-transparent text-sm font-semibold placeholder:text-[#8E7B6D]">

                    </div>

                    <button type="submit"
                        class="jl-button min-h-14 px-6 rounded-xl bg-[#3B2314] text-[#FFF8F1] font-bold text-sm hover:bg-[#C9A45C] hover:text-[#3B2314]">

                        <i class="fa-solid fa-magnifying-glass mr-2 text-[#C9A45C]"></i>
                        Cari Kamar

                    </button>

                </div>

                <div class="flex flex-wrap items-center gap-2 mt-4 px-1">

                    <span class="text-xs text-[#8E7B6D] mr-1">
                        Pencarian cepat:
                    </span>

                    @foreach(['AC', 'WiFi', 'Kamar Mandi', 'Lemari', 'Kasur'] as $tag)

                        <button type="button"
                            data-search-tag="{{ $tag }}"
                            class="jl-button px-3 py-2 rounded-full border border-[#E9DDD2] text-xs font-semibold hover:border-[#C9A45C] hover:bg-[#D99AA8]/20">

                            {{ $tag }}

                        </button>

                    @endforeach

                </div>

            </form>

        </section>


        {{-- =====================================================
             TRUST
        ====================================================== --}}
        <section class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-12 jl-reveal">

            <div class="grid grid-cols-2 lg:grid-cols-4 border-y border-[#E9DDD2] divide-x divide-[#E9DDD2]">

                @foreach([
                    ['fa-female', 'Hunian Khusus Putri'],
                    ['fa-door-open', 'Berbagai Tipe Kamar'],
                    ['fa-map-location-dot', 'Lokasi Strategis'],
                    ['fa-calendar-check', 'Reservasi Online']
                ] as $item)

                    <div class="px-3 sm:px-6 py-6 text-center group">

                        <i class="fa-solid {{ $item[0] }} text-[#C9A45C] text-xl mb-3 transition-transform group-hover:scale-110"></i>

                        <p class="text-xs sm:text-sm font-bold">
                            {{ $item[1] }}
                        </p>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- =====================================================
             ABOUT
        ====================================================== --}}
        <section id="tentang"
            class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-10 sm:py-16 jl-reveal">

            <div class="grid lg:grid-cols-[1fr_.92fr] gap-10 lg:gap-16 items-center">

                <div class="relative min-h-[340px] sm:min-h-[430px] rounded-[1.5rem] overflow-x-clip bg-[#D99AA8]/20 border border-[#E9DDD2]">

                    <img
                        src="{{ $heroImage }}"
                        alt="{{ $property?->title ?? 'Jumala Living' }}"
                        loading="lazy"
                        class="absolute inset-0 w-full h-full object-cover">

                    <div class="absolute bottom-5 left-5 right-5 bg-[#FFFCF8]/95 backdrop-blur-sm rounded-xl p-4 border border-[#FFF8F1]/70">

                        <p class="text-[10px] uppercase tracking-[.18em] text-[#8E7B6D] font-bold">
                            Lokasi Hunian
                        </p>

                        <p class="text-sm font-bold mt-1">
                            Jl. Kenanga No. 3, Lowokwaru, Malang
                        </p>

                    </div>

                </div>

                <div>

                    <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                        <span class="jl-accent-line"></span>
                        Tentang Jumala Living
                    </div>

                    <h2 class="jl-serif jl-section-title text-3xl sm:text-5xl mt-4">
                        Lebih dari Sekadar
                        <span class="text-[#C9A45C] italic">Tempat Tinggal.</span>
                    </h2>

                    <p class="mt-6 text-sm sm:text-base leading-8 text-[#8E7B6D]">
                        Jumala Living merupakan hunian kos khusus putri yang menghadirkan lingkungan nyaman, privat, dan mendukung aktivitas sehari-hari.
                    </p>

                    <p class="mt-4 text-sm sm:text-base leading-8 text-[#8E7B6D]">
                        Dengan pilihan kamar dan fasilitas yang beragam, kami ingin memberikan pengalaman tinggal yang lebih menyenangkan.
                    </p>

                    <div class="mt-6 p-4 rounded-xl bg-[#D99AA8]/15 border-l-4 border-[#D99AA8]">

                        <p class="text-sm font-semibold leading-7">
                            <i class="fa-solid fa-location-dot text-[#C9A45C] mr-2"></i>
                            Jumala Living Jl. Kenanga No. 3, Lowokwaru, Kec. Lowokwaru, Malang, Jawa Timur 65141.
                        </p>

                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 mt-7">

                        <a href="{{ $mapsUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jl-button inline-flex justify-center items-center gap-2 px-5 py-3.5 rounded-xl border border-[#3B2314] text-sm font-bold hover:bg-[#3B2314] hover:text-[#FFF8F1]">

                            <i class="fa-solid fa-map-location-dot"></i>
                            Lihat Lokasi

                        </a>

                        <a href="{{ $waUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jl-button inline-flex justify-center items-center gap-2 px-5 py-3.5 rounded-xl bg-[#3B2314] text-[#FFF8F1] text-sm font-bold hover:bg-[#C9A45C] hover:text-[#3B2314]">

                            <i class="fa-brands fa-whatsapp text-[#C9A45C]"></i>
                            Konsultasi WhatsApp

                        </a>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             WHY CHOOSE US
        ====================================================== --}}
        <section id="fasilitas" class="bg-[#FFFCF8] border-y border-[#E9DDD2] py-16 sm:py-20">

            <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 jl-reveal">

                <div class="max-w-2xl">

                    <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                        <span class="jl-accent-line"></span>
                        Keunggulan Hunian
                    </div>

                    <h2 class="jl-serif jl-section-title text-3xl sm:text-4xl mt-4">
                        Kenapa Memilih
                        <span class="text-[#C9A45C]">Jumala Living?</span>
                    </h2>

                    <p class="text-sm leading-7 text-[#8E7B6D] mt-4">
                        Kami menghadirkan lingkungan hunian yang dirancang untuk menunjang kenyamanan dan kebutuhan sehari-hari.
                    </p>

                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-10">

                    @foreach([
                        ['fa-female', 'Hunian Khusus Putri', 'Lingkungan hunian yang dikhususkan untuk penghuni perempuan.'],
                        ['fa-lock', 'Kenyamanan dan Privasi', 'Hunian yang dirancang untuk memberi suasana tinggal yang lebih tenang dan privat.'],
                        ['fa-door-open', 'Pilihan Kamar Beragam', 'Beragam tipe dan ukuran kamar dengan harga serta fasilitas yang dapat dipilih.'],
                        ['fa-couch', 'Fasilitas Penunjang', 'Fasilitas kamar dan bersama ditampilkan sesuai data hunian yang tersedia.'],
                        ['fa-map-location-dot', 'Lokasi Strategis', 'Berada di Lowokwaru, Malang, dekat lingkungan aktivitas sehari-hari.'],
                        ['fa-calendar-check', 'Reservasi Praktis', 'Pilih kamar, tentukan jadwal check-in, dan ikuti proses reservasi online.']
                    ] as $feature)

                        <article class="jl-card bg-[#FFF8F1] border border-[#E9DDD2] rounded-2xl p-6">

                            <div class="w-12 h-12 rounded-xl bg-[#C9A45C]/15 flex items-center justify-center">

                                <i class="fa-solid {{ $feature[0] }} text-[#C9A45C] text-xl"></i>

                            </div>

                            <h3 class="jl-serif text-xl mt-5">
                                {{ $feature[1] }}
                            </h3>

                            <p class="text-sm leading-7 text-[#8E7B6D] mt-3">
                                {{ $feature[2] }}
                            </p>

                        </article>

                    @endforeach

                </div>

            </div>

        </section>


        {{-- =====================================================
             ROOM COLLECTION
        ====================================================== --}}
        <section id="pilihan-kamar"
            class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-16 sm:py-20 jl-reveal">

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-9">

                <div>

                    <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                        <span class="jl-accent-line"></span>
                        Pilihan Kamar
                    </div>

                    <h2 class="jl-serif jl-section-title text-3xl sm:text-4xl mt-4">
                        Temukan Ruangan
                        <span class="text-[#C9A45C] italic">yang Tepat Untukmu.</span>
                    </h2>

                    <p class="text-sm text-[#8E7B6D] mt-3">
                        Pilih kamar sesuai kebutuhan dan kenyamananmu.
                    </p>

                </div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-2">

                    <label for="availability-filter" class="sr-only">
                        Filter ketersediaan
                    </label>

                    <select id="availability-filter"
                        class="jl-filter rounded-xl border border-[#E9DDD2] bg-[#FFFCF8] px-4 py-3 text-sm font-semibold">

                        <option value="semua">Semua Status</option>
                        <option value="kosong">Kosong</option>
                        <option value="pending">Pending</option>
                        <option value="terisi">Terisi</option>

                    </select>

                    <label for="room-sort" class="sr-only">
                        Urutkan kamar
                    </label>

                    <select id="room-sort"
                        class="jl-filter rounded-xl border border-[#E9DDD2] bg-[#FFFCF8] px-4 py-3 text-sm font-semibold">

                        <option value="default">Urutan Default</option>
                        <option value="price-asc">Harga Terendah</option>
                        <option value="price-desc">Harga Tertinggi</option>

                    </select>

                </div>

            </div>

            @if($kamarList->count() > 0)

                <div id="room-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

                    @foreach($kamarList as $kamar)

                        @php
                            $firstImage = $kamar->productKamarImageKosan?->first();
                            $propertyImage = $kamar->productKosan?->productImageKosan?->first();
                            $roomImagePath = trim((string) ($firstImage?->image ?? $propertyImage?->image ?? ''));
                            $roomImage = $roomImagePath !== ''
                                ? asset('storage/' . $roomImagePath)
                                : asset('logo.jpeg');

                            $monthly = $kamar->priceKamar->first(
                                fn($price) => strtolower((string) ($price->kategori ?? '')) === 'bulan'
                            );

                            $monthlyPrice = $monthly ? (float) ($monthly->price ?? 0) : null;

                            $discount = $monthly && (float) $monthly->discount > 0
                                ? (float) $monthly->discount
                                : (float) ($kamar->cumulative_discount ?? 0);

                            $finalPrice = $monthlyPrice;

                            if ($monthlyPrice && $discount > 0) {
                                $finalPrice = max(
                                    0,
                                    round($monthlyPrice - ($monthlyPrice * $discount / 100))
                                );
                            }

                            $status = in_array(
                                $kamar->room_status ?? 'kosong',
                                ['kosong', 'pending', 'terisi']
                            )
                                ? ($kamar->room_status ?? 'kosong')
                                : 'kosong';

                            $facilities = array_filter(
                                array_map('trim', explode(',', (string) ($kamar->fasilitas ?? '')))
                            );

                            $propertyFacilitiesRoom = array_filter(
                                array_map('trim', explode(',', (string) ($kamar->productKosan?->fasilitas ?? '')))
                            );

                            $facilities = array_slice(
                                array_unique(array_merge($facilities, $propertyFacilitiesRoom)),
                                0,
                                4
                            );
                        @endphp

                        <article class="room-card jl-card bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl overflow-hidden flex flex-col"
                            data-status="{{ $status }}"
                            data-price="{{ $finalPrice ?: ($monthlyPrice ?: 0) }}">

                            {{-- ROOM IMAGE --}}
                            <div class="jl-room-image relative h-52 sm:h-56 bg-[#E9DDD2] overflow-hidden">

                                <img
                                    src="{{ $roomImage }}"
                                    alt="Kamar {{ $kamar->room ?? 'Jumala Living' }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover">

                                @if($status === 'kosong')

                                    <span class="absolute top-3 left-3 bg-[#FFF8F1] text-[#3B2314] rounded-full px-3 py-1.5 text-[10px] font-bold border border-[#E9DDD2]">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#C9A45C] mr-1.5"></span>
                                        Kosong
                                    </span>

                                @elseif($status === 'pending')

                                    <span class="absolute top-3 left-3 bg-[#FFF8F1] text-[#3B2314] rounded-full px-3 py-1.5 text-[10px] font-bold border border-[#D99AA8]">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#D99AA8] mr-1.5"></span>
                                        Pending
                                    </span>

                                @else

                                    <span class="absolute top-3 left-3 bg-[#3B2314] text-[#FFF8F1] rounded-full px-3 py-1.5 text-[10px] font-bold">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#D99AA8] mr-1.5"></span>
                                        Terisi
                                    </span>

                                @endif

                            </div>

                            {{-- ROOM CONTENT --}}
                            <div class="p-5 flex-1 flex flex-col">

                                <h3 class="jl-serif text-xl font-semibold">
                                    {{ $kamar->room ?? 'Kamar' }}
                                </h3>

                                {{-- FACILITIES --}}
                                <div class="flex flex-wrap gap-1.5 mt-4 min-h-8">

                                    @forelse($facilities as $facility)

                                        @php
                                            $icon = $getFacilityIcon($facility);
                                        @endphp

                                        <span class="inline-flex items-center gap-1.5 px-2 py-1.5 rounded-lg bg-[#FFF8F1] border border-[#E9DDD2] text-[10px] text-[#8E7B6D]">

                                            <i class="fa-solid {{ $icon }} text-[#C9A45C]"></i>

                                            {{ $facility }}

                                        </span>

                                    @empty

                                        <span class="text-xs text-[#8E7B6D]">
                                            Fasilitas belum ditampilkan
                                        </span>

                                    @endforelse

                                </div>

                                {{-- PRICE --}}
                                <div class="mt-auto pt-5">

                                    @if($monthlyPrice)

                                        @if($discount > 0)

                                            <div class="flex items-center gap-2">

                                                <span class="text-xs line-through text-[#8E7B6D]">
                                                    Rp {{ number_format($monthlyPrice, 0, ',', '.') }}
                                                </span>

                                                <span class="text-[10px] font-bold text-[#D99AA8] bg-[#D99AA8]/15 px-2 py-1 rounded-full">
                                                    -{{ round($discount) }}%
                                                </span>

                                            </div>

                                        @endif

                                        <p class="text-xl font-bold mt-1">
                                            Rp {{ number_format($finalPrice, 0, ',', '.') }}

                                            <span class="text-xs font-normal text-[#8E7B6D]">
                                                / bulan
                                            </span>
                                        </p>

                                    @elseif($kamar->priceKamar->isNotEmpty())

                                        @php
                                            $alternate = $kamar->priceKamar->first();
                                        @endphp

                                        @if($alternate)
                                        <p class="text-lg font-bold mt-1">

                                            Rp {{ number_format((float) ($alternate->price ?? 0), 0, ',', '.') }}

                                            <span class="text-xs font-normal text-[#8E7B6D]">
                                                / {{ strtolower((string) ($alternate->kategori ?? '')) }}
                                            </span>

                                        </p>
                                        @else
                                            <p class="text-sm font-bold mt-1 text-[#8E7B6D]">
                                                Hubungi untuk mengetahui harga
                                            </p>
                                        @endif

                                    @else

                                        <p class="text-sm font-bold mt-1 text-[#8E7B6D]">
                                            Hubungi untuk mengetahui harga
                                        </p>

                                    @endif

                                    {{-- BUTTONS --}}
                                    <div class="grid grid-cols-2 gap-2 mt-5">

                                        <a href="{{ route('kamar.detail', $kamar->id) }}"
                                            class="jl-button text-center px-3 py-3.5 rounded-xl border border-[#E9DDD2] text-xs font-bold hover:border-[#C9A45C] hover:bg-[#C9A45C]/10">

                                            Detail

                                        </a>

                                        <a href="{{ route('form.booking.kamar', $kamar->id) }}"
                                            class="jl-button text-center px-3 py-3.5 rounded-xl bg-[#3B2314] text-[#FFF8F1] text-xs font-bold hover:bg-[#C9A45C] hover:text-[#3B2314]">

                                            {{ $status === 'terisi' ? 'Booking Periode Nanti' : 'Pesan' }}

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

                {{-- EMPTY FILTER --}}
                <div id="room-empty"
                    class="hidden rounded-2xl border border-[#E9DDD2] bg-[#FFFCF8] p-10 text-center">

                    <i class="fa-solid fa-bed text-3xl text-[#C9A45C]"></i>

                    <h3 class="jl-serif text-xl mt-4">
                        Tidak Ada Kamar Sesuai Filter
                    </h3>

                    <p class="text-sm text-[#8E7B6D] mt-2">
                        Silakan pilih status atau urutan lain.
                    </p>

                </div>

            @else

                <div class="rounded-2xl border border-[#E9DDD2] bg-[#FFFCF8] p-10 text-center">

                    <i class="fa-solid fa-bed text-3xl text-[#C9A45C]"></i>

                    <h3 class="jl-serif text-xl mt-4">
                        Belum Ada Kamar Tersedia
                    </h3>

                    <p class="text-sm text-[#8E7B6D] mt-2">
                        Data kamar sedang diperbarui. Silakan hubungi admin untuk informasi terkini.
                    </p>

                </div>

            @endif

        </section>


        {{-- =====================================================
             FACILITIES
        ====================================================== --}}
        @if($roomFacilities->count() > 0 || $propertyFacilities->count() > 0)

            <section class="border-y border-[#E9DDD2] bg-[#FFFCF8] py-16 sm:py-20 jl-reveal">

                <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

                    <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                        <span class="jl-accent-line"></span>
                        Fasilitas Hunian
                    </div>

                    <h2 class="jl-serif jl-section-title text-3xl sm:text-4xl mt-4">
                        Fasilitas untuk
                        <span class="text-[#C9A45C] italic">Kenyamananmu.</span>
                    </h2>

                    <div class="grid lg:grid-cols-2 gap-8 mt-10">

                        @if($roomFacilities->count() > 0)

                            <div>

                                <h3 class="text-sm font-bold mb-5">
                                    <i class="fa-solid fa-bed text-[#C9A45C] mr-2"></i>
                                    Fasilitas Kamar
                                </h3>

                                <div class="grid sm:grid-cols-2 gap-3">

                                    @foreach($roomFacilities as $facility)

                                        @php
                                            $icon = $getFacilityIcon($facility);
                                        @endphp

                                        <div class="jl-card flex items-center gap-3 rounded-xl border border-[#E9DDD2] bg-[#FFF8F1] px-4 py-4 text-sm">

                                            <i class="fa-solid {{ $icon }} text-[#C9A45C] w-5"></i>

                                            {{ $facility }}

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif

                        @if($propertyFacilities->count() > 0)

                            <div>

                                <h3 class="text-sm font-bold mb-5">
                                    <i class="fa-solid fa-house text-[#C9A45C] mr-2"></i>
                                    Fasilitas Bersama
                                </h3>

                                <div class="grid sm:grid-cols-2 gap-3">

                                    @foreach($propertyFacilities as $facility)

                                        @php
                                            $icon = $getFacilityIcon($facility);
                                        @endphp

                                        <div class="jl-card flex items-center gap-3 rounded-xl border border-[#E9DDD2] bg-[#FFF8F1] px-4 py-4 text-sm">

                                            <i class="fa-solid {{ $icon }} text-[#C9A45C] w-5"></i>

                                            {{ $facility }}

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </section>

        @endif


        {{-- =====================================================
             BOOKING PROCESS
        ====================================================== --}}
        <section class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-16 sm:py-20 jl-reveal">

            <div class="text-center max-w-2xl mx-auto">

                <div class="flex items-center justify-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                    <span class="jl-accent-line"></span>
                    Cara Booking
                    <span class="jl-accent-line"></span>
                </div>

                <h2 class="jl-serif jl-section-title text-3xl sm:text-4xl mt-4">
                    Tiga Langkah Menuju
                    <span class="text-[#C9A45C] italic">Hunian Baru.</span>
                </h2>

                <p class="text-sm leading-7 text-[#8E7B6D] mt-4">
                    Proses reservasi yang praktis untuk memulai pengalaman tinggal bersama Jumala Living.
                </p>

            </div>

            <div class="grid md:grid-cols-3 gap-5 mt-10">

                @foreach([
                    ['01', 'Pilih Kamar', 'Jelajahi pilihan kamar dan lihat fasilitas serta harga yang tersedia.', 'fa-door-open'],
                    ['02', 'Reservasi', 'Isi data pemesanan, pilih tanggal check-in, dan metode pembayaran yang tersedia.', 'fa-calendar-check'],
                    ['03', 'Konfirmasi dan Check-in', 'Ikuti proses konfirmasi booking dan serah terima kunci.', 'fa-key']
                ] as $step)

                    <article class="jl-card relative bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl p-7 overflow-hidden">

                        <div class="flex justify-between items-start">

                            <span class="jl-serif text-5xl text-[#C9A45C]/60">
                                {{ $step[0] }}
                            </span>

                            <div class="w-12 h-12 rounded-xl bg-[#D99AA8]/20 flex items-center justify-center">

                                <i class="fa-solid {{ $step[3] }} text-[#C9A45C] text-lg"></i>

                            </div>

                        </div>

                        <h3 class="jl-serif text-xl mt-7">
                            {{ $step[1] }}
                        </h3>

                        <p class="text-sm leading-7 text-[#8E7B6D] mt-3">
                            {{ $step[2] }}
                        </p>

                    </article>

                @endforeach

            </div>

        </section>


        {{-- =====================================================
             TESTIMONIALS
        ====================================================== --}}
        <section class="bg-[#3B2314] text-[#FFF8F1] py-16 sm:py-20 jl-reveal">

            <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

                <div class="max-w-2xl">

                    <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                        <span class="jl-accent-line"></span>
                        Pengalaman Penghuni
                    </div>

                    <h2 class="jl-serif jl-section-title text-3xl sm:text-4xl mt-4">
                        Cerita dari
                        <span class="text-[#C9A45C] italic">Penghuni Kami.</span>
                    </h2>

                </div>

                @if($testimonis->isNotEmpty())

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 mt-10">

                        @foreach($testimonis as $review)

                            @php
                                $rating = max(1, min(5, (int) ($review->rating ?? 5)));
                                $profileImage = trim((string) ($review->image_profile ?? ''));
                            @endphp

                            <article class="jl-card bg-[#FFF8F1] text-[#3B2314] border border-[#C9A45C]/20 rounded-2xl p-6">

                                <div class="text-[#C9A45C] text-xs">

                                    @for($i = 0; $i < $rating; $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor

                                    @for($i = $rating; $i < 5; $i++)
                                        <i class="fa-regular fa-star"></i>
                                    @endfor

                                </div>

                                <p class="text-sm leading-7 text-[#8E7B6D] mt-5">
                                    "{{ $review->review ?? '' }}"
                                </p>

                                <div class="flex items-center gap-3 mt-6 pt-4 border-t border-[#E9DDD2]">

                                    @if($profileImage !== '')

                                        <img
                                            src="{{ asset('storage/' . $profileImage) }}"
                                            alt="{{ $review->name ?? 'Penghuni Jumala Living' }}"
                                            loading="lazy"
                                            class="w-11 h-11 rounded-full object-cover">

                                    @else

                                        <i class="fa-solid fa-circle-user text-3xl text-[#C9A45C]"></i>

                                    @endif

                                    <div>

                                        <p class="text-sm font-bold">
                                            {{ $review->name ?? 'Penghuni Jumala Living' }}
                                        </p>

                                        <p class="text-[10px] text-[#8E7B6D] mt-1">
                                            {{ $review->created_at?->translatedFormat('d M Y') }}
                                        </p>

                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                @else

                    <div class="mt-9 border border-[#C9A45C]/30 rounded-2xl px-6 py-12 text-center">

                        <i class="fa-regular fa-comment-dots text-3xl text-[#C9A45C]"></i>

                        <p class="mt-4 text-sm text-[#FFF8F1]/80">
                            Pengalaman dan cerita penghuni Jumala Living.
                        </p>

                        <p class="mt-2 text-xs text-[#FFF8F1]/50">
                            Belum ada ulasan. Jadilah salah satu penghuni pertama yang berbagi pengalaman.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             LOCATION
        ====================================================== --}}
        <section id="lokasi"
            class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-16 sm:py-20 jl-reveal">

            <div class="grid lg:grid-cols-[.85fr_1.15fr] gap-6 items-stretch">

                <div class="bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl p-6 sm:p-8 flex flex-col justify-center">

                    <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">
                        <span class="jl-accent-line"></span>
                        Lokasi
                    </div>

                    <h2 class="jl-serif text-3xl mt-4">
                        Jumala Living
                    </h2>

                    <p class="text-sm leading-8 text-[#8E7B6D] mt-5">
                        Jl. Kenanga No. 3, Lowokwaru, Kec. Lowokwaru, Malang, Jawa Timur 65141.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 mt-7">

                        <a href="{{ $mapsUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jl-button inline-flex justify-center items-center gap-2 px-5 py-3.5 rounded-xl bg-[#3B2314] text-[#FFF8F1] text-sm font-bold hover:bg-[#C9A45C] hover:text-[#3B2314]">

                            <i class="fa-solid fa-map-location-dot text-[#C9A45C]"></i>
                            Google Maps

                        </a>

                        <a href="{{ $waUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jl-button inline-flex justify-center items-center gap-2 px-5 py-3.5 rounded-xl border border-[#E9DDD2] text-sm font-bold hover:bg-[#D99AA8]/20">

                            <i class="fa-brands fa-whatsapp text-[#C9A45C]"></i>
                            WhatsApp

                        </a>

                    </div>

                </div>

                <div class="min-h-[360px] rounded-2xl overflow-hidden border border-[#E9DDD2] bg-[#E9DDD2]">

                    <iframe
                        title="Peta Jumala Living"
                        src="https://www.google.com/maps?q={{ rawurlencode('Jumala Living Jl. Kenanga No. 3 Lowokwaru Malang Jawa Timur 65141') }}&output=embed"
                        class="w-full h-full min-h-[360px]"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>

                </div>

            </div>

        </section>


        {{-- =====================================================
             FINAL CTA
        ====================================================== --}}
        <section class="pb-16 sm:pb-20">

            <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

                <div class="relative overflow-hidden rounded-[1.75rem] bg-[#3B2314] px-7 py-12 sm:px-12 sm:py-16 text-[#FFF8F1] border border-[#C9A45C]/30">

                    <div class="absolute right-0 top-0 h-full w-1/3 bg-[#D99AA8]/10 pointer-events-none"></div>

                    <div class="absolute -right-10 -bottom-24 w-64 h-64 rounded-full border border-[#C9A45C]/20 pointer-events-none"></div>

                    <div class="relative max-w-2xl">

                        <div class="flex items-center gap-3 text-[#C9A45C] text-xs font-bold uppercase tracking-[.22em]">

                            <span class="jl-accent-line"></span>
                            Mulai Perjalananmu

                        </div>

                        <h2 class="jl-serif text-3xl sm:text-5xl leading-tight mt-5">

                            Temukan Ruang Nyaman
                            <span class="text-[#C9A45C] italic">
                                untuk Cerita Barumu.
                            </span>

                        </h2>

                        <p class="text-sm leading-7 text-[#FFF8F1]/70 mt-5">
                            Mulai perjalanan tinggal yang lebih nyaman bersama Jumala Living.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 mt-8">

                            <a href="#pilihan-kamar"
                                class="jl-button inline-flex justify-center items-center gap-2 px-6 py-4 rounded-xl bg-[#C9A45C] text-[#3B2314] text-sm font-bold hover:bg-[#D99AA8]">

                                Lihat Pilihan Kamar
                                <i class="fa-solid fa-arrow-right text-xs"></i>

                            </a>

                            <a href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="jl-button inline-flex justify-center items-center gap-2 px-6 py-4 rounded-xl border border-[#FFF8F1]/25 text-[#FFF8F1] text-sm font-bold hover:bg-[#FFFCF8]/10">

                                <i class="fa-brands fa-whatsapp text-[#C9A45C]"></i>
                                Hubungi WhatsApp

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

    {{-- FOOTER --}}
    @include('frontend.footer')


    {{-- =====================================================
         JAVASCRIPT INTERACTION
    ====================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* ==========================================
               STICKY NAVBAR SHADOW
            ========================================== */

            const navbarWrapper = document.getElementById('jl-navbar-wrapper');

            function updateNavbar() {
                if (!navbarWrapper) return;

                navbarWrapper.classList.toggle('scrolled', window.scrollY > 20);
            }

            window.addEventListener('scroll', updateNavbar, { passive: true });

            updateNavbar();


            /* ==========================================
               QUICK SEARCH TAGS
            ========================================== */

            document.querySelectorAll('[data-search-tag]').forEach(button => {

                button.addEventListener('click', () => {

                    const input = document.getElementById('hero-search');

                    if (!input) return;

                    input.value = button.dataset.searchTag;

                    input.closest('form').submit();

                });

            });


            /* ==========================================
               REVEAL ANIMATION
            ========================================== */

            const revealElements = document.querySelectorAll('.jl-reveal');

            if ('IntersectionObserver' in window) {

                const revealObserver = new IntersectionObserver(entries => {

                    entries.forEach(entry => {

                        if (entry.isIntersecting) {

                            entry.target.classList.add('active');

                            revealObserver.unobserve(entry.target);

                        }

                    });

                }, {
                    threshold: 0.08,
                    rootMargin: '0px 0px -30px 0px'
                });

                revealElements.forEach(element => {
                    revealObserver.observe(element);
                });

            } else {

                revealElements.forEach(element => {
                    element.classList.add('active');
                });

            }


            /* ==========================================
               ROOM FILTER AND SORT
            ========================================== */

            const availabilityFilter = document.getElementById('availability-filter');
            const roomSort = document.getElementById('room-sort');
            const roomGrid = document.getElementById('room-grid');
            const roomEmpty = document.getElementById('room-empty');

            function updateRoomView() {

                if (!roomGrid) return;

                const cards = Array.from(
                    roomGrid.querySelectorAll('.room-card')
                );

                const status = availabilityFilter?.value || 'semua';

                let visible = 0;

                cards.forEach(card => {
 
                    const show = status === 'semua' ||
                        card.dataset.status === status;

                    card.style.display = show ? 'flex' : 'none';

                    if (show) visible++;

                });

                if (roomSort?.value && roomSort.value !== 'default') {

                    const sortedCards = [...cards].sort((a, b) => {

                        const av = Number(a.dataset.price || 0);
                        const bv = Number(b.dataset.price || 0);

                        return roomSort.value === 'price-asc'
                            ? av - bv
                            : bv - av;

                    });

                    sortedCards.forEach(card => {
                        roomGrid.appendChild(card);
                    });

                }

                if (roomEmpty) {

                    roomEmpty.classList.toggle('hidden', visible !== 0);

                }

            }

            availabilityFilter?.addEventListener('change', updateRoomView);

            roomSort?.addEventListener('change', updateRoomView);


            /* ==========================================
               CLOSE FLASH MODALS
            ========================================== */

            const flashModals = [
                document.getElementById('flash-success-modal'),
                document.getElementById('flash-failed-modal')
            ];

            flashModals.forEach(modal => {

                if (!modal) return;

                modal.addEventListener('click', event => {

                    if (event.target === modal) {
                        modal.remove();
                    }

                });

            });

            document.addEventListener('keydown', event => {

                if (event.key === 'Escape') {

                    flashModals.forEach(modal => {
                        modal?.remove();
                    });

                }

            });


            /* ==========================================
               SMOOTH ANCHOR NAVIGATION
            ========================================== */

            document.querySelectorAll('a[href^="#"]').forEach(link => {

                link.addEventListener('click', function (event) {

                    const targetId = this.getAttribute('href');

                    if (!targetId || targetId === '#') return;

                    const target = document.querySelector(targetId);

                    if (!target) return;

                    event.preventDefault();

                    target.scrollIntoView({
                        behavior: window.matchMedia(
                            '(prefers-reduced-motion: reduce)'
                        ).matches ? 'auto' : 'smooth',
                        block: 'start'
                    });

                });

            });

        });
    </script>

</body>
</html>