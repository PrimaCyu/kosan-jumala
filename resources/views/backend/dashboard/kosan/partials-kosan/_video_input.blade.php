@php
    $uid = $uid ?? 'default';
    $contextLabel = $contextLabel ?? 'Properti';
    $existingModel = $model ?? null;
    $hasExistingVideo = $existingModel && $existingModel->has_video;
    $existingVideoEmbed = $hasExistingVideo ? $existingModel->video_embed : null;
    $existingVideoPath = $existingModel->video_path ?? null;
    $existingVideoUrl  = $existingModel->video_url ?? null;

    // Default mode: 'upload' jika sudah ada file unggahan, selain itu 'link'
    $defaultMode = old('video_mode', !empty($existingVideoPath) ? 'upload' : 'link');
@endphp

<div class="video-input-group p-3 rounded-3 bg-light border" data-uid="{{ $uid }}">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <h6 class="fw-bold mb-0 fs-7 text-dark">
            <i class="bi bi-camera-video-fill text-danger me-1.5"></i> Video {{ $contextLabel }}
            <span class="badge bg-secondary-subtle text-secondary fs-8 fw-normal ms-1">Opsional</span>
        </h6>
        <div class="btn-group btn-group-sm" role="group" aria-label="Mode Input Video">
            <input type="radio" class="btn-check video-mode-radio" name="video_mode" id="video_mode_link_{{ $uid }}" value="link" autocomplete="off" {{ $defaultMode === 'link' ? 'checked' : '' }}>
            <label class="btn btn-outline-primary fs-8 py-1 px-2.5" for="video_mode_link_{{ $uid }}">
                <i class="bi bi-link-45deg me-1"></i>Link Video
            </label>

            <input type="radio" class="btn-check video-mode-radio" name="video_mode" id="video_mode_upload_{{ $uid }}" value="upload" autocomplete="off" {{ $defaultMode === 'upload' ? 'checked' : '' }}>
            <label class="btn btn-outline-primary fs-8 py-1 px-2.5" for="video_mode_upload_{{ $uid }}">
                <i class="bi bi-upload me-1"></i>Upload File
            </label>
        </div>
    </div>

    @if($hasExistingVideo && $existingVideoEmbed)
        <div class="p-2.5 mb-3 rounded-2 bg-white border">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-subtle text-danger fs-8">
                        <i class="bi bi-play-circle-fill me-1"></i>{{ $existingVideoEmbed['label'] }}
                    </span>
                    <small class="text-muted fs-8">Video aktif saat ini</small>
                </div>
                <div class="form-check m-0">
                    <input class="form-check-input video-remove-checkbox" type="checkbox" name="remove_video" value="1" id="remove_video_{{ $uid }}">
                    <label class="form-check-label fs-8 text-danger fw-semibold cursor-pointer" for="remove_video_{{ $uid }}">
                        <i class="bi bi-trash me-1"></i>Hapus video ini
                    </label>
                </div>
            </div>

            @if($existingVideoEmbed['type'] === 'file')
                <video controls preload="metadata" class="w-100 rounded-2 bg-dark" style="max-height: 180px;">
                    <source src="{{ $existingVideoEmbed['src'] }}" type="{{ $existingVideoEmbed['mime'] ?? 'video/mp4' }}">
                    Browser tidak mendukung pemutaran video.
                </video>
            @else
                <div class="d-flex align-items-center justify-content-between gap-2 bg-light rounded-2 px-2.5 py-2 border">
                    <small class="text-truncate text-secondary fs-8" title="{{ $existingVideoEmbed['original_url'] }}">
                        <i class="bi bi-link-45deg text-primary me-1"></i>{{ $existingVideoEmbed['original_url'] }}
                    </small>
                    <a href="{{ $existingVideoEmbed['original_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-outline-secondary text-nowrap fs-8">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Panel Input Link Video -->
    <div class="video-panel-link {{ $defaultMode === 'link' ? '' : 'd-none' }}">
        <label for="video_url_{{ $uid }}" class="form-label fs-8 fw-semibold mb-1">
            Tautan / Link Video (YouTube, Shorts, TikTok, Google Drive, Vimeo, atau MP4)
        </label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white text-danger"><i class="bi bi-youtube"></i></span>
            <input type="url"
                   name="video_url"
                   id="video_url_{{ $uid }}"
                   class="form-control video-url-input"
                   value="{{ old('video_url', $existingVideoUrl) }}"
                   placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/...">
        </div>
        <div class="form-text fs-8 text-muted">
            Tempel tautan video YouTube / YouTube Shorts / Google Drive / TikTok / Vimeo / link langsung <code>.mp4</code>.
        </div>
        <div class="video-url-status fs-8 mt-1 d-none"></div>
    </div>

    <!-- Panel Input Upload File Video -->
    <div class="video-panel-upload {{ $defaultMode === 'upload' ? '' : 'd-none' }}">
        <label for="video_file_{{ $uid }}" class="form-label fs-8 fw-semibold mb-1">
            Unggah File Video Langsung <span class="text-muted fw-normal">(MP4, WebM, MOV, OGG • Maks. 50 MB)</span>
        </label>
        <input type="file"
               name="video_file"
               id="video_file_{{ $uid }}"
               class="form-control form-control-sm video-file-input"
               accept="video/mp4,video/webm,video/quicktime,video/ogg">
        <div class="form-text fs-8 text-muted">
            @if($existingVideoPath)
                Pilih file video baru jika ingin mengganti video yang sudah tersimpan. Kosongkan untuk mempertahankan video lama.
            @else
                Video akan disimpan dan diputar langsung melalui pemutar video modern di halaman publik.
            @endif
        </div>
        <div class="video-file-preview d-none mt-2">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="badge bg-success-subtle text-success fs-8 video-file-meta"></span>
                <button type="button" class="btn btn-xs btn-outline-danger btn-clear-video-file fs-8 py-0 px-2">
                    <i class="bi bi-x me-1"></i>Batal Pilih
                </button>
            </div>
            <video controls class="w-100 rounded-2 bg-dark video-local-preview-player" style="max-height: 200px;"></video>
        </div>
    </div>
</div>
