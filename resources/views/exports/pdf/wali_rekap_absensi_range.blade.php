@extends('exports.pdf.layout')

@section('title', 'Rekap Absensi Siswa - ' . ($namaKelas ?? 'Kelas'))

@section('content')
<div class="doc-header">
    <div class="doc-title">REKAPITULASI PRESENSI SISWA</div>
    <div class="doc-subtitle">Periode Tanggal: {{ date('d F Y', strtotime($tglMulai)) }} s/d {{ date('d F Y', strtotime($tglSelesai)) }}</div>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 15%"><strong>Kelas</strong></td>
        <td style="width: 35%">: {{ $namaKelas ?? '-' }}</td>
        <td style="width: 18%"><strong>Tahun Ajaran</strong></td>
        <td style="width: 32%">: {{ $tahunAjaran->nama_tahun ?? (date('Y') . '/' . (date('Y') + 1)) }}</td>
    </tr>
    <tr>
        <td><strong>Wali Kelas</strong></td>
        <td>: {{ $waliKelasNama ?? '-' }}</td>
        <td><strong>Total Murid</strong></td>
        <td>: {{ count($rekap['siswa'] ?? []) }} Siswa</td>
    </tr>
</table>

{{-- Stats Grid --}}
<table class="stats-grid">
    <tr>
        <td style="width: 16.6%">
            <div class="stats-label">Total Siswa</div>
            <div class="stats-val">{{ count($rekap['siswa'] ?? []) }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #16a34a">Total Hadir</div>
            <div class="stats-val" style="color: #16a34a">{{ $rekap['hadir'] ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #d97706">Total Sakit</div>
            <div class="stats-val" style="color: #d97706">{{ $rekap['sakit'] ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #2563eb">Total Izin</div>
            <div class="stats-val" style="color: #2563eb">{{ $rekap['izin'] ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #7c3aed">Total Dispen</div>
            <div class="stats-val" style="color: #7c3aed">{{ $rekap['dispen'] ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #dc2626">Total Alpa</div>
            <div class="stats-val" style="color: #dc2626">{{ $rekap['alpa'] ?? 0 }}</div>
        </td>
    </tr>
</table>

{{-- Data Table --}}
<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width: 25px" rowspan="2">No</th>
            <th style="width: 75px" rowspan="2">NISN</th>
            <th rowspan="2">Nama Siswa</th>
            <th class="center" style="width: 25px" rowspan="2">L/P</th>
            <th class="center" colspan="5" style="padding: 3px">Jumlah Presensi</th>
            <th class="center" style="width: 55px" rowspan="2">% Hadir</th>
            <th style="width: 90px" rowspan="2">Keterangan</th>
        </tr>
        <tr>
            <th class="center" style="width: 28px; color: #16a34a">H</th>
            <th class="center" style="width: 28px; color: #d97706">S</th>
            <th class="center" style="width: 28px; color: #2563eb">I</th>
            <th class="center" style="width: 28px; color: #7c3aed">D</th>
            <th class="center" style="width: 28px; color: #dc2626">A</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekap['siswa'] ?? [] as $idx => $s)
            @php
                $pct = (int) ($s['persentase'] ?? 0);
                $badgeClass = $pct >= 85 ? 'badge-success' : ($pct >= 75 ? 'badge-warning' : 'badge-danger');
            @endphp
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>{{ $s['nisn'] ?? '-' }}</td>
                <td style="font-weight: 600">{{ $s['nama_siswa'] }}</td>
                <td class="center">{{ $s['jenis_kelamin'] ?? '-' }}</td>
                <td class="center" style="font-weight: 600; color: #16a34a">{{ $s['hadir'] ?? 0 }}</td>
                <td class="center" style="font-weight: 600; color: #d97706">{{ $s['sakit'] ?? 0 }}</td>
                <td class="center" style="font-weight: 600; color: #2563eb">{{ $s['izin'] ?? 0 }}</td>
                <td class="center" style="font-weight: 600; color: #7c3aed">{{ $s['dispen'] ?? 0 }}</td>
                <td class="center" style="font-weight: 600; color: #dc2626">{{ $s['alpa'] ?? 0 }}</td>
                <td class="center">
                    <span class="badge {{ $badgeClass }}">{{ $pct }}%</span>
                </td>
                <td style="font-size: 8pt">{{ $s['keterangan'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="center" style="padding: 20px; color: #94a3b8">Tidak ada data kehadiran siswa pada rentang tanggal ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Tanda Tangan --}}
<table class="signature-table">
    <tr>
        <td>
            <div class="signature-box" style="margin-left: 10px;">
                <div>Mengetahui,</div>
                <div style="font-weight: 600;">Kepala Sekolah</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $kepalaSekolahNama ?? 'Drs. H. Mulyono, M.Pd.' }}</div>
                <div class="signature-nip">NIP. {{ $kepalaSekolahNip ?? '19680512 199412 1 002' }}</div>
            </div>
        </td>
        <td>
            <div class="signature-box" style="margin-right: 10px;">
                <div>Tulungagung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: 600;">Wali Kelas {{ $namaKelas ?? '' }}</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $waliKelasNama ?? 'Wali Kelas' }}</div>
                <div class="signature-nip">NIP. {{ $waliKelasNip ?? '-' }}</div>
            </div>
        </td>
    </tr>
</table>
@endsection
