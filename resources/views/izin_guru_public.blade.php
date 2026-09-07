<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persetujuan Izin Guru - PresensiKita</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_white.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            margin: 0;
            padding: 24px 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            box-sizing: border-box;
        }
        .box {
            max-width: 620px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .box-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff;
            padding: 24px 28px;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .box-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0 0 4px;
            letter-spacing: -0.01em;
        }
        .intro {
            font-size: 12.5px;
            color: #bfdbfe;
            margin: 0;
            line-height: 1.5;
        }
        .box-body {
            padding: 24px 28px;
        }
        .info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 9px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13.5px;
        }
        .row:last-child {
            border-bottom: 0;
        }
        .label {
            color: #64748b;
            font-weight: 500;
        }
        .decision {
            border-top: 1px solid #e2e8f0;
            margin-top: 16px;
            padding-top: 18px;
        }
        .decision h3 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 10px;
        }
        .decision form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 16px;
        }
        .decision .form-textarea {
            width: 100%;
            box-sizing: border-box;
            border-radius: 10px;
            padding: 10px 14px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-group .btn {
            flex: 1;
            min-width: 120px;
            padding: 10px 16px;
            font-size: 13px;
        }
        @media (max-width: 520px) {
            body { padding: 12px; }
            .box-header { padding: 20px; }
            .box-body { padding: 18px; }
            .row {
                flex-direction: column;
                align-items: flex-start;
                gap: 3px;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<main class="box">
    <div class="box-header">
        <div class="brand-badge">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="width:16px;height:16px;object-fit:contain;background:#fff;border-radius:4px;">
            PresensiKita
        </div>
        <h1 class="box-title">Persetujuan Izin Guru</h1>
        <p class="intro">Halaman persetujuan izin guru oleh Kepala Sekolah dan Wakil Kepala Sekolah.</p>
    </div>

    <div class="box-body">
        <section class="info">
            <div class="row"><span class="label">Nama Guru</span><strong style="color:#0f172a">{{ $izin->guru->nama_guru ?? '-' }}</strong></div>
            <div class="row"><span class="label">Tanggal Izin</span><strong>{{ $izin->tanggal_izin->format('d-m-Y') }}</strong></div>
            <div class="row"><span class="label">Dimintakan Oleh</span><strong>{{ $izin->guruPiket->nama_guru ?? 'Guru Piket' }}</strong></div>
            <div class="row"><span class="label">Alasan Izin</span><strong style="color:#334155;text-align:right">{{ $izin->alasan }}</strong></div>
            @if($izin->foto_surat)
            <div class="row">
                <span class="label">Foto Surat Keterangan</span>
                <a href="{{ Storage::disk('public')->url($izin->foto_surat) }}" target="_blank" rel="noopener" style="color:#2563eb;font-weight:700;text-decoration:none">
                    Buka Lampiran Surat &rarr;
                </a>
            </div>
            @endif
        </section>

        <div class="row"><span class="label">Status Kepala Sekolah</span><span class="badge {{ $izin->status_kepsek === 'disetujui' ? 'badge-success' : ($izin->status_kepsek === 'ditolak' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($izin->status_kepsek) }}</span></div>
        <div class="row" style="margin-bottom:14px"><span class="label">Status Waka</span><span class="badge {{ $izin->status_waka === 'disetujui' ? 'badge-success' : ($izin->status_waka === 'ditolak' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($izin->status_waka) }}</span></div>

        @if(session('approval_message'))
            <div class="alert alert-success" style="margin-bottom:14px">
                {{ session('approval_message') }}
            </div>
        @endif

        @if($izin->isDisetujui())
            <div class="alert alert-success" style="margin-bottom:14px">
                Izin lengkap telah disetujui oleh Kepsek dan Waka.
            </div>
        @elseif($izin->status_kepsek === 'ditolak' || $izin->status_waka === 'ditolak')
            <div class="alert alert-danger" style="margin-bottom:14px">
                Permintaan izin ditolak.
            </div>
        @endif

        <div class="decision">
            <h3>Keputusan Kepsek</h3>
            @if($izin->status_kepsek === 'menunggu')
                <form method="POST" action="{{ $kepsekUrl }}">
                    @csrf
                    <textarea name="catatan" rows="2" class="form-textarea" placeholder="Catatan Kepsek (opsional)"></textarea>
                    <div class="btn-group">
                        <button class="btn btn-success" name="keputusan" value="disetujui">Setujui Kepsek</button>
                        <button class="btn btn-danger" name="keputusan" value="ditolak">Tolak</button>
                    </div>
                </form>
            @else
                <p style="font-size:12.5px;color:#64748b;margin-bottom:14px">Keputusan Kepsek sudah tersimpan ({{ ucfirst($izin->status_kepsek) }}).</p>
            @endif
        </div>

        <div class="decision">
            <h3>Keputusan Waka</h3>
            @if($izin->status_waka === 'menunggu')
                <form method="POST" action="{{ $wakaUrl }}">
                    @csrf
                    <textarea name="catatan" rows="2" class="form-textarea" placeholder="Catatan Waka (opsional)"></textarea>
                    <div class="btn-group">
                        <button class="btn btn-success" name="keputusan" value="disetujui">Setujui Waka</button>
                        <button class="btn btn-danger" name="keputusan" value="ditolak">Tolak</button>
                    </div>
                </form>
            @else
                <p style="font-size:12.5px;color:#64748b">Keputusan Waka sudah tersimpan ({{ ucfirst($izin->status_waka) }}).</p>
            @endif
        </div>
    </div>
</main>
</body>
</html>