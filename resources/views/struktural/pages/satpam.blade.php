<div class="page-content page-anim" id="page-satpam" style="display:block">
    <div class="greeting-row">
        <div class="greeting-text">
            <h2>Dashboard Satpam</h2>
            <p>{{ $hariIni }}, {{ now()->format('d F Y') }} · {{ $namaSekolah }}</p>
        </div>
        <div class="header-date" style="background:#f1f5f9;border-color:#cbd5e1;color:#334155">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
            {{ now()->format('H:i') }} WIB
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="stat-cards" style="margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="stat-label">Total Dispen Hari Ini</div>
                <div class="stat-value" id="stat-total">{{ $dispenHariIni->count() }}</div>
                <div class="stat-sub">siswa</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed;color:#ea580c">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2 2 2M12 12v6"/><circle cx="12" cy="7" r="1"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M4 21v-2a4 4 0 0 1 3-3.87"/></svg>
            </div>
            <div>
                <div class="stat-label">Sedang Di Luar</div>
                <div class="stat-value" id="stat-diluar">
                    {{ $dispenHariIni->filter(fn($d) => $d->waktu_keluar && !$d->waktu_masuk)->count() }}
                </div>
                <div class="stat-sub">siswa</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;color:#16a34a">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="stat-label">Sudah Kembali</div>
                <div class="stat-value" id="stat-kembali">
                    {{ $dispenHariIni->filter(fn($d) => $d->waktu_masuk)->count() }}
                </div>
                <div class="stat-sub">siswa</div>
            </div>
        </div>
    </div>

    <!-- MAIN CARD TABLE -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
            <div>
                <div class="card-title">Daftar Log Keluar-Masuk Siswa Dispensasi</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">Menampilkan siswa yang memiliki izin dispensasi khusus hari ini.</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <span class="card-action">Hari Ini</span>
                <button type="button" class="btn-secondary" onclick="showPage('satpam-harian')" style="border-radius:8px;padding:6px 12px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Lihat Riwayat &amp; Data Harian
                </button>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:900px">
                <thead>
                    <tr>
                        <th>Siswa &amp; Kelas</th>
                        <th>Alasan</th>
                        <th>Status Waka</th>
                        <th>Foto Surat</th>
                        <th>Status Keluar/Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Waktu Masuk</th>
                        <th style="width:150px; text-align:center">Aksi Satpam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispenHariIni as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong>
                                <div style="font-size:12px;color:#64748b">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</div>
                            </td>
                            <td>{{ $item->alasan }}</td>
                            <td>
                                @if($item->status_waka === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($item->status_waka === 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($item->foto_surat)
                                    <a href="#" onclick="showSuratPopup('{{ Storage::disk('public')->url($item->foto_surat) }}'); return false;" style="font-size:12.5px;font-weight:600;color:#2563eb">
                                        Lihat Foto
                                    </a>
                                @else
                                    <span style="color:#94a3b8">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->waktu_masuk)
                                    <span class="badge badge-success badge-status-satpam">Sudah Kembali</span>
                                @elseif($item->waktu_keluar)
                                    <span class="badge badge-warning badge-status-satpam">Di Luar</span>
                                @else
                                    <span class="badge badge-info badge-status-satpam">Belum Keluar</span>
                                @endif
                            </td>
                            <td class="waktu-keluar">
                                @if($item->waktu_keluar)
                                    <span class="badge badge-info">{{ $item->waktu_keluar->format('H:i:s') }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12.5px">-</span>
                                @endif
                            </td>
                            <td class="waktu-masuk">
                                @if($item->waktu_masuk)
                                    <span class="badge badge-success">{{ $item->waktu_masuk->format('H:i:s') }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12.5px">-</span>
                                @endif
                            </td>
                            <td style="text-align:center" class="aksi-satpam">
                                @if($item->waktu_masuk)
                                    <span style="color:#64748b;font-size:12px;font-weight:600">Selesai</span>
                                @elseif($item->waktu_keluar)
                                    <button type="button" class="btn-satpam-masuk" onclick="prosesMasuk({{ $item->id_dispen_siswa }}, this)">
                                        Izinkan Masuk
                                    </button>
                                @else
                                    <button type="button" class="btn-satpam-keluar" onclick="prosesKeluar({{ $item->id_dispen_siswa }}, this)">
                                        Izinkan Keluar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:#64748b;padding:28px">
                                Tidak ada siswa yang dispen hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-satpam-keluar {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    transition: all 0.2s;
    font-family: inherit;
}
.btn-satpam-keluar:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(59, 130, 246, 0.4);
}
.btn-satpam-masuk {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    transition: all 0.2s;
    font-family: inherit;
}
.btn-satpam-masuk:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(16, 185, 129, 0.4);
}
.swal-popup-image {
    max-height: 70vh !important;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<script>
function updateStatCounters() {
    // Re-calculate statistics client-side based on badges
    const rows = document.querySelectorAll('tbody tr');
    let total = 0;
    let diluar = 0;
    let kembali = 0;

    rows.forEach(row => {
        const badge = row.querySelector('.badge-status-satpam');
        if (badge) {
            total++;
            if (badge.textContent.trim() === 'Di Luar') {
                diluar++;
            } else if (badge.textContent.trim() === 'Sudah Kembali') {
                kembali++;
            }
        }
    });

    const elTotal = document.getElementById('stat-total');
    const elDiluar = document.getElementById('stat-diluar');
    const elKembali = document.getElementById('stat-kembali');

    if (elTotal) elTotal.textContent = total;
    if (elDiluar) elDiluar.textContent = diluar;
    if (elKembali) elKembali.textContent = kembali;
}

function prosesKeluar(id, btn) {
    Swal.fire({
        title: 'Izinkan Siswa Keluar?',
        text: 'Waktu keluar siswa akan dicatat pada sistem.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            fetch('{{ url("/satpam/izinkan-keluar") }}/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal memproses request.');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                const row = btn.closest('tr');

                // Update Badge Keluar/Masuk
                const statusBadge = row.querySelector('.badge-status-satpam');
                if (statusBadge) {
                    statusBadge.className = 'badge badge-warning badge-status-satpam';
                    statusBadge.textContent = 'Di Luar';
                }

                // Update Kolom Waktu Keluar
                const colWaktuKeluar = row.querySelector('.waktu-keluar');
                if (colWaktuKeluar) {
                    colWaktuKeluar.innerHTML = `<span class="badge badge-info">${data.waktu_keluar}</span>`;
                }

                // Update Kolom Aksi
                const colAksi = row.querySelector('.aksi-satpam');
                if (colAksi) {
                    colAksi.innerHTML = `
                        <button type="button" class="btn-satpam-masuk" onclick="prosesMasuk(${id}, this)">
                            Izinkan Masuk
                        </button>
                    `;
                }

                updateStatCounters();
            })
            .catch(err => {
                btn.disabled = false;
                btn.textContent = 'Izinkan Keluar';
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: err.message
                });
            });
        }
    });
}

