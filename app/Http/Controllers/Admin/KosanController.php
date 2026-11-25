<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImageKosan;
use App\Models\ProductKosan;
use App\Service\WilayahService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KosanController extends Controller
{
    public function editProperti()
    {
        $kosan = ProductKosan::jumala()->load('productImageKosan');
        $provinces = WilayahService::getProvinces();

        return view('backend.dashboard.properti.edit', compact('kosan', 'provinces'));
    }

    public function updateProperti(Request $request)
    {
        $productKosan = ProductKosan::jumala();
        $oldSlug = $productKosan->slug;

        $request->validate([
            'title'              => 'required|string|max:255',
            'wilayah'            => 'nullable|string|max:255',
            'provinsi_id'        => 'nullable|string|max:15',
            'provinsi'           => 'nullable|string|max:120',
            'kabupaten_id'       => 'nullable|string|max:15',
            'kabupaten'          => 'nullable|string|max:120',
            'kecamatan_id'       => 'nullable|string|max:20',
            'kecamatan'          => 'nullable|string|max:120',
            'description'        => 'nullable|string',
            'fasilitas_tambahan' => 'nullable|string|max:1000',
            'gmaps'              => 'nullable|string|max:2000',
            'video_mode'         => 'nullable|in:link,upload',
            'video_url'          => 'nullable|url|max:1000',
            'video_file'         => 'nullable|file|mimes:mp4,webm,mov,ogg,ogv|max:51200',
            'remove_video'       => 'nullable|boolean',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images'      => 'nullable|array',
            'delete_images.*'    => 'integer',
        ], [
            'title.required'   => 'Nama properti wajib diisi.',
            'video_url.url'    => 'Link video harus berupa URL yang valid (diawali https:// atau http://).',
            'video_file.mimes' => 'Format file video harus MP4, WebM, MOV, atau OGG.',
            'video_file.max'   => 'Ukuran file video maksimal 50 MB.',
        ]);

        // Gabungkan fasilitas checkbox + fasilitas tambahan
        $fasilitasInput = $request->input('fasilitas', []);
        if (!is_array($fasilitasInput)) {
            $fasilitasInput = array_filter(array_map('trim', explode(',', (string) $fasilitasInput)));
        }
        $extraFasilitas = array_filter(array_map('trim', explode(',', (string) $request->input('fasilitas_tambahan', ''))));
        $mergedFasilitas = implode(', ', array_unique(array_merge($fasilitasInput, $extraFasilitas)));

        $description = $request->input('description', $productKosan->description ?? '');
        $wilayahData = $this->resolveWilayahData($request, $productKosan);
        $videoData   = $this->resolveVideoData($request, $productKosan);

        $slug = Str::slug($request->title) ?: 'jumala-living';
        while (ProductKosan::where('slug', $slug)->where('id', '!=', $productKosan->id)->exists()) {
            $slug .= '-1';
        }

        $data = array_merge([
            'title'       => $request->title,
            'slug'        => $slug,
            'description' => HtmlSanitizer::clean($description),
            'fasilitas'   => $mergedFasilitas,
            'gmaps'       => $request->input('gmaps'),
        ], $wilayahData, $videoData);

        DB::transaction(function () use ($productKosan, $data, $request) {
            $productKosan->update($data);

            // Hapus foto yang dipilih
            if ($request->filled('delete_images')) {
                $toDelete = ProductImageKosan::where('product_kosan_id', $productKosan->id)
                    ->whereIn('id', $request->input('delete_images', []))
                    ->get();
                foreach ($toDelete as $img) {
                    if ($img->image && Storage::disk('public')->exists($img->image)) {
                        Storage::disk('public')->delete($img->image);
                    }
                    $img->delete();
                }
            }

            // Upload foto galeri baru
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
        });

        $productKosan->syncAvailableCount();

        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');
        if ($oldSlug) {
            Cache::forget("kosan_detail_{$oldSlug}");
        }
        Cache::forget("kosan_detail_{$productKosan->slug}");

        return back()->with('success', 'Profil & fasilitas hunian Jumala Living berhasil diperbarui.');
    }

    public function insertPropertiImage(Request $request)
    {
        $productKosan = ProductKosan::jumala();

        $request->validate([
            'images'   => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $uploadedCount = 0;
        DB::transaction(function () use ($request, $productKosan, &$uploadedCount) {
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
                        'product_kosan_id' => $productKosan->id,
                        'image'            => $path,
                    ]);
                    $uploadedCount++;
                }
            }
        });

        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');
        Cache::forget("kosan_detail_{$productKosan->slug}");

        if ($uploadedCount > 0) {
            return back()->with('success', "Berhasil mengunggah {$uploadedCount} foto galeri hunian.");
        }

        return back()->with('warning', 'Tidak ada foto yang diunggah. Pastikan format file JPG, PNG, atau WEBP.');
    }

    public function deleteImage($product_image_kosan)
    {
        $productImage = ProductImageKosan::findOrFail($product_image_kosan);
        $productKosan = $productImage->productKosan;

        if ($productImage->image && Storage::disk('public')->exists($productImage->image)) {
            Storage::disk('public')->delete($productImage->image);
        }

        $productImage->delete();

        Cache::forget('home_kamar_list');
        Cache::forget('home_kosan_list');
        if ($productKosan) {
            Cache::forget("kosan_detail_{$productKosan->slug}");
        }

        return back()->with('success', 'Foto hunian berhasil dihapus.');
    }

    private function resolveWilayahData(Request $request, ProductKosan $existing): array
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

        if ($request->filled('wilayah')) {
            return [
                'provinsi_id'  => $provId !== '' ? $provId : $existing->provinsi_id,
                'provinsi'     => $provName !== '' ? $provName : $existing->provinsi,
                'kabupaten_id' => $kabId !== '' ? $kabId : $existing->kabupaten_id,
                'kabupaten'    => $kabName !== '' ? $kabName : $existing->kabupaten,
                'kecamatan_id' => $kecId !== '' ? $kecId : $existing->kecamatan_id,
                'kecamatan'    => $kecName !== '' ? $kecName : $existing->kecamatan,
                'wilayah'      => trim((string) $request->input('wilayah')),
            ];
        }

        if ($provName !== '' || $kabName !== '' || $kecName !== '') {
            $composed = WilayahService::composeWilayahLabel($kecName, $kabName, $provName);
            return [
                'provinsi_id'  => $provId !== '' ? $provId : null,
                'provinsi'     => $provName !== '' ? $provName : null,
                'kabupaten_id' => $kabId !== '' ? $kabId : null,
                'kabupaten'    => $kabName !== '' ? $kabName : null,
                'kecamatan_id' => $kecId !== '' ? $kecId : null,
                'kecamatan'    => $kecName !== '' ? $kecName : null,
                'wilayah'      => $composed !== '' ? $composed : ($existing->wilayah ?? 'Malang, Jawa Timur'),
            ];
        }

        return [
            'provinsi_id'  => $existing->provinsi_id,
            'provinsi'     => $existing->provinsi,
            'kabupaten_id' => $existing->kabupaten_id,
            'kabupaten'    => $existing->kabupaten,
            'kecamatan_id' => $existing->kecamatan_id,
            'kecamatan'    => $existing->kecamatan,
            'wilayah'      => $existing->wilayah ?: 'Jl. Kenanga No. 3, Lowokwaru, Malang, Jawa Timur 65141',
        ];
    }

    private function resolveVideoData(Request $request, ProductKosan $existing): array
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
            if (!$oldPath) {
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
}
