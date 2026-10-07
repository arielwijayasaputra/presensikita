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
    <!-- Theme Initialization (Prevent FOUC) -->
    <script>
        (function(){
            var t = localStorage.getItem('presensikita_theme');
            if(!t){
                t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [data-theme="dark"] body {
            background: linear-gradient(135deg, #050a14 0%, #0d172e 50%, #050a14 100%);
            color: #f8fafc;
        }
        [data-theme="dark"] .box {
            background: #152238;
            border-color: #243552;
            box-shadow: 0 16px 40px rgba(0,0,0,0.5);
        }
        [data-theme="dark"] .info {
            background: #0f1a2e;
            border-color: #243552;
        }
        [data-theme="dark"] .row {
            border-bottom-color: #243552;
        }
        [data-theme="dark"] .label {
            color: #94a3b8;
        }
        [data-theme="dark"] .decision h2 {
            color: #f8fafc;
        }
        [data-theme="dark"] .decision {
            border-top-color: #243552;
        }
        .header-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<main class="box">
    <div class="box-header">
        <div class="header-top-row">
            <div class="brand-badge" style="margin-bottom:0">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="width:16px;height:16px;object-fit:contain;background:#fff;border-radius:4px;">
                PresensiKita
            </div>
            <button class="theme-toggle-btn" type="button" onclick="toggleDarkMode()" aria-label="Ganti Tema" title="Ganti Mode Gelap / Terang" style="background:rgba(255,255,255,0.15);border-color:rgba(255,255,255,0.25);color:#fff">
                <svg class="theme-icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <svg class="theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/>
                    <line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </button>
        </div>
        <h1 class="box-title">Persetujuan Dispensasi Siswa</h1>
        <p class="intro">Halaman khusus {{ $role === 'waka' ? 'Waka Kesiswaan' : 'Guru Piket' }}. Keputusan langsung tercatat di sistem.</p>
    </div>

    <div class="box-body">
        <section class="info">
            @if(isset($allDispens) && $allDispens->count() > 1)
                <div class="row" style="flex-direction:column;align-items:flex-start;gap:8px">
                    <span class="label">Daftar Siswa ({{ $allDispens->count() }} Orang)</span>
                    <div style="width:100%;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden">
                        <table style="width:100%;border-collapse:collapse;font-size:13px">
                            <thead>
                                <tr style="background:#f1f5f9;text-align:left;border-bottom:1px solid #e2e8f0">
                                    <th style="padding:7px 10px;font-size:12px;color:#475569">No</th>
                                    <th style="padding:7px 10px;font-size:12px;color:#475569">Nama Siswa</th>
                                    <th style="padding:7px 10px;font-size:12px;color:#475569">Kelas</th>
                                    <th style="padding:7px 10px;font-size:12px;color:#475569">NISN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allDispens as $idx => $dItem)
                                    <tr style="border-bottom:1px solid #f1f5f9">
                                        <td style="padding:7px 10px;color:#64748b">{{ $idx + 1 }}</td>
                                        <td style="padding:7px 10px;font-weight:700">{{ $dItem->siswa->nama_siswa ?? '-' }}</td>
                                        <td style="padding:7px 10px;font-weight:600;color:#2563eb">{{ $dItem->siswa->kelas->nama_kelas ?? '-' }}</td>
                                        <td style="padding:7px 10px;color:#64748b">{{ $dItem->siswa->nisn ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="row"><span class="label">Nama Siswa</span><strong style="color:#0f172a">{{ $dispen->siswa->nama_siswa ?? '-' }}</strong></div>
                <div class="row"><span class="label">Kelas</span><strong style="color:#1e3a8a">{{ $dispen->siswa->kelas->nama_kelas ?? '-' }}</strong></div>
            @endif
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

            @if($dispen->tanda_tangan_waka)
                <div class="row" style="align-items:flex-start;padding-top:12px;padding-bottom:12px">
                    <span class="label">Tanda Tangan Waka</span>
                    <div style="text-align:right">
                        <img src="{{ Storage::disk('public')->url($dispen->tanda_tangan_waka) }}" alt="Tanda Tangan Waka Kesiswaan" style="max-height:75px;background:#ffffff;border:1px solid #cbd5e1;border-radius:8px;padding:4px;display:inline-block">
                    </div>
                </div>
            @endif

            @if(session('approval_message'))
                <div class="alert alert-success" style="margin-top:14px">
                    {{ session('approval_message') }}
                </div>
            @endif

            @if($status === 'menunggu')
                <form id="dispen-approval-form" method="POST" action="{{ $approvalUrl }}" onsubmit="return konfirmasi(event, '{{ $role === 'waka' ? 'Waka Kesiswaan' : 'Guru Piket' }}')">
                    @csrf
                    <div>
                        <label class="form-label" style="margin-bottom:6px">Catatan Persetujuan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-textarea" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <div style="margin-top:16px;margin-bottom:14px">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                            <label class="form-label" style="margin-bottom:0">
                                Tanda Tangan Digital <span style="color:#ef4444">*</span>
                            </label>
                            <button type="button" onclick="clearSignature()" style="background:transparent;border:none;color:#ef4444;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Ulangi / Hapus
                            </button>
                        </div>
                        <div style="border:1.5px dashed #cbd5e1;border-radius:10px;overflow:hidden;background:#ffffff;position:relative;touch-action:none">
                            <canvas id="signature-canvas" style="width:100%;height:150px;display:block;cursor:crosshair;background:#ffffff"></canvas>
                            <div id="signature-hint" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#94a3b8;font-size:12px;pointer-events:none;text-align:center">
                                ✍️ Goreskan tanda tangan di sini
                            </div>
                        </div>
                        <input type="hidden" id="input-tanda-tangan" name="tanda_tangan" value="">
                        <div style="font-size:11.5px;color:#64748b;margin-top:4px">Wajib tanda tangan terlebih dahulu dengan jari (layar sentuh) atau mouse sebelum menyetujui / menolak.</div>
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
let canvas, ctx;
let drawing = false;
let hasSigned = false;

function initCanvas() {
    canvas = document.getElementById('signature-canvas');
    if (!canvas) return;
    
    const rect = canvas.getBoundingClientRect();
    canvas.width = Math.round(rect.width || 480);
    canvas.height = 150;
    
    ctx = canvas.getContext('2d');
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: (clientX - r.left) * (canvas.width / r.width),
            y: (clientY - r.top) * (canvas.height / r.height)
        };
    }

    function startDraw(e) {
        e.preventDefault();
        drawing = true;
        hasSigned = true;
        const hint = document.getElementById('signature-hint');
        if (hint) hint.style.display = 'none';
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    }

    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function endDraw() {
        if (drawing) {
            drawing = false;
            ctx.closePath();
            syncSignatureInput();
        }
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', endDraw);
    canvas.addEventListener('mouseleave', endDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', endDraw);
}

function clearSignature() {
    if (!canvas || !ctx) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasSigned = false;
    const input = document.getElementById('input-tanda-tangan');
    if (input) input.value = '';
    const hint = document.getElementById('signature-hint');
    if (hint) hint.style.display = 'block';
}

function isCanvasBlank() {
    if (!canvas || !ctx || !hasSigned) return true;
    const pixelBuffer = new Uint32Array(
        ctx.getImageData(0, 0, canvas.width, canvas.height).data.buffer
    );
    return !pixelBuffer.some(color => color !== 0);
}

function syncSignatureInput() {
    const input = document.getElementById('input-tanda-tangan');
    if (!input) return;
    if (isCanvasBlank()) {
        input.value = '';
    } else {
        input.value = canvas.toDataURL('image/png');
    }
}

window.addEventListener('load', initCanvas);
window.addEventListener('resize', function() {
    if (canvas && isCanvasBlank()) {
        initCanvas();
    }
});

function konfirmasi(e, role) {
    e.preventDefault();
    const form = e.target;
    const isReject = e.submitter && e.submitter.value === 'ditolak';

    syncSignatureInput();
    const ttdVal = document.getElementById('input-tanda-tangan')?.value;
    if (!ttdVal || isCanvasBlank()) {
        Swal.fire({
            icon: 'warning',
            title: 'Tanda Tangan Diperlukan',
            text: 'Silakan tanda tangan terlebih dahulu sebelum menyetujui atau menolak dispensasi.',
            confirmButtonColor: '#2563eb'
        });
        return false;
    }

    Swal.fire({
        title: isReject ? 'Tolak dispensasi?' : 'Setujui dispensasi?',
        text: 'Keputusan sebagai ' + role + ' beserta tanda tangan akan disimpan ke sistem.',
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