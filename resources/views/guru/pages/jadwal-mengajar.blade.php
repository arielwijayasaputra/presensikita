@php
    // ── Siapkan data untuk grid tabel guru ──
    $hariUrutan = ['Senin','Selasa','Rabu','Kamis','Jumat'];

    // Ambil semua jadwal guru ini (semua hari) dari DB
    $guruId = session('auth_guru_id');
    $tahunAjaran = App\Models\TahunAjaran::where('is_aktif', 1)->first() ?? App\Models\TahunAjaran::first();
    $tahunId = $tahunAjaran?->id_tahun_ajaran ?? 1;

    $semuaJadwalGuru = Illuminate\Support\Facades\DB::table('jadwal_mengajar')
        ->join('jam_pelajaran', 'jadwal_mengajar.id_jam', '=', 'jam_pelajaran.id_jam')
        ->join('kelas', 'jadwal_mengajar.id_kelas', '=', 'kelas.id_kelas')
        ->join('mapel', 'jadwal_mengajar.id_mapel', '=', 'mapel.id_mapel')
        ->whereNull('jadwal_mengajar.deleted_at')
        ->whereNull('jam_pelajaran.deleted_at')
        ->whereNull('kelas.deleted_at')
        ->whereNull('mapel.deleted_at')
        ->where('jadwal_mengajar.id_guru', $guruId)
        ->where('jadwal_mengajar.id_tahun_ajaran', $tahunId)
        ->select(
            'jadwal_mengajar.id_jadwal',
            'jadwal_mengajar.hari',
            'jadwal_mengajar.id_jam',
            'jadwal_mengajar.id_kelas',
            'kelas.nama_kelas',
            'mapel.nama_mapel',
            'jam_pelajaran.jam_ke',
            'jam_pelajaran.jam_mulai',
            'jam_pelajaran.jam_selesai'
        )
        ->orderBy('jadwal_mengajar.hari')
        ->orderBy('jam_pelajaran.jam_ke')
        ->get();

    // Map: [hari][id_jam] => jadwal
    $guruGridMap = [];
    foreach ($semuaJadwalGuru as $j) {
        $guruGridMap[$j->hari][$j->id_jam] = $j;
    }

    // Ambil semua jam pelajaran, group by hari
    $allJamAll = App\Models\JamPelajaran::whereNull('deleted_at')
        ->orderBy('hari')->orderBy('jam_ke')->get();
    $jamPerHariAll = [];
    foreach ($allJamAll as $jp) {
        $jamPerHariAll[$jp->hari][] = $jp;
    }

    // Hitung statistik semua hari
    $totalJamSemua = $semuaJadwalGuru->count();
    $totalKelasSemua = $semuaJadwalGuru->pluck('nama_kelas')->unique()->count();
    $daftarMapelSemua = $semuaJadwalGuru->pluck('nama_mapel')->unique()->filter()->values();
    $countPerHari = $semuaJadwalGuru->groupBy('hari')->map->count();
@endphp

