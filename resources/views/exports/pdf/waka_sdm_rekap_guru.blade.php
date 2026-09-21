@extends('exports.pdf.layout')

@section('title', 'Rekap Kehadiran Mengajar Guru - ' . ($namaBulan ?? '') . ' ' . ($tahun ?? date('Y')))

@section('content')
<div class="doc-header">
    <div class="doc-title">REKAPITULASI KEHADIRAN MENGAJAR GURU</div>
    <div class="doc-subtitle">Periode: {{ $namaBulan ?? '' }} {{ $tahun ?? date('Y') }}</div>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 20%"><strong>Total Tenaga Pendidik</strong></td>
        <td style="width: 30%">: {{ count($rekap ?? []) }} Guru</td>
        <td style="width: 20%"><strong>Tahun Ajaran</strong></td>
        <td style="width: 30%">: {{ $tahunAjaran->nama_tahun ?? (date('Y') . '/' . (date('Y') + 1)) }}</td>
    </tr>
</table>

{{-- Data Table --}}
<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width: 25px">No</th>
            <th style="width: 90px">NIP</th>
            <th>Nama Guru</th>
            <th class="center" style="width: 55px">Total Sesi</th>
            <th class="center" style="width: 50px; color: #16a34a">Hadir</th>
            <th class="center" style="width: 65px; color: #d97706">Izin/Tidak</th>
            <th class="center" style="width: 65px; color: #64748b">Belum Isi</th>
            <th class="center" style="width: 55px">% Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekap as $idx => $row)
            @php
                $pct = (int) ($row['persentase'] ?? 0);
                $badgeClass = $pct >= 85 ? 'badge-success' : ($pct >= 75 ? 'badge-warning' : 'badge-danger');
            @endphp
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>{{ $row['nip'] ?: '-' }}</td>
                <td style="font-weight: 600">{{ $row['nama_guru'] }}</td>
                <td class="center" style="font-weight: 600">{{ $row['total_sesi'] }}</td>
                <td class="center" style="font-weight: 600; color: #16a34a">{{ $row['hadir'] }}</td>
                <td class="center" style="font-weight: 600; color: #d97706">{{ $row['tidak_hadir'] }}</td>
                <td class="center" style="font-weight: 600; color: #64748b">{{ $row['belum_isi'] }}</td>
                <td class="center">
                    <span class="badge {{ $badgeClass }}">{{ $pct }}%</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="center" style="padding: 20px; color: #94a3b8">Belum ada data mengajar guru pada periode ini.</td>
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
                <div style="font-weight: 600;">Waka SDM / Kurikulum</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $wakaSdmNama ?? 'Waka SDM' }}</div>
                <div class="signature-nip">NIP. {{ $wakaSdmNip ?? '-' }}</div>
            </div>
        </td>
    </tr>
</table>
@endsection
