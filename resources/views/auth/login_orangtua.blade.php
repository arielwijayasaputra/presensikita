<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Orang Tua - PresensiKita</title>
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
            overflow: hidden;
        }

        /* ─── BACKGROUND EDUKASI MODERN ─── */
        .page-bg {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);
            position: relative;
            overflow: hidden;
        }

        /* Pattern & Geometric Education Elements Overlay */
        .bg-pattern {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(37, 99, 235, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        /* Floating Decorative SVGs */
        .dec-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.06);
            pointer-events: none;
        }

        .dec-1 { top: 10%; left: 8%; width: 120px; height: 120px; transform: rotate(-15deg); }
        .dec-2 { bottom: 12%; right: 8%; width: 140px; height: 140px; transform: rotate(12deg); }
        .dec-3 { top: 65%; left: 12%; width: 90px; height: 90px; transform: rotate(25deg); }
        .dec-4 { top: 15%; right: 15%; width: 100px; height: 100px; transform: rotate(-20deg); }

        /* ─── CARD LOGIN CENTERED ─── */
        .login-card-centered {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 
                0 20px 50px rgba(15, 23, 42, 0.3),
                0 10px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
            text-align: center;
        }

        /* Logo / Header Badge */
        .card-badge-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            border-radius: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.35);
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .brand-sub {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 28px;
        }

        /* Alert Error */
        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 22px;
            text-align: left;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 15px;
            font-family: inherit;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #0f172a;
            background-color: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            background-color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(2, 132, 199, 0.4);
        }

        .footer-copyright {
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="page-bg">
        <div class="bg-pattern"></div>

        <!-- Decorative Floating SVGs (Edukasi Theme) -->
        <svg class="dec-icon dec-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        <svg class="dec-icon dec-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <svg class="dec-icon dec-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <svg class="dec-icon dec-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>

        <!-- CARD LOGIN CENTERED -->
        <div class="login-card-centered">
            
            <div class="card-badge-wrapper">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>

            <h1 class="brand-title">Portal Orang Tua</h1>
            <p class="brand-sub">Masukkan NISN Siswa untuk mengecek dan memantau kehadiran anak Anda.</p>

            @if($errors->any())
            <div class="alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.orangtua.post') }}" id="form-login-orangtua">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="nisn">NISN Siswa (10 Digit)</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <line x1="7" y1="8" x2="17" y2="8"/>
                                <line x1="7" y1="12" x2="13" y2="12"/>
                                <line x1="7" y1="16" x2="10" y2="16"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="nisn"
                            name="nisn"
                            class="form-input"
                            placeholder="Masukkan NISN (contoh: 0099166409)"
                            value="{{ old('nisn') }}"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="btn-submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <span>Masuk ke Portal</span>
                </button>
            </form>

            <div class="footer-copyright">
                © {{ date('Y') }} PresensiKita — SMKN 1 Boyolangu
            </div>
        </div>

    </div>

    <script>
        document.getElementById('form-login-orangtua').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
            btn.querySelector('span').textContent = 'Memeriksa NISN...';
        });
    </script>
</body>
</html>
