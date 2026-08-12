@php
    $hariMap = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    $riwayatTotalCount = count($riwayatList);
    $totalHadirSum = $riwayatList->sum('jumlah_hadir');
    $totalSakitSum = $riwayatList->sum('jumlah_sakit');
    $totalIzinSum  = $riwayatList->sum('jumlah_izin');
    $totalAlpaSum  = $riwayatList->sum('jumlah_alpa');
    $grandTotal    = $totalHadirSum + $totalSakitSum + $totalIzinSum + $totalAlpaSum;
    $overallPct    = $grandTotal > 0 ? round(($totalHadirSum / $grandTotal) * 100) : 100;
@endphp

<div class="page-content page-anim" id="page-riwayat" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px;display:flex;align-items:flex-start;justify-space-between">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;color:#1e293b">Riwayat / Jurnal</div>
            <div class="page-subtitle" id="riwayat-subtitle" style="font-size:13px;color:#64748b;margin-top:2px">
                Kelas {{ $selectedKelas->nama_kelas }}
            </div>
        </div>
        <div class="breadcrumb" style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
            Dashboard
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#1e293b;font-weight:600">Riwayat / Jurnal</span>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:18px;align-items:flex-end;gap:12px;flex-wrap:wrap">
        {{-- Pilih Bulan --}}
        <div class="filter-group" style="min-width:180px">
            <label style="font-size:11.5px;font-weight:600;color:#475569;margin-bottom:5px;display:block">Pilih Bulan</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <select class="filter-select" id="riwayat-filter-bulan" onchange="filterRiwayatPage()" style="width:100%;padding-left:32px;padding-right:28px;appearance:none">
                    @php
                        $bulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                        $currentMonthNum = (int) date('n');
                        $currentYearNum = date('Y');
                    @endphp
                    @foreach($bulanList as $num => $namaBulan)
                        <option value="{{ sprintf('%02d', $num) }}" {{ $currentMonthNum === $num ? 'selected' : '' }}>
                            {{ $namaBulan }} {{ $currentYearNum }}
                        </option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Pilih Kelas --}}
        <div class="filter-group" style="min-width:160px">
            <label style="font-size:11.5px;font-weight:600;color:#475569;margin-bottom:5px;display:block">Pilih Kelas</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <select class="filter-select" id="riwayat-filter-kelas" onchange="filterRiwayatPage()" style="width:100%;padding-left:32px;padding-right:28px;appearance:none">
                    <option value="">Semua Kelas</option>
                    @foreach($kelases as $k)
                    <option value="{{ $k->id_kelas }}" {{ $k->id_kelas == $selectedKelas->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Tombol Filter di kanan --}}
        <button type="button" class="btn-secondary" onclick="filterRiwayatPage()" style="margin-left:auto;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:8px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
            Filter
        </button>
    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <table id="riwayat-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:48px;vertical-align:middle">No.</th>
                    <th rowspan="2" style="vertical-align:middle;width:120px">Tanggal</th>
                    <th rowspan="2" style="vertical-align:middle;width:100px">Hari</th>
                    <th colspan="4" style="text-align:center;border-bottom:1px solid #e2e8f0;padding:8px">Jumlah Siswa</th>
                    <th rowspan="2" style="text-align:center;vertical-align:middle;width:160px">Persentase Kehadiran</th>
                    <th rowspan="2" style="text-align:center;vertical-align:middle;width:130px">Aksi</th>
                </tr>
                <tr class="riwayat-subhead">
                    <th style="text-align:center;color:#16a34a;width:60px">H</th>
                    <th style="text-align:center;color:#d97706;width:60px">S</th>
                    <th style="text-align:center;color:#2563eb;width:60px">I</th>
                    <th style="text-align:center;color:#dc2626;width:60px">A</th>
                </tr>
            </thead>
            <tbody id="riwayat-tbody-page">
                @forelse($riwayatList as $idx => $r)
                @php
                    $dt = \Carbon\Carbon::parse($r->tanggal);
                    $namaHari = $hariMap[$dt->format('l')] ?? $dt->format('l');
                    $tglFormatted = $dt->format('d/m/Y');
                    $monthNumStr = $dt->format('m');

                    $pct = (int) ($r->persentase ?? 100);
                    if($pct >= 85) {
                        $badgeStyle = 'background:#dcfce7;color:#15803d;';
                    } elseif($pct >= 75) {
                        $badgeStyle = 'background:#fef3c7;color:#b45309;';
                    } else {
                        $badgeStyle = 'background:#fee2e2;color:#b91c1c;';
                    }
                @endphp
                <tr class="riwayat-row-item"
                    data-search="{{ strtolower(($r->tanggal ?? '') . ' ' . $tglFormatted . ' ' . $namaHari . ' ' . ($r->nama_kelas ?? '')) }}"
                    data-kelas="{{ $r->id_kelas ?? '' }}"
                    data-bulan="{{ $monthNumStr }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td style="font-weight:500;color:#1e293b;font-size:13px">{{ $tglFormatted }}</td>
                    <td style="color:#475569;font-weight:500;font-size:13px">{{ $namaHari }}</td>
                    <td style="text-align:center;color:#16a34a;font-weight:600;font-size:13.5px">{{ $r->jumlah_hadir }}</td>
                    <td style="text-align:center;color:#d97706;font-weight:600;font-size:13.5px">{{ $r->jumlah_sakit }}</td>
                    <td style="text-align:center;color:#2563eb;font-weight:600;font-size:13.5px">{{ $r->jumlah_izin }}</td>
                    <td style="text-align:center;color:#dc2626;font-weight:600;font-size:13.5px">{{ $r->jumlah_alpa }}</td>
                    <td style="text-align:center">
                        <span style="padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;display:inline-block;{{ $badgeStyle }}">
                            {{ $pct }}%
                        </span>
                    </td>
                    <td style="text-align:center">
                        <button onclick="lihatDetailJurnal({{ $r->id_jurnal ?? 0 }}, '{{ $tglFormatted }}', '{{ $namaHari }}', '{{ addslashes($r->nama_kelas ?? '') }}', '{{ addslashes($r->materi ?? '') }}')"
                            style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Lihat Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="riwayat-empty-state">
                    <td colspan="9" style="text-align:center;padding:48px 20px;color:#94a3b8">
                        <div style="width:54px;height:54px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div style="font-size:15px;font-weight:700;color:#475569;margin-bottom:4px">Belum Ada Data Jurnal</div>
                        <div style="font-size:12.5px;color:#94a3b8">Jurnal absensi akan otomatis tercatat di sini setelah guru menyimpan data absensi harian.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer: info + pagination --}}
        <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span id="riwayat-page-info" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan {{ $riwayatTotalCount > 0 ? '1' : '0' }} - {{ min(8, $riwayatTotalCount) }} dari {{ $riwayatTotalCount }} data
            </span>
            <div id="riwayat-pagination" style="display:flex;align-items:center;gap:4px"></div>
        </div>
    </div>

    {{-- ── Ringkasan Rekap Kehadiran (Kartu Bawah) ── --}}
    <div style="margin-top:24px">
        <div id="riwayat-rekap-title" style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:14px">
            Rekap Kehadiran Bulan Mei {{ date('Y') }}
        </div>
        <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;align-items:center" id="riwayat-summary-grid">

            {{-- Hadir --}}
            <div class="riwayat-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div style="width:40px;height:40px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div style="font-size:11px;color:#16a34a;font-weight:600">Hadir</div>
                    <div id="r-sum-hadir" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $totalHadirSum }}</div>
                </div>
            </div>

            {{-- Sakit --}}
            <div class="riwayat-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div style="width:40px;height:40px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <div>
                    <div style="font-size:11px;color:#d97706;font-weight:600">Sakit</div>
                    <div id="r-sum-sakit" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $totalSakitSum }}</div>
                </div>
            </div>

            {{-- Izin --}}
            <div class="riwayat-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div style="width:40px;height:40px;background:#f0f9ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div style="font-size:11px;color:#0284c7;font-weight:600">Izin</div>
                    <div id="r-sum-izin" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $totalIzinSum }}</div>
                </div>
            </div>

            {{-- Alpa --}}
            <div class="riwayat-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div style="width:40px;height:40px;background:#fff1f2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div style="font-size:11px;color:#e11d48;font-weight:600">Alpa</div>
                    <div id="r-sum-alpa" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $totalAlpaSum }}</div>
                </div>
            </div>

            {{-- Persentase Kehadiran --}}
            <div class="riwayat-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <div style="font-size:11px;color:#64748b;font-weight:600;margin-bottom:2px">Persentase Kehadiran</div>
                <div id="r-sum-pct" style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $overallPct }}%</div>
            </div>

            {{-- Export Rekap Button --}}
            <button type="button" onclick="window.print()" class="btn-primary" style="border-radius:12px;padding:14px 16px;font-size:13.5px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;box-shadow:0 4px 12px rgba(10,25,47,0.25)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Rekap
            </button>
        </div>
    </div>

