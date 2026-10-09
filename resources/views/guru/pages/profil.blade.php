<div class="page-content page-anim" id="page-profil" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Profil Guru</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Kelola informasi profil dan kata sandi akun</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start">

        <!-- ══ CARD KIRI: FOTO PROFIL & LOGOUT ══ -->
        <div class="card" style="text-align:center;padding:28px 24px">
            @php
                $hasFoto = !empty($guru->foto_profil) && file_exists(public_path($guru->foto_profil));
                $fotoUrl = $hasFoto ? asset($guru->foto_profil) : '';
                $inisial = strtoupper(substr($guru->nama_guru ?? 'GUR', 0, 2));
                $nama = $guru->nama_guru ?? 'Guru';
                $peran = ($guru->is_admin ?? false) ? 'Administrator Sistem' : ($guru->Peran ?? 'Guru');
            @endphp
            <div style="position:relative;width:110px;height:110px;margin:0 auto 16px">
                <div id="avatar-container" onclick="zoomFotoProfil(document.getElementById('avatar-preview-img')?.src || '{{ $fotoUrl }}', '{{ addslashes($nama) }}', '{{ $inisial }}', '{{ addslashes($peran) }}')" style="width:110px;height:110px;border-radius:50%;overflow:hidden;cursor:pointer;border:3.5px solid #2563eb;box-shadow:0 6px 18px rgba(37,99,235,0.25);background:linear-gradient(135deg, #2563eb, #1d4ed8);display:flex;align-items:center;justify-content:center;margin:0 auto;" title="Klik untuk memperbesar foto">
                    @if($hasFoto)
                        <img id="avatar-preview-img" src="{{ $fotoUrl }}" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;display:block;">
                        <div id="avatar-preview-fallback" style="display:none;color:#ffffff;font-size:38px;font-weight:800;line-height:1">
                            {{ $inisial }}
                        </div>
                    @else
                        <div id="avatar-preview-fallback" style="color:#ffffff;font-size:38px;font-weight:800;line-height:1">
                            {{ $inisial }}
                        </div>
                        <img id="avatar-preview-img" src="" alt="Foto Profil" style="display:none;width:100%;height:100%;object-fit:cover;">
                    @endif
                </div>

                <label for="input-foto-profil" style="position:absolute;bottom:0;right:0;width:34px;height:34px;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;border:2.5px solid var(--card-bg, #ffffff);box-shadow:0 3px 8px rgba(0,0,0,0.25);transition:transform 0.2s" title="Ubah Foto Profil">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </label>
                <input type="file" id="input-foto-profil" accept="image/*" style="display:none" onchange="previewProfilePhoto(this)">
            </div>

            <h3 style="font-size:18px;font-weight:800;color:var(--text-primary);margin-bottom:4px" id="prof-title-name">{{ $nama }}</h3>
            <p style="font-size:13px;color:var(--text-secondary);margin-bottom:16px">{{ $peran }}</p>

            <button type="button" class="btn-secondary" style="width:100%;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;justify-content:center;gap:6px" onclick="document.getElementById('input-foto-profil').click()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <span>Pilih Foto Baru</span>
            </button>

            <hr style="border:none;border-top:1px solid var(--border, #e2e8f0);margin:16px 0">

            <!-- Tombol Keluar -->
            <button type="button" style="width:100%;padding:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#b91c1c;font-weight:700;font-size:13.5px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onclick="confirmKeluar('logout-form-sidebar')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Keluar</span>
            </button>
        </div>

        <!-- ══ CARD KANAN: FORM EDIT PROFIL, USERNAME & PASSWORD ══ -->
        <div class="card" style="padding:28px">
            <h4 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:20px;display:flex;align-items:center;gap:8px">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Akun &amp; Keamanan
            </h4>

            <form id="guru-profile-form" onsubmit="updateProfilSubmit(event)">
                @csrf
                <div style="display:grid;gap:18px">

                    <!-- Nama Lengkap -->
                    <div>
                        <label style="font-size:12.5px;font-weight:700;color:#334155">Nama Lengkap &amp; Gelar</label>
                        <input type="text" class="filter-input" id="input-prof-nama" value="{{ $guru->nama_guru ?? '' }}" readonly style="width:100%;margin-top:6px;padding:10px 14px;background:#f8fafc;color:#64748b;cursor:not-allowed">
                    </div>

                    <!-- Username & No HP -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div>
                            <label style="font-size:12.5px;font-weight:700;color:#334155">Username</label>
                            <input type="text" class="filter-input" id="input-prof-username" value="{{ $guru->username ?? '' }}" required style="width:100%;margin-top:6px;padding:10px 14px">
                        </div>
                        <div>
                            <label style="font-size:12.5px;font-weight:700;color:#334155">Nomor HP / WhatsApp</label>
                            <input type="text" class="filter-input" id="input-prof-hp" value="{{ $guru->no_hp ?? '' }}" style="width:100%;margin-top:6px;padding:10px 14px" placeholder="08123456789">
                        </div>
                    </div>

                    <!-- Role (Readonly) -->
                    <div>
                        <label style="font-size:12.5px;font-weight:700;color:#334155">Hak Akses Sistem</label>
                        <input type="text" class="filter-input" value="{{ ($guru->is_admin ?? false) ? 'Administrator Sistem' : ($guru->Peran ?? 'Guru') }}" readonly style="width:100%;margin-top:6px;padding:10px 14px;background:#f8fafc;color:#64748b">
                    </div>

                    <hr style="border:none;border-top:1px solid #e2e8f0;margin:8px 0">

                    <!-- Ganti Password -->
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:8px">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Ganti Password (Opsional)
                    </h4>
                    <p style="font-size:12px;color:#64748b;margin-bottom:12px">Kosongkan jika tidak ingin mengubah password akun Anda.</p>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div>
                            <label style="font-size:12.5px;font-weight:700;color:#334155">Password Saat Ini</label>
                            <input type="password" class="filter-input" id="input-prof-old-pass" placeholder="Password lama" style="width:100%;margin-top:6px;padding:10px 14px">
                        </div>
                        <div>
                            <label style="font-size:12.5px;font-weight:700;color:#334155">Password Baru (Ganti PW)</label>
                            <input type="password" class="filter-input" id="input-prof-new-pass" placeholder="Password baru" style="width:100%;margin-top:6px;padding:10px 14px">
                        </div>
                    </div>

                    <div style="margin-top:12px">
                        <button type="submit" class="btn-primary" id="btn-save-profile" style="padding:10px 20px;font-size:13.5px;font-weight:700;border-radius:10px">
                            Simpan Perubahan Profil
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>
