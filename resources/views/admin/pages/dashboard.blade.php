<div class="page-content page-anim" id="page-dashboard">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Dashboard Kehadiran</div>
            <div class="page-subtitle">Sistem Informasi Kehadiran Siswa - PresensiKita</div>
        </div>
    </div>

    {{-- ── Peringatan Data Belum Lengkap ── --}}
    @if(($totalPeringatan ?? 0) > 0)
    <div id="dashboard-peringatan" class="card" style="margin-bottom:20px;padding:16px 20px;border-left:5px solid #f59e0b;background:#fffbeb">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
            <div style="width:34px;height:34px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div style="font-size:14.5px;font-weight:800;color:#92400e">Perhatian: ada {{ $totalPeringatan }} data yang belum lengkap</div>
                <div style="font-size:12.5px;color:#a16207">Klik salah satu peringatan di bawah untuk melihat detail datanya.</div>
            </div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            @if($kelasTanpaWali->count() > 0)
            <button type="button" onclick="bukaModalPeringatan('kelas')" class="peringatan-item" style="flex:1;min-width:240px;display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #fcd34d;border-radius:10px;background:#fff;cursor:pointer;text-align:left;font-family:inherit">
                <div style="font-size:26px;font-weight:800;color:#b45309;line-height:1;min-width:34px;text-align:center">{{ $kelasTanpaWali->count() }}</div>
                <div>
                    <div style="font-size:13.5px;font-weight:700;color:#1e293b">Kelas belum memiliki wali kelas</div>
                    <div style="font-size:12px;color:#64748b">Klik untuk melihat daftar kelas</div>
                </div>
                <svg style="margin-left:auto;flex-shrink:0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            @endif
            @if($jadwalBermasalah->count() > 0)
            <button type="button" onclick="bukaModalPeringatan('jadwal')" class="peringatan-item" style="flex:1;min-width:240px;display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #fcd34d;border-radius:10px;background:#fff;cursor:pointer;text-align:left;font-family:inherit">
                <div style="font-size:26px;font-weight:800;color:#b45309;line-height:1;min-width:34px;text-align:center">{{ $jadwalBermasalah->count() }}</div>
                <div>
                    <div style="font-size:13.5px;font-weight:700;color:#1e293b">Jadwal mengajar belum lengkap</div>
                    <div style="font-size:12px;color:#64748b">Belum ada jam pelajaran, guru pengajar, atau kelas</div>
                </div>
                <svg style="margin-left:auto;flex-shrink:0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            @endif
        </div>
    </div>
    @endif

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
            <div class="stat-icon dispen"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div>
                <div class="stat-label">Dispensasi</div>
                <div class="stat-value">{{ number_format($totalDispen) }}</div>
                <div class="stat-pct">
                    <div class="pct-bar"><div class="pct-fill dispen" style="width:{{ $pctDispen }}%"></div></div>
                    {{ $pctDispen }}% dari seluruh absensi
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

@if(($totalPeringatan ?? 0) > 0)
{{-- ── Modal: Kelas Tanpa Wali Kelas ── --}}
<div id="modal-peringatan-kelas" class="modal-peringatan" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:220;align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this) tutupModalPeringatan('kelas')">
    <div class="card" style="width:100%;max-width:720px;max-height:85vh;display:flex;flex-direction:column;padding:24px;border-radius:var(--radius);box-shadow:0 12px 36px rgba(0,0,0,0.2)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #e2e8f0">
            <div>
                <h3 style="font-size:18px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:8px">
                    <span style="width:10px;height:10px;background:#f59e0b;border-radius:50%;display:inline-block"></span>
                    Kelas Belum Memiliki Wali Kelas ({{ $kelasTanpaWali->count() }})
                </h3>
                <p style="font-size:13px;color:#64748b;margin-top:3px">Berikut daftar kelas yang belum ditetapkan wali kelasnya. Klik <strong>Atur Wali Kelas</strong> untuk membuka halaman Data Kelas.</p>
            </div>
            <button type="button" onclick="tutupModalPeringatan('kelas')" aria-label="Tutup" style="border:0;background:none;font-size:24px;color:#64748b;cursor:pointer;line-height:1">&times;</button>
        </div>
        <div style="overflow-y:auto;flex:1;padding-right:4px">
            <table class="data-table" style="width:100%">
                <thead>
                    <tr style="background:#f8fafc">
                        <th style="width:40px">No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th style="text-align:center">Jumlah Siswa</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelasTanpaWali as $idx => $k)
                    <tr>
                        <td style="color:#94a3b8;font-weight:600">{{ $idx + 1 }}</td>
                        <td><strong>{{ $k->nama_kelas }}</strong></td>
                        <td>{{ $k->tingkat_kelas ?: '-' }}</td>
                        <td>{{ $k->jurusan ?: '-' }}</td>
                        <td style="text-align:center">{{ $k->siswa_count ?? 0 }}</td>
                        <td style="text-align:center">
                            <button type="button" class="btn-primary" onclick="tutupModalPeringatan('kelas'); showPage('data-kelas')" style="padding:6px 14px;font-size:12px;background:#d97706;border-color:#b45309;border-radius:6px;font-weight:700">Atur Wali Kelas</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e2e8f0">
            <button type="button" class="btn-secondary" onclick="tutupModalPeringatan('kelas')">Tutup</button>
        </div>
    </div>
