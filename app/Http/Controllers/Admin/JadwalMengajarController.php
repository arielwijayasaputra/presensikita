<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\JadwalMengajar;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

/**
 * Mengelola jadwal mengajar guru melalui operasi CRUD, impor bulk, serta penugasan dan ketersediaan guru.
 */
class JadwalMengajarController extends Controller
{
    /**
     * Mengimpor data jadwal mengajar dari file spreadsheet lalu membuat atau memperbarui jadwal berdasarkan hari, jam, dan kelas.
     *
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate(['file_jadwal' => ['required', 'file', 'max:25600', 'mimes:csv,txt,xlsx,xls']], [
            'file_jadwal.required' => 'File jadwal mengajar wajib dipilih.',
            'file_jadwal.mimes' => 'Format file harus CSV, Excel, atau TXT.',
            'file_jadwal.max' => 'Ukuran file maksimal 25MB.',
        ]);

        $file = $request->file('file_jadwal');
        try {
            $rows = $this->readSpreadsheetRows($file->getRealPath());
        } catch (\Throwable $exception) {
            return response()->json(['status' => 'error', 'message' => 'File tidak dapat dibaca: '.$exception->getMessage()], 422);
        }

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $tahunId = $tahunAjaran?->id_tahun_ajaran ?? 1;

        $allMapel = Mapel::whereNull('deleted_at')->get();
        $allKelas = Kelas::whereNull('deleted_at')->get();
        $allGuru = Guru::whereNull('deleted_at')->get();
        $jamByHariKe = JamPelajaran::whereNull('deleted_at')->get()->keyBy(fn ($j) => $j->hari.':'.$j->jam_ke);

        $mapelCache = [];
        $kelasCache = [];
        $guruCache = [];

        $guruById = $allGuru->keyBy('id_guru');
        $guruByNip = $allGuru->filter(fn ($g) => filled($g->nip))->keyBy(fn ($g) => strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $g->nip)));

        $findGuruById = function (mixed $value) use ($guruById) {
            $id = (int) ($value ?? 0);
            if ($id <= 0) {
                return null;
            }

            return $guruById->get($id);
        };

        $findGuruByNip = function (mixed $value) use ($guruByNip) {
            $cleaned = trim((string) $value);
            if ($cleaned === '') {
                return null;
            }
            $norm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $cleaned));
            if ($norm === '') {
                return null;
            }

            return $guruByNip->get($norm);
        };

        $findMapel = function (string $key) use (&$allMapel, &$mapelCache) {
            $cleaned = trim($key);
            if ($cleaned === '') {
                return null;
            }
            $norm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $cleaned));
            if ($norm === '') {
                return null;
            }

            if (isset($mapelCache[$norm])) {
                return $mapelCache[$norm];
            }

            // 1. Direct match kode_mapel / nama_mapel
            foreach ($allMapel as $m) {
                if (strcasecmp(trim($m->kode_mapel), $cleaned) === 0 || strcasecmp(trim($m->nama_mapel), $cleaned) === 0) {
                    return $mapelCache[$norm] = $m;
                }
            }

            // 2. Normalized alphanumeric match
            foreach ($allMapel as $m) {
                $normKode = strtolower(preg_replace('/[^a-z0-9]+/i', '', $m->kode_mapel));
                $normNama = strtolower(preg_replace('/[^a-z0-9]+/i', '', $m->nama_mapel));
                if ($normKode === $norm || $normNama === $norm) {
                    return $mapelCache[$norm] = $m;
                }
            }

            // 3. Known aliases
            $aliasMap = [
                'upacara' => 'Upacara/Apel',
                'upacaraapel' => 'Upacara/Apel',
                'pembiasaan' => 'Pembiasaan Hari Jumat',
                'pembiasaanharijumat' => 'Pembiasaan Hari Jumat',
                'pancasila' => 'Pendidikan Pancasila',
                'ppkn' => 'Pendidikan Pancasila',
                'agama' => 'Pendidikan Agama Islam dan Budi Pekerti',
                'pai' => 'Pendidikan Agama Islam dan Budi Pekerti',
                'penjas' => 'PJOK',
                'penjaskes' => 'PJOK',
                'olahraga' => 'PJOK',
                'bimbingankonseling' => 'BK',
                'kewirausahaan' => 'Kreativitas, Inovasi, dan Kewirausahaan',
                'pkwu' => 'Kreativitas, Inovasi, dan Kewirausahaan',
                'kwu' => 'Kreativitas, Inovasi, dan Kewirausahaan',
            ];

            if (isset($aliasMap[$norm])) {
                $targetName = $aliasMap[$norm];
                foreach ($allMapel as $m) {
                    if (strcasecmp($m->nama_mapel, $targetName) === 0) {
                        return $mapelCache[$norm] = $m;
                    }
                }
            }

            // 4. Buat mapel otomatis jika belum ada di master (seperti Upacara/Apel, Pembiasaan Hari Jumat, dsb)
            $cleanKode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $cleaned), 0, 8)) ?: 'MAPEL';
            $existingKode = Mapel::where('kode_mapel', $cleanKode)->first();
            if ($existingKode) {
                $cleanKode .= rand(1, 99);
            }

            $newMapel = Mapel::create([
                'kode_mapel' => $cleanKode,
                'nama_mapel' => $cleaned,
                'kelompok' => 'A',
            ]);
            $allMapel->push($newMapel);

            return $mapelCache[$norm] = $newMapel;
        };

        $findKelas = function (string $key) use ($allKelas, &$kelasCache) {
            $cleaned = trim($key);
            if ($cleaned === '') {
                return null;
            }
            $norm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $cleaned));
            if ($norm === '') {
                return null;
            }

            if (isset($kelasCache[$norm])) {
                return $kelasCache[$norm];
            }

            foreach ($allKelas as $k) {
                $kNorm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $k->nama_kelas));
                if ($kNorm === $norm) {
                    return $kelasCache[$norm] = $k;
                }
            }

            return null;
        };

        $findGuru = function (string $key) use ($allGuru, &$guruCache) {
            $cleaned = trim($key);
            if ($cleaned === '' || $cleaned === '-' || strtolower($cleaned) === 'null') {
                return null;
            }

            $norm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $cleaned));
            if (isset($guruCache[$norm])) {
                return $guruCache[$norm];
            }

            // 1. Direct match
            foreach ($allGuru as $g) {
                if (strcasecmp(trim($g->nama_guru), $cleaned) === 0) {
                    return $guruCache[$norm] = $g;
                }
            }

            // 2. Normalized alphanumeric match
            foreach ($allGuru as $g) {
                $gNorm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $g->nama_guru));
                if ($gNorm === $norm) {
                    return $guruCache[$norm] = $g;
                }
            }

            // 3. Strip gelar dan bandingkan nama inti
            $stripTitles = function (string $name) {
                $name = preg_replace('/\b(dra|drs|dr|prof|ir|pdt|hj|h|s\.pd|s\.pd\.i|m\.pd|s\.kom|s\.si|s\.t|st|s\.sn|s\.ds|s\.ag|s\.psi|s\.s|se|s\.e|m\.t|mt|m\.m|mm)\b/i', '', $name);

                return strtolower(preg_replace('/[^a-z0-9]+/i', '', $name));
            };

            $nameCore = $stripTitles($cleaned);
            if ($nameCore !== '') {
                foreach ($allGuru as $g) {
                    $gCore = $stripTitles($g->nama_guru);
                    if ($gCore !== '' && $gCore === $nameCore) {
                        return $guruCache[$norm] = $g;
                    }
                }
            }

            return null;
        };

        $imported = 0;
        $updated = 0;
        $skipped = [];
        $unmatchedGuruCount = [];
        $totalRows = count($rows);
        foreach ($rows as $line => $row) {
            $hari = ucfirst(strtolower(trim((string) ($row['hari'] ?? ''))));
            $mapelRaw = trim((string) ($row['mapel'] ?? ''));
            $kelasRaw = trim((string) ($row['kelas'] ?? ''));
            $guruRaw = trim((string) ($row['guru'] ?? ''));
            $idGuruRaw = trim((string) ($row['id_guru'] ?? ''));
            $nipRaw = trim((string) ($row['nip'] ?? ''));
            $jamRaw = trim((string) ($row['jam_ke'] ?? ''));

            if (! in_array($hari, Hari::getWeekdayNames(), true)) {
                $skipped[] = 'Baris '.($line + 1).': hari "'.($row['hari'] ?? '').'" tidak valid (gunakan Senin s.d. Jumat).';

                continue;
            }

            if ($mapelRaw === '' || $kelasRaw === '') {
                $skipped[] = 'Baris '.($line + 1).': mapel dan kelas wajib diisi.';

                continue;
            }

            $mapel = $findMapel($mapelRaw);
            if (! $mapel) {
                $skipped[] = 'Baris '.($line + 1).': mapel "'.$mapelRaw.'" tidak terdaftar.';

                continue;
            }

            $kelas = $findKelas($kelasRaw);
            if (! $kelas) {
                $skipped[] = 'Baris '.($line + 1).': kelas "'.$kelasRaw.'" tidak terdaftar.';

                continue;
            }

            $jamNumbers = $this->parseJamList($hari, $jamRaw);
            if (empty($jamNumbers)) {
                $skipped[] = 'Baris '.($line + 1).': jam "'.$jamRaw.'" tidak valid untuk hari '.$hari.'.';

                continue;
            }

            $guru = null;
            $guruUnmatched = false;
            $guruUnmatchedDesc = '';

            // 1. Cocokkan lewat ID Guru (prioritas tertinggi)
            if (filled($idGuruRaw)) {
                $guru = $findGuruById($idGuruRaw);
                if (! $guru) {
                    $guruUnmatched = true;
                    $guruUnmatchedDesc = 'id_guru "'.$idGuruRaw.'" tidak terdaftar';
                }
            }

            // 2. Cocokkan lewat NIP / NUPTK
            if (! $guru && filled($nipRaw)) {
                $guru = $findGuruByNip($nipRaw);
                if (! $guru) {
                    $guruUnmatched = true;
                    $guruUnmatchedDesc = 'NIP/NUPTK "'.$nipRaw.'" tidak terdaftar';
                }
            }

            // 3. Fallback: cocokkan lewat nama guru
            if (! $guru && $guruRaw !== '' && $guruRaw !== '-' && strtolower($guruRaw) !== 'null') {
                $guru = $findGuru($guruRaw);
                if (! $guru) {
                    $guruUnmatched = true;
                    $guruUnmatchedDesc = 'nama guru "'.$guruRaw.'" tidak terdaftar';
                }
            }

            if ($guruUnmatched && ! $guru) {
                $keyGuru = $idGuruRaw !== '' ? 'ID:'.$idGuruRaw : ($nipRaw !== '' ? 'NIP:'.$nipRaw : 'Nama:'.$guruRaw);
                if (isset($unmatchedGuruCount[$keyGuru])) {
                    $unmatchedGuruCount[$keyGuru]++;
                } else {
                    $unmatchedGuruCount[$keyGuru] = 1;
                }

                $skipped[] = 'Baris '.($line + 1).': guru tidak dapat dicocokkan — '.$guruUnmatchedDesc.'. Perbaiki kolom Guru/ID/NIP atau daftarkan guru terlebih dahulu di halaman Guru.';

                continue;
            }

            foreach ($jamNumbers as $jamKe) {
                $jam = $jamByHariKe->get($hari.':'.$jamKe) ?? $jamByHariKe->get($hari.':'.($jamKe % 100));
                if (! $jam) {
                    $displayJam = ($jamKe >= 100 && $hari === 'Jumat') ? ($jamKe - 100) : $jamKe;
                    $skipped[] = 'Baris '.($line + 1).': jam ke-'.$displayJam.' tidak terdaftar pada master jam pelajaran hari '.$hari.'.';

                    continue;
                }

                $assignedGuruId = $guru?->id_guru;
                if ($assignedGuruId) {
                    // Cek apakah guru sudah mengajar di kelas lain pada hari & jam yang sama
                    $bentrokLain = JadwalMengajar::whereNull('deleted_at')
                        ->where('hari', $hari)
                        ->where('id_jam', $jam->id_jam)
                        ->where('id_tahun_ajaran', $tahunId)
                        ->where('id_guru', $assignedGuruId)
                        ->where('id_kelas', '!=', $kelas->id_kelas)
                        ->first();

                    if ($bentrokLain) {
                        $displayJam = ($jamKe >= 100 && $hari === 'Jumat') ? ($jamKe - 100) : $jamKe;
                        $skipped[] = 'Baris '.($line + 1).': guru "'.$guruRaw.'" bentrok mengajar di kelas lain pada hari '.$hari.' jam ke-'.$displayJam.'. Jadwal kelas tetap dibuat tanpa guru.';
                        $assignedGuruId = null;
                    }
                }

                try {
                    $jadwal = JadwalMengajar::whereNull('deleted_at')
                        ->where('hari', $hari)
                        ->where('id_jam', $jam->id_jam)
                        ->where('id_kelas', $kelas->id_kelas)
                        ->where('id_tahun_ajaran', $tahunId)
                        ->first();

                    if ($jadwal) {
                        $jadwal->update([
                            'id_mapel' => $mapel->id_mapel,
                            'id_guru' => $assignedGuruId,
                        ]);
                        $updated++;
                    } else {
                        JadwalMengajar::create([
                            'hari' => $hari,
                            'id_jam' => $jam->id_jam,
                            'id_kelas' => $kelas->id_kelas,
                            'id_mapel' => $mapel->id_mapel,
                            'id_guru' => $assignedGuruId,
                            'id_tahun_ajaran' => $tahunId,
                        ]);
                        $imported++;
                    }
                } catch (\Throwable $ex) {
                    $displayJam = ($jamKe >= 100 && $hari === 'Jumat') ? ($jamKe - 100) : $jamKe;
                    $skipped[] = 'Baris '.($line + 1).': gagal menyimpan jam ke-'.$displayJam.' ('.$ex->getMessage().').';
                }
            }
        }

        $unmatchedSummary = '';
        if (! empty($unmatchedGuruCount)) {
            $lines = [];
            foreach ($unmatchedGuruCount as $key => $count) {
                $lines[] = $key.' ('.$count.' baris)';
            }
            $unmatchedSummary = ' Guru yang tidak terdaftar: '.implode('; ', $lines).'.';
        }

        if ($imported === 0 && $updated === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data jadwal yang berhasil dibaca. Pastikan file berisi kolom judul yang didukung (Kelas, Hari, Jam, Mata Pelajaran / Mapel, Guru).'.$unmatchedSummary,
                'total_rows' => $totalRows,
                'imported' => $imported,
                'updated' => $updated,
                'skipped' => $skipped,
                'unmatched_guru' => $unmatchedGuruCount,
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Import selesai: {$imported} jadwal baru, {$updated} jadwal diperbarui, ".count($skipped).' jadwal dilewati.'.$unmatchedSummary,
            'total_rows' => $totalRows,
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'unmatched_guru' => $unmatchedGuruCount,
        ]);
    }

    /**
     * Mengurai string jam pelajaran menjadi array jam ke (mendukung angka tunggal "1", rentang "2-3", "4-5-6", "7-8-9-10", "2,3", dsb).
     *
     * @return int[]
     */
    private function parseJamList(string $hari, mixed $value): array
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return [];
        }

        // Ambil semua kumpulan angka
        $numbers = array_values(array_filter(
            preg_split('/[^0-9]+/', $raw),
            fn ($n) => $n !== '' && is_numeric($n)
        ));

        if (empty($numbers)) {
            return [];
        }

        $ints = array_map('intval', $numbers);

        // Jika hanya 2 angka dan ada tanda minus (misal "2-5"), bentangkan range 2, 3, 4, 5
        if (count($ints) === 2 && $ints[1] > $ints[0] + 1 && str_contains($raw, '-')) {
            $ints = range($ints[0], $ints[1]);
        }

        $jamList = [];
        foreach ($ints as $jamN) {
            if ($jamN <= 0) {
                continue;
            }
            $resolved = $this->resolveJamKe($hari, $jamN);
            if ($resolved !== null && ! in_array($resolved, $jamList, true)) {
                $jamList[] = $resolved;
            }
        }

        return $jamList;
    }

    /**
     * Menyelesaikan nomor jam ke awal, menambahkan offset khusus untuk hari Jumat.
     */
    private function resolveJamKe(string $hari, mixed $value): ?int
    {
        $jamN = (int) trim((string) $value);
        if ($jamN <= 0) {
            return null;
        }
        if ($jamN >= 100) {
            return $jamN;
        }

        return strtolower($hari) === 'jumat' ? 100 + $jamN : $jamN;
    }

    /**
     * Membaca hanya kolom Hari, Kelas, Jam, Mata Pelajaran, dan Guru berdasarkan header.
     * Kolom lain dalam CSV atau spreadsheet sengaja diabaikan.
     */
    private function readSpreadsheetRows(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        if ($reader instanceof Csv) {
            $header = strtok((string) file_get_contents($path), "\r\n") ?: '';
            $delimiter = substr_count($header, ';') > substr_count($header, ',') ? ';' : ',';
            if (str_contains($header, "\t")) {
                $delimiter = "\t";
            }
            $reader->setDelimiter($delimiter);
            $reader->setEnclosure('"');
        }

        $sheet = $reader->load($path)->getActiveSheet()->toArray(null, true, true, false);
        if (count($sheet) < 1) {
            return [];
        }
        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), array_shift($sheet));
        $aliases = [
            'hari' => ['hari', 'day', 'hari_ke', 'nama_hari'],
            'kelas' => ['kelas', 'nama_kelas', 'rombel', 'kelas_rombel'],
            'jam_ke' => ['jam', 'jam_ke', 'jam_keberapa', 'jam_ke_berapa', 'ke', 'jam_pelajaran', 'jam_ke_1'],
            'mapel' => ['mapel', 'matapelajaran', 'mata_pelajaran', 'matapelajaram', 'mata_pelajaram', 'nama_mapel', 'pelajaran', 'kode_mapel', 'kd_mapel', 'kode', 'nama_mata_pelajaran'],
            'guru' => ['guru', 'nama_guru', 'pengampu', 'guru_pengampu', 'nama_pengampu', 'guru_pengajar'],
            'id_guru' => ['id_guru', 'guru_id', 'kode_guru', 'no_urut'],
            'nip' => ['nip', 'nuptk', 'nik', 'nomor_induk_pegawai'],
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

    /**
     * Menormalisasi header kolom menjadi format snake_case huruf kecil.
     */
    private function normalizeHeader(string $header): string
    {
        // Hilangkan BOM UTF-8 yang sering menempel pada header kolom pertama CSV.
        $header = preg_replace('/^\\xEF\\xBB\\xBF/', '', $header) ?? $header;

        return trim(preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($header))), '_');
    }

    /**
     * Menentukan daftar guru yang tersedia atau yang sedang mengajar (bentrok) pada hari dan jam tertentu.
     *
     * @return JsonResponse
     */
    public function guruTersedia(Request $request)
    {
        $request->validate([
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'id_jam' => ['required', 'integer'],
            'id_jadwal' => ['nullable', 'integer'],
        ]);

        $hari = $request->hari;
        $idJam = (int) $request->id_jam;
        $idJadwal = $request->id_jadwal ? (int) $request->id_jadwal : null;

        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $tahunId = $tahunAjaran?->id_tahun_ajaran ?? 1;

        // Ambil semua jadwal pada hari & jam tersebut yang sudah punya guru
        $bentrokJadwals = JadwalMengajar::where('jadwal_mengajar.hari', $hari)
            ->where('jadwal_mengajar.id_jam', $idJam)
            ->where('jadwal_mengajar.id_tahun_ajaran', $tahunId)
            ->whereNotNull('jadwal_mengajar.id_guru')
            ->whereNull('jadwal_mengajar.deleted_at')
            ->when($idJadwal, fn ($q) => $q->where('jadwal_mengajar.id_jadwal', '!=', $idJadwal))
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
            ->whereNull('kelas.deleted_at')
            ->whereNull('mapel.deleted_at')
            ->select(
                'jadwal_mengajar.id_guru',
                'kelas.nama_kelas',
                'mapel.nama_mapel'
            )
            ->get()
            ->keyBy('id_guru');

        $bentrokGuruIds = $bentrokJadwals->keys()->all();

        $allGuru = Guru::where('is_admin', 0)
            ->where('is_aktif', 1)
            ->orderBy('nama_guru')
            ->get();

        $guruTersedia = [];
        $guruBentrok = [];

        foreach ($allGuru as $g) {
            if (in_array($g->id_guru, $bentrokGuruIds)) {
                $infoBentrok = $bentrokJadwals->get($g->id_guru);
                $guruBentrok[] = [
                    'id_guru' => $g->id_guru,
                    'nama_guru' => $g->nama_guru,
                    'peran' => $g->Peran ?? 'Guru',
                    'status_jam' => 'bentrok',
                    'keterangan' => "Sedang Mengajar {$infoBentrok->nama_mapel} di {$infoBentrok->nama_kelas}",
                ];
            } else {
                $guruTersedia[] = [
                    'id_guru' => $g->id_guru,
                    'nama_guru' => $g->nama_guru,
                    'peran' => $g->Peran ?? 'Guru',
                    'status_jam' => 'tersedia',
                    'keterangan' => 'Jam Mengajar Kosong / Siap Ditugaskan',
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'hari' => $hari,
            'id_jam' => $idJam,
            'total_guru_tersedia' => count($guruTersedia),
            'total_guru_bentrok' => count($guruBentrok),
            'guru_tersedia' => $guruTersedia,
            'guru_bentrok' => $guruBentrok,
        ]);
    }

    /**
     * Menugaskan seorang guru pengampu untuk satu jadwal, dengan pengecekan jadwal bentrok pada hari dan jam yang sama.
     *
     * @return JsonResponse
     */
    public function tugaskanGuru(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);

        $request->validate([
            'id_guru' => ['required', 'integer', 'exists:guru,id_guru'],
        ], [
            'id_guru.required' => 'Pilih guru pengampu terlebih dahulu.',
            'id_guru.exists' => 'Guru pengampu tidak ditemukan.',
        ]);

        $guru = Guru::where('id_guru', $request->id_guru)->where('is_aktif', 1)->firstOrFail();

        $tahunId = $jadwal->id_tahun_ajaran;

        // Cek apakah guru ini sedang mengajar di jam dan hari yang sama di kelas lain
        $bentrok = JadwalMengajar::where('hari', $jadwal->hari)
            ->where('id_jam', $jadwal->id_jam)
            ->where('id_tahun_ajaran', $tahunId)
            ->where('id_guru', $guru->id_guru)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->whereNull('deleted_at')
            ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas')
            ->first();

        if ($bentrok) {
            return response()->json([
                'status' => 'error',
                'message' => "Guru {$guru->nama_guru} tidak dapat ditugaskan karena sudah memiliki jadwal mengajar di {$bentrok->nama_kelas} pada hari {$jadwal->hari} jam ke-{$jadwal->id_jam}.",
            ], 422);
        }

        $jadwal->update([
            'id_guru' => $guru->id_guru,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Guru {$guru->nama_guru} berhasil ditugaskan untuk jadwal ini.",
            'nama_guru' => $guru->nama_guru,
        ]);
    }

    /**
     * Memperbarui data jadwal mengajar berdasarkan input yang divalidasi.
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalMengajar::findOrFail($id);

        $data = $request->validate([
            'id_guru' => ['required', 'integer', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'integer', 'exists:mapel,id_mapel'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'id_jam' => ['required', 'integer', 'exists:jam_pelajaran,id_jam'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
        ]);

        $jadwal->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil diperbarui.',
        ]);
    }

    /**
     * Menyimpan data jadwal mengajar baru berdasarkan input yang divalidasi.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_guru' => ['required', 'integer', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'integer', 'exists:mapel,id_mapel'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'id_jam' => ['required', 'integer', 'exists:jam_pelajaran,id_jam'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat'],
            'id_tahun_ajaran' => ['required', 'integer', 'exists:tahun_ajaran,id_tahun_ajaran'],
        ]);

        JadwalMengajar::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil ditambahkan.',
        ]);
    }

    /**
     * Menghapus seluruh jadwal aktif dengan soft delete untuk tahun ajaran aktif.
     *
     * @return JsonResponse
     */
    public function destroyAll()
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $query = JadwalMengajar::whereNull('deleted_at');

        if ($tahunAjaran) {
            $query->where('id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran);
        }

        $deleted = $query->count();
        $query->delete();

        return response()->json([
            'status' => 'success',
            'message' => $deleted.' jadwal berhasil dihapus sementara.',
            'deleted' => $deleted,
        ]);
    }

    /**
     * Menghapus data jadwal mengajar berdasarkan ID.
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        JadwalMengajar::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal mengajar berhasil dihapus.',
        ]);
    }
}
