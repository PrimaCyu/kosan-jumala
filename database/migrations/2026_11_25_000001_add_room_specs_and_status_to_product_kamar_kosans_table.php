<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_kamar_kosans', function (Blueprint $table) {
            if (!Schema::hasColumn('product_kamar_kosans', 'tipe_kamar')) {
                $table->string('tipe_kamar', 100)->nullable()->after('room');
            }
            if (!Schema::hasColumn('product_kamar_kosans', 'luas_kamar')) {
                $table->string('luas_kamar', 60)->nullable()->after('tipe_kamar');
            }
            if (!Schema::hasColumn('product_kamar_kosans', 'lantai')) {
                $table->string('lantai', 50)->nullable()->after('luas_kamar');
            }
            if (!Schema::hasColumn('product_kamar_kosans', 'catatan')) {
                $table->text('catatan')->nullable()->after('description');
            }
            if (!Schema::hasColumn('product_kamar_kosans', 'status_ketersediaan')) {
                $table->string('status_ketersediaan', 30)->default('tersedia')->index()->after('catatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_kamar_kosans', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('product_kamar_kosans', 'tipe_kamar') ? 'tipe_kamar' : null,
                Schema::hasColumn('product_kamar_kosans', 'luas_kamar') ? 'luas_kamar' : null,
                Schema::hasColumn('product_kamar_kosans', 'lantai') ? 'lantai' : null,
                Schema::hasColumn('product_kamar_kosans', 'catatan') ? 'catatan' : null,
                Schema::hasColumn('product_kamar_kosans', 'status_ketersediaan') ? 'status_ketersediaan' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
