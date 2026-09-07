<div class="page-content page-anim" id="page-pengaturan-wa" style="display:none">
    <div class="page-header" style="margin-bottom:24px">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;width:100%">
            <div>
                <div class="page-title" style="font-size:22px;font-weight:800;display:flex;align-items:center;gap:10px">
                    <div style="width:38px;height:38px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#059669">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    Pengaturan WhatsApp Bot Gateway
                </div>
                <div class="page-subtitle" style="margin-top:4px">Kelola integrasi bot WhatsApp, nomor penerima notifikasi persetujuan izin guru &amp; dispensasi siswa.</div>
            </div>
            <div>
                @if(($waBotStatus['online'] ?? false))
                    <span class="badge badge-success" id="piket-wa-bot-badge" style="font-size:12px;padding:6px 14px;display:inline-flex;align-items:center;gap:6px">
                        <span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block"></span> Bot Online
                    </span>
                @else
                    <span class="badge badge-danger" id="piket-wa-bot-badge" style="font-size:12px;padding:6px 14px;display:inline-flex;align-items:center;gap:6px">
                        <span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block"></span> Bot Offline
                    </span>
                @endif
            </div>
        </div>
    </div>

    <form id="form-piket-pengaturan-wa" onsubmit="simpanPengaturanWaPiketPage(event)" style="display:grid;gap:20px;max-width:960px">
        @csrf

        {{-- ── CARD 1: Status & Opsi Gateway ── --}}
        <div class="card" style="padding:22px 24px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                <div style="width:38px;height:38px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Layanan &amp; Endpoint Bot</h3>
                    <div style="font-size:12px;color:#64748b">Konfigurasi endpoint REST API server Node.js / Baileys</div>
                </div>
            </div>

            <div style="display:grid;gap:16px">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:14px 16px;border-radius:10px">
                    <label style="display:flex;align-items:center;gap:12px;cursor:pointer;user-select:none">
                        <input type="checkbox" id="piket-set-wa-gateway-aktif" {{ ($waGatewayAktif ?? '1') === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#059669">
                        <div>
                            <div style="font-size:13.5px;font-weight:700;color:#1e293b">Aktifkan Notifikasi WhatsApp Otomatis</div>
                            <div style="font-size:12px;color:#64748b">Mengirim link persetujuan langsung ke nomor WhatsApp terkait saat Guru Piket membuat surat.</div>
                        </div>
                    </label>
                </div>

                <div>
                    <label style="font-size:12.5px;font-weight:600;color:#475569">Endpoint Gateway Bot</label>
                    <input type="text" class="filter-input" id="piket-set-wa-endpoint" value="{{ $waGatewayEndpoint ?? 'http://127.0.0.1:3000/send-message' }}" placeholder="http://127.0.0.1:3000/send-message" style="width:100%;margin-top:5px" readonly>
                    <div style="font-size:11.5px;color:#94a3b8;margin-top:4px">Endpoint default lokal port 3000 untuk service background Baileys.</div>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: Nomor Tujuan Notifikasi ── --}}
        <div class="card" style="padding:22px 24px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                <div style="width:38px;height:38px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <h3 style="font-size:15.5px;font-weight:700;color:#1e293b">Nomor WhatsApp Penerima Notifikasi</h3>
                    <div style="font-size:12px;color:#64748b">Format nomor dapat diawali dengan 08... atau 628...</div>
                </div>
            </div>

            <div style="display:grid;gap:16px">
                <div>
                    <label style="font-size:12.5px;font-weight:600;color:#475569">Nomor WhatsApp Waka Kesiswaan <span style="color:#ef4444">*</span></label>
                    <input type="text" class="filter-input" id="piket-set-wa-waka-kesiswaan" value="{{ $waNomorWakaKesiswaan ?? '' }}" placeholder="Contoh: 081234567890 atau 6281234567890" style="width:100%;margin-top:5px">
                    <div style="font-size:11.5px;color:#64748b;margin-top:4px">Menerima notifikasi otomatis untuk persetujuan <strong>Dispensasi &amp; Izin Siswa</strong>.</div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="font-size:12.5px;font-weight:600;color:#475569">Nomor WhatsApp Waka SDM <span style="color:#ef4444">*</span></label>
                        <input type="text" class="filter-input" id="piket-set-wa-waka-sdm" value="{{ $waNomorWakaSdm ?? '' }}" placeholder="Contoh: 081234567890" style="width:100%;margin-top:5px">
                        <div style="font-size:11.5px;color:#64748b;margin-top:4px">Menerima permohonan <strong>Izin Guru</strong>.</div>
                    </div>
                    <div>
                        <label style="font-size:12.5px;font-weight:600;color:#475569">Nomor WhatsApp Kepala Sekolah <span style="color:#ef4444">*</span></label>
                        <input type="text" class="filter-input" id="piket-set-wa-kepsek" value="{{ $waNomorKepsek ?? '' }}" placeholder="Contoh: 081234567890" style="width:100%;margin-top:5px">
                        <div style="font-size:11.5px;color:#64748b;margin-top:4px">Menerima permohonan <strong>Izin Guru</strong>.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BOX QR CODE (Jika bot belum login) ── --}}
        <div id="piket-wa-qr-box" class="card" style="display:none;padding:22px 24px;background:#fffbeb;border:1px solid #fde68a;text-align:center">
            <h4 style="font-size:15px;font-weight:700;color:#92400e;margin-bottom:6px">Scan QR Code WhatsApp Bot</h4>
            <p style="font-size:12px;color:#b45309;margin-bottom:14px">Buka aplikasi WhatsApp di HP &gt; Perangkat Tertaut &gt; Tautkan Perangkat, lalu scan QR Code berikut:</p>
            <div style="display:inline-block;padding:12px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.08)">
                <img id="piket-wa-qr-img" src="" alt="QR Code WhatsApp" style="width:220px;height:220px;display:block">
            </div>
        </div>

        {{-- ── CARD 3: Kontrol Server Bot & Test Pesan ── --}}
        <div class="card" style="padding:22px 24px;background:#fafafa">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <button type="button" class="btn-secondary" onclick="restartBotPiketPage()" id="btn-restart-bot-piket" style="border-radius:8px;padding:9px 14px;font-size:12.5px;display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Hubungkan Ulang Bot
                    </button>
                    <button type="button" class="btn-secondary" onclick="cekStatusBotPiketPage(true)" style="border-radius:8px;padding:9px 14px;font-size:12.5px;display:inline-flex;align-items:center;gap:6px">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        Cek Koneksi Bot
                    </button>
                    <button type="button" class="btn-secondary" onclick="modalTestKirimWaPiket()" style="border-radius:8px;padding:9px 14px;font-size:12.5px;display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Uji Coba Kirim WA
                    </button>
                </div>

                <button type="submit" class="btn-primary" id="btn-simpan-wa-piket-page" style="border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function simpanPengaturanWaPiketPage(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-simpan-wa-piket-page');
    if (btn) btn.disabled = true;

    const kepsek = document.getElementById('piket-set-wa-kepsek')?.value?.trim() ?? '';
    const wakaSdm = document.getElementById('piket-set-wa-waka-sdm')?.value?.trim() ?? '';
    const wakaKesiswaan = document.getElementById('piket-set-wa-waka-kesiswaan')?.value?.trim() ?? '';
    const gatewayAktif = document.getElementById('piket-set-wa-gateway-aktif')?.checked ? 1 : 0;

    fetch(@json(route('struktural.pengaturan.update-wa')), {
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
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Disimpan',
            text: data.message || 'Pengaturan nomor WhatsApp berhasil diperbarui!',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            text: err.message || 'Terjadi kesalahan sistem.',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

function cekStatusBotPiketPage(showAlert = true) {
    const badge = document.getElementById('piket-wa-bot-badge');
    const qrBox = document.getElementById('piket-wa-qr-box');
    const qrImg = document.getElementById('piket-wa-qr-img');

    if (badge) {
        badge.className = 'badge badge-info';
        badge.innerHTML = '<span style="width:8px;height:8px;background:#3b82f6;border-radius:50%;display:inline-block"></span> Memeriksa...';
    }

    fetch(@json(route('struktural.pengaturan.qr-wa')), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'connected') {
            if (badge) {
                badge.className = 'badge badge-success';
                badge.style.cssText = 'font-size:12px;padding:6px 14px;display:inline-flex;align-items:center;gap:6px';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block"></span> Online' + (data.user ? ' (' + data.user + ')' : '');
            }
            if (qrBox) qrBox.style.display = 'none';
            if (showAlert) {
                Swal.fire({ icon: 'success', title: 'Bot WhatsApp Online!', text: data.message || 'Bot terhubung dan siap digunakan.', timer: 2000, showConfirmButton: false });
            }
        } else if (data.status === 'waiting_qr' && data.qr_image) {
            if (badge) {
                badge.className = 'badge badge-warning';
                badge.style.cssText = 'font-size:12px;padding:6px 14px;display:inline-flex;align-items:center;gap:6px;background:#fef3c7;color:#92400e;border:1px solid #fde68a';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block"></span> Menunggu Scan QR';
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
                badge.style.cssText = 'font-size:12px;padding:6px 14px;display:inline-flex;align-items:center;gap:6px';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block"></span> Offline';
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
            badge.innerHTML = '<span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block"></span> Offline';
        }
        if (qrBox) qrBox.style.display = 'none';
        if (showAlert) {
            Swal.fire({ icon: 'warning', title: 'Bot Belum Aktif', text: 'Server bot belum berjalan. Klik tombol "Hubungkan Ulang Bot" untuk mengaktifkan.', confirmButtonColor: '#ea580c' });
        }
    });
}

function restartBotPiketPage() {
    const btn = document.getElementById('btn-restart-bot-piket');
    if (btn) btn.disabled = true;

    Swal.fire({
        title: 'Menghubungkan Bot...',
        text: 'Memulai server bot WhatsApp di background...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(@json(route('struktural.pengaturan.restart-wa')), {
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
            cekStatusBotPiketPage(true);
        }, 2500);
    })
    .catch(() => {
        setTimeout(() => {
            Swal.close();
            cekStatusBotPiketPage(true);
        }, 2000);
    })
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

