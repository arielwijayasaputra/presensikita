@extends('exports.pdf.layout')

@section('title', 'Rekap Jurnal Pembelajaran - ' . ($namaKelas ?? 'Kelas'))

@section('content')
<div class="doc-header">
    <div class="doc-title">REKAPITULASI JURNAL PEMBELAJARAN</div>
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
        <td><strong>Total Sesi Belajar</strong></td>
        <td>: {{ count($jurnals ?? []) }} Sesi</td>
    </tr>
</table>

{{-- Data Table --}}
<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width: 25px">No</th>
            <th style="width: 65px">Tanggal</th>
            <th style="width: 50px">Waktu</th>
            <th style="width: 110px">Mata Pelajaran</th>
            <th style="width: 110px">Guru Pengajar</th>
            <th class="center" style="width: 60px">Kehadiran Guru</th>
            <th>Materi Pembelajaran</th>
            <th class="center" style="width: 50px">Siswa Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jurnals as $idx => $j)
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>{{ date('d/m/Y', strtotime($j->tanggal)) }}</td>
                <td>{{ date('H:i', strtotime($j->waktu_input)) }}</td>
                <td style="font-weight: 600">{{ $j->nama_mapel }}</td>
                <td>{{ $j->nama_guru }}</td>
                <td class="center">
                    <span class="badge {{ $j->status_kehadiran_guru === 'Hadir' ? 'badge-success' : 'badge-warning' }}">
                        {{ $j->status_kehadiran_guru }}
                    </span>
                </td>
                <td style="font-size: 8pt">{{ $j->materi ?: '-' }}</td>
                <td class="center" style="font-weight: 700; color: #16a34a">{{ $j->jumlah_hadir }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="center" style="padding: 20px; color: #94a3b8">Belum ada riwayat jurnal pada periode ini.</td>
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
