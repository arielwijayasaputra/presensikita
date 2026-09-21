@extends('exports.pdf.layout')

@section('title', 'Riwayat Jurnal Presensi - ' . ($namaKelas ?? 'Semua Kelas'))

@section('page_size', 'A4 portrait')

@section('content')
<div class="doc-header">
    <div class="doc-title">REKAPITULASI JURNAL &amp; RIWAYAT PRESENSI</div>
    <div class="doc-subtitle">Periode: {{ $namaBulan ?? '' }} {{ $tahun ?? date('Y') }}</div>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 15%"><strong>Kelas</strong></td>
        <td style="width: 35%">: {{ $namaKelas ?? 'Semua Kelas' }}</td>
        <td style="width: 18%"><strong>Tahun Ajaran</strong></td>
        <td style="width: 32%">: {{ $tahunAjaran->nama_tahun ?? (date('Y') . '/' . (date('Y') + 1)) }}</td>
    </tr>
    <tr>
        <td><strong>Total Sesi Jurnal</strong></td>
        <td>: {{ count($riwayatList ?? []) }} Sesi</td>
        <td><strong>Rata-Rata Kehadiran</strong></td>
        <td>: {{ $overallPct ?? 0 }}%</td>
    </tr>
</table>

{{-- Stats Grid --}}
<table class="stats-grid">
    <tr>
        <td style="width: 16.6%">
            <div class="stats-label">Total Sesi</div>
            <div class="stats-val">{{ count($riwayatList ?? []) }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #16a34a">Total Hadir</div>
            <div class="stats-val" style="color: #16a34a">{{ $totalHadir ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #d97706">Total Sakit</div>
            <div class="stats-val" style="color: #d97706">{{ $totalSakit ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #2563eb">Total Izin</div>
            <div class="stats-val" style="color: #2563eb">{{ $totalIzin ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #7c3aed">Total Dispen</div>
            <div class="stats-val" style="color: #7c3aed">{{ $totalDispen ?? 0 }}</div>
        </td>
        <td style="width: 16.6%">
            <div class="stats-label" style="color: #dc2626">Total Alpa</div>
            <div class="stats-val" style="color: #dc2626">{{ $totalAlpa ?? 0 }}</div>
        </td>
    </tr>
</table>

{{-- Data Table --}}
<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width: 25px" rowspan="2">No</th>
            <th style="width: 65px" rowspan="2">Tanggal</th>
            <th style="width: 55px" rowspan="2">Hari</th>
            <th style="width: 60px" rowspan="2">Kelas</th>
            <th rowspan="2">Mata Pelajaran / Guru</th>
            <th class="center" colspan="5" style="padding: 3px">Jumlah Siswa</th>
            <th class="center" style="width: 55px" rowspan="2">% Hadir</th>
        </tr>
        <tr>
            <th class="center" style="width: 25px; color: #16a34a">H</th>
            <th class="center" style="width: 25px; color: #d97706">S</th>
            <th class="center" style="width: 25px; color: #2563eb">I</th>
            <th class="center" style="width: 25px; color: #7c3aed">D</th>
            <th class="center" style="width: 25px; color: #dc2626">A</th>
        </tr>
    </thead>
    <tbody>
        @php
            $hariMap = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
        @endphp
        @forelse($riwayatList as $idx => $r)
            @php
                $dt = \Carbon\Carbon::parse($r->tanggal);
                $namaHari = $hariMap[$dt->format('l')] ?? $dt->format('l');
                $pct = (int) ($r->persentase ?? 100);
                $badgeClass = $pct >= 85 ? 'badge-success' : ($pct >= 75 ? 'badge-warning' : 'badge-danger');
            @endphp
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>{{ $dt->format('d/m/Y') }}</td>
                <td>{{ $namaHari }}</td>
                <td>{{ $r->nama_kelas ?? '-' }}</td>
                <td>
                    <div style="font-weight: 600">{{ $r->nama_mapel ?? 'Presensi Harian' }}</div>
                    <div style="font-size: 7.5pt; color: #64748b">{{ $r->nama_guru ?? '-' }}</div>
                </td>
                <td class="center" style="font-weight: 600; color: #16a34a">{{ $r->jumlah_hadir }}</td>
                <td class="center" style="font-weight: 600; color: #d97706">{{ $r->jumlah_sakit }}</td>
                <td class="center" style="font-weight: 600; color: #2563eb">{{ $r->jumlah_izin }}</td>
                <td class="center" style="font-weight: 600; color: #7c3aed">{{ $r->jumlah_dispen ?? 0 }}</td>
                <td class="center" style="font-weight: 600; color: #dc2626">{{ $r->jumlah_alpa }}</td>
                <td class="center">
                    <span class="badge {{ $badgeClass }}">{{ $pct }}%</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="center" style="padding: 20px; color: #94a3b8">Belum ada riwayat jurnal presensi pada periode ini.</td>
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
                <div style="font-weight: 600;">Petugas / Penanggung Jawab</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $waliKelasNama ?? ($guruAktifNama ?? 'Administrator') }}</div>
                <div class="signature-nip">NIP. {{ $waliKelasNip ?? '-' }}</div>
            </div>
        </td>
    </tr>
</table>
@endsection