function modalTestKirimWaPiket() {
    const defaultNomor = document.getElementById('piket-set-wa-waka-kesiswaan')?.value?.trim()
        || document.getElementById('piket-set-wa-kepsek')?.value?.trim()
        || document.getElementById('piket-set-wa-waka-sdm')?.value?.trim()
        || '';

    Swal.fire({
        title: 'Uji Coba Kirim Pesan WA',
        html: `
            <div style="text-align:left;display:grid;gap:10px;margin-top:10px">
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Nomor WhatsApp Tujuan:</label>
                    <input type="text" id="swal-piket-test-phone" class="swal2-input" style="margin:4px 0 0 0;width:100%" placeholder="Contoh: 081234567890" value="${defaultNomor}">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Isi Pesan Uji Coba:</label>
                    <textarea id="swal-piket-test-pesan" class="swal2-textarea" style="margin:4px 0 0 0;width:100%;height:80px" placeholder="Pesan tes...">Halo! Ini adalah pesan uji coba integrasi WhatsApp Bot PresensiKita dari Guru Piket.</textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim Pesan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16a34a',
        preConfirm: () => {
            const phone = document.getElementById('swal-piket-test-phone').value.trim();
            const msg = document.getElementById('swal-piket-test-pesan').value.trim();
            if (!phone) {
                Swal.showValidationMessage('Nomor WhatsApp tujuan wajib diisi.');
                return false;
            }
            return { target_phone: phone, pesan: msg };
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mengirim Pesan...',
                text: 'Menghubungi server bot WhatsApp...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(@json(route('struktural.pengaturan.test-wa')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal mengirim pesan.');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Terkirim!',
                    text: data.message,
                    confirmButtonColor: '#16a34a'
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Kirim Pesan',
                    text: err.message,
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}
</script>
