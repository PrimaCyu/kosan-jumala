<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Booking - {{ $kamar->room ?? 'Kamar Kos' }} - Jumala Living</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="icon" href="{{ asset('logo.jpeg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  @endif

  <style>
    :root {
      --brown: #3B2314;
      --red: #D99AA8;
      --yellow: #C9A45C;
      --green: #C9A45C;
      --cream: #FFF8F1;
      --line: #E9DDD2;
    }

    body {
      background:
        radial-gradient(circle at 5% 10%, rgba(201,164,92,.13), transparent 23rem),
        radial-gradient(circle at 95% 55%, rgba(118,92,85,.06), transparent 25rem),
        var(--cream);
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

    .booking-card {
      background: rgba(255,252,248,.9);
      border: 1px solid rgba(233,221,210,.95);
      box-shadow: 0 18px 50px rgba(59,35,20,.07);
    }

    .input-scl {
      transition: border-color .25s ease, box-shadow .25s ease, background .25s ease;
    }

    .input-scl:focus {
      border-color: var(--yellow) !important;
      box-shadow: 0 0 0 4px rgba(201,164,92,.14);
      outline: none;
    }

    .payment-option {
      transition: transform .25s ease, border-color .25s ease, background .25s ease, box-shadow .25s ease;
    }

    .payment-option:hover {
      transform: translateY(-2px);
    }

    /* FLATPICKR LUXURY THEME (RESPONSIVE & ZERO CLIPPING) */
    .flatpickr-calendar {
      background: #FFFCF8 !important;
      border-radius: 1.5rem !important;
      box-shadow: 0 24px 60px rgba(59,35,20,0.18) !important;
      border: 1px solid var(--line) !important;
      font-family: inherit !important;
      width: min(320px, calc(100vw - 20px)) !important;
      max-width: calc(100vw - 20px) !important;
      padding: 12px !important;
      box-sizing: border-box !important;
      z-index: 99999 !important;
    }

    .flatpickr-calendar::before,
    .flatpickr-calendar::after {
      border-bottom-color: #FFFCF8 !important;
    }

    .flatpickr-months {
      position: relative !important;
      align-items: center !important;
      padding: 4px 0 10px 0 !important;
      border-bottom: 1px solid #FFF8F1 !important;
    }

    .flatpickr-months .flatpickr-month {
      color: #3B2314 !important;
      height: 38px !important;
    }

    .flatpickr-current-month {
      font-size: 105% !important;
      font-weight: 800 !important;
      padding-top: 2px !important;
    }

    .flatpickr-current-month .cur-month {
      font-weight: 800 !important;
      color: #3B2314 !important;
    }

    .flatpickr-current-month input.cur-year {
      font-weight: 800 !important;
      color: #3B2314 !important;
    }

    /* Month Navigation Prev / Next Buttons */
    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
      position: absolute !important;
      top: 6px !important;
      width: 32px !important;
      height: 32px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      border-radius: 10px !important;
      background: #FFF8F1 !important;
      border: 1px solid #E9DDD2 !important;
      color: #3B2314 !important;
      transition: all 0.2s ease !important;
      cursor: pointer !important;
      z-index: 10 !important;
    }

    .flatpickr-months .flatpickr-prev-month {
      left: 6px !important;
    }

    .flatpickr-months .flatpickr-next-month {
      right: 6px !important;
    }

    .flatpickr-months .flatpickr-prev-month:hover,
    .flatpickr-months .flatpickr-next-month:hover {
      background: #D99AA8 !important;
      color: #FFFCF8 !important;
      border-color: #D99AA8 !important;
    }

    .flatpickr-months .flatpickr-prev-month svg,
    .flatpickr-months .flatpickr-next-month svg {
      width: 14px !important;
      height: 14px !important;
      fill: currentColor !important;
      stroke: currentColor !important;
      stroke-width: 1px !important;
    }

    /* Weekday Headers */
    .flatpickr-weekdays {
      margin-top: 6px !important;
      height: 30px !important;
    }

    span.flatpickr-weekday {
      color: #8E7B6D !important;
      font-weight: 800 !important;
      font-size: 11px !important;
      letter-spacing: 0.05em !important;
    }

    /* Days Grid Layout (7 columns, guaranteed fit with zero cut off) */
    .flatpickr-innerContainer {
      width: 100% !important;
      justify-content: center !important;
      overflow: visible !important;
    }

    .flatpickr-rContainer {
      width: 100% !important;
    }

    .flatpickr-days {
      width: 100% !important;
    }

    .dayContainer {
      width: 100% !important;
      min-width: 100% !important;
      max-width: 100% !important;
      display: grid !important;
      grid-template-columns: repeat(7, 1fr) !important;
      gap: 3px !important;
      padding: 4px 0 !important;
    }

    .flatpickr-day {
      width: auto !important;
      max-width: none !important;
      height: 38px !important;
      line-height: 36px !important;
      margin: 0 !important;
      border-radius: 10px !important;
      font-weight: 700 !important;
      font-size: 13px !important;
      color: #3B2314 !important;
      border: 1px solid transparent !important;
      transition: all 0.15s ease !important;
    }

    .flatpickr-day:hover {
      background: #FFF8F1 !important;
      border-color: #D99AA8 !important;
      color: #D99AA8 !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
      background: #D99AA8 !important;
      border-color: #D99AA8 !important;
      color: #FFFCF8 !important;
      font-weight: 800 !important;
      box-shadow: 0 4px 14px rgba(118,92,85,0.35) !important;
    }

    .flatpickr-day.today {
      border-color: #C9A45C !important;
      background: #FFF8F1 !important;
      color: #3B2314 !important;
      font-weight: 800 !important;
    }

    /* Disabled Dates (Past or Booked) */
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover {
      color: #E9DDD2 !important;
      background: #FFF8F1 !important;
      cursor: not-allowed !important;
      border-color: transparent !important;
      opacity: 0.65 !important;
      text-decoration: line-through !important;
    }

    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
      color: #E9DDD2 !important;
    }

    .shine {
      position: relative;
      overflow: hidden;
    }

    .shine::after {
      content: "";
      position: absolute;
      inset: 0;
      transform: translateX(-110%);
      background: linear-gradient(110deg, transparent 35%, rgba(255,252,248,.22), transparent 65%);
      transition: transform .7s ease;
      pointer-events: none;
    }

    .shine:hover::after {
      transform: translateX(110%);
    }

    @media (max-width: 360px) {
      .flatpickr-calendar {
        width: calc(100vw - 16px) !important;
        max-width: calc(100vw - 16px) !important;
        padding: 8px !important;
        border-radius: 1rem !important;
      }
      .flatpickr-day {
        height: 33px !important;
        line-height: 31px !important;
        font-size: 11px !important;
      }
      .flatpickr-current-month {
        font-size: 90% !important;
      }
    }

    /* Prevent sticky stepper crowding mobile landscape (< 520px height) */
    @media (max-height: 520px) {
      .booking-stepper-sticky {
        position: relative !important;
        top: 0 !important;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal, .payment-option, .shine::after, .input-scl {
        transition: none;
      }
    }


    /* =========================================================
       LAYOUT REFINEMENT — FORM BOOKING
       Catatan: hanya mengatur tata letak, jarak, alignment,
       ukuran container, dan responsivitas. Warna, font, isi,
       atribut form, serta seluruh ID asli tidak diubah.
       ========================================================= */

    /* Lebar halaman dan ritme vertikal */
    main {
      max-width: 1320px;
      padding-top: clamp(1.5rem, 3vw, 2.75rem);
      padding-bottom: clamp(5.5rem, 8vw, 7rem);
    }

    /* Stepper lebih lapang dan terpusat */
    .booking-stepper-sticky > div > div {
      max-width: 980px;
      margin-inline: auto;
      justify-content: center;
      gap: clamp(.45rem, 2.4vw, 2rem);
    }
    .booking-stepper-sticky .step-btn {
      min-width: 0;
      flex: 0 1 auto;
    }
    .booking-stepper-sticky .step-btn > span:last-child {
      white-space: nowrap;
    }
    .booking-stepper-sticky .step-btn > span:first-child {
      flex: 0 0 2.25rem;
    }
    .booking-stepper-sticky .flex-1 {
      flex: 0 1 4.5rem;
      min-width: 12px;
    }

    /* Breadcrumb dan judul dibuat lebih editorial */
    main > nav[aria-label="Breadcrumb"] {
      display: flex;
      justify-content: center;
      margin-bottom: clamp(1.75rem, 4vw, 3rem);
    }
    main > nav[aria-label="Breadcrumb"] > div {
      max-width: 100%;
      justify-content: center;
      text-align: center;
    }
    main > header {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 1.35rem;
      margin-inline: auto;
      margin-bottom: clamp(2rem, 4vw, 3.5rem);
      max-width: 850px;
    }
    main > header > div:first-child {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    main > header h1 {
      max-width: 16ch;
      margin-inline: auto;
      text-wrap: balance;
      line-height: 1.08;
    }
    main > header p {
      margin-inline: auto;
      max-width: 62ch;
    }
    main > header > div:last-child {
      align-self: center;
      justify-content: center;
      min-width: min(100%, 240px);
    }

    /* Rasio kolom lebih seimbang untuk form dan ringkasan */
    #booking-form > .grid {
      grid-template-columns: minmax(0, 1.7fr) minmax(310px, .9fr);
      gap: clamp(1.25rem, 2.5vw, 2.25rem);
      align-items: start;
    }
    #booking-form > .grid > div:first-child {
      min-width: 0;
      display: grid;
      gap: clamp(1.25rem, 2vw, 1.8rem);
    }
    #booking-form > .grid > aside {
      min-width: 0;
      top: 112px;
    }

    /* Kartu form: ruang napas dan sudut lebih halus */
    #sec-data-diri,
    #sec-durasi,
    #sec-pembayaran {
      min-width: 0;
      border-radius: 1.65rem;
      scroll-margin-top: 145px;
    }
    #sec-data-diri > .bg-\[\#3B2314\] {
      padding: clamp(1.35rem, 2.7vw, 2rem);
    }
    #sec-data-diri > .bg-\[\#3B2314\] > div {
      justify-content: center;
      text-align: center;
      gap: 1rem;
    }
    #sec-data-diri > .bg-\[\#3B2314\] > div > div {
      text-align: left;
    }
    #sec-data-diri > div:last-child,
    #sec-pembayaran > div:last-child {
      padding: clamp(1.25rem, 2.8vw, 2.15rem);
    }

    /* Form fields ditata dengan jarak konsisten */
    #sec-data-diri > div:last-child {
      display: grid;
      gap: 1.35rem;
    }
    #sec-data-diri > div:last-child > div {
      min-width: 0;
    }
    #sec-data-diri input,
    #sec-durasi input:not([type="hidden"]),
    #sec-pembayaran select {
      min-width: 0;
      max-width: 100%;
    }

    /* Heading durasi dan pembayaran diseimbangkan di tengah */
    #sec-durasi {
      padding: clamp(1.25rem, 2.8vw, 2.15rem);
    }
    #sec-durasi > div:first-child {
      align-items: center;
      text-align: center;
      padding-bottom: 1.2rem;
      border-bottom: 1px solid var(--line);
    }
    #sec-durasi > div:first-child > span:last-child {
      flex: 0 0 auto;
    }
    #sec-durasi > div:nth-child(2) {
      gap: clamp(1.25rem, 2.5vw, 2rem);
    }
    #sec-durasi > div:nth-child(2) > div {
      min-width: 0;
    }
    #sec-durasi .grid {
      align-items: stretch;
    }
    #sec-durasi .grid > div {
      min-width: 0;
    }

    #sec-pembayaran > div:first-child {
      padding: clamp(1.35rem, 2.8vw, 2.15rem);
      text-align: center;
    }
    #sec-pembayaran > div:first-child > div {
      justify-content: center;
    }
    #sec-pembayaran > div:first-child > div > div {
      text-align: center;
    }
    #sec-pembayaran > div:first-child > div > span {
      flex: 0 0 auto;
    }
    #sec-pembayaran > div:last-child {
      display: grid;
      gap: clamp(1.25rem, 2.5vw, 1.8rem);
    }
    #sec-pembayaran #transfer-method-container,
    #sec-pembayaran #cod-method-container {
      min-width: 0;
    }

    /* Opsi pembayaran dan area upload lebih proporsional */
    #sec-pembayaran .payment-option {
      min-height: 104px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    #sec-pembayaran .border-dashed {
      min-height: 180px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    #sec-pembayaran #qris-display > div,
    #sec-pembayaran #bank-transfer-card > div {
      min-width: 0;
    }
    #sec-pembayaran #bank-transfer-card span,
    #sec-pembayaran #qris-display p {
      overflow-wrap: anywhere;
    }

    /* Ringkasan booking dibuat seperti panel terpisah */
    #booking-form > .grid > aside > div {
      position: relative;
      border-radius: 1.8rem;
    }
    #booking-form > .grid > aside > div > div {
      border-radius: 1.5rem;
    }
    #booking-form > .grid > aside .relative.h-52 {
      height: clamp(190px, 19vw, 245px);
    }
    #booking-form > .grid > aside .p-5.sm\:p-6 {
      padding: clamp(1.15rem, 2.2vw, 1.65rem);
    }
    #summary-grand-total {
      overflow-wrap: anywhere;
    }

    /* Modal tetap nyaman dibaca pada layar sempit */
    #modal-review-booking > div,
    #modal-failed-booking > div {
      width: min(100%, 540px);
      border-radius: 1.65rem;
    }

    /* Tablet: ringkasan pindah di bawah form agar lebar konten lega */
    @media (max-width: 1023px) {
      main {
        max-width: 820px;
      }
      #booking-form > .grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 1.5rem;
      }
      #booking-form > .grid > aside {
        position: static;
        width: min(100%, 620px);
        margin-inline: auto;
      }
      #booking-form > .grid > div:first-child {
        gap: 1.35rem;
      }
      .booking-stepper-sticky {
        top: 64px;
      }
    }

    /* Smartphone: ringkas tanpa mengubah urutan maupun isi form */
    @media (max-width: 640px) {
      main {
        padding-inline: 1rem;
        padding-top: 1.25rem;
      }
      main > nav[aria-label="Breadcrumb"] {
        justify-content: flex-start;
        margin-bottom: 1.6rem;
      }
      main > nav[aria-label="Breadcrumb"] > div {
        justify-content: flex-start;
        border-radius: 1rem;
        line-height: 1.7;
        padding: .65rem .85rem;
        text-align: left;
      }
      main > header {
        align-items: flex-start;
        text-align: left;
        margin-bottom: 1.8rem;
        gap: 1rem;
      }
      main > header > div:first-child {
        align-items: flex-start;
      }
      main > header h1,
      main > header p {
        margin-inline: 0;
      }
      main > header h1 {
        max-width: 14ch;
        font-size: clamp(1.8rem, 8vw, 2.5rem);
      }
      main > header > div:last-child {
        align-self: flex-start;
        min-width: 0;
      }
      .booking-stepper-sticky {
        padding-block: .65rem;
      }
      .booking-stepper-sticky > div {
        padding-inline: .8rem;
      }
      .booking-stepper-sticky > div > div {
        justify-content: space-between;
        gap: .35rem;
      }
      .booking-stepper-sticky .step-btn {
        gap: .35rem;
      }
      .booking-stepper-sticky .step-btn > span:first-child {
        width: 2rem;
        height: 2rem;
        flex-basis: 2rem;
        border-radius: .8rem;
      }
      .booking-stepper-sticky .flex-1 {
        flex: 1 1 12px;
        max-width: none;
      }
      #sec-data-diri,
      #sec-durasi,
      #sec-pembayaran {
        border-radius: 1.35rem;
        scroll-margin-top: 118px;
      }
      #sec-data-diri > .bg-\[\#3B2314\] > div {
        justify-content: flex-start;
        text-align: left;
      }
      #sec-data-diri > .bg-\[\#3B2314\] > div > div {
        text-align: left;
      }
      #sec-durasi > div:first-child {
        align-items: flex-start;
        text-align: left;
      }
      #sec-pembayaran > div:first-child {
        text-align: left;
      }
      #sec-pembayaran > div:first-child > div {
        justify-content: space-between;
      }
      #sec-pembayaran > div:first-child > div > div {
        text-align: left;
      }
      #sec-pembayaran .payment-option {
        min-height: 88px;
      }
      #sec-pembayaran #qris-display,
      #sec-pembayaran #bank-transfer-card {
        padding: 1.15rem;
        border-radius: 1.35rem;
      }
      #booking-form > .grid > aside > div {
        padding: .4rem;
        border-radius: 1.5rem;
      }
      #booking-form > .grid > aside > div > div {
        border-radius: 1.2rem;
      }
      #booking-form > .grid > aside .relative.h-52 {
        height: 190px;
      }
      #modal-review-booking {
        align-items: flex-end;
        padding: .55rem;
      }
      #modal-review-booking > div {
        max-height: calc(100dvh - 1.1rem);
        border-radius: 1.4rem;
        margin-block: auto;
      }
      body {
        padding-bottom: calc(4.8rem + env(safe-area-inset-bottom));
      }
    }

    @media (max-width: 380px) {
      .booking-stepper-sticky .step-btn > span:first-child {
        width: 1.8rem;
        height: 1.8rem;
        flex-basis: 1.8rem;
      }
      .booking-stepper-sticky > div {
        padding-inline: .55rem;
      }
      #sec-data-diri > div:last-child,
      #sec-pembayaran > div:last-child,
      #sec-durasi {
        padding-inline: 1rem;
      }
      #booking-form > .grid > aside .p-5.sm\:p-6 {
        padding: 1rem;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .booking-card,
      .booking-stepper-sticky,
      .payment-option {
        scroll-behavior: auto !important;
        transition-duration: .01ms !important;
      }
    }

  </style>
