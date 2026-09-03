<div class="page-content page-anim" id="page-jadwal" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Jadwal Mengajar</div>
            <div class="page-subtitle">Pilih kelas untuk melihat dan mengubah jadwal lengkapnya.</div>
        </div>
    </div>

    @php
        $jadwalKosongList = $allJadwal->filter(fn($item) => empty($item->id_guru))->values();
    @endphp

    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px">
        <div class="card" style="flex:1;min-width:220px;padding:18px 20px;border-left:5px solid #2563eb">
            <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase">Total Jadwal Aktif</div>
            <div style="font-size:30px;font-weight:800;color:#1d4ed8;margin-top:4px">{{ $totalJadwalAktif }}</div>
            <div style="font-size:12px;color:#64748b">Jadwal yang tampil dan digunakan</div>
        </div>
        <button type="button" onclick="bukaModalJadwalKosong()" class="card" style="flex:1;min-width:220px;padding:18px 20px;border:0;border-left:5px solid #f59e0b;text-align:left;cursor:pointer;background:#fff">
            <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase">Belum Ada Guru</div>
            <div style="font-size:30px;font-weight:800;color:#b45309;margin-top:4px">{{ $totalJadwalTanpaGuru }}</div>
            <div style="font-size:12px;color:#64748b">Klik untuk melihat jadwal tanpa guru</div>
        </button>
    </div>

    <div class="card" style="padding:20px 22px">
        <div style="display:flex;justify-content:flex-end;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
            <button class="btn-primary" onclick="bukaFormJadwal()" style="border-radius:8px;padding:10px 16px;font-size:13px">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Jadwal
            </button>
            <button class="btn-primary" onclick="bukaImportJadwalModal()" style="border-radius:8px;padding:10px 16px;font-size:13px;background:var(--green,#22c55e);border-color:var(--green,#22c55e)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload CSV
            </button>
            <button type="button" onclick="hapusSemuaJadwal()" style="border-radius:8px;padding:10px 16px;font-size:13px;background:#dc2626;color:#fff;border:1px solid #dc2626;font-weight:700;cursor:pointer">
                Hapus Semua Jadwal
            </button>

        </div>

        <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">
            <div class="filter-group" style="flex:1;min-width:200px">
                <label>Cari jadwal</label>
                <input type="search" id="jadwal-cari" class="filter-input" placeholder="Ketik guru, mapel, kelas..." oninput="filterJadwal()" style="width:100%">
            </div>
            <div class="filter-group" style="min-width:130px">
                <label>Hari</label>
                <select id="jadwal-hari" class="filter-select" onchange="filterJadwal()" style="width:100%">
                    <option value="">Semua Hari</option>
                    @foreach(\App\Models\Hari::getWeekdayNames() as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="min-width:160px">
                <label>Kelas</label>
                <select id="jadwal-kelas" class="filter-select" onchange="filterJadwal()" style="width:100%">
                    <option value="">Semua Kelas</option>
                    @foreach($allKelas as $kelasItem)
                        <option value="{{ $kelasItem->id_kelas }}">{{ $kelasItem->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="min-width:160px">
                <label>Status Guru</label>
                <select id="jadwal-filter-status" class="filter-select" onchange="filterJadwal()" style="width:100%">
                    <option value="">Semua Status</option>
                    <option value="kosong">Jadwal Kosong (Tanpa Guru)</option>
                    <option value="terisi">Jadwal Terisi</option>
                </select>
            </div>
            <span id="jadwal-count" style="font-size:12px;color:#64748b;padding-bottom:9px">{{ count($allJadwal) }} jadwal</span>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:820px">
                <thead>
                    <tr><th>Hari</th><th>Jam Ke-</th><th>Mata Pelajaran</th><th>Guru</th><th>Kelas</th><th>Aksi</th></tr>
                </thead>
                <tbody id="jadwal-table-body">
                    @forelse($allJadwal as $jadwalItem)
                        @php
                            $isKosong = empty($jadwalItem->id_guru);
                        @endphp
                        <tr class="jadwal-row"
                            data-search="{{ strtolower(($jadwalItem->nama_guru ?? 'kosong belum ada pengampu tanpa guru') . ' ' . $jadwalItem->nama_mapel . ' ' . $jadwalItem->nama_kelas . ' ' . $jadwalItem->hari . ' ' . $jadwalItem->jam_ke) }}"
                            data-hari="{{ $jadwalItem->hari }}"
                            data-kelas="{{ $jadwalItem->id_kelas }}"
                            data-status="{{ $isKosong ? 'kosong' : 'terisi' }}">
                            <td><strong>{{ $jadwalItem->hari }}</strong></td>
                            <td><span class="badge badge-info">Jam ke-{{ $jadwalItem->jam_ke >= 100 ? $jadwalItem->jam_ke - 100 : $jadwalItem->jam_ke }}</span></td>
                            <td>{{ $jadwalItem->nama_mapel }}</td>
                            <td>
                                @if(!$isKosong)
                                    <span style="font-weight:600;color:#1e293b">{{ $jadwalItem->nama_guru }}</span>
                                @else
                                    <span class="badge badge-warning" style="background:#fef3c7;color:#b45309;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:700">
                                        Kosong (Belum Ada Guru)
                                    </span>
                                @endif
                            </td>
                            <td><strong>{{ $jadwalItem->nama_kelas }}</strong></td>
                            <td>
                                @if($isKosong)
                                    <button type="button" class="btn-primary" onclick="pilihGuruJadwalKosong({{ $jadwalItem->id_jadwal }}, '{{ $jadwalItem->hari }}', {{ $jadwalItem->jam_ke }}, {{ $jadwalItem->id_jam }}, '{{ htmlspecialchars($jadwalItem->nama_kelas) }}', '{{ htmlspecialchars($jadwalItem->nama_mapel) }}')" style="padding:6px 12px;font-size:12px;background:#d97706;border-color:#b45309;font-weight:700">
                                        Tugaskan Guru
                                    </button>
                                @else
                                    <button type="button" class="btn-secondary" onclick='editJadwal(@json($jadwalItem))' style="padding:6px 12px;font-size:12px">
                                        Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:#64748b;padding:24px">Belum ada data jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="jadwal-no-result" style="display:none;color:#64748b;font-size:13px;text-align:center;padding:20px">Jadwal tidak ditemukan.</div>
    </div>
</div>

{{-- ── Modal Jadwal Biasa ── --}}
<div id="jadwal-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:200;align-items:center;justify-content:center;padding:20px">
    <div class="card" style="width:100%;max-width:520px;padding:24px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px"><h3 id="jadwal-modal-title" style="font-size:17px">Tambah Jadwal</h3><button type="button" onclick="tutupFormJadwal()" aria-label="Tutup" style="border:0;background:none;font-size:22px;color:#64748b;cursor:pointer">&times;</button></div>
        <form id="jadwal-form" onsubmit="simpanJadwal(event)">
            @csrf
            <input type="hidden" id="jadwal-id">
            <div style="display:grid;gap:12px">
                <div><label>Hari</label><select id="form-jadwal-hari" class="filter-select" disabled required style="width:100%;margin-top:4px;background:#f1f5f9;color:#64748b;cursor:not-allowed">@foreach(\App\Models\Hari::getWeekdayNames() as $hari)<option value="{{ $hari }}">{{ $hari }}</option>@endforeach</select></div>
                <div><label>Jam Ke-</label><select id="form-jadwal-jam" class="filter-select" disabled required style="width:100%;margin-top:4px;background:#f1f5f9;color:#64748b;cursor:not-allowed">@foreach($allJamPelajaran as $jamItem)<option value="{{ $jamItem->id_jam }}">{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }} ({{ substr($jamItem->jam_mulai, 0, 5) }} - {{ substr($jamItem->jam_selesai, 0, 5) }})</option>@endforeach</select></div>
                <div><label>Kelas</label><select id="form-jadwal-kelas" class="filter-select" required style="width:100%;margin-top:4px">@foreach($allKelas as $kelasItem)<option value="{{ $kelasItem->id_kelas }}">{{ $kelasItem->nama_kelas }}</option>@endforeach</select></div>
                <div><label>Guru Pengampu</label><select id="form-jadwal-guru" class="filter-select" required style="width:100%;margin-top:4px"><option value="">-- Pilih Guru Pengampu --</option>@foreach($allGuru as $guruItem)<option value="{{ $guruItem->id_guru }}">{{ $guruItem->nama_guru }}</option>@endforeach</select></div>
                <div><label>Mata Pelajaran</label><select id="form-jadwal-mapel" class="filter-select" required style="width:100%;margin-top:4px">@foreach($allMapel as $mapelItem)<option value="{{ $mapelItem->id_mapel }}">{{ $mapelItem->nama_mapel }}</option>@endforeach</select></div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px"><button type="button" class="btn-secondary" onclick="tutupFormJadwal()">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
        </form>
    </div>
</div>

{{-- ── Modal Daftar Jadwal Kosong ── --}}
<div id="modal-jadwal-kosong" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:220;align-items:center;justify-content:center;padding:20px">
    <div class="card" style="width:100%;max-width:860px;max-height:85vh;display:flex;flex-direction:column;padding:24px;border-radius:var(--radius);box-shadow:0 12px 36px rgba(0,0,0,0.2)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #e2e8f0">
            <div>
                <h3 style="font-size:18px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:8px">
                    <span style="width:10px;height:10px;background:#f59e0b;border-radius:50%;display:inline-block"></span>
                    Daftar Jadwal Mengajar Kosong (Tanpa Guru)
                </h3>
                <p style="font-size:13px;color:#64748b;margin-top:3px">
                    Berikut adalah jadwal pelajaran yang belum memiliki guru pengampu. Klik tombol <strong>Tugaskan Guru</strong> untuk memilih guru yang jam mengajarnya sedang kosong.
                </p>
            </div>
            <button type="button" onclick="tutupModalJadwalKosong()" aria-label="Tutup" style="border:0;background:none;font-size:24px;color:#64748b;cursor:pointer;line-height:1">&times;</button>
        </div>

        <div style="overflow-y:auto;flex:1;padding-right:4px">
            <table class="data-table" style="width:100%">
                <thead>
                    <tr style="background:#f8fafc">
                        <th style="width:40px">No</th>
                        <th>Hari</th>
                        <th>Jam Ke- & Waktu</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalKosongList as $kIdx => $kItem)
                    <tr>
                        <td style="color:#94a3b8;font-weight:600">{{ $kIdx + 1 }}</td>
                        <td><strong>{{ $kItem->hari }}</strong></td>
                        <td>
                            <span class="badge badge-info" style="font-size:11.5px">Jam ke-{{ $kItem->jam_ke >= 100 ? $kItem->jam_ke - 100 : $kItem->jam_ke }}</span>
                            <span style="font-size:12px;color:#64748b;display:block;margin-top:2px">{{ substr($kItem->jam_mulai, 0, 5) }} - {{ substr($kItem->jam_selesai, 0, 5) }}</span>
                        </td>
                        <td><strong>{{ $kItem->nama_kelas }}</strong></td>
                        <td>{{ $kItem->nama_mapel }}</td>
                        <td style="text-align:center">
                            <button type="button"
                                class="btn-primary"
                                onclick="pilihGuruJadwalKosong({{ $kItem->id_jadwal }}, '{{ $kItem->hari }}', {{ $kItem->jam_ke }}, {{ $kItem->id_jam }}, '{{ htmlspecialchars($kItem->nama_kelas) }}', '{{ htmlspecialchars($kItem->nama_mapel) }}')"
                                style="padding:6px 14px;font-size:12px;background:#d97706;border-color:#b45309;border-radius:6px;font-weight:700">
                                Tugaskan Guru
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:30px;color:#64748b">
                            Semua jadwal mengajar telah memiliki guru pengampu.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #e2e8f0">
            <button type="button" class="btn-secondary" onclick="tutupModalJadwalKosong()">Tutup</button>
        </div>
    </div>
</div>

<script>
function filterJadwal() {
    const keyword = document.getElementById('jadwal-cari').value.trim().toLowerCase();
    const hari = document.getElementById('jadwal-hari').value;
    const kelas = document.getElementById('jadwal-kelas').value;
    const status = document.getElementById('jadwal-filter-status').value;
    let visible = 0;

    document.querySelectorAll('.jadwal-row').forEach(row => {
        const matches = row.dataset.search.includes(keyword) &&
            (!hari || row.dataset.hari === hari) &&
            (!kelas || row.dataset.kelas === kelas) &&
            (!status || row.dataset.status === status);
        row.style.display = matches ? '' : 'none';
        if (matches) visible++;
    });

    document.getElementById('jadwal-count').textContent = `${visible} jadwal`;
    document.getElementById('jadwal-no-result').style.display = visible ? 'none' : 'block';
}

function bukaModalJadwalKosong() {
    document.getElementById('modal-jadwal-kosong').style.display = 'flex';
}

function tutupModalJadwalKosong() {
    document.getElementById('modal-jadwal-kosong').style.display = 'none';
}

function bukaFormJadwal() {
    document.getElementById('jadwal-id').value = '';
    document.getElementById('jadwal-modal-title').textContent = 'Tambah Jadwal';
    document.getElementById('jadwal-modal').style.display = 'flex';
}

function tutupFormJadwal() {
    document.getElementById('jadwal-modal').style.display = 'none';
}

function editJadwal(jadwal) {
    document.getElementById('jadwal-id').value = jadwal.id_jadwal;
    document.getElementById('form-jadwal-hari').value = jadwal.hari;
    document.getElementById('form-jadwal-kelas').value = jadwal.id_kelas;
    document.getElementById('form-jadwal-guru').value = jadwal.id_guru || '';
    document.getElementById('form-jadwal-mapel').value = jadwal.id_mapel;
    document.getElementById('form-jadwal-jam').value = jadwal.id_jam;
    document.getElementById('jadwal-modal-title').textContent = 'Edit Jadwal Mengajar';
    document.getElementById('jadwal-modal').style.display = 'flex';
}

function simpanJadwal(event) {
    event.preventDefault();
    const id = document.getElementById('jadwal-id').value;
    const data = new FormData();
    data.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    data.append('hari', document.getElementById('form-jadwal-hari').value);
    data.append('id_kelas', document.getElementById('form-jadwal-kelas').value);
    data.append('id_guru', document.getElementById('form-jadwal-guru').value);
    data.append('id_mapel', document.getElementById('form-jadwal-mapel').value);
    data.append('id_jam', document.getElementById('form-jadwal-jam').value);
    if (!id) data.append('id_tahun_ajaran', @json($tahunAjaran->id_tahun_ajaran ?? 1));

    const url = id ? `{{ url('/jadwal') }}/${id}/update` : @json(route('jadwal.tambah'));
    fetch(url, {method: 'POST', headers: {'Accept': 'application/json'}, body: data})
        .then(async response => { const result = await response.json(); if (!response.ok) throw new Error(result.message || 'Jadwal gagal disimpan.'); return result; })
        .then(result => Swal.fire({icon: 'success', title: 'Tersimpan', text: result.message, confirmButtonColor: '#2563eb'}).then(() => location.reload()))
        .catch(error => Swal.fire({icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626'}));
}

function pilihGuruJadwalKosong(idJadwal, hari, jamKe, idJam, kelasNama, mapelNama) {
    Swal.fire({
        title: 'Memeriksa Jadwal Guru...',
        text: 'Mengambil daftar guru yang jam mengajarnya sedang kosong...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/jadwal/guru-tersedia?hari=${encodeURIComponent(hari)}&id_jam=${idJam}&id_jadwal=${idJadwal}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Gagal memeriksa ketersediaan guru');
        return data;
    })
    .then(data => {
        const tersedia = data.guru_tersedia || [];
        const bentrok = data.guru_bentrok || [];

        let opsiTersediaHtml = tersedia.map(g =>
            `<option value="${g.id_guru}">[OK] ${g.nama_guru} (${g.peran}) - [Jam Kosong]</option>`
        ).join('');

        let opsiBentrokHtml = bentrok.map(g =>
            `<option value="${g.id_guru}" disabled style="color:#94a3b8">[!] ${g.nama_guru} - [${g.keterangan}]</option>`
        ).join('');

        let selectHtml = `
            <select id="swal-pilih-guru-kosong" class="swal-form-select" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13.5px">
                <option value="">-- Pilih Guru yang Sedang Kosong --</option>
                ${tersedia.length > 0 ? `<optgroup label="GURU TERSEDIA (JAM MENGAJAR KOSONG)">${opsiTersediaHtml}</optgroup>` : ''}
                ${bentrok.length > 0 ? `<optgroup label="GURU SEDANG MENGAJAR DI KELAS LAIN (BENTROK)">${opsiBentrokHtml}</optgroup>` : ''}
            </select>
        `;

        Swal.fire({
            title: 'Tugaskan Guru Pengampu',
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                confirmButton: 'custom-swal-confirm',
                cancelButton: 'custom-swal-cancel'
            },
            buttonsStyling: false,
            html: `
                <div style="text-align:left;font-family:inherit">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px;margin-bottom:14px;font-size:13px">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                            <span style="color:#64748b">Hari & Jam:</span>
                            <strong>${hari}, Jam ke-${jamKe}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                            <span style="color:#64748b">Kelas:</span>
                            <strong>${kelasNama}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:#64748b">Mata Pelajaran:</span>
                            <strong>${mapelNama}</strong>
                        </div>
                    </div>

                    <div style="margin-bottom:12px">
                        <label for="swal-pilih-guru-kosong" style="display:block;font-size:12.5px;font-weight:700;color:#334155;margin-bottom:6px">
                            Pilih Guru Pengampu (Tersedia ${tersedia.length} Guru):
                        </label>
                        ${selectHtml}
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Tugaskan Guru Sekarang',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const idGuru = document.getElementById('swal-pilih-guru-kosong')?.value;
                if (!idGuru) {
                    Swal.showValidationMessage('Silakan pilih salah satu guru yang tersedia');
                    return false;
                }
                return { id_guru: idGuru };
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                fetch(`/jadwal/${idJadwal}/tugaskan-guru`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                .then(async res => {
                    const resData = await res.json();
                    if (!res.ok || resData.status === 'error') throw new Error(resData.message || 'Gagal menugaskan guru');
                    return resData;
                })
                .then(resData => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Guru Berhasil Ditugaskan',
                        text: resData.message,
                        customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                        buttonsStyling: false
                    }).then(() => location.reload());
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
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: err.message || 'Terjadi kesalahan saat memeriksa jadwal guru.',
            customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
            buttonsStyling: false
        });
    });
}

function hapusSemuaJadwal() {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
        icon: 'warning',
        title: 'Hapus semua jadwal?',
        text: 'Semua jadwal aktif pada tahun ajaran ini akan dihapus sementara (soft delete).',
        showCancelButton: true,
        confirmButtonText: 'Ya, lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({
            icon: 'error',
            title: 'Konfirmasi terakhir',
            text: 'Tindakan ini akan menghapus semua jadwal aktif. Anda yakin?',
            input: 'text',
            inputPlaceholder: 'Ketik HAPUS untuk melanjutkan',
            showCancelButton: true,
            confirmButtonText: 'HAPUS SEMUA',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#991b1b',
            preConfirm: value => value === 'HAPUS' ? true : Swal.showValidationMessage('Ketik HAPUS dengan benar')
        }).then(confirm => {
            if (!confirm.isConfirmed) return;
            fetch('{{ route('jadwal.hapus-semua') }}', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }
            }).then(async response => {
                const data = await response.json();
                if (!response.ok || data.status !== 'success') throw new Error(data.message || 'Gagal menghapus jadwal.');
                return data;
            }).then(data => Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, confirmButtonColor: '#2563eb' }).then(() => location.reload()))
              .catch(error => Swal.fire({ icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626' }));
        });
    });
}

function bukaImportJadwalModal() {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
        title: 'Upload Jadwal Mengajar',
        html: `<div id="jadwal-drop-zone" style="border:2px dashed #93c5fd;border-radius:12px;padding:28px 18px;background:#f8fbff;text-align:center;cursor:pointer;transition:background .2s,border-color .2s"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.7" style="margin-bottom:8px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg><div style="font-size:13.5px;font-weight:700;color:#1e293b">Tarik file ke sini</div><div style="font-size:12px;color:#64748b;margin:5px 0 12px">atau pilih file dari perangkat</div><button type="button" id="jadwal-choose-file" class="jam-popup-confirm">Pilih File</button><div id="jadwal-file-name" style="font-size:12px;color:#2563eb;font-weight:600;margin-top:12px">Belum ada file dipilih</div></div><input id="jadwal-import-file" type="file" accept=".csv,.txt,.xlsx,.xls" style="display:none"><div style="font-size:11.5px;color:#64748b;margin-top:12px;line-height:1.6"><div style="font-weight:700;color:#334155;margin-bottom:4px">Format kolom yang didukung:</div><div style="display:flex;gap:5px;flex-wrap:wrap;justify-content:center"><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">Kelas</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">Hari</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">Jam (contoh: 1, 2-3, 4-5-6)</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px">MataPelajaran</span><span style="background:#f1f5f9;padding:2px 7px;border-radius:6px;font-weight:600;font-size:11px;color:#94a3b8">Guru (opsional)</span></div><div style="margin-top:4px;color:#94a3b8;font-size:10.5px">Kolom WaktuMulai, WaktuSelesai, Kind, dan Ruang otomatis diabaikan. Jam mengajar mengikuti master jam admin. Maks 25MB.</div></div>`,
        showCancelButton: true,
        confirmButtonText: 'Upload Sekarang',
        cancelButtonText: 'Batal',
        customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm', cancelButton: 'custom-swal-cancel' },
        buttonsStyling: false,
        didOpen: () => {
            const drop = document.getElementById('jadwal-drop-zone');
            const input = document.getElementById('jadwal-import-file');
            const name = document.getElementById('jadwal-file-name');
            document.getElementById('jadwal-choose-file').onclick = (e) => { e.stopPropagation(); input.click(); };
            drop.onclick = () => input.click();
            input.onchange = () => { if (input.files.length) name.textContent = input.files[0].name; };
            ['dragover', 'dragenter'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.style.borderColor = '#2563eb'; drop.style.background = '#eef4ff'; }));
            ['dragleave', 'drop'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.style.borderColor = '#93c5fd'; drop.style.background = '#f8fbff'; }));
            drop.addEventListener('drop', e => { if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; name.textContent = e.dataTransfer.files[0].name; } });
        },
        preConfirm: () => {
            const input = document.getElementById('jadwal-import-file');
            if (!input.files.length) { Swal.showValidationMessage('Pilih file terlebih dahulu'); return false; }
            return input.files[0];
        }
    }).then(result => {
        if (!result.isConfirmed || !result.value) return;
        const formData = new FormData();
        formData.append('file_jadwal', result.value);
        uploadJadwalFile(formData);
    });
}

function uploadJadwalFile(formData) {
    fetch('/jadwal/import', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok || data.status === 'error') {
            const err = new Error(data.message || 'Import gagal.');
            err.skipped = data.skipped || [];
            throw err;
        }
        return data;
    })
    .then(data => {
        const detail = (data.skipped && data.skipped.length)
            ? '<br><br><div style="font-size:11.5px;text-align:left;max-height:120px;overflow:auto;background:#f8fafc;padding:8px;border-radius:8px"><b>Baris yang dilewati:</b><br>' + data.skipped.map(s => '• ' + s).join('<br>') + '</div>'
            : '';
        Swal.fire({
            icon: 'success',
            title: 'Import Berhasil',
            text: data.message,
            html: data.message + detail,
            confirmButtonColor: '#2563eb'
        }).then(() => location.reload());
    })
    .catch(err => {
        const detail = (err.skipped && err.skipped.length)
            ? '<br><br><div style="font-size:11.5px;text-align:left;max-height:120px;overflow:auto;background:#f8fafc;padding:8px;border-radius:8px"><b>Baris yang dilewati:</b><br>' + err.skipped.map(s => '• ' + s).join('<br>') + '</div>'
            : '';
        Swal.fire({ icon: 'error', title: 'Import Gagal', text: err.message || 'Terjadi kesalahan sistem.', html: (err.message || 'Terjadi kesalahan sistem.') + detail, confirmButtonColor: '#dc2626' });
    });
}

document.addEventListener('DOMContentLoaded', filterJadwal);
</script>

