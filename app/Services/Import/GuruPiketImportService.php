<?php

namespace App\Services\Import;

use App\Models\Guru;
use App\Models\GuruPiket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class GuruPiketImportService
{
    /**
     * Mengimpor penugasan guru piket dari file CSV / Excel.
     */
    public function import(UploadedFile $file): JsonResponse
    {
        try {
            $rows = $this->readFileRows($file->getRealPath());
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'File tidak dapat dibaca: ' . $exception->getMessage(),
            ], 422);
        }

        if (empty($rows)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File kosong atau tidak memiliki data.',
            ], 422);
        }

        $allGuru = Guru::where('is_aktif', 1)
            ->where('is_admin', 0)
            ->get();

        $stripTitles = function (string $name) {
            $name = preg_replace('/\b(dra|drs|dr|prof|ir|pdt|hj|h|s\.pd|s\.pd\.i|m\.pd|s\.kom|s\.si|s\.t|st|s\.sn|s\.ds|s\.ag|s\.psi|s\.s|ss|se|s\.e|m\.t|mt|m\.m|mm|s\.tr\.par|s\.st\.par)\b/i', '', $name);
            return strtolower(preg_replace('/[^a-z0-9]+/i', '', $name));
        };

        $guruCache = [];
        $findGuru = function (string $name) use ($allGuru, $stripTitles, &$guruCache) {
            $cleaned = trim($name);
            if ($cleaned === '') {
                return null;
            }

            $norm = strtolower(preg_replace('/[^a-z0-9]+/i', '', $cleaned));
            if (isset($guruCache[$norm])) {
                return $guruCache[$norm];
            }

            // 1. Direct case-insensitive match
            foreach ($allGuru as $g) {
                if (strcasecmp(trim($g->nama_guru), $cleaned) === 0) {
                    return $guruCache[$norm] = $g;
                }
            }

            // 2. Normalized alphanumeric match
            foreach ($allGuru as $g) {
                if (strtolower(preg_replace('/[^a-z0-9]+/i', '', $g->nama_guru)) === $norm) {
                    return $guruCache[$norm] = $g;
                }
            }

            // 3. Core name match (titles stripped)
            $cCore = $stripTitles($cleaned);
            if ($cCore !== '') {
                foreach ($allGuru as $g) {
                    $gCore = $stripTitles($g->nama_guru);
                    if ($gCore !== '' && $gCore === $cCore) {
                        return $guruCache[$norm] = $g;
                    }
                }
            }

            // 4. Fuzzy Levenshtein / similar_text match
            $bestMatch = null;
            $bestScore = 0;
            foreach ($allGuru as $g) {
                $gCore = $stripTitles($g->nama_guru);
                if ($gCore === '') {
                    continue;
                }

                $lev = levenshtein($cCore, $gCore);
                if ($lev <= 2) {
                    return $guruCache[$norm] = $g;
                }

                similar_text($cCore, $gCore, $percent);
                if ($percent > 85 && $percent > $bestScore) {
                    $bestScore = $percent;
                    $bestMatch = $g;
                }
            }

            if ($bestMatch) {
                return $guruCache[$norm] = $bestMatch;
            }

            return null;
        };

        $assignmentsByDate = [];
        $skipped = [];
        $firstDate = null;

        foreach ($rows as $lineIdx => $row) {
            $rawDate = $row['tanggal'] ?? '';
            $tanggal = $this->parseDate($rawDate);

            if (! $tanggal) {
                if (! empty(array_filter($row))) {
                    $skipped[] = 'Baris ' . ($lineIdx + 1) . ": Format tanggal '{$rawDate}' tidak valid atau kosong.";
                }
                continue;
            }

            if (! $firstDate) {
                $firstDate = $tanggal;
            }

            // Ambil nama guru dari slot piket
            $piketTeacherNames = [
                $row['pagi_1'] ?? '',
                $row['pagi_2'] ?? '',
                $row['pagi_3'] ?? '',
                $row['siang_1'] ?? '',
                $row['siang_2'] ?? '',
                $row['siang_3'] ?? '',
            ];

            $assignedGurus = [];
            $seenIds = [];

            foreach ($piketTeacherNames as $slotIdx => $tName) {
                $tName = trim((string) $tName);
                if ($tName === '') {
                    continue;
                }

                $guru = $findGuru($tName);
                if ($guru) {
                    // Hindari duplikasi guru di hari yang sama
                    if (! in_array($guru->id_guru, $seenIds, true)) {
                        $assignedGurus[] = $guru->id_guru;
                        $seenIds[] = $guru->id_guru;
                    }
                } else {
                    $skipped[] = 'Baris ' . ($lineIdx + 1) . ' (' . $tanggal . "): Guru '{$tName}' tidak ditemukan di database.";
                }
            }

            if (! empty($assignedGurus)) {
                $assignmentsByDate[$tanggal] = $assignedGurus;
            }
        }

        if (empty($assignmentsByDate)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data jadwal guru piket yang valid untuk disimpan.',
                'skipped' => $skipped,
            ], 422);
        }

        $totalAssigned = 0;
        $totalDays = count($assignmentsByDate);

        DB::transaction(function () use ($assignmentsByDate, &$totalAssigned) {
            foreach ($assignmentsByDate as $tgl => $guruIds) {
                GuruPiket::withTrashed()->whereDate('tanggal', $tgl)->forceDelete();

                foreach ($guruIds as $idGuru) {
                    GuruPiket::create([
                        'id_guru' => $idGuru,
                        'tanggal' => $tgl,
                    ]);
                    $totalAssigned++;
                }
            }
        });

        $firstCarbon = $firstDate ? Carbon::parse($firstDate) : now();
        $bulanNum = (int) $firstCarbon->format('n');
        $tahunNum = (int) $firstCarbon->format('Y');

        return response()->json([
            'status' => 'success',
            'message' => "Jadwal guru piket berhasil diimpor! ({$totalDays} hari aktif, total {$totalAssigned} penugasan guru).",
            'total_days' => $totalDays,
            'total_assigned' => $totalAssigned,
            'target_bulan' => $bulanNum,
            'target_tahun' => $tahunNum,
            'skipped' => $skipped,
        ]);
    }

    /**
     * Membaca baris-baris file CSV atau Excel dan memetakannya ke struktur guru piket.
     */
    private function readFileRows(string $path): array
    {
        // Gunakan PhpSpreadsheet untuk membaca spreadsheet / CSV secara seragam
        $sheet = IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
        if (count($sheet) < 1) {
            return [];
        }

        $headerRow = array_shift($sheet);
        $headerMap = $this->detectHeaderColumns($headerRow);

        $results = [];
        foreach ($sheet as $row) {
            if (empty(array_filter($row, fn ($v) => $v !== null && trim((string) $v) !== ''))) {
                continue;
            }

            $results[] = [
                'tanggal' => isset($headerMap['tanggal']) && isset($row[$headerMap['tanggal']]) ? (string) $row[$headerMap['tanggal']] : ($row[1] ?? ''),
                'pagi_1' => isset($headerMap['pagi_1']) && isset($row[$headerMap['pagi_1']]) ? (string) $row[$headerMap['pagi_1']] : ($row[2] ?? ''),
                'pagi_2' => isset($headerMap['pagi_2']) && isset($row[$headerMap['pagi_2']]) ? (string) $row[$headerMap['pagi_2']] : ($row[3] ?? ''),
                'pagi_3' => isset($headerMap['pagi_3']) && isset($row[$headerMap['pagi_3']]) ? (string) $row[$headerMap['pagi_3']] : ($row[4] ?? ''),
                'siang_1' => isset($headerMap['siang_1']) && isset($row[$headerMap['siang_1']]) ? (string) $row[$headerMap['siang_1']] : ($row[6] ?? ''),
                'siang_2' => isset($headerMap['siang_2']) && isset($row[$headerMap['siang_2']]) ? (string) $row[$headerMap['siang_2']] : ($row[7] ?? ''),
                'siang_3' => isset($headerMap['siang_3']) && isset($row[$headerMap['siang_3']]) ? (string) $row[$headerMap['siang_3']] : ($row[8] ?? ''),
            ];
        }

        return $results;
    }

    /**
     * Memetakan kolom header ke slot yang tepat.
     * Mengabaikan kolom yang mengandung 'koordinator', 'kordinator', atau 'waka'.
     */
    private function detectHeaderColumns(array $rawHeaders): array
    {
        $map = [];
        $normalized = array_map(fn ($h) => strtolower(trim((string) $h)), $rawHeaders);

        foreach ($normalized as $idx => $col) {
            // Abaikan koordinator dan piket waka
            if (str_contains($col, 'koordinator') || str_contains($col, 'kordinator') || str_contains($col, 'waka')) {
                continue;
            }

            // Tanggal
            if (! isset($map['tanggal']) && (str_contains($col, 'tanggal') || str_contains($col, 'hari') || $col === 'tgl')) {
                $map['tanggal'] = $idx;
                continue;
            }

            // Pagi 1, 2, 3
            if (str_contains($col, 'pagi') || str_contains($col, 'sesi 1') || str_contains($col, '07.00')) {
                if (str_contains($col, '1') && ! isset($map['pagi_1'])) {
                    $map['pagi_1'] = $idx;
                } elseif (str_contains($col, '2') && ! isset($map['pagi_2'])) {
                    $map['pagi_2'] = $idx;
                } elseif (str_contains($col, '3') && ! isset($map['pagi_3'])) {
                    $map['pagi_3'] = $idx;
                }
                continue;
            }

            // Siang 1, 2, 3
            if (str_contains($col, 'siang') || str_contains($col, 'sesi 2') || str_contains($col, '11.00')) {
                if (str_contains($col, '1') && ! isset($map['siang_1'])) {
                    $map['siang_1'] = $idx;
                } elseif (str_contains($col, '2') && ! isset($map['siang_2'])) {
                    $map['siang_2'] = $idx;
                } elseif (str_contains($col, '3') && ! isset($map['siang_3'])) {
                    $map['siang_3'] = $idx;
                }
                continue;
            }
        }

        return $map;
    }

    /**
     * Mengurai string tanggal menjadi format standar Y-m-d.
     */
    private function parseDate(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        // Cek jika numeric (Excel timestamp)
        if (is_numeric($raw) && (float) $raw > 20000 && (float) $raw < 60000) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $raw))->toDateString();
            } catch (\Throwable $e) {
                // Lanjut ke string parse
            }
        }

        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        $monthMap = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agu' => 8, 'agt' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9, 'sept' => 9,
            'oktober' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12, 'dec' => 12,
        ];

        // Format: "Selasa, 1 September 2026" atau "1 September 2026"
        if (preg_match('/(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})/', $raw, $m)) {
            $day = (int) $m[1];
            $mName = strtolower($m[2]);
            $year = (int) $m[3];

            if (isset($monthMap[$mName])) {
                return sprintf('%04d-%02d-%02d', $year, $monthMap[$mName], $day);
            }
        }

        // Format: YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }

        // Format: DD-MM-YYYY atau DD/MM/YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        try {
            return Carbon::parse($raw)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
