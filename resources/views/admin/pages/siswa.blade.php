<div class="page-content page-anim" id="page-data-siswa" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>
            
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Data Siswa</div>
            <div class="page-subtitle" id="siswa-kelas-subtitle">
                {{ $allKelas->first()->nama_kelas ?? 'Semua Kelas' }}
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <button class="btn-primary" onclick="tambahSiswaModal()" style="border-radius:10px;padding:10px 20px;font-size:13.5px">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Siswa
            </button>
            <button class="btn-primary" onclick="uploadCsvModal()" style="border-radius:10px;padding:10px 20px;font-size:13.5px;background:var(--green,#22c55e);border-color:var(--green,#22c55e)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload CSV
            </button>
            <button class="btn-primary" onclick="hapusSemuaSiswa()" style="border-radius:10px;padding:10px 20px;font-size:13.5px;background:var(--red,#ef4444);border-color:var(--red,#ef4444)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Hapus Semua
            </button>
        </div>
    </div>

    {{-- ── Ringkasan Data Siswa ── --}}
    <div style="margin-bottom:20px">
        <div id="siswa-ringkasan-title" style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">
            Ringkasan Data Siswa
        </div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px" id="siswa-summary-grid">

            {{-- Total Siswa --}}
            <div class="siswa-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#2563eb;font-weight:600;margin-bottom:1px">Total Siswa</div>
                    <div id="ss-total" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $allSiswa->count() }}</div>
                    <div style="font-size:11px;color:#94a3b8">Orang</div>
                </div>
            </div>

            {{-- Perempuan --}}
            @php $jmlP = $allSiswa->where('jenis_kelamin','P')->count(); @endphp
            <div class="siswa-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#fdf2f8;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#db2777" stroke-width="1.8"><circle cx="12" cy="9" r="5"/><line x1="12" y1="14" x2="12" y2="21"/><line x1="9" y1="18" x2="15" y2="18"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#db2777;font-weight:600;margin-bottom:1px">Perempuan</div>
                    <div id="ss-perempuan" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $jmlP }}</div>
                    <div style="font-size:11px;color:#94a3b8">Orang</div>
                </div>
            </div>

            {{-- Laki-laki --}}
            @php $jmlL = $allSiswa->where('jenis_kelamin','L')->count(); @endphp
            <div class="siswa-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><circle cx="10" cy="14" r="5"/><path d="M19 5l-5.9 5.9M15 5h4v4"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#2563eb;font-weight:600;margin-bottom:1px">Laki-laki</div>
                    <div id="ss-lakilaki" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $jmlL }}</div>
                    <div style="font-size:11px;color:#94a3b8">Orang</div>
                </div>
            </div>

            {{-- Siswa Aktif --}}
            @php $jmlAktif = $allSiswa->where('is_aktif',1)->count(); @endphp
            <div class="siswa-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#16a34a;font-weight:600;margin-bottom:1px">Siswa Aktif</div>
                    <div id="ss-aktif" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $jmlAktif }}</div>
                    <div style="font-size:11px;color:#94a3b8">Orang</div>
                </div>
            </div>

            {{-- Siswa Nonaktif --}}
            @php $jmlNonaktif = $allSiswa->where('is_aktif',0)->count(); @endphp
            <div class="siswa-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#fff7ed;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#d97706;font-weight:600;margin-bottom:1px">Siswa Nonaktif</div>
                    <div id="ss-nonaktif" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $jmlNonaktif }}</div>
                    <div style="font-size:11px;color:#94a3b8">Orang</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">

        {{-- Pilih Kelas --}}
        <div class="filter-group" style="min-width:160px">
            <label>Pilih Kelas</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <select class="filter-select" id="siswa-filter-kelas" onchange="filterSiswaPage()" style="width:100%;padding-left:30px;padding-right:28px;appearance:none">
                    <option value="">Semua Kelas</option>
                    @foreach($allKelas as $k)
                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Cari Siswa --}}
        <div class="filter-group" style="flex:1;min-width:200px">
            <label>Cari Siswa</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="filter-input" id="siswa-cari"
                    placeholder="Ketik nama atau NISN..."
                    oninput="filterSiswaPage()"
                    style="width:100%;max-width:300px;padding-left:32px">
            </div>
        </div>

        {{-- Jenis Kelamin --}}
        <div class="filter-group" style="min-width:140px">
            <label>Jenis Kelamin</label>
            <div style="position:relative">
                <select class="filter-select" id="siswa-filter-jk" onchange="filterSiswaPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Status --}}
        <div class="filter-group" style="min-width:130px">
            <label>Status</label>
            <div style="position:relative">
                <select class="filter-select" id="siswa-filter-status" onchange="filterSiswaPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <table id="siswa-table">
            <thead>
                <tr>
                    <th style="width:48px">No.</th>
                    <th style="width:110px">NISN</th>
                    <th>Nama Siswa</th>
                    <th style="width:140px;text-align:center">Jenis Kelamin</th>
                    <th style="width:120px">Kelas</th>
                    <th style="width:110px;text-align:center">Status</th>
                    <th style="width:90px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="siswa-tbody-page">
                @forelse($allSiswa as $idx => $s)
                <tr class="siswa-row-item"
                    data-search="{{ strtolower(($s->nisn ?? '') . ' ' . $s->nama_siswa) }}"
                    data-kelas="{{ $s->id_kelas }}"
                    data-jk="{{ $s->jenis_kelamin }}"
                    data-status="{{ $s->is_aktif ? 'aktif' : 'nonaktif' }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td style="font-family:monospace;font-size:13px;color:#475569">{{ $s->nisn ?? '-' }}</td>
                    <td style="font-weight:600;color:#1e293b;font-size:13.5px">{{ $s->nama_siswa }}</td>
                    <td style="text-align:center">
                        @if($s->jenis_kelamin === 'L')
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
                    <td>
                        <span class="badge badge-info">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                    </td>
                    <td style="text-align:center">
                        @if($s->is_aktif)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-warning">Nonaktif</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:inline-flex;align-items:center;gap:6px">
                            <button
                                onclick="editSiswaModal({{ $s->id_siswa }}, '{{ htmlspecialchars($s->nisn ?? '') }}', '{{ htmlspecialchars($s->nama_siswa) }}', '{{ $s->jenis_kelamin }}', {{ $s->id_kelas ?? 'null' }})"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button
                                onclick="hapusSiswa({{ $s->id_siswa }}, '{{ htmlspecialchars($s->nama_siswa) }}')"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Hapus">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="siswa-empty-state">
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Belum ada data siswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer: info + pagination --}}
        <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span id="siswa-page-info" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan 1 - {{ min(10, $allSiswa->count()) }} dari {{ $allSiswa->count() }} data
            </span>
            <div id="siswa-pagination" style="display:flex;align-items:center;gap:4px"></div>
        </div>
    </div>

