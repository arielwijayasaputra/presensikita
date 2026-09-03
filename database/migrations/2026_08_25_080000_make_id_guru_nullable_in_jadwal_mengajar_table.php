<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `jadwal_mengajar` MODIFY `id_guru` INT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `jadwal_mengajar` MODIFY `id_guru` INT NOT NULL');
    }
};
