<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\ProductKamarKosan;
use App\Models\ProductKosan;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $property = ProductKosan::jumala();

        $allPublishedRooms = Cache::remember('home_kamar_list', 3600, function () use ($property) {
            return ProductKamarKosan::with(['productKosan.productImageKosan', 'productKamarImageKosan', 'priceKamar', 'tamu'])
                ->where('product_kosan_id', $property->id)
                ->where(function ($q) {
                    $q->whereNull('status_ketersediaan')
                      ->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
                })
                ->orderByDesc('views')
                ->orderByDesc('created_at')
                ->get();
        });

        $kamarList = $allPublishedRooms;

        if ($request->filled('search')) {
            $needle = strtolower(trim((string) $request->input('search')));
            $kamarList = $kamarList->filter(function ($kamar) use ($needle) {
                $haystack = strtolower(implode(' ', array_filter([
                    $kamar->room,
                    $kamar->tipe_kamar,
                    $kamar->luas_kamar,
                    $kamar->fasilitas,
                    strip_tags((string) $kamar->description),
                ])));
                return str_contains($haystack, $needle);
            })->values();
        }

        if ($request->filled('price_range')) {
            $rangeMap = [
                'under-500' => [null, 500000],
                '500-1000'  => [500000, 1000000],
                '1000-1500' => [1000000, 1500000],
                '1500-2500' => [1500000, 2500000],
                'over-2500' => [2500000, null],
            ];
            $rangeKey = $request->input('price_range');
            if (isset($rangeMap[$rangeKey])) {
                [$min, $max] = $rangeMap[$rangeKey];
                $kamarList = $kamarList->filter(function ($kamar) use ($min, $max) {
                    $monthly = $kamar->priceKamar->first(fn($p) => strtolower($p->kategori) === 'bulan');
                    if (!$monthly) {
                        return false;
                    }
                    $raw = (float) $monthly->price;
                    $disc = (float) $monthly->discount > 0 ? (float) $monthly->discount : (float) ($kamar->cumulative_discount ?? 0);
                    $net = $disc > 0 ? ($disc <= 100 ? $raw * (1 - $disc / 100) : max(0, $raw - $disc)) : $raw;

                    if (!is_null($min) && $net < $min) {
                        return false;
                    }
                    if (!is_null($max) && $net > $max) {
                        return false;
                    }
                    return true;
                })->values();
            }
        }

        $kosanList = Cache::remember('home_kosan_list', 3600, function () {
            return ProductKosan::with(['productImageKosan', 'productKamarKosan.priceKamar'])->get();
        });

        $testimonis = Cache::remember('home_testimonis', 3600, function () {
            return Testimoni::orderByDesc('created_at')->get();
        });

        return view('frontend.index', compact('kamarList', 'kosanList', 'testimonis'));
    }

    public function kosanIndex(Request $request)
    {
        $params = array_filter($request->only(['search', 'price_range']));
        $target = route('home', $params) . '#pilihan-kamar';
        return redirect($target);
    }

    public function kosanDetail($slug)
    {
        return redirect(url('/#tentang'));
    }

    public function kamarDetail($product_kamar_kosan = null)
    {
        if (!$product_kamar_kosan) {
            return redirect(url('/#pilihan-kamar'));
        }

        $today = now()->toDateString();
        $cutoff = now()->subHours(2);

        $kamar = ProductKamarKosan::with([
            'productKosan.productImageKosan',
            'productKosan.productKamarKosan' => function ($q) use ($product_kamar_kosan) {
                $q->where('id', '!=', $product_kamar_kosan)
                  ->where(function ($sub) {
                      $sub->whereNull('status_ketersediaan')
                          ->orWhere('status_ketersediaan', '!=', 'tidak_tersedia');
                  })
                  ->with(['productKamarImageKosan', 'priceKamar', 'tamu']);
            },
            'productKamarImageKosan',
            'priceKamar',
            'tamu' => function ($t) use ($today, $cutoff) {
                $t->where(function ($q) use ($today) {
                    $q->where('status', 'approved')
                      ->whereDate('end_date', '>=', $today);
                })->orWhere(function ($q) use ($cutoff) {
                    $q->where('status', 'pending')
                      ->where('created_at', '>=', $cutoff);
                });
            },
        ])->findOrFail($product_kamar_kosan);

        if (!$kamar->isBookable() && !$kamar->active_tenant) {
            return redirect(url('/#pilihan-kamar'))
                ->with('failed', 'Mohon maaf, unit kamar tersebut saat ini sedang tidak tersedia.');
        }

        $kamar->increment('views');

        return view('frontend.kosan.kamar.detail-kamar', compact('kamar'));
    }

    /**
     * Form booking kamar kos.
     */
    public function formBooking($product_kamar_kosan)
    {
        $kamar = ProductKamarKosan::with(['priceKamar', 'productKosan', 'tamu', 'productKamarImageKosan'])
            ->findOrFail($product_kamar_kosan);

        if (!$kamar->isBookable()) {
            return redirect(url('/#pilihan-kamar'))
                ->with('failed', 'Unit kamar ini sedang dinonaktifkan / tidak tersedia untuk pemesanan.');
        }

        $bookedRanges = [];
        $latestBookedEnd = null;
        if ($kamar && $kamar->tamu) {
            foreach ($kamar->tamu as $t) {
                $isStalePending = ($t->status === 'pending' && $t->created_at && $t->created_at->lt(now()->subHours(2)));
                if (!$isStalePending && in_array(strtolower($t->status), ['approved', 'pending']) && $t->start_date && $t->end_date) {
                    $sDate = \Carbon\Carbon::parse($t->start_date)->format('Y-m-d');
                    $eDate = \Carbon\Carbon::parse($t->end_date)->format('Y-m-d');
                    $bookedRanges[] = [
                        'from' => $sDate,
                        'to'   => $eDate,
                    ];
                    if (!$latestBookedEnd || $eDate > $latestBookedEnd) {
                        $latestBookedEnd = $eDate;
                    }
                }
            }
        }

        $todayStr = now()->format('Y-m-d');
        $isTodayBooked = false;
        foreach ($bookedRanges as $r) {
            if ($todayStr >= $r['from'] && $todayStr <= $r['to']) {
                $isTodayBooked = true;
                break;
            }
        }

        $firstAvailableDate = $todayStr;
        if ($isTodayBooked && $latestBookedEnd) {
            $firstAvailableDate = \Carbon\Carbon::parse($latestBookedEnd)->addDay()->format('Y-m-d');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('frontend.kosan.kamar.form-booking', compact('kamar', 'bookedRanges', 'latestBookedEnd', 'isTodayBooked', 'firstAvailableDate', 'paymentMethods'));
    }

    /**
     * API: Cek tanggal ketersediaan kamar.
     */
    public function checkDateKamar($id)
    {
        $kamar = ProductKamarKosan::findOrFail($id);

        $tamu = $kamar->tamu()
            ->where(function ($q) {
                $q->where('status', 'approved')
                  ->orWhere(function ($qp) {
                      $qp->where('status', 'pending')
                         ->where('created_at', '>=', now()->subHours(2));
                  });
            })
            ->whereDate('end_date', '>=', now()->toDateString())
            ->select('start_date', 'end_date')
            ->get();

        return response()->json($tamu);
    }
}
