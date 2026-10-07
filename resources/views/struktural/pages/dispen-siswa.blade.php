<style>
.dispen-siswa-dropdown {
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
.dispen-siswa-dropdown-item {
    border-bottom: 1px solid #f1f5f9 !important;
    color: #1e293b !important;
    background: transparent;
    transition: background 0.15s ease;
}
.dispen-siswa-dropdown-item:hover {
    background: #f1f5f9 !important;
}
.dispen-siswa-name {
    font-weight: 600;
    color: #0f172a;
}
.dispen-siswa-sub {
    font-size: 11.5px;
    color: #64748b;
}
.dispen-siswa-empty {
    color: #94a3b8;
}
.btn-hapus-dispen-row {
    border: 1px solid #fecaca;
    background: #fee2e2;
    color: #ef4444;
}
.btn-hapus-dispen-row:hover {
    background: #fecaca;
}
.btn-tambah-dispen-siswa {
    color: #2563eb;
    background: #eff6ff;
    border: 1px dashed #bfdbfe;
}
.btn-tambah-dispen-siswa:hover {
    background: #dbeafe;
}

/* ── Mode Gelap (Dark Theme) ── */
[data-theme="dark"] .dispen-siswa-dropdown {
    background: #1e293b !important;
    color: #f8fafc !important;
    border-color: #334155 !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
}
[data-theme="dark"] .dispen-siswa-dropdown-item {
    border-bottom-color: #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .dispen-siswa-dropdown-item:hover {
    background: #334155 !important;
}
[data-theme="dark"] .dispen-siswa-name {
    color: #f8fafc !important;
}
[data-theme="dark"] .dispen-siswa-sub {
    color: #94a3b8 !important;
}
[data-theme="dark"] .dispen-siswa-empty {
    color: #94a3b8 !important;
}
[data-theme="dark"] .btn-hapus-dispen-row {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #fca5a5 !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
}
[data-theme="dark"] .btn-hapus-dispen-row:hover {
    background: rgba(239, 68, 68, 0.3) !important;
}
[data-theme="dark"] .btn-tambah-dispen-siswa {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #93c5fd !important;
    border-color: rgba(59, 130, 246, 0.35) !important;
}
[data-theme="dark"] .btn-tambah-dispen-siswa:hover {
    background: rgba(59, 130, 246, 0.25) !important;
}
</style>

<div class="page-content page-anim" id="page-dispen-siswa" style="display:none">
    <div class="page-header" style="margin-bottom:20px"><div><div class="page-title" style="font-size:22px;font-weight:800">Dispensasi Siswa</div><div class="page-subtitle">Buat dan pantau izin dispensasi siswa dengan persetujuan Waka dan Guru Piket.</div></div></div>
    <div class="card" style="padding:22px 24px;max-width:900px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Buat Permintaan Dispensasi</div></div>
        <form id="dispen-form" onsubmit="buatDispen(event)">@csrf
            <div style="display:grid;grid-template-columns:minmax(0, 1fr) minmax(140px, 180px);gap:14px;margin-bottom:14px;align-items:flex-start">
                <div style="min-width:0">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Pilih Siswa</label>
                    
                    {{-- Container baris siswa --}}
                    <div id="dispen-siswa-container" style="display:flex;flex-direction:column;gap:10px">
                        <div class="dispen-siswa-row" id="dispen-siswa-row-0" style="display:flex;align-items:center;gap:8px">
                            <div style="position:relative;flex:1;min-width:0">
                                <input type="hidden" class="dispen-siswa-id" name="id_siswa[]" id="dispen-siswa-id-0" required>
                                <input type="text" id="dispen-siswa-search-0" class="filter-input dispen-siswa-search" placeholder="Ketik nama atau kelas siswa..." autocomplete="off" required style="width:100%;box-sizing:border-box"
                                    onfocus="filterDispenSiswaRow(0, this.value)"
                                    onclick="filterDispenSiswaRow(0, this.value)"
                                    oninput="filterDispenSiswaRow(0, this.value)"
                                    onkeyup="filterDispenSiswaRow(0, this.value)">
                                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                <div id="dispen-siswa-dropdown-0" class="dispen-siswa-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:999">
                                    @foreach($siswaAktif as $siswa)
                                        <div class="dispen-siswa-dropdown-item" data-id="{{ $siswa->id_siswa }}" data-search="{{ strtolower($siswa->nama_siswa . ' ' . ($siswa->kelas->nama_kelas ?? '') . ' ' . ($siswa->nisn ?? '')) }}"
                                            onclick="pilihDispenSiswaRow(0, '{{ $siswa->id_siswa }}', '{{ addslashes($siswa->nama_siswa) }} - {{ addslashes($siswa->kelas->nama_kelas ?? '') }}')"
                                            style="padding:10px 14px;cursor:pointer;font-size:13px">
                                            <div class="dispen-siswa-name">{{ $siswa->nama_siswa }}</div>
                                            <div class="dispen-siswa-sub">{{ $siswa->kelas->nama_kelas ?? '-' }} • NISN: {{ $siswa->nisn ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                    <div class="dispen-siswa-empty" style="display:none;padding:14px;text-align:center;font-size:13px">Siswa tidak ditemukan</div>
                                </div>
                            </div>
                            <button type="button" class="btn-hapus-dispen-row" onclick="hapusBarisDispenSiswa(0)" title="Hapus siswa ini" style="display:none;border-radius:8px;width:38px;height:38px;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all 0.15s">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Tambah Siswa --}}
                    <div style="margin-top:10px">
                        <button type="button" class="btn-tambah-dispen-siswa" onclick="tambahBarisDispenSiswa()" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;cursor:pointer;transition:all 0.15s">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Siswa
                        </button>
                    </div>
                </div>
                <div style="min-width:0"><label for="dispen-tanggal" style="font-size:13px;font-weight:600">Tanggal</label><input id="dispen-tanggal" type="date" name="tanggal_dispen" value="{{ now()->toDateString() }}" class="filter-input" required style="width:100%;margin-top:6px;box-sizing:border-box"></div>
            </div>
            <input type="hidden" name="jenis_absen" value="D">
            <div style="margin-bottom:14px"><label for="dispen-alasan" style="font-size:13px;font-weight:600">Alasan dispensasi</label><textarea id="dispen-alasan" name="alasan" class="filter-input" rows="3" maxlength="2000" required placeholder="Contoh: mengikuti lomba, kegiatan sekolah, atau keperluan resmi lainnya..." style="width:100%;margin-top:4px;resize:vertical"></textarea></div>
            <div style="margin-bottom:14px"><label for="dispen-foto" style="font-size:13px;font-weight:600">Foto surat keterangan <small style="color:#94a3b8">(opsional)</small></label><input id="dispen-foto" type="file" name="foto_surat" accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;margin-top:6px;font-size:13px"><small style="display:block;color:#64748b;margin-top:5px">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</small></div>
            <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 16px;font-size:13px">Simpan &amp; Absen ke Jurnal</button>
        </form>
        <div id="dispen-links" style="display:none;margin-top:16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:14px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <strong style="font-size:12px;color:#1d4ed8">Link persetujuan Waka Kesiswaan</strong>
                <div id="dispen-wa-status-badge"></div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <input id="dispen-waka-link" class="filter-input" readonly style="flex:1;font-size:12px">
                <button type="button" class="btn-secondary" onclick="salinDispen('dispen-waka-link')">Salin</button>
                <a id="dispen-waka-wa-btn" href="#" target="_blank" class="btn-secondary" style="font-size:12px;display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;text-decoration:none">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Kirim WA
                </a>
            </div>
        </div>
    </div>
    <div class="card" style="padding:22px 24px;margin-top:20px">
        <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div class="card-title">Status Dispensasi</div>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-dispen-status" oninput="filterTable('search-dispen-status','table-dispen-status')" onkeyup="filterTable('search-dispen-status','table-dispen-status')" placeholder="Cari nama, kelas, alasan..." class="filter-input" style="padding:7px 12px 7px 32px;font-size:12.5px;border-radius:8px;border:1px solid #cbd5e1;width:220px">
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" id="table-dispen-status" style="min-width:950px">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Surat</th>
                        <th>Status Waka</th>
                        <th>Status Keluar-Masuk</th>
                        <th style="text-align:center">Tanda Tangan</th>
                        <th>Link Waka</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispenTerbaru as $item)
                        <tr>
                            <td><strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong></td>
                            <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $item->tanggal_dispen->format('d-m-Y') }}</td>
                            <td>{{ $item->alasan }}</td>
                            <td>
                                @if($item->foto_surat)
                                    <a href="{{ Storage::disk('public')->url($item->foto_surat) }}" target="_blank" rel="noopener">Lihat foto</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ ucfirst($item->status_waka) }}</td>
                            <td>
                                @if($item->waktu_masuk)
                                    <span class="badge badge-success">Sudah Kembali</span>
                                @elseif($item->waktu_keluar)
                                    <span class="badge badge-warning">Di Luar Sekolah</span>
                                @else
                                    <span class="badge badge-info">Di Sekolah</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                @if($item->tanda_tangan_waka)
                                    <button type="button" onclick="showSignaturePopup('{{ Storage::disk('public')->url($item->tanda_tangan_waka) }}', 'Dispensasi - {{ $item->siswa->nama_siswa ?? '' }}', 'Tanda Tangan Waka Kesiswaan')" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;padding:4px 8px;font-size:11.5px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5a1 1 0 0 1-1.4.4l-4-2a1 1 0 0 1 .3-1.8l10-3z"/><path d="M2 19l7-7 2 2 5-5 6 6v4a1 1 0 0 1-1 1l-9 2-4 2z"/></svg>
                                        Lihat TTD
                                    </button>
                                @else
                                    <span style="color:#94a3b8;font-size:12px">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ URL::temporarySignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $item->id_dispen_siswa, 'role' => 'waka'], false) }}" target="_blank">
                                    Buka Waka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;color:#64748b;padding:22px">
                                Belum ada permintaan dispensasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
    $masterSiswaDispenList = $siswaAktif->map(function($s) {
        return [
            'id' => $s->id_siswa,
            'nama' => $s->nama_siswa,
            'kelas' => $s->kelas->nama_kelas ?? '-',
            'nisn' => $s->nisn ?? '-',
            'search' => strtolower($s->nama_siswa . ' ' . ($s->kelas->nama_kelas ?? '') . ' ' . ($s->nisn ?? '')),
        ];
    })->values();
