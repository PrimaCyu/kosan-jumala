@extends('backend.dashboard.main')

@section('content')
<div class="app-content-header">
  <div class="container-fluid">
    <div class="page-header-box">
      <div>
        <h1 class="page-title">
          <i class="bi bi-people"></i> Kelola Admin Pengelola
        </h1>
        <p class="page-subtitle">Atur akun pengelola operasional dan Super Admin untuk hunian Jumala Living.</p>
      </div>
      <div class="page-header-actions">
        <button type="button" class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
          <i class="bi bi-person-plus"></i>
          <span>Tambah Admin</span>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    @include('backend.dashboard.kosan.kamar.partials.alerts')

    <div class="card mb-4">
      <div class="card-header d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-2.5">
        <div>
          <h5 class="card-title mb-0">Daftar Akun Pengelola</h5>
          <span class="fs-8 text-muted">Menampilkan seluruh akun administrator sistem Jumala Living</span>
        </div>

        <div class="search-box-responsive">
          <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-1.5">
            <div class="search-filter-box flex-grow-1">
              <i class="bi bi-search"></i>
              <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama atau email...">
            </div>
            <button type="submit" class="btn btn-sm btn-light border px-2.5">Cari</button>
            @if(request('search'))
              <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary px-2" title="Reset Pencarian">
                <i class="bi bi-x-lg"></i>
              </a>
            @endif
          </form>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0 table-min-md">
            <thead>
              <tr>
                <th class="ps-4" style="width: 56px;">No</th>
                <th>Nama & Email</th>
                <th>Peran (Role)</th>
                <th>Cakupan Akses</th>
                <th>Terdaftar</th>
                <th class="text-end pe-4" style="width: 130px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $index => $u)
                <tr>
                  <td class="ps-4 text-muted tabular-nums">{{ $users->firstItem() + $index }}</td>
                  <td>
                    <div class="d-flex align-items-center gap-2.5">
                      <div class="user-avatar-badge">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                      </div>
                      <div>
                        <div class="table-cell-title">{{ $u->name }}</div>
                        <div class="table-cell-sub">{{ $u->email }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($u->isSuperAdmin())
                      <span class="badge badge-subtle-gold">
                        <i class="bi bi-shield-check me-1"></i>Super Admin
                      </span>
                    @else
                      <span class="badge badge-subtle-primary">
                        <i class="bi bi-person-badge me-1"></i>Admin Operasional
                      </span>
                    @endif
                  </td>
                  <td>
                    @if($u->isSuperAdmin())
                      <span class="fs-8 text-secondary">Akses penuh seluruh modul Jumala Living</span>
                    @else
                      <span class="fs-8 text-muted">Manajemen kamar, booking, profil hunian & testimoni</span>
                    @endif
                  </td>
                  <td class="text-muted fs-8 tabular-nums">
                    {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}
                  </td>
                  <td class="text-end pe-4">
                    <div class="action-btn-group">
                      <button type="button" class="btn btn-xs btn-light border px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditAdmin{{ $u->id }}" title="Edit Admin">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit</span>
                      </button>
                      @if($u->id !== auth()->id())
                        <button type="button" class="btn btn-xs btn-outline-danger px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalDeleteAdmin{{ $u->id }}" title="Hapus Admin">
                          <i class="bi bi-trash3"></i>
                        </button>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="empty-state-box">
                      <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                      <h6>Belum Ada Data Admin</h6>
                      <p>Tidak ditemukan akun pengelola yang sesuai dengan kriteria pencarian.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($users->hasPages())
        <div class="card-footer d-flex justify-content-end">
          {{ $users->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>

@foreach($users as $u)
  <div class="modal fade" id="modalEditAdmin{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Admin — {{ $u->name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
              </div>
              <div class="col-12">
                <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $u->email) }}" required>
              </div>
              <div class="col-12">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
              </div>
              <div class="col-12">
                <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                  <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin Operasional (Kamar, Booking & Testimoni)</option>
                  <option value="super_admin" {{ $u->role === 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Penuh Pengaturan)</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @if($u->id !== auth()->id())
    <div class="modal fade" id="modalDeleteAdmin{{ $u->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <div class="empty-state-icon bg-danger-subtle text-danger mx-auto mb-3 border-0">
              <i class="bi bi-trash3"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Hapus Akun Admin?</h6>
            <p class="text-muted fs-8 mb-3">
              Akun <strong>{{ $u->name }}</strong> akan dihapus secara permanen.
            </p>
            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-flex gap-2 justify-content-center m-0">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-sm btn-light border flex-grow-1" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-sm btn-danger flex-grow-1">Ya, Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif
@endforeach

<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Administrator Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Contoh: Pengelola Jumala" value="{{ old('name') }}" required>
            </div>
            <div class="col-12">
              <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="admin@jumalaliving.com" value="{{ old('email') }}" required>
            </div>
            <div class="col-12">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
            </div>
            <div class="col-12">
              <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
              <select name="role" class="form-select" required>
                <option value="admin" selected>Admin Operasional (Kamar, Booking & Testimoni)</option>
                <option value="super_admin">Super Admin (Akses Penuh Pengaturan)</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Admin</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
