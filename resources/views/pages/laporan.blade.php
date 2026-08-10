@php
    $lr = $laporanRekap;
    $laporanPerPage = 5;
    $laporanTotalSiswa = count($lr['siswa'] ?? []);
@endphp

<div class="page-content page-anim" id="page-laporan" style="display:none"
     data-hadir="{{ $lr['hadir'] }}"
     data-sakit="{{ $lr['sakit'] }}"
     data-izin="{{ $lr['izin'] }}"
     data-alpa="{{ $lr['alpa'] }}"
     data-pct-hadir="{{ $lr['pct_hadir'] }}"
     data-pct-sakit="{{ $lr['pct_sakit'] }}"
     data-pct-izin="{{ $lr['pct_izin'] }}"
     data-pct-alpa="{{ $lr['pct_alpa'] }}">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px;display:flex;align-items:flex-start;justify-content:space-between">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;color:#1e293b">Laporan / Rekap Absensi</div>
            <div class="page-subtitle" id="laporan-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">
                Kelas {{ $selectedKelas->nama_kelas }}
            </div>
        </div>
        <div class="breadcrumb" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
            Dashboard
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#1e293b;font-weight:600">Laporan / Rekap</span>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:18px;align-items:flex-end;gap:12px;flex-wrap:wrap">
        {{-- Pilih Bulan --}}
        <div class="filter-group" style="min-width:170px">
            <label style="font-size:11.5px;font-weight:600;color:#475569;margin-bottom:5px;display:block">Pilih Bulan</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <select class="filter-select" id="laporan-bulan" style="width:100%;padding-left:32px;padding-right:28px;appearance:none">
                    @php
                        $bulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                    @endphp
                    @foreach($bulanList as $num => $nama)
                        <option value="{{ $num }}" {{ (int)$laporanBulan === $num ? 'selected' : '' }}>{{ $nama }} {{ $laporanTahun }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Pilih Kelas --}}
        <div class="filter-group" style="min-width:150px">
            <label style="font-size:11.5px;font-weight:600;color:#475569;margin-bottom:5px;display:block">Pilih Kelas</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <select class="filter-select" id="laporan-kelas" onchange="updateLaporanKelasLabel(this)" style="width:100%;padding-left:32px;padding-right:28px;appearance:none">
                    @foreach($kelases as $k)
                        <option value="{{ $k->id_kelas }}" {{ $k->id_kelas == $selectedKelas->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Pilih Data --}}
        <div class="filter-group" style="min-width:160px">
            <label style="font-size:11.5px;font-weight:600;color:#475569;margin-bottom:5px;display:block">Pilih Data</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <select class="filter-select" id="laporan-data" style="width:100%;padding-left:32px;padding-right:28px;appearance:none">
                    <option value="semua">Semua Data</option>
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alpa">Alpa</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Tombol Tampilkan Laporan --}}
        <button type="button" class="btn-primary" onclick="tampilkanLaporan()" style="border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:8px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Tampilkan Laporan
        </button>

        {{-- Tombol Export PDF --}}
        <button type="button" class="btn-secondary" onclick="window.print()" style="margin-left:auto;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:8px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Export PDF
        </button>
    </div>

    {{-- ── 6 Stat Cards ── --}}
    <div class="stat-cards laporan-stat-cards" style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px">
        {{-- Total Siswa --}}
        <div class="stat-card laporan-stat" style="background:#f0fdf4;border:1px solid #dcfce7;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div class="stat-icon green" style="width:40px;height:40px;background:#dcfce7;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-label" style="font-size:11.5px;color:#16a34a;font-weight:600">Total Siswa</div>
                <div class="stat-value" id="laporan-stat-siswa" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ number_format($lr['total_siswa']) }}</div>
                <div class="stat-pct" style="font-size:11px;color:#94a3b8">Orang</div>
            </div>
        </div>

        {{-- Kehadiran --}}
        <div class="stat-card laporan-stat" style="background:#eff6ff;border:1px solid #dbeafe;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div class="stat-icon blue" style="width:40px;height:40px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="stat-label" style="font-size:11.5px;color:#2563eb;font-weight:600">Kehadiran</div>
                <div class="stat-value" id="laporan-stat-hadir" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ number_format($lr['hadir']) }}</div>
                <div class="stat-pct" style="font-size:11px;color:#94a3b8">Kali</div>
            </div>
        </div>

        {{-- Sakit --}}
        <div class="stat-card laporan-stat" style="background:#fffbeb;border:1px solid #fef3c7;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div class="stat-icon yellow" style="width:40px;height:40px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#d97706;flex-shrink:0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div>
                <div class="stat-label" style="font-size:11.5px;color:#d97706;font-weight:600">Sakit</div>
                <div class="stat-value" id="laporan-stat-sakit" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ number_format($lr['sakit']) }}</div>
                <div class="stat-pct" style="font-size:11px;color:#94a3b8">Kali</div>
            </div>
        </div>

        {{-- Izin --}}
        <div class="stat-card laporan-stat" style="background:#f0f9ff;border:1px solid #e0f2fe;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div class="stat-icon cyan" style="width:40px;height:40px;background:#e0f2fe;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#0284c7;flex-shrink:0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-label" style="font-size:11.5px;color:#0284c7;font-weight:600">Izin</div>
                <div class="stat-value" id="laporan-stat-izin" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ number_format($lr['izin']) }}</div>
                <div class="stat-pct" style="font-size:11px;color:#94a3b8">Kali</div>
            </div>
        </div>

        {{-- Alpa --}}
        <div class="stat-card laporan-stat" style="background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div class="stat-icon red" style="width:40px;height:40px;background:#fecdd3;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#e11d48;flex-shrink:0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="stat-label" style="font-size:11.5px;color:#e11d48;font-weight:600">Alpa</div>
                <div class="stat-value" id="laporan-stat-alpa" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ number_format($lr['alpa']) }}</div>
                <div class="stat-pct" style="font-size:11px;color:#94a3b8">Kali</div>
            </div>
        </div>

        {{-- Persentase Kehadiran Ring --}}
        <div class="stat-card laporan-stat laporan-stat-ring" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div class="laporan-ring" id="laporan-ring" style="position:relative;width:52px;height:52px;flex-shrink:0">
                <svg viewBox="0 0 36 36" style="width:52px;height:52px;transform:rotate(-90deg)">
                    <path class="laporan-ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e2e8f0" stroke-width="3.2"/>
                    <path class="laporan-ring-fg" id="laporan-ring-fg" stroke-dasharray="{{ $lr['pct_hadir'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#22c55e" stroke-width="3.2" stroke-linecap="round"/>
                </svg>
                <div class="laporan-ring-text" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center">
                    <strong id="laporan-ring-pct" style="font-size:12px;font-weight:800;color:#1e293b;line-height:1">{{ $lr['pct_hadir'] }}%</strong>
                </div>
            </div>
            <div>
                <div class="stat-label" style="font-size:11.5px;color:#64748b;font-weight:600">Persentase Kehadiran</div>
                <div class="stat-pct" id="laporan-ring-label" style="font-size:11.5px;font-weight:700;color:#16a34a;margin-top:2px">{{ $lr['pct_label'] }}</div>
            </div>
        </div>
    </div>

    {{-- ── 3 Charts Row ── --}}
    <div class="charts-row laporan-charts-row" style="display:grid;grid-template-columns:1fr 1.2fr 1fr;gap:16px;margin-bottom:20px">
        {{-- Donut Chart --}}
        <div class="card" style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px">
            <div class="card-header" style="margin-bottom:14px">
                <div class="card-title" style="font-size:14px;font-weight:700;color:#1e293b">Persentase Kehadiran</div>
            </div>
            <div class="laporan-donut-wrap" style="display:flex;align-items:center;gap:16px">
                <div class="laporan-donut-chart" style="flex:1;max-width:160px">
                    <canvas id="laporanDonutChart" height="170"></canvas>
                </div>
                <div class="laporan-donut-legend" style="display:flex;flex-direction:column;gap:8px">
                    <div class="legend-item" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px"><div class="legend-dot" style="width:8px;height:8px;border-radius:50%;background:#22c55e"></div>Hadir <strong id="laporan-leg-hadir" style="color:#1e293b;margin-left:auto">{{ $lr['pct_hadir'] }}%</strong></div>
                    <div class="legend-item" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px"><div class="legend-dot" style="width:8px;height:8px;border-radius:50%;background:#f59e0b"></div>Sakit <strong id="laporan-leg-sakit" style="color:#1e293b;margin-left:auto">{{ $lr['pct_sakit'] }}%</strong></div>
                    <div class="legend-item" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px"><div class="legend-dot" style="width:8px;height:8px;border-radius:50%;background:#3b82f6"></div>Izin <strong id="laporan-leg-izin" style="color:#1e293b;margin-left:auto">{{ $lr['pct_izin'] }}%</strong></div>
                    <div class="legend-item" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px"><div class="legend-dot" style="width:8px;height:8px;border-radius:50%;background:#ef4444"></div>Alpa <strong id="laporan-leg-alpa" style="color:#1e293b;margin-left:auto">{{ $lr['pct_alpa'] }}%</strong></div>
                </div>
            </div>
        </div>

        {{-- Line Chart --}}
        <div class="card" style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px">
            <div class="card-header" style="margin-bottom:14px">
                <div class="card-title" style="font-size:14px;font-weight:700;color:#1e293b">Tren Kehadiran (10 Hari Terakhir)</div>
            </div>
            <canvas id="laporanLineChart" height="170"></canvas>
        </div>

        {{-- Bar Chart --}}
        <div class="card" style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:20px">
            <div class="card-header" style="margin-bottom:14px">
                <div class="card-title" style="font-size:14px;font-weight:700;color:#1e293b">Rekap Absensi per Hari</div>
            </div>
            <canvas id="laporanBarChart" height="170"></canvas>
        </div>
    </div>

    {{-- ── Main Table: Rekap Absensi Siswa ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <div class="card-header" style="padding:18px 20px;border-bottom:1px solid #e2e8f0;margin-bottom:0">
            <div class="card-title" style="font-size:15px;font-weight:700;color:#1e293b">Rekap Absensi Siswa</div>
        </div>
        <table id="laporan-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:48px;vertical-align:middle">No.</th>
                    <th rowspan="2" style="vertical-align:middle">Nama Siswa</th>
                    <th colspan="4" style="text-align:center;border-bottom:1px solid #e2e8f0;padding:8px">Total</th>
                    <th rowspan="2" style="text-align:center;vertical-align:middle;width:160px">Persentase Kehadiran</th>
                    <th rowspan="2" style="vertical-align:middle;width:120px">Keterangan</th>
                </tr>
                <tr class="laporan-subhead">
                    <th style="text-align:center;color:#16a34a;width:70px">Hadir</th>
                    <th style="text-align:center;color:#d97706;width:70px">Sakit</th>
                    <th style="text-align:center;color:#2563eb;width:70px">Izin</th>
                    <th style="text-align:center;color:#dc2626;width:70px">Alpa</th>
                </tr>
            </thead>
            <tbody id="laporan-tbody">
                @forelse($lr['siswa'] as $idx => $s)
                    @php
                        $pct = (int) $s['persentase'];
                        if($pct >= 85) {
                            $badgeStyle = 'background:#dcfce7;color:#15803d;';
                        } elseif($pct >= 75) {
                            $badgeStyle = 'background:#fef3c7;color:#b45309;';
                        } else {
                            $badgeStyle = 'background:#fee2e2;color:#b91c1c;';
                        }
                    @endphp
                    <tr class="laporan-row" data-page="{{ (int) floor($idx / $laporanPerPage) + 1 }}">
                        <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                        <td style="font-weight:600;color:#1e293b;font-size:13.5px">{{ $s['nama_siswa'] }}</td>
                        <td style="text-align:center;color:#16a34a;font-weight:600">{{ $s['hadir'] }}</td>
                        <td style="text-align:center;color:#d97706;font-weight:600">{{ $s['sakit'] }}</td>
                        <td style="text-align:center;color:#2563eb;font-weight:600">{{ $s['izin'] }}</td>
                        <td style="text-align:center;color:#dc2626;font-weight:600">{{ $s['alpa'] }}</td>
                        <td style="text-align:center">
                            <span style="padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;display:inline-block;{{ $badgeStyle }}">
                                {{ $s['persentase'] }}%
                            </span>
                        </td>
                        <td style="color:#475569;font-weight:500;font-size:13px">{{ $s['keterangan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">Belum ada data siswa untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="table-footer laporan-table-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span class="laporan-page-info" id="laporan-page-info" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan {{ $laporanTotalSiswa > 0 ? '1' : '0' }} - {{ min($laporanPerPage, $laporanTotalSiswa) }} dari {{ $laporanTotalSiswa }} data
            </span>
            <div class="laporan-pagination" id="laporan-pagination" data-total="{{ $laporanTotalSiswa }}" data-per-page="{{ $laporanPerPage }}" style="display:flex;align-items:center;gap:4px"></div>
        </div>
    </div>
</div>
