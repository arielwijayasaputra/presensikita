<div class="page-content page-anim" id="page-satpam-harian" style="display:none">
    @php
        $tglFormatted = \Carbon\Carbon::parse($satpamTanggal)->translatedFormat('l, d F Y');
        $isHariIni = ($satpamTanggal === now()->toDateString());
        $totalHarian = $satpamDispenRiwayat->count();
        $totalDiluarHarian = $satpamDispenRiwayat->filter(fn($d) => $d->waktu_keluar && !$d->waktu_masuk)->count();
        $totalKembaliHarian = $satpamDispenRiwayat->filter(fn($d) => $d->waktu_masuk)->count();
        $totalBelumKeluarHarian = $satpamDispenRiwayat->filter(fn($d) => !$d->waktu_keluar && !$d->waktu_masuk)->count();
    @endphp

    <!-- ══ HEADER DATA HARIAN ══ -->
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin-bottom:24px">
        <div>
            <div style="display:flex;align-items:center;gap:10px">
                <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0">Data Harian &amp; Riwayat Dispensasi</h1>
                <span class="badge badge-primary" style="font-size:13px;padding:6px 12px;border-radius:8px">
                    {{ $tglFormatted }}
                </span>
            </div>
            <p style="font-size:13px;color:#64748b;margin:4px 0 0 0">
                Pilih tanggal untuk melihat riwayat log keluar-masuk siswa dispensasi.
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <!-- Filter Tanggal -->
            <form method="GET" action="{{ route('satpam.index') }}" onsubmit="this.action = '{{ route('satpam.index') }}#satpam-harian'" style="display:flex;align-items:center;gap:8px;background:#fff;padding:6px 14px;border-radius:10px;border:1px solid #cbd5e1;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <label for="input-satpam-tanggal" style="font-size:12px;font-weight:700;color:#475569">Pilih Tanggal:</label>
                <input type="date" id="input-satpam-tanggal" name="satpam_tanggal" value="{{ $satpamTanggal }}" class="filter-input" style="padding:5px 10px;font-size:12.5px;border-radius:7px;border:1px solid #cbd5e1" onchange="this.form.action='{{ route('satpam.index') }}#satpam-harian'; this.form.submit();">
                <button type="submit" class="btn-primary" style="font-size:12px;padding:6px 14px;border-radius:7px;background:#2563eb">
                    Tampilkan
                </button>
            </form>

            <button type="button" onclick="window.print()" class="btn-secondary" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;font-size:12.5px;font-weight:700;border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak Data</span>
            </button>
        </div>
    </div>

    <!-- ══ STAT CARDS PER TANGGAL ══ -->
    <div class="stat-cards" style="margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Dispen</div>
                <div class="stat-value">{{ $totalHarian }}</div>
                <div class="stat-sub">siswa pada tanggal ini</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed;color:#ea580c">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2 2 2M12 12v6"/><circle cx="12" cy="7" r="1"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M4 21v-2a4 4 0 0 1 3-3.87"/></svg>
            </div>
            <div>
                <div class="stat-label">Sedang Di Luar</div>
                <div class="stat-value" style="color:#ea580c">{{ $totalDiluarHarian }}</div>
                <div class="stat-sub">siswa belum kembali</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;color:#16a34a">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="stat-label">Sudah Kembali</div>
                <div class="stat-value" style="color:#16a34a">{{ $totalKembaliHarian }}</div>
                <div class="stat-sub">siswa telah masuk kembali</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#f8fafc;color:#64748b">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="stat-label">Belum Keluar</div>
                <div class="stat-value" style="color:#64748b">{{ $totalBelumKeluarHarian }}</div>
                <div class="stat-sub">siswa belum izin gerbang</div>
            </div>
        </div>
    </div>

    <!-- ══ TABEL DATA HARIAN ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
            <div>
                <div class="card-title" style="font-size:16px;font-weight:800;color:#0f172a">
                    Daftar Siswa Dispensasi — {{ $tglFormatted }}
                </div>
                <div style="font-size:12.5px;color:#64748b;margin-top:2px">
                    Menampilkan seluruh data dispensasi siswa untuk tanggal yang dipilih.
                </div>
            </div>
            <span class="card-action" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;padding:5px 12px;border-radius:8px;font-size:12.5px;font-weight:700">
                {{ $totalHarian }} Data Ditemukan
            </span>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:950px">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center">No.</th>
                        <th>Siswa &amp; Kelas</th>
                        <th>Alasan Dispensasi</th>
                        <th>Status Waka</th>
                        <th>Foto Surat</th>
                        <th>Status Keluar/Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Waktu Masuk</th>
                        <th>Guru Piket</th>
                        <th style="width:140px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($satpamDispenRiwayat as $idx => $item)
                        <tr>
                            <td style="text-align:center;color:#94a3b8;font-weight:700">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong>
                                <div style="font-size:12px;color:#64748b">NISN: {{ $item->siswa->nisn ?? '-' }} · {{ $item->siswa->kelas->nama_kelas ?? '-' }}</div>
                            </td>
                            <td>{{ $item->alasan }}</td>
                            <td>
                                @if($item->status_waka === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($item->status_waka === 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($item->foto_surat)
                                    <a href="#" onclick="showSuratPopup('{{ Storage::disk('public')->url($item->foto_surat) }}'); return false;" style="font-size:12.5px;font-weight:600;color:#2563eb;display:inline-flex;align-items:center;gap:4px">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        Lihat Foto
                                    </a>
                                @else
                                    <span style="color:#94a3b8">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->waktu_masuk)
                                    <span class="badge badge-success badge-status-satpam">Sudah Kembali</span>
                                @elseif($item->waktu_keluar)
                                    <span class="badge badge-warning badge-status-satpam">Di Luar</span>
                                @else
                                    <span class="badge badge-info badge-status-satpam">Belum Keluar</span>
                                @endif
                            </td>
                            <td class="waktu-keluar">
                                @if($item->waktu_keluar)
                                    <span class="badge badge-info">{{ $item->waktu_keluar->format('H:i:s') }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12.5px">-</span>
                                @endif
                            </td>
                            <td class="waktu-masuk">
                                @if($item->waktu_masuk)
                                    <span class="badge badge-success">{{ $item->waktu_masuk->format('H:i:s') }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12.5px">-</span>
                                @endif
                            </td>
                            <td style="font-size:12.5px;color:#475569">
                                {{ $item->guruPiket->nama_guru ?? '-' }}
                            </td>
                            <td style="text-align:center" class="aksi-satpam">
                                @if($item->waktu_masuk)
                                    <span style="color:#16a34a;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:4px">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        Selesai
                                    </span>
                                @elseif($isHariIni)
                                    @if($item->waktu_keluar)
                                        <button type="button" class="btn-satpam-masuk" onclick="prosesMasuk({{ $item->id_dispen_siswa }}, this)">
                                            Izinkan Masuk
                                        </button>
                                    @else
                                        <button type="button" class="btn-satpam-keluar" onclick="prosesKeluar({{ $item->id_dispen_siswa }}, this)">
                                            Izinkan Keluar
                                        </button>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-size:12px">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center;color:#64748b;padding:40px 20px">
                                <div style="max-width:360px;margin:0 auto">
                                    <div style="width:52px;height:52px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#94a3b8">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    </div>
                                    <h4 style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:4px">Tidak Ada Data Dispensasi</h4>
                                    <p style="font-size:12.5px;color:#64748b;margin:0">Tidak ditemukan riwayat dispensasi siswa pada tanggal <strong>{{ $tglFormatted }}</strong>.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
