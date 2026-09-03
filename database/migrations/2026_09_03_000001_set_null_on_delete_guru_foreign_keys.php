<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah foreign key fk_kelas_wali agar otomatis SET NULL saat data guru dihapus permanen
        try {
            DB::statement('ALTER TABLE `kelas` DROP FOREIGN KEY `fk_kelas_wali`');
            DB::statement('ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`id_wali_kelas`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL');
        } catch (\Throwable $e) {
            // Lanjut jika foreign key memiliki penamaan berbeda
        }

        // Ubah foreign key jadwal_mengajar_ibfk_1 agar otomatis SET NULL saat data guru dihapus permanen
        try {
            DB::statement('ALTER TABLE `jadwal_mengajar` DROP FOREIGN KEY `jadwal_mengajar_ibfk_1`');
            DB::statement('ALTER TABLE `jadwal_mengajar` ADD CONSTRAINT `jadwal_mengajar_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL');
        } catch (\Throwable $e) {
            // Lanjut jika foreign key memiliki penamaan berbeda
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `kelas` DROP FOREIGN KEY `fk_kelas_wali`');
            DB::statement('ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`id_wali_kelas`) REFERENCES `guru` (`id_guru`)');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('ALTER TABLE `jadwal_mengajar` DROP FOREIGN KEY `jadwal_mengajar_ibfk_1`');
            DB::statement('ALTER TABLE `jadwal_mengajar` ADD CONSTRAINT `jadwal_mengajar_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`)');
        } catch (\Throwable $e) {
        }
    }
};
