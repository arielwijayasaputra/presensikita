<div class="page-content page-anim" id="page-guru-piket" style="display:block">
    <div class="greeting-row">
        <div class="greeting-text">
            <h2>Dashboard Guru Piket</h2>
            <p>{{ $hariIni }}, {{ now()->translatedFormat('d F Y') }} · {{ $namaSekolah }}</p>
        </div>
    </div>

    <div class="alert-card alert-piket" style="background:linear-gradient(135deg,#fff7ed,#fffbeb);border-color:#fdba74;margin-bottom:22px">
        <div class="alert-icon" style="background:#f97316">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="alert-text">
            <p>Anda bertugas sebagai Guru Piket hari ini</p>
            <span>Gunakan informasi jadwal di bawah untuk membantu pemantauan kegiatan belajar mengajar.</span>
        </div>
    </div>

    <div class="stat-cards" style="margin-bottom:24px">
        <div class="stat-card"><div class="stat-icon" style="background:#fff7ed;color:#ea580c"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div><div class="stat-label">Total Jadwal</div><div class="stat-value">{{ $totalJadwalPiketHariIni ?? $totalJadwalHariIni }}</div><div class="stat-sub">hari ini</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#eff6ff;color:#2563eb"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/></svg></div><div><div class="stat-label">Kelas Terjadwal</div><div class="stat-value">{{ $totalKelasPiketHariIni ?? $totalKelasHariIni }}</div><div class="stat-sub">kelas</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg></div><div><div class="stat-label">Guru Mengajar</div><div class="stat-value">{{ $totalGuruPiketHariIni ?? $totalGuruHariIni }}</div><div class="stat-sub">guru</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#fef2f2;color:#dc2626"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 16 14"/></svg></div><div><div class="stat-label">Jam Aktif</div><div class="stat-value" style="font-size:15px;font-weight:800">{{ $jamAktif ? (substr($jamAktif->jam_mulai, 0, 5) . ' - ' . substr($jamAktif->jam_selesai, 0, 5)) : '-' }}</div><div class="stat-sub">sesi waktu saat ini</div></div></div>
    </div>

    @if(isset($izinGuruMenungguPiket) && $izinGuruMenungguPiket->count() > 0)
    <div class="card" style="padding:22px 24px;margin-bottom:24px;border:1.5px solid #fbbf24;background:#fffdfa">
        <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="background:#fef3c7;color:#d97706;padding:7px;border-radius:8px;display:flex;align-items:center;justify-content:center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
                <div>
                    <div class="card-title" style="color:#92400e;font-size:16px">Permintaan Izin Guru Masuk</div>
                    <div style="font-size:12.5px;color:#b45309">Ada {{ $izinGuruMenungguPiket->count() }} permohonan izin guru yang menunggu konfirmasi Anda untuk diteruskan ke Kepala Sekolah & Waka SDM via WhatsApp.</div>
                </div>
            </div>
            <span class="badge badge-warning" style="font-size:12px;padding:5px 10px;font-weight:700">{{ $izinGuruMenungguPiket->count() }} Menunggu</span>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:760px">
                <thead>
                    <tr>
                        <th>Tanggal Izin</th>
                        <th>Nama Guru</th>
                        <th>Alasan</th>
                        <th>Surat</th>
                        <th style="text-align:center">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($izinGuruMenungguPiket as $izinPiket)
                        <tr>
                            <td><strong style="color:var(--text, #0f172a)">{{ $izinPiket->tanggal_izin->format('d-m-Y') }}</strong></td>
                            <td><strong style="color:#2563eb">{{ $izinPiket->guru->nama_guru ?? '-' }}</strong></td>
                            <td>{{ $izinPiket->alasan }}</td>
                            <td>
                                @if($izinPiket->foto_surat)
                                    <a href="{{ Storage::disk('public')->url($izinPiket->foto_surat) }}" target="_blank" rel="noopener" style="color:#2563eb;text-decoration:none;font-weight:600">Lihat Foto</a>
                                @else
                                    <span style="color:#94a3b8">-</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <button type="button" onclick="konfirmasiIzinPiket({{ $izinPiket->id_izin_guru }}, '{{ addslashes($izinPiket->guru->nama_guru ?? '') }}')" class="btn-primary" style="background:#16a34a;border-color:#16a34a;font-size:12px;padding:6px 14px;border-radius:7px;display:inline-flex;align-items:center;gap:6px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Konfirmasi & Teruskan ke WA
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="card-title">Jadwal Mengajar Hari Ini</div>
                <span class="card-action">{{ $hariIni }}</span>
            </div>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-gurupiket-jadwal" oninput="filterTable('search-gurupiket-jadwal','table-gurupiket-jadwal')" onkeyup="filterTable('search-gurupiket-jadwal','table-gurupiket-jadwal')" placeholder="Cari kelas, mapel, guru..." class="filter-input" style="padding:7px 12px 7px 32px;font-size:12.5px;border-radius:8px;border:1px solid #cbd5e1;width:210px">
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" id="table-gurupiket-jadwal" style="min-width:760px">
                <thead><tr><th>Jam</th><th>Kelas</th><th>Mata Pelajaran</th><th>Guru</th></tr></thead>
                <tbody>
                    @forelse(($jadwalPiketHariIni ?? $jadwalHariIni) as $jadwal)
                        <tr><td><span class="badge badge-info">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span></td><td><strong>{{ $jadwal->nama_kelas }}</strong></td><td>{{ $jadwal->nama_mapel }}</td><td>{{ $jadwal->nama_guru }}</td></tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;padding:28px">Tidak ada jadwal mengajar untuk hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function konfirmasiIzinPiket(id, namaGuru){
    Swal.fire({
        title: 'Konfirmasi Izin Guru',
        html: `Konfirmasi permohonan izin dari <strong>${namaGuru}</strong>?<br><br><span style="font-size:12.5px;color:#64748b">Setelah dikonfirmasi, sistem akan otomatis mengirim notifikasi WhatsApp dan tautan persetujuan kepada Kepala Sekolah & Waka SDM.</span><br><br><textarea id="swal-catatan-piket" class="swal2-textarea" placeholder="Catatan piket (opsional)..." style="margin:0;width:100%;font-size:13px" rows="2"></textarea>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Konfirmasi & Kirim WA',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const catatan = document.getElementById('swal-catatan-piket')?.value || '';
            const baseUrl = @json(url('/guru-piket/izin-guru'));
            return fetch(baseUrl + '/' + id + '/konfirmasi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ catatan_piket: catatan })
            })
            .then(async r => {
                const d = await r.json();
                if (!r.ok) throw new Error(d.message || 'Gagal mengonfirmasi izin.');
                return d;
            })
            .catch(err => {
                Swal.showValidationMessage(err.message);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dikonfirmasi',
                text: result.value.message,
                confirmButtonColor: '#2563eb'
            }).then(() => location.reload());
        }
    });
}
</script>

