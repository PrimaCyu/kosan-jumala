<?php

namespace Database\Seeders;

use App\Models\ProductKosan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin Jumala Living',
                'password' => 'password',
                'role'     => 'super_admin',
            ]
        );

        ProductKosan::jumala();

        $this->call(PaymentMethodSeeder::class);
    }
}
