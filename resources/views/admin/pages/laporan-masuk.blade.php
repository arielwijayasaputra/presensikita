@php
    $lmList = $laporanMasukList ?? collect();
    $lmStats = $laporanMasukStats ?? [
        'total' => 0, 'menunggu' => 0, 'diterima' => 0, 'diproses' => 0, 'selesai' => 0, 'ditolak' => 0, 'dibatalkan' => 0
    ];
@endphp

<div class="page-content page-anim" id="page-laporan-masuk" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;color:#1e293b">Laporan Masuk / Pengaduan</div>
            <div class="page-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">Kelola laporan & pengaduan yang dikirim dari peran lain maupun publik luar login</div>
        </div>
        <a href="{{ route('laporan.public') }}" target="_blank" class="btn-secondary" style="border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:8px;text-decoration:none">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Buka Form Laporan Public
        </a>
    </div>

    {{-- ── Stat Cards Ringkasan ── --}}
    <div class="stat-cards" style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:22px">
        {{-- Total --}}
        <div class="stat-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:16px;display:flex;align-items:center;gap:14px">
            <div style="width:44px;height:44px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#475569;flex-shrink:0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div>
                <div style="font-size:11.5px;color:#64748b;font-weight:600">Total Laporan</div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;line-height:1.1">{{ number_format($lmStats['total']) }}</div>
                <div style="font-size:11px;color:#94a3b8">Semua riwayat</div>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="stat-card" style="background:#fffbeb;border:1px solid #fef3c7;border-radius:var(--radius);padding:16px;display:flex;align-items:center;gap:14px">
            <div style="width:44px;height:44px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#d97706;flex-shrink:0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div style="font-size:11.5px;color:#b45309;font-weight:600">Menunggu</div>
                <div style="font-size:22px;font-weight:800;color:#92400e;line-height:1.1">{{ number_format($lmStats['menunggu']) }}</div>
                <div style="font-size:11px;color:#d97706">Perlu konfirmasi</div>
            </div>
        </div>

        {{-- Diterima & Diproses --}}
        <div class="stat-card" style="background:#eff6ff;border:1px solid #dbeafe;border-radius:var(--radius);padding:16px;display:flex;align-items:center;gap:14px">
            <div style="width:44px;height:44px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div style="font-size:11.5px;color:#1d4ed8;font-weight:600">Diterima / Diproses</div>
                <div style="font-size:22px;font-weight:800;color:#1e40af;line-height:1.1">{{ number_format($lmStats['diterima'] + $lmStats['diproses']) }}</div>
                <div style="font-size:11px;color:#3b82f6">Dalam penanganan</div>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="stat-card" style="background:#f0fdf4;border:1px solid #dcfce7;border-radius:var(--radius);padding:16px;display:flex;align-items:center;gap:14px">
            <div style="width:44px;height:44px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div style="font-size:11.5px;color:#15803d;font-weight:600">Selesai</div>
                <div style="font-size:22px;font-weight:800;color:#166534;line-height:1.1">{{ number_format($lmStats['selesai']) }}</div>
                <div style="font-size:11px;color:#22c55e">Telah ditangani</div>
            </div>
        </div>

        {{-- Ditolak / Dibatalkan --}}
        <div class="stat-card" style="background:#fff1f2;border:1px solid #fecdd3;border-radius:var(--radius);padding:16px;display:flex;align-items:center;gap:14px">
            <div style="width:44px;height:44px;background:#fecdd3;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#e11d48;flex-shrink:0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div style="font-size:11.5px;color:#be123c;font-weight:600">Ditolak / Batal</div>
                <div style="font-size:22px;font-weight:800;color:#9f1239;line-height:1.1">{{ number_format($lmStats['ditolak'] + $lmStats['dibatalkan']) }}</div>
                <div style="font-size:11px;color:#f43f5e">Dibatalkan/Ditolak</div>
            </div>
        </div>
    </div>

    {{-- ── Filter & Search Bar ── --}}
    <div class="card" style="background:#fff;border-radius:var(--radius);border:1px solid #e2e8f0;padding:16px 20px;margin-bottom:20px">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px">
            {{-- Status Tabs --}}
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                <button type="button" class="btn-filter-status active" data-status="semua" onclick="filterLaporanStatus('semua', this)" style="padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:700;border:1px solid #e2e8f0;background:#1e293b;color:#fff;cursor:pointer">
                    Semua ({{ number_format($lmStats['total']) }})
                </button>
                <button type="button" class="btn-filter-status" data-status="menunggu" onclick="filterLaporanStatus('menunggu', this)" style="padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:600;border:1px solid #fef3c7;background:#fffbeb;color:#b45309;cursor:pointer">
                    Menunggu ({{ number_format($lmStats['menunggu']) }})
                </button>
                <button type="button" class="btn-filter-status" data-status="diterima" onclick="filterLaporanStatus('diterima', this)" style="padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:600;border:1px solid #dbeafe;background:#eff6ff;color:#1d4ed8;cursor:pointer">
                    Diterima ({{ number_format($lmStats['diterima']) }})
                </button>
                <button type="button" class="btn-filter-status" data-status="diproses" onclick="filterLaporanStatus('diproses', this)" style="padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:600;border:1px solid #e0f2fe;background:#f0f9ff;color:#0369a1;cursor:pointer">
                    Diproses ({{ number_format($lmStats['diproses']) }})
                </button>
                <button type="button" class="btn-filter-status" data-status="selesai" onclick="filterLaporanStatus('selesai', this)" style="padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:600;border:1px solid #dcfce7;background:#f0fdf4;color:#15803d;cursor:pointer">
                    Selesai ({{ number_format($lmStats['selesai']) }})
                </button>
                <button type="button" class="btn-filter-status" data-status="ditolak" onclick="filterLaporanStatus('ditolak', this)" style="padding:6px 14px;border-radius:99px;font-size:12.5px;font-weight:600;border:1px solid #fecdd3;background:#fff1f2;color:#be123c;cursor:pointer">
                    Ditolak ({{ number_format($lmStats['ditolak']) }})
                </button>
            </div>

            {{-- Search Box --}}
            <div style="position:relative;min-width:240px">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-laporan" onkeyup="searchLaporanTabel()" placeholder="Cari nama, role, judul..." style="width:100%;padding:8px 12px 8px 36px;font-size:13px;border:1px solid #cbd5e1;border-radius:10px;outline:none">
            </div>
        </div>
    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="background:#fff;border-radius:var(--radius);border:1px solid #e2e8f0;overflow:hidden">
        <table id="tabel-laporan-masuk" style="width:100%;border-collapse:collapse;text-align:left">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0">
                    <th style="padding:14px 16px;font-size:12px;font-weight:700;color:#475569;width:50px">No.</th>
                    <th style="padding:14px 16px;font-size:12px;font-weight:700;color:#475569;width:150px">Tanggal & Waktu</th>
                    <th style="padding:14px 16px;font-size:12px;font-weight:700;color:#475569;width:180px">Pelapor & Role</th>
                    <th style="padding:14px 16px;font-size:12px;font-weight:700;color:#475569">Judul & Detail Laporan</th>
                    <th style="padding:14px 16px;font-size:12px;font-weight:700;color:#475569;width:130px;text-align:center">Status</th>
                    <th style="padding:14px 16px;font-size:12px;font-weight:700;color:#475569;width:240px;text-align:center">Aksi / Tindakan</th>
                </tr>
            </thead>
            <tbody id="tbody-laporan-masuk">
                @forelse($lmList as $idx => $lap)
                    @php
                        $st = $lap->status;
                    @endphp
                    <tr class="row-laporan-item" data-status="{{ $st }}" style="border-bottom:1px solid #f1f5f9;transition:background 0.15s">
                        <td style="padding:14px 16px;font-size:13px;color:#64748b;font-weight:600">{{ $idx + 1 }}</td>
                        <td style="padding:14px 16px;font-size:12.5px;color:#475569">
                            <div style="font-weight:700;color:#1e293b">{{ $lap->created_at->format('d M Y') }}</div>
                            <div style="font-size:11.5px;color:#94a3b8">{{ $lap->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td style="padding:14px 16px;font-size:13px">
                            <div style="font-weight:700;color:#0f172a;margin-bottom:3px">{{ $lap->nama_pelapor }}</div>
                            <span style="display:inline-block;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">
                                {{ $lap->role_pelapor }}
                            </span>
                        </td>
                        <td style="padding:14px 16px;font-size:13px">
                            <div style="font-weight:700;color:#1e293b;margin-bottom:4px">{{ $lap->judul }}</div>
                            <div style="color:#64748b;font-size:12.5px;line-height:1.4;max-width:380px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $lap->isi_laporan }}
                            </div>
                            <button type="button" onclick="bukaDetailLaporan('{{ htmlspecialchars($lap->nama_pelapor) }}', '{{ htmlspecialchars($lap->role_pelapor) }}', '{{ htmlspecialchars($lap->judul) }}', '{{ htmlspecialchars($lap->isi_laporan) }}', '{{ $lap->created_at->format('d M Y, H:i') }} WIB')" style="background:none;border:none;color:#2563eb;font-size:11.5px;font-weight:700;cursor:pointer;padding:0;margin-top:4px">
                                lihat selengkapnya &raquo;
                            </button>
                        </td>
                        <td style="padding:14px 16px;text-align:center">
                            @if($st === 'menunggu')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:700;background:#fef3c7;color:#b45309;border:1px solid #fde68a">
                                    <span style="width:6px;height:6px;border-radius:50%;background:#d97706"></span> Menunggu
                                </span>
                            @elseif($st === 'diterima')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:700;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
                                    <span style="width:6px;height:6px;border-radius:50%;background:#2563eb"></span> Diterima
                                </span>
                            @elseif($st === 'diproses')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:700;background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd">
                                    <span style="width:6px;height:6px;border-radius:50%;background:#0284c7"></span> Diproses
                                </span>
                            @elseif($st === 'selesai')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:700;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Selesai
                                </span>
                            @elseif($st === 'ditolak')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:700;background:#fff1f2;color:#be123c;border:1px solid #fecdd3">
                                    Ditolak
                                </span>
                            @elseif($st === 'dibatalkan')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:99px;font-size:11.5px;font-weight:700;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;text-align:center">
                            {{-- ── ALUR TOMBOL SESUAI SPESIFIKASI USER ── --}}

                            {{-- 1. MENUNGGU -> Terima & Tolak --}}
                            @if($st === 'menunggu')
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px">
                                    <button type="button" onclick="ubahStatusLaporan({{ $lap->id_laporan }}, 'diterima', 'Terima laporan ini? Status akan berubah menjadi Diterima.')" style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;background:#22c55e;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;box-shadow:0 2px 6px rgba(34,197,94,0.3)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        Terima
                                    </button>
                                    <button type="button" onclick="ubahStatusLaporan({{ $lap->id_laporan }}, 'ditolak', 'Tolak laporan ini? Status akan menjadi Ditolak.')" style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;background:#ef4444;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;box-shadow:0 2px 6px rgba(239,68,68,0.3)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Tolak
                                    </button>
                                </div>

                            {{-- 2. DITERIMA -> Proses & Batalkan --}}
                            @elseif($st === 'diterima')
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px">
                                    <button type="button" onclick="ubahStatusLaporan({{ $lap->id_laporan }}, 'diproses', 'Mulai memproses laporan ini? Status akan berubah menjadi Diproses.')" style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;background:#2563eb;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;box-shadow:0 2px 6px rgba(37,99,235,0.3)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Proses
                                    </button>
                                    <button type="button" onclick="ubahStatusLaporan({{ $lap->id_laporan }}, 'dibatalkan', 'Batalkan laporan yang telah diterima ini?')" style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;background:#64748b;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Batalkan
                                    </button>
                                </div>

                            {{-- 3. DIPROSES -> Selesai & Batalkan --}}
                            @elseif($st === 'diproses')
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px">
                                    <button type="button" onclick="ubahStatusLaporan({{ $lap->id_laporan }}, 'selesai', 'Tandai laporan ini SELESAI?')" style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;background:#16a34a;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;box-shadow:0 2px 6px rgba(22,163,74,0.3)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        Selesai
                                    </button>
                                    <button type="button" onclick="ubahStatusLaporan({{ $lap->id_laporan }}, 'dibatalkan', 'Batalkan pemrosesan laporan ini?')" style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;background:#64748b;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Batalkan
                                    </button>
                                </div>

                            {{-- 4. SELESAI -> Yasudah (Badge & Opsi Hapus Harian) --}}
                            @elseif($st === 'selesai')
                                <div style="font-size:12px;font-weight:700;color:#16a34a;display:inline-flex;align-items:center;gap:4px">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Laporan Selesai
                                </div>

                            {{-- 5. DITOLAK / DIBATALKAN --}}
                            @else
                                <button type="button" onclick="hapusLaporanItem({{ $lap->id_laporan }})" style="background:none;border:none;color:#94a3b8;font-size:12px;font-weight:600;cursor:pointer;text-decoration:underline">
                                    Hapus Record
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:10px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <div style="font-size:14px;font-weight:700;color:#64748b">Belum Ada Laporan Masuk</div>
                            <div style="font-size:12.5px;color:#94a3b8;margin-top:2px">Laporan yang dikirim oleh publik atau pengguna lain akan muncul di sini.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Filter Berdasarkan Status Tabs
    function filterLaporanStatus(status, btn) {
        document.querySelectorAll('.btn-filter-status').forEach(b => {
            b.style.background = '#ffffff';
            b.style.color = '#475569';
            b.style.borderColor = '#e2e8f0';
        });

        if (status === 'semua') {
            btn.style.background = '#1e293b';
            btn.style.color = '#ffffff';
            btn.style.borderColor = '#1e293b';
        } else if (status === 'menunggu') {
            btn.style.background = '#fef3c7'; btn.style.color = '#b45309'; btn.style.borderColor = '#fde68a';
        } else if (status === 'diterima') {
            btn.style.background = '#eff6ff'; btn.style.color = '#1d4ed8'; btn.style.borderColor = '#bfdbfe';
        } else if (status === 'diproses') {
            btn.style.background = '#f0f9ff'; btn.style.color = '#0369a1'; btn.style.borderColor = '#bae6fd';
        } else if (status === 'selesai') {
            btn.style.background = '#f0fdf4'; btn.style.color = '#15803d'; btn.style.borderColor = '#bbf7d0';
        } else if (status === 'ditolak') {
            btn.style.background = '#fff1f2'; btn.style.color = '#be123c'; btn.style.borderColor = '#fecdd3';
        }

        const rows = document.querySelectorAll('.row-laporan-item');
        rows.forEach(r => {
            const st = r.dataset.status;
            if (status === 'semua' || st === status) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    // Live Search In Table
    function searchLaporanTabel() {
        const input = document.getElementById('search-laporan').value.toLowerCase();
        const rows = document.querySelectorAll('.row-laporan-item');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            if (text.includes(input)) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    // Modal / Alert Detail Laporan
    function bukaDetailLaporan(nama, role, judul, isi, tanggal) {
        Swal.fire({
            title: `<div style="font-size:17px;font-weight:800;color:#0f172a">${judul}</div>`,
            html: `
                <div style="text-align:left;font-size:13.5px;color:#334155;line-height:1.6">
                    <div style="background:#f8fafc;padding:12px;border-radius:10px;margin-bottom:14px;border:1px solid #e2e8f0">
                        <div><strong>Pelapor:</strong> ${nama} (${role})</div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px"><strong>Waktu:</strong> ${tanggal}</div>
                    </div>
                    <div style="font-weight:700;color:#1e293b;margin-bottom:4px">Rincian Laporan:</div>
                    <div style="background:#fff;border:1px solid #cbd5e1;padding:12px;border-radius:10px;white-space:pre-wrap;max-height:260px;overflow-y:auto">${isi}</div>
                </div>
            `,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#1e293b'
        });
    }

    // Update Status Laporan via AJAX
    function ubahStatusLaporan(id, newStatus, confirmText) {
        Swal.fire({
            title: 'Konfirmasi Tindakan',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: newStatus === 'ditolak' || newStatus === 'dibatalkan' ? '#ef4444' : '#2563eb',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/laporan-masuk/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route('admin.index') }}#laporan-masuk';
                        });
                    } else {
                        Swal.fire('Gagal', data.message || 'Gagal mengubah status.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal memproses permintaan ke server.', 'error');
                });
            }
        });
    }

    // Hapus Record Laporan
    function hapusLaporanItem(id) {
        Swal.fire({
            title: 'Hapus Laporan?',
            text: 'Data laporan ini akan dihapus permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444'
        }).then((res) => {
            if (res.isConfirmed) {
                fetch(`/laporan-masuk/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil', data.message, 'success').then(() => {
                            window.location.href = '{{ route('admin.index') }}#laporan-masuk';
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
