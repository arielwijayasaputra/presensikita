<div class="page-content page-anim" id="page-guru-piket" style="display:none">
    <div class="page-header" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Guru Piket</div>
            <div class="page-subtitle">Tentukan 4 guru piket untuk 2 minggu ke depan (Senin–Jumat).</div>
        </div>
        <div>
            <button type="button" class="btn-primary" onclick="bukaModalPengaturanWaPiket()" style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:9px;font-size:13px;font-weight:600;background:#059669;border-color:#059669">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Pengaturan No WA Bot Notifikasi
            </button>
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

    {{-- Modal Pengaturan Nomor WhatsApp Bot / Guru Piket --}}
    <div id="modal-wa-guru-piket" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:220;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(2px)">
        <div class="card" style="width:100%;max-width:540px;max-height:90vh;overflow-y:auto;padding:24px;border-radius:14px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f1f5f9">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:40px;height:40px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#059669;flex-shrink:0">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0">Pengaturan Nomor WA Notifikasi</h3>
                        <div style="font-size:12px;color:#64748b;margin-top:2px">Nomor tujuan notifikasi izin guru &amp; dispensasi siswa</div>
                    </div>
                </div>
                <button type="button" onclick="tutupModalPengaturanWaPiket()" aria-label="Tutup" style="border:0;background:none;font-size:24px;color:#94a3b8;cursor:pointer;padding:0;line-height:1">&times;</button>
            </div>

            <form id="form-wa-guru-piket" onsubmit="simpanPengaturanWaPiket(event)" style="display:grid;gap:14px">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none">
                        <input type="checkbox" id="modal-set-wa-gateway-aktif" {{ ($waGatewayAktif ?? '1') === '1' ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#059669">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#1e293b">Aktifkan Notifikasi WhatsApp Otomatis</div>
                            <div style="font-size:11.5px;color:#64748b">Kirim link persetujuan langsung saat Guru Piket input surat izin/dispensasi.</div>
                        </div>
                    </label>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:4px">
                        Nomor WhatsApp Kepala Sekolah
                        <span style="font-size:11px;color:#059669;font-weight:normal">(Persetujuan Izin Guru)</span>
                    </label>
                    <input type="text" class="filter-input" id="modal-set-wa-kepsek" value="{{ $waNomorKepsek ?? '' }}" placeholder="Contoh: 081234567890" style="width:100%">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:4px">
                        Nomor WhatsApp Waka SDM / Kurikulum
                        <span style="font-size:11px;color:#059669;font-weight:normal">(Persetujuan Izin Guru)</span>
                    </label>
                    <input type="text" class="filter-input" id="modal-set-wa-waka-sdm" value="{{ $waNomorWakaSdm ?? '' }}" placeholder="Contoh: 081234567890" style="width:100%">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:4px">
                        Nomor WhatsApp Waka Kesiswaan
                        <span style="font-size:11px;color:#059669;font-weight:normal">(Persetujuan Dispen Siswa)</span>
                    </label>
                    <input type="text" class="filter-input" id="modal-set-wa-waka-kesiswaan" value="{{ $waNomorWakaKesiswaan ?? '' }}" placeholder="Contoh: 081234567890" style="width:100%">
                </div>

                {{-- Status & Kontrol Server Bot --}}
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px;display:grid;gap:10px">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div style="font-size:12px;font-weight:700;color:#334155">Status Server Bot WhatsApp:</div>
                        <span class="badge {{ ($waBotStatus['online'] ?? false) ? 'badge-success' : 'badge-danger' }}" id="modal-bot-status-badge" style="font-size:11.5px;padding:4px 10px;display:inline-flex;align-items:center;gap:5px">
                            <span style="width:7px;height:7px;background:{{ ($waBotStatus['online'] ?? false) ? '#22c55e' : '#ef4444' }};border-radius:50%;display:inline-block"></span>
                            {{ ($waBotStatus['online'] ?? false) ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    {{-- Box QR Code jika butuh scan --}}
                    <div id="modal-bot-qr-box" style="display:none;background:#fff;border:1px dashed #cbd5e1;border-radius:8px;padding:12px;text-align:center">
                        <div style="font-size:12px;font-weight:600;color:#1e293b;margin-bottom:6px">Scan QR Code dengan WhatsApp di HP Anda:</div>
                        <div id="modal-bot-qr-img-wrap" style="display:flex;justify-content:center;margin-bottom:6px">
                            <img id="modal-bot-qr-img" src="" alt="Scan QR Code" style="width:200px;height:200px;border-radius:8px;border:1px solid #e2e8f0">
                        </div>
                        <div style="font-size:11px;color:#64748b">Buka WhatsApp &rarr; Perangkat Tertaut &rarr; Tautkan Perangkat</div>
                    </div>

                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button type="button" class="btn-secondary" id="btn-hubungkan-bot" onclick="startAtauRestartBot()" style="flex:1;min-width:130px;border-radius:8px;padding:8px 10px;font-size:12px;display:flex;align-items:center;justify-content:center;gap:5px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Hubungkan Ulang Bot
                        </button>
                        <button type="button" class="btn-secondary" onclick="cekStatusBotWaModal()" style="flex:1;min-width:100px;border-radius:8px;padding:8px 10px;font-size:12px;display:flex;align-items:center;justify-content:center;gap:5px">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            Cek Status
                        </button>
                        <button type="button" class="btn-secondary" onclick="modalTestKirimWa()" style="flex:1;min-width:100px;border-radius:8px;padding:8px 10px;font-size:12px;display:flex;align-items:center;justify-content:center;gap:5px;background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Test Kirim WA
                        </button>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:4px;padding-top:14px;border-top:1px solid #f1f5f9">
                    <button type="button" class="btn-secondary" onclick="tutupModalPengaturanWaPiket()" style="border-radius:8px;padding:9px 18px;font-size:13px">Batal</button>
                    <button type="submit" class="btn-primary" id="btn-simpan-wa-piket" style="border-radius:8px;padding:9px 20px;font-size:13px;font-weight:600;background:#059669;border-color:#059669">
                        Simpan Nomor WA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bukaModalPengaturanWaPiket() {
    const modal = document.getElementById('modal-wa-guru-piket');
    if (modal) {
        modal.style.display = 'flex';
        cekStatusBotWaModal(false);
    }
}

function tutupModalPengaturanWaPiket() {
    const modal = document.getElementById('modal-wa-guru-piket');
    if (modal) {
        modal.style.display = 'none';
    }
}

function cekStatusBotWaModal(showAlert = true) {
    const badge = document.getElementById('modal-bot-status-badge');
    const qrBox = document.getElementById('modal-bot-qr-box');
    const qrImg = document.getElementById('modal-bot-qr-img');

    if (badge) {
        badge.className = 'badge badge-info';
        badge.innerHTML = '<span style="width:7px;height:7px;background:#3b82f6;border-radius:50%;display:inline-block"></span> Memeriksa...';
    }

    fetch(@json(route('pengaturan.qr-wa')), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'connected') {
            if (badge) {
                badge.className = 'badge badge-success';
                badge.style.cssText = 'font-size:11.5px;padding:4px 10px;display:inline-flex;align-items:center;gap:5px';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block"></span> Online' + (data.user ? ' (' + data.user + ')' : '');
            }
            if (qrBox) qrBox.style.display = 'none';
            if (showAlert) {
                Swal.fire({ icon: 'success', title: 'Bot WhatsApp Online!', text: data.message || 'Bot terhubung dan siap digunakan.', timer: 2000, showConfirmButton: false });
            }
        } else if (data.status === 'waiting_qr' && data.qr_image) {
            if (badge) {
                badge.className = 'badge badge-warning';
                badge.style.cssText = 'font-size:11.5px;padding:4px 10px;display:inline-flex;align-items:center;gap:5px;background:#fef3c7;color:#92400e;border:1px solid #fde68a';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#f59e0b;border-radius:50%;display:inline-block"></span> Menunggu Scan QR';
            }
            if (qrBox && qrImg) {
                qrImg.src = data.qr_image;
                qrBox.style.display = 'block';
            }
            if (showAlert) {
                Swal.fire({ icon: 'info', title: 'Perlu Scan QR Code', text: 'Silakan scan QR Code yang muncul di layar dengan aplikasi WhatsApp Anda.' });
            }
        } else {
            if (badge) {
                badge.className = 'badge badge-danger';
                badge.style.cssText = 'font-size:11.5px;padding:4px 10px;display:inline-flex;align-items:center;gap:5px';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block"></span> Offline';
            }
            if (qrBox) qrBox.style.display = 'none';
            if (showAlert) {
                Swal.fire({ icon: 'warning', title: 'Bot Offline', text: data.message || 'Server bot WhatsApp belum aktif. Klik "Hubungkan Ulang Bot" untuk menyalakan.', confirmButtonColor: '#ea580c' });
            }
        }
    })
    .catch(err => {
        if (badge) {
            badge.className = 'badge badge-danger';
            badge.innerHTML = '<span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block"></span> Offline';
        }
        if (qrBox) qrBox.style.display = 'none';
        if (showAlert) {
            Swal.fire({ icon: 'warning', title: 'Bot Belum Aktif', text: 'Server bot belum berjalan. Klik tombol "Hubungkan Ulang Bot" untuk mengaktifkan.', confirmButtonColor: '#ea580c' });
        }
    });
}

function startAtauRestartBot() {
    const btn = document.getElementById('btn-hubungkan-bot');
    if (btn) btn.disabled = true;

    Swal.fire({
        title: 'Menghubungkan Bot...',
        text: 'Memulai server bot WhatsApp di background...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(@json(route('pengaturan.restart-wa')), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(() => {
        setTimeout(() => {
            Swal.close();
            cekStatusBotWaModal(true);
        }, 2500);
    })
    .catch(() => {
        setTimeout(() => {
            Swal.close();
            cekStatusBotWaModal(true);
        }, 2000);
    })
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

function simpanPengaturanWaPiket(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-simpan-wa-piket');
    if (btn) btn.disabled = true;

    const kepsek = document.getElementById('modal-set-wa-kepsek')?.value?.trim() ?? '';
    const wakaSdm = document.getElementById('modal-set-wa-waka-sdm')?.value?.trim() ?? '';
    const wakaKesiswaan = document.getElementById('modal-set-wa-waka-kesiswaan')?.value?.trim() ?? '';
    const gatewayAktif = document.getElementById('modal-set-wa-gateway-aktif')?.checked ? 1 : 0;

    fetch(@json(route('pengaturan.update-wa')), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            wa_nomor_kepsek: kepsek,
            wa_nomor_waka_sdm: wakaSdm,
            wa_nomor_waka_kesiswaan: wakaKesiswaan,
            wa_gateway_aktif: gatewayAktif
        })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan pengaturan WhatsApp.');
        return data;
    })
    .then(data => {
        // Sinkronkan ke input di halaman pengaturan utama jika elemen ada
        const setKepsek = document.getElementById('set-wa-kepsek');
        const setWakaSdm = document.getElementById('set-wa-waka-sdm');
        const setWakaKesiswaan = document.getElementById('set-wa-waka-kesiswaan');
        const setGwAktif = document.getElementById('set-wa-gateway-aktif');
        if (setKepsek) setKepsek.value = kepsek;
        if (setWakaSdm) setWakaSdm.value = wakaSdm;
        if (setWakaKesiswaan) setWakaKesiswaan.value = wakaKesiswaan;
        if (setGwAktif) setGwAktif.checked = !!gatewayAktif;

        tutupModalPengaturanWaPiket();
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: data.message || 'Pengaturan nomor WhatsApp notifikasi berhasil diperbarui.',
            timer: 2200,
            showConfirmButton: false
        });
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            text: err.message || 'Terjadi kesalahan saat menyimpan pengaturan.'
        });
    })
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

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
