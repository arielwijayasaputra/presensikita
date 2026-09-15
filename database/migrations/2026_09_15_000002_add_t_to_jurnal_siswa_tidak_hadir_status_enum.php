<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `jurnal_siswa_tidak_hadir` MODIFY `status` ENUM('S','I','D','A','T') DEFAULT 'S'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `jurnal_siswa_tidak_hadir` MODIFY `status` ENUM('S','I','D','A') DEFAULT 'S'");
        }
    }
};
