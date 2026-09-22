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
                <span>Tahun Ajaran</span>
                <span id="header-school-year">{{ $tahunAjaran->tahun_ajaran ?? '' }}</span>
                <span>({{ $tahunAjaran->semester ?? '' }})</span>
                <span class="status-dot"></span>
            </div>
        </div>
    </div>

    {{-- Kanan: Tanggal, jam, notifikasi, dan profil --}}
    <div class="header-right">

        {{-- Tanggal --}}
        <div class="header-date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            @php
    $hariMap = \App\Models\Hari::getActiveDays()->pluck('nama_hari', 'nama_inggris')->toArray();
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $now = \Carbon\Carbon::now();
    $hari = $hariMap[$now->format('l')] ?? '';
    $tgl = $now->format('j');
    $bln = $months[(int)$now->format('n')] ?? '';
    $thn = $now->format('Y');
    $tanggalFormatted = "$hari, $tgl $bln $thn";

    $nowMinutes = ((int)$now->format('H') * 60) + (int)$now->format('i');
    $initialPeriodText = 'Di luar jam';
    $initialPeriodTitle = 'Di luar jam pelajaran';

    $istirahatListAwal = $hari === 'Jumat' ? [
        1 => [\App\Models\Pengaturan::get('jam_istirahat_jumat_1_mulai', '09:00'), \App\Models\Pengaturan::get('jam_istirahat_jumat_1_selesai', '09:50')],
        2 => [\App\Models\Pengaturan::get('jam_istirahat_jumat_2_mulai', '11:20'), \App\Models\Pengaturan::get('jam_istirahat_jumat_2_selesai', '13:00')],
    ] : [
        1 => [\App\Models\Pengaturan::get('jam_istirahat_1_mulai', '09:40'), \App\Models\Pengaturan::get('jam_istirahat_1_selesai', '10:00')],
        2 => [\App\Models\Pengaturan::get('jam_istirahat_2_mulai', '12:00'), \App\Models\Pengaturan::get('jam_istirahat_2_selesai', '13:00')],
    ];

    foreach ($istirahatListAwal as $noIst => [$m, $s]) {
        $mMin = ((int)substr($m, 0, 2) * 60) + (int)substr($m, 3, 2);
        $sMin = ((int)substr($s, 0, 2) * 60) + (int)substr($s, 3, 2);
        if ($nowMinutes >= $mMin && $nowMinutes < $sMin) {
            $initialPeriodText = "Istirahat $noIst";
            $initialPeriodTitle = "Istirahat $noIst (" . substr($m, 0, 5) . " - " . substr($s, 0, 5) . ")";
            break;
        }
    }

    if ($initialPeriodText === 'Di luar jam') {
        $jamAwal = \Illuminate\Support\Facades\DB::table('jam_pelajaran')
            ->whereNull('deleted_at')
            ->where('hari', $hari)
            ->orderBy('jam_ke')
            ->get();

        foreach ($jamAwal as $jItem) {
            $mMin = ((int)substr($jItem->jam_mulai, 0, 2) * 60) + (int)substr($jItem->jam_mulai, 3, 2);
            $sMin = ((int)substr($jItem->jam_selesai, 0, 2) * 60) + (int)substr($jItem->jam_selesai, 3, 2);
            if ($nowMinutes >= $mMin && $nowMinutes < $sMin) {
                $jKe = (int)$jItem->jam_ke >= 100 ? (int)$jItem->jam_ke - 100 : (int)$jItem->jam_ke;
                $initialPeriodText = "Jam ke-$jKe";
                $initialPeriodTitle = "Jam ke-$jKe (" . substr($jItem->jam_mulai, 0, 5) . " - " . substr($jItem->jam_selesai, 0, 5) . ")";
                break;
            }
        }
    }
@endphp
            <span id="header-date">{{ $tanggalFormatted }}</span>
        </div>

        {{-- Jam real-time mengikuti waktu lokal perangkat --}}
        <div class="header-date" aria-label="Jam saat ini" title="Waktu lokal perangkat">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <polyline points="12 7 12 12 15 14"/>
            </svg>
            <span id="header-time">{{ $now->format('H:i:s') }}</span>
        </div>
        <div class="header-date header-period-box" aria-label="Jam pelajaran saat ini" title="{{ $initialPeriodTitle }}">
            <span id="header-period" title="{{ $initialPeriodTitle }}">{{ $initialPeriodText }}</span>
        </div>

        {{-- Tombol Dark / Light Mode --}}
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

        {{-- Notifikasi --}}
        <button class="notif-btn" onclick="tampilkanNotifikasi()" aria-label="Notifikasi" title="Pemberitahuan">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notif-badge" id="notif-badge-count" style="display:none">0</span>
        </button>

        {{-- Profil Pengguna Dropdown --}}
        <div class="user-profile-wrap" id="user-profile-wrap">
            <div class="user-profile" id="user-profile-btn" onclick="toggleUserDropdown(event)" title="Menu Profil Pengguna" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false">
                <div class="header-user-avatar" id="avatar-display">
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
                <div class="user-info-text">
                    <div class="user-name" id="username-display">{{ $guru->nama_guru ?? 'Administrator' }}</div>
                    <div class="user-role">{{ ($guru->is_admin ?? false) ? 'Administrator' : ($guru->Peran ?? 'Guru') }}</div>
                </div>
                {{-- Chevron --}}
                <svg class="user-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>

            {{-- Floating Dropdown Card --}}
            <div class="user-dropdown-menu" id="user-dropdown-menu">
                <div class="user-dropdown-header">
                    <div class="dropdown-avatar">
                        @if(!empty($guru->foto_profil) && file_exists(public_path($guru->foto_profil)))
                            <img src="{{ asset($guru->foto_profil) }}" alt="{{ $guru->nama_guru ?? 'User' }}">
                        @else
                            <span>{{ strtoupper(substr($guru->nama_guru ?? 'AD', 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-name">{{ $guru->nama_guru ?? 'Administrator' }}</div>
                        <div class="dropdown-user-role-badge">
                            <span class="role-dot"></span>
                            {{ ($guru->is_admin ?? false) ? 'Administrator' : ($guru->Peran ?? 'Guru') }}
                        </div>
                    </div>
                </div>
                <div class="user-dropdown-divider"></div>
                <div class="user-dropdown-body">
                    <a class="user-dropdown-item" href="javascript:void(0)" onclick="closeUserDropdown(); showPage('profil');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span>Profil Pengguna</span>
                    </a>
                    @if(session('auth_role') === 'admin')
                    <a class="user-dropdown-item" href="javascript:void(0)" onclick="closeUserDropdown(); showPage('pengaturan');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        <span>Pengaturan Sistem</span>
                    </a>
                    @endif
                </div>
                <div class="user-dropdown-divider"></div>
                <div class="user-dropdown-footer">
                    <button type="button" class="user-dropdown-logout" onclick="closeUserDropdown(); confirmKeluar('logout-form-sidebar');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Keluar Akun</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</header>
