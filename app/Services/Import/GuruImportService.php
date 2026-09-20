<?php

namespace App\Services\Import;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruImportService
{
    public function import(UploadedFile $file): JsonResponse
    {
        try {
            $rows = $this->readSpreadsheetRows($file->getRealPath());
        } catch (\Throwable $exception) {
            return response()->json(['status' => 'error', 'message' => 'File tidak dapat dibaca: '.$exception->getMessage()], 422);
        }

        $imported = 0;
        $updated = 0;
        $skipped = [];
        foreach ($rows as $line => $row) {
            $name = trim((string) ($row['nama_guru'] ?? ''));
            $nip = preg_replace('/\D+/', '', (string) ($row['nip'] ?? '')) ?: null;
            if ($name === '') {
                $skipped[] = 'Baris '.($line + 1).': nama guru kosong.';

                continue;
            }
            $requestedUsername = strtolower(trim((string) ($row['username'] ?? '')));
            $kodeMapel = trim((string) ($row['kode_mapel'] ?? ''));
            $mapel = $kodeMapel !== '' ? Mapel::where('kode_mapel', $kodeMapel)->first() : null;
            if ($kodeMapel !== '' && ! $mapel) {
                $skipped[] = 'Baris '.($line + 1).': kode mapel "'.$kodeMapel.'" tidak terdaftar.';

                continue;
            }
            $idMapel = $mapel?->id_mapel;
            $guru = $nip ? Guru::where('nip', $nip)->first() : null;
            $guru ??= $requestedUsername !== '' ? Guru::whereRaw('LOWER(username) = ?', [$requestedUsername])->first() : null;
            $guru ??= Guru::whereRaw('LOWER(TRIM(nama_guru)) = ?', [strtolower($name)])->first();
            if ($guru) {
                if ($guru->is_admin) {
                    $skipped[] = 'Baris '.($line + 1).": akun admin {$guru->username} dilewati agar username dan password tidak berubah.";

                    continue;
                }
                $guru->update([
                    'nip' => $guru->nip ?: $nip,
                    'nama_guru' => $name,
                    'Peran' => $row['peran'] ?: $guru->Peran,
                    'no_hp' => $row['no_hp'] ?: $guru->no_hp,
                    'id_mapel' => $idMapel ?: $guru->id_mapel,
                ]);
                $this->assignWaliKelas($guru, $row['peran'] ?? '', $row['nama_kelas'] ?? '');
                $updated++;

                continue;
            }
            $username = $this->uniqueUsername($requestedUsername, $name, $nip);
            $defaultPassword = $this->extractFirstName($name).'123';
            Guru::create([
                'nip' => $nip,
                'nama_guru' => $name,
                'Peran' => trim((string) ($row['peran'] ?? 'Guru')) ?: 'Guru',
                'no_hp' => trim((string) ($row['no_hp'] ?? '')) ?: null,
                'username' => $username,
                'password_hash' => Hash::make($defaultPassword),
                'is_admin' => 0,
                'is_aktif' => 1,
                'id_mapel' => $idMapel,
            ]);
            $guruBaru = Guru::where('username', $username)->first();
            $this->assignWaliKelas($guruBaru, $row['peran'] ?? '', $row['nama_kelas'] ?? '');
            $imported++;
        }

        if ($imported === 0 && $updated === 0) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data guru yang berhasil dibaca. Pastikan file berisi kolom judul yang didukung (NIP, Nama Guru, Peran/Jabatan, Kode Mapel, Wali Kelas, No HP, Username).'], 422);
        }

        return response()->json(['status' => 'success', 'message' => "Import selesai: {$imported} guru baru, {$updated} data diperbarui.", 'skipped' => $skipped]);
    }

    private function readSpreadsheetRows(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
        if (count($sheet) < 1) {
            return [];
        }
        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), array_shift($sheet));
        $aliases = [
            'nip' => ['nip', 'nuptk', 'nik'],
            'nama_guru' => ['nama', 'nama_guru', 'nama_lengkap', 'nama_pegawai'],
            'peran' => ['peran', 'jabatan', 'tugas_tambahan', 'role_jabatan', 'role', 'jabatan_tugas'],
            'nama_kelas' => ['nama_kelas', 'kelas', 'wali_kelas', 'kelas_wali'],
            'no_hp' => ['no_hp', 'no_telepon', 'telepon', 'hp', 'handphone', 'no_handphone', 'no_handfone', 'handfone'],
            'username' => ['username', 'user', 'akun'],
            'kode_mapel' => ['kode_mapel', 'kd_mapel', 'kode', 'kode_mapel'],
        ];
        $map = [];
        foreach ($aliases as $field => $names) {
            foreach ($headers as $index => $header) {
                if (in_array($header, $names, true)) {
                    $map[$field] = $index;
                    break;
                }
            }
        }

        return array_values(array_map(function ($row) use ($map, $aliases) {
            $result = [];
            foreach (array_keys($aliases) as $field) {
                $idx = $map[$field] ?? null;
                $result[$field] = ($idx !== null && isset($row[$idx])) ? trim((string) $row[$idx]) : '';
            }

            return $result;
        }, $sheet));
    }

    private function normalizeHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($header)));
    }

    private function assignWaliKelas(?Guru $guru, string $peran, string $namaKelas): void
    {
        if (! $guru || stripos($peran, 'wali kelas') === false || trim($namaKelas) === '' || trim($namaKelas) === '-') {
            return;
        }
        $kelas = Kelas::whereRaw('LOWER(TRIM(nama_kelas)) = ?', [strtolower(trim($namaKelas))])->first();
        if ($kelas) {
            $kelas->update(['id_wali_kelas' => $guru->id_guru]);
        }
    }

    private function extractFirstName(string $fullName): string
    {
        $titles = ['dra', 'drs', 'dr', 'prof', 'ir', 'pdt', 'hj', 'h'];
        // Ambil bagian sebelum koma (gelar belakang)
        $beforeComma = explode(',', $fullName)[0];
        $parts = preg_split('/\s+/', trim($beforeComma));
        foreach ($parts as $part) {
            $clean = strtolower(preg_replace('/[^a-zA-Z]/', '', $part));
            if ($clean !== '' && strlen($clean) > 1 && ! in_array($clean, $titles, true)) {
                return $clean;
            }
        }

        return strtolower(preg_replace('/[^a-z0-9]/i', '', $parts[0] ?? 'guru')) ?: 'guru';
    }

    private function uniqueUsername(string $requested, string $name, ?string $nip): string
    {
        if ($requested !== '') {
            $base = strtolower(preg_replace('/[^a-z0-9]/i', '', $requested));
        } else {
            $base = $this->extractFirstName($name);
        }
        $base = $base ?: 'guru'.($nip ?: time());
        $username = $base;
        $counter = 2;
        while (Guru::where('username', $username)->exists()) {
            $username = $base.$counter++;
        }

        return $username;
    }
}
