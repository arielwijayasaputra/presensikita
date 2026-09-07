<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persetujuan Dispensasi Siswa - PresensiKita</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_white.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            max-width: 600px;
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
        .decision h2 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px;
        }
        .decision form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 12px;
        }
        .decision .form-textarea {
            width: 100%;
            box-sizing: border-box;
            border-radius: 10px;
            padding: 10px 14px;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-group .btn {
            flex: 1;
            min-width: 140px;
            padding: 12px 16px;
            font-size: 13.5px;
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
            .row strong, .row .badge {
                margin-top: 2px;
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
        <h1 class="box-title">Persetujuan Dispensasi Siswa</h1>
        <p class="intro">Halaman khusus {{ $role === 'waka' ? 'Waka Kesiswaan' : 'Guru Piket' }}. Keputusan langsung tercatat di sistem.</p>
    </div>

    <div class="box-body">
        <section class="info">
            <div class="row"><span class="label">Nama Siswa</span><strong style="color:#0f172a">{{ $dispen->siswa->nama_siswa ?? '-' }}</strong></div>
            <div class="row"><span class="label">Kelas</span><strong style="color:#1e3a8a">{{ $dispen->siswa->kelas->nama_kelas ?? '-' }}</strong></div>
            <div class="row"><span class="label">Tanggal Dispensasi</span><strong>{{ $dispen->tanggal_dispen->format('d-m-Y') }}</strong></div>
            <div class="row"><span class="label">Alasan Keperluan</span><strong style="color:#334155;text-align:right">{{ $dispen->alasan }}</strong></div>
            @if($dispen->foto_surat)
            <div class="row">
                <span class="label">Foto Surat Keterangan</span>
                <a href="{{ Storage::disk('public')->url($dispen->foto_surat) }}" target="_blank" rel="noopener" style="color:#2563eb;font-weight:700;text-decoration:none">
                    Buka Lampiran Surat &rarr;
                </a>
            </div>
            @endif
        </section>

        <div class="decision">
            <h2>Status &amp; Keputusan {{ $role === 'waka' ? 'Waka' : 'Guru Piket' }}</h2>
            <div class="row">
                <span class="label">Status Saat Ini</span>
                <span class="badge {{ $status === 'disetujui' ? 'badge-success' : ($status === 'ditolak' ? 'badge-danger' : 'badge-warning') }}" style="font-size:12.5px;padding:5px 12px">
                    {{ ucfirst($status) }}
                </span>
            </div>

            @if(session('approval_message'))
                <div class="alert alert-success" style="margin-top:14px">
                    {{ session('approval_message') }}
                </div>
            @endif

            @if($status === 'menunggu')
                <form method="POST" action="{{ $approvalUrl }}" onsubmit="return konfirmasi(event, '{{ $role === 'waka' ? 'Waka Kesiswaan' : 'Guru Piket' }}')">
                    @csrf
                    <div>
                        <label class="form-label" style="margin-bottom:6px">Catatan Persetujuan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-textarea" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn btn-success" name="keputusan" value="disetujui">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Setujui Dispensasi
                        </button>
                        <button type="submit" class="btn btn-danger" name="keputusan" value="ditolak">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Tolak Dispensasi
                        </button>
                    </div>
                </form>
            @else
                <p class="intro" style="color:#64748b;margin-top:14px;font-style:italic">
                    Keputusan telah tersimpan di sistem.
                </p>
            @endif
        </div>
    </div>
</main>

<script>
function konfirmasi(e, role) {
    e.preventDefault();
    const form = e.target;
    const isReject = e.submitter && e.submitter.value === 'ditolak';
    Swal.fire({
        title: isReject ? 'Tolak dispensasi?' : 'Setujui dispensasi?',
        text: 'Keputusan sebagai ' + role + ' akan disimpan dan dikirimkan.',
        icon: isReject ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isReject ? 'Ya, Tolak' : 'Ya, Setujui',
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