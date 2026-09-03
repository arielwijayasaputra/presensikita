<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <img src="{{ asset('logo.png') }}" alt="Logo PresensiKita" style="width:100%;height:100%;object-fit:contain;border-radius:9px;">
        </div>
        <div class="sidebar-logo-text">
            <div class="logo-title">PresensiKita</div>
            <div class="logo-subtitle">Platform Absensi Digital</div>
        </div>
        <button type="button" class="sidebar-close-btn" onclick="closeSidebarMobile()" aria-label="Tutup menu">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <nav class="sidebar-nav">

        <!-- MENU UTAMA -->
        <div class="nav-section-label">Menu Utama</div>

        <a class="nav-item active" onclick="showPage('guru-dashboard')" id="nav-guru-dashboard">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Dashboard
        </a>

        <a class="nav-item" onclick="showPage('jadwal-mengajar')" id="nav-jadwal-mengajar">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            Jadwal Mengajar
        </a>

        <a class="nav-item" onclick="showPage('jurnal-absensi')" id="nav-jurnal-absensi">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
                <polyline points="9 16 11 18 15 14"/>
            </svg>
            Jurnal &amp; Absensi
        </a>

        <a class="nav-item" onclick="showPage('riwayat-jurnal')" id="nav-riwayat-jurnal">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Riwayat Jurnal
        </a>

        <a class="nav-item" href="{{ route('guru.izin-guru.form') }}" id="nav-izin-guru">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h8M8 17h5"/></svg>
            Permintaan Izin Guru
        </a>

        <!-- PENGATURAN -->
        <div class="nav-section-label">Pengaturan</div>

        <a class="nav-item" onclick="showPage('profil')" id="nav-profil">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Profil
        </a>

        <!-- LAPORAN & PENGADUAN -->
        <div class="nav-section-label">Laporan &amp; Pengaduan</div>

        <a class="nav-item" onclick="showPage('buat-laporan')" id="nav-buat-laporan">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            Buat Laporan
        </a>

    </nav>

    <!-- SIDEBAR FOOTER (LOGOUT) -->
    <div class="sidebar-bottom">
        <button type="button" class="sidebar-logout-btn" onclick="confirmKeluar('logout-form-sidebar')" title="Keluar dari akun">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Keluar</span>
        </button>
    </div>

    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
</aside>
