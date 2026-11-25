<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceKamar;
use App\Models\ProductKamarImageKosan;
use App\Models\ProductKamarKosan;
use App\Models\ProductKosan;
use App\Models\Tamu;
use App\Service\ProcessBookingDate;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KamarKosanController extends Controller
{
    protected $processBookingDates;

    public function __construct(ProcessBookingDate $processBookingDates)
    {
        $this->processBookingDates = $processBookingDates;
    }

    private function flushCaches(ProductKosan $kosan): void
    {
        $kosan->syncAvailableCount();
        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');
        if (!empty($kosan->slug)) {
            Cache::forget("kosan_detail_{$kosan->slug}");
        }
    }

    private function resolveFasilitas(Request $request): string
    {
        $selected = $request->input('fasilitas', []);
        if (!is_array($selected)) {
            $selected = array_filter(array_map('trim', explode(',', (string) $selected)));
        }

        $extra = array_filter(array_map('trim', explode(',', (string) $request->input('fasilitas_tambahan', ''))));
        $merged = array_unique(array_filter(array_merge($selected, $extra)));

        return implode(', ', $merged);
    }

    /**
     * Daftar seluruh kamar Jumala Living dengan pencarian & filter status.
     */
    public function index(Request $request)
    {
        $kosan = ProductKosan::jumala();

        $allRooms = ProductKamarKosan::with(['tamu', 'priceKamar'])
            ->where('product_kosan_id', $kosan->id)
            ->get();

        $totalKamarCount        = $allRooms->count();
        $occupiedKamarCount     = $allRooms->filter(fn($r) => $r->room_status === 'terisi')->count();
        $availableKamarCount    = $allRooms->filter(fn($r) => $r->room_status === 'kosong')->count();
        $pendingKamarCount      = $allRooms->filter(fn($r) => $r->room_status === 'pending')->count();
        $unavailableKamarCount  = $allRooms->filter(fn($r) => $r->room_status === 'tidak_tersedia')->count();
        $occupancyRate          = $totalKamarCount > 0 ? round(($occupiedKamarCount / $totalKamarCount) * 100, 1) : 0;

        $kpiStats = [
            'total_kamar'           => $totalKamarCount,
            'tersedia_kamar'        => $availableKamarCount,
            'terisi_kamar'          => $occupiedKamarCount,
            'pending_kamar'         => $pendingKamarCount,
            'tidak_tersedia_kamar'  => $unavailableKamarCount,
            'occupancy_rate'        => $occupancyRate,
        ];

        $query = ProductKamarKosan::with([
            'productKamarImageKosan',
            'priceKamar',
            'tamu' => fn($q) => $q->orderByDesc('end_date'),
        ])->where('product_kosan_id', $kosan->id);

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(room) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(tipe_kamar, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(luas_kamar, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(lantai, "")) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(COALESCE(fasilitas, "")) LIKE ?', ["%{$search}%"]);
            });
        }

        $statusFilter = $request->get('status', 'semua');
        $today = now()->toDateString();
        $cutoff = now()->subHours(2);

        if ($statusFilter === 'terisi') {
            $query->whereHas('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            });
        } elseif ($statusFilter === 'tidak_tersedia') {
            $query->where('status_ketersediaan', 'tidak_tersedia')
                  ->whereDoesntHave('tamu', function ($q) use ($today) {
                      $q->where('status', 'approved')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                  });
        } elseif ($statusFilter === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('status_ketersediaan')->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
            })->whereDoesntHave('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })->whereHas('tamu', function ($q) use ($cutoff) {
                $q->where('status', 'pending')->where('created_at', '>=', $cutoff);
            });
        } elseif (in_array($statusFilter, ['tersedia', 'kosong'])) {
            $statusFilter = 'tersedia';
            $query->where(function ($q) {
                $q->whereNull('status_ketersediaan')->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
            })->whereDoesntHave('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })->whereDoesntHave('tamu', function ($q) use ($cutoff) {
                $q->where('status', 'pending')->where('created_at', '>=', $cutoff);
            });
        } else {
            $statusFilter = 'semua';
        }

        $kamar_kosan = $query->orderBy('room', 'asc')->paginate(12)->withQueryString();

        return view('backend.dashboard.kamar.index', compact('kamar_kosan', 'kosan', 'kpiStats', 'statusFilter'));
    }

    public function searchAjax(Request $request)
    {
        $kosan = ProductKosan::jumala();
        $search = strtolower(trim((string) $request->get('search', '')));

        $kamar_kosan = ProductKamarKosan::with(['productKamarImageKosan', 'priceKamar', 'tamu'])
            ->where('product_kosan_id', $kosan->id)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereRaw('LOWER(room) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(COALESCE(tipe_kamar, "")) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(COALESCE(fasilitas, "")) LIKE ?', ["%{$search}%"]);
                });
            })
            ->orderBy('room', 'asc')
            ->paginate(12);

        return response()->json($kamar_kosan);
    }

    /**
     * Halaman Tambah Kamar Baru.
     */
    public function create()
    {
        $kosan = ProductKosan::jumala();
        return view('backend.dashboard.kamar.create', compact('kosan'));
    }

    /**
     * Simpan kamar baru (satuan maupun generate banyak unit sekaligus).
     */
    public function store(Request $request)
    {
        $kosan = ProductKosan::jumala();

        $isMultiple = $request->boolean('is_multiple_rooms') || ($request->filled('room_quantity') && (int) $request->input('room_quantity') > 1);

        $request->validate([
            'room'                => $isMultiple ? 'nullable|string|max:255' : 'required|string|max:255',
            'tipe_kamar'          => 'nullable|string|max:100',
            'luas_kamar'          => 'nullable|string|max:60',
            'lantai'              => 'nullable|string|max:50',
            'status_ketersediaan' => 'nullable|in:tersedia,tidak_tersedia',
            'price_bulan'         => 'required|numeric|min:0',
            'price_tahun'         => 'nullable|numeric|min:0',
            'cumulative_discount' => 'nullable|numeric|min:0|max:100',
            'description'         => 'nullable|string',
            'catatan'             => 'nullable|string|max:2000',
            'fasilitas_tambahan'  => 'nullable|string|max:1000',
            'video_mode'          => 'nullable|in:link,upload',
            'video_url'           => 'nullable|url|max:1000',
            'video_file'          => 'nullable|file|mimes:mp4,webm,mov,ogg,ogv|max:51200',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'images'              => 'nullable|array',
            'images.*'            => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'room.required'        => 'Nama atau nomor kamar wajib diisi.',
            'price_bulan.required' => 'Tarif sewa bulanan wajib diisi.',
            'price_bulan.numeric'  => 'Tarif sewa bulanan harus berupa angka.',
            'video_url.url'        => 'Link video kamar harus berupa URL yang valid (https://...).',
            'video_file.mimes'     => 'Format video harus MP4, WebM, MOV, atau OGG.',
            'video_file.max'       => 'Ukuran file video maksimal 50 MB.',
        ]);

        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $fasilitas    = $this->resolveFasilitas($request);
        $discountVal  = $isSuperAdmin ? (float) ($request->input('cumulative_discount') ?? 0) : 0;
        $priceBulan   = (float) $request->input('price_bulan');
        $priceTahun   = $request->filled('price_tahun') && (float) $request->input('price_tahun') > 0
            ? (float) $request->input('price_tahun')
            : ($priceBulan * 12);
        $statusKetersediaan = $request->input('status_ketersediaan', 'tersedia');

        $quantity = $isMultiple ? max(1, min(50, (int) $request->input('room_quantity', 1))) : 1;
        $rawPrefix = trim((string) $request->input('room_prefix', 'Kamar'));
        if ($rawPrefix !== '' && !str_ends_with($rawPrefix, '-') && !str_ends_with($rawPrefix, '_')) {
            $rawPrefix .= ' ';
        }
        $startNum = (int) $request->input('room_start_number', 101);

        $createdIds = [];

        DB::transaction(function () use (
            $request, $kosan, $fasilitas, $discountVal, $priceBulan, $priceTahun,
            $statusKetersediaan, $isMultiple, $quantity, $rawPrefix, $startNum, &$createdIds
        ) {
            $uploadedImagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    if ($file && $file->isValid()) {
                        $uploadedImagePaths[] = $file->store('kosan/kamar', 'public');
                    }
                }
            } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
                $uploadedImagePaths[] = $request->file('image')->store('kosan/kamar', 'public');
            }

            $videoPath = null;
            $videoUrl  = null;
            if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
                $videoPath = $request->file('video_file')->store('kosan/kamar/video', 'public');
            } elseif ($request->filled('video_url')) {
                $videoUrl = trim((string) $request->input('video_url'));
            }

            for ($i = 0; $i < $quantity; $i++) {
                $roomName = $isMultiple
                    ? ($rawPrefix . ($startNum + $i))
                    : trim((string) $request->input('room'));

                $kamar = ProductKamarKosan::create([
                    'product_kosan_id'    => $kosan->id,
                    'room'                => $roomName,
                    'tipe_kamar'          => $request->input('tipe_kamar') ? trim((string) $request->input('tipe_kamar')) : null,
                    'luas_kamar'          => $request->input('luas_kamar') ? trim((string) $request->input('luas_kamar')) : null,
                    'lantai'              => $request->input('lantai') ? trim((string) $request->input('lantai')) : null,
                    'description'         => HtmlSanitizer::clean($request->input('description')),
                    'catatan'             => $request->input('catatan') ? strip_tags((string) $request->input('catatan')) : null,
                    'status_ketersediaan' => $statusKetersediaan,
                    'fasilitas'           => $fasilitas,
                    'cumulative_discount' => $discountVal,
                    'video_path'          => $videoPath,
                    'video_url'           => $videoUrl,
                    'views'               => 0,
                ]);

                $createdIds[] = $kamar->id;

                PriceKamar::create([
                    'product_kamar_kosan_id' => $kamar->id,
                    'kategori'               => 'bulan',
                    'price'                  => $priceBulan,
                    'discount'               => $discountVal,
                ]);

                if ($priceTahun > 0) {
                    PriceKamar::create([
                        'product_kamar_kosan_id' => $kamar->id,
                        'kategori'               => 'tahun',
                        'price'                  => $priceTahun,
                        'discount'               => $discountVal,
                    ]);
                }

                foreach ($uploadedImagePaths as $path) {
                    ProductKamarImageKosan::create([
                        'product_kamar_kosan_id' => $kamar->id,
                        'image'                  => $path,
                    ]);
                }
            }
        });

        $this->flushCaches($kosan);

        if ($quantity > 1) {
            $endNum = $startNum + $quantity - 1;
            return redirect()
                ->route('admin.kamar.index')
                ->with('success', "Berhasil membuat {$quantity} unit kamar ({$rawPrefix}{$startNum} s/d {$rawPrefix}{$endNum}) beserta tarif sewa dan galeri foto.");
        }

        if (!empty($createdIds[0])) {
            return redirect()
                ->route('admin.kamar.show', $createdIds[0])
                ->with('success', 'Unit kamar baru berhasil ditambahkan dan disinkronkan ke website.');
        }

        return redirect()->route('admin.kamar.index')->with('success', 'Data kamar berhasil ditambahkan.');
    }

    /**
     * Halaman Detail Kamar (terstruktur lengkap dengan foto, spesifikasi, harga, & riwayat penghuni).
     */
    public function show($product_kamar_kosan)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::with([
            'productKosan',
            'productKamarImageKosan',
            'priceKamar',
            'tamu' => fn($q) => $q->with('processedBy')->orderByDesc('created_at'),
        ])->findOrFail($product_kamar_kosan);

        return view('backend.dashboard.kamar.show', compact('kamar', 'kosan'));
    }

    /**
     * Halaman Edit Kamar.
     */
    public function edit($product_kamar_kosan)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::with([
            'productKamarImageKosan',
            'priceKamar',
            'tamu',
        ])->findOrFail($product_kamar_kosan);

        return view('backend.dashboard.kamar.edit', compact('kamar', 'kosan'));
    }

    /**
     * Perbarui seluruh data kamar.
     */
    public function update($product_kamar_kosan, Request $request)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::findOrFail($product_kamar_kosan);

        $request->validate([
            'room'                => 'required|string|max:255',
            'tipe_kamar'          => 'nullable|string|max:100',
            'luas_kamar'          => 'nullable|string|max:60',
            'lantai'              => 'nullable|string|max:50',
            'status_ketersediaan' => 'nullable|in:tersedia,tidak_tersedia',
            'price_bulan'         => 'required|numeric|min:0',
            'price_tahun'         => 'nullable|numeric|min:0',
            'cumulative_discount' => 'nullable|numeric|min:0|max:100',
            'description'         => 'nullable|string',
            'catatan'             => 'nullable|string|max:2000',
            'fasilitas_tambahan'  => 'nullable|string|max:1000',
            'views'               => 'nullable|integer|min:0',
            'video_mode'          => 'nullable|in:link,upload',
            'video_url'           => 'nullable|url|max:1000',
            'video_file'          => 'nullable|file|mimes:mp4,webm,mov,ogg,ogv|max:51200',
            'remove_video'        => 'nullable|boolean',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'images'              => 'nullable|array',
            'images.*'            => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images'       => 'nullable|array',
            'delete_images.*'     => 'integer',
        ], [
            'room.required'        => 'Nama atau nomor kamar wajib diisi.',
            'price_bulan.required' => 'Tarif sewa bulanan wajib ditentukan.',
            'price_bulan.numeric'  => 'Tarif sewa bulanan harus berupa angka.',
            'video_url.url'        => 'Link video kamar harus berupa URL yang valid (https://...).',
            'video_file.mimes'     => 'Format file video kamar harus MP4, WebM, MOV, atau OGG.',
            'video_file.max'       => 'Ukuran file video kamar maksimal 50 MB.',
        ]);

        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $fasilitas    = $this->resolveFasilitas($request);
        $discountVal  = $isSuperAdmin ? (float) ($request->input('cumulative_discount') ?? 0) : (float) ($kamar->cumulative_discount ?? 0);

        $oldVideoPath  = $kamar->video_path;
        $newVideoPath  = $oldVideoPath;
        $newVideoUrl   = $kamar->video_url;
        $deleteOldFile = false;

        if ($request->boolean('remove_video')) {
            $newVideoPath  = null;
            $newVideoUrl   = null;
            $deleteOldFile = !empty($oldVideoPath);
        } elseif ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $newVideoPath  = $request->file('video_file')->store('kosan/kamar/video', 'public');
            $newVideoUrl   = null;
            $deleteOldFile = !empty($oldVideoPath);
        } else {
            $videoMode   = $request->input('video_mode');
            $rawVideoUrl = trim((string) $request->input('video_url', ''));
            if ($videoMode === 'link') {
                if ($rawVideoUrl !== '') {
                    $newVideoPath  = null;
                    $newVideoUrl   = $rawVideoUrl;
                    $deleteOldFile = !empty($oldVideoPath);
                } elseif (!$oldVideoPath) {
                    $newVideoUrl = null;
                }
            } elseif ($videoMode !== 'upload' && $rawVideoUrl !== '') {
                $newVideoPath  = null;
                $newVideoUrl   = $rawVideoUrl;
                $deleteOldFile = !empty($oldVideoPath);
            }
        }

        DB::transaction(function () use ($kamar, $request, $fasilitas, $discountVal, $newVideoPath, $newVideoUrl) {
            $kamar->update([
                'room'                => trim((string) $request->input('room')),
                'tipe_kamar'          => $request->input('tipe_kamar') ? trim((string) $request->input('tipe_kamar')) : null,
                'luas_kamar'          => $request->input('luas_kamar') ? trim((string) $request->input('luas_kamar')) : null,
                'lantai'              => $request->input('lantai') ? trim((string) $request->input('lantai')) : null,
                'status_ketersediaan' => $request->input('status_ketersediaan', $kamar->status_ketersediaan ?? 'tersedia'),
                'cumulative_discount' => $discountVal,
                'description'         => HtmlSanitizer::clean($request->input('description')),
                'catatan'             => $request->input('catatan') ? strip_tags((string) $request->input('catatan')) : null,
                'fasilitas'           => $fasilitas,
                'video_path'          => $newVideoPath,
                'video_url'           => $newVideoUrl,
                'views'               => $request->input('views', $kamar->views ?? 0),
            ]);

            $pBulanUpdate = (float) $request->input('price_bulan');
            PriceKamar::updateOrCreate(
                [
                    'product_kamar_kosan_id' => $kamar->id,
                    'kategori'               => 'bulan',
                ],
                [
                    'price'    => $pBulanUpdate,
                    'discount' => $discountVal,
                ]
            );

            $pTahunUpdate = $request->filled('price_tahun') && (float) $request->input('price_tahun') > 0
                ? (float) $request->input('price_tahun')
                : ($pBulanUpdate * 12);

            if ($pTahunUpdate > 0) {
                PriceKamar::updateOrCreate(
                    [
                        'product_kamar_kosan_id' => $kamar->id,
                        'kategori'               => 'tahun',
                    ],
                    [
                        'price'    => $pTahunUpdate,
                        'discount' => $discountVal,
                    ]
                );
            }

            // Hapus foto yang dipilih saat edit
            if ($request->filled('delete_images')) {
                $toDelete = ProductKamarImageKosan::where('product_kamar_kosan_id', $kamar->id)
                    ->whereIn('id', $request->input('delete_images', []))
                    ->get();
                foreach ($toDelete as $img) {
                    if ($img->image && Storage::disk('public')->exists($img->image)) {
                        Storage::disk('public')->delete($img->image);
                    }
                    $img->delete();
                }
            }

            // Tambah foto baru
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('kosan/kamar', 'public');
                        ProductKamarImageKosan::create([
                            'product_kamar_kosan_id' => $kamar->id,
                            'image'                  => $path,
                        ]);
                    }
                }
            } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('kosan/kamar', 'public');
                ProductKamarImageKosan::create([
                    'product_kamar_kosan_id' => $kamar->id,
                    'image'                  => $path,
                ]);
            }
        });

        if ($deleteOldFile && $oldVideoPath) {
            $stillReferenced = ProductKamarKosan::withTrashed()
                ->where('id', '!=', $kamar->id)
                ->where('video_path', $oldVideoPath)
                ->exists();
            if (!$stillReferenced && Storage::disk('public')->exists($oldVideoPath)) {
                Storage::disk('public')->delete($oldVideoPath);
            }
        }

        $this->flushCaches($kosan);

        return redirect()
            ->route('admin.kamar.show', $kamar->id)
            ->with('success', "Data kamar {$kamar->room} berhasil diperbarui dan disinkronkan.");
    }

    /**
     * Quick toggle status ketersediaan ('tersedia' <-> 'tidak_tersedia').
     */
    public function toggleStatus($product_kamar_kosan)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::findOrFail($product_kamar_kosan);

        $newStatus = $kamar->isBookable() ? 'tidak_tersedia' : 'tersedia';
        $kamar->update(['status_ketersediaan' => $newStatus]);

        $this->flushCaches($kosan);

        $label = $newStatus === 'tersedia' ? 'Tersedia (Dipublikasikan)' : 'Tidak Tersedia (Dinonaktifkan)';
        return back()->with('success', "Status kamar {$kamar->room} diubah menjadi {$label}.");
    }

    /**
     * Hapus kamar (soft delete agar riwayat booking/tamu tetap aman, serta blokir jika dihuni penyewa aktif).
     */
    public function destroy($product_kamar_kosan)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::findOrFail($product_kamar_kosan);

        if ($kamar->active_tenant) {
            return back()->with('failed', 'Tidak dapat menghapus kamar karena saat ini masih dihuni oleh penyewa aktif.');
        }

        $roomName = $kamar->room;
        $kamar->delete();

        $this->flushCaches($kosan);

        return redirect()
            ->route('admin.kamar.index')
            ->with('success', "Kamar {$roomName} berhasil dihapus.");
    }

    // FOTO KAMAR (Sub-resource)
    public function storeImage($product_kamar_kosan, Request $request)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::findOrFail($product_kamar_kosan);

        $request->validate([
            'images'   => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadedCount = 0;
        DB::transaction(function () use ($request, $kamar, &$uploadedCount) {
            $files = [];
            if ($request->hasFile('images')) {
                $f = $request->file('images');
                $files = array_merge($files, is_array($f) ? $f : [$f]);
            }
            if ($request->hasFile('image')) {
                $f = $request->file('image');
                $files = array_merge($files, is_array($f) ? $f : [$f]);
            }

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('kosan/kamar', 'public');
                    ProductKamarImageKosan::create([
                        'product_kamar_kosan_id' => $kamar->id,
                        'image'                  => $path,
                    ]);
                    $uploadedCount++;
                }
            }
        });

        $this->flushCaches($kosan);

        if ($uploadedCount > 0) {
            return back()->with('success', "Berhasil mengunggah {$uploadedCount} foto kamar.");
        }

        return back()->with('warning', 'Tidak ada foto yang diunggah.');
    }

    public function destroyImage($product_kamar_kosan, $product_kamar_image_kosan)
    {
        $kosan = ProductKosan::jumala();
        $image = ProductKamarImageKosan::where('id', $product_kamar_image_kosan)
            ->where('product_kamar_kosan_id', $product_kamar_kosan)
            ->firstOrFail();

        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }
        $image->delete();

        $this->flushCaches($kosan);

        return back()->with('success', 'Foto kamar berhasil dihapus.');
    }

    // TARIF KAMAR (Sub-resource)
    public function storePrice($product_kamar_kosan, Request $request)
    {
        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::findOrFail($product_kamar_kosan);

        $request->validate([
            'priceKamar'            => 'required|array|min:1',
            'priceKamar.*.kategori' => 'required|string|in:bulan,tahun,Bulanan,Tahunan',
            'priceKamar.*.price'    => 'required|numeric|min:0',
            'priceKamar.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $isSuperAdmin = auth()->user()->isSuperAdmin();
        foreach ($request->input('priceKamar', []) as $item) {
            $kat = strtolower(trim($item['kategori']));
            if ($kat === 'bulanan') $kat = 'bulan';
            if ($kat === 'tahunan') $kat = 'tahun';

            $disc = $isSuperAdmin ? ($item['discount'] ?? 0) : 0;

            PriceKamar::updateOrCreate(
                ['product_kamar_kosan_id' => $kamar->id, 'kategori' => $kat],
                ['price' => $item['price'], 'discount' => $disc]
            );

            if ($kat === 'bulan' && $isSuperAdmin) {
                $kamar->update(['cumulative_discount' => $disc]);
            }
        }

        $this->flushCaches($kosan);
        return back()->with('success', 'Tarif kamar berhasil diperbarui.');
    }

    public function updatePrice($product_kamar_kosan, $price_kamar, Request $request)
    {
        $kosan = ProductKosan::jumala();
        $request->validate([
            'kategori' => 'required|string|in:bulan,tahun,Bulanan,Tahunan',
            'price'    => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $price = PriceKamar::where('id', $price_kamar)
            ->where('product_kamar_kosan_id', $product_kamar_kosan)
            ->firstOrFail();

        $kat = strtolower(trim($request->kategori));
        if ($kat === 'bulanan') $kat = 'bulan';
        if ($kat === 'tahunan') $kat = 'tahun';

        $disc = $isSuperAdmin ? ($request->discount ?? 0) : ($price->discount ?? 0);

        $price->update([
            'kategori' => $kat,
            'price'    => $request->price,
            'discount' => $disc,
        ]);

        if ($kat === 'bulan' && $isSuperAdmin) {
            ProductKamarKosan::where('id', $product_kamar_kosan)->update(['cumulative_discount' => $disc]);
        }

        $this->flushCaches($kosan);
        return back()->with('success', 'Tarif kamar berhasil diperbarui.');
    }

    public function destroyPrice($product_kamar_kosan, $price_kamar)
    {
        $kosan = ProductKosan::jumala();
        $price = PriceKamar::where('id', $price_kamar)
            ->where('product_kamar_kosan_id', $product_kamar_kosan)
            ->firstOrFail();

        $price->delete();
        $this->flushCaches($kosan);

        return back()->with('success', 'Tarif kamar berhasil dihapus.');
    }

    // PENYEWA / TAMU (Sub-resource)
    public function renewTamuSingle($product_kamar_kosan, $tamu, Request $request)
    {
        $kosan = ProductKosan::jumala();
        $dataTamu = Tamu::where('id', $tamu)
            ->where('product_kamar_kosan_id', $product_kamar_kosan)
            ->firstOrFail();

        $request->validate([
            'name'              => 'nullable|string|max:255',
            'telp'              => 'nullable|string|max:25',
            'email'             => 'nullable|email|max:255',
            'start_date'        => 'nullable|date',
            'payment_method'    => 'nullable|string',
            'proof_of_transfer' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tahun'             => 'nullable|integer|min:0',
            'bulan'             => 'nullable|integer|min:0',
        ]);

        if (!$request->filled('start_date')) {
            $prevEnd = $dataTamu->end_date ? \Carbon\Carbon::parse($dataTamu->end_date) : now();
            $effectiveStart = $prevEnd->isPast() ? now()->toDateString() : $prevEnd->toDateString();
            $request->merge(['start_date' => $effectiveStart]);
        }

        $bookingDate = $this->processBookingDates->calculateBookingRange($product_kamar_kosan, $request, $dataTamu->id);
        if (isset($bookingDate['status']) && $bookingDate['status'] === false) {
            return back()->withInput()->with('failed', $bookingDate['message']);
        }

        $kamar = ProductKamarKosan::with('priceKamar')->find($product_kamar_kosan);
        $serverCalculatedPrice = $kamar ? $this->processBookingDates->calculateBookingPrice($kamar, $request) : $dataTamu->total_price;

        $data = [
            'name'           => strip_tags($request->input('name') ?: $dataTamu->name),
            'telp'           => strip_tags($request->input('telp') ?: $dataTamu->telp),
            'email'          => filter_var($request->input('email') ?: $dataTamu->email, FILTER_SANITIZE_EMAIL),
            'start_time'     => $request->start_time ?: ($dataTamu->start_time ?: '12:00'),
            'start_date'     => $bookingDate['start'],
            'end_date'       => $bookingDate['end'],
            'payment_method' => $request->payment_method ?: ($dataTamu->payment_method ?: 'cash'),
            'total_price'    => $serverCalculatedPrice,
        ];

        if ($request->hasFile('proof_of_transfer') && $request->file('proof_of_transfer')->isValid()) {
            if ($dataTamu->proof_of_transfer && Storage::disk('local')->exists($dataTamu->proof_of_transfer)) {
                Storage::disk('local')->delete($dataTamu->proof_of_transfer);
            }
            $data['proof_of_transfer'] = $request->file('proof_of_transfer')->store('tamu/proof-of-transfer', 'local');
        }

        $dataTamu->update($data);
        $this->flushCaches($kosan);

        $formattedEndDate = \Carbon\Carbon::parse($bookingDate['end'])->isoFormat('D MMMM Y');
        return back()->with('success', "Masa sewa atas nama {$dataTamu->name} berhasil diperpanjang hingga {$formattedEndDate}.");
    }

    public function destroyTamuSingle($product_kamar_kosan, $tamu)
    {
        $kosan = ProductKosan::jumala();
        $dataTamu = Tamu::where('id', $tamu)
            ->where('product_kamar_kosan_id', $product_kamar_kosan)
            ->firstOrFail();

        if ($dataTamu->proof_of_transfer) {
            if (Storage::disk('public')->exists($dataTamu->proof_of_transfer)) {
                Storage::disk('public')->delete($dataTamu->proof_of_transfer);
            }
            if (Storage::disk('local')->exists($dataTamu->proof_of_transfer)) {
                Storage::disk('local')->delete($dataTamu->proof_of_transfer);
            }
        }
        $dataTamu->delete();
        $this->flushCaches($kosan);

        return back()->with('success', 'Data penyewa berhasil dihapus.');
    }
}
