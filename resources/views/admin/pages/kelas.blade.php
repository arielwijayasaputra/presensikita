<div class="page-content page-anim" id="page-data-kelas" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>

            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Data Kelas</div>
            <div class="page-subtitle">
                @php
                    $tahunAjaranStr = ($tahunAjaran->tahun_ajaran ?? '2026/2027') . ' (' . ($tahunAjaran->semester ?? 'Ganjil') . ')';
                @endphp
                Tahun Ajaran {{ $tahunAjaranStr }}
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

            <button class="btn-primary" onclick="tambahKelasModal()" style="border-radius:10px;padding:10px 20px;font-size:13.5px">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kelas
            </button>
        </div>
    </div>

    {{-- ── Ringkasan Data Kelas ── --}}
    <div style="margin-bottom:20px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">
            Ringkasan Data Kelas
        </div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px" id="kelas-summary-grid">

            {{-- Total Kelas --}}
            <div class="kelas-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#2563eb;font-weight:600;margin-bottom:1px">Total Kelas</div>
                    <div id="ks-total-kelas" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ count($allKelas) }}</div>
                    <div style="font-size:11px;color:#94a3b8">Kelas</div>
                </div>
            </div>

            {{-- Total Siswa --}}
            @php $totalSiswaAll = $allKelas->sum('siswa_count'); @endphp
            <div class="kelas-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#16a34a;font-weight:600;margin-bottom:1px">Total Siswa</div>
                    <div id="ks-total-siswa" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $totalSiswaAll }}</div>
                    <div style="font-size:11px;color:#94a3b8">Siswa</div>
                </div>
            </div>

            {{-- Total Wali Kelas --}}
            @php $totalWali = $allKelas->pluck('id_wali_kelas')->filter()->unique()->count(); @endphp
            <div class="kelas-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#fffbeb;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#d97706;font-weight:600;margin-bottom:1px">Total Wali Kelas</div>
                    <div id="ks-total-wali" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $totalWali }}</div>
                    <div style="font-size:11px;color:#94a3b8">Orang</div>
                </div>
            </div>

            {{-- Kelas Aktif --}}
            <div class="kelas-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f5f3ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#7c3aed;font-weight:600;margin-bottom:1px">Kelas Aktif</div>
                    <div id="ks-kelas-aktif" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ count($allKelas) }}</div>
                    <div style="font-size:11px;color:#94a3b8">Kelas</div>
                </div>
            </div>

            {{-- Kelas Nonaktif --}}
            <div class="kelas-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#fff1f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#e11d48;font-weight:600;margin-bottom:1px">Kelas Nonaktif</div>
                    <div id="ks-kelas-nonaktif" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">0</div>
                    <div style="font-size:11px;color:#94a3b8">Kelas</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">

        {{-- Cari Kelas --}}
        <div class="filter-group" style="flex:1;min-width:200px">
            <label>Cari Kelas</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="filter-input" id="kelas-cari"
                    placeholder="Ketik nama kelas..."
                    oninput="filterKelasPage()"
                    style="width:100%;max-width:300px;padding-left:32px">
            </div>
        </div>

        {{-- Tingkat --}}
        <div class="filter-group" style="min-width:150px">
            <label>Tingkat</label>
            <div style="position:relative">
                <select class="filter-select" id="kelas-filter-tingkat" onchange="filterKelasPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Tingkat</option>
                    @php
                        $tingkats = $allKelas->pluck('tingkat_kelas')->unique()->filter()->sort();
                    @endphp
                    @foreach($tingkats as $t)
                    <option value="{{ $t }}">Tingkat {{ $t }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Jurusan --}}
        <div class="filter-group" style="min-width:150px">
            <label>Jurusan</label>
            <div style="position:relative">
                <select class="filter-select" id="kelas-filter-jurusan" onchange="filterKelasPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Jurusan</option>
                    @php
                        $jurusans = $allKelas->pluck('jurusan')->unique()->filter()->sort();
                    @endphp
                    @foreach($jurusans as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Wali Kelas --}}
        <div class="filter-group" style="min-width:160px">
            <label>Wali Kelas</label>
            <div style="position:relative">
                <select class="filter-select" id="kelas-filter-wali" onchange="filterKelasPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Wali Kelas</option>
                    @foreach($allGuru as $g)
                    <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>


    </div>

    <div id="checklist-bar-kelas" class="checklist-bar" style="display:none;margin-bottom:16px;padding:12px 18px;background:#eef2ff;border:1px solid #c7d2fe;border-radius:10px;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <span style="font-size:13px;font-weight:600;color:#4338ca">Terpilih: <strong id="checklist-count-kelas">0</strong> data</span>
        <div style="display:flex;gap:8px">
            <button class="btn-danger" onclick="hapusTerpilih('kelas')" style="border-radius:8px;padding:8px 16px;font-size:12.5px;background:#dc2626;border-color:#dc2626;color:#fff;font-weight:700">Hapus Terpilih</button>
            <button class="btn-secondary" onclick="cancelChecklist('kelas')" style="border-radius:8px;padding:8px 16px;font-size:12.5px">Batal</button>
        </div>
    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <table id="kelas-table">
            <thead>
                <tr>
                    <th style="width:40px"><input type="checkbox" class="checklist-select-all" data-page="kelas"></th>
                    <th style="width:48px">No.</th>
                    <th>Nama Kelas</th>
                    <th style="width:140px;text-align:center;padding-right:32px">Tingkat</th>
                    <th style="width:120px;text-align:center;padding-right:32px">Jurusan</th>
                    <th style="padding-left:20px">Wali Kelas</th>
                    <th style="width:130px;text-align:center;white-space:nowrap">Jumlah Siswa</th>
                    <th style="width:100px;text-align:center">Status</th>
                    <th style="width:90px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kelas-tbody-page">
                @forelse($allKelas as $idx => $k)
                <tr class="kelas-row-item"
                    data-search="{{ strtolower(($k->nama_kelas ?? '') . ' ' . ($k->waliKelas->nama_guru ?? '')) }}"
                    data-tingkat="{{ $k->tingkat_kelas }}"
                    data-jurusan="{{ $k->jurusan }}"
                    data-wali="{{ $k->id_wali_kelas }}"
                    data-status="aktif"
                    data-siswa-count="{{ $k->siswa_count }}">
                    <td style="width:40px"><input type="checkbox" class="checklist-item" data-id="{{ $k->id_kelas }}" data-page="kelas"></td>
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td style="font-weight:700;color:#1e293b;font-size:13.5px">{{ $k->nama_kelas }}</td>
                    <td style="text-align:center;color:#475569;font-size:13px;font-weight:500;padding-right:32px">
                        {{ $k->tingkat_kelas }} ({{ $k->tingkat_angka }})
                    </td>
                    <td style="text-align:center;padding-right:32px">
                        <span style="display:inline-block;background:#f0f9ff;border:1px solid #bae6fd;color:#0369a1;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:20px">
                            {{ $k->jurusan ?? '-' }}
                        </span>
                    </td>
                    <td style="font-weight:500;color:#334155;font-size:13.5px;padding-left:20px">
                        {{ $k->waliKelas->nama_guru ?? '-' }}
                    </td>
                    <td style="text-align:center;font-weight:600;color:#1e293b">
                        <button type="button"
                            onclick="lihatSiswaKelasModal({{ $k->id_kelas }}, '{{ htmlspecialchars($k->nama_kelas, ENT_QUOTES) }}', '{{ htmlspecialchars($k->waliKelas->nama_guru ?? '-', ENT_QUOTES) }}')"
                            style="background:#f1f5f9;border:1px solid #e2e8f0;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;color:#334155;cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:all 0.2s"
                            title="Klik untuk melihat daftar siswa">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>{{ $k->siswa_count }} Siswa</span>
                        </button>
                    </td>
                    <td style="text-align:center">
                        <span class="badge badge-success">Aktif</span>
                    </td>
                    <td style="text-align:center">
                        <div style="display:inline-flex;align-items:center;gap:6px">
                            <button
                                onclick="lihatSiswaKelasModal({{ $k->id_kelas }}, '{{ htmlspecialchars($k->nama_kelas, ENT_QUOTES) }}', '{{ htmlspecialchars($k->waliKelas->nama_guru ?? '-', ENT_QUOTES) }}')"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Lihat Daftar Siswa">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="13" r="3"/></svg>
                            </button>
                            <button
                                onclick="editKelasModal({{ $k->id_kelas }}, '{{ htmlspecialchars($k->nama_kelas, ENT_QUOTES) }}', '{{ $k->tingkat_kelas }}', '{{ htmlspecialchars($k->jurusan ?? '', ENT_QUOTES) }}', {{ $k->id_wali_kelas ?? 'null' }})"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button
                                onclick="hapusKelas({{ $k->id_kelas }}, '{{ htmlspecialchars($k->nama_kelas, ENT_QUOTES) }}')"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Hapus">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr id="kelas-empty-state" style="display:none">
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        Tidak ada data kelas yang cocok dengan pencarian/filter.
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Footer: info + pagination --}}
        <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span id="kelas-page-info" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan 1 - {{ min(10, count($allKelas)) }} dari {{ count($allKelas) }} data
            </span>
            <div id="kelas-pagination" style="display:flex;align-items:center;gap:4px"></div>
        </div>
    </div>

