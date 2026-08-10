<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PresensiKita SMKN 1 Boyolangu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
            overflow: hidden;
            color: #1e293b;
        }

        /* ─── Wrapper Utama Split Layout ─── */
        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ══════════════════════════════════
           PANEL KIRI — Info Web
        ══════════════════════════════════ */
        .left-panel {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 56px;
            overflow: hidden;
            background: linear-gradient(135deg, #0a1628 0%, #0d1f3c 50%, #0a192f 100%);
        }

        /* Grid pattern overlay */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        /* Glowing orbs di panel kiri */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(37,99,235,0.35) 0%, transparent 70%);
            top: -120px; left: -100px;
            animation: pulse 8s ease-in-out infinite;
        }
        .orb-2 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%);
            bottom: -80px; right: 60px;
            animation: pulse 10s ease-in-out infinite reverse;
        }
        .orb-3 {
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(14,165,233,0.2) 0%, transparent 70%);
            top: 50%; left: 55%;
            animation: pulse 12s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1) translate(0,0); opacity: 0.8; }
            50% { transform: scale(1.1) translate(-10px, -15px); opacity: 1; }
        }

        .left-content {
            position: relative;
            z-index: 1;
            max-width: 460px;
        }

        /* Badge di kiri */
        .left-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.3);
            color: #60a5fa;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        .left-badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #3b82f6;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Logo di kiri */
        .left-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 36px;
        }

        .left-logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37,99,235,0.45), 0 0 0 1px rgba(255,255,255,0.15) inset;
            flex-shrink: 0;
        }

        .left-logo-name {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        .left-logo-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Heading besar */
        .left-heading {
            font-size: 36px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }

        .left-heading .highlight {
            background: linear-gradient(135deg, #60a5fa, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .left-desc {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 380px;
        }

        /* Feature list */
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .feature-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon.blue   { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .feature-icon.purple { background: rgba(99,102,241,0.15); color: #a78bfa; }
        .feature-icon.cyan   { background: rgba(6,182,212,0.15);  color: #67e8f9; }

        .feature-text strong {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 3px;
        }

        .feature-text span {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Divider line di bawah */
        .left-divider {
            margin-top: 44px;
            padding-top: 28px;
            border-top: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .school-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* ══════════════════════════════════
           PANEL KANAN — Form Login
        ══════════════════════════════════ */
        .right-panel {
            width: 560px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border-left: 1px solid #e2e8f0;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle gradient di pojok kanan */
        .right-panel::before {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(219,234,254,0.6) 0%, transparent 70%);
            bottom: -120px; right: -100px;
            pointer-events: none;
        }

        .right-panel::after {
            content: '';
            position: absolute;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(224,231,255,0.5) 0%, transparent 70%);
            top: -80px; left: -80px;
            pointer-events: none;
        }

        .right-inner {
            width: 100%;
            position: relative;
            z-index: 1;
            animation: slideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(24px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Header form kanan */
        .form-header {
            margin-bottom: 32px;
        }

        .form-greeting {
            font-size: 26px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .form-subgreeting {
            font-size: 13.5px;
            color: #64748b;
            font-weight: 500;
        }

        /* Alert Error */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 8px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #475569;
            pointer-events: none;
            transition: color 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-input {
            width: 100%;
            padding: 13px 16px 13px 46px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            font-family: inherit;
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .input-wrapper:focus-within .input-icon {
            color: #60a5fa;
        }

        .form-input:focus {
            border-color: #3b82f6;
            background: rgba(59,130,246,0.05);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
            background: rgba(239,68,68,0.06);
        }

        .show-password-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .show-password-toggle:hover {
            color: #475569;
            background: rgba(0,0,0,0.04);
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            cursor: pointer;
            font-family: inherit;
            margin-top: 8px;
            box-shadow: 0 8px 24px -4px rgba(37,99,235,0.5), 0 0 0 1px rgba(255,255,255,0.15) inset;
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
            box-shadow: 0 14px 32px -4px rgba(37,99,235,0.65), 0 0 0 1px rgba(255,255,255,0.25) inset;
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 14px -2px rgba(37,99,235,0.4);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; border-left: none; padding: 40px 32px; }
        }

        @media (max-width: 480px) {
            .right-panel { padding: 36px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">

        <!-- ═══════════════════════ PANEL KIRI ═══════════════════════ -->
        <div class="left-panel">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>

            <div class="left-content">
                <!-- Badge -->


                <!-- Logo -->
                <div class="left-logo">
                    <div class="left-logo-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <div class="left-logo-name">PresensiKita</div>
                        <div class="left-logo-sub">Platform Presensi Digital</div>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="left-heading">
                    Kelola Presensi Murid<br>
                    <span class="highlight">Lebih Mudah & Cepat</span>
                </h1>

                <p class="left-desc">
                    Sistem manajemen presensi dan jurnal mengajar guru berbasis digital untuk SMKN 1 Boyolangu. Tercatat rapi, real-time, dan mudah diakses kapan saja.
                </p>

                <!-- Fitur -->
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon blue">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 11 12 14 22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <strong>Presensi Real-Time</strong>
                            <span>Catat kehadiran guru setiap hari secara digital & akurat.</span>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon purple">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <strong>Jurnal Mengajar Digital</strong>
                            <span>Dokumentasi kegiatan belajar mengajar per pertemuan.</span>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon cyan">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <strong>Rekap & Laporan Otomatis</strong>
                            <span>Pantau statistik presensi guru dengan laporan yang mudah dibaca.</span>
                        </div>
                    </div>
                </div>

                <!-- Sekolah -->
                <div class="left-divider">
                    <div class="school-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        SMKN 1 Boyolangu, Tulungagung
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════ PANEL KANAN ═══════════════════════ -->
        <div class="right-panel">
            <div class="right-inner">

                <!-- Header Form -->
                <div class="form-header">
                    <h2 class="form-greeting">Selamat Datang</h2>
                    <p class="form-subgreeting">Masuk ke akun PresensiKita Anda</p>
                </div>

                <!-- Error Banner -->
                @if($errors->any())
                <div class="alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>{{ $errors->first() }}</div>
                </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login.post') }}" id="login-form">
                    @csrf

                    <!-- Username -->
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                placeholder="Masukkan username Anda"
                                value="{{ old('username') }}"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Masukkan password Anda"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="show-password-toggle" onclick="togglePassword()" title="Tampilkan/Sembunyikan Password">
                                <svg id="eye-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="btn-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Masuk ke Akun</span>
                    </button>
                </form>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.style.opacity = '0.75';
            btn.style.pointerEvents = 'none';
            btn.querySelector('span').textContent = 'Memproses...';
        });
    </script>
</body>
</html>
