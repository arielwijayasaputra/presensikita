<div class="page-content page-anim" id="page-guru-piket" style="display:block">
    <div class="greeting-row">
        <div class="greeting-text">
            <h2>Dashboard Guru Piket</h2>
            <p>{{ $hariIni }}, {{ now()->format('d F Y') }} · {{ $namaSekolah }}</p>
        </div>
    </div>

    <div class="alert-card" style="background:linear-gradient(135deg,#fff7ed,#fffbeb);border-color:#fdba74;margin-bottom:22px">
        <div class="alert-icon" style="background:#f97316">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="alert-text">
            <p>Anda bertugas sebagai Guru Piket hari ini</p>
            <span>Gunakan informasi jadwal di bawah untuk membantu pemantauan kegiatan belajar mengajar.</span>
        </div>
    </div>

    <div class="stat-cards" style="margin-bottom:24px">
        <div class="stat-card"><div class="stat-icon" style="background:#fff7ed;color:#ea580c"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div><div class="stat-label">Total Jadwal</div><div class="stat-value">{{ $totalJadwalHariIni }}</div><div class="stat-sub">hari ini</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#eff6ff;color:#2563eb"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/></svg></div><div><div class="stat-label">Kelas Terjadwal</div><div class="stat-value">{{ $totalKelasHariIni }}</div><div class="stat-sub">kelas</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg></div><div><div class="stat-label">Guru Mengajar</div><div class="stat-value">{{ $totalGuruHariIni }}</div><div class="stat-sub">guru</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#fef2f2;color:#dc2626"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 16 14"/></svg></div><div><div class="stat-label">Jam Aktif</div><div class="stat-value">{{ $jamAktif?->jam_ke ?? '-' }}</div><div class="stat-sub">jam ke- saat ini</div></div></div>
    </div>

    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Jadwal Mengajar Hari Ini</div><span class="card-action">{{ $hariIni }}</span></div>
        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:760px">
                <thead><tr><th>Jam Ke-</th><th>Waktu</th><th>Kelas</th><th>Mata Pelajaran</th><th>Guru</th></tr></thead>
                <tbody>
                    @forelse($jadwalHariIni as $jadwal)
                        <tr><td><span class="badge badge-info">{{ $jadwal->jam_ke >= 100 ? $jadwal->jam_ke - 100 : $jadwal->jam_ke }}</span></td><td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td><td><strong>{{ $jadwal->nama_kelas }}</strong></td><td>{{ $jadwal->nama_mapel }}</td><td>{{ $jadwal->nama_guru }}</td></tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#64748b;padding:28px">Tidak ada jadwal mengajar untuk hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(false)
    <div class="card" style="padding:22px 24px;margin-top:20px;max-width:900px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Buat Permintaan Izin Guru</div></div>
        <form id="izin-guru-form" onsubmit="buatLinkIzinGuru(event)">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 180px;gap:14px;margin-bottom:14px">
                <div><label for="izin-id-guru" style="font-size:12px;font-weight:600;color:#475569">Guru yang meminta izin</label><select id="izin-id-guru" name="id_guru" class="filter-select" required style="width:100%;margin-top:4px"><option value="">Pilih guru</option>@foreach($guruAktif as $guruPilihan)<option value="{{ $guruPilihan->id_guru }}">{{ $guruPilihan->nama_guru }}</option>@endforeach</select></div>
                <div><label for="izin-tanggal" style="font-size:12px;font-weight:600;color:#475569">Tanggal izin</label><input type="date" id="izin-tanggal" name="tanggal_izin" class="filter-input" value="{{ now()->toDateString() }}" required style="width:100%;margin-top:4px"></div>
            </div>
            <div style="margin-bottom:14px"><label for="izin-alasan" style="font-size:12px;font-weight:600;color:#475569">Alasan izin</label><textarea id="izin-alasan" name="alasan" class="filter-input" rows="3" required maxlength="2000" placeholder="Tuliskan alasan guru tidak dapat mengajar..." style="width:100%;margin-top:4px;resize:vertical"></textarea></div>
            <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 16px;font-size:13px">Buat Link Persetujuan</button>
        </form>
        <div id="izin-link-result" style="display:none;margin-top:16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:14px"><div style="font-size:12px;font-weight:700;color:#1d4ed8;margin-bottom:8px">Link siap dikirim ke Kepsek dan Waka</div><div style="display:flex;gap:8px"><input id="izin-link" class="filter-input" readonly style="flex:1;font-size:12px"><button type="button" class="btn-secondary" onclick="salinLinkIzin()" style="font-size:12px">Salin</button></div></div>
    </div>

    <div class="card" style="padding:22px 24px;margin-top:20px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Permintaan Izin Guru Terbaru</div></div>
        <div style="overflow-x:auto"><table class="data-table" style="min-width:760px"><thead><tr><th>Guru</th><th>Tanggal</th><th>Alasan</th><th>Kepsek</th><th>Waka</th></tr></thead><tbody>@forelse($izinGuruTerbaru as $izin)<tr><td><strong>{{ $izin->guru->nama_guru ?? '-' }}</strong></td><td>{{ $izin->tanggal_izin->format('d-m-Y') }}</td><td>{{ $izin->alasan }}</td><td>{{ ucfirst($izin->status_kepsek) }}</td><td>{{ ucfirst($izin->status_waka) }}</td></tr>@empty<tr><td colspan="5" style="text-align:center;color:#64748b;padding:22px">Belum ada permintaan izin.</td></tr>@endforelse</tbody></table></div>
    </div>
    @endif
</div>

