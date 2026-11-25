@php
    $prefix = $prefix ?? 'tambah';
    $isEdit = isset($item) && $item !== null;
    $provincesList = $provinces ?? \App\Service\WilayahService::getProvinces();

    $selectedProvId = old('provinsi_id', $isEdit ? $item->provinsi_id : '');
    $selectedKabId  = old('kabupaten_id', $isEdit ? $item->kabupaten_id : '');
    $selectedKecId  = old('kecamatan_id', $isEdit ? $item->kecamatan_id : '');
    $legacyWilayah  = $isEdit ? ($item->wilayah ?? '') : '';

    // Auto-match data wilayah lama (misal "Denpasar" atau "Badung") agar edit kos lama langsung terisi
    if ($isEdit && empty($selectedProvId) && !empty($legacyWilayah)) {
        $guessed = \App\Service\WilayahService::guessFromLegacyWilayah($legacyWilayah);
        $selectedProvId = $guessed['provinsi_id'] ?? '';
        $selectedKabId  = $guessed['kabupaten_id'] ?? '';
        $selectedKecId  = $guessed['kecamatan_id'] ?? '';
    }

    $initialRegencies = !empty($selectedProvId) ? \App\Service\WilayahService::getRegencies($selectedProvId) : [];
    $initialDistricts = !empty($selectedKabId) ? \App\Service\WilayahService::getDistricts($selectedKabId) : [];
    $requireFields    = !$isEdit;
@endphp

<div class="wilayah-dependent-group p-3 rounded-3 bg-body-tertiary border"
     data-regencies-url="{{ url('/api/wilayah/regencies') }}"
     data-districts-url="{{ url('/api/wilayah/districts') }}"
     data-selected-prov="{{ $selectedProvId }}"
     data-selected-kab="{{ $selectedKabId }}"
     data-selected-kec="{{ $selectedKecId }}">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <label class="form-label fs-8 fw-bold text-dark mb-0">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Lokasi Wilayah Indonesia
            @if($requireFields)
                <span class="text-danger">*</span>
            @endif
        </label>
        <span class="badge bg-primary-subtle text-primary fs-8">Provinsi &rarr; Kab/Kota &rarr; Kecamatan</span>
    </div>

    <div class="row g-2">
        <!-- 1. Provinsi -->
        <div class="col-md-4">
            <label for="wilayah_prov_{{ $prefix }}" class="form-label fs-8 fw-semibold text-muted mb-1">
                1. Provinsi @if($requireFields)<span class="text-danger">*</span>@endif
            </label>
            <select name="provinsi_id"
                    id="wilayah_prov_{{ $prefix }}"
                    class="form-select form-select-sm wilayah-select-provinsi"
                    {{ $requireFields ? 'required' : '' }}>
                <option value="">-- Pilih Provinsi --</option>
                @foreach($provincesList as $prov)
                    <option value="{{ $prov['id'] }}" {{ (string)$selectedProvId === (string)$prov['id'] ? 'selected' : '' }}>
                        {{ $prov['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 2. Kabupaten / Kota -->
        <div class="col-md-4">
            <label for="wilayah_kab_{{ $prefix }}" class="form-label fs-8 fw-semibold text-muted mb-1">
                2. Kabupaten / Kota @if($requireFields)<span class="text-danger">*</span>@endif
            </label>
            <select name="kabupaten_id"
                    id="wilayah_kab_{{ $prefix }}"
                    class="form-select form-select-sm wilayah-select-kabupaten"
                    {{ empty($selectedProvId) ? 'disabled' : '' }}
                    {{ $requireFields ? 'required' : '' }}>
                <option value="">{{ empty($selectedProvId) ? '-- Pilih Provinsi dulu --' : '-- Pilih Kabupaten / Kota --' }}</option>
                @foreach($initialRegencies as $reg)
                    <option value="{{ $reg['id'] }}" {{ (string)$selectedKabId === (string)$reg['id'] ? 'selected' : '' }}>
                        {{ $reg['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 3. Kecamatan -->
        <div class="col-md-4">
            <label for="wilayah_kec_{{ $prefix }}" class="form-label fs-8 fw-semibold text-muted mb-1">
                3. Kecamatan @if($requireFields)<span class="text-danger">*</span>@endif
            </label>
            <select name="kecamatan_id"
                    id="wilayah_kec_{{ $prefix }}"
                    class="form-select form-select-sm wilayah-select-kecamatan"
                    {{ empty($selectedKabId) ? 'disabled' : '' }}
                    {{ $requireFields ? 'required' : '' }}>
                <option value="">{{ empty($selectedKabId) ? '-- Pilih Kab/Kota dulu --' : '-- Pilih Kecamatan --' }}</option>
                @foreach($initialDistricts as $dist)
                    <option value="{{ $dist['id'] }}" {{ (string)$selectedKecId === (string)$dist['id'] ? 'selected' : '' }}>
                        {{ $dist['name'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Live preview alamat berjenjang -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
        <small class="text-muted fs-8 wilayah-preview-text">
            @if($isEdit && !empty($item->full_location))
                <i class="bi bi-check-circle-fill text-success me-1"></i>Tersimpan: <strong>{{ $item->full_location }}</strong>
            @else
                <i class="bi bi-info-circle text-primary me-1"></i>Pilih Provinsi terlebih dahulu, lalu Kabupaten/Kota, kemudian Kecamatan.
            @endif
        </small>
        @if($isEdit && !empty($legacyWilayah) && empty($item->kecamatan_id))
            <span class="badge bg-warning-subtle text-warning-emphasis fs-8">Silakan lengkapi Kecamatan</span>
        @endif
    </div>
</div>
