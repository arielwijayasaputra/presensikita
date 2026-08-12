<header class="header">
    {{-- Kiri: Hamburger + Nama Sekolah --}}
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        <div>
            <div class="school-name" id="header-school-name">{{ $namaSekolah ?? 'SMKN 1 Boyolangu' }}</div>
            <div class="school-year">
                Tahun Ajaran
                <span id="header-school-year">{{ $tahunAjaran->tahun_ajaran ?? '2024/2025' }}</span>
                (<span id="header-semester">{{ $tahunAjaran->semester ?? 'Ganjil' }}</span>)
                <span class="status-dot"></span>
            </div>
        </div>
    </div>

    {{-- Kanan: Tanggal + Notifikasi + Profil --}}
    <div class="header-right">

        {{-- Tanggal --}}
        <div class="header-date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="header-date">Selasa, 27 Mei 2025</span>
        </div>

        {{-- Notifikasi --}}
        <button class="notif-btn" aria-label="Notifikasi">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notif-badge">3</span>
        </button>

        {{-- Profil Pengguna --}}
        <div class="user-profile" style="cursor:default">
            <div class="header-user-avatar" id="avatar-display" style="width:36px;height:36px;border-radius:50%;overflow:hidden;position:relative">
                @if(!empty($guru->foto_profil) && file_exists(public_path($guru->foto_profil)))
                    <img class="user-avatar-img" src="{{ asset($guru->foto_profil) }}"
                         alt="{{ $guru->nama_guru ?? 'User' }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <span class="header-avatar-initials user-avatar-fallback" style="display:none">{{ strtoupper(substr($guru->nama_guru ?? 'AD', 0, 2)) }}</span>
                @else
                    <img class="user-avatar-img" src="" alt="User" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <span class="header-avatar-initials user-avatar-fallback">{{ strtoupper(substr($guru->nama_guru ?? 'AD', 0, 2)) }}</span>
                @endif
            </div>
            <div>
                <div class="user-name" id="username-display">{{ $guru->nama_guru ?? 'Administrator' }}</div>
                <div class="user-role">{{ ($guru->is_admin ?? false) ? 'Administrator' : ($guru->Peran ?? 'Guru') }}</div>
            </div>
            {{-- Chevron --}}
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8;flex-shrink:0;margin-left:2px">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>

    </div>
</header>
