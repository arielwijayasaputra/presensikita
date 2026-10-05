<div class="page-content page-anim" id="page-data-guru" style="display:none">

    @php
        $namaBulanList = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        $totalGuruData = count($sdmDataGuru);
        $totalSesiData = array_sum(array_column($sdmDataGuru, 'total_sesi'));
        $totalHadirData = array_sum(array_column($sdmDataGuru, 'hadir'));
        $totalTidakHadirData = array_sum(array_column($sdmDataGuru, 'tidak_hadir'));
        $totalBelumIsiData = array_sum(array_column($sdmDataGuru, 'belum_isi'));
        $totalIzinData = array_sum(array_column($sdmDataGuru, 'izin_total'));
        $totalIzinDisetujuiData = array_sum(array_column($sdmDataGuru, 'izin_disetujui'));
        $totalIzinDitolakData = array_sum(array_column($sdmDataGuru, 'izin_ditolak'));
        $totalIzinMenungguData = array_sum(array_column($sdmDataGuru, 'izin_menunggu'));
        $pctHadirData = $totalSesiData > 0 ? round(($totalHadirData / $totalSesiData) * 100, 1) : 0;

        // Susunan kolom tabel (label => accessor) untuk pengurutan kolom
        $kolomUrut = [
            'no' => 'no',
            'nip' => 'nip',
            'nama' => 'nama_guru',
            'mapel' => 'nama_mapel',
            'sesi' => 'total_sesi',
            'hadir' => 'hadir',
            'tidak_hadir' => 'tidak_hadir',
            'izin' => 'izin_total',
            'belum_isi' => 'belum_isi',
            'persen' => 'persentase',
        ];

        // Nilai urut tiap guru (dikirim ke JS agar tabel bisa diurutkan)
        $dgIndexAwal = [];
        foreach ($sdmDataGuru as $i => $r) {
            $dgIndexAwal[] = [
                'no' => $i + 1,
                'id' => $r['id_guru'],
                'nip' => (string) $r['nip'],
                'nama' => (string) $r['nama_guru'],
                'mapel' => (string) $r['nama_mapel'],
                'sesi' => (int) $r['total_sesi'],
                'hadir' => (int) $r['hadir'],
                'tidak_hadir' => (int) $r['tidak_hadir'],
                'izin' => (int) $r['izin_total'],
                'belum_isi' => (int) $r['belum_isi'],
                'persen' => (float) $r['persentase'],
            ];
        }
    @endphp

    <style>
        /* ── Halaman Data Presensi Guru (scoped, tidak memengaruhi halaman lain) ── */
        #page-data-guru .dg-page-head {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 16px; margin-bottom: 20px;
        }
        #page-data-guru .dg-title-wrap { display: flex; align-items: center; gap: 14px; }
        #page-data-guru .dg-title-icon {
            width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #2563eb; display: flex; align-items: center; justify-content: center;
        }
        #page-data-guru .dg-title { font-size: 22px; font-weight: 800; color: var(--text-primary); margin: 0; line-height: 1.2; }
        #page-data-guru .dg-subtitle { font-size: 13px; color: var(--text-secondary); margin: 3px 0 0 0; }

        /* Kartu statistik mengikuti komponen .stat-card bawaan aplikasi */
        #page-data-guru .stat-card { cursor: default; }

        /* Tabel Desktop: Default Table Layout */
        #page-data-guru .dg-table-wrap { overflow-x: auto; border-radius: var(--radius); }
        #page-data-guru .data-table { width: 100%; min-width: 0; table-layout: auto; }
        #page-data-guru .data-table th,
        #page-data-guru .data-table td { padding: 10px 6px; white-space: normal; }
        #page-data-guru .data-table thead th {
            position: sticky; top: 0; z-index: 2;
            background: #f8fafc; white-space: normal; cursor: pointer;
            user-select: none; transition: color 0.15s ease; line-height: 1.25;
        }
        #page-data-guru .data-table thead th:hover { color: #2563eb; }
        #page-data-guru .data-table thead th.dg-sort-active { color: #2563eb; }
        #page-data-guru .data-table thead th .dg-sort-icon { display: inline-block; opacity: 0.25; margin-left: 3px; font-size: 9px; }
        #page-data-guru .data-table thead th.dg-sort-active .dg-sort-icon { opacity: 1; }
        #page-data-guru .data-table tbody tr.dg-row { cursor: pointer; }
        #page-data-guru .data-table tbody tr.dg-row:nth-child(even) { background: var(--border-subtle); }
        #page-data-guru .data-table tbody tr.dg-row:hover { background: #dbeafe; }
        #page-data-guru .data-table tbody tr.dg-row:focus-visible { outline: 2px solid #2563eb; outline-offset: -2px; }
        #page-data-guru .data-table tfoot td {
            background: #f8fafc; font-weight: 800; color: var(--text-primary);
            border-top: 2px solid var(--border); border-bottom: none;
        }
        #page-data-guru .dg-name-wrap { display: flex; align-items: center; gap: 8px; }
        #page-data-guru .dg-name { font-weight: 700; color: var(--text-primary); overflow-wrap: anywhere; line-height: 1.3; }
        #page-data-guru .dg-sub { font-size: 11px; color: var(--text-muted); margin-top: 1px; overflow-wrap: anywhere; }
        #page-data-guru .dg-nip { font-family: monospace; font-size: 11.5px; color: var(--text-secondary); overflow-wrap: anywhere; }
        #page-data-guru .dg-mapel { font-size: 12px; color: var(--text-secondary); overflow-wrap: anywhere; }
        #page-data-guru .dg-num { text-align: center; font-variant-numeric: tabular-nums; }
        #page-data-guru .dg-bar-cell { display: flex; align-items: center; gap: 6px; justify-content: flex-end; }
        #page-data-guru .dg-bar { flex: 1; height: 7px; border-radius: 99px; background: var(--border-subtle); overflow: hidden; min-width: 26px; }
        #page-data-guru .dg-bar > span { display: block; height: 100%; border-radius: 99px; }
        #page-data-guru .dg-pct { font-size: 11.5px; font-weight: 800; text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
        #page-data-guru .dg-view-btn {
            font-size: 11px; font-weight: 700; padding: 5px 8px; border-radius: 8px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; border: none;
            cursor: pointer; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
        }
        #page-data-guru .dg-view-btn:hover { transform: translateY(-1px); }

        /* Elemen khusus mobile - disembunyikan di desktop */
        #page-data-guru .dg-m-lbl,
        #page-data-guru .dg-m-stat-lbl,
        #page-data-guru .dg-m-bar-lbl,
        #page-data-guru .dg-m-no-badge,
        #page-data-guru .dg-m-tfoot-lbl {
            display: none !important;
        }

        /* Keterangan / legenda */
        #page-data-guru .dg-legend { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: var(--text-secondary); }
        #page-data-guru .dg-legend span.item { display: inline-flex; align-items: center; gap: 6px; }
        #page-data-guru .dg-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        /* Modal detail */
        .swal2-dg-head { text-align: left; background: var(--border-subtle); border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px; }
        .swal2-dg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; }
        .swal2-dg-cell { border: 1px solid var(--border); border-radius: 10px; padding: 10px 8px; text-align: center; background: var(--card-bg); }
        .swal2-dg-cell .lbl { font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; }
        .swal2-dg-cell .val { font-size: 19px; font-weight: 800; margin-top: 3px; }
        .swal2-dg-table { width: 100%; border-collapse: collapse; font-family: inherit; table-layout: auto; }
        .swal2-dg-table th, .swal2-dg-table td { padding: 7px 5px; font-size: 12px; overflow-wrap: anywhere; }
        .swal2-dg-table thead th { position: sticky; top: 0; background: var(--border-subtle); font-size: 11px; line-height: 1.25; }
        .swal2-dg-scroll { max-height: 280px; overflow-y: auto; overflow-x: hidden; border: 1px solid var(--border); border-radius: 10px; }

        /* ── RESPONSIVE MOBILE (<= 768px): Tampilan Card Lengkap Rapi Tanpa Geser Samping ── */
        @media (max-width: 768px) {
            #page-data-guru .dg-page-head {
                flex-direction: column;
                align-items: stretch;
                gap: 14px;
                margin-bottom: 16px;
            }
            #page-data-guru .dg-page-head form {
                width: 100%;
                justify-content: space-between;
            }
            #page-data-guru .dg-page-head form select {
                flex: 1;
                min-width: 0;
            }

            /* ── Stat Cards Summary Grid Mobile ── */
            #page-data-guru .stat-cards {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
                margin-bottom: 18px !important;
            }
            #page-data-guru .stat-card {
                padding: 12px 14px !important;
                display: flex !important;
                align-items: center !important;
                gap: 10px !important;
                border-radius: 12px !important;
            }
            #page-data-guru .stat-card:last-child:nth-child(odd) {
                grid-column: 1 / -1 !important;
            }
            #page-data-guru .stat-icon {
                width: 38px !important;
                height: 38px !important;
                border-radius: 10px !important;
                flex-shrink: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            #page-data-guru .stat-icon svg {
                width: 18px !important;
                height: 18px !important;
            }
            #page-data-guru .stat-label {
                font-size: 11px !important;
                font-weight: 700 !important;
                color: var(--text-secondary) !important;
                line-height: 1.2 !important;
            }
            #page-data-guru .stat-value {
                font-size: 20px !important;
                font-weight: 800 !important;
                color: var(--text-primary) !important;
                line-height: 1.2 !important;
                margin: 2px 0 1px 0 !important;
            }
            #page-data-guru .stat-sub {
                font-size: 10px !important;
                color: var(--text-muted) !important;
                line-height: 1.2 !important;
            }

            #page-data-guru .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                padding: 16px 14px !important;
            }
            #page-data-guru .card-header > div:last-child {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            #page-data-guru .card-header > div:last-child > div:last-child {
                width: 100% !important;
            }
            #page-data-guru .dg-legend {
                justify-content: flex-start;
                gap: 10px;
                font-size: 11px;
            }

            /* Container Tabel Mobile */
            #page-data-guru .dg-table-wrap {
                overflow: visible !important;
                background: transparent !important;
                padding: 0 12px 14px !important;
                border: none !important;
            }
            #page-data-guru .data-table {
                display: block !important;
                width: 100% !important;
                border: none !important;
                background: transparent !important;
            }
            #page-data-guru .data-table thead {
                display: none !important;
            }
            #page-data-guru .data-table tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                width: 100% !important;
            }

            /* Baris Guru sebagai Kartu Utuh */
            #page-data-guru .data-table tbody tr.dg-row {
                display: grid !important;
                grid-template-columns: repeat(5, 1fr) !important;
                gap: 8px 6px !important;
                background: var(--card-bg) !important;
                border: 1px solid var(--border) !important;
                border-radius: 14px !important;
                padding: 14px 12px !important;
                box-shadow: var(--shadow) !important;
                cursor: pointer;
                position: relative;
                transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
                -webkit-tap-highlight-color: transparent;
            }
            #page-data-guru .data-table tbody tr.dg-row:hover {
                border-color: #3b82f6 !important;
                background: var(--card-bg) !important;
                transform: translateY(-1px);
            }
            #page-data-guru .data-table tbody tr.dg-row:nth-child(even) {
                background: var(--card-bg) !important;
            }

            /* Kolom Nomor di desktop disembunyikan, diganti badge di dalam nama */
            #page-data-guru .dg-col-no {
                display: none !important;
            }

            /* Header Kartu: Nama & Tombol Detail */
            #page-data-guru .dg-col-nama {
                grid-row: 1 !important;
                grid-column: 1 / span 4 !important;
                display: flex !important;
                align-items: center !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
            }
            #page-data-guru .dg-m-no-badge {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                background: var(--border-subtle);
                color: var(--text-secondary);
                border: 1px solid var(--border);
                font-size: 11px;
                font-weight: 800;
                padding: 2px 7px;
                border-radius: 6px;
                flex-shrink: 0;
                margin-top: 1px;
            }
            #page-data-guru .dg-name {
                font-size: 14px !important;
                font-weight: 800 !important;
                color: var(--text-primary) !important;
                line-height: 1.3 !important;
            }
            #page-data-guru .dg-sub {
                font-size: 11.5px !important;
                color: var(--text-muted) !important;
                margin-top: 2px !important;
            }

            #page-data-guru .dg-col-action {
                grid-row: 1 !important;
                grid-column: 5 / span 1 !important;
                display: flex !important;
                justify-content: flex-end !important;
                align-items: flex-start !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
            }
            #page-data-guru .dg-view-btn {
                padding: 5px 9px !important;
                font-size: 11.5px !important;
                border-radius: 8px !important;
            }

            /* Metadata: NIP & Mapel */
            #page-data-guru .dg-col-nip {
                grid-row: 2 !important;
                grid-column: 1 / span 3 !important;
                display: flex !important;
                align-items: center !important;
                gap: 5px !important;
                padding: 2px 0 4px 0 !important;
                border: none !important;
                background: transparent !important;
                font-size: 11.5px !important;
                color: var(--text-secondary) !important;
            }
            #page-data-guru .dg-col-mapel {
                grid-row: 2 !important;
                grid-column: 4 / span 2 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 5px !important;
                padding: 2px 0 4px 0 !important;
                border: none !important;
                background: transparent !important;
                font-size: 11.5px !important;
                color: var(--text-secondary) !important;
                text-align: right !important;
            }
            #page-data-guru .dg-m-lbl {
                display: inline-block !important;
                font-size: 10px !important;
                font-weight: 700 !important;
                color: var(--text-muted) !important;
                text-transform: uppercase !important;
                letter-spacing: 0.3px !important;
            }
            #page-data-guru .dg-val-mapel {
                max-width: 140px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-weight: 600;
                color: var(--text-primary);
            }
            #page-data-guru .dg-val-nip {
                font-family: monospace;
                font-size: 11px;
                color: var(--text-secondary);
            }

            /* 5 Kotak Statistik Presensi (Sesi, Hadir, Tdk Hadir, Izin, Belum Isi) */
            #page-data-guru .dg-col-sesi { grid-row: 3 !important; grid-column: 1 !important; }
            #page-data-guru .dg-col-hadir { grid-row: 3 !important; grid-column: 2 !important; }
            #page-data-guru .dg-col-thadir { grid-row: 3 !important; grid-column: 3 !important; }
            #page-data-guru .dg-col-izin { grid-row: 3 !important; grid-column: 4 !important; }
            #page-data-guru .dg-col-belum { grid-row: 3 !important; grid-column: 5 !important; }
            #page-data-guru .dg-col-sesi,
            #page-data-guru .dg-col-hadir,
            #page-data-guru .dg-col-thadir,
            #page-data-guru .dg-col-izin,
            #page-data-guru .dg-col-belum {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                background: var(--border-subtle) !important;
                border: 1px solid var(--border) !important;
                border-radius: 9px !important;
                padding: 7px 2px !important;
                min-height: 48px !important;
                gap: 3px !important;
            }
            #page-data-guru .dg-m-stat-lbl {
                display: block !important;
                font-size: 8.5px !important;
                font-weight: 700 !important;
                color: var(--text-muted) !important;
                text-transform: uppercase !important;
                letter-spacing: 0.3px !important;
                line-height: 1 !important;
                text-align: center !important;
            }
            #page-data-guru .badge {
                font-size: 11.5px !important;
                padding: 2px 6px !important;
            }

            /* Progress Bar Kehadiran */
            #page-data-guru .dg-col-bar {
                grid-row: 4 !important;
                grid-column: 1 / -1 !important;
                display: block !important;
                padding: 8px 0 0 0 !important;
                border: none !important;
                border-top: 1px dashed var(--border) !important;
                background: transparent !important;
            }
            #page-data-guru .dg-bar-cell {
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
                justify-content: space-between !important;
                width: 100% !important;
            }
            #page-data-guru .dg-m-bar-lbl {
                display: inline-block !important;
                font-size: 11.5px !important;
                font-weight: 700 !important;
                color: var(--text-secondary) !important;
                white-space: nowrap !important;
            }
            #page-data-guru .dg-bar {
                flex: 1 !important;
                height: 8px !important;
                border-radius: 99px !important;
                background: var(--border-subtle) !important;
                overflow: hidden !important;
                min-width: 50px !important;
            }
            #page-data-guru .dg-pct {
                font-size: 12px !important;
                font-weight: 800 !important;
                min-width: 42px !important;
                text-align: right !important;
            }

            /* Summary / Footer di Mobile */
            #page-data-guru .data-table tfoot {
                display: block !important;
                margin-top: 12px !important;
                border: none !important;
                background: transparent !important;
            }
            #page-data-guru .data-table tfoot tr {
                display: flex !important;
                flex-direction: column !important;
                gap: 6px !important;
                background: var(--card-bg) !important;
                border: 1px solid var(--border) !important;
                border-radius: 14px !important;
                padding: 14px 16px !important;
                box-shadow: var(--shadow) !important;
            }
            #page-data-guru .data-table tfoot td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 4px 0 !important;
                border: none !important;
                background: transparent !important;
                font-size: 12px !important;
            }
            #page-data-guru .data-table tfoot td:first-child {
                font-size: 13.5px !important;
                font-weight: 800 !important;
                color: var(--text-primary) !important;
                border-bottom: 1px solid var(--border) !important;
                padding-bottom: 8px !important;
                margin-bottom: 2px !important;
                text-align: left !important;
            }
            #page-data-guru .data-table tfoot td:last-child {
                display: none !important;
            }
            #page-data-guru .dg-m-tfoot-lbl {
                display: inline-block !important;
                font-weight: 600 !important;
                color: var(--text-secondary) !important;
            }

            /* Modal detail pada mobile */
            .swal2-dg-popup {
                width: 95vw !important;
                max-width: 95vw !important;
                padding: 16px 12px !important;
                border-radius: 16px !important;
            }
            .swal2-dg-popup .swal2-tab {
                padding: 8px 4px !important;
                margin: 0 4px !important;
                font-size: 11.5px !important;
            }
            .swal2-dg-head {
                padding: 12px !important;
            }
            .swal2-dg-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }
        }
    </style>

    <!-- ══ HEADER HALAMAN ══ -->
    <div class="dg-page-head">
        <div class="dg-title-wrap">
            <div class="dg-title-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </div>
            <div>
                <h1 class="dg-title">Data Presensi Guru</h1>
                <p class="dg-subtitle">Klik baris guru untuk melihat rincian hadir, izin, dan riwayat lengkapnya</p>
            </div>
        </div>

        <form method="GET" action="{{ route('wakasdm.index') }}" style="display:flex; align-items:center; gap:8px; background:var(--card-bg); padding:8px 14px; border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow)">
            @if(request('tanggal'))
                <input type="hidden" name="tanggal" value="{{ $sdmTanggal }}">
            @endif

            <label for="dg_bulan" style="font-size:12px; font-weight:700; color:var(--text-secondary)">Periode</label>

            <select id="dg_bulan" name="sdm_bulan" onchange="this.form.submit()" class="filter-input" style="padding:7px 11px; font-size:12.5px; border-radius:9px; border:1px solid var(--input-border, #cbd5e1)">
                @foreach($namaBulanList as $mNum => $mName)
                    <option value="{{ $mNum }}" {{ $sdmBulan == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                @endforeach
            </select>

            <select name="sdm_tahun" onchange="this.form.submit()" class="filter-input" style="padding:7px 11px; font-size:12.5px; border-radius:9px; border:1px solid var(--input-border, #cbd5e1)">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $sdmTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- ══ KARTU RINGKASAN ══ -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Guru Aktif</div>
                <div class="stat-value">{{ $totalGuruData }}</div>
                <div class="stat-sub">{{ $totalSesiData }} sesi mengajar terjadwal</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Hadir</div>
                <div class="stat-value">{{ $totalHadirData }}</div>
                <div class="stat-sub">{{ $pctHadirData }}% dari total sesi</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Tidak Hadir</div>
                <div class="stat-value">{{ $totalTidakHadirData }}</div>
                <div class="stat-sub">Jurnal tercatat tidak hadir</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon cyan">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Izin</div>
                <div class="stat-value">{{ $totalIzinData }}</div>
                <div class="stat-sub">Disetujui {{ $totalIzinDisetujuiData }} &bull; Ditolak {{ $totalIzinDitolakData }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-label">Belum Isi Jurnal</div>
                <div class="stat-value">{{ $totalBelumIsiData }}</div>
                <div class="stat-sub">{{ $totalIzinMenungguData }} izin menunggu persetujuan</div>
            </div>
        </div>
    </div>

    <!-- ══ TABEL DATA SETIAP GURU ══ -->
    <div class="card" style="padding:0; overflow:hidden">
        <div class="card-header" style="padding:20px 22px 14px; margin-bottom:0; flex-wrap:wrap; gap:12px">
            <div>
                <div class="card-title" style="font-size:16px">Tabel Data Presensi Setiap Guru</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:3px">
                    Periode <strong>{{ $namaBulanList[$sdmBulan] ?? $sdmBulan }} {{ $sdmTahun }}</strong>
                    &bull; {{ $totalGuruData }} guru &bull; klik kolom untuk mengurutkan
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap">
                <div class="dg-legend">
                    <span class="item"><i class="dg-dot" style="background:#16a34a"></i> Hadir</span>
                    <span class="item"><i class="dg-dot" style="background:#dc2626"></i> Tidak Hadir</span>
                    <span class="item"><i class="dg-dot" style="background:#0284c7"></i> Izin</span>
                    <span class="item"><i class="dg-dot" style="background:#d97706"></i> Belum Isi</span>
                </div>

                <div style="position:relative; width:240px">
                    <input type="text" id="search-data-guru" onkeyup="filterTableDataGuru('search-data-guru', 'table-data-guru')" placeholder="Cari nama guru / NIP..." class="filter-input" style="width:100%; padding:8px 12px 8px 33px; font-size:12.5px; border-radius:9px; border:1px solid var(--input-border, #cbd5e1)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:11px; top:10px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>
        </div>

        <div class="dg-table-wrap">
            <table class="data-table" id="table-data-guru">
                <thead>
                    <tr>
                        <th class="dg-sort" data-sort="no">No <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="nip">NIP <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort dg-sort-active" data-sort="nama" data-dir="asc">Nama Guru <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="mapel">Mata Pelajaran <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="sesi" style="text-align:center">Sesi <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="hadir" style="text-align:center">Hadir <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="tidak_hadir" style="text-align:center">Tdk Hadir <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="izin" style="text-align:center">Izin <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="belum_isi" style="text-align:center">Belum Isi <span class="dg-sort-icon">▲</span></th>
                        <th class="dg-sort" data-sort="persen" style="text-align:right">Kehadiran <span class="dg-sort-icon">▲</span></th>
                        <th style="text-align:center">Detail</th>
                    </tr>
                </thead>

                <tbody id="tbody-data-guru">
                    @forelse($sdmDataGuru as $idx => $r)
                        @php
                            $warnaPersen = $r['persentase'] >= 85 ? '#16a34a' : ($r['persentase'] >= 70 ? '#d97706' : '#dc2626');
                        @endphp
                        <tr class="dg-row" onclick="lihatDetailGuru({{ $r['id_guru'] }})" tabindex="0"
                            onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();lihatDetailGuru({{ $r['id_guru'] }});}">
                            <td class="dg-num dg-col-no" style="color:var(--text-muted); font-weight:600">{{ $idx + 1 }}</td>
                            <td class="dg-nip dg-col-nip">
                                <span class="dg-m-lbl">NIP:</span>
                                <span class="dg-val-nip">{{ $r['nip'] ?: '-' }}</span>
                            </td>
                            <td class="dg-col-nama">
                                <div class="dg-name-wrap">
                                    <span class="dg-m-no-badge">#<span class="dg-m-no-val">{{ $idx + 1 }}</span></span>
                                    <div>
                                        <div class="dg-name">{{ $r['nama_guru'] }}</div>
                                        <div class="dg-sub">{{ $r['username'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="dg-mapel dg-col-mapel">
                                <span class="dg-m-lbl">Mapel:</span>
                                <span class="dg-val-mapel" title="{{ $r['nama_mapel'] }}">{{ $r['nama_mapel'] ?: '-' }}</span>
                            </td>
                            <td class="dg-num dg-col-sesi">
                                <span class="dg-m-stat-lbl">Sesi</span>
                                <span style="font-weight:700">{{ $r['total_sesi'] }}</span>
                            </td>
                            <td class="dg-num dg-col-hadir">
                                <span class="dg-m-stat-lbl">Hadir</span>
                                <span class="badge badge-success" style="font-size:12px">{{ $r['hadir'] }}</span>
                            </td>
                            <td class="dg-num dg-col-thadir">
                                <span class="dg-m-stat-lbl">Tdk Hadir</span>
                                @if($r['tidak_hadir'] > 0)
                                    <span class="badge badge-danger" style="font-size:12px">{{ $r['tidak_hadir'] }}</span>
                                @else
                                    <span style="color:var(--text-muted)">0</span>
                                @endif
                            </td>
                            <td class="dg-num dg-col-izin">
                                <span class="dg-m-stat-lbl">Izin</span>
                                @if($r['izin_total'] > 0)
                                    <span class="badge badge-info" style="font-size:12px">{{ $r['izin_total'] }}x</span>
                                @else
                                    <span style="color:var(--text-muted)">0</span>
                                @endif
                            </td>
                            <td class="dg-num dg-col-belum">
                                <span class="dg-m-stat-lbl">Belum Isi</span>
                                @if($r['belum_isi'] > 0)
                                    <span class="badge badge-warning" style="font-size:12px">{{ $r['belum_isi'] }}</span>
                                @else
                                    <span style="color:var(--text-muted)">0</span>
                                @endif
                            </td>
                            <td class="dg-col-bar">
                                <div class="dg-bar-cell">
                                    <span class="dg-m-bar-lbl">Kehadiran:</span>
                                    <div class="dg-bar"><span style="width:{{ $r['persentase'] }}%; background:{{ $warnaPersen }}"></span></div>
                                    <span class="dg-pct" style="color:{{ $warnaPersen }}">{{ $r['persentase'] }}%</span>
                                </div>
                            </td>
                            <td class="dg-col-action" style="text-align:center">
                                <button type="button" class="dg-view-btn" onclick="event.stopPropagation(); lihatDetailGuru({{ $r['id_guru'] }});">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Lihat</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align:center; color:var(--text-secondary); padding:36px">Belum ada data guru pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($sdmDataGuru) > 0)
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right; font-size:12px; letter-spacing:0.03em; text-transform:uppercase; color:var(--text-secondary)">Total {{ $totalGuruData }} Guru</td>
                        <td class="dg-num"><span class="dg-m-tfoot-lbl">Total Sesi:</span> {{ $totalSesiData }}</td>
                        <td class="dg-num"><span class="dg-m-tfoot-lbl">Total Hadir:</span> {{ $totalHadirData }}</td>
                        <td class="dg-num"><span class="dg-m-tfoot-lbl">Total Tdk Hadir:</span> {{ $totalTidakHadirData }}</td>
                        <td class="dg-num"><span class="dg-m-tfoot-lbl">Total Izin:</span> {{ $totalIzinData }}</td>
                        <td class="dg-num"><span class="dg-m-tfoot-lbl">Total Belum Isi:</span> {{ $totalBelumIsiData }}</td>
                        <td style="text-align:right"><span class="dg-m-tfoot-lbl">Rata-rata:</span> <span class="dg-pct">{{ $pctHadirData }}%</span></td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

<!-- ══ SCRIPT PENCARIAN, PENGURUTAN & DETAIL GURU ══ -->
<script>
(function () {
    window.dgKolomUrut = @json($kolomUrut);
    window.dgIndexAwal = @json($dgIndexAwal);
})();

/** Pencarian langsung pada tabel Data Presensi Guru. */
function filterTableDataGuru(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    const filter = (input.value || '').toLowerCase().trim();
    const trs = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < trs.length; i++) {
        const text = (trs[i].textContent || '').toLowerCase();
        trs[i].style.display = (filter === '' || text.indexOf(filter) > -1) ? '' : 'none';
    }
}

/** Pengurutan kolom: klik header tabel. */
function urutkanDataGuru(th) {
    const key = th.getAttribute('data-sort');
    const accessor = window.dgKolomUrut ? window.dgKolomUrut[key] : null;
    const tbody = document.getElementById('tbody-data-guru');
    if (!key || !accessor || !tbody) return;

    const isNumeric = ['no', 'sesi', 'hadir', 'tidak_hadir', 'izin', 'belum_isi', 'persen'].indexOf(key) > -1;

    // Tentukan arah urut (klik pertama = naik, klik berikutnya = turun)
    const asc = th.getAttribute('data-dir') !== 'asc';

    document.querySelectorAll('#page-data-guru th.dg-sort').forEach(el => {
        el.classList.remove('dg-sort-active');
        el.removeAttribute('data-dir');
        const ic = el.querySelector('.dg-sort-icon');
        if (ic) ic.textContent = '▲';
    });
    th.classList.add('dg-sort-active');
    th.setAttribute('data-dir', asc ? 'asc' : 'desc');
    const icon = th.querySelector('.dg-sort-icon');
    if (icon) icon.textContent = asc ? '▲' : '▼';

    const rows = Array.prototype.slice.call(tbody.getElementsByTagName('tr'));
    const nilai = {};
    (window.dgIndexAwal || []).forEach(item => { nilai[item.id] = item; });

    /** Nilai urut baris, atau null untuk baris tanpa data (selalu ditaruh paling akhir). */
    const nilaiBaris = (tr) => {
        const m = (tr.getAttribute('onclick') || '').match(/lihatDetailGuru\((\d+)\)/);
        const item = m ? nilai[m[1]] : null;
        if (!item) return null;
        const v = item[accessor];
        return isNumeric ? (Number(v) || 0) : String(v === null || v === undefined ? '' : v).toLowerCase();
    };

    const urut = rows.slice().sort((a, b) => {
        const va = nilaiBaris(a);
        const vb = nilaiBaris(b);
        if (va === null && vb === null) return 0;
        if (va === null) return 1;
        if (vb === null) return -1;
        if (va < vb) return asc ? -1 : 1;
        if (va > vb) return asc ? 1 : -1;
        return 0;
    });

    // Penomoran urut mengikuti urutan tampilan
    urut.forEach((tr, i) => {
        if (nilaiBaris(tr) !== null) {
            tr.cells[0].textContent = String(i + 1);
            const mBadge = tr.querySelector('.dg-m-no-val');
            if (mBadge) mBadge.textContent = String(i + 1);
        }
    });

    urut.forEach(tr => tbody.appendChild(tr));
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#page-data-guru th.dg-sort').forEach(function (th) {
        th.addEventListener('click', function () { urutkanDataGuru(th); });
    });
});

/** Escape teks agar aman ditampilkan di dalam modal. */
function escDataGuru(value) {
    const div = document.createElement('div');
    div.textContent = (value === null || value === undefined) ? '' : String(value);
    return div.innerHTML;
}

function badgeIzinDataGuru(status) {
    if (status === 'disetujui') return '<span class="badge badge-success" style="font-size:11.5px">Disetujui</span>';
    if (status === 'ditolak') return '<span class="badge badge-danger" style="font-size:11.5px">Ditolak</span>';
    return '<span class="badge badge-warning" style="font-size:11.5px">Menunggu</span>';
}

const NAMA_BULAN_DATA_GURU = {1:'Januari',2:'Februari',3:'Maret',4:'April',5:'Mei',6:'Juni',7:'Juli',8:'Agustus',9:'September',10:'Oktober',11:'November',12:'Desember'};

/** Tampilkan rincian data guru (dipakai saat baris tabel diklik). */
function lihatDetailGuru(idGuru) {
    const url = @json(route('wakasdm.data-guru.detail', ['guru' => 0], false))
        .replace('/0/detail', '/' + idGuru + '/detail')
        + '?bulan=' + @json($sdmBulan) + '&tahun=' + @json($sdmTahun);

    Swal.fire({
        title: 'Memuat data guru...',
        html: '<div style="padding:24px;text-align:center;color:#64748b;font-size:13px">Mohon tunggu sebentar</div>',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat data guru.');
            return response.json();
        })
        .then(data => {
            const s = data.statistik;
            const g = data.guru;
            const p = data.periode;
            const namaBulan = NAMA_BULAN_DATA_GURU[p.bulan] || p.bulan;

            const sel = [
                { l: 'Total Sesi', v: s.total_sesi, c: '#2563eb' },
                { l: 'Hadir', v: s.hadir, c: '#16a34a' },
                { l: 'Tidak Hadir', v: s.tidak_hadir, c: '#dc2626' },
                { l: 'Izin', v: s.izin_total + ' Kali', c: '#0284c7' },
                { l: 'Belum Isi', v: s.belum_isi, c: '#d97706' }
            ].map(k => `
                <div class="swal2-dg-cell">
                    <div class="lbl">${escDataGuru(k.l)}</div>
                    <div class="val" style="color:${k.c}">${escDataGuru(k.v)}</div>
                </div>`).join('');

            const bar = Math.max(0, Math.min(100, Number(s.persentase) || 0));
            const warnaBar = bar >= 85 ? '#16a34a' : (bar >= 70 ? '#d97706' : '#dc2626');

            const rowsIzin = (data.izin && data.izin.length)
                ? data.izin.map((z, i) => `
                    <tr>
                        <td class="dg-num" style="color:var(--text-muted)">${i + 1}</td>
                        <td style="font-weight:600; white-space:nowrap">${escDataGuru(z.tanggal)}</td>
                        <td>${escDataGuru(z.alasan)}</td>
                        <td style="text-align:center">${badgeIzinDataGuru(z.status)}</td>
                        <td style="color:var(--text-secondary)">${escDataGuru(z.catatan)}</td>
                    </tr>`).join('')
                : '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--text-muted)">Tidak ada pengajuan izin pada periode ini.</td></tr>';

            const rowsRiwayat = (data.riwayat && data.riwayat.length)
                ? data.riwayat.map((r, i) => `
                    <tr>
                        <td class="dg-num" style="color:var(--text-muted)">${i + 1}</td>
                        <td style="font-weight:600; white-space:nowrap">${escDataGuru(r.tanggal)}</td>
                        <td style="white-space:nowrap">${escDataGuru(r.hari)}</td>
                        <td style="color:#0284c7; white-space:nowrap">${escDataGuru(r.jam)}</td>
                        <td>${escDataGuru(r.mapel)}</td>
                        <td>${escDataGuru(r.kelas)}</td>
                        <td style="text-align:center">${r.status === 'Hadir'
                            ? '<span class="badge badge-success" style="font-size:11.5px">Hadir</span>'
                            : '<span class="badge badge-danger" style="font-size:11.5px">Tidak Hadir</span>'}</td>
                        <td class="dg-num" style="font-weight:600">${escDataGuru(r.siswa_hadir)}</td>
                    </tr>`).join('')
                : '<tr><td colspan="8" style="padding:20px;text-align:center;color:var(--text-muted)">Belum ada jurnal yang diisi pada periode ini.</td></tr>';

            const rowsBulan = (data.per_bulan || []).map(b => {
                const c = b.persentase >= 85 ? '#16a34a' : (b.persentase >= 70 ? '#d97706' : '#dc2626');
                const bold = b.bulan === p.bulan ? 'font-weight:800;' : '';
                return `
                    <tr style="${bold}">
                        <td>${escDataGuru(b.nama_bulan)}</td>
                        <td class="dg-num" style="font-weight:700">${b.total_sesi}</td>
                        <td class="dg-num" style="color:#16a34a; font-weight:700">${b.hadir}</td>
                        <td class="dg-num" style="color:#dc2626; font-weight:700">${b.tidak_hadir}</td>
                        <td class="dg-num" style="color:#d97706; font-weight:700">${b.belum_isi}</td>
                        <td class="dg-num" style="font-weight:800; color:${c}">${b.persentase}%</td>
                    </tr>`;
            }).join('');

            Swal.fire({
                title: '<div style="font-size:18px;font-weight:800;color:var(--text-primary);text-align:left">Rincian Data Guru</div>',
                html: `
                    <div style="text-align:left">

                        <div class="swal2-dg-head">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
                                <div>
                                    <div style="font-size:16px;font-weight:800;color:var(--text-primary)">${escDataGuru(g.nama_guru)}</div>
                                    <div style="font-size:12px;color:var(--text-secondary);font-family:monospace;margin-top:2px">NIP: ${escDataGuru(g.nip)}</div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:1px">Username: ${escDataGuru(g.username)}</div>
                                </div>
                                <div style="text-align:right">
                                    <div style="font-size:12.5px;font-weight:700;color:var(--text-primary)">${escDataGuru(g.nama_mapel)}</div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px">No. HP: ${escDataGuru(g.no_hp)}</div>
                                </div>
                            </div>
                            <div style="margin-top:12px">
                                <div style="display:flex;justify-content:space-between;font-size:11.5px;font-weight:700;color:var(--text-secondary)">
                                    <span>Periode: ${escDataGuru(namaBulan)} ${escDataGuru(p.tahun)}</span>
                                    <span>Kehadiran: ${s.persentase}%</span>
                                </div>
                                <div style="height:8px;border-radius:99px;background:var(--border-subtle);overflow:hidden;margin-top:5px">
                                    <div style="width:${bar}%;height:100%;background:${warnaBar};border-radius:99px"></div>
                                </div>
                            </div>
                        </div>

                        <div id="dg-tab-ringkasan" style="padding-top:14px">
                            <div class="swal2-dg-grid">${sel}</div>
                            <div style="margin-top:14px;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px">
                                <div class="swal2-dg-cell">
                                    <div class="lbl">Izin Disetujui</div>
                                    <div class="val" style="color:#16a34a">${s.izin_disetujui}</div>
                                </div>
                                <div class="swal2-dg-cell">
                                    <div class="lbl">Izin Ditolak</div>
                                    <div class="val" style="color:#dc2626">${s.izin_ditolak}</div>
                                </div>
                                <div class="swal2-dg-cell">
                                    <div class="lbl">Izin Menunggu</div>
                                    <div class="val" style="color:#d97706">${s.izin_menunggu}</div>
                                </div>
                            </div>
                        </div>

                        <div id="dg-tab-izin">
                            <div class="swal2-dg-scroll">
                                <table class="swal2-dg-table">
                                    <thead><tr>
                                        <th style="text-align:center">No</th>
                                        <th>Tanggal</th>
                                        <th>Alasan Izin</th>
                                        <th style="text-align:center">Status</th>
                                        <th>Catatan</th>
                                    </tr></thead>
                                    <tbody>${rowsIzin}</tbody>
                                </table>
                            </div>
                        </div>

                        <div id="dg-tab-riwayat">
                            <div class="swal2-dg-scroll" style="max-height:340px">
                                <table class="swal2-dg-table">
                                    <thead><tr>
                                        <th style="text-align:center">No</th>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mapel</th>
                                        <th>Kelas</th>
                                        <th style="text-align:center">Status</th>
                                        <th style="text-align:center">Siswa</th>
                                    </tr></thead>
                                    <tbody>${rowsRiwayat}</tbody>
                                </table>
                            </div>
                        </div>

                        <div id="dg-tab-bulan">
                            <div class="swal2-dg-scroll" style="max-height:300px">
                                <table class="swal2-dg-table">
                                    <thead><tr>
                                        <th>Bulan</th>
                                        <th style="text-align:center">Sesi</th>
                                        <th style="text-align:center">Hadir</th>
                                        <th style="text-align:center">Tdk Hadir</th>
                                        <th style="text-align:center">Belum Isi</th>
                                        <th style="text-align:center">Persentase</th>
                                    </tr></thead>
                                    <tbody>${rowsBulan}</tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                `,
                tabs: ['#dg-tab-ringkasan', '#dg-tab-izin', '#dg-tab-riwayat', '#dg-tab-bulan'],
                width: '900px',
                showCloseButton: true,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'swal2-dg-popup' }
            });

            // Judul tab dibuat dinamis agar ikut tema terang/gelap
            const swalPopup = Swal.getPopup();
            if (swalPopup) {
                swalPopup.querySelectorAll('.swal2-tab').forEach(function (tab, i) {
                    const label = ['Ringkasan', 'Riwayat Izin (' + s.izin_total + ')', 'Riwayat Kehadiran', 'Rekap per Bulan'][i] || '';
                    tab.textContent = label;
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: error.message || 'Terjadi kesalahan saat memuat rincian data guru.',
                confirmButtonColor: '#dc2626'
            });
        });
}
</script>

<style>
/* Gaya isi modal mengikuti tema aplikasi */
.swal2-dg-popup .swal2-tab {
    background: transparent; color: var(--text-muted); font-weight: 700; font-size: 13px;
    border: none; border-bottom: 2px solid transparent; padding: 10px 4px; margin: 0 12px;
}
.swal2-dg-popup .swal2-tab[aria-selected="true"] { color: #2563eb; border-bottom-color: #2563eb; }
.swal2-dg-popup .swal2-tab:hover { color: #2563eb; }
.swal2-dg-popup .swal2-content { text-align: left; }
.swal2-dg-popup .swal2-table th { position: sticky; top: 0; z-index: 1; border-bottom: 1px solid var(--border); }
.swal2-dg-popup .swal2-table td { border-bottom: 1px solid var(--border-subtle); }

@media print {
    #page-data-guru .sidebar, #page-data-guru .header, #page-data-guru form,
    #page-data-guru .dg-view-btn, #page-data-guru .dg-legend, #page-data-guru #search-data-guru {
        display: none !important;
    }
    #page-data-guru .main-content, #page-data-guru .page-content {
        margin: 0 !important; padding: 0 !important; background: #fff !important;
    }
    #page-data-guru .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
}
</style>
