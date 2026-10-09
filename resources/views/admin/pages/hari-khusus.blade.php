<div class="page-content page-anim" id="page-hari-khusus" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Event &amp; Pulang Cepat</div>
            <div class="page-subtitle">Atur hari khusus yang mengubah aturan presensi (Event libur/hadir atau jam kepulangan lebih awal) per tingkat kelas.</div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:18px">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1">
                <div style="position:relative;min-width:220px">
                    <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="search-hari-khusus" oninput="filterHariKhususTable()" onkeyup="filterHariKhususTable()" placeholder="Cari judul event / hari khusus..." class="filter-input" style="padding:8px 12px 8px 32px;font-size:12.5px;border-radius:8px;border:1px solid #cbd5e1;width:100%;box-sizing:border-box">
                </div>
                <select id="filter-tipe-hari-khusus" onchange="filterHariKhususTable()" class="filter-select" style="border-radius:8px;padding:8px 12px;font-size:12.5px">
                    <option value="">Semua Tipe</option>
                    <option value="event">Event Khusus</option>
                    <option value="pulang_cepat">Pulang Cepat</option>
                </select>
                <select id="filter-tingkat-hari-khusus" onchange="filterHariKhususTable()" class="filter-select" style="border-radius:8px;padding:8px 12px;font-size:12.5px">
                    <option value="">Semua Tingkat</option>
                    <option value="10">Kelas 10</option>
                    <option value="11">Kelas 11</option>
                    <option value="12">Kelas 12</option>
                </select>
            </div>
            <div>
                <button type="button" class="btn-primary" onclick="bukaModalHariKhusus()" style="border-radius:8px;padding:9px 16px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Hari Khusus
                </button>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-hari-khusus" style="min-width:820px">
                <thead>
                    <tr>
                        <th style="width:45px;text-align:center">No</th>
                        <th style="width:130px">Jenis</th>
                        <th>Judul &amp; Keterangan</th>
                        <th style="width:170px">Tanggal Berlaku</th>
                        <th style="width:150px">Tingkat Kelas</th>
                        <th style="width:195px">Aturan / Jam</th>
                        <th style="width:100px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="body-hari-khusus">
                    @forelse($allHariKhusus as $idx => $hk)
                        @php
                            $tingkatList = $hk->tingkat ?: [];
                            $tglMulaiFormatted = $hk->tanggal_mulai ? $hk->tanggal_mulai->format('d M Y') : '-';
                            $tglSelesaiFormatted = $hk->tanggal_selesai ? $hk->tanggal_selesai->format('d M Y') : '-';
                            $isSameDate = ($hk->tanggal_mulai && $hk->tanggal_selesai && $hk->tanggal_mulai->toDateString() === $hk->tanggal_selesai->toDateString());
                        @endphp
                        <tr class="row-hari-khusus"
                            data-tipe="{{ $hk->tipe }}"
                            data-tingkat="{{ implode(',', $tingkatList) }}"
                            data-search="{{ strtolower($hk->judul . ' ' . $hk->tipe . ' ' . implode(' ', $tingkatList)) }}">
                            <td style="text-align:center;color:#94a3b8;font-weight:600">{{ $idx + 1 }}</td>
                            <td>
                                @if($hk->tipe === 'pulang_cepat')
                                    <span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:12px;font-weight:700;padding:4px 9px;display:inline-flex;align-items:center;gap:4px">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Pulang Cepat
                                    </span>
                                @else
                                    <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:12px;font-weight:700;padding:4px 9px;display:inline-flex;align-items:center;gap:4px">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        Event Khusus
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong style="color:var(--text-primary, #0f172a);font-size:13.5px;display:block">{{ $hk->judul }}</strong>
                            </td>
                            <td style="font-size:12.5px;color:#475569">
                                @if($isSameDate)
                                    <strong>{{ $tglMulaiFormatted }}</strong>
                                @else
                                    <strong>{{ $tglMulaiFormatted }}</strong>
                                    <span style="color:#94a3b8;display:block;font-size:11.5px">s/d {{ $tglSelesaiFormatted }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;flex-wrap:wrap">
                                    @foreach($tingkatList as $t)
                                        <span class="badge badge-secondary" style="font-size:11px;padding:3px 7px;font-weight:700">Kelas {{ $t }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($hk->tipe === 'pulang_cepat')
                                    <span style="font-size:12.5px;font-weight:700;color:#c2410c">
                                        Pukul {{ substr($hk->jam_pulang, 0, 5) }} WIB
                                    </span>
                                    <span style="display:block;font-size:11px;color:#94a3b8;margin-top:2px">Sesi setelahnya ditiadakan</span>
                                @else
                                    @if($hk->aturan_presensi === 'diliburkan')
                                        <span class="badge" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:11.5px;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:5px">
                                            <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#dc2626"></span>
                                            Diliburkan (Bebas Absen)
                                        </span>
                                    @elseif($hk->aturan_presensi === 'hadir_event')
                                        <span class="badge badge-success" style="font-size:11.5px;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:5px">
                                            <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#16a34a"></span>
                                            Hadir Otomatis Event
                                        </span>
                                    @else
                                        <span class="badge badge-info" style="font-size:11.5px;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:5px">
                                            <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#2563eb"></span>
                                            Presensi Normal
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align:center">
                                <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                    <button type="button" class="jam-edit-btn" aria-label="Ubah Hari Khusus" title="Ubah data" onclick='editHariKhusus(@json($hk))'>
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    </button>
                                    <button type="button" class="jam-del-btn" aria-label="Hapus Hari Khusus" title="Hapus data" onclick="hapusHariKhusus({{ $hk->id_hari_khusus }}, '{{ addslashes($hk->judul) }}')">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-hari-khusus"><td colspan="7" style="text-align:center;color:#64748b;padding:32px">Belum ada data hari khusus (Event atau Pulang Cepat).</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="no-result-hari-khusus" style="display:none;color:#64748b;font-size:13px;text-align:center;padding:24px">Hari khusus tidak ditemukan dengan filter yang dipilih.</div>
    </div>
</div>

<style>
/* Hari Khusus Popup Styles */
.hk-type-card-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 14px;
}
.hk-type-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    cursor: pointer;
    background: #f8fafc;
    transition: all .15s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    user-select: none;
}
.hk-type-card:hover {
    border-color: #93c5fd;
    background: #eff6ff;
}
.hk-type-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 2px 6px rgba(37,99,235,0.12);
}
.hk-tingkat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 6px;
}
.hk-tingkat-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 8px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: all .15s ease;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.hk-tingkat-card:hover {
    border-color: #93c5fd;
    background: #eff6ff;
}
.hk-tingkat-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
    color: #1d4ed8;
}

