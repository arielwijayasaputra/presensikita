<?php

namespace App\Http\Controllers;

use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DispenSiswaController extends Controller
{
    public function form()
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);

        return redirect()->to(route('gurupiket.index').'#dispen-siswa');
    }

    public function storeAbsensi(Request $request)
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);

        return $this->store($request);
    }

    public function store(Request $request)
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);
        $data = $request->validate([
            'id_siswa' => ['required', 'integer', 'exists:siswa,id_siswa'],
            'tanggal_dispen' => ['required', 'date'],
            'jenis_absen' => ['required', 'in:S,I,D'],
            'alasan' => ['nullable', 'string', 'max:2000'],
            'foto_surat' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $siswa = Siswa::where('id_siswa', $data['id_siswa'])->where('is_aktif', 1)->firstOrFail();
        $guruPiket = Guru::where('id_guru', session('auth_guru_id'))->where('is_aktif', 1)->firstOrFail();
        $fotoSurat = $request->hasFile('foto_surat') ? $request->file('foto_surat')->store('surat-dispen', 'public') : null;

        try {
            $dispen = DB::transaction(function () use ($data, $siswa, $guruPiket, $fotoSurat) {
                $jurnal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                    ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                    ->whereDate('jurnal_kelas.tanggal', $data['tanggal_dispen'])
                    ->select('jurnal_kelas.*')
                    ->orderByDesc('jurnal_kelas.waktu_input')
                    ->first();

                if (! $jurnal) {
                    $idJadwal = DB::table('jadwal_mengajar')->where('id_kelas', $siswa->id_kelas)->value('id_jadwal');
                    if (! $idJadwal) {
                        $hari = Hari::getNamaHariFromAbbr(date('D', strtotime($data['tanggal_dispen'])));
                        $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
                        abort_if(! $hari || ! $tahunAjaran, 422, 'Jadwal kelas belum tersedia untuk tanggal tersebut.');
                        $idJadwal = DB::table('jadwal_mengajar')->insertGetId([
                            'id_guru' => $guruPiket->id_guru,
                            'id_mapel' => (int) (DB::table('mapel')->min('id_mapel') ?? 1),
                            'id_kelas' => $siswa->id_kelas,
                            'id_jam' => (int) (DB::table('jam_pelajaran')->min('id_jam') ?? 1),
                            'hari' => $hari,
                            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                        ]);
                    }
                    $jurnal = JurnalKelas::create([
                        'id_jadwal' => $idJadwal,
                        'id_guru' => $guruPiket->id_guru,
                        'tanggal' => $data['tanggal_dispen'],
                        'status_kehadiran_guru' => 'Hadir',
                        'materi' => 'Absensi Guru Piket',
                        'jumlah_hadir' => Siswa::where('id_kelas', $siswa->id_kelas)->where('is_aktif', 1)->count(),
                        'waktu_input' => now(),
                    ]);
                }

                $existing = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->where('id_siswa', $siswa->id_siswa)->first();
                if (! $existing) {
                    $jurnal->decrement('jumlah_hadir');
                }
                JurnalSiswaTidakHadir::updateOrCreate(
                    ['id_jurnal' => $jurnal->id_jurnal, 'id_siswa' => $siswa->id_siswa],
                    ['status' => $data['jenis_absen'] === 'S' ? 'S' : 'I', 'keterangan' => strtoupper($data['jenis_absen']).($data['alasan'] ? ': '.$data['alasan'] : '')]
                );

                return DispenSiswa::create(array_merge($data, [
                    'id_guru_piket' => $guruPiket->id_guru,
                    'foto_surat' => $fotoSurat,
                    'id_jurnal' => $jurnal->id_jurnal,
                    'status_guru_piket' => 'disetujui',
                    'disetujui_guru_piket_pada' => now(),
                ]));
            });
        } catch (\Throwable $exception) {
            if ($fotoSurat) {
                Storage::disk('public')->delete($fotoSurat);
            }
            throw $exception;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Siswa berhasil diabsen dan surat berhasil disimpan.',
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
        $statusField = 'status_'.$role;
        $noteField = 'catatan_'.$role;
        $dateField = 'disetujui_'.$role.'_pada';
        $dispen->{$statusField} = $data['keputusan'];
        $dispen->{$noteField} = $data['catatan'] ?? null;
        $dispen->{$dateField} = $data['keputusan'] === 'disetujui' ? now() : null;
        $dispen->save();

        $url = URL::temporarySignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => $role]);

        return redirect()->to($url)->with('approval_message', 'Keputusan '.strtoupper($role).' berhasil disimpan.');
    }
}