</div>

{{-- ── Styles ── --}}
<style>
.kelas-summary-card { transition: transform 0.2s, box-shadow 0.2s; }
.kelas-summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.09) !important; }
#page-data-kelas tbody button:hover { opacity: 0.8; transform: scale(1.07); }
.kelas-page-btn {
    min-width: 32px; height: 32px; padding: 0 8px;
    border: 1px solid #e2e8f0; background: #fff; border-radius: 6px;
    font-size: 12.5px; font-weight: 600; color: #64748b; cursor: pointer;
    font-family: inherit; transition: all 0.15s;
    display: inline-flex; align-items: center; justify-content: center;
}
.kelas-page-btn:hover:not(:disabled) { background: #f1f5f9; color: #1e293b; }
.kelas-page-btn.active { background: #1e293b; border-color: #1e293b; color: #fff; }
.kelas-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
@media (max-width: 1100px) {
    #page-data-kelas [style*="grid-template-columns:repeat(5"] { grid-template-columns: repeat(3,1fr) !important; }
}
@media (max-width: 768px) {
    #page-data-kelas [style*="grid-template-columns:repeat(5"] { grid-template-columns: repeat(2,1fr) !important; }
}
@media (max-width: 480px) {
    #page-data-kelas [style*="grid-template-columns:repeat(5"] { grid-template-columns: 1fr !important; }
.checklist-item { width:16px; height:16px; accent-color:#6366f1; cursor:pointer; }
.checklist-item:disabled { cursor:not-allowed; opacity:0.4; }
.checklist-select-all { width:16px; height:16px; accent-color:#6366f1; cursor:pointer; }
.checklist-select-all:disabled { cursor:not-allowed; opacity:0.4; }
.checklist-bar { animation: checklistIn 0.2s ease; }
@keyframes checklistIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
</style>

{{-- ── JS: Filter + Pagination ── --}}
<script>
(function(){
    const PER_PAGE = 10;
    let kelasPage = 1;
    let kelasFiltered = [];

    function allRows(){ return Array.from(document.querySelectorAll('#kelas-tbody-page .kelas-row-item')); }

    window.filterKelasPage = function(){
        const q        = (document.getElementById('kelas-cari')?.value || '').toLowerCase().trim();
        const tingkat  = (document.getElementById('kelas-filter-tingkat')?.value || '');
        const jurusan  = (document.getElementById('kelas-filter-jurusan')?.value || '');
        const wali     = (document.getElementById('kelas-filter-wali')?.value || '');
        const status   = (document.getElementById('kelas-filter-status')?.value || '');

        kelasFiltered = allRows().filter(row => {
            const hay      = row.dataset.search || '';
            const rTingkat = row.dataset.tingkat || '';
            const rJurusan = row.dataset.jurusan || '';
            const rWali    = row.dataset.wali || '';
            const rSt      = row.dataset.status || '';
            return (!q       || hay.includes(q))
                && (!tingkat || rTingkat === tingkat)
                && (!jurusan || rJurusan === jurusan)
                && (!wali    || rWali === wali)
                && (!status  || rSt === status);
        });

        updateKelasSummary();

        kelasPage = 1;
        renderKelasPage();
    };

    function updateKelasSummary(){
        const rows = kelasFiltered.length ? kelasFiltered : allRows();
        let totalKelas=0, totalSiswa=0, waliSet=new Set(), aktif=0, nonaktif=0;
        rows.forEach(r => {
            totalKelas++;
            totalSiswa += parseInt(r.dataset.siswaCount || 0);
            if(r.dataset.wali) waliSet.add(r.dataset.wali);
            if(r.dataset.status==='aktif') aktif++; else nonaktif++;
        });
        const set = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
        set('ks-total-kelas', totalKelas);
        set('ks-total-siswa', totalSiswa);
        set('ks-total-wali', waliSet.size);
        set('ks-kelas-aktif', aktif);
        set('ks-kelas-nonaktif', nonaktif);
    }

    function renderKelasPage(){
        const total      = kelasFiltered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if(kelasPage > totalPages) kelasPage = totalPages;

        const start = (kelasPage - 1) * PER_PAGE;
        const end   = Math.min(start + PER_PAGE, total);

        allRows().forEach(r => r.style.display = 'none');
        kelasFiltered.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
            if(i >= start && i < end){
                r.querySelectorAll('td')[1].textContent = start + (i - start) + 1;
            }
        });

        const emptyRow = document.getElementById('kelas-empty-state');
        if(emptyRow) emptyRow.style.display = total === 0 ? '' : 'none';

        const info = document.getElementById('kelas-page-info');
        if(info){
            info.textContent = total === 0
                ? 'Tidak ada data yang ditemukan'
                : `Menampilkan ${start + 1} - ${end} dari ${total} data`;
        }

        buildKelasPagination(totalPages);
    }

    function buildKelasPagination(totalPages){
        const container = document.getElementById('kelas-pagination');
        if(!container) return;
        container.innerHTML = '';

        const prev = document.createElement('button');
        prev.className = 'kelas-page-btn';
        prev.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
        prev.disabled = kelasPage <= 1;
        prev.onclick = () => { if(kelasPage > 1){ kelasPage--; renderKelasPage(); } };
        container.appendChild(prev);

        let pages = [];
        if(totalPages <= 5){
            for(let i=1;i<=totalPages;i++) pages.push(i);
        } else {
            pages = [1];
            if(kelasPage > 3) pages.push('...');
            for(let i=Math.max(2,kelasPage-1);i<=Math.min(totalPages-1,kelasPage+1);i++) pages.push(i);
            if(kelasPage < totalPages-2) pages.push('...');
            pages.push(totalPages);
        }

        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'kelas-page-btn' + (p === kelasPage ? ' active' : '');
            btn.textContent = p;
            if(p === '...'){
                btn.disabled = true;
                btn.style.cursor = 'default';
            } else {
                btn.onclick = () => { kelasPage = p; renderKelasPage(); };
            }
            container.appendChild(btn);
        });

        const nxt = document.createElement('button');
        nxt.className = 'kelas-page-btn';
        nxt.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
        nxt.disabled = kelasPage >= totalPages;
        nxt.onclick = () => { if(kelasPage < totalPages){ kelasPage++; renderKelasPage(); } };
        container.appendChild(nxt);
    }

    function initKelasPage(){
        kelasFiltered = allRows();
        renderKelasPage();
    }

    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-data-kelas');
        if(page && page.style.display !== 'none'){
            if(kelasFiltered.length === 0 && allRows().length > 0) initKelasPage();
        }
    });
    const target = document.getElementById('page-data-kelas');
    if(target) observer.observe(target, { attributes: true, attributeFilter: ['style'] });
    if(target && target.style.display !== 'none') initKelasPage();
    setTimeout(initKelasPage, 300);
})();

