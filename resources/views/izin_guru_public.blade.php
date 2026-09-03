<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persetujuan Izin Guru - PresensiKita</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        body { background: var(--body-bg); margin: 0; padding: 24px; }
        .box {
            max-width: 620px; margin: 30px auto; background: #fff;
            border-radius: var(--radius); padding: 26px;
            border: 1px solid var(--border); box-shadow: var(--shadow-md);
        }
        h1 { font-size: 22px; font-weight: 800; margin: 0 0 6px; }
        p { color: var(--text-secondary); font-size: 13px; line-height: 1.5; }
        .info { background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin: 20px 0; }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 9px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
        .row:last-child { border-bottom: 0; }
        .label { color: var(--text-secondary); }
        .status { border-radius: 99px; }
        .decision { border-top: 1px solid var(--border); padding-top: 18px; margin-top: 18px; }
        .decision h3 { font-size: 15px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; }
        .decision form { display: flex; flex-direction: column; gap: 12px; }
        .decision .form-textarea { margin: 0; }
        .decision .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
        @media (max-width: 640px) {
            body { padding: 12px; }
            .box { margin: 10px auto; padding: 20px; }
            .row { display: block; }
            .row strong, .row .status { display: block; margin-top: 4px; }
        }
    </style>
</head>
<body>
<main class="box">
    <h1>Persetujuan Izin Guru</h1>
    <p>Halaman ini dapat dibuka tanpa login. Silakan periksa data berikut sebelum memberikan keputusan.</p>

    <section class="info">
        <div class="row"><span class="label">Guru</span><strong>{{ $izin->guru->nama_guru ?? '-' }}</strong></div>
        <div class="row"><span class="label">Tanggal izin</span><strong>{{ $izin->tanggal_izin->format('d-m-Y') }}</strong></div>
        <div class="row"><span class="label">Dimintakan oleh</span><strong>{{ $izin->guruPiket->nama_guru ?? '-' }}</strong></div>
        <div class="row"><span class="label">Alasan</span><strong>{{ $izin->alasan }}</strong></div>
    </section>

    <div class="row"><span>Status Kepsek</span><span class="badge badge-warning status {{ $izin->status_kepsek === 'disetujui' ? 'badge-success' : ($izin->status_kepsek === 'ditolak' ? 'badge-danger' : '') }}">{{ ucfirst($izin->status_kepsek) }}</span></div>
    <div class="row"><span>Status Waka</span><span class="badge badge-warning status {{ $izin->status_waka === 'disetujui' ? 'badge-success' : ($izin->status_waka === 'ditolak' ? 'badge-danger' : '') }}">{{ ucfirst($izin->status_waka) }}</span></div>

    @if(session('approval_message'))
        <p style="color:#166534;font-weight:700">{{ session('approval_message') }}</p>
    @endif

    @if($izin->isDisetujui())
        <p style="color:#166534;font-weight:700">Izin lengkap disetujui Kepsek dan Waka.</p>
    @endif

    @if($izin->status_kepsek === 'ditolak' || $izin->status_waka === 'ditolak')
        <p style="color:#991b1b;font-weight:700">Permintaan izin ditolak.</p>
    @endif

    <div class="decision">
        <h3>Keputusan Kepsek</h3>
        @if($izin->status_kepsek === 'menunggu')
            <form method="POST" action="{{ $kepsekUrl }}">
                @csrf
                <textarea name="catatan" rows="2" class="form-textarea" placeholder="Catatan (opsional)"></textarea>
                <div class="btn-group">
                    <button class="btn btn-success" name="keputusan" value="disetujui">Setujui</button>
                    <button class="btn btn-danger" name="keputusan" value="ditolak">Tolak</button>
                </div>
            </form>
        @else
            <p>Keputusan Kepsek sudah diberikan.</p>
        @endif
    </div>

    <div class="decision">
        <h3>Keputusan Waka</h3>
        @if($izin->status_waka === 'menunggu')
            <form method="POST" action="{{ $wakaUrl }}">
                @csrf
                <textarea name="catatan" rows="2" class="form-textarea" placeholder="Catatan (opsional)"></textarea>
                <div class="btn-group">
                    <button class="btn btn-success" name="keputusan" value="disetujui">Setujui</button>
                    <button class="btn btn-danger" name="keputusan" value="ditolak">Tolak</button>
                </div>
            </form>
        @else
            <p>Keputusan Waka sudah diberikan.</p>
        @endif
    </div>
</main>
</body>
</html>