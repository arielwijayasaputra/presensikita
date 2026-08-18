<div class="page-content page-anim" id="page-naik-kelas" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Naik Kelas</div>
            <div class="page-subtitle">
                @php
                    $tahunAjaranStr = ($tahunAjaran->tahun_ajaran ?? '2026/2027') . ' (' . ($tahunAjaran->semester ?? 'Ganjil') . ')';
                @endphp
                Tahun Ajaran {{ $tahunAjaranStr }}
            </div>
        </div>
        <button class="btn-primary" onclick="naikKelasPreview()" style="border-radius:10px;padding:10px 20px;font-size:13.5px;background:#7c3aed;border-color:#7c3aed">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
            Proses Naik Kelas
        </button>
    </div>

    {{-- ── Siswa Tidak Naik Kelas ── --}}
    <div class="filter-bar" style="align-items:flex-end;gap:12px;flex-wrap:wrap">
        <div class="filter-group" style="min-width:200px;max-width:300px">
            <label>Siswa Tidak Naik Kelas</label>
            <div style="position:relative">
                <select class="filter-select" id="nk-pilih-kelas" onchange="loadSiswaTidakNaik()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($allKelas as $k)
                        @if($k->siswa_count > 0 && strtoupper($k->tingkat_kelas) !== 'XII')
                        <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }} ({{ $k->siswa_count }} siswa)</option>
                        @endif
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div id="nk-retained-count" style="display:none" class="filter-hint" style="background:#f5f3ff;border:1px solid #c4b5fd;color:#6d28d9">
            <span id="nk-retained-num">0</span>&nbsp;siswa dipilih tidak naik
        </div>
    </div>

    <div id="nk-siswa-container" class="table-card" style="display:none;margin-bottom:24px">
        <div style="padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:13px;font-weight:600;color:#475569">Daftar Siswa</div>
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12.5px;color:#64748b">
                <input type="checkbox" id="nk-select-all" onchange="toggleSelectAllTidakNaik(this.checked)" style="accent-color:#7c3aed;width:15px;height:15px">
                Pilih Semua
            </label>
        </div>
        <div id="nk-siswa-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:8px;padding:16px 20px"></div>
        <div id="nk-siswa-loading" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;display:none">Memuat data siswa...</div>
        <div id="nk-siswa-empty" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;display:none">Tidak ada siswa aktif di kelas ini.</div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">
        <div class="filter-group" style="min-width:140px">
            <label>Tingkat</label>
            <div style="position:relative">
                <select class="filter-select" id="nk-filter-tingkat" onchange="filterKelasRingkasan()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Tingkat</option>
                    <option value="X">X</option>
                    <option value="XI">XI</option>
                    <option value="XII">XII</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div class="filter-group" style="min-width:160px">
            <label>Jurusan</label>
            <div style="position:relative">
                <select class="filter-select" id="nk-filter-jurusan" onchange="filterKelasRingkasan()" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Jurusan</option>
                    @php $jurusanList = $allKelas->pluck('jurusan')->filter()->unique()->sort(); @endphp
                    @foreach($jurusanList as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div class="filter-hint filter-hint-info">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span id="nk-kelas-info">Menampilkan {{ $allKelas->count() }} kelas</span>
        </div>
    </div>

    {{-- ── Main Table: Ringkasan per Kelas ── --}}
    <div class="table-card" style="margin-bottom:24px">
        <table id="nk-kelas-table">
            <thead>
                <tr>
                    <th style="width:48px">No.</th>
                    <th>Nama Kelas</th>
                    <th style="width:80px;text-align:center">Tingkat</th>
                    <th style="width:100px">Jurusan</th>
                    <th style="width:100px;text-align:center">Jumlah Siswa</th>
                </tr>
            </thead>
            <tbody id="nk-kelas-tbody">
                @php
                    $orderMap = ['X' => 1, 'XI' => 2, 'XII' => 3];
                    $sortedKelas = $allKelas->sort(function ($a, $b) use ($orderMap) {
                        $cmp = ($orderMap[$a->tingkat_kelas] ?? 0) - ($orderMap[$b->tingkat_kelas] ?? 0);
                        if ($cmp !== 0) return $cmp;
                        return strcmp($a->jurusan, $b->jurusan);
                    });
                @endphp
                @foreach($sortedKelas as $idx => $k)
                <tr class="nk-kelas-row" data-tingkat="{{ $k->tingkat_kelas }}" data-jurusan="{{ $k->jurusan }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td style="font-weight:600;color:#1e293b;font-size:13.5px">{{ $k->nama_kelas }}</td>
                    <td style="text-align:center">
                        @if(strtoupper($k->tingkat_kelas) === 'XII')
                            <span class="badge badge-danger">{{ $k->tingkat_kelas }}</span>
                        @elseif(strtoupper($k->tingkat_kelas) === 'XI')
                            <span class="badge badge-warning">{{ $k->tingkat_kelas }}</span>
                        @else
                            <span class="badge badge-info">{{ $k->tingkat_kelas }}</span>
                        @endif
                    </td>
                    <td style="color:#475569;font-size:13px">{{ $k->jurusan }}</td>
                    <td style="text-align:center;font-weight:700;color:#1e293b;font-size:14px">{{ $k->siswa_count ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="table-footer">
            <span id="nk-kelas-info-footer" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan {{ $allKelas->count() }} kelas
            </span>
        </div>
    </div>

</div>

<script>
let nkRetainedIds = [];

function filterKelasRingkasan() {
    const tingkat = document.getElementById('nk-filter-tingkat').value.toLowerCase();
    const jurusan = document.getElementById('nk-filter-jurusan').value.toLowerCase();
    const rows = document.querySelectorAll('#nk-kelas-tbody .nk-kelas-row');
    let visible = 0;

    rows.forEach(row => {
        const t = (row.dataset.tingkat || '').toLowerCase();
        const j = (row.dataset.jurusan || '').toLowerCase();
        const show = (!tingkat || t === tingkat) && (!jurusan || j === jurusan);
        row.style.display = show ? '' : 'none';
        if (show) {
            visible++;
            row.querySelector('td:first-child').textContent = visible;
        }
    });

    document.getElementById('nk-kelas-info').textContent = `Menampilkan ${visible} kelas`;
    document.getElementById('nk-kelas-info-footer').textContent = `Menampilkan ${visible} kelas`;
}

function loadSiswaTidakNaik() {
    const idKelas = document.getElementById('nk-pilih-kelas').value;
    const container = document.getElementById('nk-siswa-container');
    const list = document.getElementById('nk-siswa-list');
    const loading = document.getElementById('nk-siswa-loading');
    const empty = document.getElementById('nk-siswa-empty');

    if (!idKelas) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    list.innerHTML = '';
    loading.style.display = 'block';
    empty.style.display = 'none';

    fetch(`/naik-kelas/siswa/${idKelas}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok || data.status === 'error') throw new Error(data.message);
        return data;
    })
    .then(data => {
        loading.style.display = 'none';
        const siswa = data.data;

        if (siswa.length === 0) {
            empty.style.display = 'block';
            return;
        }

        siswa.forEach(s => {
            const checked = nkRetainedIds.includes(s.id_siswa) ? 'checked' : '';
            const jkLabel = s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
            const jkColor = s.jenis_kelamin === 'L' ? '#2563eb' : '#db2777';
            list.innerHTML += `
                <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:all 0.15s;background:${checked ? '#faf5ff' : '#fff'}"
                       onmouseenter="this.style.borderColor='#c4b5fd'" onmouseleave="this.style.borderColor='#e2e8f0'">
                    <input type="checkbox" value="${s.id_siswa}" ${checked}
                           onchange="toggleRetained(${s.id_siswa}, this.checked)"
                           style="accent-color:#7c3aed;width:16px;height:16px;flex-shrink:0">
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${s.nama_siswa}</div>
                        <div style="font-size:11px;color:#94a3b8">${s.nisn || '-'}</div>
                    </div>
                    <span style="font-size:11px;padding:2px 8px;border-radius:4px;background:${jkColor}10;color:${jkColor};font-weight:600;flex-shrink:0">${jkLabel}</span>
                </label>
            `;
        });

        updateRetainedCount();
    })
    .catch(err => {
        loading.style.display = 'none';
        list.innerHTML = '<div style="color:#e11d48;font-size:13px;text-align:center;padding:10px">Gagal memuat data siswa.</div>';
    });
}

function toggleRetained(id, checked) {
    if (checked) {
        if (!nkRetainedIds.includes(id)) nkRetainedIds.push(id);
    } else {
        nkRetainedIds = nkRetainedIds.filter(i => i !== id);
    }

    const label = document.querySelector(`input[type="checkbox"][value="${id}"]`)?.closest('label');
    if (label) {
        label.style.background = checked ? '#faf5ff' : '#fff';
    }

    updateRetainedCount();
}

function toggleSelectAllTidakNaik(checked) {
    document.querySelectorAll('#nk-siswa-list input[type="checkbox"]').forEach(cb => {
        cb.checked = checked;
        const id = parseInt(cb.value);
        toggleRetained(id, checked);
    });
}

function updateRetainedCount() {
    const countEl = document.getElementById('nk-retained-count');
    const numEl = document.getElementById('nk-retained-num');
    if (nkRetainedIds.length > 0) {
        countEl.style.display = 'flex';
        numEl.textContent = nkRetainedIds.length;
    } else {
        countEl.style.display = 'none';
    }
}

function naikKelasPreview() {
    Swal.fire({
        title: 'Proses Naik Kelas?',
        html: '<div style="color:#64748b;font-size:13.5px">Sedang memuat data siswa...</div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        showCancelButton: false,
        customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title' },
        buttonsStyling: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('/naik-kelas/preview', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal memuat data');
        return data;
    })
    .then(data => {
        const d = data.data;
        let html = '<div style="text-align:left;font-size:13.5px;color:#475569;line-height:1.7">';

        if (d.lulus > 0) {
            html += `<div style="margin-bottom:10px"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#db2777;margin-right:6px"></span><strong>${d.lulus} siswa kelas XII</strong> akan lulus → dipindah ke alumni</div>`;
        }
        if (d.xi_ke_xii.length > 0) {
            html += `<div style="margin-bottom:10px"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#d97706;margin-right:6px"></span><strong>${d.xi_ke_xii.length} siswa kelas XI</strong> naik ke XII</div>`;
        }
        if (d.x_ke_xi.length > 0) {
            html += `<div style="margin-bottom:10px"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#2563eb;margin-right:6px"></span><strong>${d.x_ke_xi.length} siswa kelas X</strong> naik ke XI</div>`;
        }

        if (nkRetainedIds.length > 0) {
            html += `<div style="margin-top:12px;padding-top:10px;border-top:1px solid #e2e8f0"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#7c3aed;margin-right:6px"></span><strong>${nkRetainedIds.length} siswa</strong> <strong style="color:#7c3aed">tidak naik kelas</strong> (tinggal di kelas yang sama)</div>`;
        }

        if (d.lulus === 0 && d.xi_ke_xii.length === 0 && d.x_ke_xi.length === 0 && nkRetainedIds.length === 0) {
            html += '<div style="color:#94a3b8;text-align:center;padding:10px">Tidak ada siswa aktif untuk dipromosikan.</div>';
        }

        html += '</div>';

        Swal.fire({
            title: 'Konfirmasi Naik Kelas',
            html: html,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses Sekarang',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                confirmButton: 'custom-swal-confirm',
                cancelButton: 'custom-swal-cancel'
            },
            focusCancel: true
        }).then(result => {
            if (result.isConfirmed) {
                executeNaikKelas();
            }
        });
    })
    .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan.', 'error'));
}

function executeNaikKelas() {
    Swal.fire({
        title: 'Memproses...',
        html: '<div style="color:#64748b;font-size:13.5px">Sedang menjalankan naik kelas...</div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title' },
        buttonsStyling: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('/naik-kelas/execute', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ retained_ids: nkRetainedIds })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok || data.status === 'error') throw new Error(data.message || 'Gagal menjalankan naik kelas');
        return data;
    })
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: `<div style="font-size:14px;color:#475569">${data.message}</div>`,
            customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
            buttonsStyling: false
        }).then(() => reloadCurrentPage());
    })
    .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan.', 'error'));
}
</script>
