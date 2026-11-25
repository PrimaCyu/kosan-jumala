@extends('backend.dashboard.main')
@section('content')

<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <h1 class="page-title">
          <i class="bi bi-door-open"></i> Manajemen Kamar Jumala Living
        </h1>
        <p class="page-subtitle">Kelola seluruh unit kamar, tarif sewa, fasilitas, galeri foto, dan status ketersediaan.</p>
      </div>
      <div class="page-header-actions">
        <a href="{{ route('admin.properti.edit') }}" class="btn btn-light border">
          <i class="bi bi-house-gear"></i>
          <span>Profil Hunian</span>
        </a>
        <a href="{{ route('admin.kamar.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg"></i>
          <span>Tambah Kamar Baru</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    @include('backend.dashboard.kosan.kamar.partials.alerts')

    <!-- Filter Status Pills & Pencarian -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div class="status-filter-nav">
        <a href="{{ route('admin.kamar.index', request()->except(['status', 'page'])) }}"
           class="status-filter-btn {{ ($statusFilter ?? 'semua') === 'semua' ? 'active' : '' }}">
          Semua Kamar
          <span class="badge badge-subtle-secondary rounded-pill ms-1">{{ $kpiStats['total_kamar'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.kamar.index', array_merge(['status' => 'tersedia'], request()->except(['status', 'page']))) }}"
           class="status-filter-btn {{ ($statusFilter ?? '') === 'tersedia' ? 'active' : '' }}">
          <i class="bi bi-check-circle text-success"></i> Tersedia
          <span class="badge badge-subtle-success rounded-pill ms-1">{{ $kpiStats['tersedia_kamar'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.kamar.index', array_merge(['status' => 'terisi'], request()->except(['status', 'page']))) }}"
           class="status-filter-btn {{ ($statusFilter ?? '') === 'terisi' ? 'active' : '' }}">
          <i class="bi bi-person-fill"></i> Terisi
          <span class="badge badge-subtle-primary rounded-pill ms-1">{{ $kpiStats['terisi_kamar'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.kamar.index', array_merge(['status' => 'pending'], request()->except(['status', 'page']))) }}"
           class="status-filter-btn {{ ($statusFilter ?? '') === 'pending' ? 'active' : '' }}">
          <i class="bi bi-hourglass-split text-warning"></i> Pending
          <span class="badge badge-subtle-warning rounded-pill ms-1">{{ $kpiStats['pending_kamar'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.kamar.index', array_merge(['status' => 'tidak_tersedia'], request()->except(['status', 'page']))) }}"
           class="status-filter-btn {{ ($statusFilter ?? '') === 'tidak_tersedia' ? 'active' : '' }}">
          <i class="bi bi-slash-circle text-muted"></i> Tidak Tersedia
          <span class="badge badge-subtle-secondary rounded-pill ms-1">{{ $kpiStats['tidak_tersedia_kamar'] ?? 0 }}</span>
        </a>
      </div>

      @if(request('status') || request('search'))
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-xs btn-light border">
          <i class="bi bi-x-circle"></i> Reset Filter
        </a>
      @endif
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
          <h5 class="card-title mb-0">Daftar Unit Kamar</h5>
          <span class="fs-8 text-muted">Menampilkan {{ $kamar_kosan->total() }} unit kamar sesuai filter aktif.</span>
        </div>

        <form action="{{ route('admin.kamar.index') }}" method="GET" class="search-box-responsive">
          @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
          @endif
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted">
              <i class="bi bi-search"></i>
            </span>
            <input
              type="text"
              name="search"
              class="form-control border-start-0 ps-0"
              placeholder="Cari kamar, tipe, luas, fasilitas..."
              value="{{ request('search') }}"
              autocomplete="off"
            >
            <button type="submit" class="btn btn-sm btn-primary px-3">Cari</button>
          </div>
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 table-min-lg">
            <thead>
              <tr>
                <th class="ps-4" style="width: 52px;">No</th>
                <th>Foto & Unit Kamar</th>
                <th>Tipe & Dimensi</th>
                <th>Harga Sewa</th>
                <th>Fasilitas</th>
                <th>Status Ketersediaan</th>
                <th class="text-end pe-4" style="width: 210px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($kamar_kosan as $index => $item)
                @php
                  $monthly = $item->monthly_price;
                  $yearly  = $item->yearly_price;
                  $rawPrice = $monthly ? (float) $monthly->price : 0;
                  $disc     = $monthly && (float) $monthly->discount > 0 ? (float) $monthly->discount : (float) ($item->cumulative_discount ?? 0);
                  $netPrice = ($rawPrice > 0 && $disc > 0)
                      ? ($disc <= 100 ? $rawPrice * (1 - $disc / 100) : max(0, $rawPrice - $disc))
                      : $rawPrice;
                  $fasilitasArr = array_values(array_filter(array_map('trim', explode(',', $item->fasilitas ?? ''))));
                  $status = $item->room_status;
                @endphp
                <tr>
                  <td class="ps-4 text-muted fw-semibold tabular-nums">{{ $kamar_kosan->firstItem() + $index }}</td>
                  <td>
                    <div class="d-flex align-items-center gap-2.5">
                      @if($item->primary_image)
                        <img src="{{ asset('storage/' . $item->primary_image->image) }}" alt="{{ $item->room }}" class="table-room-thumb">
                      @else
                        <div class="table-room-thumb-placeholder">
                          <i class="bi bi-door-closed"></i>
                        </div>
                      @endif
                      <div>
                        <a href="{{ route('admin.kamar.show', $item->id) }}" class="table-cell-title text-decoration-none d-inline-flex align-items-center gap-1.5">
                          <span>{{ $item->room }}</span>
                          @if($item->has_video)
                            <span class="badge badge-subtle-gold fs-9"><i class="bi bi-camera-video"></i></span>
                          @endif
                        </a>
                        <div class="table-cell-sub d-flex align-items-center gap-2 mt-0.5">
                          <span><i class="bi bi-images me-1"></i>{{ $item->productKamarImageKosan->count() }} foto</span>
                          <span>&bull;</span>
                          <span><i class="bi bi-eye me-1"></i>{{ number_format($item->views ?? 0) }}</span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="fs-8 fw-semibold text-dark">{{ $item->tipe_kamar ?: 'Standard' }}</div>
                    <div class="fs-9 text-muted">
                      {{ $item->luas_kamar ?: '-' }}
                      @if($item->lantai) &bull; {{ $item->lantai }} @endif
                    </div>
                  </td>
                  <td class="tabular-nums text-nowrap">
                    @if($rawPrice > 0)
                      <div class="fw-bold text-dark fs-8">
                        Rp {{ number_format($netPrice, 0, ',', '.') }}<span class="text-muted fw-normal">/bln</span>
                      </div>
                      @if($disc > 0)
                        <div class="d-flex align-items-center gap-1.5">
                          <span class="fs-9 text-muted text-decoration-line-through">Rp {{ number_format($rawPrice, 0, ',', '.') }}</span>
                          <span class="badge badge-subtle-success fs-9">-{{ round($disc) }}%</span>
                        </div>
                      @endif
                      @if($yearly)
                        <div class="fs-9 text-muted">Rp {{ number_format((float) $yearly->price, 0, ',', '.') }}/thn</div>
                      @endif
                    @else
                      <span class="badge badge-subtle-secondary">Belum diatur</span>
                    @endif
                  </td>
                  <td style="max-width: 210px;">
                    @if(!empty($fasilitasArr))
                      <div class="d-flex flex-wrap gap-1">
                        @foreach(array_slice($fasilitasArr, 0, 3) as $fas)
                          <span class="badge badge-subtle-secondary fs-9">{{ $fas }}</span>
                        @endforeach
                        @if(count($fasilitasArr) > 3)
                          <span class="badge badge-subtle-gold fs-9">+{{ count($fasilitasArr) - 3 }}</span>
                        @endif
                      </div>
                    @else
                      <span class="text-muted fs-8">-</span>
                    @endif
                  </td>
                  <td class="text-nowrap">
                    @if($status === 'terisi')
                      <span class="badge badge-subtle-primary"><i class="bi bi-person-fill-check me-1"></i>Terisi</span>
                      @if($item->active_tenant)
                        <div class="fs-9 text-muted mt-1">{{ Str::limit($item->active_tenant->name, 16) }} &bull; s/d {{ \Carbon\Carbon::parse($item->active_tenant->end_date)->format('d/m/y') }}</div>
                      @endif
                    @elseif($status === 'tidak_tersedia')
                      <span class="badge badge-subtle-secondary"><i class="bi bi-slash-circle me-1"></i>Tidak Tersedia</span>
                      <div class="fs-9 text-muted mt-1">Dinonaktifkan</div>
                    @elseif($status === 'pending')
                      <span class="badge badge-subtle-warning"><i class="bi bi-clock-history me-1"></i>Pending</span>
                      @if($item->pending_tenant)
                        <div class="fs-9 text-muted mt-1">{{ Str::limit($item->pending_tenant->name, 16) }}</div>
                      @endif
                    @else
                      <span class="badge badge-subtle-success"><i class="bi bi-check-circle-fill me-1"></i>Tersedia</span>
                    @endif
                  </td>
                  <td class="text-end pe-4 text-nowrap">
                    <div class="action-btn-group">
                      <a href="{{ route('admin.kamar.show', $item->id) }}" class="btn btn-xs btn-light border" title="Detail Kamar">
                        <i class="bi bi-eye"></i> Detail
                      </a>
                      <a href="{{ route('admin.kamar.edit', $item->id) }}" class="btn btn-xs btn-outline-primary" title="Edit Kamar">
                        <i class="bi bi-pencil"></i> Edit
                      </a>
                      <div class="dropdown">
                        <button class="btn btn-xs btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opsi lainnya">
                          <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end fs-8" style="min-width: 190px;">
                          <li>
                            <form action="{{ route('admin.kamar.toggle-status', $item->id) }}" method="POST" class="m-0">
                              @csrf
                              @method('PATCH')
                              <button type="submit" class="dropdown-item py-1.5 d-flex align-items-center gap-2">
                                @if($item->isBookable())
                                  <i class="bi bi-pause-circle text-warning"></i>
                                  <span>Set Tidak Tersedia</span>
                                @else
                                  <i class="bi bi-play-circle text-success"></i>
                                  <span>Aktifkan (Tersedia)</span>
                                @endif
                              </button>
                            </form>
                          </li>
                          @if($item->isBookable())
                            <li>
                              <a href="{{ route('kamar.detail', $item->id) }}" target="_blank" class="dropdown-item py-1.5 d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-up-right text-secondary"></i>
                                <span>Lihat di Website</span>
                              </a>
                            </li>
                          @endif
                          <li><hr class="dropdown-divider my-1"></li>
                          <li>
                            <button type="button" class="dropdown-item py-1.5 d-flex align-items-center gap-2 text-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteKamar{{ $item->id }}">
                              <i class="bi bi-trash3"></i>
                              <span>Hapus Kamar</span>
                            </button>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="empty-state-box">
                      <div class="empty-state-icon"><i class="bi bi-door-closed"></i></div>
                      <h6>Tidak Ada Unit Kamar Ditemukan</h6>
                      <p class="mb-3">Belum ada kamar yang cocok dengan pencarian atau filter status saat ini.</p>
                      <a href="{{ route('admin.kamar.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Kamar Baru
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($kamar_kosan->total() > 0)
        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
          <small class="text-muted fs-8">
            Menampilkan {{ $kamar_kosan->firstItem() ?? 0 }}–{{ $kamar_kosan->lastItem() ?? 0 }} dari {{ $kamar_kosan->total() }} unit kamar
          </small>
          <div>
            {{ $kamar_kosan->links('pagination::bootstrap-5') }}
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@foreach($kamar_kosan as $item)
  <div class="modal fade" id="modalDeleteKamar{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-body text-center p-4">
          <div class="empty-state-icon bg-danger-subtle text-danger mx-auto mb-3 border-0">
            <i class="bi bi-trash3"></i>
          </div>
          <h6 class="fw-bold text-dark mb-1">Hapus {{ $item->room }}?</h6>
          <p class="text-muted fs-8 mb-3">
            @if($item->active_tenant)
              Kamar ini sedang ditempati oleh <strong>{{ $item->active_tenant->name }}</strong> sehingga tidak dapat dihapus.
            @else
              Unit kamar akan dihapus dari katalog aktif. Riwayat transaksi booking lama tetap tersimpan aman.
            @endif
          </p>
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-sm btn-light border flex-grow-1" data-bs-dismiss="modal">Batal</button>
            @if(!$item->active_tenant)
              <form action="{{ route('admin.kamar.destroy', $item->id) }}" method="POST" class="flex-grow-1 m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger w-100">Ya, Hapus</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach

@endsection
