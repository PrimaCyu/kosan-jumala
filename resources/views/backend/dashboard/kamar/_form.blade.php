@php
    $isEdit = isset($kamar) && $kamar->exists;
    $monthly = $isEdit ? $kamar->monthly_price : null;
    $yearly  = $isEdit ? $kamar->yearly_price : null;

    $defaultFasilitas = [
        'AC',
        'Kamar Mandi Dalam',
        'Water Heater / Air Hangat',
        'Kasur Springbed',
        'Lemari Pakaian',
        'Meja & Kursi Belajar',
        'TV / Smart TV',
        'Wastafel',
        'Balkon Kamar',
        'Jendela / Ventilasi Bagus',
        'Kipas Angin',
        'Kulkas Mini',
    ];

    $existingFasilitas = $isEdit
        ? array_values(array_filter(array_map('trim', explode(',', (string) $kamar->fasilitas))))
        : [];
    $selectedFasilitas = old('fasilitas', $existingFasilitas);
    if (!is_array($selectedFasilitas)) {
        $selectedFasilitas = array_filter(array_map('trim', explode(',', (string) $selectedFasilitas)));
    }
    $customFasilitas = array_diff($existingFasilitas, $defaultFasilitas);
    $extraFasilitasValue = old('fasilitas_tambahan', implode(', ', $customFasilitas));

    $rawMonthlyVal = (float) old('price_bulan', $monthly->price ?? 0);
    $discVal = (float) old('cumulative_discount', $kamar->cumulative_discount ?? ($monthly->discount ?? 0));
    $netPreview = $rawMonthlyVal > 0
        ? ($discVal > 0 ? ($discVal <= 100 ? $rawMonthlyVal * (1 - $discVal / 100) : max(0, $rawMonthlyVal - $discVal)) : $rawMonthlyVal)
        : 0;
@endphp

