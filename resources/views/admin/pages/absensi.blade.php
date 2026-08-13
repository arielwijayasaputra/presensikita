<div class="page-content page-anim" id="page-absensi-harian" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Data Absensi Harian</div>
            <div class="page-subtitle" id="ah-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Informasi Data Absensi Kelas {{ $selectedKelas->nama_kelas }}</div>
        </div>
    </div>
    <div class="filter-bar">
        <div class="filter-group">
            <label>Pilih Kelas</label>
            <select class="filter-select" id="pilih-kelas" onchange="loadSiswaByKelas(this.value)">
                @foreach($kelases as $k)
                    <option value="{{ $k->id_kelas }}" {{ $k->id_kelas == $selectedKelas->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Tanggal</label>
            <input type="date" class="filter-select" id="input-tanggal" value="{{ date('Y-m-d') }}">
        </div>
        <div class="filter-group">
            <label>Cari Siswa</label>
            <input type="text" class="filter-input" placeholder="Ketik nama atau NISN..." oninput="filterSiswa(this.value)">
        </div>
        <div class="filter-hint filter-hint-info">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Hanya dapat diisi oleh guru</span>
        </div>
    </div>
    <div class="table-card">
        <table id="absensi-table">
            <thead>
                <tr>
                    <th style="width:44px">No.</th>
                    <th>NISN</th>
                    <th>Nama Siswa</th>
                    <th style="text-align:center;width:80px"><div class="status-header"><span class="status-dot-c green"></span>Hadir</div></th>
                    <th style="text-align:center;width:80px"><div class="status-header"><span class="status-dot-c yellow"></span>Sakit</div></th>
                    <th style="text-align:center;width:80px"><div class="status-header"><span class="status-dot-c blue"></span>Izin</div></th>
                    <th style="text-align:center;width:80px"><div class="status-header"><span class="status-dot-c red"></span>Alpa</div></th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody id="siswa-tbody"></tbody>
        </table>
        <div class="table-footer">
            <span class="rekap-label">Rekap Kehadiran</span>
            <div class="rekap-item green"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/></svg>Hadir <strong id="rekap-hadir">0</strong></div>
            <div class="rekap-item yellow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>Sakit <strong id="rekap-sakit">0</strong></div>
            <div class="rekap-item blue"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>Izin <strong id="rekap-izin">0</strong></div>
            <div class="rekap-item red"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>Alpa <strong id="rekap-alpa">0</strong></div>
            <div style="margin-left: auto; display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1d4ed8; padding: 7px 14px; border-radius: 99px; border: 1px solid #bfdbfe; font-size: 12px; font-weight: 600;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Pengisian & Perubahan Absensi Adalah Wewenang Guru
            </div>
        </div>
    </div>
</div>
