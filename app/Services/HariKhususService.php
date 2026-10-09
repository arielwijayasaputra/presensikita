<?php

namespace App\Services;

use App\Models\Hari;
use App\Models\HariKhusus;
use App\Models\Kelas;
use App\Models\Tingkat;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Layanan terpusat untuk mengelola dan mengevaluasi aturan Hari Khusus (Event & Pulang Cepat).
 */
class HariKhususService
{
    /**
     * Cache hasil query hari khusus per request agar performa tetap cepat.
     * @var array
     */
    protected static array $cacheHariKhusus = [];

    /**
     * Mengubah format tingkat (Romawi atau angka string) menjadi integer standar (10, 11, 12).
     */
    public static function normalizeTingkat(mixed $tingkat): ?int
    {
        if (empty($tingkat)) {
            return null;
        }

        $val = trim((string) $tingkat);
        if (is_numeric($val)) {
            return (int) $val;
        }

        $upper = strtoupper($val);
        if ($upper === 'X' || str_starts_with($upper, '10') || str_starts_with($upper, 'X ') || str_starts_with($upper, 'X-')) {
            return 10;
        }
        if ($upper === 'XI' || str_starts_with($upper, '11') || str_starts_with($upper, 'XI ') || str_starts_with($upper, 'XI-')) {
            return 11;
        }
        if ($upper === 'XII' || str_starts_with($upper, '12') || str_starts_with($upper, 'XII ') || str_starts_with($upper, 'XII-')) {
            return 12;
        }

        $angka = Tingkat::getAngka($upper);
        return $angka ?: null;
    }

    /**
     * Mengambil entri HariKhusus yang aktif pada tanggal dan tingkat tertentu.
     *
     * @param string|Carbon $tanggal
     * @param int|string|null $tingkat (10, 11, 12, 'X', dsb.)
     * @return HariKhusus|null
     */
    public static function getHariKhusus(string|Carbon $tanggal, mixed $tingkat = null): ?HariKhusus
    {
        $dateStr = $tanggal instanceof Carbon ? $tanggal->toDateString() : substr((string) $tanggal, 0, 10);
        $normTingkat = self::normalizeTingkat($tingkat);

        $cacheKey = $dateStr . '_' . ($normTingkat ?? 'all');
        if (array_key_exists($cacheKey, self::$cacheHariKhusus)) {
            return self::$cacheHariKhusus[$cacheKey];
        }

        $records = HariKhusus::whereDate('tanggal_mulai', '<=', $dateStr)
            ->whereDate('tanggal_selesai', '>=', $dateStr)
            ->whereNull('deleted_at')
            ->get();

        if ($records->isEmpty()) {
            return self::$cacheHariKhusus[$cacheKey] = null;
        }

        if ($normTingkat === null) {
            return self::$cacheHariKhusus[$cacheKey] = $records->first();
        }

        foreach ($records as $item) {
            $tingkatArray = array_map('intval', $item->tingkat ?: []);
            if (in_array($normTingkat, $tingkatArray, true)) {
                return self::$cacheHariKhusus[$cacheKey] = $item;
            }
        }

        return self::$cacheHariKhusus[$cacheKey] = null;
    }

    /**
     * Mengambil entri HariKhusus yang aktif untuk suatu kelas pada tanggal tertentu.
     */
    public static function getHariKhususForKelas(string|Carbon $tanggal, int|Kelas $kelas): ?HariKhusus
    {
        if (is_numeric($kelas)) {
            $kelasObj = Kelas::find($kelas);
        } else {
            $kelasObj = $kelas;
        }

        if (! $kelasObj) {
            return self::getHariKhusus($tanggal, null);
        }

        $tingkat = self::normalizeTingkat($kelasObj->tingkat_kelas ?: $kelasObj->nama_kelas);
        return self::getHariKhusus($tanggal, $tingkat);
    }

    /**
     * Mengambil semua entri HariKhusus yang berlaku pada tanggal tertentu.
     */
    public static function getHariKhususListForDate(string|Carbon $tanggal): Collection
    {
        $dateStr = $tanggal instanceof Carbon ? $tanggal->toDateString() : substr((string) $tanggal, 0, 10);

        return HariKhusus::whereDate('tanggal_mulai', '<=', $dateStr)
            ->whereDate('tanggal_selesai', '>=', $dateStr)
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Mengambil semua entri HariKhusus yang relevan bagi guru pada hari tersebut
     * berdasarkan kelas yang diajar pada jadwal hari tersebut.
     */
    public static function getHariKhususListForGuru(string|Carbon $tanggal, int $guruId): Collection
    {
        $allEventsToday = self::getHariKhususListForDate($tanggal);
        if ($allEventsToday->isEmpty()) {
            return collect();
        }

        $carbonDate = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);
        $hariIndo = Hari::getNamaHariFromDayOfWeek($carbonDate->dayOfWeekIso);

        $tingkatsGuru = DB::table('jadwal_mengajar')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->where('jadwal_mengajar.id_guru', $guruId)
            ->where('jadwal_mengajar.hari', $hariIndo)
            ->whereNull('jadwal_mengajar.deleted_at')
            ->whereNull('kelas.deleted_at')
            ->select('kelas.tingkat_kelas', 'kelas.nama_kelas')
            ->get()
            ->map(fn ($k) => self::normalizeTingkat($k->tingkat_kelas ?: $k->nama_kelas))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Jika guru tidak punya jadwal mengajar spesifik hari ini, tampilkan semua event hari ini
        if (empty($tingkatsGuru)) {
            return $allEventsToday;
        }

        return $allEventsToday->filter(function ($event) use ($tingkatsGuru) {
            $eventTingkats = array_map('intval', $event->tingkat ?: []);
            return count(array_intersect($tingkatsGuru, $eventTingkats)) > 0;
        })->values();
    }