</head>

<body class="bg-[#FFF8F1] text-[#3B2314] font-sans antialiased overflow-x-clip min-h-screen flex flex-col justify-between">

  <!-- 1. BAR LOADING -->
  <div id="page-loader"
       class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-[#3B2314] via-[#D99AA8] to-[#C9A45C] z-[100] transition-all duration-500 ease-out">
  </div>

  <!-- 2. NAVBAR -->
  @include('frontend.navbar')

  <!-- 3. STICKY STEPPER NAVIGASI -->
  <div class="booking-stepper-sticky sticky top-[64px] sm:top-[80px] z-30 bg-[#FFF8F1]/95 backdrop-blur-md border-b border-[#E9DDD2] py-3 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between gap-2">

        <button type="button" onclick="scrollToSection('sec-data-diri')" id="step-btn-1"
          class="step-btn flex items-center gap-2 text-left transition-all shrink-0">
          <span class="w-9 h-9 rounded-2xl bg-[#D99AA8] text-[#FFF8F1] flex items-center justify-center text-xs font-black shadow-sm">1</span>
          <span class="hidden sm:block">
            <span class="block text-[10px] uppercase tracking-wider font-black text-[#D99AA8]">Langkah 01</span>
            <span class="block text-xs font-extrabold text-[#3B2314]">Informasi Penyewa</span>
          </span>
        </button>

        <div class="flex-1 h-px bg-[#E9DDD2] max-w-[70px]"></div>

        <button type="button" onclick="scrollToSection('sec-durasi')" id="step-btn-2"
          class="step-btn flex items-center gap-2 text-left transition-all shrink-0">
          <span class="w-9 h-9 rounded-2xl bg-[#E9DDD2] text-[#8E7B6D] flex items-center justify-center text-xs font-black">2</span>
          <span class="hidden sm:block">
            <span class="block text-[10px] uppercase tracking-wider font-black text-[#8E7B6D]">Langkah 02</span>
            <span class="block text-xs font-bold text-[#8E7B6D]">Durasi & Tanggal</span>
          </span>
        </button>

        <div class="flex-1 h-px bg-[#E9DDD2] max-w-[70px]"></div>

        <button type="button" onclick="scrollToSection('sec-pembayaran')" id="step-btn-3"
          class="step-btn flex items-center gap-2 text-left transition-all shrink-0">
          <span class="w-9 h-9 rounded-2xl bg-[#E9DDD2] text-[#8E7B6D] flex items-center justify-center text-xs font-black">3</span>
          <span class="hidden sm:block">
            <span class="block text-[10px] uppercase tracking-wider font-black text-[#8E7B6D]">Langkah 03</span>
            <span class="block text-xs font-bold text-[#8E7B6D]">Pembayaran</span>
          </span>
        </button>

      </div>
    </div>
  </div>

  <!-- MAIN CONTENT CONTAINER -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 sm:py-8 flex-grow w-full">

    @php
      $namaKosan = $kamar->productKosan->title ?? 'Jumala Living';
      $bookedRanges = $bookedRanges ?? [];
      $latestBookedEnd = $latestBookedEnd ?? null;
      $isTodayBooked = $isTodayBooked ?? false;
      $firstAvailableDate = $firstAvailableDate ?? now()->format('Y-m-d');
    @endphp

    <!-- BREADCRUMB -->
    <nav class="reveal mb-7" aria-label="Breadcrumb">
      <div class="inline-flex flex-wrap items-center gap-2 bg-[#FFFCF8]/90 border border-[#E9DDD2] rounded-full px-4 py-2.5 shadow-sm text-[11px] sm:text-xs">
        <a href="{{ route('kosan.index') }}" class="font-bold hover:text-[#D99AA8] transition-colors">Kos-Kosan</a>
        <i class="fa-solid fa-minus text-[#C9A45C] text-[9px]"></i>
        @if($kamar->productKosan)
          <a href="{{ route('kosan.detail', $kamar->productKosan->slug) }}" class="font-semibold text-[#C9A45C] hover:text-[#D99AA8] transition-colors">{{ $namaKosan }}</a>
          <i class="fa-solid fa-minus text-[#C9A45C] text-[9px]"></i>
        @endif
        <a href="{{ route('kamar.detail', $kamar->id) }}" class="font-semibold hover:text-[#D99AA8] transition-colors">{{ $kamar->room }}</a>
        <i class="fa-solid fa-minus text-[#C9A45C] text-[9px]"></i>
        <span class="font-black">Booking Kamar</span>
      </div>
    </nav>

    <!-- HEADER TITLE -->
    <header class="reveal mb-10 grid lg:grid-cols-[1fr_auto] gap-6 items-end">
      <div>
        <span class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[.2em] font-black text-[#D99AA8]">
          <span class="w-8 h-8 rounded-xl bg-[#D99AA8]/10 flex items-center justify-center">
            <i class="fa-solid fa-key"></i>
          </span>
          Reservasi Kamar
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight mt-4 text-[#3B2314]">
          Amankan kamar pilihanmu.
        </h1>
        <p class="text-sm sm:text-base text-[#8E7B6D] mt-3 max-w-2xl leading-7">
          Isi informasi penyewa, tentukan durasi, lalu pilih metode pembayaran. Ringkasan biaya akan diperbarui otomatis.
        </p>
      </div>

      <div class="hidden sm:flex items-center gap-3 rounded-2xl bg-[#3B2314] text-[#FFF8F1] px-5 py-4 shadow-lg">
        <span class="w-10 h-10 rounded-xl bg-[#C9A45C] text-[#3B2314] flex items-center justify-center">
          <i class="fa-solid fa-house"></i>
        </span>
        <div>
          <p class="text-[10px] uppercase tracking-wider text-[#FFF8F1]/50 font-bold">Kamar dipilih</p>
          <p class="text-sm font-black">{{ $kamar->room }}</p>
        </div>
      </div>
    </header>

    <!-- FORM UTAMA -->
    <form id="booking-form" action="{{ route('tamu.booking', $kamar->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="total_price" id="input-hidden-total-price" value="0">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 lg:gap-10 items-start">

        <!-- KOLOM FORM (3 SECTION) -->
        <div class="lg:col-span-8 space-y-7">

          <!-- SECTION 1: DATA DIRI -->
          <section id="sec-data-diri" class="booking-card rounded-[2rem] overflow-hidden reveal">
            <div class="bg-[#3B2314] text-[#FFF8F1] p-6 sm:p-8">
              <div class="flex items-center gap-4">
                <span class="w-12 h-12 rounded-2xl bg-[#C9A45C] text-[#3B2314] flex items-center justify-center font-black text-lg">1</span>
                <div>
                  <p class="text-[10px] uppercase tracking-[.18em] font-black text-[#C9A45C]">Data utama</p>
                  <h2 class="text-xl sm:text-2xl font-black">Informasi Penyewa</h2>
                </div>
              </div>
            </div>

            <div class="p-6 sm:p-8 space-y-5">
              <div>
                <label class="block text-xs font-bold text-[#765C55] mb-2">Tipe Unit Kamar Terpilih</label>
                <div class="flex items-center gap-3 rounded-2xl bg-[#FFF8F1] border border-[#E9DDD2] px-4 py-3">
                  <i class="fa-solid fa-bed text-[#D99AA8]"></i>
                  <input type="text" value="{{ $kamar->room }} - {{ $namaKosan }}" readonly
                    class="w-full bg-transparent font-bold text-[#3B2314] focus:outline-none cursor-not-allowed">
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label class="block text-xs font-bold text-[#765C55] mb-2">Nama Lengkap (sesuai KTP) <span class="text-[#D99AA8]">*</span></label>
                  <input type="text" name="name" id="input-nama" required placeholder="Contoh: Budi Santoso"
                    class="input-scl w-full px-4 py-3.5 bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl font-medium text-[#3B2314]">
                </div>

                <div>
                  <label class="block text-xs font-bold text-[#765C55] mb-2">Nomor WhatsApp / HP <span class="text-[#D99AA8]">*</span></label>
                  <input type="tel" name="telp" id="input-wa" required placeholder="08123456XXXX"
                    class="input-scl w-full px-4 py-3.5 bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl font-medium text-[#3B2314]">
                  <p class="text-[10px] text-[#8E7B6D] mt-2">Gunakan format biasa, misalnya <code>081234567890</code>.</p>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-[#765C55] mb-2">Email Aktif (untuk penerimaan PDF Booking) <span class="text-[#D99AA8]">*</span></label>
                <input type="email" name="email" id="input-email" required placeholder="budi@example.com"
                  class="input-scl w-full px-4 py-3.5 bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl font-medium text-[#3B2314]">
                <p class="text-[10px] text-[#8E7B6D] mt-2">E-tiket dan invoice PDF akan dikirimkan otomatis ke alamat email ini.</p>
              </div>
            </div>
          </section>

          <!-- SECTION 2: DURASI & TANGGAL -->
          <section id="sec-durasi" class="booking-card rounded-[2rem] p-6 sm:p-8 reveal">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-7">
              <div>
                <span class="text-[10px] uppercase tracking-[.18em] font-black text-[#C9A45C]">Langkah berikutnya</span>
                <h2 class="text-2xl sm:text-3xl font-black mt-2">Durasi & tanggal masuk</h2>
              </div>
              <span class="w-11 h-11 rounded-2xl bg-[#C9A45C]/10 text-[#C9A45C] flex items-center justify-center">
                <i class="fa-solid fa-calendar-days"></i>
              </span>
            </div>

            <div class="space-y-6 text-xs sm:text-sm">
              <div>
                <span class="block font-bold text-[#765C55] mb-3">Pilih Durasi Sewa <span class="text-[#D99AA8]">*</span></span>

                <input type="hidden" id="durasi-jam" name="jam" value="0">
                <input type="hidden" id="durasi-hari" name="hari" value="0">
                <input type="hidden" id="durasi-minggu" name="minggu" value="0">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="rounded-2xl border-2 border-[#C9A45C] bg-[#FFF8F1] p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                      <label for="durasi-bulan" class="font-black text-[#3B2314] cursor-pointer flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-[#C9A45C]/20 flex items-center justify-center">
                          <i class="fa-solid fa-calendar text-[#3B2314]"></i>
                        </span>
                        Durasi Bulanan
                      </label>
                      <span class="text-[9px] font-black text-[#3B2314] bg-[#C9A45C]/30 px-2 py-1 rounded-full">PER BULAN</span>
                    </div>
                    <div class="relative">
                      <input type="number" id="durasi-bulan" name="bulan" value="1" min="0"
                        class="input-scl w-full pl-4 pr-16 py-3 bg-[#FFFCF8] border border-[#E9DDD2] rounded-xl font-black text-lg text-[#3B2314]">
                      <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-[#8E7B6D] pointer-events-none">Bulan</span>
                    </div>
                  </div>

                  @if($kamar->yearly_price)
                  <div class="rounded-2xl border border-[#E9DDD2] bg-[#FFFCF8] p-4 hover:border-[#C9A45C] transition-all">
                    <div class="flex items-center justify-between mb-3">
                      <label for="durasi-tahun" class="font-black text-[#3B2314] cursor-pointer flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-[#C9A45C]/10 flex items-center justify-center">
                          <i class="fa-solid fa-calendar-days text-[#C9A45C]"></i>
                        </span>
                        Durasi Tahunan
                      </label>
                      <span class="text-[9px] font-black text-[#C9A45C] bg-[#C9A45C]/10 px-2 py-1 rounded-full">PER TAHUN</span>
                    </div>
                    <div class="relative">
                      <input type="number" id="durasi-tahun" name="tahun" value="0" min="0"
                        class="input-scl w-full pl-4 pr-16 py-3 bg-[#FFFCF8] border border-[#E9DDD2] rounded-xl font-black text-lg text-[#3B2314]">
                      <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-[#8E7B6D] pointer-events-none">Tahun</span>
                    </div>
                  </div>
                  @else
                  <input type="hidden" id="durasi-tahun" name="tahun" value="0">
                  @endif
                </div>

                <p class="mt-3 text-[10px] text-[#8E7B6D] flex items-start gap-2">
                  <i class="fa-solid fa-circle-info text-[#D99AA8] mt-0.5"></i>
                  @if($kamar->yearly_price)
                  <span>Sewa kos ini tersedia pilihan bulanan atau tahunan. Masukkan durasi yang diinginkan (minimal 1 bulan atau 1 tahun).</span>
                  @else
                  <span>Sewa kos ini khusus bulanan (Tarif tahunan belum dibuka untuk unit ini). Masukkan durasi sewa bulanan (minimal 1 bulan).</span>
                  @endif
                </p>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-[#765C55]">Tanggal Mulai Masuk <span class="text-[#D99AA8]">*</span></label>
                    @if($isTodayBooked && $latestBookedEnd)
                      <span class="text-[10px] font-extrabold text-[#D99AA8] bg-[#D99AA8]/10 border border-[#D99AA8]/80 px-2 py-0.5 rounded-md">
                        Ter-booking s/d {{ \Carbon\Carbon::parse($latestBookedEnd)->locale('id')->isoFormat('D MMM Y') }}
                      </span>
                    @endif
                  </div>

                  @if($isTodayBooked && $latestBookedEnd)
                    <div class="mb-3 p-3.5 rounded-2xl bg-[#FFF8F1] border-2 border-[#C9A45C] shadow-sm flex items-start gap-3 text-xs text-[#3B2314] leading-relaxed">
                      <span class="w-8 h-8 rounded-xl bg-[#C9A45C]/20 text-[#C9A45C] flex items-center justify-center shrink-0 text-sm mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                      </span>
                      <div class="flex-1">
                        <span class="font-black text-[#3B2314] block">Kamar sedang terisi saat ini.</span>
                        <p class="text-[11px] text-[#8E7B6D] mt-0.5">
                          Tersedia mulai: <strong class="text-[#D99AA8]">{{ \Carbon\Carbon::parse($firstAvailableDate)->locale('id')->isoFormat('D MMMM Y') }}</strong>
                        </p>
                        <button
                          type="button"
                          onclick="selectQuickDate('{{ $firstAvailableDate }}')"
                          style="background-color: #D99AA8; color: #FFFCF8;"
                          class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-2 hover:bg-[#D99AA8] text-[#FFF8F1] rounded-xl font-black text-xs shadow-md transition-all active:scale-95 cursor-pointer"
                        >
                          <i class="fa-solid fa-calendar-check text-[#C9A45C]"></i>
                          <span>Pilih Cepat ({{ \Carbon\Carbon::parse($firstAvailableDate)->locale('id')->isoFormat('D MMMM Y') }})</span>
                        </button>
                      </div>
                    </div>
                  @endif

                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#D99AA8] z-10">
                      <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <input type="text" id="input-tanggal" name="start_date" required placeholder="Pilih tanggal mulai masuk..."
                      class="input-scl w-full pl-11 pr-10 py-3.5 bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl font-bold text-[#3B2314] cursor-pointer">
                    <button type="button" onclick="if(fpInstance) fpInstance.toggle()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#8E7B6D] hover:text-[#D99AA8] transition-colors cursor-pointer z-10" title="Buka/Tutup Kalender">
                      <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                  </div>

                  <!-- Legend Status Kalender -->
                  <div class="mt-2.5 flex items-center gap-3 text-[10px] text-[#8E7B6D]">
                    <span class="flex items-center gap-1.5 font-bold">
                      <span class="w-2 h-2 rounded-full bg-[#C9A45C]"></span>
                      <span>Bisa Dipilih</span>
                    </span>
                    <span class="flex items-center gap-1.5 font-bold text-[#3B2314]">
                      <span class="w-2 h-2 rounded-full bg-[#D99AA8]"></span>
                      <span>Terisi / Booked</span>
                    </span>
                    <span class="text-[#8E7B6D] ml-auto">Bisa digeser ↔</span>
                  </div>
                </div>

                <div>
                  <label class="block font-bold text-[#765C55] mb-2">Perkiraan Jam Check-in <span class="text-[#D99AA8]">*</span></label>
                  <div class="relative">
                    <i class="fa-solid fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-[#C9A45C] pointer-events-none"></i>
                    <input type="time" id="input-jam" name="start_time" value="13:00" required
                      class="input-scl w-full pl-11 pr-4 py-3.5 bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl font-medium text-[#3B2314]">
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- SECTION 3: PEMBAYARAN -->
          @php
            $methodsList = isset($paymentMethods) && $paymentMethods->count() > 0 
                ? $paymentMethods 
                : \App\Models\PaymentMethod::where('is_active', true)->orderBy('sort_order', 'asc')->get();

            if ($methodsList->isEmpty()) {
                $methodsList = collect([
                    (object)['code' => 'qris', 'name' => 'QRIS (Semua Pembayaran)', 'type' => 'qris', 'bank_code' => 'QRIS', 'account_number' => null, 'account_holder' => 'Jumala Living', 'qr_image_url' => null, 'instructions' => 'Buka aplikasi m-Banking atau e-Wallet favorit Anda, pilih menu QRIS / Scan.', 'is_active' => true],
                    (object)['code' => 'bca', 'name' => 'Bank Central Asia (BCA)', 'type' => 'bank', 'bank_code' => 'BCA', 'account_number' => '1234567890', 'account_holder' => 'Jumala Living', 'qr_image_url' => null, 'instructions' => 'Transfer tepat sesuai nominal tagihan ke rekening BCA kami.', 'is_active' => true],
                    (object)['code' => 'cash', 'name' => 'Bayar di Tempat (COD)', 'type' => 'cash', 'bank_code' => 'COD', 'account_number' => null, 'account_holder' => null, 'qr_image_url' => null, 'instructions' => 'Pembayaran dilakukan secara langsung kepada pengelola kos saat serah terima kunci di lokasi.', 'is_active' => true],
                ]);
            }

            $onlineMethods = $methodsList->filter(fn($m) => $m->type !== 'cash');
            $cashMethod = $methodsList->firstWhere('type', 'cash');
            $hasOnline = $onlineMethods->isNotEmpty();
            $hasCash = !is_null($cashMethod);

            $firstOnline = $onlineMethods->first();
            $defaultMethod = $hasOnline ? $firstOnline : $cashMethod;
            $defaultPaymentType = $hasOnline ? 'transfer' : 'cod';
            $defaultCode = $defaultMethod ? $defaultMethod->code : 'transfer';
          @endphp

          <section id="sec-pembayaran" class="booking-card rounded-[2rem] overflow-hidden reveal">
            <div class="p-6 sm:p-8 border-b border-[#E9DDD2]">
              <span class="text-[10px] uppercase tracking-[.18em] font-black text-[#D99AA8]">Langkah terakhir</span>
              <div class="flex items-center justify-between gap-4 mt-2">
                <div>
                  <h2 class="text-2xl sm:text-3xl font-black">Opsi & metode pembayaran</h2>
                  <p class="text-xs text-[#8E7B6D] mt-2">Pilih cara pembayaran yang paling sesuai.</p>
                </div>
                <span class="hidden sm:flex w-12 h-12 rounded-2xl bg-[#D99AA8]/10 text-[#D99AA8] items-center justify-center">
                  <i class="fa-solid fa-wallet"></i>
                </span>
              </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
              <!-- PILIHAN TIPE PEMBAYARAN (ONLINE VS COD) -->
              @if($hasOnline && $hasCash)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <button type="button" onclick="selectPaymentOption('transfer')" id="opt-transfer"
                    class="payment-option p-5 rounded-2xl border-2 border-[#D99AA8] bg-[#D99AA8]/5 text-left shadow-sm">
                    <span class="flex items-center justify-between gap-3">
                      <span class="font-black text-sm text-[#3B2314]">TRANSFER ONLINE / E-WALLET</span>
                      <i class="fa-solid fa-circle-check text-[#D99AA8]"></i>
                    </span>
                    <span class="block text-[11px] text-[#8E7B6D] mt-2">QRIS / Rekening Bank / E-Wallet</span>
                  </button>

                  <button type="button" onclick="selectPaymentOption('cod')" id="opt-cod"
                    class="payment-option p-5 rounded-2xl border border-[#E9DDD2] bg-[#FFFCF8] text-left">
                    <span class="flex items-center justify-between gap-3">
                      <span class="font-black text-sm text-[#3B2314]">BAYAR DI TEMPAT (COD)</span>
                      <i class="fa-regular fa-circle text-[#8E7B6D]"></i>
                    </span>
                    <span class="block text-[11px] text-[#8E7B6D] mt-2">Bayar saat check-in di kos</span>
                  </button>
                </div>
              @elseif($hasOnline && !$hasCash)
                <div class="p-4 rounded-2xl bg-[#D99AA8]/5 border border-[#D99AA8]/20 flex items-center justify-between">
                  <div>
                    <span class="font-black text-sm text-[#3B2314] block">TRANSFER ONLINE / E-WALLET</span>
                    <span class="text-[11px] text-[#8E7B6D]">Selesaikan pembayaran via QRIS, Rekening Bank, atau E-Wallet di bawah.</span>
                  </div>
                  <i class="fa-solid fa-circle-check text-[#D99AA8] text-lg"></i>
                </div>
              @elseif(!$hasOnline && $hasCash)
                <div class="p-4 rounded-2xl bg-[#C9A45C]/10 border border-[#C9A45C]/30 flex items-center justify-between">
                  <div>
                    <span class="font-black text-sm text-[#3B2314] block">BAYAR DI TEMPAT (TUNAI / COD)</span>
                    <span class="text-[11px] text-[#8E7B6D]">Pembayaran dilakukan langsung saat serah terima kunci kamar.</span>
                  </div>
                  <i class="fa-solid fa-circle-check text-[#C9A45C] text-lg"></i>
                </div>
              @endif

              <input type="hidden" name="payment_method" id="input-payment-method" value="{{ $defaultCode }}">

              <!-- CONTAINER TRANSFER ONLINE (BANK & QRIS) -->
              <div id="transfer-method-container" class="{{ $defaultPaymentType === 'transfer' ? '' : 'hidden' }} space-y-5">
                @if($hasOnline)
                  <div>
                    <label class="block font-bold text-xs sm:text-sm text-[#765C55] mb-2">Pilih Rekening / Saluran Pembayaran</label>
                    <select id="select-bank" onchange="updateSelectedBank(this.value); toggleQrisDisplay();"
                      class="input-scl w-full px-4 py-3.5 bg-[#FFFCF8] border border-[#E9DDD2] rounded-2xl font-medium text-[#3B2314]">
                      @foreach($onlineMethods as $m)
                        <option value="{{ $m->code }}" {{ $m->code === $defaultCode ? 'selected' : '' }}>
                          {{ $m->name }} @if($m->account_number) - {{ $m->account_number }} @endif @if($m->account_holder) (a/n {{ $m->account_holder }}) @endif
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <!-- BLOK QRIS DISPLAY -->
                  <div id="qris-display" class="{{ $defaultMethod && $defaultMethod->type === 'qris' ? '' : 'hidden' }} rounded-3xl bg-[#3B2314] p-5 sm:p-7 text-center text-[#FFF8F1]">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                      <div class="shrink-0">
                        <p class="text-[10px] uppercase tracking-widest text-[#C9A45C] font-black mb-3">Pembayaran Barcode QRIS</p>
                        <!-- Wadah gambar QRIS dengan proteksi distorsi rasio (object-contain aspect-square) -->
                        <div class="p-3 bg-[#FFFCF8] rounded-2xl shadow-xl aspect-square w-44 h-44 sm:w-52 sm:h-52 flex items-center justify-center mx-auto overflow-hidden">
                          @php
                            $initQrSrc = ($defaultMethod && $defaultMethod->type === 'qris' && $defaultMethod->qr_image_url) 
                                ? $defaultMethod->qr_image_url 
                                : ($defaultMethod ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=SCL-' . $defaultMethod->code : 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=SCL-Sewa-Kosan');
                          @endphp
                          <img id="qris-image-el" src="{{ $initQrSrc }}"
                            alt="Kode QRIS Pembayaran" class="w-full h-full object-contain">
                        </div>
                        <div class="mt-3 flex items-center justify-center gap-2">
                          <a id="btn-download-qris" href="{{ $initQrSrc }}" download="QRIS-Pembayaran-Kosan.png" target="_blank"
                             class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#FFFCF8]/10 hover:bg-[#FFFCF8]/20 text-[10px] font-bold text-[#FFF8F1] transition-all active:scale-95">
                            <i class="fa-solid fa-download text-[9px]"></i>
                            <span>Simpan QRIS ke Galeri</span>
                          </a>
                        </div>
                      </div>
                      <div class="text-left space-y-2">
                        <h3 class="text-xl font-black text-[#FFF8F1]" id="qris-title-label">
                          {{ $defaultMethod && $defaultMethod->type === 'qris' ? 'Scan & bayar via ' . $defaultMethod->name : 'Scan & selesaikan pembayaran' }}
                        </h3>
                        <p class="text-xs leading-6 text-[#FFF8F1]/70" id="qris-instructions-label">
                          {{ ($defaultMethod && $defaultMethod->instructions) ? $defaultMethod->instructions : 'Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, DANA, ShopeePay), pilih menu QRIS / Scan, lalu bayar sesuai total tagihan.' }}
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-2">
                          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#C9A45C]/15 text-[#C9A45C] text-[10px] font-bold">
                            <i class="fa-solid fa-shield-halved"></i> Pembayaran QRIS Resmi Terverifikasi
                          </span>
                          <span class="text-[10px] text-[#FFF8F1]/50">Mendukung semua bank & e-wallet</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- BLOK BANK / E-WALLET TRANSFER CARD -->
                  <div id="bank-transfer-card" class="{{ $defaultMethod && $defaultMethod->type !== 'qris' ? '' : 'hidden' }} rounded-3xl bg-[#3B2314] p-5 sm:p-7 text-[#FFF8F1] space-y-4 shadow-lg border border-[#FFF8F1]/10">
                    <div class="flex items-center justify-between border-b border-[#FFF8F1]/10 pb-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#FFFCF8] text-[#3B2314] font-black flex items-center justify-center text-xs tracking-wider uppercase shadow-md" id="bank-badge-name">
                          {{ $defaultMethod ? ($defaultMethod->bank_code ?: strtoupper(substr($defaultMethod->code, 0, 3))) : 'BANK' }}
                        </div>
                        <div>
                          <h3 class="text-sm sm:text-base font-black text-[#FFF8F1]" id="bank-name-label">
                            {{ $defaultMethod ? 'Transfer ' . $defaultMethod->name : 'Transfer Bank' }}
                          </h3>
                          <p class="text-[11px] text-[#FFF8F1]/60">Verifikasi manual via bukti resi transfer</p>
                        </div>
                      </div>
                      <span class="px-2.5 py-1 rounded-full bg-[#C9A45C]/20 text-[#C9A45C] text-[10px] font-bold">
                        <i class="fa-solid fa-circle-check mr-1"></i> Aktif
                      </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div class="bg-[#FFFCF8]/5 border border-[#FFF8F1]/10 rounded-2xl p-4">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#FFF8F1]/50 block">Nomor Rekening / Akun Tujuan</span>
                        <div class="flex items-center justify-between gap-2 mt-1">
                          <span id="bank-account-number" class="text-base sm:text-lg font-black tracking-wider text-[#C9A45C]">
                            {{ $defaultMethod->account_number ?? '-' }}
                          </span>
                          <button
                            type="button"
                            id="btn-copy-rekening"
                            onclick="copyToClipboard(document.getElementById('bank-account-number').textContent, 'btn-copy-rekening', 'Tersalin! ✅')"
                            class="px-2.5 py-1 rounded-lg bg-[#FFFCF8]/10 hover:bg-[#FFFCF8]/20 text-[11px] font-bold text-[#FFF8F1] transition-all active:scale-95 flex items-center gap-1 shrink-0"
                            title="Salin Nomor Rekening"
                          >
                            <i class="fa-regular fa-copy text-[10px]"></i>
                            <span>Salin</span>
                          </button>
                        </div>
                        <span id="bank-account-holder" class="text-[10px] text-[#FFF8F1]/60 block mt-1">
                          {{ $defaultMethod && $defaultMethod->account_holder ? 'a/n ' . $defaultMethod->account_holder : 'a/n Pengelola Kos' }}
                        </span>
                      </div>

                      <div class="bg-[#FFFCF8]/5 border border-[#FFF8F1]/10 rounded-2xl p-4">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#FFF8F1]/50 block">Nominal Harus Ditransfer</span>
                        <div class="flex items-center justify-between gap-2 mt-1">
                          <span id="bank-nominal-transfer" class="text-base sm:text-lg font-black text-[#C9A45C]">Rp 0</span>
                          <button
                            type="button"
                            id="btn-copy-nominal"
                            onclick="copyNominalTransfer()"
                            class="px-2.5 py-1 rounded-lg bg-[#FFFCF8]/10 hover:bg-[#FFFCF8]/20 text-[11px] font-bold text-[#FFF8F1] transition-all active:scale-95 flex items-center gap-1 shrink-0"
                            title="Salin Nominal Transfer"
                          >
                            <i class="fa-regular fa-copy text-[10px]"></i>
                            <span>Salin</span>
                          </button>
                        </div>
                        <span class="text-[10px] text-[#FFF8F1]/60 block mt-1">Transfer tepat sesuai angka di atas</span>
                      </div>
                    </div>

                    <div id="bank-instructions-box" class="p-3 bg-[#FFFCF8]/5 rounded-2xl border border-[#FFF8F1]/10 flex items-start gap-2.5 text-[11px] text-[#FFF8F1]/70">
                      <i class="fa-solid fa-circle-info text-[#C9A45C] mt-0.5 shrink-0"></i>
                      <span id="bank-instructions-text">
                        {{ ($defaultMethod && $defaultMethod->instructions) ? $defaultMethod->instructions : 'Setelah transfer berhasil, simpan dan lampirkan bukti transfer/struk di bawah ini untuk verifikasi admin yang lebih cepat.' }}
                      </span>
                    </div>

                    <!-- OPSIONAL QR CODE PADA E-WALLET / REKENING BANK -->
                    <div id="bank-optional-qr-container" class="{{ ($defaultMethod && $defaultMethod->qr_image_url && $defaultMethod->type !== 'qris') ? '' : 'hidden' }} p-3 bg-[#FFFCF8]/5 rounded-2xl border border-[#FFF8F1]/10 text-center">
                      <span class="text-[10px] text-[#FFF8F1]/60 block mb-2">Atau scan barcode di bawah:</span>
                      <div class="p-2 bg-[#FFFCF8] rounded-xl aspect-square w-36 h-36 mx-auto flex items-center justify-center">
                        <img id="bank-optional-qr-img" src="{{ $defaultMethod->qr_image_url ?? '' }}" alt="QR" class="w-full h-full object-contain">
                      </div>
                    </div>
                  </div>

                  <!-- UPLOAD BUKTI TRANSFER -->
                  <div>
                    <label class="block font-bold text-xs sm:text-sm text-[#765C55] mb-2">Unggah Bukti Transfer / Resi <span class="text-[#D99AA8]">*</span></label>
                    <div onclick="document.getElementById('file-bukti').click()"
                      class="border-2 border-dashed border-[#E9DDD2] hover:border-[#D99AA8] rounded-3xl p-7 text-center bg-[#FFF8F1]/70 hover:bg-[#D99AA8]/5 transition-all cursor-pointer group">
                      <input type="file" id="file-bukti" name="proof_of_transfer" accept="image/*" class="hidden" onchange="handleFileUpload(event)">

                      <div id="upload-placeholder" class="space-y-2">
                        <span class="mx-auto w-14 h-14 rounded-2xl bg-[#C9A45C]/15 text-[#3B2314] flex items-center justify-center">
                          <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </span>
                        <p class="text-sm font-black text-[#3B2314]">Klik untuk upload bukti pembayaran</p>
                        <p class="text-[10px] text-[#8E7B6D]">JPG, PNG, WEBP · Maksimal 5MB</p>
                      </div>

                      <div id="upload-preview" class="hidden flex-col items-center justify-center space-y-2">
                        <img id="preview-img" src="" alt="Bukti Transfer" class="h-32 rounded-xl object-contain border border-[#E9DDD2] shadow-sm">
                        <span id="preview-filename" class="text-xs font-semibold text-[#3B2314] truncate max-w-full block"></span>
                        <span class="text-[10px] text-[#C9A45C] font-bold bg-[#C9A45C]/10 px-3 py-1 rounded-full">
                          <i class="fa-solid fa-check mr-1"></i> File Siap Diunggah
                        </span>
                      </div>
                    </div>
                  </div>
                @endif
              </div>

              <!-- CONTAINER INFORMASI COD / TUNAI -->
              <div id="cod-method-container" class="{{ $defaultPaymentType === 'cod' ? '' : 'hidden' }} rounded-3xl bg-[#FFF8F1] border border-[#C9A45C]/50 p-5 text-[#C9A45C] text-xs leading-relaxed">
                <span class="font-black block mb-2">
                  <i class="fa-solid fa-circle-info text-[#D99AA8] mr-1"></i> Informasi Pembayaran Tunai (Bayar di Tempat)
                </span>
                <p id="cod-instructions-text">
                  {{ $cashMethod->instructions ?? 'Pembayaran dilakukan secara langsung kepada pengelola kos saat serah terima kunci di lokasi. Pastikan nomor kontak WhatsApp aktif untuk konfirmasi jadwal kedatangan.' }}
                </p>
              </div>
            </div>
          </section>

        </div>

        <!-- KOLOM SIDEBAR RINGKASAN -->
        <aside class="lg:col-span-4 lg:sticky lg:top-[140px] reveal">
          @php
            $imgKamar = $kamar->productKamarImageKosan->first();
            $previewKamarImg = $imgKamar
              ? asset('storage/' . $imgKamar->image)
              : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';
          @endphp

          <div class="rounded-[2rem] bg-[#3B2314] p-2 shadow-[0_25px_60px_rgba(59,35,20,.16)]">
            <div class="rounded-[1.7rem] bg-[#FFFCF8] overflow-hidden">

              <div class="relative h-52">
                <img src="{{ $previewKamarImg }}" alt="{{ $kamar->room }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#3B2314]/85 via-transparent to-transparent"></div>
                <div class="absolute left-5 right-5 bottom-5 text-[#FFF8F1]">
                  <p class="text-[10px] uppercase tracking-widest text-[#C9A45C] font-black">Ringkasan Booking</p>
                  <h2 class="text-2xl font-black mt-1">{{ $kamar->room }}</h2>
                  <p class="text-xs text-[#FFF8F1]/75 mt-1">
                    <i class="fa-solid fa-location-dot text-[#C9A45C] mr-1"></i>
                    {{ $kamar->productKosan->wilayah ?? 'Lokasi Terdaftar' }}
                  </p>
                </div>
              </div>

              <div class="p-5 sm:p-6 space-y-5">
                <div class="rounded-2xl bg-[#FFF8F1] border border-[#E9DDD2] p-4">
                  <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] uppercase tracking-wider font-black text-[#8E7B6D]">Durasi</span>
                    <span id="summary-durasi-text" class="text-xs font-black text-[#D99AA8]">1 Bulan</span>
                  </div>
                </div>

                <div class="space-y-3 text-xs sm:text-sm">
                  <div class="flex justify-between gap-4 text-[#8E7B6D]">
                    <span>Subtotal Sewa</span>
                    <span id="summary-subtotal" class="font-black text-[#3B2314]">Rp 0</span>
                  </div>

                  <div id="summary-discount-row" class="flex justify-between gap-4 hidden">
                    <span class="text-[#C9A45C] font-bold">Diskon Promo</span>
                    <span id="summary-discount-amount" class="font-black text-[#C9A45C]">- Rp 0</span>
                  </div>

                  <div class="flex justify-between gap-4 text-[#8E7B6D]">
                    <span>PPN (12%)</span>
                    <span id="summary-ppn" class="font-black text-[#3B2314]">+ Rp 0</span>
                  </div>
                </div>

                <div class="h-px bg-[#E9DDD2]"></div>

                <div class="rounded-2xl bg-[#3B2314] p-5 text-[#FFF8F1]">
                  <span class="text-[10px] uppercase tracking-widest text-[#FFF8F1]/50 font-black">Total Pembayaran</span>
                  <div id="summary-grand-total" class="text-2xl sm:text-3xl font-black text-[#C9A45C] mt-1">Rp 0</div>
                </div>

                @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
                  <!-- Anti-Bot Turnstile Security Widget -->
                  <div class="rounded-2xl bg-[#FFF8F1] border border-[#E9DDD2] p-3 text-center">
                    <div class="flex items-center justify-center gap-1.5 mb-2 text-[11px] font-bold text-[#8E7B6D]">
                      <i class="fa-solid fa-shield-halved text-[#C9A45C]"></i>
                      <span>Verifikasi Keamanan Reservasi</span>
                    </div>
                    <div class="flex justify-center overflow-hidden rounded-lg">
                      <div class="cf-turnstile"
                           data-sitekey="{{ config('services.turnstile.site_key') }}"
                           data-theme="light"
                           data-size="flexible"></div>
                    </div>
                  </div>
                @endif

                <!-- Tombol Submit Form (Memicu Review Modal) -->
                <button id="btn-submit-booking" type="button" onclick="triggerBookingReview()"
                  class="shine w-full py-4 bg-[#D99AA8] hover:bg-[#D99AA8] text-[#FFF8F1] font-black rounded-2xl transition-all shadow-lg flex items-center justify-center gap-2 active:scale-[.98] text-xs sm:text-sm">
                  <span>Konfirmasi & Selesaikan Booking</span>
                  <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <div class="p-4 rounded-2xl bg-[#C9A45C]/8 border border-[#C9A45C]/20 flex items-start gap-3 text-[10px] text-[#765C55] leading-relaxed">
                  <i class="fa-solid fa-shield-halved text-[#C9A45C] text-sm mt-0.5 shrink-0"></i>
                  <span>Data Anda dilindungi dengan enkripsi aman. Invoice & Tiket PDF diterbitkan instan pasca-transaksi.</span>
                </div>
              </div>
            </div>
          </div>
        </aside>

      </div>
    </form>

  </main>

  <!-- MOBILE BOTTOM STICKY FLOATING ACTION BAR -->
  <aside class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#FFFCF8]/95 backdrop-blur-md border-t border-[#E9DDD2] p-3 px-4 shadow-[0_-8px_25px_rgba(59,35,20,0.12)]">
    <div class="max-w-xl mx-auto flex items-center justify-between gap-3">
      <div>
        <span class="text-[10px] uppercase font-black tracking-wider text-[#8E7B6D] block leading-none">Total Pembayaran</span>
        <div id="mobile-grand-total" class="text-base sm:text-lg font-black text-[#D99AA8] mt-0.5 leading-tight">Rp 0</div>
      </div>
      <button type="button" onclick="document.getElementById('btn-submit-booking').click();"
        class="shine px-5 py-2.5 bg-[#D99AA8] hover:bg-[#D99AA8] text-[#FFF8F1] font-black rounded-xl text-xs sm:text-sm shadow-md active:scale-95 flex items-center gap-1.5 transition-all">
        <span>Selesaikan Booking</span>
        <i class="fa-solid fa-arrow-right text-xs"></i>
      </button>
    </div>
  </aside>

  <!-- MODAL REVIEW DATA RESERVASI SEBELUM SUBMIT -->
  <div id="modal-review-booking" 
       onclick="if(event.target.id === 'modal-review-booking') closeReviewModal();"
       class="fixed inset-0 z-[1000] hidden items-center justify-center p-3 sm:p-4 bg-[#3B2314]/75 backdrop-blur-sm overflow-y-auto">
    <div class="bg-[#FFFCF8] w-full max-w-lg rounded-[2rem] p-5 sm:p-7 shadow-2xl border border-[#E9DDD2] my-auto max-h-[94vh] flex flex-col transform transition-all duration-200">
      
      <!-- HEADER MODAL -->
      <div class="flex items-start justify-between gap-3 pb-4 border-b border-[#E9DDD2]">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-2xl bg-[#C9A45C]/15 text-[#C9A45C] flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-clipboard-check"></i>
          </div>
          <div>
            <h3 class="text-lg sm:text-xl font-black text-[#3B2314] leading-tight">Periksa Data Reservasi</h3>
            <p class="text-[11px] sm:text-xs text-[#8E7B6D] mt-0.5">Pastikan kontak & tanggal sudah sesuai sebelum memproses tiket.</p>
          </div>
        </div>
        <button type="button" onclick="closeReviewModal()" 
          class="w-8 h-8 rounded-full bg-[#FFF8F1] hover:bg-[#E9DDD2] text-[#8E7B6D] flex items-center justify-center text-xs transition-colors shrink-0"
          title="Tutup dialog">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- KONTEN RINCIAN (SCROLLABLE UNTUK DEVICE LAYAR PENDEK) -->
      <div class="overflow-y-auto py-4 space-y-3 pr-1 text-xs sm:text-sm">
        
        <!-- CARD UNIT & JADWAL -->
        <div class="bg-[#FFF8F1] rounded-2xl p-4 border border-[#E9DDD2] space-y-2">
          <div class="flex items-center justify-between gap-3 border-b border-[#E9DDD2]/70 pb-2">
            <span class="text-[11px] font-bold text-[#8E7B6D] shrink-0">Unit Kamar</span>
            <span class="font-black text-[#3B2314] text-right truncate">{{ $kamar->room }} - {{ $namaKosan }}</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-[11px] font-bold text-[#8E7B6D] shrink-0">Tanggal Check-in</span>
            <span id="review-modal-tanggal" class="font-extrabold text-[#3B2314] text-right">-</span>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-[11px] font-bold text-[#8E7B6D] shrink-0">Durasi Sewa</span>
            <span id="review-modal-durasi" class="font-extrabold text-[#D99AA8] text-right">-</span>
          </div>
        </div>

        <!-- CARD DATA DIRI PENYEWA -->
        <div class="bg-[#FFFCF8] rounded-2xl p-4 border border-[#E9DDD2] space-y-2.5 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase tracking-wider font-black text-[#D99AA8]">
              <i class="fa-solid fa-address-card mr-1"></i> Data Kontak Penerima Tiket
            </span>
            <span class="text-[10px] text-[#C9A45C] font-bold bg-[#C9A45C]/10 px-2.5 py-0.5 rounded-full">
              Penting
            </span>
          </div>
          <div class="space-y-2 pt-1">
            <div class="flex items-start justify-between gap-3">
              <span class="text-[11px] font-bold text-[#8E7B6D] shrink-0">Nama Lengkap</span>
              <span id="review-modal-nama" class="font-extrabold text-[#3B2314] text-right break-words">-</span>
            </div>
            <div class="flex items-start justify-between gap-3">
              <span class="text-[11px] font-bold text-[#8E7B6D] shrink-0">WhatsApp / HP</span>
              <span id="review-modal-wa" class="font-sans font-extrabold text-[#C9A45C] text-right break-words">-</span>
            </div>
            <div class="flex items-start justify-between gap-3">
              <span class="text-[11px] font-bold text-[#8E7B6D] shrink-0">Email Penerima</span>
              <span id="review-modal-email" class="font-extrabold text-[#3B2314] text-right break-words text-xs">-</span>
            </div>
          </div>
        </div>

        <!-- CARD PEMBAYARAN -->
        <div class="bg-[#3B2314] text-[#FFF8F1] rounded-2xl p-4 space-y-2 shadow-md">
          <div class="flex items-center justify-between gap-3 text-xs">
            <span class="text-[#FFF8F1]/60 font-bold shrink-0">Metode Bayar</span>
            <span id="review-modal-metode" class="font-bold text-[#FFF8F1] text-right truncate">-</span>
          </div>
          <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#FFF8F1]/10">
            <span class="text-[10px] uppercase tracking-wider text-[#FFF8F1]/60 font-bold shrink-0">Total Pembayaran</span>
            <span id="review-modal-total" class="text-lg sm:text-xl font-black text-[#C9A45C] text-right">Rp 0</span>
          </div>
        </div>

        <!-- PERINGATAN EMAIL & WA -->
        <div class="p-3 rounded-xl bg-[#C9A45C]/10 border border-[#C9A45C] text-[#3B2314] text-[11px] leading-relaxed flex items-start gap-2">
          <i class="fa-solid fa-circle-exclamation text-[#C9A45C] mt-0.5 shrink-0"></i>
          <span>Invoice resmi & tiket kamar kos akan dikirimkan langsung ke email dan WhatsApp di atas.</span>
        </div>

      </div>

      <!-- FOOTER TOMBOL AKSI -->
      <div class="pt-3 border-t border-[#E9DDD2] grid grid-cols-1 sm:grid-cols-2 gap-2.5">
        <button type="button" onclick="cancelReviewAndEdit()"
          class="w-full py-3.5 px-4 bg-[#FFF8F1] hover:bg-[#E9DDD2] text-[#765C55] font-bold rounded-2xl transition-all border border-[#E9DDD2] text-xs sm:text-sm flex items-center justify-center gap-1.5 active:scale-95">
          <i class="fa-solid fa-pen-to-square text-xs"></i>
          <span>Ubah Data</span>
        </button>

        <button type="button" id="btn-modal-confirm-submit" onclick="executeFinalBookingSubmit()"
          class="shine w-full py-3.5 px-4 bg-[#D99AA8] hover:bg-[#D99AA8] text-[#FFF8F1] font-black rounded-2xl transition-all shadow-md text-xs sm:text-sm flex items-center justify-center gap-1.5 active:scale-95">
          <span>Ya, Lanjutkan Booking</span>
          <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
      </div>

    </div>
  </div>

  <!-- 4. MODAL POP-UP FAILED BOOKING / VALIDATION ERROR -->
  @if (session('failed') || (isset($errors) && $errors->any()))
    <div id="flash-failed-modal" class="fixed inset-0 bg-[#3B2314]/70 backdrop-blur-sm z-[999] flex items-center justify-center p-4">
      <div class="bg-[#FFFCF8] w-full max-w-md rounded-[2rem] p-6 sm:p-8 shadow-2xl relative space-y-5 border border-[#E9DDD2]">
        <button type="button" onclick="closeFailedModal()"
          class="absolute top-5 right-5 w-9 h-9 rounded-full bg-[#FFF8F1] flex items-center justify-center text-[#8E7B6D] hover:bg-[#E9DDD2] transition-colors">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <div class="text-center">
          <div class="w-16 h-16 rounded-2xl bg-[#D99AA8]/10 text-[#D99AA8] flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-circle-xmark text-3xl"></i>
          </div>
          <h3 class="text-2xl font-black text-[#3B2314]">Booking Belum Berhasil</h3>
          <div class="text-xs sm:text-sm text-[#8E7B6D] mt-2 leading-6 space-y-1.5 text-left bg-[#FFF8F1] border border-[#E9DDD2] rounded-xl p-3.5">
            @if (session('failed'))
              <p class="flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation text-[#D99AA8] mt-0.5 shrink-0"></i>
                <span>{{ session('failed') }}</span>
              </p>
            @endif
            @if (isset($errors) && $errors->any())
              @foreach ($errors->all() as $err)
                <p class="flex items-start gap-2">
                  <i class="fa-solid fa-circle-exclamation text-[#D99AA8] mt-0.5 shrink-0"></i>
                  <span>{{ $err }}</span>
                </p>
              @endforeach
            @endif
          </div>
        </div>

        <button type="button" onclick="closeFailedModal()"
          class="w-full py-3.5 bg-[#D99AA8] hover:bg-[#D99AA8] text-[#FFF8F1] font-black rounded-2xl transition-all shadow-md active:scale-95 text-xs sm:text-sm">
          Tutup & Coba Lagi
        </button>
      </div>
    </div>
  @endif

  @include('frontend.footer')

  <!-- Flatpickr JS & Locale ID -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

  <script>
    let currentPaymentType = '{{ $defaultPaymentType }}';
    const allPaymentMethods = @json($methodsList->keyBy('code'));
    let uploadedFileBase64 = '';
    let fpInstance = null;

    const kamarPriceData = @json($kamar->priceKamar);
    const cumulativeDiscountVal = parseFloat("{{ $kamar->cumulative_discount ?? 0 }}") || 0;
    const bookedRanges = @json($bookedRanges);
    const firstAvailableDateStr = "{{ $firstAvailableDate }}";

    function selectQuickDate(dateStr) {
      if (fpInstance) {
        fpInstance.setDate(dateStr, true);
        fpInstance.jumpToDate(dateStr);
      }
    }

    document.addEventListener("DOMContentLoaded", function() {
      // Initialize Flatpickr with preloaded booked ranges and swipe gestures
      fpInstance = flatpickr("#input-tanggal", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        minDate: "today",
        locale: "id",
        disable: bookedRanges,
        defaultDate: null,
        appendTo: document.body,
        position: "auto left",
        onReady: function(selectedDates, dateStr, instance) {
          if (firstAvailableDateStr) {
            instance.jumpToDate(firstAvailableDateStr);
          }
          attachFlatpickrSwipe(instance);
        },
        onOpen: function(selectedDates, dateStr, instance) {
          if (!instance.selectedDates.length && firstAvailableDateStr) {
            instance.jumpToDate(firstAvailableDateStr);
          }
        }
      });

      // Enable touch swipe / drag gestures between months on mobile & tablet
      function attachFlatpickrSwipe(instance) {
        if (!instance || !instance.calendarContainer) return;
        const container = instance.calendarContainer;
        let startX = 0;
        let endX = 0;

        container.addEventListener('touchstart', function(e) {
          startX = e.changedTouches[0].screenX;
        }, { passive: true });

        container.addEventListener('touchend', function(e) {
          endX = e.changedTouches[0].screenX;
          const deltaX = endX - startX;
          if (Math.abs(deltaX) > 35) {
            if (deltaX < 0) {
              instance.changeMonth(1); // Swipe left -> Next month
            } else {
              instance.changeMonth(-1); // Swipe right -> Previous month
            }
          }
        }, { passive: true });
      }

      const durationInputs = ['durasi-jam', 'durasi-hari', 'durasi-minggu', 'durasi-bulan', 'durasi-tahun'];

      durationInputs.forEach(id => {
        const inputEl = document.getElementById(id);

        if (inputEl) {
          inputEl.addEventListener('input', calculateRealtimePrice);
        }
      });

      calculateRealtimePrice();

      const bookingForm = document.getElementById('booking-form');
      const submitBtn = document.getElementById('btn-submit-booking');

      if (bookingForm && submitBtn) {
        bookingForm.addEventListener('submit', function(e) {
          const fileInput = document.getElementById('file-bukti');
          if (currentPaymentType === 'transfer' && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
            e.preventDefault();
            alert('Silakan unggah bukti transfer / resi pembayaran terlebih dahulu sebelum melanjutkan reservasi.');
            document.getElementById('file-bukti')?.closest('.border-dashed')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
          }
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i> <span>Memproses Pesanan...</span>';
        });
      }

      // Inisialisasi tampilan metode pembayaran QRIS/Bank secara instan
      toggleQrisDisplay();
    });

    function calculateRealtimePrice() {
      const bulan = parseInt(document.getElementById('durasi-bulan')?.value) || 0;
      const tahun = parseInt(document.getElementById('durasi-tahun')?.value) || 0;

      const durationMap = {
        'bulan': bulan,
        'tahun': tahun
      };

      const filledCategories = Object.keys(durationMap).filter(cat => durationMap[cat] > 0);
      const isSingleInput = filledCategories.length === 1;
      const isMultipleInputs = filledCategories.length > 1;

      let totalBasePrice = 0;
      let totalDiscountAmount = 0;
      let durationTexts = [];

      function getCategoryPriceAndDiscount(categoryName) {
        if (!kamarPriceData || !Array.isArray(kamarPriceData)) {
          return { price: 0, discount: 0 };
        }

        const key = categoryName.toLowerCase().trim();

        const found = kamarPriceData.find(item => {
          const itemCat = (item.kategori || '').toLowerCase().trim();
          return itemCat === key || itemCat.startsWith(key) || key.startsWith(itemCat);
        });

        if (found) {
          return {
            price: parseFloat(found.price) || 0,
            discount: parseFloat(found.discount) || 0
          };
        }

        return { price: 0, discount: 0 };
      }

      Object.keys(durationMap).forEach(cat => {
        const qty = durationMap[cat];

        if (qty > 0) {
          let label = cat.charAt(0).toUpperCase() + cat.slice(1);
          durationTexts.push(`${qty} ${label}`);

          const pInfo = getCategoryPriceAndDiscount(cat);
          const categorySubtotal = pInfo.price * qty;

          totalBasePrice += categorySubtotal;

          if (isSingleInput) {
            let discVal = pInfo.discount;
            if (discVal <= 0 && cat === 'bulan') {
              discVal = cumulativeDiscountVal;
            }

            if (discVal > 0) {
              if (discVal <= 100) {
                totalDiscountAmount = (categorySubtotal * discVal) / 100;
              } else {
                totalDiscountAmount = discVal * qty;
              }
            }
          }
        }
      });

      if (isMultipleInputs && cumulativeDiscountVal > 0) {
        if (cumulativeDiscountVal <= 100) {
          totalDiscountAmount = (totalBasePrice * cumulativeDiscountVal) / 100;
        } else {
          totalDiscountAmount = cumulativeDiscountVal;
        }
      }

      if (totalDiscountAmount > totalBasePrice) {
        totalDiscountAmount = totalBasePrice;
      }

      const priceAfterDiscount = totalBasePrice - totalDiscountAmount;
      const ppnAmount = priceAfterDiscount * 0.12;
      const grandTotal = priceAfterDiscount + ppnAmount;

      const durasiTextElem = document.getElementById('summary-durasi-text');
      const subtotalElem = document.getElementById('summary-subtotal');
      const ppnElem = document.getElementById('summary-ppn');
      const grandTotalElem = document.getElementById('summary-grand-total');

      if (durasiTextElem) {
        durasiTextElem.textContent = durationTexts.length > 0 ? durationTexts.join(', ') : '0 Durasi';
      }

      if (subtotalElem) {
        subtotalElem.textContent = formatRupiah(totalBasePrice);
      }

      const discountRow = document.getElementById('summary-discount-row');
      const discountAmountText = document.getElementById('summary-discount-amount');

      if (discountRow) {
        if (totalDiscountAmount > 0) {
          discountRow.classList.remove('hidden');

          if (discountAmountText) {
            discountAmountText.textContent = `- ${formatRupiah(totalDiscountAmount)}`;
          }
        } else {
          discountRow.classList.add('hidden');
        }
      }

      if (ppnElem) {
        ppnElem.textContent = `+ ${formatRupiah(ppnAmount)}`;
      }

      if (grandTotalElem) {
        grandTotalElem.textContent = formatRupiah(grandTotal);
      }

      const mobileGrandTotal = document.getElementById('mobile-grand-total');
      if (mobileGrandTotal) {
        mobileGrandTotal.textContent = formatRupiah(grandTotal);
      }

      const bankNominal = document.getElementById('bank-nominal-transfer');
      if (bankNominal) {
        bankNominal.textContent = formatRupiah(grandTotal);
      }

      const hiddenTotalPrice = document.getElementById('input-hidden-total-price');

      if (hiddenTotalPrice) {
        hiddenTotalPrice.value = grandTotal;
      }
    }

    function formatRupiah(number) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
      }).format(number);
    }

    window.addEventListener('load', () => {
      const loader = document.getElementById('page-loader');

      if (loader) {
        loader.style.width = '100%';

        setTimeout(() => {
          loader.style.opacity = '0';
        }, 400);
      }

      toggleQrisDisplay();
    });

    function toggleQrisDisplay() {
      const selectBank = document.getElementById('select-bank');
      const qrisDisplay = document.getElementById('qris-display');
      const bankCard = document.getElementById('bank-transfer-card');
      const bankBadge = document.getElementById('bank-badge-name');
      const bankNameLabel = document.getElementById('bank-name-label');
      const bankAccNo = document.getElementById('bank-account-number');
      const bankAccHolder = document.getElementById('bank-account-holder');
      const bankInstructions = document.getElementById('bank-instructions-text');
      const optionalQrContainer = document.getElementById('bank-optional-qr-container');
      const optionalQrImg = document.getElementById('bank-optional-qr-img');

      const qrisImageEl = document.getElementById('qris-image-el');
      const qrisTitleLabel = document.getElementById('qris-title-label');
      const qrisInstructionsLabel = document.getElementById('qris-instructions-label');
      const btnDownloadQris = document.getElementById('btn-download-qris');

      if (!selectBank) return;

      const code = selectBank.value;
      const method = allPaymentMethods[code];
      if (!method) return;

      if (method.type === 'qris') {
        if (qrisDisplay) qrisDisplay.classList.remove('hidden');
        if (bankCard) bankCard.classList.add('hidden');

        if (qrisTitleLabel) qrisTitleLabel.textContent = `Scan & bayar via ${method.name}`;
        if (qrisInstructionsLabel && method.instructions) {
          qrisInstructionsLabel.textContent = method.instructions;
        }

        const qrSrc = method.qr_image_url || `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=SCL-${encodeURIComponent(method.code)}`;
        if (qrisImageEl) qrisImageEl.src = qrSrc;
        if (btnDownloadQris) {
          btnDownloadQris.href = qrSrc;
          btnDownloadQris.download = `QRIS-${method.code}.png`;
        }
      } else {
        // Tipe bank atau ewallet
        if (qrisDisplay) qrisDisplay.classList.add('hidden');
        if (bankCard) {
          bankCard.classList.remove('hidden');
          if (bankBadge) bankBadge.textContent = method.bank_code || method.code.toUpperCase().slice(0, 4);
          if (bankNameLabel) bankNameLabel.textContent = `Transfer ${method.name}`;
          if (bankAccNo) bankAccNo.textContent = method.account_number || '-';
          if (bankAccHolder) bankAccHolder.textContent = method.account_holder ? `a/n ${method.account_holder}` : '';
          if (bankInstructions && method.instructions) {
            bankInstructions.textContent = method.instructions;
          }

          if (method.qr_image_url && optionalQrContainer && optionalQrImg) {
            optionalQrImg.src = method.qr_image_url;
            optionalQrContainer.classList.remove('hidden');
          } else if (optionalQrContainer) {
            optionalQrContainer.classList.add('hidden');
          }
        }
      }
    }

    function copyToClipboard(text, btnId, successMsg) {
      const cleanText = text.replace(/[^0-9]/g, '') || text;
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(cleanText).then(() => showBtnFeedback(btnId, successMsg)).catch(() => fallbackClipboardCopy(cleanText, btnId, successMsg));
      } else {
        fallbackClipboardCopy(cleanText, btnId, successMsg);
      }
    }

    function copyNominalTransfer() {
      const hiddenPrice = document.getElementById('input-hidden-total-price');
      const nominal = hiddenPrice ? hiddenPrice.value : '0';
      copyToClipboard(nominal, 'btn-copy-nominal', 'Tersalin! ✅');
    }

    function fallbackClipboardCopy(text, btnId, successMsg) {
      const ta = document.createElement("textarea");
      ta.value = text;
      ta.style.position = "fixed";
      ta.style.left = "-999999px";
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      try {
        document.execCommand('copy');
        showBtnFeedback(btnId, successMsg);
      } catch(e) {}
      document.body.removeChild(ta);
    }

    function showBtnFeedback(btnId, msg) {
      const btn = document.getElementById(btnId);
      if (!btn) return;
      const span = btn.querySelector('span');
      if (span) {
        const orig = span.textContent;
        span.textContent = msg || 'Tersalin! ✅';
        btn.classList.add('bg-[#C9A45C]');
        setTimeout(() => {
          span.textContent = orig;
          btn.classList.remove('bg-[#C9A45C]');
        }, 2000);
      }
    }

    function scrollToSection(id) {
      const element = document.getElementById(id);

      if (element) {
        const offset = 120;
        const top = element.getBoundingClientRect().top + window.scrollY - offset;

        window.scrollTo({
          top,
          behavior: 'smooth'
        });
      }
    }

    function updateStepUI(activeIndex) {
      const configs = [
        { id: 'step-btn-1', color: '#D99AA8' },
        { id: 'step-btn-2', color: '#C9A45C' },
        { id: 'step-btn-3', color: '#C9A45C' }
      ];

      configs.forEach((config, index) => {
        const btn = document.getElementById(config.id);

        if (!btn) return;

        const numSpan = btn.querySelector('span:first-child');
        const textSpans = btn.querySelectorAll('span span');

        if (index === activeIndex) {
          numSpan.style.backgroundColor = config.color;
          numSpan.style.color = '#FFFCF8';

          if (textSpans[0]) textSpans[0].style.color = config.color;
          if (textSpans[1]) textSpans[1].style.color = '#3B2314';

          btn.style.transform = 'translateY(-2px)';
        } else {
          numSpan.style.backgroundColor = '#E9DDD2';
          numSpan.style.color = '#8E7B6D';

          if (textSpans[0]) textSpans[0].style.color = '#8E7B6D';
          if (textSpans[1]) textSpans[1].style.color = '#8E7B6D';

          btn.style.transform = '';
        }
      });
    }

    window.addEventListener('scroll', () => {
      const sections = ['sec-data-diri', 'sec-durasi', 'sec-pembayaran'];
      const scrollPosition = window.scrollY + 220;

      let activeIndex = 0;

      sections.forEach((secId, idx) => {
        const el = document.getElementById(secId);

        if (el && scrollPosition >= el.offsetTop) {
          activeIndex = idx;
        }
      });

      updateStepUI(activeIndex);
    }, { passive: true });

    function selectPaymentOption(type) {
      currentPaymentType = type;

      const optTransfer = document.getElementById('opt-transfer');
      const optCod = document.getElementById('opt-cod');
      const transferContainer = document.getElementById('transfer-method-container');
      const codContainer = document.getElementById('cod-method-container');

      const transferIcon = optTransfer?.querySelector('i');
      const codIcon = optCod?.querySelector('i');

      if (type === 'transfer') {
        optTransfer.className = "payment-option p-5 rounded-2xl border-2 border-[#D99AA8] bg-[#D99AA8]/5 text-left shadow-sm";
        optCod.className = "payment-option p-5 rounded-2xl border border-[#E9DDD2] bg-[#FFFCF8] text-left";

        if (transferIcon) {
          transferIcon.className = "fa-solid fa-circle-check text-[#D99AA8]";
        }

        if (codIcon) {
          codIcon.className = "fa-regular fa-circle text-[#8E7B6D]";
        }

        transferContainer.classList.remove('hidden');
        codContainer.classList.add('hidden');

        const selectBank = document.getElementById('select-bank');
        const paymentInput = document.getElementById('input-payment-method');
        if (paymentInput) {
          paymentInput.value = selectBank ? selectBank.value : 'qris';
        }
      } else {
        optCod.className = "payment-option p-5 rounded-2xl border-2 border-[#C9A45C] bg-[#C9A45C]/5 text-left shadow-sm";
        optTransfer.className = "payment-option p-5 rounded-2xl border border-[#E9DDD2] bg-[#FFFCF8] text-left";

        if (codIcon) {
          codIcon.className = "fa-solid fa-circle-check text-[#C9A45C]";
        }

        if (transferIcon) {
          transferIcon.className = "fa-regular fa-circle text-[#8E7B6D]";
        }

        transferContainer.classList.add('hidden');
        codContainer.classList.remove('hidden');

        const paymentInput = document.getElementById('input-payment-method');
        if (paymentInput) {
          paymentInput.value = 'cash';
        }
      }
    }

    function updateSelectedBank(val) {
      if (currentPaymentType === 'transfer') {
        const paymentInput = document.getElementById('input-payment-method');
        if (paymentInput) {
          paymentInput.value = val;
        }
      }
    }

    function handleFileUpload(event) {
      const file = event.target.files[0];

      if (file) {
        if (file.size > 5 * 1024 * 1024) {
          alert('Ukuran file maksimal 5MB!');
          event.target.value = '';
          return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
          uploadedFileBase64 = e.target.result;

          document.getElementById('upload-placeholder').classList.add('hidden');

          const previewContainer = document.getElementById('upload-preview');
          previewContainer.classList.remove('hidden');
          previewContainer.classList.add('flex');

          document.getElementById('preview-img').src = uploadedFileBase64;
          document.getElementById('preview-filename').textContent = file.name;
        };

        reader.readAsDataURL(file);
      }
    }

    function closeFailedModal() {
      const modal = document.getElementById('flash-failed-modal');

      if (modal) {
        modal.remove();
      }
    }

    const revealElements = document.querySelectorAll('.reveal');

    const revealOnScroll = () => {
      const windowHeight = window.innerHeight;

      revealElements.forEach((el, index) => {
        if (el.getBoundingClientRect().top < windowHeight - 70) {
          el.style.transitionDelay = `${Math.min(index * 45, 250)}ms`;
          el.classList.add('active');
        }
      });
    };

    // --- FITUR PRE-SUBMISSION CONFIRMATION MODAL ---
    let isFinalBookingSubmitting = false;

    function formatReviewIndonesianDate(dateStr) {
      if (!dateStr) return '-';
      const parts = dateStr.split('-');
      if (parts.length !== 3) return dateStr;
      const year = parts[0];
      const monthIdx = parseInt(parts[1], 10) - 1;
      const day = parseInt(parts[2], 10);
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const jamVal = document.getElementById('input-jam')?.value || '13:00';
      return `${day} ${months[monthIdx] || ''} ${year} (pkl ${jamVal} WIB)`;
    }

    function getReviewPaymentMethodLabel() {
      if (currentPaymentType === 'cod') {
        const cashM = Object.values(allPaymentMethods).find(m => m.type === 'cash');
        return cashM ? cashM.name : 'Bayar di Tempat (COD / Tunai)';
      }
      const bankSelect = document.getElementById('select-bank');
      const selectedBankVal = bankSelect ? bankSelect.value : '';
      if (allPaymentMethods[selectedBankVal]) {
        const m = allPaymentMethods[selectedBankVal];
        if (m.type === 'qris') {
          return m.name;
        }
        return `${m.name} (${m.account_number || 'Transfer'})`;
      }
      return 'Transfer Online';
    }

    function validateBookingFormBeforeReview() {
      const namaInput = document.getElementById('input-nama');
      const waInput = document.getElementById('input-wa');
      const emailInput = document.getElementById('input-email');
      const tanggalInput = document.getElementById('input-tanggal');

      if (!namaInput || !namaInput.value.trim()) {
        alert('Silakan isi Nama Lengkap penyewa terlebih dahulu.');
        scrollToSection('sec-data-diri');
        if (namaInput) namaInput.focus();
        return false;
      }

      if (!waInput || !waInput.value.trim()) {
        alert('Silakan isi Nomor WhatsApp / HP aktif terlebih dahulu.');
        scrollToSection('sec-data-diri');
        if (waInput) waInput.focus();
        return false;
      }

      if (!emailInput || !emailInput.value.trim()) {
        alert('Silakan isi Alamat Email untuk penerimaan invoice & tiket kamar.');
        scrollToSection('sec-data-diri');
        if (emailInput) emailInput.focus();
        return false;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value.trim())) {
        alert('Format alamat email tidak valid. Mohon periksa kembali.');
        scrollToSection('sec-data-diri');
        emailInput.focus();
        return false;
      }

      if (!tanggalInput || !tanggalInput.value.trim()) {
        alert('Silakan pilih Tanggal Masuk (Check-in) terlebih dahulu.');
        scrollToSection('sec-durasi');
        return false;
      }

      if (currentPaymentType === 'transfer') {
        const fileBukti = document.getElementById('file-bukti');
        if (!fileBukti || !fileBukti.files || fileBukti.files.length === 0) {
          alert('Silakan unggah foto bukti transfer pembayaran terlebih dahulu.');
          scrollToSection('sec-pembayaran');
          return false;
        }
      }

      @if (config('services.turnstile.enabled', true) && !empty(config('services.turnstile.site_key')))
        const turnstileInput = document.querySelector('#booking-form [name="cf-turnstile-response"]');
        if (turnstileInput && !turnstileInput.value) {
          alert('Harap selesaikan verifikasi keamanan bot (Cloudflare Turnstile) terlebih dahulu.');
          return false;
        }
      @endif

      return true;
    }

    function triggerBookingReview() {
      if (!validateBookingFormBeforeReview()) {
        return;
      }

      const namaVal = document.getElementById('input-nama')?.value.trim() || '-';
      const waVal = document.getElementById('input-wa')?.value.trim() || '-';
      const emailVal = document.getElementById('input-email')?.value.trim() || '-';
      const tanggalVal = document.getElementById('input-tanggal')?.value.trim() || '';
      const durasiText = document.getElementById('summary-durasi-text')?.textContent.trim() || '1 Bulan';
      const grandTotalText = document.getElementById('summary-grand-total')?.textContent.trim() || 'Rp 0';

      document.getElementById('review-modal-nama').textContent = namaVal;
      document.getElementById('review-modal-wa').textContent = waVal;
      document.getElementById('review-modal-email').textContent = emailVal;
      document.getElementById('review-modal-tanggal').textContent = formatReviewIndonesianDate(tanggalVal);
      document.getElementById('review-modal-durasi').textContent = durasiText;
      document.getElementById('review-modal-metode').textContent = getReviewPaymentMethodLabel();
      document.getElementById('review-modal-total').textContent = grandTotalText;

      const modal = document.getElementById('modal-review-booking');
      if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
      }
    }

    function closeReviewModal() {
      const modal = document.getElementById('modal-review-booking');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
      }
    }

    function cancelReviewAndEdit() {
      closeReviewModal();
      scrollToSection('sec-data-diri');
      setTimeout(() => {
        const namaInput = document.getElementById('input-nama');
        if (namaInput) namaInput.focus();
      }, 450);
    }

    function executeFinalBookingSubmit() {
      if (isFinalBookingSubmitting) return;
      isFinalBookingSubmitting = true;

      const modalSubmitBtn = document.getElementById('btn-modal-confirm-submit');
      const sidebarSubmitBtn = document.getElementById('btn-submit-booking');

      if (modalSubmitBtn) {
        modalSubmitBtn.disabled = true;
        modalSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        modalSubmitBtn.innerHTML = `
          <svg class="animate-spin mr-2 h-4 w-4 text-[#FFF8F1] inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memproses Reservasi...</span>
        `;
      }

      if (sidebarSubmitBtn) {
        sidebarSubmitBtn.disabled = true;
        sidebarSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
      }

      const bookingForm = document.getElementById('booking-form');
      if (bookingForm) {
        bookingForm.submit();
      }
    }

    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
      bookingForm.addEventListener('submit', function(e) {
        if (!isFinalBookingSubmitting) {
          e.preventDefault();
          triggerBookingReview();
        }
      });
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeReviewModal();
      }
    });

    window.addEventListener('scroll', revealOnScroll, { passive: true });
    window.addEventListener('load', revealOnScroll);
  </script>
</body>
</html>
