<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PresensiKita - Presensi Guru SMKN 1 Boyolangu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- External CSS Style Asset -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

@include($sidebar ?? 'partials.sidebar')
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebarMobile()"></div>

<div class="main-content">
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
</script>

<!-- External JavaScript Asset -->
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
