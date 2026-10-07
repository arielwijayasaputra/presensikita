<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login - PresensiKita</title>
    <meta name="description" content="Masuk ke sistem PresensiKita untuk mengelola kehadiran siswa secara digital.">
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
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
            color: inherit;
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
            background: rgba(255,255,255,0.12);
            border: 1.5px solid rgba(255,255,255,0.35);
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
            max-width: 460px;
            background: #fff;
            border-radius: 12px;
            padding: 36px 36px 32px;
            box-shadow: 0 4px 32px rgba(15,40,100,0.09), 0 1px 4px rgba(15,40,100,0.05);
            border: 1px solid #e2e8f0;
        }

        /* Lock icon */
        .lock-icon-circle {
            width: clamp(52px,6vw,66px); height: clamp(52px,6vw,66px);
            background: linear-gradient(135deg, #eef3fb, #e2ecfb);
            border-radius: 12px;
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
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: #4d6080;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-family: inherit;
            transition: all 0.2s;
        }
        .role-arrow:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
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
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
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
        .role-card.active .role-card-icon-admin     { background: linear-gradient(135deg, #1d4ed8, #2563eb); color: #fff; }
        .role-card.active .role-card-icon-guru      { background: linear-gradient(135deg, #0e7490, #0891b2); color: #fff; }
        .role-card.active .role-card-icon-gurupiket { background: linear-gradient(135deg, #0891b2, #06b6d4); color: #fff; }
        .role-card.active .role-card-icon-walikelas { background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
        .role-card.active .role-card-icon-satpam    { background: linear-gradient(135deg, #334155, #64748b); color: #fff; }
        .role-card.active .role-card-icon-wakasdm   { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; }
        .role-card.active .role-card-icon-wali      { background: linear-gradient(135deg, #7c3aed, #a78bfa); color: #fff; }
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
            border: 1px solid #c7d2e6;
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
            background: #2563eb;
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
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: clamp(12px,1.2vw,14px);
            color: #1a2a45;
            background: #fff;
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input::placeholder { color: #94a3b8; }
        .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); background: #fff; }
        .form-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239aaac4' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 42px;
            cursor: pointer;
        }
        .form-select:invalid { color: #b0bdd4; }
        .form-select option { color: #1a2a45; }
        .btn-eye-toggle { position: absolute; right: 12px; background: none; border: none; color: #9aaac4; cursor: pointer; padding: 4px; display: flex; align-items: center; }
        .btn-eye-toggle:hover { color: #4a6fa8; }

        /* Remember + forgot */
        .form-options { display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; font-size: clamp(11px,1.1vw,13px); margin-bottom: clamp(14px,1.8vw,20px); }
        .forgot-link { text-decoration: none; font-weight: 600; font-size: 12px; color: #2563c0; }
        .forgot-link:hover { text-decoration: underline; }

        /* Submit buttons */
        .btn-submit {
            width: 100%;
            height: clamp(42px,4.5vw,50px);
            border: none; border-radius: 10px;
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

        .btn-admin { background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 16px rgba(37,99,235,0.35); }
        .btn-admin:hover { box-shadow: 0 6px 22px rgba(37,99,235,0.45); }

        .btn-guru  { background: linear-gradient(135deg, #0e7490, #0891b2); box-shadow: 0 4px 16px rgba(14,116,144,0.35); }
        .btn-guru:hover { box-shadow: 0 6px 22px rgba(14,116,144,0.45); }

        .btn-gurupiket  { background: linear-gradient(135deg, #0891b2, #06b6d4); box-shadow: 0 4px 16px rgba(8,145,178,0.35); }
        .btn-gurupiket:hover { box-shadow: 0 6px 22px rgba(8,145,178,0.45); }

        .btn-wali  { background: linear-gradient(135deg, #7c3aed, #8b5cf6); box-shadow: 0 4px 16px rgba(124,58,237,0.35); }
        .btn-wali:hover { box-shadow: 0 6px 22px rgba(124,58,237,0.45); }

        .btn-walikelas { background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 4px 16px rgba(5,150,105,0.35); }
        .btn-walikelas:hover { box-shadow: 0 6px 22px rgba(5,150,105,0.45); }

        .btn-satpam  { background: linear-gradient(135deg, #334155, #64748b); box-shadow: 0 4px 16px rgba(51,65,85,0.35); }
        .btn-satpam:hover { box-shadow: 0 6px 22px rgba(51,65,85,0.45); }

        .btn-wakasdm  { background: linear-gradient(135deg, #d97706, #f59e0b); box-shadow: 0 4px 16px rgba(217,119,6,0.35); }
        .btn-wakasdm:hover { box-shadow: 0 6px 22px rgba(217,119,6,0.45); }

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
        .divider-line { flex: 1; height: 1px; background: #e2e8f0; }
        .divider-text { font-size: 11px; color: #a0aec0; font-weight: 500; white-space: nowrap; }

        /* ───── RESPONSIVE ───── */
        @media (max-width: 820px) {
            body { overflow: auto; }
            .login-container { grid-template-columns: 1fr; height: auto; min-height: 100vh; }
            .left-hero { padding: 22px 20px 26px; min-height: auto; border-radius: 0 0 24px 24px; }
            .wave-bottom { display: none; }
            .hero-body { padding: 14px 0 0; justify-content: flex-start; }
            .hero-title { font-size: 20px; margin-bottom: 6px; }
            .hero-desc { max-width: 100%; font-size: 12.5px; margin-bottom: 8px; }
            .feature-list { display: none; }
            .hero-quote { display: none; }
            .left-footer { margin-top: 10px; }
            .right-form { padding: 24px 16px 32px; min-height: auto; }
            .form-card { padding: 26px 20px 22px; }
        }
        @media (max-width: 480px) {
            .brand-icon-box { width: 40px; height: 40px; }
            .brand-title { font-size: 16px; }
            .brand-sub { font-size: 10.5px; }
            .hero-title { font-size: 17px; }
            .hero-badge { font-size: 10.5px; padding: 4px 12px; }
            .hero-desc { font-size: 12px; }
            .left-footer { font-size: 10px; }
            .form-card { padding: 22px 14px 18px; border-radius: var(--radius); }
            .role-display { height: 96px; }
            .role-hint { font-size: 10px; }
            .form-header h2 { font-size: 19px; }
            .form-options { justify-content: flex-start; }
            .nisn-helper { padding: 9px 12px; font-size: 11.5px; }
        }
        @media (max-height: 600px) { .hero-quote { display: none; } }

        /* ══════════ NEW DESIGN OVERRIDE (sesuai desain halaman login) ══════════ */
        body {
            background:
                radial-gradient(circle at 100% 0%, rgba(59,130,246,0.08) 0%, transparent 40%),
                #f2f5fb;
            overflow: auto;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            min-height: 100vh;
            background: transparent;
        }

        /* ── LEFT: blob navy dengan lengkung kanan ── */
        .left-hero {
            background:
                radial-gradient(circle at 85% 15%, rgba(59,130,246,0.35) 0%, transparent 45%),
                radial-gradient(circle at 20% 85%, rgba(37,99,235,0.30) 0%, transparent 50%),
                linear-gradient(150deg, #0a173f 0%, #0d2160 45%, #0a1a4f 100%);
            border-radius: 0 48% 44% 0 / 0 52% 50% 0;
            align-items: center;
            justify-content: center;
            padding: 40px 8% 40px 6%;
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 16px 0 48px rgba(10,23,63,0.08);
        }
        .left-hero::before {
            content: '';
            position: absolute;
            width: 560px; height: 560px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.22) 0%, transparent 65%);
            filter: blur(40px);
            top: -140px; right: -100px;
            pointer-events: none;
        }
        .left-hero::after {
            content: '';
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.08);
            bottom: -120px; left: -120px;
            pointer-events: none;
        }

        /* ilustrasi tengah: lingkaran glow + logo */
        .hero-illus {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin: auto;
        }
        .illus-ring {
            width: clamp(180px, 22vw, 280px);
            height: clamp(180px, 22vw, 280px);
            border-radius: 50%;
            background: linear-gradient(160deg, rgba(96,165,250,0.25), rgba(37,99,235,0.12));
            border: 1.5px solid rgba(147,197,253,0.35);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 80px rgba(59,130,246,0.45), inset 0 0 40px rgba(59,130,246,0.18);
            position: relative;
        }
        .orbit-track {
            position: absolute;
            inset: -22px;
            border-radius: 50%;
            border: 1.5px dashed rgba(147,197,253,0.35);
            animation: orbitTrackSpin 18s linear infinite;
            pointer-events: none;
        }
        .orbit-satellite {
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
        }
        .illus-badge {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(160deg, #38bdf8, #1d4ed8);
            border: 2.5px solid rgba(255,255,255,0.9);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            box-shadow: 0 0 22px rgba(56,189,248,0.65), 0 6px 18px rgba(0,0,0,0.35);
            animation: counterOrbitSpin 18s linear infinite;
        }
        @keyframes orbitTrackSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes counterOrbitSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(-360deg); }
        }
        .illus-ring img {
            width: 62%; height: 62%;
            object-fit: contain;
            border-radius: 24px;
            filter: drop-shadow(0 10px 24px rgba(0,0,0,0.35));
        }
        .illus-title {
            margin-top: clamp(20px,3vw,34px);
            font-size: clamp(34px, 4.6vw, 58px);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #fff;
            text-shadow: 0 4px 30px rgba(59,130,246,0.55);
        }
        .illus-sub {
            margin-top: 8px;
            font-size: clamp(12px,1.2vw,14.5px);
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 600;
        }
        /* Floating Hero Badge Kiri (Presensi Realtime Hijau - Compact & Sleek) */
        .hero-float-badge {
            position: absolute;
            background: rgba(14, 30, 64, 0.72);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            padding: 7px 13px;
            display: flex;
            align-items: center;
            gap: 9px;
            color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.28), 0 0 16px rgba(16, 185, 129, 0.12);
            z-index: 5;
            pointer-events: auto;
            animation: heroFloatSoft 5s ease-in-out infinite;
        }
        .hero-float-badge.badge-realtime-left {
            top: 6%;
            left: 5%;
        }
        .float-badge-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.4);
        }
        .float-badge-icon svg {
            width: 15px;
            height: 15px;
        }
        .float-badge-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .float-badge-desc {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.65);
            margin-top: 2px;
            line-height: 1.2;
        }
        @keyframes heroFloatSoft {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        .left-footer { position: absolute; bottom: 22px; left: 0; right: 0; text-align: center; }

        /* sembunyikan elemen hero lama yang tak terpakai */
        .wave-bottom, .orb, .dot-grid, .hero-body, .brand-header { display: none !important; }

        /* ── RIGHT: latar terang + kartu login modern ── */
        .right-form {
            background: transparent;
            padding: 40px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12), 0 0 1px rgba(0, 0, 0, 0.05);
            padding: clamp(32px, 3.5vw, 44px);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .form-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%);
        }
        .lock-icon-circle { display: none; }
        
        .form-header {
            text-align: left;
            margin-bottom: 24px;
        }
        .login-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(56, 189, 248, 0.12));
            color: #1d4ed8;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            margin-bottom: 14px;
            border: 1px solid rgba(37, 99, 235, 0.18);
            letter-spacing: 0.2px;
        }
        .badge-dot-glow {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2563eb;
            box-shadow: 0 0 10px #3b82f6;
            animation: dotPulse 2s infinite ease-in-out;
        }
        @keyframes dotPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
        .form-header h2 {
            font-size: clamp(22px, 2.3vw, 27px);
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.4px;
            margin-bottom: 6px;
            line-height: 1.25;
        }
        .form-header p {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            margin: 0;
        }

        /* ── FORM GROUPS & INPUTS ── */
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .label-hint {
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
        }
        .input-relative {
            position: relative;
        }
        .form-input {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 13.5px;
            font-weight: 500;
            color: #0f172a;
            padding: 0 14px 0 44px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-input:focus {
            background: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            outline: none;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.2s ease;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .input-relative:focus-within .input-icon {
            color: #2563eb;
        }

        /* ── TIP BOX ── */
        .login-tip-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 11px 14px;
            margin: 18px 0 16px;
            font-size: 12px;
            color: #15803d;
            line-height: 1.45;
        }
        .login-tip-box .tip-icon {
            color: #16a34a;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── OPTIONS ── */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 12.5px;
        }
        .remember-me {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }
        .remember-me input {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
            border-radius: 4px;
        }
        .forgot-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* ── TOMBOL SUBMIT MEWAH ── */
        .button-boundary {
            position: relative;
            display: flex;
            justify-content: center;
            padding: 12px 14px;
            border: 1.5px dashed rgba(203, 213, 225, 0.8);
            border-radius: 20px;
            background: #fafcff;
        }
        .btn-submit {
            height: 48px;
            width: 100%;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 24px;
            font-size: 14.5px;
            font-weight: 700;
            color: #ffffff;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #38bdf8 100%);
            box-shadow: 0 10px 24px -4px rgba(37, 99, 235, 0.45);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-submit:hover {
            box-shadow: 0 14px 28px -4px rgba(37, 99, 235, 0.55);
            transform: translateY(-1.5px);
        }
        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 6px 16px -2px rgba(37, 99, 235, 0.4);
        }
        .btn-submit > svg:not(.btn-spinner) {
            width: 16px;
            height: 16px;
            transition: transform 0.2s ease;
        }
        .btn-submit:hover > svg:not(.btn-spinner) {
            transform: translateX(3px);
        }

        /* ── RESPONSIVE TERTATA & MODERN ── */
        @media (max-width: 900px) {
            body {
                overflow-y: auto;
                overflow-x: hidden;
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
            }
            .login-container {
                display: flex;
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
            }
            .left-hero {
                height: auto;
                min-height: auto;
                border-radius: 0 0 28px 28px;
                padding: 24px 20px 26px;
                position: relative;
                flex: none;
                box-shadow: 0 12px 32px rgba(10, 23, 63, 0.18);
            }
            .left-hero::before, .left-hero::after {
                display: none;
            }
            /* Sembunyikan floating badge di HP / Mobile agar tampilan hero sangat ringkas & fokus */
            .hero-float-badge {
                display: none !important;
            }

            .hero-illus {
                margin: 0 auto;
            }
            .illus-ring {
                width: 100px;
                height: 100px;
                box-shadow: 0 0 35px rgba(59,130,246,0.35);
            }
            .orbit-track {
                inset: -14px;
                border-width: 1.2px;
            }
            .orbit-satellite {
                top: -16px;
            }
            .illus-badge {
                width: 32px;
                height: 32px;
                box-shadow: 0 0 14px rgba(56,189,248,0.5);
            }
            .illus-badge svg {
                width: 14px;
                height: 14px;
            }
            .illus-title {
                font-size: 22px;
                margin-top: 10px;
            }
            .illus-sub {
                font-size: 10.5px;
                letter-spacing: 0.08em;
                margin-top: 4px;
            }
            .hero-live-pill {
                margin-top: 12px;
                padding: 5px 14px;
            }
            .live-pill-text {
                font-size: 11px;
            }
            .left-footer { display: none; }

            .right-form {
                padding: 20px 16px 36px;
                overflow: visible;
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .form-card {
                width: 100%;
                max-width: 440px;
                padding: 24px 20px;
                border-radius: 20px;
                box-shadow: 0 12px 36px rgba(15,40,100,0.1);
            }
            .form-header {
                text-align: center;
                margin-bottom: 18px;
            }
            .form-header h2 {
                font-size: 20px;
            }
            .form-header p {
                font-size: 12.5px;
            }

            /* Sembunyikan Role Admin di HP/Mobile */
            #card-admin,
            .role-dot[data-role="admin"],
            #panel-admin {
                display: none !important;
            }

            /* Ukuran & kenyamanan input di mobile */
            .form-input {
                font-size: 15px;
                height: 48px;
            }
            .btn-submit {
                height: 44px;
                font-size: 14px;
            }
            .role-selector {
                gap: 10px;
                margin-bottom: 12px;
            }
            .role-display {
                height: 94px;
                max-width: 180px;
            }
            .role-card-icon {
                width: 40px;
                height: 40px;
            }
            .role-card-name {
                font-size: 13px;
            }
            .role-arrow {
                width: 40px;
                height: 40px;
                border-radius: 10px;
            }
            .role-dots {
                gap: 6px;
                margin-bottom: 18px;
            }
        }

        @media (max-width: 480px) {
            .left-hero {
                padding: 20px 14px 22px;
                border-radius: 0 0 24px 24px;
            }
            .illus-ring {
                width: 90px;
                height: 90px;
            }
            .illus-title {
                font-size: 20px;
            }
            .right-form {
                padding: 16px 12px 28px;
            }
            .form-card {
                padding: 20px 16px 18px;
                border-radius: 18px;
            }
            .form-header h2 {
                font-size: 18.5px;
            }
            .role-display {
                max-width: 160px;
                height: 90px;
            }
        }
        /* ── Dark Mode Overrides untuk Login (Cohesive & Modern) ── */
        .login-theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 120;
        }
        .login-theme-toggle .theme-toggle-btn {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        [data-theme="dark"] body {
            background:
                radial-gradient(circle at 100% 0%, rgba(37, 99, 235, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 0% 100%, rgba(30, 58, 138, 0.16) 0%, transparent 45%),
                #070d19;
            color: #f8fafc;
        }

        [data-theme="dark"] .login-container {
            background: transparent;
        }

        [data-theme="dark"] .left-hero {
            background:
                radial-gradient(circle at 85% 15%, rgba(59, 130, 246, 0.28) 0%, transparent 50%),
                radial-gradient(circle at 20% 85%, rgba(37, 99, 235, 0.22) 0%, transparent 50%),
                linear-gradient(150deg, #081226 0%, #0c1c42 45%, #08132d 100%);
            border-right: 1px solid rgba(59, 130, 246, 0.15);
            box-shadow: 20px 0 60px rgba(0, 0, 0, 0.6);
        }

        [data-theme="dark"] .left-hero::before {
            background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, transparent 65%);
        }

        [data-theme="dark"] .left-hero::after {
            border-color: rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] .illus-ring {
            background: linear-gradient(160deg, rgba(59, 130, 246, 0.2), rgba(15, 23, 42, 0.6));
            border-color: rgba(96, 165, 250, 0.35);
            box-shadow: 0 0 80px rgba(37, 99, 235, 0.35), inset 0 0 40px rgba(59, 130, 246, 0.15);
        }

        [data-theme="dark"] .illus-ring::before {
            border-color: rgba(96, 165, 250, 0.28);
        }

        [data-theme="dark"] .illus-badge {
            background: linear-gradient(160deg, #3b82f6, #1d4ed8);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.6);
        }

        [data-theme="dark"] .illus-title {
            color: #ffffff;
            text-shadow: 0 4px 30px rgba(59, 130, 246, 0.65);
        }

        [data-theme="dark"] .illus-sub {
            color: rgba(255, 255, 255, 0.55);
        }

        /* ══════════ DARK MODE STYLING ══════════ */
        [data-theme="dark"] body {
            background:
                radial-gradient(circle at 100% 0%, rgba(37,99,235,0.12) 0%, transparent 45%),
                #070d1a !important;
            color: #f8fafc;
        }

        [data-theme="dark"] .left-footer {
            color: rgba(255, 255, 255, 0.35);
        }

        [data-theme="dark"] .right-form {
            background: transparent;
        }

        [data-theme="dark"] .form-card {
            background: #0f172a !important;
            border: 1px solid #1e293b !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.75), 0 0 1px rgba(255, 255, 255, 0.08) inset !important;
        }

        [data-theme="dark"] .form-card::before {
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 50%, #38bdf8 100%);
        }

        [data-theme="dark"] .login-badge-pill {
            background: rgba(37, 99, 235, 0.18) !important;
            border-color: rgba(96, 165, 250, 0.35) !important;
            color: #93c5fd !important;
        }

        [data-theme="dark"] .badge-dot-glow {
            background: #60a5fa;
            box-shadow: 0 0 10px #60a5fa;
        }

        [data-theme="dark"] .form-header h2 {
            color: #f8fafc !important;
        }

        [data-theme="dark"] .form-header p {
            color: #94a3b8 !important;
        }

        /* Inputs & Form Groups */
        [data-theme="dark"] .form-label {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .label-hint {
            color: #64748b !important;
        }

        [data-theme="dark"] .input-icon {
            color: #64748b !important;
        }

        [data-theme="dark"] .input-relative:focus-within .input-icon {
            color: #60a5fa !important;
        }

        [data-theme="dark"] .form-input {
            background: #141f36 !important;
            border-color: #243552 !important;
            color: #f8fafc !important;
        }

        [data-theme="dark"] .form-input::placeholder {
            color: #52637a !important;
        }

        [data-theme="dark"] .form-input:focus {
            background: #182542 !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
        }

        [data-theme="dark"] .btn-eye-toggle {
            color: #64748b;
        }

        [data-theme="dark"] .btn-eye-toggle:hover {
            color: #cbd5e1;
        }

        [data-theme="dark"] .login-tip-box {
            background: rgba(22, 163, 74, 0.12) !important;
            border-color: rgba(34, 197, 94, 0.3) !important;
            color: #86efac !important;
        }

        [data-theme="dark"] .login-tip-box .tip-icon {
            color: #4ade80 !important;
        }

        [data-theme="dark"] .login-tip-box strong {
            color: #bbf7d0 !important;
        }

        [data-theme="dark"] .remember-me {
            color: #94a3b8 !important;
        }

        [data-theme="dark"] .remember-me:hover {
            color: #e2e8f0 !important;
        }

        [data-theme="dark"] .remember-me input {
            accent-color: #3b82f6 !important;
        }

        [data-theme="dark"] .forgot-link {
            color: #60a5fa !important;
        }

        [data-theme="dark"] .forgot-link:hover {
            color: #93c5fd !important;
        }

        [data-theme="dark"] .button-boundary {
            background: #0b1326 !important;
            border-color: #1e2c48 !important;
        }

        [data-theme="dark"] .btn-submit {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #38bdf8 100%) !important;
            box-shadow: 0 10px 28px -4px rgba(37, 99, 235, 0.55) !important;
        }

        [data-theme="dark"] .btn-submit:hover {
            box-shadow: 0 14px 32px -4px rgba(37, 99, 235, 0.65) !important;
            filter: brightness(1.08);
        }

        [data-theme="dark"] .alert-danger {
            background: rgba(239, 68, 68, 0.15) !important;
            border-color: rgba(239, 68, 68, 0.35) !important;
            color: #fca5a5 !important;
        }

        [data-theme="dark"] .form-footer-text {
            color: #64748b !important;
        }

        [data-theme="dark"] .form-footer-text a {
            color: #60a5fa !important;
        }

        [data-theme="dark"] .form-footer-text a:hover {
            color: #93c5fd !important;
        }

        [data-theme="dark"] .login-theme-toggle .theme-toggle-btn {
            background: #0f172a !important;
            border-color: #1e293b !important;
            color: #fbbf24 !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45);
        }

        @media (max-width: 900px) {
            [data-theme="dark"] .left-hero {
                border-right: none;
                border-bottom: 1px solid rgba(59, 130, 246, 0.15);
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
            }
        }

        /* ── Submit Spinner & Button Submitting State ── */
        .btn-spinner {
            display: none;
            width: 18px;
            height: 18px;
            animation: btnSpin 0.7s linear infinite;
            flex-shrink: 0;
        }
        .btn-submit.is-submitting .btn-spinner {
            display: inline-block;
        }
        .btn-submit.is-submitting > svg:not(.btn-spinner) {
            display: none;
        }
        @keyframes btnSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ── Minimalist Clean Hero Animations ── */
        .hero-live-pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-top: 24px;
            padding: 8px 18px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15), 0 0 16px rgba(59, 130, 246, 0.12);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-live-pill:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(96, 165, 250, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22), 0 0 24px rgba(59, 130, 246, 0.25);
        }
        .live-dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
            animation: livePulse 2s infinite ease-in-out;
            flex-shrink: 0;
        }
        @keyframes livePulse {
            0%, 100% { transform: scale(0.9); opacity: 1; }
            50% { transform: scale(1.35); opacity: 0.55; }
        }
        .live-pill-text {
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.92);
            letter-spacing: 0.01em;
        }

        /* Role switch pop animation */
        .role-card.active {
            animation: rolePop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes rolePop {
            0% { transform: scale(0.92); }
            100% { transform: scale(1); }
        }

        [data-theme="dark"] .hero-live-pill {
            background: rgba(18, 29, 51, 0.7);
            border-color: rgba(59, 130, 246, 0.25);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 16px rgba(59, 130, 246, 0.15);
        }

        .alert-danger {
            animation: alertShake 0.45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        @keyframes alertShake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(3px); }
            30%, 50%, 70% { transform: translateX(-4px); }
            40%, 60% { transform: translateX(4px); }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>

<!-- Floating Theme Toggle Button -->
<div class="login-theme-toggle">
    <button class="theme-toggle-btn" type="button" onclick="toggleDarkMode()" aria-label="Ganti Tema" title="Ganti Mode Gelap / Terang">
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

<div class="login-container">

    <!-- ─── LEFT HERO ─── -->
    <div class="left-hero">
        <!-- Floating Badge Hijau: Presensi Realtime (Kiri Atas) -->
        <div class="hero-float-badge badge-realtime-left">
            <div class="float-badge-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="float-badge-title">Presensi Realtime</div>
                <div class="float-badge-desc" id="live-realtime-badge-desc">06:45:00 WIB • Tepat Waktu ✨</div>
            </div>
        </div>

        <div class="hero-illus">
            <div class="illus-ring">
                <!-- Orbiting Checkmark Badge along dashed ring -->
                <div class="orbit-track">
                    <div class="orbit-satellite">
                        <div class="illus-badge" title="Sistem Presensi Terverifikasi">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <img src="{{ asset('logo.png') }}" alt="Logo PresensiKita">
            </div>
            <div class="illus-title">PresensiKita</div>
            <div class="illus-sub">Sistem Informasi Kehadiran Siswa</div>

            <!-- Single Minimalist Live Status Pill (Clean & Spacious) -->
            <div class="hero-live-pill">
                <span class="live-dot-pulse"></span>
                <span class="live-pill-text" id="live-school-clock">SMKN 1 Boyolangu • Sistem Aktif</span>
            </div>
        </div>
        <div class="left-footer">&copy; {{ date('Y') }} PresensiKita. All rights reserved.</div>
    </div>

    <!-- ─── RIGHT FORM ─── -->
    <div class="right-form">
        <div class="form-card">

            <!-- Header dengan Badge Menarik -->
            <div class="form-header">
                <div class="login-badge-pill">
                    <span class="badge-dot-glow"></span>
                    <span>Portal Presensi Terpadu</span>
                </div>
                <h2>Selamat Datang</h2>
                <p>Satu akses untuk Guru, Siswa, Orang Tua, dan Staf SMKN 1 Boyolangu.</p>
            </div>

            <!-- ─── ERROR MESSAGES ─── -->
            @if($errors->any())
            <div class="alert-danger" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <!-- ─── FORM LOGIN TUNGGAL TERPADU ─── -->
            <div class="form-panel active" id="panel-login" role="tabpanel">
                <form method="POST" action="{{ route('login.post') }}" id="form-login">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="login-username">
                            <span>Username / NIP / NISN</span>
                            <span class="label-hint">Identitas Akun</span>
                        </label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" id="login-username" name="username" class="form-input" placeholder="Masukkan username, NIP, atau NISN" value="{{ old('username') }}" autocomplete="username" required autofocus>
                        </div>
                    </div>
                    <div class="form-group" id="group-password">
                        <label class="form-label" for="login-password">
                            <span>Password</span>
                            <span class="label-hint">Kata Sandi</span>
                        </label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" id="login-password" name="password" class="form-input" placeholder="Masukkan password (opsional untuk NISN murid)" autocomplete="current-password">
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('login-password','eye-login')" title="Tampilkan/Sembunyikan">
                                <svg id="eye-login" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Petunjuk Ringkas Menarik -->
                    <div class="login-tip-box">
                        <div class="tip-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                        <div>
                            <strong>Petunjuk:</strong> Guru &amp; Staf gunakan <em>Username/NIP</em>, Wali Murid gunakan <em>10 digit NISN Siswa</em>.
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk me-reset password Anda.')" class="forgot-link">Lupa password?</a>
                    </div>
                    <div class="button-boundary">
                        <button type="submit" class="btn-submit" data-runaway id="btn-login">
                            <svg class="btn-spinner" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
                            <span>Masuk ke PresensiKita</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="form-footer-text">
                Ada kendala akun? <a href="{{ route('laporan.public') }}">Laporkan ke Admin</a>
            </div>

        </div>
    </div>
</div>

<script>
    // ── Toggle password visibility
    function togglePassword(inputId, iconId) {
        const pwd  = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!pwd || !icon) return;
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            pwd.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    // ── Button Submit State
    function attachLoginSubmit(formId, btnId, initialText) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function() {
            const btn = document.getElementById(btnId);
            if (btn) {
                btn.classList.add('is-submitting');
                btn.style.pointerEvents = 'none';
                const span = btn.querySelector('span');
                if (span) span.textContent = initialText || 'Memproses...';
            }
        });
    }

    attachLoginSubmit('form-login', 'btn-login', 'Memproses...');

    // ── Live School Clock
    (function() {
        function updateSchoolClock() {
            const now = new Date();
            const hrs = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');
            const clockText = `${hrs}:${mins}:${secs} WIB`;

            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dayName = dayNames[now.getDay()];
            const date = now.getDate();
            const monthName = monthNames[now.getMonth()];
            const year = now.getFullYear();
            const dateText = `${dayName}, ${date} ${monthName} ${year}`;

            const el = document.getElementById('live-school-clock');
            if (el) {
                el.textContent = `SMKN 1 Boyolangu • ${clockText}`;
            }

            const badgeDesc = document.getElementById('live-realtime-badge-desc');
            if (badgeDesc) {
                badgeDesc.textContent = `${dateText} • Tepat Waktu ✨`;
            }
        }
        updateSchoolClock();
        setInterval(updateSchoolClock, 1000);
    })();
</script>
<script src="{{ asset('js/runaway-button.js') }}"></script>
</body>
</html>