function editKelasModal(id, nama, tingkat, jurusan, idWali){
    Swal.fire({
        title: 'Edit Data Kelas',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div class="swal-form-container">
                <div class="swal-form-group">
                    <label for="swal-edit-kname">Nama Kelas</label>
                    <input id="swal-edit-kname" class="swal-form-input" value="${nama}" placeholder="Contoh: VII-A">
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-edit-ktingkat">Tingkat</label>
                        <input id="swal-edit-ktingkat" class="swal-form-input" value="${tingkat}" placeholder="Contoh: VII, VIII, IX">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-edit-kjurusan">Jurusan</label>
                        <select id="swal-edit-kjurusan" class="swal-form-select">
                            <option value="">Pilih Jurusan</option>
                            @foreach($allJurusan as $j)
                            <option value="{{ $j->kode_jurusan }}">{{ $j->kode_jurusan }} - {{ $j->nama_jurusan }} {{ !$j->is_aktif ? '(Nonaktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="swal-form-group">
                    <label for="swal-edit-kwali">Wali Kelas</label>
                    <select id="swal-edit-kwali" class="swal-form-select">
                        <option value="">Pilih Wali Kelas</option>
                        @foreach($allGuru as $g)
                        <option value="{{ $g->id_guru }}">{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        didOpen: () => {
            const selectWali = document.getElementById('swal-edit-kwali');
            if(selectWali && idWali) selectWali.value = idWali;
            const selectJurusan = document.getElementById('swal-edit-kjurusan');
            if(selectJurusan && jurusan) selectJurusan.value = jurusan;
        },
        preConfirm: () => {
            const newNama = document.getElementById('swal-edit-kname').value.trim();
            if(!newNama){
                Swal.showValidationMessage('Nama kelas tidak boleh kosong');
                return false;
            }
            return {
                nama_kelas:    newNama,
                tingkat_kelas: document.getElementById('swal-edit-ktingkat').value.trim(),
                jurusan:       document.getElementById('swal-edit-kjurusan').value.trim(),
                id_wali_kelas: document.getElementById('swal-edit-kwali').value
            };
        }
    }).then(result => {
        if(result.isConfirmed && result.value){
            fetch(`/kelas/${id}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(async res => {
                const data = await res.json();
                if(!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal memperbarui data');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: data.message || 'Data berhasil diperbarui.',
                    customClass: { popup:'custom-swal-popup', title:'custom-swal-title', confirmButton:'custom-swal-confirm' },
                    buttonsStyling: false
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

function lihatSiswaKelasModal(id, namaKelas, waliKelas) {
    Swal.fire({
        title: 'Memuat Data Siswa...',
        html: `<div style="padding:24px;text-align:center"><div class="swal2-loading" style="display:inline-block"></div><p style="margin-top:12px;color:#64748b;font-size:13.5px">Mengambil daftar siswa kelas <strong>${namaKelas}</strong>...</p></div>`,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
            fetch(`/kelas/${id}/siswa`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json())
            .then(res => {
                if(res.status !== 'success') {
                    throw new Error(res.message || 'Gagal memuat data siswa');
                }
                const siswaList = res.data || [];
                const total = siswaList.length;
                const totalL = siswaList.filter(s => (s.jenis_kelamin || '').toUpperCase() === 'L').length;
                const totalP = siswaList.filter(s => (s.jenis_kelamin || '').toUpperCase() === 'P').length;

                let rowsHtml = '';
                if(total === 0) {
                    rowsHtml = `<tr><td colspan="4" style="text-align:center;padding:48px 16px;color:#94a3b8;font-style:italic"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg><span style="font-size:13.5px">Belum ada siswa yang terdaftar aktif di kelas <strong>${namaKelas}</strong>.</span></td></tr>`;
                } else {
                    siswaList.forEach((s, idx) => {
                        const jkBadge = s.jenis_kelamin === 'L'
                            ? `<span style="display:inline-flex;align-items:center;gap:5px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:12px;font-weight:700;padding:4px 10px;border-radius:20px;white-space:nowrap"><span style="width:6px;height:6px;background:#2563eb;border-radius:50%"></span>Laki-laki (L)</span>`
                            : (s.jenis_kelamin === 'P'
                                ? `<span style="display:inline-flex;align-items:center;gap:5px;background:#fdf2f8;color:#be185d;border:1px solid #fbcfe8;font-size:12px;font-weight:700;padding:4px 10px;border-radius:20px;white-space:nowrap"><span style="width:6px;height:6px;background:#db2777;border-radius:50%"></span>Perempuan (P)</span>`
                                : `<span style="color:#94a3b8">-</span>`);

                        rowsHtml += `
                            <tr class="modal-siswa-row" data-search="${(s.nama_siswa + ' ' + (s.nisn || '')).toLowerCase()}" style="transition:background-color 0.15s ease">
                                <td style="color:#94a3b8;font-weight:700;font-size:12.5px;text-align:center;padding:12px 14px;border-bottom:1px solid #f1f5f9;white-space:nowrap">${idx + 1}</td>
                                <td style="font-family:monospace;font-size:13px;font-weight:600;color:#475569;padding:12px 14px;border-bottom:1px solid #f1f5f9;white-space:nowrap">${s.nisn || '<span style="color:#cbd5e1">-</span>'}</td>
                                <td style="font-weight:700;color:#0f172a;font-size:13.5px;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-align:left">${s.nama_siswa}</td>
                                <td style="text-align:center;padding:12px 14px;border-bottom:1px solid #f1f5f9;white-space:nowrap">${jkBadge}</td>
                            </tr>
                        `;
                    });
                }

                Swal.fire({
                    title: `Daftar Siswa — ${namaKelas}`,
                    customClass: {
                        popup: 'custom-swal-popup modal-wide-siswa',
                        title: 'custom-swal-title',
                        confirmButton: 'custom-swal-confirm'
                    },
                    width: '840px',
                    html: `
                        <div style="text-align:left;margin-bottom:16px">
                            <!-- Info Bar & Badges -->
                            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px">
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="width:34px;height:34px;background:#eff6ff;color:#2563eb;border-radius:9px;display:flex;align-items:center;justify-content:center">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em">Wali Kelas</div>
                                        <div style="font-size:13px;font-weight:700;color:#1e293b">${waliKelas || '-'}</div>
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                    <span style="display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #cbd5e1;color:#1e293b;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:800;box-shadow:0 1px 2px rgba(0,0,0,0.04)">
                                        <span style="width:7px;height:7px;background:#3b82f6;border-radius:50%"></span>
                                        Total: ${total} Siswa
                                    </span>
                                    <span style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:800">
                                        L: ${totalL}
                                    </span>
                                    <span style="display:inline-flex;align-items:center;gap:6px;background:#fdf2f8;border:1px solid #fbcfe8;color:#be185d;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:800">
                                        P: ${totalP}
                                    </span>
                                </div>
                            </div>

                            <!-- Search Input -->
                            ${total > 0 ? `
                            <div style="position:relative;margin-bottom:12px">
                                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" id="modal-cari-siswa" placeholder="Cari berdasarkan nama lengkap atau NISN..." oninput="filterModalSiswaList(this.value)" style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #cbd5e1;border-radius:10px;font-size:13px;outline:none;box-sizing:border-box;background:#fff;transition:border-color 0.2s, box-shadow 0.2s;">
                            </div>
                            ` : ''}
                        </div>

                        <!-- Data Table Container -->
                        <div style="max-height:380px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:12px;background:#ffffff;box-shadow:0 1px 3px rgba(0,0,0,0.03)">
                            <table style="width:100%;border-collapse:collapse;table-layout:auto">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;position:sticky;top:0;z-index:2">
                                        <th style="width:50px;text-align:center;padding:11px 14px;font-size:11.5px;color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;white-space:nowrap">No</th>
                                        <th style="width:150px;text-align:left;padding:11px 14px;font-size:11.5px;color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;white-space:nowrap">NISN</th>
                                        <th style="text-align:left;padding:11px 16px;font-size:11.5px;color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:0.04em">Nama Lengkap Siswa</th>
                                        <th style="width:160px;text-align:center;padding:11px 14px;font-size:11.5px;color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:0.04em;white-space:nowrap">Jenis Kelamin</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-siswa-tbody">
                                    ${rowsHtml}
                                </tbody>
                            </table>
                        </div>
                    `,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#2563eb'
                });
            })
            .catch(err => {
                Swal.fire('Gagal', err.message || 'Gagal memuat data siswa.', 'error');
            });
        }
    });
}

function filterModalSiswaList(q) {
    q = (q || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.modal-siswa-row');
    let matchCount = 0;
    rows.forEach(r => {
        const hay = r.dataset.search || '';
        const isMatch = (!q || hay.includes(q));
        r.style.display = isMatch ? '' : 'none';
        if(isMatch) matchCount++;
    });

    const tbody = document.getElementById('modal-siswa-tbody');
    if(tbody) {
        let emptySearch = tbody.querySelector('.modal-search-empty');
        if(matchCount === 0 && rows.length > 0) {
            if(!emptySearch) {
                emptySearch = document.createElement('tr');
                emptySearch.className = 'modal-search-empty';
                emptySearch.innerHTML = '<td colspan="4" style="text-align:center;padding:30px;color:#94a3b8;font-style:italic">Tidak ada siswa yang cocok dengan kata kunci pencarian.</td>';
                tbody.appendChild(emptySearch);
            }
            emptySearch.style.display = '';
        } else if(emptySearch) {
            emptySearch.style.display = 'none';
        }
    }
}
</script>
