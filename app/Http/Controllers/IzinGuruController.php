<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIzinGuruRequest;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\IzinGuru;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class IzinGuruController extends Controller
{
    public function form()
    {
        $isGuruPiket = session('auth_role') === 'guru_piket' || (session('auth_guru_id') && \App\Models\GuruPiket::where('id_guru', session('auth_guru_id'))->whereDate('tanggal', now()->toDateString())->exists());
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

        $izinGuruMenungguPiket = $isGuruPiket
            ? IzinGuru::with('guru')->menungguPiket()->latest()->get()
            : collect();

        $view = $isGuruPiket ? 'struktural.pages.izin-guru' : 'guru.izin-guru';

        return view($view, [
            'isGuruPiket' => $isGuruPiket,
            'guruAktif' => $guruAktif,
            'izinGuruTerbaru' => $izinGuruTerbaru,
            'izinGuruMenungguPiket' => $izinGuruMenungguPiket,
            'sidebar' => 'partials.sidebar_guru',
            'profilUpdateUrl' => route('guru.profil.update'),
        ]);
    }

    public function store(StoreIzinGuruRequest $request)
    {
        $data = $request->validated();

        $authGuruId = session('auth_guru_id');
        $authGuru = Guru::where('id_guru', $authGuruId)
            ->where('is_aktif', 1)
            ->first();

        if (! $authGuru) {
            abort(403);
        }

        $isGuruPiket = session('auth_role') === 'guru_piket' || (\App\Models\GuruPiket::where('id_guru', $authGuruId)->whereDate('tanggal', now()->toDateString())->exists());
        
        // Cek apakah diajukan langsung oleh guru piket melalui form piket
        $isInputLangsungPiket = $isGuruPiket && $request->filled('id_guru') && ! $request->routeIs('guru.izin-guru.store');
        $requestedGuruId = $isInputLangsungPiket
            ? $request->id_guru
            : $authGuruId;

        $guru = Guru::where('id_guru', $requestedGuruId)
            ->where('is_admin', 0)
            ->where('is_aktif', 1)
            ->firstOrFail();

        $fotoSurat = $request->hasFile('foto_surat')
            ? $request->file('foto_surat')->store('surat-izin-guru', 'public')
            : null;

        if ($isInputLangsungPiket) {
            $izin = IzinGuru::create([
                'id_guru' => $guru->id_guru,
                'id_guru_piket' => $authGuru->id_guru,
                'tanggal_izin' => $data['tanggal_izin'],
                'alasan' => $data['alasan'],
                'foto_surat' => $fotoSurat,
                'status_konfirmasi_piket' => 'dikonfirmasi',
                'dikonfirmasi_piket_pada' => now(),
            ]);

            $link = URL::temporarySignedRoute(
                'izin-guru.public',
                now()->addDays(2),
                ['izin' => $izin->id_izin_guru],
                false
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

        // Permintaan izin oleh Guru Pengajar biasa: masuk ke antrean konfirmasi Guru Piket dulu
        $izin = IzinGuru::create([
            'id_guru' => $guru->id_guru,
            'id_guru_piket' => null,
            'tanggal_izin' => $data['tanggal_izin'],
            'alasan' => $data['alasan'],
            'foto_surat' => $fotoSurat,
            'status_konfirmasi_piket' => 'menunggu',
            'dikonfirmasi_piket_pada' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permohonan izin berhasil diajukan dan masuk ke Guru Piket. Menunggu konfirmasi Guru Piket sebelum diteruskan ke Kepala Sekolah & Waka SDM.',
            'menunggu_piket' => true,
        ]);
    }

    public function konfirmasiPiket(Request $request, IzinGuru $izin)
    {
        $authGuruId = session('auth_guru_id');
        $authGuru = Guru::where('id_guru', $authGuruId)
            ->where('is_aktif', 1)
            ->first();

        if (! $authGuru) {
            abort(403);
        }

        $isGuruPiket = session('auth_role') === 'guru_piket' || (\App\Models\GuruPiket::where('id_guru', $authGuruId)->whereDate('tanggal', now()->toDateString())->exists());
        if (! $isGuruPiket) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya Guru Piket yang dapat mengonfirmasi izin.',
                ], 403);
            }
            abort(403, 'Hanya Guru Piket yang dapat mengonfirmasi izin.');
        }

        if ($izin->isDikonfirmasiPiket()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Izin ini sudah dikonfirmasi sebelumnya.',
                ], 422);
            }
            return back()->with('error', 'Izin ini sudah dikonfirmasi sebelumnya.');
        }

        $validated = $request->validate([
            'catatan_piket' => ['nullable', 'string', 'max:500'],
        ]);

        $izin->update([
            'status_konfirmasi_piket' => 'dikonfirmasi',
            'id_guru_piket' => $authGuru->id_guru,
            'dikonfirmasi_piket_pada' => now(),
            'catatan_piket' => $validated['catatan_piket'] ?? null,
        ]);

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

        $message = 'Izin guru berhasil dikonfirmasi dan diteruskan ke Kepala Sekolah & Waka SDM via WhatsApp.';
        if ($waSentCount === 2) {
            $message .= ' Notifikasi WhatsApp otomatis berhasil terkirim ke kedua penerima.';
        } elseif ($waSentCount === 1) {
            $message .= ' Notifikasi WhatsApp berhasil terkirim ke salah satu penerima.';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'kepsek_link' => $kepsekLink,
                'waka_link' => $wakaLink,
                'wa_notification' => $waNotification,
            ]);
        }

        return back()->with('success', $message);
    }

    public function publicShow(Request $request, IzinGuru $izin, ?string $role = null)
    {
        if (! $izin->isDikonfirmasiPiket()) {
            abort(403, 'Permohonan izin ini belum dikonfirmasi oleh Guru Piket.');
        }

        if ($role !== null && ! in_array($role, ['kepsek', 'waka'], true)) {
            abort(404);
        }

        if ($role === 'kepsek') {
            return view('izin_guru_kepsek', [
                'izin' => $izin->load(['guru', 'guruPiket']),
                'approvalUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek'], false),
            ]);
        }

        if ($role === 'waka') {
            return view('izin_guru_waka', [
                'izin' => $izin->load(['guru', 'guruPiket']),
                'approvalUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka'], false),
            ]);
        }

        return view('izin_guru_public', [
            'izin' => $izin->load(['guru', 'guruPiket']),
            'role' => $role,
            'kepsekUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek'], false),
            'wakaUrl' => URL::temporarySignedRoute('izin-guru.approve', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka'], false),
        ]);
    }

    public function approve(Request $request, IzinGuru $izin, string $role)
    {
        if (! $izin->isDikonfirmasiPiket()) {
            abort(403, 'Permohonan izin ini belum dikonfirmasi oleh Guru Piket.');
        }

        if (! in_array($role, ['kepsek', 'waka'], true)) {
            abort(404);
        }

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
                    $filename = 'ttd_izin_'.$role.'_'.$izin->id_izin_guru.'_'.time().'_'.Str::random(6).'.'.$type;
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

        DB::transaction(function () use ($izin, $statusField, $noteField, $dateField, $ttdField, $tandaTanganPath, $data) {
            $izin->{$statusField} = $data['keputusan'];
            $izin->{$noteField} = $data['catatan'] ?? null;
            $izin->{$dateField} = $data['keputusan'] === 'disetujui' ? now() : null;
            $izin->{$ttdField} = $tandaTanganPath;
            $izin->save();

            if ($izin->isDisetujui()) {
                $this->syncJurnalIzin($izin);
            }
        });

        $resultUrl = URL::temporarySignedRoute(
            'izin-guru.public.role',
            now()->addDays(2),
            ['izin' => $izin->id_izin_guru, 'role' => $role],
            false
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
