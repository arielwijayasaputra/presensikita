<div class="page-content page-anim" id="page-siswa-terlambat" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800">Siswa Terlambat</div>
            <div class="page-subtitle">Catat perizinan siswa yang datang terlambat. Status di jam sebelum hadir otomatis berubah menjadi <strong>Masuk Terlambat</strong>, dan jam berikutnya menjadi <strong>Hadir</strong>.</div>
        </div>
    </div>

    <div class="card" style="max-width:900px">
        <div class="card-header" style="margin-bottom:16px">
            <div class="card-title">Input Keterlambatan Siswa</div>
        </div>
        <form id="siswa-terlambat-form" onsubmit="simpanSiswaTerlambat(event)">
            @csrf
            <div class="siswa-terlambat-form-grid">
                <div>
                    <label for="terlambat-siswa">Siswa</label>
                    <input type="hidden" id="terlambat-siswa" name="id_siswa" required>
                    <div style="position:relative;margin-top:4px">
                        <input type="text" id="terlambat-siswa-search" class="filter-input" placeholder="Ketik nama siswa..." autocomplete="off" required style="width:100%"
                            onfocus="toggleTerlambatSiswaDropdown(true)"
                            oninput="filterTerlambatSiswaDropdown(this.value)">
                        <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        <div id="terlambat-siswa-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:50;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                            @foreach($siswaAktif as $siswa)
                                <div class="terlambat-siswa-dropdown-item" data-id="{{ $siswa->id_siswa }}" data-search="{{ strtolower($siswa->nama_siswa . ' ' . ($siswa->kelas->nama_kelas ?? '')) }}"
                                    onclick="pilihTerlambatSiswaDropdown('{{ $siswa->id_siswa }}', '{{ addslashes($siswa->nama_siswa) }} - {{ addslashes($siswa->kelas->nama_kelas ?? '') }}')"
                                    style="padding:12px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9;transition:background 0.15s"
                                    onmouseenter="this.style.background='#f1f5f9'" onmouseleave="this.style.background='#fff'">
                                    {{ $siswa->nama_siswa }} - {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            @endforeach
                            <div id="terlambat-siswa-empty" style="display:none;padding:14px;text-align:center;color:#94a3b8;font-size:13px">Siswa tidak ditemukan</div>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="terlambat-tanggal">Tanggal</label>
                    <input id="terlambat-tanggal" type="date" name="tanggal" value="{{ now()->toDateString() }}" class="filter-input" required style="width:100%;margin-top:4px">
                </div>
                <div>
                    <label for="terlambat-jam-masuk">Waktu Datang</label>
                    <input id="terlambat-jam-masuk" type="time" name="jam_masuk" value="{{ now()->format('H:i') }}" class="filter-input" required style="width:100%;margin-top:4px">
                </div>
                <div>
                    <label for="terlambat-jam-ke">Mulai Jam Ke-</label>
                    <select id="terlambat-jam-ke" name="jam_ke" class="filter-select" required style="width:100%;margin-top:4px">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ ($jamAktif?->jam_ke == $i || ($jamAktif?->jam_ke >= 100 && $jamAktif?->jam_ke - 100 == $i)) ? 'selected' : ($i == 2 ? 'selected' : '') }}>
                                Jam ke-{{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div style="margin-bottom:14px">
                <label for="terlambat-alasan">Alasan Keterlambatan</label>
                <textarea id="terlambat-alasan" name="alasan" class="filter-input" rows="3" maxlength="2000" placeholder="Contoh: Ban bocor di jalan, kendala transportasi, mengantar orang tua, dll..." style="width:100%;margin-top:4px;resize:vertical"></textarea>
            </div>
            <div style="margin-bottom:14px">
                <label for="terlambat-foto">Foto surat / Bukti pendukung <small style="color:#94a3b8">(opsional)</small></label>
                <input id="terlambat-foto" type="file" name="foto_surat" accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;margin-top:6px;font-size:13px">
                <small style="display:block;color:#64748b;margin-top:5px">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</small>
            </div>
            <button type="submit" class="btn-primary btn-submit-terlambat">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Izinkan Siswa Masuk &amp; Sinkronkan Jurnal
            </button>
        </form>
    </div>

    <div class="card" style="margin-top:20px">
        <div class="card-header" style="margin-bottom:16px">
            <div class="card-title">Riwayat Siswa Terlambat Hari Ini</div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Waktu Datang</th>
                        <th>Mulai Masuk</th>
                        <th>Alasan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keterlambatanTerbaru ?? [] as $item)
                        <tr>
                            <td><strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong></td>
                            <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ substr($item->jam_masuk, 0, 5) }} WIB ({{ $item->tanggal->format('d-m-Y') }})</td>
                            <td><span class="badge badge-info">Jam ke-{{ $item->jam_ke }}</span></td>
                            <td>{{ $item->alasan ?: '-' }}</td>
                            <td>
                                @if($item->foto_surat)
                                    <a href="{{ Storage::disk('public')->url($item->foto_surat) }}" target="_blank" rel="noopener" style="color:#2563eb;font-weight:600;text-decoration:underline">Lihat Foto</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-warning" style="background:#ffedd5;color:#c2410c;">Diizinkan Masuk</span>
                            </td>
                            <td>
                                <button type="button" class="btn-secondary" style="padding:5px 10px;font-size:11.5px;color:#dc2626;border-color:#fecaca;" onclick="hapusKeterlambatan('{{ $item->id_keterlambatan }}')">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:#64748b;padding:22px">Belum ada data siswa terlambat hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function simpanSiswaTerlambat(event) {
    event.preventDefault();
    var form = document.getElementById('siswa-terlambat-form');
    var idSiswa = document.getElementById('terlambat-siswa').value;
    if (!idSiswa) {
        Swal.fire({icon: 'warning', title: 'Pilih Siswa', text: 'Silakan pilih nama siswa terlebih dahulu dari daftar pencarian.', confirmButtonColor: '#2563eb'});
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Perizinan Terlambat',
        text: 'Siswa akan diizinkan masuk kelas. Status di jurnal sebelum jam masuk otomatis diubah menjadi Masuk Terlambat, dan jam setelahnya menjadi Hadir.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Izinkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b'
    }).then(function(res) {
        if (!res.isConfirmed) return;

        fetch(@json(route('siswa-terlambat.store')), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(async function(r) {
            var d = await r.json();
            if (!r.ok) throw new Error(d.message || 'Gagal menyimpan perizinan keterlambatan.');
            return d;
        })
        .then(function(d) {
            Swal.fire({
                icon: 'success',
                title: 'Perizinan Berhasil Disimpan',
                text: d.message,
                confirmButtonColor: '#2563eb'
            }).then(function() {
                window.location.reload();
            });
        })
        .catch(function(e) {
            Swal.fire({icon: 'error', title: 'Gagal', text: e.message, confirmButtonColor: '#dc2626'});
        });
    });
}

