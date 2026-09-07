<div class="page-content page-anim" id="page-izin-guru" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div><div class="page-title" style="font-size:22px;font-weight:800">Permintaan Izin Guru</div><div class="page-subtitle">Kirim permintaan izin untuk mendapatkan persetujuan Kepsek dan Waka.</div></div>
    </div>
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
                                onfocus="toggleGuruDropdown(true)"
                                oninput="filterGuruDropdown(this.value)">
                            <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            <div id="izin-guru-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:50;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                                @foreach($guruAktif as $guruPilihan)
                                    <div class="guru-dropdown-item" data-id="{{ $guruPilihan->id_guru }}" data-nama="{{ strtolower($guruPilihan->nama_guru) }}"
                                        onclick="pilihGuruDropdown('{{ $guruPilihan->id_guru }}', '{{ addslashes($guruPilihan->nama_guru) }}')"
                                        style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9;transition:background 0.15s"
                                        onmouseenter="this.style.background='#f1f5f9'" onmouseleave="this.style.background='#fff'">
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
    <div class="card" style="padding:22px 24px;margin-top:20px"><div class="card-header" style="margin-bottom:16px"><div class="card-title">Status Permintaan Izin</div></div><div style="overflow-x:auto"><table class="data-table" style="min-width:980px"><thead><tr><th>Guru</th><th>Tanggal</th><th>Alasan</th><th>Status Kepsek</th><th>Status Waka</th><th>Link</th></tr></thead><tbody>@forelse($izinGuruTerbaru as $izin)<tr><td><strong>{{ $izin->guru->nama_guru ?? '-' }}</strong></td><td>{{ $izin->tanggal_izin->format('d-m-Y') }}</td><td>{{ $izin->alasan }}</td><td>{{ ucfirst($izin->status_kepsek) }}</td><td>{{ ucfirst($izin->status_waka) }}</td><td><a href="{{ URL::temporarySignedRoute('izin-guru.public.role', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'kepsek']) }}" target="_blank" style="font-size:12px;margin-right:8px">Kepsek</a><a href="{{ URL::temporarySignedRoute('izin-guru.public.role', now()->addDays(2), ['izin' => $izin->id_izin_guru, 'role' => 'waka']) }}" target="_blank" style="font-size:12px">Waka</a></td></tr>@empty<tr><td colspan="6" style="text-align:center;color:#64748b;padding:22px">Belum ada permintaan izin.</td></tr>@endforelse</tbody></table></div></div>
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
    const q = keyword.toLowerCase().trim();
    let found = 0;
    items.forEach(item => {
        const match = item.dataset.nama.includes(q);
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
</script>
