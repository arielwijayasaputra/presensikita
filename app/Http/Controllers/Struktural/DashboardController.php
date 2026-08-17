<?php

namespace App\Http\Controllers\Struktural;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Models\TahunAjaran;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $guru = Guru::find(session('auth_guru_id'));
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        $sidebar = 'partials.sidebar_struktural';
        $profilUpdateUrl = route('struktural.profil.update');

        return view('struktural.dashboard', compact(
            'guru',
            'namaSekolah',
            'tahunAjaran',
            'sidebar',
            'profilUpdateUrl'
        ));
    }
}
