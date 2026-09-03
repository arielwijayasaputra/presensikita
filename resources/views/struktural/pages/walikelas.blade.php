<div class="page-content page-anim" id="page-walikelas" style="display:block">

    @php
        $namaKelasAktif = $waliKelasObj->nama_kelas ?? session('auth_nama_kelas', 'Kelas');
        $namaBulanList = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    @endphp

    <!-- ══ HEADER WALI KELAS ══ -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0">Dashboard Wali Kelas</h1>
                <span class="badge badge-primary" style="font-size:13px; padding:6px 12px; border-radius:8px">{{ $namaKelasAktif }}</span>
            </div>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0 0">
                Wali Kelas: <strong>{{ $guru->nama_guru ?? 'Guru' }}</strong> &bull; Total Murid: <strong>{{ $waliSiswaList->count() }} Siswa</strong>
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <!-- Filter Bulan & Tahun Rekap -->
            <form method="GET" action="{{ route('walikelas.index') }}" style="display:flex; align-items:center; gap:8px; background:#fff; padding:6px 12px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                <label style="font-size:12px; font-weight:700; color:#475569">Periode:</label>
                <select name="wali_bulan" onchange="this.form.submit()" class="filter-input" style="padding:4px 8px; font-size:12.5px; border-radius:6px; border:1px solid #cbd5e1">
                    @foreach($namaBulanList as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ $waliBulan == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>

                <select name="wali_tahun" onchange="this.form.submit()" class="filter-input" style="padding:4px 8px; font-size:12.5px; border-radius:6px; border:1px solid #cbd5e1">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $waliTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            <!-- Ekspor CSV -->
            <a href="{{ route('walikelas.export', ['bulan' => $waliBulan, 'tahun' => $waliTahun]) }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; background:linear-gradient(135deg, #059669, #10b981); text-decoration:none">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Ekspor Excel / CSV</span>
            </a>

            <!-- Cetak Laporan -->
            <button type="button" onclick="window.print()" class="btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700; border:1px solid #cbd5e1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- ══ KARTU RINGKASAN STATISTIK KELAS ══ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
        
        <div class="card" style="padding:20px; border-left:4px solid #3b82f6">
            <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px">Total Siswa Kelas</div>
            <div style="font-size:28px; font-weight:800; color:#1e293b; margin-top:6px">{{ $waliSiswaList->count() }}</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:2px">Siswa Aktif Terdaftar</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #10b981">
            <div style="font-size:12px; font-weight:700; color:#047857; text-transform:uppercase; letter-spacing:0.5px">Rata-rata Kehadiran</div>
            @php
                $pctKel = $waliRekapData['pct_hadir'] ?? 0;
            @endphp
            <div style="font-size:28px; font-weight:800; color:#065f46; margin-top:6px">{{ $pctKel }}%</div>
            <div style="font-size:12px; color:#10b981; margin-top:2px">Bulan {{ $namaBulanList[$waliBulan] ?? $waliBulan }}</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #f59e0b">
            <div style="font-size:12px; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.5px">Izin / Dispen Bulan Ini</div>
            @php
                $totalIzinSakit = ($waliRekapData['sakit'] ?? 0) + ($waliRekapData['izin'] ?? 0);
            @endphp
            <div style="font-size:28px; font-weight:800; color:#78350f; margin-top:6px">{{ $totalIzinSakit }}</div>
            <div style="font-size:12px; color:#f59e0b; margin-top:2px">Kasus Sakit / Izin</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #ef4444">
            <div style="font-size:12px; font-weight:700; color:#b91c1c; text-transform:uppercase; letter-spacing:0.5px">Perlu Perhatian</div>
            <div style="font-size:28px; font-weight:800; color:#991b1b; margin-top:6px">{{ count($siswaPerluPerhatian) }}</div>
            <div style="font-size:12px; color:#ef4444; margin-top:2px">Siswa (Sering Alpa / Izin)</div>
        </div>

    </div>

    <!-- ══ WIDGET EARLY WARNING PERINGATAN DINI SISWA ══ -->
    @if(count($siswaPerluPerhatian) > 0)
        <div class="card" style="padding:20px 24px; margin-bottom:28px; background:#fff5f5; border:1px solid #fecaca; border-left:5px solid #ef4444">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <strong style="font-size:15px; color:#991b1b">Peringatan Dini: Siswa Perlu Perhatian Khusus Wali Kelas</strong>
            </div>
            <p style="font-size:13px; color:#7f1d1d; margin:0 0 14px 0">
                Berikut adalah siswa di kelas <strong>{{ $namaKelasAktif }}</strong> yang memiliki akumulasi ketidakhadiran tinggi (Alpa &ge; 3 kali atau Sakit/Izin &ge; 5 kali) pada bulan {{ $namaBulanList[$waliBulan] ?? $waliBulan }}:
            </p>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:12px">
                @foreach($siswaPerluPerhatian as $sp)
                    <div style="background:#fff; padding:12px 14px; border-radius:10px; border:1px solid #fca5a5; display:flex; justify-content:space-between; align-items:center">
                        <div>
                            <strong style="font-size:13.5px; color:#0f172a; display:block">{{ $sp['nama_siswa'] }}</strong>
                            <div style="font-size:12px; color:#dc2626; margin-top:2px">
                                Alpa: <strong>{{ $sp['alpa'] }}</strong> &bull; Sakit: <strong>{{ $sp['sakit'] }}</strong> &bull; Izin: <strong>{{ $sp['izin'] }}</strong>
                            </div>
                        </div>
                        @if(!empty($sp['no_hp_ortu']))
                            @php
                                $hpWa = preg_replace('/[^0-9]/', '', $sp['no_hp_ortu']);
                                if(str_starts_with($hpWa, '0')) { $hpWa = '62' . substr($hpWa, 1); }
                                $msgWa = urlencode("Halo Bapak/Ibu Wali dari " . $sp['nama_siswa'] . ", saya " . ($guru->nama_guru ?? 'Wali Kelas') . " Wali Kelas " . $namaKelasAktif . ". Saya ingin menginformasikan terkait presensi siswa di sekolah.");
                            @endphp
                            <a href="https://wa.me/{{ $hpWa }}?text={{ $msgWa }}" target="_blank" class="btn-primary" style="font-size:11.5px; padding:6px 10px; border-radius:6px; background:#25d366; color:#fff; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:4px" title="Hubungi Orang Tua lewat WhatsApp">
                                WA Ortu
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ══ TABEL REKAPITULASI ABSENSI BULANAN SISWA KELAS ══ -->
    <div class="card" style="padding:22px 24px; margin-bottom:28px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Rekapitulasi Absensi Siswa Kelas {{ $namaKelasAktif }}
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">
                    Periode: <strong>{{ $namaBulanList[$waliBulan] ?? $waliBulan }} {{ $waliTahun }}</strong>
                </div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-rekap" onkeyup="filterTable('search-wali-rekap', 'table-wali-rekap')" placeholder="Cari nama / NISN..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-rekap" style="min-width:950px">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th style="text-align:center">Hadir (H)</th>
                        <th style="text-align:center">Sakit (S)</th>
                        <th style="text-align:center">Izin (I)</th>
                        <th style="text-align:center">Alpa (A)</th>
                        <th style="width:180px; text-align:center">Persentase Kehadiran</th>
                        <th style="text-align:center">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliRekapData['siswa'] ?? [] as $idx => $r)
                        <tr>
                            <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                            <td style="font-family:monospace; font-size:12.5px; color:#64748b">{{ $r['nisn'] ?? '-' }}</td>
                            <td><strong style="color:#0f172a">{{ $r['nama_siswa'] }}</strong></td>
                            <td style="text-align:center"><span class="badge badge-success" style="font-size:12px">{{ $r['hadir'] }}</span></td>
                            <td style="text-align:center"><span class="badge badge-warning" style="font-size:12px">{{ $r['sakit'] }}</span></td>
                            <td style="text-align:center"><span class="badge badge-info" style="font-size:12px">{{ $r['izin'] }}</span></td>
                            <td style="text-align:center">
                                @if($r['alpa'] > 0)
                                    <span class="badge badge-danger" style="font-size:12px">{{ $r['alpa'] }}</span>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">0</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <div style="display:flex; align-items:center; gap:8px; justify-content:center">
                                    <div style="flex:1; background:#e2e8f0; height:8px; border-radius:4px; overflow:hidden">
                                        <div style="width:{{ $r['persentase'] }}%; height:100%; background:{{ $r['persentase'] >= 85 ? '#10b981' : ($r['persentase'] >= 70 ? '#f59e0b' : '#ef4444') }}"></div>
                                    </div>
                                    <span style="font-size:12px; font-weight:700; color:#334155; width:45px; text-align:right">{{ $r['persentase'] }}%</span>
                                </div>
                            </td>
                            <td style="text-align:center; font-size:12.5px; font-weight:600; color:#475569">
                                {{ $r['keterangan'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada data rekapitulasi untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ TABEL DATA SISWA & KONTAK ORANG TUA ══ -->
    <div class="card" style="padding:22px 24px; margin-bottom:28px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Buku Induk &amp; Kontak Orang Tua Siswa (Kelas {{ $namaKelasAktif }})
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">Daftar siswa beserta nomor kontak orang tua untuk koordinasi wali kelas</div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-siswa" onkeyup="filterTable('search-wali-siswa', 'table-wali-siswa')" placeholder="Cari nama / NISN..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-siswa" style="min-width:850px">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NISN</th>
                        <th>Nama Lengkap Siswa</th>
                        <th style="text-align:center">L/P</th>
                        <th>No. HP Orang Tua / Wali</th>
                        <th style="text-align:center">Aksi Kontak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waliSiswaList as $idx => $s)
                        <tr>
                            <td style="color:#94a3b8; font-weight:600">{{ $idx + 1 }}</td>
                            <td style="font-family:monospace; font-size:12.5px; color:#64748b">{{ $s->nisn ?? '-' }}</td>
                            <td><strong style="color:#0f172a">{{ $s->nama_siswa }}</strong></td>
                            <td style="text-align:center">{{ $s->jenis_kelamin ?? 'L' }}</td>
                            <td style="font-family:monospace; font-size:12.5px">{{ $s->no_hp_ortu ?? '-' }}</td>
                            <td style="text-align:center">
                                @if(!empty($s->no_hp_ortu))
                                    @php
                                        $hpWa = preg_replace('/[^0-9]/', '', $s->no_hp_ortu);
                                        if(str_starts_with($hpWa, '0')) { $hpWa = '62' . substr($hpWa, 1); }
                                        $msgWa = urlencode("Halo Bapak/Ibu Wali dari " . $s->nama_siswa . ", saya " . ($guru->nama_guru ?? 'Wali Kelas') . " Wali Kelas " . $namaKelasAktif . " SMKN 1 Boyolangu.");
                                    @endphp
                                    <a href="https://wa.me/{{ $hpWa }}?text={{ $msgWa }}" target="_blank" class="btn-primary" style="font-size:12px; padding:5px 12px; border-radius:8px; background:#25d366; color:#fff; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:6px">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                                        Hubungi WA
                                    </a>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada data siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══ TABEL RIWAYAT IZIN & DISPENSASI SISWA KELAS ══ -->
    <div class="card" style="padding:22px 24px">
        <div class="card-header" style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px">
            <div>
                <div class="card-title" style="font-size:16px; font-weight:700; color:#0f172a">
                    Riwayat Izin, Sakit &amp; Dispensasi Siswa
                </div>
                <div style="font-size:12px; color:#64748b; margin-top:2px">Pengajuan izin dan surat keterangan dokter/dispensasi siswa kelas {{ $namaKelasAktif }}</div>
            </div>

            <div style="position:relative; width:250px">
                <input type="text" id="search-wali-dispen" onkeyup="filterTable('search-wali-dispen', 'table-wali-dispen')" placeholder="Cari nama / alasan..." class="filter-input" style="width:100%; padding:7px 12px 7px 32px; font-size:12.5px; border-radius:8px; border:1px solid #cbd5e1">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:10px; top:9px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="data-table" id="table-wali-dispen" style="min-width:850px">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th style="text-align:center">Jenis Absen</th>
                        <th>Alasan / Keterangan</th>
                        <th style="text-align:center">Foto Surat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispenDanIzinKelas as $d)
                        <tr>
                            <td><strong>{{ $d->tanggal_dispen->format('d-m-Y') }}</strong></td>
                            <td><strong style="color:#0f172a">{{ $d->siswa->nama_siswa ?? '-' }}</strong></td>
                            <td style="text-align:center">
                                @if($d->jenis_absen === 'S')
                                    <span class="badge badge-warning">Sakit</span>
                                @elseif($d->jenis_absen === 'I')
                                    <span class="badge badge-info">Izin</span>
                                @elseif($d->jenis_absen === 'D')
                                    <span class="badge badge-primary">Dispensasi</span>
                                @else
                                    <span class="badge badge-secondary">{{ $d->jenis_absen }}</span>
                                @endif
                            </td>
                            <td>{{ $d->alasan }}</td>
                            <td style="text-align:center">
                                @if($d->foto_surat)
                                    <a href="#" onclick="showSuratPopup('{{ Storage::disk('public')->url($d->foto_surat) }}'); return false;" style="font-size:12.5px; font-weight:600; color:#2563eb">
                                        Lihat Surat
                                    </a>
                                @else
                                    <span style="color:#94a3b8; font-size:12px">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:#64748b; padding:22px">
                                Belum ada riwayat izin atau dispensasi siswa kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ══ SCRIPT FOR LIVE SEARCHING & POPUP ══ -->
<script>
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const trs = table.getElementsByTagName("tr");

    for (let i = 1; i < trs.length; i++) {
        let textContent = trs[i].textContent || trs[i].innerText;
        if (textContent.toLowerCase().indexOf(filter) > -1) {
            trs[i].style.display = "";
        } else {
            trs[i].style.display = "none";
        }
    }
}

function showSuratPopup(url) {
    Swal.fire({
        title: 'Foto Surat Izin / Keterangan',
        imageUrl: url,
        imageAlt: 'Foto Surat',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#475569',
        customClass: {
            image: 'swal-popup-image'
        }
    });
}
</script>

<style>
.swal-popup-image {
    max-height: 70vh !important;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@media print {
    .sidebar, .header, .btn-primary, .btn-secondary, form, .filter-input, #search-wali-rekap, #search-wali-siswa, #search-wali-dispen {
        display: none !important;
    }
    .main-content, .page-content {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
}
</style>
