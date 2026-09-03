<div class="page-content page-anim" id="page-wali-rekap-1tahun" style="display:none">

    @php
        $namaKelasAktif = $waliKelasObj->nama_kelas ?? session('auth_nama_kelas', 'Kelas');
    @endphp

    <!-- ══ HEADER REKAP 1 TAHUN ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Rekapitulasi 1 Tahun Ajaran</h1>
                <span class="badge badge-primary" style="font-size:13px; padding:6px 12px; border-radius:8px">{{ $namaKelasAktif }}</span>
            </div>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">
                Akumulasi Presensi Siswa &amp; Rekapitulasi Jurnal Pembelajaran 1 Tahun Ajaran di Kelas <strong>{{ $namaKelasAktif }}</strong>
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <!-- Filter Tahun -->
            <form method="GET" action="{{ route('walikelas.index') }}" style="display:flex; align-items:center; gap:8px; background:#fff; padding:6px 12px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <label style="font-size:12px; font-weight:700; color:#475569">Tahun Ajaran:</label>
                <select name="wali_tahun" onchange="this.form.submit()" class="filter-input" style="padding:4px 8px; font-size:12.5px; border-radius:6px; border:1px solid #cbd5e1">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $waliTahun == $y ? 'selected' : '' }}>{{ $y }} / {{ $y + 1 }}</option>
                    @endfor
                </select>
            </form>

            <!-- Ekspor CSV 1 Tahun -->
            <a href="{{ route('walikelas.export-1tahun', ['tahun' => $waliTahun]) }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #059669, #10b981); text-decoration:none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor CSV 1 Tahun</span>
            </a>

            <!-- Cetak Laporan -->
            <button type="button" onclick="window.print()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak 1 Tahun</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK 1 TAHUN ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px; border-left:4px solid #3b82f6">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Siswa Aktif</div>
            <div style="font-size:26px; font-weight:800; color:#1e293b; margin-top:4px">{{ count($waliRekap1Tahun['siswa'] ?? []) }} Siswa</div>
            <div style="font-size:11.5px; color:#94a3b8; margin-top:2px">Terdaftar 1 Tahun</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #10b981">
            <div style="font-size:11.5px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Rata-Rata Kehadiran</div>
            <div style="font-size:26px; font-weight:800; color:#065f46; margin-top:4px">{{ $waliRekap1Tahun['pct_hadir'] ?? 0 }}%</div>
            <div style="font-size:11.5px; color:#10b981; margin-top:2px">Persentase 1 Tahun Ajaran</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #8b5cf6">
            <div style="font-size:11.5px; font-weight:700; color:#6d28d9; text-transform:uppercase; letter-spacing:0.5px">Total Sesi Jurnal</div>
            <div style="font-size:26px; font-weight:800; color:#5b21b6; margin-top:4px">{{ $waliRekap1Tahun['jurnal_count'] ?? 0 }} Sesi</div>
            <div style="font-size:11.5px; color:#8b5cf6; margin-top:2px">Jurnal Pembelajaran 1 Tahun</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #f59e0b">
            <div style="font-size:11.5px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px">Total Sakit &amp; Izin</div>
            <div style="font-size:26px; font-weight:800; color:#78350f; margin-top:4px">{{ ($waliRekap1Tahun['sakit'] ?? 0) + ($waliRekap1Tahun['izin'] ?? 0) }}</div>
            <div style="font-size:11.5px; color:#f59e0b; margin-top:2px">Akumulasi 1 Tahun</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #ef4444">
            <div style="font-size:11.5px; font-weight:700; color:#b91c1c; text-transform:uppercase; letter-spacing:0.5px">Total Alpa 1 Tahun</div>
            <div style="font-size:26px; font-weight:800; color:#991b1b; margin-top:4px">{{ $waliRekap1Tahun['alpa'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#ef4444; margin-top:2px">Tanpa Keterangan</div>
        </div>
    </div>

    <!-- ══ NAVIGASI TAB REKAP 1 TAHUN ══ -->
    <div style="display:flex; gap:10px; margin-bottom:18px; border-bottom:2px solid #e2e8f0; padding-bottom:8px">
        <button type="button" onclick="switchWaliTab1Tahun('absensi')" id="tab-btn-wali-absensi-1th" class="btn-primary" style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700">
            Rekap Presensi Siswa (1 Tahun)
        </button>
        <button type="button" onclick="switchWaliTab1Tahun('jurnal')" id="tab-btn-wali-jurnal-1th" class="btn-secondary" style="padding:8px 16px; border-radius:8px; font-size:13px; font-weight:700; border:1px solid #cbd5e1">
            Rekap Jurnal Pembelajaran (1 Tahun)
        </button>
    </div>

    <!-- ══ TAB 1: REKAP PRESENSI SISWA 1 TAHUN ══ -->
    <div id="wali-tab-absensi-1th" class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Akumulasi Absensi Siswa 1 Tahun Ajaran (Kelas {{ $namaKelasAktif }})
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Tahun Ajaran: <strong>{{ $waliTahun }} / {{ $waliTahun + 1 }}</strong>
                </div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-rekap-1th" onkeyup="filterTable('search-wali-rekap-1th', 'table-wali-rekap-1th')" placeholder="Cari siswa / NISN..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-rekap-1th" style="min-width:950px">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th style="text-align:center">L/P</th>
                        <th style="text-align:center">Hadir (H)</th>
                        <th style="text-align:center">Sakit (S)</th>
                        <th style="text-align:center">Izin (I)</th>
                        <th style="text-align:center">Alpa (A)</th>
                        <th style="width:180px; text-align:center">Persentase 1 Tahun</th>
                        <th style="text-align:center">Predikat Performa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliRekap1Tahun['siswa'] ?? [] as $idx => $r)
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada data rekapitulasi presensi untuk tahun ajaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ TAB 2: REKAP JURNAL PEMBELAJARAN 1 TAHUN ══ -->
    <div id="wali-tab-jurnal-1th" class="card" style="padding:22px 24px; display:none">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Daftar Seluruh Jurnal Pembelajaran 1 Tahun (Kelas {{ $namaKelasAktif }})
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Riwayat lengkap pengajaran guru-guru mapel selama 1 tahun ajaran {{ $waliTahun }}
                </div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-jurnal-1th" onkeyup="filterTable('search-wali-jurnal-1th', 'table-wali-jurnal-1th')" placeholder="Cari mapel / guru / tanggal..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-jurnal-1th" style="min-width:900px">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th style="text-align:center">Kehadiran Guru</th>
                        <th>Materi Pembelajaran</th>
                        <th style="text-align:center">Jumlah Hadir Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliJurnal1Tahun as $idx => $j)
                        <tr>
                            <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                            <td style="font-weight:600; color:#334155; font-size:12.5px">
                                {{ date('d-m-Y', strtotime($j->tanggal)) }}
                            </td>
                            <td><strong style="color:#0f172a">{{ $j->nama_mapel }}</strong></td>
                            <td style="font-weight:600; color:#475569">{{ $j->nama_guru }}</td>
                            <td style="text-align:center">
                                @if($j->status_kehadiran_guru === 'Hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @else
                                    <span class="badge badge-danger">Tidak Hadir</span>
                                @endif
                            </td>
                            <td style="font-size:12.5px; color:#334155">
                                {{ $j->materi ?? '-' }}
                            </td>
                            <td style="text-align:center">
                                <span class="badge badge-primary" style="font-size:12px">
                                    {{ $j->jumlah_hadir }} Siswa
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada riwayat jurnal pembelajaran untuk tahun ajaran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function switchWaliTab1Tahun(tab) {
    const tabAbsensi = document.getElementById('wali-tab-absensi-1th');
    const tabJurnal = document.getElementById('wali-tab-jurnal-1th');
    const btnAbsensi = document.getElementById('tab-btn-wali-absensi-1th');
    const btnJurnal = document.getElementById('tab-btn-wali-jurnal-1th');

    if (tab === 'absensi') {
        tabAbsensi.style.display = 'block';
        tabJurnal.style.display = 'none';
        btnAbsensi.className = 'btn-primary';
        btnJurnal.className = 'btn-secondary';
    } else {
        tabAbsensi.style.display = 'none';
        tabJurnal.style.display = 'block';
        btnAbsensi.className = 'btn-secondary';
        btnJurnal.className = 'btn-primary';
    }
}
</script>