<div class="row g-4">
  <!-- Kolom Kiri: Identitas, Spesifikasi, Fasilitas & Media -->
  <div class="col-12 col-lg-8">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-door-open me-2 text-gold"></i>Identitas & Spesifikasi Kamar</h5>
      </div>
      <div class="card-body p-4">

        @if(!$isEdit)
          <div class="p-3 rounded-3 bg-light border mb-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div>
                <label class="form-check-label fs-8 fw-bold text-dark d-block" for="toggleGenerateMultipleRooms">
                  <i class="bi bi-collection me-1 text-gold"></i>Buat Banyak Unit Kamar Sekaligus
                </label>
                <span class="text-muted fs-9">Aktifkan jika ingin membuat beberapa unit berurutan (misal Kamar 101–105) dengan spesifikasi & tarif sama.</span>
              </div>
              <div class="form-check form-switch m-0 fs-5">
                <input class="form-check-input cursor-pointer" type="checkbox" id="toggleGenerateMultipleRooms" name="is_multiple_rooms" value="1" {{ old('is_multiple_rooms') ? 'checked' : '' }}>
              </div>
            </div>

            <div id="multipleRoomsOptions" class="{{ old('is_multiple_rooms') ? '' : 'd-none' }} pt-3 mt-3 border-top">
              <div class="row g-2">
                <div class="col-md-4">
                  <label class="form-label fs-8 mb-1">Awalan Nama</label>
                  <input type="text" name="room_prefix" id="room_prefix" class="form-control form-control-sm" value="{{ old('room_prefix', 'Kamar ') }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label fs-8 mb-1">Nomor Mulai</label>
                  <input type="number" name="room_start_number" id="room_start_number" class="form-control form-control-sm" value="{{ old('room_start_number', 101) }}" min="1">
                </div>
                <div class="col-md-4">
                  <label class="form-label fs-8 mb-1">Jumlah Unit</label>
                  <input type="number" name="room_quantity" id="room_quantity" class="form-control form-control-sm" value="{{ old('room_quantity', 3) }}" min="1" max="50">
                </div>
              </div>
              <div class="mt-2 p-2 rounded bg-white border fs-8">
                <strong>Pratinjau:</strong>
                <span id="previewRoomGeneration" class="badge badge-subtle-gold ms-1">Kamar 101, Kamar 102, Kamar 103</span>
              </div>
            </div>
          </div>
        @endif

        <div class="row g-3">
          <div class="col-md-6" id="wrapSingleRoomName">
            <label for="inputSingleRoom" class="form-label">Nama / Nomor Kamar <span class="text-danger">*</span></label>
            <input
              type="text"
              name="room"
              id="inputSingleRoom"
              class="form-control"
              value="{{ old('room', $kamar->room ?? '') }}"
              placeholder="Contoh: Kamar 101 / Melati 01"
              {{ !$isEdit && old('is_multiple_rooms') ? '' : 'required' }}
            >
          </div>

          <div class="col-md-6">
            <label for="tipe_kamar" class="form-label">Tipe Kamar</label>
            <input
              type="text"
              name="tipe_kamar"
              id="tipe_kamar"
              list="tipeKamarOptions"
              class="form-control"
              value="{{ old('tipe_kamar', $kamar->tipe_kamar ?? '') }}"
              placeholder="Contoh: Deluxe / Standard / VIP"
            >
            <datalist id="tipeKamarOptions">
              <option value="Standard">
              <option value="Superior">
              <option value="Deluxe">
              <option value="Executive">
              <option value="VIP">
            </datalist>
          </div>

          <div class="col-md-6">
            <label for="luas_kamar" class="form-label">Luas Kamar</label>
            <input
              type="text"
              name="luas_kamar"
              id="luas_kamar"
              class="form-control"
              value="{{ old('luas_kamar', $kamar->luas_kamar ?? '') }}"
              placeholder="Contoh: 3 x 4 m (12 m²)"
            >
          </div>

          <div class="col-md-6">
            <label for="lantai" class="form-label">Lokasi Lantai</label>
            <input
              type="text"
              name="lantai"
              id="lantai"
              class="form-control"
              value="{{ old('lantai', $kamar->lantai ?? '') }}"
              placeholder="Contoh: Lantai 1"
            >
          </div>

          <div class="col-12">
            <label for="description" class="form-label">Deskripsi Kamar</label>
            <textarea
              name="description"
              id="description"
              class="form-control summernote-editor"
              rows="4"
              placeholder="Jelaskan kenyamanan, pencahayaan, dan detail kamar..."
            >{{ old('description', $kamar->description ?? '') }}</textarea>
          </div>

          <div class="col-12">
            <label for="catatan" class="form-label">Informasi Tambahan / Ketentuan Kamar <span class="text-muted fw-normal">(Opsional)</span></label>
            <textarea
              name="catatan"
              id="catatan"
              class="form-control"
              rows="2"
              placeholder="Contoh: Listrik token mandiri 1300W, maksimal 1 penghuni per kamar..."
            >{{ old('catatan', $kamar->catatan ?? '') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Fasilitas Kamar -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-check2-square me-2 text-gold"></i>Fasilitas Kamar</h5>
      </div>
      <div class="card-body p-4">
        <div class="row g-2 mb-3">
          @foreach($defaultFasilitas as $idx => $fasName)
            <div class="col-6 col-md-4">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="fasilitas[]"
                  value="{{ $fasName }}"
                  id="fas_{{ $idx }}"
                  {{ in_array($fasName, $selectedFasilitas) ? 'checked' : '' }}
                >
                <label class="form-check-label fs-8 text-dark cursor-pointer" for="fas_{{ $idx }}">
                  {{ $fasName }}
                </label>
              </div>
            </div>
          @endforeach
        </div>
        <div>
          <label for="fasilitas_tambahan" class="form-label fs-8">Fasilitas Tambahan Lainnya <span class="text-muted fw-normal">(pisahkan dengan koma)</span></label>
          <input
            type="text"
            name="fasilitas_tambahan"
            id="fasilitas_tambahan"
            class="form-control"
            value="{{ $extraFasilitasValue }}"
            placeholder="Contoh: Smart Door Lock, Meja Rias, Lampu Tidur"
          >
        </div>
      </div>
    </div>

    <!-- Foto & Video Kamar -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-images me-2 text-gold"></i>Foto & Video Kamar</h5>
      </div>
      <div class="card-body p-4">
        @if($isEdit && $kamar->productKamarImageKosan && $kamar->productKamarImageKosan->count() > 0)
          <div class="mb-4">
            <label class="form-label d-block">Foto Kamar Saat Ini ({{ $kamar->productKamarImageKosan->count() }} foto)</label>
            <div class="row g-2.5">
              @foreach($kamar->productKamarImageKosan as $photo)
                <div class="col-6 col-sm-4 col-md-3">
                  <div class="border rounded-3 p-1.5 bg-light text-center">
                    <img src="{{ asset('storage/' . $photo->image) }}" alt="Foto {{ $kamar->room }}" class="w-100 rounded-2 object-fit-cover mb-1.5" style="height: 95px;">
                    <div class="form-check form-check-inline m-0">
                      <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $photo->id }}" id="del_img_{{ $photo->id }}">
                      <label class="form-check-label fs-9 text-danger fw-semibold cursor-pointer" for="del_img_{{ $photo->id }}">Hapus foto</label>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="mb-4">
          <label class="form-label">{{ $isEdit ? 'Tambah Foto Baru' : 'Upload Foto Kamar' }} <span class="text-muted fw-normal">(JPG, PNG, WEBP &bull; Multi-foto)</span></label>
          <div class="multi-image-dropzone">
            <input type="file" name="images[]" id="kamarImagesInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept="image/*" multiple>
            <div id="kamarImagesPlaceholder">
              <i class="bi bi-cloud-arrow-up fs-2 text-gold d-block mb-1"></i>
              <div class="fs-8 fw-semibold text-dark">Klik atau seret beberapa foto kamar ke area ini</div>
              <small class="text-muted fs-9">Disarankan foto ruang kamar, kamar mandi, dan detail interior</small>
            </div>
            <div id="kamarImagesPreviewWrap" class="d-none">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-subtle-success" id="kamarImagesCountLabel">0 foto dipilih</span>
                <button type="button" class="btn btn-xs btn-light border" id="btnResetKamarImages">Reset Pilihan</button>
              </div>
              <div class="multi-image-grid" id="kamarImagesGrid"></div>
            </div>
          </div>
        </div>

        @include('backend.dashboard.kosan.partials-kosan._video_input', [
            'uid'          => $isEdit ? 'kamar_edit_' . $kamar->id : 'kamar_create',
            'model'        => $isEdit ? $kamar : null,
            'contextLabel' => 'Unit Kamar',
        ])
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Status Ketersediaan, Tarif Sewa & Simpan -->
  <div class="col-12 col-lg-4">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-toggle-on me-2 text-gold"></i>Status Ketersediaan</h5>
      </div>
      <div class="card-body p-4">
        @php
          $currentAvailability = old('status_ketersediaan', $kamar->status_ketersediaan ?? 'tersedia');
        @endphp
        <label for="status_ketersediaan" class="form-label">Status Publikasi Kamar</label>
        <select name="status_ketersediaan" id="status_ketersediaan" class="form-select">
          <option value="tersedia" {{ $currentAvailability === 'tersedia' ? 'selected' : '' }}>
            Tersedia (Tampil & Bisa Dipesan)
          </option>
          <option value="tidak_tersedia" {{ $currentAvailability === 'tidak_tersedia' ? 'selected' : '' }}>
            Tidak Tersedia (Nonaktif / Maintenance)
          </option>
        </select>
        <div class="form-text fs-8 mt-2">
          Jika diatur ke <strong>Tidak Tersedia</strong>, kamar otomatis disembunyikan dari daftar kamar tersedia di website dan tidak dapat dibooking.
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2 text-gold"></i>Harga Sewa Kamar</h5>
      </div>
      <div class="card-body p-4">
        <div class="mb-3">
          <label for="price_bulan" class="form-label">Harga Sewa Bulanan (Rp) <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-light fs-8">Rp</span>
            <input
              type="number"
              name="price_bulan"
              id="price_bulan"
              class="form-control fw-bold"
              value="{{ old('price_bulan', $monthly ? (int) $monthly->price : '') }}"
              placeholder="1500000"
              min="0"
              step="1"
              required
            >
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="price_tahun" class="form-label mb-0">Harga Sewa Tahunan (Rp)</label>
            <button type="button" class="btn btn-link btn-xs p-0 text-decoration-none text-gold" id="btnAutoYearly">
              12 × Bulanan
            </button>
          </div>
          <div class="input-group">
            <span class="input-group-text bg-light fs-8">Rp</span>
            <input
              type="number"
              name="price_tahun"
              id="price_tahun"
              class="form-control"
              value="{{ old('price_tahun', $yearly ? (int) $yearly->price : '') }}"
              placeholder="Otomatis 12 × bulanan"
              min="0"
              step="1"
            >
          </div>
        </div>

        <div class="mb-3">
          <label for="cumulative_discount" class="form-label">Diskon Promo (%)</label>
          @if(Auth::user()->isSuperAdmin())
            <div class="input-group">
              <input
                type="number"
                step="0.01"
                min="0"
                max="100"
                name="cumulative_discount"
                id="cumulative_discount"
                class="form-control"
                value="{{ old('cumulative_discount', $kamar->cumulative_discount ?? 0) }}"
                placeholder="0"
              >
              <span class="input-group-text bg-light fs-8">%</span>
            </div>
          @else
            <div class="input-group">
              <input type="number" class="form-control bg-light" value="{{ $kamar->cumulative_discount ?? 0 }}" readonly disabled>
              <span class="input-group-text bg-light fs-8">%</span>
            </div>
          @endif
        </div>

        <div class="p-3 rounded-3 bg-light border">
          <div class="fs-9 text-uppercase fw-bold text-muted">Harga Tampil di Website</div>
          <div class="fs-5 fw-extrabold text-primary tabular-nums mt-0.5" id="netPricePreviewText">
            Rp {{ number_format($netPreview, 0, ',', '.') }} <span class="fs-8 fw-normal text-muted">/ bulan</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body p-4 d-grid gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check2-circle"></i>
          <span>{{ $isEdit ? 'Simpan Perubahan Kamar' : 'Simpan Kamar Baru' }}</span>
        </button>
        <a href="{{ $isEdit ? route('admin.kamar.show', $kamar->id) : route('admin.kamar.index') }}" class="btn btn-light border">
          <span>Batal</span>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleMulti = document.getElementById('toggleGenerateMultipleRooms');
    const multiWrap   = document.getElementById('multipleRoomsOptions');
    const singleWrap  = document.getElementById('wrapSingleRoomName');
    const singleInput = document.getElementById('inputSingleRoom');
    const previewGen  = document.getElementById('previewRoomGeneration');

    function syncMultiRooms() {
      if (!toggleMulti || !multiWrap) return;
      const on = toggleMulti.checked;
      multiWrap.classList.toggle('d-none', !on);
      if (singleWrap) singleWrap.classList.toggle('d-none', on);
      if (singleInput) {
        on ? singleInput.removeAttribute('required') : singleInput.setAttribute('required', 'required');
      }
      if (on && previewGen) {
        const prefix = document.getElementById('room_prefix')?.value || 'Kamar ';
        const start = parseInt(document.getElementById('room_start_number')?.value) || 101;
        const qty = Math.min(50, Math.max(1, parseInt(document.getElementById('room_quantity')?.value) || 1));
        const items = [];
        for (let i = 0; i < Math.min(4, qty); i++) items.push(prefix + (start + i));
        if (qty > 4) items.push(`... s/d ${prefix}${start + qty - 1} (${qty} unit)`);
        previewGen.textContent = items.join(', ');
      }
    }

    if (toggleMulti) {
      toggleMulti.addEventListener('change', syncMultiRooms);
      ['room_prefix', 'room_start_number', 'room_quantity'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', syncMultiRooms);
      });
    }

    const priceBulan = document.getElementById('price_bulan');
    const priceTahun = document.getElementById('price_tahun');
    const discInput  = document.getElementById('cumulative_discount');
    const netText    = document.getElementById('netPricePreviewText');
    const btnYearly  = document.getElementById('btnAutoYearly');

    function updateNetPrice() {
      if (!priceBulan || !netText) return;
      const raw = parseFloat(priceBulan.value) || 0;
      const disc = parseFloat(discInput?.value) || 0;
      const net = raw > 0 ? (disc > 0 ? (disc <= 100 ? raw * (1 - disc / 100) : Math.max(0, raw - disc)) : raw) : 0;
      netText.innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(net)) + ' <span class="fs-8 fw-normal text-muted">/ bulan</span>';
    }

    if (priceBulan) priceBulan.addEventListener('input', updateNetPrice);
    if (discInput) discInput.addEventListener('input', updateNetPrice);
    if (btnYearly && priceBulan && priceTahun) {
      btnYearly.addEventListener('click', function () {
        const val = parseFloat(priceBulan.value) || 0;
        if (val > 0) priceTahun.value = Math.round(val * 12);
      });
    }

    const imgInput = document.getElementById('kamarImagesInput');
    const imgPlaceholder = document.getElementById('kamarImagesPlaceholder');
    const imgPreviewWrap = document.getElementById('kamarImagesPreviewWrap');
    const imgGrid = document.getElementById('kamarImagesGrid');
    const imgCount = document.getElementById('kamarImagesCountLabel');
    const btnResetImg = document.getElementById('btnResetKamarImages');

    if (imgInput) {
      imgInput.addEventListener('change', function () {
        const files = Array.from(this.files || []);
        if (!files.length) return;
        if (imgGrid) imgGrid.innerHTML = '';
        if (imgCount) imgCount.textContent = `${files.length} foto dipilih`;
        files.forEach((f, idx) => {
          if (!f.type.startsWith('image/')) return;
          const reader = new FileReader();
          reader.onload = ev => {
            const card = document.createElement('div');
            card.className = 'multi-image-card';
            card.innerHTML = `<span class="badge-idx">#${idx + 1}</span><img src="${ev.target.result}" alt="Preview">`;
            imgGrid?.appendChild(card);
          };
          reader.readAsDataURL(f);
        });
        imgPlaceholder?.classList.add('d-none');
        imgPreviewWrap?.classList.remove('d-none');
      });
    }

    if (btnResetImg) {
      btnResetImg.addEventListener('click', function () {
        if (imgInput) imgInput.value = '';
        if (imgGrid) imgGrid.innerHTML = '';
        imgPreviewWrap?.classList.add('d-none');
        imgPlaceholder?.classList.remove('d-none');
      });
    }
  });
</script>
