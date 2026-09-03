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

        @if(session('auth_role') === 'guru_piket')
        <a class="nav-item active" onclick="showPage('guru-piket')" id="nav-guru-piket">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
            Dashboard Guru Piket
        </a>
        <a class="nav-item" onclick="showPage('izin-guru')" id="nav-izin-guru">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h8M8 17h5"/></svg>
            Permintaan Izin Guru
        </a>
        <a class="nav-item" href="#dispen-siswa" onclick="showPage('dispen-siswa'); return false;" id="nav-dispen-siswa">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
            Dispensasi Siswa
        </a>
        <a class="nav-item" href="#absensi-siswa" onclick="showPage('absensi-siswa'); return false;" id="nav-absensi-siswa">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Absensi Siswa
        </a>
        @endif

        @if(session('auth_role') === 'satpam')
        <a class="nav-item active" onclick="showPage('satpam')" id="nav-satpam">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Dashboard Satpam
        </a>
        <a class="nav-item" onclick="showPage('satpam-harian')" id="nav-satpam-harian">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
            Data Harian Dispen
        </a>
        @endif

        @if(session('auth_role') === 'waka_sdm')
        <a class="nav-item active" onclick="showPage('waka-sdm')" id="nav-waka-sdm">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Dashboard Waka SDM
        </a>
        @endif

        @if(session('auth_role') === 'walikelas')
        <a class="nav-item active" onclick="showPage('walikelas')" id="nav-walikelas">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Dashboard Wali Kelas
        </a>
        <a class="nav-item" onclick="showPage('wali-absensi-harian')" id="nav-wali-absensi-harian">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Absensi Harian Kelas
        </a>
        <a class="nav-item" onclick="showPage('wali-jurnal-harian')" id="nav-wali-jurnal-harian">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
            Jurnal Harian Kelas
        </a>
        <a class="nav-item" onclick="showPage('wali-rekap-absensi')" id="nav-wali-rekap-absensi">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Rekap Absensi Kelas
        </a>
        <a class="nav-item" onclick="showPage('wali-rekap-jurnal')" id="nav-wali-rekap-jurnal">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Rekap Jurnal Kelas
        </a>
        @endif

        <!-- PENGATURAN -->
        <div class="nav-section-label">Pengaturan</div>

        <a class="nav-item {{ in_array(session('auth_role'), ['guru_piket', 'satpam', 'waka_sdm', 'walikelas']) ? '' : 'active' }}" onclick="showPage('profil')" id="nav-profil">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Profil Pengguna
        </a>

        <!-- LAPORAN & PENGADUAN -->
        @if(session('auth_role') !== 'satpam')
        <div class="nav-section-label">Laporan &amp; Pengaduan</div>

        <a class="nav-item" onclick="showPage('buat-laporan')" id="nav-buat-laporan">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            Buat Laporan
        </a>
        @endif
    </nav>

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
