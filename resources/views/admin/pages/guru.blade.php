<div class="page-content page-anim" id="page-data-guru" style="display:none">

    @php
        $daftarPeran = $roles ?? \App\Models\Role::orderBy('id_role')->get();
    @endphp
    <script>var daftarPeran = @json($daftarPeran->map(fn($r) => ['nama' => $r->nama_role])->toArray());</script>

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>

            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Data Guru</div>
            <div class="page-subtitle">
                @php
                    $tahunAjaranStr = ($tahunAjaran->tahun_ajaran ?? '2026/2027') . ' (' . ($tahunAjaran->semester ?? 'Ganjil') . ')';
                @endphp
                Tahun Ajaran {{ $tahunAjaranStr }}
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <button class="btn-primary" onclick="tambahGuruModal()" style="border-radius:10px;padding:10px 20px;font-size:13.5px">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Guru
            </button>
            <button class="btn-primary" onclick="bukaImportGuruModal()" style="border-radius:10px;padding:10px 20px;font-size:13.5px;background:var(--green,#22c55e);border-color:var(--green,#22c55e)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload CSV
            </button>
            <button class="btn-primary" onclick="hapusSemuaGuru()" style="border-radius:10px;padding:10px 20px;font-size:13.5px;background:var(--red,#ef4444);border-color:var(--red,#ef4444)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Hapus Semua
            </button>
        </div>
    </div>

    {{-- ── Ringkasan Data Guru ── --}}
    <div style="margin-bottom:20px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">
            Ringkasan Data Guru
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px" id="guru-summary-grid">

            {{-- Total Guru --}}
            <div class="guru-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#2563eb;font-weight:600;margin-bottom:1px">Total Guru</div>
                    <div id="gs-total-guru" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ count($allGuru) }}</div>
                    <div style="font-size:11px;color:#94a3b8">Guru</div>
                </div>
            </div>

            {{-- Guru Aktif --}}
            @php $guruAktifCount = $allGuru->where('is_aktif', 1)->count(); @endphp
            <div class="guru-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#16a34a;font-weight:600;margin-bottom:1px">Guru Aktif</div>
                    <div id="gs-guru-aktif" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $guruAktifCount ?: count($allGuru) }}</div>
                    <div style="font-size:11px;color:#94a3b8">Guru</div>
                </div>
            </div>

            {{-- Guru Nonaktif --}}
            @php $guruNonaktifCount = $allGuru->where('is_aktif', 0)->count(); @endphp
            <div class="guru-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f5f3ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="22" y2="12"/><line x1="22" y1="8" x2="18" y2="12"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#7c3aed;font-weight:600;margin-bottom:1px">Guru Nonaktif</div>
                    <div id="gs-guru-nonaktif" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ $guruNonaktifCount }}</div>
                    <div style="font-size:11px;color:#94a3b8">Guru</div>
                </div>
            </div>

            {{-- Total Mata Pelajaran --}}
            <div class="guru-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#fff1f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#e11d48;font-weight:600;margin-bottom:1px">Mata Pelajaran</div>
                    <div id="gs-total-mapel" style="font-size:26px;font-weight:800;color:#1e293b;line-height:1.1">{{ count($allMapel ?? []) }}</div>
                    <div style="font-size:11px;color:#94a3b8">Mata Pelajaran</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">

        {{-- Cari Guru --}}
        <div class="filter-group" style="flex:1;min-width:200px">
            <label>Cari Guru</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="filter-input" id="guru-cari"
                    placeholder="Ketik nama, NIP, atau username..."
                    oninput="filterGuruPage()"
                    style="width:100%;max-width:320px;padding-left:32px">
            </div>
        </div>

        {{-- Filter Peran --}}
        <div class="filter-group" style="min-width:160px">
            <label>Peran</label>
            <div style="position:relative">
                <select class="filter-select" id="guru-filter-peran" onchange="filterGuruPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Peran</option>
                    @php
                        $perans = $allGuru->pluck('Peran')->filter()->unique();
                    @endphp
                    @foreach($perans as $p)
                    <option value="{{ strtolower($p) }}">{{ $p }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="filter-group" style="min-width:140px">
            <label>Status</label>
            <div style="position:relative">
                <select class="filter-select" id="guru-filter-status" onchange="filterGuruPage()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <table id="guru-table">
            <thead>
                <tr>
                    <th style="width:48px">No.</th>
                    <th style="width:160px">NIP</th>
                    <th>Nama Guru</th>
                    <th style="width:120px">Peran</th>
                    <th style="width:130px">Username</th>
                    <th style="width:100px;text-align:center">Status</th>
                    <th style="width:90px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="guru-tbody-page">
                @forelse($allGuru as $idx => $g)
                @php
                    $isAktif = $g->is_aktif ?? true;
                    $jadwalCount = (int) ($g->jadwal_count ?? $g->jadwal()->count());
                @endphp
                <tr class="guru-row-item"
                    data-search="{{ strtolower(($g->nip ?? '') . ' ' . $g->nama_guru . ' ' . ($g->username ?? '')) }}"
                    data-peran="{{ strtolower($g->Peran ?? '') }}"
                    data-status="{{ $isAktif ? 'aktif' : 'nonaktif' }}"
                    data-jadwal-count="{{ $jadwalCount }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td style="font-family:monospace;font-size:13px;color:#475569">{{ $g->nip ?? '-' }}</td>
                    <td style="font-weight:600;color:#1e293b;font-size:13.5px">
                        {{ $g->nama_guru }}
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $g->Peran ?? 'Guru' }}</span>
                    </td>
                    <td style="font-family:monospace;color:#2563eb;font-weight:600;font-size:13px">{{ $g->username }}</td>
                    <td style="text-align:center">
                        @if($isAktif)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-warning">Nonaktif</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:inline-flex;align-items:center;gap:8px">
                            <div onclick="btnToggleAktifGuru(this)"
                                 data-id="{{ $g->id_guru }}"
                                 data-nama="{{ $g->nama_guru }}"
                                 data-aktif="{{ (int)$isAktif }}"
                                 data-jadwal-count="{{ (int)$jadwalCount }}"
                                 class="guru-toggle-switch"
                                 style="width:40px;height:22px;border-radius:11px;cursor:pointer;position:relative;transition:background 0.25s;@if($isAktif) background:var(--green,#22c55e);@else background:var(--red,#ef4444);@endif"
                                 title="{{ $isAktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <div class="guru-toggle-dot" style="width:16px;height:16px;border-radius:50%;background:#fff;position:absolute;top:3px;transition:left 0.25s;box-shadow:0 1px 3px rgba(0,0,0,0.15);@if($isAktif) left:21px;@else left:3px;@endif"></div>
                            </div>
                            <button
                                type="button"
                                onclick="btnEditGuru(this)"
                                data-id="{{ $g->id_guru }}"
                                data-nama="{{ $g->nama_guru }}"
                                data-nip="{{ $g->nip ?? '' }}"
                                data-peran="{{ $g->Peran ?? 'Guru' }}"
                                data-hp="{{ $g->no_hp ?? '' }}"
                                data-username="{{ $g->username }}"
                                data-is-admin="{{ (int)($g->is_admin ?? 0) }}"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Edit">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button
                                type="button"
                                onclick="btnHapusGuru(this)"
                                data-id="{{ $g->id_guru }}"
                                data-nama="{{ $g->nama_guru }}"
                                data-jadwal-count="{{ (int)$jadwalCount }}"
                                data-is-admin="{{ (int)($g->is_admin ?? 0) }}"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Hapus">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="guru-empty-state">
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Belum ada data guru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer: info + pagination --}}
        <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span id="guru-page-info" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan 1 - {{ min(10, count($allGuru)) }} dari {{ count($allGuru) }} data
            </span>
            <div id="guru-pagination" style="display:flex;align-items:center;gap:4px"></div>
        </div>
    </div>

