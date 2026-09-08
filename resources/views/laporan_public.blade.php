<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Laporan & Pengaduan - PresensiKita</title>
    <meta name="description" content="Form pengaduan dan laporan masalah presensi digital SMKN 1 Boyolangu.">
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_white.png') }}">

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background:
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 0% 100%, rgba(37, 99, 235, 0.06) 0%, transparent 40%),
                #f2f5fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            color: #1e293b;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .laporan-wrapper {
            width: 100%;
            max-width: 620px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .laporan-card {
            background: #ffffff;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(15, 40, 100, 0.09), 0 2px 8px rgba(15, 40, 100, 0.04);
            overflow: hidden;
            border: 1px solid #e5eaf2;
            transition: all 0.3s ease;
        }

        /* ── Header Kartu Modern ── */
        .laporan-header {
            background:
                radial-gradient(circle at 85% 15%, rgba(96, 165, 250, 0.28) 0%, transparent 45%),
                linear-gradient(145deg, #0a173f 0%, #0d2160 55%, #102d73 100%);
            padding: 30px 32px 28px;
            color: #ffffff;
            position: relative;
        }

        .header-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.03em;
        }

        .brand-pill img {
            width: 18px;
            height: 18px;
            object-fit: contain;
            border-radius: 4px;
        }

        .header-theme-btn {
            background: rgba(255, 255, 255, 0.12) !important;
            border: 1px solid rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
            backdrop-filter: blur(10px);
        }

        .laporan-title {
            font-size: clamp(20px, 3.2vw, 25px);
            font-weight: 800;
            margin-bottom: 6px;
            line-height: 1.25;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .laporan-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.5;
            max-width: 90%;
        }

        /* ── Form Body ── */
        .laporan-body {
            padding: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 7px;
        }

        .form-label .req {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-hint {
            font-size: 11.5px;
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-wrap .form-control,
        .input-wrap .form-select,
        .input-wrap .form-textarea {
            width: 100%;
            padding: 11px 14px 11px 42px;
            border: 1.5px solid #dce4ef;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafd;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-wrap .form-control:focus,
        .input-wrap .form-select:focus,
        .input-wrap .form-textarea:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .input-wrap .form-select {
            padding-right: 38px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
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

        .input-wrap .form-textarea {
            min-height: 110px;
            resize: vertical;
            padding-top: 13px;
        }

        /* ── Submit Button ── */
        .btn-submit-laporan {
            width: 100%;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(90deg, #1d4ed8 0%, #2563eb 55%, #3b82f6 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 8px;
        }

        .btn-submit-laporan:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.45);
            filter: brightness(1.05);
        }

        .btn-submit-laporan:active {
            transform: translateY(0);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .btn-submit-laporan:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ── Back Link ── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
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

        /* ── Dark Mode Overrides ── */
        [data-theme="dark"] body {
            background:
                radial-gradient(circle at 100% 0%, rgba(37, 99, 235, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 0% 100%, rgba(30, 58, 138, 0.16) 0%, transparent 45%),
                #070d19;
            color: #f8fafc;
        }

        [data-theme="dark"] .laporan-card {
            background: rgba(18, 29, 51, 0.95);
            border: 1px solid rgba(59, 130, 246, 0.22);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.65);
        }

        [data-theme="dark"] .laporan-header {
            background:
                radial-gradient(circle at 85% 15%, rgba(59, 130, 246, 0.28) 0%, transparent 50%),
                linear-gradient(150deg, #081226 0%, #0c1c42 55%, #08132d 100%);
            border-bottom: 1px solid rgba(59, 130, 246, 0.15);
        }

        [data-theme="dark"] .form-label {
            color: #e2e8f0;
        }

        [data-theme="dark"] .form-hint {
            color: #94a3b8;
        }

        [data-theme="dark"] .input-wrap .form-control,
        [data-theme="dark"] .input-wrap .form-select,
        [data-theme="dark"] .input-wrap .form-textarea {
            background: #101c30;
            border-color: #243754;
            color: #f8fafc;
        }

        [data-theme="dark"] .input-wrap .form-control:focus,
        [data-theme="dark"] .input-wrap .form-select:focus,
        [data-theme="dark"] .input-wrap .form-textarea:focus {
            background: #14233c;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        [data-theme="dark"] .input-wrap .form-control::placeholder,
        [data-theme="dark"] .input-wrap .form-textarea::placeholder {
            color: #64748b;
        }

        [data-theme="dark"] .input-icon,
        [data-theme="dark"] .select-chevron {
            color: #64748b;
        }

        [data-theme="dark"] .back-link {
            color: #94a3b8;
        }

        [data-theme="dark"] .back-link:hover {
            color: #60a5fa;
        }

        /* ── Responsiveness ── */
        @media (max-width: 640px) {
            body {
                padding: 16px 12px 28px;
            }
            .laporan-card {
                border-radius: 16px;
            }
            .laporan-header {
                padding: 22px 18px 20px;
            }
            .laporan-body {
                padding: 22px 18px;
            }
            .laporan-title {
                font-size: 19px;
            }
            .laporan-subtitle {
                font-size: 12px;
                max-width: 100%;
            }
            .input-wrap .form-control,
            .input-wrap .form-select,
            .input-wrap .form-textarea {
                font-size: 15px; /* cegah iOS auto-zoom */
                padding: 10px 12px 10px 38px;
            }
            .btn-submit-laporan {
                height: 46px;
                font-size: 14px;
            }
        }

        @keyframes btnSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="laporan-wrapper">
    <div class="laporan-card">
        <!-- Header Laporan -->
        <div class="laporan-header">
            <div class="header-top-row">
                <div class="brand-pill">
                    <img src="{{ asset('logo.png') }}" alt="Logo PresensiKita">
                    PresensiKita
                </div>
                <button class="theme-toggle-btn header-theme-btn" type="button" onclick="toggleDarkMode()" aria-label="Ganti Tema" title="Ganti Mode Gelap / Terang">
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
            <h1 class="laporan-title">Form Laporan & Pengaduan</h1>
            <p class="laporan-subtitle">Sampaikan saran, kendala, atau laporan Anda langsung ke Administrator sekolah.</p>
        </div>

        <!-- Body Form Laporan -->
        <div class="laporan-body">
            @if(session('success_laporan'))
                <div class="alert alert-success" style="margin-bottom: 20px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>{{ session('success_laporan') }}</span>
                </div>
            @endif

            <form id="public-laporan-form" action="{{ route('laporan.public.store') }}" method="POST">
                @csrf

                {{-- 1. Pilih Role --}}
                <div class="form-group">
                    <label class="form-label" for="role_pelapor">Peran / Role Anda <span class="req">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <select name="role_pelapor" id="role_pelapor" class="form-select" required>
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
                    <label class="form-label" for="nama_pelapor">Nama Pelapor <span class="req">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input type="text" name="nama_pelapor" id="nama_pelapor" class="form-control" placeholder="Masukkan Nama Lengkap Anda" required>
                    </div>
                </div>

                {{-- 3. Judul Laporan --}}
                <div class="form-group">
                    <label class="form-label" for="judul">Judul / Subjek Laporan <span class="req">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </span>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Contoh: Kendala Absensi / Saran Fasilitas / Kendala Login" required>
                    </div>
                </div>

                {{-- 4. Isi Laporan --}}
                <div class="form-group">
                    <label class="form-label" for="isi_laporan">Rincian Laporan <span class="req">*</span></label>
                    <div class="input-wrap">
                        <span class="input-icon textarea-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </span>
                        <textarea name="isi_laporan" id="isi_laporan" class="form-textarea" placeholder="Jelaskan detail laporan atau masalah Anda secara jelas..." required></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit-laporan" id="btn-submit-laporan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    <span>Kirim Laporan</span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Halaman Login
            </a>
        </div>
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
            <svg style="animation:btnSpin 0.8s linear infinite" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
            <span>Mengirim...</span>
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
</body>
</html>