</div>

{{-- ── Modal: Jadwal Mengajar Belum Lengkap ── --}}
<div id="modal-peringatan-jadwal" class="modal-peringatan" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:220;align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this) tutupModalPeringatan('jadwal')">
    <div class="card" style="width:100%;max-width:900px;max-height:85vh;display:flex;flex-direction:column;padding:24px;border-radius:var(--radius);box-shadow:0 12px 36px rgba(0,0,0,0.2)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #e2e8f0">
            <div>
                <h3 style="font-size:18px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:8px">
                    <span style="width:10px;height:10px;background:#f59e0b;border-radius:50%;display:inline-block"></span>
                    Jadwal Mengajar Belum Lengkap ({{ $jadwalBermasalah->count() }})
                </h3>
                <p style="font-size:13px;color:#64748b;margin-top:3px">Jadwal berikut belum memiliki jam pelajaran, guru pengajar, atau kelas. Klik <strong>Perbaiki</strong> untuk membuka halaman Jadwal Mengajar.</p>
            </div>
            <button type="button" onclick="tutupModalPeringatan('jadwal')" aria-label="Tutup" style="border:0;background:none;font-size:24px;color:#64748b;cursor:pointer;line-height:1">&times;</button>
        </div>
        <div style="overflow-y:auto;flex:1;padding-right:4px">
            <table class="data-table" style="width:100%">
                <thead>
                    <tr style="background:#f8fafc">
                        <th style="width:40px">No</th>
                        <th>Hari</th>
                        <th>Jam Ke- &amp; Waktu</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Masalah</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalBermasalah as $idx => $j)
                    <tr>
                        <td style="color:#94a3b8;font-weight:600">{{ $idx + 1 }}</td>
                        <td><strong>{{ $j->hari ?: '-' }}</strong></td>
                        <td>
                            @if(!empty($j->jam_ke))
                                <span class="badge badge-info" style="font-size:11.5px">Jam ke-{{ $j->jam_ke >= 100 ? $j->jam_ke - 100 : $j->jam_ke }}</span>
                                <span style="font-size:12px;color:#64748b;display:block;margin-top:2px">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;padding:4px 8px;border-radius:6px;font-size:11.5px;font-weight:700">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($j->nama_kelas))
                                <strong>{{ $j->nama_kelas }}</strong>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;padding:4px 8px;border-radius:6px;font-size:11.5px;font-weight:700">Belum ada</span>
                            @endif
                        </td>
                        <td>{{ $j->nama_mapel ?: '-' }}</td>
                        <td>
                            @if(!empty($j->nama_guru))
                                {{ $j->nama_guru }}
                            @else
                                <span class="badge" style="background:#fee2e2;color:#b91c1c;padding:4px 8px;border-radius:6px;font-size:11.5px;font-weight:700">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            @foreach($j->masalah as $m)
                                <span style="display:inline-block;background:#fef3c7;color:#b45309;border:1px solid #fcd34d;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;margin:2px 2px 0">{{ $m }}</span>
                            @endforeach
                        </td>
                        <td style="text-align:center">
                            <button type="button" class="btn-primary" onclick="tutupModalPeringatan('jadwal'); showPage('jadwal')" style="padding:6px 14px;font-size:12px;background:#d97706;border-color:#b45309;border-radius:6px;font-weight:700">Perbaiki</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e2e8f0">
            <button type="button" class="btn-secondary" onclick="tutupModalPeringatan('jadwal')">Tutup</button>
        </div>
    </div>
</div>

<script>
function bukaModalPeringatan(jenis) {
    const el = document.getElementById('modal-peringatan-' + jenis);
    if (el) el.style.display = 'flex';
}
function tutupModalPeringatan(jenis) {
    const el = document.getElementById('modal-peringatan-' + jenis);
    if (el) el.style.display = 'none';
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-peringatan').forEach(m => m.style.display = 'none');
    }
});
</script>
@endif
