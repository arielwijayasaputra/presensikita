<div class="page-content page-anim" id="page-wali-absensi-harian" style="display:none">

    @php
        $namaKelasAktif = $waliKelasObj->nama_kelas ?? session('auth_nama_kelas', 'Kelas');
    @endphp

    <!-- ══ HEADER ABSENSI HARIAN REAL TIME ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Absensi Harian Kelas</h1>
                <span class="badge badge-primary" style="font-size:13px; padding:6px 12px; border-radius:8px">{{ $namaKelasAktif }}</span>
                <span class="badge badge-success" style="font-size:12px; padding:6px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:6px">
                    <span style="width:8px; height:8px; background:#22c55e; border-radius:50%; display:inline-block; animation:pulse 1.5s infinite"></span>
                    Real Time Hari Ini
                </span>
            </div>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">
                Pantau &amp; Kelola presensi siswa kelas <strong>{{ $namaKelasAktif }}</strong> hari ini: <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <button type="button" onclick="location.reload()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                <span>Muat Ulang Data</span>
            </button>

            <button type="button" onclick="simpanAbsensiHarianWaliSubmit()" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #2563eb, #3b82f6)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span>Simpan Presensi Hari Ini</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK HARIAN ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px; border-left:4px solid #3b82f6">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Siswa</div>
            <div style="font-size:26px; font-weight:800; color:#1e293b; margin-top:4px">{{ $waliStatsHariIni['total_siswa'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#94a3b8; margin-top:2px">Siswa Terdaftar</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #10b981">
            <div style="font-size:11.5px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Hadir Hari Ini</div>
            <div style="font-size:26px; font-weight:800; color:#065f46; margin-top:4px">{{ $waliStatsHariIni['hadir'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#10b981; margin-top:2px">{{ $waliStatsHariIni['pct_hadir'] ?? 0 }}% Kehadiran Siswa</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #f59e0b">
            <div style="font-size:11.5px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px">Sakit Hari Ini</div>
            <div style="font-size:26px; font-weight:800; color:#78350f; margin-top:4px">{{ $waliStatsHariIni['sakit'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#f59e0b; margin-top:2px">Siswa Sakit</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #06b6d4">
            <div style="font-size:11.5px; font-weight:700; color:#0891b2; text-transform:uppercase; letter-spacing:0.5px">Izin / Dispen</div>
            <div style="font-size:26px; font-weight:800; color:#164e63; margin-top:4px">{{ ($waliStatsHariIni['izin'] ?? 0) + ($waliStatsHariIni['dispensasi'] ?? 0) }}</div>
            <div style="font-size:11.5px; color:#06b6d4; margin-top:2px">Izin &amp; Surat Dispen</div>
        </div>

        <div class="card" style="padding:18px; border-left:4px solid #ef4444">
            <div style="font-size:11.5px; font-weight:700; color:#b91c1c; text-transform:uppercase; letter-spacing:0.5px">Alpa Hari Ini</div>
            <div style="font-size:26px; font-weight:800; color:#991b1b; margin-top:4px">{{ $waliStatsHariIni['alpa'] ?? 0 }}</div>
            <div style="font-size:11.5px; color:#ef4444; margin-top:2px">Tanpa Keterangan</div>
        </div>
    </div>

    <!-- ══ TABEL PRESENSI SISWA REAL TIME ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Daftar Presensi Siswa Real-Time Hari Ini
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Ubah status presensi per siswa jika ada perbaikan data wali kelas
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
                <div style="display:flex; gap:6px">
                    <button type="button" onclick="tandaiSemuaWali('H')" class="btn-secondary" style="font-size:11.5px; padding:5px 10px; border-radius:6px; border:1px solid #cbd5e1">Set Semuar Hadir</button>
                </div>

                <div style="position:relative; width:230px">
                    <input type="text" id="search-wali-harian" onkeyup="filterTable('search-wali-harian', 'table-wali-harian')" placeholder="Cari nama / NISN..." class="filter-input" style="width:100%; padding:6px 12px 6px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:8px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>
        </div>

        <form id="form-wali-absensi-harian">
            <div style="overflow-x:auto">
                <table class="data-table" id="table-wali-harian" style="min-width:900px">
                    <thead>
                        <tr>
                            <th style="width:45px">No</th>
                            <th>NISN</th>
                            <th>Nama Lengkap Siswa</th>
                            <th style="text-align:center; width:50px">L/P</th>
                            <th style="text-align:center; width:220px">Status Presensi Hari Ini</th>
                            <th>Keterangan</th>
                            <th style="text-align:center; width:130px">Aksi Ortu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waliAbsensiHariIniList as $idx => $s)
                            <tr>
                                <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                                <td style="font-family:monospace; font-size:12.5px; color:#64748b">{{ $s['nisn'] }}</td>
                                <td><strong style="color:#0f172a">{{ $s['nama_siswa'] }}</strong></td>
                                <td style="text-align:center">{{ $s['jenis_kelamin'] }}</td>
                                <td style="text-align:center">
                                    <div style="display:flex; justify-content:center; gap:8px">
                                        <label style="cursor:pointer; display:inline-flex; align-items:center; gap:3px; font-size:12px; font-weight:600; color:#15803d">
                                            <input type="radio" name="absensi[{{ $s['id_siswa'] }}][status]" value="H" {{ $s['status'] === 'H' ? 'checked' : '' }} style="accent-color:#22c55e"> H
                                        </label>
                                        <label style="cursor:pointer; display:inline-flex; align-items:center; gap:3px; font-size:12px; font-weight:600; color:#b45309">
                                            <input type="radio" name="absensi[{{ $s['id_siswa'] }}][status]" value="S" {{ $s['status'] === 'S' ? 'checked' : '' }} style="accent-color:#f59e0b"> S
                                        </label>
                                        <label style="cursor:pointer; display:inline-flex; align-items:center; gap:3px; font-size:12px; font-weight:600; color:#1d4ed8">
                                            <input type="radio" name="absensi[{{ $s['id_siswa'] }}][status]" value="I" {{ $s['status'] === 'I' ? 'checked' : '' }} style="accent-color:#3b82f6"> I
                                        </label>
                                        <label style="cursor:pointer; display:inline-flex; align-items:center; gap:3px; font-size:12px; font-weight:600; color:#b91c1c">
                                            <input type="radio" name="absensi[{{ $s['id_siswa'] }}][status]" value="A" {{ $s['status'] === 'A' ? 'checked' : '' }} style="accent-color:#ef4444"> A
                                        </label>
                                        @if($s['status'] === 'D')
                                            <label style="display:inline-flex; align-items:center; gap:3px; font-size:12px; font-weight:600; color:#0e7490">
                                                <input type="radio" name="absensi[{{ $s['id_siswa'] }}][status]" value="D" checked style="accent-color:#06b6d4"> D
                                            </label>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="absensi[{{ $s['id_siswa'] }}][keterangan]" value="{{ $s['keterangan'] }}" placeholder="Catatan/Keterangan..." style="width:100%; padding:5px 8px; font-size:12px; border-radius:6px; border:1px solid #cbd5e1">
                                </td>
                                <td style="text-align:center">
                                    @if(!empty($s['no_hp_ortu']))
                                        @php
                                            $hpWa = preg_replace('/[^0-9]/', '', $s['no_hp_ortu']);
                                            if(str_starts_with($hpWa, '0')) { $hpWa = '62' . substr($hpWa, 1); }
                                            $msgWa = urlencode("Halo Bapak/Ibu Wali dari " . $s['nama_siswa'] . ", menginformasikan presensi siswa hari ini.");
                                        @endphp
                                        <a href="https://wa.me/{{ $hpWa }}?text={{ $msgWa }}" target="_blank" style="font-size:11.5px; padding:4px 8px; border-radius:6px; background:#25d366; color:#fff; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:4px">
                                            WA Ortu
                                        </a>
                                    @else
                                        <span style="color:#94a3b8; font-size:12px">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color:#64748b; padding:24px">
                                    Belum ada data siswa terdaftar di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

</div>

<script>
function tandaiSemuaWali(val) {
    const radios = document.querySelectorAll(`#table-wali-harian input[type="radio"][value="${val}"]`);
    radios.forEach(r => r.checked = true);
}

function simpanAbsensiHarianWaliSubmit() {
    const form = document.getElementById('form-wali-absensi-harian');
    const formData = new FormData(form);

    Swal.fire({
        title: 'Menyimpan Presensi Harian...',
        text: 'Sedang memproses penyimpanan presensi siswa hari ini',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('{{ route("walikelas.simpan-absensi") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonColor: '#2563eb'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan.',
            });
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan koneksi server.',
        });
    });
}
</script>