</div>

{{-- ── Styles ── --}}
<style>
.guru-summary-card { transition: transform 0.2s, box-shadow 0.2s; }
.guru-summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.09) !important; }
#page-data-guru tbody button:hover { opacity: 0.8; transform: scale(1.07); }
.guru-page-btn {
    min-width: 32px; height: 32px; padding: 0 8px;
    border: 1px solid #e2e8f0; background: #fff; border-radius: 6px;
    font-size: 12.5px; font-weight: 600; color: #64748b; cursor: pointer;
    font-family: inherit; transition: all 0.15s;
    display: inline-flex; align-items: center; justify-content: center;
}
.guru-page-btn:hover:not(:disabled) { background: #f1f5f9; color: #1e293b; }
.guru-page-btn.active { background: #1e293b; border-color: #1e293b; color: #fff; }
.guru-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
@media (max-width: 1100px) {
    #page-data-guru [style*="grid-template-columns:repeat(4"] { grid-template-columns: repeat(2,1fr) !important; }
}
@media (max-width: 600px) {
    #page-data-guru [style*="grid-template-columns:repeat(4"] { grid-template-columns: 1fr !important; }
}
</style>

{{-- ── JS: Filter + Pagination + Edit ── --}}
<script>
function bukaImportGuruModal() {
    const oldModal = document.getElementById('guru-upload-modal');
    if (oldModal) oldModal.remove();
    const modal = document.createElement('div');
    modal.id = 'guru-upload-modal';
    modal.innerHTML = `<div class="guru-upload-backdrop"><div class="guru-upload-dialog" role="dialog" aria-modal="true" aria-labelledby="guru-upload-title"><div class="guru-upload-head"><div><h3 id="guru-upload-title">Upload Data Guru</h3><p>CSV, Excel, atau TXT</p></div><button type="button" class="guru-upload-close" aria-label="Tutup">&times;</button></div><div id="guru-upload-drop" class="guru-upload-drop"><svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.7"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg><strong>Tarik file ke sini</strong><span>atau pilih file dari komputer</span><button type="button" id="guru-upload-choose" class="guru-upload-choose">Pilih File</button><input id="guru-upload-input" type="file" accept=".csv,.txt,.xlsx,.xls" hidden><div id="guru-upload-name">Belum ada file dipilih</div></div><div style="padding:0 22px 6px;font-size:11.5px;color:#64748b;line-height:1.6"><div style="font-weight:700;color:#334155;margin-bottom:4px">Format kolom yang didukung:</div><div style="display:flex;gap:6px;flex-wrap:wrap"><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">NIP</span><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">Nama Guru</span><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">Role / Jabatan</span><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">Wali Kelas</span><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px;color:#94a3b8">No HP (opsional)</span><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px;color:#94a3b8">Username (opsional)</span></div><div style="margin-top:6px;color:#94a3b8;font-size:11px">Sesuai format file Data Guru SMKN. Kolom No akan diabaikan otomatis. Maks 25MB.</div></div><div class="guru-upload-foot"><button type="button" id="guru-upload-cancel" class="guru-upload-cancel">Batal</button><button type="button" id="guru-upload-submit" class="guru-upload-submit" disabled>Upload Sekarang</button></div></div></div>`;
    document.body.appendChild(modal);
    const backdrop = modal.querySelector('.guru-upload-backdrop');
    const drop = modal.querySelector('#guru-upload-drop');
    const input = modal.querySelector('#guru-upload-input');
    const name = modal.querySelector('#guru-upload-name');
    const submit = modal.querySelector('#guru-upload-submit');
    let selectedFile = null;
    const close = () => modal.remove();
    const setFile = file => {
        if (!file) return;
        selectedFile = file;
        name.textContent = file.name;
        submit.disabled = false;
        drop.classList.add('has-file');
    };
    modal.querySelector('#guru-upload-choose').onclick = event => { event.stopPropagation(); input.click(); };
    modal.querySelector('.guru-upload-close').onclick = close;
    modal.querySelector('#guru-upload-cancel').onclick = close;
    backdrop.onclick = event => { if (event.target === backdrop) close(); };
    drop.onclick = () => input.click();
    input.onchange = () => setFile(input.files[0]);
    drop.ondragover = event => { event.preventDefault(); drop.classList.add('dragging'); };
    drop.ondragleave = () => drop.classList.remove('dragging');
    drop.ondrop = event => { event.preventDefault(); drop.classList.remove('dragging'); setFile(event.dataTransfer.files[0]); };
    submit.onclick = () => {
        if (!selectedFile || !window.confirm(`Upload file "${selectedFile.name}" sekarang?`)) return;
        const formData = new FormData();
        formData.append('file_guru', selectedFile);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        close();
        uploadGuruFile(formData);
    };
}

function bukaImportGuruModalSwal() {
    let selectedFile = null;
    Swal.fire({
        title: 'Upload Data Guru',
        html: `<div id="guru-drop-zone" style="border:2px dashed #93c5fd;border-radius:12px;padding:28px 18px;background:#f8fbff;text-align:center;cursor:pointer;transition:background .2s,border-color .2s"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.7" style="margin-bottom:8px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg><div style="font-size:13.5px;font-weight:700;color:#1e293b">Tarik file ke sini</div><div style="font-size:12px;color:#64748b;margin:5px 0 12px">atau pilih file dari perangkat</div><button type="button" id="guru-choose-file" class="jam-popup-confirm">Pilih File</button><div id="guru-file-name" style="font-size:12px;color:#2563eb;font-weight:600;margin-top:12px">Belum ada file dipilih</div></div><input id="guru-import-file" type="file" accept=".csv,.txt,.xlsx,.xls" style="display:none"><div style="font-size:11.5px;color:#64748b;margin-top:12px;line-height:1.6"><div style="font-weight:700;color:#334155;margin-bottom:4px">Format kolom yang didukung:</div><div style="display:flex;gap:5px;flex-wrap:wrap;justify-content:center"><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">NIP</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">Nama Guru</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">Role / Jabatan</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">Wali Kelas</span></div><div style="margin-top:4px;color:#94a3b8;font-size:10.5px">Sesuai format file Data Guru SMKN. Maks 25MB.</div></div>`,
        showCancelButton: true,
        confirmButtonText: 'Upload Sekarang',
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm', cancelButton: 'custom-swal-cancel' },
        didOpen: () => {
            const zone = document.getElementById('guru-drop-zone');
            const input = document.getElementById('guru-import-file');
            const choose = document.getElementById('guru-choose-file');
            const name = document.getElementById('guru-file-name');
            const confirm = Swal.getConfirmButton();
            confirm.disabled = true;
            const setFile = file => {
                if (!file) return;
                selectedFile = file;
                name.textContent = file.name;
                confirm.disabled = false;
                zone.style.borderColor = '#22c55e';
                zone.style.background = '#f0fdf4';
            };
            choose.onclick = event => { event.stopPropagation(); input.click(); };
            zone.onclick = () => input.click();
            input.onchange = () => setFile(input.files[0]);
            zone.ondragover = event => { event.preventDefault(); zone.style.background = '#eff6ff'; };
            zone.ondragleave = () => { zone.style.background = selectedFile ? '#f0fdf4' : '#f8fbff'; };
            zone.ondrop = event => { event.preventDefault(); setFile(event.dataTransfer.files[0]); };
        },
        preConfirm: () => selectedFile || Swal.showValidationMessage('Pilih atau tarik file terlebih dahulu.')
    }).then(result => {
        if (!result.isConfirmed || !result.value) return;
        const formData = new FormData();
        formData.append('file_guru', result.value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        uploadGuruFile(formData);
    });
}

function uploadGuruFile(formData) {
    if (window.Swal) Swal.fire({ title: 'Mengimpor data guru...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    fetch('/guru/import', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: formData
    }).then(async response => {
        const data = await response.json();
        if (!response.ok || data.status === 'error') throw new Error(data.message || 'Import gagal.');
        return data;
    }).then(data => {
        const detail = data.skipped?.length ? `\n${data.skipped.length} baris dilewati karena tidak lengkap.` : '';
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Import Berhasil', text: data.message + detail, confirmButtonColor: '#2563eb' }).then(() => reloadCurrentPage());
        else { window.alert(data.message + detail); reloadCurrentPage(); }
    }).catch(error => {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Import Gagal', text: error.message, confirmButtonColor: '#dc2626' });
        else window.alert(`Import Gagal: ${error.message}`);
    });
}

(function(){
    const PER_PAGE = 10;
    let guruPage = 1;
    let guruFiltered = [];

    function allRows(){ return Array.from(document.querySelectorAll('#guru-tbody-page .guru-row-item')); }

    window.filterGuruPage = function(){
        const q      = (document.getElementById('guru-cari')?.value || '').toLowerCase().trim();
        const peran  = (document.getElementById('guru-filter-peran')?.value || '').toLowerCase();
        const status = (document.getElementById('guru-filter-status')?.value || '').toLowerCase();

        guruFiltered = allRows().filter(row => {
            const hay    = row.dataset.search || '';
            const rPeran = (row.dataset.peran || '').toLowerCase();
            const rSt    = (row.dataset.status || '').toLowerCase();
            return (!q      || hay.includes(q))
                && (!peran  || rPeran === peran)
                && (!status || rSt === status);
        });

        updateGuruSummary();

        guruPage = 1;
        renderGuruPage();
    };

    function updateGuruSummary(){
        const rows = guruFiltered.length ? guruFiltered : allRows();
        let totalGuru=0, aktif=0, nonaktif=0;
        rows.forEach(r => {
            totalGuru++;
            if(r.dataset.status==='aktif') aktif++; else nonaktif++;
        });
        const set = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
        set('gs-total-guru', totalGuru);
        set('gs-guru-aktif', aktif);
        set('gs-guru-nonaktif', nonaktif);
    }

    function renderGuruPage(){
        const total      = guruFiltered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if(guruPage > totalPages) guruPage = totalPages;

        const start = (guruPage - 1) * PER_PAGE;
        const end   = Math.min(start + PER_PAGE, total);

        allRows().forEach(r => r.style.display = 'none');
        guruFiltered.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
            if(i >= start && i < end){
                r.querySelector('td').textContent = start + (i - start) + 1;
            }
        });

        const emptyRow = document.getElementById('guru-empty-state');
        if(emptyRow) emptyRow.style.display = total === 0 ? '' : 'none';

        const info = document.getElementById('guru-page-info');
        if(info){
            info.textContent = total === 0
                ? 'Tidak ada data yang ditemukan'
                : `Menampilkan ${start + 1} - ${end} dari ${total} data`;
        }

        buildGuruPagination(totalPages);
    }

    function buildGuruPagination(totalPages){
        const container = document.getElementById('guru-pagination');
        if(!container) return;
        container.innerHTML = '';

        const prev = document.createElement('button');
        prev.className = 'guru-page-btn';
        prev.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>';
        prev.disabled = guruPage <= 1;
        prev.onclick = () => { if(guruPage > 1){ guruPage--; renderGuruPage(); } };
        container.appendChild(prev);

        let pages = [];
        if(totalPages <= 5){
            for(let i=1;i<=totalPages;i++) pages.push(i);
        } else {
            pages = [1];
            if(guruPage > 3) pages.push('...');
            for(let i=Math.max(2,guruPage-1);i<=Math.min(totalPages-1,guruPage+1);i++) pages.push(i);
            if(guruPage < totalPages-2) pages.push('...');
            pages.push(totalPages);
        }

        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'guru-page-btn' + (p === guruPage ? ' active' : '');
            btn.textContent = p;
            if(p === '...'){
                btn.disabled = true;
                btn.style.cursor = 'default';
            } else {
                btn.onclick = () => { guruPage = p; renderGuruPage(); };
            }
            container.appendChild(btn);
        });

        const nxt = document.createElement('button');
        nxt.className = 'guru-page-btn';
        nxt.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>';
        nxt.disabled = guruPage >= totalPages;
        nxt.onclick = () => { if(guruPage < totalPages){ guruPage++; renderGuruPage(); } };
        container.appendChild(nxt);
    }

    function initGuruPage(){
        guruFiltered = allRows();
        renderGuruPage();
    }

    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-data-guru');
        if(page && page.style.display !== 'none'){
            if(guruFiltered.length === 0 && allRows().length > 0) initGuruPage();
        }
    });
    const target = document.getElementById('page-data-guru');
    if(target) observer.observe(target, { attributes: true, attributeFilter: ['style'] });
    if(target && target.style.display !== 'none') initGuruPage();
    setTimeout(initGuruPage, 300);
})();

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

