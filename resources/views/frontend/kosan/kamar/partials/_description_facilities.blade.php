@php
    $allFasilitas = array_filter(array_map('trim', explode(',', (string) ($kamar?->fasilitas ?? ''))));
    $buildingFasilitas = array_filter(array_map('trim', explode(',', (string) ($kamar?->productKosan?->fasilitas ?? ''))));
    $wilayahNama = $kamar?->productKosan?->wilayah ?? 'Lowokwaru, Malang';

    $iconMap = [
        'AC' => 'fa-snowflake',
        'Kamar Mandi Dalam' => 'fa-bath',
        'Water Heater / Air Hangat' => 'fa-temperature-arrow-up',
        'Water Heater' => 'fa-temperature-arrow-up',
        'Kasur Springbed' => 'fa-bed',
        'Kasur' => 'fa-bed',
        'Lemari Pakaian' => 'fa-door-closed',
        'Lemari' => 'fa-door-closed',
        'Meja & Kursi Belajar' => 'fa-chair',
        'Meja' => 'fa-table',
        'TV / Smart TV' => 'fa-tv',
        'Wastafel' => 'fa-sink',
        'Balkon Kamar' => 'fa-person-through-window',
        'Jendela / Ventilasi Bagus' => 'fa-wind',
        'Kipas Angin' => 'fa-fan',
        'Kulkas Mini' => 'fa-box',

        'Wi-Fi / Internet' => 'fa-wifi',
        'Parkir Mobil' => 'fa-car',
        'Parkir Motor' => 'fa-motorcycle',
        'Dapur Bersama' => 'fa-kitchen-set',
        'CCTV 24 Jam' => 'fa-video',
        'Keamanan / Satpam' => 'fa-user-shield',
        'Ruang Tamu Bersama' => 'fa-couch',
        'Ruang Jemur' => 'fa-shirt',
        'Mesin Cuci Bersama' => 'fa-soap',
        'Kulkas Bersama' => 'fa-box',
        'Air Minum / Dispenser' => 'fa-glass-water',
        'Penjaga Kos' => 'fa-user-clock',
        'Listrik Gratis / Included' => 'fa-bolt',
        'Bebas Jam Malam' => 'fa-key',
        'Akses Kartu / Smart Lock' => 'fa-id-card',
        'Balkon / Rooftop' => 'fa-building',
        'Musholla' => 'fa-mosque',
        'Gazebo / Area Santai' => 'fa-umbrella-beach'
    ];
@endphp