    /**
     * Memeriksa apakah suatu sesi jam pelajaran ditiadakan karena Hari Khusus (Pulang Cepat atau Event Diliburkan).
     *
     * @param string|Carbon $tanggal
     * @param mixed $tingkat
     * @param string $jamMulai ('07:00' atau '07:00:00')
     * @return bool
     */
    public static function isJamPelajaranDitiadakan(string|Carbon $tanggal, mixed $tingkat, string $jamMulai): bool
    {
        $hariKhusus = self::getHariKhusus($tanggal, $tingkat);
        if (! $hariKhusus) {
            return false;
        }

        if ($hariKhusus->tipe === 'event') {
            return $hariKhusus->aturan_presensi === 'diliburkan';
        }

        if ($hariKhusus->tipe === 'pulang_cepat' && ! empty($hariKhusus->jam_pulang)) {
            $formatJamMulai = substr($jamMulai, 0, 5) . ':00';
            $formatJamPulang = substr($hariKhusus->jam_pulang, 0, 5) . ':00';
            return $formatJamMulai >= $formatJamPulang;
        }

        return false;
    }

    /**
     * Memeriksa apakah tanggal tersebut berstatus libur total untuk tingkat tertentu.
     */
    public static function isTanggalDiliburkan(string|Carbon $tanggal, mixed $tingkat): bool
    {
        $hariKhusus = self::getHariKhusus($tanggal, $tingkat);
        return $hariKhusus && $hariKhusus->tipe === 'event' && $hariKhusus->aturan_presensi === 'diliburkan';
    }

    /**
     * Memeriksa apakah tanggal tersebut berstatus Hadir Otomatis Event untuk tingkat tertentu.
     */
    public static function isHadirEvent(string|Carbon $tanggal, mixed $tingkat): bool
    {
        $hariKhusus = self::getHariKhusus($tanggal, $tingkat);
        return $hariKhusus && $hariKhusus->tipe === 'event' && $hariKhusus->aturan_presensi === 'hadir_event';
    }

    /**
     * Validasi bentrok jadwal hari khusus (irisan tanggal DAN irisan tingkat).
     *
     * @param string $tanggalMulai
     * @param string $tanggalSelesai
     * @param array $tingkatList (contoh: [10, 11])
     * @param int|null $ignoreId (untuk update)
     * @return string|null Pesan error jika bentrok, atau null jika valid.
     */
    public static function checkConflict(string $tanggalMulai, string $tanggalSelesai, array $tingkatList, ?int $ignoreId = null): ?string
    {
        if ($tanggalSelesai < $tanggalMulai) {
            return 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        }

        if (empty($tingkatList)) {
            return 'Pilih minimal satu tingkat kelas (10, 11, atau 12).';
        }

        $tingkatListInt = array_map('intval', $tingkatList);

        // Cari record yang beririsan tanggal
        $overlaps = HariKhusus::whereNull('deleted_at')
            ->whereDate('tanggal_mulai', '<=', $tanggalSelesai)
            ->whereDate('tanggal_selesai', '>=', $tanggalMulai)
            ->when($ignoreId, fn ($q) => $q->where('id_hari_khusus', '!=', $ignoreId))
            ->get();

        foreach ($overlaps as $item) {
            $itemTingkatInt = array_map('intval', $item->tingkat ?: []);
            $intersect = array_intersect($tingkatListInt, $itemTingkatInt);
            if (! empty($intersect)) {
                $bentrokTingkat = 'Kelas ' . implode(', ', $intersect);
                $tipeLabel = $item->tipe === 'pulang_cepat' ? 'Pulang Cepat' : 'Event';
                return "Jadwal bentrok dengan entri '{$item->judul}' ({$tipeLabel}) untuk {$bentrokTingkat} pada periode {$item->tanggal_mulai->format('d/m/Y')} s/d {$item->tanggal_selesai->format('d/m/Y')}.";
            }
        }

        return null;
    }

    /**
     * Membersihkan cache memory.
     */
    public static function clearCache(): void
    {
        self::$cacheHariKhusus = [];
    }
}
