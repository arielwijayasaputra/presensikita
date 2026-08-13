<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PresensiKita</title>
    <meta name="description" content="Masuk ke sistem PresensiKita untuk mengelola kehadiran siswa secara digital.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #e8edf5;
            height: 100vh;
            overflow: hidden;
        }
        .login-container {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.15fr 1fr;
        }
        .left-hero {
            background: linear-gradient(160deg, #1a3a6e 0%, #1e4080 40%, #1a3368 100%);
            padding: 5% 6%;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        .left-hero::before {
            content: '';
            position: absolute;
            top: 6%; right: 4%;
            width: 140px; height: 100px;
            background-image: radial-gradient(circle, rgba(255,255,255,0.18) 1.5px, transparent 1.5px);
            background-size: 14px 14px;
            pointer-events: none;
        }
        .wave-bottom { position: absolute; bottom: 0; left: 0; right: 0; pointer-events: none; z-index: 0; }
        .left-content { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; justify-content: space-between; }
        .brand-header { display: flex; align-items: center; gap: 12px; }
        .brand-icon-box { width: 46px; height: 46px; background: rgba(255,255,255,0.12); border: 1.5px solid rgba(255,255,255,0.25); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .brand-title { font-size: clamp(17px,2vw,22px); font-weight: 800; color: #fff; letter-spacing: -0.01em; }
        .brand-sub { font-size: clamp(10px,1.1vw,12.5px); color: rgba(255,255,255,0.6); margin-top: 2px; }
        .hero-body { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 4% 0; }
        .hero-title { font-size: clamp(20px,2.6vw,32px); font-weight: 800; line-height: 1.22; letter-spacing: -0.02em; margin-bottom: 10px; color: #fff; }
        .hero-title .accent { color: #5ba4f5; }
        .hero-desc { font-size: clamp(11px,1.15vw,13.5px); color: rgba(255,255,255,0.6); line-height: 1.65; margin-bottom: 6%; max-width: 88%; }
        .feature-list { display: flex; flex-direction: column; gap: clamp(10px,1.6vw,20px); }
        .feature-item { display: flex; align-items: flex-start; gap: 13px; }
        .feature-icon-badge { width: clamp(30px,3vw,38px); height: clamp(30px,3vw,38px); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #7ec8f8; flex-shrink: 0; }
        .feature-title { font-size: clamp(11.5px,1.2vw,14px); font-weight: 700; color: #f0f6ff; margin-bottom: 2px; }
        .feature-sub { font-size: clamp(10px,1vw,12.5px); color: rgba(255,255,255,0.45); line-height: 1.45; }
        .hero-quote { background: rgba(0,0,0,0.18); border-left: 3px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 12px 16px; margin-top: 4%; }
        .hero-quote p { font-size: clamp(10.5px,1.05vw,13px); color: rgba(255,255,255,0.75); line-height: 1.55; font-style: italic; }
        .left-footer { font-size: clamp(9.5px,0.95vw,12px); color: rgba(255,255,255,0.35); }
        .right-form { padding: 5% 7%; display: flex; flex-direction: column; justify-content: center; background: #f5f7fb; }
        .form-card { background: #fff; border-radius: 16px; padding: 7% 8%; box-shadow: 0 2px 16px rgba(15,40,100,0.07); border: 1px solid #e4eaf3; }
        .lock-icon-circle { width: clamp(52px,6vw,70px); height: clamp(52px,6vw,70px); background: #eef3fb; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1e3a6e; margin: 0 auto clamp(12px,1.6vw,20px); }
        .form-header { text-align: center; margin-bottom: clamp(16px,2vw,24px); }
        .form-header h2 { font-size: clamp(17px,2vw,22px); font-weight: 800; color: #0f1f3d; margin-bottom: 4px; }
        .form-header p { font-size: clamp(11px,1.1vw,13px); color: #6b7a99; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #b91c1c; display: flex; align-items: center; gap: 8px; }
        .form-group { margin-bottom: clamp(12px,1.5vw,18px); }
        .form-label { display: block; font-size: clamp(11px,1.1vw,13px); font-weight: 700; color: #2d3a55; margin-bottom: 6px; }
        .input-relative { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 13px; color: #9aaac4; display: flex; align-items: center; pointer-events: none; }
        .form-input { width: 100%; height: clamp(40px,4.5vw,50px); padding: 10px 14px 10px 40px; border: 1.5px solid #d0d9ea; border-radius: 10px; font-size: clamp(12px,1.2vw,14px); color: #1a2a45; background: #fafbfd; outline: none; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input::placeholder { color: #b0bdd4; }
        .form-input:focus { border-color: #2563c0; box-shadow: 0 0 0 3px rgba(37,99,192,0.12); background: #fff; }
        .btn-eye-toggle { position: absolute; right: 12px; background: none; border: none; color: #9aaac4; cursor: pointer; padding: 4px; display: flex; align-items: center; }
        .btn-eye-toggle:hover { color: #4a6fa8; }
        .form-options { display: flex; align-items: center; justify-content: space-between; font-size: clamp(11px,1.1vw,13px); margin-bottom: clamp(14px,1.8vw,22px); }
        .remember-label { display: flex; align-items: center; gap: 7px; color: #4d6080; cursor: pointer; user-select: none; }
        .remember-label input[type="checkbox"] { width: 15px; height: 15px; accent-color: #1e3a8a; cursor: pointer; }
        .forgot-link { color: #2563c0; text-decoration: none; font-weight: 600; }
        .forgot-link:hover { text-decoration: underline; }
        .btn-submit { width: 100%; height: clamp(40px,4.5vw,50px); background: #1a3268; border: none; border-radius: 10px; color: #fff; font-size: clamp(13px,1.3vw,15px); font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-family: inherit; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(26,50,104,0.35); }
        .btn-submit:hover { background: #22408a; box-shadow: 0 6px 18px rgba(26,50,104,0.45); }
        .btn-submit:active { transform: scale(0.985); }
        .form-footer-text { text-align: center; font-size: clamp(10.5px,1.05vw,12.5px); color: #6b7a99; margin-top: clamp(14px,1.8vw,22px); }
        .form-footer-text a { color: #2563c0; font-weight: 600; text-decoration: none; }
        .form-footer-text a:hover { text-decoration: underline; }
        @media (max-width: 820px) {
            body { height: auto; overflow: auto; }
            .login-container { width: 100%; height: auto; min-height: 100vh; grid-template-columns: 1fr; }
            .left-hero { padding: 32px 28px; min-height: 260px; }
            .right-form { padding: 28px 24px; }
            .left-hero::before { display: none; }
        }
        @media (max-height: 600px) { .hero-quote { display: none; } }
    </style>
</head>
<body>
<div class="login-container">
    <div class="left-hero">
        <svg class="wave-bottom" viewBox="0 0 600 160" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,100 C80,40 200,160 320,80 C440,0 520,120 600,80 L600,160 L0,160 Z" fill="rgba(90,150,240,0.10)"/>
            <path d="M0,130 C100,70 250,160 400,110 C500,75 560,130 600,110 L600,160 L0,160 Z" fill="rgba(90,150,240,0.07)"/>
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
                <h1 class="hero-title">Kelola Kehadiran Siswa<br>Lebih <span class="accent">Mudah &amp; Terorganisir</span></h1>
                <p class="hero-desc">Catat, pantau, dan rekap kehadiran siswa setiap hari secara digital, akurat, dan efisien.</p>
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
                        <div><div class="feature-title">Rekap Otomatis</div><div class="feature-sub">Dapatkan laporan dan grafik kehadiran secara otomatis.</div></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        </div>
                        <div><div class="feature-title">Aman &amp; Terpercaya</div><div class="feature-sub">Data tersimpan aman dan dapat diakses sesuai hak akses pengguna.</div></div>
                    </div>
                </div>
                <div class="hero-quote">
                    <p>&#10077; Disiplin hari ini, sukses di masa depan.<br>Mari bersama wujudkan sekolah yang tertib dan berprestasi. &#10078;</p>
                </div>
            </div>
            <div class="left-footer">&copy; {{ date('Y') }} PresensiKita. All rights reserved.</div>
        </div>
    </div>
    <div class="right-form">
        <div class="form-card">
            <div class="lock-icon-circle">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div class="form-header">
                <h2>Selamat Datang!</h2>
                <p>Silakan masuk untuk melanjutkan ke sistem.</p>
            </div>
            @if($errors->any())
            <div class="alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif
            <form method="POST" action="{{ route('login.post') }}" id="login-form">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-relative">
                        <span class="input-icon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Masukkan username" value="{{ old('username') }}" autocomplete="username" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-relative">
                        <span class="input-icon"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="btn-eye-toggle" onclick="togglePassword()" title="Tampilkan/Sembunyikan Password">
                            <svg id="eye-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div class="form-options">
                    <label class="remember-label"><input type="checkbox" name="remember" id="remember"><span>Ingat saya</span></label>
                    <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk me-reset password Anda.')" class="forgot-link">Lupa password?</a>
                </div>
                <button type="submit" class="btn-submit" id="btn-submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span>Masuk</span>
                </button>
            </form>
            <div class="form-footer-text">Belum punya akun? <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah untuk membuat akun baru.')">Hubungi administrator sekolah.</a></div>
        </div>
    </div>
</div>
<script>
    function togglePassword() {
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
    document.getElementById('login-form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-submit');
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
        btn.querySelector('span').textContent = 'Memproses...';
    });
</script>
</body>
</html>