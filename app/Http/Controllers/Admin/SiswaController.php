<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file_csv.required' => 'File CSV wajib diupload.',
            'file_csv.mimes'    => 'Format file harus CSV (.csv).',
            'file_csv.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membaca file CSV.',
            ], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json([
                'status'  => 'error',
                'message' => 'File CSV kosong atau format tidak valid.',
            ], 422);
        }

        $header = array_map('strtolower', array_map('trim', $header));
        $required = ['nisn', 'nama_siswa', 'jenis_kelamin', 'nama_kelas'];
        $missing = array_diff($required, $header);

        if (!empty($missing)) {
            fclose($handle);
            return response()->json([
                'status'  => 'error',
                'message' => 'Kolom yang harus ada: ' . implode(', ', $required) . '. Kolom hilang: ' . implode(', ', $missing),
            ], 422);
        }

        $colMap = [];
        foreach ($required as $col) {
            $colMap[$col] = array_search($col, $header);
        }

        $hasAktifCol = in_array('is_aktif', $header);
        if ($hasAktifCol) {
            $colMap['is_aktif'] = array_search('is_aktif', $header);
        }

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first();
        if (!$tahunAjaran) {
            fclose($handle);
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.',
            ], 422);
        }

        $kelasCache = [];
        $success = 0;
        $errors = [];
        $lineNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNum++;

            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }

            $nisn          = trim($row[$colMap['nisn']] ?? '');
            $namaSiswa     = trim($row[$colMap['nama_siswa']] ?? '');
            $jenisKelamin  = strtoupper(trim($row[$colMap['jenis_kelamin']] ?? ''));
            $namaKelas     = trim($row[$colMap['nama_kelas']] ?? '');

            if ($hasAktifCol) {
                $rawAktif = strtolower(trim($row[$colMap['is_aktif']] ?? ''));
                $isAktif  = in_array($rawAktif, ['1', 'yes', 'aktif', 'true']) ? 1 : 0;
            } else {
                $isAktif = 1;
            }

            if ($nisn === '' && $namaSiswa === '') continue;

            if ($nisn === '' || $namaSiswa === '' || $jenisKelamin === '' || $namaKelas === '') {
                $errors[] = "Baris $lineNum: Ada kolom yang kosong.";
                continue;
            }

            if (!in_array($jenisKelamin, ['L', 'P'])) {
                $errors[] = "Baris $lineNum: Jenis kelamin '$jenisKelamin' tidak valid (hanya L/P).";
                continue;
            }

            $parsed = self::parseNamaKelas($namaKelas);
            if (!$parsed) {
                $errors[] = "Baris $lineNum: Format nama_kelas '$namaKelas' tidak dikenali (contoh: X TKJ 1).";
                continue;
            }

            $kelasKey = strtolower($namaKelas);
            if (!isset($kelasCache[$kelasKey])) {
                $existing = Kelas::withTrashed()
                    ->where('nama_kelas', $namaKelas)
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }
                    $kelasCache[$kelasKey] = $existing->id_kelas;
                } else {
                    $newKelas = Kelas::create([
                        'nama_kelas'       => $namaKelas,
                        'tingkat_kelas'    => $parsed['tingkat'],
                        'jurusan'          => $parsed['jurusan'],
                        'id_tahun_ajaran'  => $tahunAjaran->id_tahun_ajaran,
                        'id_wali_kelas'    => null,
                    ]);
                    $kelasCache[$kelasKey] = $newKelas->id_kelas;
                }
            }

            $existingSiswa = Siswa::withTrashed()
                ->where('nisn', $nisn)
                ->first();

            if ($existingSiswa) {
                if ($existingSiswa->trashed()) {
                    $existingSiswa->restore();
                }
                $existingSiswa->update([
                    'nama_siswa'    => $namaSiswa,
                    'jenis_kelamin' => $jenisKelamin,
                    'id_kelas'      => $kelasCache[$kelasKey],
                    'is_aktif'      => $isAktif,
                ]);
            } else {
                Siswa::create([
                    'nisn'           => $nisn,
                    'nama_siswa'     => $namaSiswa,
                    'jenis_kelamin'  => $jenisKelamin,
                    'id_kelas'       => $kelasCache[$kelasKey],
                    'is_aktif'       => $isAktif,
                ]);
            }

            $success++;
        }

        fclose($handle);

        $msg = "Berhasil import $success siswa.";
        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' baris gagal.';
        }

        return response()->json([
            'status'       => 'success',
            'message'      => $msg,
            'imported'     => $success,
            'errors'       => $errors,
            'kelas_created'=> count(array_filter($kelasCache, fn($v) => is_int($v))) > 0 ? count($kelasCache) : 0,
        ]);
    }

    private static function parseNamaKelas($nama)
    {
        $nama = trim($nama);
        $parts = preg_split('/\s+/', $nama, 3);

        if (count($parts) < 3) return null;

        $tingkatMap = ['X' => 'X', 'XI' => 'XI', 'XII' => 'XII'];
        $tingkat = strtoupper($parts[0]);

        if (!isset($tingkatMap[$tingkat])) return null;

        $jurusan = strtoupper($parts[1]);
        $nomor   = $parts[2];

        if (!is_numeric($nomor)) return null;

        return [
            'tingkat' => $tingkatMap[$tingkat],
            'jurusan' => $jurusan,
            'nomor'   => (int) $nomor,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa'    => 'required',
            'nisn'          => 'required',
            'id_kelas'      => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $siswa = Siswa::create([
            'nama_siswa'    => $request->nama_siswa,
            'nisn'          => $request->nisn,
            'id_kelas'      => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'is_aktif'      => 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Siswa berhasil ditambahkan!',
            'data'    => $siswa,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_siswa'    => 'required',
            'nisn'          => 'required',
            'id_kelas'      => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->update([
            'nama_siswa'    => $request->nama_siswa,
            'nisn'          => $request->nisn,
            'id_kelas'      => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data siswa berhasil diperbarui!',
            'data'    => $siswa,
        ]);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data siswa berhasil dihapus!',
        ]);
    }
}
