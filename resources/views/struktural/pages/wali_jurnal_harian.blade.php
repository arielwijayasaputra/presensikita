<div class="page-content page-anim" id="page-wali-jurnal-harian" style="display:none">

    @php
        $namaKelasAktif = $waliKelasObj->nama_kelas ?? session('auth_nama_kelas', 'Kelas');
    @endphp

    <!-- ══ HEADER JURNAL HARIAN REAL TIME ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Jurnal Harian Kelas</h1>
                <span class="badge badge-primary" style="font-size:13px; padding:6px 12px; border-radius:8px">{{ $namaKelasAktif }}</span>
                <span class="badge badge-success" style="font-size:12px; padding:6px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:6px">
                    <span style="width:8px; height:8px; background:#22c55e; border-radius:50%; display:inline-block; animation:pulse 1.5s infinite"></span>
                    Update Otomatis Hari Ini
                </span>
            </div>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">
                Pantau pengisian jurnal mengajar seluruh guru mata pelajaran di kelas <strong>{{ $namaKelasAktif }}</strong> pada <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>
            </p>
        </div>

        <div>
            <button type="button" onclick="location.reload()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                <span>Refresh Jurnal</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN JURNAL HARIAN ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px; border-left:4px solid #3b82f6">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Sesi Mengajar</div>
            <div style="font-size:26px; font-weight:800; color:#1e293b; margin-top:4px">{{ $waliStatsJurnalHariIni['total_jam'] ?? 0 }} Sesi</div>
            <div style="font-size:11.5px; color:#94a3b8; margin-top:2px">Jadwal Kelas Hari Ini</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #10b981">
            <div style="font-size:11.5px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Jurnal Terisi</div>
            <div style="font-size:26px; font-weight:800; color:#065f46; margin-top:4px">{{ $waliStatsJurnalHariIni['terisi'] ?? 0 }} Sesi</div>
            <div style="font-size:11.5px; color:#10b981; margin-top:2px">{{ $waliStatsJurnalHariIni['pct_terisi'] ?? 0 }}% Sesi Sudah Diisi Guru</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #8b5cf6">
            <div style="font-size:11.5px; font-weight:700; color:#6d28d9; text-transform:uppercase; letter-spacing:0.5px">Guru Mengajar Hari Ini</div>
            <div style="font-size:26px; font-weight:800; color:#5b21b6; margin-top:4px">{{ $waliStatsJurnalHariIni['guru_count'] ?? 0 }} Guru</div>
            <div style="font-size:11.5px; color:#8b5cf6; margin-top:2px">Guru Mapel Berbeda</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #f59e0b">
            <div style="font-size:11.5px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px">Sesi Belum Diisi</div>
            @php
                $belumIsiCount = max(0, ($waliStatsJurnalHariIni['total_jam'] ?? 0) - ($waliStatsJurnalHariIni['terisi'] ?? 0));
            @endphp
            <div style="font-size:26px; font-weight:800; color:#78350f; margin-top:4px">{{ $belumIsiCount }} Sesi</div>
            <div style="font-size:11.5px; color:#f59e0b; margin-top:2px">Menunggu Input Guru</div>
        </div>
    </div>

    <!-- ══ TABEL JURNAL MENGAJAR HARIAN KELAS ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Daftar Pembelajaran &amp; Jurnal Guru Hari Ini
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Riwayat real-time pengisian materi dan presensi per jam pelajaran di kelas {{ $namaKelasAktif }}
                </div>
            </div>

            <div style="position:relative; width:240px">
                <input type="text" id="search-wali-jurnal-harian" onkeyup="filterTable('search-wali-jurnal-harian', 'table-wali-jurnal-harian')" placeholder="Cari mapel / guru / materi..." class="filter-input" style="width:100%; padding:6px 12px 6px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:8px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-jurnal-harian" style="min-width:900px">
                <thead>
                    <tr>
                        <th style="width:70px">Jam Ke-</th>
                        <th style="width:130px">Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th style="text-align:center; width:130px">Status Jurnal</th>
                        <th>Materi Pembelajaran</th>
                        <th style="text-align:center; width:110px">Hadir Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliJadwalHariIni as $j)
                        <tr>
                            <td style="text-align:center">
                                <span class="badge badge-info" style="font-weight:700; font-size:12.5px">
                                    {{ $j->jam_ke >= 100 ? $j->jam_ke - 100 : $j->jam_ke }}
                                </span>
                            </td>
                            <td style="font-family:monospace; font-size:12.5px; color:#475569">
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </td>
                            <td><strong style="color:#0f172a">{{ $j->nama_mapel }}</strong></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px">
                                    <div style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden">
                                        @if(!empty($j->foto_profil))
                                            <img src="{{ Storage::disk('public')->url($j->foto_profil) }}" style="width:100%; height:100%; object-fit:cover">
                                        @else
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        @endif
                                    </div>
                                    <span style="font-size:13px; font-weight:600; color:#334155">{{ $j->nama_guru }}</span>
                                </div>
                            </td>
                            <td style="text-align:center">
                                @if($j->jurnal)
                                    <span class="badge badge-success" style="font-size:11.5px; padding:4px 8px">Sudah Diisi</span>
                                @elseif($j->status_pembelajaran === 'Sedang Berlangsung')
                                    <span class="badge badge-warning" style="font-size:11.5px; padding:4px 8px">Berlangsung</span>
                                @else
                                    <span class="badge badge-secondary" style="font-size:11.5px; padding:4px 8px">Belum Diisi</span>
                                @endif
                            </td>
                            <td style="font-size:12.5px; color:#475569">
                                @if($j->jurnal && !empty($j->jurnal->materi))
                                    <strong style="color:#1e293b">{{ $j->jurnal->materi }}</strong>
                                    <div style="font-size:11px; color:#94a3b8; margin-top:2px">Waktu Input: {{ date('H:i', strtotime($j->jurnal->waktu_input)) }} WIB</div>
                                @else
                                    <span style="color:#94a3b8; font-style:italic">Materi belum diinput guru</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                @if($j->jurnal)
                                    <span class="badge badge-primary" style="font-size:12px">
                                        {{ $j->jurnal->jumlah_hadir }} Siswa
                                    </span>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#64748b; padding:24px">
                                Tidak ada jadwal mengajar di kelas ini untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
