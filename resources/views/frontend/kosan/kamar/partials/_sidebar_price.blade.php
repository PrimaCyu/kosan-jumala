@php
    $pricesByKategori = [];
    if ($kamar && $kamar->priceKamar) {
        foreach ($kamar->priceKamar as $p) {
            $kategori = strtolower((string) ($p->kategori ?? ''));
            if ($kategori !== '') {
                $pricesByKategori[$kategori] = $p;
            }
        }
    }

    $kategoriOrdered = [
        'bulan' => ['title' => 'Tarif Bulanan', 'unit' => 'bulan', 'icon' => 'fa-calendar'],
        'tahun' => ['title' => 'Tarif Tahunan', 'unit' => 'tahun', 'icon' => 'fa-calendar-days'],
    ];

    $monthlyPriceObj = $pricesByKategori['bulan'] ?? null;
    $yearlyPriceObj = $pricesByKategori['tahun'] ?? null;

    $yearlySavings = 0;
    if ($monthlyPriceObj && $yearlyPriceObj) {
        $totalMonthlyForYear = $monthlyPriceObj->price * 12;
        $yearlySavings = max(0, $totalMonthlyForYear - $yearlyPriceObj->price);
    }

    $roomStatus = $kamar->room_status ?? 'kosong';
    $isOccupied = ($roomStatus === 'terisi');
    $activeTenant = $kamar->active_tenant;
    $endDateFormatted = null;
    if ($activeTenant && !empty($activeTenant->end_date)) {
        try {
            $endDateFormatted = \Carbon\Carbon::parse($activeTenant->end_date)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            $endDateFormatted = date('d-m-Y', strtotime($activeTenant->end_date));
        }
    }

    // Resolusi nomor WhatsApp
    $waNumber = '6282146138847';
    foreach (collect($globalSosmed ?? []) as $sm) {
        $socialTitle = strtolower((string) ($sm->title ?? $sm->name ?? $sm->platform ?? ''));
        $socialUrl = (string) ($sm->url ?? $sm->link ?? '');

        if (str_contains($socialTitle, 'whatsapp') || str_contains(strtolower($socialUrl), 'wa.me')) {
            preg_match('/[0-9]{9,15}/', $socialUrl, $waMatch);
            if (!empty($waMatch[0])) {
                $waNumber = $waMatch[0];
                break;
            }
        }
    }

    $kamarWaMsg = "Halo Admin Jumala Living, saya tertarik dengan unit " . ($kamar->room ?? 'Kamar') . " di " . ($kamar->productKosan->title ?? 'Jumala Living') . " yang saat ini sedang terisi. Apakah saya bisa masuk daftar antrean (waiting list) untuk periode sewa berikutnya?";
    $kamarWaUrl = "https://wa.me/" . $waNumber . "?text=" . urlencode($kamarWaMsg);
@endphp

