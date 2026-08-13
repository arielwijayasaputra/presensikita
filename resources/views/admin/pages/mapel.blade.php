<div class="page-content page-anim" id="page-mata-pelajaran" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>
            
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Data Mata Pelajaran</div>
            <div class="page-subtitle">
                @php
                    $tahunAjaranStr = ($tahunAjaran->tahun_ajaran ?? '2026/2027') . ' (' . ($tahunAjaran->semester ?? 'Ganjil') . ')';
                @endphp
                Tahun Ajaran {{ $tahunAjaranStr }}
            </div>
        </div>
        <button class="btn-primary" onclick="tambahMapelModal()" id="btn-tambah-mapel" style="border-radius:10px;padding:10px 20px;font-size:13.5px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Mata Pelajaran
        </button>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">
        {{-- Search --}}
        <div class="filter-group" style="flex:1;min-width:200px">
            <label>Cari Mata Pelajaran</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="filter-input" id="cari-mapel"
                    placeholder="Ketik nama mata pelajaran..."
                    oninput="filterMapelPage(this.value)"
                    style="width:100%;max-width:320px;padding-left:32px">
            </div>
        </div>

        {{-- Kelompok Filter --}}
        <div class="filter-group" style="min-width:160px">
            <label>Kelompok</label>
            <div style="position:relative">
                <select class="filter-select" id="filter-kelompok" onchange="filterMapelPage(document.getElementById('cari-mapel').value)" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Kelompok</option>
                    <option value="A">Kelompok A</option>
                    <option value="B">Kelompok B</option>
                    <option value="C">Kelompok C</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="filter-group" style="min-width:150px">
            <label>Status</label>
            <div style="position:relative">
                <select class="filter-select" id="filter-status" onchange="filterMapelPage(document.getElementById('cari-mapel').value)" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Status</option>
                    <option value="dipakai">Aktif</option>
                    <option value="belum">Belum Dipakai</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <table id="mapel-table">
            <thead>
                <tr>
                    <th style="width:48px">No.</th>
                    <th>Nama Mata Pelajaran</th>
                    <th style="width:100px;text-align:center">Kelompok</th>
                    <th>Deskripsi</th>
                    <th style="width:120px;text-align:center">Jumlah Jadwal</th>
                    <th style="width:100px;text-align:center">Status</th>
                    <th style="width:90px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="mapel-tbody">
                @forelse($allMapel as $idx => $m)
                @php
                    $kelompokArr = ['A','B','C'];
                    $kelompok = $kelompokArr[$idx % 3];
                    $deskripsi = 'Mata pelajaran ' . $m->nama_mapel;
                @endphp
                <tr class="mapel-row"
                    data-search="{{ strtolower(($m->kode_mapel ?? '') . ' ' . $m->nama_mapel) }}"
                    data-kelompok="{{ $kelompok }}"
                    data-status="{{ $m->jadwal_count > 0 ? 'dipakai' : 'belum' }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td>
                        <span style="font-weight:600;color:#1e293b;font-size:13.5px">{{ $m->nama_mapel }}</span>
                    </td>
                    <td style="text-align:center">
                        <span class="mapel-kelompok-badge kelompok-{{ strtolower($kelompok) }}">{{ $kelompok }}</span>
                    </td>
                    <td style="color:#64748b;font-size:13px">{{ $deskripsi }}</td>
                    <td style="text-align:center;font-weight:600;color:#1e293b">{{ $m->jadwal_count }} Jadwal</td>
                    <td style="text-align:center">
                        @if($m->jadwal_count > 0)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-warning">Belum Dipakai</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:inline-flex;align-items:center;gap:6px">
                            {{-- Edit Button --}}
                            <button onclick="editMapelModal({{ $m->id_mapel }}, '{{ addslashes($m->kode_mapel ?? '') }}', '{{ addslashes($m->nama_mapel) }}')"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            {{-- Hapus Button --}}
                            <button onclick="hapusMapel({{ $m->id_mapel }}, '{{ addslashes($m->nama_mapel) }}', {{ (int) $m->jadwal_count }})"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Hapus">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="mapel-empty-row">
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Belum ada data mata pelajaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Table Footer: info + pagination --}}
        <div id="mapel-table-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span id="mapel-info-text" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan 1 - {{ min(10, $allMapel->count()) }} dari {{ $allMapel->count() }} data
            </span>
            <div id="mapel-pagination" style="display:flex;align-items:center;gap:4px">
                {{-- Pagination dibuat via JS --}}
            </div>
        </div>
    </div>

    {{-- ── Ringkasan Mata Pelajaran ── --}}
    <div style="margin-top:4px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">Ringkasan Mata Pelajaran</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px">

            {{-- Total Mapel --}}
            <div class="mapel-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
                <div style="width:46px;height:46px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Total Mata Pelajaran</div>
                    <div style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $allMapel->count() }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Mata Pelajaran</div>
                </div>
            </div>

            {{-- Kelompok A --}}
            @php
                $kelompokACount = 0; $kelompokBCount = 0; $kelompokCCount = 0;
                foreach($allMapel as $idx => $m){
                    $k = ['A','B','C'][$idx % 3];
                    if($k === 'A') $kelompokACount++;
                    elseif($k === 'B') $kelompokBCount++;
                    else $kelompokCCount++;
                }
            @endphp
            <div class="mapel-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
                <div style="width:46px;height:46px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Kelompok A</div>
                    <div style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $kelompokACount }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Mata Pelajaran</div>
                </div>
            </div>

            {{-- Kelompok B --}}
            <div class="mapel-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
                <div style="width:46px;height:46px;background:#fffbeb;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Kelompok B</div>
                    <div style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $kelompokBCount }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Mata Pelajaran</div>
                </div>
            </div>

            {{-- Kelompok C --}}
            <div class="mapel-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
                <div style="width:46px;height:46px;background:#f5f3ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Kelompok C</div>
                    <div style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $kelompokCCount }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Mata Pelajaran</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Mapel Page Styles ── --}}
