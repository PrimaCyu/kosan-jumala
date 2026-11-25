<?php

namespace Tests\Feature;

use App\Models\PriceKamar;
use App\Models\ProductKamarKosan;
use App\Models\ProductKosan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_dashboard_pages_render_cleanly_for_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $kosan = ProductKosan::jumala();

        $kamar = ProductKamarKosan::create([
            'product_kosan_id'    => $kosan->id,
            'room'                => 'Kamar 101',
            'tipe_kamar'          => 'Deluxe',
            'luas_kamar'          => '3x4 m',
            'lantai'              => 'Lantai 1',
            'status_ketersediaan' => 'tersedia',
            'description'         => 'Kamar AC kamar mandi dalam',
            'fasilitas'           => 'AC, WiFi',
        ]);

        PriceKamar::create([
            'product_kamar_kosan_id' => $kamar->id,
            'kategori'               => 'bulan',
            'price'                  => 1500000,
            'discount'               => 0,
        ]);

        $routes = [
            route('admin.dashboard'),
            route('admin.kamar.index'),
            route('admin.kamar.create'),
            route('admin.kamar.show', $kamar->id),
            route('admin.kamar.edit', $kamar->id),
            route('admin.properti.edit'),
            route('admin.booking.index'),
            route('admin.testimoni.index'),
            route('admin.users.index'),
            route('admin.payment-methods.index'),
            route('admin.sosial.media.index'),
            route('admin.profile.edit'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($superAdmin)->get($url);
            $response->assertStatus(200);
        }
    }

    public function test_operational_admin_dashboard_and_role_permissions(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $kosan = ProductKosan::jumala();
        $kamar = ProductKamarKosan::create([
            'product_kosan_id'    => $kosan->id,
            'room'                => 'Kamar 102',
            'status_ketersediaan' => 'tersedia',
        ]);

        $allowedRoutes = [
            route('admin.dashboard'),
            route('admin.kamar.index'),
            route('admin.kamar.create'),
            route('admin.kamar.show', $kamar->id),
            route('admin.kamar.edit', $kamar->id),
            route('admin.properti.edit'),
            route('admin.booking.index'),
            route('admin.testimoni.index'),
            route('admin.profile.edit'),
        ];

        foreach ($allowedRoutes as $url) {
            $response = $this->actingAs($admin)->get($url);
            $response->assertStatus(200);
        }

        // Super admin only modules remain protected
        $this->actingAs($admin)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('admin.payment-methods.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('admin.sosial.media.index'))->assertStatus(403);
    }
}
