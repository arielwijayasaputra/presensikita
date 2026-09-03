<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Persetujuan Dispensasi Siswa - PresensiKita</title>
<link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background: var(--body-bg); margin: 0; padding: 20px; }
.box { max-width: 620px; margin: auto; background: #fff; padding: 26px; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-md); }
h1 { font-size: 22px; font-weight: 800; }
.intro { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }
.info { background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius); padding: 14px; }
.row { display: flex; justify-content: space-between; gap: 16px; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.row:last-child { border: 0; }
.label { color: var(--text-secondary); }
.decision { border-top: 1px solid var(--border); margin-top: 20px; padding-top: 18px; }
.decision form { display: flex; flex-direction: column; gap: 12px; align-items: flex-start; margin-top: 8px; }
.decision .form-textarea { max-width: 100%; }
.decision .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
@media (max-width: 520px) {
    body { padding: 10px; }
    .box { padding: 20px; }
    .row { display: block; }
    .row strong, .row .status { display: block; margin-top: 4px; }
}
</style>
</head>
<body>
<main class="box">
<h1>Persetujuan Dispensasi Siswa</h1>
<p class="intro">Halaman khusus {{ $role === 'waka' ? 'Waka' : 'Guru Piket' }}. Tidak perlu login.</p>
<section class="info">
<div class="row"><span class="label">Siswa</span><strong>{{ $dispen->siswa->nama_siswa ?? '-' }}</strong></div>
<div class="row"><span class="label">Kelas</span><strong>{{ $dispen->siswa->kelas->nama_kelas ?? '-' }}</strong></div>
<div class="row"><span class="label">Tanggal</span><strong>{{ $dispen->tanggal_dispen->format('d-m-Y') }}</strong></div>
<div class="row"><span class="label">Alasan</span><strong>{{ $dispen->alasan }}</strong></div>
</section>
<div class="decision">
<h2>Keputusan {{ $role === 'waka' ? 'Waka' : 'Guru Piket' }}</h2>
<div class="row"><span class="label">Status</span><span class="badge badge-warning {{ $status === 'disetujui' ? 'badge-success' : ($status === 'ditolak' ? 'badge-danger' : '') }}">{{ ucfirst($status) }}</span></div>
@if(session('approval_message'))<p style="color:#166534;font-weight:700">{{ session('approval_message') }}</p>@endif
@if($status === 'menunggu')
<form method="POST" action="{{ $approvalUrl }}" onsubmit="return konfirmasi(event, '{{ $role === 'waka' ? 'Waka' : 'Guru Piket' }}')">@csrf
<textarea name="catatan" rows="3" class="form-textarea" placeholder="Catatan (opsional)"></textarea>
<div class="btn-group">
<button class="btn btn-success" name="keputusan" value="disetujui">Setujui Dispensasi</button>
<button class="btn btn-danger" name="keputusan" value="ditolak">Tolak Dispensasi</button>
</div>
</form>
@else<p class="intro">Keputusan sudah diberikan.</p>@endif
</div>
</main>
<script>
function konfirmasi(e, role) {
    e.preventDefault();
    const form = e.target;
    const isReject = e.submitter && e.submitter.value === 'ditolak';
    Swal.fire({
        title: isReject ? 'Tolak dispensasi?' : 'Setujui dispensasi?',
        text: 'Keputusan sebagai ' + role + ' akan disimpan.',
        icon: isReject ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isReject ? 'Ya, tolak' : 'Ya, setujui',
        cancelButtonText: 'Batal',
        confirmButtonColor: isReject ? '#dc2626' : '#16a34a',
        cancelButtonColor: '#64748b',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            const i = document.createElement('input');
            i.type = 'hidden';
            i.name = 'keputusan';
            i.value = isReject ? 'ditolak' : 'disetujui';
            form.appendChild(i);
            form.submit();
        }
    });
}
</script>
</body>
</html>