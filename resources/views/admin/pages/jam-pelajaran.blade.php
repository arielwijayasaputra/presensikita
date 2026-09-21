<div class="page-content page-anim" id="page-jam-pelajaran" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Jam Pelajaran</div>
            <div class="page-subtitle">Atur waktu nyata untuk setiap nomor jam pelajaran yang dipakai jadwal.</div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px;max-width:850px">
        <div id="jam-pelajaran-form">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px" role="tablist" aria-label="Hari jam pelajaran">
                <button type="button" class="btn-primary jam-day-tab active" data-day="Senin" onclick="pilihHariJam('Senin', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Senin</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="Selasa" onclick="pilihHariJam('Selasa', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Selasa</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="Rabu" onclick="pilihHariJam('Rabu', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Rabu</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="Kamis" onclick="pilihHariJam('Kamis', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Kamis</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="Jumat" onclick="pilihHariJam('Jumat', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Jumat</button>
            </div>
            <div style="overflow-x:auto">
                <table class="data-table" style="min-width:520px">
                    <thead><tr><th>Jam Ke-</th><th>Mulai</th><th>Selesai</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @php $shownBreak = []; @endphp
                        @forelse($allJamPelajaran as $jamItem)
                            @if((int) $jamItem->jam_ke === 5 && !in_array('ist1_weekday', $shownBreak))
                                @php $shownBreak[] = 'ist1_weekday'; @endphp
                                <tr class="jam-row jam-break-row" data-day="all-weekday" style="background:#fff7ed">
                                    <td><strong>Istirahat 1</strong></td>
                                    <td>{{ $istirahat1Mulai ?? '09:40' }}</td>
                                    <td>{{ $istirahat1Selesai ?? '10:00' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 1" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'weekday', 1, 'Istirahat 1', '{{ $istirahat1Mulai ?? '09:40' }}', '{{ $istirahat1Selesai ?? '10:00' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @elseif((int) $jamItem->jam_ke === 8 && !in_array('ist2_weekday', $shownBreak))
                                @php $shownBreak[] = 'ist2_weekday'; @endphp
                                <tr class="jam-row jam-break-row" data-day="all-weekday" style="background:#fff7ed">
                                    <td><strong>Istirahat 2</strong></td>
                                    <td>{{ $istirahat2Mulai ?? '12:00' }}</td>
                                    <td>{{ $istirahat2Selesai ?? '13:00' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 2" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'weekday', 2, 'Istirahat 2', '{{ $istirahat2Mulai ?? '12:00' }}', '{{ $istirahat2Selesai ?? '13:00' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @elseif((int) $jamItem->jam_ke === 106 && !in_array('ist1_jumat', $shownBreak))
                                @php $shownBreak[] = 'ist1_jumat'; @endphp
                                <tr class="jam-row jam-break-row" data-day="Jumat" style="background:#fff7ed">
                                    <td><strong>Istirahat 1</strong></td>
                                    <td>{{ $istirahatJumat1Mulai ?? '09:00' }}</td>
                                    <td>{{ $istirahatJumat1Selesai ?? '09:50' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 1 Jumat" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'friday', 1, 'Istirahat 1 Jumat', '{{ $istirahatJumat1Mulai ?? '09:00' }}', '{{ $istirahatJumat1Selesai ?? '09:50' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @elseif((int) $jamItem->jam_ke === 109 && !in_array('ist2_jumat', $shownBreak))
                                @php $shownBreak[] = 'ist2_jumat'; @endphp
                                <tr class="jam-row jam-break-row" data-day="Jumat" style="background:#fff7ed">
                                    <td><strong>Istirahat 2</strong></td>
                                    <td>{{ $istirahatJumat2Mulai ?? '11:20' }}</td>
                                    <td>{{ $istirahatJumat2Selesai ?? '13:00' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 2 Jumat" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'friday', 2, 'Istirahat 2 Jumat', '{{ $istirahatJumat2Mulai ?? '11:20' }}', '{{ $istirahatJumat2Selesai ?? '13:00' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @endif
                            <tr class="jam-row" data-day="{{ $jamItem->hari ?? ($jamItem->jam_ke >= 100 ? 'Jumat' : 'Senin') }}">
                                <td><strong>Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}</strong></td>
                                <td>{{ substr($jamItem->jam_mulai, 0, 5) }}</td>
                                <td>{{ substr($jamItem->jam_selesai, 0, 5) }}</td>
                                  <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}" title="Ubah waktu" onclick="ubahWaktuJam('jam', {{ $jamItem->id_jam }}, 'Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}', '{{ substr($jamItem->jam_mulai, 0, 5) }}', '{{ substr($jamItem->jam_selesai, 0, 5) }}', '{{ $jamItem->hari ?? ($jamItem->jam_ke >= 100 ? 'Jumat' : 'Senin') }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1-1 4Z"/></svg></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center;color:#64748b;padding:24px">Belum ada data jam pelajaran.</td></tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            <div style="margin-top:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <div style="font-size:12px;color:#94a3b8">Klik Ubah pada baris yang ingin disesuaikan.</div>
                <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;color:#15803d;font-size:11.5px;font-weight:600">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Jam Jumat dikelola di halaman Pengaturan Jumat
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.jam-edit-btn { width:34px; height:34px; padding:0; border:1px solid #bfdbfe; border-radius:8px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:all .15s ease; }
.jam-edit-btn:hover { background:#dbeafe; color:#1d4ed8; transform:translateY(-1px); }

/* Jam Pelajaran Modal Enhancements */
.jam-popup-time-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}
.jam-popup-field {
    text-align: left;
    margin-bottom: 14px;
}
.jam-popup-field label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
}
.jam-popup-field input[type="time"] {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 12px;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    color: #0f172a;
    font-size: 14.5px;
    font-weight: 700;
    font-family: inherit;
    background: #f8fafc;
    transition: all .2s ease;
}
.jam-popup-field input[type="time"]:focus {
    outline: none;
    background: #fff;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
}
.jam-days-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.jam-days-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    text-align: left;
}
.jam-day-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 4px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    cursor: pointer;
    transition: all .18s ease;
    user-select: none;
    position: relative;
}
.jam-day-card:hover {
    border-color: #93c5fd;
    background: #eff6ff;
    transform: translateY(-1px);
}
.jam-day-card input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    pointer-events: none;
}
.jam-day-card .jam-day-check-indicator {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 1.5px solid #cbd5e1;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    transition: all .18s ease;
}
.jam-day-card.selected,
.jam-day-card:has(input:checked) {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 2px 6px rgba(37,99,235,0.12);
}
.jam-day-card.selected .jam-day-check-indicator,
.jam-day-card:has(input:checked) .jam-day-check-indicator {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}
.jam-day-card .jam-day-name {
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    white-space: nowrap;
}
.jam-day-card.selected .jam-day-name,
.jam-day-card:has(input:checked) .jam-day-name {
    color: #1e40af;
}
.jam-day-friday-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 10px;
    color: #15803d;
    text-align: left;
}
.jam-popup-confirm {
    border: 0;
    border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    transition: all .15s ease;
}
.jam-popup-confirm:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-1px);
}
.jam-popup-cancel {
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    background: #fff;
    color: #475569;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
}
.jam-popup-cancel:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

/* Dark mode overrides */
[data-theme="dark"] .jam-break-row { background: rgba(234,88,12,0.1) !important; }
[data-theme="dark"] .jam-break-row td { color: #fb923c !important; }
[data-theme="dark"] .jam-break-row td strong { color: #fb923c !important; }
[data-theme="dark"] .jam-edit-btn {
    background: rgba(37,99,235,0.15) !important;
    border-color: rgba(37,99,235,0.3) !important;
    color: #60a5fa !important;
}
[data-theme="dark"] .jam-edit-btn:hover {
    background: rgba(37,99,235,0.25) !important;
    color: #93c5fd !important;
}
[data-theme="dark"] .jam-popup-field label { color: #94a3b8 !important; }
[data-theme="dark"] .jam-popup-field input[type="time"] {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}
[data-theme="dark"] .jam-popup-field input[type="time"]:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.25) !important;
}
[data-theme="dark"] .jam-day-card {
    background: #1e293b !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .jam-day-card .jam-day-name { color: #cbd5e1 !important; }
[data-theme="dark"] .jam-day-card .jam-day-check-indicator {
    background: #0f172a !important;
    border-color: #475569 !important;
}
[data-theme="dark"] .jam-day-card.selected,
[data-theme="dark"] .jam-day-card:has(input:checked) {
    background: rgba(37,99,235,0.2) !important;
    border-color: #3b82f6 !important;
}
[data-theme="dark"] .jam-day-card.selected .jam-day-name,
[data-theme="dark"] .jam-day-card:has(input:checked) .jam-day-name {
    color: #93c5fd !important;
}
[data-theme="dark"] .jam-day-card.selected .jam-day-check-indicator,
[data-theme="dark"] .jam-day-card:has(input:checked) .jam-day-check-indicator {
    background: #2563eb !important;
    border-color: #2563eb !important;
}
[data-theme="dark"] .jam-day-friday-badge {
    background: rgba(22,163,74,0.15) !important;
    border-color: rgba(34,197,94,0.3) !important;
    color: #4ade80 !important;
}
[data-theme="dark"] .jam-popup-cancel {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
</style>

<script>
function toggleAllDaysInPopup(btn) {
    const checkboxes = document.querySelectorAll('#popup-hari-pilihan input[type="checkbox"]');
    const anyUnchecked = Array.from(checkboxes).some(c => !c.checked);
    checkboxes.forEach(c => {
        c.checked = anyUnchecked;
        c.closest('.jam-day-card').classList.toggle('selected', anyUnchecked);
    });
    btn.textContent = anyUnchecked ? 'Batal Semua' : 'Pilih Semua';
}

function updateToggleAllDaysBtn() {
    const btn = document.querySelector('.btn-toggle-all-days');
    if (!btn) return;
    const checkboxes = document.querySelectorAll('#popup-hari-pilihan input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(c => c.checked);
    btn.textContent = allChecked ? 'Batal Semua' : 'Pilih Semua';
}

async function ubahWaktuJam(type, idOrDay, nomorOrLabel, labelOrMulai, mulaiOrSelesai, selesai, hariSumber = null) {
    const nomor = type === 'istirahat' ? nomorOrLabel : null;
    const label = type === 'istirahat' ? labelOrMulai : nomorOrLabel;
    const mulai = type === 'istirahat' ? mulaiOrSelesai : labelOrMulai;
    const selesaiAwal = type === 'istirahat' ? selesai : mulaiOrSelesai;
    const activeTabDay = document.querySelector('.jam-day-tab.active')?.dataset.day || 'Senin';
    const defaultHari = hariSumber || (idOrDay === 'friday' ? 'Jumat' : activeTabDay);
    const isFriday = defaultHari === 'Jumat' || idOrDay === 'friday';

    let hariFieldHtml = '';
    if (isFriday) {
        hariFieldHtml = `
            <div class="jam-popup-field" style="margin-bottom:0">
                <label>Terapkan ke hari</label>
                <div class="jam-day-friday-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>
                        <strong style="display:block;font-size:13px;line-height:1.2">Khusus Hari Jumat</strong>
                        <span style="font-size:11px;opacity:0.85">Jadwal & jam istirahat Jumat dikelola terpisah.</span>
                    </div>
                    <input type="checkbox" id="popup-chk-jumat" value="Jumat" checked style="display:none">
                </div>
            </div>
        `;
    } else {
        const weekdays = ['Senin', 'Selasa', 'Rabu', 'Kamis'];
        hariFieldHtml = `
            <div class="jam-popup-field" style="margin-bottom:0">
                <div class="jam-days-header">
                    <label style="margin-bottom:0">Terapkan ke hari</label>
                    <button type="button" class="btn-toggle-all-days" onclick="toggleAllDaysInPopup(this)" style="background:none;border:none;color:#2563eb;font-size:11.5px;font-weight:700;cursor:pointer;padding:0">Batal Semua</button>
                </div>
                <div id="popup-hari-pilihan" class="jam-days-grid">
                    ${weekdays.map(hari => `
                        <label class="jam-day-card selected">
                            <input type="checkbox" value="${hari}" checked onchange="this.closest('.jam-day-card').classList.toggle('selected', this.checked); updateToggleAllDaysBtn();">
                            <span class="jam-day-check-indicator">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span class="jam-day-name">${hari}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
        `;
    }

    const result = await Swal.fire({
        title: `Ubah ${label}`,
        html: `
            <div style="text-align:left">
                <div class="jam-popup-time-grid">
                    <div class="jam-popup-field">
                        <label for="popup-jam-mulai">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Waktu Mulai
                        </label>
                        <input id="popup-jam-mulai" type="time" value="${mulai}">
                    </div>
                    <div class="jam-popup-field">
                        <label for="popup-jam-selesai">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Waktu Selesai
                        </label>
                        <input id="popup-jam-selesai" type="time" value="${selesaiAwal}">
                    </div>
                </div>
                ${hariFieldHtml}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        customClass: { confirmButton: 'jam-popup-confirm', cancelButton: 'jam-popup-cancel' },
        preConfirm: () => {
            const jamMulai = document.getElementById('popup-jam-mulai').value;
            const jamSelesai = document.getElementById('popup-jam-selesai').value;
            if (!jamMulai || !jamSelesai || jamSelesai <= jamMulai) {
                Swal.showValidationMessage('Jam selesai harus lebih besar dari jam mulai.');
                return false;
            }
            let hari = [];
            if (isFriday) {
                hari = ['Jumat'];
            } else {
                hari = Array.from(document.querySelectorAll('#popup-hari-pilihan input:checked')).map(input => input.value);
            }
            if (!hari.length) {
                Swal.showValidationMessage('Pilih minimal satu hari untuk menerapkan perubahan.');
                return false;
            }
            return { jam_mulai: jamMulai, jam_selesai: jamSelesai, hari };
        }
    });
    if (!result.isConfirmed) return;

    const confirm = await Swal.fire({
        icon: 'question',
        title: 'Simpan perubahan?',
        text: `${label}: ${result.value.jam_mulai} - ${result.value.jam_selesai}\nHari: ${result.value.hari.join(', ')}`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Periksa Lagi',
        buttonsStyling: false,
        customClass: { confirmButton: 'jam-popup-confirm', cancelButton: 'jam-popup-cancel' }
    });
    if (!confirm.isConfirmed) return;

    const url = type === 'istirahat'
        ? `/jam-pelajaran/istirahat/${idOrDay}/${nomor}`
        : `/jam-pelajaran/${idOrDay}/update`;
    fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json'},
        body: JSON.stringify(result.value)
    }).then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Perubahan gagal disimpan.');
        await Swal.fire({icon: 'success', title: 'Tersimpan', text: data.message, confirmButtonColor: '#2563eb'});
        location.reload();
    }).catch(error => Swal.fire({icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626'}));
}

function pilihHariJam(day, button) {
    document.querySelectorAll('.jam-row').forEach(row => row.style.display = row.dataset.day === day || (day !== 'Jumat' && row.dataset.day === 'all-weekday') ? '' : 'none');
    document.querySelectorAll('.jam-day-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.remove('btn-primary');
        tab.classList.add('btn-secondary');
    });
    button.classList.add('active');
    button.classList.remove('btn-secondary');
    button.classList.add('btn-primary');
}

document.addEventListener('DOMContentLoaded', () => {
    const firstTab = document.querySelector('.jam-day-tab.active');
    if (firstTab) pilihHariJam(firstTab.dataset.day, firstTab);
});
</script>