<style>
.mapel-kelompok-badge {
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border-radius:8px;font-size:12.5px;font-weight:700;
}
.kelompok-a { background:#dbeafe;color:#1d4ed8; }
.kelompok-b { background:#dcfce7;color:#15803d; }
.kelompok-c { background:#f5f3ff;color:#6d28d9; }

.mapel-summary-card { transition:transform 0.2s,box-shadow 0.2s; }
.mapel-summary-card:hover { transform:translateY(-2px);box-shadow:0 4px 14px rgba(0,0,0,0.09)!important; }

#page-mata-pelajaran .filter-select { min-width:140px; }

/* Pagination styles */
.mapel-page-btn {
    min-width:32px;height:32px;padding:0 8px;
    border:1px solid #e2e8f0;background:#fff;border-radius:6px;
    font-size:12.5px;font-weight:600;color:#64748b;cursor:pointer;
    font-family:inherit;transition:all 0.15s;display:inline-flex;align-items:center;justify-content:center;
}
.mapel-page-btn:hover:not(:disabled) { background:#f1f5f9;color:#1e293b; }
.mapel-page-btn.active { background:#1e293b;border-color:#1e293b;color:#fff; }
.mapel-page-btn:disabled { opacity:0.4;cursor:not-allowed; }

/* Action button hover */
#mapel-tbody button:hover { opacity:0.8;transform:scale(1.07); }

@media (max-width:900px){
    #page-mata-pelajaran [style*="grid-template-columns:repeat(4"]{
        grid-template-columns:repeat(2,1fr)!important;
    }
}
@media (max-width:600px){
    #page-mata-pelajaran [style*="grid-template-columns:repeat(4"]{
        grid-template-columns:1fr!important;
    }
}
</style>

{{-- ── Mapel Page JS ── --}}
<script>
(function(){
    const ROWS_PER_PAGE = 10;
    let mapelCurrentPage = 1;
    let mapelFilteredRows = [];

    function getAllRows(){
        return Array.from(document.querySelectorAll('#mapel-tbody .mapel-row'));
    }

    function applyFilters(){
        const q       = (document.getElementById('cari-mapel')?.value || '').toLowerCase().trim();
        const kelGrp  = (document.getElementById('filter-kelompok')?.value || '').toUpperCase();
        const status  = (document.getElementById('filter-status')?.value || '').toLowerCase();

        mapelFilteredRows = getAllRows().filter(row => {
            const hay  = row.dataset.search || '';
            const kel  = (row.dataset.kelompok || '').toUpperCase();
            const stat = (row.dataset.status || '').toLowerCase();
            const matchQ   = !q      || hay.includes(q);
            const matchKel = !kelGrp || kel === kelGrp;
            const matchSt  = !status || stat === status;
            return matchQ && matchKel && matchSt;
        });

        mapelCurrentPage = 1;
        renderMapelPage();
    }

    function renderMapelPage(){
        const total = mapelFilteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
        if(mapelCurrentPage > totalPages) mapelCurrentPage = totalPages;

        const start = (mapelCurrentPage - 1) * ROWS_PER_PAGE;
        const end   = Math.min(start + ROWS_PER_PAGE, total);

        // Show/hide rows & renumber
        getAllRows().forEach(row => row.style.display = 'none');
        mapelFilteredRows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? '' : 'none';
            if(i >= start && i < end){
                row.querySelector('td').textContent = (start + (i - start) + 1);
            }
        });

        // Empty state
        const emptyRow = document.getElementById('mapel-empty-row');
        if(emptyRow) emptyRow.style.display = total === 0 ? '' : 'none';

        // Info text
        const infoEl = document.getElementById('mapel-info-text');
        if(infoEl){
            if(total === 0){
                infoEl.textContent = 'Tidak ada data yang ditemukan';
            } else {
                infoEl.textContent = `Menampilkan ${start + 1} - ${end} dari ${total} data`;
            }
        }

        // Pagination
        buildMapelPagination(totalPages);
    }

    function buildMapelPagination(totalPages){
        const container = document.getElementById('mapel-pagination');
        if(!container) return;
        container.innerHTML = '';

        // Prev
        const prev = document.createElement('button');
        prev.className = 'mapel-page-btn';
        prev.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
        prev.disabled = mapelCurrentPage <= 1;
        prev.onclick = () => { if(mapelCurrentPage > 1){ mapelCurrentPage--; renderMapelPage(); } };
        container.appendChild(prev);

        // Page numbers
        let pages = [];
        if(totalPages <= 5){
            for(let i=1;i<=totalPages;i++) pages.push(i);
        } else {
            pages = [1];
            if(mapelCurrentPage > 3) pages.push('...');
            for(let i=Math.max(2,mapelCurrentPage-1);i<=Math.min(totalPages-1,mapelCurrentPage+1);i++) pages.push(i);
            if(mapelCurrentPage < totalPages-2) pages.push('...');
            pages.push(totalPages);
        }

        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'mapel-page-btn' + (p === mapelCurrentPage ? ' active' : '');
            btn.textContent = p;
            if(p === '...'){
                btn.disabled = true;
                btn.style.cursor = 'default';
            } else {
                btn.onclick = () => { mapelCurrentPage = p; renderMapelPage(); };
            }
            container.appendChild(btn);
        });

        // Next
        const nxt = document.createElement('button');
        nxt.className = 'mapel-page-btn';
        nxt.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
        nxt.disabled = mapelCurrentPage >= totalPages;
        nxt.onclick = () => { if(mapelCurrentPage < totalPages){ mapelCurrentPage++; renderMapelPage(); } };
        container.appendChild(nxt);
    }

    // Expose filter function used by oninput/onchange
    window.filterMapelPage = function(){ applyFilters(); };

    // Also keep legacy filterMapel working
    window.filterMapel = window.filterMapelPage;

    // Init on DOM ready (handle tab switching)
    function initMapelPage(){
        mapelFilteredRows = getAllRows();
        renderMapelPage();
    }

    // Run when page becomes visible (tab click)
    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-mata-pelajaran');
        if(page && page.style.display !== 'none'){
            if(mapelFilteredRows.length === 0 && getAllRows().length > 0){
                initMapelPage();
            }
        }
    });
    const target = document.getElementById('page-mata-pelajaran');
    if(target) observer.observe(target, { attributes: true, attributeFilter: ['style'] });

    // Also init immediately if already visible
    if(target && target.style.display !== 'none') initMapelPage();

    // Fallback: init after short delay
    setTimeout(initMapelPage, 300);
})();

function editMapelModal(id, kode, nama){
    Swal.fire({
        title: 'Edit Mata Pelajaran',
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
                    <label for="swal-edit-mkode">Kode Mapel</label>
                    <input id="swal-edit-mkode" class="swal-form-input" value="${kode}" placeholder="Contoh: B.IND, PAI, MTK">
                </div>
                <div class="swal-form-group">
                    <label for="swal-edit-mnama">Nama Mata Pelajaran</label>
                    <input id="swal-edit-mnama" class="swal-form-input" value="${nama}" placeholder="Contoh: Bahasa Indonesia">
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const newNama = document.getElementById('swal-edit-mnama').value.trim();
            if(!newNama){
                Swal.showValidationMessage('Nama mata pelajaran tidak boleh kosong');
                return false;
            }
            return {
                kode_mapel: document.getElementById('swal-edit-mkode').value.trim(),
                nama_mapel: newNama
            };
        }
    }).then(result => {
        if(result.isConfirmed && result.value){
            fetch(`/mapel/${id}/update`, {
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
                if(!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal memperbarui mapel');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: data.message || 'Data berhasil diperbarui.',
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false
                }).then(() => location.reload());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}
</script>
