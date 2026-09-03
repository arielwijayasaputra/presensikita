<?php

namespace Tests\Feature;

use App\Models\AkunAdmin;
use App\Models\JamPelajaran;
use App\Models\Pengaturan;
use Tests\TestCase;

class JamPelajaranSekarangTest extends TestCase
{
    public function test_jam_pelajaran_sekarang_endpoint_returns_data()
    {
        $response = $this->getJson(route('jam-pelajaran.sekarang'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'jam',
            'istirahat',
            'jam_semua',
            'istirahat_semua',
            'waktu_server',
        ]);
    }
}
