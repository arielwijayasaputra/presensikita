<div class="page-content page-anim" id="page-guru-piket" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Guru Piket</div>
            <div class="page-subtitle">Tentukan guru yang dapat masuk sebagai Guru Piket pada tanggal tertentu.</div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px;max-width:900px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f1f5f9">
            <div style="width:42px;height:42px;background:#fff7ed;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#ea580c;flex-shrink:0">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
            </div>
            <div>
                <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Penugasan Harian</h3>
                <div style="font-size:12px;color:#64748b">Akun yang dipilih dapat login melalui menu Guru Piket pada tanggal tersebut.</div>
            </div>
        </div>

        <form id="guru-piket-form" onsubmit="simpanGuruPiket(event)">
            @csrf
            <div style="display:flex;align-items:end;gap:12px;margin-bottom:20px;flex-wrap:wrap">
                <div style="min-width:220px">
                    <label for="guru-piket-tanggal" style="font-size:12px;font-weight:600;color:#475569">Tanggal penugasan</label>
                    <input type="date" id="guru-piket-tanggal" name="tanggal" class="filter-input" value="{{ $guruPiketTanggal ?? now()->toDateString() }}" onchange="muatGuruPiketTanggal(this.value)" style="width:100%;margin-top:4px">
                </div>
                <span id="guru-piket-status" style="font-size:12px;color:#64748b;padding-bottom:9px">{{ count($guruPiketHariIni ?? []) }} guru ditugaskan hari ini</span>
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;flex-wrap:wrap">
                <div style="position:relative;flex:1;min-width:240px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);pointer-events:none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="search" id="guru-piket-search" class="filter-input" placeholder="Cari nama atau username guru..." aria-label="Cari guru piket" oninput="cariGuruPiket(this.value)" style="width:100%;padding-left:36px">
                </div>
                <span id="guru-piket-result-count" style="font-size:12px;color:#64748b;white-space:nowrap">{{ count($allGuruPiket) }} guru tersedia</span>
            </div>

            <div id="guru-piket-list" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:10px;margin-bottom:20px">
                @forelse($allGuruPiket as $guruItem)
                    <label class="guru-piket-item" data-search-text="{{ strtolower($guruItem->nama_guru . ' ' . $guruItem->username) }}" style="display:flex;align-items:center;gap:10px;border:1px solid #e2e8f0;border-radius:9px;padding:12px 14px;cursor:pointer;background:#fff">
                        <input type="checkbox" name="guru_ids[]" value="{{ $guruItem->id_guru }}" class="guru-piket-checkbox" {{ in_array($guruItem->id_guru, $guruPiketHariIni ?? []) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#ea580c">
                        <span>
                            <strong style="display:block;font-size:13px;color:#1e293b">{{ $guruItem->nama_guru }}</strong>
                            <small style="color:#94a3b8">{{ $guruItem->username }}</small>
                        </span>
                    </label>
                @empty
                    <div style="color:#64748b;font-size:13px">Belum ada guru aktif yang dapat ditugaskan.</div>
                @endforelse
            </div>
            <div id="guru-piket-no-result" style="display:none;color:#64748b;font-size:13px;padding:8px 0 20px">Guru yang dicari tidak ditemukan.</div>

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 18px;font-size:13px">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Penugasan
                </button>
                <span style="font-size:12px;color:#94a3b8">Kosongkan semua pilihan untuk membatalkan penugasan pada tanggal tersebut.</span>
            </div>
        </form>
    </div>
</div>

<script>
function simpanGuruPiket(event) {
    event.preventDefault();
    const form = document.getElementById('guru-piket-form');
    fetch(@json(route('guru-piket.update')), {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
        body: new FormData(form)
    }).then(async response => {
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Penugasan gagal disimpan.');
        await Swal.fire({icon: 'success', title: 'Tersimpan', text: result.message, confirmButtonColor: '#ea580c'});
        location.reload();
    }).catch(error => Swal.fire({icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626'}));
}

function muatGuruPiketTanggal(tanggal) {
    window.location.href = `${window.location.pathname}?guru_piket_tanggal=${encodeURIComponent(tanggal)}#guru-piket`;
}

function cariGuruPiket(keyword) {
    const normalizedKeyword = keyword.trim().toLowerCase();
    const items = document.querySelectorAll('.guru-piket-item');
    let visibleCount = 0;

    items.forEach(item => {
        const matches = item.dataset.searchText.includes(normalizedKeyword);
        item.style.display = matches ? 'flex' : 'none';
        if (matches) visibleCount++;
    });

    const countLabel = document.getElementById('guru-piket-result-count');
    const noResult = document.getElementById('guru-piket-no-result');
    if (countLabel) countLabel.textContent = `${visibleCount} guru ditemukan`;
    if (noResult) noResult.style.display = visibleCount ? 'none' : 'block';
}
</script>