@endphp

<script>
window.masterSiswaDispenList = {!! json_encode($masterSiswaDispenList) !!};

let dispenRowIndex = 1;

function updateDispenHapusButtons() {
    const rows = document.querySelectorAll('.dispen-siswa-row');
    rows.forEach(row => {
        const btnHapus = row.querySelector('.btn-hapus-dispen-row');
        if (btnHapus) {
            btnHapus.style.display = rows.length > 1 ? 'inline-flex' : 'none';
        }
    });
}

function tambahBarisDispenSiswa() {
    const container = document.getElementById('dispen-siswa-container');
    if (!container) return;

    const rowId = dispenRowIndex++;
    const row = document.createElement('div');
    row.className = 'dispen-siswa-row';
    row.id = 'dispen-siswa-row-' + rowId;
    row.style.cssText = 'display:flex;align-items:center;gap:8px';

    let optionsHtml = '';
    (window.masterSiswaDispenList || []).forEach(s => {
        const escapedNama = s.nama.replace(/'/g, "\\'");
        const escapedKelas = s.kelas.replace(/'/g, "\\'");
        optionsHtml += `
            <div class="dispen-siswa-dropdown-item" data-id="${s.id}" data-search="${s.search}"
                onclick="pilihDispenSiswaRow(${rowId}, '${s.id}', '${escapedNama} - ${escapedKelas}')"
                style="padding:10px 14px;cursor:pointer;font-size:13px">
                <div class="dispen-siswa-name">${s.nama}</div>
                <div class="dispen-siswa-sub">${s.kelas} • NISN: ${s.nisn}</div>
            </div>
        `;
    });

    row.innerHTML = `
        <div style="position:relative;flex:1;min-width:0">
            <input type="hidden" class="dispen-siswa-id" name="id_siswa[]" id="dispen-siswa-id-${rowId}" required>
            <input type="text" id="dispen-siswa-search-${rowId}" class="filter-input dispen-siswa-search" placeholder="Ketik nama atau kelas siswa..." autocomplete="off" required style="width:100%;box-sizing:border-box"
                onfocus="filterDispenSiswaRow(${rowId}, this.value)"
                onclick="filterDispenSiswaRow(${rowId}, this.value)"
                oninput="filterDispenSiswaRow(${rowId}, this.value)"
                onkeyup="filterDispenSiswaRow(${rowId}, this.value)">
            <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            <div id="dispen-siswa-dropdown-${rowId}" class="dispen-siswa-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:999">
                ${optionsHtml}
                <div class="dispen-siswa-empty" style="display:none;padding:14px;text-align:center;font-size:13px">Siswa tidak ditemukan</div>
            </div>
        </div>
        <button type="button" class="btn-hapus-dispen-row" onclick="hapusBarisDispenSiswa(${rowId})" title="Hapus siswa ini" style="display:inline-flex;border-radius:8px;width:38px;height:38px;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all 0.15s">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    `;

    container.appendChild(row);
    updateDispenHapusButtons();

    const newSearchInput = document.getElementById('dispen-siswa-search-' + rowId);
    if (newSearchInput) newSearchInput.focus();
}

function hapusBarisDispenSiswa(rowId) {
    const row = document.getElementById('dispen-siswa-row-' + rowId);
    if (row) {
        row.remove();
        updateDispenHapusButtons();
    }
}

function toggleDispenSiswaDropdownRow(rowId, show) {
    const dd = document.getElementById('dispen-siswa-dropdown-' + rowId);
    if (dd) dd.style.display = show ? 'block' : 'none';
}

function filterDispenSiswaRow(rowId, keyword) {
    // Tutup dropdown lain terlebih dahulu
    document.querySelectorAll('.dispen-siswa-dropdown').forEach(dd => {
        if (dd.id !== 'dispen-siswa-dropdown-' + rowId) {
            dd.style.display = 'none';
        }
    });

    const dd = document.getElementById('dispen-siswa-dropdown-' + rowId);
    if (!dd) return;

    const items = dd.querySelectorAll('.dispen-siswa-dropdown-item');
    const empty = dd.querySelector('.dispen-siswa-empty');
    const q = (keyword || '').toLowerCase().trim();
    let found = 0;

    items.forEach(function(item) {
        const searchStr = (item.getAttribute('data-search') || item.dataset.search || '').toLowerCase();
        const match = !q || searchStr.includes(q);
        item.style.display = match ? 'block' : 'none';
        if (match) found++;
    });

    if (empty) empty.style.display = found === 0 ? 'block' : 'none';
    dd.style.display = 'block';
}

function pilihDispenSiswaRow(rowId, id, label) {
    // Cek apakah siswa ini sudah dipilih di baris lain
    const otherInputs = document.querySelectorAll('.dispen-siswa-id');
    let isDuplicate = false;
    otherInputs.forEach(input => {
        if (input.id !== 'dispen-siswa-id-' + rowId && input.value === String(id)) {
            isDuplicate = true;
        }
    });

    if (isDuplicate) {
        Swal.fire({
            icon: 'warning',
            title: 'Siswa Sudah Dipilih',
            text: 'Siswa tersebut sudah ada di daftar dispensasi.',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    const idInput = document.getElementById('dispen-siswa-id-' + rowId);
    const searchInput = document.getElementById('dispen-siswa-search-' + rowId);
    if (idInput) idInput.value = id;
    if (searchInput) searchInput.value = label;

    toggleDispenSiswaDropdownRow(rowId, false);
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('.dispen-siswa-row').forEach(row => {
        if (!row.contains(e.target)) {
            const dd = row.querySelector('.dispen-siswa-dropdown');
            if (dd) dd.style.display = 'none';
        }
    });
});

function buatDispen(event) {
    event.preventDefault();

    // Validasi apakah setidaknya 1 siswa dipilih
    const idInputs = document.querySelectorAll('.dispen-siswa-id');
    let selectedCount = 0;
    idInputs.forEach(inp => {
        if (inp.value && inp.value.trim() !== '') selectedCount++;
    });

    if (selectedCount === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Siswa',
            text: 'Silakan pilih minimal 1 siswa untuk permohonan dispensasi.',
            confirmButtonColor: '#ea580c'
        });
        return;
    }

    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Menyimpan...';
    }

    fetch(@json(route('dispen-siswa.store')), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: new FormData(document.getElementById('dispen-form'))
    })
    .then(async r => {
        const d = await r.json();
        if (!r.ok) throw new Error(d.message || 'Gagal membuat dispensasi.');
        return d;
    })
    .then(d => {
        document.getElementById('dispen-waka-link').value = d.waka_link;

        const waUrl = d.wa_notification?.wa_me_link || ('https://wa.me/?text=' + encodeURIComponent(d.waka_link));
        const btnWa = document.getElementById('dispen-waka-wa-btn');
        if (btnWa) btnWa.href = waUrl;

        const statusBadge = document.getElementById('dispen-wa-status-badge');
        if (statusBadge) {
            if (d.wa_notification?.sent) {
                statusBadge.innerHTML = '<span class="badge badge-success" style="font-size:11px;padding:3px 8px">● WA Terkirim ke Waka Kesiswaan</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge badge-secondary" style="font-size:11px;padding:3px 8px">WA Belum Terkirim Otomatis</span>';
            }
        }

        document.getElementById('dispen-links').style.display = 'block';
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Dibuat!',
            text: d.message,
            confirmButtonColor: '#ea580c'
        });
    })
    .catch(e => Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: e.message,
        confirmButtonColor: '#dc2626'
    }))
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

function salinDispen(id) {
    navigator.clipboard.writeText(document.getElementById(id).value)
        .then(() => Swal.fire({ icon: 'success', title: 'Link disalin', timer: 1200, showConfirmButton: false }));
}
</script>
