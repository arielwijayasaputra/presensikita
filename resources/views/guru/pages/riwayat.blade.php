<div class="page-content page-anim" id="page-riwayat-jurnal" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Riwayat Jurnal</div>
            <div class="page-subtitle">Seluruh jurnal mengajar yang telah Anda simpan.</div>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">No.</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Materi</th>
                    <th>Hadir</th>
                    <th>S</th>
                    <th>I</th>
                    <th>A</th>
                    <th>Total Siswa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatJurnal as $idx => $j)
                @php
                    $rincian = $tidakHadirPerJurnal->get($j->id_jurnal, collect());
                    $s = $rincian->get('S', 0);
                    $i = $rincian->get('I', 0);
                    $a = $rincian->get('A', 0);
                    $total = $j->jumlah_hadir + $s + $i + $a;
                @endphp
                <tr>
                    <td style="color:#94a3b8;font-weight:600">{{ $idx + 1 }}</td>
                    <td style="font-weight:700;color:#1e293b">{{ $j->nama_kelas }}</td>
                    <td style="color:#475569">{{ date('d M Y', strtotime($j->tanggal)) }}</td>
                    <td style="color:#475569">{{ $j->materi ?? '-' }}</td>
                    <td style="text-align:center;color:#16a34a;font-weight:700">{{ $j->jumlah_hadir }}</td>
                    <td style="text-align:center;color:#d97706;font-weight:600">{{ $s }}</td>
                    <td style="text-align:center;color:#2563eb;font-weight:600">{{ $i }}</td>
                    <td style="text-align:center;color:#dc2626;font-weight:600">{{ $a }}</td>
                    <td style="text-align:center;color:#475569;font-weight:600">{{ $total }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:30px;color:#94a3b8">Belum ada jurnal yang disimpan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
