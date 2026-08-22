<div class="page-content page-anim" id="page-jam-pelajaran" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Jam Pelajaran</div>
            <div class="page-subtitle">Atur waktu nyata untuk setiap nomor jam pelajaran yang dipakai jadwal.</div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px;max-width:850px">
        <form id="jam-pelajaran-form" onsubmit="simpanJamPelajaran(event)">
            @csrf
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px" role="tablist" aria-label="Hari jam pelajaran">
                <button type="button" class="btn-primary jam-day-tab active" data-day="weekday" onclick="pilihHariJam('weekday', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Senin</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="weekday" onclick="pilihHariJam('weekday', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Selasa</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="weekday" onclick="pilihHariJam('weekday', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Rabu</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="weekday" onclick="pilihHariJam('weekday', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Kamis</button>
                <button type="button" class="btn-secondary jam-day-tab" data-day="friday" onclick="pilihHariJam('friday', this)" style="border-radius:8px;padding:8px 12px;font-size:12px">Jumat</button>
            </div>
            <div style="overflow-x:auto">
                <table class="data-table" style="min-width:520px">
                    <thead><tr><th>Jam Ke-</th><th>Mulai</th><th>Selesai</th><th>Kelompok Hari</th></tr></thead>
                    <tbody>
                        @forelse($allJamPelajaran as $jamItem)
                            <tr class="jam-row" data-day="{{ $jamItem->jam_ke >= 100 ? 'friday' : 'weekday' }}">
                                <td><strong>Jam ke-{{ $jamItem->jam_ke }}</strong></td>
                                <td><input type="time" name="jam[{{ $jamItem->id_jam }}][jam_mulai]" value="{{ substr($jamItem->jam_mulai, 0, 5) }}" class="filter-input" required></td>
                                <td><input type="time" name="jam[{{ $jamItem->id_jam }}][jam_selesai]" value="{{ substr($jamItem->jam_selesai, 0, 5) }}" class="filter-input" required></td>
                                <td style="color:#64748b;font-size:12px">{{ $jamItem->jam_ke >= 100 ? 'Jumat' : 'Senin - Kamis' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align:center;color:#64748b;padding:24px">Belum ada data jam pelajaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="display:flex;align-items:center;gap:12px;margin-top:18px;flex-wrap:wrap">
                <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 18px;font-size:13px">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan Jam
                </button>
                <span style="font-size:12px;color:#94a3b8">Perubahan waktu otomatis terlihat pada jadwal dan tampilan absensi.</span>
            </div>
        </form>
    </div>
</div>

<script>
function simpanJamPelajaran(event) {
    event.preventDefault();
    const form = document.getElementById('jam-pelajaran-form');
    fetch(@json(route('jam-pelajaran.update')), {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
        body: new FormData(form)
    }).then(async response => {
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Perubahan jam gagal disimpan.');
        await Swal.fire({icon: 'success', title: 'Tersimpan', text: result.message, confirmButtonColor: '#2563eb'});
        location.reload();
    }).catch(error => Swal.fire({icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626'}));
}

function pilihHariJam(day, button) {
    document.querySelectorAll('.jam-row').forEach(row => row.style.display = row.dataset.day === day ? '' : 'none');
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
