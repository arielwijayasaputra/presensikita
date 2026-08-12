<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('guru', 'Peran')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->string('Peran', 50)->default('Guru')->change();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
