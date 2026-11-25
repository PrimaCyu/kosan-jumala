@extends('backend.dashboard.main')
@section('content')

@php
  $status = $kamar->room_status;
  $monthly = $kamar->monthly_price;
  $yearly  = $kamar->yearly_price;
  $rawMonthly = $monthly ? (float) $monthly->price : 0;
  $disc = $monthly && (float) $monthly->discount > 0 ? (float) $monthly->discount : (float) ($kamar->cumulative_discount ?? 0);
  $netMonthly = ($rawMonthly > 0 && $disc > 0)
      ? ($disc <= 100 ? $rawMonthly * (1 - $disc / 100) : max(0, $rawMonthly - $disc))
      : $rawMonthly;
  $fasilitasArr = array_values(array_filter(array_map('trim', explode(',', (string) $kamar->fasilitas))));
  $activeTenant = $kamar->active_tenant;
@endphp

<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <div class="d-flex align-items-center flex-wrap gap-2 mb-1.5">
          @if($status === 'terisi')
            <span class="badge badge-subtle-primary"><i class="bi bi-person-fill-check me-1"></i>Terisi</span>
          @elseif($status === 'tidak_tersedia')
            <span class="badge badge-subtle-secondary"><i class="bi bi-slash-circle me-1"></i>Tidak Tersedia (Nonaktif)</span>
          @elseif($status === 'pending')
            <span class="badge badge-subtle-warning"><i class="bi bi-clock-history me-1"></i>Pending Verifikasi</span>
          @else
            <span class="badge badge-subtle-success"><i class="bi bi-check-circle-fill me-1"></i>Tersedia (Siap Huni)</span>
          @endif
          @if($kamar->tipe_kamar)
            <span class="badge badge-subtle-gold">{{ $kamar->tipe_kamar }}</span>
          @endif
          @if($kamar->luas_kamar)
            <span class="badge badge-subtle-secondary">{{ $kamar->luas_kamar }}</span>
          @endif
          @if($kamar->lantai)
            <span class="badge badge-subtle-secondary">{{ $kamar->lantai }}</span>
          @endif
        </div>
        <h1 class="page-title">
          <i class="bi bi-door-open"></i> Detail Kamar: {{ $kamar->room }}
        </h1>
        <p class="page-subtitle">Informasi lengkap unit kamar Jumala Living, tarif sewa, galeri foto, dan riwayat penyewa.</p>
      </div>
      <div class="page-header-actions">
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-light border">
          <i class="bi bi-arrow-left"></i>
          <span>Daftar Kamar</span>
        </a>
        @if($kamar->isBookable())
          <a href="{{ route('kamar.detail', $kamar->id) }}" target="_blank" class="btn btn-light border">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Lihat di Website</span>
          </a>
        @endif
        <form action="{{ route('admin.kamar.toggle-status', $kamar->id) }}" method="POST" class="d-inline m-0">
          @csrf
          @method('PATCH')
          <button type="submit" class="btn {{ $kamar->isBookable() ? 'btn-light border' : 'btn-gold' }}">
            <i class="bi {{ $kamar->isBookable() ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
            <span>{{ $kamar->isBookable() ? 'Nonaktifkan Kamar' : 'Aktifkan Kamar' }}</span>
          </button>
        </form>
        <a href="{{ route('admin.kamar.edit', $kamar->id) }}" class="btn btn-primary">
          <i class="bi bi-pencil-square"></i>
          <span>Edit Kamar</span>
        </a>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteDetailKamar">
          <i class="bi bi-trash3"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    @include('backend.dashboard.kosan.kamar.partials.alerts')

    <div class="row g-4">
      <!-- Kiri: Galeri Foto, Video, Deskripsi & Fasilitas -->
      <div class="col-12 col-lg-8">
        <!-- Galeri Foto Kamar -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0"><i class="bi bi-images me-2 text-gold"></i>Galeri Foto Kamar ({{ $kamar->productKamarImageKosan->count() }})</h5>
            </div>
            <form action="{{ route('admin.kamar.image.store', $kamar->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2 m-0">
              @csrf
              <input type="file" name="images[]" accept="image/*" multiple class="form-control form-control-sm" style="max-width: 210px;" required>
              <button type="submit" class="btn btn-xs btn-primary">
                <i class="bi bi-upload"></i> Upload
              </button>
            </form>
          </div>
          <div class="card-body p-4">
            @if($kamar->productKamarImageKosan->count() > 0)
              <div class="row g-3">
                @foreach($kamar->productKamarImageKosan as $img)
                  <div class="col-6 col-sm-4 col-md-3">
                    <div class="border rounded-3 overflow-hidden bg-light position-relative">
                      <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $kamar->room }}" class="w-100 object-fit-cover" style="height: 130px;">
                      <form action="{{ route('admin.kamar.image.destroy', [$kamar->id, $img->id]) }}" method="POST" class="p-1.5 text-center border-top m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger w-100" onclick="return confirm('Hapus foto kamar ini?')">
                          <i class="bi bi-trash3"></i> Hapus
                        </button>
                      </form>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="empty-state-box py-3">
                <div class="empty-state-icon"><i class="bi bi-image"></i></div>
                <h6>Belum Ada Foto Kamar</h6>
                <p>Unggah foto interior kamar agar tampil menarik di website publik Jumala Living.</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Deskripsi & Fasilitas -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-card-text me-2 text-gold"></i>Deskripsi, Spesifikasi & Fasilitas</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3 mb-4 pb-3 border-bottom">
              <div class="col-6 col-md-3">
                <div class="fs-9 text-uppercase text-muted fw-bold">Nama / Nomor</div>
                <div class="fw-bold text-dark">{{ $kamar->room }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="fs-9 text-uppercase text-muted fw-bold">Tipe Kamar</div>
                <div class="fw-semibold text-dark">{{ $kamar->tipe_kamar ?: 'Standard' }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="fs-9 text-uppercase text-muted fw-bold">Luas Kamar</div>
                <div class="fw-semibold text-dark">{{ $kamar->luas_kamar ?: '-' }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="fs-9 text-uppercase text-muted fw-bold">Lantai / Dilihat</div>
                <div class="fw-semibold text-dark">{{ $kamar->lantai ?: '-' }} &bull; {{ number_format($kamar->views ?? 0) }}x</div>
              </div>
            </div>

            <div class="mb-4">
              <h6 class="fw-bold fs-8 text-uppercase text-muted mb-2">Fasilitas Kamar</h6>
              @if(!empty($fasilitasArr))
                <div class="d-flex flex-wrap gap-1.5">
                  @foreach($fasilitasArr as $fas)
                    <span class="badge badge-subtle-gold py-1.5 px-2.5">
                      <i class="bi bi-check2 me-1"></i>{{ $fas }}
                    </span>
                  @endforeach
                </div>
              @else
                <span class="text-muted fs-8">Belum ada fasilitas dipilih.</span>
              @endif
            </div>

            <div class="mb-3">
              <h6 class="fw-bold fs-8 text-uppercase text-muted mb-2">Deskripsi Kamar</h6>
              <div class="p-3 rounded-3 bg-light border fs-8 text-secondary">
                @if(!empty(trim(strip_tags($kamar->description ?? ''))))
                  {!! \App\Support\HtmlSanitizer::clean($kamar->description) !!}
                @else
                  <span class="text-muted fst-italic">Deskripsi kamar belum ditambahkan.</span>
                @endif
              </div>
            </div>

            @if(!empty($kamar->catatan))
              <div>
                <h6 class="fw-bold fs-8 text-uppercase text-muted mb-1">Catatan Tambahan</h6>
                <div class="fs-8 text-dark">{{ $kamar->catatan }}</div>
              </div>
            @endif
          </div>
        </div>

        <!-- Riwayat Booking & Penyewa -->
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-people me-2 text-gold"></i>Riwayat Penyewa & Booking ({{ $kamar->tamu->count() }})</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th class="ps-4">Penyewa</th>
                    <th>Periode Sewa</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($kamar->tamu as $tamu)
                    <tr>
                      <td class="ps-4">
                        <div class="fw-bold fs-8 text-dark">{{ $tamu->name }}</div>
                        <div class="fs-9 text-muted">{{ $tamu->telp }} &bull; {{ $tamu->email }}</div>
                      </td>
                      <td class="fs-8 tabular-nums">
                        {{ \Carbon\Carbon::parse($tamu->start_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($tamu->end_date)->format('d M Y') }}
                      </td>
                      <td class="fw-semibold tabular-nums fs-8">
                        Rp {{ number_format((float) $tamu->total_price, 0, ',', '.') }}
                      </td>
                      <td>
                        @if($tamu->status === 'approved')
                          <span class="badge badge-subtle-success">Disetujui</span>
                        @elseif($tamu->status === 'pending')
                          <span class="badge badge-subtle-warning">Pending</span>
                        @else
                          <span class="badge badge-subtle-danger">Ditolak</span>
                        @endif
                      </td>
                      <td class="text-end pe-4">
                        <form action="{{ route('admin.kamar.tamu.destroy', [$kamar->id, $tamu->id]) }}" method="POST" class="d-inline m-0">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Hapus catatan penyewa ini?')">
                            <i class="bi bi-trash3"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted fs-8">
                        Belum ada riwayat pemesanan untuk unit kamar ini.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Kanan: Ringkasan Tarif & Penghuni Aktif -->
      <div class="col-12 col-lg-4">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2 text-gold"></i>Informasi Tarif Sewa</h5>
          </div>
          <div class="card-body p-4">
            <div class="p-3 rounded-3 bg-light border mb-3">
              <div class="fs-9 text-uppercase fw-bold text-muted">Tarif Bulanan</div>
              @if($rawMonthly > 0)
                @if($disc > 0)
                  <div class="fs-8 text-muted text-decoration-line-through mt-1">Rp {{ number_format($rawMonthly, 0, ',', '.') }}</div>
                @endif
                <div class="fs-4 fw-extrabold text-primary tabular-nums">
                  Rp {{ number_format($netMonthly, 0, ',', '.') }} <span class="fs-8 fw-normal text-muted">/ bulan</span>
                </div>
                @if($disc > 0)
                  <span class="badge badge-subtle-success mt-1">Promo Diskon {{ round($disc) }}%</span>
                @endif
              @else
                <div class="fs-8 text-muted mt-1">Tarif bulanan belum diatur.</div>
              @endif
            </div>

            <div class="p-3 rounded-3 bg-light border">
              <div class="fs-9 text-uppercase fw-bold text-muted">Tarif Tahunan</div>
              @if($yearly)
                <div class="fs-5 fw-bold text-dark tabular-nums mt-0.5">
                  Rp {{ number_format((float) $yearly->price, 0, ',', '.') }} <span class="fs-8 fw-normal text-muted">/ tahun</span>
                </div>
              @else
                <div class="fs-8 text-muted mt-1">Belum diatur.</div>
              @endif
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-person-badge me-2 text-gold"></i>Penghuni Aktif</h5>
          </div>
          <div class="card-body p-4">
            @if($activeTenant)
              <div class="fw-bold text-dark fs-6">{{ $activeTenant->name }}</div>
              <div class="fs-8 text-muted mb-2">{{ $activeTenant->email }}</div>
              <div class="p-2.5 rounded-3 bg-light border fs-8 mb-3">
                <div><strong>Periode:</strong> {{ \Carbon\Carbon::parse($activeTenant->start_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($activeTenant->end_date)->format('d M Y') }}</div>
                <div><strong>Metode:</strong> {{ $activeTenant->payment_method_label }}</div>
              </div>
              <div class="d-grid gap-2">
                @if($activeTenant->telp)
                  <a href="https://wa.me/{{ $activeTenant->whatsapp_number }}" target="_blank" class="btn btn-sm btn-light border">
                    <i class="bi bi-whatsapp text-success"></i> Hubungi via WhatsApp
                  </a>
                @endif
                <form action="{{ route('admin.kamar.tamu.renew', [$kamar->id, $activeTenant->id]) }}" method="POST" class="d-flex gap-1.5 m-0">
                  @csrf
                  @method('PUT')
                  <input type="number" name="bulan" value="1" min="1" max="12" class="form-control form-control-sm" style="max-width: 72px;" title="Jumlah bulan perpanjangan">
                  <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Perpanjang (+Bln)</button>
                </form>
              </div>
            @else
              <div class="text-center py-2 text-muted fs-8">
                Saat ini belum ada penyewa aktif yang menempati kamar ini.
              </div>
            @endif
          </div>
        </div>

        @if($kamar->has_video && $kamar->video_embed)
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0"><i class="bi bi-camera-video me-2 text-gold"></i>Video Tour Kamar</h5>
            </div>
            <div class="card-body p-3">
              @if($kamar->video_embed['type'] === 'file')
                <video controls class="w-100 rounded-3 bg-dark" style="max-height: 220px;">
                  <source src="{{ $kamar->video_embed['src'] }}" type="{{ $kamar->video_embed['mime'] ?? 'video/mp4' }}">
                </video>
              @else
                <a href="{{ $kamar->video_embed['original_url'] }}" target="_blank" class="btn btn-sm btn-light border w-100">
                  <i class="bi bi-play-circle"></i> Buka Video ({{ $kamar->video_embed['label'] }})
                </a>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Hapus Kamar -->
<div class="modal fade" id="modalDeleteDetailKamar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <div class="empty-state-icon bg-danger-subtle text-danger mx-auto mb-3 border-0">
          <i class="bi bi-trash3"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Hapus {{ $kamar->room }}?</h6>
        <p class="text-muted fs-8 mb-3">
          @if($activeTenant)
            Kamar sedang ditempati oleh <strong>{{ $activeTenant->name }}</strong> sehingga tidak dapat dihapus.
          @else
            Yakin ingin menghapus kamar ini? Data histori transaksi tetap terjaga.
          @endif
        </p>
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-sm btn-light border flex-grow-1" data-bs-dismiss="modal">Batal</button>
          @if(!$activeTenant)
            <form action="{{ route('admin.kamar.destroy', $kamar->id) }}" method="POST" class="flex-grow-1 m-0">
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

@endsection
