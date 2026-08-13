<div class="page-content page-anim" id="page-pengaturan" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Pengaturan Sistem PresensiKita</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Kelola konfigurasi sekolah, periode, pengguna, kelas, jurnal, dan keamanan</div>
        </div>
    </div>

    {{-- ── Sub-Tab Navigation Bar ── --}}
    <div class="setting-tabs-bar" style="display:flex;gap:8px;margin-bottom:20px;overflow-x:auto;padding-bottom:4px;border-bottom:1px solid #e2e8f0">
        <button class="setting-tab-btn active" onclick="switchSettingTab('profil-sekolah', this)">
            <span></span> Profil Sekolah
        </button>
        <button class="setting-tab-btn" onclick="switchSettingTab('tahun-pelajaran', this)">
            <span></span> Tahun Pelajaran &amp; Semester
        </button>
        <button class="setting-tab-btn" onclick="switchSettingTab('pengguna', this)">
            <span></span> Pengguna
        </button>
        <button class="setting-tab-btn" onclick="switchSettingTab('kelas-jurusan', this)">
            <span></span> Kelas &amp; Jurusan
        </button>
        <button class="setting-tab-btn" onclick="switchSettingTab('pengaturan-jurnal', this)">
            <span></span> Pengaturan Jurnal
        </button>
        <button class="setting-tab-btn" onclick="switchSettingTab('keamanan', this)">
            <span></span> Keamanan
        </button>
    </div>

    {{-- ── Tab 1: Profil Sekolah ── --}}
    <div class="setting-section-card" id="setting-sec-profil-sekolah">
        <div class="card" style="max-width:760px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:38px;height:38px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">🏫</div>
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:#1e293b">Profil Sekolah</h3>
                    <div style="font-size:12px;color:#64748b">Informasi dasar dan identitas resmi sekolah</div>
                </div>
            </div>

            <div style="display:grid;gap:14px">
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Nama Sekolah</label>
                    <input type="text" class="filter-input" id="set-nama-sekolah" value="{{ $namaSekolah ?? 'SMK NEGERI 1 Boyolangu' }}" style="width:100%;margin-top:4px">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">NPSN</label>
                        <input type="text" class="filter-input" id="set-npsn" value="20515123" placeholder="Contoh: 20515123" style="width:100%;margin-top:4px">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Kepala Sekolah</label>
                        <input type="text" class="filter-input" id="set-kepsek" value="Drs. H. Mahrus, M.Pd." placeholder="Nama Kepala Sekolah" style="width:100%;margin-top:4px">
                    </div>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Alamat Lengkap</label>
                    <input type="text" class="filter-input" id="set-alamat" value="Jl. Ki Mangunsarkoro No. 1, Boyolangu, Tulungagung" style="width:100%;margin-top:4px">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Email Sekolah</label>
                        <input type="email" class="filter-input" id="set-email" value="info@smkn1boyolangu.sch.id" style="width:100%;margin-top:4px">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Telepon / Fax</label>
                        <input type="text" class="filter-input" id="set-telepon" value="(0355) 321456" style="width:100%;margin-top:4px">
                    </div>
                </div>
                <button class="btn-primary" style="border-radius:10px;padding:10px 20px;font-size:13.5px;width:fit-content;margin-top:10px" onclick="simpanPengaturan()">
                    Simpan Profil Sekolah
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tab 2: Tahun Pelajaran & Semester ── --}}
    <div class="setting-section-card" id="setting-sec-tahun-pelajaran" style="display:none">
        <div class="card" style="max-width:760px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:38px;height:38px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">📅</div>
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:#1e293b">Tahun Pelajaran &amp; Semester</h3>
                    <div style="font-size:12px;color:#64748b">Pengaturan periode aktif akademik sekolah</div>
                </div>
            </div>

            <div style="display:grid;gap:14px">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Tahun Ajaran</label>
                        <input type="text" class="filter-input" id="set-tahun-ajaran" value="{{ $tahunAjaran->tahun_ajaran ?? '2026/2027' }}" placeholder="Contoh: 2026/2027" style="width:100%;margin-top:4px">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Semester</label>
                        <select class="filter-select" id="set-semester" style="width:100%;margin-top:4px">
                            <option value="Ganjil" {{ ($tahunAjaran->semester ?? '') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap"  {{ ($tahunAjaran->semester ?? '') === 'Genap'  ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 16px;border-radius:10px;display:flex;align-items:center;justify-content:space-between">
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b">Status Periode Saat Ini</div>
                        <div style="font-size:12px;color:#64748b">Periode ini aktif digunakan untuk seluruh absensi &amp; jurnal harian.</div>
                    </div>
                    <span class="badge badge-success" style="font-size:12px;padding:5px 12px">Aktif Digunakan</span>
                </div>
                <button class="btn-primary" style="border-radius:10px;padding:10px 20px;font-size:13.5px;width:fit-content;margin-top:10px" onclick="simpanPengaturan()">
                    Simpan Tahun Pelajaran
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tab 3: Pengguna ── --}}
    <div class="setting-section-card" id="setting-sec-pengguna" style="display:none">
        <div class="card" style="max-width:760px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:38px;height:38px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">👥</div>
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:#1e293b">Ringkasan Pengguna Sistem</h3>
                    <div style="font-size:12px;color:#64748b">Informasi akun terdaftar &amp; hak akses pengguna</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px;text-align:center">
                    <div style="font-size:24px;font-weight:800;color:#1e293b">{{ count($allGuru ?? []) }}</div>
                    <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px">Total Guru Pengajar</div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px;text-align:center">
                    <div style="font-size:24px;font-weight:800;color:#1e293b">{{ count($allAdmin ?? []) }}</div>
                    <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px">Administrator</div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px;text-align:center">
                    <div style="font-size:24px;font-weight:800;color:#1e293b">{{ count($allSiswa ?? []) }}</div>
                    <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px">Siswa Terdaftar</div>
                </div>
            </div>

            <div style="display:flex;gap:10px">
                <button class="btn-primary" onclick="showPage('data-guru')" style="border-radius:10px;padding:10px 18px;font-size:13px">
                    Kelola Data Guru
                </button>
                <button class="btn-secondary" onclick="showPage('data-siswa')" style="border-radius:10px;padding:10px 18px;font-size:13px">
                    Kelola Data Siswa
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tab 4: Kelas & Jurusan ── --}}
    <div class="setting-section-card" id="setting-sec-kelas-jurusan" style="display:none">
        <div class="card" style="max-width:760px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:38px;height:38px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">🏫</div>
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:#1e293b">Kelas &amp; Jurusan</h3>
                    <div style="font-size:12px;color:#64748b">Ringkasan rombel dan konsentrasi keahlian</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:18px">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px">
                    <div style="font-size:12px;color:#64748b;font-weight:600">Total Kelas / Rombel</div>
                    <div style="font-size:24px;font-weight:800;color:#1e293b;margin-top:4px">{{ count($allKelas ?? []) }} Kelas</div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px">
                    <div style="font-size:12px;color:#64748b;font-weight:600">Jurusan Aktif</div>
                    <div style="font-size:13px;font-weight:700;color:#1e293b;margin-top:6px;display:flex;gap:6px;flex-wrap:wrap">
                        <span class="badge badge-info">AK</span>
                        <span class="badge badge-info">BD</span>
                        <span class="badge badge-info">TKJ</span>
                        <span class="badge badge-info">RPL</span>
                    </div>
                </div>
            </div>

            <button class="btn-primary" onclick="showPage('data-kelas')" style="border-radius:10px;padding:10px 18px;font-size:13px">
                Buka Pengelolaan Kelas &amp; Wali Kelas
            </button>
        </div>
    </div>

    {{-- ── Tab 5: Pengaturan Jurnal ── --}}
    <div class="setting-section-card" id="setting-sec-pengaturan-jurnal" style="display:none">
        <div class="card" style="max-width:760px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:38px;height:38px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">📖</div>
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:#1e293b">Pengaturan Jurnal Absensi</h3>
                    <div style="font-size:12px;color:#64748b">Aturan pengisian dan batas waktu input presensi harian</div>
                </div>
            </div>

            <div style="display:grid;gap:14px">
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Sistem &amp; Mode Absensi</label>
                    <select class="filter-select" id="set-sistem-absensi" style="width:100%;margin-top:4px">
                        <option value="Absensi Realtime & Otomatis Rekap" {{ ($sistemAbsensi ?? '') === 'Absensi Realtime & Otomatis Rekap' ? 'selected' : '' }}>Absensi Realtime &amp; Otomatis Rekap</option>
                        <option value="Absensi Manual" {{ ($sistemAbsensi ?? '') === 'Absensi Manual' ? 'selected' : '' }}>Absensi Manual</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Batas Waktu Pengisian Jurnal Harian</label>
                    <select class="filter-select" id="set-batas-waktu" style="width:100%;margin-top:4px">
                        <option value="16:00">Pukul 16:00 WIB (Akhir Jam Sekolah)</option>
                        <option value="18:00">Pukul 18:00 WIB (Sore hari)</option>
                        <option value="23:59" selected>Pukul 23:59 WIB (Sampai Akhir Hari)</option>
                    </select>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:10px">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none">
                        <input type="checkbox" checked style="width:16px;height:16px;accent-color:#2563eb">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#1e293b">Izinkan Edit Jurnal Setelah Disimpan</div>
                            <div style="font-size:12px;color:#64748b">Guru pengajar dapat memperbarui status kehadiran jika ada susulan/perubahan.</div>
                        </div>
                    </label>
                </div>
                <button class="btn-primary" style="border-radius:10px;padding:10px 20px;font-size:13.5px;width:fit-content;margin-top:10px" onclick="simpanPengaturan()">
                    Simpan Pengaturan Jurnal
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tab 6: Keamanan ── --}}
    <div class="setting-section-card" id="setting-sec-keamanan" style="display:none">
        <div class="card" style="max-width:760px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <div style="width:38px;height:38px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">🔐</div>
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:#1e293b">Keamanan &amp; Sesi Akun</h3>
                    <div style="font-size:12px;color:#64748b">Proteksi akses, enkripsi kata sandi &amp; sesi login admin</div>
                </div>
            </div>

            <div style="display:grid;gap:14px">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:10px;display:flex;align-items:center;justify-content:space-between">
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b">Enkripsi Kata Sandi</div>
                        <div style="font-size:12px;color:#64748b">Seluruh password disimpan dengan algoritma Hash Bcrypt aman.</div>
                    </div>
                    <span class="badge badge-success" style="font-size:12px;padding:5px 12px">Bcrypt Active</span>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:10px;display:flex;align-items:center;justify-content:space-between">
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b">Sesi Akses Admin</div>
                        <div style="font-size:12px;color:#64748b">Sesi login Anda saat ini terlindungi dengan middleware Auth Admin.</div>
                    </div>
                    <span class="badge badge-info" style="font-size:12px;padding:5px 12px">Terautentikasi</span>
                </div>
                <div style="display:flex;gap:10px;margin-top:6px">
                    <button class="btn-primary" onclick="showPage('profil')" style="border-radius:10px;padding:10px 18px;font-size:13px">
                        Ubah Password Admin di Profil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.setting-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
    font-family: inherit;
}
.setting-tab-btn:hover {
    background: #f1f5f9;
    color: #1e293b;
}
.setting-tab-btn.active {
    background: #1a3268;
    color: #ffffff;
    border-color: #1a3268;
    box-shadow: 0 4px 12px rgba(26, 50, 104, 0.25);
}
</style>

<script>
function switchSettingTab(tabId, btn) {
    document.querySelectorAll('.setting-section-card').forEach(card => card.style.display = 'none');
    document.querySelectorAll('.setting-tab-btn').forEach(b => b.classList.remove('active'));
    
    const target = document.getElementById('setting-sec-' + tabId);
    if(target) target.style.display = 'block';
    if(btn) btn.classList.add('active');
}
</script>
