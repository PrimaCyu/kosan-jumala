<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductKamarKosan;
use App\Models\ProductKosan;
use App\Models\Tamu;

class DashboardController extends Controller
{
    public function index()
    {
        $kosan = ProductKosan::jumala();

        // Seluruh unit kamar Jumala Living beserta harga, foto, dan relasi penyewa
        $allRooms = ProductKamarKosan::with([
                'productKamarImageKosan',
                'priceKamar',
                'tamu' => fn($q) => $q->orderByDesc('end_date'),
            ])
            ->where('product_kosan_id', $kosan->id)
            ->orderBy('room', 'asc')
            ->get();

        $totalKamar       = $allRooms->count();
        $occupiedKamar    = $allRooms->filter(fn($r) => $r->room_status === 'terisi')->count();
        $availableKamar   = $allRooms->filter(fn($r) => $r->room_status === 'kosong')->count();
        $pendingKamar     = $allRooms->filter(fn($r) => $r->room_status === 'pending')->count();
        $unavailableKamar = $allRooms->filter(fn($r) => $r->room_status === 'tidak_tersedia')->count();

        $occupancyRate = $totalKamar > 0 ? round(($occupiedKamar / $totalKamar) * 100, 1) : 0;

        // Pendapatan (Bulan ini & Total Akumulasi Approved)
        $currentMonthRevenue = Tamu::where('status', 'approved')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_price');

        $totalRevenue = Tamu::where('status', 'approved')->sum('total_price');

        // Booking Pending Menunggu Persetujuan
        $pendingBookings = Tamu::with(['productKamarKosan'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
        $pendingBookingsCount = $pendingBookings->count();

        // Sewa Aktif Mendekati Jatuh Tempo (dalam 7 hari)
        $expiringTenanciesCount = Tamu::where('status', 'approved')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->whereDate('end_date', '<=', now()->addDays(7)->toDateString())
            ->count();

        $operationalAlerts = [];
        if ($pendingBookingsCount > 0) {
            $operationalAlerts[] = [
                'type'         => 'pending_booking',
                'title'        => $pendingBookingsCount . ' Booking Menunggu Verifikasi',
                'subtitle'     => 'Permohonan sewa baru memerlukan pengecekan bukti pembayaran dan persetujuan pengelola.',
                'action_label' => 'Tinjau Booking',
                'action_url'   => route('admin.booking.index'),
                'icon'         => 'bi-calendar-check',
                'badge_class'  => 'bg-amber-subtle text-amber',
            ];
        }
        if ($expiringTenanciesCount > 0) {
            $operationalAlerts[] = [
                'type'         => 'expiring_lease',
                'title'        => $expiringTenanciesCount . ' Masa Sewa Akan Berakhir dalam 7 Hari',
                'subtitle'     => 'Periksa unit kamar terisi untuk koordinasi perpanjangan sewa penghuni.',
                'action_label' => 'Lihat Kamar Terisi',
                'action_url'   => route('admin.kamar.index', ['status' => 'terisi']),
                'icon'         => 'bi-clock-history',
                'badge_class'  => 'bg-gold-subtle',
            ];
        }

        // Tren Pendapatan 6 Bulan Terakhir
        $revenueTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $actualRev = Tamu::where('status', 'approved')
                ->whereYear('created_at', (int) $dt->year)
                ->whereMonth('created_at', (int) $dt->month)
                ->sum('total_price');

            $revenueTrend[] = [
                'month'      => $dt->isoFormat('MMM Y'),
                'full_month' => $dt->isoFormat('MMMM Y'),
                'revenue'    => (int) $actualRev,
            ];
        }

        // Aktivitas Pengelolaan Terbaru (menggabungkan transaksi booking & pembaruan kamar nyata)
        $timelineItems = collect();

        $recentTamus = Tamu::with(['productKamarKosan'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($recentTamus as $t) {
            $roomName = $t->productKamarKosan->room ?? 'Unit Kamar';
            $ts = $t->updated_at ?? $t->created_at ?? now();
            $timeFormatted = $ts->isToday() ? $ts->format('H:i') : ($ts->isYesterday() ? 'Kemarin' : $ts->isoFormat('D MMM'));

            if ($t->status === 'approved') {
                $timelineItems->push([
                    'timestamp' => $ts->timestamp,
                    'time'      => $timeFormatted,
                    'title'     => "Booking {$roomName} disetujui",
                    'detail'    => "{$t->name} • Rp " . number_format((float) $t->total_price, 0, ',', '.'),
                    'icon'      => 'bi-check-circle-fill',
                    'tone'      => 'success',
                ]);
            } elseif ($t->status === 'reject') {
                $timelineItems->push([
                    'timestamp' => $ts->timestamp,
                    'time'      => $timeFormatted,
                    'title'     => "Booking {$roomName} ditolak",
                    'detail'    => $t->name,
                    'icon'      => 'bi-x-circle',
                    'tone'      => 'danger',
                ]);
            } else {
                $timelineItems->push([
                    'timestamp' => $ts->timestamp,
                    'time'      => $timeFormatted,
                    'title'     => "Booking baru untuk {$roomName}",
                    'detail'    => "{$t->name} • Menunggu verifikasi",
                    'icon'      => 'bi-inbox-fill',
                    'tone'      => 'warning',
                ]);
            }
        }

        $recentRooms = ProductKamarKosan::where('product_kosan_id', $kosan->id)
            ->latest('updated_at')
            ->take(4)
            ->get();

        foreach ($recentRooms as $rm) {
            $ts = $rm->updated_at ?? $rm->created_at ?? now();
            $timeFormatted = $ts->isToday() ? $ts->format('H:i') : ($ts->isYesterday() ? 'Kemarin' : $ts->isoFormat('D MMM'));
            $isNew = $rm->created_at && $rm->updated_at && $rm->created_at->eq($rm->updated_at);
            $timelineItems->push([
                'timestamp' => $ts->timestamp,
                'time'      => $timeFormatted,
                'title'     => $isNew ? "Kamar {$rm->room} ditambahkan" : "Data kamar {$rm->room} diperbarui",
                'detail'    => trim(($rm->tipe_kamar ?: 'Unit Hunian') . ' • ' . ($rm->isBookable() ? 'Aktif' : 'Tidak Tersedia')),
                'icon'      => 'bi-door-open',
                'tone'      => 'brown',
            ]);
        }

        $recentActivities = $timelineItems
            ->sortByDesc('timestamp')
            ->take(6)
            ->values()
            ->all();

        // Ringkasan Data Kamar (maks 8 unit ditampilkan di dashboard)
        $roomsSummary = $allRooms->take(8);

        return view('backend.dashboard.home', compact(
            'kosan',
            'totalKamar',
            'occupiedKamar',
            'availableKamar',
            'pendingKamar',
            'unavailableKamar',
            'occupancyRate',
            'currentMonthRevenue',
            'totalRevenue',
            'pendingBookingsCount',
            'operationalAlerts',
            'revenueTrend',
            'recentActivities',
            'roomsSummary'
        ));
    }
}