[data-theme="dark"] .hk-type-card,
[data-theme="dark"] .hk-tingkat-card {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}
[data-theme="dark"] .hk-type-card.selected,
[data-theme="dark"] .hk-tingkat-card.selected {
    background: rgba(37,99,235,0.2) !important;
    border-color: #3b82f6 !important;
    color: #93c5fd !important;
}
</style>

<script>
function filterHariKhususTable() {
    const searchVal = document.getElementById('search-hari-khusus')?.value.toLowerCase().trim() || '';
    const tipeVal = document.getElementById('filter-tipe-hari-khusus')?.value || '';
    const tingkatVal = document.getElementById('filter-tingkat-hari-khusus')?.value || '';

    const rows = document.querySelectorAll('.row-hari-khusus');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowTipe = row.dataset.tipe || '';
        const rowTingkat = (row.dataset.tingkat || '').split(',');
        const rowSearch = row.dataset.search || '';

        const matchSearch = !searchVal || rowSearch.includes(searchVal);
        const matchTipe = !tipeVal || rowTipe === tipeVal;
        const matchTingkat = !tingkatVal || rowTingkat.includes(tingkatVal);

        if (matchSearch && matchTipe && matchTingkat) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noResult = document.getElementById('no-result-hari-khusus');
    if (noResult) {
        noResult.style.display = (rows.length > 0 && visibleCount === 0) ? 'block' : 'none';
    }
}

