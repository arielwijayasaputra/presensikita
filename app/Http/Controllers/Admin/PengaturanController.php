<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\TahunAjaran;
use App\Services\ProfilService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mengelola pengaturan sistem sekolah seperti nama sekolah, tahun ajaran, dan profil pengguna.
 */
class PengaturanController extends Controller
{
    public function __construct(private ProfilService $profilService) {}

    /**
     * Memperbarui pengaturan sistem sekolah (nama, tahun ajaran, semester, dan opsi absensi).
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|string',
        ], [
            'nama_sekolah.required' => 'Nama sekolah tidak boleh kosong.',
            'tahun_ajaran.required' => 'Tahun ajaran tidak boleh kosong.',
            'semester.required' => 'Semester tidak boleh kosong.',
        ]);

        Pengaturan::set('nama_sekolah', trim($request->nama_sekolah));
        Pengaturan::set('npsn', trim($request->npsn ?? ''));
        Pengaturan::set('kepsek', trim($request->kepsek ?? ''));
        Pengaturan::set('alamat', trim($request->alamat ?? ''));
        Pengaturan::set('email_sekolah', trim($request->email_sekolah ?? ''));
        Pengaturan::set('telepon_sekolah', trim($request->telepon_sekolah ?? ''));
        if ($request->filled('sistem_absensi')) {
            Pengaturan::set('sistem_absensi', trim($request->sistem_absensi));
        }
        Pengaturan::set('batas_waktu_jurnal', trim($request->batas_waktu_jurnal ?? '23:59'));
        Pengaturan::set('izin_edit_jurnal', $request->has('izin_edit_jurnal') ? '1' : '0');

        // Pengaturan Bot WhatsApp
        if ($request->has('wa_gateway_aktif')) {
            Pengaturan::set('wa_gateway_aktif', ($request->boolean('wa_gateway_aktif') || $request->wa_gateway_aktif === '1' || $request->wa_gateway_aktif === 1) ? '1' : '0');
        }
        if ($request->has('wa_gateway_endpoint') && $request->filled('wa_gateway_endpoint')) {
            Pengaturan::set('wa_gateway_endpoint', trim($request->wa_gateway_endpoint));
        }
        if ($request->has('wa_public_url')) {
            Pengaturan::set('wa_public_url', trim($request->wa_public_url ?? ''));
        }
        if ($request->has('wa_nomor_bot') && $request->filled('wa_nomor_bot')) {
            Pengaturan::set('wa_nomor_bot', trim($request->wa_nomor_bot));
        }
        if ($request->has('wa_nomor_waka_kesiswaan')) {
            Pengaturan::set('wa_nomor_waka_kesiswaan', trim($request->wa_nomor_waka_kesiswaan ?? ''));
        }
        if ($request->has('wa_nomor_waka_sdm')) {
            Pengaturan::set('wa_nomor_waka_sdm', trim($request->wa_nomor_waka_sdm ?? ''));
        }
        if ($request->has('wa_nomor_kepsek')) {
            Pengaturan::set('wa_nomor_kepsek', trim($request->wa_nomor_kepsek ?? ''));
        }

        $tahun = TahunAjaran::where('is_aktif', 1)->first() ?? TahunAjaran::first();
        if ($tahun) {
            $tahun->update([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester' => trim($request->semester),
            ]);
        } else {
            TahunAjaran::create([
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester' => trim($request->semester),
                'is_aktif' => 1,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan sistem berhasil diperbarui!',
            'data' => [
                'nama_sekolah' => trim($request->nama_sekolah),
                'tahun_ajaran' => trim($request->tahun_ajaran),
                'semester' => trim($request->semester),
                'sistem_absensi' => trim($request->sistem_absensi ?? Pengaturan::get('sistem_absensi')),
                'batas_waktu_jurnal' => trim($request->batas_waktu_jurnal ?? '23:59'),
            ],
        ]);
    }

    /**
     * Memperbarui pengaturan khusus nomor bot WhatsApp (Nomor Bot, Kepsek, Waka SDM, Waka Kesiswaan).
     *
     * @return JsonResponse
     */
    public function updateWa(Request $request)
    {
        $request->validate([
            'wa_nomor_bot' => 'nullable|string|max:30',
            'wa_nomor_kepsek' => 'nullable|string|max:30',
            'wa_nomor_waka_sdm' => 'nullable|string|max:30',
            'wa_nomor_waka_kesiswaan' => 'nullable|string|max:30',
        ]);

        if ($request->has('wa_gateway_aktif')) {
            Pengaturan::set('wa_gateway_aktif', $request->boolean('wa_gateway_aktif') ? '1' : '0');
        }
        if ($request->has('wa_public_url')) {
            Pengaturan::set('wa_public_url', trim($request->wa_public_url ?? ''));
        }
        if ($request->has('wa_nomor_bot')) {
            Pengaturan::set('wa_nomor_bot', trim($request->wa_nomor_bot ?? ''));
        }
        if ($request->has('wa_nomor_kepsek')) {
            Pengaturan::set('wa_nomor_kepsek', trim($request->wa_nomor_kepsek ?? ''));
        }
        if ($request->has('wa_nomor_waka_sdm')) {
            Pengaturan::set('wa_nomor_waka_sdm', trim($request->wa_nomor_waka_sdm ?? ''));
        }
        if ($request->has('wa_nomor_waka_kesiswaan')) {
            Pengaturan::set('wa_nomor_waka_kesiswaan', trim($request->wa_nomor_waka_kesiswaan ?? ''));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan nomor WhatsApp notifikasi berhasil diperbarui!',
            'data' => [
                'wa_public_url' => Pengaturan::get('wa_public_url', ''),
                'wa_nomor_bot' => Pengaturan::get('wa_nomor_bot', ''),
                'wa_nomor_kepsek' => Pengaturan::get('wa_nomor_kepsek', ''),
                'wa_nomor_waka_sdm' => Pengaturan::get('wa_nomor_waka_sdm', ''),
                'wa_nomor_waka_kesiswaan' => Pengaturan::get('wa_nomor_waka_kesiswaan', ''),
                'wa_gateway_aktif' => Pengaturan::get('wa_gateway_aktif', '1'),
            ],
        ]);
    }

    /**
     * Uji coba pengiriman pesan WhatsApp melalui bot.
     *
     * @return JsonResponse
     */
    public function testKirimWa(Request $request)
    {
        $request->validate([
            'target_phone' => 'required|string',
            'pesan' => 'nullable|string|max:1000',
        ], [
            'target_phone.required' => 'Nomor WhatsApp tujuan wajib diisi.',
        ]);

        $pesan = trim($request->pesan ?? '') ?: 'Halo, ini adalah pesan uji coba integrasi WhatsApp Bot PresensiKita.';
        $hasil = WhatsAppService::kirimPesan($request->target_phone, $pesan);

        if ($hasil['success']) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan uji coba berhasil dikirim ke nomor '.$request->target_phone,
                'details' => $hasil,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengirim pesan uji coba: '.($hasil['error'] ?? 'Terjadi kesalahan.'),
            'details' => $hasil,
        ], 422);
    }

    /**
     * Memeriksa status kesehatan server bot WhatsApp.
     *
     * @return JsonResponse
     */
    public function statusBotWa()
    {
        $status = WhatsAppService::checkBotStatus();
        if (! empty($status['user'])) {
            Pengaturan::set('wa_nomor_bot', (string) $status['user']);
        }

        return response()->json($status);
    }

    /**
     * Memulai server bot WhatsApp lokal di background.
     *
     * @return JsonResponse
     */
    public function startBotWa()
    {
        $hasil = WhatsAppService::startLocalBot();

        return response()->json($hasil);
    }

    /**
     * Memulai ulang / menghubungkan kembali bot WhatsApp.
     *
     * @return JsonResponse
     */
    public function restartBotWa()
    {
        $hasil = WhatsAppService::restartBot();

        return response()->json($hasil);
    }

    /**
     * Memutuskan koneksi bot WhatsApp (logout sesi aktif).
     *
     * @return JsonResponse
     */
    public function disconnectBotWa()
    {
        $hasil = WhatsAppService::disconnectBot();

        return response()->json($hasil);
    }

    /**
     * Mengambil QR code jika bot belum terhubung.
     *
     * @return JsonResponse
     */
    public function qrBotWa()
    {
        $qr = WhatsAppService::getQrCode();
        if (! empty($qr['user'])) {
            Pengaturan::set('wa_nomor_bot', (string) $qr['user']);
        }

        return response()->json($qr);
    }

    /**
     * Memperbarui profil dan kredensial pengguna (admin, satpam, atau guru) berdasarkan sesi aktif.
     */
    public function updateProfil(Request $request): JsonResponse
    {
        return $this->profilService->update($request);
    }
}
