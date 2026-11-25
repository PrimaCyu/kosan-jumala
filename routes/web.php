<?php

use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KamarKosanController;
use App\Http\Controllers\Admin\KosanController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\SosialMediaController;
use App\Http\Controllers\TamuBookingController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public / Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kosan', [HomeController::class, 'kosanIndex'])->name('kosan.index');
Route::get('/kosan/detail', fn() => redirect()->route('kosan.index'));
Route::get('/kosan/detail/{slug}', [HomeController::class, 'kosanDetail'])->name('kosan.detail');
Route::get('/kamar/detail/{product_kamar_kosan?}', [HomeController::class, 'kamarDetail'])->name('kamar.detail');

Route::get('/kamar/booking/{product_kamar_kosan}', [HomeController::class, 'formBooking'])->name('form.booking.kamar');

Route::post('/booking/kamar/{product_kamar_kosan}', [TamuBookingController::class, 'booking'])->middleware('throttle:10,1')->name('tamu.booking');
Route::get('/booking/success/{token}', [TamuBookingController::class, 'bookingSuccess'])->name('booking.success');
Route::get('/booking/download-invoice/{token}', [TamuBookingController::class, 'downloadInvoice'])->name('booking.download.invoice');

Route::get('/check-date/kamar/{id}', [HomeController::class, 'checkDateKamar'])->name('check.date.kamar');

/*
|--------------------------------------------------------------------------
| Wilayah Indonesia Dependent Dropdown API (Provinsi -> Kab/Kota -> Kecamatan)
|--------------------------------------------------------------------------
*/
Route::prefix('/api/wilayah')->controller(WilayahController::class)->name('api.wilayah.')->group(function () {
    Route::get('/provinces', 'provinces')->name('provinces');
    Route::get('/regencies/{provinceId}', 'regencies')->name('regencies');
    Route::get('/districts/{regencyId}', 'districts')->name('districts');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes (Requires Auth) - Single Property: Jumala Living
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Kamar Jumala Living (Primary Room Management)
    Route::prefix('/kamar')->controller(KamarKosanController::class)->name('kamar.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/search-ajax', 'searchAjax')->name('search.ajax');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{product_kamar_kosan}', 'show')->whereNumber('product_kamar_kosan')->name('show');
        Route::get('/{product_kamar_kosan}/edit', 'edit')->whereNumber('product_kamar_kosan')->name('edit');
        Route::put('/{product_kamar_kosan}', 'update')->whereNumber('product_kamar_kosan')->name('update');
        Route::patch('/{product_kamar_kosan}/toggle-status', 'toggleStatus')->whereNumber('product_kamar_kosan')->name('toggle-status');
        Route::delete('/{product_kamar_kosan}', 'destroy')->whereNumber('product_kamar_kosan')->name('destroy');

        Route::post('/{product_kamar_kosan}/image', 'storeImage')->whereNumber('product_kamar_kosan')->name('image.store');
        Route::delete('/{product_kamar_kosan}/image/{product_kamar_image_kosan}', 'destroyImage')->name('image.destroy');

        Route::post('/{product_kamar_kosan}/price', 'storePrice')->whereNumber('product_kamar_kosan')->name('price.store');
        Route::put('/{product_kamar_kosan}/price/{price_kamar}', 'updatePrice')->name('price.update');
        Route::delete('/{product_kamar_kosan}/price/{price_kamar}', 'destroyPrice')->name('price.destroy');

        Route::put('/{product_kamar_kosan}/tamu/{tamu}/renew', 'renewTamuSingle')->name('tamu.renew');
        Route::delete('/{product_kamar_kosan}/tamu/{tamu}', 'destroyTamuSingle')->name('tamu.destroy');
    });

    // Profil & Fasilitas Hunian Tunggal (Jumala Living)
    Route::prefix('/properti')->controller(KosanController::class)->name('properti.')->group(function () {
        Route::get('/', 'editProperti')->name('edit');
        Route::put('/', 'updateProperti')->name('update');
        Route::post('/image', 'insertPropertiImage')->name('image.insert');
        Route::delete('/image/{product_image_kosan}', 'deleteImage')->name('image.delete');
    });

    // Redirect legacy /admin/product-kosan paths to single-property rooms
    Route::get('/product-kosan/index', fn() => redirect()->route('admin.kamar.index'))->name('product.kosan.index');

    // Booking Approval Management
    Route::prefix('/booking')->controller(BookingAdminController::class)->name('booking.')->group(function () {
        Route::get('/index', 'indexBooking')->name('index');
        Route::put('/approve/{tamu}', 'approveBooking')->name('approve');
        Route::put('/reject/{tamu}', 'rejectBooking')->name('reject');
        Route::get('/proof/{tamu}', 'viewProof')->name('proof');
    });

    // Admin Management (Super Admin only)
    Route::prefix('/users')->middleware(['super_admin'])->controller(AdminManagementController::class)->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::put('/update/{user}', 'update')->name('update');
        Route::delete('/destroy/{user}', 'destroy')->name('destroy');
    });

    // Social Media (Super Admin only)
    Route::prefix('/sosial-media')->middleware(['super_admin'])->controller(SosialMediaController::class)->name('sosial.media')->group(function () {
        Route::get('/index', 'index')->name('.index');
        Route::get('/search-ajax', 'searchAjax')->name('.search.ajax');
        Route::post('/insert', 'insert')->name('.insert');
        Route::put('/update/{sosial_media}', 'update')->name('.update');
        Route::delete('/delete/{sosial_media}', 'delete')->name('.delete');
    });

    // Payment Methods Management (Super Admin only)
    Route::prefix('/payment-methods')->middleware(['super_admin'])->controller(PaymentMethodController::class)->name('payment-methods.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::put('/update/{paymentMethod}', 'update')->name('update');
        Route::patch('/toggle/{paymentMethod}', 'toggle')->name('toggle');
        Route::delete('/destroy/{paymentMethod}', 'destroy')->name('destroy');
    });

    // Testimoni
    Route::prefix('/testimoni')->controller(TestimoniController::class)->name('testimoni')->group(function () {
        Route::get('/index', 'index')->name('.index');
        Route::get('/search-ajax', 'searchAjax')->name('.search.ajax');
        Route::post('/insert', 'insert')->name('.insert');
        Route::put('/update/{testimoni}', 'update')->name('.update');
        Route::delete('/delete/{testimoni}', 'delete')->name('.delete');
    });

    // Profile & Password Management
    Route::prefix('/profile')->controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::put('/update', 'updateProfile')->name('update');
        Route::put('/password', 'updatePassword')->name('password');
    });

});
