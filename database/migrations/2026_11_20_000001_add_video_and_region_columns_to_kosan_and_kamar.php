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
        Schema::table('product_kosans', function (Blueprint $table) {
            if (!Schema::hasColumn('product_kosans', 'provinsi_id')) {
                $table->string('provinsi_id', 15)->nullable()->after('wilayah');
            }
            if (!Schema::hasColumn('product_kosans', 'provinsi')) {
                $table->string('provinsi', 120)->nullable()->after('provinsi_id');
            }
            if (!Schema::hasColumn('product_kosans', 'kabupaten_id')) {
                $table->string('kabupaten_id', 15)->nullable()->after('provinsi');
            }
            if (!Schema::hasColumn('product_kosans', 'kabupaten')) {
                $table->string('kabupaten', 120)->nullable()->after('kabupaten_id');
            }
            if (!Schema::hasColumn('product_kosans', 'kecamatan_id')) {
                $table->string('kecamatan_id', 20)->nullable()->after('kabupaten');
            }
            if (!Schema::hasColumn('product_kosans', 'kecamatan')) {
                $table->string('kecamatan', 120)->nullable()->after('kecamatan_id');
            }
            if (!Schema::hasColumn('product_kosans', 'video_path')) {
                $table->string('video_path')->nullable()->after('gmaps');
            }
            if (!Schema::hasColumn('product_kosans', 'video_url')) {
                $table->text('video_url')->nullable()->after('video_path');
            }
        });

        Schema::table('product_kamar_kosans', function (Blueprint $table) {
            if (!Schema::hasColumn('product_kamar_kosans', 'video_path')) {
                $table->string('video_path')->nullable()->after('gmaps');
            }
            if (!Schema::hasColumn('product_kamar_kosans', 'video_url')) {
                $table->text('video_url')->nullable()->after('video_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_kosans', function (Blueprint $table) {
            $cols = array_filter(
                ['provinsi_id', 'provinsi', 'kabupaten_id', 'kabupaten', 'kecamatan_id', 'kecamatan', 'video_path', 'video_url'],
                fn($col) => Schema::hasColumn('product_kosans', $col)
            );
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('product_kamar_kosans', function (Blueprint $table) {
            $cols = array_filter(
                ['video_path', 'video_url'],
                fn($col) => Schema::hasColumn('product_kamar_kosans', $col)
            );
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
