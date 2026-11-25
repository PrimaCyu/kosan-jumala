<?php

namespace Tests\Feature;

use App\Models\PriceKamar;
use App\Models\ProductKamarKosan;
use App\Models\ProductKosan;
use App\Models\Tamu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $operationalAdmin;
    protected ProductKosan $kosan;
    protected ProductKamarKosan $kamarA;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->superAdmin = User::create([
            'name'     => 'Super Administrator',
            'email'    => 'superadmin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'super_admin',
        ]);

        $this->operationalAdmin = User::create([
            'name'     => 'Admin Operasional',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->kosan = ProductKosan::jumala();

        $this->kamarA = ProductKamarKosan::create([
            'product_kosan_id'    => $this->kosan->id,
            'room'                => 'Kamar 101 Deluxe',
            'tipe_kamar'          => 'Deluxe',
            'luas_kamar'          => '3x4 m',
            'lantai'              => 'Lantai 1',
            'status_ketersediaan' => 'tersedia',
            'description'         => 'Kamar lantai 1 menghadap taman',
            'fasilitas'           => 'AC, Kasur Springbed, Lemari Pakaian',
            'cumulative_discount' => 10,
            'views'               => 0,
        ]);

        PriceKamar::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'kategori'               => 'bulan',
            'price'                  => 1000000,
            'discount'               => 10,
        ]);

        PriceKamar::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'kategori'               => 'tahun',
            'price'                  => 11000000,
            'discount'               => 10,
        ]);
    }

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_booking_total_price_is_strictly_computed_server_side(): void
    {
        $startDate = now()->addDays(5)->format('Y-m-d');
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $payload = [
            'name'              => 'Siti Aisyah',
            'email'             => 'siti@example.com',
            'telp'              => '081234567890',
            'start_date'        => $startDate,
            'start_time'        => '09:00',
            'bulan'             => 2, // 2 * 1,000,000 - 10% = 1,800,000 + 12% PPN = 2,016,000
            'tahun'             => 0,
            'payment_method'    => 'transfer',
            'proof_of_transfer' => $file,
            'total_price'       => 500,
        ];

        $response = $this->post(route('tamu.booking', $this->kamarA->id), $payload);
        $response->assertRedirect();

        $tamu = Tamu::where('email', 'siti@example.com')->first();
        $this->assertNotNull($tamu);
        $this->assertEquals(2016000, (float) $tamu->total_price);
        $this->assertEquals('pending', $tamu->status);
    }

    public function test_proof_of_transfer_is_required_for_transfer_and_stored_privately(): void
    {
        $startDate = now()->addDays(10)->format('Y-m-d');

        $payloadNoProof = [
            'name'           => 'Rina Melati',
            'email'          => 'rina@example.com',
            'telp'           => '081987654321',
            'start_date'     => $startDate,
            'start_time'     => '10:00',
            'bulan'          => 1,
            'tahun'          => 0,
            'payment_method' => 'transfer',
        ];

        $this->post(route('tamu.booking', $this->kamarA->id), $payloadNoProof)
            ->assertSessionHasErrors(['proof_of_transfer']);

        $file = UploadedFile::fake()->image('struk_atm.png');
        $this->post(route('tamu.booking', $this->kamarA->id), array_merge($payloadNoProof, ['proof_of_transfer' => $file]))
            ->assertRedirect();

        $tamu = Tamu::where('email', 'rina@example.com')->first();
        $this->assertNotNull($tamu);
        $this->assertNotEmpty($tamu->proof_of_transfer);

        Storage::disk('local')->assertExists($tamu->proof_of_transfer);
        Storage::disk('public')->assertMissing($tamu->proof_of_transfer);
    }

    public function test_operational_admin_cannot_access_super_admin_only_modules(): void
    {
        $this->actingAs($this->operationalAdmin);

        $this->get(route('admin.users.index'))->assertStatus(403);
        $this->get(route('admin.sosial.media.index'))->assertStatus(403);
        $this->get(route('admin.payment-methods.index'))->assertStatus(403);

        $this->actingAs($this->superAdmin);
        $this->get(route('admin.users.index'))->assertStatus(200);
        $this->get(route('admin.sosial.media.index'))->assertStatus(200);
        $this->get(route('admin.payment-methods.index'))->assertStatus(200);
    }

    public function test_soft_deletes_preserves_tenant_records_when_room_is_deleted(): void
    {
        $this->actingAs($this->superAdmin);

        $tamu = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Dewi Lestari',
            'email'                  => 'dewi@example.com',
            'telp'                   => '081233445566',
            'start_date'             => now()->subMonths(2)->format('Y-m-d'),
            'end_date'               => now()->subMonth()->format('Y-m-d'),
            'start_time'             => '12:00',
            'payment_method'         => 'cash',
            'total_price'            => 1000000,
            'status'                 => 'approved',
        ]);

        $response = $this->delete(route('admin.kamar.destroy', $this->kamarA->id));
        $response->assertRedirect(route('admin.kamar.index'));

        $this->assertSoftDeleted('product_kamar_kosans', ['id' => $this->kamarA->id]);
        $this->assertDatabaseHas('tamus', ['id' => $tamu->id]);
    }

    public function test_cannot_delete_room_with_active_occupant(): void
    {
        $this->actingAs($this->superAdmin);

        Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Penyewa Aktif',
            'email'                  => 'aktif@example.com',
            'telp'                   => '08111222333',
            'start_date'             => now()->subDays(5)->format('Y-m-d'),
            'end_date'               => now()->addDays(20)->format('Y-m-d'),
            'start_time'             => '12:00',
            'payment_method'         => 'transfer',
            'total_price'            => 1000000,
            'status'                 => 'approved',
        ]);

        $response = $this->delete(route('admin.kamar.destroy', $this->kamarA->id));
        $response->assertSessionHas('failed');
        $this->assertNotSoftDeleted('product_kamar_kosans', ['id' => $this->kamarA->id]);
    }

    public function test_approving_booking_auto_rejects_colliding_pending_bookings(): void
    {
        $this->actingAs($this->superAdmin);

        $booking1 = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Pemohon Utama',
            'email'                  => 'utama@example.com',
            'telp'                   => '08122334455',
            'start_date'             => now()->addDays(2)->format('Y-m-d'),
            'end_date'               => now()->addDays(32)->format('Y-m-d'),
            'start_time'             => '10:00',
            'payment_method'         => 'transfer',
            'total_price'            => 1000000,
            'status'                 => 'pending',
        ]);

        $booking2 = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Pemohon Bertabrakan',
            'email'                  => 'tabrakan@example.com',
            'telp'                   => '08199887766',
            'start_date'             => now()->addDays(10)->format('Y-m-d'),
            'end_date'               => now()->addDays(40)->format('Y-m-d'),
            'start_time'             => '14:00',
            'payment_method'         => 'transfer',
            'total_price'            => 1000000,
            'status'                 => 'pending',
        ]);

        $this->put(route('admin.booking.approve', $booking1->id))->assertSessionHas('success');

        $booking1->refresh();
        $booking2->refresh();

        $this->assertEquals('approved', $booking1->status);
        $this->assertEquals('reject', $booking2->status);
        $this->assertStringContainsString('Kamar telah disetujui untuk pemohon booking lain', $booking2->rejection_reason);
    }

    public function test_expire_pending_command_cleans_stale_bookings(): void
    {
        $staleBooking = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Pemohon Menggantung',
            'email'                  => 'gantung@example.com',
            'telp'                   => '08133221100',
            'start_date'             => now()->addDays(5)->format('Y-m-d'),
            'end_date'               => now()->addDays(35)->format('Y-m-d'),
            'start_time'             => '10:00',
            'payment_method'         => 'transfer',
            'total_price'            => 1000000,
            'status'                 => 'pending',
        ]);
        Tamu::where('id', $staleBooking->id)->update(['created_at' => now()->subHours(3)]);

        $freshBooking = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Pemohon Baru',
            'email'                  => 'baru@example.com',
            'telp'                   => '08177665544',
            'start_date'             => now()->addDays(60)->format('Y-m-d'),
            'end_date'               => now()->addDays(90)->format('Y-m-d'),
            'start_time'             => '11:00',
            'payment_method'         => 'transfer',
            'total_price'            => 1000000,
            'status'                 => 'pending',
            'created_at'             => now()->subMinutes(30),
        ]);

        Artisan::call('kosan:expire-pending', ['--hours' => 2]);

        $staleBooking->refresh();
        $freshBooking->refresh();

        $this->assertEquals('reject', $staleBooking->status);
        $this->assertEquals('pending', $freshBooking->status);
    }

    public function test_room_full_crud_frontend_sync_and_deactivation_rules(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create room with specs, photo, and price
        $photo = UploadedFile::fake()->image('kamar_vip.jpg');
        $storeRes = $this->post(route('admin.kamar.store'), [
            'room'                => 'Suite Anggrek 09',
            'tipe_kamar'          => 'VIP Suite',
            'luas_kamar'          => '4x5 m',
            'lantai'              => 'Lantai 2',
            'status_ketersediaan' => 'tersedia',
            'price_bulan'         => 2000000,
            'cumulative_discount' => 25,
            'fasilitas'           => ['AC', 'Kamar Mandi Dalam'],
            'fasilitas_tambahan'  => 'Smart Lock',
            'description'         => '<p>Kamar VIP nyaman untuk mahasiswi.</p>',
            'catatan'             => 'Include air bersih.',
            'images'              => [$photo],
        ]);
        $storeRes->assertRedirect();

        $suite = ProductKamarKosan::where('room', 'Suite Anggrek 09')->first();
        $this->assertNotNull($suite);
        $this->assertEquals('VIP Suite', $suite->tipe_kamar);
        $this->assertEquals('4x5 m', $suite->luas_kamar);
        $this->assertEquals('tersedia', $suite->status_ketersediaan);
        $this->assertEquals(1, $suite->productKamarImageKosan()->count());

        // 2. Verify appears on homepage & detail page with consistent discounted price (1.500.000)
        Cache::flush();
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('Suite Anggrek 09');
        $homeRes->assertSee('Rp 1.500.000');

        $detailRes = $this->get(route('kamar.detail', $suite->id));
        $detailRes->assertStatus(200);
        $detailRes->assertSee('Suite Anggrek 09');
        $detailRes->assertSee('Rp 1.500.000');
        $detailRes->assertSee('VIP Suite');
        $detailRes->assertSee('Smart Lock');

        // 3. Update room price & facilities -> verify immediately synced to frontend
        $this->put(route('admin.kamar.update', $suite->id), [
            'room'                => 'Suite Anggrek 09 Updated',
            'tipe_kamar'          => 'Executive',
            'luas_kamar'          => '4x5 m',
            'lantai'              => 'Lantai 2',
            'status_ketersediaan' => 'tersedia',
            'price_bulan'         => 2400000,
            'cumulative_discount' => 0,
            'fasilitas'           => ['AC', 'TV / Smart TV'],
            'description'         => '<p>Kamar Executive diperbarui.</p>',
        ])->assertRedirect(route('admin.kamar.show', $suite->id));

        Cache::flush();
        $homeUpdated = $this->get('/');
        $homeUpdated->assertSee('Suite Anggrek 09 Updated');
        $homeUpdated->assertSee('Rp 2.400.000');

        // 4. Toggle room to tidak_tersedia -> hidden from homepage & blocked from booking
        $this->patch(route('admin.kamar.toggle-status', $suite->id))->assertRedirect();
        $suite->refresh();
        $this->assertEquals('tidak_tersedia', $suite->status_ketersediaan);

        Cache::flush();
        auth()->logout();
        session()->flush();
        $this->get('/')->assertDontSee('Suite Anggrek 09 Updated');
        $this->get(route('form.booking.kamar', $suite->id))->assertRedirect();
        $this->post(route('tamu.booking', $suite->id), [
            'name'           => 'Tamu Test',
            'email'          => 'tamutest@example.com',
            'telp'           => '081234567890',
            'start_date'     => now()->addDays(3)->format('Y-m-d'),
            'start_time'     => '12:00',
            'bulan'          => 1,
            'payment_method' => 'cash',
        ])->assertSessionHas('failed');
    }

    public function test_multi_room_generator_creates_units_and_annual_price(): void
    {
        $this->actingAs($this->superAdmin);

        $res = $this->post(route('admin.kamar.store'), [
            'is_multiple_rooms' => 1,
            'room_prefix'       => 'Unit ',
            'room_start_number' => 201,
            'room_quantity'     => 3,
            'price_bulan'       => 850000,
        ]);
        $res->assertRedirect(route('admin.kamar.index'));

        $this->assertDatabaseHas('product_kamar_kosans', ['room' => 'Unit 201']);
        $this->assertDatabaseHas('product_kamar_kosans', ['room' => 'Unit 202']);
        $this->assertDatabaseHas('product_kamar_kosans', ['room' => 'Unit 203']);

        $unit201 = ProductKamarKosan::where('room', 'Unit 201')->first();
        $yearly = PriceKamar::where('product_kamar_kosan_id', $unit201->id)->where('kategori', 'tahun')->first();
        $this->assertEquals(850000 * 12, (float) $yearly->price);
    }

    public function test_artikel_routes_are_completely_removed(): void
    {
        $this->get('/news')->assertStatus(404);
        $this->get('/news/detail/tips-kos')->assertStatus(404);
        $this->actingAs($this->superAdmin)->get('/admin/artikel/index')->assertStatus(404);
    }

    public function test_properti_profile_update_and_video_safety(): void
    {
        $this->actingAs($this->superAdmin);

        $videoFile = UploadedFile::fake()->create('tour_jumala.mp4', 512, 'video/mp4');
        $res = $this->put(route('admin.properti.update'), [
            'title'       => 'Jumala Living Residence',
            'wilayah'     => 'Jl. Kenanga No. 3, Lowokwaru, Malang',
            'fasilitas'   => ['Wi-Fi / Internet', 'CCTV 24 Jam'],
            'description' => '<p>Hunian nyaman khusus putri di Malang.</p>',
            'video_mode'  => 'upload',
            'video_file'  => $videoFile,
        ]);
        $res->assertSessionHas('success');

        $this->kosan->refresh();
        $this->assertEquals('Jumala Living Residence', $this->kosan->title);
        $this->assertNotNull($this->kosan->video_path);
        Storage::disk('public')->assertExists($this->kosan->video_path);

        // Reject javascript: XSS video URL on room
        $this->assertNull(ProductKosan::parseVideoMedia(null, 'javascript:alert(1)'));
        $this->put(route('admin.kamar.update', $this->kamarA->id), [
            'room'        => $this->kamarA->room,
            'price_bulan' => 1000000,
            'video_mode'  => 'link',
            'video_url'   => 'javascript:alert(1)',
        ])->assertSessionHasErrors(['video_url']);
    }

    public function test_wilayah_indonesia_api_returns_hierarchy(): void
    {
        $resProv = $this->getJson(route('api.wilayah.provinces'));
        $resProv->assertStatus(200);
        $this->assertGreaterThanOrEqual(38, count($resProv->json()));
    }
}
