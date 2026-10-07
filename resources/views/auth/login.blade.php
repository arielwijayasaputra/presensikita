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
            background:
                radial-gradient(circle at 100% 0%, rgba(59,130,246,0.08) 0%, transparent 40%),
                #f2f5fb;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            color: #0f172a;
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* ───── LAYOUT ───── */
        .login-container {
            width: 100vw;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            overflow: hidden;
            background: transparent;
        }

        /* ───── LEFT HERO ───── */
        .left-hero {
            background:
                radial-gradient(circle at 85% 15%, rgba(59,130,246,0.35) 0%, transparent 45%),
                radial-gradient(circle at 20% 85%, rgba(37,99,235,0.30) 0%, transparent 50%),
                linear-gradient(150deg, #0a173f 0%, #0d2160 45%, #0a1a4f 100%);
            border-radius: 0 48% 44% 0 / 0 52% 50% 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 8% 40px 6%;
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 16px 0 48px rgba(10,23,63,0.08);
            overflow: hidden;
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

        /* Hero Content & Orbit Illustration */
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

        /* Floating Hero Badge Kiri */
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
        .float-badge-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
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
        .left-footer {
            position: absolute;
            bottom: 22px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.35);
        }

        /* ───── RIGHT FORM & MODERN CARD ───── */
        .right-form {
            background: transparent;
            padding: 40px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .form-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.10), 0 0 1px rgba(0, 0, 0, 0.05);
            padding: clamp(32px, 3.5vw, 44px);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
        }
        .form-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%);
        }

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

        /* ── ALERTS ── */
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: alertShake 0.45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        @keyframes alertShake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(3px); }
            30%, 50%, 70% { transform: translateX(-4px); }
            40%, 60% { transform: translateX(4px); }
        }

        /* ── FORM ELEMENTS ── */
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
            transition: color 0.2s ease;
        }
        .input-relative {
            position: relative;
            display: flex;
            align-items: center;
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
        .form-input {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 13.5px;
            font-weight: 500;
            color: #0f172a;
            padding: 0 42px 0 44px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-input::placeholder {
            color: #94a3b8;
        }
        .form-input:focus {
            background: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .btn-eye-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: color 0.2s ease, background-color 0.2s ease;
        }
        .btn-eye-toggle:hover {
            color: #2563eb;
            background: rgba(37, 99, 235, 0.08);
        }

        /* ── LIVE ROLE AUTO-DETECT CHIP ── */
        .role-detect-container {
            margin-top: 8px;
            min-height: 28px;
            display: flex;
            align-items: center;
        }
        .role-detect-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 13px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            animation: chipFadeIn 0.25s ease-out;
        }
        @keyframes chipFadeIn {
            from { opacity: 0; transform: translateY(-3px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .role-detect-chip.default {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .role-detect-chip.chip-siswa {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(167, 139, 250, 0.15));
            color: #7c3aed;
            border: 1px solid rgba(124, 58, 237, 0.28);
        }
        .role-detect-chip.chip-guru {
            background: linear-gradient(135deg, rgba(14, 116, 144, 0.1), rgba(6, 182, 212, 0.15));
            color: #0e7490;
            border: 1px solid rgba(14, 116, 144, 0.28);
        }
        .role-detect-chip.chip-staff {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(59, 130, 246, 0.15));
            color: #1d4ed8;
            border: 1px solid rgba(37, 99, 235, 0.28);
        }

        /* ── FORM OPTIONS ── */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
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

        /* ── SUBMIT BUTTON WITH SHIMMER ── */
        .button-boundary {
            position: relative;
            display: flex;
            justify-content: center;
            padding: 10px 12px;
            border: 1.5px dashed rgba(203, 213, 225, 0.8);
            border-radius: 18px;
            background: #fafcff;
        }
        .btn-submit {
            position: relative;
            overflow: hidden;
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
            font-family: inherit;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #38bdf8 100%);
            box-shadow: 0 10px 24px -4px rgba(37, 99, 235, 0.45);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0; left: -120%;
            width: 70%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: skewX(-20deg);
            pointer-events: none;
        }
        .btn-submit:hover::after {
            left: 140%;
            transition: left 0.8s ease-in-out;
        }
        .btn-submit:hover {
            box-shadow: 0 14px 28px -4px rgba(37, 99, 235, 0.55);
            transform: translateY(-2px);
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

        /* Submit Spinner */
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

        /* ── SECURITY & TRUST BADGE ── */
        .security-trust-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
            font-size: 11.5px;
            color: #64748b;
            font-weight: 500;
            text-align: center;
        }
        .security-trust-badge svg {
            color: #10b981;
            flex-shrink: 0;
        }

        /* Footer */
        .form-footer-text {
            text-align: center;
            font-size: 12px;
            color: #6b7a99;
            margin-top: 14px;
        }
        .form-footer-text a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }
        .form-footer-text a:hover {
            text-decoration: underline;
        }

        /* ── FLOATING THEME TOGGLE ── */
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

        /* ══════════ DARK MODE STYLING ══════════ */
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

        [data-theme="dark"] .illus-badge {
            background: linear-gradient(160deg, #3b82f6, #1d4ed8);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.6);
        }

        [data-theme="dark"] .illus-title {
            color: #ffffff;
            text-shadow: 0 4px 30px rgba(59, 130, 246, 0.65);
        }

        [data-theme="dark"] .hero-live-pill {
            background: rgba(18, 29, 51, 0.7);
            border-color: rgba(59, 130, 246, 0.25);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 16px rgba(59, 130, 246, 0.15);
        }

        [data-theme="dark"] .left-footer {
            color: rgba(255, 255, 255, 0.35);
        }

        [data-theme="dark"] .form-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.75), 0 0 1px rgba(255, 255, 255, 0.08) inset;
        }

        [data-theme="dark"] .form-card::before {
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 50%, #38bdf8 100%);
        }

        [data-theme="dark"] .login-badge-pill {
            background: rgba(37, 99, 235, 0.18);
            border-color: rgba(96, 165, 250, 0.35);
            color: #93c5fd;
        }

        [data-theme="dark"] .badge-dot-glow {
            background: #60a5fa;
            box-shadow: 0 0 10px #60a5fa;
        }

        [data-theme="dark"] .form-header h2 {
            color: #f8fafc;
        }

        [data-theme="dark"] .form-header p {
            color: #94a3b8;
        }

        [data-theme="dark"] .form-label {
            color: #e2e8f0;
        }

        [data-theme="dark"] .label-hint {
            color: #64748b;
        }

        [data-theme="dark"] .input-icon {
            color: #64748b;
        }

        [data-theme="dark"] .input-relative:focus-within .input-icon {
            color: #60a5fa;
        }

        [data-theme="dark"] .form-input {
            background: #141f36;
            border-color: #243552;
            color: #f8fafc;
        }

        [data-theme="dark"] .form-input::placeholder {
            color: #52637a;
        }

        [data-theme="dark"] .form-input:focus {
            background: #182542;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
        }

        [data-theme="dark"] .btn-eye-toggle {
            color: #64748b;
        }

        [data-theme="dark"] .btn-eye-toggle:hover {
            color: #93c5fd;
            background: rgba(59, 130, 246, 0.15);
        }

        [data-theme="dark"] .role-detect-chip.default {
            background: #141f36;
            color: #94a3b8;
            border-color: #243552;
        }
        [data-theme="dark"] .role-detect-chip.chip-siswa {
            background: rgba(124, 58, 237, 0.2);
            color: #c4b5fd;
            border-color: rgba(167, 139, 250, 0.35);
        }
        [data-theme="dark"] .role-detect-chip.chip-guru {
            background: rgba(14, 116, 144, 0.25);
            color: #67e8f9;
            border-color: rgba(6, 182, 212, 0.35);
        }
        [data-theme="dark"] .role-detect-chip.chip-staff {
            background: rgba(37, 99, 235, 0.25);
            color: #93c5fd;
            border-color: rgba(96, 165, 250, 0.35);
        }

        [data-theme="dark"] .remember-me {
            color: #94a3b8;
        }

        [data-theme="dark"] .remember-me:hover {
            color: #e2e8f0;
        }

        [data-theme="dark"] .remember-me input {
            accent-color: #3b82f6;
        }

        [data-theme="dark"] .forgot-link {
            color: #60a5fa;
        }

        [data-theme="dark"] .forgot-link:hover {
            color: #93c5fd;
        }

        [data-theme="dark"] .button-boundary {
            background: #0b1326;
            border-color: #1e2c48;
        }

        [data-theme="dark"] .btn-submit {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #38bdf8 100%);
            box-shadow: 0 10px 28px -4px rgba(37, 99, 235, 0.55);
        }

        [data-theme="dark"] .btn-submit:hover {
            box-shadow: 0 14px 32px -4px rgba(37, 99, 235, 0.65);
            filter: brightness(1.08);
        }

        [data-theme="dark"] .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        [data-theme="dark"] .security-trust-badge {
            border-top-color: #1e293b;
            color: #94a3b8;
        }
        [data-theme="dark"] .security-trust-badge svg {
            color: #34d399;
        }

        [data-theme="dark"] .form-footer-text {
            color: #64748b;
        }

        [data-theme="dark"] .form-footer-text a {
            color: #60a5fa;
        }

        [data-theme="dark"] .form-footer-text a:hover {
            color: #93c5fd;
        }

        [data-theme="dark"] .login-theme-toggle .theme-toggle-btn {
            background: #0f172a;
            border-color: #1e293b;
            color: #fbbf24;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            body {
                overflow-y: auto;
                min-height: 100vh;
                min-height: 100dvh;
            }
            .login-container {
                display: flex;
                flex-direction: column;
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
            }
            .form-card {
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

            .form-input {
                font-size: 15px;
                height: 48px;
            }
            .btn-submit {
                height: 44px;
                font-size: 14px;
            }

            [data-theme="dark"] .left-hero {
                border-right: none;
                border-bottom: 1px solid rgba(59, 130, 246, 0.15);
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <div class="float-badge-title">Presensi Realtime</div>
                <div class="float-badge-desc" id="live-realtime-badge-desc">Tepat Waktu &amp; Terverifikasi ✨</div>
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

            <!-- Single Minimalist Live Status Pill -->
            <div class="hero-live-pill">
                <span class="live-dot-pulse"></span>
                <span class="live-pill-text" id="live-school-clock">SMKN 1 Boyolangu • Sistem Aktif</span>
            </div>
        </div>
        <div class="left-footer">&copy; {{ date('Y') }} PresensiKita. SMKN 1 Boyolangu.</div>
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
                <p>Satu akses terintegrasi untuk Guru, Wali Murid, Siswa, dan Staf SMKN 1 Boyolangu.</p>
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
                    
                    <!-- Username / NISN Input -->
                    <div class="form-group">
                        <label class="form-label" for="login-username">
                            <span>Username / NISN</span>
                        </label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" id="login-username" name="username" class="form-input" placeholder="Masukkan Username atau NISN" value="{{ old('username') }}" autocomplete="username" required autofocus>
                        </div>
                        
                        <!-- Live Role Auto-Detect Chip -->
                        <div class="role-detect-container">
                            <div class="role-detect-chip default" id="role-detect-chip">
                                <span class="detect-icon" id="detect-icon">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                </span>
                                <span class="detect-text" id="detect-text">Ketik Username atau 10 digit NISN</span>
                            </div>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-group" id="group-password">
                        <label class="form-label" for="login-password">
                            <span>Password</span>
                        </label>
                        <div class="input-relative">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" id="login-password" name="password" class="form-input" placeholder="Masukkan password akun Anda" autocomplete="current-password">
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('login-password','eye-login')" title="Tampilkan/Sembunyikan Kata Sandi">
                                <svg id="eye-login" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Options: Ingat Saya & Lupa Password -->
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk me-reset password Anda.')" class="forgot-link">Lupa password?</a>
                    </div>

                    <!-- Submit Button with Modern Boundary & Shimmer -->
                    <div class="button-boundary">
                        <button type="submit" class="btn-submit" data-runaway id="btn-login">
                            <svg class="btn-spinner" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/></svg>
                            <span>Masuk ke PresensiKita</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>

                    <!-- Security & Trust Badge -->
                    <div class="security-trust-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>Koneksi Terenkripsi &amp; Presensi Real-Time SMKN 1 Boyolangu</span>
                    </div>

                </form>
            </div>

            <!-- Footer Link -->
            <div class="form-footer-text">
                Ada kendala akses akun? <a href="{{ route('laporan.public') }}">Laporkan ke Admin</a>
            </div>

        </div>
    </div>
</div>

<script>
    // ── Toggle Password Visibility
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

    // ── Live Role Auto-Detection
    (function() {
        const usernameInput = document.getElementById('login-username');
        const chip = document.getElementById('role-detect-chip');
        const detectIcon = document.getElementById('detect-icon');
        const detectText = document.getElementById('detect-text');
        const pwdInput = document.getElementById('login-password');

        if (!usernameInput || !chip) return;

        function evaluateRole() {
            const val = (usernameInput.value || '').trim();
            chip.className = 'role-detect-chip';

            if (!val) {
                chip.classList.add('default');
                detectIcon.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
                detectText.textContent = 'Ketik Username atau 10 digit NISN';
                if (pwdInput) pwdInput.placeholder = 'Masukkan password akun Anda';
                return;
            }

            // Pola 1: 10 digit angka persis = NISN Siswa / Wali Murid
            if (/^\d{10}$/.test(val)) {
                chip.classList.add('chip-siswa');
                detectIcon.innerHTML = '🎓';
                detectText.textContent = 'Terdeteksi: Siswa / Wali Murid (NISN)';
                if (pwdInput) pwdInput.placeholder = 'Opsional (Kosongkan jika hanya memantau presensi)';
            }
            // Pola 2: Angka sebagian (belum 10 digit) = sedang mengetik NISN
            else if (/^\d+$/.test(val) && val.length < 10) {
                chip.classList.add('default');
                detectIcon.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
                detectText.textContent = `NISN: ${val.length}/10 digit angka`;
                if (pwdInput) pwdInput.placeholder = 'Masukkan password akun Anda';
            }
            // Pola 3: Teks umum / username = Akun Guru / Staf / Admin
            else {
                chip.classList.add('chip-staff');
                detectIcon.innerHTML = '🛡️';
                detectText.textContent = 'Terdeteksi: Akun Guru / Staf / Admin';
                if (pwdInput) pwdInput.placeholder = 'Masukkan password akun Anda';
            }
        }

        usernameInput.addEventListener('input', evaluateRole);
        evaluateRole(); // Initial check on load (e.g. if old value exists)
    })();

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

    attachLoginSubmit('form-login', 'btn-login', 'Memproses Masuk...');

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