window.btnEditGuru = function(btn) {
    const id = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama') || '';
    const nip = btn.getAttribute('data-nip') || '';
    const peran = btn.getAttribute('data-peran') || 'Guru';
    const hp = btn.getAttribute('data-hp') || '';
    const username = btn.getAttribute('data-username') || '';
    const isAdmin = parseInt(btn.getAttribute('data-is-admin') || '0');

    if (isAdmin) {
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'Akun Paten (Admin)',
                text: 'Akun Admin ini merupakan data paten dan tidak dapat diubah.',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert('Akun Admin ini merupakan data paten dan tidak dapat diubah.');
        }
        return;
    }

    editGuruModal(id, nama, nip, peran, hp, username, isAdmin);
};

window.btnToggleAktifGuru = function(el) {
    const id = el.getAttribute('data-id');
    const nama = el.getAttribute('data-nama') || '';
    const isAktif = parseInt(el.getAttribute('data-aktif') || '1');
    const jadwalCount = parseInt(el.getAttribute('data-jadwal-count') || '0');

    toggleAktifGuru(el, id, nama, isAktif, jadwalCount);
};

window.btnHapusGuru = function(btn) {
    const id = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama') || '';
    const jadwalCount = parseInt(btn.getAttribute('data-jadwal-count') || '0');
    const isAdmin = parseInt(btn.getAttribute('data-is-admin') || '0');

    if (isAdmin) {
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'Akun Paten (Admin)',
                text: 'Akun Admin ini merupakan data paten dan tidak dapat dihapus.',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert('Akun Admin ini merupakan data paten dan tidak dapat dihapus.');
        }
        return;
    }

    hapusGuru(id, nama, jadwalCount);
};