</div>

{{-- ── Styles ── --}}
<style>
.riwayat-subhead th { padding-top:4px; padding-bottom:10px; border-bottom:1px solid #e2e8f0; text-transform:none; letter-spacing:0; font-size:12px; background:#f8fafc; }
.riwayat-summary-card { transition: transform 0.2s, box-shadow 0.2s; }
.riwayat-summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.09) !important; }
.riwayat-page-btn {
    min-width: 32px; height: 32px; padding: 0 8px;
    border: 1px solid #e2e8f0; background: #fff; border-radius: 6px;
    font-size: 12.5px; font-weight: 600; color: #64748b; cursor: pointer;
    font-family: inherit; transition: all 0.15s;
    display: inline-flex; align-items: center; justify-content: center;
}
.riwayat-page-btn:hover:not(:disabled) { background: #f1f5f9; color: #1e293b; }
.riwayat-page-btn.active { background: #1e293b; border-color: #1e293b; color: #fff; }
.riwayat-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

@media (max-width: 1100px) {
    #riwayat-summary-grid { grid-template-columns: repeat(3,1fr) !important; }
}
@media (max-width: 600px) {
    #riwayat-summary-grid { grid-template-columns: 1fr !important; }
}
</style>

{{-- ── JS Filter + Pagination + Detail Modal ── --}}
<script>
(function(){
    const PER_PAGE = 8;
    let riwayatPage = 1;
    let riwayatFiltered = [];

    function allRows(){ return Array.from(document.querySelectorAll('#riwayat-tbody-page .riwayat-row-item')); }

    window.filterRiwayatPage = function(){
        const bulanSelect = document.getElementById('riwayat-filter-bulan');
        const kelasSelect = document.getElementById('riwayat-filter-kelas');

        const bulan = (bulanSelect?.value || '');
        const kelas = (kelasSelect?.value || '');

        // Subtitle Update
        const sub = document.getElementById('riwayat-subtitle');
        if(sub && kelasSelect){
            const opt = kelasSelect.options[kelasSelect.selectedIndex];
            sub.textContent = 'Kelas ' + (kelas ? opt?.text : 'Semua Kelas');
        }

        // Title rekap update
        const rekapTitle = document.getElementById('riwayat-rekap-title');
        if(rekapTitle && bulanSelect){
            const optBulan = bulanSelect.options[bulanSelect.selectedIndex];
            rekapTitle.textContent = 'Rekap Kehadiran Bulan ' + (optBulan?.text || '');
        }

        riwayatFiltered = allRows().filter(row => {
            const rKelas = row.dataset.kelas || '';
            const rBulan = row.dataset.bulan || '';
            return (!kelas || rKelas === kelas)
                && (!bulan || rBulan === bulan);
        });

        updateRiwayatSummary();

        riwayatPage = 1;
        renderRiwayatPage();
    };

    function updateRiwayatSummary(){
        const rows = riwayatFiltered.length ? riwayatFiltered : allRows();
        let hadir=0, sakit=0, izin=0, alpa=0;
        rows.forEach(r => {
            const cells = r.querySelectorAll('td');
            if(cells.length >= 7){
                hadir += parseInt(cells[3]?.textContent || 0);
                sakit += parseInt(cells[4]?.textContent || 0);
                izin  += parseInt(cells[5]?.textContent || 0);
                alpa  += parseInt(cells[6]?.textContent || 0);
            }
        });
        const total = hadir + sakit + izin + alpa;
        const pct   = total > 0 ? Math.round((hadir / total) * 100) : 100;

        const set = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
        set('r-sum-hadir', hadir);
        set('r-sum-sakit', sakit);
        set('r-sum-izin', izin);
        set('r-sum-alpa', alpa);
        set('r-sum-pct', `${pct}%`);
    }

    function renderRiwayatPage(){
        const total      = riwayatFiltered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if(riwayatPage > totalPages) riwayatPage = totalPages;

        const start = (riwayatPage - 1) * PER_PAGE;
        const end   = Math.min(start + PER_PAGE, total);

        allRows().forEach(r => r.style.display = 'none');
        riwayatFiltered.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
            if(i >= start && i < end){
                r.querySelector('td').textContent = start + (i - start) + 1;
            }
        });

        const emptyRow = document.getElementById('riwayat-empty-state');
        if(emptyRow) emptyRow.style.display = total === 0 ? '' : 'none';

        const info = document.getElementById('riwayat-page-info');
        if(info){
            info.textContent = total === 0
                ? 'Tidak ada data jurnal yang ditemukan'
                : `Menampilkan ${start + 1} - ${end} dari ${total} data`;
        }

        buildRiwayatPagination(totalPages);
    }

    function buildRiwayatPagination(totalPages){
        const container = document.getElementById('riwayat-pagination');
        if(!container) return;
        container.innerHTML = '';

        const prev = document.createElement('button');
        prev.className = 'riwayat-page-btn';
        prev.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
        prev.disabled = riwayatPage <= 1;
        prev.onclick = () => { if(riwayatPage > 1){ riwayatPage--; renderRiwayatPage(); } };
        container.appendChild(prev);

        let pages = [];
        if(totalPages <= 5){
            for(let i=1;i<=totalPages;i++) pages.push(i);
        } else {
            pages = [1];
            if(riwayatPage > 3) pages.push('...');
            for(let i=Math.max(2,riwayatPage-1);i<=Math.min(totalPages-1,riwayatPage+1);i++) pages.push(i);
            if(riwayatPage < totalPages-2) pages.push('...');
            pages.push(totalPages);
        }

        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'riwayat-page-btn' + (p === riwayatPage ? ' active' : '');
            btn.textContent = p;
            if(p === '...'){
                btn.disabled = true;
                btn.style.cursor = 'default';
            } else {
                btn.onclick = () => { riwayatPage = p; renderRiwayatPage(); };
            }
            container.appendChild(btn);
        });

        const nxt = document.createElement('button');
        nxt.className = 'riwayat-page-btn';
        nxt.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
        nxt.disabled = riwayatPage >= totalPages;
        nxt.onclick = () => { if(riwayatPage < totalPages){ riwayatPage++; renderRiwayatPage(); } };
        container.appendChild(nxt);
    }

    function initRiwayatPage(){
        riwayatFiltered = allRows();
        renderRiwayatPage();
    }

    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-riwayat');
        if(page && page.style.display !== 'none'){
            if(riwayatFiltered.length === 0 && allRows().length > 0) initRiwayatPage();
        }
    });
    const target = document.getElementById('page-riwayat');
    if(target) observer.observe(target, { attributes: true, attributeFilter: ['style'] });
    if(target && target.style.display !== 'none') initRiwayatPage();
    setTimeout(initRiwayatPage, 300);
})();

function lihatDetailJurnal(id, tgl, hari, kelas, materi){
    Swal.fire({
        title: `Detail Jurnal (${tgl})`,
        html: `
            <div style="text-align:left;display:flex;flex-direction:column;gap:10px;font-size:13.5px;color:#1e293b;">
                <div><strong>Hari / Tanggal:</strong> ${hari}, ${tgl}</div>
                <div><strong>Kelas:</strong> ${kelas || '-'}</div>
                <div><strong>Materi Pembelajaran:</strong><br><span style="color:#64748b;">${materi || 'Pembelajaran Harian'}</span></div>
            </div>
        `,
        confirmButtonText: 'Tutup'
    });
}
</script>
