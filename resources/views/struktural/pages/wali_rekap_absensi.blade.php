<div class="page-content page-anim" id="page-wali-rekap-absensi" style="display:none">

    @php
        $namaKelasAktif = $waliKelasObj->nama_kelas ?? session('auth_nama_kelas', 'Kelas');
    @endphp

    <!-- ══ HEADER REKAP ABSENSI KELAS ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Rekapitulasi Absensi Siswa</h1>
                <span class="badge badge-primary" style="font-size:13px; padding:6px 12px; border-radius:8px">{{ $namaKelasAktif }}</span>
            </div>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">
                Pilih periode tanggal bebas untuk melihat rincian tabel presensi siswa di kelas <strong>{{ $namaKelasAktif }}</strong>
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <!-- Filter Rentang Tanggal -->
            <form method="GET" action="{{ route('walikelas.index') }}" style="display:flex; align-items:center; gap:8px; background:#fff; padding:6px 12px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <label style="font-size:12px; font-weight:700; color:#475569">Dari:</label>
                <input type="date" name="wali_tgl_mulai" value="{{ $waliTglMulaiAbsen }}" class="filter-input" style="padding:4px 8px; font-size:12px; border-radius:6px; border:1px solid #cbd5e1">
                
                <label style="font-size:12px; font-weight:700; color:#475569">S/D:</label>
                <input type="date" name="wali_tgl_selesai" value="{{ $waliTglSelesaiAbsen }}" class="filter-input" style="padding:4px 8px; font-size:12px; border-radius:6px; border:1px solid #cbd5e1">

                <button type="submit" class="btn-primary" style="font-size:12px; padding:5px 12px; border-radius:6px; background:#3b82f6">Tampilkan</button>
            </form>

            <!-- Ekspor CSV -->
            <a href="{{ route('walikelas.export-absensi', ['tgl_mulai' => $waliTglMulaiAbsen, 'tgl_selesai' => $waliTglSelesaiAbsen]) }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #059669, #10b981); text-decoration:none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor CSV Absensi</span>
            </a>

            <!-- Cetak Laporan -->
            <button type="button" onclick="window.print()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK PERIODE ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px; border-left:4px solid #3b82f6">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Siswa Aktif</div>
            <div style="font-size:26px; font-weight:800; color:#1e293b; margin-top:4px">{{ count($waliRekapAbsensiRange['siswa'] ?? []) }} Siswa</div>
            <div style="font-size:11.5px; color:#94a3b8; margin-top:2px">Kelas {{ $namaKelasAktif }}</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #10b981">
            <div style="font-size:11.5px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Rata-Rata Kehadiran</div>
            <div style="font-size:26px; font-weight:800; color:#065f46; margin-top:4px">{{ $waliRekapAbsensiRange['pct_hadir'] ?? 0 }}%</div>
            <div style="font-size:11.5px; color:#10b981; margin-top:2px">Periode {{ date('d M Y', strtotime($waliTglMulaiAbsen)) }} - {{ date('d M Y', strtotime($waliTglSelesaiAbsen)) }}</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #f59e0b">
            <div style="font-size:11.5px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px">Total Sakit</div>
            <div style="font-size:26px; font-weight:800; color:#78350f; margin-top:4px">{{ $waliRekapAbsensiRange['sakit'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#f59e0b; margin-top:2px">Kasus Sakit</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #06b6d4">
            <div style="font-size:11.5px; font-weight:700; color:#0891b2; text-transform:uppercase; letter-spacing:0.5px">Total Izin</div>
            <div style="font-size:26px; font-weight:800; color:#164e63; margin-top:4px">{{ $waliRekapAbsensiRange['izin'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#06b6d4; margin-top:2px">Kasus Izin</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #ef4444">
            <div style="font-size:11.5px; font-weight:700; color:#b91c1c; text-transform:uppercase; letter-spacing:0.5px">Total Alpa</div>
            <div style="font-size:26px; font-weight:800; color:#991b1b; margin-top:4px">{{ $waliRekapAbsensiRange['alpa'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#ef4444; margin-top:2px">Tanpa Keterangan</div>
        </div>
    </div>

    <!-- ══ TABEL RINCIAN PRESENSI SISWA ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Tabel Rincian Absensi Siswa Kelas {{ $namaKelasAktif }}
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Periode: <strong>{{ date('d-m-Y', strtotime($waliTglMulaiAbsen)) }}</strong> s/d <strong>{{ date('d-m-Y', strtotime($waliTglSelesaiAbsen)) }}</strong> (Total Sesi: {{ $waliRekapAbsensiRange['jurnal_count'] ?? 0 }})
                </div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-rekap-absensi-tbl" onkeyup="filterTable('search-wali-rekap-absensi-tbl', 'table-wali-rekap-absensi-tbl')" placeholder="Cari siswa / NISN..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-rekap-absensi-tbl" style="min-width:950px">
                <thead>
                    <tr>
                        <th style="width:45px">No</th>
                        <th>NISN</th>
                        <th>Nama Lengkap Siswa</th>
                        <th style="text-align:center; width:50px">L/P</th>
                        <th style="text-align:center">Hadir (H)</th>
                        <th style="text-align:center">Sakit (S)</th>
                        <th style="text-align:center">Izin (I)</th>
                        <th style="text-align:center">Alpa (A)</th>
                        <th style="width:180px; text-align:center">Persentase Kehadiran</th>
                        <th style="text-align:center">Keterangan</th>
                        <th style="text-align:center; width:110px">Hubungi Ortu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliRekapAbsensiRange['siswa'] ?? [] as $idx => $r)
                        <tr>
                            <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                            <td style="font-family:monospace; font-size:12.5px; color:#64748b">{{ $r['nisn'] ?? '-' }}</td>
                            <td><strong style="color:#0f172a">{{ $r['nama_siswa'] }}</strong></td>
                            <td style="text-align:center">{{ $r['jenis_kelamin'] ?? 'L' }}</td>
                            <td style="text-align:center"><span class="badge badge-success" style="font-size:12px">{{ $r['hadir'] }}</span></td>
                            <td style="text-align:center"><span class="badge badge-warning" style="font-size:12px">{{ $r['sakit'] }}</span></td>
                            <td style="text-align:center"><span class="badge badge-info" style="font-size:12px">{{ $r['izin'] }}</span></td>
                            <td style="text-align:center">
                                @if($r['alpa'] > 0)
                                    <span class="badge badge-danger" style="font-size:12px">{{ $r['alpa'] }}</span>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">0</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <div style="display:flex; align-items:center; gap:8px; justify-content:center">
                                    <div style="flex:1; background:#e2e8f0; height:8px; border-radius:4px; overflow:hidden">
                                        <div style="width:{{ $r['persentase'] }}%; height:100%; background:{{ $r['persentase'] >= 85 ? '#10b981' : ($r['persentase'] >= 70 ? '#f59e0b' : '#ef4444') }}"></div>
                                    </div>
                                    <span style="font-size:12px; font-weight:700; color:#334155; width:45px; text-align:right">{{ $r['persentase'] }}%</span>
                                </div>
                            </td>
                            <td style="text-align:center">
                                @if($r['persentase'] >= 90)
                                    <span class="badge badge-success">Sangat Baik</span>
                                @elseif($r['persentase'] >= 80)
                                    <span class="badge badge-primary">Baik</span>
                                @elseif($r['persentase'] >= 70)
                                    <span class="badge badge-warning">Cukup</span>
                                @else
                                    <span class="badge badge-danger">Perlu Perhatian</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                @if(!empty($r['no_hp_ortu']))
                                    @php
                                        $hpWa = preg_replace('/[^0-9]/', '', $r['no_hp_ortu']);
                                        if(str_starts_with($hpWa, '0')) { $hpWa = '62' . substr($hpWa, 1); }
                                        $msgWa = urlencode("Halo Bapak/Ibu Wali dari " . $r['nama_siswa'] . ", menginformasikan rekapitulasi presensi siswa.");
                                    @endphp
                                    <a href="https://wa.me/{{ $hpWa }}?text={{ $msgWa }}" target="_blank" style="font-size:11.5px; padding:4px 8px; border-radius:6px; background:#25d366; color:#fff; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:4px">
                                        WA Ortu
                                    </a>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align:center; color:#64748b; padding:24px">
                                Belum ada data presensi pada rentang tanggal terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
