<div class="page-content page-anim" id="page-guru-dashboard">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Dashboard Guru</div>
            <div class="page-subtitle">Selamat datang, {{ $guru->nama_guru ?? 'Guru' }} — kelola jurnal dan absensi mengajar Anda.</div>
        </div>
    </div>

    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
            <div>
                <div class="stat-label">Total Kelas Hari Ini</div>
                <div class="stat-value">{{ $totalKelas }}</div>
                <div class="stat-pct">kelas yang diajar hari ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div>
                <div class="stat-label">Total Siswa {{ $namaKelasDiajarHariIni }}</div>
                <div class="stat-value">{{ $totalSiswa === null ? '-' : number_format($totalSiswa) }}</div>
                <div class="stat-pct">Siswa aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon cyan"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
            <div>
                <div class="stat-label">Hadir Hari Ini</div>
                <div class="stat-value">{{ number_format($hadirHariIni) }}</div>
                <div class="stat-pct">total siswa hadir</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $statusKehadiranHariIni === 'Izin' ? 'yellow' : 'green' }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
            <div>
                <div class="stat-label">Status Hari Ini</div>
                <div class="stat-value">{{ $statusKehadiranHariIni }}</div>
                <div class="stat-pct">{{ $statusKehadiranHariIni === 'Izin' ? 'Kepsek & Waka menyetujui' : ($izinGuruHariIni ? 'Izin belum lengkap' : 'aktif mengajar') }}</div>
            </div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px;margin-bottom:20px">
        <div class="card-header" style="margin-bottom:16px"><div><div class="card-title">Jadwal Mengajar Hari Ini</div><div style="font-size:12px;color:#64748b;margin-top:3px">Jadwal otomatis berdasarkan akun Guru yang sedang login</div></div><span class="card-action">{{ now()->format('d M Y') }}</span></div>
        <div style="overflow-x:auto"><table class="data-table" style="min-width:700px"><thead><tr><th>Jam Ke-</th><th>Waktu</th><th>Kelas</th><th>Mata Pelajaran</th><th>Status</th></tr></thead><tbody>@forelse($jadwalMengajarHariIni as $jadwal)<tr><td><span class="badge badge-info">{{ $jadwal->jam_ke >= 100 ? $jadwal->jam_ke - 100 : $jadwal->jam_ke }}</span></td><td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td><td><strong>{{ $jadwal->nama_kelas }}</strong></td><td>{{ $jadwal->nama_mapel }}</td><td>@if(now()->format('H:i:s') >= $jadwal->jam_mulai && now()->format('H:i:s') <= $jadwal->jam_selesai)<span class="badge badge-success">Sedang berlangsung</span>@elseif(now()->format('H:i:s') < $jadwal->jam_mulai)<span class="badge badge-info">Belum dimulai</span>@else<span class="badge">Selesai</span>@endif</td></tr>@empty<tr><td colspan="5" style="text-align:center;color:#64748b;padding:24px">Tidak ada jadwal mengajar hari ini.</td></tr>@endforelse</tbody></table></div>
    </div>

    <div class="charts-row" style="grid-template-columns:1.5fr 1fr">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Grafik Kehadiran (7 Hari Terakhir)</div>
                    <div class="chart-legend">
                        <div class="legend-item"><div class="legend-dot" style="background:#22c55e"></div>Hadir</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#f59e0b"></div>Sakit</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#3b82f6"></div>Izin</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#ef4444"></div>Alpa</div>
                    </div>
                </div>
            </div>
            <canvas id="lineChart" height="200"></canvas>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Jurnal Terbaru</div>
                    <span class="card-action" onclick="showPage('riwayat-jurnal')">Lihat Semua</span>
                </div>
            </div>
            @forelse($riwayatJurnal->take(5) as $j)
            <div class="activity-item">
                <div class="activity-icon green">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
                </div>
                <div class="activity-text">
                    <p>Jurnal <strong>{{ $j->nama_kelas }}</strong> — {{ date('d M Y', strtotime($j->tanggal)) }} ({{ $j->jumlah_hadir }} hadir)</p>
                    <span>{{ \Illuminate\Support\Str::limit($j->materi ?? '-', 40) }}</span>
                </div>
            </div>
            @empty
            <div style="padding:12px 0;color:#94a3b8;font-size:13px">Belum ada jurnal yang disimpan.</div>
            @endforelse
        </div>
    </div>
</div>