</div>

{{-- ── Styles ── --}}
<style>
.siswa-summary-card { transition: transform 0.2s, box-shadow 0.2s; }
.siswa-summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.09) !important; }
#page-data-siswa tbody button:hover { opacity: 0.8; transform: scale(1.07); }
.siswa-page-btn {
    min-width: 32px; height: 32px; padding: 0 8px;
    border: 1px solid #e2e8f0; background: #fff; border-radius: 6px;
    font-size: 12.5px; font-weight: 600; color: #64748b; cursor: pointer;
    font-family: inherit; transition: all 0.15s;
    display: inline-flex; align-items: center; justify-content: center;
}
.siswa-page-btn:hover:not(:disabled) { background: #f1f5f9; color: #1e293b; }
.siswa-page-btn.active { background: #1e293b; border-color: #1e293b; color: #fff; }
.siswa-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
@media (max-width: 1100px) {
    #page-data-siswa [style*="grid-template-columns:repeat(5"] { grid-template-columns: repeat(3,1fr) !important; }
}
@media (max-width: 768px) {
    #page-data-siswa [style*="grid-template-columns:repeat(5"] { grid-template-columns: repeat(2,1fr) !important; }
}
@media (max-width: 480px) {
    #page-data-siswa [style*="grid-template-columns:repeat(5"] { grid-template-columns: 1fr !important; }
}
</style>

{{-- ── JS: Filter + Pagination ── --}}
<script>
(function(){
    const PER_PAGE = 10;
    let siswaPage = 1;
    let siswaFiltered = [];

    function allRows(){ return Array.from(document.querySelectorAll('#siswa-tbody-page .siswa-row-item')); }

    window.filterSiswaPage = function(){
        const q      = (document.getElementById('siswa-cari')?.value || '').toLowerCase().trim();
        const kelas  = (document.getElementById('siswa-filter-kelas')?.value || '');
        const jk     = (document.getElementById('siswa-filter-jk')?.value || '');
        const status = (document.getElementById('siswa-filter-status')?.value || '');

        // Update subtitle
        const kelasSelect = document.getElementById('siswa-filter-kelas');
        const subtitle = document.getElementById('siswa-kelas-subtitle');
        if(subtitle && kelasSelect){
            const opt = kelasSelect.options[kelasSelect.selectedIndex];
            subtitle.textContent = opt?.text || 'Semua Kelas';
        }

        // Update ringkasan title
        const title = document.getElementById('siswa-ringkasan-title');
        if(title && kelasSelect){
            const opt = kelasSelect.options[kelasSelect.selectedIndex];
            const namaKelas = kelas ? opt?.text : 'Semua Kelas';
            title.textContent = 'Ringkasan Data Siswa ' + namaKelas;
        }

        siswaFiltered = allRows().filter(row => {
            const hay    = row.dataset.search || '';
            const rKelas = row.dataset.kelas || '';
            const rJk    = row.dataset.jk || '';
            const rSt    = row.dataset.status || '';
            return (!q      || hay.includes(q))
                && (!kelas  || rKelas === kelas)
                && (!jk     || rJk === jk)
                && (!status || rSt === status);
        });

        // Update summary counts dynamically
        updateSiswaSummary();

        siswaPage = 1;
        renderSiswaPage();
    };

    function updateSiswaSummary(){
        const rows = siswaFiltered.length ? siswaFiltered : allRows();
        let total=0, laki=0, perempuan=0, aktif=0, nonaktif=0;
        rows.forEach(r => {
            total++;
            if(r.dataset.jk==='L') laki++; else perempuan++;
            if(r.dataset.status==='aktif') aktif++; else nonaktif++;
        });
        const set = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
        set('ss-total', total);
        set('ss-lakilaki', laki);
        set('ss-perempuan', perempuan);
        set('ss-aktif', aktif);
        set('ss-nonaktif', nonaktif);
    }

    function renderSiswaPage(){
        const total      = siswaFiltered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if(siswaPage > totalPages) siswaPage = totalPages;

        const start = (siswaPage - 1) * PER_PAGE;
        const end   = Math.min(start + PER_PAGE, total);

        allRows().forEach(r => r.style.display = 'none');
        siswaFiltered.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
            if(i >= start && i < end){
                r.querySelector('td').textContent = start + (i - start) + 1;
            }
        });

        const emptyRow = document.getElementById('siswa-empty-state');
        if(emptyRow) emptyRow.style.display = total === 0 ? '' : 'none';

        const info = document.getElementById('siswa-page-info');
        if(info){
            info.textContent = total === 0
                ? 'Tidak ada data yang ditemukan'
                : `Menampilkan ${start + 1} - ${end} dari ${total} data`;
        }

        buildSiswaPagination(totalPages);
    }

    function buildSiswaPagination(totalPages){
        const container = document.getElementById('siswa-pagination');
        if(!container) return;
        container.innerHTML = '';

        const prev = document.createElement('button');
        prev.className = 'siswa-page-btn';
        prev.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
        prev.disabled = siswaPage <= 1;
        prev.onclick = () => { if(siswaPage > 1){ siswaPage--; renderSiswaPage(); } };
        container.appendChild(prev);

        let pages = [];
        if(totalPages <= 5){
            for(let i=1;i<=totalPages;i++) pages.push(i);
        } else {
            pages = [1];
            if(siswaPage > 3) pages.push('...');
            for(let i=Math.max(2,siswaPage-1);i<=Math.min(totalPages-1,siswaPage+1);i++) pages.push(i);
            if(siswaPage < totalPages-2) pages.push('...');
            pages.push(totalPages);
        }

        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'siswa-page-btn' + (p === siswaPage ? ' active' : '');
            btn.textContent = p;
            if(p === '...'){
                btn.disabled = true;
                btn.style.cursor = 'default';
            } else {
                btn.onclick = () => { siswaPage = p; renderSiswaPage(); };
            }
            container.appendChild(btn);
        });

        const nxt = document.createElement('button');
        nxt.className = 'siswa-page-btn';
        nxt.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
        nxt.disabled = siswaPage >= totalPages;
        nxt.onclick = () => { if(siswaPage < totalPages){ siswaPage++; renderSiswaPage(); } };
        container.appendChild(nxt);
    }

    function initSiswaPage(){
        siswaFiltered = allRows();
        renderSiswaPage();
    }

    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-data-siswa');
        if(page && page.style.display !== 'none'){
            if(siswaFiltered.length === 0 && allRows().length > 0) initSiswaPage();
        }
    });
    const target = document.getElementById('page-data-siswa');
    if(target) observer.observe(target, { attributes: true, attributeFilter: ['style'] });
    if(target && target.style.display !== 'none') initSiswaPage();
    setTimeout(initSiswaPage, 300);
})();