function prosesMasuk(id, btn) {
    Swal.fire({
        title: 'Izinkan Siswa Kembali?',
        text: 'Waktu masuk kembali siswa akan dicatat pada sistem.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Masuk',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            fetch('{{ url("/satpam/izinkan-masuk") }}/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal memproses request.');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                const row = btn.closest('tr');

                // Update Badge Keluar/Masuk
                const statusBadge = row.querySelector('.badge-status-satpam');
                if (statusBadge) {
                    statusBadge.className = 'badge badge-success badge-status-satpam';
                    statusBadge.textContent = 'Sudah Kembali';
                }

                // Update Kolom Waktu Masuk
                const colWaktuMasuk = row.querySelector('.waktu-masuk');
                if (colWaktuMasuk) {
                    colWaktuMasuk.innerHTML = `<span class="badge badge-success">${data.waktu_masuk}</span>`;
                }

                // Update Kolom Aksi
                const colAksi = row.querySelector('.aksi-satpam');
                if (colAksi) {
                    colAksi.innerHTML = `<span style="color:#64748b;font-size:12px;font-weight:600">Selesai</span>`;
                }

                updateStatCounters();
            })
            .catch(err => {
                btn.disabled = false;
                btn.textContent = 'Izinkan Masuk';
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: err.message
                });
            });
        }
    });
}

function showSuratPopup(url) {
    Swal.fire({
        title: 'Foto Surat Dispensasi',
        imageUrl: url,
        imageAlt: 'Foto Surat Dispensasi',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#475569',
        customClass: {
            image: 'swal-popup-image'
        }
    });
}
</script>
