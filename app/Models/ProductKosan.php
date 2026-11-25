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
     * Hitung ulang dan sinkronkan kolom tersedia di database dengan data kamar riil.
     */
    public function syncAvailableCount(): int
    {
        $totalRooms = $this->productKamarKosan()->count();
        if ($totalRooms === 0) {
            return (int) ($this->tersedia ?? 0);
        }

        $today = now()->toDateString();
        $occupiedRooms = $this->productKamarKosan()
            ->whereHas('tamu', function ($q) use ($today) {
                $q->where('status', 'approved')
                  ->whereDate('start_date', '<=', $today)
                  ->whereDate('end_date', '>=', $today);
            })
            ->count();

        $available = max(0, $totalRooms - $occupiedRooms);
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
     * Accessor untuk jumlah kamar kosong riil.
     */
    public function getAvailableRoomsCountAttribute(): int
    {
        $total = $this->total_rooms_count;
        if ($total === 0) {
            return (int) ($this->tersedia ?? 0);
        }
        return max(0, $total - $this->occupied_rooms_count);
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
