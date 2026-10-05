<div class="page-content page-anim" id="page-waka-sdm" style="display:block">

    <!-- ══ HEADER & ACTIONS ══ -->
    <div class="sdm-header-wrap">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:var(--text-primary, #0f172a); margin:0">Dashboard Waka SDM</h1>
            <p style="font-size:13px; color:var(--text-secondary, #64748b); margin:4px 0 0 0">Kontrol &amp; Pemantauan Kehadiran Mengajar Guru serta Data Perizinan</p>
        </div>

        <div class="sdm-actions-wrap">
            <!-- Filter Tanggal Harian -->
            <form method="GET" action="{{ route('wakasdm.index') }}" class="sdm-filter-form">
                <label for="tanggal_sdm" style="font-size:12px; font-weight:700; color:var(--text-secondary, #475569)">Hari Ini:</label>
                <input type="date" id="tanggal_sdm" name="tanggal" value="{{ $sdmTanggal }}" onchange="this.form.submit()" class="filter-input" style="padding:4px 8px; font-size:12.5px; border-radius:6px; border:1px solid var(--input-border, #cbd5e1); background:var(--card-bg)">
                @if(request('sdm_bulan'))
                    <input type="hidden" name="sdm_bulan" value="{{ $sdmBulan }}">
                @endif
                @if(request('sdm_tahun'))
                    <input type="hidden" name="sdm_tahun" value="{{ $sdmTahun }}">
                @endif
            </form>

            <div class="sdm-btn-group">
                <!-- Tombol Export PDF -->
                <a href="{{ route('wakasdm.export-pdf', ['bulan' => $sdmBulan, 'tahun' => $sdmTahun]) }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #1e293b, #334155); text-decoration:none">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span>PDF</span>
                </a>

                <!-- Tombol Export CSV -->
                <a href="{{ route('wakasdm.export', ['bulan' => $sdmBulan, 'tahun' => $sdmTahun]) }}" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid var(--border, #cbd5e1); text-decoration:none">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span>CSV</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK HARIAN ══ -->
    <div class="sdm-stat-cards">
        
        <div class="card sdm-stat-card" style="padding:16px 18px; border-left:4px solid #3b82f6">
            <div style="font-size:11.5px; font-weight:700; color:var(--text-secondary, #64748b); text-transform:uppercase; letter-spacing:0.5px">Total Jadwal Harian</div>
            <div style="font-size:24px; font-weight:800; color:var(--text-primary, #1e293b); margin-top:4px">{{ $sdmJadwal->count() }}</div>
            <div style="font-size:11.5px; color:var(--text-muted, #94a3b8); margin-top:2px">Sesi {{ \Carbon\Carbon::parse($sdmTanggal)->translatedFormat('l') }}</div>
        </div>

        <div class="card sdm-stat-card" style="padding:16px 18px; border-left:4px solid #10b981">
            <div style="font-size:11.5px; font-weight:700; color:#10b981; text-transform:uppercase; letter-spacing:0.5px">Hadir Mengajar</div>
            <div style="font-size:24px; font-weight:800; color:var(--text-primary, #065f46); margin-top:4px">{{ $sdmStatHadir }}</div>
            <div style="font-size:11.5px; color:#10b981; margin-top:2px">Jurnal Terisi (Hadir)</div>
        </div>

        <div class="card sdm-stat-card" style="padding:16px 18px; border-left:4px solid #ef4444">
            <div style="font-size:11.5px; font-weight:700; color:#ef4444; text-transform:uppercase; letter-spacing:0.5px">Tidak Hadir / Izin</div>
            <div style="font-size:24px; font-weight:800; color:var(--text-primary, #991b1b); margin-top:4px">{{ $sdmStatTidakHadir }}</div>
            <div style="font-size:11.5px; color:#ef4444; margin-top:2px">Konfirmasi Tidak Hadir</div>
        </div>

        <div class="card sdm-stat-card" style="padding:16px 18px; border-left:4px solid #f59e0b">
            <div style="font-size:11.5px; font-weight:700; color:#f59e0b; text-transform:uppercase; letter-spacing:0.5px">Belum Mengisi Jurnal</div>
            <div style="font-size:24px; font-weight:800; color:var(--text-primary, #78350f); margin-top:4px">{{ $sdmStatBelumIsi }}</div>
            <div style="font-size:11.5px; color:#f59e0b; margin-top:2px">Perlu Diingatkan</div>
        </div>

    </div>

    <style>
        /* Scoped styles for Waka SDM Dashboard */
        #page-waka-sdm .sdm-header-wrap {
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;
        }
        #page-waka-sdm .sdm-actions-wrap {
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        }
        #page-waka-sdm .sdm-filter-form {
            display: flex; align-items: center; gap: 8px; background: var(--card-bg, #fff); padding: 6px 12px; border-radius: 10px; border: 1px solid var(--border, #cbd5e1); box-shadow: var(--shadow, 0 1px 3px rgba(0,0,0,0.05));
        }
        #page-waka-sdm .sdm-btn-group {
            display: flex; align-items: center; gap: 8px;
        }
        #page-waka-sdm .sdm-stat-cards {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;
        }

        #page-waka-sdm .sdm-m-lbl,
        #page-waka-sdm .sdm-m-stat-lbl,
        #page-waka-sdm .sdm-m-bar-lbl,
        #page-waka-sdm .sdm-m-waktu,
        #page-waka-sdm .sdm-h-status-m {
            display: none !important;
        }

        @media (max-width: 768px) {
            #page-waka-sdm .sdm-header-wrap {
                flex-direction: column;
                align-items: stretch;
                gap: 14px;
                margin-bottom: 18px;
            }
            #page-waka-sdm .sdm-actions-wrap {
                width: 100%;
                justify-content: space-between;
                gap: 8px;
            }
            #page-waka-sdm .sdm-filter-form {
                flex: 1;
                justify-content: space-between;
            }
            #page-waka-sdm .sdm-stat-cards {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
                margin-bottom: 18px !important;
            }
            #page-waka-sdm .sdm-stat-card {
                padding: 12px 14px !important;
                border-radius: 12px !important;
            }

            #page-waka-sdm .sdm-scroll-wrap {
                overflow: visible !important;
                width: 100% !important;
            }
            #page-waka-sdm .data-table {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                white-space: normal !important;
                border: none !important;
                background: transparent !important;
            }
            #page-waka-sdm .data-table thead {
                display: none !important;
            }
            #page-waka-sdm .data-table tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                width: 100% !important;
            }

            /* ── Card Log Harian ── */
            #page-waka-sdm .sdm-row-harian {
                display: flex !important;
                flex-direction: column !important;
                gap: 8px !important;
                background: var(--card-bg, #fff) !important;
                border: 1px solid var(--border, #e2e8f0) !important;
                border-radius: 14px !important;
                padding: 14px 14px !important;
                box-shadow: var(--shadow, 0 1px 3px rgba(0,0,0,0.06)) !important;
            }
            #page-waka-sdm .sdm-row-harian td {
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
            }
            #page-waka-sdm .sdm-h-top {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                width: 100% !important;
            }
            #page-waka-sdm .sdm-h-jam-box {
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
                font-size: 12.5px !important;
            }
            #page-waka-sdm .sdm-m-waktu {
                display: inline-block !important;
                font-size: 11.5px !important;
                color: var(--text-secondary, #64748b) !important;
                font-weight: normal !important;
            }
            #page-waka-sdm .sdm-h-status-m {
                display: block !important;
            }
            #page-waka-sdm .sdm-h-waktu-td,
            #page-waka-sdm .sdm-h-status-d {
                display: none !important;
            }
            #page-waka-sdm .sdm-h-guru {
                font-size: 14.5px !important;
                font-weight: 800 !important;
                color: var(--text-primary) !important;
                line-height: 1.3 !important;
                margin-top: 1px !important;
            }
            #page-waka-sdm .sdm-h-mapel {
                font-size: 12.5px !important;
                color: var(--text-secondary, #64748b) !important;
                line-height: 1.35 !important;
            }
            #page-waka-sdm .sdm-h-kelas {
                display: inline-block !important;
                margin-top: 2px !important;
            }
            #page-waka-sdm .sdm-h-materi {
                padding-top: 8px !important;
                border-top: 1px dashed var(--border, #e2e8f0) !important;
                font-size: 12px !important;
                margin-top: 2px !important;
            }

            /* ── Card Rekap Bulanan ── */
            #page-waka-sdm .sdm-row-rekap {
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 8px 6px !important;
                background: var(--card-bg, #fff) !important;
                border: 1px solid var(--border, #e2e8f0) !important;
                border-radius: 14px !important;
                padding: 14px 12px !important;
                box-shadow: var(--shadow, 0 1px 3px rgba(0,0,0,0.06)) !important;
            }
            #page-waka-sdm .sdm-r-no {
                display: none !important;
            }
            #page-waka-sdm .sdm-r-guru {
                grid-row: 1 !important;
                grid-column: 1 / -1 !important;
                padding: 0 !important;
                border: none !important;
                font-size: 14.5px !important;
                font-weight: 800 !important;
                color: var(--text-primary) !important;
                line-height: 1.3 !important;
            }
            #page-waka-sdm .sdm-r-nip {
                grid-row: 2 !important;
                grid-column: 1 / -1 !important;
                padding: 0 0 4px 0 !important;
                border: none !important;
                font-size: 11.5px !important;
                color: var(--text-secondary, #64748b) !important;
            }
            #page-waka-sdm .sdm-r-sesi { grid-row: 3 !important; grid-column: 1 !important; }
            #page-waka-sdm .sdm-r-hadir { grid-row: 3 !important; grid-column: 2 !important; }
            #page-waka-sdm .sdm-r-thadir { grid-row: 3 !important; grid-column: 3 !important; }
            #page-waka-sdm .sdm-r-belum { grid-row: 3 !important; grid-column: 4 !important; }
            #page-waka-sdm .sdm-r-sesi,
            #page-waka-sdm .sdm-r-hadir,
            #page-waka-sdm .sdm-r-thadir,
            #page-waka-sdm .sdm-r-belum {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                background: var(--border-subtle, #f8fafc) !important;
                border: 1px solid var(--border, #e2e8f0) !important;
                border-radius: 9px !important;
                padding: 7px 2px !important;
                min-height: 48px !important;
                gap: 3px !important;
            }
            #page-waka-sdm .sdm-m-stat-lbl {
                display: block !important;
                font-size: 9px !important;
                font-weight: 700 !important;
                color: var(--text-muted, #94a3b8) !important;
                text-transform: uppercase !important;
                letter-spacing: 0.3px !important;
                line-height: 1 !important;
                text-align: center !important;
            }
            #page-waka-sdm .sdm-r-bar {
                grid-row: 4 !important;
                grid-column: 1 / -1 !important;
                display: block !important;
                padding: 8px 0 0 0 !important;
                border: none !important;
                border-top: 1px dashed var(--border, #e2e8f0) !important;
            }
            #page-waka-sdm .sdm-m-bar-lbl {
                display: inline-block !important;
                font-size: 11.5px !important;
                font-weight: 700 !important;
                color: var(--text-secondary, #64748b) !important;
            }

            /* ── Card Izin Guru ── */
            #page-waka-sdm .sdm-row-izin {
                display: flex !important;
                flex-direction: column !important;
                gap: 8px !important;
                background: var(--card-bg, #fff) !important;
                border: 1px solid var(--border, #e2e8f0) !important;
                border-radius: 14px !important;
                padding: 14px 14px !important;
                box-shadow: var(--shadow, 0 1px 3px rgba(0,0,0,0.06)) !important;
            }
            #page-waka-sdm .sdm-row-izin td {
                padding: 0 !important;
                border: none !important;
                text-align: left !important;
            }
            #page-waka-sdm .sdm-i-head {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            #page-waka-sdm .sdm-i-status-row {
                display: flex !important;
                gap: 10px !important;
                align-items: center !important;
                flex-wrap: wrap !important;
            }
            #page-waka-sdm .sdm-i-footer {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding-top: 8px !important;
                border-top: 1px dashed var(--border, #e2e8f0) !important;
                margin-top: 2px !important;
            }
        }
    </style>

    <!-- ══ TABEL LOG KEHADIRAN MENGAJAR HARIAN ══ -->
    <div class="card" style="padding:22px 24px; margin-bottom:28px">
        <div class="card-header" style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:var(--text-primary, #0f172a)">
                    Monitoring Kehadiran &amp; Jurnal Mengajar Harian ({{ \Carbon\Carbon::parse($sdmTanggal)->format('d-m-Y') }})
                </div>
                <div style="font-size:12px; color:var(--text-secondary, #64748b); margin-top:2px">Pemantauan jam mengajar per sesi hari {{ \Carbon\Carbon::parse($sdmTanggal)->translatedFormat('l') }}</div>
            </div>

            <!-- Searching Harian -->
            <div style="position:relative; width:260px; max-width:100%">
                <input type="text" id="search-harian" oninput="filterTable('search-harian', 'table-harian')" onkeyup="filterTable('search-harian', 'table-harian')" placeholder="Cari guru, mapel, atau kelas..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div class="sdm-scroll-wrap" style="overflow-x:auto; overflow-y:visible; width:100%; display:block">
            <table class="data-table" id="table-harian" style="min-width:900px; white-space:nowrap">
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
                        <tr class="sdm-row-harian">
                            <td class="sdm-h-jam">
                                <div class="sdm-h-top">
                                    <div class="sdm-h-jam-box">
                                        <strong>Ke-{{ $item->jam_ke >= 100 ? $item->jam_ke - 100 : $item->jam_ke }}</strong>
                                        <span class="sdm-m-waktu">• {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                    </div>
                                    <div class="sdm-h-status-m">
                                        @if($item->status_jurnal === 'Hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($item->status_jurnal === 'Tidak Hadir')
                                            <span class="badge badge-danger">Tidak Hadir / Izin</span>
                                        @else
                                            <span class="badge badge-warning">Belum Isi Jurnal</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="sdm-h-waktu-td" style="font-size:12.5px; color:#64748b">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</td>
                            <td class="sdm-h-guru"><strong style="color:var(--text-primary, #0f172a)">{{ $item->nama_guru }}</strong></td>
                            <td class="sdm-h-mapel">{{ $item->nama_mapel }}</td>
                            <td class="sdm-h-kelas"><span class="badge badge-secondary" style="font-size:12px">{{ $item->nama_kelas }}</span></td>
                            <td class="sdm-h-status-d" style="text-align:center">
                                @if($item->status_jurnal === 'Hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($item->status_jurnal === 'Tidak Hadir')
                                    <span class="badge badge-danger">Tidak Hadir / Izin</span>
                                @else
                                    <span class="badge badge-warning">Belum Isi Jurnal</span>
                                @endif
                            </td>
                            <td class="sdm-h-materi">
                                @if($item->materi)
                                    <div style="font-size:12.5px; font-weight:600; color:var(--text-primary, #334155)">Materi: {{ $item->materi }}</div>
                                    @if($item->waktu_input)
                                        <div style="font-size:11px; color:var(--text-muted, #94a3b8); margin-top:2px">Diisi pukul {{ \Carbon\Carbon::parse($item->waktu_input)->format('H:i') }} WIB</div>
                                    @endif
                                @else
                                    <span style="color:var(--text-muted, #94a3b8); font-size:12px; font-style:italic">Materi belum diisi</span>
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
                <div class="card-title" style="font-size:16px; font-weight:700; color:var(--text-primary, #0f172a)">
                    Rekapitulasi Presensi Mengajar Per Guru (Bulanan)
                </div>
                @php
                    $namaBulanList = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                @endphp
                <div style="font-size:12px; color:var(--text-secondary, #64748b); margin-top:2px">
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
                <div style="position:relative; width:220px; max-width:100%">
                    <input type="text" id="search-rekap" oninput="filterTable('search-rekap', 'table-rekap')" onkeyup="filterTable('search-rekap', 'table-rekap')" placeholder="Cari nama guru / NIP..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>
        </div>

        <div class="sdm-scroll-wrap" style="overflow-x:auto; overflow-y:visible; width:100%; display:block">
            <table class="data-table" id="table-rekap" style="min-width:950px; white-space:nowrap">
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
                        <tr class="sdm-row-rekap">
                            <td class="sdm-r-no" style="color:var(--text-muted, #94a3b8); font-weight:600">{{ $idx + 1 }}</td>
                            <td class="sdm-r-guru"><strong style="color:var(--text-primary, #0f172a)">{{ $r['nama_guru'] }}</strong></td>
                            <td class="sdm-r-nip" style="font-family:monospace; font-size:12.5px; color:var(--text-secondary, #64748b); white-space:nowrap">
                                <span class="sdm-m-lbl">NIP:</span> {{ $r['nip'] ?: '-' }}
                            </td>
                            <td class="sdm-r-sesi" style="text-align:center; font-weight:700; color:var(--text-primary, #1e293b)">
                                <span class="sdm-m-stat-lbl">Sesi</span>
                                {{ $r['total_sesi'] }} Sesi
                            </td>
                            <td class="sdm-r-hadir" style="text-align:center">
                                <span class="sdm-m-stat-lbl">Hadir</span>
                                <span class="badge badge-success" style="font-size:12px">{{ $r['hadir'] }}</span>
                            </td>
                            <td class="sdm-r-thadir" style="text-align:center">
                                <span class="sdm-m-stat-lbl">Tdk Hadir</span>
                                @if($r['tidak_hadir'] > 0)
                                    <span class="badge badge-danger" style="font-size:12px">{{ $r['tidak_hadir'] }}</span>
                                @else
                                    <span style="color:var(--text-muted, #94a3b8); font-size:12px">0</span>
                                @endif
                            </td>
                            <td class="sdm-r-belum" style="text-align:center">
                                <span class="sdm-m-stat-lbl">Belum Isi</span>
                                @if($r['belum_isi'] > 0)
                                    <span class="badge badge-warning" style="font-size:12px">{{ $r['belum_isi'] }}</span>
                                @else
                                    <span style="color:var(--text-muted, #94a3b8); font-size:12px">0</span>
                                @endif
                            </td>
                            <td class="sdm-r-bar" style="text-align:center">
                                <div style="display:flex; align-items:center; gap:8px; justify-content:center">
                                    <span class="sdm-m-bar-lbl">Kehadiran:</span>
                                    <div style="flex:1; background:var(--border-subtle, #e2e8f0); height:8px; border-radius:4px; overflow:hidden">
                                        <div style="width:{{ $r['persentase'] }}%; height:100%; background:{{ $r['persentase'] >= 85 ? '#10b981' : ($r['persentase'] >= 70 ? '#f59e0b' : '#ef4444') }}"></div>
                                    </div>
                                    <span style="font-size:12px; font-weight:700; color:var(--text-primary, #334155); width:45px; text-align:right">{{ $r['persentase'] }}%</span>
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
            <div class="card-title" style="font-size:16px; font-weight:700; color:var(--text-primary, #0f172a)">
                Daftar Permintaan Izin / Ketidakhadiran Guru
            </div>
            
            <div style="position:relative; width:220px; max-width:100%">
                <input type="text" id="search-izin" oninput="filterTable('search-izin', 'table-izin')" onkeyup="filterTable('search-izin', 'table-izin')" placeholder="Cari nama guru / alasan..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div class="sdm-scroll-wrap" style="overflow-x:auto; overflow-y:visible; width:100%; display:block">
            <table class="data-table" id="table-izin" style="min-width:850px; white-space:nowrap">
                <thead>
                    <tr>
                        <th>Tanggal Izin</th>
                        <th>Nama Guru</th>
                        <th>Alasan Izin</th>
                        <th style="text-align:center">Status Kepsek</th>
                        <th style="text-align:center">Status Waka</th>
                        <th style="text-align:center">Tanda Tangan</th>
                        <th style="text-align:center">Detail / Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sdmIzinGuru as $izin)
                        <tr class="sdm-row-izin">
                            <td>
                                <div class="sdm-i-head">
                                    <strong style="color:var(--text-primary, #0f172a); font-size:13.5px">{{ $izin->guru->nama_guru ?? '-' }}</strong>
                                    <span style="font-size:12px; color:var(--text-secondary, #64748b); font-weight:600">{{ $izin->tanggal_izin->format('d-m-Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:12.5px; color:var(--text-secondary, #475569)">
                                    <span class="sdm-m-lbl">Alasan:</span> {{ $izin->alasan }}
                                </div>
                            </td>
                            <td class="sdm-i-status-cell">
                                <div class="sdm-i-status-row">
                                    <div style="display:flex; align-items:center; gap:5px">
                                        <span class="sdm-m-lbl">Kepsek:</span>
                                        @if($izin->status_kepsek === 'disetujui')
                                            <span class="badge badge-success">Disetujui</span>
                                        @elseif($izin->status_kepsek === 'ditolak')
                                            <span class="badge badge-danger">Ditolak</span>
                                        @else
                                            <span class="badge badge-warning">Menunggu</span>
                                        @endif
                                    </div>
                                    <div style="display:flex; align-items:center; gap:5px">
                                        <span class="sdm-m-lbl">Waka:</span>
                                        @if($izin->status_waka === 'disetujui')
                                            <span class="badge badge-success">Disetujui</span>
                                        @elseif($izin->status_waka === 'ditolak')
                                            <span class="badge badge-danger">Ditolak</span>
                                        @else
                                            <span class="badge badge-warning">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="sdm-i-footer">
                                    <div style="display:inline-flex;gap:4px;flex-wrap:wrap;align-items:center">
                                        <span class="sdm-m-lbl">TTD:</span>
                                        @if($izin->tanda_tangan_kepsek)
                                            <button type="button" onclick="showSignaturePopup('{{ Storage::disk('public')->url($izin->tanda_tangan_kepsek) }}', 'Izin: {{ $izin->guru->nama_guru ?? '' }}', 'Tanda Tangan Kepala Sekolah')" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:6px;padding:3px 7px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5a1 1 0 0 1-1.4.4l-4-2a1 1 0 0 1 .3-1.8l10-3z"/><path d="M2 19l7-7 2 2 5-5 6 6v4a1 1 0 0 1-1 1l-9 2-4 2z"/></svg>
                                                Kepsek
                                            </button>
                                        @endif
                                        @if($izin->tanda_tangan_waka)
                                            <button type="button" onclick="showSignaturePopup('{{ Storage::disk('public')->url($izin->tanda_tangan_waka) }}', 'Izin: {{ $izin->guru->nama_guru ?? '' }}', 'Tanda Tangan Waka SDM')" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;padding:3px 7px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5a1 1 0 0 1-1.4.4l-4-2a1 1 0 0 1 .3-1.8l10-3z"/><path d="M2 19l7-7 2 2 5-5 6 6v4a1 1 0 0 1-1 1l-9 2-4 2z"/></svg>
                                                Waka
                                            </button>
                                        @endif
                                        @if(!$izin->tanda_tangan_kepsek && !$izin->tanda_tangan_waka)
                                            <span style="color:#94a3b8;font-size:12px">-</span>
                                        @endif
                                    </div>
                                    <a href="{{ URL::temporarySignedRoute('izin-guru.public', now()->addDays(2), ['izin' => $izin->id_izin_guru], false) }}" target="_blank" style="font-size:12.5px; font-weight:600; color:#2563eb; text-decoration:none">
                                        Buka Surat &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#64748b; padding:22px">
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

/* ── Mobile: Waka SDM — tampil persis desktop, geser horizontal ── */
@media (max-width: 768px) {
    #page-waka-sdm,
    #page-waka-sdm .card { overflow: visible !important; }

    .sdm-scroll-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        display: block !important;
    }

    #page-waka-sdm .data-table {
        width: auto !important;
        white-space: nowrap !important;
        border-collapse: collapse !important;
    }
}
</style>
