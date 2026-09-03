<div class="page-content page-anim" id="page-jadwal-mengajar" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Jadwal Mengajar Hari Ini</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">
                Jadwal mengajar khusus hari <strong>{{ $hariIni ?? 'Hari Ini' }}</strong>, {{ now()->translatedFormat('d F Y') }}.
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ $hariIni ?? 'Hari Ini' }}
            </span>
        </div>
    </div>

    @php
        $totalJamHariIni = $jadwalMengajarHariIni->count();
        $totalKelasDiajar = $jadwalMengajarHariIni->pluck('nama_kelas')->unique()->count();
        $daftarMapel = $jadwalMengajarHariIni->pluck('nama_mapel')->unique()->filter()->values();
        $currentTime = now()->format('H:i:s');
        $activeJadwal = $jadwalMengajarHariIni->first(function($j) use ($currentTime) {
            return $currentTime >= $j->jam_mulai && $currentTime <= $j->jam_selesai;
        });
    @endphp

    {{-- ── Stat Cards Ringkasan Jadwal Hari Ini ── --}}
    <div class="stat-cards" style="margin-bottom:22px">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Jam Mengajar</div>
                <div class="stat-value">{{ $totalJamHariIni }} <span style="font-size:14px;font-weight:500;color:#64748b">Jam</span></div>
                <div class="stat-pct">sesi hari {{ strtolower($hariIni ?? 'ini') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Kelas Diajar</div>
                <div class="stat-value">{{ $totalKelasDiajar }} <span style="font-size:14px;font-weight:500;color:#64748b">Kelas</span></div>
                <div class="stat-pct">{{ $jadwalMengajarHariIni->pluck('nama_kelas')->unique()->join(', ') ?: 'Tidak ada kelas' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon cyan">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Mata Pelajaran</div>
                <div class="stat-value" style="font-size:16px;line-height:1.3;font-weight:700">{{ $daftarMapel->isNotEmpty() ? $daftarMapel->first() : '-' }}</div>
                <div class="stat-pct">{{ $daftarMapel->count() > 1 ? '+' . ($daftarMapel->count() - 1) . ' mapel lainnya' : 'mapel diajar hari ini' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon {{ $activeJadwal ? 'green' : 'yellow' }}">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="stat-label">Status Sesi Saat Ini</div>
                <div class="stat-value" style="font-size:15px;font-weight:800;color:{{ $activeJadwal ? '#15803d' : '#475569' }}">
                    {{ $activeJadwal ? 'Sedang Mengajar' : 'Jam Kosong / Luar Sesi' }}
                </div>
                <div class="stat-pct">{{ $activeJadwal ? $activeJadwal->nama_kelas . ' (Jam ke-' . ($activeJadwal->jam_ke >= 100 ? $activeJadwal->jam_ke - 100 : $activeJadwal->jam_ke) . ')' : 'tidak ada sesi aktif' }}</div>
            </div>
        </div>
    </div>

    {{-- ── Tabel Jadwal Mengajar Hari Ini ── --}}
    <div class="card" style="padding:24px">
        <div class="card-header" style="margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
            <div>
                <div class="card-title" style="font-size:16px;font-weight:800;color:#0f172a">Daftar Jadwal Mengajar — {{ $hariIni ?? 'Hari Ini' }}</div>
                <div style="font-size:12.5px;color:#64748b;margin-top:3px">
                    Urutan jam mengajar otomatis disesuaikan dengan pengaturan jam sekolah.
                </div>
            </div>
            <button type="button" class="btn-secondary" onclick="showPage('jurnal-absensi')" style="border-radius:8px;padding:8px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Buka Jurnal &amp; Absensi
            </button>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:750px">
                <thead>
                    <tr>
                        <th style="width:90px;text-align:center">Jam Ke-</th>
                        <th style="width:160px">Rentang Waktu</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th style="width:170px;text-align:center">Status Sesi</th>
                        <th style="width:150px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalMengajarHariIni as $idx => $jadwal)
                    @php
                        $jamTampil = $jadwal->jam_ke >= 100 ? $jadwal->jam_ke - 100 : $jadwal->jam_ke;
                        $nowStr = now()->format('H:i:s');
                        $isSedang = ($nowStr >= $jadwal->jam_mulai && $nowStr <= $jadwal->jam_selesai);
                        $isBelum = ($nowStr < $jadwal->jam_mulai);
                        $isSelesai = ($nowStr > $jadwal->jam_selesai);
                    @endphp
                    <tr class="jadwal-row-item" data-mulai="{{ $jadwal->jam_mulai }}" data-selesai="{{ $jadwal->jam_selesai }}" data-kelas="{{ $jadwal->id_kelas }}" style="{{ $isSedang ? 'background:#f0fdf4;' : '' }}">
                        <td style="text-align:center">
                            <span class="badge {{ $isSedang ? 'badge-success' : 'badge-info' }}" style="font-weight:800;font-size:12.5px;padding:4px 10px">
                                Ke-{{ $jamTampil }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:#334155">
                            <div style="display:flex;align-items:center;gap:6px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                            </div>
                        </td>
                        <td>
                            <strong style="color:#1e3a8a;font-size:13.5px">{{ $jadwal->nama_kelas }}</strong>
                        </td>
                        <td style="color:#1e293b;font-weight:600">
                            {{ $jadwal->nama_mapel }}
                        </td>
                        <td style="text-align:center" class="status-cell">
                            @if($isSedang)
                                <span class="badge badge-success" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:700">
                                    <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>
                                    Sedang Berlangsung
                                </span>
                            @elseif($isBelum)
                                <span class="badge badge-info" style="padding:5px 10px;font-weight:600;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">
                                    Belum Dimulai
                                </span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;padding:5px 10px;font-weight:600">
                                    Selesai
                                </span>
                            @endif
                        </td>
                        <td style="text-align:center" class="action-cell">
                            @if($isBelum)
                                <button type="button" class="btn-disabled-jurnal" disabled style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#e2e8f0;color:#94a3b8;border:1px solid #cbd5e1;cursor:not-allowed;box-shadow:none" title="Belum waktunya, jam pelajaran belum dimulai">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    Isi Jurnal
                                </button>
                            @elseif($isSedang)
                                <button type="button" class="btn-primary btn-isi-jurnal" onclick="bukaJurnalKelas('{{ $jadwal->id_kelas }}')" style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#16a34a;box-shadow:0 2px 6px rgba(22,163,74,0.25)" title="Isi jurnal sekarang">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    Isi Jurnal
                                </button>
                            @else
                                <button type="button" class="btn-secondary btn-isi-jurnal" onclick="bukaJurnalKelas('{{ $jadwal->id_kelas }}')" style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px" title="Isi / lihat jurnal kelas ini">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                    Isi Jurnal
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px 20px">
                            <div style="max-width:360px;margin:0 auto;color:#64748b">
                                <div style="width:52px;height:52px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#94a3b8">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <h4 style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:4px">Tidak Ada Jadwal Mengajar Hari Ini</h4>
                                <p style="font-size:12.5px;color:#64748b;margin:0">Anda tidak memiliki jam pelajaran mengajar yang terjadwal untuk hari {{ $hariIni ?? 'ini' }}.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
