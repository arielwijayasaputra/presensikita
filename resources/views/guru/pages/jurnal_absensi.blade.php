<div class="page-content page-anim" id="page-jurnal-absensi" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Jurnal &amp; Absensi</div>
            <div class="page-subtitle">Isi jurnal mengajar dan tandai kehadiran siswa dalam satu alur.</div>
        </div>
    </div>

    @if($jadwalMengajarHariIni->isEmpty())
        <div id="jadwal-status-alert" class="alert-card" style="background:#fff7ed;border-color:#fed7aa;margin-bottom:16px"><div class="alert-text"><p>Belum ada jadwal mengajar hari ini</p><span>Anda tidak memiliki jadwal mengajar yang terjadwal untuk hari ini.</span></div></div>
    @endif

    <div id="jurnal-form-card" class="card" style="padding:24px;{{ $jadwalMengajarHariIni->isEmpty() ? 'opacity:.6' : '' }}">
        <div class="card-heading" style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
            <span>Form Jurnal Mengajar</span>
            @if($jadwalGuruAktif->isNotEmpty())
                <span class="badge badge-success" style="font-size:12px;padding:4px 10px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                    <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>
                    Sesi Aktif: {{ $jadwalGuruAktif->first()->nama_kelas }} (Jam ke-{{ $jadwalGuruAktif->first()->jam_ke >= 100 ? $jadwalGuruAktif->first()->jam_ke - 100 : $jadwalGuruAktif->first()->jam_ke }})
                </span>
            @endif
        </div>
        <div class="jurnal-form-grid">
            <div class="form-field">
                <label for="pilih-kelas">Pilih Kelas</label>
                <select id="pilih-kelas" class="form-select" onchange="loadSiswaByKelas(this.value)" {{ $jadwalMengajarHariIni->isEmpty() ? 'disabled' : '' }}>
                    @foreach($kelases as $k)
                    <option value="{{ $k->id_kelas }}" {{ (isset($selectedKelas->id_kelas) && $selectedKelas->id_kelas == $k->id_kelas) ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="input-tanggal">Tanggal Pelaksanaan</label>
                <input type="date" id="input-tanggal" class="form-input" value="{{ date('Y-m-d') }}" readonly>
            </div>
            <div class="form-field">
                <label for="input-materi">Materi Pembelajaran</label>
                <input type="text" id="input-materi" class="form-input" placeholder="Tuliskan materi pembelajaran hari ini..." {{ $jadwalMengajarHariIni->isEmpty() ? 'disabled' : '' }}>
            </div>
        </div>

        {{-- ── Komponen Foto Selfie Guru Per Jadwal/Kelas ── --}}
        <div id="selfie-section" style="margin-top:16px;padding:18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px">
                <div style="display:flex;align-items:center;gap:8px">
                    <div style="width:34px;height:34px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#1e293b">Foto Selfie Mengajar di Kelas</div>
                        <div style="font-size:12px;color:#64748b">Wajib diambil 1 kali di awal pembelajaran saat mengajar di kelas ini.</div>
                    </div>
                </div>
                <div id="selfie-badge-container">
                    <span id="selfie-status-badge" class="badge" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;padding:5px 12px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Belum Ambil Foto
                    </span>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:16px;align-items:start">
                <!-- Video Camera Box -->
                <div id="camera-box" style="position:relative;background:#0f172a;border-radius:10px;overflow:hidden;aspect-ratio:4/3;max-height:280px;display:flex;align-items:center;justify-content:center;box-shadow:inset 0 0 10px rgba(0,0,0,0.5)">
                    <video id="selfie-video" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;transform:scaleX(-1);display:none;"></video>
                    
                    <div id="camera-placeholder" style="text-align:center;color:#94a3b8;padding:20px">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 8px;display:block"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <div style="font-size:13px;font-weight:600">Kamera Belum Aktif</div>
                        <div style="font-size:11.5px;color:#64748b;margin-top:2px">Klik "Buka Kamera" untuk mengaktifkan</div>
                    </div>

                    <div id="camera-overlay-controls" style="position:absolute;bottom:10px;left:0;right:0;display:none;justify-content:center;gap:10px;z-index:5">
                        <button type="button" id="btn-snap-photo" onclick="snapSelfiePhoto()" style="background:#22c55e;color:#fff;border:none;padding:8px 18px;border-radius:20px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:pointer;box-shadow:0 4px 12px rgba(34,197,94,0.4)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                            Ambil Foto
                        </button>
                    </div>
                </div>

                <!-- Preview Box & Actions -->
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div id="preview-box" style="position:relative;background:#f1f5f9;border:2px dashed #cbd5e1;border-radius:10px;overflow:hidden;aspect-ratio:4/3;max-height:280px;display:flex;align-items:center;justify-content:center">
                        <img id="selfie-preview" src="" alt="Preview Selfie" style="width:100%;height:100%;object-fit:cover;display:none;">
                        <div id="preview-placeholder" style="text-align:center;color:#94a3b8;padding:20px">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 8px;display:block"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <div style="font-size:12.5px;font-weight:600">Hasil Foto Selfie</div>
                            <div style="font-size:11px;color:#94a3b8">Foto selfie mengajar akan tampil di sini</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                        <button type="button" id="btn-start-camera" class="btn-primary" onclick="startSelfieCamera()" style="padding:8px 14px;font-size:12.5px;font-weight:700;border-radius:8px;display:inline-flex;align-items:center;gap:6px" {{ $jadwalMengajarHariIni->isEmpty() ? 'disabled' : '' }}>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            Buka Kamera
                        </button>
                        <button type="button" id="btn-retake-photo" class="btn-secondary" onclick="retakeSelfiePhoto()" style="padding:8px 14px;font-size:12.5px;font-weight:700;border-radius:8px;display:none;align-items:center;gap:6px">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Ambil Ulang
                        </button>
                        <label for="input-file-selfie" class="btn-secondary" style="padding:8px 14px;font-size:12.5px;font-weight:600;border-radius:8px;display:inline-flex;align-items:center;gap:6px;cursor:pointer;margin:0;{{ $jadwalMengajarHariIni->isEmpty() ? 'opacity:.6;pointer-events:none;' : '' }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Upload / File
                        </label>
                        <input type="file" id="input-file-selfie" accept="image/*" capture="user" style="display:none" onchange="handleSelfieFileSelect(this)">
                        <input type="hidden" id="input-foto-selfie" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card" style="margin-top:16px">
        <div class="absensi-toolbar" style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #e2e8f0">
            <div>
                <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0" id="guru-absensi-subtitle">Daftar Absensi Siswa - {{ $selectedKelas->nama_kelas }}</h3>
                <p style="font-size:12px;color:#64748b;margin-top:2px">Tandai status kehadiran setiap siswa di bawah ini</p>
            </div>
            <div class="tandai-row">
                <input type="text" class="form-input search-input" placeholder="Cari nama siswa..." onkeyup="filterSiswa(this.value)" style="width:220px">
                <button class="btn-tandai green" onclick="tandaiSemua('H')" {{ $jadwalMengajarHariIni->isEmpty() ? 'disabled' : '' }}>Tandai Semua Hadir</button>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">No.</th>
                        <th style="width:140px;">NISN</th>
                        <th>Nama Siswa</th>
                        <th class="td-status" style="width:60px;"><div class="status-header"><span class="status-dot-c green"></span>H</div></th>
                        <th class="td-status" style="width:60px;"><div class="status-header"><span class="status-dot-c yellow"></span>S</div></th>
                        <th class="td-status" style="width:60px;"><div class="status-header"><span class="status-dot-c blue"></span>I</div></th>
                        <th class="td-status" style="width:60px;"><div class="status-header"><span class="status-dot-c" style="background:#06b6d4"></span>D</div></th>
                        <th class="td-status" style="width:60px;"><div class="status-header"><span class="status-dot-c red"></span>A</div></th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="siswa-tbody">
                    <!-- Populated dynamically by app.js -->
                </tbody>
            </table>
        </div>

        <div class="rekap-summary-bar">
            <div class="rekap-chips">
                <span class="rekap-label">Rekap:</span>
                <div class="rekap-chip hadir">Hadir: <span id="rekap-hadir">0</span></div>
                <div class="rekap-chip sakit">Sakit: <span id="rekap-sakit">0</span></div>
                <div class="rekap-chip izin">Izin: <span id="rekap-izin">0</span></div>
                <div class="rekap-chip dispen" style="background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;font-weight:700;padding:4px 10px;border-radius:20px;font-size:12px;display:inline-flex;align-items:center;gap:4px">Dispen: <span id="rekap-dispen">0</span></div>
                <div class="rekap-chip alpa">Alpa: <span id="rekap-alpa">0</span></div>
            </div>
            <button id="btn-submit-jurnal" class="btn-submit-jurnal" onclick="submitAbsensi()" {{ $jadwalMengajarHariIni->isEmpty() ? 'disabled' : '' }}>Simpan Jurnal &amp; Absensi</button>
        </div>
    </div>
</div>
