<div class="page-content page-anim" id="page-pengaturan" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Pengaturan Sistem PresensiKita</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Konfigurasi umum aplikasi presensi digital</div>
        </div>
    </div>
    <div class="card" style="max-width:700px">
        <h3 style="font-size:16px;font-weight:700;margin-bottom:16px">Konfigurasi Sekolah</h3>
        <div style="display:grid;gap:14px">
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Nama Sekolah</label>
                <input type="text" class="filter-input" id="set-nama-sekolah" value="{{ $namaSekolah ?? 'SMKN 1 Boyolangu' }}" style="width:100%;margin-top:4px">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Tahun Ajaran</label>
                    <input type="text" class="filter-input" id="set-tahun-ajaran" value="{{ $tahunAjaran->tahun_ajaran ?? '2026/2027' }}" placeholder="Contoh: 2026/2027" style="width:100%;margin-top:4px">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Semester</label>
                    <select class="filter-select" id="set-semester" style="width:100%;margin-top:4px">
                        <option value="Ganjil" {{ ($tahunAjaran->semester ?? '') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap"  {{ ($tahunAjaran->semester ?? '') === 'Genap'  ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:var(--text-secondary)">Sistem Absensi</label>
                <select class="filter-select" id="set-sistem-absensi" style="width:100%;margin-top:4px">
                    <option value="Absensi Realtime & Otomatis Rekap" {{ ($sistemAbsensi ?? '') === 'Absensi Realtime & Otomatis Rekap' ? 'selected' : '' }}>Absensi Realtime &amp; Otomatis Rekap</option>
                    <option value="Absensi Manual" {{ ($sistemAbsensi ?? '') === 'Absensi Manual' ? 'selected' : '' }}>Absensi Manual</option>
                </select>
            </div>
            <button class="btn-primary" style="border-radius:10px;padding:10px 20px;font-size:13.5px;width:fit-content;margin-top:10px" onclick="simpanPengaturan()">Simpan Pengaturan</button>
        </div>
    </div>
</div>
