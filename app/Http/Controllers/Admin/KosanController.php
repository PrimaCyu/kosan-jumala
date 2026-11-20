<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\ProductImageKosan;
use App\Models\ProductKosan;
use App\Service\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KosanController extends Controller
{
    private function checkKosanAccess($kosanId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $assigned = $user->kosans()->pluck('product_kosans.id')->toArray();
            if (!in_array($kosanId, $assigned)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk mengelola properti kos ini.');
            }
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();
        $assignedIds = $isSuper ? collect() : $user->kosans()->pluck('product_kosans.id');

        // Query kosan dengan relasi lengkap kamar & penyewa aktif
        $query = ProductKosan::with([
            'productImageKosan',
            'productKamarKosan' => function ($q) {
                $q->with([
                    'priceKamar',
                    'tamu' => function ($t) {
                        $t->where('status', 'approved')
                          ->whereDate('end_date', '>=', now()->toDateString());
                    }
                ]);
            }
        ])->when(!$isSuper, function ($q) use ($assignedIds) {
            $q->whereIn('id', $assignedIds);
        });

        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(wilayah) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(provinsi) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(kabupaten) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(kecamatan) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(fasilitas) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }
        $product_kosan = $query->orderByDesc('created_at')->paginate(10, ['*'], 'kosan_page');

        // Hitung Ringkasan Metrik Okupansi (KPI Cards)
        $allKosansQuery = ProductKosan::with(['productKamarKosan.tamu' => function ($t) {
            $t->where('status', 'approved')->whereDate('end_date', '>=', now()->toDateString());
        }])->when(!$isSuper, function ($q) use ($assignedIds) {
            $q->whereIn('id', $assignedIds);
        })->get();

        $kpiTotalKosan = $allKosansQuery->count();
        $kpiTotalCapacity = 0;
        $kpiTotalOccupied = 0;

        foreach ($allKosansQuery as $k) {
            $roomCount = $k->productKamarKosan->count();
            $kpiTotalCapacity += $roomCount;
            $kpiTotalOccupied += $k->productKamarKosan->filter(function ($room) {
                return $room->tamu->count() > 0;
            })->count();
        }

        $kpiTotalAvailable = max(0, $kpiTotalCapacity - $kpiTotalOccupied);
        $kpiOccupancyRate = $kpiTotalCapacity > 0 ? round(($kpiTotalOccupied / $kpiTotalCapacity) * 100, 1) : 0;

        $kpiStats = [
            'total_kosan'    => $kpiTotalKosan,
            'total_capacity' => $kpiTotalCapacity,
            'total_occupied' => $kpiTotalOccupied,
            'total_available'=> $kpiTotalAvailable,
            'occupancy_rate' => $kpiOccupancyRate,
        ];

        $provinces = WilayahService::getProvinces();

        return view('backend.dashboard.kosan.index', compact('product_kosan', 'kpiStats', 'provinces'));
    }

    public function searchKosan(Request $request)
    {
        $user = auth()->user();
        $isSuper = $user->isSuperAdmin();
        $assignedIds = $isSuper ? collect() : $user->kosans()->pluck('product_kosans.id');

        $search = strtolower($request->get('search'));
        $product_kosan = ProductKosan::with([
            'productImageKosan',
            'productKamarKosan' => function ($q) {
                $q->with([
                    'priceKamar',
                    'tamu' => function ($t) {
                        $t->where('status', 'approved')
                          ->whereDate('end_date', '>=', now()->toDateString());
                    }
                ]);
            }
        ])
            ->when(!$isSuper, function ($q) use ($assignedIds) {
                $q->whereIn('id', $assignedIds);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(wilayah) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(provinsi) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(kabupaten) LIKE ?', ["%{$search}%"])
                      ->orWhereRaw('LOWER(kecamatan) LIKE ?', ["%{$search}%"]);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($product_kosan);
    }

    private function generateUniqueSlug($title, $ignoreId = null)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (ProductKosan::where('slug', $slug)->when($ignoreId, function ($query, $id) {
            return $query->where('id', '!=', $id);
        })->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Resolusi data wilayah berjenjang (Provinsi -> Kabupaten/Kota -> Kecamatan) dengan kompatibilitas penuh wilayah lama.
     */
    private function resolveWilayahData(Request $request, ?ProductKosan $existing = null): array
    {
        $provId = trim((string) $request->input('provinsi_id', ''));
        $kabId  = trim((string) $request->input('kabupaten_id', ''));
        $kecId  = trim((string) $request->input('kecamatan_id', ''));

        $provObj = $provId !== '' ? WilayahService::findProvince($provId) : null;
        $kabObj  = $kabId !== '' ? WilayahService::findRegency($kabId) : null;
        $kecObj  = $kecId !== '' ? WilayahService::findDistrict($kecId) : null;

        $provName = $provObj['name'] ?? trim((string) $request->input('provinsi', ''));
        $kabName  = $kabObj['name'] ?? trim((string) $request->input('kabupaten', ''));
        $kecName  = $kecObj['name'] ?? trim((string) $request->input('kecamatan', ''));

        if ($provName !== '' || $kabName !== '' || $kecName !== '') {
            $composed = WilayahService::composeWilayahLabel($kecName, $kabName, $provName);
            return [
                'provinsi_id'  => $provId !== '' ? $provId : null,
                'provinsi'     => $provName !== '' ? $provName : null,
                'kabupaten_id' => $kabId !== '' ? $kabId : null,
                'kabupaten'    => $kabName !== '' ? $kabName : null,
                'kecamatan_id' => $kecId !== '' ? $kecId : null,
                'kecamatan'    => $kecName !== '' ? $kecName : null,
                'wilayah'      => $composed !== '' ? $composed : ($existing->wilayah ?? 'Indonesia'),
            ];
        }

        if ($request->filled('wilayah')) {
            return [
                'provinsi_id'  => $existing->provinsi_id ?? null,
                'provinsi'     => $existing->provinsi ?? null,
                'kabupaten_id' => $existing->kabupaten_id ?? null,
                'kabupaten'    => $existing->kabupaten ?? null,
                'kecamatan_id' => $existing->kecamatan_id ?? null,
                'kecamatan'    => $existing->kecamatan ?? null,
                'wilayah'      => trim((string) $request->input('wilayah')),
            ];
        }

        return [
            'provinsi_id'  => $existing->provinsi_id ?? null,
            'provinsi'     => $existing->provinsi ?? null,
            'kabupaten_id' => $existing->kabupaten_id ?? null,
            'kabupaten'    => $existing->kabupaten ?? null,
            'kecamatan_id' => $existing->kecamatan_id ?? null,
            'kecamatan'    => $existing->kecamatan ?? null,
            'wilayah'      => $existing && $existing->wilayah ? $existing->wilayah : 'Bali',
        ];
    }

    /**
     * Resolusi media video (Upload file MP4/WebM/MOV atau Link YouTube/Drive/TikTok/MP4) secara aman.
     */
    private function resolveVideoData(Request $request, ?ProductKosan $existing = null): array
    {
        $oldPath = $existing->video_path ?? null;
        $oldUrl  = $existing->video_url ?? null;

        if ($request->boolean('remove_video')) {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            return ['video_path' => null, 'video_url' => null];
        }

        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $storedPath = $request->file('video_file')->store('kosan/video', 'public');
            return ['video_path' => $storedPath, 'video_url' => null];
        }

        $mode = $request->input('video_mode');
        $rawUrl = trim((string) $request->input('video_url', ''));

        if ($mode === 'link') {
            if ($rawUrl !== '') {
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                return ['video_path' => null, 'video_url' => $rawUrl];
            }
            // Jika mode link dikosongkan dan sebelumnya memang link (bukan file upload), kosongkan link
            if ($existing && !$oldPath) {
                return ['video_path' => null, 'video_url' => null];
            }
            return ['video_path' => $oldPath, 'video_url' => $oldUrl];
        }

        if ($mode === 'upload') {
            return ['video_path' => $oldPath, 'video_url' => $oldUrl];
        }

        if ($rawUrl !== '') {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            return ['video_path' => null, 'video_url' => $rawUrl];
        }

        return ['video_path' => $oldPath, 'video_url' => $oldUrl];
    }

    public function insert(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang berhak menambahkan properti kos-kosan baru.');
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'wilayah'      => 'nullable|string|max:255',
            'provinsi_id'  => 'nullable|string|max:15',
            'provinsi'     => 'nullable|string|max:120',
            'kabupaten_id' => 'nullable|string|max:15',
            'kabupaten'    => 'nullable|string|max:120',
            'kecamatan_id' => 'nullable|string|max:20',
            'kecamatan'    => 'nullable|string|max:120',
            'description'  => 'nullable|string',
            'tersedia'     => 'nullable|numeric',
            'view'         => 'nullable|numeric',
            'gmaps'        => 'nullable|string',
            'video_mode'   => 'nullable|in:link,upload',
            'video_url'    => 'nullable|url|max:1000',
            'video_file'   => 'nullable|file|mimes:mp4,webm,mov,ogg,ogv|max:51200',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'video_url.url'    => 'Link video harus berupa URL yang valid (diawali https:// atau http://).',
            'video_file.mimes' => 'Format file video harus MP4, WebM, MOV, atau OGG.',
            'video_file.max'   => 'Ukuran file video maksimal 50 MB.',
        ]);

        $fasilitas = $request->fasilitas;
        if (is_array($fasilitas)) {
            $fasilitas = implode(', ', $fasilitas);
        }

        $description = $request->description ?: ($request->deskripsi ?: '');
        $wilayahData = $this->resolveWilayahData($request);
        $videoData   = $this->resolveVideoData($request);

        $data = array_merge([
            'title'       => $request->title,
            'slug'        => $this->generateUniqueSlug($request->title),
            'description' => Artikel::sanitizeHtml($description),
            'fasilitas'   => $fasilitas ?? '',
            'tersedia'    => $request->tersedia ?? 0,
            'view'        => $request->view ?? 0,
            'gmaps'       => $request->gmaps ?? $request->google_maps,
        ], $wilayahData, $videoData);

        $kosan = ProductKosan::create($data);

        // Dukung multi-image upload atau single image fallback
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('kosan/image', 'public');
                    ProductImageKosan::create([
                        'product_kosan_id' => $kosan->id,
                        'image'            => $path,
                    ]);
                }
            }
        } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('kosan/image', 'public');
            ProductImageKosan::create([
                'product_kosan_id' => $kosan->id,
                'image'            => $path,
            ]);
        }

        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');

        return back()->with('success', 'Properti Kos-kosan berhasil ditambahkan');
    }

    public function update($product_kosan, Request $request)
    {
        $this->checkKosanAccess($product_kosan);
        $productKosan = ProductKosan::findOrFail($product_kosan);
        $oldSlug = $productKosan->slug;

        $request->validate([
            'title'        => 'required|string|max:255',
            'wilayah'      => 'nullable|string|max:255',
            'provinsi_id'  => 'nullable|string|max:15',
            'provinsi'     => 'nullable|string|max:120',
            'kabupaten_id' => 'nullable|string|max:15',
            'kabupaten'    => 'nullable|string|max:120',
            'kecamatan_id' => 'nullable|string|max:20',
            'kecamatan'    => 'nullable|string|max:120',
            'description'  => 'nullable|string',
            'deskripsi'    => 'nullable|string',
            'tersedia'     => 'nullable|numeric',
            'view'         => 'nullable|numeric',
            'gmaps'        => 'nullable|string',
            'google_maps'  => 'nullable|string',
            'video_mode'   => 'nullable|in:link,upload',
            'video_url'    => 'nullable|url|max:1000',
            'video_file'   => 'nullable|file|mimes:mp4,webm,mov,ogg,ogv|max:51200',
            'remove_video' => 'nullable|boolean',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'video_url.url'    => 'Link video harus berupa URL yang valid (diawali https:// atau http://).',
            'video_file.mimes' => 'Format file video harus MP4, WebM, MOV, atau OGG.',
            'video_file.max'   => 'Ukuran file video maksimal 50 MB.',
        ]);

        $fasilitas = $request->fasilitas;
        if (is_array($fasilitas)) {
            $fasilitas = implode(', ', $fasilitas);
        }

        $description = $request->description ?: ($request->deskripsi ?: ($productKosan->description ?: ''));
        $wilayahData = $this->resolveWilayahData($request, $productKosan);
        $videoData   = $this->resolveVideoData($request, $productKosan);

        $data = array_merge([
            'title'       => $request->title,
            'slug'        => $this->generateUniqueSlug($request->title, $productKosan->id),
            'description' => Artikel::sanitizeHtml($description),
            'fasilitas'   => $fasilitas ?? '',
            'tersedia'    => $productKosan->productKamarKosan()->count() > 0 ? $productKosan->tersedia : ($request->tersedia ?? 0),
            'view'        => $request->view ?? ($productKosan->view ?? 0),
            'gmaps'       => $request->gmaps ?? $request->google_maps,
        ], $wilayahData, $videoData);

        $productKosan->update($data);
        if ($productKosan->productKamarKosan()->count() > 0) {
            $productKosan->syncAvailableCount();
        }

        // Dukung multi-image upload atau single image fallback
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('kosan/image', 'public');
                    ProductImageKosan::create([
                        'product_kosan_id' => $productKosan->id,
                        'image'            => $path,
                    ]);
                }
            }
        } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('kosan/image', 'public');
            ProductImageKosan::create([
                'product_kosan_id' => $productKosan->id,
                'image'            => $path,
            ]);
        }

        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');
        Cache::forget("kosan_detail_{$oldSlug}");
        Cache::forget("kosan_detail_{$productKosan->slug}");

        return back()->with('success', 'Properti Kos-kosan berhasil diperbarui');
    }

    public function delete($product_kosan)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang berhak menghapus properti kos-kosan.');
        }

        $productKosan = ProductKosan::findOrFail($product_kosan);
        if ($productKosan->occupied_rooms_count > 0) {
            return back()->with('failed', 'Tidak dapat menghapus properti kosan karena masih terdapat kamar yang dihuni oleh penyewa aktif.');
        }

        $slug = $productKosan->slug;
        $productKosan->delete();

        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');
        Cache::forget("kosan_detail_{$slug}");

        return back()->with('success', 'Properti Kos-kosan berhasil dihapus');
    }

    // IMAGE KOSAN
    public function insertImage($product_kosan, Request $request)
    {
        $this->checkKosanAccess($product_kosan);
        $request->validate([
            'images'            => 'nullable|array',
            'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image'             => 'nullable|array',
            'image.*'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'dataImage'         => 'nullable|array',
            'dataImage.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadedCount = 0;
        DB::transaction(function () use ($request, $product_kosan, &$uploadedCount) {
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
                    $path = $file->store('kosan/image', 'public');
                    ProductImageKosan::create([
                        'product_kosan_id' => $product_kosan,
                        'image'            => $path,
                    ]);
                    $uploadedCount++;
                }
            }

            if ($request->hasFile('dataImage')) {
                foreach ($request->file('dataImage') as $item) {
                    if (isset($item['image']) && $item['image']->isValid()) {
                        $path = $item['image']->store('kosan/image', 'public');
                        ProductImageKosan::create([
                            'product_kosan_id' => $product_kosan,
                            'image'            => $path,
                        ]);
                        $uploadedCount++;
                    }
                }
            }
        });

        $productKosan = ProductKosan::find($product_kosan);
        if ($productKosan) {
            Cache::forget('home_kamar_list');
            Cache::forget("kosan_detail_{$productKosan->slug}");
        }

        if ($uploadedCount > 0) {
            return back()->with('success', "Berhasil mengunggah {$uploadedCount} foto galeri kosan.");
        }

        return back()->with('warning', 'Tidak ada foto yang diunggah. Pastikan format file JPG, PNG, atau WEBP.');
    }

    public function deleteImage($product_image_kosan)
    {
        $productImage = ProductImageKosan::findOrFail($product_image_kosan);
        $this->checkKosanAccess($productImage->product_kosan_id);
        $productKosan = $productImage->productKosan;

        if ($productImage->image && Storage::disk('public')->exists($productImage->image)) {
            Storage::disk('public')->delete($productImage->image);
        }

        $productImage->delete();

        if ($productKosan) {
            Cache::forget('home_kamar_list');
            Cache::forget("kosan_detail_{$productKosan->slug}");
        }

        return back()->with('success', 'delete success');
    }

    public function indexImage($product_kosan)
    {
        $kosan = ProductKosan::with('productImageKosan')->findOrFail($product_kosan);
        $this->checkKosanAccess($kosan->id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($kosan->productImageKosan);
        }

        return redirect()->route('admin.product.kosan.index')->with('info', "Foto galeri properti '{$kosan->title}' dapat dikelola langsung melalui tombol kelola foto pada tabel.");
    }
}
