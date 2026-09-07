<?php

namespace App\Services;

use App\Models\DispenSiswa;
use App\Models\IzinGuru;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Mengambil URL endpoint pengiriman bot WA dari pengaturan admin.
     */
    public static function getEndpoint(): string
    {
        return trim(Pengaturan::get('wa_gateway_endpoint', 'http://127.0.0.1:3000/send-message'));
    }

    /**
     * Memeriksa apakah integrasi WA Gateway aktif di pengaturan.
     */
    public static function isAktif(): bool
    {
        return (string) Pengaturan::get('wa_gateway_aktif', '1') === '1';
    }

    /**
     * Menormalisasi nomor telepon menjadi format internasional (misal 0812 -> 62812).
     */
    public static function normalizePhoneNumber(?string $number): ?string
    {
        if (empty($number)) {
            return null;
        }

        // Hapus karakter non-angka
        $cleaned = preg_replace('/\D/', '', $number);
        if ($cleaned === '') {
            return null;
        }

        // Ubah 08xxx menjadi 628xxx
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Memeriksa status kesehatan server bot WA (misal di port 3000).
     */
    public static function checkBotStatus(): array
    {
        $endpoint = static::getEndpoint();
        $baseHost = preg_replace('#/send-message.*$#', '', $endpoint);
        $statusUrl = rtrim($baseHost, '/').'/status';

        try {
            $response = Http::timeout(3)->get($statusUrl);
            if ($response->successful()) {
                $data = $response->json();

                return [
                    'online' => true,
                    'status' => $data['status'] ?? 'connected',
                    'message' => 'Server bot WhatsApp aktif dan terhubung.',
                ];
            }

            return [
                'online' => false,
                'status' => 'http_error',
                'message' => 'Server bot merespon dengan kode: '.$response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'online' => false,
                'status' => 'unreachable',
                'message' => 'Server bot tidak dapat dihubungi di '.$statusUrl.' ('.$e->getMessage().')',
            ];
        }
    }

    /**
     * Mengirim pesan WhatsApp ke nomor tujuan melalui REST API Bot.
     */
    public static function kirimPesan(string $nomor, string $pesan): array
    {
        if (! static::isAktif()) {
            return [
                'success' => false,
                'error' => 'Fitur notifikasi bot WhatsApp sedang dinonaktifkan di pengaturan.',
            ];
        }

        $formattedNumber = static::normalizePhoneNumber($nomor);
        if (! $formattedNumber) {
            return [
                'success' => false,
                'error' => 'Nomor tujuan tidak valid atau kosong.',
            ];
        }

        $endpoint = static::getEndpoint();

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, [
                    'number' => $formattedNumber,
                    'message' => $pesan,
                ]);

            if ($response->successful()) {
                $resData = $response->json();
                $isSuccess = $resData['status'] ?? true;

                return [
                    'success' => (bool) $isSuccess,
                    'message' => $resData['message'] ?? 'Pesan berhasil dikirim.',
                    'response' => $resData,
                ];
            }

            Log::warning('Gagal kirim WA via bot: '.$response->body());

            return [
                'success' => false,
                'error' => 'Server bot mengembalikan status: '.$response->status().' - '.$response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Exception saat mengirim pesan WA: '.$e->getMessage());

            return [
                'success' => false,
                'error' => 'Tidak dapat terhubung ke bot WhatsApp: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Mengirim notifikasi WhatsApp ke Waka Kesiswaan untuk Dispensasi / Izin Siswa.
     */
    public static function kirimNotifikasiDispenSiswa(DispenSiswa $dispen, string $linkWaka): array
    {
        $nomorWaka = Pengaturan::get('wa_nomor_waka_kesiswaan', '');
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');

        $dispen->loadMissing(['siswa.kelas', 'guruPiket']);

        $namaSiswa = $dispen->siswa?->nama_siswa ?? 'Siswa';
        $namaKelas = $dispen->siswa?->kelas?->nama_kelas ?? '-';
        $jenis = match ($dispen->jenis_absen) {
            'S' => 'Sakit',
            'I' => 'Izin',
            default => 'Dispensasi',
        };
        $tanggal = date('d-m-Y', strtotime($dispen->tanggal_dispen));
        $alasan = $dispen->alasan ?: '-';
        $namaPiket = $dispen->guruPiket?->nama_guru ?? 'Guru Piket';

        $pesan = "🔔 *NOTIFIKASI {$namaSekolah}*\n"
            ."*PERMINTAAN PERSETUJUAN ".strtoupper($jenis)." SISWA*\n\n"
            ."Yth. *Bapak/Ibu Waka Kesiswaan*,\n"
            ."Terdapat permohonan surat baru yang diajukan oleh Guru Piket:\n\n"
            ."• *Nama Siswa:* {$namaSiswa}\n"
            ."• *Kelas:* {$namaKelas}\n"
            ."• *Jenis:* {$jenis}\n"
            ."• *Tanggal:* {$tanggal}\n"
            ."• *Alasan:* {$alasan}\n"
            ."• *Guru Piket:* {$namaPiket}\n\n"
            ."Silakan klik link berikut untuk melihat surat & memberikan persetujuan:\n"
            ."🔗 {$linkWaka}\n\n"
            ."_Pesan otomatis dari Sistem PresensiKita._";

        $hasilKirim = ! empty($nomorWaka) ? static::kirimPesan($nomorWaka, $pesan) : ['success' => false, 'error' => 'Nomor WA Waka Kesiswaan belum diatur.'];

        return [
            'sent' => $hasilKirim['success'],
            'target_phone' => $nomorWaka,
            'wa_me_link' => static::generateWaMeLink($nomorWaka, $pesan),
            'pesan' => $pesan,
            'details' => $hasilKirim,
        ];
    }

    /**
     * Mengirim notifikasi WhatsApp ke Kepala Sekolah dan Waka SDM untuk Izin Guru.
     */
    public static function kirimNotifikasiIzinGuru(IzinGuru $izin, string $linkKepsek, string $linkWaka): array
    {
        $nomorKepsek = Pengaturan::get('wa_nomor_kepsek', '');
        $nomorWakaSdm = Pengaturan::get('wa_nomor_waka_sdm', '');
        $namaSekolah = Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');

        $izin->loadMissing(['guru', 'guruPiket']);

        $namaGuru = $izin->guru?->nama_guru ?? 'Guru';
        $tanggal = date('d-m-Y', strtotime($izin->tanggal_izin));
        $alasan = $izin->alasan ?: '-';
        $namaPiket = $izin->guruPiket?->nama_guru ?? 'Guru Piket';

        // Pesan untuk Kepsek
        $pesanKepsek = "🔔 *NOTIFIKASI {$namaSekolah}*\n"
            ."*PERMINTAAN PERSETUJUAN IZIN GURU*\n\n"
            ."Yth. *Bapak/Ibu Kepala Sekolah*,\n"
            ."Terdapat permohonan izin guru yang diajukan oleh Guru Piket:\n\n"
            ."• *Nama Guru:* {$namaGuru}\n"
            ."• *Tanggal Izin:* {$tanggal}\n"
            ."• *Alasan:* {$alasan}\n"
            ."• *Guru Piket:* {$namaPiket}\n\n"
            ."Silakan klik link berikut untuk melihat detail surat & memberikan persetujuan:\n"
            ."🔗 {$linkKepsek}\n\n"
            ."_Pesan otomatis dari Sistem PresensiKita._";

        // Pesan untuk Waka SDM
        $pesanWaka = "🔔 *NOTIFIKASI {$namaSekolah}*\n"
            ."*PERMINTAAN PERSETUJUAN IZIN GURU*\n\n"
            ."Yth. *Bapak/Ibu Waka SDM / Kurikulum*,\n"
            ."Terdapat permohonan izin guru yang diajukan oleh Guru Piket:\n\n"
            ."• *Nama Guru:* {$namaGuru}\n"
            ."• *Tanggal Izin:* {$tanggal}\n"
            ."• *Alasan:* {$alasan}\n"
            ."• *Guru Piket:* {$namaPiket}\n\n"
            ."Silakan klik link berikut untuk melihat detail surat & memberikan persetujuan:\n"
            ."🔗 {$linkWaka}\n\n"
            ."_Pesan otomatis dari Sistem PresensiKita._";

        $hasilKepsek = ! empty($nomorKepsek)
            ? static::kirimPesan($nomorKepsek, $pesanKepsek)
            : ['success' => false, 'error' => 'Nomor WA Kepala Sekolah belum diatur.'];

        $hasilWaka = ! empty($nomorWakaSdm)
            ? static::kirimPesan($nomorWakaSdm, $pesanWaka)
            : ['success' => false, 'error' => 'Nomor WA Waka SDM belum diatur.'];

        return [
            'kepsek' => [
                'sent' => $hasilKepsek['success'],
                'target_phone' => $nomorKepsek,
                'wa_me_link' => static::generateWaMeLink($nomorKepsek, $pesanKepsek),
                'details' => $hasilKepsek,
            ],
            'waka_sdm' => [
                'sent' => $hasilWaka['success'],
                'target_phone' => $nomorWakaSdm,
                'wa_me_link' => static::generateWaMeLink($nomorWakaSdm, $pesanWaka),
                'details' => $hasilWaka,
            ],
        ];
    }

    /**
     * Membuat URL https://wa.me/xxx?text=yyy untuk pengiriman manual.
     */
    public static function generateWaMeLink(?string $number, string $message): string
    {
        $normalized = static::normalizePhoneNumber($number);
        if (! $normalized) {
            return 'https://wa.me/?text='.rawurlencode($message);
        }

        return 'https://wa.me/'.$normalized.'?text='.rawurlencode($message);
    }
}
