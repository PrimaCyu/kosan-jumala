<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jumala Living - {{ $kamar?->room ?? 'Detail Kamar' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="{{ asset('logo.jpeg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    :root {
      --brown: #3B2314;
      --gold: #C9A45C;
      --ivory: #FFF8F1;
      --rose: #D99AA8;
      --rose-dark: #D99AA8;
      --line: #EAD8D7;
      --muted: #765C55;
    }

    body {
      background:
        radial-gradient(circle at 8% 12%, rgba(217,154,168,.16), transparent 24rem),
        radial-gradient(circle at 92% 42%, rgba(201,164,92,.10), transparent 22rem),
        var(--ivory);
    }

    .reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .65s ease, transform .65s cubic-bezier(.16,1,.3,1);
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    .scl-card {
      background: rgba(255,252,248,.92);
      border: 1px solid rgba(233,221,210,.9);
      box-shadow: 0 18px 45px rgba(59,35,20,.06);
    }

    .scl-hover {
      transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }

    .scl-hover:hover {
      transform: translateY(-4px);
      box-shadow: 0 24px 55px rgba(59,35,20,.10);
      border-color: rgba(217,154,168,.65);
    }

    .shine {
      position: relative;
      overflow: hidden;
    }

    .shine:after {
      content: "";
      position: absolute;
      inset: 0;
      transform: translateX(-110%);
      background: linear-gradient(
        110deg,
        transparent 35%,
        rgba(255,252,248,.25),
        transparent 65%
      );
      transition: transform .7s ease;
      pointer-events: none;
    }

    .shine:hover:after {
      transform: translateX(110%);
    }

    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }

    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    @media(prefers-reduced-motion: reduce) {
      .reveal,
      .scl-hover,
      .shine:after {
        transition: none;
      }
    }

    /* =========================================================
       JUMALA LIVING — SOFT ROSE EDITION
       Palette: Dusty Rose, Chocolate Brown, Champagne Gold, Warm Ivory
       ========================================================= */
    :root {
      --jl-rose: #D99AA8;
      --jl-brown: #3B2314;
      --jl-gold: #C9A45C;
      --jl-ivory: #FFF8F1;
      --jl-rose-soft: rgba(217,154,168,.12);
      --jl-line: #EAD8D7;
      --jl-muted: #765C55;
      --jl-shadow: 0 18px 48px rgba(59,35,20,.075);
    }

    html { scroll-padding-top: 88px; }
    body {
      color: var(--jl-brown);
      font-family: "DM Sans", "Plus Jakarta Sans", sans-serif;
      background:
        radial-gradient(ellipse at 5% 10%, rgba(217,154,168,.17), transparent 32rem),
        radial-gradient(ellipse at 96% 35%, rgba(201,164,92,.10), transparent 28rem),
        var(--jl-ivory);
    }

    a, button { -webkit-tap-highlight-color: transparent; }
    button, a { touch-action: manipulation; }
    :focus-visible {
      outline: 3px solid rgba(201,164,92,.8);
      outline-offset: 3px;
    }

    /* Navigation and page rhythm */
    main { isolation: isolate; }
    main > section { scroll-margin-top: 92px; }
    main > section:first-child { padding: .35rem .15rem; }
    .scl-card {
      background: rgba(255,252,248,.88);
      border: 1px solid rgba(234,216,215,.85);
      box-shadow: var(--jl-shadow);
      backdrop-filter: blur(12px);
    }
    .scl-hover {
      transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
    }
    .scl-hover:hover {
      transform: translateY(-3px);
      border-color: rgba(217,154,168,.75);
      box-shadow: 0 24px 55px rgba(59,35,20,.10);
    }

    /* Hero: softer pink panel, balanced text alignment */
    main > section:nth-of-type(2) > aside:first-child {
      min-height: 100%;
      border: 1px solid rgba(255,248,241,.65);
      border-radius: 2rem;
      background:
        radial-gradient(circle at 90% 8%, rgba(255,248,241,.38), transparent 13rem),
        linear-gradient(145deg, #D99AA8 0%, var(--jl-rose) 58%, #D99AA8 100%);
      box-shadow: 0 22px 52px rgba(142,123,109,.17);
    }
    main > section:nth-of-type(2) > aside:first-child h1 {
      max-width: 12ch;
      letter-spacing: -.045em;
      line-height: 1.08;
      text-wrap: balance;
    }
    main > section:nth-of-type(2) > aside:first-child .relative > a {
      flex-wrap: wrap;
      line-height: 1.6;
    }
    main > section:nth-of-type(2) > aside:first-child .grid > div {
      backdrop-filter: blur(8px);
      transition: transform .25s ease, background .25s ease;
    }
    main > section:nth-of-type(2) > aside:first-child .grid > div:hover {
      transform: translateY(-3px);
      background: rgba(255,252,248,.48);
    }

    /* Gallery frame */
    main > section:nth-of-type(2) > div:last-child {
      border-radius: 2rem;
      box-shadow: var(--jl-shadow);
    }
    main > section:nth-of-type(2) > div:last-child > div {
      border-radius: 1.55rem;
    }

    /* Information and pricing cards */
    main > section:nth-of-type(3) > div > .scl-card {
      border-radius: 1.8rem;
    }
    main > section:nth-of-type(3) > div > .scl-card h2 {
      max-width: 17ch;
      letter-spacing: -.035em;
      line-height: 1.15;
      text-wrap: balance;
    }
    main > section:nth-of-type(3) > aside > div {
      border-radius: 1.9rem;
      background: linear-gradient(145deg, #D99AA8, var(--jl-rose));
      box-shadow: 0 20px 48px rgba(142,123,109,.16);
    }
    main > section:nth-of-type(3) > aside > div > div {
      border-radius: 1.55rem;
    }

    /* CTA banner */
    main > section:nth-of-type(4) {
      border: 1px solid rgba(255,248,241,.55);
      background:
        radial-gradient(circle at 90% 15%, rgba(255,248,241,.42), transparent 15rem),
        linear-gradient(120deg, #D99AA8, var(--jl-rose) 60%, #D99AA8);
      box-shadow: 0 20px 48px rgba(142,123,109,.14);
    }
    main > section:nth-of-type(4) h2 {
      letter-spacing: -.035em;
      line-height: 1.12;
      text-wrap: balance;
    }

    /* Recommendation cards */
    #sibling-rooms-section > div:last-child > div {
      border-radius: 1.45rem;
      border-color: var(--jl-line);
      box-shadow: 0 10px 28px rgba(59,35,20,.055);
    }
    #sibling-rooms-section img {
      transition: transform .55s cubic-bezier(.2,.75,.25,1);
    }
    #sibling-rooms-section a:focus-visible,
    #sibling-rooms-section button:focus-visible { outline-offset: 4px; }

    /* Consistent interaction states */
    button, a {
      transition-property: color, background-color, border-color, box-shadow, transform, opacity;
      transition-duration: .22s;
      transition-timing-function: ease;
    }
    button:active, a:active { transform: scale(.985); }
    .shine:after { opacity: .75; }
    .reveal { will-change: opacity, transform; }

    /* Toast and mobile booking bar */
    #toast-message {
      max-width: min( calc(100vw - 2rem), 390px );
      padding: .85rem 1rem;
      border: 1px solid rgba(255,248,241,.16);
      backdrop-filter: blur(14px);
    }
    body { padding-bottom: env(safe-area-inset-bottom); }
    @media (max-width: 1023px) {
      main { padding-bottom: 1.75rem; }
      main > section:nth-of-type(2) { gap: 1rem; }
      main > section:nth-of-type(2) > aside:first-child { min-height: auto; }
      main > section:nth-of-type(3) { gap: 1.15rem; }
      main > section:nth-of-type(3) > aside { position: static; }
      #sibling-rooms-section { scroll-margin-bottom: 6rem; }
    }
    @media (max-width: 640px) {
      main { padding-top: 1.1rem; }
      main > section:nth-of-type(2) > aside:first-child {
        padding: 1.35rem;
        border-radius: 1.55rem;
      }
      main > section:nth-of-type(2) > aside:first-child h1 {
        font-size: clamp(1.8rem, 8vw, 2.35rem);
      }
      main > section:nth-of-type(2) > div:last-child {
        padding: .55rem;
        border-radius: 1.45rem;
      }
      main > section:nth-of-type(2) > div:last-child > div {
        padding: .5rem;
        border-radius: 1.1rem;
      }
      main > section:nth-of-type(3) > div > .scl-card {
        padding: 1.25rem;
        border-radius: 1.45rem;
      }
      main > section:nth-of-type(4) {
        margin-top: 2rem;
        padding: 1.35rem;
        border-radius: 1.55rem;
      }
      #toast-message {
        top: 1rem;
        right: 1rem;
        left: 1rem;
        width: auto;
      }
    }
    @media (hover: none) {
      .scl-hover:hover { transform: none; }
      .shine:hover:after { transform: translateX(-110%); }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
      }
      .reveal {
        opacity: 1 !important;
        transform: none !important;
      }
    }

  </style>
