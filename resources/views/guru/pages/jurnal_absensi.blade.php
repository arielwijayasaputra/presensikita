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
                <div class="rekap-chip alpa">Alpa: <span id="rekap-alpa">0</span></div>
            </div>
            <button id="btn-submit-jurnal" class="btn-submit-jurnal" onclick="submitAbsensi()" {{ $jadwalMengajarHariIni->isEmpty() ? 'disabled' : '' }}>Simpan Jurnal &amp; Absensi</button>
        </div>
    </div>
</div>
