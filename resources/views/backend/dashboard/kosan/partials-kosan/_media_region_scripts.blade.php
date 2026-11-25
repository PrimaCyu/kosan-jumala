<script>
(function() {
    if (window.__sclMediaRegionInit) return;
    window.__sclMediaRegionInit = true;

    // =========================================================================
    // 1. DEPENDENT DROPDOWN WILAYAH INDONESIA (PROVINSI -> KAB/KOTA -> KECAMATAN)
    // =========================================================================
    const wilayahCache = {
        regencies: {},
        districts: {}
    };

    async function fetchWilayahJson(url, cacheBucket, key) {
        if (cacheBucket[key]) return cacheBucket[key];
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Gagal memuat data wilayah');
        const data = await res.json();
        cacheBucket[key] = Array.isArray(data) ? data : [];
        return cacheBucket[key];
    }

    function updateWilayahPreview(group) {
        const provSel = group.querySelector('.wilayah-select-provinsi');
        const kabSel  = group.querySelector('.wilayah-select-kabupaten');
        const kecSel  = group.querySelector('.wilayah-select-kecamatan');
        const preview = group.querySelector('.wilayah-preview-text');
        if (!preview) return;

        const provText = provSel && provSel.value ? provSel.options[provSel.selectedIndex]?.text.trim() : '';
        const kabText  = kabSel && kabSel.value ? kabSel.options[kabSel.selectedIndex]?.text.trim() : '';
        const kecText  = kecSel && kecSel.value ? kecSel.options[kecSel.selectedIndex]?.text.trim() : '';

        const parts = [];
        if (kecText) parts.push('Kec. ' + kecText);
        if (kabText) parts.push(kabText);
        if (provText) parts.push(provText);

        if (parts.length > 0) {
            preview.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i>Lokasi terpilih: <strong>${parts.join(', ')}</strong>`;
        } else {
            preview.innerHTML = `<i class="bi bi-info-circle text-primary me-1"></i>Pilih Provinsi terlebih dahulu, lalu Kabupaten/Kota, kemudian Kecamatan.`;
        }
    }

    function populateSelect(selectEl, items, placeholder, selectedId = '') {
        selectEl.innerHTML = '';
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = placeholder;
        selectEl.appendChild(defaultOpt);

        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            if (selectedId && String(selectedId) === String(item.id)) {
                opt.selected = true;
            }
            selectEl.appendChild(opt);
        });
    }

    document.addEventListener('change', async function(e) {
        // Provinsi berubah -> muat Kabupaten/Kota & reset Kecamatan
        if (e.target && e.target.classList.contains('wilayah-select-provinsi')) {
            const group = e.target.closest('.wilayah-dependent-group');
            if (!group) return;

            const provId  = e.target.value;
            const kabSel  = group.querySelector('.wilayah-select-kabupaten');
            const kecSel  = group.querySelector('.wilayah-select-kecamatan');
            const regBase = group.getAttribute('data-regencies-url');

            if (kecSel) {
                populateSelect(kecSel, [], '-- Pilih Kab/Kota dulu --');
                kecSel.disabled = true;
            }

            if (!provId) {
                if (kabSel) {
                    populateSelect(kabSel, [], '-- Pilih Provinsi dulu --');
                    kabSel.disabled = true;
                }
                updateWilayahPreview(group);
                return;
            }

            if (kabSel) {
                populateSelect(kabSel, [], 'Memuat Kabupaten/Kota...');
                kabSel.disabled = true;
                try {
                    const regencies = await fetchWilayahJson(`${regBase}/${encodeURIComponent(provId)}`, wilayahCache.regencies, provId);
                    populateSelect(kabSel, regencies, '-- Pilih Kabupaten / Kota --');
                    kabSel.disabled = false;
                } catch (err) {
                    console.error(err);
                    populateSelect(kabSel, [], '-- Gagal memuat, coba pilih ulang --');
                }
            }
            updateWilayahPreview(group);
            return;
        }

        // Kabupaten/Kota berubah -> muat Kecamatan
        if (e.target && e.target.classList.contains('wilayah-select-kabupaten')) {
            const group = e.target.closest('.wilayah-dependent-group');
            if (!group) return;

            const kabId    = e.target.value;
            const kecSel   = group.querySelector('.wilayah-select-kecamatan');
            const distBase = group.getAttribute('data-districts-url');

            if (!kabId) {
                if (kecSel) {
                    populateSelect(kecSel, [], '-- Pilih Kab/Kota dulu --');
                    kecSel.disabled = true;
                }
                updateWilayahPreview(group);
                return;
            }

            if (kecSel) {
                populateSelect(kecSel, [], 'Memuat Kecamatan...');
                kecSel.disabled = true;
                try {
                    const districts = await fetchWilayahJson(`${distBase}/${encodeURIComponent(kabId)}`, wilayahCache.districts, kabId);
                    populateSelect(kecSel, districts, '-- Pilih Kecamatan --');
                    kecSel.disabled = false;
                } catch (err) {
                    console.error(err);
                    populateSelect(kecSel, [], '-- Gagal memuat, coba pilih ulang --');
                }
            }
            updateWilayahPreview(group);
            return;
        }

        // Kecamatan berubah -> perbarui ringkasan lokasi
        if (e.target && e.target.classList.contains('wilayah-select-kecamatan')) {
            const group = e.target.closest('.wilayah-dependent-group');
            if (group) updateWilayahPreview(group);
        }
    });

    // =========================================================================
    // 2. VIDEO INPUT CONTROLLER (SWITCH LINK / UPLOAD + LIVE PREVIEW)
    // =========================================================================
    function detectVideoProvider(url) {
        if (!url) return null;
        if (/(youtube\.com|youtu\.be)/i.test(url)) return /\/shorts\//i.test(url) ? 'YouTube Shorts' : 'YouTube';
        if (/vimeo\.com/i.test(url)) return 'Vimeo';
        if (/drive\.google\.com/i.test(url)) return 'Google Drive';
        if (/tiktok\.com/i.test(url)) return 'TikTok';
        if (/instagram\.com/i.test(url)) return 'Instagram';
        if (/\.(mp4|webm|ogg|ogv|mov)(\?.*)?$/i.test(url)) return 'Video Langsung (Direct)';
        if (/^https?:\/\//i.test(url)) return 'Tautan Video Eksternal';
        return null;
    }

    document.addEventListener('change', function(e) {
        // Toggle mode Link vs Upload
        if (e.target && e.target.classList.contains('video-mode-radio')) {
            const group = e.target.closest('.video-input-group');
            if (!group) return;
            const linkPanel   = group.querySelector('.video-panel-link');
            const uploadPanel = group.querySelector('.video-panel-upload');
            if (e.target.value === 'upload') {
                if (linkPanel) linkPanel.classList.add('d-none');
                if (uploadPanel) uploadPanel.classList.remove('d-none');
            } else {
                if (uploadPanel) uploadPanel.classList.add('d-none');
                if (linkPanel) linkPanel.classList.remove('d-none');
            }
            return;
        }

        // Live preview file video lokal
        if (e.target && e.target.classList.contains('video-file-input')) {
            const group = e.target.closest('.video-input-group');
            if (!group) return;
            const previewWrap = group.querySelector('.video-file-preview');
            const metaBadge   = group.querySelector('.video-file-meta');
            const player      = group.querySelector('.video-local-preview-player');
            const file        = e.target.files && e.target.files[0];

            if (!file) {
                if (previewWrap) previewWrap.classList.add('d-none');
                if (player) player.removeAttribute('src');
                return;
            }

            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            if (file.size > 50 * 1024 * 1024) {
                alert('Ukuran file video melebihi batas maksimal 50 MB. Gunakan file lebih kecil atau gunakan opsi Link Video.');
                e.target.value = '';
                if (previewWrap) previewWrap.classList.add('d-none');
                return;
            }

            if (metaBadge) {
                metaBadge.innerHTML = `<i class="bi bi-check2-circle me-1"></i>${file.name} (${sizeMb} MB)`;
            }
            if (player) {
                if (player.dataset.objectUrl) {
                    URL.revokeObjectURL(player.dataset.objectUrl);
                }
                const objUrl = URL.createObjectURL(file);
                player.dataset.objectUrl = objUrl;
                player.src = objUrl;
            }
            if (previewWrap) previewWrap.classList.remove('d-none');
        }
    });

    // Clear chosen local video file
    document.addEventListener('click', function(e) {
        const clearBtn = e.target.closest('.btn-clear-video-file');
        if (!clearBtn) return;
        const group = clearBtn.closest('.video-input-group');
        if (!group) return;
        const input = group.querySelector('.video-file-input');
        const previewWrap = group.querySelector('.video-file-preview');
        const player = group.querySelector('.video-local-preview-player');
        if (input) input.value = '';
        if (player) {
            if (player.dataset.objectUrl) {
                URL.revokeObjectURL(player.dataset.objectUrl);
                delete player.dataset.objectUrl;
            }
            player.removeAttribute('src');
            player.load();
        }
        if (previewWrap) previewWrap.classList.add('d-none');
    });

    // Live provider indicator for video URL input
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('video-url-input')) {
            const group = e.target.closest('.video-input-group');
            const statusEl = group ? group.querySelector('.video-url-status') : null;
            if (!statusEl) return;

            const val = e.target.value.trim();
            if (!val) {
                statusEl.classList.add('d-none');
                statusEl.innerHTML = '';
                return;
            }

            const provider = detectVideoProvider(val);
            statusEl.classList.remove('d-none');
            if (provider) {
                statusEl.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Terdeteksi: ${provider}</span>`;
            } else {
                statusEl.innerHTML = `<span class="text-warning-emphasis"><i class="bi bi-exclamation-triangle me-1"></i>Pastikan link diawali dengan https:// atau http://</span>`;
            }
        }
    });
})();
</script>
