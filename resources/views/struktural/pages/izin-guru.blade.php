<div class="page-content page-anim" id="page-izin-guru" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div><div class="page-title" style="font-size:22px;font-weight:800">Permintaan Izin Guru</div><div class="page-subtitle">Kirim permintaan izin untuk mendapatkan persetujuan Kepsek dan Waka.</div></div>
    </div>

    @php
        $pendingIzinList = $izinMenungguKonfirmasi ?? $izinGuruMenungguPiket ?? collect();
    @endphp
    @if($isGuruPiket && $pendingIzinList->count() > 0)
    <div class="card" style="padding:22px 24px;margin-bottom:24px;border:1.5px solid #fbbf24;background:#fffdfa;max-width:900px">
        <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="background:#fef3c7;color:#d97706;padding:7px;border-radius:8px;display:flex;align-items:center;justify-content:center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
                <div>
                    <div class="card-title" style="color:#92400e;font-size:16px">Permintaan Izin Guru Masuk</div>
                    <div style="font-size:12.5px;color:#b45309">Ada {{ $pendingIzinList->count() }} permohonan izin guru yang menunggu konfirmasi Anda untuk diteruskan ke Kepala Sekolah & Waka SDM via WhatsApp.</div>
                </div>
            </div>
            <span class="badge badge-warning" style="font-size:12px;padding:5px 10px;font-weight:700">{{ $pendingIzinList->count() }} Menunggu</span>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:760px">
                <thead>
                    <tr>
                        <th>Tanggal Izin</th>
                        <th>Nama Guru</th>
                        <th>Alasan</th>
                        <th>Surat</th>
                        <th style="text-align:center">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingIzinList as $izinPiket)
                        <tr>
                            <td><strong style="color:var(--text, #0f172a)">{{ $izinPiket->tanggal_izin->format('d-m-Y') }}</strong></td>
                            <td><strong style="color:#2563eb">{{ $izinPiket->guru->nama_guru ?? '-' }}</strong></td>
                            <td>{{ $izinPiket->alasan }}</td>
                            <td>
                                @if($izinPiket->foto_surat)
                                    <a href="{{ Storage::disk('public')->url($izinPiket->foto_surat) }}" target="_blank" rel="noopener" style="color:#2563eb;text-decoration:none;font-weight:600">Lihat Foto</a>
                                @else
                                    <span style="color:#94a3b8">-</span>
                                @endif
                            </td>
                            <td style="text-align:center">
                                <button type="button" onclick="konfirmasiIzinPiket({{ $izinPiket->id_izin_guru }}, '{{ addslashes($izinPiket->guru->nama_guru ?? '') }}')" class="btn-primary" style="background:#16a34a;border-color:#16a34a;font-size:12px;padding:6px 14px;border-radius:7px;display:inline-flex;align-items:center;gap:6px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Konfirmasi & Teruskan ke WA
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card" style="padding:22px 24px;max-width:900px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Buat Permintaan Izin Guru</div></div>
        <form id="izin-guru-form" onsubmit="buatLinkIzinGuru(event)">@csrf
            @if($isGuruPiket)
                <div style="display:grid;grid-template-columns:1fr 180px;gap:14px;margin-bottom:14px">
                    <div>
                        <label for="izin-id-guru">Guru yang meminta izin</label>
                        <input type="hidden" id="izin-id-guru" name="id_guru" required>
                        <div style="position:relative;margin-top:4px">
                            <input type="text" id="izin-guru-search" class="filter-input" placeholder="Ketik nama guru..." autocomplete="off" required style="width:100%"
                                onfocus="filterGuruDropdown(this.value)"
                                onclick="filterGuruDropdown(this.value)"
                                oninput="filterGuruDropdown(this.value)"
                                onkeyup="filterGuruDropdown(this.value)">
                            <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            <div id="izin-guru-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:var(--card-bg, #ffffff);color:var(--text, #0f172a);border:1px solid var(--border, #cbd5e1);border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:999;box-shadow:0 10px 25px rgba(0,0,0,0.25)">
                                @foreach($guruAktif as $guruPilihan)
                                    <div class="guru-dropdown-item" data-id="{{ $guruPilihan->id_guru }}" data-nama="{{ strtolower($guruPilihan->nama_guru) }}"
                                        onclick="pilihGuruDropdown('{{ $guruPilihan->id_guru }}', '{{ addslashes($guruPilihan->nama_guru) }}')"
                                        style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border, #f1f5f9);transition:background 0.15s;color:inherit"
                                        onmouseenter="this.style.background='var(--hover-bg, rgba(255,255,255,0.06))'" onmouseleave="this.style.background='transparent'">
                                        {{ $guruPilihan->nama_guru }}
                                    </div>
                                @endforeach
                                <div id="izin-guru-empty" style="display:none;padding:14px;text-align:center;color:#94a3b8;font-size:13px">Guru tidak ditemukan</div>
                            </div>
                        </div>
                    </div>
                    <div><label for="izin-tanggal">Tanggal izin</label><input type="date" id="izin-tanggal" name="tanggal_izin" class="filter-input" value="{{ now()->toDateString() }}" required style="width:100%;margin-top:4px"></div>
                </div>
            @else
                <div style="display:grid;grid-template-columns:1fr 180px;gap:14px;margin-bottom:14px"><div><label>Guru yang meminta izin</label><input type="text" class="filter-input" value="{{ session('auth_nama_guru') }}" readonly style="width:100%;margin-top:4px;background:#f8fafc"></div><div><label for="izin-tanggal">Tanggal izin</label><input type="date" id="izin-tanggal" name="tanggal_izin" class="filter-input" value="{{ now()->toDateString() }}" required style="width:100%;margin-top:4px"></div></div>
            @endif
            <div style="margin-bottom:14px"><label for="izin-alasan">Alasan izin</label><textarea id="izin-alasan" name="alasan" class="filter-input" rows="3" required maxlength="2000" placeholder="Tuliskan alasan tidak dapat mengajar..." style="width:100%;margin-top:4px;resize:vertical"></textarea></div>
            <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 16px;font-size:13px">Buat Link Persetujuan</button>
        </form>
        <div id="izin-link-result" style="display:none;margin-top:16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:14px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:#1d4ed8">Link persetujuan terpisah</div>
                <div id="izin-wa-status-badge"></div>
            </div>
            <div style="display:grid;gap:8px">
                <div style="display:flex;gap:8px;align-items:center">
                    <strong style="width:58px;font-size:12px">Kepsek</strong>
                    <input id="izin-kepsek-link" class="filter-input" readonly style="flex:1;font-size:12px">
                    <button type="button" class="btn-secondary" onclick="salinLinkIzin('izin-kepsek-link')" style="font-size:12px">Salin</button>
                    <a id="izin-kepsek-wa-btn" href="#" target="_blank" class="btn-secondary" style="font-size:12px;display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;text-decoration:none">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Kirim WA
                    </a>
                </div>
                <div style="display:flex;gap:8px;align-items:center">
                    <strong style="width:58px;font-size:12px">Waka</strong>
                    <input id="izin-waka-link" class="filter-input" readonly style="flex:1;font-size:12px">
                    <button type="button" class="btn-secondary" onclick="salinLinkIzin('izin-waka-link')" style="font-size:12px">Salin</button>
                    <a id="izin-waka-wa-btn" href="#" target="_blank" class="btn-secondary" style="font-size:12px;display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;text-decoration:none">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Kirim WA
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="padding:22px 24px;margin-top:20px">
        <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div class="card-title">Status Permintaan Izin</div>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-izin-guru-tbl" oninput="filterTable('search-izin-guru-tbl','table-izin-guru-tbl')" onkeyup="filterTable('search-izin-guru-tbl','table-izin-guru-tbl')" placeholder="Cari nama guru / alasan..." class="filter-input" style="padding:7px 12px 7px 32px;font-size:12.5px;border-radius:8px;border:1px solid #cbd5e1;width:210px">
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" id="table-izin-guru-tbl" style="min-width:980px">
                <thead><tr><th>Guru</th><th>Tanggal</th><th>Alasan</th><th>Status Kepsek</th><th>Status Waka</th><th style="text-align:center">Tanda Tangan</th><th>Link</th></tr></thead>
                <tbody>
                    @forelse($izinGuruTerbaru as $izin)
                        <tr>
                            <td><strong>{{ $izin->guru->nama_guru ?? '-' }}</strong></td>
                            <td>{{ $izin->tanggal_izin->format('d-m-Y') }}</td>
                            <td>{{ $izin->alasan }}</td>
                            <td>{{ ucfirst($izin->status_kepsek) }}</td>
                            <td>{{ ucfirst($izin->status_waka) }}</td>
                            <td style="text-align:center">
                                <div style="display:inline-flex;gap:4px;flex-wrap:wrap;justify-content:center">
                                    @if($izin->tanda_tangan_kepsek)
                                        <button type="button" onclick="showSignaturePopup('{{ Storage::disk('public')->url($izin->tanda_tangan_kepsek) }}', 'Izin: {{ $izin->guru->nama_guru ?? '' }}', 'Tanda Tangan Kepala Sekolah')" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:6px;padding:3px 7px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5a1 1 0 0 1-1.4.4l-4-2a1 1 0 0 1 .3-1.8l10-3z"/><path d="M2 19l7-7 2 2 5-5 6 6v4a1 1 0 0 1-1 1l-9 2-4 2z"/></svg>
                                            Kepsek
                                        </button>
                                    @endif
                                    @if($izin->tanda_tangan_waka)
                                        <button type="button" onclick="showSignaturePopup('{{ Storage::disk('public')->url($izin->tanda_tangan_waka) }}', 'Izin: {{ $izin->guru->nama_guru ?? '' }}', 'Tanda Tangan Waka SDM')" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;padding:3px 7px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:3px">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5a1 1 0 0 1-1.4.4l-4-2a1 1 0 0 1 .3-1.8l10-3z"/><path d="M2 19l7-7 2 2 5-5 6 6v4a1 1 0 0 1-1 1l-9 2-4 2z"/></svg>
                                            Waka
                                        </button>
                                    @endif
                                    @if(!$izin->tanda_tangan_kepsek && !$izin->tanda_tangan_waka)
                                        <span style="color:#94a3b8;font-size:12px">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a href="{{ URL::temporarySignedRoute('izin-guru.public.role', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek'], false) }}" target="_blank" style="font-size:12px;margin-right:8px">Kepsek</a>
                                <a href="{{ URL::temporarySignedRoute('izin-guru.public.role', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka'], false) }}" target="_blank" style="font-size:12px">Waka</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:#64748b;padding:22px">Belum ada permintaan izin.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function buatLinkIzinGuru(event) {
    event.preventDefault();
    fetch(@json($isGuruPiket ? route('izin-guru.store') : route('guru.izin-guru.store')), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: new FormData(document.getElementById('izin-guru-form'))
    })
    .then(async response => {
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Permintaan gagal dibuat.');
        return result;
    })
    .then(result => {
        document.getElementById('izin-kepsek-link').value = result.kepsek_link;
        document.getElementById('izin-waka-link').value = result.waka_link;

        const waKepsekUrl = result.wa_notification?.kepsek?.wa_me_link || ('https://wa.me/?text=' + encodeURIComponent(result.kepsek_link));
        const waWakaUrl = result.wa_notification?.waka_sdm?.wa_me_link || ('https://wa.me/?text=' + encodeURIComponent(result.waka_link));

        const btnKepsek = document.getElementById('izin-kepsek-wa-btn');
        const btnWaka = document.getElementById('izin-waka-wa-btn');
        if (btnKepsek) btnKepsek.href = waKepsekUrl;
        if (btnWaka) btnWaka.href = waWakaUrl;

        const statusBadge = document.getElementById('izin-wa-status-badge');
        if (statusBadge) {
            const kepsekSent = result.wa_notification?.kepsek?.sent;
            const wakaSent = result.wa_notification?.waka_sdm?.sent;
            if (kepsekSent && wakaSent) {
                statusBadge.innerHTML = '<span class="badge badge-success" style="font-size:11px;padding:3px 8px">● WA Terkirim ke Kepsek & Waka</span>';
            } else if (kepsekSent || wakaSent) {
                statusBadge.innerHTML = '<span class="badge badge-warning" style="font-size:11px;padding:3px 8px">● WA Terkirim Sebagian</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge badge-secondary" style="font-size:11px;padding:3px 8px">WA Belum Terkirim Otomatis</span>';
            }
        }

        document.getElementById('izin-link-result').style.display = 'block';
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Dibuat!',
            text: result.message,
            confirmButtonColor: '#ea580c'
        });
    })
    .catch(error => Swal.fire({icon:'error',title:'Gagal',text:error.message,confirmButtonColor:'#dc2626'}));
}
function salinLinkIzin(inputId) { navigator.clipboard.writeText(document.getElementById(inputId).value).then(() => Swal.fire({icon:'success',title:'Link disalin',timer:1200,showConfirmButton:false})); }

