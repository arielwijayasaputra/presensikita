<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PresensiKita - Presensi Guru SMKN 1 Boyolangu</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo_white.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <!-- External CSS Style Asset -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body>

@include($sidebar ?? 'partials.sidebar')
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebarMobile()"></div>

<div class="main-content{{ session('auth_role') === 'admin' ? ' is-admin' : '' }}">
    @if(session('auth_role') === 'admin')
        <div class="admin-mobile-notice" style="display:none;background:#fef3c7;border-bottom:1px solid #fde68a;color:#92400e;padding:10px 16px;font-size:12.5px;font-weight:600;align-items:center;gap:8px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Panel Administrator dirancang khusus untuk layar Desktop / Laptop.</span>
        </div>
    @endif
    @include('partials.header')

    @yield('content')
</div>

<!-- Inline Server Variables -->
<script>
    let currentSiswaList = {!! json_encode($siswaList ?? []) !!};
    window.dashboardTren = @json($dashboardTren ?? []);
    window.laporanInitial = @json($laporanRekap ?? null);
    window.profilUpdateUrl = @json($profilUpdateUrl ?? route('profil.update'));
    window.daftarGuru = @json($allGuru ?? []);
    window.daftarJurusan = @json($allJurusan ?? []);
</script>

<!-- External JavaScript Asset -->
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
