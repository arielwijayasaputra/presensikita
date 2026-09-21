<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dokumen Laporan Presensi')</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            size: @yield('page_size', 'A4 portrait');
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-title {
            text-align: center;
        }
        .kop-title h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-title h3 {
            margin: 2px 0 0 0;
            font-size: 11pt;
            font-weight: 700;
            color: #334155;
        }
        .kop-title p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            color: #64748b;
        }
        .doc-header {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            font-size: 9.5pt;
            color: #475569;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            font-size: 9pt;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 6px;
        }
        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .stats-grid td {
            padding: 8px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .stats-label {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
        }
        .stats-val {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
        }
        .data-table th.center, .data-table td.center {
            text-align: center;
        }
        .data-table th.right, .data-table td.right {
            text-align: right;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafbfd;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger  { background: #fee2e2; color: #b91c1c; }
        .badge-info    { background: #e0f2fe; color: #0369a1; }
        
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .signature-table td {
            vertical-align: top;
            width: 50%;
        }
        .signature-box {
            text-align: center;
            width: 240px;
            margin: 0 auto;
        }
        .signature-space {
            height: 60px;
        }
        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }
        .signature-nip {
            font-size: 8.5pt;
            color: #475569;
        }
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
    @yield('extra_css')
</head>
<body>
    {{-- Kop Surat Sekolah --}}
    @php
        $namaSekolah = \App\Models\Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu');
        $alamatSekolah = \App\Models\Pengaturan::get('alamat', 'Jl. Ki Mangunsarkoro VI No. 3, Boyolangu, Tulungagung');
        $teleponSekolah = \App\Models\Pengaturan::get('telepon_sekolah', '(0355) 323457');
        $emailSekolah = \App\Models\Pengaturan::get('email_sekolah', 'smkn1boyolangu@yahoo.co.id');
        $npsn = \App\Models\Pengaturan::get('npsn', '20516934');
    @endphp
    <table class="kop-table">
        <tr>
            <td class="kop-title">
                <h2>{{ $namaSekolah }}</h2>
                <h3>SISTEM INFORMASI PRESENSI &amp; JURNAL PEMBELAJARAN</h3>
                <p>
                    {{ $alamatSekolah ?: 'Boyolangu, Tulungagung, Jawa Timur' }}
                    @if($teleponSekolah) &bull; Telp: {{ $teleponSekolah }} @endif
                    @if($emailSekolah) &bull; Email: {{ $emailSekolah }} @endif
                    @if($npsn) &bull; NPSN: {{ $npsn }} @endif
                </p>
            </td>
        </tr>
    </table>

    {{-- Main Content --}}
    @yield('content')

    {{-- Footer --}}
    <div class="footer-note">
        Dicetak otomatis dari Sistem PresensiKita pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i:s') }} WIB
    </div>
</body>
</html>
