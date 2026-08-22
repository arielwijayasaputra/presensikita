<div class="page-content page-anim" id="page-jadwal" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Jadwal Mengajar</div>
            <div class="page-subtitle">Pilih kelas untuk melihat dan mengubah jadwal lengkapnya.</div>
        </div>
    </div>

    <div class="card" style="padding:20px 22px">
        <button class="btn-primary" onclick="bukaFormJadwal()" style="border-radius:8px;padding:10px 16px;font-size:13px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jadwal
        </button>
        <div class="filter-bar" style="margin-bottom:16px;align-items:flex-end;gap:12px;flex-wrap:wrap">
            <div class="filter-group" style="flex:1;min-width:220px">
                <label>Cari jadwal</label>
                <input type="search" id="jadwal-cari" class="filter-input" placeholder="Ketik untuk mencari..." oninput="filterJadwal()" style="width:100%">
            </div>
            <div class="filter-group" style="min-width:140px">
                <label>Hari</label>
                <select id="jadwal-hari" class="filter-select" onchange="filterJadwal()" style="width:100%">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="min-width:170px">
                <label>Kelas</label>
                <select id="jadwal-kelas" class="filter-select" onchange="filterJadwal()" style="width:100%">
                    <option value="">Semua Kelas</option>
                    @foreach($allKelas as $kelasItem)
                        <option value="{{ $kelasItem->id_kelas }}" {{ $loop->first ? 'selected' : '' }}>{{ $kelasItem->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <span id="jadwal-count" style="font-size:12px;color:#64748b;padding-bottom:9px">{{ count($allJadwal) }} jadwal</span>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:920px">
                <thead>
                    <tr><th>Hari</th><th>Jam Ke-</th><th>Waktu</th><th>Mata Pelajaran</th><th>Guru</th><th>Kelas</th><th>Aksi</th></tr>
                </thead>
                <tbody id="jadwal-table-body">
                    @forelse($allJadwal as $jadwalItem)
                        <tr class="jadwal-row" data-search="{{ strtolower($jadwalItem->nama_guru . ' ' . $jadwalItem->nama_mapel . ' ' . $jadwalItem->nama_kelas . ' ' . $jadwalItem->hari . ' ' . $jadwalItem->jam_ke) }}" data-hari="{{ $jadwalItem->hari }}" data-kelas="{{ $jadwalItem->id_kelas }}">
                            <td><strong>{{ $jadwalItem->hari }}</strong></td>
                            <td><span class="badge badge-info">Jam ke-{{ $jadwalItem->jam_ke }}</span></td>
                            <td>{{ substr($jadwalItem->jam_mulai, 0, 5) }} - {{ substr($jadwalItem->jam_selesai, 0, 5) }}</td>
                            <td>{{ $jadwalItem->nama_mapel }}</td>
                            <td>{{ $jadwalItem->nama_guru }}</td>
                            <td><strong>{{ $jadwalItem->nama_kelas }}</strong></td>
                            <td><button type="button" class="btn-secondary" onclick='editJadwal(@json($jadwalItem))' style="padding:7px 10px;font-size:12px">Edit</button></td>
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

<div id="jadwal-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:200;align-items:center;justify-content:center;padding:20px">
    <div class="card" style="width:100%;max-width:520px;padding:24px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px"><h3 id="jadwal-modal-title" style="font-size:17px">Tambah Jadwal</h3><button type="button" onclick="tutupFormJadwal()" aria-label="Tutup" style="border:0;background:none;font-size:22px;color:#64748b;cursor:pointer">&times;</button></div>
        <form id="jadwal-form" onsubmit="simpanJadwal(event)">
            @csrf
            <input type="hidden" id="jadwal-id">
            <div style="display:grid;gap:12px">
                <div><label>Hari</label><select id="form-jadwal-hari" class="filter-select" required style="width:100%;margin-top:4px">@foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)<option value="{{ $hari }}">{{ $hari }}</option>@endforeach</select></div>
                <div><label>Kelas</label><select id="form-jadwal-kelas" class="filter-select" required style="width:100%;margin-top:4px">@foreach($allKelas as $kelasItem)<option value="{{ $kelasItem->id_kelas }}">{{ $kelasItem->nama_kelas }}</option>@endforeach</select></div>
                <div><label>Guru</label><select id="form-jadwal-guru" class="filter-select" required style="width:100%;margin-top:4px">@foreach($allGuru as $guruItem)<option value="{{ $guruItem->id_guru }}">{{ $guruItem->nama_guru }}</option>@endforeach</select></div>
                <div><label>Mata Pelajaran</label><select id="form-jadwal-mapel" class="filter-select" required style="width:100%;margin-top:4px">@foreach($allMapel as $mapelItem)<option value="{{ $mapelItem->id_mapel }}">{{ $mapelItem->nama_mapel }}</option>@endforeach</select></div>
                <div><label>Jam Ke-</label><select id="form-jadwal-jam" class="filter-select" required style="width:100%;margin-top:4px">@foreach($allJamPelajaran as $jamItem)<option value="{{ $jamItem->id_jam }}">{{ $jamItem->jam_ke }} ({{ substr($jamItem->jam_mulai, 0, 5) }} - {{ substr($jamItem->jam_selesai, 0, 5) }})</option>@endforeach</select></div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px"><button type="button" class="btn-secondary" onclick="tutupFormJadwal()">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
        </form>
    </div>
</div>

<script>
function filterJadwal() {
    const keyword = document.getElementById('jadwal-cari').value.trim().toLowerCase();
    const hari = document.getElementById('jadwal-hari').value;
    const kelas = document.getElementById('jadwal-kelas').value;
    let visible = 0;

    document.querySelectorAll('.jadwal-row').forEach(row => {
        const matches = row.dataset.search.includes(keyword) &&
            (!hari || row.dataset.hari === hari) && (!kelas || row.dataset.kelas === kelas);
        row.style.display = matches ? '' : 'none';
        if (matches) visible++;
    });

    document.getElementById('jadwal-count').textContent = `${visible} jadwal`;
    document.getElementById('jadwal-no-result').style.display = visible ? 'none' : 'block';
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
    document.getElementById('form-jadwal-guru').value = jadwal.id_guru;
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

document.addEventListener('DOMContentLoaded', filterJadwal);
</script>
