<div class="page-content page-anim" id="page-waka-sdm" style="display:block">

    <!-- ══ HEADER & ACTIONS ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Dashboard Waka SDM</h1>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">Kontrol &amp; Pemantauan Kehadiran Mengajar Guru serta Data Perizinan</p>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <!-- Filter Tanggal Harian -->
            <form method="GET" action="{{ route('wakasdm.index') }}" style="display:flex; align-items:center; gap:8px; background:#fff; padding:6px 12px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <label for="tanggal_sdm" style="font-size:12px; font-weight:700; color:#475569">Hari Ini:</label>
                <input type="date" id="tanggal_sdm" name="tanggal" value="{{ $sdmTanggal }}" onchange="this.form.submit()" class="filter-input" style="padding:4px 8px; font-size:12.5px; border-radius:6px; border:1px solid #cbd5e1">
                @if(request('sdm_bulan'))
                    <input type="hidden" name="sdm_bulan" value="{{ $sdmBulan }}">
                @endif
                @if(request('sdm_tahun'))
                    <input type="hidden" name="sdm_tahun" value="{{ $sdmTahun }}">
                @endif
            </form>

            <!-- Tombol Export CSV -->
            <a href="{{ route('wakasdm.export', ['bulan' => $sdmBulan, 'tahun' => $sdmTahun]) }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #059669, #10b981); text-decoration:none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor Excel / CSV</span>
            </a>

            <!-- Tombol Cetak -->
            <button type="button" onclick="window.print()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK HARIAN ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
        
        <div class="card" style="padding:20px; border-left:4px solid #3b82f6">
            <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Jadwal Harian</div>
            <div style="font-size:28px; font-weight:800; color:#1e293b; margin-top:6px">{{ $sdmJadwal->count() }}</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:2px">Sesi {{ \Carbon\Carbon::parse($sdmTanggal)->translatedFormat('l') }}</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #10b981">
            <div style="font-size:12px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Hadir Mengajar</div>
            <div style="font-size:28px; font-weight:800; color:#065f46; margin-top:6px">{{ $sdmStatHadir }}</div>
            <div style="font-size:12px; color:#10b981; margin-top:2px">Jurnal Terisi (Hadir)</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #ef4444">
            <div style="font-size:12px; font-weight:700; color:#b91c1c; text-transform:uppercase; letter-spacing:0.5px">Tidak Hadir / Izin</div>
            <div style="font-size:28px; font-weight:800; color:#991b1b; margin-top:6px">{{ $sdmStatTidakHadir }}</div>
            <div style="font-size:12px; color:#ef4444; margin-top:2px">Konfirmasi Tidak Hadir</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #f59e0b">
            <div style="font-size:12px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px">Belum Mengisi Jurnal</div>
            <div style="font-size:28px; font-weight:800; color:#78350f; margin-top:6px">{{ $sdmStatBelumIsi }}</div>
            <div style="font-size:12px; color:#f59e0b; margin-top:2px">Perlu Diingatkan</div>
        </div>

    </div>

    <!-- ══ TABEL LOG KEHADIRAN MENGAJAR HARIAN ══ -->
    <div class="card" style="padding:22px 24px; margin-bottom:28px">
        <div class="card-header" style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Monitoring Kehadiran &amp; Jurnal Mengajar Harian ({{ \Carbon\Carbon::parse($sdmTanggal)->format('d-m-Y') }})
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">Pemantauan jam mengajar per sesi hari {{ \Carbon\Carbon::parse($sdmTanggal)->translatedFormat('l') }}</div>
            </div>

            <!-- Searching Harian -->
            <div style="position:relative; width:260px">
                <input type="text" id="search-harian" onkeyup="filterTable('search-harian', 'table-harian')" placeholder="Cari guru, mapel, atau kelas..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-harian" style="min-width:900px">
                <thead>
                    <tr>
                        <th style="width:80px">Jam Ke</th>
                        <th>Waktu</th>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th style="text-align:center">Status Kehadiran</th>
                        <th>Materi / Waktu Input</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sdmJadwal as $item)
                        <tr>
                            <td><strong>Ke-{{ $item->jam_ke >= 100 ? $item->jam_ke - 100 : $item->jam_ke }}</strong></td>
                            <td style="font-size:12.5px; color:#64748b">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td><strong style="color:#0f172a">{{ $item->nama_guru }}</strong></td>
                            <td>{{ $item->nama_mapel }}</td>
                            <td><span class="badge badge-secondary" style="font-size:12px">{{ $item->nama_kelas }}</span></td>
                            <td style="text-align:center">
                                @if($item->status_jurnal === 'Hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($item->status_jurnal === 'Tidak Hadir')
                                    <span class="badge badge-danger">Tidak Hadir / Izin</span>
                                @else
                                    <span class="badge badge-warning">Belum Isi Jurnal</span>
                                @endif
                            </td>
                            <td>
                                @if($item->materi)
                                    <div style="font-size:12.5px; font-weight:600; color:#334155">{{ $item->materi }}</div>
                                    @if($item->waktu_input)
                                        <div style="font-size:11px; color:#94a3b8">Diisi: {{ \Carbon\Carbon::parse($item->waktu_input)->format('H:i') }} WIB</div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8; font-size:12px">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#64748b; padding:24px">
                                Tidak ada jadwal mengajar pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ SECTION REKAP PRESENSI MENGAJAR PER GURU (BULANAN) ══ -->
    <div class="card" style="padding:22px 24px; margin-bottom:28px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Rekapitulasi Presensi Mengajar Per Guru (Bulanan)
                </div>
                @php
                    $namaBulanList = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                @endphp
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Periode: <strong>{{ $namaBulanList[$sdmBulan] ?? $sdmBulan }} {{ $sdmTahun }}</strong>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap">
                <!-- Form Filter Bulan & Tahun -->
                <form method="GET" action="{{ route('wakasdm.index') }}" style="display:flex; align-items:center; gap:8px">
                    @if(request('tanggal'))
                        <input type="hidden" name="tanggal" value="{{ $sdmTanggal }}">
                    @endif
                    <select name="sdm_bulan" onchange="this.form.submit()" class="filter-input" style="padding:6px 10px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                        @foreach($namaBulanList as $mNum => $mName)
                            <option value="{{ $mNum }}" {{ $sdmBulan == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                        @endforeach
                    </select>

                    <select name="sdm_tahun" onchange="this.form.submit()" class="filter-input" style="padding:6px 10px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                        @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ $sdmTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>

                <!-- Search Rekap Bulanan -->
                <div style="position:relative; width:220px">
                    <input type="text" id="search-rekap" onkeyup="filterTable('search-rekap', 'table-rekap')" placeholder="Cari nama guru / NIP..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-rekap" style="min-width:950px">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NIP</th>
                        <th>Nama Guru</th>
                        <th style="text-align:center">Total Sesi Mengajar</th>
                        <th style="text-align:center">Hadir</th>
                        <th style="text-align:center">Tidak Hadir / Izin</th>
                        <th style="text-align:center">Belum Isi Jurnal</th>
                        <th style="width:180px; text-align:center">Persentase Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sdmRekapGuru as $idx => $r)
                        <tr>
                            <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                            <td style="font-family:monospace; font-size:12.5px; color:#64748b">{{ $r['nip'] }}</td>
                            <td><strong style="color:#0f172a">{{ $r['nama_guru'] }}</strong></td>
                            <td style="text-align:center; font-weight:700; color:#1e293b">{{ $r['total_sesi'] }} Sesi</td>
                            <td style="text-align:center">
                                <span class="badge badge-success" style="font-size:12px">{{ $r['hadir'] }}</span>
                            </td>
                            <td style="text-align:center">
                                @if($r['tidak_hadir'] > 0)
                                    <span class="badge badge-danger" style="font-size:12px">{{ $r['tidak_hadir'] }}</span>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">0</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                @if($r['belum_isi'] > 0)
                                    <span class="badge badge-warning" style="font-size:12px">{{ $r['belum_isi'] }}</span>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:#64748b; padding:22px">
                                Data rekapitulasi belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ TABEL DATA PERIZINAN GURU ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
            <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                Daftar Permintaan Izin / Ketidakhadiran Guru
            </div>
            
            <div style="position:relative; width:220px">
                <input type="text" id="search-izin" onkeyup="filterTable('search-izin', 'table-izin')" placeholder="Cari nama guru / alasan..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-izin" style="min-width:850px">
                <thead>
                    <tr>
                        <th>Tanggal Izin</th>
                        <th>Nama Guru</th>
                        <th>Alasan Izin</th>
                        <th style="text-align:center">Status Kepsek</th>
                        <th style="text-align:center">Status Waka</th>
                        <th style="text-align:center">Detail / Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sdmIzinGuru as $izin)
                        <tr>
                            <td><strong>{{ $izin->tanggal_izin->format('d-m-Y') }}</strong></td>
                            <td><strong style="color:#0f172a">{{ $izin->guru->nama_guru ?? '-' }}</strong></td>
                            <td>{{ $izin->alasan }}</td>
                            <td style="text-align:center">
                                @if($izin->status_kepsek === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($izin->status_kepsek === 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                @if($izin->status_waka === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($izin->status_waka === 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <a href="{{ URL::temporarySignedRoute('izin-guru.public', now()->addDays(2), ['izin' => $izin->id_izin_guru]) }}" target="_blank" style="font-size:12.5px; font-weight:600; color:#2563eb">
                                    Buka Surat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada pengajuan izin guru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ══ SCRIPT FOR LIVE SEARCHING & PRINT ══ -->
<script>
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const trs = table.getElementsByTagName("tr");

    for (let i = 1; i < trs.length; i++) {
        let textContent = trs[i].textContent || trs[i].innerText;
        if (textContent.toLowerCase().indexOf(filter) > -1) {
            trs[i].style.display = "";
        } else {
            trs[i].style.display = "none";
        }
    }
}
</script>

<style>
@media print {
    .sidebar, .header, .btn-primary, .btn-secondary, form, .filter-input, #search-harian, #search-rekap, #search-izin {
        display: none !important;
    }
    .main-content, .page-content {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
}
</style>