<div class="space-y-4">
    <!-- Status Ketersediaan Real-Time -->
    @if($isOccupied)
        <div class="p-3.5 rounded-2xl bg-[#D99AA8]/10 border border-[#D99AA8]/90 shadow-sm">
            <div class="flex items-center gap-2 text-[#3B2314] font-black text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-[#D99AA8] animate-pulse"></span>
                <span>Status Unit: Terisi (Penuh)</span>
            </div>
            <p class="text-[11px] text-[#3B2314]/80 mt-1.5 leading-relaxed">
                @if($endDateFormatted)
                    Unit ini sedang dihuni penyewa aktif hingga <strong>{{ $endDateFormatted }}</strong>. Anda dapat mendaftar antrean reservasi atau memilih kamar lain yang tersedia di kos ini.
                @else
                    Unit ini sedang terisi aktif. Hubungi pengelola untuk informasi jadwal ketersediaan berikutnya.
                @endif
            </p>
        </div>
    @elseif($roomStatus === 'pending')
        <div class="p-3 rounded-2xl bg-[#C9A45C]/10 border border-[#C9A45C] text-[#3B2314] shadow-sm flex items-center justify-between text-xs font-bold">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-clock text-[#C9A45C]"></i>
                <span>Menunggu Verifikasi</span>
            </span>
            <span class="text-[10px] bg-[#FFFCF8] px-2 py-0.5 rounded-md border border-[#C9A45C]">Dalam Proses</span>
        </div>
    @else
        <div class="p-3 rounded-2xl bg-[#C9A45C]/10 border border-[#C9A45C] text-[#3B2314] shadow-sm flex items-center justify-between text-xs font-bold">
            <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#C9A45C] animate-pulse"></span>
                <span>Unit Siap Huni</span>
            </span>
            <span class="text-[10px] bg-[#FFFCF8] px-2 py-0.5 rounded-md border border-[#C9A45C] font-black text-[#3B2314]">Kamar Kosong</span>
        </div>
    @endif

    <!-- Kotak Harga -->
    <div class="space-y-3">
        @foreach($kategoriOrdered as $katKey => $meta)
            @if(isset($pricesByKategori[$katKey]))
                @php 
                    $priceObj = $pricesByKategori[$katKey];
                    $basePrice = (float) $priceObj->price;
                    $disc = (float) ($priceObj->discount > 0 ? $priceObj->discount : ($katKey === 'bulan' ? ($kamar->cumulative_discount ?? 0) : 0));
                    $hasDiscount = ($disc > 0 && $basePrice > 0);
                    $originalPrice = $hasDiscount ? $basePrice : null;
                    $netPrice = $hasDiscount
                        ? ($disc <= 100 ? max(0, round($basePrice * (1 - ($disc / 100)))) : max(0, $basePrice - $disc))
                        : $basePrice;
                @endphp
                <div class="bg-[#FFF8F1] hover:bg-[#C9A45C]/15 p-4 rounded-2xl border border-[#E9DDD2] hover:border-[#C9A45C] transition-all duration-200 relative overflow-hidden group shadow-sm">
                    <div class="flex justify-between items-start gap-3">
                        <div class="space-y-1">
                            <span class="text-xs text-[#8E7B6D] font-bold flex items-center gap-2">
                                <i class="fa-regular {{ $meta['icon'] }} text-[#C9A45C]"></i>
                                {{ $meta['title'] }}
                            </span>

                            @if($hasDiscount && $originalPrice)
                                <div class="text-[11px] text-[#8E7B6D] line-through font-semibold">
                                    Rp {{ number_format($originalPrice, 0, ',', '.') }}
                                </div>
                            @endif

                            <div class="text-xl sm:text-2xl font-black text-[#3B2314]">
                                Rp {{ number_format($netPrice, 0, ',', '.') }} 
                                <span class="text-xs text-[#8E7B6D] font-bold">/{{ $meta['unit'] }}</span>
                            </div>

                            @if($katKey === 'tahun' && $yearlySavings > 0)
                                <div class="text-[10px] text-[#3B2314] font-bold bg-[#C9A45C]/10 px-2 py-0.5 rounded-md inline-block mt-1">
                                    💰 Hemat Rp {{ number_format($yearlySavings, 0, ',', '.') }}/thn
                                </div>
                            @endif
                        </div>

                        @if($hasDiscount)
                            <div class="text-[11px] bg-[#D99AA8] text-[#FFF8F1] font-black px-2.5 py-1 rounded-full shadow-md shrink-0">
                                Diskon {{ round($disc) }}%
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach

        @if(empty($pricesByKategori))
            <div class="bg-[#FFF8F1] p-5 rounded-2xl border border-[#E9DDD2] text-center text-[#8E7B6D] text-xs">
                <i class="fa-solid fa-circle-exclamation text-2xl text-[#C9A45C] mb-2 block"></i>
                Tarif resmi belum ditentukan untuk unit ini. Silakan tanyakan ke pengelola kos.
            </div>
        @endif
    </div>

    <!-- Tombol Aksi Cerdas (Smart CTA) -->
    <div class="pt-2 space-y-2">
        @if($isOccupied)
            <a 
                href="{{ $kamarWaUrl }}" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="w-full py-3.5 px-4 bg-[#C9A45C] hover:bg-[#3B2314] text-[#FFF8F1] font-black rounded-2xl transition-all shadow-md hover:shadow-md flex items-center justify-center gap-2 active:scale-95 text-xs sm:text-sm text-center"
            >
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span>Masuk Waiting List via WA</span>
            </a>

            @if($kamar->productKosan && $kamar->productKosan->productKamarKosan->count() > 0)
                <button 
                    type="button" 
                    onclick="scrollToSiblingRooms()" 
                    class="w-full py-3 px-4 bg-[#FFFCF8] hover:bg-[#C9A45C]/15 text-[#3B2314] border border-[#E9DDD2] font-black rounded-2xl transition-all shadow-sm flex items-center justify-center gap-2 text-xs"
                >
                    <i class="fa-solid fa-door-open text-[#C9A45C]"></i>
                    <span>Cek Kamar Lain di Kos Ini</span>
                </button>
            @endif
        @else
            <a 
                href="{{ route('form.booking.kamar', $kamar->id) }}" 
                class="shine w-full py-4 bg-[#D99AA8] hover:bg-[#D99AA8] text-[#FFF8F1] font-black rounded-2xl transition-all shadow-lg hover:shadow-[#D99AA8]/30 flex items-center justify-center gap-2 active:scale-95 text-xs sm:text-sm"
            >
                <i class="fa-solid fa-bolt text-[#C9A45C]"></i>
                <span>Pesan Unit Ini Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        @endif
    </div>

    <p class="text-[10px] text-[#8E7B6D] flex items-start gap-2 leading-relaxed pt-1">
        <i class="fa-solid fa-shield-halved text-[#C9A45C] text-xs mt-0.5 shrink-0"></i>
        <span>Harga resmi tertera transparan. Booking diverifikasi langsung oleh pengelola resmi Jumala Living.</span>
    </p>
</div>
