<div class="page-content page-anim" id="page-pengaturan" style="display:none">
    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:24px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Pengaturan Sistem PresensiKita</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Kelola seluruh konfigurasi sekolah, periode akademik, pengguna, jurnal, dan keamanan dalam satu halaman</div>
        </div>
    </div>

    {{-- ── Grid Utama 2 Kolom ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- ══ KOLOM KIRI ══ --}}
        <div style="display:grid;gap:20px">

            {{-- 1. Profil Sekolah --}}
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4 6 4v14"/><path d="M9 10a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v11H9V10z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Profil Sekolah</h3>
                        <div style="font-size:12px;color:#64748b">Identitas resmi dan informasi kontak instansi</div>
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
                            <input type="text" class="filter-input" id="set-npsn" value="{{ $npsn ?? '' }}" placeholder="NPSN Sekolah" style="width:100%;margin-top:4px">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#475569">Kepala Sekolah</label>
                            <input type="text" class="filter-input" id="set-kepsek" value="{{ $kepsek ?? '' }}" placeholder="Nama Kepsek" style="width:100%;margin-top:4px">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Alamat Lengkap</label>
                        <input type="text" class="filter-input" id="set-alamat" value="{{ $alamat ?? '' }}" placeholder="Alamat lengkap sekolah" style="width:100%;margin-top:4px">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#475569">Email Sekolah</label>
                            <input type="email" class="filter-input" id="set-email" value="{{ $emailSekolah ?? '' }}" placeholder="email@sekolah.sch.id" style="width:100%;margin-top:4px">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#475569">Telepon / Fax</label>
                            <input type="text" class="filter-input" id="set-telepon" value="{{ $teleponSekolah ?? '' }}" placeholder="Nomor telepon sekolah" style="width:100%;margin-top:4px">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Tahun Pelajaran & Semester --}}
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div style="width:40px;height:40px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Tahun Pelajaran &amp; Semester</h3>
                        <div style="font-size:12px;color:#64748b">Periode akademik aktif yang digunakan sistem</div>
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
                            <div style="font-size:13px;font-weight:700;color:#1e293b">Status Periode Academic</div>
                            <div style="font-size:12px;color:#64748b">Aktif untuk pencatatan absensi &amp; jurnal harian.</div>
                        </div>
                        <span class="badge badge-success" style="font-size:12px;padding:5px 12px">Aktif</span>
                    </div>
                </div>
            </div>

            {{-- 3. WhatsApp Gateway & Bot Notifikasi --}}
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#059669;flex-shrink:0">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">WhatsApp Bot Gateway</h3>
                            <div style="font-size:12px;color:#64748b">Kirim otomatis link izin &amp; dispensasi via WhatsApp</div>
                        </div>
                    </div>
                    <div>
                        @if(($waBotStatus['online'] ?? false))
                            <span class="badge badge-success" id="wa-bot-status-badge" style="font-size:11.5px;padding:4px 10px;display:inline-flex;align-items:center;gap:5px">
                                <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block"></span> Online
                            </span>
                        @else
                            <span class="badge badge-danger" id="wa-bot-status-badge" style="font-size:11.5px;padding:4px 10px;display:inline-flex;align-items:center;gap:5px">
                                <span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block"></span> Offline
                            </span>
                        @endif
                    </div>
                </div>

                <div style="display:grid;gap:14px">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none">
                            <input type="checkbox" id="set-wa-gateway-aktif" {{ ($waGatewayAktif ?? '1') === '1' ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#059669">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e293b">Aktifkan Notifikasi WhatsApp Otomatis</div>
                                <div style="font-size:12px;color:#64748b">Kirim link persetujuan langsung saat Guru Piket input surat.</div>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Endpoint Gateway Bot (Baileys / Node.js)</label>
                        <input type="text" class="filter-input" id="set-wa-endpoint" value="{{ $waGatewayEndpoint ?? 'http://127.0.0.1:3000/send-message' }}" placeholder="http://127.0.0.1:3000/send-message" style="width:100%;margin-top:4px">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569">Nomor WhatsApp Waka Kesiswaan (Dispen Siswa)</label>
                        <input type="text" class="filter-input" id="set-wa-waka-kesiswaan" value="{{ $waNomorWakaKesiswaan ?? '' }}" placeholder="Contoh: 081234567890 atau 6281234567890" style="width:100%;margin-top:4px">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#475569">Nomor WA Waka SDM (Izin Guru)</label>
                            <input type="text" class="filter-input" id="set-wa-waka-sdm" value="{{ $waNomorWakaSdm ?? '' }}" placeholder="081234567890" style="width:100%;margin-top:4px">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#475569">Nomor WA Kepala Sekolah (Izin Guru)</label>
                            <input type="text" class="filter-input" id="set-wa-kepsek" value="{{ $waNomorKepsek ?? '' }}" placeholder="081234567890" style="width:100%;margin-top:4px">
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;margin-top:4px">
                        <button type="button" class="btn-secondary" onclick="cekStatusBotWa()" style="flex:1;border-radius:8px;padding:8px 12px;font-size:12.5px;display:flex;align-items:center;justify-content:center;gap:6px">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Cek Koneksi Bot
                        </button>
                        <button type="button" class="btn-secondary" onclick="modalTestKirimWa()" style="flex:1;border-radius:8px;padding:8px 12px;font-size:12.5px;display:flex;align-items:center;justify-content:center;gap:6px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Test Kirim WA
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ KOLOM KANAN ══ --}}
        <div style="display:grid;gap:20px">

            {{-- 3. Pengaturan Jurnal --}}
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Pengaturan Jurnal Absensi</h3>
                        <div style="font-size:12px;color:#64748b">Aturan pengisian dan jam operasional input</div>
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
                        <label style="font-size:12px;font-weight:600;color:#475569">Batas Waktu Input Jurnal Harian</label>
                        <select class="filter-select" id="set-batas-waktu" style="width:100%;margin-top:4px">
                            <option value="16:00" {{ ($batasWaktuJurnal ?? '23:59') === '16:00' ? 'selected' : '' }}>Pukul 16:00 WIB (Akhir Jam Sekolah)</option>
                            <option value="18:00" {{ ($batasWaktuJurnal ?? '23:59') === '18:00' ? 'selected' : '' }}>Pukul 18:00 WIB (Sore hari)</option>
                            <option value="23:59" {{ ($batasWaktuJurnal ?? '23:59') === '23:59' ? 'selected' : '' }}>Pukul 23:59 WIB (Sampai Akhir Hari)</option>
                        </select>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none">
                            <input type="checkbox" id="set-izin-edit" {{ ($izinEditJurnal ?? '1') === '1' ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#2563eb">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e293b">Izinkan Edit Jurnal Setelah Disimpan</div>
                                <div style="font-size:12px;color:#64748b">Guru dapat memperbarui data jika terdapat susulan.</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 4. Pengguna, Kelas & Jurusan --}}
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div style="width:40px;height:40px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#7c3aed;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Pengguna, Kelas &amp; Jurusan</h3>
                        <div style="font-size:12px;color:#64748b">Ringkasan master data dan akses cepat</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(80px,1fr));gap:10px;margin-bottom:16px">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 8px;border-radius:10px;text-align:center">
                        <div style="font-size:20px;font-weight:800;color:#1e293b">{{ count($allGuru ?? []) }}</div>
                        <div style="font-size:11px;color:#64748b;font-weight:600;margin-top:2px">Guru</div>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 8px;border-radius:10px;text-align:center">
                        <div style="font-size:20px;font-weight:800;color:#1e293b">{{ count($allAdmin ?? []) }}</div>
                        <div style="font-size:11px;color:#64748b;font-weight:600;margin-top:2px">Admin</div>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 8px;border-radius:10px;text-align:center">
                        <div style="font-size:20px;font-weight:800;color:#1e293b">{{ count($allSiswa ?? []) }}</div>
                        <div style="font-size:11px;color:#64748b;font-weight:600;margin-top:2px">Siswa</div>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 8px;border-radius:10px;text-align:center">
                        <div style="font-size:20px;font-weight:800;color:#1e293b">{{ count($allKelas ?? []) }}</div>
                        <div style="font-size:11px;color:#64748b;font-weight:600;margin-top:2px">Kelas</div>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 8px;border-radius:10px;text-align:center">
                        <div style="font-size:20px;font-weight:800;color:#2563eb">{{ count($allJurusan ?? []) }}</div>
                        <div style="font-size:11px;color:#64748b;font-weight:600;margin-top:2px">Jurusan</div>
                    </div>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button class="btn-secondary" onclick="showPage('data-guru')" style="border-radius:8px;padding:8px 14px;font-size:12.5px">
                        Kelola Guru
                    </button>
                    <button class="btn-secondary" onclick="showPage('data-siswa')" style="border-radius:8px;padding:8px 14px;font-size:12.5px">
                        Kelola Siswa
                    </button>
                    <button class="btn-secondary" onclick="showPage('data-kelas')" style="border-radius:8px;padding:8px 14px;font-size:12.5px">
                        Kelola Kelas
                    </button>
                    <button class="btn-secondary" onclick="showPage('data-jurusan')" style="border-radius:8px;padding:8px 14px;font-size:12.5px">
                        Kelola Jurusan
                    </button>
                </div>
            </div>

            {{-- 5. Keamanan & Akses --}}
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div style="width:40px;height:40px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#ef4444;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Keamanan &amp; Sesi Akun</h3>
                        <div style="font-size:12px;color:#64748b">Status proteksi password &amp; otentikasi admin</div>
                    </div>
                </div>

                <div style="display:grid;gap:12px">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px;display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <div style="font-size:12.5px;font-weight:700;color:#1e293b">Enkripsi Kata Sandi</div>
                            <div style="font-size:11.5px;color:#64748b">Disimpan aman dengan Hash Bcrypt.</div>
                        </div>
                        <span class="badge badge-success" style="font-size:11.5px;padding:4px 10px">Bcrypt Active</span>
                    </div>
                    <div style="display:flex;gap:10px">
                        <button class="btn-primary" onclick="showPage('profil')" style="border-radius:10px;padding:9px 16px;font-size:12.5px;width:100%">
                            Ubah Password &amp; Profil Admin
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Tombol Simpan Utama ── --}}
    <div style="margin-top:24px;background:#fff;border:1px solid #e2e8f0;padding:16px 24px;border-radius:var(--radius);display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow)">
        <div>
            <div style="font-size:14px;font-weight:700;color:#1e293b">Simpan Seluruh Pengaturan</div>
            <div style="font-size:12px;color:#64748b">Perubahan akan langsung diterapkan ke sistem presensi digital.</div>
        </div>
        <button class="btn-primary" style="border-radius:10px;padding:11px 24px;font-size:14px;font-weight:700" onclick="simpanPengaturan()">
            Simpan Semua Pengaturan
        </button>
    </div>
</div>
