<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ZipArchive;

/**
 * Mengelola data siswa: impor dari file CSV/Excel, CRUD, dan penghapusan massal.
 */
class SiswaController extends Controller
{
    /**
     * Mengimpor data siswa dari file CSV atau Excel, lalu membuat atau memperbarui data siswa berdasarkan NISN.
     *
     * @return JsonResponse
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|max:25600',
        ], [
            'file_csv.required' => 'File Excel / CSV wajib diupload.',
            'file_csv.max' => 'Ukuran file maksimal 25MB.',
        ]);

        $file = $request->file('file_csv');
        $realPath = $file->getRealPath();
        $ext = strtolower($file->getClientOriginalExtension());

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first();
        if (! $tahunAjaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu di menu Pengaturan.',
            ], 422);
        }

        $allSheets = [];

        // ── 1. Jika File Excel (.xlsx) ──
        if ($ext === 'xlsx' || $this->isZipFile($realPath)) {
            if (! class_exists(ZipArchive::class)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ekstensi ZIP PHP belum aktif. Restart Apache/Laragon lalu coba upload file Excel lagi.',
                ], 422);
            }

            try {
                $allSheets = $this->parseXlsxSheets($realPath);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal membaca file Excel (.xlsx): '.$e->getMessage(),
                ], 422);
            }
        } else {
            // ── 2. Jika File CSV / Text ──
            $rawContent = file_get_contents($realPath);
            if ($rawContent === false || trim($rawContent) === '') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File CSV kosong atau tidak dapat dibaca.',
                ], 422);
            }

            $rawContent = preg_replace('/^\xEF\xBB\xBF/', '', $rawContent);

            $sample = substr($rawContent, 0, 4000);
            $commaCount = substr_count($sample, ',');
            $semicolonCount = substr_count($sample, ';');
            $tabCount = substr_count($sample, "\t");

            $delimiter = ',';
            if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
                $delimiter = ';';
            } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
                $delimiter = "\t";
            }

            $lines = preg_split('/\r\n|\r|\n/', $rawContent);
            $rows = [];
            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }
                $parsedRow = str_getcsv($line, $delimiter);
                if (! empty($parsedRow)) {
                    $rows[] = array_map('trim', $parsedRow);
                }
            }
            $allSheets['CSV_DATA'] = $rows;
        }

        if (empty($allSheets)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File tidak berisi data atau lembar sheet yang valid.',
            ], 422);
        }

        $kelasCache = [];
        $success = 0;
        $errors = [];

        foreach ($allSheets as $sheetName => $rows) {
            if (empty($rows)) {
                continue;
            }

            // Check if it is a standard flat CSV
            $firstRow = array_map('strtolower', $rows[0]);
            $isFlat = in_array('nisn', $firstRow) && in_array('nama_siswa', $firstRow) && in_array('nama_kelas', $firstRow);

            if ($isFlat) {
                $colMap = [];
                $required = ['nisn', 'nama_siswa', 'jenis_kelamin', 'nama_kelas'];
                foreach ($required as $col) {
                    $colMap[$col] = array_search($col, $firstRow);
                }
                $hasAktifCol = in_array('is_aktif', $firstRow);
                if ($hasAktifCol) {
                    $colMap['is_aktif'] = array_search('is_aktif', $firstRow);
                }

                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    $lineNum = $i + 1;

                    $nisn = trim($row[$colMap['nisn']] ?? '');
                    $namaSiswa = trim($row[$colMap['nama_siswa']] ?? '');
                    $jenisKelamin = strtoupper(trim($row[$colMap['jenis_kelamin']] ?? ''));
                    $namaKelas = trim($row[$colMap['nama_kelas']] ?? '');

                    if ($nisn === '' && $namaSiswa === '') {
                        continue;
                    }

                    if ($nisn === '' || $namaSiswa === '' || $jenisKelamin === '' || $namaKelas === '') {
                        $errors[] = "Sheet '$sheetName' Baris $lineNum: Ada kolom wajib yang kosong.";

                        continue;
                    }

                    $jk = in_array($jenisKelamin, ['P', 'PEREMPUAN']) ? 'P' : 'L';
                    $isAktif = $hasAktifCol ? (in_array(strtolower(trim($row[$colMap['is_aktif']] ?? '')), ['1', 'yes', 'aktif', 'true']) ? 1 : 0) : 1;

                    $idKelas = $this->resolveKelas($namaKelas, $tahunAjaran, $kelasCache);
                    $this->upsertSiswa($nisn, $namaSiswa, $jk, $idKelas, $isAktif);
                    $success++;
                }
            } else {
                // Sectional / Presensi Sekolah
                $currentKelasId = null;

                foreach ($rows as $idx => $row) {
                    $rowJoined = implode(' ', $row);

                    // 1. Cek Header Kelas
                    if (stripos($rowJoined, 'Kelas') !== false) {
                        foreach ($row as $cell) {
                            $cell = trim($cell);
                            if (preg_match('/^[:=]?\s*(X|XI|XII|10|11|12)\s+[A-Za-z0-9\s\(\)\-_]+$/i', $cell)) {
                                $candidate = trim(ltrim($cell, ': ='));
                                if (stripos($candidate, 'Wali') === false && stripos($candidate, 'Materi') === false) {
                                    $currentKelasId = $this->resolveKelas($candidate, $tahunAjaran, $kelasCache);
                                    break;
                                }
                            }
                        }
                    }

                    // 2. Cek Baris Siswa (Nomor urut di col 0, NISN di col 1/selanjutnya, Nama di col 2/selanjutnya)
                    $col0 = trim($row[0] ?? '');
                    $col1 = trim($row[1] ?? '');
                    $col2 = trim($row[2] ?? '');

                    if (is_numeric($col0) && $col0 >= 1 && $col0 <= 60 && $currentKelasId) {
                        $nisn = '';
                        $nama = '';
                        $jk = 'L';

                        if (preg_match('/^\d{8,12}$/', $col1)) {
                            $nisn = $col1;
                            $nama = $col2;
                        } else {
                            foreach ($row as $k => $val) {
                                if (preg_match('/^\d{8,12}$/', $val) && $nisn === '') {
                                    $nisn = $val;
                                    $nama = trim($row[$k + 1] ?? '');
                                }
                            }
                        }

                        foreach ($row as $val) {
                            if (in_array(strtoupper($val), ['L', 'P'])) {
                                $jk = strtoupper($val);
                            }
                        }

                        if ($nama !== '') {
                            if ($nisn === '') {
                                $nisn = 'GEN'.str_pad($currentKelasId, 3, '0', STR_PAD_LEFT).substr(md5($nama), 0, 5);
                            }

                            $this->upsertSiswa($nisn, $nama, $jk, $currentKelasId, 1);
                            $success++;
                        }
                    }
                }
            }
        }

        if ($success === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data siswa yang berhasil diimport. Pastikan file berisi daftar presensi dengan nama kelas dan nomor/NISN/Nama Siswa.',
            ], 422);
        }

        $msg = "Berhasil mengimpor $success siswa ke ".count($kelasCache).' kelas!';
        if (! empty($errors)) {
            $msg .= ' ('.count($errors).' baris bermasalah).';
        }

        return response()->json([
            'status' => 'success',
            'message' => $msg,
            'imported' => $success,
            'errors' => $errors,
            'kelas_created' => count($kelasCache),
        ]);
    }

    /**
     * Memeriksa apakah file pada path yang diberikan merupakan arsip ZIP berdasarkan magic number.
     *
     * @return bool
     */
    private function isZipFile($path)
    {
        $handle = @fopen($path, 'r');
        if (! $handle) {
            return false;
        }
        $bytes = fread($handle, 4);
        fclose($handle);

        return $bytes === "PK\x03\x04";
    }

