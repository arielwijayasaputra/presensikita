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
                <div class="stat-label">Total Kelas</div>
                <div class="stat-value">{{ $totalKelas }}</div>
                <div class="stat-pct">kelas yang tersedia</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div>
                <div class="stat-label">Total Siswa Aktif</div>
                <div class="stat-value">{{ number_format($totalSiswa) }}</div>
                <div class="stat-pct">siswa terdaftar</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
            <div>
                <div class="stat-label">Jurnal Disimpan</div>
                <div class="stat-value">{{ number_format($totalJurnal) }}</div>
                <div class="stat-pct">{{ $jurnalHariIni }} jurnal hari ini</div>
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