async function bukaModalHariKhusus(data = null) {
    const isEdit = !!data;
    const defaultTipe = data ? data.tipe : 'pulang_cepat';
    const defaultJudul = data ? data.judul : '';
    const defaultMulai = data ? (data.tanggal_mulai ? String(data.tanggal_mulai).slice(0, 10) : '') : '{{ now()->toDateString() }}';
    const defaultSelesai = data ? (data.tanggal_selesai ? String(data.tanggal_selesai).slice(0, 10) : '') : defaultMulai;
    const defaultTingkat = data ? (data.tingkat || [10, 11, 12]) : [10, 11, 12];
    const defaultJamPulang = data && data.jam_pulang ? String(data.jam_pulang).slice(0, 5) : '11:40';
    const defaultAturan = data && data.aturan_presensi ? data.aturan_presensi : 'diliburkan';

    const is10 = defaultTingkat.map(Number).includes(10);
    const is11 = defaultTingkat.map(Number).includes(11);
    const is12 = defaultTingkat.map(Number).includes(12);

    const result = await Swal.fire({
        title: isEdit ? 'Ubah Hari Khusus' : 'Tambah Hari Khusus',
        customClass: {
            confirmButton: 'jam-popup-confirm',
            cancelButton: 'jam-popup-cancel'
        },
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: isEdit ? 'Simpan Perubahan' : 'Tambahkan',
        cancelButtonText: 'Batal',
        html: `
            <div style="text-align:left;font-family:inherit">
                <div class="jam-popup-field" style="margin-bottom:14px">
                    <label style="font-weight:700;color:#334155;margin-bottom:8px">Pilih Jenis Hari Khusus:</label>
                    <div class="hk-type-card-grid">
                        <label id="lbl-hk-pulang-cepat" class="hk-type-card ${defaultTipe === 'pulang_cepat' ? 'selected' : ''}">
                            <input type="radio" name="modal_hk_tipe" value="pulang_cepat" ${defaultTipe === 'pulang_cepat' ? 'checked' : ''} style="display:none" onchange="switchHkTipe('pulang_cepat')">
                            <span class="jam-day-check-indicator" style="border-radius:50%;width:18px;height:18px">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8"/></svg>
                            </span>
                            <span style="font-size:13px;font-weight:700">Pulang Cepat</span>
                        </label>
                        <label id="lbl-hk-event" class="hk-type-card ${defaultTipe === 'event' ? 'selected' : ''}">
                            <input type="radio" name="modal_hk_tipe" value="event" ${defaultTipe === 'event' ? 'checked' : ''} style="display:none" onchange="switchHkTipe('event')">
                            <span class="jam-day-check-indicator" style="border-radius:50%;width:18px;height:18px">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8"/></svg>
                            </span>
                            <span style="font-size:13px;font-weight:700">Event Khusus</span>
                        </label>
                    </div>
                </div>

                <div class="jam-popup-field" style="margin-bottom:12px">
                    <label for="modal-hk-judul">Judul / Keterangan Event</label>
                    <input id="modal-hk-judul" type="text" value="${defaultJudul}" placeholder="Contoh: Rapat Pleno Guru / Classmeeting / Peringatan Hardiknas" style="width:100%;box-sizing:border-box;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13.5px;background:#f8fafc">
                </div>

                <div class="jam-popup-time-grid" style="margin-bottom:12px">
                    <div class="jam-popup-field" style="margin-bottom:0">
                        <label for="modal-hk-tgl-mulai">Tanggal Mulai</label>
                        <input id="modal-hk-tgl-mulai" type="date" value="${defaultMulai}" onchange="if(document.getElementById('modal-hk-tgl-selesai').value < this.value) document.getElementById('modal-hk-tgl-selesai').value = this.value;" style="width:100%;box-sizing:border-box;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13.5px;background:#f8fafc">
                    </div>
                    <div class="jam-popup-field" style="margin-bottom:0">
                        <label for="modal-hk-tgl-selesai">Tanggal Selesai</label>
                        <input id="modal-hk-tgl-selesai" type="date" value="${defaultSelesai}" style="width:100%;box-sizing:border-box;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13.5px;background:#f8fafc">
                    </div>
                </div>

                <div class="jam-popup-field" style="margin-bottom:12px">
                    <label>Berlaku untuk Tingkat Kelas (Minimal 1):</label>
                    <div class="hk-tingkat-grid">
                        <label class="hk-tingkat-card ${is10 ? 'selected' : ''}">
                            <input type="checkbox" id="modal-hk-t10" value="10" ${is10 ? 'checked' : ''} style="display:none" onchange="this.closest('.hk-tingkat-card').classList.toggle('selected', this.checked)">
                            Kelas 10
                        </label>
                        <label class="hk-tingkat-card ${is11 ? 'selected' : ''}">
                            <input type="checkbox" id="modal-hk-t11" value="11" ${is11 ? 'checked' : ''} style="display:none" onchange="this.closest('.hk-tingkat-card').classList.toggle('selected', this.checked)">
                            Kelas 11
                        </label>
                        <label class="hk-tingkat-card ${is12 ? 'selected' : ''}">
                            <input type="checkbox" id="modal-hk-t12" value="12" ${is12 ? 'checked' : ''} style="display:none" onchange="this.closest('.hk-tingkat-card').classList.toggle('selected', this.checked)">
                            Kelas 12
                        </label>
                    </div>
                </div>

                <!-- Opsi Pulang Cepat -->
                <div id="section-hk-pulang-cepat" style="display:${defaultTipe === 'pulang_cepat' ? 'block' : 'none'};margin-bottom:12px">
                    <div class="jam-popup-field" style="margin-bottom:0">
                        <label for="modal-hk-jam-pulang">Jam Kepulangan (Batas Akhir Jam Masuk Mengajar)</label>
                        <input id="modal-hk-jam-pulang" type="time" value="${defaultJamPulang}" style="width:100%;box-sizing:border-box;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13.5px;background:#f8fafc">
                        <small style="display:block;color:#64748b;margin-top:4px;font-size:11.5px">
                            Sesi pelajaran yang dimulai pada atau setelah jam ini dianggap selesai/tidak ada. Guru tidak wajib isi jurnal dan siswa tidak dihitung alpa.
                        </small>
                    </div>
                </div>

                <!-- Opsi Event -->
                <div id="section-hk-event" style="display:${defaultTipe === 'event' ? 'block' : 'none'};margin-bottom:12px">
                    <div class="jam-popup-field" style="margin-bottom:0">
                        <label for="modal-hk-aturan">Aturan Presensi Siswa &amp; Guru</label>
                        <select id="modal-hk-aturan" class="filter-select" style="width:100%;padding:9px 12px;border-radius:8px;font-size:13.5px">
                            <option value="diliburkan" ${defaultAturan === 'diliburkan' ? 'selected' : ''}>Diliburkan (Tidak ada presensi &amp; tidak dihitung alpa)</option>
                            <option value="hadir_event" ${defaultAturan === 'hadir_event' ? 'selected' : ''}>Hadir Otomatis Event (Semua siswa tercatat hadir)</option>
                            <option value="tetap_wajib" ${defaultAturan === 'tetap_wajib' ? 'selected' : ''}>Tetap Wajib (Presensi berlangsung normal)</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        preConfirm: () => {
            const tipe = document.querySelector('input[name="modal_hk_tipe"]:checked')?.value || 'pulang_cepat';
            const judul = document.getElementById('modal-hk-judul')?.value.trim();
            const tanggalMulai = document.getElementById('modal-hk-tgl-mulai')?.value;
            const tanggalSelesai = document.getElementById('modal-hk-tgl-selesai')?.value;
            const jamPulang = document.getElementById('modal-hk-jam-pulang')?.value;
            const aturanPresensi = document.getElementById('modal-hk-aturan')?.value;

            const tingkats = [];
            if (document.getElementById('modal-hk-t10')?.checked) tingkats.push(10);
            if (document.getElementById('modal-hk-t11')?.checked) tingkats.push(11);
            if (document.getElementById('modal-hk-t12')?.checked) tingkats.push(12);

            if (!judul) {
                Swal.showValidationMessage('Judul / keterangan hari khusus wajib diisi.');
                return false;
            }
            if (!tanggalMulai || !tanggalSelesai) {
                Swal.showValidationMessage('Tanggal mulai dan selesai wajib diisi.');
                return false;
            }
            if (tanggalSelesai < tanggalMulai) {
                Swal.showValidationMessage('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
                return false;
            }
            if (tingkats.length === 0) {
                Swal.showValidationMessage('Pilih minimal satu tingkat kelas (10, 11, atau 12).');
                return false;
            }
            if (tipe === 'pulang_cepat' && !jamPulang) {
                Swal.showValidationMessage('Jam kepulangan wajib diisi untuk jenis Pulang Cepat.');
                return false;
            }

            return {
                tipe: tipe,
                judul: judul,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai,
                tingkat: tingkats,
                jam_pulang: tipe === 'pulang_cepat' ? jamPulang : null,
                aturan_presensi: tipe === 'event' ? aturanPresensi : null
            };
        }
    });

    if (!result.isConfirmed) return;

    const url = isEdit ? `/hari-khusus/${data.id_hari_khusus}/update` : '/hari-khusus/tambah';

    Swal.fire({
        title: 'Menyimpan...',
        text: 'Memproses data hari khusus...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(result.value)
        });

        const resData = await response.json();
        if (!response.ok) {
            throw new Error(resData.message || 'Gagal menyimpan hari khusus.');
        }

        await Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: resData.message,
            confirmButtonColor: '#2563eb'
        });

        location.reload();
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: err.message,
            confirmButtonColor: '#dc2626'
        });
    }
}

function switchHkTipe(tipe) {
    const isPulang = tipe === 'pulang_cepat';
    document.getElementById('lbl-hk-pulang-cepat')?.classList.toggle('selected', isPulang);
    document.getElementById('lbl-hk-event')?.classList.toggle('selected', !isPulang);
    document.getElementById('section-hk-pulang-cepat').style.display = isPulang ? 'block' : 'none';
    document.getElementById('section-hk-event').style.display = isPulang ? 'none' : 'block';
}

function editHariKhusus(data) {
    bukaModalHariKhusus(data);
}

async function hapusHariKhusus(id, judul) {
    const confirm = await Swal.fire({
        title: `Hapus "${judul}"?`,
        text: 'Hari khusus yang dihapus tidak akan lagi mempengaruhi perhitungan presensi.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b'
    });

    if (!confirm.isConfirmed) return;

    try {
        const response = await fetch(`/hari-khusus/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const resData = await response.json();
        if (!response.ok) throw new Error(resData.message || 'Gagal menghapus hari khusus.');

        await Swal.fire({
            icon: 'success',
            title: 'Terhapus',
            text: resData.message,
            confirmButtonColor: '#2563eb'
        });

        location.reload();
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: err.message,
            confirmButtonColor: '#dc2626'
        });
    }
}
</script>