<div class="page-content page-anim" id="page-jadwal-mengajar" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Jadwal Mengajar Saya</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">
                Jadwal lengkap semua hari — <strong>{{ $tahunAjaran?->nama_tahun_ajaran ?? 'Tahun Ajaran Aktif' }}</strong>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Hari Ini: {{ $hariIni ?? 'Hari Ini' }}
            </span>
        </div>
    </div>

    {{-- ── Stat Cards Ringkasan Semua Jadwal ── --}}
    <div class="stat-cards" style="margin-bottom:22px">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Jam / Minggu</div>
                <div class="stat-value">{{ $totalJamSemua }} <span style="font-size:14px;font-weight:500;color:#64748b">Jam</span></div>
                <div class="stat-pct">semua hari dalam seminggu</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Kelas Berbeda</div>
                <div class="stat-value">{{ $totalKelasSemua }} <span style="font-size:14px;font-weight:500;color:#64748b">Kelas</span></div>
                <div class="stat-pct">{{ $semuaJadwalGuru->pluck('nama_kelas')->unique()->join(', ') ?: 'Tidak ada kelas' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon cyan">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div>
                <div class="stat-label">Mata Pelajaran</div>
                <div class="stat-value" style="font-size:16px;line-height:1.3;font-weight:700">{{ $daftarMapelSemua->isNotEmpty() ? $daftarMapelSemua->first() : '-' }}</div>
                <div class="stat-pct">{{ $daftarMapelSemua->count() > 1 ? '+' . ($daftarMapelSemua->count() - 1) . ' mapel lainnya' : 'mapel yang diajar' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="stat-label">Hari Mengajar</div>
                <div class="stat-value">{{ $countPerHari->count() }} <span style="font-size:14px;font-weight:500;color:#64748b">Hari</span></div>
                <div class="stat-pct">{{ $countPerHari->keys()->join(', ') ?: 'Belum ada jadwal' }}</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         GRID JADWAL MENGAJAR — baris: hari, kolom: jam pelajaran
         Gaya aSc Timetables (komponen shared: partials.jadwal-asc)
         ══════════════════════════════════════════════════════════ --}}
    <div style="margin-bottom:20px">
        @if($semuaJadwalGuru->isEmpty())
            <div class="card" style="text-align:center;padding:60px 20px;color:#94a3b8">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:12px;opacity:.4"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <div style="font-size:15px;font-weight:700;color:#64748b;margin-bottom:4px">Belum Ada Jadwal Mengajar</div>
                <div style="font-size:12.5px">Hubungi admin untuk mengatur jadwal mengajar Anda.</div>
            </div>
        @else
            @include('partials.jadwal-asc', [
                'gridMap'        => $guruGridMap,
                'judulBaris2'    => session('auth_nama_guru', ''),
                'jamPerHari'     => $jamPerHariAll,
                'hariUrutan'     => $hariUrutan,
                'tahunAjaran'    => $tahunAjaran,
                'barisBawah'     => 'kelas',
                'hariIni'        => $hariIni ?? null,
                'sorotSekarang'  => true,
                'legenda'        => '<span class="legenda-item"><span class="legenda-dot" style="background:var(--blue)"></span> Jadwal Anda</span>'
                    . '<span class="legenda-item"><span class="legenda-dot" style="background:var(--green)"></span> Sedang berlangsung</span>'
                    . '<span class="legenda-item"><span class="legenda-dot" style="background:var(--border)"></span> Jam kosong / bukan jadwal Anda</span>',
            ])
        @endif
    </div>

    {{-- ── Jadwal Hari Ini (detail list, gaya aSc — aksi jurnal tetap ada) ── --}}
    <div class="asc-sheet">
        <div class="asc-sheet-head is-toolbar">
            <div>
                <div class="asc-sheet-title">Daftar Jadwal Hari Ini — {{ $hariIni ?? 'Hari Ini' }}</div>
                <div class="asc-sheet-sub">
                    Urutan jam mengajar otomatis disesuaikan dengan pengaturan jam sekolah.
                </div>
            </div>
            <div class="asc-sheet-tools">
                <div style="position:relative">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="search-guru-jadwal-mengajar" oninput="filterTable('search-guru-jadwal-mengajar','table-guru-jadwal-mengajar')" onkeyup="filterTable('search-guru-jadwal-mengajar','table-guru-jadwal-mengajar')" placeholder="Cari kelas / mapel..." class="filter-input" style="padding:7px 12px 7px 32px;font-size:12.5px;border-radius:8px;border:1px solid var(--border, #cbd5e1);width:200px;background:var(--card-bg, #fff);color:var(--text-primary, #0f172a)">
                </div>
                <button type="button" class="btn-secondary" onclick="showPage('jurnal-absensi')" style="border-radius:8px;padding:8px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Buka Jurnal &amp; Absensi
                </button>
            </div>
        </div>

        <div class="asc-sheet-scroll">
            <table class="data-table asc-table is-list" id="table-guru-jadwal-mengajar">
                <thead>
                    <tr>
                        <th class="asc-period" style="text-align:center">Jam</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th style="text-align:center">Status Sesi</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalMengajarHariIni as $idx => $jadwal)
                    @php
                        $nowStr = now()->format('H:i:s');
                        $isSedang = ($nowStr >= $jadwal->jam_mulai && $nowStr <= $jadwal->jam_selesai);
                        $isBelum = ($nowStr < $jadwal->jam_mulai);
                        $isSelesai = ($nowStr > $jadwal->jam_selesai);
                        $hasJurnal = ! empty($jadwal->has_jurnal);
                    @endphp
                    <tr class="jadwal-row-item {{ $isSedang ? 'is-sedang is-now' : '' }}" data-mulai="{{ $jadwal->jam_mulai }}" data-selesai="{{ $jadwal->jam_selesai }}" data-kelas="{{ $jadwal->id_kelas }}" data-has-jurnal="{{ $hasJurnal ? '1' : '0' }}">
                        <td class="asc-time">
                            <span style="display:block;font-size:13.5px;font-weight:800">
                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                            </span>
                            @if($isSedang)
                                <span class="asc-now-dot" style="display:inline-block;margin-top:6px"></span>
                            @endif
                        </td>
                        <td>
                            <strong style="color:#1e3a8a;font-size:13.5px">{{ $jadwal->nama_kelas }}</strong>
                        </td>
                        <td style="color:#1e293b;font-weight:600">
                            {{ $jadwal->nama_mapel }}
                        </td>
                        <td style="text-align:center" class="status-cell">
                            @if($isSedang)
                                @if($hasJurnal)
                                    <span class="badge badge-success" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:700;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0">
                                        <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>
                                        Sudah Diisi
                                    </span>
                                @else
                                    <span class="badge badge-success" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:700">
                                        <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>
                                        Sedang Berlangsung
                                    </span>
                                @endif
                            @elseif($isBelum)
                                <span class="badge badge-info" style="padding:5px 10px;font-weight:600;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">
                                    Belum Dimulai
                                </span>
                            @else
                                @if($hasJurnal)
                                    <span class="badge" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:600;background:#f8fafc;color:#15803d;border:1px solid #e2e8f0">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        Sudah Diisi
                                    </span>
                                @else
                                    <span class="badge" style="background:#fef2f2;color:#b91c1c;padding:5px 10px;font-weight:600;border:1px solid #fecaca">
                                        Tidak Diisi
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td style="text-align:center" class="action-cell">
                            @if($isSedang)
                                @if($hasJurnal)
                                    <button type="button" class="btn-warning btn-isi-jurnal" onclick="bukaJurnalKelas('{{ $jadwal->id_kelas }}')" style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#f59e0b;color:#fff;border:none;outline:none;cursor:pointer;box-shadow:0 2px 6px rgba(245,158,11,0.25)" title="Edit jurnal yang sedang aktif">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit Jurnal
                                    </button>
                                @else
                                    <button type="button" class="btn-primary btn-isi-jurnal" onclick="bukaJurnalKelas('{{ $jadwal->id_kelas }}')" style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#16a34a;color:#fff;border:none;outline:none;cursor:pointer;box-shadow:0 2px 6px rgba(22,163,74,0.25)" title="Isi jurnal sekarang">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                        Isi Jurnal
                                    </button>
                                @endif
                            @elseif($isBelum)
                                <button type="button" disabled style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;cursor:not-allowed">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    Belum Dimulai
                                </button>
                            @else
                                <button type="button" disabled style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;cursor:not-allowed">
                                    @if($hasJurnal)
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        Selesai
                                    @else
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        Terlewat
                                    @endif
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px 20px">
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