window.editGuruModal = function(id, nama, nip, peran, hp, username, isAdmin){
    const safeNama = escapeHtml(nama);
    const safeNip = escapeHtml(nip);
    const safePeran = escapeHtml(peran);
    const safeHp = escapeHtml(hp);
    const safeUsername = escapeHtml(username);
    const rolesList = (typeof daftarPeran !== 'undefined' && daftarPeran ? daftarPeran : (window.daftarPeran || []));

    Swal.fire({
        title: 'Edit Data Guru',
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
                    <label for="swal-edit-gname">Nama Lengkap & Gelar</label>
                    <input id="swal-edit-gname" class="swal-form-input" value="${safeNama}" placeholder="Contoh: Budi Santoso, S.Pd.">
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-edit-gnip">NIP (Opsional)</label>
                        <input id="swal-edit-gnip" class="swal-form-input" value="${safeNip}" placeholder="19850101...">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-edit-gperan">Peran</label>
                        <select id="swal-edit-gperan" class="swal-form-select">
                            ${(rolesList || []).map(r => `<option value="${escapeHtml(r.nama)}" ${peran===r.nama?'selected':''}>${escapeHtml(r.nama)}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-edit-ghp">No. HP (Opsional)</label>
                        <input id="swal-edit-ghp" class="swal-form-input" value="${safeHp}" placeholder="08123456789">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-edit-gadmin">Hak Akses</label>
                        <select id="swal-edit-gadmin" class="swal-form-select">
                            <option value="0" ${!isAdmin?'selected':''}>Guru Biasa</option>
                            <option value="1" ${isAdmin?'selected':''}>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-edit-gusername">Username Login</label>
                        <input id="swal-edit-gusername" class="swal-form-input" value="${safeUsername}" placeholder="contoh: budi">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-edit-gpass">Password Baru (Opsional)</label>
                        <input type="password" id="swal-edit-gpass" class="swal-form-input" placeholder="Biarkan kosong jika tidak diubah">
                    </div>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const newNama = document.getElementById('swal-edit-gname').value.trim();
            const newUsername = document.getElementById('swal-edit-gusername').value.trim();

            if (!newNama) {
                Swal.showValidationMessage('Nama guru tidak boleh kosong');
                return false;
            }
            if (!newUsername) {
                Swal.showValidationMessage('Username tidak boleh kosong');
                return false;
            }

            return {
                nama_guru: newNama,
                nip: document.getElementById('swal-edit-gnip').value.trim(),
                peran: document.getElementById('swal-edit-gperan').value,
                no_hp: document.getElementById('swal-edit-ghp').value.trim(),
                is_admin: parseInt(document.getElementById('swal-edit-gadmin').value),
                username: newUsername,
                password: document.getElementById('swal-edit-gpass').value.trim()
            };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch(`/guru/${id}/update`, {
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
                if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal memperbarui data guru');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: data.message || 'Data berhasil diperbarui.',
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

function toggleAktifGuru(el, id, nama, isAktif, jadwalCount = 0) {
    const action = isAktif ? 'menonaktifkan' : 'mengaktifkan';

    if (isAktif && jadwalCount > 0) {
        tampilkanPeringatanJadwalGuru(id, nama, jadwalCount, 'dinonaktifkan');
        return;
    }

    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Guru?`,
        html: `<div style="font-size:14px;color:#64748b">Anda yakin ingin ${action} <strong>${nama}</strong>?</div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Ya, ${action}`,
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/guru/${id}/toggle-aktif`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (data.status === 'has_jadwal') {
                    tampilkanPeringatanJadwalGuru(id, nama, data.jadwal_count || jadwalCount, 'dinonaktifkan');
                    return null;
                }
                if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal mengubah status guru');
                return data;
            })
            .then(data => {
                if (!data) return;
                const newIsAktif = data.is_aktif;
                const row = el.closest('tr');

                el.style.background = newIsAktif ? 'var(--green,#22c55e)' : 'var(--red,#ef4444)';
                el.querySelector('.guru-toggle-dot').style.left = newIsAktif ? '21px' : '3px';
                el.title = newIsAktif ? 'Nonaktifkan' : 'Aktifkan';
                el.setAttribute('onclick', `toggleAktifGuru(this, ${id}, '${nama.replace(/'/g, "\\'")}', ${newIsAktif}, ${jadwalCount})`);

                if (row) {
                    row.dataset.status = newIsAktif ? 'aktif' : 'nonaktif';
                    const statusCell = row.querySelectorAll('td')[6];
                    if (statusCell) {
                        statusCell.innerHTML = newIsAktif
                            ? '<span class="badge badge-success">Aktif</span>'
                            : '<span class="badge badge-warning">Nonaktif</span>';
                    }
                }

                const rows = Array.from(document.querySelectorAll('#guru-tbody-page .guru-row-item'));
                let totalGuru = rows.length, aktif = 0, nonaktif = 0;
                rows.forEach(r => { if (r.dataset.status === 'aktif') aktif++; else nonaktif++; });
                const set = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
                set('gs-total-guru', totalGuru);
                set('gs-guru-aktif', aktif);
                set('gs-guru-nonaktif', nonaktif);

                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: data.message,
                    timer: 1200, showConfirmButton: false
                });
            })
            .catch(err => Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: err.message || 'Terjadi kesalahan sistem.',
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false
            }));
        }
    });
}

