<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Orang Tua - PresensiKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── HEADER ─── */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .nav-brand-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .nav-brand-sub {
            font-size: 11px;
            font-weight: 600;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .school-badge {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 12px;
            color: #64748b;
        }

        .school-badge strong {
            font-size: 13px;
            color: #0f172a;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background-color: #fee2e2;
            color: #dc2626;
        }

        /* ─── CONTAINER ─── */
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 28px 20px 60px;
            flex: 1;
        }

        /* ─── STUDENT INFO HERO ─── */
        .student-hero-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 28px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .student-hero-card::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .student-profile-group {
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1;
        }

        .student-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.4);
            flex-shrink: 0;
        }

        .student-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .student-meta-list {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #94a3b8;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            color: #f8fafc;
            font-weight: 600;
        }

        .attendance-gauge-box {
            text-align: right;
            z-index: 1;
            flex-shrink: 0;
        }

        .gauge-number {
            font-size: 36px;
            font-weight: 800;
            color: #38bdf8;
            line-height: 1;
        }

        .gauge-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ─── SECTION HEADER & DATE FILTER ─── */
        .filter-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
        }

        .section-title-group h3 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .section-title-group p {
            font-size: 13px;
            color: #64748b;
        }

        .date-filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #ffffff;
            padding: 8px 14px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .date-filter-form label {
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
        }

        .date-filter-input {
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            background: transparent;
            cursor: pointer;
        }

        /* ─── STATS MINI CARDS ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 14px;
            padding: 16px 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-val {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-lbl {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        /* ─── TIMELINE PRESENSI PER JAM ─── */
        .card-main-box {
            background-color: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 14px rgba(0,0,0,0.03);
            overflow: hidden;
            margin-bottom: 32px;
        }

        .card-box-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-box-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .jam-list-container {
            padding: 16px 24px;
        }

        .jam-row-item {
            display: grid;
            grid-template-columns: 140px 1fr 180px;
            align-items: center;
            gap: 20px;
            padding: 16px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .jam-row-item:last-child {
            border-bottom: none;
        }

        .jam-badge-time {
            display: flex;
            flex-direction: column;
        }

        .jam-number {
            font-size: 14px;
            font-weight: 800;
            color: #0284c7;
        }

        .jam-time-span {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }

        .jam-subject-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .subject-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .guru-name {
            font-size: 12.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .materi-text {
            font-size: 12.5px;
            color: #334155;
            background-color: #f8fafc;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            margin-top: 4px;
            display: inline-block;
        }

        .jam-status-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-info    { background-color: #e0f2fe; color: #0369a1; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; }

        .ket-note {
            font-size: 11.5px;
            color: #64748b;
            font-style: italic;
        }

        .empty-jam-state {
            padding: 40px 20px;
            text-align: center;
            color: #64748b;
        }

        .empty-jam-state svg {
            margin-bottom: 10px;
            color: #cbd5e1;
        }

        /* ─── TABEL REKAP PER MAPEL BULANAN ─── */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .custom-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-align: left;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .custom-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .custom-table tr:hover td {
            background-color: #f8fafc;
        }

        .progress-bar-bg {
            width: 100px;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #0284c7;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .student-hero-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .attendance-gauge-box {
                text-align: left;
            }
            .jam-row-item {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .jam-status-col {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR TOP -->
    <header class="top-navbar">
        <div class="nav-brand">
            <div class="nav-brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="nav-brand-title">PresensiKita</div>
                <div class="nav-brand-sub">Platform Absensi Digital</div>
            </div>
        </div>

        <div class="nav-right">
            <div class="school-badge">
                <strong>{{ $namaSekolah }}</strong>
                <span>TA {{ $tahunAjaran->tahun_ajaran ?? '' }} ({{ $tahunAjaran->semester ?? '' }})</span>
            </div>

            <button type="button" onclick="bukaModalLaporOrangTua()" class="btn-logout" style="background-color: #f0f9ff; color: #0284c7; border-color: #bae6fd; margin-right: 8px;" title="Laporkan Kendala">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Lapor Admin
            </button>

            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="btn-logout" title="Keluar dari sistem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- CONTAINER UTAMA -->
    <div class="container">

        <!-- STUDENT PROFILE HERO -->
        <div class="student-hero-card">
            <div class="student-profile-group">
                <div class="student-avatar">
                    {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                </div>
                <div>
                    <h2 class="student-name">{{ $siswa->nama_siswa }}</h2>
                    <div class="student-meta-list">
                        <span class="meta-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/></svg>
                            NISN: {{ $siswa->nisn }}
                        </span>
                        <span class="meta-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                        <span class="meta-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="attendance-gauge-box">
                <div class="gauge-number">{{ $pctHadirBulan }}%</div>
                <div class="gauge-label">Tingkat Kehadiran Bulan Ini</div>
            </div>
        </div>

        <!-- FILTER & SUMMARY HARI INI -->
        <div class="filter-header-bar">
            <div class="section-title-group">
                <h3>Detail Presensi Kehadiran Per Jam & Mapel</h3>
                <p>Menampilkan jadwal dan presensi untuk hari <strong>{{ $hariIndo }}, {{ date('d F Y', strtotime($tanggal)) }}</strong></p>
            </div>

            <form method="GET" action="{{ route('orangtua.index') }}" class="date-filter-form">
                <label for="tanggal">Pilih Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="date-filter-input" onchange="this.form.submit()">
            </form>
        </div>

        <!-- MINI STATS CARDS HARI INI -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #dcfce7; color: #16a34a;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Hadir'] }} Jam</div>
                    <div class="stat-lbl">Hadir</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #fef3c7; color: #d97706;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Sakit'] }} Jam</div>
                    <div class="stat-lbl">Sakit</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #e0f2fe; color: #0284c7;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Izin'] }} Jam</div>
                    <div class="stat-lbl">Izin</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #fee2e2; color: #dc2626;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Alpa'] }} Jam</div>
                    <div class="stat-lbl">Alpa</div>
                </div>
            </div>
        </div>

        <!-- TIMELINE PER JAM & MAPEL -->
        <div class="card-main-box">
            <div class="card-box-header">
                <div class="card-box-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Jadwal & Presensi Jam Pelajaran Hari {{ $hariIndo }}
                </div>
            </div>

            <div class="jam-list-container">
                @forelse($presensiPerJam as $p)
                <div class="jam-row-item">
                    <div class="jam-badge-time">
                        <span class="jam-number">Jam Ke-{{ $p['jam_ke'] }}</span>
                        <span class="jam-time-span">{{ $p['jam_mulai'] }} - {{ $p['jam_selesai'] }} WIB</span>
                    </div>

                    <div class="jam-subject-info">
                        <div class="subject-title">
                            [{{ $p['kode_mapel'] }}] {{ $p['nama_mapel'] }}
                        </div>
                        <div class="guru-name">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Guru Pengampu: {{ $p['nama_guru'] }}
                        </div>
                        @if($p['materi'] !== '-')
                        <div>
                            <span class="materi-text">Materi: {{ $p['materi'] }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="jam-status-col">
                        <span class="badge-status {{ $p['badge_class'] }}">
                            @if($p['status'] === 'Hadir')
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                            {{ $p['status_label'] }}
                        </span>
                        @if($p['keterangan'] !== '-')
                        <span class="ket-note">Ket: {{ $p['keterangan'] }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="empty-jam-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <p style="font-weight:700; font-size:15px; color:#334155;">Tidak Ada Jadwal Pelajaran</p>
                    <p style="font-size:13px; margin-top:4px;">Tidak terdapat jadwal kegiatan belajar mengajar pada hari {{ $hariIndo }}.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- REKAPITULASI BULANAN PER MAPEL -->
        <div class="card-main-box">
            <div class="card-box-header">
                <div class="card-box-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Rekapitulasi Kehadiran Per Mata Pelajaran (Bulan {{ date('F Y', strtotime($tanggal)) }})
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Total Jam</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPerMapel as $idx => $rm)
                        <tr>
                            <td style="font-weight:700;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $rm['nama_mapel'] }}</strong>
                                <span style="font-size:11px; color:#64748b; display:block;">Kode: {{ $rm['kode_mapel'] }}</span>
                            </td>
                            <td>{{ $rm['total_jam'] }} Jam</td>
                            <td><span style="color:#15803d; font-weight:700;">{{ $rm['hadir'] }}</span></td>
                            <td><span style="color:#b45309; font-weight:700;">{{ $rm['sakit'] }}</span></td>
                            <td><span style="color:#0369a1; font-weight:700;">{{ $rm['izin'] }}</span></td>
                            <td><span style="color:#b91c1c; font-weight:700;">{{ $rm['alpa'] }}</span></td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: {{ $rm['persentase'] }}%;"></div>
                                </div>
                                <strong>{{ $rm['persentase'] }}%</strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:#64748b; padding:24px;">Belum ada data rekap presensi mata pelajaran pada bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
    function bukaModalLaporOrangTua() {
        Swal.fire({
            title: 'Kirim Laporan / Pengaduan Ke Admin',
            html: `
                <div style="text-align: left; font-family: inherit;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Nama Orang Tua / Pelapor</label>
                        <input type="text" id="swal-nama-pelapor" class="swal2-input" value="Orang Tua dari {{ $siswa->nama_siswa }}" style="margin: 0; width: 100%; font-size: 14px; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1;" readonly>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Judul Laporan</label>
                        <input type="text" id="swal-judul-laporan" class="swal2-input" placeholder="Contoh: Masalah Absensi / Kendala Akun" style="margin: 0; width: 100%; font-size: 14px; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Rincian Masalah</label>
                        <textarea id="swal-isi-laporan" class="swal2-textarea" placeholder="Jelaskan kendala Anda secara rinci..." style="margin: 0; width: 100%; height: 100px; font-size: 14px; font-family: inherit; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim Laporan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            preConfirm: () => {
                const judul = Swal.getPopup().querySelector('#swal-judul-laporan').value;
                const isi = Swal.getPopup().querySelector('#swal-isi-laporan').value;
                if (!judul || !isi) {
                    Swal.showValidationMessage(`Judul dan rincian laporan tidak boleh kosong`);
                }
                return { judul: judul, isi_laporan: isi }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('role_pelapor', 'Orang Tua');
                formData.append('nama_pelapor', 'Orang Tua dari {{ $siswa->nama_siswa }}');
                formData.append('judul', result.value.judul);
                formData.append('isi_laporan', result.value.isi_laporan);

                fetch('{{ route("laporan.public.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            confirmButtonColor: '#2563eb'
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal mengirim laporan.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }
    </script>

</body>
</html>
