<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PresensiKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f3f6fa;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ─── Main Viewport Grid ─── */
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            position: relative;
            width: 100%;
        }

        /* ══════════════════════════════════
           SISI KIRI: HERO & BRANDING
        ══════════════════════════════════ */
        .left-hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            padding: 48px 60px 0 60px;
            min-height: 100vh;
            z-index: 1;
        }

        /* Brand Logo Top */
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.25);
            flex-shrink: 0;
        }

        .brand-text-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .brand-text-sub {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            margin-top: 2px;
        }

        /* Content Area */
        .hero-main-content {
            margin-top: 40px;
            margin-bottom: 40px;
            max-width: 680px;
        }

        .hero-heading {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.03em;
            margin-bottom: 12px;
        }

        .hero-subheading {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 36px;
            max-width: 460px;
        }

        /* Features & Laptop Layout Container */
        .hero-body-grid {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            position: relative;
        }

        /* Features List */
        .features-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1;
            max-width: 360px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e3a8a;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            flex-shrink: 0;
        }

        .feature-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .feature-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.45;
        }

        /* Laptop Illustration */
        .illustration-wrapper {
            width: 280px;
            flex-shrink: 0;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-bottom: -10px;
        }

        .laptop-svg {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 12px 24px rgba(15, 23, 42, 0.12));
        }

        /* ─── Wave Section Bottom Left ─── */
        .left-bottom-wave {
            position: relative;
            background: #112846;
            margin-left: -60px;
            margin-right: -60px;
            padding: 32px 60px 24px;
            color: #ffffff;
            z-index: 2;
        }

        .left-bottom-wave::before {
            content: '';
            position: absolute;
            top: -35px;
            left: 0;
            right: 0;
            height: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0 C300,90 600,90 1200,20 L1200,120 L0,120 Z' fill='%23112846'%3E%3C/path%3E%3C/svg%3E");
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

        .quote-container {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px;
            max-width: 520px;
        }

        .quote-mark {
            font-size: 38px;
            line-height: 1;
            font-family: Georgia, serif;
            color: rgba(255,255,255,0.7);
            font-weight: bold;
        }

        .quote-text-primary {
            font-size: 13.5px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 3px;
        }

        .quote-text-secondary {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }

        .copyright-notice {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
        }


        /* ══════════════════════════════════
           SISI KANAN: FLOATING LOGIN CARD
        ══════════════════════════════════ */
        .right-card-section {
            width: 480px;
            padding: 40px 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(0,0,0,0.02);
            padding: 40px 36px;
            width: 100%;
            border: 1px solid #edf2f7;
        }

        /* Circle Lock Badge Header */
        .badge-lock-header {
            width: 72px;
            height: 72px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #1e3a8a;
        }

        .card-title-group {
            text-align: center;
            margin-bottom: 28px;
        }

        .card-title-group h2 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .card-title-group p {
            font-size: 13px;
            color: #64748b;
        }

        /* Alert Error */
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-relative {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 13.5px;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control-input::placeholder {
            color: #94a3b8;
        }

        .input-relative:focus-within .input-icon-left {
            color: #1e3a8a;
        }

        .form-control-input:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3.5px rgba(30, 58, 138, 0.12);
        }

        .btn-toggle-eye {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.2s;
        }

        .btn-toggle-eye:hover {
            color: #475569;
        }

        /* Form Controls Extra (Remember & Forgot) */
        .form-extra-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: #1e3a8a;
            border-radius: 4px;
            cursor: pointer;
        }

        .forgot-password-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-password-link:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-login-submit {
            width: 100%;
            padding: 13px;
            background: #112846;
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: inherit;
            box-shadow: 0 8px 20px rgba(17, 40, 70, 0.25);
            transition: all 0.2s;
        }

        .btn-login-submit:hover {
            background: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(17, 40, 70, 0.35);
        }

        .btn-login-submit:active {
            transform: translateY(0);
        }

        /* Help Footer Link */
        .card-footer-help {
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
            margin-top: 24px;
        }

        .card-footer-help a {
            color: #112846;
            font-weight: 700;
            text-decoration: none;
        }

        .card-footer-help a:hover {
            text-decoration: underline;
        }


        /* ══════════════════════════════════
           RESPONSIVE DESIGN BREAKPOINTS
        ══════════════════════════════════ */
        @media (max-width: 1120px) {
            .illustration-wrapper { display: none; }
            .hero-body-grid { flex-direction: column; align-items: flex-start; }
            .features-list { max-width: 100%; }
        }

        @media (max-width: 960px) {
            .page-wrapper {
                flex-direction: column;
                align-items: center;
            }
            .left-hero {
                width: 100%;
                min-height: auto;
                padding: 36px 24px 0 24px;
            }
            .left-bottom-wave {
                margin-left: -24px;
                margin-right: -24px;
                padding: 32px 24px 24px;
            }
            .right-card-section {
                width: 100%;
                max-width: 480px;
                padding: 32px 20px 48px;
            }
            .hero-heading { font-size: 26px; }
        }

        @media (max-width: 480px) {
            .login-card { padding: 28px 20px; }
            .left-hero { padding: 24px 16px 0 16px; }
            .left-bottom-wave { margin-left: -16px; margin-right: -16px; padding: 28px 16px 20px; }
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <!-- ════════════════════ SISI KIRI: HERO & FEATURES ════════════════════ -->
        <div class="left-hero">

            <!-- Brand Logo Top -->
            <div class="brand-logo">
                <div class="brand-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Perisai / Shield Outer -->
                        <path d="M12 2L3 6v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V6l-9-4z" fill="#0f172a" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <!-- Bintang Atas -->
                        <polygon points="12 5.5 12.8 7.2 14.7 7.5 13.3 8.8 13.7 10.7 12 9.8 10.3 10.7 10.7 8.8 9.3 7.5 11.2 7.2" fill="#ffffff"/>
                        <!-- Buku Terbuka Tengah -->
                        <path d="M6.5 15.5c1.5-.8 3.5-.8 5.5 0 2-.8 4-.8 5.5 0" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M6.5 12.5c1.5-.8 3.5-.8 5.5 0 2-.8 4-.8 5.5 0" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="12" y1="12.5" x2="12" y2="16" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                        <!-- Wreath / Padi Samping -->
                        <path d="M5 9c-.5 2 0 4 1 5M19 9c.5 2 0 4-1 5" stroke="rgba(255,255,255,0.6)" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <h1 class="brand-text-title">PresensiKita</h1>
                    <div class="brand-text-sub">Sistem Informasi Kehadiran Siswa</div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="hero-main-content">
                <h2 class="hero-heading">
                    Kelola Kehadiran Siswa<br>Lebih Mudah & Terorganisir
                </h2>
                <p class="hero-subheading">
                    Catat, pantau, dan rekap kehadiran siswa setiap hari secara digital, akurat, dan efisien.
                </p>

                <div class="hero-body-grid">
                    <!-- Feature Bullet Points -->
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                    <polyline points="9 16 11 18 15 14"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="feature-title">Pencatatan Cepat</h4>
                                <p class="feature-desc">Input kehadiran harian siswa hanya dalam beberapa klik.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"/>
                                    <line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="feature-title">Rekap Otomatis</h4>
                                <p class="feature-desc">Dapatkan laporan dan grafik kehadiran secara otomatis.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <polyline points="9 12 11 14 15 10"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="feature-title">Aman & Terpercaya</h4>
                                <p class="feature-desc">Data tersimpan aman dan dapat diakses sesuai hak akses pengguna.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Laptop Mockup & Illustration -->
                    <div class="illustration-wrapper">
                        <svg class="laptop-svg" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Laptop Screen Frame -->
                            <rect x="35" y="15" width="210" height="130" rx="8" fill="#1e293b"/>
                            <!-- Inner Display Screen -->
                            <rect x="41" y="21" width="198" height="118" rx="4" fill="#ffffff"/>

                            <!-- UI Header inside Laptop -->
                            <rect x="47" y="27" width="186" height="18" rx="3" fill="#f1f5f9"/>
                            <circle cx="56" cy="36" r="3" fill="#ef4444"/>
                            <circle cx="66" cy="36" r="3" fill="#f59e0b"/>
                            <circle cx="76" cy="36" r="3" fill="#10b981"/>

                            <!-- UI Sidebar inside Laptop -->
                            <rect x="47" y="49" width="36" height="84" rx="3" fill="#0f172a"/>
                            <rect x="53" y="57" width="24" height="4" rx="2" fill="#38bdf8"/>
                            <rect x="53" y="67" width="24" height="3" rx="1.5" fill="#64748b"/>
                            <rect x="53" y="75" width="24" height="3" rx="1.5" fill="#64748b"/>

                            <!-- UI Stats Cards inside Laptop -->
                            <rect x="89" y="51" width="42" height="26" rx="3" fill="#dcfce7"/>
                            <rect x="135" y="51" width="42" height="26" rx="3" fill="#fef3c7"/>
                            <rect x="181" y="51" width="46" height="26" rx="3" fill="#fee2e2"/>

                            <!-- UI Charts inside Laptop -->
                            <path d="M89 110 Q 120 85, 150 98 T 220 80" stroke="#2563eb" stroke-width="2.5" fill="none"/>
                            <circle cx="185" cy="112" r="14" fill="#3b82f6" opacity="0.85"/>
                            <circle cx="185" cy="112" r="7" fill="#ffffff"/>

                            <!-- Laptop Stand Base -->
                            <path d="M15 145 L265 145 L252 153 L28 153 Z" fill="#cbd5e1"/>
                            <rect x="115" y="145" width="50" height="3" rx="1.5" fill="#94a3b8"/>

                            <!-- Potted Plant Left -->
                            <path d="M22 128 L30 128 L28 145 L24 145 Z" fill="#d97706"/>
                            <path d="M18 120 Q 26 110 26 128" stroke="#16a34a" stroke-width="3.5" stroke-linecap="round"/>
                            <path d="M34 116 Q 26 110 26 128" stroke="#22c55e" stroke-width="3.5" stroke-linecap="round"/>

                            <!-- Stack of Books Right -->
                            <rect x="220" y="134" width="48" height="9" rx="1.5" fill="#1d4ed8"/>
                            <rect x="222" y="125" width="46" height="9" rx="1.5" fill="#2563eb"/>
                            <rect x="225" y="116" width="42" height="9" rx="1.5" fill="#3b82f6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Wave Section Bottom Left -->
            <div class="left-bottom-wave">
                <div class="quote-container">
                    <div class="quote-mark">“</div>
                    <div>
                        <div class="quote-text-primary">Disiplin hari ini, sukses di masa depan.</div>
                        <div class="quote-text-secondary">Mari bersama wujudkan sekolah yang tertib dan berprestasi.</div>
                    </div>
                </div>
                <div class="copyright-notice">
                    © {{ date('Y') }} PresensiKita. All rights reserved.
                </div>
            </div>

        </div>


        <!-- ════════════════════ SISI KANAN: FLOATING LOGIN CARD ════════════════════ -->
        <div class="right-card-section">
            <div class="login-card">

                <!-- Lock Badge Header -->
                <div class="badge-lock-header">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>

                <!-- Title & Subtitle -->
                <div class="card-title-group">
                    <h2>Selamat Datang!</h2>
                    <p>Silakan masuk untuk melanjutkan ke sistem.</p>
                </div>

                <!-- Error Alert -->
                @if($errors->any())
                <div class="alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>{{ $errors->first() }}</div>
                </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login.post') }}" id="login-form">
                    @csrf

                    <!-- Field Username -->
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-relative">
                            <span class="input-icon-left">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control-input"
                                placeholder="Masukkan username"
                                value="{{ old('username') }}"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Field Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-relative">
                            <span class="input-icon-left">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control-input"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordView()" title="Tampilkan/Sembunyikan Password">
                                <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Options (Remember Me & Forgot Password) -->
                    <div class="form-extra-row">
                        <label class="remember-checkbox">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk me-reset password Anda.')" class="forgot-password-link">Lupa password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login-submit" id="btn-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        <span>Masuk</span>
                    </button>
                </form>

                <!-- Help Footer Text -->
                <div class="card-footer-help">
                    Belum punya akun? <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk membuat akun baru.')">Hubungi administrator sekolah.</a>
                </div>

            </div>
        </div>

    </div>

    <!-- ── SCRIPT PASS & FORM SUBMIT ── -->
    <script>
        function togglePasswordView() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                pwd.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
            btn.querySelector('span').textContent = 'Memproses...';
        });
    </script>
</body>
</html>
