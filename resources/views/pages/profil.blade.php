<div class="page-content page-anim" id="page-profil" style="display:none">
    <div class="page-header">
        <div>
            <div class="breadcrumb">Dashboard <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg> <span>Profil Pengguna</span></div>
            <div class="page-title">Profil Pengguna</div>
        </div>
    </div>
    <div class="card" style="max-width:600px">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
            <div class="user-avatar" style="width:60px;height:60px;font-size:22px">
                {{ strtoupper(substr($guru->nama_guru ?? 'SN', 0, 2)) }}
            </div>
            <div>
                <h3 style="font-size:18px;font-weight:700" id="prof-title-name">{{ $guru->nama_guru ?? 'Administrator' }}</h3>
                <p style="font-size:13px;color:var(--text-secondary)">{{ ($guru->is_admin ?? false) ? 'Administrator' : ($guru->Peran ?? 'Guru') }} - SMKN 1 Boyolangu</p>
            </div>
        </div>
        <div style="display:grid;gap:14px">
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Nama Lengkap</label>
                <input type="text" class="filter-input" id="input-prof-nama" value="{{ $guru->nama_guru ?? 'Administrator' }}" style="width:100%;margin-top:4px">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Peran</label>
                <input type="text" class="filter-input" value="{{ ($guru->is_admin ?? false) ? 'Administrator' : ($guru->Peran ?? 'Guru') }}" readonly style="width:100%;margin-top:4px;background:#f8fafc">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Nomor HP</label>
                <input type="text" class="filter-input" id="input-prof-hp" value="{{ $guru->no_hp ?? '08123456789' }}" style="width:100%;margin-top:4px">
            </div>
            <button class="save-btn" style="width:fit-content;margin-top:10px" onclick="updateProfilSubmit()">Update Profil</button>
        </div>
    </div>
</div>
