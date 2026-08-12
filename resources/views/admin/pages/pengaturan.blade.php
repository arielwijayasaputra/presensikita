<div class="page-content page-anim" id="page-pengaturan" style="display:none">
    <div class="page-header">
        <div>
            <div class="breadcrumb">Dashboard <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg> <span>Pengaturan</span></div>
            <div class="page-title">Pengaturan Sistem PresensiKita</div>
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
            <button class="save-btn" style="width:fit-content;margin-top:10px" onclick="simpanPengaturan()">Simpan Pengaturan</button>
        </div>
    </div>
</div>