function toggleGuruDropdown(show) {
    const dd = document.getElementById('izin-guru-dropdown');
    if (!dd) return;
    dd.style.display = show ? 'block' : 'none';
}

function filterGuruDropdown(keyword) {
    const items = document.querySelectorAll('.guru-dropdown-item');
    const empty = document.getElementById('izin-guru-empty');
    const q = (keyword || '').toLowerCase().trim();
    let found = 0;
    items.forEach(item => {
        const match = !q || (item.dataset.nama || '').includes(q);
        item.style.display = match ? '' : 'none';
        if (match) found++;
    });
    if (empty) empty.style.display = found === 0 ? 'block' : 'none';
    toggleGuruDropdown(true);
}

function pilihGuruDropdown(id, nama) {
    document.getElementById('izin-id-guru').value = id;
    document.getElementById('izin-guru-search').value = nama;
    toggleGuruDropdown(false);
}

document.addEventListener('click', function(e) {
    const wrapper = document.querySelector('[style*="position:relative"] [id="izin-guru-search"]');
    if (wrapper && !wrapper.closest('div[style*="position:relative"]').contains(e.target)) {
        toggleGuruDropdown(false);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('izin-guru-search');
    if (search && search.value) {
        const items = document.querySelectorAll('.guru-dropdown-item');
        items.forEach(item => {
            if (item.dataset.nama === search.value.toLowerCase()) {
                document.getElementById('izin-id-guru').value = item.dataset.id;
            }
        });
    }
});

function konfirmasiIzinPiket(id, namaGuru){
    Swal.fire({
        title: 'Konfirmasi Izin Guru',
        html: `Konfirmasi permohonan izin dari <strong>${namaGuru}</strong>?<br><br><span style="font-size:12.5px;color:#64748b">Setelah dikonfirmasi, sistem akan otomatis mengirim notifikasi WhatsApp dan tautan persetujuan kepada Kepala Sekolah & Waka SDM.</span><br><br><textarea id="swal-catatan-piket-izin" class="swal2-textarea" placeholder="Catatan piket (opsional)..." style="margin:0;width:100%;font-size:13px" rows="2"></textarea>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Konfirmasi & Kirim WA',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const catatan = document.getElementById('swal-catatan-piket-izin')?.value || '';
            const baseUrl = @json(url('/guru-piket/izin-guru'));
            return fetch(baseUrl + '/' + id + '/konfirmasi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ catatan_piket: catatan })
            })
            .then(async r => {
                const d = await r.json();
                if (!r.ok) throw new Error(d.message || 'Gagal mengonfirmasi izin.');
                return d;
            })
            .catch(err => {
                Swal.showValidationMessage(err.message);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dikonfirmasi',
                text: result.value.message,
                confirmButtonColor: '#2563eb'
            }).then(() => location.reload());
        }
    });
}
</script>
