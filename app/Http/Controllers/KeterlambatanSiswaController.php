<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKeterlambatanSiswaRequest;
use App\Models\Guru;
use App\Models\Hari;
use App\Models\JurnalKelas;
use App\Models\JurnalSiswaTidakHadir;
use App\Models\KeterlambatanSiswa;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KeterlambatanSiswaController extends Controller
{
    public function form()
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);

        return redirect()->to(route('gurupiket.index').'#siswa-terlambat');
    }

    public function store(StoreKeterlambatanSiswaRequest $request)
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);

        $data = $request->validated();

        // Format jam masuk to H:i:s
        if (strlen($data['jam_masuk']) === 5) {
            $data['jam_masuk'] .= ':00';
        }

        $siswa = Siswa::where('id_siswa', $data['id_siswa'])->where('is_aktif', 1)->firstOrFail();
        $guruPiket = Guru::where('id_guru', session('auth_guru_id'))->where('is_aktif', 1)->firstOrFail();
        $fotoSurat = $request->hasFile('foto_surat') ? $request->file('foto_surat')->store('surat-terlambat', 'public') : null;

        try {
            $keterlambatan = DB::transaction(function () use ($data, $siswa, $guruPiket, $fotoSurat) {
                $record = KeterlambatanSiswa::create([
                    'id_siswa' => $siswa->id_siswa,
                    'id_guru_piket' => $guruPiket->id_guru,
                    'tanggal' => $data['tanggal'],
                    'jam_masuk' => $data['jam_masuk'],
                    'jam_ke' => $data['jam_ke'],
                    'alasan' => $data['alasan'] ?? null,
                    'foto_surat' => $fotoSurat,
                    'status' => 'diizinkan',
                    'disetujui_pada' => now(),
                ]);

                // Cari guru pengajar yang mengajar di kelas siswa tersebut pada hari ini
                $hariMap = Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
                $carbonDate = Carbon::parse($data['tanggal']);
                $namaHari = $hariMap[$carbonDate->format('l')] ?? $carbonDate->format('l');

                // Notifikasi telat hanya untuk: (1) wali kelas siswa, (2) guru pengajar saat jam itu
                $waliKelasId = DB::table('kelas')
                    ->where('id_kelas', $siswa->id_kelas)
                    ->value('id_wali_kelas');

                $jamKeMasuk = (int) $data['jam_ke'];
                $guruPengajarSaatItuIds = DB::table('jadwal_mengajar')
                    ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                    ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                    ->where('jadwal_mengajar.hari', $namaHari)
                    ->where(function ($q) use ($jamKeMasuk) {
                        $q->where('jam_pelajaran.jam_ke', $jamKeMasuk)
                            ->orWhere('jam_pelajaran.jam_ke', $jamKeMasuk + 100);
                    })
                    ->whereNull('jadwal_mengajar.deleted_at')
                    ->whereNull('jam_pelajaran.deleted_at')
                    ->pluck('jadwal_mengajar.id_guru')
                    ->merge([$waliKelasId])
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $judulNotif = 'Siswa Terlambat: '.$siswa->nama_siswa.' ('.($siswa->kelas->nama_kelas ?? '-').')';
                $pesanNotif = 'Siswa '.$siswa->nama_siswa.' terlambat (datang jam '.substr($data['jam_masuk'], 0, 5).', mulai masuk jam ke-'.$data['jam_ke'].'). Alasan: '.($data['alasan'] ?: 'Tanpa keterangan').'. Diizinkan oleh: '.$guruPiket->nama_guru.'.';

                // Kirim notifikasi sistem khusus ke wali kelas dan guru pengajar saat itu
                foreach ($guruPengajarSaatItuIds as $gId) {
                    DB::table('notifikasi')->insert([
                        'id_guru' => $gId,
                        'id_kelas' => $siswa->id_kelas,
                        'judul' => $judulNotif,
                        'pesan' => $pesanNotif,
                        'tipe' => 'warning',
                        'is_read' => 0,
                        'created_at' => now(),
                    ]);
                }

                // Sinkronisasi otomatis ke Jurnal yang sudah ada hari ini
                $jurnalsWithJadwal = JurnalKelas::join('jadwal_mengajar', 'jurnal_kelas.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
                    ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
                    ->whereNull('jadwal_mengajar.deleted_at')
                    ->whereNull('jam_pelajaran.deleted_at')
                    ->where('jadwal_mengajar.id_kelas', $siswa->id_kelas)
                    ->whereDate('jurnal_kelas.tanggal', $data['tanggal'])
                    ->select('jurnal_kelas.*', 'jam_pelajaran.jam_ke')
                    ->get();

                $totalSiswaKelas = Siswa::where('id_kelas', $siswa->id_kelas)->where('is_aktif', 1)->count();

                foreach ($jurnalsWithJadwal as $jurnal) {
                    $normalizedJamKe = $jurnal->jam_ke >= 100 ? $jurnal->jam_ke - 100 : $jurnal->jam_ke;
                    $targetJamKe = (int) $data['jam_ke'];

                    if ($normalizedJamKe < $targetJamKe) {
                        // Jam SEBELUM siswa tiba: ubah Alpha ('A') menjadi Masuk Terlambat ('T')
                        $th = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)
                            ->where('id_siswa', $siswa->id_siswa)
                            ->first();

                        $keteranganTerlambat = 'Masuk Terlambat'.($data['alasan'] ? ': '.$data['alasan'] : '');

                        if ($th) {
                            if ($th->status === 'A') {
                                $th->update([
                                    'status' => 'T',
                                    'keterangan' => $keteranganTerlambat,
                                ]);
                            }
                        } else {
                            // Jika belum tercatat tidak hadir pada jam sebelum masuk, tandai sebagai Terlambat
                            JurnalSiswaTidakHadir::create([
                                'id_jurnal' => $jurnal->id_jurnal,
                                'id_siswa' => $siswa->id_siswa,
                                'status' => 'T',
                                'keterangan' => $keteranganTerlambat,
                            ]);
                        }
                    } else {
                        // Jam SAAT / SETELAH siswa tiba: pulihkan dari Alpha ('A') ke Hadir ('H')
                        $th = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)
                            ->where('id_siswa', $siswa->id_siswa)
                            ->first();

                        if ($th && $th->status === 'A') {
                            $th->forceDelete();
                        }
                    }

                    // Rekalkulasi jumlah hadir jurnal secara presisi
                    $countTidakHadir = JurnalSiswaTidakHadir::where('id_jurnal', $jurnal->id_jurnal)->count();
                    JurnalKelas::where('id_jurnal', $jurnal->id_jurnal)->update([
                        'jumlah_hadir' => max(0, $totalSiswaKelas - $countTidakHadir),
                    ]);
                }

                return $record;
            });
        } catch (\Throwable $exception) {
            if ($fotoSurat) {
                Storage::disk('public')->delete($fotoSurat);
            }
            throw $exception;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Perizinan siswa terlambat berhasil dicatat. Status absensi di jurnal otomatis diperbarui.',
            'data' => $keterlambatan,
        ]);
    }

    public function destroy($id)
    {
        abort_unless(session('auth_role') === 'guru_piket', 403);

        $keterlambatan = KeterlambatanSiswa::findOrFail($id);
        $keterlambatan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data perizinan keterlambatan berhasil dihapus.',
        ]);
    }
}
