<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PresensiKita</title>
    <meta name="description" content="Masuk ke sistem PresensiKita untuk mengelola kehadiran siswa secara digital.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0d1b3e;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        /* ───── LAYOUT ───── */
        .login-container {
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: 2fr 1fr;
            overflow: hidden;
        }

        /* ───── LEFT HERO ───── */
        .left-hero {
            background: linear-gradient(155deg, #0a1628 0%, #102150 40%, #0f2a60 70%, #0a1e4f 100%);
            padding: 5% 6%;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* Animated gradient orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            animation: floatOrb 8s ease-in-out infinite;
        }
        .orb-1 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(59,130,246,0.25) 0%, transparent 70%);
            top: -80px; right: -60px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
            bottom: 60px; left: -40px;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(14,165,233,0.18) 0%, transparent 70%);
            top: 45%; right: 10%;
            animation-delay: -2s;
        }
        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, -20px); }
        }

        /* Dot grid */
        .dot-grid {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* Wave bottom */
        .wave-bottom {
            position: absolute; bottom: 0; left: 0; right: 0;
            pointer-events: none; z-index: 0;
        }

        .left-content { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

        /* Brand header */
        .brand-header { display: flex; align-items: center; gap: 13px; margin-bottom: auto; }
        .brand-icon-box {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, rgba(59,130,246,0.3), rgba(99,102,241,0.2));
            border: 1.5px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(59,130,246,0.2);
        }
        .brand-title { font-size: clamp(17px,2vw,22px); font-weight: 800; color: #fff; letter-spacing: -0.01em; }
        .brand-sub { font-size: clamp(10px,1.1vw,12.5px); color: rgba(255,255,255,0.5); margin-top: 2px; }

        /* Hero body */
        .hero-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4% 0 6%;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(59,130,246,0.15);
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 50px;
            padding: 5px 14px;
            font-size: 11.5px;
            color: #93c5fd;
            font-weight: 600;
            margin-bottom: 18px;
            width: fit-content;
        }
        .hero-title {
            font-size: clamp(22px,2.8vw,36px);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin-bottom: 14px;
            color: #fff;
        }
        .hero-title .accent {
            background: linear-gradient(90deg, #60a5fa, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: clamp(11px,1.15vw,13.5px);
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            margin-bottom: 7%;
            max-width: 90%;
        }

        /* Feature list */
        .feature-list { display: flex; flex-direction: column; gap: clamp(12px,1.6vw,20px); }
        .feature-item { display: flex; align-items: flex-start; gap: 14px; }
        .feature-icon-badge {
            width: clamp(32px,3.2vw,40px); height: clamp(32px,3.2vw,40px);
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            color: #7ec8f8; flex-shrink: 0;
            transition: background 0.2s;
        }
        .feature-title { font-size: clamp(12px,1.2vw,14px); font-weight: 700; color: #f0f6ff; margin-bottom: 2px; }
        .feature-sub { font-size: clamp(10px,1vw,12.5px); color: rgba(255,255,255,0.4); line-height: 1.5; }

        /* Quote */
        .hero-quote {
            background: rgba(255,255,255,0.05);
            border-left: 3px solid rgba(99,102,241,0.6);
            border-radius: 8px;
            padding: 13px 16px;
            margin-top: 5%;
        }
        .hero-quote p { font-size: clamp(10.5px,1.05vw,13px); color: rgba(255,255,255,0.65); line-height: 1.6; font-style: italic; }

        .left-footer { font-size: clamp(9.5px,0.95vw,12px); color: rgba(255,255,255,0.28); margin-top: 4%; }

        /* ───── RIGHT FORM ───── */
        .right-form {
            background: #f4f7fc;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4% 6%;
            overflow-y: auto;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 20px;
            padding: 36px 36px 32px;
            box-shadow: 0 4px 32px rgba(15,40,100,0.09), 0 1px 4px rgba(15,40,100,0.05);
            border: 1px solid #e2e8f4;
        }

        /* Lock icon */
        .lock-icon-circle {
            width: clamp(52px,6vw,66px); height: clamp(52px,6vw,66px);
            background: linear-gradient(135deg, #eef3fb, #e2ecfb);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            color: #1e3a6e;
            margin: 0 auto clamp(12px,1.6vw,18px);
            box-shadow: 0 4px 12px rgba(30,58,110,0.12);
        }

        .form-header { text-align: center; margin-bottom: 24px; }
        .form-header h2 { font-size: clamp(18px,2vw,23px); font-weight: 800; color: #0f1f3d; margin-bottom: 4px; }
        .form-header p { font-size: clamp(11px,1.1vw,13px); color: #6b7a99; }

        /* ───── ROLE SELECTOR (SINGLE) ───── */
        .role-selector {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 14px;
        }
        .role-arrow {
            width: 42px; height: 42px;
            border-radius: 12px;
            border: 1.5px solid #d0d9ea;
            background: #fff;
            color: #4d6080;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-family: inherit;
            transition: all 0.2s;
        }
        .role-arrow:hover {
            border-color: #2563c0;
            color: #2563c0;
            background: #f0f5ff;
        }
        .role-arrow:active { transform: scale(0.94); }

        .role-display {
            position: relative;
            width: 100%;
            max-width: 200px;
            height: 108px;
        }
        .role-card {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #f0f4fb;
            border: 1.5px solid #e2e8f4;
            border-radius: 16px;
            padding: 12px;
            opacity: 0;
            transform: scale(0.88);
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .role-card.active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
            border-color: transparent;
        }
        .role-card-icon {
            width: 46px; height: 46px;
            border-radius: 13px;
            background: rgba(0,0,0,0.04);
            color: #9aaac4;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.25s;
        }
        .role-card.active .role-card-icon-admin { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: #fff; }
        .role-card.active .role-card-icon-guru  { background: linear-gradient(135deg, #0e7490, #06b6d4); color: #fff; }
        .role-card.active .role-card-icon-wali  { background: linear-gradient(135deg, #7c3aed, #a78bfa); color: #fff; }
        .role-card-name { font-size: 14px; font-weight: 700; color: #1a2a45; }

        .role-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 11px;
            color: #9aaac4;
            margin-bottom: 8px;
        }
        .role-hint .key {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 4px;
            border: 1px solid #d0d9ea;
            border-radius: 6px;
            background: #fff;
            color: #4d6080;
            font-weight: 700;
            font-size: 11px;
        }

        .role-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 22px;
        }
        .role-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #c7d2e6;
            cursor: pointer;
            border: none;
            padding: 0;
            transition: all 0.2s;
        }
        .role-dot.active {
            width: 22px;
            border-radius: 50px;
            background: #2563c0;
        }

        /* ───── FORM PANELS ───── */
        .form-panel { display: none; animation: panelIn 0.3s ease forwards; }
        .form-panel.active { display: block; }
        @keyframes panelIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Alert */
        .alert-danger {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 10px; padding: 10px 14px;
            margin-bottom: 16px; font-size: 13px; color: #b91c1c;
            display: flex; align-items: center; gap: 8px;
        }

        /* Form elements */
        .form-group { margin-bottom: clamp(12px,1.5vw,18px); }
        .form-label { display: block; font-size: clamp(11px,1.1vw,13px); font-weight: 700; color: #2d3a55; margin-bottom: 6px; }
        .input-relative { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 13px; color: #9aaac4; display: flex; align-items: center; pointer-events: none; }
        .form-input {
            width: 100%;
            height: clamp(42px,4.5vw,50px);
            padding: 10px 14px 10px 42px;
            border: 1.5px solid #d0d9ea;
            border-radius: 11px;
            font-size: clamp(12px,1.2vw,14px);
            color: #1a2a45;
            background: #fafbfd;
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input::placeholder { color: #b0bdd4; }
        .form-input:focus { border-color: #2563c0; box-shadow: 0 0 0 3px rgba(37,99,192,0.12); background: #fff; }
        .btn-eye-toggle { position: absolute; right: 12px; background: none; border: none; color: #9aaac4; cursor: pointer; padding: 4px; display: flex; align-items: center; }
        .btn-eye-toggle:hover { color: #4a6fa8; }

        /* Remember + forgot */
        .form-options { display: flex; align-items: center; justify-content: space-between; font-size: clamp(11px,1.1vw,13px); margin-bottom: clamp(14px,1.8vw,20px); }
        .remember-label { display: flex; align-items: center; gap: 7px; color: #4d6080; cursor: pointer; user-select: none; }
        .remember-label input[type="checkbox"] { width: 15px; height: 15px; accent-color: #1e3a8a; cursor: pointer; }
        .forgot-link { color: #2563c0; text-decoration: none; font-weight: 600; font-size: 12px; }
        .forgot-link:hover { text-decoration: underline; }

        /* Submit buttons */
        .btn-submit {
            width: 100%;
            height: clamp(42px,4.5vw,50px);
            border: none; border-radius: 11px;
            color: #fff;
            font-size: clamp(13px,1.3vw,15px);
            font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit;
            transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
        }
        .btn-submit:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .btn-submit:active { transform: scale(0.985) translateY(0); }

        .btn-admin { background: linear-gradient(135deg, #1d4ed8, #2563eb); box-shadow: 0 4px 16px rgba(29,78,216,0.35); }
        .btn-admin:hover { box-shadow: 0 6px 22px rgba(29,78,216,0.45); }

        .btn-guru  { background: linear-gradient(135deg, #0e7490, #0891b2); box-shadow: 0 4px 16px rgba(14,116,144,0.35); }
        .btn-guru:hover { box-shadow: 0 6px 22px rgba(14,116,144,0.45); }

        .btn-wali  { background: linear-gradient(135deg, #7c3aed, #8b5cf6); box-shadow: 0 4px 16px rgba(124,58,237,0.35); }
        .btn-wali:hover { box-shadow: 0 6px 22px rgba(124,58,237,0.45); }

        /* NISN helper */
        .nisn-helper {
            background: linear-gradient(135deg, #faf5ff, #f0f4ff);
            border: 1px solid #e9d5ff;
            border-radius: 10px;
            padding: 11px 14px;
            margin-bottom: 14px;
            font-size: 12px;
            color: #6d28d9;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.5;
        }
        .nisn-helper svg { flex-shrink: 0; margin-top: 1px; }

        /* Footer */
        .form-footer-text { text-align: center; font-size: clamp(10.5px,1.05vw,12.5px); color: #6b7a99; margin-top: clamp(14px,1.8vw,20px); }
        .form-footer-text a { color: #2563c0; font-weight: 600; text-decoration: none; }
        .form-footer-text a:hover { text-decoration: underline; }

        /* Divider */
        .divider { display: flex; align-items: center; gap: 10px; margin: 16px 0; }
        .divider-line { flex: 1; height: 1px; background: #e2e8f4; }
        .divider-text { font-size: 11px; color: #a0aec0; font-weight: 500; white-space: nowrap; }

        /* ───── RESPONSIVE ───── */
        @media (max-width: 820px) {
            body { overflow: auto; }
            .login-container { grid-template-columns: 1fr; height: auto; min-height: 100vh; }
            .left-hero { padding: 28px 24px; min-height: 220px; }
            .right-form { padding: 28px 20px; min-height: auto; }
            .form-card { padding: 28px 24px 24px; }
        }
        @media (max-height: 600px) { .hero-quote { display: none; } }
    </style>
</head>
<body>
<div class="login-container">

    <!-- ─── LEFT HERO ─── -->
    <div class="left-hero">
        <div class="dot-grid"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <svg class="wave-bottom" viewBox="0 0 600 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,80 C100,20 220,120 360,60 C480,10 540,90 600,60 L600,120 L0,120 Z" fill="rgba(99,102,241,0.08)"/>
            <path d="M0,100 C80,50 200,120 340,90 C460,65 540,110 600,90 L600,120 L0,120 Z" fill="rgba(59,130,246,0.06)"/>
        </svg>

        <div class="left-content">
            <div class="brand-header">
                <div class="brand-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l8 3v6c0 5-4 9-8 10C8 20 4 16 4 11V5l8-3z"/>
                        <path d="M9 12h6M12 9v6"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-title">PresensiKita</div>
                    <div class="brand-sub">Sistem Informasi Kehadiran Siswa</div>
                </div>
            </div>

            <div class="hero-body">
                <div class="hero-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="5"/></svg>
                    Platform Digital Sekolah
                </div>
                <h1 class="hero-title">Kelola Kehadiran Siswa<br>Lebih <span class="accent">Mudah &amp; Terorganisir</span></h1>
                <p class="hero-desc">Catat, pantau, dan rekap kehadiran siswa setiap hari secara digital, akurat, dan efisien. Tersedia untuk Admin, Guru, dan Wali Murid.</p>
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
                        </div>
                        <div><div class="feature-title">Pencatatan Cepat</div><div class="feature-sub">Input kehadiran harian siswa hanya dalam beberapa klik.</div></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        </div>
                        <div><div class="feature-title">Rekap Otomatis</div><div class="feature-sub">Laporan dan grafik kehadiran secara otomatis dan real-time.</div></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div><div class="feature-title">Multi-Role Akses</div><div class="feature-sub">Admin, Guru, dan Wali Murid memiliki akses dan fitur tersendiri.</div></div>
                    </div>
                </div>
                <div class="hero-quote">
                    <p>&#10077; Disiplin hari ini, sukses di masa depan.<br>Mari bersama wujudkan sekolah yang tertib dan berprestasi. &#10078;</p>
                </div>
            </div>

            <div class="left-footer">&copy; {{ date('Y') }} PresensiKita. All rights reserved.</div>
        </div>
    </div>

    <!-- ─── RIGHT FORM ─── -->
    <div class="right-form">
        <div class="form-card">

            <!-- Lock icon -->
            <div class="lock-icon-circle">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>

            <!-- Header -->
            <div class="form-header">
                <h2>Selamat Datang!</h2>
                <p>Pilih peran Anda untuk masuk ke sistem.</p>
            </div>

            <!-- ─── ROLE SELECTOR (SINGLE) ─── -->
            <div class="role-selector">
                <button type="button" class="role-arrow" id="arrow-prev" onclick="prevRole()" aria-label="Role sebelumnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>

                <div class="role-display" role="group" aria-label="Pilih Role Login">
                    <div class="role-card active" id="card-admin">
                        <div class="role-card-icon role-card-icon-admin">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l8 3v6c0 5-4 9-8 10C8 20 4 16 4 11V5l8-3z"/>
                            </svg>
                        </div>
                        <div class="role-card-name">Admin</div>
                    </div>
                    <div class="role-card" id="card-guru">
                        <div class="role-card-icon role-card-icon-guru">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                        </div>
                        <div class="role-card-name">Guru</div>
                    </div>
                    <div class="role-card" id="card-wali">
                        <div class="role-card-icon role-card-icon-wali">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="role-card-name">Wali Murid</div>
                    </div>
                </div>

                <button type="button" class="role-arrow" id="arrow-next" onclick="nextRole()" aria-label="Role berikutnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            <div class="role-dots">
                <button type="button" class="role-dot active" data-role="admin" aria-label="Pilih Admin"></button>
                <button type="button" class="role-dot" data-role="guru" aria-label="Pilih Guru"></button>
                <button type="button" class="role-dot" data-role="wali" aria-label="Pilih Wali Murid"></button>
            </div>

            <!-- ─── ERROR MESSAGES ─── -->
            @if($errors->any())
            <div class="alert-danger" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <!-- ─── PANEL ADMIN ─── -->
            <div class="form-panel active" id="panel-admin" role="tabpanel" aria-labelledby="card-admin">
                <form method="POST" action="{{ route('login.post') }}" id="form-admin">
                    @csrf
                    <input type="hidden" name="role" value="admin">
                    <div class="form-group">
                        <label class="form-label" for="admin-username">Username Admin</label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" id="admin-username" name="username" class="form-input" placeholder="Masukkan username admin" value="{{ old('username') }}" autocomplete="username" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="admin-password">Password</label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" id="admin-password" name="password" class="form-input" placeholder="Masukkan password" autocomplete="current-password" required>
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('admin-password','eye-admin')" title="Tampilkan/Sembunyikan">
                                <svg id="eye-admin" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="remember-label"><input type="checkbox" name="remember" id="remember-admin"><span>Ingat saya</span></label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi superadmin untuk me-reset password.')" class="forgot-link">Lupa password?</a>
                    </div>
                    <button type="submit" class="btn-submit btn-admin" id="btn-admin">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Masuk sebagai Admin</span>
                    </button>
                </form>
            </div>

            <!-- ─── PANEL GURU ─── -->
            <div class="form-panel" id="panel-guru" role="tabpanel" aria-labelledby="card-guru">
                <form method="POST" action="{{ route('login.post') }}" id="form-guru">
                    @csrf
                    <input type="hidden" name="role" value="guru">
                    <div class="form-group">
                        <label class="form-label" for="guru-username">Username Guru</label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" id="guru-username" name="username" class="form-input" placeholder="Masukkan username guru" value="{{ old('username') }}" autocomplete="username" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="guru-password">Password</label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" id="guru-password" name="password" class="form-input" placeholder="Masukkan password" autocomplete="current-password" required>
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('guru-password','eye-guru')" title="Tampilkan/Sembunyikan">
                                <svg id="eye-guru" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="remember-label"><input type="checkbox" name="remember" id="remember-guru"><span>Ingat saya</span></label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk me-reset password Anda.')" class="forgot-link">Lupa password?</a>
                    </div>
                    <button type="submit" class="btn-submit btn-guru" id="btn-guru">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Masuk sebagai Guru</span>
                    </button>
                </form>
            </div>

            <!-- ─── PANEL WALI MURID ─── -->
            <div class="form-panel" id="panel-wali" role="tabpanel" aria-labelledby="card-wali">
                <div class="nisn-helper">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>Masukkan <strong>NISN anak/murid</strong> (10 digit) untuk mengakses portal kehadiran. NISN dapat dilihat di kartu pelajar atau buku raport.</span>
                </div>
                <form method="POST" action="{{ route('login.orangtua.post') }}" id="form-wali">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="nisn">NISN Siswa (10 Digit)</label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/><line x1="7" y1="16" x2="10" y2="16"/></svg>
                            </span>
                            <input type="text" id="nisn" name="nisn" class="form-input" placeholder="Contoh: 0099166409" value="{{ old('nisn') }}" maxlength="10" inputmode="numeric" pattern="[0-9]*" required autofocus>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit btn-wali" id="btn-wali">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <span>Masuk ke Portal Wali Murid</span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="form-footer-text">
                Butuh bantuan? <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk mendapatkan bantuan.')">Hubungi administrator.</a>
            </div>

        </div>
    </div>
</div>

<script>
    // ── Role switching (single display)
    const roles = ['admin', 'guru', 'wali'];
    let currentRoleIndex = 0;

    function switchRole(role) {
        const idx = roles.indexOf(role);
        if (idx === -1) return;
        currentRoleIndex = idx;

        roles.forEach(r => {
            const card = document.getElementById('card-' + r);
            const panel = document.getElementById('panel-' + r);
            const isActive = (r === role);
            card.classList.toggle('active', isActive);
            if (isActive) {
                panel.classList.add('active');
                // Trigger animation restart
                panel.style.animation = 'none';
                panel.offsetHeight; // reflow
                panel.style.animation = '';
            } else {
                panel.classList.remove('active');
            }
        });

        document.querySelectorAll('.role-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === idx);
        });

        // Focus first input in the active panel
        setTimeout(() => {
            const activePanel = document.getElementById('panel-' + role);
            const firstInput = activePanel.querySelector('input');
            if (firstInput) firstInput.focus();
        }, 50);
    }

    function nextRole() {
        switchRole(roles[(currentRoleIndex + 1) % roles.length]);
    }

    function prevRole() {
        switchRole(roles[(currentRoleIndex - 1 + roles.length) % roles.length]);
    }

    // ── Keyboard navigation with arrow keys
    document.addEventListener('keydown', function(e) {
        const tag = e.target.tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;
        if (e.key === 'ArrowRight') nextRole();
        else if (e.key === 'ArrowLeft') prevRole();
    });

    // ── Clickable dots
    document.querySelectorAll('.role-dot').forEach(dot => {
        dot.addEventListener('click', () => switchRole(dot.dataset.role));
    });

    // ── Auto-activate tab based on error (if redirected back with error)
    @if($errors->any())
        const oldRole = '{{ old("role", "admin") }}';
        if (oldRole === 'guru') switchRole('guru');
        else if (oldRole === 'wali' || {{ $errors->has('nisn') ? 'true' : 'false' }}) switchRole('wali');
    @endif

    // ── Toggle password visibility
    function togglePassword(inputId, iconId) {
        const pwd  = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            pwd.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    // ── Loading state on submit
    function setLoading(btnId, text) {
        const btn = document.getElementById(btnId);
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
        btn.querySelector('span').textContent = text;
    }

    document.getElementById('form-admin').addEventListener('submit', () => setLoading('btn-admin', 'Memproses...'));
    document.getElementById('form-guru').addEventListener('submit',  () => setLoading('btn-guru',  'Memproses...'));
    document.getElementById('form-wali').addEventListener('submit',  () => setLoading('btn-wali',  'Memeriksa NISN...'));

    // ── NISN: only allow digits
    document.getElementById('nisn').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });
</script>
</body>
</html>