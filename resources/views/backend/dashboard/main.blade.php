<!doctype html>
<html lang="id" data-bs-theme="light">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Jumala Living | Admin Dashboard</title>

    <script>
      (() => {
        'use strict';
        try {
          localStorage.setItem('lte-theme', 'light');
        } catch {}
        document.documentElement.setAttribute('data-bs-theme', 'light');
        document.documentElement.style.colorScheme = 'light';

        const observer = new MutationObserver(() => {
          if (document.documentElement.getAttribute('data-bs-theme') !== 'light') {
            document.documentElement.setAttribute('data-bs-theme', 'light');
            document.documentElement.style.colorScheme = 'light';
          }
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
      })();
    </script>

    <link rel="icon" href="{{ asset('logo.jpeg') }}?v={{ @filemtime(public_path('logo.jpeg')) ?: '5' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light" />
    <meta name="theme-color" content="#3B1D12" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />

    <link rel="stylesheet" href="{{ asset('assets-dashboard-admin/css/adminlte.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets-dashboard-admin/css/custom-admin.css') }}?v={{ @filemtime(public_path('assets-dashboard-admin/css/custom-admin.css')) ?: '5' }}" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
      @font-face {
        font-family: 'summernote';
        src: url('https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/font/summernote.woff2') format('woff2'),
             url('https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/font/summernote.woff') format('woff'),
             url('https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/font/summernote.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
      }
      .note-editor [class*=" note-icon"]:before,
      .note-editor [class^="note-icon"]:before,
      .note-editor [class*=" note-icon"]::before,
      .note-editor [class^="note-icon"]::before {
        font-family: 'summernote' !important;
        font-style: normal !important;
        font-weight: normal !important;
        display: inline-block !important;
      }
    </style>
  </head>

  @php
    $currentPageLabel = match(true) {
      request()->routeIs('admin.dashboard') => 'Dashboard',
      request()->routeIs('admin.kamar.create') => 'Tambah Kamar',
      request()->routeIs('admin.kamar.edit') => 'Edit Kamar',
      request()->routeIs('admin.kamar.show') => 'Detail Kamar',
      request()->routeIs('admin.kamar.*') => 'Manajemen Kamar',
      request()->routeIs('admin.properti.*') => 'Profil & Fasilitas Hunian',
      request()->routeIs('admin.booking.*') => 'Permintaan Booking',
      request()->routeIs('admin.testimoni.*') => 'Testimoni & Review',
      request()->routeIs('admin.users.*') => 'Kelola Admin',
      request()->routeIs('admin.sosial.media.*') => 'Sosial Media',
      request()->routeIs('admin.payment-methods.*') => 'Metode Pembayaran',
      request()->routeIs('admin.profile.*') => 'Profil Akun',
      default => 'Admin Panel',
    };

    $isKamarSubPage = request()->routeIs('admin.kamar.create', 'admin.kamar.edit', 'admin.kamar.show');
  @endphp

  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
      <!-- Header -->
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <ul class="navbar-nav align-items-center gap-2">
            <li class="nav-item">
              <a
                class="nav-action-btn"
                data-lte-toggle="sidebar"
                href="#"
                role="button"
                aria-label="Toggle sidebar"
                title="Buka / Tutup Sidebar"
              >
                <i class="bi bi-list fs-5"></i>
              </a>
            </li>

            <li class="nav-item d-flex d-md-none align-items-center">
              <span class="fw-bold text-dark fs-7 text-truncate" style="max-width: 155px;">
                {{ $currentPageLabel }}
              </span>
            </li>

            <li class="nav-item d-none d-md-flex align-items-center">
              <nav class="header-breadcrumb-nav" aria-label="breadcrumb">
                <ol class="header-breadcrumb">
                  <li class="header-breadcrumb-item @if(request()->routeIs('admin.dashboard')) active @endif">
                    <a href="{{ route('admin.dashboard') }}" class="header-breadcrumb-link">
                      <i class="bi bi-grid-1x2 fs-8"></i>
                      <span>Dashboard</span>
                    </a>
                  </li>
                  @if($isKamarSubPage)
                  <li class="header-breadcrumb-sep">
                    <i class="bi bi-chevron-right"></i>
                  </li>
                  <li class="header-breadcrumb-item">
                    <a href="{{ route('admin.kamar.index') }}" class="header-breadcrumb-link">
                      <span>Manajemen Kamar</span>
                    </a>
                  </li>
                  @endif
                  @if(!request()->routeIs('admin.dashboard'))
                  <li class="header-breadcrumb-sep">
                    <i class="bi bi-chevron-right"></i>
                  </li>
                  <li class="header-breadcrumb-item active" aria-current="page">
                    <span>{{ $currentPageLabel }}</span>
                  </li>
                  @endif
                </ol>
              </nav>
            </li>
          </ul>

          <ul class="navbar-nav ms-auto align-items-center gap-1.5 gap-sm-2">
            <li class="nav-item">
              <button type="button" class="nav-search-btn" data-bs-toggle="modal" data-bs-target="#globalSearchModal" title="Pencarian Cepat Modul (Ctrl+K)" aria-label="Pencarian Cepat Modul">
                <i class="bi bi-search nav-search-icon"></i>
                <span class="nav-search-placeholder">Cari modul...</span>
                <kbd class="nav-search-kbd">
                  <span>Ctrl</span>
                  <span>K</span>
                </kbd>
              </button>
            </li>

            <li class="nav-item d-none d-lg-inline-block">
              <a href="{{ route('home') }}" target="_blank" class="nav-public-btn" title="Buka Website Jumala Living">
                <i class="bi bi-globe2 fs-8"></i>
                <span>Lihat Website</span>
                <i class="bi bi-box-arrow-up-right" style="font-size: 0.65rem;"></i>
              </a>
            </li>

            <li class="nav-item d-none d-md-flex align-items-center" aria-hidden="true">
              <span class="header-divider"></span>
            </li>

            <!-- Notifications -->
            <li class="nav-item dropdown">
              <a class="nav-action-btn position-relative" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi Booking" aria-label="Notifikasi Booking">
                <i class="bi bi-bell fs-6"></i>
                @if(isset($pendingBookingCount) && $pendingBookingCount > 0)
                  <span class="nav-action-badge">
                    {{ $pendingBookingCount > 99 ? '99+' : $pendingBookingCount }}
                  </span>
                @endif
              </a>
              <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu mt-2">
                <div class="notification-dropdown-header">
                  <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold fs-7 text-dark">Notifikasi Reservasi</span>
                    @if(isset($pendingBookingCount) && $pendingBookingCount > 0)
                      <span class="badge badge-subtle-warning rounded-pill px-2 py-0.5">{{ $pendingBookingCount }} Baru</span>
                    @endif
                  </div>
                  <a href="{{ route('admin.booking.index') }}" class="fs-8 text-decoration-none text-gold fw-semibold">Buka Semua</a>
                </div>
                <div class="notification-dropdown-body">
                  @if(isset($navbarPendingBookings) && $navbarPendingBookings->count() > 0)
                    @foreach($navbarPendingBookings as $nb)
                      <a href="{{ route('admin.booking.index') }}" class="notification-item">
                        <div class="notification-item-icon">
                          <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                          <div class="d-flex justify-content-between align-items-center mb-0.5">
                            <span class="fw-semibold fs-8 text-dark text-truncate" style="max-width: 170px;">{{ $nb->name }}</span>
                            <small class="text-muted fs-9">{{ \Carbon\Carbon::parse($nb->created_at)->diffForHumans() }}</small>
                          </div>
                          <div class="fs-8 text-muted text-truncate" style="max-width: 230px;">
                            Jumala Living &bull; {{ $nb->productKamarKosan->room ?? 'Kamar' }}
                          </div>
                          <div class="fw-semibold text-primary fs-9 mt-0.5 tabular-nums">
                            Rp {{ number_format((float) $nb->total_price, 0, ',', '.') }}
                          </div>
                        </div>
                      </a>
                    @endforeach
                  @else
                    <div class="text-center py-4 px-3 text-muted">
                      <i class="bi bi-check2-circle fs-3 d-block mb-1.5 text-success"></i>
                      <div class="fw-semibold fs-8 text-dark">Tidak Ada Antrean Baru</div>
                      <small class="text-muted fs-9">Seluruh permintaan booking telah ditindaklanjuti.</small>
                    </div>
                  @endif
                </div>
                <div class="notification-dropdown-footer">
                  <a href="{{ route('admin.booking.index') }}" class="btn btn-xs btn-light border w-100 py-1.5 fw-semibold justify-content-center">
                    <span>Lihat Semua Permintaan Booking</span>
                    <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            </li>

            <!-- User Menu -->
            <li class="nav-item dropdown user-menu ms-0.5">
              <a href="#" class="nav-user-chip" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Akun Admin">
                <div class="nav-user-avatar">
                  {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="nav-user-meta d-none d-md-flex flex-column text-start">
                  <span class="nav-user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                  <span class="nav-user-role">
                    {{ Auth::user()->isSuperAdmin() ? 'Super Admin' : 'Admin Operasional' }}
                  </span>
                </div>
                <i class="bi bi-chevron-down nav-user-chevron ms-0.5 d-none d-sm-inline"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end mt-2 p-0 overflow-hidden" style="min-width: 240px;">
                <li class="p-3 border-bottom bg-light">
                  <div class="d-flex align-items-center gap-2.5">
                    <div class="nav-user-avatar flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.95rem;">
                      {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                      <div class="fw-bold fs-8 text-dark text-truncate">{{ Auth::user()->name ?? 'Administrator' }}</div>
                      <div class="text-muted fs-9 text-truncate">{{ Auth::user()->email ?? 'admin@jumalaliving.com' }}</div>
                      <span class="badge badge-subtle-gold mt-1" style="font-size: 0.65rem;">
                        {{ Auth::user()->isSuperAdmin() ? 'Super Admin' : 'Admin Operasional' }}
                      </span>
                    </div>
                  </div>
                </li>
                <li class="p-1.5">
                  <a href="{{ route('admin.profile.edit') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-2.5 rounded">
                    <i class="bi bi-shield-lock text-secondary"></i>
                    <span>Pengaturan Akun</span>
                  </a>
                  <a href="{{ route('home') }}" target="_blank" class="dropdown-item d-flex d-lg-none align-items-center gap-2 py-2 px-2.5 rounded">
                    <i class="bi bi-globe2 text-secondary"></i>
                    <span>Lihat Website</span>
                  </a>
                </li>
                <li class="p-1.5 border-top">
                  <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 px-2.5 rounded text-danger fw-semibold">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>Keluar Sistem</span>
                    </button>
                  </form>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Sidebar -->
      <aside class="app-sidebar" data-bs-theme="dark">
        <div class="sidebar-brand">
          <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <div class="brand-logo-box">
              <img
                src="{{ asset('logo.jpeg') }}?v={{ @filemtime(public_path('logo.jpeg')) ?: '5' }}"
                alt="Jumala Living Logo"
                class="brand-logo-img"
              />
            </div>
            <div class="brand-text-wrapper">
              <div class="brand-title">
                <span>Jumala</span>
                <span class="brand-title-accent">Living</span>
              </div>
              <div class="brand-subtitle">
                <span class="brand-status-dot"></span>
                <span>Property Panel</span>
              </div>
            </div>
          </a>
        </div>

        <div class="sidebar-wrapper">
          <nav class="mt-1 d-flex flex-column h-100" aria-label="Main navigation">
            <ul
              class="nav sidebar-menu flex-column flex-grow-1"
              data-lte-toggle="treeview"
              data-accordion="false"
              id="navigation">

              <li class="nav-header">Operasional</li>

              <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-grid-1x2"></i>
                  <p>Dashboard</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('admin.kamar.index') }}" class="nav-link {{ request()->routeIs('admin.kamar.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-door-open"></i>
                  <p>Manajemen Kamar</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('admin.booking.index') }}" class="nav-link {{ request()->routeIs('admin.booking.*') ? 'active' : '' }} d-flex align-items-center justify-content-between">
                  <span class="d-flex align-items-center gap-2">
                    <i class="nav-icon bi bi-calendar-check"></i>
                    <p class="mb-0">Permintaan Booking</p>
                  </span>
                  @if(isset($pendingBookingCount) && $pendingBookingCount > 0)
                    <span class="badge sidebar-badge-count ms-2">{{ $pendingBookingCount }}</span>
                  @endif
                </a>
              </li>

              <li class="nav-header">Hunian & Publik</li>

              <li class="nav-item">
                <a href="{{ route('admin.properti.edit') }}" class="nav-link {{ request()->routeIs('admin.properti.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-house-gear"></i>
                  <p>Profil & Fasilitas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('admin.testimoni.index') }}" class="nav-link {{ request()->routeIs('admin.testimoni.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-chat-square-quote"></i>
                  <p>Testimoni & Review</p>
                </a>
              </li>

              <li class="nav-header">Pengaturan</li>

              @if(Auth::user()->isSuperAdmin())
              <li class="nav-item">
                <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-credit-card-2-front"></i>
                  <p>Metode Pembayaran</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('admin.sosial.media.index') }}" class="nav-link {{ request()->routeIs('admin.sosial.media.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-share"></i>
                  <p>Sosial Media</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-people"></i>
                  <p>Kelola Admin</p>
                </a>
              </li>
              @endif

              <li class="nav-item">
                <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-shield-lock"></i>
                  <p>Profil Akun</p>
                </a>
              </li>

              <li class="nav-header">Pintasan</li>

              <li class="nav-item mb-3">
                <a href="{{ route('home') }}" target="_blank" class="nav-link">
                  <i class="nav-icon bi bi-box-arrow-up-right"></i>
                  <p>Lihat Website</p>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="app-main">
        @yield('content')
      </main>

      <!-- Footer -->
      <footer class="app-footer text-muted fs-8 py-2.5">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <div>
            <span>&copy; {{ date('Y') }}</span>
            <a href="{{ route('home') }}" target="_blank" class="text-decoration-none fw-bold text-dark ms-1">Jumala Living</a>
          </div>
          <div class="d-none d-sm-inline text-muted">
            Sistem Manajemen Hunian Jumala Living
          </div>
        </div>
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets-dashboard-admin/js/adminlte.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined && window.innerWidth > 992) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: { theme: 'os-theme-light', autoHide: 'leave', clickScroll: true }
          });
        }
      });

      document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('sidebar-overlay') || e.target.closest('.sidebar-overlay'))) {
          document.body.classList.remove('sidebar-open');
        }
      });

      $(document).ready(function() {
        const summernoteConfig = {
          placeholder: 'Tuliskan deskripsi lengkap dan informasi detail...',
          tabsize: 2,
          height: 190,
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
          ]
        };

        $('.summernote-editor').each(function() {
          if (!$(this).next('.note-editor').length) {
            $(this).summernote(summernoteConfig);
          }
        });

        $(document).on('focusin', function(e) {
          if ($(e.target).closest('.note-modal, .note-editor').length) {
            e.stopImmediatePropagation();
          }
        });

        $('.modal').on('shown.bs.modal', function () {
          $(this).find('.summernote-edit, .summernote-init').each(function() {
            if (!$(this).next('.note-editor').length) {
              $(this).summernote(summernoteConfig);
            }
          });
        });

        $(document).on('submit', 'form', function () {
          $(this).find('.summernote-editor, .summernote-init, .summernote-edit, textarea').each(function () {
            if (typeof $.fn.summernote !== 'undefined' && $(this).data('summernote')) {
              $(this).val($(this).summernote('code'));
            }
          });
        });
      });

      // Global Video Mode Input Helper (shared across Properti & Kamar forms)
      document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('video-mode-radio')) {
          const group = e.target.closest('.video-input-group');
          if (!group) return;
          const mode = e.target.value;
          const linkPanel = group.querySelector('.video-panel-link');
          const uploadPanel = group.querySelector('.video-panel-upload');
          if (linkPanel) linkPanel.classList.toggle('d-none', mode !== 'link');
          if (uploadPanel) uploadPanel.classList.toggle('d-none', mode !== 'upload');
        }
      });

      // Quick Search Palette Modal (Ctrl+K)
      document.addEventListener('DOMContentLoaded', () => {
        const searchModalEl = document.getElementById('globalSearchModal');
        const searchInput = document.getElementById('globalQuickSearchInput');
        const resultItems = Array.from(document.querySelectorAll('#quickSearchResults .search-palette-item'));
        const emptyState = document.getElementById('quickSearchEmptyState');
        if (!searchModalEl || !searchInput) return;

        searchModalEl.addEventListener('shown.bs.modal', () => {
          searchInput.value = '';
          filterPalette('');
          searchInput.focus();
        });

        document.addEventListener('keydown', (e) => {
          if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            bootstrap.Modal.getOrCreateInstance(searchModalEl).show();
          }
        });

        function filterPalette(query) {
          let visibleCount = 0;
          resultItems.forEach(item => {
            const match = item.textContent.toLowerCase().includes(query);
            item.style.display = match ? 'flex' : 'none';
            if (match) visibleCount++;
          });
          if (emptyState) emptyState.classList.toggle('d-none', visibleCount > 0);
        }

        searchInput.addEventListener('input', function () {
          filterPalette(this.value.toLowerCase().trim());
        });
      });
    </script>

    <!-- Global Quick Search Palette Modal -->
    <div class="modal fade search-palette-modal" id="globalSearchModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
          <div class="modal-header py-2.5 px-3">
            <div class="d-flex align-items-center gap-2 w-100">
              <i class="bi bi-search text-muted fs-6"></i>
              <input type="text" id="globalQuickSearchInput" class="form-control search-palette-input" placeholder="Ketik nama modul atau halaman..." autocomplete="off">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          </div>
          <div class="modal-body p-2" id="quickSearchResults">
            <small class="text-muted px-2.5 py-1.5 d-block text-uppercase fs-9 fw-bold">Navigasi Modul</small>
            <a href="{{ route('admin.dashboard') }}" class="search-palette-item">
              <i class="bi bi-grid-1x2"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Dashboard Utama</div>
                <small class="text-muted fs-9">Ringkasan kamar, status hunian, dan aktivitas terbaru</small>
              </div>
            </a>
            <a href="{{ route('admin.kamar.index') }}" class="search-palette-item">
              <i class="bi bi-door-open"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Manajemen Kamar</div>
                <small class="text-muted fs-9">Daftar unit kamar, foto, harga sewa, dan ketersediaan</small>
              </div>
            </a>
            <a href="{{ route('admin.kamar.create') }}" class="search-palette-item">
              <i class="bi bi-plus-circle"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Tambah Kamar Baru</div>
                <small class="text-muted fs-9">Buat unit kamar baru untuk Jumala Living</small>
              </div>
            </a>
            <a href="{{ route('admin.booking.index') }}" class="search-palette-item">
              <i class="bi bi-calendar-check"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Permintaan Booking</div>
                <small class="text-muted fs-9">Verifikasi reservasi dan bukti transfer penghuni</small>
              </div>
            </a>
            <a href="{{ route('admin.properti.edit') }}" class="search-palette-item">
              <i class="bi bi-house-gear"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Profil & Fasilitas Hunian</div>
                <small class="text-muted fs-9">Atur foto utama, lokasi Maps, dan fasilitas bersama</small>
              </div>
            </a>
            <a href="{{ route('admin.testimoni.index') }}" class="search-palette-item">
              <i class="bi bi-chat-square-quote"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Testimoni & Review</div>
                <small class="text-muted fs-9">Kelola ulasan penghuni Jumala Living</small>
              </div>
            </a>
            @if(Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.payment-methods.index') }}" class="search-palette-item">
              <i class="bi bi-credit-card-2-front"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Metode Pembayaran</div>
                <small class="text-muted fs-9">Atur rekening bank, QRIS, dan tunai</small>
              </div>
            </a>
            <a href="{{ route('admin.sosial.media.index') }}" class="search-palette-item">
              <i class="bi bi-share"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Sosial Media & WhatsApp</div>
                <small class="text-muted fs-9">Kelola kontak WhatsApp, Instagram, dan TikTok</small>
              </div>
            </a>
            <a href="{{ route('admin.users.index') }}" class="search-palette-item">
              <i class="bi bi-people"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Kelola Admin</div>
                <small class="text-muted fs-9">Atur akun pengelola Jumala Living</small>
              </div>
            </a>
            @endif
            <a href="{{ route('admin.profile.edit') }}" class="search-palette-item">
              <i class="bi bi-shield-lock"></i>
              <div>
                <div class="fw-semibold fs-8 text-dark">Profil Akun</div>
                <small class="text-muted fs-9">Perbarui profil dan kata sandi</small>
              </div>
            </a>

            <div id="quickSearchEmptyState" class="text-center py-4 px-3 text-muted d-none">
              <i class="bi bi-search fs-4 d-block mb-1 opacity-50"></i>
              <div class="fs-8 fw-medium text-secondary">Modul tidak ditemukan</div>
            </div>
          </div>
          <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted fs-9">Tekan <kbd class="px-1.5 py-0.5 bg-white border rounded">ESC</kbd> untuk menutup</small>
            <small class="text-muted fs-9">Jumala Living</small>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
