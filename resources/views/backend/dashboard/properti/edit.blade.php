@extends('backend.dashboard.main')
@section('content')

@php
  $sharedPresets = [
      'Wi-Fi / Internet',
      'Parkir Mobil',
      'Parkir Motor',
      'Dapur Bersama',
      'CCTV 24 Jam',
      'Keamanan / Satpam',
      'Ruang Tamu Bersama',
      'Ruang Jemur',
      'Mesin Cuci Bersama',
      'Kulkas Bersama',
      'Air Minum / Dispenser',
      'Penjaga Kos',
      'Listrik Gratis / Included',
      'Akses Kartu / Smart Lock',
      'Musholla',
  ];
  $currentShared = array_values(array_filter(array_map('trim', explode(',', (string) $kosan->fasilitas))));
  $extraShared   = array_diff($currentShared, $sharedPresets);
@endphp

<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <h1 class="page-title">
          <i class="bi bi-house-gear"></i> Profil & Fasilitas Hunian Jumala Living
        </h1>
        <p class="page-subtitle">Atur informasi utama bangunan, alamat Google Maps, foto galeri hunian, dan fasilitas bersama.</p>
      </div>
      <div class="page-header-actions">
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-light border">
          <i class="bi bi-door-open"></i>
          <span>Kelola Kamar</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    @include('backend.dashboard.kosan.kamar.partials.alerts')

    <form action="{{ route('admin.properti.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row g-4">
        <div class="col-12 col-lg-8">
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-gold"></i>Informasi Utama Hunian</h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="title" class="form-label">Nama Hunian <span class="text-danger">*</span></label>
                  <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $kosan->title) }}" required>
                </div>
                <div class="col-md-6">
                  <label for="wilayah" class="form-label">Alamat Lengkap</label>
                  <input type="text" name="wilayah" id="wilayah" class="form-control" value="{{ old('wilayah', $kosan->wilayah) }}">
                </div>
                <div class="col-12">
                  <label for="gmaps" class="form-label">Tautan Google Maps</label>
                  <input type="text" name="gmaps" id="gmaps" class="form-control" value="{{ old('gmaps', $kosan->gmaps) }}" placeholder="https://maps.google.com/...">
                </div>
                <div class="col-12">
                  <label for="description" class="form-label">Deskripsi Hunian</label>
                  <textarea name="description" id="description" class="form-control summernote-editor" rows="4">{{ old('description', $kosan->description) }}</textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-header">
              <h5 class="card-title mb-0"><i class="bi bi-check2-square me-2 text-gold"></i>Fasilitas Bersama Gedung</h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-2 mb-3">
                @foreach($sharedPresets as $idx => $fas)
                  <div class="col-6 col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="fasilitas[]" value="{{ $fas }}" id="shared_{{ $idx }}" {{ in_array($fas, $currentShared) ? 'checked' : '' }}>
                      <label class="form-check-label fs-8 text-dark cursor-pointer" for="shared_{{ $idx }}">{{ $fas }}</label>
                    </div>
                  </div>
                @endforeach
              </div>
              <div>
                <label for="fasilitas_tambahan" class="form-label fs-8">Fasilitas Bersama Tambahan <span class="text-muted fw-normal">(pisahkan koma)</span></label>
                <input type="text" name="fasilitas_tambahan" id="fasilitas_tambahan" class="form-control" value="{{ old('fasilitas_tambahan', implode(', ', $extraShared)) }}">
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0"><i class="bi bi-images me-2 text-gold"></i>Foto Utama & Galeri Gedung</h5>
            </div>
            <div class="card-body p-4">
              @if($kosan->productImageKosan->count() > 0)
                <div class="row g-2.5 mb-4">
                  @foreach($kosan->productImageKosan as $img)
                    <div class="col-6 col-sm-4 col-md-3">
                      <div class="border rounded-3 p-1.5 bg-light text-center">
                        <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $kosan->title }}" class="w-100 rounded-2 object-fit-cover mb-1.5" style="height: 100px;">
                        <div class="form-check form-check-inline m-0">
                          <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $img->id }}" id="del_prop_{{ $img->id }}">
                          <label class="form-check-label fs-9 text-danger fw-semibold cursor-pointer" for="del_prop_{{ $img->id }}">Hapus foto</label>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif

              <div class="mb-4">
                <label for="propertiImages" class="form-label">Unggah Foto Bangunan / Hero Beranda</label>
                <input type="file" name="images[]" id="propertiImages" class="form-control" accept="image/*" multiple>
                <div class="form-text fs-8">Foto pertama digunakan sebagai foto utama pada bagian Hero dan Tentang Jumala Living di beranda website.</div>
              </div>

              @include('backend.dashboard.kosan.partials-kosan._video_input', [
                  'uid' => 'properti_jumala',
                  'model' => $kosan,
                  'contextLabel' => 'Hunian Jumala Living',
              ])
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card mb-4">
            <div class="card-body p-4 text-center">
              <img src="{{ asset('logo.jpeg') }}" alt="Jumala Living" class="rounded-4 border mb-3" style="width: 88px; height: 88px; object-fit: cover;">
              <h5 class="fw-bold text-dark mb-1">{{ $kosan->title }}</h5>
              <p class="fs-8 text-muted mb-3">{{ $kosan->wilayah }}</p>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check2-circle"></i>
                  <span>Simpan Profil Hunian</span>
                </button>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-light border">
                  <i class="bi bi-globe2"></i>
                  <span>Lihat Tampilan Beranda</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
