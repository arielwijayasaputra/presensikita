<div class="page-content page-anim" id="page-riwayat-jurnal" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Riwayat Jurnal</div>
            <div class="page-subtitle">Seluruh jurnal mengajar yang telah Anda simpan.</div>
        </div>
    </div>

    <div class="table-card">
        <div style="overflow-x:auto">
            <table style="min-width:760px">
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center">No.</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th style="text-align:center">Materi</th>
                        <th style="text-align:center">Foto Selfie</th>
                        <th style="text-align:center">Hadir</th>
                        <th style="text-align:center">S</th>
                        <th style="text-align:center">I</th>
                        <th style="text-align:center">A</th>
                        <th style="text-align:center">Total Siswa</th>
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
                        <td style="color:#94a3b8;font-weight:600;text-align:center">{{ $idx + 1 }}</td>
                        <td style="font-weight:700;color:#1e293b">{{ $j->nama_kelas }}</td>
                        <td style="color:#475569">{{ date('d M Y', strtotime($j->tanggal)) }}</td>
                        <td style="color:#475569">{{ $j->materi ?? '-' }}</td>
                        <td style="text-align:center">
                            @if($j->foto_selfie)
                                <button type="button" onclick="showSelfiePopup('{{ Storage::disk('public')->url($j->foto_selfie) }}', '{{ $j->nama_kelas }} ({{ date('d M Y', strtotime($j->tanggal)) }})')" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;padding:4px 8px;font-size:11.5px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                    Lihat Foto
                                </button>
                            @else
                                <span style="color:#94a3b8;font-size:12px">-</span>
                            @endif
                        </td>
                        <td style="text-align:center;color:#16a34a;font-weight:700">{{ $j->jumlah_hadir }}</td>
                        <td style="text-align:center;color:#d97706;font-weight:600">{{ $s }}</td>
                        <td style="text-align:center;color:#2563eb;font-weight:600">{{ $i }}</td>
                        <td style="text-align:center;color:#dc2626;font-weight:600">{{ $a }}</td>
                        <td style="text-align:center;color:#475569;font-weight:700">{{ $total }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:30px;color:#94a3b8">Belum ada jurnal yang disimpan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
