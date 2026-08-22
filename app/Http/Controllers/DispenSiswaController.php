<?php

namespace App\Http\Controllers;

use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class DispenSiswaController extends Controller
{
    public function form()
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);

        return redirect()->to(route('gurupiket.index') . '#dispen-siswa');
    }

    public function store(Request $request)
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);
        $data = $request->validate([
            'id_siswa' => ['required', 'integer', 'exists:siswa,id_siswa'],
            'tanggal_dispen' => ['required', 'date'],
            'alasan' => ['required', 'string', 'max:2000'],
        ]);

        $siswa = Siswa::where('id_siswa', $data['id_siswa'])->where('is_aktif', 1)->firstOrFail();
        $guruPiket = Guru::where('id_guru', session('auth_guru_id'))->where('is_aktif', 1)->firstOrFail();
        $dispen = DispenSiswa::create(array_merge($data, ['id_guru_piket' => $guruPiket->id_guru]));

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan dispensasi siswa berhasil dibuat.',
            'waka_link' => URL::temporarySignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => 'waka']),
        ]);
    }

    public function publicShow(DispenSiswa $dispen, string $role)
    {
        abort_unless($role === 'waka', 404);
        return view('dispen_siswa_public', [
            'dispen' => $dispen->load(['siswa.kelas', 'guruPiket']),
            'role' => $role,
            'status' => $dispen->status_waka,
            'approvalUrl' => URL::temporarySignedRoute('dispen-siswa.approve', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => $role]),
        ]);
    }

    public function approve(Request $request, DispenSiswa $dispen, string $role)
    {
        abort_unless($role === 'waka', 404);
        $data = $request->validate(['keputusan' => ['required', 'in:disetujui,ditolak'], 'catatan' => ['nullable', 'string', 'max:1000']]);
        $statusField = 'status_' . $role;
        $noteField = 'catatan_' . $role;
        $dateField = 'disetujui_' . $role . '_pada';
        $dispen->{$statusField} = $data['keputusan'];
        $dispen->{$noteField} = $data['catatan'] ?? null;
        $dispen->{$dateField} = $data['keputusan'] === 'disetujui' ? now() : null;
        $dispen->save();

        $url = URL::temporarySignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => $role]);
        return redirect()->to($url)->with('approval_message', 'Keputusan ' . strtoupper($role) . ' berhasil disimpan.');
    }
}
