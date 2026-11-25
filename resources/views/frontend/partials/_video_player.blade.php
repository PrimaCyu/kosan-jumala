@php
    $embed = $videoEmbed ?? null;
    $eyebrow = $eyebrow ?? 'Video Tour';
    $heading = $heading ?? 'Lihat Suasana Secara Langsung';
    $subtitle = $subtitle ?? 'Tonton video review singkat untuk melihat kondisi ruangan dan lingkungan secara nyata.';
    $compact = $compact ?? false;
@endphp

@if($embed)
<div class="bg-white rounded-[2rem] border border-[#E9DDD2] shadow-sm overflow-hidden">
    <!-- Header Bar -->
    <div class="p-5 sm:p-6 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#F3E9DF]">
        <div>
            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-[.2em] text-[#E60049]">
                <i class="fa-solid fa-clapperboard"></i>
                {{ $eyebrow }}
            </span>
            <h3 class="{{ $compact ? 'text-xl sm:text-2xl' : 'text-2xl sm:text-3xl' }} font-black text-[#3B2314] mt-1 leading-tight">
                {{ $heading }}
            </h3>
            @if(!empty($subtitle))
                <p class="text-xs text-[#7B6759] mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#FFF8F1] border border-[#E9DDD2] text-[#5D483A] text-[11px] font-bold">
                <i class="fa-solid fa-play text-[#E60049] text-[10px]"></i>
                <span>{{ $embed['label'] }}</span>
            </span>
            @if(($embed['provider'] ?? '') !== 'upload' && !empty($embed['original_url']))
                <a href="{{ $embed['original_url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#3B2314] hover:bg-[#E60049] text-white text-[11px] font-bold transition-colors shadow-sm">
                    <span>Buka Link</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Video Player Stage -->
    <div class="p-4 sm:p-6 bg-[#FAF4ED]/60">
        @if($embed['type'] === 'file')
            <div class="rounded-2xl overflow-hidden bg-[#1D110A] shadow-lg border border-[#3B2314]/15">
                <video
                    controls
                    playsinline
                    preload="metadata"
                    class="w-full {{ $compact ? 'max-h-[420px]' : 'max-h-[520px]' }} mx-auto block"
                >
                    <source src="{{ $embed['src'] }}" type="{{ $embed['mime'] ?? 'video/mp4' }}">
                    Browser Anda tidak mendukung pemutar video HTML5.
                    <a href="{{ $embed['src'] }}" target="_blank" rel="noopener noreferrer" class="underline text-[#F3A833]">Unduh video di sini</a>.
                </video>
            </div>
        @elseif($embed['type'] === 'iframe')
            @if(!empty($embed['is_vertical']))
                <div class="rounded-2xl bg-[#1D110A] p-4 sm:p-6 flex justify-center shadow-lg">
                    <div class="w-full max-w-[360px] aspect-[9/16] rounded-xl overflow-hidden shadow-2xl bg-black">
                        <iframe
                            src="{{ $embed['src'] }}"
                            title="{{ $heading }}"
                            class="w-full h-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                </div>
            @else
                <div class="w-full aspect-video rounded-2xl overflow-hidden bg-[#1D110A] shadow-lg border border-[#3B2314]/15">
                    <iframe
                        src="{{ $embed['src'] }}"
                        title="{{ $heading }}"
                        class="w-full h-full border-0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
            @endif
        @else
            <!-- External Video Link Card Fallback -->
            <div class="rounded-2xl bg-gradient-to-br from-[#3B2314] to-[#5D3822] text-white p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-5 shadow-lg">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-14 h-14 rounded-2xl bg-[#E60049] text-white flex items-center justify-center text-xl shadow-lg shrink-0">
                        <i class="fa-solid fa-play"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest font-black text-[#F3A833]">Tautan Video Eksternal</p>
                        <h4 class="text-base sm:text-lg font-black mt-0.5">Tonton Video {{ $heading }}</h4>
                        <p class="text-xs text-white/75 mt-1 break-all line-clamp-1">{{ $embed['original_url'] }}</p>
                    </div>
                </div>
                <a href="{{ $embed['original_url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="px-5 py-3 bg-[#F3A833] hover:bg-white text-[#3B2314] text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-play text-[11px]"></i>
                    <span>Putar Video</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </div>
        @endif
    </div>
</div>
@endif
