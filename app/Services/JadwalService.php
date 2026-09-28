<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Layanan untuk mengelola jadwal pelajaran dan penyesuaian jadwal maju
 * khusus hari Senin (tanpa upacara) dan hari Jumat (tanpa pembiasaan).
 */
class JadwalService
{
    /**
     * Memeriksa apakah saklar kemajuan jadwal aktif untuk hari tertentu.
     */
    public static function isJadwalMaju(string $hari): bool
    {
        if ($hari === 'Senin') {
            return (string) Pengaturan::get('jadwal_maju_senin', '0') === '1';
        }
        if ($hari === 'Jumat') {
            return (string) Pengaturan::get('jadwal_maju_jumat', '0') === '1';
        }

        return false;
    }

    /**
     * Mengatur status saklar kemajuan jadwal untuk hari tertentu.
     */
    public static function setJadwalMaju(string $hari, bool $status): void
    {
        if ($hari === 'Senin') {
            Pengaturan::set('jadwal_maju_senin', $status ? '1' : '0');
        } elseif ($hari === 'Jumat') {
            Pengaturan::set('jadwal_maju_jumat', $status ? '1' : '0');
        }
    }

    /**
     * Menerapkan kemajuan jadwal mengajar jika saklar hari tersebut aktif.
     *
     * Logika saat aktif:
     * - Senin: Jam upacara diambil oleh mapel setelah upacara (jadwal mapel bergeser maju 1 jam).
     * - Jumat: Jam pembiasaan diambil oleh mapel setelah pembiasaan (jadwal mapel bergeser maju 1 jam).
     *
     * @param  Collection  $jadwals  Daftar objek jadwal mengajar (memiliki id_jam, jam_ke, jam_mulai, jam_selesai, nama_mapel, dll.)
     * @param  string  $hari  Nama hari ('Senin', 'Jumat', dsb.)
     * @return Collection
     */
    public static function applyJadwalMaju(Collection $jadwals, string $hari): Collection
    {
        if ($jadwals->isEmpty() || ! self::isJadwalMaju($hari) || ! in_array($hari, ['Senin', 'Jumat'], true)) {
            return $jadwals;
        }

        $jamSlots = DB::table('jam_pelajaran')
            ->where('hari', $hari)
            ->whereNull('deleted_at')
            ->orderBy('jam_ke')
            ->get();

        if ($jamSlots->isEmpty()) {
            return $jadwals;
        }

        $slotMapByJamKe = [];
        $slotMapById = [];
        foreach ($jamSlots as $i => $slot) {
            if ($i > 0) {
                $prevSlot = $jamSlots[$i - 1];
                $slotMapByJamKe[$slot->jam_ke] = $prevSlot;
                $slotMapById[$slot->id_jam] = $prevSlot;
            }
        }

        $result = collect();

        foreach ($jadwals as $item) {
            $namaMapel = strtolower($item->nama_mapel ?? '');
            $kodeMapel = strtolower($item->kode_mapel ?? '');

            // Abaikan upacara pada hari Senin dan pembiasaan pada hari Jumat
            if ($hari === 'Senin' && (str_contains($namaMapel, 'upacara') || str_contains($namaMapel, 'apel') || str_contains($kodeMapel, 'upacara'))) {
                continue;
            }
            if ($hari === 'Jumat' && (str_contains($namaMapel, 'pembiasaan') || str_contains($kodeMapel, 'pembiasaan'))) {
                continue;
            }

            $c = is_object($item) ? clone $item : (object) $item;

            // Cek pergeseran berdasarkan id_jam atau jam_ke
            $newSlot = null;
            if (isset($c->id_jam) && isset($slotMapById[$c->id_jam])) {
                $newSlot = $slotMapById[$c->id_jam];
            } elseif (isset($c->jam_ke) && isset($slotMapByJamKe[$c->jam_ke])) {
                $newSlot = $slotMapByJamKe[$c->jam_ke];
            }

            if ($newSlot) {
                $c->id_jam = $newSlot->id_jam;
                $c->jam_ke = $newSlot->jam_ke;
                $c->jam_mulai = $newSlot->jam_mulai;
                $c->jam_selesai = $newSlot->jam_selesai;
            }

            $result->push($c);
        }

        return $result->sortBy('jam_ke')->values();
    }
}
