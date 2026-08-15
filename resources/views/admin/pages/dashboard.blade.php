<div class="page-content page-anim" id="page-dashboard">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Dashboard Kehadiran</div>
            <div class="page-subtitle">Sistem Informasi Kehadiran Siswa - PresensiKita</div>
        </div>
    </div>
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
            <div>
                <div class="stat-label">Hadir</div>
                <div class="stat-value">{{ number_format($totalHadir) }}</div>
                <div class="stat-pct">
                    <div class="pct-bar"><div class="pct-fill green" style="width:{{ $pctHadir }}%"></div></div>
                    {{ $pctHadir }}% dari seluruh absensi
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
            <div>
                <div class="stat-label">Sakit</div>
                <div class="stat-value">{{ number_format($totalSakit) }}</div>
                <div class="stat-pct">
                    <div class="pct-bar"><div class="pct-fill yellow" style="width:{{ $pctSakit }}%"></div></div>
                    {{ $pctSakit }}% dari seluruh absensi
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
            <div>
                <div class="stat-label">Izin</div>
                <div class="stat-value">{{ number_format($totalIzin) }}</div>
                <div class="stat-pct">
                    <div class="pct-bar"><div class="pct-fill blue" style="width:{{ $pctIzin }}%"></div></div>
                    {{ $pctIzin }}% dari seluruh absensi
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
            <div>
                <div class="stat-label">Alpa</div>
                <div class="stat-value">{{ number_format($totalAlpa) }}</div>
                <div class="stat-pct">
                    <div class="pct-bar"><div class="pct-fill red" style="width:{{ $pctAlpa }}%"></div></div>
                    {{ $pctAlpa }}% dari seluruh absensi
                </div>
            </div>
        </div>
    </div>

    <div class="charts-row">
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
            <div class="card-header"><div class="card-title">Persentase Kehadiran per Kelas</div></div>
            @forelse($kelasPersentase as $k)
            <div class="class-pct-item">
                <div class="class-label">{{ $k['nama_kelas'] }}</div>
                <div class="class-bar-bg"><div class="class-bar-fill" style="width:{{ $k['persentase'] }}%"></div></div>
                <div class="class-pct-val">{{ $k['persentase'] }}%</div>
            </div>
            @empty
            <div style="padding:12px 0;color:#94a3b8;font-size:13px">Belum ada data absensi untuk ditampilkan.</div>
            @endforelse
        </div>
        <div class="card">
            <div class="card-header"><div class="card-title">Aktivitas Terbaru</div><span class="card-action" onclick="showPage('riwayat')">Lihat Semua</span></div>
            @forelse($recentActivities as $act)
            <div class="activity-item">
                <div class="activity-icon green"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg></div>
                <div class="activity-text">
                    <p>Absensi {{ $act->nama_kelas ?? 'kelas' }} tanggal {{ date('d-m-Y', strtotime($act->tanggal)) }} disimpan ({{ $act->jumlah_hadir }} hadir)</p>
                    <span>{{ $act->waktu_input ? date('d-m-Y H:i', strtotime($act->waktu_input)) : '-' }}</span>
                </div>
            </div>
            @empty
            <div style="padding:12px 0;color:#94a3b8;font-size:13px">Belum ada aktivitas absensi.</div>
            @endforelse
        </div>
    </div>
</div>
