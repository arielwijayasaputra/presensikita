<div class="page-content page-anim" id="page-jam-pelajaran" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Jam Pelajaran</div>
            <div class="page-subtitle">Atur waktu nyata untuk setiap nomor jam pelajaran yang dipakai jadwal.</div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px;max-width:850px">
        <div id="jam-pelajaran-form">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:16px">
                <div style="display:flex;gap:8px;flex-wrap:wrap" role="tablist" aria-label="Hari jam pelajaran">
                    <button type="button" class="btn-primary jam-day-tab active" data-day="Senin" onclick="pilihHariJam('Senin', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Senin</button>
                    <button type="button" class="btn-secondary jam-day-tab" data-day="Selasa" onclick="pilihHariJam('Selasa', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Selasa</button>
                    <button type="button" class="btn-secondary jam-day-tab" data-day="Rabu" onclick="pilihHariJam('Rabu', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Rabu</button>
                    <button type="button" class="btn-secondary jam-day-tab" data-day="Kamis" onclick="pilihHariJam('Kamis', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Kamis</button>
                    <button type="button" class="btn-secondary jam-day-tab" data-day="Jumat" onclick="pilihHariJam('Jumat', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Jumat</button>
                    <button type="button" class="btn-secondary jam-day-tab" data-day="Sabtu" onclick="pilihHariJam('Sabtu', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Sabtu</button>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <button type="button" class="btn-primary" onclick="bukaModalTambahJam()" style="border-radius:8px;padding:8px 14px;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Jam Pelajaran
                    </button>
                    <button type="button" onclick="hapusJamHariAktif()" style="border-radius:8px;padding:8px 12px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:6px;background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;cursor:pointer">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Hapus Jam Hari Ini
                    </button>
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="data-table" style="min-width:520px">
                    <thead><tr><th>Jam Ke-</th><th>Mulai</th><th>Selesai</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @php $shownBreak = []; @endphp
                        @forelse($allJamPelajaran as $jamItem)
                            @if(!empty($istirahat1Mulai) && !empty($istirahat1Selesai) && (int) $jamItem->jam_ke === 5 && !in_array('ist1_weekday', $shownBreak))
                                @php $shownBreak[] = 'ist1_weekday'; @endphp
                                <tr class="jam-row jam-break-row" data-day="all-weekday" style="background:#fff7ed">
                                    <td><strong>Istirahat 1</strong></td>
                                    <td>{{ $istirahat1Mulai }}</td>
                                    <td>{{ $istirahat1Selesai }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:6px">
                                            <button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 1" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'weekday', 1, 'Istirahat 1', '{{ $istirahat1Mulai }}', '{{ $istirahat1Selesai }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
                                            <button type="button" class="jam-del-btn" aria-label="Hapus Istirahat 1" title="Hapus istirahat" onclick="hapusIstirahat('weekday', 1, 'Istirahat 1')"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @elseif(!empty($istirahat2Mulai) && !empty($istirahat2Selesai) && (int) $jamItem->jam_ke === 8 && !in_array('ist2_weekday', $shownBreak))
                                @php $shownBreak[] = 'ist2_weekday'; @endphp
                                <tr class="jam-row jam-break-row" data-day="all-weekday" style="background:#fff7ed">
                                    <td><strong>Istirahat 2</strong></td>
                                    <td>{{ $istirahat2Mulai }}</td>
                                    <td>{{ $istirahat2Selesai }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:6px">
                                            <button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 2" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'weekday', 2, 'Istirahat 2', '{{ $istirahat2Mulai }}', '{{ $istirahat2Selesai }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
                                            <button type="button" class="jam-del-btn" aria-label="Hapus Istirahat 2" title="Hapus istirahat" onclick="hapusIstirahat('weekday', 2, 'Istirahat 2')"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @elseif(!empty($istirahatJumat1Mulai) && !empty($istirahatJumat1Selesai) && (int) $jamItem->jam_ke === 106 && !in_array('ist1_jumat', $shownBreak))
                                @php $shownBreak[] = 'ist1_jumat'; @endphp
                                <tr class="jam-row jam-break-row" data-day="Jumat" style="background:#fff7ed">
                                    <td><strong>Istirahat 1</strong></td>
                                    <td>{{ $istirahatJumat1Mulai }}</td>
                                    <td>{{ $istirahatJumat1Selesai }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:6px">
                                            <button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 1 Jumat" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'friday', 1, 'Istirahat 1 Jumat', '{{ $istirahatJumat1Mulai }}', '{{ $istirahatJumat1Selesai }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
                                            <button type="button" class="jam-del-btn" aria-label="Hapus Istirahat 1 Jumat" title="Hapus istirahat" onclick="hapusIstirahat('friday', 1, 'Istirahat 1 Jumat')"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @elseif(!empty($istirahatJumat2Mulai) && !empty($istirahatJumat2Selesai) && (int) $jamItem->jam_ke === 109 && !in_array('ist2_jumat', $shownBreak))
                                @php $shownBreak[] = 'ist2_jumat'; @endphp
                                <tr class="jam-row jam-break-row" data-day="Jumat" style="background:#fff7ed">
                                    <td><strong>Istirahat 2</strong></td>
                                    <td>{{ $istirahatJumat2Mulai }}</td>
                                    <td>{{ $istirahatJumat2Selesai }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:6px">
                                            <button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 2 Jumat" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'friday', 2, 'Istirahat 2 Jumat', '{{ $istirahatJumat2Mulai }}', '{{ $istirahatJumat2Selesai }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
                                            <button type="button" class="jam-del-btn" aria-label="Hapus Istirahat 2 Jumat" title="Hapus istirahat" onclick="hapusIstirahat('friday', 2, 'Istirahat 2 Jumat')"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="jam-row" data-day="{{ $jamItem->hari ?? ($jamItem->jam_ke >= 100 ? 'Jumat' : 'Senin') }}">
                                <td><strong>Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}</strong></td>
                                <td>{{ substr($jamItem->jam_mulai, 0, 5) }}</td>
                                <td>{{ substr($jamItem->jam_selesai, 0, 5) }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px">
                                        <button type="button" class="jam-edit-btn" aria-label="Ubah waktu Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}" title="Ubah waktu" onclick="ubahWaktuJam('jam', {{ $jamItem->id_jam }}, 'Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}', '{{ substr($jamItem->jam_mulai, 0, 5) }}', '{{ substr($jamItem->jam_selesai, 0, 5) }}', '{{ $jamItem->hari ?? ($jamItem->jam_ke >= 100 ? 'Jumat' : 'Senin') }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1-1 4Z"/></svg></button>
                                        <button type="button" class="jam-del-btn" aria-label="Hapus Jam ke-{{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }}" title="Hapus jam" onclick="hapusJamPelajaran({{ $jamItem->id_jam }}, '{{ $jamItem->hari ?? ($jamItem->jam_ke >= 100 ? 'Jumat' : 'Senin') }}', {{ $jamItem->jam_ke >= 100 ? $jamItem->jam_ke - 100 : $jamItem->jam_ke }})"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                    </div>
                                </td>
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
.jam-del-btn { width:34px; height:34px; padding:0; border:1px solid #fecaca; border-radius:8px; background:#fef2f2; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:all .15s ease; }
.jam-del-btn:hover { background:#fee2e2; color:#b91c1c; transform:translateY(-1px); }

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
    grid-template-columns: repeat(5, 1fr);
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
.jam-day-card input[type="checkbox"],
.jam-day-card input[type="radio"] {
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
[data-theme="dark"] .jam-del-btn {
    background: rgba(220,38,38,0.15) !important;
    border-color: rgba(220,38,38,0.3) !important;
    color: #f87171 !important;
}
[data-theme="dark"] .jam-del-btn:hover {
    background: rgba(220,38,38,0.25) !important;
    color: #fca5a5 !important;
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
        const weekdays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Sabtu'];
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

const ALL_JAM_PELAJARAN_DATA = @json($allJamPelajaran);

function hitungNextJamUntukHari(hari) {
    const isFriday = hari === 'Jumat';
    const jamRows = ALL_JAM_PELAJARAN_DATA.filter(j => {
        if (isFriday) {
            return j.hari === 'Jumat' || parseInt(j.jam_ke, 10) >= 100;
        }
        return j.hari === hari || (!j.hari && parseInt(j.jam_ke, 10) < 100);
    });

    let maxJamDisplay = 0;
    let lastJamItem = null;

    jamRows.forEach(j => {
        const raw = parseInt(j.jam_ke, 10);
        const display = (isFriday && raw >= 100) ? (raw - 100) : raw;
        if (display > maxJamDisplay) {
            maxJamDisplay = display;
            lastJamItem = j;
        }
    });

    const nextJamDisplay = maxJamDisplay > 0 ? maxJamDisplay + 1 : 1;
    let defaultMulai = '15:45';
    let defaultSelesai = '16:25';

    if (lastJamItem && lastJamItem.jam_selesai) {
        defaultMulai = lastJamItem.jam_selesai.substring(0, 5);
        const parts = defaultMulai.split(':');
        const totalMinutes = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10) + 40;
        const endH = Math.floor(totalMinutes / 60) % 24;
        const endM = totalMinutes % 60;
        defaultSelesai = String(endH).padStart(2, '0') + ':' + String(endM).padStart(2, '0');
    }

    return {
        lastJamDisplay: maxJamDisplay,
        nextJamDisplay: nextJamDisplay,
        defaultMulai: defaultMulai,
        defaultSelesai: defaultSelesai
    };
}

async function bukaModalTambahJam() {
    const activeTabDay = document.querySelector('.jam-day-tab.active')?.dataset.day || 'Senin';
    const isFriday = activeTabDay === 'Jumat';
    const calc = hitungNextJamUntukHari(activeTabDay);

    const weekdays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Sabtu'];

    let hariSectionHtml = '';
    if (isFriday) {
        hariSectionHtml = `
            <div class="jam-popup-field">
                <label>Pilih Hari (Checklist)</label>
                <div class="jam-day-friday-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>
                        <strong style="display:block;font-size:13px;line-height:1.2">Khusus Hari Jumat</strong>
                        <span style="font-size:11px;opacity:0.85">Jam pelajaran Jumat dikelola terpisah.</span>
                    </div>
                    <input type="checkbox" id="popup-tambah-chk-jumat" value="Jumat" checked style="display:none">
                </div>
            </div>
        `;
    } else {
        hariSectionHtml = `
            <div class="jam-popup-field">
                <div class="jam-days-header">
                    <label style="margin-bottom:0">Pilih Hari (Checklist)</label>
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
        title: 'Tambah Jam / Istirahat',
        html: `
            <div style="text-align:left">
                <div class="jam-popup-field" style="margin-bottom:14px">
                    <label style="font-weight:700;color:#334155;margin-bottom:8px">Pilih Jenis:</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <label id="lbl-jenis-pelajaran" class="jam-day-card selected" style="padding:10px 8px;flex-direction:row;gap:8px;cursor:pointer">
                            <input type="radio" name="popup_jenis_jam" value="pelajaran" checked onchange="document.getElementById('lbl-jenis-pelajaran').classList.add('selected');document.getElementById('lbl-jenis-istirahat').classList.remove('selected');document.getElementById('section-tambah-pelajaran').style.display='block';document.getElementById('section-tambah-istirahat').style.display='none';">
                            <span class="jam-day-check-indicator" style="border-radius:50%">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8"/></svg>
                            </span>
                            <span class="jam-day-name" style="font-size:12.5px">Jam Pelajaran</span>
                        </label>
                        <label id="lbl-jenis-istirahat" class="jam-day-card" style="padding:10px 8px;flex-direction:row;gap:8px;cursor:pointer">
                            <input type="radio" name="popup_jenis_jam" value="istirahat" onchange="document.getElementById('lbl-jenis-istirahat').classList.add('selected');document.getElementById('lbl-jenis-pelajaran').classList.remove('selected');document.getElementById('section-tambah-pelajaran').style.display='none';document.getElementById('section-tambah-istirahat').style.display='block';">
                            <span class="jam-day-check-indicator" style="border-radius:50%">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8"/></svg>
                            </span>
                            <span class="jam-day-name" style="font-size:12.5px">Jam Istirahat</span>
                        </label>
                    </div>
                </div>

                <div id="section-tambah-pelajaran">
                    ${hariSectionHtml}

                    <div class="jam-popup-field">
                        <label for="popup-tambah-jam-ke">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Jam Ke-
                        </label>
                        <div style="display:flex;align-items:center;gap:10px">
                            <input id="popup-tambah-jam-ke" type="number" min="1" value="${calc.nextJamDisplay}" style="width:110px;box-sizing:border-box;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:10px;color:#0f172a;font-size:14.5px;font-weight:700;background:#f8fafc">
                            <div id="popup-tambah-jam-info" style="font-size:11.5px;color:#64748b;line-height:1.3">
                                Ditambahkan setelah jam terakhir <strong>Jam ke-${calc.lastJamDisplay || 0}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section-tambah-istirahat" style="display:none">
                    <div class="jam-popup-field">
                        <label for="popup-tambah-nomor-istirahat">Pilihan Istirahat</label>
                        <select id="popup-tambah-nomor-istirahat" class="filter-select" style="width:100%;padding:10px 12px;border-radius:10px;font-size:13.5px">
                            <option value="1">Istirahat 1</option>
                            <option value="2">Istirahat 2</option>
                        </select>
                    </div>
                    <div class="jam-popup-field">
                        <label for="popup-tambah-hari-istirahat">Terapkan ke</label>
                        <select id="popup-tambah-hari-istirahat" class="filter-select" style="width:100%;padding:10px 12px;border-radius:10px;font-size:13.5px">
                            <option value="weekday" ${!isFriday ? 'selected' : ''}>Hari Biasa (Senin - Sabtu)</option>
                            <option value="friday" ${isFriday ? 'selected' : ''}>Khusus Hari Jumat</option>
                        </select>
                    </div>
                </div>

                <div class="jam-popup-time-grid">
                    <div class="jam-popup-field" style="margin-bottom:0">
                        <label for="popup-tambah-jam-mulai">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Waktu Mulai
                        </label>
                        <input id="popup-tambah-jam-mulai" type="time" value="${calc.defaultMulai}">
                    </div>
                    <div class="jam-popup-field" style="margin-bottom:0">
                        <label for="popup-tambah-jam-selesai">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Waktu Selesai
                        </label>
                        <input id="popup-tambah-jam-selesai" type="time" value="${calc.defaultSelesai}">
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        customClass: { confirmButton: 'jam-popup-confirm', cancelButton: 'jam-popup-cancel' },
        preConfirm: () => {
            const jenisJam = document.querySelector('input[name="popup_jenis_jam"]:checked')?.value || 'pelajaran';
            const jamMulai = document.getElementById('popup-tambah-jam-mulai')?.value;
            const jamSelesai = document.getElementById('popup-tambah-jam-selesai')?.value;

            if (!jamMulai || !jamSelesai || jamSelesai <= jamMulai) {
                Swal.showValidationMessage('Waktu selesai harus lebih besar dari waktu mulai.');
                return false;
            }

            if (jenisJam === 'istirahat') {
                const nomor = parseInt(document.getElementById('popup-tambah-nomor-istirahat')?.value, 10) || 1;
                const hariTipe = document.getElementById('popup-tambah-hari-istirahat')?.value || 'weekday';
                return {
                    jenis: 'istirahat',
                    nomor: nomor,
                    hari_tipe: hariTipe,
                    jam_mulai: jamMulai,
                    jam_selesai: jamSelesai,
                };
            }

            const jamKeInput = document.getElementById('popup-tambah-jam-ke')?.value;
            const jamKeNum = parseInt(jamKeInput, 10);
            if (!jamKeNum || jamKeNum < 1) {
                Swal.showValidationMessage('Nomor jam pelajaran (jam ke-) harus berupa angka minimal 1.');
                return false;
            }

            let hari = [];
            if (isFriday) {
                hari = ['Jumat'];
            } else {
                hari = Array.from(document.querySelectorAll('#popup-hari-pilihan input:checked')).map(input => input.value);
            }

            if (!hari.length) {
                Swal.showValidationMessage('Pilih minimal satu hari untuk menambahkan jam pelajaran.');
                return false;
            }

            // Peringatan jika jam sudah ada sebelumnya
            for (const h of hari) {
                const checkInternal = h === 'Jumat'
                    ? (jamKeNum < 100 ? jamKeNum + 100 : jamKeNum)
                    : (jamKeNum >= 100 ? jamKeNum - 100 : jamKeNum);

                const exists = ALL_JAM_PELAJARAN_DATA.some(j => {
                    if (h === 'Jumat') {
                        return (j.hari === 'Jumat' || parseInt(j.jam_ke, 10) >= 100) && parseInt(j.jam_ke, 10) === checkInternal;
                    }
                    return (j.hari === h || (!j.hari && parseInt(j.jam_ke, 10) < 100)) && parseInt(j.jam_ke, 10) === checkInternal;
                });

                if (exists) {
                    Swal.showValidationMessage(`Jam ke-${jamKeNum} pada hari ${h} sudah ada sebelumnya. Silakan gunakan nomor jam lain atau sesuaikan jadwal yang sudah ada.`);
                    return false;
                }
            }

            return {
                jenis: 'pelajaran',
                jam_ke: jamKeNum,
                jam_mulai: jamMulai,
                jam_selesai: jamSelesai,
                hari: hari
            };
        }
    });

    if (!result.isConfirmed || !result.value) return;

    if (result.value.jenis === 'istirahat') {
        const url = `/jam-pelajaran/istirahat/${result.value.hari_tipe}/${result.value.nomor}`;
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                jam_mulai: result.value.jam_mulai,
                jam_selesai: result.value.jam_selesai,
                hari: [result.value.hari_tipe]
            })
        }).then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menyimpan waktu istirahat.');
            await Swal.fire({ icon: 'success', title: 'Berhasil Disimpan', text: data.message, confirmButtonColor: '#2563eb' });
            location.reload();
        }).catch(error => Swal.fire({ icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626' }));
        return;
    }

    fetch('/jam-pelajaran/tambah', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(result.value)
    }).then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Gagal menambahkan jam pelajaran.');
        await Swal.fire({ icon: 'success', title: 'Berhasil Ditambahkan', text: data.message, confirmButtonColor: '#2563eb' });
        location.reload();
    }).catch(error => Swal.fire({ icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626' }));
}

function hapusIstirahat(hariTipe, nomor, label) {
    Swal.fire({
        title: `Hapus ${label}?`,
        text: `Waktu ${label} akan dihapus dari jam pelajaran.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/jam-pelajaran/istirahat/${hariTipe}/${nomor}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        }).then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menghapus waktu istirahat.');
            await Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, confirmButtonColor: '#2563eb' });
            location.reload();
        }).catch(error => Swal.fire({ icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626' }));
    });
}

function hapusJamPelajaran(idJam, hari, jamKe) {
    Swal.fire({
        title: `Hapus Jam ke-${jamKe}?`,
        text: `Jam pelajaran ke-${jamKe} pada hari ${hari} akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/jam-pelajaran/${idJam}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        }).then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menghapus jam pelajaran.');
            await Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, confirmButtonColor: '#2563eb' });
            location.reload();
        }).catch(error => Swal.fire({ icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626' }));
    });
}

function hapusJamHariAktif() {
    const activeTabDay = document.querySelector('.jam-day-tab.active')?.dataset.day || 'Senin';
    Swal.fire({
        title: `Hapus Semua Jam Hari ${activeTabDay}?`,
        text: `Seluruh jam pelajaran yang terdaftar pada hari ${activeTabDay} akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: `Ya, Hapus Semua Jam Hari ${activeTabDay}`,
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/jam-pelajaran/hari/${encodeURIComponent(activeTabDay)}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        }).then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menghapus jam pelajaran hari ini.');
            await Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, confirmButtonColor: '#2563eb' });
            location.reload();
        }).catch(error => Swal.fire({ icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626' }));
    });
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
