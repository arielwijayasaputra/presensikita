<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PresensiKita - Absensi &amp; Presensi Mengajar Guru</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        .guru-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }
        .guru-header {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        .guru-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .guru-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .guru-logo-title {
            font-size: 17px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }
        .guru-logo-sub {
            font-size: 11.5px;
            color: #64748b;
        }
        .guru-nav-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .btn-admin-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-admin-link:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .jurnal-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        .card-heading {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .jurnal-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr;
            gap: 16px;
        }
        @media (max-width: 768px) {
            .jurnal-form-grid {
                grid-template-columns: 1fr;
            }
        }
        .form-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-field label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .form-input, .form-select {
            padding: 10px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 13.5px;
            color: #1e293b;
            background: #ffffff;
            outline: none;
            font-family: inherit;
            transition: all 0.2s;
        }
        .form-input:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        .absensi-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .search-input {
            width: 240px;
        }
        .rekap-summary-bar {
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            padding: 18px 24px;
            border-radius: 0 0 16px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .rekap-chips {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .rekap-chip {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .rekap-chip.hadir { background: #dcfce7; color: #15803d; }
        .rekap-chip.sakit { background: #fef3c7; color: #b45309; }
        .rekap-chip.izin { background: #dbeafe; color: #1d4ed8; }
        .rekap-chip.alpa { background: #fee2e2; color: #b91c1c; }
        .btn-submit-jurnal {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s;
        }
        .btn-submit-jurnal:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }
    </style>
</head>
<body>

<div class="guru-wrapper">
    <!-- Header Guru -->
    <header class="guru-header">
        <div class="guru-brand">
            <div class="guru-logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div>
                <div class="guru-logo-title">PresensiKita</div>
                <div class="guru-logo-sub">Presensi Guru – <span id="guru-school-name">{{ $namaSekolah ?? 'SMKN 1 Boyolangu' }}</span></div>
            </div>
        </div>
        <div class="guru-nav-right">
            <span style="font-size: 13px; font-weight: 600; color: #475569;">{{ $guru->nama_guru ?? 'Guru' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0" id="guru-logout-form">
                @csrf
                <button type="button" class="btn-admin-link" style="cursor:pointer;background:#fee2e2;border-color:#fecaca;color:#b91c1c;" onclick="confirmLogout('guru-logout-form')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Card 1: Form Informational Jurnal Mengajar -->
    <div class="jurnal-card">
        <div class="card-heading">
            <span>Form Jurnal Mengajar</span>
            <span id="guru-header-date" style="font-size: 12.5px; font-weight: 500; color: #64748b;"></span>
        </div>
        <div class="jurnal-form-grid">
            <div class="form-field">
                <label for="pilih-kelas">Pilih Kelas</label>
                <select id="pilih-kelas" class="form-select" onchange="loadSiswaByKelas(this.value)">
                    @foreach($kelases as $k)
                    <option value="{{ $k->id_kelas }}" {{ $selectedKelas->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="input-tanggal">Tanggal Pelaksanaan</label>
                <input type="date" id="input-tanggal" class="form-input" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-field">
                <label for="input-materi">Materi Pembelajaran</label>
                <input type="text" id="input-materi" class="form-input" placeholder="Tuliskan materi pembelajaran hari ini...">
            </div>
        </div>
    </div>

    <!-- Card 2: Pengisian Absensi Siswa -->
    <div class="jurnal-card" style="padding-bottom: 0;">
        <div class="absensi-toolbar">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;" id="guru-absensi-subtitle">Daftar Absensi Siswa - {{ $selectedKelas->nama_kelas }}</h3>
                <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Tandai status kehadiran setiap siswa di bawah ini</p>
            </div>
            <div class="tandai-row">
                <input type="text" class="form-input search-input" placeholder="Cari nama siswa..." onkeyup="filterSiswa(this.value)">
                <button class="btn-tandai green" onclick="tandaiSemua('H')">Tandai Semua Hadir</button>
            </div>
        </div>

        <div class="table-card" style="border: none; box-shadow: none;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th style="width: 140px;">NISN</th>
                        <th>Nama Siswa</th>
                        <th class="td-status" style="width: 60px;"><div class="status-header"><span class="status-dot-c green"></span>H</div></th>
                        <th class="td-status" style="width: 60px;"><div class="status-header"><span class="status-dot-c yellow"></span>S</div></th>
                        <th class="td-status" style="width: 60px;"><div class="status-header"><span class="status-dot-c blue"></span>I</div></th>
                        <th class="td-status" style="width: 60px;"><div class="status-header"><span class="status-dot-c red"></span>A</div></th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="siswa-tbody">
                    <!-- Populated dynamically by app.js -->
                </tbody>
            </table>
        </div>

        <!-- Rekap Footer -->
        <div class="rekap-summary-bar">
            <div class="rekap-chips">
                <span class="rekap-label">Rekap:</span>
                <div class="rekap-chip hadir">Hadir: <span id="rekap-hadir">0</span></div>
                <div class="rekap-chip sakit">Sakit: <span id="rekap-sakit">0</span></div>
                <div class="rekap-chip izin">Izin: <span id="rekap-izin">0</span></div>
                <div class="rekap-chip alpa">Alpa: <span id="rekap-alpa">0</span></div>
            </div>
            <button class="btn-submit-jurnal" onclick="submitAbsensi()">Simpan Jurnal &amp; Absensi</button>
        </div>
    </div>
</div>

<script>
    let currentSiswaList = @json($siswaList);
</script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const dateEl = document.getElementById('guru-header-date');
        if (dateEl) {
            dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        }
    });

</script>
</body>
</html>