<div class="space-y-7">
    <!-- 1. DESKRIPSI LENGKAP KAMAR -->
    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-wider text-[#3B2314] flex items-center gap-2">
                <i class="fa-solid fa-align-left text-[#C9A45C]"></i>
                Deskripsi Kamar
            </h3>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#FFFCF8] border border-[#E9DDD2] text-[#765C55] text-[11px] font-bold rounded-full shadow-sm">
                <i class="fa-solid fa-eye text-[#C9A45C]"></i> {{ number_format($kamar->views ?? 0, 0, ',', '.') }} Dilihat
            </span>
        </div>
        <div class="text-xs sm:text-sm text-[#765C55] leading-[1.8] bg-[#FFFCF8] p-4 sm:p-5 rounded-2xl border border-[#E9DDD2] shadow-sm space-y-2">
            @if(!empty($kamar->tipe_kamar) || !empty($kamar->luas_kamar) || !empty($kamar->lantai))
                <div class="flex flex-wrap gap-2 pb-2 mb-2 border-b border-[#E9DDD2]">
                    @if(!empty($kamar->tipe_kamar))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FFF8F1] border border-[#E9DDD2] text-[11px] font-bold text-[#3B2314]">
                            <i class="fa-solid fa-tag text-[#C9A45C]"></i> Tipe: {{ $kamar->tipe_kamar }}
                        </span>
                    @endif
                    @if(!empty($kamar->luas_kamar))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FFF8F1] border border-[#E9DDD2] text-[11px] font-bold text-[#3B2314]">
                            <i class="fa-solid fa-ruler-combined text-[#C9A45C]"></i> Luas: {{ $kamar->luas_kamar }}
                        </span>
                    @endif
                    @if(!empty($kamar->lantai))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FFF8F1] border border-[#E9DDD2] text-[11px] font-bold text-[#3B2314]">
                            <i class="fa-solid fa-layer-group text-[#C9A45C]"></i> {{ $kamar->lantai }}
                        </span>
                    @endif
                </div>
            @endif
            @if($kamar && !empty(trim(strip_tags($kamar->description ?? ''))))
                {!! \App\Support\HtmlSanitizer::clean($kamar->description) !!}
            @elseif($kamar && $kamar->productKosan && !empty(trim(strip_tags($kamar->productKosan->description ?? ''))))
                {!! \App\Support\HtmlSanitizer::clean($kamar->productKosan->description) !!}
            @else
                <p>Unit kamar kos nyaman, bersih, dan terang berlokasi di area strategis kawasan {{ $wilayahNama }}. Menawarkan privasi maksimal dengan sirkulasi udara yang baik dan siap huni kapan saja.</p>
            @endif
            @if(!empty($kamar->catatan))
                <div class="pt-2 mt-2 border-t border-[#E9DDD2] text-xs text-[#765C55]">
                    <strong>Catatan Tambahan:</strong> {{ $kamar->catatan }}
                </div>
            @endif
        </div>
    </section>

    <!-- 2. FASILITAS PRIBADI KAMAR -->
    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-wider text-[#3B2314] flex items-center gap-2">
                <i class="fa-solid fa-couch text-[#D99AA8]"></i>
                Fasilitas Kamar Pribadi
            </h3>
            <span class="text-[11px] font-bold text-[#8E7B6D]">{{ count($allFasilitas) }} Fasilitas</span>
        </div>

        @if(count($allFasilitas) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                @foreach($allFasilitas as $fasName)
                    @php $iconClass = $iconMap[$fasName] ?? 'fa-check'; @endphp
                    <div class="bg-[#FFFCF8] p-3.5 rounded-2xl border border-[#E9DDD2] shadow-sm flex items-center gap-3 hover:border-[#D99AA8] transition-colors group">
                        <div class="w-8 h-8 rounded-xl bg-[#D99AA8]/10 text-[#D99AA8] group-hover:bg-[#D99AA8] group-hover:text-[#FFF8F1] transition-colors flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $iconClass }} text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#3B2314] leading-snug">{{ $fasName }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-[#FFFCF8] p-4 rounded-2xl border border-[#E9DDD2] text-[#8E7B6D] text-xs">
                Fasilitas kamar standar lengkap. Silakan tanyakan ke pengelola untuk perlengkapan tambahan.
            </div>
        @endif
    </section>

    <!-- 3. FASILITAS BERSAMA GEDUNG KOS -->
    @if(count($buildingFasilitas) > 0)
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-black uppercase tracking-wider text-[#3B2314] flex items-center gap-2">
                    <i class="fa-solid fa-building text-[#C9A45C]"></i>
                    Fasilitas Bersama Gedung Kos
                </h3>
                <span class="text-[11px] font-bold text-[#8E7B6D]">{{ count($buildingFasilitas) }} Fasilitas</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                @foreach($buildingFasilitas as $bFas)
                    @php $bIcon = $iconMap[$bFas] ?? 'fa-circle-check'; @endphp
                    <div class="bg-[#FFFCF8] p-3.5 rounded-2xl border border-[#E9DDD2] shadow-sm flex items-center gap-3 hover:border-[#C9A45C] transition-colors group">
                        <div class="w-8 h-8 rounded-xl bg-[#C9A45C]/10 text-[#C9A45C] group-hover:bg-[#C9A45C] group-hover:text-[#FFF8F1] transition-colors flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $bIcon }} text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-[#3B2314] leading-snug">{{ $bFas }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
