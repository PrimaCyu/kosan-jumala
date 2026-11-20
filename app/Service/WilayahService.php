<?php

namespace App\Service;

class WilayahService
{
    private static ?array $provincesCache = null;
    private static ?array $regenciesCache = null;
    private static ?array $districtsCache = null;

    private static function dataPath(string $filename): string
    {
        return database_path('data/wilayah/' . $filename);
    }

    /**
     * Ambil seluruh daftar 38 provinsi di Indonesia.
     */
    public static function getProvinces(): array
    {
        if (self::$provincesCache === null) {
            $path = self::dataPath('provinces.json');
            self::$provincesCache = file_exists($path)
                ? (json_decode(file_get_contents($path), true) ?: [])
                : [];
        }

        return self::$provincesCache;
    }

    /**
     * Ambil semua data kabupaten/kota yang dikelompokkan berdasarkan ID provinsi.
     */
    private static function allRegenciesGrouped(): array
    {
        if (self::$regenciesCache === null) {
            $path = self::dataPath('regencies.json');
            self::$regenciesCache = file_exists($path)
                ? (json_decode(file_get_contents($path), true) ?: [])
                : [];
        }

        return self::$regenciesCache;
    }

    /**
     * Ambil semua data kecamatan yang dikelompokkan berdasarkan ID kabupaten/kota.
     */
    private static function allDistrictsGrouped(): array
    {
        if (self::$districtsCache === null) {
            $path = self::dataPath('districts.json');
            self::$districtsCache = file_exists($path)
                ? (json_decode(file_get_contents($path), true) ?: [])
                : [];
        }

        return self::$districtsCache;
    }

    /**
     * Ambil daftar Kabupaten/Kota berdasarkan ID Provinsi.
     */
    public static function getRegencies(string $provinceId): array
    {
        $grouped = self::allRegenciesGrouped();
        return $grouped[$provinceId] ?? [];
    }

    /**
     * Ambil daftar Kecamatan berdasarkan ID Kabupaten/Kota.
     */
    public static function getDistricts(string $regencyId): array
    {
        $grouped = self::allDistrictsGrouped();
        return $grouped[$regencyId] ?? [];
    }

    public static function findProvince(?string $provinceId): ?array
    {
        if (!$provinceId) {
            return null;
        }
        foreach (self::getProvinces() as $prov) {
            if ($prov['id'] === (string) $provinceId) {
                return $prov;
            }
        }
        return null;
    }

    public static function findRegency(?string $regencyId): ?array
    {
        if (!$regencyId) {
            return null;
        }
        $provId = substr((string) $regencyId, 0, 2);
        foreach (self::getRegencies($provId) as $reg) {
            if ($reg['id'] === (string) $regencyId) {
                return $reg;
            }
        }
        return null;
    }

    public static function findDistrict(?string $districtId): ?array
    {
        if (!$districtId) {
            return null;
        }
        $regId = substr((string) $districtId, 0, 5);
        foreach (self::getDistricts($regId) as $dist) {
            if ($dist['id'] === (string) $districtId) {
                return $dist;
            }
        }
        return null;
    }

    /**
     * Menyusun string tampilan wilayah lengkap yang rapi untuk disimpan di kolom `wilayah`.
     */
    public static function composeWilayahLabel(?string $kecamatan, ?string $kabupaten, ?string $provinsi): string
    {
        $parts = [];
        if (!empty(trim((string) $kecamatan))) {
            $kec = trim((string) $kecamatan);
            $parts[] = str_starts_with(strtolower($kec), 'kec.') ? $kec : ('Kec. ' . $kec);
        }
        if (!empty(trim((string) $kabupaten))) {
            $parts[] = trim((string) $kabupaten);
        }
        if (!empty(trim((string) $provinsi))) {
            $parts[] = trim((string) $provinsi);
        }

        return implode(', ', $parts);
    }

    /**
     * Mencocokkan string wilayah lama (misal "Denpasar" atau "Badung") ke kode Kemendagri agar modal Edit otomatis terisi.
     */
    public static function guessFromLegacyWilayah(?string $wilayah): array
    {
        $result = [
            'provinsi_id'  => null,
            'provinsi'     => null,
            'kabupaten_id' => null,
            'kabupaten'    => null,
            'kecamatan_id' => null,
            'kecamatan'    => null,
        ];

        $needle = strtolower(trim((string) $wilayah));
        if ($needle === '') {
            return $result;
        }

        // Cek kecocokan terhadap nama Kabupaten/Kota
        foreach (self::allRegenciesGrouped() as $provId => $regList) {
            foreach ($regList as $reg) {
                $regNameLower = strtolower($reg['name']);
                $regShort = trim(preg_replace('/^(kabupaten|kota)\s+/i', '', $regNameLower));
                if ($needle === $regNameLower || $needle === $regShort || str_contains($needle, $regShort)) {
                    $prov = self::findProvince($provId);
                    $result['provinsi_id']  = $provId;
                    $result['provinsi']     = $prov['name'] ?? null;
                    $result['kabupaten_id'] = $reg['id'];
                    $result['kabupaten']    = $reg['name'];
                    return $result;
                }
            }
        }

        // Fallback cek kecocokan terhadap nama Provinsi
        foreach (self::getProvinces() as $prov) {
            if ($needle === strtolower($prov['name']) || str_contains($needle, strtolower($prov['name']))) {
                $result['provinsi_id'] = $prov['id'];
                $result['provinsi']    = $prov['name'];
                return $result;
            }
        }

        return $result;
    }
}
