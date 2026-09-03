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
                        @forelse($allJamPelajaran as $jamItem)
                            @if((int) $jamItem->jam_ke === 5)
                                <tr class="jam-row jam-break-row" data-day="all-weekday" style="background:#fff7ed">
                                    <td><strong>Istirahat 1</strong></td>
                                    <td>{{ $istirahat1Mulai ?? '09:40' }}</td>
                                    <td>{{ $istirahat1Selesai ?? '10:00' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 1" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'weekday', 1, 'Istirahat 1', '{{ $istirahat1Mulai ?? '09:40' }}', '{{ $istirahat1Selesai ?? '10:00' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @elseif((int) $jamItem->jam_ke === 8)
                                <tr class="jam-row jam-break-row" data-day="all-weekday" style="background:#fff7ed">
                                    <td><strong>Istirahat 2</strong></td>
                                    <td>{{ $istirahat2Mulai ?? '12:00' }}</td>
                                    <td>{{ $istirahat2Selesai ?? '13:00' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 2" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'weekday', 2, 'Istirahat 2', '{{ $istirahat2Mulai ?? '12:00' }}', '{{ $istirahat2Selesai ?? '13:00' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @elseif((int) $jamItem->jam_ke === 106)
                                <tr class="jam-row jam-break-row" data-day="Jumat" style="background:#fff7ed">
                                    <td><strong>Istirahat 1</strong></td>
                                    <td>{{ $istirahatJumat1Mulai ?? '09:00' }}</td>
                                    <td>{{ $istirahatJumat1Selesai ?? '09:50' }}</td>
                                    <td><button type="button" class="jam-edit-btn" aria-label="Ubah waktu Istirahat 1 Jumat" title="Ubah waktu" onclick="ubahWaktuJam('istirahat', 'friday', 1, 'Istirahat 1 Jumat', '{{ $istirahatJumat1Mulai ?? '09:00' }}', '{{ $istirahatJumat1Selesai ?? '09:50' }}')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button></td>
                                </tr>
                            @elseif((int) $jamItem->jam_ke === 109)
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
            <div style="margin-top:18px;font-size:12px;color:#94a3b8">Klik Ubah pada baris yang ingin disesuaikan.</div>
        </div>
    </div>
</div>

<style>
.jam-edit-btn { width:34px; height:34px; padding:0; border:1px solid #bfdbfe; border-radius:8px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:all .15s ease; }
.jam-edit-btn:hover { background:#dbeafe; color:#1d4ed8; transform:translateY(-1px); }
.jam-popup-field { display:block; text-align:left; margin-bottom:14px; }
.jam-popup-field label { display:block; margin-bottom:6px; color:#475569; font-size:12px; font-weight:700; }
.jam-popup-field input { width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; color:#0f172a; font-size:14px; }
.jam-popup-field input:focus { outline:2px solid #bfdbfe; border-color:#2563eb; }
.jam-popup-confirm { border:0; border-radius:8px; background:#2563eb; color:#fff; padding:10px 16px; font-weight:700; cursor:pointer; }
.jam-popup-cancel { border:1px solid #cbd5e1; border-radius:8px; background:#fff; color:#475569; padding:10px 16px; font-weight:700; cursor:pointer; }
</style>

<script>
async function ubahWaktuJam(type, idOrDay, nomorOrLabel, labelOrMulai, mulaiOrSelesai, selesai, hariSumber = null) {
    const nomor = type === 'istirahat' ? nomorOrLabel : null;
    const label = type === 'istirahat' ? labelOrMulai : nomorOrLabel;
    const mulai = type === 'istirahat' ? mulaiOrSelesai : labelOrMulai;
    const selesaiAwal = type === 'istirahat' ? selesai : mulaiOrSelesai;
    const defaultHari = hariSumber || (idOrDay === 'friday' ? 'Jumat' : (document.querySelector('.jam-day-tab.active')?.dataset.day || 'Senin'));
    const result = await Swal.fire({
        title: `Ubah ${label}`,
        html: `<div><div class="jam-popup-field"><label for="popup-jam-mulai">Waktu mulai</label><input id="popup-jam-mulai" type="time" value="${mulai}"></div><div class="jam-popup-field"><label for="popup-jam-selesai">Waktu selesai</label><input id="popup-jam-selesai" type="time" value="${selesaiAwal}"></div><div class="jam-popup-field"><label>Terapkan ke hari</label><div id="popup-hari-pilihan" style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;text-align:left">${['Senin','Selasa','Rabu','Kamis','Jumat'].map(hari => `<label style="display:flex;align-items:center;gap:7px;padding:8px 10px;border:1px solid #e2e8f0;border-radius:7px;font-size:12px;color:#475569;cursor:pointer"><input type="checkbox" value="${hari}" ${hari === defaultHari ? 'checked' : ''}>${hari}</label>`).join('')}</div></div></div>`,
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
            const hari = Array.from(document.querySelectorAll('#popup-hari-pilihan input:checked')).map(input => input.value);
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