</head>

<body class="bg-[#FFF8F1] text-[#3B2314] font-sans antialiased overflow-x-clip min-h-screen flex flex-col justify-between">

  <!-- 1. BAR LOADING -->
  <div id="page-loader"
    class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-[#D99AA8] via-[#D99AA8] to-[#C9A45C] z-[100] transition-all duration-500 ease-out">
  </div>

  <!-- 2. NAVBAR -->
  @include('frontend.navbar')

  @php
    $productKosan = $kamar?->productKosan;
    $namaKosan = $productKosan?->title ?? 'Jumala Living';
    $namaKamar = $kamar?->room ?? 'Kamar Exclusive';
    $wilayahKosan = $productKosan?->full_location ?? 'Lowokwaru, Malang';
    $kosanUrl = $productKosan
        ? route('kosan.detail', $productKosan->slug ?? $productKosan->getKey())
        : url('/#tentang');

    $images = [];

    foreach ($kamar?->productKamarImageKosan ?? [] as $imgObj) {
        $imagePath = trim((string) ($imgObj?->image ?? ''));
        if ($imagePath !== '') {
            $images[] = asset('storage/' . $imagePath);
        }
    }

    if (empty($images)) {
        foreach ($productKosan?->productImageKosan ?? [] as $kosImg) {
            $imagePath = trim((string) ($kosImg?->image ?? ''));
            if ($imagePath !== '') {
                $images[] = asset('storage/' . $imagePath);
            }
        }
    }
    $images = $images ?: [asset('logo.jpeg')];

    // Status ketersediaan kamar
    $roomStatus = $kamar?->room_status ?? 'kosong';
    $isOccupied = ($roomStatus === 'terisi');
    $activeTenant = $kamar?->active_tenant;

    $endDateFormatted = null;

    if ($activeTenant && !empty($activeTenant->end_date)) {
        try {
            $endDateFormatted = \Carbon\Carbon::parse($activeTenant->end_date)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            $endDateFormatted = date('d-m-Y', strtotime($activeTenant->end_date));
        }
    }

    // Resolusi nomor WhatsApp
    $waNumber = '6282146138847';

    foreach (collect($globalSosmed ?? []) as $sm) {
        $socialTitle = strtolower((string) ($sm->title ?? $sm->name ?? $sm->platform ?? ''));
        $socialUrl = (string) ($sm->url ?? $sm->link ?? '');

        if (str_contains($socialTitle, 'whatsapp') || str_contains(strtolower($socialUrl), 'wa.me')) {
            preg_match('/[0-9]{9,15}/', $socialUrl, $waMatch);

            if (!empty($waMatch[0])) {
                $waNumber = $waMatch[0];
                break;
            }
        }
    }

    $kamarWaMsg = "Halo Admin Jumala Living, saya tertarik dengan unit " . $namaKamar . " di " . $namaKosan . " (" . $wilayahKosan . ")" . ($isOccupied ? " yang saat ini sedang terisi. Apakah ada info jadwal ketersediaan atau waiting list?" : ". Apakah unit ini siap untuk disurvei/dipesan?");

    $waUrl = "https://wa.me/" . $waNumber . "?text=" . urlencode($kamarWaMsg);

    // Sibling rooms in the same kos
    $siblingRooms = collect($productKosan?->productKamarKosan ?? []);

    // Monthly price
    $monthlyPriceObj = $kamar?->monthly_price;
    $monthlyPriceVal = $monthlyPriceObj?->price;
  @endphp



  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 sm:py-8 space-y-8 sm:space-y-10 flex-grow w-full">

    <!-- 3. BREADCRUMB & TOOLBAR -->
    <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs mb-6">

      <nav class="flex items-center gap-2 text-[#765C55] font-medium overflow-x-auto no-scrollbar py-1">

        <a href="{{ route('home') }}"
          class="hover:text-[#D99AA8] transition-colors whitespace-nowrap">
          Beranda
        </a>

        <i class="fa-solid fa-chevron-right text-[9px] text-[#C9A45C]"></i>

        <a href="{{ route('kosan.index') }}"
          class="hover:text-[#D99AA8] transition-colors whitespace-nowrap">
          Daftar Kos
        </a>

        <i class="fa-solid fa-chevron-right text-[9px] text-[#C9A45C]"></i>

        <a href="{{ $kosanUrl }}"
          class="hover:text-[#D99AA8] font-bold text-[#D99AA8] transition-colors whitespace-nowrap truncate max-w-[160px] sm:max-w-[220px]">
          {{ $namaKosan }}
        </a>

        <i class="fa-solid fa-chevron-right text-[9px] text-[#C9A45C]"></i>

        <span class="font-bold text-[#3B2314] truncate max-w-[160px] sm:max-w-[220px]">
          {{ $namaKamar }}
        </span>

      </nav>


      <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-between sm:justify-end shrink-0">

        <!-- Bookmark -->
        <button
          type="button"
          onclick="toggleBookmarkKamar({{ $kamar->id }})"
          id="bookmark-kamar-btn"
          class="flex-1 min-w-[90px] sm:min-w-0 sm:flex-initial px-3.5 py-2 bg-[#FFFCF8] hover:bg-[#D99AA8] text-[#765C55] border border-[#EAD8D7] rounded-xl font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 active:scale-95"
          title="Simpan Kamar ini">

          <i class="fa-regular fa-heart text-xs text-[#D99AA8]"></i>
          <span id="bookmark-kamar-label">Simpan</span>

        </button>


        <!-- Share -->
        <button
          type="button"
          onclick="copyKamarLink()"
          class="flex-1 min-w-[90px] sm:min-w-0 sm:flex-initial px-3.5 py-2 bg-[#FFFCF8] hover:bg-[#D99AA8] text-[#765C55] border border-[#EAD8D7] rounded-xl font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 active:scale-95"
          title="Bagikan Tautan Kamar">

          <i class="fa-solid fa-share-nodes text-xs text-[#C9A45C]"></i>
          <span>Bagikan</span>

        </button>

      </div>
    </section>


    <!-- 4. GALLERY GRID SECTION -->
    <section class="reveal grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-7 items-stretch mb-12">

      <!-- ROOM INFORMATION -->
      <aside class="lg:col-span-4 order-2 lg:order-1 rounded-[2rem] bg-[#D99AA8] text-[#3B2314] p-6 sm:p-8 flex flex-col justify-between min-h-[440px] relative overflow-hidden">

        <div class="relative">

          <div class="flex flex-wrap items-center justify-between gap-2 mb-4">

            <div class="flex items-center gap-2">

              <span class="w-9 h-9 rounded-xl bg-[#FFF8F1] text-[#D99AA8] flex items-center justify-center font-bold text-sm shadow-sm">
                <i class="fa-solid fa-door-open"></i>
              </span>

              <span class="text-[10px] uppercase tracking-[.2em] font-black text-[#3B2314]/70">
                Unit Kamar
              </span>

            </div>


            <!-- Status -->
            @if($isOccupied)

              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#3B2314] text-[#FFF8F1] rounded-full text-[9px] sm:text-[10px] font-black shadow-md whitespace-nowrap">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D99AA8] animate-pulse"></span>
                Terisi (Penuh)
              </span>

            @elseif($roomStatus === 'pending')

              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#C9A45C] text-[#3B2314] rounded-full text-[9px] sm:text-[10px] font-black shadow-md whitespace-nowrap">
                <i class="fa-solid fa-clock text-[9px]"></i>
                Verifikasi
              </span>

            @else

              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#FFF8F1] text-[#3B2314] rounded-full text-[9px] sm:text-[10px] font-black shadow-md whitespace-nowrap">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D99AA8] animate-pulse"></span>
                Siap Huni
              </span>

            @endif

          </div>


          <a href="{{ $kosanUrl }}"
            class="text-xs font-semibold text-[#3B2314]/75 hover:text-[#FFF8F1] transition-colors flex items-center gap-1.5 mb-1.5">

            <i class="fa-solid fa-house text-[#FFF8F1]"></i>

            <span>{{ $namaKosan }}</span>

            <span class="text-[#3B2314]/50">• {{ $wilayahKosan }}</span>

          </a>


          <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black leading-tight tracking-tight">
            {{ $namaKamar }}
          </h1>

          @if($isOccupied)

            <div class="p-3 rounded-xl bg-[#FFFCF8]/30 border border-[#FFF8F1]/30 text-xs text-[#3B2314]/90 leading-relaxed mb-4">

              <i class="fa-solid fa-calendar-xmark text-[#3B2314] mr-1"></i>

              @if($endDateFormatted)

                Sedang dihuni aktif hingga <strong>{{ $endDateFormatted }}</strong>.

              @else

                Sedang dihuni aktif saat ini.

              @endif

              Tersedia opsi daftar antrean / waiting list.

            </div>

          @else

            <p class="text-xs sm:text-sm leading-relaxed text-[#3B2314]/80 max-w-sm mb-4">

              Kamar siap huni langsung dengan fasilitas lengkap, bersih, dan lingkungan nyaman di {{ $wilayahKosan }}.

            </p>

          @endif

        </div>


        <div class="relative space-y-2.5 mt-4">

          @if($kamar->has_video)

            <button
              type="button"
              onclick="scrollToVideoKamar()"
              class="w-full py-2.5 px-4 rounded-2xl bg-[#3B2314] hover:bg-[#C9A45C] hover:text-[#3B2314] text-[#FFF8F1] font-black text-xs transition-all flex items-center justify-center gap-2 shadow-lg active:scale-95">

              <i class="fa-solid fa-play text-[11px]"></i>
              <span>Tonton Video Tour Kamar</span>

            </button>

          @endif


          <div class="grid grid-cols-2 gap-2.5">

            <div class="rounded-2xl bg-[#FFFCF8]/30 border border-[#FFF8F1]/30 p-3">

              <i class="fa-solid fa-camera text-[#3B2314] mb-1 block"></i>

              <p class="text-[9px] uppercase tracking-wider text-[#3B2314]/60">
                Galeri Foto
              </p>

              <p class="font-bold text-xs">
                {{ count($images) }} Foto Tersedia
              </p>

            </div>


            <div class="rounded-2xl bg-[#FFFCF8]/30 border border-[#FFF8F1]/30 p-3">

              <i class="fa-solid fa-eye text-[#3B2314] mb-1 block"></i>

              <p class="text-[9px] uppercase tracking-wider text-[#3B2314]/60">
                Total Kunjungan
              </p>

              <p class="font-bold text-xs">
                {{ number_format($kamar->views ?? 0, 0, ',', '.') }} Dilihat
              </p>

            </div>

          </div>

        </div>

      </aside>


      <!-- GALLERY -->
      <div class="lg:col-span-8 order-1 lg:order-2 rounded-[2rem] bg-[#FFFCF8] p-3 sm:p-4 border border-[#EAD8D7] shadow-[0_20px_55px_rgba(59,35,20,.07)]">

        <div class="rounded-[1.5rem] bg-[#D99AA8] p-3 sm:p-4 h-full">

          @include('frontend.kosan.kamar.partials._gallery')

        </div>

      </div>

    </section>


    <!-- 5. DETAIL CONTENT & PRICE SIDEBAR -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

      <!-- DETAIL CONTENT -->
      <div class="lg:col-span-7 order-2 lg:order-1 space-y-6 reveal">

        @if($kamar->has_video)

          <div id="video-tour-kamar">

            @include('frontend.partials._video_player', [
              'videoEmbed' => $kamar->video_embed,
              'eyebrow'    => 'Video Tour Unit',
              'heading'    => 'Video ' . $namaKamar,
              'subtitle'   => 'Lihat langsung kondisi asli interior dan kelengkapan kamar ini.',
              'compact'    => true,
            ])

          </div>

        @endif


        <!-- INFORMATION CARD -->
        <div class="scl-card rounded-[2rem] p-5 sm:p-7 lg:p-8 scl-hover">

          <div class="flex items-start justify-between gap-5 mb-7">

            <div>

              <span class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[.18em] font-black text-[#D99AA8]">

                <span class="w-7 h-7 rounded-full bg-[#D99AA8]/20 flex items-center justify-center">
                  <i class="fa-solid fa-circle-info"></i>
                </span>

                Informasi & Fasilitas Lengkap

              </span>


              <h2 class="text-2xl sm:text-3xl font-black mt-3 text-[#3B2314]">
                Kenali kamar sebelum booking
              </h2>

            </div>


            <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-[#D99AA8]/20 text-[#D99AA8] items-center justify-center shrink-0">

              <i class="fa-solid fa-house-circle-check text-lg"></i>

            </div>

          </div>


          <div class="rounded-2xl bg-[#FFF8F1] border border-[#EAD8D7] p-4 sm:p-6">

            @include('frontend.kosan.kamar.partials._description_facilities')

          </div>

        </div>

      </div>


      <!-- PRICE SIDEBAR -->
      <aside class="lg:col-span-5 order-1 lg:order-2 reveal lg:sticky lg:top-24">

        <div class="rounded-[2rem] bg-[#D99AA8] p-1.5 shadow-[0_22px_50px_rgba(59,35,20,.10)]">

          <div class="rounded-[1.7rem] bg-[#FFFCF8] p-5 sm:p-7">

            <div class="flex items-center justify-between gap-4 mb-6">

              <div>

                <p class="text-[10px] uppercase tracking-[.18em] font-black text-[#D99AA8]">
                  Pilihan Paket
                </p>

                <h2 class="text-2xl font-black mt-1 text-[#3B2314]">
                  Harga Sewa
                </h2>

              </div>


              <span class="w-11 h-11 rounded-2xl bg-[#D99AA8]/20 text-[#D99AA8] flex items-center justify-center">

                <i class="fa-solid fa-receipt"></i>

              </span>

            </div>


            @include('frontend.kosan.kamar.partials._sidebar_price')

          </div>

        </div>

      </aside>

    </section>


    <!-- 6. BOTTOM ACTION BANNER -->
    <section class="reveal mt-12 rounded-[2rem] bg-[#D99AA8] p-5 sm:p-7 lg:p-8 overflow-hidden relative">

      <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

        <div class="max-w-xl">

          <div class="flex items-center gap-2 text-[#3B2314]/70 mb-2">

            <i class="fa-solid fa-bolt"></i>

            <span class="text-[10px] uppercase tracking-[.2em] font-black">
              Langkah berikutnya
            </span>

          </div>


          @if($isOccupied)

            <h2 class="text-2xl sm:text-3xl font-black text-[#3B2314]">
              Unit ini sedang penuh?
            </h2>

            <p class="text-xs sm:text-sm text-[#3B2314]/75 mt-2 leading-relaxed">

              Daftarkan diri Anda ke daftar antrean reservasi (waiting list) atau cek kamar alternatif lainnya di {{ $namaKosan }}.

            </p>

          @else

            <h2 class="text-2xl sm:text-3xl font-black text-[#3B2314]">
              Sudah cocok dengan unit ini?
            </h2>

            <p class="text-xs sm:text-sm text-[#3B2314]/75 mt-2 leading-relaxed">

              Lanjutkan pemesanan online sekarang atau jadwalkan survei langsung bersama pengelola kos.

            </p>

          @endif

        </div>


        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

          @if($isOccupied)

            <a
              href="{{ $waUrl }}"
              target="_blank"
              rel="noopener noreferrer"
              class="shine w-full sm:min-w-[210px] py-4 px-6 bg-[#C9A45C] hover:bg-[#3B2314] text-[#FFF8F1] text-xs sm:text-sm font-extrabold rounded-2xl transition-all shadow-lg text-center flex items-center justify-center gap-2.5 active:scale-[.98]">

              <i class="fa-brands fa-whatsapp text-lg"></i>
              <span>Daftar Waiting List via WA</span>

            </a>


            @if($siblingRooms->count() > 0)

              <button
                type="button"
                onclick="scrollToSiblingRooms()"
                class="w-full sm:min-w-[190px] py-4 px-6 bg-[#FFF8F1] hover:bg-[#FFFCF8] text-[#3B2314] text-xs sm:text-sm font-extrabold rounded-2xl transition-all shadow-lg text-center flex items-center justify-center gap-2.5 active:scale-[.98]">

                <i class="fa-solid fa-door-open text-[#D99AA8]"></i>
                <span>Cek Kamar Lain</span>

              </button>

            @endif

          @else

            <a
              href="{{ route('form.booking.kamar', $kamar->id) }}"
              class="shine w-full sm:min-w-[230px] py-4 px-6 bg-[#3B2314] hover:bg-[#D99AA8] hover:text-[#3B2314] text-[#FFF8F1] text-xs sm:text-sm font-extrabold rounded-2xl transition-all shadow-lg text-center flex items-center justify-center gap-2.5 active:scale-[.98]">

              <i class="fa-solid fa-bolt text-[#C9A45C]"></i>
              <span>Pesan Kamar Ini Sekarang</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>

            </a>

          @endif

        </div>

      </div>

    </section>


    <!-- 6.1 REKOMENDASI KAMAR LAIN -->
    @if($siblingRooms->count() > 0)

      <section id="sibling-rooms-section" class="reveal mt-12 pt-8 border-t border-[#EAD8D7] space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">

          <div>

            <span class="text-[10px] font-extrabold uppercase tracking-[.2em] text-[#D99AA8]">
              Pilihan Unit Lainnya
            </span>

            <h3 class="text-2xl sm:text-3xl font-black text-[#3B2314] mt-1">
              Kamar Lain di {{ $namaKosan }}
            </h3>

            <p class="text-xs sm:text-sm text-[#765C55] mt-0.5">
              Bandingkan ukuran dan fasilitas unit lainnya dalam properti ini.
            </p>

          </div>


          <a
            href="{{ $kosanUrl }}"
            class="inline-flex items-center gap-1.5 text-xs font-black text-[#D99AA8] hover:underline">

            <span>Lihat Profil Lengkap Kos</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>

          </a>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

          @foreach($siblingRooms as $sibKamar)

            @php

              $sibImg = $sibKamar->productKamarImageKosan?->first();
              $sibImagePath = trim((string) ($sibImg?->image ?? ''));
              $sibImgUrl = $sibImagePath !== ''
                ? asset('storage/' . $sibImagePath)
                : asset('logo.jpeg');

              $sibStatus = $sibKamar->room_status ?? 'kosong';

              $sibIsOccupied = ($sibStatus === 'terisi');

              $sibPriceObj = $sibKamar->priceKamar->first(fn($p) => strtolower((string) ($p->kategori ?? '')) === 'bulan');

              $sibPriceVal = $sibPriceObj ? $sibPriceObj->price : null;

            @endphp


            <div class="bg-[#FFFCF8] rounded-3xl border border-[#EAD8D7] overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-[#D99AA8] transition-all duration-300 group flex flex-col justify-between">

              <div>

                <!-- IMAGE -->
                <div class="relative h-44 overflow-hidden bg-[#D99AA8]">

                  <img
                    src="{{ $sibImgUrl }}"
                    alt="{{ $sibKamar->room ?? 'Kamar' }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">


                  <div class="absolute top-3 left-3">

                    @if($sibIsOccupied)

                      <span class="px-2.5 py-0.5 bg-[#3B2314]/90 text-[#FFF8F1] rounded-full text-[9px] font-black shadow-md flex items-center gap-1">

                        <span class="w-1.5 h-1.5 rounded-full bg-[#D99AA8] animate-pulse"></span>

                        Terisi (Penuh)

                      </span>

                    @else

                      <span class="px-2.5 py-0.5 bg-[#D99AA8] text-[#3B2314] rounded-full text-[9px] font-black shadow-md flex items-center gap-1">

                        <span class="w-1.5 h-1.5 rounded-full bg-[#3B2314] animate-pulse"></span>

                        Siap Huni

                      </span>

                    @endif

                  </div>

                </div>


                <!-- CARD CONTENT -->
                <div class="p-4 sm:p-5">

                  <h4 class="font-black text-[#3B2314] text-base group-hover:text-[#D99AA8] transition-colors line-clamp-1">

                    {{ $sibKamar->room ?? 'Kamar' }}

                  </h4>


                  <p class="text-xs text-[#765C55] mt-1 line-clamp-1">

                    {{ strip_tags($sibKamar->description ?? 'Fasilitas lengkap dan nyaman.') }}

                  </p>


                  <div class="mt-4 pt-3 border-t border-[#E9DDD2] flex items-center justify-between">

                    <div>

                      <span class="text-[9px] uppercase font-bold text-[#8E7B6D] block">
                        Tarif
                      </span>

                      @if($sibPriceVal)

                        <span class="text-sm font-black text-[#D99AA8]">
                          Rp {{ number_format($sibPriceVal, 0, ',', '.') }}
                        </span>

                        <span class="text-[10px] text-[#8E7B6D]">
                          /bln
                        </span>

                      @else

                        <span class="text-xs font-bold text-[#3B2314]">
                          Hubungi Kami
                        </span>

                      @endif

                    </div>


                    <a
                      href="{{ route('kamar.detail', $sibKamar->id) }}"
                      class="px-3 py-1.5 bg-[#D99AA8] hover:bg-[#3B2314] text-[#3B2314] hover:text-[#FFF8F1] text-xs font-bold rounded-xl transition-colors shadow-sm flex items-center gap-1">

                      <span>Detail</span>
                      <i class="fa-solid fa-arrow-right text-[9px]"></i>

                    </a>

                  </div>

                </div>

              </div>

            </div>

          @endforeach

        </div>

      </section>

    @endif

  </main>


  <!-- 7. LIGHTBOX GALERI FOTO MODAL -->
  @include('frontend.kosan.kamar.partials._gallery_modal')


  <!-- TOAST NOTIFICATION -->
  <div
    id="toast-message"
    class="fixed top-5 right-5 z-[9999] hidden items-center gap-2.5 px-4 py-3 bg-[#3B2314] text-[#FFF8F1] text-xs font-bold rounded-2xl shadow-2xl transition-all duration-300 border border-[#FFF8F1]/10">

    <i class="fa-solid fa-circle-check text-[#D99AA8] text-base"></i>

    <span id="toast-text">
      Tautan kamar berhasil disalin!
    </span>

  </div>


  <!-- 8. FOOTER -->
  @include('frontend.footer')


  <!-- 9. MOBILE FLOATING STICKY BOOKING BAR -->
  <div class="fixed bottom-0 left-0 right-0 z-40 bg-[#FFF8F1]/95 backdrop-blur-md border-t border-[#EAD8D7] p-3 px-4 shadow-[0_-6px_25px_rgba(59,35,20,0.1)] block lg:hidden">

    <div class="max-w-md mx-auto flex items-center justify-between gap-2.5">

      <div class="space-y-0.5">

        <div class="flex items-center gap-1.5">

          @if($isOccupied)

            <span class="w-2 h-2 rounded-full bg-[#3B2314]"></span>

            <span class="text-[10px] font-bold text-[#3B2314] uppercase tracking-wider">
              Unit Terisi (Penuh)
            </span>

          @else

            <span class="w-2 h-2 rounded-full bg-[#D99AA8] animate-pulse"></span>

            <span class="text-[10px] font-bold text-[#D99AA8] uppercase tracking-wider">
              Siap Huni
            </span>

          @endif

        </div>


        <div class="flex items-baseline gap-1">

          <span class="text-base sm:text-lg font-black text-[#D99AA8]">

            {{ $monthlyPriceVal ? 'Rp ' . number_format($monthlyPriceVal, 0, ',', '.') : 'Hubungi Kami' }}

          </span>

          @if($monthlyPriceVal)

            <span class="text-[10px] font-bold text-[#8E7B6D]">
              /bln
            </span>

          @endif

        </div>

      </div>


      <div class="flex items-center gap-2">


        @if($isOccupied)

          <a
            href="{{ $waUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="shine px-4 py-2.5 bg-[#3B2314] hover:bg-[#D99AA8] hover:text-[#3B2314] text-[#FFF8F1] font-black text-xs rounded-xl shadow-md active:scale-95 flex items-center gap-1.5 shrink-0">

            <span>Waiting List</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>

          </a>

        @else

          <a
            href="{{ route('form.booking.kamar', $kamar->id) }}"
            class="shine px-5 py-2.5 bg-[#D99AA8] hover:bg-[#D99AA8] text-[#3B2314] font-black text-xs rounded-xl shadow-md active:scale-95 flex items-center gap-1.5 shrink-0">

            <span>Booking</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>

          </a>

        @endif

      </div>

    </div>

  </div>


  <!-- 10. JAVASCRIPT -->
  <script>

    // PAGE LOADER
    window.addEventListener('load', () => {

      const loader = document.getElementById('page-loader');

      if (loader) {

        loader.style.width = '100%';

        setTimeout(() => {
          loader.style.opacity = '0';
        }, 350);

      }

    });


    // SCROLL REVEAL — graceful fallback and reduced-motion support
    const revealElements = document.querySelectorAll('.reveal');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      revealElements.forEach(el => el.classList.add('active'));
    } else {
      const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('active');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.08, rootMargin: '0px 0px -45px 0px' });

      revealElements.forEach((el, index) => {
        el.style.transitionDelay = `${Math.min(index * 45, 225)}ms`;
        revealObserver.observe(el);
      });
    }


    // SCROLL TO SIBLING ROOMS
    function scrollToSiblingRooms() {

      const el = document.getElementById('sibling-rooms-section');

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
      }

    }


    // SCROLL TO VIDEO
    function scrollToVideoKamar() {

      const el = document.getElementById('video-tour-kamar');

      if (el) {

        const top = el.getBoundingClientRect().top + window.scrollY - 90;

        window.scrollTo({
          top: top,
          behavior: 'smooth'
        });

      }

    }


    // COPY ROOM LINK
    function copyKamarLink() {

      const url = window.location.href;

      if (navigator.clipboard && window.isSecureContext) {

        navigator.clipboard.writeText(url)

          .then(() => {
            showToast("Tautan kamar berhasil disalin ke clipboard!");
          })

          .catch(() => fallbackCopyKamar(url));

      } else {

        fallbackCopyKamar(url);

      }

    }


    // FALLBACK COPY
    function fallbackCopyKamar(text) {

      const textArea = document.createElement("textarea");

      textArea.value = text;

      textArea.style.position = "fixed";
      textArea.style.left = "-999999px";

      document.body.appendChild(textArea);

      textArea.focus();
      textArea.select();

      try {

        document.execCommand('copy');

        showToast("Tautan kamar berhasil disalin ke clipboard!");

      } catch (err) {

        showToast("Gagal menyalin tautan");

      }

      document.body.removeChild(textArea);

    }


    // TOAST NOTIFICATION
    function showToast(msg) {

      const toast = document.getElementById('toast-message');

      const toastText = document.getElementById('toast-text');

      if (!toast || !toastText) return;

      toastText.textContent = msg;

      toast.classList.remove('hidden');
      toast.classList.add('flex');

      setTimeout(() => {

        toast.classList.add('hidden');
        toast.classList.remove('flex');

      }, 2800);

    }


    // BOOKMARK ROOM
    function toggleBookmarkKamar(id) {

      const key = 'scl_fav_kamar_' + id;

      const btn = document.getElementById('bookmark-kamar-btn');

      const label = document.getElementById('bookmark-kamar-label');

      const icon = btn ? btn.querySelector('i') : null;

      let isSaved = false;
      try { isSaved = localStorage.getItem(key) === 'true'; } catch (error) {
        showToast('Penyimpanan favorit tidak tersedia di browser ini.');
        return;
      }


      if (isSaved) {

        try { localStorage.removeItem(key); } catch (error) {
          showToast('Favorit tidak dapat diperbarui di browser ini.');
          return;
        }

        if (icon) {
          icon.className = 'fa-regular fa-heart text-xs text-[#D99AA8]';
        }

        if (label) {
          label.textContent = 'Simpan';
        }

        showToast("Kamar dihapus dari daftar simpan");

      } else {

        try { localStorage.setItem(key, 'true'); } catch (error) {
          showToast('Favorit tidak dapat disimpan di browser ini.');
          return;
        }

        if (icon) {
          icon.className = 'fa-solid fa-heart text-xs text-[#D99AA8]';
        }

        if (label) {
          label.textContent = 'Tersimpan';
        }

        showToast("Kamar disimpan ke favorit!");

      }

    }


    // CHECK BOOKMARK STATUS
    document.addEventListener('DOMContentLoaded', () => {

      const id = {{ $kamar->id }};

      let isSaved = false;
      try { isSaved = localStorage.getItem('scl_fav_kamar_' + id) === 'true'; } catch (error) {
        // Favorit tetap dapat digunakan saat localStorage tersedia kembali.
      }

      const btn = document.getElementById('bookmark-kamar-btn');

      const label = document.getElementById('bookmark-kamar-label');

      const icon = btn ? btn.querySelector('i') : null;


      if (isSaved) {

        if (icon) {
          icon.className = 'fa-solid fa-heart text-xs text-[#D99AA8]';
        }

        if (label) {
          label.textContent = 'Tersimpan';
        }

      }

    });

  </script>

</body>
</html>