    /**
     * Mengekstrak dan mengurai semua lembar data dari file Excel (.xlsx) menjadi array associative.
     *
     * @return array<string, array<int, array<int, string>>>
     */
    private function parseXlsxSheets($filePath)
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new Exception('Gagal mengekstrak arsip file Excel.');
        }

        // Shared strings
        $sst = [];
        if ($zip->locateName('xl/sharedStrings.xml') !== false) {
            $sstXml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
            if ($sstXml) {
                foreach ($sstXml->si as $si) {
                    if (isset($si->t)) {
                        $sst[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sst[] = $text;
                    } else {
                        $sst[] = '';
                    }
                }
            }
        }

        // Workbook sheets
        $sheets = [];
        if ($zip->locateName('xl/workbook.xml') !== false) {
            $wbXml = simplexml_load_string($zip->getFromName('xl/workbook.xml'));
            if ($wbXml && isset($wbXml->sheets)) {
                foreach ($wbXml->sheets->sheet as $s) {
                    $rId = (string) $s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    $name = (string) $s['name'];
                    $sheets[$rId] = $name;
                }
            }
        }

        // Relationships
        $sheetFiles = [];
        if ($zip->locateName('xl/_rels/workbook.xml.rels') !== false) {
            $relsXml = simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));
            if ($relsXml) {
                foreach ($relsXml->Relationship as $rel) {
                    $id = (string) $rel['Id'];
                    if (isset($sheets[$id])) {
                        $sheetFiles[$sheets[$id]] = (string) $rel['Target'];
                    }
                }
            }
        }

        $allData = [];
        foreach ($sheetFiles as $sheetName => $relTarget) {
            $targetPath = 'xl/'.ltrim($relTarget, '/');
            if ($zip->locateName($targetPath) === false) {
                continue;
            }

            $sheetXml = simplexml_load_string($zip->getFromName($targetPath));
            if (! $sheetXml || ! isset($sheetXml->sheetData)) {
                continue;
            }

            $rows = [];
            foreach ($sheetXml->sheetData->row as $r) {
                $rowArr = [];
                foreach ($r->c as $c) {
                    $cellRef = (string) $c['r'];
                    $colLetter = preg_replace('/[0-9]/', '', $cellRef);
                    $colIdx = 0;
                    for ($i = 0; $i < strlen($colLetter); $i++) {
                        $colIdx = $colIdx * 26 + (ord(strtoupper($colLetter[$i])) - ord('A') + 1);
                    }
                    $colIdx -= 1;

                    $type = (string) $c['t'];
                    $val = (string) $c->v;
                    if ($type === 's' && isset($sst[(int) $val])) {
                        $val = $sst[(int) $val];
                    } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                        $val = (string) $c->is->t;
                    }

                    while (count($rowArr) < $colIdx) {
                        $rowArr[] = '';
                    }
                    $rowArr[$colIdx] = trim($val);
                }
                if (! empty(array_filter($rowArr, fn ($v) => $v !== ''))) {
                    $rows[] = $rowArr;
                }
            }
            $allData[$sheetName] = $rows;
        }

        $zip->close();

        return $allData;
    }

    /**
     * Mencari atau membuat kelas berdasarkan nama, mengembalikan ID kelas yang sesuai.
     *
     * @return int
     */
    private function resolveKelas($namaKelas, $tahunAjaran, &$kelasCache)
    {
        $namaKelas = trim($namaKelas);
        $key = strtolower($namaKelas);
        if (isset($kelasCache[$key])) {
            return $kelasCache[$key];
        }

        $existing = Kelas::withTrashed()
            ->where('nama_kelas', $namaKelas)
            ->first();

        if (! $existing && preg_match('/^(.*?)\s*\(.*?\)$/', $namaKelas, $m)) {
            $baseName = trim($m[1]);
            $existing = Kelas::withTrashed()->where('nama_kelas', $baseName)->first();
        }

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $kelasCache[$key] = $existing->id_kelas;

            return $existing->id_kelas;
        }

        $parsed = self::parseNamaKelas($namaKelas);
        $newKelas = Kelas::create([
            'nama_kelas' => $namaKelas,
            'tingkat_kelas' => $parsed['tingkat'],
            'jurusan' => $parsed['jurusan'],
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
            'id_wali_kelas' => null,
        ]);

        $kelasCache[$key] = $newKelas->id_kelas;

        return $newKelas->id_kelas;
    }

    /**
     * Membuat siswa baru atau memperbarui data siswa yang sudah ada berdasarkan NISN.
     */
    private function upsertSiswa($nisn, $namaSiswa, $jenisKelamin, $idKelas, $isAktif = 1)
    {
        $existingSiswa = Siswa::withTrashed()
            ->where('nisn', $nisn)
            ->first();

        if ($existingSiswa) {
            if ($existingSiswa->trashed()) {
                $existingSiswa->restore();
            }
            $existingSiswa->update([
                'nama_siswa' => $namaSiswa,
                'jenis_kelamin' => $jenisKelamin,
                'id_kelas' => $idKelas,
                'is_aktif' => $isAktif,
            ]);
        } else {
            Siswa::create([
                'nisn' => $nisn,
                'nama_siswa' => $namaSiswa,
                'jenis_kelamin' => $jenisKelamin,
                'id_kelas' => $idKelas,
                'is_aktif' => $isAktif,
            ]);
        }
    }

    /**
     * Mengurai nama kelas menjadi tingkat dan jurusan.
     *
     * @return array{tingkat: string, jurusan: string}
     */
    private static function parseNamaKelas($nama)
    {
        $nama = trim($nama);
        if (preg_match('/^(X|XI|XII|10|11|12)\b\s*([A-Za-z0-9\-_]+)?/i', $nama, $matches)) {
            $rawTingkat = strtoupper($matches[1]);
            $tingkatMap = [
                'X' => 'X', '10' => 'X',
                'XI' => 'XI', '11' => 'XI',
                'XII' => 'XII', '12' => 'XII',
            ];
            $tingkat = $tingkatMap[$rawTingkat] ?? 'X';
            $jurusan = isset($matches[2]) ? strtoupper(trim($matches[2])) : 'UMUM';

            return [
                'tingkat' => $tingkat,
                'jurusan' => $jurusan,
            ];
        }

        return [
            'tingkat' => 'X',
            'jurusan' => 'UMUM',
        ];
    }

    /**
     * Menghapus seluruh data siswa dari sistem.
     *
     * @return JsonResponse
     */
    public function hapusSemua(Request $request)
    {
        $total = Siswa::count();
        if ($total === 0) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data siswa untuk dihapus.'], 422);
        }

        Siswa::query()->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Semua data siswa berhasil dihapus ($total siswa).",
            'deleted' => $total,
        ]);
    }

    /**
     * Menyimpan data siswa baru berdasarkan input dari request.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required',
            'nisn' => 'required',
            'id_kelas' => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $siswa = Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'nisn' => $request->nisn,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'is_aktif' => 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Siswa berhasil ditambahkan!',
            'data' => $siswa,
        ]);
    }

    /**
     * Memperbarui data siswa yang sudah ada berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_siswa' => 'required',
            'nisn' => 'required',
            'id_kelas' => 'required',
            'jenis_kelamin' => 'required',
        ]);

        $siswa = Siswa::findOrFail($id);
        $siswa->update([
            'nama_siswa' => $request->nama_siswa,
            'nisn' => $request->nisn,
            'id_kelas' => $request->id_kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil diperbarui!',
            'data' => $siswa,
        ]);
    }

    /**
     * Menghapus data siswa berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data siswa berhasil dihapus!',
        ]);
    }
}
