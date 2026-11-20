<?php

namespace App\Http\Controllers;

use App\Service\WilayahService;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    /**
     * Daftar 38 Provinsi Indonesia (Kemendagri).
     */
    public function provinces(): JsonResponse
    {
        return response()->json(WilayahService::getProvinces());
    }

    /**
     * Daftar Kabupaten/Kota berdasarkan ID Provinsi.
     */
    public function regencies(string $provinceId): JsonResponse
    {
        $cleanId = preg_replace('/[^0-9]/', '', $provinceId);
        return response()->json(WilayahService::getRegencies($cleanId));
    }

    /**
     * Daftar Kecamatan berdasarkan ID Kabupaten/Kota.
     */
    public function districts(string $regencyId): JsonResponse
    {
        $cleanId = preg_replace('/[^0-9.]/', '', $regencyId);
        return response()->json(WilayahService::getDistricts($cleanId));
    }
}