function hapusKeterlambatan(id) {
    Swal.fire({
        title: 'Hapus Data Keterlambatan?',
        text: 'Data perizinan keterlambatan ini akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b'
    }).then(function(res) {
        if (!res.isConfirmed) return;

        fetch('/guru-piket/siswa-terlambat/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(async function(r) {
            var d = await r.json();
            if (!r.ok) throw new Error(d.message || 'Gagal menghapus data.');
            return d;
        })
        .then(function(d) {
            Swal.fire({icon: 'success', title: 'Berhasil Dihapus', text: d.message, confirmButtonColor: '#2563eb'})
                .then(function() { window.location.reload(); });
        })
        .catch(function(e) {
            Swal.fire({icon: 'error', title: 'Gagal', text: e.message, confirmButtonColor: '#dc2626'});
        });
    });
}

function toggleTerlambatSiswaDropdown(show) {
    var dd = document.getElementById('terlambat-siswa-dropdown');
    if (dd) dd.style.display = show ? 'block' : 'none';
}

function filterTerlambatSiswaDropdown(keyword) {
    var items = document.querySelectorAll('.terlambat-siswa-dropdown-item');
    var empty = document.getElementById('terlambat-siswa-empty');
    var q = keyword.toLowerCase().trim();
    var found = 0;
    items.forEach(function(item) {
        var match = item.dataset.search.includes(q);
        item.style.display = match ? '' : 'none';
        if (match) found++;
    });
    if (empty) empty.style.display = found === 0 ? 'block' : 'none';
    toggleTerlambatSiswaDropdown(true);
}

function pilihTerlambatSiswaDropdown(id, nama) {
    document.getElementById('terlambat-siswa').value = id;
    document.getElementById('terlambat-siswa-search').value = nama;
    toggleTerlambatSiswaDropdown(false);
}

document.addEventListener('click', function(e) {
    var search = document.getElementById('terlambat-siswa-search');
    if (search && search.parentElement && !search.parentElement.contains(e.target)) {
        toggleTerlambatSiswaDropdown(false);
    }
});
</script>
