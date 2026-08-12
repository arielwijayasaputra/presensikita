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

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            padding: 24px 16px;
        }

        /* ─── Main Wrapper Container ─── */
        .login-container {
            width: 100%;
            max-width: 1060px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            min-height: 600px;
        }

        /* ══════════════════════════════════
           SISI KIRI: HERO & FITUR (SIMPLE)
        ══════════════════════════════════ */
        .left-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 48px 44px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon-box {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            flex-shrink: 0;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .hero-body {
            margin: 36px 0;
        }

        .hero-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .hero-desc {
            font-size: 13.5px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .feature-icon-badge {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            flex-shrink: 0;
        }

        .feature-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 2px;
        }

        .feature-sub {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        .left-footer {
            font-size: 12px;
            color: #64748b;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* ══════════════════════════════════
           SISI KANAN: FORM LOGIN (SIMPLE)
        ══════════════════════════════════ */
        .right-form {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .lock-icon-circle {
            width: 64px;
            height: 64px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            margin: 0 auto 20px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .form-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .form-header p {
            font-size: 13px;
            color: #64748b;
        }

        /* Error Alert */
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-relative {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 46px;
            padding: 10px 16px 10px 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 13.5px;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        .btn-eye-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-eye-toggle:hover {
            color: #475569;
        }

        /* Form Options Row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 22px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .remember-label input {
            width: 16px;
            height: 16px;
            accent-color: #0f172a;
            cursor: pointer;
        }

        .forgot-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            height: 46px;
            background: #0f172a;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background: #1e293b;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .form-footer-text {
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
            margin-top: 22px;
        }

        .form-footer-text a {
            color: #0f172a;
            font-weight: 700;
            text-decoration: none;
        }

        .form-footer-text a:hover {
            text-decoration: underline;
        }

        /* ─── Responsive Breakpoints ─── */
        @media (max-width: 860px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 480px;
            }
            .left-hero {
                padding: 32px 28px;
            }
            .right-form {
                padding: 36px 28px;
            }
            .hero-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">

        <!-- ════════════════════ SISI KIRI: HERO & FITUR ════════════════════ -->
        <div class="left-hero">

            <!-- Logo -->
            <div class="brand-header">
                <div class="brand-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="brand-title">PresensiKita</h1>
                    <div class="brand-sub">Sistem Informasi Kehadiran Siswa</div>
                </div>
            </div>

            <!-- Content -->
            <div class="hero-body">
                <h2 class="hero-title">Kelola Kehadiran Siswa Lebih Mudah & Terorganisir</h2>
                <p class="hero-desc">Catat, pantau, dan rekap kehadiran siswa setiap hari secara digital, akurat, dan efisien.</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                                <polyline points="9 16 11 18 15 14"/>
                            </svg>
                        </div>
                        <div>
                            <div class="feature-title">Pencatatan Cepat</div>
                            <div class="feature-sub">Input kehadiran harian siswa hanya dalam beberapa klik.</div>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </div>
                        <div>
                            <div class="feature-title">Rekap Otomatis</div>
                            <div class="feature-sub">Dapatkan laporan dan grafik kehadiran secara otomatis.</div>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon-badge">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <polyline points="9 12 11 14 15 10"/>
                            </svg>
                        </div>
                        <div>
                            <div class="feature-title">Aman & Terpercaya</div>
                            <div class="feature-sub">Data tersimpan aman dan dapat diakses sesuai hak akses pengguna.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="left-footer">
                © {{ date('Y') }} PresensiKita. All rights reserved.
            </div>

        </div>

        <!-- ════════════════════ SISI KANAN: FORM LOGIN ════════════════════ -->
        <div class="right-form">

            <!-- Lock Icon Badge -->
            <div class="lock-icon-circle">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <!-- Header Text -->
            <div class="form-header">
                <h2>Selamat Datang!</h2>
                <p>Silakan masuk untuk melanjutkan ke sistem.</p>
            </div>

            <!-- Error Banner -->
            @if($errors->any())
            <div class="alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login.post') }}" id="login-form">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-relative">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-input"
                            placeholder="Masukkan username"
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
                    <div class="input-relative">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="btn-eye-toggle" onclick="togglePassword()" title="Tampilkan/Sembunyikan Password">
                            <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Options -->
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="javascript:void(0)" onclick="Swal.fire({ icon: 'info', title: 'Lupa Password', text: 'Silakan hubungi administrator sekolah untuk me-reset password Anda.' })" class="forgot-link">Lupa password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="btn-submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    <span>Masuk</span>
                </button>
            </form>

            <!-- Help -->
            <div class="form-footer-text">
                Belum punya akun? <a href="javascript:void(0)" onclick="Swal.fire({ icon: 'info', title: 'Bantuan Akun', text: 'Silakan hubungi administrator sekolah untuk membuat akun baru.' })">Hubungi administrator sekolah.</a>
            </div>

        </div>

    </div>

    <!-- Script -->
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

        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
            btn.querySelector('span').textContent = 'Memproses...';
        });
    </script>
</body>
</html>