function hapusGuru(id, nama, jadwalCount = 0) {
    if (jadwalCount > 0) {
        tampilkanPeringatanJadwalGuru(id, nama, jadwalCount, 'dihapus');
        return;
    }

    confirmDeleteData({
        title: 'Hapus Data Guru?',
        itemName: `guru ${nama}`,
        onConfirm: () => {
            fetch(`/guru/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (data.status === 'has_jadwal') {
                    tampilkanPeringatanJadwalGuru(id, nama, data.jadwal_count || jadwalCount, 'dihapus');
                    return;
                }
                if (!res.ok || data.status === 'error') {
                    throw new Error(data.message || 'Gagal menghapus data guru');
                }
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1500, showConfirmButton: false })
                    .then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: err.message || 'Terjadi kesalahan sistem.',
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false
            }));
        }
    });
}

function tampilkanPeringatanJadwalGuru(id, nama, jadwalCount, aksi) {
    Swal.fire({
        title: 'Guru Memiliki Jadwal Mengajar Aktif',
        icon: 'warning',
        html: `
            <div style="text-align:left;font-size:13.5px;color:#334155;line-height:1.5">
                <p>Guru <strong>${nama}</strong> tidak dapat ${aksi} karena masih memiliki <strong>${jadwalCount}</strong> jadwal mengajar aktif.</p>
                <p style="margin-top:8px;color:#64748b;font-size:12.5px">Silakan pilih salah satu opsi di bawah ini untuk menangani jadwal mengajar terlebih dahulu:</p>

                <div style="margin-top:16px;display:flex;flex-direction:column;gap:10px">
                    <button type="button" onclick="bukaModalAlihkanJadwal(${id}, '${nama.replace(/'/g, "\\'")}', ${jadwalCount})" style="width:100%;padding:11px 16px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 2px 6px rgba(37,99,235,0.25);transition:background 0.2s">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                        Alihkan Semua Jadwal ke Guru Lain
                    </button>

                    <button type="button" onclick="konfirmasiKosongkanJadwal(${id}, '${nama.replace(/'/g, "\\'")}', ${jadwalCount})" style="width:100%;padding:11px 16px;background:#f59e0b;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 2px 6px rgba(245,158,11,0.25);transition:background 0.2s">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        Kosongkan Jadwal (Jadwal Kosong Tanpa Guru)
                    </button>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Tutup Peringatan',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false
    });
}

