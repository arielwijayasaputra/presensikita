<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jurusan')) {
            Schema::create('jurusan', function (Blueprint $table) {
                $table->id('id_jurusan');
                $table->string('kode_jurusan', 20)->unique();
                $table->string('nama_jurusan', 100);
                $table->text('deskripsi')->nullable();
                $table->boolean('is_aktif')->default(true);
                $table->timestamps();
            });
        }

        $initialJurusan = [
            ['kode_jurusan' => 'AK', 'nama_jurusan' => 'Akuntansi dan Keuangan Lembaga', 'deskripsi' => 'Program keahlian Akuntansi dan Keuangan Lembaga'],
            ['kode_jurusan' => 'AN', 'nama_jurusan' => 'Animasi', 'deskripsi' => 'Program keahlian Seni dan Industri Kreatif Animasi'],
            ['kode_jurusan' => 'BD', 'nama_jurusan' => 'Bisnis Digital', 'deskripsi' => 'Program keahlian Pemasaran dan Bisnis Digital'],
            ['kode_jurusan' => 'DKV', 'nama_jurusan' => 'Desain Komunikasi Visual', 'deskripsi' => 'Program keahlian Desain Komunikasi Visual'],
            ['kode_jurusan' => 'MP', 'nama_jurusan' => 'Manajemen Perkantoran', 'deskripsi' => 'Program keahlian Manajemen Perkantoran dan Layanan Bisnis'],
            ['kode_jurusan' => 'PSPT', 'nama_jurusan' => 'Produksi dan Siaran Program Televisi', 'deskripsi' => 'Program keahlian Broadcasting dan Perfilman'],
            ['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak', 'deskripsi' => 'Program keahlian Pengembangan Perangkat Lunak dan Gim'],
            ['kode_jurusan' => 'TKI', 'nama_jurusan' => 'Teknik Komputer dan Informatika', 'deskripsi' => 'Program keahlian Teknik Komputer dan Informatika'],
            ['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer dan Jaringan', 'deskripsi' => 'Program keahlian Teknik Komputer dan Jaringan'],
            ['kode_jurusan' => 'ULW', 'nama_jurusan' => 'Usaha Layanan Wisata', 'deskripsi' => 'Program keahlian Kepariwisataan dan Usaha Layanan Wisata'],
        ];

        foreach ($initialJurusan as $j) {
            $exists = DB::table('jurusan')->where('kode_jurusan', $j['kode_jurusan'])->exists();
            if (! $exists) {
                DB::table('jurusan')->insert([
                    'kode_jurusan' => $j['kode_jurusan'],
                    'nama_jurusan' => $j['nama_jurusan'],
                    'deskripsi' => $j['deskripsi'],
                    'is_aktif' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan');
    }
};
