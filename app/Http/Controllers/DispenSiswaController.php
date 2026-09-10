<?php

namespace App\Http\Controllers;

use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\WhatsAppService;
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
                $nowTime   = now()->format('H:i:s');
                $dayMap    = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu'];
                $hariIndo  = $dayMap[date('N', strtotime($data['tanggal_dispen']))] ?? 'Senin';
                $tahunAjaran = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();

                // Cari semua jadwal kelas hari ini yang aktif atau akan datang sejak dispen dibuat
                $jadwalsHariIni = DB::table('jadwal_mengajar')
                    ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                    ->whereNull('jadwal_mengajar.deleted_at')
                    ->whereNull('jam_pelajaran.deleted_at')
                    ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                    ->where('jadwal_mengajar.hari', $hariIndo)
                    ->when($tahunAjaran, fn ($q) => $q->where('jadwal_mengajar.id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran))
                    ->whereTime('jam_pelajaran.jam_selesai', '>', $nowTime)
                    ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_guru', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai')
                    ->orderBy('jam_pelajaran.jam_ke')
                    ->get();

                $jadwalAktif = $jadwalsHariIni->first(fn ($item) => $nowTime >= $item->jam_mulai && $nowTime < $item->jam_selesai) ?? $jadwalsHariIni->first();

                // Dapatkan atau buat jurnal untuk jadwal aktif saat ini
                if ($jadwalAktif) {
                    $jurnal = JurnalKelas::where('id_jadwal', $jadwalAktif->id_jadwal)
                        ->whereDate('tanggal', $data['tanggal_dispen'])
                        ->first();

                    if (! $jurnal) {
                        $jurnal = JurnalKelas::create([
                            'id_jadwal' => $jadwalAktif->id_jadwal,
                            'id_guru'   => $jadwalAktif->id_guru ?? $guruPiket->id_guru,
                            'tanggal'   => $data['tanggal_dispen'],
                            'status_kehadiran_guru' => 'Hadir',
                            'materi'    => 'Absensi Guru Piket',
                            'jumlah_hadir' => Siswa::where('id_kelas', $siswa->id_kelas)->where('is_aktif', 1)->count(),
                            'waktu_input' => now(),
                        ]);
                    }
                } else {
                    $jurnal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                        ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                        ->whereDate('jurnal_kelas.tanggal', $data['tanggal_dispen'])
                        ->select('jurnal_kelas.*')
                        ->orderByDesc('jurnal_kelas.waktu_input')
                        ->first();
                }

                if (! $jurnal) {
                    $idJadwal = DB::table('jadwal_mengajar')
                        ->whereNull('deleted_at')
                        ->where('id_kelas', $siswa->id_kelas)
                        ->where('hari', $hariIndo)
                        ->when($tahunAjaran, fn ($q) => $q->where('id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran))
                        ->value('id_jadwal');

                    if (! $idJadwal) {
                        abort(422, 'Jadwal kelas belum tersedia untuk tanggal tersebut.');
                    }

                    $jurnal = JurnalKelas::create([
                        'id_jadwal' => $idJadwal,
                        'id_guru'   => $guruPiket->id_guru,
                        'tanggal'   => $data['tanggal_dispen'],
                        'status_kehadiran_guru' => 'Hadir',
                        'materi'    => 'Absensi Guru Piket',
                        'jumlah_hadir' => Siswa::where('id_kelas', $siswa->id_kelas)->where('is_aktif', 1)->count(),
                        'waktu_input' => now(),
                    ]);
                }

                // Update absensi di semua jurnal yang ada hari ini mulai dari jam dispen
                $existingJurnals = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                    ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                    ->whereDate('jurnal_kelas.tanggal', $data['tanggal_dispen'])
                    ->pluck('jurnal_kelas.id_jurnal');

                foreach ($existingJurnals as $jId) {
                    $existingTh = JurnalSiswaTidakHadir::where('id_jurnal', $jId)->where('id_siswa', $siswa->id_siswa)->first();
                    if (! $existingTh) {
                        JurnalKelas::where('id_jurnal', $jId)->decrement('jumlah_hadir');
                    }
                    JurnalSiswaTidakHadir::updateOrCreate(
                        ['id_jurnal' => $jId, 'id_siswa' => $siswa->id_siswa],
                        ['status' => $data['jenis_absen'], 'keterangan' => strtoupper($data['jenis_absen']).($data['alasan'] ? ': '.$data['alasan'] : '')]
                    );
                }

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

        $wakaLink = WhatsAppService::generateLanSignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => 'waka']);

        $waNotification = WhatsAppService::kirimNotifikasiDispenSiswa($dispen, $wakaLink);

        $message = 'Siswa berhasil diabsen dan surat berhasil disimpan.';
        if (! empty($waNotification['sent'])) {
            $message .= ' Notifikasi WhatsApp otomatis telah terkirim ke Waka Kesiswaan.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'waka_link' => $wakaLink,
            'wa_notification' => $waNotification,
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