function bukaModalAlihkanJadwal(id, nama, jadwalCount) {
    const listGuruLain = (window.daftarGuru || []).filter(g => g.id_guru != id && g.is_aktif != 0);
    const optionsHtml = listGuruLain.map(g => `<option value="${g.id_guru}">${g.nama_guru} (${g.Peran || 'Guru'})</option>`).join('');

    Swal.fire({
        title: 'Alihkan Jadwal Mengajar',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div style="text-align:left;font-family:inherit">
                <p style="font-size:13.5px;color:#334155;margin-bottom:12px;line-height:1.4">
                    Alihkan <strong>${jadwalCount}</strong> jadwal mengajar dari <strong>${nama}</strong> ke guru pengganti berikut:
                </p>
                <div style="margin-bottom:12px">
                    <label for="swal-guru-tujuan" style="display:block;font-size:12.5px;font-weight:700;color:#475569;margin-bottom:6px">Guru Pengganti</label>
                    <select id="swal-guru-tujuan" class="swal-form-select" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13.5px">
                        <option value="">-- Pilih Guru Pengganti --</option>
                        ${optionsHtml}
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Alihkan Jadwal Sekarang',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const idTujuan = document.getElementById('swal-guru-tujuan')?.value;
            if (!idTujuan) {
                Swal.showValidationMessage('Silakan pilih guru pengganti terlebih dahulu');
                return false;
            }
            return { id_guru_tujuan: idTujuan };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch(`/guru/${id}/alihkan-jadwal`, {
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
                if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal mengalihkan jadwal');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Jadwal Berhasil Dialihkan',
                    text: data.message,
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: err.message || 'Terjadi kesalahan sistem.',
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false
            }));
        }
    });
}

function konfirmasiKosongkanJadwal(id, nama, jadwalCount) {
    Swal.fire({
        title: 'Kosongkan Semua Jadwal Guru?',
        html: `
            <div style="text-align:left;font-size:13.5px;color:#334155;line-height:1.5">
                <p>Anda akan melepaskan guru <strong>${nama}</strong> dari <strong>${jadwalCount}</strong> jadwal mengajar.</p>
                <div style="margin-top:10px;padding:12px;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;font-size:12.5px;color:#b45309">
                    <strong>Catatan:</strong> Jadwal tersebut tidak akan dihapus dari susunan kelas, melainkan statusnya akan menjadi <strong>Kosong (Belum Ada Guru Pengampu)</strong> di menu Jadwal Mengajar dan dapat ditugaskan ke guru lain kapan saja.
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kosongkan Jadwal',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm-danger',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/guru/${id}/kosongkan-jadwal`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal mengosongkan jadwal');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Jadwal Dikosongkan',
                    text: data.message,
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: err.message || 'Terjadi kesalahan sistem.',
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false
            }));
        }
    });
}

                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: data.message,
                    timer: 1200, showConfirmButton: false
                });
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}
</script>