function editSiswaModal(id, nisn, nama, jk, idKelas){
    Swal.fire({
        title: 'Edit Data Siswa',
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
                    <label for="swal-edit-snama">Nama Lengkap Siswa</label>
                    <input id="swal-edit-snama" class="swal-form-input" value="${nama}" placeholder="Masukkan nama siswa...">
                </div>
                <div class="swal-form-group">
                    <label for="swal-edit-snisn">NISN</label>
                    <input id="swal-edit-snisn" class="swal-form-input" value="${nisn}" placeholder="Contoh: 0092124616">
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-edit-skelas">Kelas</label>
                        <select id="swal-edit-skelas" class="swal-form-select"></select>
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-edit-sjk">Jenis Kelamin</label>
                        <select id="swal-edit-sjk" class="swal-form-select">
                            <option value="L" ${jk==='L'?'selected':''}>Laki-laki</option>
                            <option value="P" ${jk==='P'?'selected':''}>Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        didOpen: () => {
            const kSelect = document.getElementById('pilih-kelas');
            const swalKSelect = document.getElementById('swal-edit-skelas');
            if(kSelect && swalKSelect){
                swalKSelect.innerHTML = kSelect.innerHTML;
                if(idKelas) swalKSelect.value = idKelas;
            }
        },
        preConfirm: () => {
            const newNama = document.getElementById('swal-edit-snama').value.trim();
            if(!newNama){
                Swal.showValidationMessage('Nama siswa tidak boleh kosong');
                return false;
            }
            return {
                nama_siswa:    newNama,
                nisn:          document.getElementById('swal-edit-snisn').value.trim(),
                id_kelas:      document.getElementById('swal-edit-skelas').value,
                jenis_kelamin: document.getElementById('swal-edit-sjk').value
            };
        }
    }).then(result => {
        if(result.isConfirmed && result.value){
            fetch(`/siswa/${id}/update`, {
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

function uploadCsvModal() {
    Swal.fire({
        title: 'Upload CSV Siswa',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div style="text-align:left;padding:4px 0">
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:14px">
                    <div style="font-size:13px;font-weight:700;color:#16a34a;margin-bottom:4px">Format File yang Didukung</div>
                    <div style="font-size:12px;color:#334155;line-height:1.6">
                        <strong>1. File Excel Langsung (.xlsx):</strong> Bisa langsung upload file Excel presensi tanpa convert. Seluruh sheet (Kelas X, XI, XII) otomatis diimpor.<br>
                        <strong>2. File CSV (.csv):</strong> Format presensi per kelas ataupun format tabel standar (<code>nisn,nama_siswa,jenis_kelamin,nama_kelas</code>).
                    </div>
                </div>
                <div style="position:relative;border:2px dashed #cbd5e1;border-radius:10px;padding:24px 16px;text-align:center;cursor:pointer;transition:all 0.2s;background:#f8fafc" id="csv-dropzone"
                     onclick="document.getElementById('csv-file-input').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#22c55e';this.style.background='#f0fdf4'"
                     ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc'"
                     ondrop="event.preventDefault();this.style.borderColor='#cbd5e1';this.style.background='#f8fafc';handleCsvFile(event.dataTransfer.files[0])">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 8px;display:block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <div style="font-size:13.5px;color:#64748b;font-weight:600">Klik atau seret file Excel/CSV ke sini</div>
                    <div style="font-size:11.5px;color:#94a3b8;margin-top:4px">Format .xlsx, .csv (Maks 25MB)</div>
                    <div id="csv-filename" style="font-size:12.5px;color:#22c55e;font-weight:600;margin-top:8px;display:none"></div>
                </div>
                <input type="file" id="csv-file-input" accept=".xlsx,.xls,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" style="display:none" onchange="handleCsvFile(this.files[0])">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:6px;vertical-align:-2px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Upload',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
            const file = window._csvFile;
            if (!file) {
                Swal.showValidationMessage('Pilih file CSV terlebih dahulu');
                return false;
            }
            return { file };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            const formData = new FormData();
            formData.append('file_csv', result.value.file);

            Swal.fire({
                title: 'Mengupload...',
                html: '<div style="color:#64748b;font-size:13.5px">Sedang memproses data siswa...</div>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title' },
                buttonsStyling: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('/siswa/import-csv', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal upload CSV');
                return data;
            })
            .then(data => {
                let detailHtml = `<div style="text-align:left;font-size:13.5px;color:#475569;line-height:1.8">`;
                detailHtml += `<div style="font-size:22px;font-weight:800;color:#16a34a;margin-bottom:4px">${data.imported} siswa</div>`;
                detailHtml += `<div style="margin-bottom:2px">Berhasil diimport ke database.</div>`;
                if (data.errors && data.errors.length > 0) {
                    detailHtml += `<div style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0">`;
                    detailHtml += `<div style="font-weight:700;color:#e11d48;margin-bottom:4px">${data.errors.length} baris gagal:</div>`;
                    detailHtml += `<div style="max-height:120px;overflow-y:auto;font-size:12px;color:#64748b;background:#fff1f2;padding:8px 10px;border-radius:6px">`;
                    data.errors.forEach(e => { detailHtml += `<div style="margin-bottom:2px">• ${e}</div>`; });
                    detailHtml += `</div></div>`;
                }
                detailHtml += `</div>`;

                Swal.fire({
                    icon: 'success',
                    title: 'Upload Selesai!',
                    html: detailHtml,
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false,
                    confirmButtonText: 'OK'
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

window._csvFile = null;
window.handleCsvFile = function(file) {
    if (!file) return;
    window._csvFile = file;
    const nameEl = document.getElementById('csv-filename');
    if (nameEl) {
        nameEl.textContent = file.name;
        nameEl.style.display = 'block';
    }
};
</script>
