<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Persetujuan Kepsek - PresensiKita</title>
<link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background: var(--body-bg); margin: 0; padding: 20px; }
.box { width: 100%; max-width: 620px; margin: 20px auto; background: #fff; border-radius: var(--radius); padding: 26px; border: 1px solid var(--border); box-shadow: var(--shadow-md); }
h1 { font-size: 22px; font-weight: 800; margin: 0 0 8px; }
.intro { color: var(--text-secondary); font-size: 13px; line-height: 1.5; }
.info { background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius); padding: 14px; }
.row { display: flex; justify-content: space-between; gap: 16px; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.row:last-child { border: 0; }
.label { color: var(--text-secondary); }
.decision { border-top: 1px solid var(--border); margin-top: 20px; padding-top: 18px; }
h2 { font-size: 16px; font-weight: 800; color: #1d4ed8; }
.decision form { display: flex; flex-direction: column; gap: 12px; align-items: flex-start; margin-top: 8px; }
.decision .form-textarea { max-width: 100%; }
.decision .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
@media (max-width: 520px) {
    body { padding: 10px; }
    .box { margin: 0 auto; padding: 20px; }
    .row { display: block; }
    .row strong, .row .status { display: block; margin-top: 4px; }
}
</style>
</head>
<body>
<main class="box">
<h1>Persetujuan Izin Guru</h1>
<p class="intro">Halaman khusus Kepala Sekolah. Tidak perlu login untuk memberikan keputusan.</p>
<section class="info">
<div class="row"><span class="label">Guru</span><strong>{{ $izin->guru->nama_guru ?? '-' }}</strong></div>
<div class="row"><span class="label">Tanggal izin</span><strong>{{ $izin->tanggal_izin->format('d-m-Y') }}</strong></div>
<div class="row"><span class="label">Dimintakan oleh</span><strong>{{ $izin->guruPiket->nama_guru ?? '-' }}</strong></div>
<div class="row"><span class="label">Alasan</span><strong>{{ $izin->alasan }}</strong></div>
</section>
<div class="decision">
<h2>Keputusan Kepsek</h2>
<div class="row"><span class="label">Status</span><span class="badge badge-warning {{ $izin->status_kepsek === 'disetujui' ? 'badge-success' : ($izin->status_kepsek === 'ditolak' ? 'badge-danger' : '') }}">{{ ucfirst($izin->status_kepsek) }}</span></div>
@if(session('approval_message'))<p style="color:#166534;font-weight:700">{{ session('approval_message') }}</p>@endif
@if($izin->status_kepsek === 'menunggu')
<form method="POST" action="{{ $approvalUrl }}" onsubmit="return konfirmasiKeputusan(event, 'Kepsek')">@csrf
<textarea name="catatan" rows="3" class="form-textarea" placeholder="Catatan (opsional)"></textarea>
<div class="btn-group">
<button class="btn btn-success" name="keputusan" value="disetujui">Setujui Izin</button>
<button class="btn btn-danger" name="keputusan" value="ditolak">Tolak Izin</button>
</div>
</form>
@else<p class="intro">Keputusan Kepsek sudah diberikan.</p>@endif
</div>
</main>
<script>
function konfirmasiKeputusan(event, role) {
    event.preventDefault();
    const form = event.target;
    const isReject = event.submitter && event.submitter.value === 'ditolak';
    Swal.fire({
        title: isReject ? 'Tolak izin guru?' : 'Setujui izin guru?',
        text: 'Keputusan sebagai ' + role + ' akan disimpan.',
        icon: isReject ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isReject ? 'Ya, tolak izin' : 'Ya, setujui izin',
        cancelButtonText: 'Batal',
        confirmButtonColor: isReject ? '#dc2626' : '#16a34a',
        cancelButtonColor: '#64748b',
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'keputusan';
            hidden.value = isReject ? 'ditolak' : 'disetujui';
            form.appendChild(hidden);
            form.submit();
        }
    });
}
</script>
</body>
</html>