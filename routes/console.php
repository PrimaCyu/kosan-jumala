<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Otomatis Kedaluwarsa Booking:
// Berjalan secara berkala untuk membersihkan pesanan 'pending' yang melewati batas waktu (default 2 jam)
// agar kamar yang belum dibayar tidak terkunci dan langsung tersedia kembali untuk penyewa lain.
\Illuminate\Support\Facades\Schedule::command('kosan:expire-pending --hours=2')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

