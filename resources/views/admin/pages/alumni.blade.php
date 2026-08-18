<div class="page-content page-anim" id="page-alumni" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Data Alumni</div>
            <div class="page-subtitle">
                @php
                    $tahunAjaranStr = ($tahunAjaran->tahun_ajaran ?? '2026/2027') . ' (' . ($tahunAjaran->semester ?? 'Ganjil') . ')';
                @endphp
                Tahun Ajaran {{ $tahunAjaranStr }}
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">
        <div class="filter-group" style="min-width:160px">
            <label>Tahun Lulus</label>
            <div style="position:relative">
                <select class="filter-select" id="alumni-filter-tahun" onchange="filterAlumni()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Tahun</option>
                    @php $tahunList = $alumniTahunan->pluck('tahun_lulus')->sort()->reverse(); @endphp
                    @foreach($tahunList as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div class="filter-group" style="flex:1;min-width:200px">
            <label>Cari Alumni</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="filter-input" id="alumni-cari"
                    placeholder="Ketik nama atau NIS..."
                    oninput="filterAlumni()"
                    style="width:100%;max-width:320px;padding-left:32px">
            </div>
        </div>
        <div class="filter-hint filter-hint-info">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span id="alumni-info">Menampilkan {{ $allAlumni->count() }} alumni</span>
        </div>
    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:24px">
        <table id="alumni-table">
            <thead>
                <tr>
                    <th style="width:48px">No.</th>
                    <th>Nama Alumni</th>
                    <th style="width:130px">NIS</th>
                    <th style="width:120px">Kelas Asal</th>
                    <th style="width:100px;text-align:center">Tahun Lulus</th>
                    <th style="width:100px;text-align:center">Jenis Kelamin</th>
                </tr>
            </thead>
            <tbody id="alumni-tbody">
                @forelse($allAlumni as $idx => $a)
                <tr class="alumni-row-item"
                    data-search="{{ strtolower(($a->nisn ?? '') . ' ' . $a->nama_siswa) }}"
                    data-tahun="{{ $a->tahun_lulus }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td style="font-weight:600;color:#1e293b;font-size:13.5px">{{ $a->nama_siswa }}</td>
                    <td style="font-family:monospace;font-size:13px;color:#475569">{{ $a->nisn ?? '-' }}</td>
                    <td>
                        <span class="badge badge-info">{{ $a->nama_kelas ?? '-' }}</span>
                    </td>
                    <td style="text-align:center">
                        <span class="badge badge-success">{{ $a->tahun_lulus }}</span>
                    </td>
                    <td style="text-align:center">
                        @if($a->jenis_kelamin === 'L')
                            <span style="display:inline-flex;align-items:center;gap:5px;color:#2563eb;font-size:13px;font-weight:500">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="14" r="5"/><path d="M19 5l-5.9 5.9M15 5h4v4"/></svg>
                                Laki-laki
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:5px;color:#db2777;font-size:13px;font-weight:500">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="9" r="5"/><line x1="12" y1="14" x2="12" y2="21"/><line x1="9" y1="18" x2="15" y2="18"/></svg>
                                Perempuan
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Belum ada data alumni.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <span id="alumni-page-info" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan {{ $allAlumni->count() }} alumni
            </span>
            <div id="alumni-pagination" style="display:flex;align-items:center;gap:4px"></div>
        </div>
    </div>

    {{-- ── Ringkasan per Tahun ── --}}
    <div>
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">Ringkasan per Tahun Kelulusan</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px">
            @forelse($alumniTahunan as $a)
            <div class="card" style="padding:16px 18px;display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="stat-icon green" style="width:36px;height:36px">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#1e293b">{{ $a->tahun_lulus }}</div>
                        <div style="font-size:12px;color:#94a3b8">Tahun Kelulusan</div>
                    </div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:20px;font-weight:800;color:#15803d">{{ $a->jumlah }}</div>
                    <div style="font-size:11px;color:#94a3b8">alumni</div>
                </div>
            </div>
            @empty
            <div class="card" style="text-align:center;padding:30px;color:#94a3b8;grid-column:1/-1">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 8px;display:block"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Belum ada data alumni.
            </div>
            @endforelse
        </div>
    </div>

</div>

<script>
(function(){
    const PER_PAGE = 10;
    let alumniPage = 1;
    let alumniFiltered = [];

    function allRows(){ return Array.from(document.querySelectorAll('#alumni-tbody .alumni-row-item')); }

    window.filterAlumni = function(){
        const q     = (document.getElementById('alumni-cari')?.value || '').toLowerCase().trim();
        const tahun = (document.getElementById('alumni-filter-tahun')?.value || '').toLowerCase();

        alumniFiltered = allRows().filter(row => {
            const hay = row.dataset.search || '';
            const t   = (row.dataset.tahun || '').toLowerCase();
            return (!q    || hay.includes(q))
                && (!tahun || t === tahun);
        });

        alumniPage = 1;
        renderAlumniPage();
    };

    function renderAlumniPage(){
        const total   = alumniFiltered.length;
        const totalPages = Math.ceil(total / PER_PAGE) || 1;
        if(alumniPage > totalPages) alumniPage = totalPages;

        const start = (alumniPage - 1) * PER_PAGE;
        const end   = start + PER_PAGE;

        allRows().forEach(r => r.style.display = 'none');
        alumniFiltered.slice(start, end).forEach(r => r.style.display = '');

        document.getElementById('alumni-page-info').textContent =
            total > 0
                ? `Menampilkan ${start + 1} - ${Math.min(end, total)} dari ${total} data`
                : 'Tidak ada data';

        renderAlumniPagination(totalPages);
    }

    function renderAlumniPagination(totalPages){
        const c = document.getElementById('alumni-pagination');
        if(!c) return;
        c.innerHTML = '';

        const btn = (label, pg, active=false, disabled=false) => {
            const b = document.createElement('button');
            b.className = 'alumni-page-btn' + (active ? ' active' : '');
            b.textContent = label;
            b.disabled = disabled;
            if(!disabled && !active) b.onclick = () => { alumniPage = pg; renderAlumniPage(); };
            c.appendChild(b);
        };

        btn('‹', alumniPage - 1, false, alumniPage <= 1);

        let startP = Math.max(1, alumniPage - 2);
        let endP   = Math.min(totalPages, startP + 4);
        if(endP - startP < 4) startP = Math.max(1, endP - 4);

        for(let i = startP; i <= endP; i++){
            btn(i, i, i === alumniPage);
        }

        btn('›', alumniPage + 1, false, alumniPage >= totalPages);
    }

    alumniFiltered = allRows();
    renderAlumniPage();
})();
</script>
