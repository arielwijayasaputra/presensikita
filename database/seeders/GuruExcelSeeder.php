<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruExcelSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path('Data_Guru_SMKN1_Boyolangu_2026-2027.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("File tidak ditemukan: $filePath");
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Reset id_wali_kelas di tabel kelas
        DB::table('kelas')->update(['id_wali_kelas' => null]);

        // 2. Hapus seluruh data di tabel guru
        DB::table('guru')->truncate();
        $this->command->warn("Tabel guru telah di-truncate...");

        // Load Excel
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $usernameCount = [];
        $inserted = 0;
        $waliKelasUpdated = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $noVal      = $sheet->getCell('A' . $row)->getValue();
            $idGuru     = is_numeric($noVal) ? (int) $noVal : ($row - 1);
            $nip        = trim((string) $sheet->getCell('B' . $row)->getValue());
            $namaGuru   = trim((string) $sheet->getCell('C' . $row)->getValue());
            $peranExcel = trim((string) $sheet->getCell('D' . $row)->getValue());
            $kelasExcel = trim((string) $sheet->getCell('E' . $row)->getValue());

            if (empty($namaGuru)) {
                continue;
            }

            if ($nip === '-' || $nip === '' || $nip === null) {
                $nip = null;
            }

            // Map Peran ke Sistem Role (Kepsek, Wali Kelas, Waka SDM, Waka, Guru)
            $peran = $this->mapPeran($peranExcel, $namaGuru);

            // Generate Username dari Nama Depan
            $namaFirstWord = $this->getFirstName($namaGuru);
            $usernameBase = strtolower($namaFirstWord);
            $usernameBase = preg_replace('/[^a-z0-9]/', '', $usernameBase);

            if (empty($usernameBase)) {
                $usernameBase = 'guru';
            }

            if (!isset($usernameCount[$usernameBase])) {
                $usernameCount[$usernameBase] = 0;
            }
            $usernameCount[$usernameBase]++;

            if ($usernameCount[$usernameBase] === 1) {
                $username = $usernameBase;
            } else {
                $username = $usernameBase . $usernameCount[$usernameBase];
            }

            // Password: nama depan + 123
            $password = $usernameBase . '123';
            $passwordHash = Hash::make($password);

            DB::table('guru')->insert([
                'id_guru'       => $idGuru,
                'nip'           => $nip,
                'nama_guru'     => $namaGuru,
                'Peran'         => $peran,
                'foto_profil'   => null,
                'no_hp'         => null,
                'username'      => $username,
                'password_hash' => $passwordHash,
                'is_admin'      => 0,
                'is_aktif'      => 1,
                'created_at'    => now(),
                'deleted_at'    => null,
            ]);

            // Jika ada kelas di Excel, update id_wali_kelas di tabel kelas
            if (!empty($kelasExcel) && $kelasExcel !== '-') {
                $namaKelasNormalized = str_replace('-', ' ', $kelasExcel);
                $updated = DB::table('kelas')
                    ->where('nama_kelas', $namaKelasNormalized)
                    ->orWhere('nama_kelas', $kelasExcel)
                    ->update(['id_wali_kelas' => $idGuru]);

                if ($updated) {
                    $waliKelasUpdated++;
                }
            }

            $this->command->info("INSERT [ID $idGuru]: $namaGuru | Role: $peran | User: $username | Pass: $password");
            $inserted++;
        }

        // Reset Auto Increment ke max(id_guru) + 1
        $maxId = DB::table('guru')->max('id_guru') ?? 1;
        DB::statement("ALTER TABLE guru AUTO_INCREMENT = " . ($maxId + 1));

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->newLine();
        $this->command->info("==========================================");
        $this->command->info("Selesai!");
        $this->command->info("Guru diinsert  : $inserted (ID 1 - $maxId)");
        $this->command->info("Wali kelas set : $waliKelasUpdated kelas");
        $total = DB::table('guru')->count();
        $this->command->info("Total guru di DB : $total");
        $this->command->info("==========================================");
    }

    private function mapPeran(string $peranExcel, string $namaGuru): string
    {
        $lower = strtolower(trim($peranExcel));
        $namaLower = strtolower(trim($namaGuru));

        if ($lower === 'kepala sekolah' || $lower === 'kepsek' || str_contains($namaLower, 'trisno wibowo')) {
            return 'Kepsek';
        }

        if ($lower === 'wali kelas') {
            return 'Wali Kelas';
        }

        if (str_contains($lower, 'waka')) {
            if (str_contains($lower, 'sdm') || str_contains($lower, 'pengembangan sdm')) {
                return 'Waka SDM';
            }
            return 'Waka';
        }

        if (str_contains($lower, 'piket')) {
            return 'Guru Piket';
        }

        if (str_contains($lower, 'satpam')) {
            return 'Satpam';
        }

        return 'Guru';
    }

    private function getFirstName(string $namaGuru): string
    {
        $nama = explode(',', $namaGuru)[0];
        $parts = preg_split('/\s+/', trim($nama));
        $titles = ['dra.', 'drs.', 'dr.', 'prof.', 'ir.', 'pdt.', 'hj.', 'h.'];

        foreach ($parts as $part) {
            $cleanPart = trim($part);
            if (!in_array(strtolower($cleanPart), $titles) && strlen($cleanPart) > 1) {
                return $cleanPart;
            }
        }

        return $parts[0] ?? 'guru';
    }
}
