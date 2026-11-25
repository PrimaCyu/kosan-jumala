<!-- MODAL TAMBAH KAMAR (UNIFIED SINGLE CLEAN FORM) -->
<div class="modal fade" id="modalTambahKamar" tabindex="-1" aria-labelledby="modalTambahKamarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('admin.product.kosan.kamar.insert', $product_kosan) }}" method="POST" enctype="multipart/form-data" id="formSingleKamar" class="modal-content border-0 shadow-lg">
            @csrf
            <!-- Modal Header -->
            <div class="modal-header border-bottom py-3">
                <div>
                    <h5 class="modal-title fw-bold fs-6" id="modalTambahKamarLabel">
                        <i class="bi bi-door-open text-primary me-2"></i>Tambah Kamar Baru: {{ $kosan->title }}
                    </h5>
                    <p class="text-muted fs-8 mb-0">Lengkapi nomor kamar, tarif sewa, foto, dan fasilitas kamar dalam satu formulir.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <!-- Bagian 1: Identitas & Pilihan Unit -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 fs-7 text-dark">
                            <i class="bi bi-door-open text-primary me-1.5"></i> 1. Identitas Unit Kamar
                        </h6>
                    </div>

                    <!-- Card Pilihan Tambah Satuan vs Sekaligus Banyak -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-check-label fs-8 fw-bold text-dark d-block" for="toggleGenerateMultipleRooms">
                                    <i class="bi bi-collection text-primary me-1"></i> Ingin Buat Banyak Kamar Sekaligus?
                                </label>
                                <span class="text-muted fs-8">Aktifkan jika ingin membuat beberapa unit kamar dengan tipe, tarif, dan foto yang sama.</span>
                            </div>
                            <div class="form-check form-switch m-0 fs-5">
                                <input class="form-check-input cursor-pointer" type="checkbox" id="toggleGenerateMultipleRooms" name="is_multiple_rooms" value="1">
                            </div>
                        </div>

                        <!-- Opsi Multi-Kamar (Ditampilkan saat switch aktif) -->
                        <div id="multipleRoomsOptions" class="d-none pt-3 border-top mt-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label fs-8 text-dark fw-semibold mb-1">Awalan Nama</label>
                                    <input type="text" name="room_prefix" id="room_prefix" class="form-control form-control-sm" value="Kamar " placeholder="Kamar ">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-8 text-dark fw-semibold mb-1">Nomor Mulai</label>
                                    <input type="number" name="room_start_number" id="room_start_number" class="form-control form-control-sm" value="101" min="1" step="1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-8 text-dark fw-semibold mb-1">Jumlah Kamar</label>
                                    <input type="number" name="room_quantity" id="room_quantity" class="form-control form-control-sm" value="5" min="1" max="50" step="1">
                                </div>
                            </div>
                            <div class="p-2.5 mt-2 rounded bg-white border fs-8 text-dark">
                                <i class="bi bi-magic text-primary me-1"></i><strong>Pratinjau Unit yang Akan Dibuat:</strong>
                                <span class="badge bg-primary-subtle text-primary ms-1" id="previewRoomGeneration">Kamar 101, Kamar 102, Kamar 103, Kamar 104, Kamar 105</span>
                                <div class="text-muted mt-1 fs-8">Unit-unit ini akan otomatis memiliki tarif, foto, dan fasilitas yang Anda isi di bawah.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Nama Kamar Satuan (Default) -->
                    <div class="row g-3" id="wrapSingleRoomName">
                        <div class="col-12">
                            <label class="form-label fs-8 fw-semibold mb-1">Nama / Tipe / Nomor Kamar <span class="text-danger">*</span></label>
                            <input type="text" name="room" id="inputSingleRoom" class="form-control" placeholder="Contoh: Kamar 101 (atau Tipe Standar 01)" required>
                            <div class="form-text fs-8 text-muted">Ketik nama atau nomor unit kamar agar mudah dikenali saat ada penyewa.</div>
                        </div>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <!-- Bagian 2: Tarif Sewa & Diskon Promo -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 fs-7 text-dark">
                            <i class="bi bi-cash-stack text-success me-1.5"></i> 2. Tarif Sewa & Diskon Promo
                        </h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fs-8 fw-semibold mb-1">Tarif Bulanan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fs-8">Rp</span>
                                <input type="number" name="price_bulan" id="add_price_bulan" class="form-control fw-semibold" placeholder="Contoh: 1500000" min="0" step="1" required>
                            </div>
                            <div class="form-text fs-8 text-muted">Bisa diisi nominal angka berapa pun (misal: 25000, 750000, 1500000).</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fs-8 fw-semibold mb-1">Diskon Promo (%)</label>
                            @if(Auth::user()->isSuperAdmin())
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="cumulative_discount" id="add_cumulative_discount" class="form-control" placeholder="0">
                                    <span class="input-group-text bg-light text-muted fs-8">%</span>
                                </div>
                                <div class="form-text fs-8 text-muted">Potongan harga promo jika ada (0 - 100%).</div>
                            @else
                                <div class="input-group">
                                    <input type="number" step="0.01" name="cumulative_discount" id="add_cumulative_discount" class="form-control bg-body-secondary" value="0" readonly disabled>
                                    <span class="input-group-text bg-light text-muted fs-8">%</span>
                                </div>
                                <div class="form-text fs-8 text-warning"><i class="bi bi-lock-fill me-1"></i>Khusus Super Admin</div>
                            @endif
                        </div>

                        <!-- Live Calculation Preview Box -->
                        <div class="col-12">
                            <div id="addPricePreviewBox" class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                <div class="fs-8 text-muted">
                                    <i class="bi bi-receipt text-primary me-1"></i> Estimasi Harga Net Per Bulan:
                                </div>
                                <div class="fs-7 fw-bold text-success" id="addPriceNetDisplay">
                                    Rp 0 <span class="fs-8 fw-normal text-muted">/ bulan</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fs-8 fw-semibold mb-0">
                                    Tarif Tahunan (Rp) <span class="text-muted fw-normal">(Otomatis 12 × Bulanan jika dikosongkan)</span>
                                </label>
                                <button type="button" class="btn btn-link btn-xs p-0 text-decoration-none fs-8 text-primary" id="btnSyncYearlyPrice">
                                    <i class="bi bi-magic me-1"></i>Hitung 12 × Bulanan
                                </button>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fs-8">Rp</span>
                                <input type="number" name="price_tahun" id="add_price_tahun" class="form-control" placeholder="Otomatis 12 × tarif bulanan" min="0" step="1">
                            </div>
                            <div class="form-text fs-8 text-muted">Jika dikosongkan, sistem otomatis menghitung 12 × tarif bulanan.</div>
                        </div>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <!-- Bagian 3: Foto Unit Kamar (Multi-Image Dropzone) -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 fs-7 text-dark">
                            <i class="bi bi-images text-primary me-1.5"></i> 3. Foto Unit Kamar
                        </h6>
                        <span class="text-muted fs-8">JPG, PNG, WEBP (Bisa pilih banyak foto)</span>
                    </div>
                    <div class="multi-image-dropzone" id="singleRoomDropzone">
                        <input type="file" name="images[]" id="singleRoomImageInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept="image/*" multiple>
                        <div id="singleRoomPlaceholder" class="text-center py-3">
                            <i class="bi bi-cloud-arrow-up text-secondary fs-3 mb-1 d-block opacity-75"></i>
                            <p class="fs-8 fw-semibold text-dark mb-0">Klik atau seret foto-foto unit kamar ke sini</p>
                            <p class="text-muted fs-8 mb-0">Pilih 1 atau lebih foto sudut kamar, kamar mandi, kasur, dsb.</p>
                        </div>
                        <div id="singleRoomPreviewWrap" class="d-none mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="multi-image-badge-count" id="singleRoomBadgeCount">
                                    <i class="bi bi-check2-circle text-success"></i> <span id="singleRoomFileName">0 Foto Dipilih</span>
                                </span>
                                <button type="button" class="btn btn-xs btn-outline-danger" id="btnCancelSingleImg">
                                    <i class="bi bi-x-circle me-1"></i> Reset Foto
                                </button>
                            </div>
                            <div class="multi-image-grid" id="singleRoomImagesGrid"></div>
                        </div>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <!-- Bagian 3.5: Video Unit Kamar (Upload / Link) -->
                <div class="mb-4">
                    @include('backend.dashboard.kosan.partials-kosan._video_input', [
                        'uid' => 'kamar_tambah',
                        'model' => null,
                        'contextLabel' => 'Unit Kamar',
                    ])
                </div>

                <hr class="my-3 opacity-25">

                <!-- Bagian 4: Fasilitas Dalam Kamar -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 fs-7 text-dark">
                        <i class="bi bi-check2-square text-info me-1.5"></i> 4. Fasilitas Dalam Kamar
                    </h6>
                    <div class="p-3 rounded-2 bg-light border">
                        @php
                            $listFasilitasKamar = [
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
                                'Kulkas Mini'
                            ];
                        @endphp
                        <div class="row g-2">
                            @foreach($listFasilitasKamar as $fasKey => $fasName)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="fasilitas[]" value="{{ $fasName }}" id="fas_single_{{ $fasKey }}">
                                        <label class="form-check-label fs-8 user-select-none text-secondary" for="fas_single_{{ $fasKey }}">
                                            {{ $fasName }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Bagian 5: Deskripsi Spesifikasi Kamar -->
                <div class="mb-2">
                    <label class="form-label fs-8 fw-semibold mb-1">
                        <i class="bi bi-card-text text-secondary me-1"></i> 5. Deskripsi / Catatan Tambahan <span class="text-muted fw-normal">(Opsional)</span>
                    </label>
                    <textarea name="description" id="add_single_room_description" class="form-control summernote-edit" rows="3" placeholder="Contoh: Ukuran 3x4 meter, listrik token mandiri 900 watt, view menghadap taman..."></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Simpan Unit Kamar
                </button>
            </div>
        </form>
    </div>
</div>
