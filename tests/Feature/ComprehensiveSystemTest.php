<?php

namespace Tests\Feature;

use App\Models\FasilitasKamar;
use App\Models\PriceKamar;
use App\Models\ProductKamarKosan;
use App\Models\ProductKosan;
use App\Models\Tamu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $branchAdmin;
    protected ProductKosan $kosanA;
    protected ProductKosan $kosanB;
    protected ProductKamarKosan $kamarA;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        // Create Super Admin
        $this->superAdmin = User::create([
            'name'     => 'Super Administrator',
            'email'    => 'superadmin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'super_admin',
        ]);

        // Create Branch Admin
        $this->branchAdmin = User::create([
            'name'     => 'Branch Admin',
            'email'    => 'branchadmin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        // Create Kosan A and assign to Branch Admin
        $this->kosanA = ProductKosan::create([
            'title'       => 'Kos Sakura Indah',
            'slug'        => 'kos-sakura-indah',
            'description' => 'Kos nyaman dan bersih.',
            'fasilitas'   => 'WiFi, AC, Parkir',
            'wilayah'     => 'Denpasar',
            'view'        => 0,
            'tersedia'    => 5,
        ]);
        $this->branchAdmin->kosans()->attach($this->kosanA->id);

        // Create Kosan B (not assigned to Branch Admin)
        $this->kosanB = ProductKosan::create([
            'title'       => 'Kos Melati Harmoni',
            'slug'        => 'kos-melati-harmoni',
            'description' => 'Kos tengah kota.',
            'fasilitas'   => 'WiFi, Dapur',
            'wilayah'     => 'Badung',
            'view'        => 0,
            'tersedia'    => 3,
        ]);

        // Create Kamar for Kosan A
        $this->kamarA = ProductKamarKosan::create([
            'product_kosan_id'    => $this->kosanA->id,
            'room'                => 'Kamar 101 Standard',
            'description'         => 'Kamar lantai 1',
            'fasilitas'           => 'Kasur, Lemari, Meja',
            'cumulative_discount' => 10, // 10% discount
            'views'               => 0,
        ]);

        // Add monthly & yearly price to Kamar A
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

    /**
     * 1. Test Security Headers Middleware
     */
    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * 2. Test Anti-Price Tampering on Booking
     */
    public function test_booking_total_price_is_strictly_computed_server_side(): void
    {
        $startDate = now()->addDays(5)->format('Y-m-d');
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        // Attacker attempts to send fake client total_price of 500 rupiah
        $payload = [
            'name'              => 'Budi Santoso',
            'email'             => 'budi@example.com',
            'telp'              => '081234567890',
            'start_date'        => $startDate,
            'start_time'        => '09:00',
            'bulan'             => 2, // 2 months: 2 * 1,000,000 = 2,000,000 - 10% discount = 1,800,000
            'tahun'             => 0,
            'payment_method'    => 'transfer',
            'proof_of_transfer' => $file,
            'total_price'       => 500, // Attacker tamper attempt
        ];

        $response = $this->post(route('tamu.booking', $this->kamarA->id), $payload);

        $response->assertRedirect();

        $tamu = Tamu::where('email', 'budi@example.com')->first();
        $this->assertNotNull($tamu);
        // Server MUST calculate 2,016,000 (1,800,000 + 12% PPN) and completely ignore the 500 rupiah client payload
        $this->assertEquals(2016000, (float)$tamu->total_price);
        $this->assertEquals('pending', $tamu->status);
    }

    /**
     * 3. Test Proof of Transfer Validation & Private Disk Storage
     */
    public function test_proof_of_transfer_is_required_for_transfer_and_stored_privately(): void
    {
        $startDate = now()->addDays(10)->format('Y-m-d');

        // Attempt transfer without proof_of_transfer
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

        $response = $this->post(route('tamu.booking', $this->kamarA->id), $payloadNoProof);
        $response->assertSessionHasErrors(['proof_of_transfer']);

        // Now submit with valid proof_of_transfer file
        $file = UploadedFile::fake()->image('struk_atm.png');
        $payloadWithProof = array_merge($payloadNoProof, ['proof_of_transfer' => $file]);

        $responseSuccess = $this->post(route('tamu.booking', $this->kamarA->id), $payloadWithProof);
        $responseSuccess->assertRedirect();

        $tamu = Tamu::where('email', 'rina@example.com')->first();
        $this->assertNotNull($tamu);
        $this->assertNotEmpty($tamu->proof_of_transfer);

        // Verify stored in private local disk, NOT in public
        Storage::disk('local')->assertExists($tamu->proof_of_transfer);
        Storage::disk('public')->assertMissing($tamu->proof_of_transfer);
    }

    /**
     * 4. Test Sub-resource IDOR Protection in KamarKosanController
     */
    public function test_branch_admin_cannot_access_or_modify_unauthorized_kosan_subresources(): void
    {
        // Create room in Kosan B (which branch admin does not manage)
        $kamarB = ProductKamarKosan::create([
            'product_kosan_id' => $this->kosanB->id,
            'room'             => 'Kamar 202 Melati',
            'views'            => 0,
        ]);

        $this->actingAs($this->branchAdmin);

        // Attempt to insert facility to Kamar B
        $response = $this->post(route('admin.product.kosan.kamar.fasilitas.insert', [
            'product_kosan'       => $this->kosanB->id,
            'product_kamar_kosan' => $kamarB->id,
        ]), [
            'fasilitasKamar' => [
                ['title' => 'AC Inverter'],
            ],
        ]);

        // Must be rejected with 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * 5. Test Super Admin Authorization on Global Modules
     */
    public function test_branch_admin_cannot_access_super_admin_social_media_and_users(): void
    {
        $this->actingAs($this->branchAdmin);

        // Attempt to access users management
        $responseUsers = $this->get(route('admin.users.index'));
        $responseUsers->assertStatus(403);

        // Attempt to access social media management
        $responseSosmed = $this->get(route('admin.sosial.media.index'));
        $responseSosmed->assertStatus(403);

        // Super Admin CAN access both
        $this->actingAs($this->superAdmin);
        $this->get(route('admin.users.index'))->assertStatus(200);
        $this->get(route('admin.sosial.media.index'))->assertStatus(200);
    }

    /**
     * 6. Test Soft Deletes Preserving Booking Ledger
     */
    public function test_soft_deletes_preserves_tenant_records_when_room_is_deleted(): void
    {
        $this->actingAs($this->superAdmin);

        // Create completed past tenant booking
        $tamu = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Hendra Wijaya',
            'email'                  => 'hendra@example.com',
            'telp'                   => '081233445566',
            'start_date'             => now()->subMonths(2)->format('Y-m-d'),
            'end_date'               => now()->subMonth()->format('Y-m-d'),
            'start_time'             => '12:00',
            'payment_method'         => 'cod',
            'total_price'            => 1000000,
            'status'                 => 'approved',
        ]);

        // Delete Kamar A
        $response = $this->delete(route('admin.product.kosan.kamar.delete', [
            'product_kosan'       => $this->kosanA->id,
            'product_kamar_kosan' => $this->kamarA->id,
        ]));

        $response->assertRedirect();

        // Kamar is soft deleted (deleted_at is set)
        $this->assertSoftDeleted('product_kamar_kosans', ['id' => $this->kamarA->id]);

        // Tamu record STILL EXISTS in database!
        $this->assertDatabaseHas('tamus', ['id' => $tamu->id]);
    }

    /**
     * 7. Test Active Tenant Protects Room from Deletion
     */
    public function test_cannot_delete_room_with_active_occupant(): void
    {
        $this->actingAs($this->superAdmin);

        // Create active occupying tenant
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

        $response = $this->delete(route('admin.product.kosan.kamar.delete', [
            'product_kosan'       => $this->kosanA->id,
            'product_kamar_kosan' => $this->kamarA->id,
        ]));

        $response->assertSessionHas('failed');
        $this->assertNotSoftDeleted('product_kamar_kosans', ['id' => $this->kamarA->id]);
    }

    /**
     * 8. Test Automatic Collision Rejection upon Approval
     */
    public function test_approving_booking_auto_rejects_colliding_pending_bookings(): void
    {
        $this->actingAs($this->superAdmin);

        $start = now()->addDays(2)->format('Y-m-d');
        $end = now()->addDays(32)->format('Y-m-d');

        // Booking 1 (To be approved)
        $booking1 = Tamu::create([
            'product_kamar_kosan_id' => $this->kamarA->id,
            'name'                   => 'Pemohon Utama',
            'email'                  => 'utama@example.com',
            'telp'                   => '08122334455',
            'start_date'             => $start,
            'end_date'               => $end,
            'start_time'             => '10:00',
            'payment_method'         => 'transfer',
            'total_price'            => 1000000,
            'status'                 => 'pending',
        ]);

        // Booking 2 (Colliding dates, pending)
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

        // Approve Booking 1
        $response = $this->put(route('admin.booking.approve', $booking1->id));
        $response->assertSessionHas('success');

        $booking1->refresh();
        $booking2->refresh();

        $this->assertEquals('approved', $booking1->status);
        $this->assertEquals('reject', $booking2->status);
        $this->assertStringContainsString('Kamar telah disetujui untuk pemohon booking lain', $booking2->rejection_reason);
    }

    /**
     * 9. Test Stale Pending Expiration Command
     */
    public function test_expire_pending_command_cleans_stale_bookings(): void
    {
        // Stale booking created 3 hours ago
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

        // Fresh booking created 30 minutes ago
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
        $this->assertStringContainsString('otomatis kedaluwarsa', $staleBooking->rejection_reason);
        $this->assertEquals('pending', $freshBooking->status);
    }

    /**
     * 10. Test Kamar Creation Auto-Creates Annual Rate & Multi-Room Generator
     */
    public function test_kamar_creation_auto_creates_annual_price_and_multi_room_generator(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Single room without price_tahun specified: auto-creates 12x monthly price
        $response = $this->post(route('admin.product.kosan.kamar.insert', $this->kosanA->id), [
            'room'        => 'Kamar Single 1',
            'price_bulan' => 1250000,
            'price_tahun' => '', // Left empty!
        ]);
        $response->assertSessionHas('success');

        $kamar = ProductKamarKosan::where('room', 'Kamar Single 1')->first();
        $this->assertNotNull($kamar);
        $monthly = PriceKamar::where('product_kamar_kosan_id', $kamar->id)->where('kategori', 'bulan')->first();
        $yearly = PriceKamar::where('product_kamar_kosan_id', $kamar->id)->where('kategori', 'tahun')->first();
        $this->assertEquals(1250000, (float)$monthly->price);
        $this->assertEquals(1250000 * 12, (float)$yearly->price);

        // 2. Multi-room generator: creates 3 rooms with prefix "Kamar 20", starting at 1
        $responseGen = $this->post(route('admin.product.kosan.kamar.insert', $this->kosanA->id), [
            'is_multiple_rooms'  => 1,
            'room_prefix'        => 'Unit ',
            'room_start_number'  => 301,
            'room_quantity'      => 3,
            'price_bulan'        => 750000,
        ]);
        $responseGen->assertSessionHas('success');

        $this->assertDatabaseHas('product_kamar_kosans', ['room' => 'Unit 301']);
        $this->assertDatabaseHas('product_kamar_kosans', ['room' => 'Unit 302']);
        $this->assertDatabaseHas('product_kamar_kosans', ['room' => 'Unit 303']);
    }

    /**
     * 11. Test Artikel View Count Can Be Set and Updated
     */
    public function test_artikel_view_can_be_set_and_updated(): void
    {
        $this->actingAs($this->superAdmin);

        $artikel = \App\Models\Artikel::create([
            'title'     => 'Tips Memilih Kosan Murah',
            'slug'      => 'tips-memilih-kosan-murah',
            'deskripsi' => '<p>Konten artikel lengkap dan mendalam...</p>',
            'view'      => 150,
        ]);

        $this->assertEquals(150, $artikel->view);

        // Update view count
        $response = $this->put(route('admin.artikel.update', $artikel->id), [
            'title'     => 'Tips Memilih Kosan Murah Update',
            'deskripsi' => '<p>Konten artikel baru yang telah diperbarui...</p>',
            'view'      => 325,
        ]);
        $response->assertSessionHas('success');

        $artikel->refresh();
        $this->assertEquals(325, $artikel->view);
    }

    /**
     * 12. Test Kosan Wilayah is Preserved on Update
     */
    public function test_kosan_wilayah_is_preserved_on_update(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->put(route('admin.product.kosan.update', $this->kosanA->id), [
            'title'   => 'Kos Sakura Indah Updated',
            'wilayah' => 'Badung',
        ]);
        $response->assertSessionHas('success');

        $this->kosanA->refresh();
        $this->assertEquals('Badung', $this->kosanA->wilayah);
    }

    /**
     * 13. Test Dependent Wilayah Indonesia API (Provinsi -> Kabupaten/Kota -> Kecamatan)
     */
    public function test_wilayah_indonesia_dependent_dropdown_api_returns_full_hierarchy(): void
    {
        // 1. Provinces (38 provinces in Indonesia)
        $resProv = $this->getJson(route('api.wilayah.provinces'));
        $resProv->assertStatus(200);
        $provinces = $resProv->json();
        $this->assertGreaterThanOrEqual(38, count($provinces));
        $this->assertTrue(collect($provinces)->contains(fn($p) => $p['id'] === '51' && $p['name'] === 'Bali'));

        // 2. Regencies for Bali (51)
        $resReg = $this->getJson(route('api.wilayah.regencies', '51'));
        $resReg->assertStatus(200);
        $regencies = $resReg->json();
        $this->assertCount(9, $regencies);
        $this->assertTrue(collect($regencies)->contains(fn($r) => $r['id'] === '51.03' && $r['name'] === 'Kabupaten Badung'));

        // 3. Districts for Kabupaten Badung (51.03)
        $resDist = $this->getJson(route('api.wilayah.districts', '51.03'));
        $resDist->assertStatus(200);
        $districts = $resDist->json();
        $this->assertNotEmpty($districts);
        $this->assertTrue(collect($districts)->contains(fn($d) => $d['id'] === '51.03.05' && $d['name'] === 'Kuta Selatan'));
    }

    /**
     * 14. Test Kosan Create, Update, Display & Filter with Dependent Wilayah & Video (Link & Upload)
     */
    public function test_kosan_dependent_wilayah_and_video_link_upload_lifecycle(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Create Kosan with Provinsi -> Kabupaten -> Kecamatan AND YouTube link
        $response = $this->post(route('admin.product.kosan.insert'), [
            'title'        => 'Kos Jimbaran Hills',
            'provinsi_id'  => '51',
            'kabupaten_id' => '51.03',
            'kecamatan_id' => '51.03.05',
            'tersedia'     => 4,
            'video_mode'   => 'link',
            'video_url'    => 'https://youtu.be/dQw4w9WgXcQ',
        ]);
        $response->assertSessionHas('success');

        $kosan = ProductKosan::where('title', 'Kos Jimbaran Hills')->first();
        $this->assertNotNull($kosan);
        $this->assertEquals('51', $kosan->provinsi_id);
        $this->assertEquals('Bali', $kosan->provinsi);
        $this->assertEquals('51.03', $kosan->kabupaten_id);
        $this->assertEquals('Kabupaten Badung', $kosan->kabupaten);
        $this->assertEquals('51.03.05', $kosan->kecamatan_id);
        $this->assertEquals('Kuta Selatan', $kosan->kecamatan);
        $this->assertEquals('Kec. Kuta Selatan, Kabupaten Badung, Bali', $kosan->wilayah);
        $this->assertTrue($kosan->has_video);
        $this->assertEquals('https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0', $kosan->video_embed['src']);

        // Verify frontend detail page displays both structured location and YouTube embed
        $detailRes = $this->get(route('kosan.detail', $kosan->slug));
        $detailRes->assertStatus(200);
        $detailRes->assertSee('Kec. Kuta Selatan, Kabupaten Badung, Bali');
        $detailRes->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0');

        // Verify frontend kosan index dependent filter works
        $filterRes = $this->get(route('kosan.index', [
            'provinsi_id'  => '51',
            'kabupaten_id' => '51.03',
            'kecamatan_id' => '51.03.05',
        ]));
        $filterRes->assertStatus(200);
        $filterRes->assertSee('Kos Jimbaran Hills');
        $filterRes->assertDontSee('Kos Sakura Indah');

        // 2. Update Kosan to uploaded MP4 video file
        $videoFile = UploadedFile::fake()->create('tour_kosan.mp4', 512, 'video/mp4');
        $updateRes = $this->put(route('admin.product.kosan.update', $kosan->id), [
            'title'        => 'Kos Jimbaran Hills',
            'provinsi_id'  => '51',
            'kabupaten_id' => '51.03',
            'kecamatan_id' => '51.03.05',
            'video_mode'   => 'upload',
            'video_file'   => $videoFile,
        ]);
        $updateRes->assertSessionHas('success');

        $kosan->refresh();
        $this->assertNotNull($kosan->video_path);
        $this->assertNull($kosan->video_url);
        Storage::disk('public')->assertExists($kosan->video_path);
        $uploadedPath = $kosan->video_path;

        // Verify HTML5 video player renders on frontend
        $detailRes2 = $this->get(route('kosan.detail', $kosan->slug));
        $detailRes2->assertStatus(200);
        $detailRes2->assertSee('<video', false);
        $detailRes2->assertSee($uploadedPath);

        // 3. Unrelated edit preserves existing uploaded video
        $preserveRes = $this->put(route('admin.product.kosan.update', $kosan->id), [
            'title'      => 'Kos Jimbaran Hills Executive',
            'video_mode' => 'upload',
        ]);
        $preserveRes->assertSessionHas('success');
        $kosan->refresh();
        $this->assertEquals($uploadedPath, $kosan->video_path);

        // 4. Removing video deletes file and clears columns
        $removeRes = $this->put(route('admin.product.kosan.update', $kosan->id), [
            'title'        => 'Kos Jimbaran Hills Executive',
            'remove_video' => 1,
        ]);
        $removeRes->assertSessionHas('success');
        $kosan->refresh();
        $this->assertFalse($kosan->has_video);
        Storage::disk('public')->assertMissing($uploadedPath);
    }

    /**
     * 15. Test Kamar Video Upload, Link, Multi-Room Shared Video Safety & Frontend Rendering
     */
    public function test_kamar_video_upload_link_multi_room_and_frontend_display(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Multi-room creation with uploaded video file
        $videoFile = UploadedFile::fake()->create('kamar_tour.mp4', 512, 'video/mp4');
        $resMulti = $this->post(route('admin.product.kosan.kamar.insert', $this->kosanA->id), [
            'is_multiple_rooms' => 1,
            'room_prefix'       => 'Deluxe',
            'room_start_number' => 501,
            'room_quantity'     => 2,
            'price_bulan'       => 1800000,
            'video_mode'        => 'upload',
            'video_file'        => $videoFile,
        ]);
        $resMulti->assertSessionHas('success');

        $kamar501 = ProductKamarKosan::where('room', 'Deluxe 501')->first();
        $kamar502 = ProductKamarKosan::where('room', 'Deluxe 502')->first();
        $this->assertNotNull($kamar501);
        $this->assertNotNull($kamar502);
        $this->assertNotEmpty($kamar501->video_path);
        $this->assertEquals($kamar501->video_path, $kamar502->video_path);
        Storage::disk('public')->assertExists($kamar501->video_path);
        $sharedVideoPath = $kamar501->video_path;

        // Verify Kamar detail page renders video player
        $detailKamar = $this->get(route('kamar.detail', $kamar501->id));
        $detailKamar->assertStatus(200);
        $detailKamar->assertSee('Video Deluxe 501');
        $detailKamar->assertSee('<video', false);

        // 2. Switching Kamar 501 to YouTube Shorts link MUST NOT delete shared video file still used by Kamar 502
        $resUpdate501 = $this->put(route('admin.product.kosan.kamar.update', [$this->kosanA->id, $kamar501->id]), [
            'room'        => 'Deluxe 501',
            'price_bulan' => 1800000,
            'video_mode'  => 'link',
            'video_url'   => 'https://www.youtube.com/shorts/abcDEF12345',
        ]);
        $resUpdate501->assertSessionHas('success');

        $kamar501->refresh();
        $this->assertNull($kamar501->video_path);
        $this->assertEquals('https://www.youtube.com/embed/abcDEF12345?rel=0', $kamar501->video_embed['src']);
        $this->assertTrue($kamar501->video_embed['is_vertical']);
        // Shared file MUST still exist for Kamar 502!
        Storage::disk('public')->assertExists($sharedVideoPath);

        // Verify Kamar detail page renders YouTube Shorts embed
        $detailKamar2 = $this->get(route('kamar.detail', $kamar501->id));
        $detailKamar2->assertStatus(200);
        $detailKamar2->assertSee('https://www.youtube.com/embed/abcDEF12345?rel=0');

        // 3. Reject javascript: XSS video URL
        $this->assertNull(\App\Models\ProductKosan::parseVideoMedia(null, 'javascript:alert(1)'));
        $resXss = $this->put(route('admin.product.kosan.kamar.update', [$this->kosanA->id, $kamar501->id]), [
            'room'        => 'Deluxe 501',
            'price_bulan' => 1800000,
            'video_mode'  => 'link',
            'video_url'   => 'javascript:alert(1)',
        ]);
        $resXss->assertSessionHasErrors(['video_url']);
    }
}
