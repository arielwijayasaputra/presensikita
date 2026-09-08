<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class IzinGuruController extends Controller
{
    public function form()
    {
        $isGuruPiket = session('auth_role') === 'guru_piket';
        if (! $isGuruPiket) {
            return redirect()->to(route('guru.index').'#izin-guru');
        }

        $guruAktif = Guru::where('is_admin', 0)->where('is_aktif', 1)->orderBy('nama_guru')->get();
        $izinGuruTerbaru = IzinGuru::with('guru')
            ->where(
                $isGuruPiket ? 'id_guru_piket' : 'id_guru',
                session('auth_guru_id')
            )
            ->latest()
            ->limit(20)
            ->get();

        $view = $isGuruPiket ? 'struktural.pages.izin-guru' : 'guru.izin-guru';

        return view($view, [
            'isGuruPiket' => $isGuruPiket,
            'guruAktif' => $guruAktif,
            'izinGuruTerbaru' => $izinGuruTerbaru,
            'sidebar' => $isGuruPiket ? 'partials.sidebar_struktural' : 'partials.sidebar_guru',
            'profilUpdateUrl' => $isGuruPiket ? route('struktural.profil.update') : route('guru.profil.update'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_guru' => ['nullable', 'integer', 'exists:guru,id_guru'],
            'tanggal_izin' => ['required', 'date'],
            'alasan' => ['required', 'string', 'max:2000'],
            'foto_surat' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $guruPiketId = session('auth_guru_id');
        $guruPiket = Guru::where('id_guru', $guruPiketId)
            ->where('is_aktif', 1)
            ->first();

        if (! $guruPiket) {
            abort(403);
        }

        $requestedGuruId = session('auth_role') === 'guru_piket'
            ? $request->id_guru
            : $guruPiketId;
        $guru = Guru::where('id_guru', $requestedGuruId)
            ->where('is_admin', 0)
            ->where('is_aktif', 1)
            ->firstOrFail();

        $fotoSurat = $request->hasFile('foto_surat')
            ? $request->file('foto_surat')->store('surat-izin-guru', 'public')
            : null;

        $izin = IzinGuru::create([
            'id_guru' => $guru->id_guru,
            'id_guru_piket' => $guruPiket->id_guru,
            'tanggal_izin' => $data['tanggal_izin'],
            'alasan' => $data['alasan'],
            'foto_surat' => $fotoSurat,
        ]);

        $link = URL::temporarySignedRoute(
            'izin-guru.public',
            now()->addDays(2),
            ['izin' => $izin->id_izin_guru]
        );

        $kepsekLink = WhatsAppService::generateLanSignedRoute('izin-guru.public.role', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek']);
        $wakaLink = WhatsAppService::generateLanSignedRoute('izin-guru.public.role', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka']);

        $waNotification = WhatsAppService::kirimNotifikasiIzinGuru($izin, $kepsekLink, $wakaLink);

        $waSentCount = 0;
        if (! empty($waNotification['kepsek']['sent'])) {
            $waSentCount++;
        }
        if (! empty($waNotification['waka_sdm']['sent'])) {
            $waSentCount++;
        }

        $message = 'Permintaan izin berhasil dibuat.';
        if ($waSentCount === 2) {
            $message .= ' Notifikasi WhatsApp otomatis berhasil dikirim ke Kepala Sekolah dan Waka SDM.';
        } elseif ($waSentCount === 1) {
            $message .= ' Notifikasi WhatsApp berhasil dikirim ke salah satu penerima.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'link' => $link,
            'kepsek_link' => $kepsekLink,
            'waka_link' => $wakaLink,
            'wa_notification' => $waNotification,
        ]);
    }

    public function publicShow(Request $request, IzinGuru $izin, ?string $role = null)
    {
        if ($role !== null && ! in_array($role, ['kepsek', 'waka'], true)) {
            abort(404);
        }

        if ($role === 'kepsek') {
            return view('izin_guru_kepsek', [
                'izin' => $izin->load(['guru', 'guruPiket']),
                'approvalUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek']),
            ]);
        }

        if ($role === 'waka') {
            return view('izin_guru_waka', [
                'izin' => $izin->load(['guru', 'guruPiket']),
                'approvalUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka']),
            ]);
        }

        return view('izin_guru_public', [
            'izin' => $izin->load(['guru', 'guruPiket']),
            'role' => $role,
            'kepsekUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek']),
            'wakaUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka']),
        ]);
    }

    public function approve(Request $request, IzinGuru $izin, string $role)
    {
        if (! in_array($role, ['kepsek', 'waka'], true)) {
            abort(404);
        }

        $data = $request->validate([
            'keputusan' => ['required', 'in:disetujui,ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusField = 'status_'.$role;
        $noteField = 'catatan_'.$role;
        $dateField = 'disetujui_'.$role.'_pada';

        DB::transaction(function () use ($izin, $statusField, $noteField, $dateField, $data) {
            $izin->{$statusField} = $data['keputusan'];
            $izin->{$noteField} = $data['catatan'] ?? null;
            $izin->{$dateField} = $data['keputusan'] === 'disetujui' ? now() : null;
            $izin->save();

            if ($izin->isDisetujui()) {
                $this->syncJurnalIzin($izin);
            }
        });

        $resultUrl = URL::temporarySignedRoute(
            'izin-guru.public.role',
            now()->addDays(2),
            ['izin' => $izin->id_izin_guru, 'role' => $role]
        );

        return redirect()->to($resultUrl)
            ->with('approval_message', 'Keputusan '.strtoupper($role).' berhasil disimpan.');
    }

    private function syncJurnalIzin(IzinGuru $izin): void
    {
        $hari = Hari::getNamaHariFromAbbr(date('D', strtotime($izin->tanggal_izin->toDateString())));

        if (! $hari) {
            return;
        }

        $jadwals = DB::table('jadwal_mengajar')
            ->where('id_guru', $izin->id_guru)
            ->where('hari', $hari)
            ->whereNull('deleted_at')
            ->select('id_jadwal', 'id_kelas')
            ->get();

        foreach ($jadwals as $jadwal) {
            $jadwalId = $jadwal->id_jadwal;
            $kelasId = $jadwal->id_kelas;

            // Cari data absensi siswa sebelumnya untuk kelas ini
            // Prioritas 1: Jurnal lain di kelas yang sama pada hari yang sama (sebelum jam izin)
            // Prioritas 2: Jurnal terakhir sebelum hari ini di kelas yang sama
            $jurnalAcuan = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                ->where('jadwal_mengajar.id_kelas', $kelasId)
                ->where('jurnal_kelas.id_jadwal', '!=', $jadwalId)
                ->whereNull('jadwal_mengajar.deleted_at')
                ->where(function ($q) use ($izin) {
                    $q->whereDate('jurnal_kelas.tanggal', $izin->tanggal_izin)
                        ->orWhereDate('jurnal_kelas.tanggal', '<', $izin->tanggal_izin);
                })
                ->orderByDesc('jurnal_kelas.tanggal')
                ->orderByDesc('jurnal_kelas.waktu_input')
                ->select('jurnal_kelas.*')
                ->first();

            $totalSiswaKelas = Siswa::where('id_kelas', $kelasId)->where('is_aktif', 1)->count();
            $copiedTidakHadir = collect();

            if ($jurnalAcuan) {
                $copiedTidakHadir = JurnalSiswaTidakHadir::where('id_jurnal', $jurnalAcuan->id_jurnal)->get();
                $jumlahHadir = max(0, $totalSiswaKelas - $copiedTidakHadir->count());
            } else {
                // Jika belum ada acuan sama sekali, default semua siswa aktif hadir
                $jumlahHadir = $totalSiswaKelas;
            }

            $jurnal = JurnalKelas::where('id_jadwal', $jadwalId)
                ->whereDate('tanggal', $izin->tanggal_izin)
                ->first();

            if ($jurnal) {
                $jurnal->update([
                    'status_kehadiran_guru' => 'Tidak Hadir',
                    'materi' => 'Izin guru: '.$izin->alasan,
                    'jumlah_hadir' => $jumlahHadir,
                    'waktu_input' => now(),
                ]);
            } else {
                $jurnal = JurnalKelas::create([
                    'id_jadwal' => $jadwalId,
                    'id_guru' => $izin->id_guru,
                    'tanggal' => $izin->tanggal_izin,
                    'status_kehadiran_guru' => 'Tidak Hadir',
                    'materi' => 'Izin guru: '.$izin->alasan,
                    'jumlah_hadir' => $jumlahHadir,
                    'waktu_input' => now(),
                ]);
            }

            // Otomatis salin status siswa yang tidak hadir sesuai data absensi sebelumnya
            JurnalSiswaTidakHadir::withTrashed()->where('id_jurnal', $jurnal->id_jurnal)->forceDelete();
            foreach ($copiedTidakHadir as $th) {
                JurnalSiswaTidakHadir::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $th->id_siswa,
                    'status' => $th->status,
                    'keterangan' => $th->keterangan,
                ]);
            }
        }
    }
}
