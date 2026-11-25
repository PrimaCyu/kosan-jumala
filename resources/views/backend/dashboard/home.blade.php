@extends('backend.dashboard.main')

@section('content')

@php
    $hour = (int) date('H');
    $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $sixMonthTotal = collect($revenueTrend ?? [])->sum('revenue');
@endphp

<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <div class="d-flex align-items-center flex-wrap gap-2 mb-1.5">
          <span class="fs-8 text-muted d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-calendar3 text-gold"></i>
            <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
          </span>
          <span class="text-muted opacity-50">&bull;</span>
          <span class="badge badge-subtle-gold">
            <i class="bi bi-house-heart me-1"></i>Jumala Living
          </span>
          @if(!empty($operationalAlerts) && count($operationalAlerts) > 0)
            <span class="badge badge-subtle-warning">
              <i class="bi bi-exclamation-circle me-1"></i>{{ count($operationalAlerts) }} Perlu Tindakan
            </span>
          @else
            <span class="badge badge-subtle-success">
              <i class="bi bi-check2-circle me-1"></i>Operasional Normal
            </span>
          @endif
        </div>
        <h1 class="page-title mb-0">{{ $greeting }}, {{ Auth::user()->name ?? 'Pengelola' }}</h1>
        <p class="page-subtitle">Ringkasan ketersediaan kamar, okupansi hunian, dan aktivitas pengelolaan Jumala Living.</p>
      </div>
      <div class="page-header-actions">
        <a href="{{ route('admin.kamar.create') }}" class="btn btn-gold px-3">
          <i class="bi bi-plus-lg"></i>
          <span>Tambah Kamar</span>
        </a>
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-light border px-3">
          <i class="bi bi-door-open"></i>
          <span>Semua Kamar</span>
        </a>
        <a href="{{ route('admin.booking.index') }}" class="btn btn-primary px-3">
          <i class="bi bi-calendar-check"></i>
          <span>Booking</span>
          @if(($pendingBookingsCount ?? 0) > 0)
            <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $pendingBookingsCount }}</span>
          @endif
        </a>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    @if(!empty($operationalAlerts) && count($operationalAlerts) > 0)
      <div class="mb-4">
        <div class="d-flex flex-column gap-2">
          @foreach($operationalAlerts as $alert)
            <div class="card p-3">
              <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                <div class="d-flex align-items-center gap-2.5">
                  <div class="dash-alert-indicator {{ $alert['badge_class'] ?? 'bg-amber-subtle' }}">
                    <i class="bi {{ $alert['icon'] ?? 'bi-info-circle' }}"></i>
                  </div>
                  <div>
                    <div class="fs-7 fw-bold text-dark">{{ $alert['title'] }}</div>
                    <div class="fs-8 text-muted">{{ $alert['subtitle'] }}</div>
                  </div>
                </div>
                <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-light border px-3 flex-shrink-0">
                  <span>{{ $alert['action_label'] }}</span>
                  <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- 4 KPI UTAMA KAMAR JUMALA LIVING -->
    <div class="kpi-stat-grid">
      <div class="kpi-stat-card">
        <div class="kpi-stat-header">
          <span class="kpi-stat-label">Total Kamar</span>
          <span class="kpi-stat-icon"><i class="bi bi-buildings"></i></span>
        </div>
        <div class="kpi-stat-value">{{ $totalKamar }} <span class="fs-6 fw-medium text-muted">unit</span></div>
        <div class="progress-thin mb-2">
          <div class="progress-bar-normal" style="width: {{ min(100, $occupancyRate) }}%;"></div>
        </div>
        <div class="kpi-stat-meta">
          Okupansi hunian <strong class="text-dark">{{ $occupancyRate }}%</strong>
        </div>
      </div>

      <div class="kpi-stat-card">
        <div class="kpi-stat-header">
          <span class="kpi-stat-label">Kamar Tersedia</span>
          <span class="kpi-stat-icon"><i class="bi bi-check2-circle"></i></span>
        </div>
        <div class="kpi-stat-value text-success">{{ $availableKamar }} <span class="fs-6 fw-medium text-muted">unit</span></div>
        <div class="kpi-stat-meta">
          @if($availableKamar > 0)
            <span class="badge badge-subtle-success">Siap dipesan online</span>
          @else
            <span class="badge badge-subtle-secondary">Belum ada unit kosong</span>
          @endif
        </div>
      </div>

      <div class="kpi-stat-card">
        <div class="kpi-stat-header">
          <span class="kpi-stat-label">Kamar Terisi</span>
          <span class="kpi-stat-icon"><i class="bi bi-person-check"></i></span>
        </div>
        <div class="kpi-stat-value">{{ $occupiedKamar }} <span class="fs-6 fw-medium text-muted">unit</span></div>
        <div class="kpi-stat-meta">
          @if(($pendingKamar ?? 0) > 0)
            <span class="badge badge-subtle-warning">{{ $pendingKamar }} menunggu verifikasi</span>
          @else
            <span>Dihuni oleh penyewa aktif</span>
          @endif
        </div>
      </div>

      <div class="kpi-stat-card">
        <div class="kpi-stat-header">
          <span class="kpi-stat-label">Tidak Tersedia</span>
          <span class="kpi-stat-icon"><i class="bi bi-slash-circle"></i></span>
        </div>
        <div class="kpi-stat-value">{{ $unavailableKamar }} <span class="fs-6 fw-medium text-muted">unit</span></div>
        <div class="kpi-stat-meta">
          <span>Nonaktif / dalam perawatan</span>
        </div>
      </div>
    </div>

    <!-- RINGKASAN DATA KAMAR -->
    <div class="card mb-4">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5 class="card-title mb-0">Ringkasan Data Kamar</h5>
          <span class="fs-8 text-muted">Daftar unit kamar Jumala Living beserta tarif dan status ketersediaan terbaru</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('admin.kamar.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Kamar
          </a>
          <a href="{{ route('admin.kamar.index') }}" class="btn btn-sm btn-light border">
            <span>Kelola Semua</span>
            <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 table-min-md">
            <thead>
              <tr>
                <th class="ps-4">Unit Kamar</th>
                <th>Tipe & Luas</th>
                <th>Harga Sewa</th>
                <th>Status</th>
                <th>Penghuni</th>
                <th class="text-end pe-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roomsSummary as $room)
                @php
                  $monthly = $room->monthly_price;
                  $rawPrice = $monthly ? (float) $monthly->price : 0;
                  $disc = $monthly && (float) $monthly->discount > 0 ? (float) $monthly->discount : (float) ($room->cumulative_discount ?? 0);
                  $netPrice = ($rawPrice > 0 && $disc > 0) ? ($disc <= 100 ? $rawPrice * (1 - $disc / 100) : max(0, $rawPrice - $disc)) : $rawPrice;
                  $status = $room->room_status;
                @endphp
                <tr>
                  <td class="ps-4">
                    <div class="d-flex align-items-center gap-2.5">
                      @if($room->primary_image)
                        <img src="{{ asset('storage/' . $room->primary_image->image) }}" alt="{{ $room->room }}" class="table-room-thumb">
                      @else
                        <div class="table-room-thumb-placeholder">
                          <i class="bi bi-door-closed"></i>
                        </div>
                      @endif
                      <div>
                        <a href="{{ route('admin.kamar.show', $room->id) }}" class="table-cell-title text-decoration-none">
                          {{ $room->room }}
                        </a>
                        @if($room->lantai)
                          <div class="table-cell-sub">{{ $room->lantai }}</div>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="fs-8 fw-semibold text-dark">{{ $room->tipe_kamar ?: 'Standard' }}</div>
                    <div class="fs-9 text-muted">{{ $room->luas_kamar ?: '-' }}</div>
                  </td>
                  <td class="tabular-nums">
                    @if($rawPrice > 0)
                      <div class="fw-bold text-dark fs-8">Rp {{ number_format($netPrice, 0, ',', '.') }}<span class="text-muted fw-normal">/bln</span></div>
                      @if($disc > 0)
                        <div class="fs-9 text-muted text-decoration-line-through">Rp {{ number_format($rawPrice, 0, ',', '.') }}</div>
                      @endif
                    @else
                      <span class="badge badge-subtle-secondary">Belum diatur</span>
                    @endif
                  </td>
                  <td>
                    @if($status === 'terisi')
                      <span class="badge badge-subtle-primary"><i class="bi bi-person-fill-check me-1"></i>Terisi</span>
                    @elseif($status === 'tidak_tersedia')
                      <span class="badge badge-subtle-secondary"><i class="bi bi-slash-circle me-1"></i>Tidak Tersedia</span>
                    @elseif($status === 'pending')
                      <span class="badge badge-subtle-warning"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                    @else
                      <span class="badge badge-subtle-success"><i class="bi bi-check-circle me-1"></i>Tersedia</span>
                    @endif
                  </td>
                  <td>
                    @if($room->active_tenant)
                      <div class="fs-8 fw-semibold text-dark">{{ Str::limit($room->active_tenant->name, 18) }}</div>
                      <div class="fs-9 text-muted">s/d {{ \Carbon\Carbon::parse($room->active_tenant->end_date)->format('d M Y') }}</div>
                    @elseif($room->pending_tenant)
                      <div class="fs-8 text-warning fw-semibold">{{ Str::limit($room->pending_tenant->name, 18) }}</div>
                      <div class="fs-9 text-muted">Menunggu approval</div>
                    @else
                      <span class="text-muted fs-8">-</span>
                    @endif
                  </td>
                  <td class="text-end pe-4">
                    <div class="action-btn-group">
                      <a href="{{ route('admin.kamar.show', $room->id) }}" class="btn btn-xs btn-light border" title="Detail Kamar">
                        <i class="bi bi-eye"></i> Detail
                      </a>
                      <a href="{{ route('admin.kamar.edit', $room->id) }}" class="btn btn-xs btn-outline-primary" title="Edit Kamar">
                        <i class="bi bi-pencil"></i> Edit
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4">
                    <div class="empty-state-box">
                      <div class="empty-state-icon"><i class="bi bi-door-open"></i></div>
                      <h6>Belum Ada Unit Kamar</h6>
                      <p class="mb-3">Tambahkan kamar pertama Jumala Living untuk mulai mengelola dan mempublikasikan di website.</p>
                      <a href="{{ route('admin.kamar.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Kamar Pertama
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PENDAPATAN & AKTIVITAS PENGELOLAAN TERBARU -->
    <div class="row g-3">
      <div class="col-12 col-lg-7">
        <div class="card h-100">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h5 class="card-title mb-0">Pendapatan Sewa (6 Bulan Terakhir)</h5>
              <span class="fs-8 text-muted">Bulan ini: <strong class="text-dark">Rp {{ number_format($currentMonthRevenue, 0, ',', '.') }}</strong></span>
            </div>
            <span class="badge badge-subtle-gold tabular-nums">
              Total 6 Bln: Rp {{ number_format($sixMonthTotal, 0, ',', '.') }}
            </span>
          </div>
          <div class="card-body d-flex flex-column justify-content-center">
            <div style="min-height: 230px; position: relative; width: 100%;">
              <canvas id="revenueChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-5">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0">Aktivitas Pengelolaan Terbaru</h5>
              <span class="fs-8 text-muted">Pembaruan kamar dan transaksi terkini</span>
            </div>
            <a href="{{ route('admin.booking.index') }}" class="fs-8 fw-semibold text-gold text-decoration-none">
              Booking
            </a>
          </div>
          <div class="card-body">
            <div class="dash-timeline">
              @forelse($recentActivities as $act)
                <div class="dash-timeline-item">
                  <div class="dash-timeline-dot"></div>
                  <div class="dash-timeline-time">{{ $act['time'] }}</div>
                  <div class="fs-8 fw-bold text-dark">{{ $act['title'] }}</div>
                  <div class="fs-8 text-muted">{{ $act['detail'] }}</div>
                </div>
              @empty
                <div class="text-center py-4 text-muted fs-8">
                  <i class="bi bi-clock-history fs-3 d-block mb-1.5 opacity-50"></i>
                  Belum ada aktivitas pengelolaan tercatat.
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const labels = {!! json_encode(array_column($revenueTrend, 'month')) !!};
    const values = {!! json_encode(array_column($revenueTrend, 'revenue')) !!};

    const chartCtx = ctx.getContext('2d');
    const areaGradient = chartCtx.createLinearGradient(0, 0, 0, 240);
    areaGradient.addColorStop(0, 'rgba(198, 161, 91, 0.28)');
    areaGradient.addColorStop(1, 'rgba(198, 161, 91, 0.0)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Pendapatan',
          data: values,
          borderColor: '#3B1D12',
          borderWidth: 2.5,
          backgroundColor: areaGradient,
          fill: true,
          tension: 0.35,
          pointBackgroundColor: '#FFFFFF',
          pointBorderColor: '#C6A15B',
          pointBorderWidth: 2.5,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointHoverBackgroundColor: '#C6A15B',
          pointHoverBorderColor: '#3B1D12',
          pointHoverBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#3B1D12',
            titleColor: '#E1C98E',
            bodyColor: '#FFFFFF',
            padding: 12,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: (context) => 'Rp ' + Number(context.raw).toLocaleString('id-ID')
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            border: { color: '#E9DDCD' },
            ticks: { color: '#846E60', font: { family: 'Plus Jakarta Sans', size: 11 } }
          },
          y: {
            beginAtZero: true,
            grid: { color: '#F2EAE0', drawBorder: false },
            ticks: {
              color: '#846E60',
              font: { family: 'Plus Jakarta Sans', size: 11 },
              callback: (value) => value >= 1000000 ? 'Rp ' + (value / 1000000).toFixed(1) + ' jt' : (value >= 1000 ? 'Rp ' + (value / 1000).toFixed(0) + ' rb' : 'Rp ' + value)
            }
          }
        }
      }
    });
  });
</script>

@endsection
