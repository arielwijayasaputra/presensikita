<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDispenSiswaRequest;
use App\Models\DispenSiswa;
use App\Models\Guru;
use App\Models\GuruPiket;
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
use Illuminate\Support\Str;

class DispenSiswaController extends Controller
{
    public function form()
    {
        $isGuruPiket = session('auth_role') === 'guru_piket' || (session('auth_guru_id') && GuruPiket::where('id_guru', session('auth_guru_id'))->whereDate('tanggal', now()->toDateString())->exists());
        abort_unless($isGuruPiket, 403, 'Akses khusus Guru Piket yang bertugas.');

        return redirect()->to(route('guru.index').'#dispen-siswa');
    }

    public function storeAbsensi(Request $request)
    {
        $isGuruPiket = session('auth_role') === 'guru_piket' || (session('auth_guru_id') && GuruPiket::where('id_guru', session('auth_guru_id'))->whereDate('tanggal', now()->toDateString())->exists());
        abort_unless($isGuruPiket, 403, 'Akses khusus Guru Piket yang bertugas.');

        return $this->store(app(StoreDispenSiswaRequest::class));
    }

    public function store(StoreDispenSiswaRequest $request)
    {
        $isGuruPiket = session('auth_role') === 'guru_piket' || (session('auth_guru_id') && GuruPiket::where('id_guru', session('auth_guru_id'))->whereDate('tanggal', now()->toDateString())->exists());
        abort_unless($isGuruPiket, 403, 'Akses khusus Guru Piket yang bertugas.');
        $data = $request->validated();

        $idSiswaList = (array) $data['id_siswa'];
        $guruPiket = Guru::where('id_guru', session('auth_guru_id'))->where('is_aktif', 1)->firstOrFail();
        $fotoSurat = $request->hasFile('foto_surat') ? $request->file('foto_surat')->store('surat-dispen', 'public') : null;
        $kodeDispen = 'DSP-'.date('Ymd').'-'.strtoupper(Str::random(6));

        try {
            $createdDispens = DB::transaction(function () use ($data, $idSiswaList, $guruPiket, $fotoSurat, $kodeDispen) {
                $nowTime = now()->format('H:i:s');
                $dispens = collect();

                foreach ($idSiswaList as $idSiswa) {
                    $siswa = Siswa::where('id_siswa', $idSiswa)->where('is_aktif', 1)->firstOrFail();

                    // Cari jurnal yang sudah ada pada jam yang sedang berlangsung (jika ada)
                    $jurnal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                        ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                        ->whereNull('jadwal_mengajar.deleted_at')
                        ->whereNull('jam_pelajaran.deleted_at')
                        ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                        ->whereDate('jurnal_kelas.tanggal', $data['tanggal_dispen'])
                        ->whereTime('jam_pelajaran.jam_mulai', '<=', $nowTime)
                        ->whereTime('jam_pelajaran.jam_selesai', '>', $nowTime)
                        ->select('jurnal_kelas.*')
                        ->first();

                    // Update absensi di semua jurnal yang sudah ada hari ini mulai dari jam dispen
                    $existingJurnals = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                        ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                        ->whereNull('jadwal_mengajar.deleted_at')
                        ->whereNull('jam_pelajaran.deleted_at')
                        ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                        ->whereDate('jurnal_kelas.tanggal', $data['tanggal_dispen'])
                        ->whereTime('jam_pelajaran.jam_selesai', '>', $nowTime)
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

                    $dispen = DispenSiswa::create([
                        'kode_dispen' => $kodeDispen,
                        'id_siswa' => $siswa->id_siswa,
                        'id_guru_piket' => $guruPiket->id_guru,
                        'tanggal_dispen' => $data['tanggal_dispen'],
                        'alasan' => $data['alasan'] ?? null,
                        'jenis_absen' => $data['jenis_absen'],
                        'foto_surat' => $fotoSurat,
                        'id_jurnal' => $jurnal?->id_jurnal,
                        'status_guru_piket' => 'disetujui',
                        'disetujui_guru_piket_pada' => now(),
                    ]);

                    $dispens->push($dispen);
                }

                return $dispens;
            });
        } catch (\Throwable $exception) {
            if ($fotoSurat) {
                Storage::disk('public')->delete($fotoSurat);
            }
            throw $exception;
        }

        $primaryDispen = $createdDispens->first();
        $wakaLink = WhatsAppService::generateLanSignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $primaryDispen->id_dispen_siswa, 'role' => 'waka']);

        $waNotification = WhatsAppService::kirimNotifikasiDispenSiswa($createdDispens, $wakaLink);

        $jumlahSiswa = $createdDispens->count();
        $message = $jumlahSiswa > 1
            ? "{$jumlahSiswa} siswa berhasil diabsen dan surat berhasil disimpan."
            : 'Siswa berhasil diabsen dan surat berhasil disimpan.';

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

        $allDispens = $dispen->kode_dispen
            ? DispenSiswa::with(['siswa.kelas', 'guruPiket'])->where('kode_dispen', $dispen->kode_dispen)->get()
            : collect([$dispen->loadMissing(['siswa.kelas', 'guruPiket'])]);

        return view('dispen_siswa_public', [
            'dispen' => $dispen->loadMissing(['siswa.kelas', 'guruPiket']),
            'allDispens' => $allDispens,
            'role' => $role,
            'status' => $dispen->status_waka,
            'approvalUrl' => URL::temporarySignedRoute('dispen-siswa.approve', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => $role], false),
        ]);
    }

    public function approve(Request $request, DispenSiswa $dispen, string $role)
    {
        abort_unless($role === 'waka', 404);
        $data = $request->validate([
            'keputusan' => ['required', 'in:disetujui,ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'tanda_tangan' => ['required', 'string'],
        ], [
            'tanda_tangan.required' => 'Tanda tangan digital wajib diisi sebelum menyimpan keputusan.',
        ]);

        $tandaTanganPath = null;
        if ($request->filled('tanda_tangan') && str_starts_with($request->tanda_tangan, 'data:image')) {
            $base64Ttd = $request->tanda_tangan;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Ttd, $type)) {
                $base64Ttd = substr($base64Ttd, strpos($base64Ttd, ',') + 1);
                $type = strtolower($type[1]);
                if (! in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $type = 'png';
                }
                $decodedTtd = base64_decode($base64Ttd);
                if ($decodedTtd !== false) {
                    $filename = 'ttd_dispen_waka_'.$dispen->id_dispen_siswa.'_'.time().'_'.Str::random(6).'.'.$type;
                    Storage::disk('public')->put('tanda-tangan-struktural/'.$filename, $decodedTtd);
                    $tandaTanganPath = 'tanda-tangan-struktural/'.$filename;
                }
            }
        }

        if (! $tandaTanganPath) {
            return back()->withErrors(['tanda_tangan' => 'Tanda tangan digital tidak valid atau gagal diproses.']);
        }

        $statusField = 'status_'.$role;
        $noteField = 'catatan_'.$role;
        $dateField = 'disetujui_'.$role.'_pada';
        $ttdField = 'tanda_tangan_'.$role;

        $targetQuery = $dispen->kode_dispen
            ? DispenSiswa::where('kode_dispen', $dispen->kode_dispen)
            : DispenSiswa::where('id_dispen_siswa', $dispen->id_dispen_siswa);

        $targetQuery->update([
            $statusField => $data['keputusan'],
            $noteField => $data['catatan'] ?? null,
            $dateField => $data['keputusan'] === 'disetujui' ? now() : null,
            $ttdField => $tandaTanganPath,
        ]);

        $url = URL::temporarySignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $dispen->id_dispen_siswa, 'role' => $role], false);

        return redirect()->to($url)->with('approval_message', 'Keputusan '.strtoupper($role).' berhasil disimpan.');
    }
}
