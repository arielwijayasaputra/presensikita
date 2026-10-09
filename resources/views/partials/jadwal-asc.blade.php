{{--
    Komponen lembar jadwal gaya aSc Timetables — satu tabel, baris = hari,
    kolom = posisi jam ke-N, sel berisi ruang / mapel / baris bawah.

    Dipakai oleh: admin (per kelas), guru (per guru), wali kelas (per kelas).

    Dua tampilan dari data yang sama (tanpa query ganda):
      - Desktop (≥768px): tabel grid (.asc-sheet-scroll)
      - Mobile  (<768px): chip hari + timeline kartu (.asc-mobile)
    Keduanya merender dari $blokPerHari yang dihitung sekali di bawah.

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

    // ── Blok per hari: sel isi yang menyatu + deret jam kosong ────────────
    // Dihitung sekali, dipakai tabel desktop (colspan) & timeline mobile.
    $blokPerHari = [];
    foreach ($hariUrutan as $hNama) {
        $jamHari   = $kolomPerHari[$hNama] ?? [];
        $jumlahJam = count($jamHari);
        $entries   = [];
        $i = 0;

        while ($i < $jumlahJam) {
            $jamEntry = $jamHari[$i];
            $baris    = $gridMap[$hNama][$jamEntry->id_jam] ?? null;

            // Jam kosong → satukan satu entri (mobile: satu baris "Jam X-Y kosong")
            if (!$baris) {
                $j = $i;
                while ($j < $jumlahJam && !isset($gridMap[$hNama][$jamHari[$j]->id_jam])) {
                    $j++;
                }
                $entries[] = [
                    'tipe'        => 'kosong',
                    'span'        => $j - $i,
                    'jamKeDari'   => $jamHari[$i]->jam_ke,
                    'jamKeSampai' => $jamHari[$j - 1]->jam_ke,
                ];
                $i = $j;
                continue;
            }

            $ruangan = $ruanganDariBaris($baris);
            $tanda   = $baris->nama_mapel
                . '|' . ($baris->nama_guru ?? '')
                . '|' . ($baris->nama_kelas ?? '')
                . '|' . $ruangan;
            $span    = 1;
            $sedang  = $sorotSekarang && $hariIni === $hNama
                && isset($baris->jam_mulai, $baris->jam_selesai)
                && $jamSekarang >= $baris->jam_mulai
                && $jamSekarang <= $baris->jam_selesai;

            while ($i + $span < $jumlahJam) {
                $jamBerikut   = $jamHari[$i + $span];
                $barisBerikut = $gridMap[$hNama][$jamBerikut->id_jam] ?? null;
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

            $jamAkhir     = $jamHari[$i + $span - 1];
            $isUpacara    = \App\Models\Mapel::isUpacaraName($baris->nama_mapel);
            $entries[] = [
                'tipe'        => 'isi',
                'span'        => $span,
                'baris'       => $baris,
                'ruangan'     => $ruangan,
                'sedang'      => $sedang,
                'isUpacara'   => $isUpacara,
                'tanpaGuru'   => !$isUpacara && empty($baris->id_guru),
                'jamMulai'    => $jamEntry->jam_mulai,
                'jamSelesai'  => $jamAkhir->jam_selesai,
                'jamKeDari'   => $jamEntry->jam_ke,
                'jamKeSampai' => $jamAkhir->jam_ke,
            ];
            $i += $span;
        }

        // Sel padding bila jumlah jam hari ini < kolom tabel (desktop saja;
        // mobile tidak menampilkannya karena jam itu memang tidak ada)
        if ($jumlahJam < $jumlahKolom) {
            $entries[] = [
                'tipe'        => 'kosong',
                'span'        => $jumlahKolom - $jumlahJam,
                'jamKeDari'   => null,
                'jamKeSampai' => null,
            ];
        }

        $blokPerHari[$hNama] = $entries;
    }

    // ID unik tiap instance (admin merender partial untuk tiap kelas)
    static $ascUrut = 0;
    $ascUid = 'asc-m' . (++$ascUrut);

    // Hari aktif tab mobile: hari ini bila ada di daftar, else hari pertama
    $hariAktif = in_array($hariIni, $hariUrutan, true) ? $hariIni : ($hariUrutan[0] ?? null);
@endphp

<div class="asc-sheet asc-sheet-grid">
    <div class="asc-sheet-head">
        <div class="asc-sheet-title">
            JADWAL SEMESTER {{ $semester }} TAHUN PELAJARAN {{ $tahunPelajaran }} {{ $namaSekolah }}
        </div>
        <div class="asc-sheet-class">{{ $judulBaris2 ?? '' }}</div>
        @if(!empty($legenda))
            <div class="asc-sheet-legend">{!! $legenda !!}</div>
        @endif
    </div>

    {{-- ── Desktop: tabel grid aSc ── --}}
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
                    <tr @if($hariIni === $hNama) class="is-today" @endif>
                        <th class="asc-day" scope="row">
                            <span>{{ $hNama }}</span>
                            @if($hariIni === $hNama)
                                <span class="asc-today-tag">Hari Ini</span>
                            @endif
                        </th>

                        @foreach ($blokPerHari[$hNama] ?? [] as $blok)
                            @if($blok['tipe'] === 'isi')
                                @php
                                    $baris     = $blok['baris'];
                                    $ruangan   = $blok['ruangan'];
                                    $isUpacara = $blok['isUpacara'];
                                    $tanpaGuru = $blok['tanpaGuru'];
                                    $sedang    = $blok['sedang'];
                                @endphp
                                <td class="asc-cell{{ $isUpacara ? ' is-upacara' : '' }}{{ $sedang ? ' is-now' : '' }}"
                                    @if($blok['span'] > 1) colspan="{{ $blok['span'] }}" @endif>
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
                                @for ($k = 0; $k < $blok['span']; $k++)
                                    <td class="asc-cell is-empty"></td>
                                @endfor
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Mobile: chip hari (sticky) + timeline satu kartu per blok ── --}}
    <div class="asc-mobile">
        <div class="asc-m-tabs" role="tablist" aria-label="Pilih hari jadwal">
            @foreach ($hariUrutan as $idx => $hNama)
                <button type="button"
                    class="asc-m-tab{{ $hNama === $hariAktif ? ' is-active' : '' }}"
                    id="{{ $ascUid }}-tab-{{ $idx }}"
                    data-idx="{{ $idx }}"
                    role="tab"
                    aria-selected="{{ $hNama === $hariAktif ? 'true' : 'false' }}"
                    aria-controls="{{ $ascUid }}-panel-{{ $idx }}">
                    @if($hNama === $hariIni)<span class="asc-m-dot" aria-hidden="true"></span>@endif
                    {{ $hNama }}
                </button>
            @endforeach
        </div>

        <div class="asc-m-panels">
            @foreach ($hariUrutan as $idx => $hNama)
                @php
                    $blokHari  = $blokPerHari[$hNama] ?? [];
                    $adaJadwal = false;
                    foreach ($blokHari as $b) {
                        if ($b['tipe'] === 'isi') { $adaJadwal = true; break; }
                    }
                @endphp
                <div class="asc-m-panel{{ $hNama === $hariAktif ? ' is-active' : '' }}"
                    id="{{ $ascUid }}-panel-{{ $idx }}"
                    data-idx="{{ $idx }}"
                    role="tabpanel"
                    aria-labelledby="{{ $ascUid }}-tab-{{ $idx }}"
                    @if($hNama !== $hariAktif) hidden @endif>

                    @if(!$adaJadwal)
                        <div class="asc-m-empty">Tidak ada jadwal</div>
                    @else
                        @foreach ($blokHari as $blok)
                            @if($blok['tipe'] === 'kosong')
                                @if($blok['jamKeDari'] !== null)
                                    <div class="asc-m-gap">
                                        Jam {{ $blok['jamKeDari'] === $blok['jamKeSampai']
                                            ? $blok['jamKeDari']
                                            : $blok['jamKeDari'] . '-' . $blok['jamKeSampai'] }} kosong
                                    </div>
                                @endif
                            @else
                                @php
                                    $baris    = $blok['baris'];
                                    $bawahTeks = '';
                                    $bawahKelas = '';
                                    if ($barisBawah === 'kelas') {
                                        $bawahTeks = (string) ($baris->nama_kelas ?? '');
                                    } elseif ($barisBawah !== 'off') {
                                        if ($blok['isUpacara']) {
                                            $bawahTeks  = 'Tidak Ada Guru';
                                            $bawahKelas = ' is-warning';
                                        } elseif ($blok['tanpaGuru']) {
                                            $bawahTeks  = 'Belum ada guru';
                                            $bawahKelas = ' is-warning';
                                        } else {
                                            $bawahTeks = (string) ($baris->nama_guru ?? '');
                                        }
                                    }
                                    $ruangTeks = $blok['ruangan'];
                                    $jamKeTeks = $blok['jamKeDari'] === $blok['jamKeSampai']
                                        ? 'Jam ' . $blok['jamKeDari']
                                        : 'Jam ' . $blok['jamKeDari'] . '-' . $blok['jamKeSampai'];
                                @endphp
                                <article class="asc-m-card{{ $blok['isUpacara'] ? ' is-upacara' : '' }}{{ $blok['sedang'] ? ' is-now' : '' }}">
                                    <div class="asc-m-time">
                                        <span class="asc-m-range">
                                            {{ substr((string) $blok['jamMulai'], 0, 5) }} - {{ substr((string) $blok['jamSelesai'], 0, 5) }}
                                        </span>
                                        <span class="asc-m-jamke">{{ $jamKeTeks }}</span>
                                    </div>
                                    <div class="asc-m-main">
                                        <div class="asc-m-subject">
                                            @if($blok['sedang'])
                                                <span class="asc-now-dot" aria-hidden="true"></span>
                                            @endif
                                            <span>{{ $baris->nama_mapel }}</span>
                                        </div>
                                        @if($bawahTeks !== '' || $ruangTeks !== '')
                                            <div class="asc-m-meta{{ $bawahKelas }}">
                                                @if($bawahTeks !== '')<span>{{ $bawahTeks }}</span>@endif
                                                @if($ruangTeks !== '')<span class="asc-m-room">{{ $bawahTeks !== '' ? '· ' : '' }}{{ $ruangTeks }}</span>@endif
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
