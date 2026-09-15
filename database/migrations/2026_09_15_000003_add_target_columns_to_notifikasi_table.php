<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->integer('id_guru')->nullable()->after('id');
            $table->integer('id_kelas')->nullable()->after('id_guru');
            $table->index(['id_guru']);
            $table->index(['id_kelas']);
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropIndex(['id_guru']);
            $table->dropIndex(['id_kelas']);
            $table->dropColumn(['id_guru', 'id_kelas']);
        });
    }
};
