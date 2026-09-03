<div class="page-content page-anim" id="page-guru-piket" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Guru Piket</div>
            <div class="page-subtitle">Tentukan 4 guru piket untuk 2 minggu ke depan (Senin–Jumat).</div>
        </div>
    </div>

    <div class="card" style="padding:22px 24px;max-width:1100px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f1f5f9">
            <div style="width:42px;height:42px;background:#fff7ed;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#ea580c;flex-shrink:0">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg>
            </div>
            <div>
                <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Penugasan 2 Minggu</h3>
                <div style="font-size:12px;color:#64748b">Ketik nama guru pada tiap slot untuk mencari &amp; memilih guru piket.</div>
            </div>
        </div>

        <form id="guru-piket-form" onsubmit="simpanGuruPiketBulk(event)">
            @csrf

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;justify-content:flex-end">
                <span style="font-size:12px;color:#64748b">{{ count($allGuruPiket) }} guru tersedia — klik slot lalu ketik untuk mencari</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(480px,1fr));gap:16px;margin-bottom:20px">
                @foreach($guruPiketDates as $idx => $day)
                    @if($idx == 5)
                        <div style="grid-column:1/-1;border-top:2px solid #e2e8f0;margin:4px 0 8px"></div>
                    @endif
                    <div style="border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;background:#fafbfc">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #f1f5f9">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span style="font-size:13px;font-weight:700;color:#1e293b">{{ $day['hari'] }}</span>
                            <span style="font-size:11.5px;color:#64748b">{{ \Carbon\Carbon::parse($day['tanggal'])->format('d M Y') }}</span>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px">
                            @for($slot = 0; $slot < 4; $slot++)
                                @php($selectedId = $guruPiketAssignments[$day['tanggal']][$slot] ?? '')
                                <div class="gp-sd" data-slot>
                                    <input type="hidden" name="assignments[{{ $day['tanggal'] }}][]" value="{{ $selectedId }}">
                                    <label style="font-size:11px;color:#94a3b8;display:block;margin-bottom:3px">Guru {{ $slot + 1 }}</label>
                                    <button type="button" class="gp-sd-trigger filter-input" style="padding:7px 8px;font-size:12.5px;" data-trigger>
                                        <span class="gp-sd-value {{ $selectedId === '' ? 'empty' : '' }}">{{ $selectedId !== '' && isset($guruNameMap[$selectedId]) ? $guruNameMap[$selectedId] : '— Pilih Guru —' }}</span>
                                        @if($selectedId !== '')
                                            <span class="gp-sd-clear" role="button" tabindex="-1" data-clear>
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </span>
                                        @endif
                                        <svg class="gp-sd-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                    </button>
                                    <div class="gp-sd-panel" data-panel>
                                        <input type="text" class="gp-sd-search" placeholder="Cari nama guru..." data-search>
                                        <div class="gp-sd-list" data-list>
                                            @foreach($allGuruPiket as $g)
                                                <button type="button" class="gp-sd-option" data-value="{{ $g->id_guru }}" data-search-text="{{ strtolower($g->nama_guru . ' ' . $g->username) }}" data-guru-name="{{ $g->nama_guru }}">
                                                    {{ $g->nama_guru }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 18px;font-size:13px">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Semua Penugasan
                </button>
                <span style="font-size:12px;color:#94a3b8">Kosongkan semua pilihan pada hari tertentu untuk membatalkan penugasan di hari tersebut.</span>
            </div>
        </form>
    </div>
</div>

<script>
function simpanGuruPiketBulk(event) {
    event.preventDefault();
    const form = document.getElementById('guru-piket-form');
    fetch(@json(route('guru-piket.update-bulk')), {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
        body: new FormData(form),
        redirect: 'manual'
    }).then(async response => {
        if (response.type === 'opaqueredirect' || response.status === 0) {
            throw new Error('Sesi Anda telah berakhir. Silakan login kembali.');
        }
        const contentType = response.headers.get('content-type') || '';
        let result;
        if (contentType.includes('application/json')) {
            result = await response.json();
        } else {
            throw new Error('Terjadi kesalahan server (kode ' + response.status + '). Silakan coba lagi.');
        }
        if (!response.ok) throw new Error(result.message || 'Penugasan gagal disimpan.');
        await Swal.fire({icon: 'success', title: 'Tersimpan', text: result.message, confirmButtonColor: '#ea580c'});
        location.reload();
    }).catch(error => Swal.fire({icon: 'error', title: 'Gagal', text: error.message, confirmButtonColor: '#dc2626'}));
}

(function () {
    const form = document.getElementById('guru-piket-form');
    const root = form || document;

    function closeAll(except) {
        document.querySelectorAll('.gp-sd.open').forEach(sd => {
            if (except && sd === except) return;
            closeSlot(sd);
        });
    }

    function openSlot(sd) {
        const search = sd.querySelector('[data-search]');
        const panel = sd.querySelector('[data-panel]');
        sd.classList.add('open');
        if (search) { search.value = ''; filterOptions(sd, ''); }
        requestAnimationFrame(() => { if (search) search.focus(); });
    }

    function closeSlot(sd) {
        sd.classList.remove('open');
    }

    function setValue(sd, value, guruName) {
        const hidden = sd.querySelector('input[type="hidden"]');
        const valueEl = sd.querySelector('.gp-sd-value');
        const clearBtn = sd.querySelector('[data-clear]');
        if (hidden) hidden.value = value || '';
        if (valueEl) {
            valueEl.textContent = value ? guruName : '— Pilih Guru —';
            valueEl.classList.toggle('empty', !value);
        }
        let clear = sd.querySelector('.gp-sd-clear');
        if (!clear && value) {
            // reinsert clear button before caret if missing
            const caret = sd.querySelector('.gp-sd-caret');
            const btn = document.createElement('span');
            btn.className = 'gp-sd-clear';
            btn.setAttribute('role', 'button');
            btn.setAttribute('tabindex', '-1');
            btn.setAttribute('data-clear', '');
            btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
            if (caret) caret.parentNode.insertBefore(btn, caret);
            clear = btn;
        }
        if (clear) clear.style.display = value ? '' : 'none';
    }

    function filterOptions(sd, keyword) {
        const normalized = keyword.trim().toLowerCase();
        const list = sd.querySelector('[data-list]');
        let empty = true;
        list.querySelectorAll('.gp-sd-option').forEach(opt => {
            const match = opt.dataset.searchText.includes(normalized);
            opt.style.display = match ? '' : 'none';
            if (match) empty = false;
        });
        let emptyEl = list.querySelector('.gp-sd-empty');
        if (empty) {
            if (!emptyEl) {
                emptyEl = document.createElement('div');
                emptyEl.className = 'gp-sd-empty';
                emptyEl.textContent = 'Guru tidak ditemukan.';
                list.appendChild(emptyEl);
            }
            emptyEl.style.display = '';
        } else if (emptyEl) {
            emptyEl.style.display = 'none';
        }
    }

    // Open on trigger click
    root.addEventListener('click', e => {
        const trigger = e.target.closest('[data-trigger]');
        if (trigger) {
            const sd = trigger.closest('.gp-sd');
            const wasOpen = sd.classList.contains('open');
            closeAll(sd);
            if (!wasOpen) openSlot(sd);
            return;
        }
        // Ignore clicks inside panel (handled separately)
        if (e.target.closest('.gp-sd-panel')) return;
        // Clear button
        const clearBtn = e.target.closest('[data-clear]');
        if (clearBtn) {
            e.stopPropagation();
            const sd = clearBtn.closest('.gp-sd');
            setValue(sd, '', '');
            closeSlot(sd);
            return;
        }
        // Close if clicking outside any .gp-sd
        if (!e.target.closest('.gp-sd')) closeAll();
    });

    // Option selection (delegated, works for dynamically appended too)
    root.addEventListener('click', e => {
        const opt = e.target.closest('.gp-sd-option');
        if (!opt) return;
        const sd = opt.closest('.gp-sd');
        const value = opt.dataset.value;
        const guruName = opt.dataset.guruName;
        setValue(sd, value, guruName);
        // highlight selected
        sd.querySelectorAll('.gp-sd-option').forEach(o => o.classList.toggle('selected', o === opt));
        closeSlot(sd);
    });

    // Search input
    root.addEventListener('input', e => {
        const search = e.target.closest('[data-search]');
        if (search) {
            const sd = search.closest('.gp-sd');
            filterOptions(sd, search.value);
        }
    });

    // Keyboard navigation (Escape to close)
    root.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAll();
    });

    // Mark existing selections as selected on load
    document.querySelectorAll('.gp-sd').forEach(sd => {
        const hidden = sd.querySelector('input[type="hidden"]');
        const val = hidden ? hidden.value : '';
        if (val) {
            const opt = sd.querySelector('.gp-sd-option[data-value="' + val + '"]');
            if (opt) opt.classList.add('selected');
        }
    });
})();
</script>
