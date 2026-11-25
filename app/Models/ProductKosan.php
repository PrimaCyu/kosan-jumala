<?php

namespace App\Models;

use App\Models\Concerns\HasVideoMedia;
use App\Service\WilayahService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductKosan extends Model
{
    use SoftDeletes, HasVideoMedia;
    protected $fillable =
    [
        'title',
        'slug',
        'description',
        'fasilitas',
        'wilayah',
        'provinsi_id',
        'provinsi',
        'kabupaten_id',
        'kabupaten',
        'kecamatan_id',
        'kecamatan',
        'view',
        'tersedia',
        'gmaps',
        'video_path',
        'video_url',
    ];

    protected static function boot(){
        parent::boot();

        static::deleting(function ($kosan) {
            foreach ($kosan->productImageKosan as $image) {
                $image->delete();
            }

            foreach ($kosan->productKamarKosan as $kamar) {
                $kamar->delete();
            }
        });
    }

    public function productImageKosan(){
        return $this->hasMany(ProductImageKosan::class);
    }

    public function productKamarKosan(){
        return $this->hasMany(ProductKamarKosan::class);
    }

    public function admins(){
        return $this->belongsToMany(User::class, 'admin_kosan', 'product_kosan_id', 'user_id')->withTimestamps();
    }

    /**
     * Resolve or initialize the single canonical Jumala Living property record.
     */
    public static function jumala(): self
    {
        $property = static::orderBy('id', 'asc')->first();
        if ($property) {
            return $property;
        }

        return static::create([
            'title'        => 'Jumala Living',
            'slug'         => 'jumala-living',
            'wilayah'      => 'Jl. Kenanga No. 3, Lowokwaru, Malang, Jawa Timur 65141',
            'provinsi_id'  => '35',
            'provinsi'     => 'Jawa Timur',
            'kabupaten_id' => '35.73',
            'kabupaten'    => 'Kota Malang',
            'kecamatan_id' => '35.73.05',
            'kecamatan'    => 'Lowokwaru',
            'description'  => '<p>Jumala Living merupakan hunian kos khusus putri yang menghadirkan lingkungan nyaman, privat, dan mendukung aktivitas sehari-hari di kawasan Lowokwaru, Malang.</p>',
            'fasilitas'    => 'Wi-Fi / Internet, Parkir Motor, Dapur Bersama, CCTV 24 Jam, Ruang Tamu Bersama, Ruang Jemur, Kulkas Bersama, Air Minum / Dispenser',
            'gmaps'        => 'https://maps.google.com/?q=Jumala+Living+Jl.+Kenanga+No.+3,+Lowokwaru,+Malang',
            'tersedia'     => 0,
            'view'         => 0,
        ]);
    }

    /**
     * Hitung ulang dan sinkronkan kolom tersedia di database dengan data kamar riil.
     */
    public function syncAvailableCount(): int
    {
        $totalBookable = $this->productKamarKosan()
            ->where(function ($q) {
                $q->whereNull('status_ketersediaan')
                  ->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
            })
            ->count();

        if ($this->productKamarKosan()->count() === 0) {
            return (int) ($this->tersedia ?? 0);
        }

        $today = now()->toDateString();
        $occupiedBookable = $this->productKamarKosan()
            ->where(function ($q) {
                $q->whereNull('status_ketersediaan')
                  ->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
            })
            ->whereHas('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })
            ->count();

        $available = max(0, $totalBookable - $occupiedBookable);
        $this->update(['tersedia' => $available]);
        return $available;
    }

    /**
     * Accessor untuk total kapasitas kamar riil.
     */
    public function getTotalRoomsCountAttribute(): int
    {
        if ($this->relationLoaded('productKamarKosan')) {
            return $this->productKamarKosan->count();
        }
        return $this->productKamarKosan()->count();
    }

    /**
     * Accessor untuk jumlah kamar terisi aktif saat ini.
     */
    public function getOccupiedRoomsCountAttribute(): int
    {
        $today = now()->toDateString();
        if ($this->relationLoaded('productKamarKosan')) {
            return $this->productKamarKosan->filter(function ($kamar) use ($today) {
                if ($kamar->relationLoaded('tamu')) {
                    return $kamar->tamu->where('status', 'approved')
                        ->filter(fn($t) => \Carbon\Carbon::parse($t->start_date)->toDateString() <= $today && \Carbon\Carbon::parse($t->end_date)->toDateString() >= $today)
                        ->count() > 0;
                }
                return $kamar->tamu()->where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->exists();
            })->count();
        }

        return $this->productKamarKosan()
            ->whereHas('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })
            ->count();
    }

    /**
     * Accessor untuk jumlah kamar kosong siap huni (tidak termasuk tidak_tersedia / terisi).
     */
    public function getAvailableRoomsCountAttribute(): int
    {
        if ($this->relationLoaded('productKamarKosan')) {
            if ($this->productKamarKosan->count() === 0) {
                return (int) ($this->tersedia ?? 0);
            }
            return $this->productKamarKosan->filter(fn($k) => $k->room_status === 'kosong')->count();
        }

        $total = $this->total_rooms_count;
        if ($total === 0) {
            return (int) ($this->tersedia ?? 0);
        }

        $today = now()->toDateString();
        $bookableCount = $this->productKamarKosan()
            ->where(function ($q) {
                $q->whereNull('status_ketersediaan')
                  ->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
            })
            ->whereDoesntHave('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })
            ->count();

        return $bookableCount;
    }

    /**
     * Accessor persentase tingkat okupansi.
     */
    public function getOccupancyRateAttribute(): float
    {
        $total = $this->total_rooms_count;
        if ($total === 0) return 0.0;
        return round(($this->occupied_rooms_count / $total) * 100, 1);
    }

    /**
     * Accessor alamat wilayah lengkap (Kecamatan, Kabupaten/Kota, Provinsi) dengan fallback ke wilayah lama.
     */
    public function getFullLocationAttribute(): string
    {
        $composed = WilayahService::composeWilayahLabel($this->kecamatan, $this->kabupaten, $this->provinsi);
        if ($composed !== '') {
            return $composed;
        }
        return $this->wilayah ?: 'Indonesia';
    }
}
