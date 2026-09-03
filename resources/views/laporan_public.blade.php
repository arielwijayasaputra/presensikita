<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Laporan / Pengaduan - PresensiKita</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_white.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #334155;
        }

        .laporan-card {
            background: #ffffff;
            width: 100%;
            max-width: 640px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .laporan-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 32px 36px;
            color: #ffffff;
            position: relative;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .laporan-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 6px;
            line-height: 1.25;
        }

        .laporan-subtitle {
            font-size: 13.5px;
            color: #93c5fd;
            line-height: 1.5;
        }

        .laporan-body {
            padding: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .input-wrap .form-control,
        .input-wrap .form-textarea {
            padding-left: 42px;
        }

        .input-wrap .form-select {
            padding-right: 36px;
            background-image: none;
        }

        .select-chevron {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .textarea-icon {
            top: 18px;
            transform: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            justify-content: center;
            width: 100%;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #2563eb;
        }

        @media (max-width: 640px) {
            .laporan-header { padding: 24px 20px; }
            .laporan-body { padding: 24px 20px; }
            .laporan-title { font-size: 20px; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>

<div class="laporan-card">
    <div class="laporan-header">
        <div class="brand-pill">
            <img src="{{ asset('logo.png') }}" alt="Logo PresensiKita" style="width:18px;height:18px;object-fit:contain;background:#fff;border-radius:5px;">
            PresensiKita
        </div>
        <h1 class="laporan-title">Form Laporan / Pengaduan</h1>
        <p class="laporan-subtitle">Sampaikan saran, masalah, atau laporan Anda langsung kepada Administrator sekolah.</p>
    </div>

    <div class="laporan-body">
        @if(session('success_laporan'))
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success_laporan') }}
            </div>
        @endif

        <form id="public-laporan-form" action="{{ route('laporan.public.store') }}" method="POST">
            @csrf

            {{-- 1. Pilih Role --}}
            <div class="form-group">
                <label class="form-label">Peran / Role Anda <span class="req">*</span></label>
                <div class="input-wrap">
                    <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <select name="role_pelapor" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Role Pelapor --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                    <svg class="select-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="form-hint">Pilih identitas peran Anda saat mengirimkan laporan ini.</div>
            </div>

            {{-- 2. Nama Pelapor --}}
            <div class="form-group">
                <label class="form-label">Nama Pelapor <span class="req">*</span></label>
                <div class="input-wrap">
                    <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" name="nama_pelapor" class="form-control" placeholder="Masukkan Nama Lengkap Anda" required>
                </div>
            </div>

            {{-- 3. Judul Laporan --}}
            <div class="form-group">
                <label class="form-label">Judul / Subjek Laporan <span class="req">*</span></label>
                <div class="input-wrap">
                    <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Kendala Absensi / Saran Fasilitas / Kendala Login" required>
                </div>
            </div>

            {{-- 4. Isi Laporan --}}
            <div class="form-group">
                <label class="form-label">Rincian Laporan <span class="req">*</span></label>
                <div class="input-wrap">
                    <svg class="input-icon textarea-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <textarea name="isi_laporan" class="form-textarea" placeholder="Jelaskan detail laporan atau masalah Anda secara jelas..." required></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="btn-submit-laporan">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Kirim Laporan
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke Halaman Login
        </a>
    </div>
</div>

<script>
    const navigationEntry = performance.getEntriesByType('navigation')[0];
    if (navigationEntry && navigationEntry.type === 'reload') {
        window.location.replace(@json(route('login')));
    }

    document.getElementById('public-laporan-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('btn-submit-laporan');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg style="animation:spin 1s linear infinite" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
            Mengirim...
        `;

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Laporan Berhasil Terkirim!',
                    text: data.message,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Selesai'
                }).then(() => {
                    form.reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: data.message || 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Sistem',
                text: 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
                confirmButtonColor: '#ef4444'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
</script>

<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
</body>
</html>
