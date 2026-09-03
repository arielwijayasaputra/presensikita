<div class="page-content page-anim" id="page-data-jurusan" style="display:none">

    {{-- ── Page Header ── --}}
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Data Jurusan / Program Keahlian</div>
            <div class="page-subtitle">
                @php
                    $tahunAjaranStr = ($tahunAjaran->tahun_ajaran ?? '2026/2027') . ' (' . ($tahunAjaran->semester ?? 'Ganjil') . ')';
                @endphp
                Tahun Ajaran {{ $tahunAjaranStr }}
            </div>
        </div>
        <button class="btn-primary" onclick="tambahJurusanModal()" id="btn-tambah-jurusan" style="border-radius:10px;padding:10px 20px;font-size:13.5px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jurusan
        </button>
    </div>

    {{-- ── Ringkasan Jurusan ── --}}
    <div style="margin-bottom:20px">
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:14px">Ringkasan Jurusan</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px">

            {{-- Total Jurusan --}}
            <div class="jurusan-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Total Jurusan</div>
                    <div style="font-size:22px;font-weight:800;color:#1e293b;line-height:1.1">{{ $allJurusan->count() }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Program Keahlian</div>
                </div>
            </div>

            {{-- Jurusan Aktif --}}
            <div class="jurusan-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Jurusan Aktif</div>
                    <div style="font-size:22px;font-weight:800;color:#16a34a;line-height:1.1">{{ $allJurusan->where('is_aktif', true)->count() }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Dapat Menerima Kelas</div>
                </div>
            </div>

            {{-- Jurusan Nonaktif --}}
            <div class="jurusan-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#fff1f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Jurusan Nonaktif</div>
                    <div style="font-size:22px;font-weight:800;color:#e11d48;line-height:1.1">{{ $allJurusan->where('is_aktif', false)->count() }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Tidak Aktif</div>
                </div>
            </div>

            {{-- Total Kelas Terkait --}}
            <div class="jurusan-summary-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)">
                <div style="width:46px;height:46px;background:#f5f3ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div>
                    <div style="font-size:11.5px;color:#64748b;font-weight:500;margin-bottom:2px">Total Kelas</div>
                    <div style="font-size:22px;font-weight:800;color:#7c3aed;line-height:1.1">{{ $allKelas->count() }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:1px">Kelas Terdaftar</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">
        {{-- Search --}}
        <div class="filter-group" style="flex:1;min-width:200px">
            <label>Cari Jurusan</label>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="filter-input" id="cari-jurusan"
                    placeholder="Ketik kode atau nama jurusan..."
                    oninput="filterJurusanPage(this.value)"
                    style="width:100%;max-width:320px;padding-left:32px">
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="filter-group" style="min-width:160px">
            <label>Status</label>
            <div style="position:relative">
                <select class="filter-select" id="filter-status-jurusan" onchange="filterJurusanPage(document.getElementById('cari-jurusan').value)" style="width:100%;padding-right:28px;appearance:none">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
    </div>

    {{-- ── Main Table ── --}}
    <div class="table-card" style="margin-bottom:20px">
        <table id="jurusan-table">
            <thead>
                <tr>
                    <th style="width:48px">No.</th>
                    <th style="width:120px">Kode Jurusan</th>
                    <th>Nama Jurusan / Program Keahlian</th>
                    <th>Deskripsi</th>
                    <th style="width:110px;text-align:center;white-space:nowrap">Jumlah Kelas</th>
                    <th style="width:110px;text-align:center;white-space:nowrap">Siswa Aktif</th>
                    <th style="width:100px;text-align:center">Status</th>
                    <th style="width:120px;text-align:center">Aktif / Nonaktif</th>
                    <th style="width:90px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="jurusan-tbody">
                @forelse($allJurusan as $idx => $j)
                @php
                    $siswaAktifCount = $j->siswa_aktif_count ?? $j->countSiswaAktif();
                @endphp
                <tr class="jurusan-row"
                    id="row-jurusan-{{ $j->id_jurusan }}"
                    data-search="{{ strtolower($j->kode_jurusan . ' ' . $j->nama_jurusan . ' ' . ($j->deskripsi ?? '')) }}"
                    data-status="{{ $j->is_aktif ? 'aktif' : 'nonaktif' }}"
                    data-siswa-aktif="{{ $siswaAktifCount }}">
                    <td style="color:#94a3b8;font-weight:600;font-size:13px">{{ $idx + 1 }}</td>
                    <td>
                        <span style="display:inline-block;padding:4px 10px;background:#e0f2fe;color:#0369a1;font-weight:800;font-size:13px;border-radius:6px;letter-spacing:0.02em">
                            {{ $j->kode_jurusan }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;color:#1e293b;font-size:13.5px">{{ $j->nama_jurusan }}</span>
                    </td>
                    <td style="color:#64748b;font-size:13px">
                        {{ $j->deskripsi ?: '-' }}
                    </td>
                    <td style="text-align:center;font-weight:700;color:#1e293b">
                        <span style="padding:3px 8px;background:#f1f5f9;border-radius:6px;font-size:12.5px">
                            {{ $j->kelas_count ?? 0 }} Kelas
                        </span>
                    </td>
                    <td style="text-align:center;font-weight:700">
                        @if($siswaAktifCount > 0)
                            <span style="padding:3px 8px;background:#dcfce7;color:#15803d;border-radius:6px;font-size:12.5px">
                                {{ $siswaAktifCount }} Siswa
                            </span>
                        @else
                            <span style="padding:3px 8px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:12.5px">
                                0 Siswa
                            </span>
                        @endif
                    </td>
                    <td style="text-align:center" id="status-cell-jurusan-{{ $j->id_jurusan }}">
                        @if($j->is_aktif)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <label class="toggle-switch" style="position:relative;display:inline-block;width:38px;height:22px;cursor:pointer;vertical-align:middle">
                            <input type="checkbox" {{ $j->is_aktif ? 'checked' : '' }} onchange="toggleAktifJurusan({{ $j->id_jurusan }}, this, '{{ htmlspecialchars($j->nama_jurusan) }}', {{ (int) $siswaAktifCount }})" style="opacity:0;width:0;height:0">
                            <span class="toggle-slider" style="position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:{{ $j->is_aktif ? '#10b981' : '#cbd5e1' }};transition:.3s;border-radius:22px">
                                <span class="toggle-knob" style="position:absolute;height:16px;width:16px;left:{{ $j->is_aktif ? '19px' : '3px' }};bottom:3px;background-color:white;transition:.3s;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                        </label>
                    </td>
                    <td style="text-align:center">
                        <div style="display:inline-flex;align-items:center;gap:6px">
                            {{-- Edit Button --}}
                            <button onclick="editJurusanModal({{ $j->id_jurusan }}, '{{ htmlspecialchars($j->kode_jurusan) }}', '{{ htmlspecialchars($j->nama_jurusan) }}', '{{ htmlspecialchars($j->deskripsi ?? '') }}')"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Edit Jurusan">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            {{-- Hapus Button --}}
                            <button onclick="hapusJurusan({{ $j->id_jurusan }}, '{{ htmlspecialchars($j->nama_jurusan) }}', {{ (int) ($j->kelas_count ?? 0) }}, {{ (int) $siswaAktifCount }})"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;border-radius:8px;cursor:pointer;transition:all 0.2s;"
                                title="Hapus Jurusan">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="jurusan-empty-row">
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 10px;display:block"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        Belum ada data jurusan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Table Footer --}}
        <div id="jurusan-table-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <span id="jurusan-info-text" style="font-size:12.5px;color:#64748b;font-weight:500">
                Menampilkan {{ $allJurusan->count() }} data jurusan
            </span>
        </div>
    </div>

</div>

<style>
.jurusan-summary-card { transition:transform 0.2s,box-shadow 0.2s; }
.jurusan-summary-card:hover { transform:translateY(-2px);box-shadow:0 4px 14px rgba(0,0,0,0.09)!important; }
</style>

<script>
function filterJurusanPage(query) {
    const q = (query || '').toLowerCase().trim();
    const statusFilter = document.getElementById('filter-status-jurusan')?.value || '';
    const rows = document.querySelectorAll('#jurusan-tbody .jurusan-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const searchTarget = row.dataset.search || '';
        const statusTarget = row.dataset.status || '';

        const matchSearch = !q || searchTarget.includes(q);
        const matchStatus = !statusFilter || statusTarget === statusFilter;

        if (matchSearch && matchStatus) {
            row.style.display = '';
            visibleCount++;
            const tdNo = row.querySelector('td');
            if (tdNo) tdNo.textContent = visibleCount;
        } else {
            row.style.display = 'none';
        }
    });

    const emptyRow = document.getElementById('jurusan-empty-row');
    if (emptyRow) {
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
    }

    const infoText = document.getElementById('jurusan-info-text');
    if (infoText) {
        infoText.textContent = `Menampilkan ${visibleCount} dari ${rows.length} data jurusan`;
    }
}

function tambahJurusanModal() {
    Swal.fire({
        title: 'Tambah Jurusan Baru',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div class="swal-form-container" style="text-align:left;font-family:inherit">
                <div class="swal-form-group" style="margin-bottom:14px">
                    <label for="swal-jkode" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">Kode Jurusan / Singkatan</label>
                    <input id="swal-jkode" class="swal-form-input" placeholder="Contoh: RPL, TKJ, DKV, AK" style="width:100%;text-transform:uppercase;font-weight:700;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13.5px">
                </div>
                <div class="swal-form-group" style="margin-bottom:14px">
                    <label for="swal-jnama" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">Nama Lengkap Program Keahlian</label>
                    <input id="swal-jnama" class="swal-form-input" placeholder="Contoh: Rekayasa Perangkat Lunak" style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13.5px">
                </div>
                <div class="swal-form-group">
                    <label for="swal-jdesc" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">Deskripsi / Keterangan (Opsional)</label>
                    <textarea id="swal-jdesc" class="swal-form-input" placeholder="Deskripsi ringkas program keahlian..." style="width:100%;height:80px;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;resize:vertical"></textarea>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Jurusan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const kode = document.getElementById('swal-jkode').value.trim();
            const nama = document.getElementById('swal-jnama').value.trim();
            const desc = document.getElementById('swal-jdesc').value.trim();

            if (!kode) {
                Swal.showValidationMessage('Kode jurusan tidak boleh kosong');
                return false;
            }
            if (!nama) {
                Swal.showValidationMessage('Nama jurusan tidak boleh kosong');
                return false;
            }

            return {
                kode_jurusan: kode.toUpperCase(),
                nama_jurusan: nama,
                deskripsi: desc || null
            };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch('/jurusan/tambah', {
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
                if (!res.ok || data.status === 'error') {
                    throw new Error(data.message || 'Gagal menambahkan jurusan');
                }
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Jurusan berhasil ditambahkan.',
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' }
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

function editJurusanModal(id, kode, nama, deskripsi) {
    Swal.fire({
        title: 'Edit Data Jurusan',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div class="swal-form-container" style="text-align:left;font-family:inherit">
                <div class="swal-form-group" style="margin-bottom:14px">
                    <label for="swal-edit-jkode" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">Kode Jurusan / Singkatan</label>
                    <input id="swal-edit-jkode" class="swal-form-input" value="${kode}" placeholder="Contoh: RPL, TKJ, DKV, AK" style="width:100%;text-transform:uppercase;font-weight:700;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13.5px">
                </div>
                <div class="swal-form-group" style="margin-bottom:14px">
                    <label for="swal-edit-jnama" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">Nama Lengkap Program Keahlian</label>
                    <input id="swal-edit-jnama" class="swal-form-input" value="${nama}" placeholder="Contoh: Rekayasa Perangkat Lunak" style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13.5px">
                </div>
                <div class="swal-form-group">
                    <label for="swal-edit-jdesc" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">Deskripsi / Keterangan (Opsional)</label>
                    <textarea id="swal-edit-jdesc" class="swal-form-input" placeholder="Deskripsi ringkas program keahlian..." style="width:100%;height:80px;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;resize:vertical">${deskripsi || ''}</textarea>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const newKode = document.getElementById('swal-edit-jkode').value.trim();
            const newNama = document.getElementById('swal-edit-jnama').value.trim();
            const newDesc = document.getElementById('swal-edit-jdesc').value.trim();

            if (!newKode) {
                Swal.showValidationMessage('Kode jurusan tidak boleh kosong');
                return false;
            }
            if (!newNama) {
                Swal.showValidationMessage('Nama jurusan tidak boleh kosong');
                return false;
            }

            return {
                kode_jurusan: newKode.toUpperCase(),
                nama_jurusan: newNama,
                deskripsi: newDesc || null
            };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch(`/jurusan/${id}/update`, {
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
                if (!res.ok || data.status === 'error') {
                    throw new Error(data.message || 'Gagal memperbarui jurusan');
                }
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Data jurusan berhasil diperbarui.',
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' }
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

function toggleAktifJurusan(id, checkboxEl, nama, siswaAktifCount) {
    const isChecked = checkboxEl.checked;
    const actionText = isChecked ? 'mengaktifkan' : 'menonaktifkan';

    // Jika ingin menonaktifkan dan masih ada siswa aktif (bukan alumni)
    if (!isChecked && siswaAktifCount > 0) {
        checkboxEl.checked = true;
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Dapat Dinonaktifkan',
            html: `Jurusan <strong>${nama}</strong> tidak dapat dinonaktifkan karena masih terdapat <strong>${siswaAktifCount}</strong> siswa aktif (bukan alumni) yang terdaftar.<br><br><span style="font-size:13px;color:#64748b">Jurusan hanya dapat dinonaktifkan jika semua siswa telah lulus (menjadi alumni) atau dipindahkan ke jurusan lain.</span>`,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                confirmButton: 'custom-swal-confirm'
            },
            buttonsStyling: false
        });
        return;
    }

    Swal.fire({
        title: `${isChecked ? 'Aktifkan' : 'Nonaktifkan'} Jurusan?`,
        text: `Apakah Anda yakin ingin ${actionText} jurusan ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Ya, ${isChecked ? 'Aktifkan' : 'Nonaktifkan'}`,
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/jurusan/${id}/toggle-aktif`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok || data.status === 'error') {
                    throw new Error(data.message || 'Gagal mengubah status jurusan');
                }
                return data;
            })
            .then(data => {
                const newStatus = data.is_aktif;
                const row = document.getElementById(`row-jurusan-${id}`);
                const statusCell = document.getElementById(`status-cell-jurusan-${id}`);
                const slider = checkboxEl.nextElementSibling;
                const knob = slider ? slider.querySelector('.toggle-knob') : null;

                if (slider) {
                    slider.style.backgroundColor = newStatus ? '#10b981' : '#cbd5e1';
                }
                if (knob) {
                    knob.style.left = newStatus ? '19px' : '3px';
                }

                if (statusCell) {
                    statusCell.innerHTML = newStatus
                        ? '<span class="badge badge-success">Aktif</span>'
                        : '<span class="badge badge-danger">Nonaktif</span>';
                }

                if (row) {
                    row.dataset.status = newStatus ? 'aktif' : 'nonaktif';
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title' }
                });
            })
            .catch(err => {
                checkboxEl.checked = !isChecked;
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: err.message || 'Terjadi kesalahan sistem.',
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false
                });
            });
        } else {
            checkboxEl.checked = !isChecked;
        }
    });
}

function hapusJurusan(id, nama, kelasCount, siswaAktifCount) {
    if (siswaAktifCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Dapat Dihapus',
            html: `Jurusan <strong>${nama}</strong> tidak dapat dihapus karena masih terdapat <strong>${siswaAktifCount}</strong> siswa aktif (bukan alumni) yang terdaftar.<br><br><span style="font-size:13px;color:#64748b">Jurusan hanya dapat dihapus jika sudah tidak ada siswa aktif maupun kelas terdaftar pada jurusan ini.</span>`,
            customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
            buttonsStyling: false
        });
        return;
    }

    if (kelasCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Dapat Dihapus',
            html: `Jurusan <strong>${nama}</strong> masih digunakan oleh <strong>${kelasCount}</strong> data kelas terdaftar.<br><br><span style="font-size:13px;color:#64748b">Silakan hapus atau pindahkan kelas terkait terlebih dahulu.</span>`,
            customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
            buttonsStyling: false
        });
        return;
    }

    Swal.fire({
        title: 'Hapus Jurusan?',
        html: `Apakah Anda yakin ingin menghapus jurusan <strong>${nama}</strong> secara permanen?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
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
            fetch(`/jurusan/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok || data.status === 'error') {
                    throw new Error(data.message || 'Gagal menghapus jurusan');
                }
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus',
                    text: data.message || 'Jurusan berhasil dihapus.',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title' }
                }).then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: err.message || 'Terjadi kesalahan sistem.',
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false
            }));
        }
    });
}
</script>

