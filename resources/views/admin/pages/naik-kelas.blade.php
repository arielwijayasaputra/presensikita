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

    {{-- ── Ringkasan Per Kelas ── --}}
    <div style="margin-bottom:24px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:6px">Ringkasan Siswa per Kelas</div>

        {{-- Filter --}}
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:14px">
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
            <div style="font-size:12.5px;color:#94a3b8;padding-bottom:4px" id="nk-kelas-info">Menampilkan {{ $allKelas->count() }} kelas</div>
        </div>

        {{-- Grid Cards --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px" id="nk-kelas-grid">
            @php
                $orderMap = ['X' => 1, 'XI' => 2, 'XII' => 3];
                $sortedKelas = $allKelas->sort(function ($a, $b) use ($orderMap) {
                    $cmp = ($orderMap[$a->tingkat_kelas] ?? 0) - ($orderMap[$b->tingkat_kelas] ?? 0);
                    if ($cmp !== 0) return $cmp;
                    return strcmp($a->jurusan, $b->jurusan);
                });
            @endphp
            @foreach($sortedKelas as $k)
            <div class="nk-kelas-card" data-tingkat="{{ $k->tingkat_kelas }}" data-jurusan="{{ $k->jurusan }}"
                 style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;transition:all 0.2s;cursor:default">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                    @if(strtoupper($k->tingkat_kelas) === 'XII')
                    <span style="font-size:11px;padding:2px 8px;border-radius:4px;background:#fce7f3;color:#db2777;font-weight:700">XII</span>
                    @elseif(strtoupper($k->tingkat_kelas) === 'XI')
                    <span style="font-size:11px;padding:2px 8px;border-radius:4px;background:#fef3c7;color:#d97706;font-weight:700">XI</span>
                    @else
                    <span style="font-size:11px;padding:2px 8px;border-radius:4px;background:#dbeafe;color:#2563eb;font-weight:700">X</span>
                    @endif
                    <span style="font-size:11px;color:#94a3b8;font-weight:600">{{ $k->jurusan }}</span>
                </div>
                <div style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:2px">{{ $k->nama_kelas }}</div>
                <div style="font-size:22px;font-weight:800;color:{{ strtoupper($k->tingkat_kelas) === 'XII' ? '#db2777' : (strtoupper($k->tingkat_kelas) === 'XI' ? '#d97706' : '#2563eb') }}">{{ $k->siswa_count ?? 0 }}</div>
                <div style="font-size:11px;color:#94a3b8">siswa aktif</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Alur Proses ── --}}
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px 24px;margin-bottom:24px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">Alur Proses Naik Kelas</div>
        <div style="display:flex;align-items:center;gap:0;flex-wrap:wrap">
            <div style="flex:1;min-width:180px;text-align:center;padding:14px 10px;background:#fff;border:1px solid #e2e8f0;border-radius:10px">
                <div style="font-size:22px;font-weight:800;color:#db2777;margin-bottom:2px">XII</div>
                <div style="font-size:12px;color:#64748b;font-weight:600">Lulus → Alumni</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:2px">Dipindah ke tabel alumni</div>
            </div>
            <div style="padding:0 8px;color:#94a3b8">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div style="flex:1;min-width:180px;text-align:center;padding:14px 10px;background:#fff;border:1px solid #e2e8f0;border-radius:10px">
                <div style="font-size:22px;font-weight:800;color:#d97706;margin-bottom:2px">XI → XII</div>
                <div style="font-size:12px;color:#64748b;font-weight:600">Naik ke XII</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:2px">Proses kedua</div>
            </div>
            <div style="padding:0 8px;color:#94a3b8">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div style="flex:1;min-width:180px;text-align:center;padding:14px 10px;background:#fff;border:1px solid #e2e8f0;border-radius:10px">
                <div style="font-size:22px;font-weight:800;color:#2563eb;margin-bottom:2px">X → XI</div>
                <div style="font-size:12px;color:#64748b;font-weight:600">Naik ke XI</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:2px">Proses terakhir</div>
            </div>
        </div>
    </div>

    {{-- ── Siswa Tidak Naik Kelas ── --}}
    <div style="margin-bottom:24px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:6px">Siswa Tidak Naik Kelas</div>
        <div style="font-size:12.5px;color:#64748b;margin-bottom:14px">Pilih kelas, lalu centang siswa yang <strong>tidak</strong> akan naik kelas.</div>

        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:14px">
            <div class="filter-group" style="min-width:200px;max-width:300px">
                <label>Pilih Kelas</label>
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
            <div id="nk-retained-count" style="font-size:13px;color:#7c3aed;font-weight:600;padding-bottom:4px;display:none">
                <span id="nk-retained-num">0</span> siswa dipilih tidak naik
            </div>
        </div>

        <div id="nk-siswa-container" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <div style="font-size:13px;font-weight:600;color:#475569">Daftar Siswa</div>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12.5px;color:#64748b">
                    <input type="checkbox" id="nk-select-all" onchange="toggleSelectAllTidakNaik(this.checked)" style="accent-color:#7c3aed;width:15px;height:15px">
                    Pilih Semua
                </label>
            </div>
            <div id="nk-siswa-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:8px"></div>
            <div id="nk-siswa-loading" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;display:none">Memuat data siswa...</div>
            <div id="nk-siswa-empty" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;display:none">Tidak ada siswa aktif di kelas ini.</div>
        </div>
    </div>

    {{-- ── Data Alumni ── --}}
    <div>
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">Data Alumni</div>
        @forelse($alumniTahunan as $a)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#1e293b">{{ $a->tahun_lulus }}</div>
                    <div style="font-size:12px;color:#94a3b8">Tahun Kelulusan</div>
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-size:20px;font-weight:800;color:#16a34a">{{ $a->jumlah }}</div>
                <div style="font-size:11px;color:#94a3b8">alumni</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:30px;color:#94a3b8;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 8px;display:block"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Belum ada data alumni.
        </div>
        @endforelse
    </div>

</div>

<script>
let nkRetainedIds = [];

function filterKelasRingkasan() {
    const tingkat = document.getElementById('nk-filter-tingkat').value.toLowerCase();
    const jurusan = document.getElementById('nk-filter-jurusan').value.toLowerCase();
    const cards = document.querySelectorAll('.nk-kelas-card');
    let visible = 0;

    cards.forEach(card => {
        const t = (card.dataset.tingkat || '').toLowerCase();
        const j = (card.dataset.jurusan || '').toLowerCase();
        const show = (!tingkat || t === tingkat) && (!jurusan || j === jurusan);
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('nk-kelas-info').textContent = `Menampilkan ${visible} kelas`;
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
        countEl.style.display = 'block';
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
        }).then(() => location.reload());
    })
    .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan.', 'error'));
}
</script>
