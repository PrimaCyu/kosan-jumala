@extends('backend.dashboard.main')
@section('content')

<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <h1 class="page-title">
          <i class="bi bi-plus-circle"></i> Tambah Kamar Baru
        </h1>
        <p class="page-subtitle">Tambahkan unit kamar baru untuk properti Jumala Living lengkap dengan tarif, foto, dan fasilitas.</p>
      </div>
      <div class="page-header-actions">
        <a href="{{ route('admin.kamar.index') }}" class="btn btn-light border">
          <i class="bi bi-arrow-left"></i>
          <span>Kembali ke Daftar Kamar</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    @include('backend.dashboard.kosan.kamar.partials.alerts')

    <form action="{{ route('admin.kamar.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('backend.dashboard.kamar._form')
    </form>
  </div>
</div>

@endsection
