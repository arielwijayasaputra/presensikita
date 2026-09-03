<div class="page-content page-anim" id="page-wali-rekap-jurnal" style="display:none">

    @php
        $namaKelasAktif = $waliKelasObj->nama_kelas ?? session('auth_nama_kelas', 'Kelas');
    @endphp

    <!-- ══ HEADER REKAP JURNAL KELAS ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Rekapitulasi Jurnal Pembelajaran</h1>
                <span class="badge badge-primary" style="font-size:13px; padding:6px 12px; border-radius:8px">{{ $namaKelasAktif }}</span>
            </div>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">
                Pilih periode tanggal untuk memfilter riwayat jurnal pengajaran di kelas <strong>{{ $namaKelasAktif }}</strong>
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <!-- Filter Rentang Tanggal Jurnal -->
            <form method="GET" action="{{ route('walikelas.index') }}" style="display:flex; align-items:center; gap:8px; background:#fff; padding:6px 12px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <label style="font-size:12px; font-weight:700; color:#475569">Dari:</label>
                <input type="date" name="jurnal_tgl_mulai" value="{{ $waliTglMulaiJurnal }}" class="filter-input" style="padding:4px 8px; font-size:12px; border-radius:6px; border:1px solid #cbd5e1">
                
                <label style="font-size:12px; font-weight:700; color:#475569">S/D:</label>
                <input type="date" name="jurnal_tgl_selesai" value="{{ $waliTglSelesaiJurnal }}" class="filter-input" style="padding:4px 8px; font-size:12px; border-radius:6px; border:1px solid #cbd5e1">

                <button type="submit" class="btn-primary" style="font-size:12px; padding:5px 12px; border-radius:6px; background:#3b82f6">Tampilkan</button>
            </form>

            <!-- Ekspor CSV Jurnal -->
            <a href="{{ route('walikelas.export-jurnal', ['jurnal_tgl_mulai' => $waliTglMulaiJurnal, 'jurnal_tgl_selesai' => $waliTglSelesaiJurnal]) }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #059669, #10b981); text-decoration:none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor CSV Jurnal</span>
            </a>

            <!-- Cetak Laporan -->
            <button type="button" onclick="window.print()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak Jurnal</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK JURNAL ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px; border-left:4px solid #3b82f6">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Jurnal Terisi</div>
            <div style="font-size:26px; font-weight:800; color:#1e293b; margin-top:4px">{{ count($waliRekapJurnalList) }} Sesi</div>
            <div style="font-size:11.5px; color:#94a3b8; margin-top:2px">Periode {{ date('d M Y', strtotime($waliTglMulaiJurnal)) }} - {{ date('d M Y', strtotime($waliTglSelesaiJurnal)) }}</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #8b5cf6">
            <div style="font-size:11.5px; font-weight:700; color:#6d28d9; text-transform:uppercase; letter-spacing:0.5px">Guru Pengajar</div>
            <div style="font-size:26px; font-weight:800; color:#5b21b6; margin-top:4px">{{ $waliRekapJurnalList->pluck('nama_guru')->unique()->count() }} Guru</div>
            <div style="font-size:11.5px; color:#8b5cf6; margin-top:2px">Guru Mapel Aktif</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #10b981">
            <div style="font-size:11.5px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Mata Pelajaran</div>
            <div style="font-size:26px; font-weight:800; color:#065f46; margin-top:4px">{{ $waliRekapJurnalList->pluck('nama_mapel')->unique()->count() }} Mapel</div>
            <div style="font-size:11.5px; color:#10b981; margin-top:2px">Variasi Mapel Diajarkan</div>
        </div>
    </div>

    <!-- ══ TABEL RINCIAN JURNAL PEMBELAJARAN ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Daftar Riwayat Jurnal Pembelajaran Guru (Kelas {{ $namaKelasAktif }})
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Periode: <strong>{{ date('d-m-Y', strtotime($waliTglMulaiJurnal)) }}</strong> s/d <strong>{{ date('d-m-Y', strtotime($waliTglSelesaiJurnal)) }}</strong>
                </div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-rekap-jurnal-tbl" onkeyup="filterTable('search-wali-rekap-jurnal-tbl', 'table-wali-rekap-jurnal-tbl')" placeholder="Cari mapel / guru / materi..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-rekap-jurnal-tbl" style="min-width:900px">
                <thead>
                    <tr>
                        <th style="width:45px">No</th>
                        <th>Tanggal</th>
                        <th>Waktu Input</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th style="text-align:center">Kehadiran Guru</th>
                        <th>Materi Pembelajaran</th>
                        <th style="text-align:center">Hadir Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliRekapJurnalList as $idx => $j)
                        <tr>
                            <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                            <td style="font-weight:600; color:#334155; font-size:12.5px">
                                {{ date('d-m-Y', strtotime($j->tanggal)) }}
                            </td>
                            <td style="font-family:monospace; font-size:12px; color:#64748b">
                                {{ date('H:i', strtotime($j->waktu_input)) }} WIB
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
                            <td colspan="8" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada riwayat jurnal pembelajaran untuk periode tanggal terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
