<div class="page-content page-anim" id="page-profil">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Profil Pengguna</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Kelola informasi profil dan kata sandi akun</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start">

        <!-- ══ CARD KIRI: FOTO PROFIL & LOGOUT ══ -->
        <div class="card" style="text-align:center;padding:28px 24px">
            <div style="position:relative;width:100px;height:100px;margin:0 auto 16px">
                @if(!empty($guru->foto_profil) && file_exists(public_path($guru->foto_profil)))
                    <img id="avatar-preview-img" src="{{ asset($guru->foto_profil) }}" alt="Foto Profil" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #3b82f6;box-shadow:0 4px 12px rgba(59,130,246,0.25)">
                @else
                    <div id="avatar-preview-fallback" class="user-avatar" style="width:100px;height:100px;font-size:36px;border-radius:50%;margin:0 auto;box-shadow:0 4px 12px rgba(30,58,138,0.2)">
                        {{ strtoupper(substr($guru->nama_guru ?? 'US', 0, 2)) }}
                    </div>
                    <img id="avatar-preview-img" src="" alt="Foto Profil" style="display:none;width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #3b82f6;box-shadow:0 4px 12px rgba(59,130,246,0.25)">
                @endif

                <label for="input-foto-profil" style="position:absolute;bottom:0;right:0;width:32px;height:32px;background:#1e3a8a;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.15)" title="Ubah Foto Profil">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </label>
                <input type="file" id="input-foto-profil" accept="image/*" style="display:none" onchange="previewProfilePhoto(this)">
            </div>

            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px" id="prof-title-name">{{ $guru->nama_guru ?? 'User' }}</h3>
            <p style="font-size:13px;color:var(--text-secondary);margin-bottom:16px">{{ $guru->Peran ?? 'Guru' }}</p>

            <button type="button" class="btn-secondary" style="width:100%;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:600;margin-bottom:16px" onclick="document.getElementById('input-foto-profil').click()">
                Pilih Foto Baru
            </button>

            <hr style="border:none;border-top:1px solid #e2e8f0;margin:16px 0">

            <button type="button" style="width:100%;padding:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#b91c1c;font-weight:700;font-size:13.5px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s" onclick="confirmKeluar('logout-form-sidebar')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Keluar</span>
            </button>
        </div>

        <!-- ══ CARD KANAN: FORM EDIT PROFIL ══ -->
        <div class="card" style="padding:28px">
            <h4 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:20px;display:flex;align-items:center;gap:8px">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Akun & Keamanan
            </h4>

            <form id="struktural-profile-form" onsubmit="updateStrukturalProfil(event)">
                @csrf
                <div style="display:grid;gap:18px">

                    <div>
                        <label style="font-size:12.5px;font-weight:700;color:#334155">Nama Lengkap & Gelar</label>
                        <input type="text" class="filter-input" id="input-prof-nama" value="{{ $guru->nama_guru ?? '' }}" required style="width:100%;margin-top:6px;padding:10px 14px">
                    </div>

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

                    <div>
                        <label style="font-size:12.5px;font-weight:700;color:#334155">Hak Akses Sistem</label>
                        <input type="text" class="filter-input" value="{{ $guru->Peran ?? 'Guru' }}" readonly style="width:100%;margin-top:6px;padding:10px 14px;background:#f8fafc;color:#64748b">
                    </div>

                    <hr style="border:none;border-top:1px solid #e2e8f0;margin:8px 0">

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
                            <label style="font-size:12.5px;font-weight:700;color:#334155">Password Baru</label>
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

<script>
function updateStrukturalProfil(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-profile');
    const originalText = btn.textContent;
    btn.textContent = 'Menyimpan...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('nama_guru', document.getElementById('input-prof-nama').value);
    formData.append('username', document.getElementById('input-prof-username').value);
    formData.append('no_hp', document.getElementById('input-prof-hp').value);

    const oldPass = document.getElementById('input-prof-old-pass').value;
    const newPass = document.getElementById('input-prof-new-pass').value;
    if (newPass) {
        formData.append('current_password', oldPass);
        formData.append('new_password', newPass);
    }

    const fotoInput = document.getElementById('input-foto-profil');
    if (fotoInput.files.length > 0) {
        formData.append('foto', fotoInput.files[0]);
    }

    fetch('{{ $profilUpdateUrl }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
            if (res.nama_guru) {
                document.getElementById('prof-title-name').textContent = res.nama_guru;
                const displayName = document.getElementById('username-display');
                if (displayName) displayName.textContent = res.nama_guru;
            }
            if (res.foto_profil) {
                const img = document.getElementById('avatar-preview-img');
                if (img) { img.src = res.foto_profil; img.style.display = 'block'; }
                const fb = document.getElementById('avatar-preview-fallback');
                if (fb) fb.style.display = 'none';
            }
            document.getElementById('input-prof-old-pass').value = '';
            document.getElementById('input-prof-new-pass').value = '';
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan jaringan.' });
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

function previewProfilePhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatar-preview-img');
            if (img) { img.src = e.target.result; img.style.display = 'block'; }
            const fb = document.getElementById('avatar-preview-fallback');
            if (fb) fb.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
