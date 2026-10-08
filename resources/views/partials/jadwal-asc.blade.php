{{--
    Komponen lembar jadwal gaya aSc Timetables — satu tabel, baris = hari,
    kolom = posisi jam ke-N, sel berisi ruang / mapel / baris bawah.

    Dipakai oleh: admin (per kelas), guru (per guru), wali kelas (per kelas).

    Variabel yang dikirim saat @include:
      - $gridMap       : [hari][id_jam] = baris jadwal (field: nama_mapel,
                         nama_guru, nama_kelas, jam_mulai, jam_selesai, ruang?)
      - $jamPerHari    : [hari] = daftar jam_pelajaran terurut jam_ke
      - $hariUrutan    : urutan hari (Senin..Jumat)
      - $tahunAjaran   : objek tahun ajaran aktif (tahun_ajaran, semester)
      - $judulBaris2   : teks baris besar di bawah judul (nama kelas / guru)
      - $legenda       : (opsional) HTML legenda/keterangan di bawah judul
      - $barisBawah    : 'guru' (default) | 'kelas' | 'off'
      - $hariIni       : nama hari hari ini (opsional) → sorot baris
      - $sorotSekarang : bool (opsional) → sorot sel jam berjalan

    Catatan ruangan: field ruang/ruangan belum tersedia di tabel
    `jadwal_mengajar` (maupun di query backend), jadi selalu kosong.
    Bila ditambahkan nanti, cukup muncul otomatis lewat $cell->ruang.
--}}

@php
    $gridMap      = $gridMap ?? [];
    $barisBawah   = $barisBawah ?? 'guru';

    // Sorot baris hari ini (default: hari ini menurut kalender)
    $hariIni      = $hariIni ?? \App\Models\Hari::getNamaHariFromDayOfWeek(now()->dayOfWeekIso);
    $sorotSekarang = (bool) ($sorotSekarang ?? false);
    $jamSekarang  = now()->format('H:i:s');

    // Kolom = posisi jam ke-N pada tiap hari → header 1, 2, 3, ...
    $kolomPerHari = [];
    $jumlahKolom  = 0;
    foreach ($hariUrutan as $hNama) {
        $daftarJam           = $jamPerHari[$hNama] ?? [];
        $kolomPerHari[$hNama] = $daftarJam;
        $jumlahKolom         = max($jumlahKolom, count($daftarJam));
    }

    $semester       = strtoupper((string) ($tahunAjaran->semester ?? ''));
    $tahunPelajaran = strtoupper((string) ($tahunAjaran->tahun_ajaran ?? ''));
    $namaSekolah    = strtoupper((string) \App\Models\Pengaturan::get('nama_sekolah', 'SMKN 1 Boyolangu'));

    $ruanganDariBaris = function ($baris) {
        return trim((string) ($baris->ruang ?? $baris->ruangan ?? ''));
    };
@endphp

<div class="asc-sheet">
    <div class="asc-sheet-head">
        <div class="asc-sheet-title">
            JADWAL SEMESTER {{ $semester }} TAHUN PELAJARAN {{ $tahunPelajaran }} {{ $namaSekolah }}
        </div>
        <div class="asc-sheet-class">{{ $judulBaris2 ?? '' }}</div>
        @if(!empty($legenda))
            <div class="asc-sheet-legend">{!! $legenda !!}</div>
        @endif
    </div>

    <div class="asc-sheet-scroll">
        <table class="asc-table">
            <thead>
                <tr>
                    <th class="asc-corner" scope="col"></th>
                    @for ($kolom = 1; $kolom <= $jumlahKolom; $kolom++)
                        <th class="asc-period" scope="col">{{ $kolom }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($hariUrutan as $hNama)
                    @php $jamHari = $kolomPerHari[$hNama] ?? []; @endphp
                    <tr @if($hariIni === $hNama) class="is-today" @endif>
                        <th class="asc-day" scope="row">
                            <span>{{ $hNama }}</span>
                            @if($hariIni === $hNama)
                                <span class="asc-today-tag">Hari Ini</span>
                            @endif
                        </th>

                        @php $kolomKe = 0; @endphp
                        @while ($kolomKe < $jumlahKolom)
                            @php
                                $jamEntry = $jamHari[$kolomKe] ?? null;
                                $baris = $jamEntry
                                    ? ($gridMap[$hNama][$jamEntry->id_jam] ?? null)
                                    : null;
                                $ruangan = $baris ? $ruanganDariBaris($baris) : '';
                                $span    = 1;
                                $sedang   = false;

                                if ($baris) {
                                    $tanda = $baris->nama_mapel
                                        . '|' . ($baris->nama_guru ?? '')
                                        . '|' . ($baris->nama_kelas ?? '')
                                        . '|' . $ruangan;

                                    if (
                                        $sorotSekarang && $hariIni === $hNama
                                        && isset($baris->jam_mulai, $baris->jam_selesai)
                                        && $jamSekarang >= $baris->jam_mulai
                                        && $jamSekarang <= $baris->jam_selesai
                                    ) {
                                        $sedang = true;
                                    }

                                    while ($kolomKe + $span < $jumlahKolom) {
                                        $jamBerikut  = $jamHari[$kolomKe + $span] ?? null;
                                        $barisBerikut = $jamBerikut
                                            ? ($gridMap[$hNama][$jamBerikut->id_jam] ?? null)
                                            : null;
                                        if (!$barisBerikut) break;

                                        $tandaBerikut = $barisBerikut->nama_mapel
                                            . '|' . ($barisBerikut->nama_guru ?? '')
                                            . '|' . ($barisBerikut->nama_kelas ?? '')
                                            . '|' . $ruanganDariBaris($barisBerikut);
                                        if ($tandaBerikut !== $tanda) break;

                                        if (
                                            $sorotSekarang && $hariIni === $hNama
                                            && isset($barisBerikut->jam_mulai, $barisBerikut->jam_selesai)
                                            && $jamSekarang >= $barisBerikut->jam_mulai
                                            && $jamSekarang <= $barisBerikut->jam_selesai
                                        ) {
                                            $sedang = true;
                                        }

                                        $span++;
                                    }
                                }

                                $isUpacara = $baris && \App\Models\Mapel::isUpacaraName($baris->nama_mapel);
                                $tanpaGuru = $baris && !$isUpacara && empty($baris->id_guru);
                            @endphp

                            @if($baris)
                                <td class="asc-cell{{ $isUpacara ? ' is-upacara' : '' }}{{ $sedang ? ' is-now' : '' }}"
                                    @if($span > 1) colspan="{{ $span }}" @endif>
                                    <div class="asc-cell-body">
                                        @if($sedang)
                                            <span class="asc-now-dot" aria-hidden="true"></span>
                                        @endif
                                        <span class="asc-room">{{ $ruangan }}</span>
                                        <span class="asc-subject">{{ $baris->nama_mapel }}</span>
                                        @if($barisBawah === 'kelas')
                                            <span class="asc-teacher">{{ $baris->nama_kelas }}</span>
                                        @elseif($barisBawah !== 'off')
                                            <span class="asc-teacher{{ $tanpaGuru ? ' is-warning' : '' }}">
                                                @if($isUpacara)
                                                    Tidak Ada Guru
                                                @elseif($tanpaGuru)
                                                    Belum ada guru
                                                @else
                                                    {{ $baris->nama_guru }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            @else
                                <td class="asc-cell is-empty"></td>
                            @endif

                            @php $kolomKe += $span; @endphp
                        @endwhile
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
