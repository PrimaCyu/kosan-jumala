@extends('backend.dashboard.main')
@section('content')

<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <h1 class="page-title">
          <i class="bi bi-pencil-square"></i> Edit Kamar: {{ $kamar->room }}
        </h1>
        <p class="page-subtitle">Perbarui informasi kamar, harga sewa, foto galeri, fasilitas, maupun status ketersediaan.</p>
      </div>
      <div class="page-header-actions">
        <a href="{{ route('admin.kamar.show', $kamar->id) }}" class="btn btn-light border">
          <i class="bi bi-eye"></i>
          <span>Lihat Detail</span>
        </a>
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-light border">
          <i class="bi bi-arrow-left"></i>
          <span>Daftar Kamar</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    @include('backend.dashboard.kosan.kamar.partials.alerts')

    <form action="{{ route('admin.kamar.update', $kamar->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('backend.dashboard.kamar._form', ['kamar' => $kamar])
    </form>
  </div>
</div>

@endsection
