<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persetujuan Izin Guru - PresensiKita</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f1f5f9; color:#1e293b; margin:0; padding:24px; }
        .box { max-width:620px; margin:30px auto; background:#fff; border-radius:14px; padding:26px; box-shadow:0 8px 24px rgba(15,23,42,.12); }
        h1 { font-size:22px; margin:0 0 6px; }
        p { color:#64748b; font-size:13px; line-height:1.5; }
        .info { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin:20px 0; }
        .row { display:flex; justify-content:space-between; gap:16px; padding:9px 0; border-bottom:1px solid #e2e8f0; font-size:13px; }
        .row:last-child { border-bottom:0; }
        .label { color:#64748b; }
        .status { display:inline-block; padding:5px 9px; border-radius:6px; background:#fef3c7; color:#92400e; font-size:12px; font-weight:700; }
        .status.ok { background:#dcfce7; color:#166534; }
        .status.no { background:#fee2e2; color:#991b1b; }
        .decision { border-top:1px solid #e2e8f0; padding-top:18px; margin-top:18px; }
        textarea { width:100%; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:8px; padding:10px; margin:8px 0 10px; font:inherit; }
        button { border:0; border-radius:8px; padding:10px 14px; color:#fff; font-weight:700; cursor:pointer; margin-right:6px; }
        .yes { background:#16a34a; }
        .no { background:#dc2626; }
        @media (max-width:640px) { body { padding:12px; } .box { margin:10px auto; padding:20px; } .row { display:block; } .row strong, .row .status { display:block; margin-top:4px; } }
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

    <div class="row"><span>Status Kepsek</span><span class="status {{ $izin->status_kepsek === 'disetujui' ? 'ok' : ($izin->status_kepsek === 'ditolak' ? 'no' : '') }}">{{ ucfirst($izin->status_kepsek) }}</span></div>
    <div class="row"><span>Status Waka</span><span class="status {{ $izin->status_waka === 'disetujui' ? 'ok' : ($izin->status_waka === 'ditolak' ? 'no' : '') }}">{{ ucfirst($izin->status_waka) }}</span></div>

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
                <textarea name="catatan" rows="2" placeholder="Catatan (opsional)"></textarea>
                <button class="yes" name="keputusan" value="disetujui">Setujui</button>
                <button class="no" name="keputusan" value="ditolak">Tolak</button>
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
                <textarea name="catatan" rows="2" placeholder="Catatan (opsional)"></textarea>
                <button class="yes" name="keputusan" value="disetujui">Setujui</button>
                <button class="no" name="keputusan" value="ditolak">Tolak</button>
            </form>
        @else
            <p>Keputusan Waka sudah diberikan.</p>
        @endif
    </div>
</main>
</body>
</html>
