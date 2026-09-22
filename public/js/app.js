/* ── Global SweetAlert2 Modern Mixin ───────────────────── */
const SwalModern = window.Swal ? Swal.mixin({
    buttonsStyling: true,
    customClass: {
        popup:         '',
        confirmButton: '',
        cancelButton:  '',
    }
}) : { fire: () => Promise.resolve({}) };

/* ── Konfirmasi Keluar ─────────────────────────── */
function confirmKeluar(formId) {
    Swal.fire({
        html: `
            <div style="padding:12px 0 4px;text-align:center">
                <div style="
                    width:72px;height:72px;
                    background:linear-gradient(135deg,#fef2f2,#fee2e2);
                    border-radius:50%;
                    display:flex;align-items:center;justify-content:center;
                    margin:0 auto 20px;
                    border:2px solid #fecaca;
                ">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:10px;letter-spacing:-0.02em">Keluar dari Akun?</div>
                <div style="font-size:13.5px;color:#64748b;line-height:1.65">Sesi Anda akan diakhiri.<br>Yakin ingin keluar?</div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            confirmButton: 'swal2-confirm',
            cancelButton:  'swal2-cancel',
            actions:       'swal2-actions',
        },
        confirmButtonColor: '#ef4444',
        cancelButtonColor: 'transparent',
        buttonsStyling: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

function showPage(page){
    document.querySelectorAll('[id^="page-"]').forEach(el=>{
        if(el.id!=='page-'+page) el.style.display='none';
    });
    const t=document.getElementById('page-'+page);
    if(t){t.style.display='block';t.classList.remove('page-anim');void t.offsetWidth;t.classList.add('page-anim');}
    document.querySelectorAll('.nav-item').forEach(el=>el.classList.remove('active'));
    const n=document.getElementById('nav-'+page);
    if(n) n.classList.add('active');
    if(page==='absensi-harian' || page==='absensi' || page==='jurnal-absensi'){
        const root = absensiRoot();
        const select = root ? qs('#pilih-kelas', root) : document.getElementById('pilih-kelas');
        if(select && select.value){
            loadSiswaByKelas(select.value);
        } else {
            renderTable(currentSiswaList);
            muatAbsensiTersimpan();
        }
    }
    if(page==='laporan') initLaporanCharts();
    if(page !== 'jurnal-absensi') stopSelfieCamera();
    if (window.location.hash !== '#' + page) {
        history.replaceState(null, '', '#' + page);
    }
    closeSidebarMobile();
    closeUserDropdown();
}

function reloadCurrentPage(){
    const visible = document.querySelector('[id^="page-"]:not([style*="display: none"]):not([style*="display:none"])');
    const page = visible ? visible.id.replace('page-', '') : 'dashboard';
    window.location.hash = page;
    location.reload();
}

let sidebarVisible=true;

function setSidebarOverlay(open){
    const overlay = document.getElementById('sidebar-overlay');
    if (overlay) overlay.classList.toggle('active', !!open);
    document.body.classList.toggle('sidebar-open', !!open);
}

function closeSidebarMobile(){
    const sb = document.getElementById('sidebar');
    if (!sb) return;
    if (window.innerWidth <= 992) {
        sb.classList.remove('active-mobile');
        setSidebarOverlay(false);
    }
}

function toggleSidebar(){
    const sb=document.getElementById('sidebar');
    const mc=document.querySelector('.main-content');
    if(!sb) return;
    if(window.innerWidth <= 992) {
        const willOpen = !sb.classList.contains('active-mobile');
        sb.classList.toggle('active-mobile', willOpen);
        setSidebarOverlay(willOpen);
    } else {
        sidebarVisible = !sidebarVisible;
        sb.style.width = sidebarVisible ? '240px' : '0';
        if(mc) mc.style.marginLeft = sidebarVisible ? '240px' : '0';
        setSidebarOverlay(false);
    }
}

/* ── User Profile Dropdown ───────────────────────────── */
function toggleUserDropdown(e) {
    if (e) e.stopPropagation();
    const wrap = document.getElementById('user-profile-wrap');
    if (!wrap) return;
    const isOpen = wrap.classList.contains('open');
    wrap.classList.toggle('open', !isOpen);
    const btn = document.getElementById('user-profile-btn');
    if (btn) btn.setAttribute('aria-expanded', (!isOpen).toString());
}

function closeUserDropdown() {
    const wrap = document.getElementById('user-profile-wrap');
    if (wrap && wrap.classList.contains('open')) {
        wrap.classList.remove('open');
        const btn = document.getElementById('user-profile-btn');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }
}

document.addEventListener('click', function(e) {
    const wrap = document.getElementById('user-profile-wrap');
    if (wrap && !wrap.contains(e.target)) {
        closeUserDropdown();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUserDropdown();
    }
});

function absensiRoot(){
    const ids = ['absensi-harian','jurnal-absensi','absensi'];
    for(const id of ids){
        const el = document.getElementById('page-'+id);
        if(el && el.style.display !== 'none') return '#page-'+id;
    }
    return null;
}
function qs(sel, root){
    return document.querySelector(root ? (root + ' ' + sel) : sel);
}

let cachedJadwalGuruData = null;

function updateGuruJurnalUI(data, targetKelasId) {
    const page = document.getElementById('page-jurnal-absensi');
    if (!page || !data) return;

    const kelasSelect = page.querySelector('#pilih-kelas');
    const selectedKelas = targetKelasId || (kelasSelect ? kelasSelect.value : null);
    const activeKelasIds = data.active_kelas_ids || [];
    const isRealtimeMode = data.is_realtime_mode !== false;
    const izinEdit = !!data.izin_edit;
    const isKelasAktif = !isRealtimeMode || izinEdit || (activeKelasIds.map(String).includes(String(selectedKelas)));
    const canInput = isKelasAktif;
    const kelasHariIni = data.kelas_hari_ini || [];
    const hasJadwalHariIni = kelasHariIni.length > 0;
    const jadwal = data.jadwal;

    const materiInput = page.querySelector('#input-materi');
    const submitButton = page.querySelector('#btn-submit-jurnal');
    const submitButtonTop = page.querySelector('#btn-submit-jurnal-top');
    const startCameraBtn = page.querySelector('#btn-start-camera');
    const tandaiButtons = page.querySelectorAll('.btn-tandai');
    const formCard = page.querySelector('#jurnal-form-card');
    const badgeContainer = page.querySelector('#badge-jadwal-aktif-container');
    const lockedPlaceholder = page.querySelector('#absensi-locked-placeholder');
    const tableWrapper = page.querySelector('#absensi-table-wrapper');
    let alert = page.querySelector('#jadwal-status-alert');

    // Controls disability
    [materiInput, submitButton, submitButtonTop, startCameraBtn, ...tandaiButtons].forEach(control => {
        if (control) control.disabled = !canInput;
    });
    page.querySelectorAll('#siswa-tbody input').forEach(control => { control.disabled = !canInput; });
    page.querySelectorAll('.absensi-status-btn').forEach(btn => {
        btn.style.pointerEvents = canInput ? '' : 'none';
        btn.style.opacity = canInput ? '' : '.6';
    });
    if (formCard) formCard.style.opacity = canInput ? '1' : '.6';

    // Show / Hide student attendance table & placeholder
    if (lockedPlaceholder) {
        lockedPlaceholder.style.display = canInput ? 'none' : 'block';
    }
    if (tableWrapper) {
        tableWrapper.style.display = canInput ? 'block' : 'none';
    }

    // Alert Status
    if (!hasJadwalHariIni) {
        if (!alert) {
            alert = document.createElement('div');
            alert.id = 'jadwal-status-alert';
            alert.className = 'alert-card';
            page.insertBefore(alert, formCard);
        }
        alert.style.cssText = 'background:#fff7ed;border-color:#fed7aa;margin-bottom:16px';
        alert.innerHTML = '<div class="alert-text"><p>Belum ada jadwal mengajar hari ini</p><span>Anda tidak memiliki jadwal mengajar yang terjadwal untuk hari ini.</span></div>';
    } else if (!canInput) {
        if (!alert) {
            alert = document.createElement('div');
            alert.id = 'jadwal-status-alert';
            alert.className = 'alert-card';
            page.insertBefore(alert, formCard);
        }
        const jadwalInfoKelas = (data.jadwal_per_kelas && data.jadwal_per_kelas[selectedKelas]) ? data.jadwal_per_kelas[selectedKelas] : '';
        const infoText = jadwalInfoKelas ? ` Jadwal mengajar kelas ini: ${jadwalInfoKelas}.` : '';
        alert.style.cssText = 'background:#fef2f2;border-color:#fecaca;margin-bottom:16px';
        alert.innerHTML = `<div class="alert-text"><p style="color:#b91c1c">Di luar jam mengajar aktif</p><span style="color:#7f1d1d">Pengisian jurnal dan absensi untuk kelas ini hanya dapat dilakukan saat jam mengajar sedang berlangsung.${infoText}</span></div>`;
    } else if (alert) {
        alert.remove();
    }

    // Badge Sesi
    if (badgeContainer) {
        if (jadwal && String(jadwal.id_kelas) === String(selectedKelas)) {
            const jamKe = jadwal.jam_ke >= 100 ? jadwal.jam_ke - 100 : jadwal.jam_ke;
            badgeContainer.innerHTML = `<span id="badge-jadwal-aktif" class="badge badge-success" style="font-size:12px;padding:4px 10px;font-weight:700;display:inline-flex;align-items:center;gap:6px"><span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>Sesi Aktif: ${jadwal.nama_kelas} (Jam ke-${jamKe})</span>`;
        } else if (canInput) {
            badgeContainer.innerHTML = `<span id="badge-jadwal-aktif" class="badge badge-success" style="font-size:12px;padding:4px 10px;font-weight:700;display:inline-flex;align-items:center;gap:6px">Sesi Terbuka</span>`;
        } else {
            badgeContainer.innerHTML = `<span id="badge-jadwal-aktif" class="badge" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;font-size:12px;padding:4px 10px;font-weight:700;display:inline-flex;align-items:center;gap:6px">Di Luar Jam Mengajar</span>`;
        }
    }
}

function loadSiswaByKelas(idKelas){
    const root = absensiRoot();
    const select = root ? qs('#pilih-kelas', root) : document.getElementById('pilih-kelas');
    if (select && select.selectedIndex >= 0) {
        const text = select.options[select.selectedIndex].text;
        const ahSub = qs('#ah-subtitle', root);
        if (ahSub) ahSub.textContent = 'Informasi Data Absensi Kelas ' + text;
        const guruSub = qs('#guru-absensi-subtitle', root);
        if (guruSub) guruSub.textContent = 'Daftar Absensi Siswa - ' + text;
    }

    if (cachedJadwalGuruData) {
        updateGuruJurnalUI(cachedJadwalGuruData, idKelas);
    }

    fetch(`/absensi/siswa/${idKelas}`)
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                const searchInput = root ? qs('.search-input, .filter-input', root) : null;
                if (searchInput) searchInput.value = '';
                currentSiswaList = res.data;
                renderTable(currentSiswaList);
                muatAbsensiTersimpan();
            }
        })
        .catch(err => console.error('Error fetching siswa:', err));
}

let activeJadwalGuruId = null;

function refreshJadwalGuru(){
    const page = document.getElementById('page-jurnal-absensi');
    if (!page) return;

    fetch('/absensi/jadwal-aktif', { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') return;

            cachedJadwalGuruData = data;
            const jadwal = data.jadwal;
            const kelasHariIni = data.kelas_hari_ini || [];
            const activeKelasIds = (data.active_kelas_ids || []).map(String);
            const jadwalId = jadwal ? String(jadwal.id_jadwal) : null;
            const jadwalBerubah = jadwalId !== activeJadwalGuruId;
            const kelasSelect = page.querySelector('#pilih-kelas');

            if (kelasSelect && kelasHariIni.length > 0) {
                kelasHariIni.forEach(k => {
                    let option = Array.from(kelasSelect.options).find(item => item.value === String(k.id_kelas));
                    if (!option) {
                        option = new Option(k.nama_kelas, k.id_kelas);
                        kelasSelect.appendChild(option);
                    }
                });
            }

            if (jadwal && kelasSelect) {
                if (!kelasSelect.value || kelasSelect.value === '0' || (!activeKelasIds.includes(kelasSelect.value) && activeKelasIds.length > 0)) {
                    kelasSelect.value = String(jadwal.id_kelas);
                }
            }

            const currentKelasId = kelasSelect ? kelasSelect.value : null;
            updateGuruJurnalUI(data, currentKelasId);

            activeJadwalGuruId = jadwalId;
            if (jadwalBerubah && jadwal && kelasSelect && kelasSelect.value === String(jadwal.id_kelas)) {
                loadSiswaByKelas(kelasSelect.value);
            }
        })
        .catch(err => console.error('Error mengecek jadwal aktif:', err));
}

let selfieStream = null;
let selfieFacingMode = 'user';

function getMediaDevices() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        return navigator.mediaDevices;
    }
    const legacy = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    if (legacy) {
        return {
            getUserMedia: function(constraints) {
                return new Promise((resolve, reject) => {
                    legacy.call(navigator, constraints, resolve, reject);
                });
            }
        };
    }
    return null;
}

function startSelfieCamera() {
    const media = getMediaDevices();

    // Jika WebRTC getUserMedia tersedia, coba aktifkan live stream kamera
    if (media) {
        // Stop stream aktif sebelumnya jika ada
        if (selfieStream) {
            selfieStream.getTracks().forEach(t => t.stop());
            selfieStream = null;
        }

        const constraintList = [
            { video: { facingMode: { ideal: selfieFacingMode }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false },
            { video: { facingMode: selfieFacingMode }, audio: false },
            { video: true, audio: false }
        ];

        async function attemptCamera() {
            let lastError = null;
            for (const c of constraintList) {
                try {
                    return await media.getUserMedia(c);
                } catch (err) {
                    lastError = err;
                    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                        throw err;
                    }
                }
            }
            throw lastError || new Error('Gagal membuka WebRTC kamera.');
        }

        attemptCamera()
            .then(stream => {
                selfieStream = stream;
                const root = absensiRoot();
                const video = root ? qs('#selfie-video', root) : document.getElementById('selfie-video');
                const placeholder = root ? qs('#camera-placeholder', root) : document.getElementById('camera-placeholder');
                const overlay = root ? qs('#camera-overlay-controls', root) : document.getElementById('camera-overlay-controls');
                const startBtn = root ? qs('#btn-start-camera', root) : document.getElementById('btn-start-camera');
                const stopBtn = root ? qs('#btn-stop-camera', root) : document.getElementById('btn-stop-camera');

                if (video) {
                    video.srcObject = stream;
                    video.setAttribute('playsinline', '');
                    video.setAttribute('webkit-playsinline', '');
                    video.muted = true;
                    video.style.transform = (selfieFacingMode === 'user') ? 'scaleX(-1)' : 'scaleX(1)';
                    video.style.display = 'block';
                    video.onloadedmetadata = () => {
                        video.play().catch(e => console.warn('Video auto-play suppressed:', e));
                    };
                }
                if (placeholder) placeholder.style.display = 'none';
                if (overlay) overlay.style.display = 'flex';
                if (startBtn) startBtn.style.display = 'none';
                if (stopBtn) stopBtn.style.display = 'inline-flex';
            })
            .catch(err => {
                console.warn('WebRTC tidak berhasil, mengaktifkan kamera native HP/perangkat:', err);
                // Fallback otomatis ke kamera native perangkat via HTML5 capture API
                triggerNativeCameraCapture();
            });
    } else {
        // Lingkungan HTTP Non-SSL / Browser tanpa WebRTC: Buka kamera native perangkat secara langsung
        triggerNativeCameraCapture();
    }
}
window.startSelfieCamera = startSelfieCamera;

function triggerNativeCameraCapture() {
    const root = absensiRoot();
    const nativeInput = root ? qs('#native-camera-input', root) : document.getElementById('native-camera-input');
    if (nativeInput) {
        nativeInput.click();
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Kamera Tidak Tersedia',
            text: 'Perangkat atau browser tidak mendukung pembukaan kamera.',
            confirmButtonColor: '#1a3268'
        });
    }
}

function handleNativeCameraCapture(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const maxDimension = 1280;
            let width = img.width;
            let height = img.height;
            if (width > maxDimension || height > maxDimension) {
                if (width > height) {
                    height = Math.round((height * maxDimension) / width);
                    width = maxDimension;
                } else {
                    width = Math.round((width * maxDimension) / height);
                    height = maxDimension;
                }
            }
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

            const root = absensiRoot();
            const inputHidden = root ? qs('#input-foto-selfie', root) : document.getElementById('input-foto-selfie');
            const preview = root ? qs('#selfie-preview', root) : document.getElementById('selfie-preview');
            const previewPlaceholder = root ? qs('#preview-placeholder', root) : document.getElementById('preview-placeholder');
            const retakeBtn = root ? qs('#btn-retake-photo', root) : document.getElementById('btn-retake-photo');
            const statusBadge = root ? qs('#selfie-status-badge', root) : document.getElementById('selfie-status-badge');

            if (inputHidden) {
                inputHidden.value = dataUrl;
                inputHidden.dataset.hasExistingSelfie = '1';
            }
            if (preview) {
                preview.src = dataUrl;
                preview.style.display = 'block';
            }
            if (previewPlaceholder) previewPlaceholder.style.display = 'none';
            if (retakeBtn) retakeBtn.style.display = 'inline-flex';
            if (statusBadge) {
                statusBadge.style.background = '#dcfce7';
                statusBadge.style.color = '#15803d';
                statusBadge.style.borderColor = '#bbf7d0';
                statusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Foto Selfie Siap Disimpan`;
            }

            input.value = '';
            stopSelfieCamera();
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
window.handleNativeCameraCapture = handleNativeCameraCapture;

function switchSelfieCamera() {
    selfieFacingMode = (selfieFacingMode === 'user') ? 'environment' : 'user';
    startSelfieCamera();
}
window.switchSelfieCamera = switchSelfieCamera;

function stopSelfieCamera() {
    if (selfieStream) {
        selfieStream.getTracks().forEach(track => track.stop());
        selfieStream = null;
    }
    const root = absensiRoot();
    const video = root ? qs('#selfie-video', root) : document.getElementById('selfie-video');
    const placeholder = root ? qs('#camera-placeholder', root) : document.getElementById('camera-placeholder');
    const overlay = root ? qs('#camera-overlay-controls', root) : document.getElementById('camera-overlay-controls');
    const startBtn = root ? qs('#btn-start-camera', root) : document.getElementById('btn-start-camera');
    const stopBtn = root ? qs('#btn-stop-camera', root) : document.getElementById('btn-stop-camera');

    if (video) {
        video.pause();
        video.srcObject = null;
        video.style.display = 'none';
    }
    if (placeholder) placeholder.style.display = 'block';
    if (overlay) overlay.style.display = 'none';
    if (startBtn) startBtn.style.display = 'inline-flex';
    if (stopBtn) stopBtn.style.display = 'none';
}
window.stopSelfieCamera = stopSelfieCamera;

function snapSelfiePhoto() {
    const root = absensiRoot();
    const video = root ? qs('#selfie-video', root) : document.getElementById('selfie-video');
    if (!video || !video.videoWidth || !video.videoHeight) {
        Swal.fire({
            icon: 'warning',
            title: 'Kamera Belum Siap',
            text: 'Tunggu beberapa saat hingga gambar kamera muncul sebelum mengambil foto.',
            confirmButtonColor: '#1a3268'
        });
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    
    // Cerminkan hasil jepretan jika kamera depan
    if (selfieFacingMode === 'user') {
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
    }
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

    const inputHidden = root ? qs('#input-foto-selfie', root) : document.getElementById('input-foto-selfie');
    const preview = root ? qs('#selfie-preview', root) : document.getElementById('selfie-preview');
    const previewPlaceholder = root ? qs('#preview-placeholder', root) : document.getElementById('preview-placeholder');
    const retakeBtn = root ? qs('#btn-retake-photo', root) : document.getElementById('btn-retake-photo');
    const statusBadge = root ? qs('#selfie-status-badge', root) : document.getElementById('selfie-status-badge');

    if (inputHidden) {
        inputHidden.value = dataUrl;
        inputHidden.dataset.hasExistingSelfie = '1';
    }
    if (preview) {
        preview.src = dataUrl;
        preview.style.display = 'block';
    }
    if (previewPlaceholder) previewPlaceholder.style.display = 'none';
    if (retakeBtn) retakeBtn.style.display = 'inline-flex';
    if (statusBadge) {
        statusBadge.style.background = '#dcfce7';
        statusBadge.style.color = '#15803d';
        statusBadge.style.borderColor = '#bbf7d0';
        statusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Foto Selfie Siap Disimpan`;
    }

    stopSelfieCamera();
}
window.snapSelfiePhoto = snapSelfiePhoto;

function retakeSelfiePhoto() {
    const root = absensiRoot();
    const inputHidden = root ? qs('#input-foto-selfie', root) : document.getElementById('input-foto-selfie');
    const preview = root ? qs('#selfie-preview', root) : document.getElementById('selfie-preview');
    const previewPlaceholder = root ? qs('#preview-placeholder', root) : document.getElementById('preview-placeholder');
    const retakeBtn = root ? qs('#btn-retake-photo', root) : document.getElementById('btn-retake-photo');
    const statusBadge = root ? qs('#selfie-status-badge', root) : document.getElementById('selfie-status-badge');

    if (inputHidden) {
        inputHidden.value = '';
        inputHidden.dataset.hasExistingSelfie = '0';
    }
    if (preview) {
        preview.src = '';
        preview.style.display = 'none';
    }
    if (previewPlaceholder) previewPlaceholder.style.display = 'block';
    if (retakeBtn) retakeBtn.style.display = 'none';
    if (statusBadge) {
        statusBadge.style.background = '#fee2e2';
        statusBadge.style.color = '#b91c1c';
        statusBadge.style.borderColor = '#fecaca';
        statusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Belum Ambil Foto`;
    }

    startSelfieCamera();
}
window.retakeSelfiePhoto = retakeSelfiePhoto;

function muatAbsensiTersimpan(){
    const root = absensiRoot();
    if (!root) return;
    const kelasSelect = qs('#pilih-kelas', root);
    const tanggalInput = qs('#input-tanggal', root);
    if (!kelasSelect || !tanggalInput) return;
    const kelasId = kelasSelect.value;
    const tanggal = tanggalInput.value;
    if (!kelasId || !tanggal) return;

    fetch(`/absensi/cek?kelas_id=${kelasId}&tanggal=${tanggal}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') return;

            // Reset semua ke status Hadir terlebih dahulu
            currentSiswaList.forEach(s => {
                const radioH = qs(`input[name="st-${s.id_siswa}"][value="H"]`, root);
                if (radioH) radioH.checked = true;
                const ket = qs(`#ket-${s.id_siswa}`, root);
                if (ket) ket.value = '';
            });

            if (data.siswa && data.siswa.length > 0) {
                const map = {};
                data.siswa.forEach(s => { map[s.id_siswa] = s; });
                currentSiswaList.forEach(s => {
                    const rec = map[s.id_siswa];
                    if (rec && rec.status && rec.status !== 'H') {
                        const radio = qs(`input[name="st-${s.id_siswa}"][value="${rec.status}"]`, root);
                        if (radio) radio.checked = true;
                    }
                    if (rec && rec.keterangan) {
                        const ket = qs(`#ket-${s.id_siswa}`, root);
                        if (ket) ket.value = rec.keterangan;
                        const ketCard = document.querySelector(`.absensi-card[data-siswa-id="${s.id_siswa}"] .absensi-ket-input`);
                        if (ketCard) ketCard.value = rec.keterangan;
                    }
                });
            }

            if (data.jurnal && data.jurnal.materi) {
                const materiInput = qs('#input-materi', root);
                if (materiInput && !materiInput.value) {
                    materiInput.value = data.jurnal.materi;
                }
            }

            // Sync status foto selfie
            const fotoInput = root ? qs('#input-foto-selfie', root) : document.getElementById('input-foto-selfie');
            const preview = root ? qs('#selfie-preview', root) : document.getElementById('selfie-preview');
            const previewPlaceholder = root ? qs('#preview-placeholder', root) : document.getElementById('preview-placeholder');
            const retakeBtn = root ? qs('#btn-retake-photo', root) : document.getElementById('btn-retake-photo');
            const statusBadge = root ? qs('#selfie-status-badge', root) : document.getElementById('selfie-status-badge');
            const cameraBox = root ? qs('#camera-box', root) : document.getElementById('camera-box');
            const startCameraBtn = root ? qs('#btn-start-camera', root) : document.getElementById('btn-start-camera');
            const stopCameraBtn = root ? qs('#btn-stop-camera', root) : document.getElementById('btn-stop-camera');
            const nativeCameraInput = root ? qs('#native-camera-input', root) : document.getElementById('native-camera-input');
            const selfieGridContainer = root ? qs('#selfie-section > div:last-child', root) : document.querySelector('#selfie-section > div:last-child');
            const sudahAbsensiMsg = root ? qs('#sudah-absensi-message', root) : document.getElementById('sudah-absensi-message');

            if (data.jurnal && (data.jurnal.foto_selfie_url || data.jurnal.foto_selfie)) {
                // Sudah ada selfie untuk kelas ini - sembunyikan seluruh UI kamera dan preview
                if (fotoInput) {
                    fotoInput.value = '';
                    fotoInput.dataset.hasExistingSelfie = '1';
                }
                if (cameraBox) cameraBox.style.display = 'none';
                if (startCameraBtn) startCameraBtn.style.display = 'none';
                if (stopCameraBtn) stopCameraBtn.style.display = 'none';
                if (retakeBtn) retakeBtn.style.display = 'none';
                if (nativeCameraInput) nativeCameraInput.style.display = 'none';
                if (preview) {
                    preview.src = data.jurnal.foto_selfie_url || '';
                    preview.style.display = 'none';
                }
                if (previewPlaceholder) previewPlaceholder.style.display = 'none';
                // Sembunyikan seluruh grid kamera & preview
                if (selfieGridContainer) selfieGridContainer.style.display = 'none';
                if (statusBadge) {
                    statusBadge.style.background = '#dcfce7';
                    statusBadge.style.color = '#15803d';
                    statusBadge.style.borderColor = '#bbf7d0';
                    statusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Sudah Absensi`;
                }
                // Tampilkan pesan 'Sudah Absensi' di bawah header selfie section
                const selfieSection = root ? qs('#selfie-section', root) : document.getElementById('selfie-section');
                if (sudahAbsensiMsg) {
                    sudahAbsensiMsg.style.display = 'flex';
                } else if (selfieSection) {
                    const msg = document.createElement('div');
                    msg.id = 'sudah-absensi-message';
                    msg.style.cssText = 'display:flex;align-items:center;justify-content:center;gap:10px;padding:20px;background:#f0fdf4;border:2px solid #bbf7d0;border-radius:10px;text-align:center;margin-top:12px';
                    msg.innerHTML = `<div><div style="width:44px;height:44px;border-radius:50%;background:#dcfce7;color:#16a34a;display:flex;align-items:center;justify-content:center;margin:0 auto 8px"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div><div style="font-size:14px;font-weight:700;color:#15803d">Sudah Absensi</div><div style="font-size:12px;color:#16a34a;margin-top:2px">Foto selfie untuk kelas ini sudah diambil</div></div>`;
                    selfieSection.appendChild(msg);
                }
                stopSelfieCamera();
            } else {
                // Belum ada selfie - tampilkan UI kamera normal
                if (fotoInput) {
                    fotoInput.value = '';
                    fotoInput.dataset.hasExistingSelfie = '0';
                }
                if (selfieGridContainer) selfieGridContainer.style.display = 'grid';
                if (cameraBox) cameraBox.style.display = 'flex';
                if (startCameraBtn) startCameraBtn.style.display = 'inline-flex';
                if (stopCameraBtn) stopCameraBtn.style.display = 'none';
                if (retakeBtn) retakeBtn.style.display = 'none';
                if (nativeCameraInput) nativeCameraInput.style.display = 'none';
                if (preview) {
                    preview.src = '';
                    preview.style.display = 'none';
                }
                if (previewPlaceholder) previewPlaceholder.style.display = 'block';
                if (statusBadge) {
                    statusBadge.style.background = '#fee2e2';
                    statusBadge.style.color = '#b91c1c';
                    statusBadge.style.borderColor = '#fecaca';
                    statusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Belum Ambil Foto`;
                }
                // Hapus pesan 'Sudah Absensi' jika ada
                if (sudahAbsensiMsg) {
                    sudahAbsensiMsg.style.display = 'none';
                }
            }

            const submitBtn = root ? qs('#btn-submit-jurnal', root) : document.getElementById('btn-submit-jurnal');
            const submitBtnTop = root ? qs('#btn-submit-jurnal-top', root) : document.getElementById('btn-submit-jurnal-top');
            const formHeader = root ? qs('#jurnal-form-card .card-heading', root) : document.querySelector('#jurnal-form-card .card-heading');
            let modeBadge = root ? qs('#jurnal-mode-badge', root) : document.getElementById('jurnal-mode-badge');

            if (data.jurnal) {
                if (submitBtn) {
                    submitBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:5px"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan Perubahan Jurnal &amp; Absensi`;
                    submitBtn.style.background = 'linear-gradient(135deg, #d97706, #f59e0b)';
                }
                if (submitBtnTop) {
                    submitBtnTop.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:5px"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan Perubahan Jurnal &amp; Absensi`;
                    submitBtnTop.style.background = 'linear-gradient(135deg, #d97706, #f59e0b)';
                }
                if (formHeader && !modeBadge) {
                    modeBadge = document.createElement('span');
                    modeBadge.id = 'jurnal-mode-badge';
                    modeBadge.className = 'badge';
                    modeBadge.style.cssText = 'background:#fef3c7;color:#b45309;font-size:11.5px;padding:3px 8px;font-weight:700;border:1px solid #fde68a;margin-left:8px';
                    modeBadge.textContent = 'Mode Edit (Jurnal Sudah Diisi)';
                    formHeader.appendChild(modeBadge);
                }
            } else {
                if (submitBtn) {
                    submitBtn.textContent = 'Simpan Jurnal & Absensi';
                    submitBtn.style.background = '';
                }
                if (submitBtnTop) {
                    submitBtnTop.textContent = 'Simpan Jurnal & Absensi';
                    submitBtnTop.style.background = '';
                }
                if (modeBadge) {
                    modeBadge.remove();
                }
            }

            updateRekap();
            syncAbsensiCards();
        })
        .catch(err => console.error('Error fetching saved absensi:', err));
}

function renderTable(data){
    const root = absensiRoot();
    const tbody = root ? qs('#siswa-tbody', root) : document.getElementById('siswa-tbody');
    if(!tbody) return;
    tbody.innerHTML='';
    if(data.length === 0){
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#94a3b8">Belum ada siswa di kelas ini.</td></tr>';
        updateRekap();
        return;
    }
    const isAdmin = root === '#page-absensi-harian';
    const pageGuru = document.getElementById('page-jurnal-absensi');
    const isGuruDisabled = pageGuru && pageGuru.querySelector('#btn-submit-jurnal')?.disabled;
    const radioAttr = isAdmin ? 'onclick="return false;" tabindex="-1"' : (isGuruDisabled ? 'disabled onchange="updateRekap()"' : 'onchange="updateRekap()"');
    const ketAttr = isAdmin
        ? 'readonly placeholder="Diisi oleh Guru..." style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:12px;width:100%;outline:none;background:#f8fafc;color:#475569;cursor:default;"'
        : (isGuruDisabled
            ? 'disabled placeholder="Keterangan (opsional)..." style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:12px;width:100%;outline:none;background:#f8fafc;"'
            : 'placeholder="Keterangan (opsional)..." style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:12px;width:100%;outline:none;"');

    data.forEach((s, idx)=>{
        const id = s.id_siswa;
        const nisn = s.nisn || '-';
        const nama = s.nama_siswa;
        const r=document.createElement('tr');
        r.dataset.siswaId = id;
        r.dataset.nama = (nama || '').toLowerCase();
        r.dataset.nisn = (nisn || '').toLowerCase();
        r.innerHTML=`
            <td style="color:#94a3b8;font-weight:600">${idx+1}</td>
            <td style="font-family:monospace;font-size:13px;color:#64748b">${nisn}</td>
            <td style="font-weight:600">${nama}</td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="H" checked ${radioAttr} style="accent-color:#22c55e;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="S" ${radioAttr} style="accent-color:#f59e0b;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="I" ${radioAttr} style="accent-color:#3b82f6;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="D" ${radioAttr} style="accent-color:#7c3aed;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="A" ${radioAttr} style="accent-color:#ef4444;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td><input type="text" id="ket-${id}" ${ketAttr}${isAdmin || isGuruDisabled ? '' : ` oninput="mirrorKetGuru(this, '${id}')"`}></td>
        `;
        tbody.appendChild(r);

        if(!isAdmin){
            // Kartu mobile (tampil hanya di layar ≤ 768px, sinkron dengan input tabel)
            const card=document.createElement('div');
            card.className='absensi-card';
            card.dataset.siswaId=id;
            card.dataset.nama = (nama || '').toLowerCase();
            card.dataset.nisn = (nisn || '').toLowerCase();
            const stBtn=(v,label)=>`
                <button type="button" class="absensi-status-btn ${v==='H'?'selected':''}" data-status="${v}" data-sid="${id}" ${isGuruDisabled ? 'style="pointer-events:none;opacity:.6;"' : ''} onclick="pickAbsensiStatus(this)">${label}<input type="radio" name="st-${id}" value="${v}" ${v==='H'?'checked':''} ${isGuruDisabled ? 'disabled' : ''} onchange="updateRekap()"></button>`;
            card.innerHTML=`
                <div class="absensi-card-head">
                    <span class="absensi-card-no">${idx+1}</span>
                    <div>
                        <div class="absensi-card-nama">${nama}</div>
                        <div class="absensi-card-nisn">NISN: ${nisn}</div>
                    </div>
                </div>
                <div class="absensi-status-row">${stBtn('H','Hadir')}${stBtn('S','Sakit')}${stBtn('I','Izin')}${stBtn('D','Dispen')}${stBtn('A','Alpa')}</div>
                <input type="text" class="absensi-ket-input" data-ket-sid="${id}" placeholder="Keterangan (opsional)..." ${isGuruDisabled ? 'disabled' : ''} oninput="mirrorKetGuru(this, '${id}')">
            `;
            tbody.appendChild(card);
        }
    });
    updateRekap();
    syncAbsensiCards();
}

// Mirror nilai keterangan antara input tabel dan input kartu mobile
function mirrorKetGuru(src, sid){
    const isCard = src.classList.contains('absensi-ket-input');
    const target = isCard
        ? document.getElementById(`ket-${sid}`)
        : document.querySelector(`.absensi-card[data-siswa-id="${sid}"] .absensi-ket-input`);
    if(target) target.value = src.value;
}

// Sinkronkan radio tabel <-> kartu mobile + highlight tombol status
function syncAbsensiCards(){
    if(!currentSiswaList) return;
    currentSiswaList.forEach(d=>{
        const checked = document.querySelector(`input[name="st-${d.id_siswa}"]:checked`);
        if(!checked) return;
        const card = document.querySelector(`.absensi-card[data-siswa-id="${d.id_siswa}"]`);
        if(!card) return;
        card.querySelectorAll('.absensi-status-btn').forEach(b=>b.classList.toggle('selected', b.dataset.status===checked.value));
    });
}

function pickAbsensiStatus(btn){
    const sid = btn.dataset.sid;
    const val = btn.dataset.status;
    const radio = document.querySelector(`input[name="st-${sid}"][value="${val}"]`);
    if(radio){ radio.checked = true; radio.dispatchEvent(new Event('change', {bubbles:true})); }
    const card = btn.closest('.absensi-card');
    if(card) card.querySelectorAll('.absensi-status-btn').forEach(b=>b.classList.toggle('selected', b===btn));
}

function updateRekap(){
    const root = absensiRoot();
    let h=0,s=0,i=0,d=0,a=0;
    currentSiswaList.forEach(item=>{
        const c=root ? qs(`input[name="st-${item.id_siswa}"]:checked`, root) : document.querySelector(`input[name="st-${item.id_siswa}"]:checked`);
        if(c){if(c.value==='H')h++;else if(c.value==='S')s++;else if(c.value==='I')i++;else if(c.value==='D')d++;else if(c.value==='A')a++;}
    });
    const set=(id,val)=>{const el= root ? qs('#'+id, root) : document.getElementById(id); if(el) el.textContent=val;};
    set('rekap-hadir',h);
    set('rekap-sakit',s);
    set('rekap-izin',i);
    set('rekap-dispen',d);
    set('rekap-alpa',a);
}

function tandaiSemua(v){
    const root = absensiRoot();
    if (root === '#page-absensi-harian') return;
    currentSiswaList.forEach(d=>{
        const r=root ? qs(`input[name="st-${d.id_siswa}"][value="${v}"]`, root) : document.querySelector(`input[name="st-${d.id_siswa}"][value="${v}"]`);
        if(r) r.checked=true;
    });
    updateRekap();
    syncAbsensiCards();
}

function filterSiswa(q){
    q = (q || '').trim().toLowerCase();
    const root = absensiRoot();
    const tbody = root ? qs('#siswa-tbody', root) : document.getElementById('siswa-tbody');
    if(!tbody) return;

    const rows = tbody.querySelectorAll('tr[data-siswa-id]');
    const cards = tbody.querySelectorAll('.absensi-card[data-siswa-id]');
    let matchCount = 0;

    rows.forEach(row => {
        const nama = row.dataset.nama || '';
        const nisn = row.dataset.nisn || '';
        const isMatch = !q || nama.includes(q) || nisn.includes(q);
        row.style.display = isMatch ? '' : 'none';
        if (isMatch) matchCount++;
    });

    cards.forEach(card => {
        const nama = card.dataset.nama || '';
        const nisn = card.dataset.nisn || '';
        const isMatch = !q || nama.includes(q) || nisn.includes(q);
        card.style.display = isMatch ? '' : 'none';
    });

    let emptyRow = tbody.querySelector('.search-empty-row');
    if (matchCount === 0 && rows.length > 0) {
        if (!emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.className = 'search-empty-row';
            emptyRow.innerHTML = '<td colspan="9" style="text-align:center;padding:24px;color:#94a3b8;font-style:italic">Tidak ada siswa yang cocok dengan pencarian.</td>';
            tbody.appendChild(emptyRow);
        }
        emptyRow.style.display = '';
    } else if (emptyRow) {
        emptyRow.style.display = 'none';
    }
}

function submitAbsensi(){
    const root = absensiRoot();
    if (root === '#page-absensi-harian') {
        Swal.fire({
            icon: 'warning',
            title: 'Akses Dibatasi',
            text: 'Admin hanya memantau data. Pengisian dan perubahan absensi adalah hak akses Guru.',
            confirmButtonColor: '#1a3268'
        });
        return;
    }
    const kelasId = (root ? qs('#pilih-kelas', root) : document.getElementById('pilih-kelas')).value;
    const tanggal = (root ? qs('#input-tanggal', root) : document.getElementById('input-tanggal')).value;
    const materi = (root ? qs('#input-materi', root) : document.getElementById('input-materi'))?.value || '';
    const fotoInputEl = root ? qs('#input-foto-selfie', root) : document.getElementById('input-foto-selfie');
    const fotoSelfie = fotoInputEl?.value || '';
    const previewSrc = (root ? qs('#selfie-preview', root) : document.getElementById('selfie-preview'))?.getAttribute('src') || '';
    const hasExistingSelfie = fotoInputEl?.dataset?.hasExistingSelfie === '1'
        || (document.getElementById('sudah-absensi-message') && document.getElementById('sudah-absensi-message').style.display !== 'none');

    // Validasi foto selfie: wajib ada foto selfie baru atau foto tersimpan sebelumnya
    if (!hasExistingSelfie && !fotoSelfie && (!previewSrc || previewSrc === '' || previewSrc === window.location.href)) {
        Swal.fire({
            icon: 'warning',
            title: 'Foto Selfie Diperlukan',
            text: 'Silakan ambil foto selfie mengajar terlebih dahulu di awal pembelajaran kelas ini.',
            confirmButtonColor: '#1a3268'
        });
        return;
    }

    const absensiData = {};

    currentSiswaList.forEach(s => {
        const id = s.id_siswa;
        const checked = root ? qs(`input[name="st-${id}"]:checked`, root) : document.querySelector(`input[name="st-${id}"]:checked`);
        const ket = (root ? qs(`#ket-${id}`, root) : document.getElementById(`ket-${id}`))?.value || '';
        absensiData[id] = {
            status: checked ? checked.value : 'H',
            keterangan: ket
        };
    });

    Swal.fire({
        title: 'Menyimpan Absensi...',
        text: 'Sedang menyimpan data absensi',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('/absensi/simpan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            id_kelas: kelasId,
            tanggal: tanggal,
            materi: materi,
            foto_selfie: fotoSelfie,
            absensi: absensiData
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            document.querySelectorAll(`.jadwal-row-item[data-kelas="${kelasId}"]`).forEach(row => {
                row.dataset.hasJurnal = '1';
            });
            updateJadwalGuruRows();
            muatAbsensiTersimpan();

            Swal.fire({
                icon: 'success',
                title: 'Jurnal & Absensi Tersimpan!',
                text: 'Absensi berhasil disimpan untuk seluruh jam mengajar Anda! (Hadir: ' + data.rekap.hadir + ', Sakit: ' + data.rekap.sakit + ', Izin: ' + data.rekap.izin + ', Alpa: ' + data.rekap.alpa + ')',
                confirmButtonColor: '#1a3268'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
            });
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan sistem saat menyimpan data.',
        });
    });
}

function tambahSiswaModal(){
    Swal.fire({
        title: 'Tambah Siswa Baru',
        html: `
            <div class="swal-form-container">
                <div class="swal-form-group">
                    <label for="swal-nama">Nama Lengkap Siswa</label>
                    <input id="swal-nama" class="swal-form-input" placeholder="Masukkan nama siswa...">
                </div>
                <div class="swal-form-group">
                    <label for="swal-nisn">NISN</label>
                    <input id="swal-nisn" class="swal-form-input" placeholder="Contoh: 0092124616">
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-kelas">Kelas</label>
                        <select id="swal-kelas" class="swal-form-select"></select>
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-jk">Jenis Kelamin</label>
                        <select id="swal-jk" class="swal-form-select">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Data',
        cancelButtonText: 'Batal',
        didOpen: () => {
            const kSelect = document.getElementById('pilih-kelas');
            const swalKSelect = document.getElementById('swal-kelas');
            if (kSelect && swalKSelect) {
                swalKSelect.innerHTML = kSelect.innerHTML;
            }
        },
        preConfirm: () => {
            const nama = document.getElementById('swal-nama').value.trim();
            const nisn = document.getElementById('swal-nisn').value.trim();
            if (!nama) {
                Swal.showValidationMessage('Nama siswa tidak boleh kosong');
                return false;
            }
            return {
                nama_siswa: nama,
                nisn: nisn,
                id_kelas: document.getElementById('swal-kelas').value,
                jenis_kelamin: document.getElementById('swal-jk').value,
            }
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch('/siswa/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message })
                .then(() => reloadCurrentPage());
            });
        }
    });
}

function tambahKelasModal(){
    const guruOptions = (window.daftarGuru || []).map(g =>
        `<option value="${g.id_guru}">${g.nama_guru}</option>`
    ).join('');
    const jurusans = (window.daftarJurusan || []).filter(j => j.is_aktif !== false);
    const jurusanHtml = jurusans.length > 0
        ? `<select id="swal-kjurusan" class="swal-form-select"><option value="">Pilih Jurusan</option>${jurusans.map(j => `<option value="${j.kode_jurusan}">${j.kode_jurusan} - ${j.nama_jurusan}</option>`).join('')}</select>`
        : `<input id="swal-kjurusan" class="swal-form-input" placeholder="Contoh: RPL, AK, DKV">`;

    Swal.fire({
        title: 'Tambah Kelas Baru',
        html: `
            <div class="swal-form-container">
                <div class="swal-form-group">
                    <label for="swal-kname">Nama Kelas</label>
                    <input id="swal-kname" class="swal-form-input" placeholder="Contoh: X RPL 3">
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-ktingkat">Tingkat</label>
                        <input id="swal-ktingkat" class="swal-form-input" placeholder="Contoh: X, XI, XII">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-kjurusan">Jurusan</label>
                        ${jurusanHtml}
                    </div>
                </div>
                <div class="swal-form-group">
                    <label for="swal-kwali">Wali Kelas</label>
                    <select id="swal-kwali" class="swal-form-select">
                        <option value="">Belum Ada Wali Kelas</option>
                        ${guruOptions}
                    </select>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Kelas',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const kname = document.getElementById('swal-kname').value.trim();
            if (!kname) {
                Swal.showValidationMessage('Nama kelas tidak boleh kosong');
                return false;
            }
            return {
                nama_kelas: kname,
                tingkat_kelas: document.getElementById('swal-ktingkat').value.trim(),
                jurusan: document.getElementById('swal-kjurusan').value.trim(),
                id_wali_kelas: document.getElementById('swal-kwali').value || null,
            }
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch('/kelas/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message })
                .then(() => reloadCurrentPage());
            });
        }
    });
}

function tambahMapelModal(){
    Swal.fire({
        title: 'Tambah Mata Pelajaran',
        html: `
            <div class="swal-form-container">
                <div class="swal-form-group">
                    <label for="swal-mkode">Kode Mapel</label>
                    <input id="swal-mkode" class="swal-form-input" placeholder="Contoh: B.IND, PAI, MTK">
                </div>
                <div class="swal-form-group">
                    <label for="swal-mnama">Nama Mata Pelajaran</label>
                    <input id="swal-mnama" class="swal-form-input" placeholder="Contoh: Bahasa Indonesia">
                </div>
                <div class="swal-form-group">
                    <label for="swal-mkelompok">Kelompok</label>
                    <select id="swal-mkelompok" class="swal-form-input">
                        <option value="">-- Pilih Kelompok --</option>
                        <option value="A">Kelompok A</option>
                        <option value="B">Kelompok B</option>
                        <option value="C">Kelompok C</option>
                    </select>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Mapel',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const nama = document.getElementById('swal-mnama').value.trim();
            if (!nama) {
                Swal.showValidationMessage('Nama mata pelajaran tidak boleh kosong');
                return false;
            }
            return {
                kode_mapel: document.getElementById('swal-mkode').value.trim(),
                nama_mapel: nama,
                kelompok: document.getElementById('swal-mkelompok').value,
            };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch('/mapel/tambah', {
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
                if (!res.ok || data.status === 'error') {
                    throw new Error(data.message || 'Gagal menambahkan mapel');
                }
                return data;
            })
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message })
                .then(() => reloadCurrentPage());
            })
            .catch(err => Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem.', 'error'));
        }
    });
}

function filterMapel(q){
    const query = (q || '').toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('#mapel-tbody .mapel-row').forEach(row => {
        const hay = row.dataset.search || '';
        const show = !query || hay.includes(query);
        row.style.display = show ? '' : 'none';
        if (show) {
            visible += 1;
            row.querySelector('td').textContent = visible;
        }
    });
}

function previewProfilePhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatar-preview-img');
            const fallback = document.getElementById('avatar-preview-fallback');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (fallback) {
                fallback.style.display = 'none';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateProfilSubmit(e) {
    if (e) e.preventDefault();

    const nama = document.getElementById('input-prof-nama')?.value?.trim();
    const username = document.getElementById('input-prof-username')?.value?.trim();
    const hp = document.getElementById('input-prof-hp')?.value?.trim();
    const oldPass = document.getElementById('input-prof-old-pass')?.value;
    const newPass = document.getElementById('input-prof-new-pass')?.value;
    const photoInput = document.getElementById('input-foto-profil');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!nama || !username) {
        Swal.fire('Peringatan', 'Nama lengkap dan Username wajib diisi!', 'warning');
        return;
    }

    const formData = new FormData();
    if (csrfToken) formData.append('_token', csrfToken);
    formData.append('nama_guru', nama);
    formData.append('username', username);
    formData.append('no_hp', hp || '');
    if (oldPass) formData.append('current_password', oldPass);
    if (newPass) formData.append('new_password', newPass);
    if (photoInput && photoInput.files[0]) {
        formData.append('foto', photoInput.files[0]);
    }

    const btn = document.getElementById('btn-save-profile');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
    }

    fetch(window.profilUpdateUrl || '/profil/update', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken || ''
        },
        body: formData
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(data.message || 'Terjadi kesalahan pada server (Status ' + res.status + ')');
        }
        return data;
    })
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Simpan Perubahan Profil';
        }
        if (data.status === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#1a3268'
            });

            // Update UI element text & avatars
            const userDisp = document.getElementById('username-display');
            if (userDisp) userDisp.textContent = nama;

            const profTitle = document.getElementById('prof-title-name');
            if (profTitle) profTitle.textContent = nama;

            document.querySelectorAll('.profile-name-text').forEach(el => el.textContent = nama);

            if (data.foto_profil) {
                document.querySelectorAll('.user-avatar-img, #avatar-preview-img').forEach(img => {
                    img.src = data.foto_profil;
                    img.style.display = 'block';
                });
                document.querySelectorAll('.user-avatar-fallback, #avatar-preview-fallback').forEach(el => {
                    el.style.display = 'none';
                });
            }

            // Reset password fields
            if (document.getElementById('input-prof-old-pass')) document.getElementById('input-prof-old-pass').value = '';
            if (document.getElementById('input-prof-new-pass')) document.getElementById('input-prof-new-pass').value = '';
        } else {
            Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat memperbarui profil.', 'error');
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = 'Simpan Perubahan Profil';
        }
        Swal.fire('Gagal Simpan', err.message || 'Terjadi kesalahan koneksi server.', 'error');
    });
}

function simpanPengaturan() {
    const namaSekolah     = document.getElementById('set-nama-sekolah')?.value?.trim();
    const npsn            = document.getElementById('set-npsn')?.value?.trim();
    const kepsek          = document.getElementById('set-kepsek')?.value?.trim();
    const alamat          = document.getElementById('set-alamat')?.value?.trim();
    const emailSekolah    = document.getElementById('set-email')?.value?.trim();
    const teleponSekolah  = document.getElementById('set-telepon')?.value?.trim();
    const tahunAjaran     = document.getElementById('set-tahun-ajaran')?.value?.trim();
    const semester        = document.getElementById('set-semester')?.value;
    const sistemAbsensi   = document.getElementById('set-sistem-absensi')?.value;
    const batasWaktu      = document.getElementById('set-batas-waktu')?.value;
    const izinEdit        = document.getElementById('set-izin-edit')?.checked ? '1' : '0';
    const waGatewayAktif  = document.getElementById('set-wa-gateway-aktif')?.checked ? '1' : '0';
    const waEndpoint      = document.getElementById('set-wa-endpoint')?.value?.trim();
    const waPublicUrl     = document.getElementById('set-wa-public-url')?.value?.trim();
    const waWakaKesiswaan = document.getElementById('set-wa-waka-kesiswaan')?.value?.trim();
    const waWakaSdm       = document.getElementById('set-wa-waka-sdm')?.value?.trim();
    const waKepsek        = document.getElementById('set-wa-kepsek')?.value?.trim();

    if (!namaSekolah) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nama sekolah tidak boleh kosong.', customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' }, buttonsStyling: false });
        return;
    }
    if (!tahunAjaran) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tahun ajaran tidak boleh kosong.', customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' }, buttonsStyling: false });
        return;
    }

    fetch('/pengaturan/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            nama_sekolah:            namaSekolah,
            npsn:                    npsn,
            kepsek:                  kepsek,
            alamat:                  alamat,
            email_sekolah:           emailSekolah,
            telepon_sekolah:         teleponSekolah,
            tahun_ajaran:            tahunAjaran,
            semester:                semester,
            sistem_absensi:          sistemAbsensi,
            batas_waktu_jurnal:      batasWaktu,
            izin_edit_jurnal:        izinEdit,
            wa_gateway_aktif:        waGatewayAktif,
            wa_gateway_endpoint:     waEndpoint,
            wa_public_url:           waPublicUrl,
            wa_nomor_waka_kesiswaan: waWakaKesiswaan,
            wa_nomor_waka_sdm:       waWakaSdm,
            wa_nomor_kepsek:         waKepsek,
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const elName = document.getElementById('header-school-name');
            const elYear = document.getElementById('header-school-year');
            const elSem  = document.getElementById('header-semester');
            if (elName) elName.textContent = data.data.nama_sekolah;
            if (elYear) elYear.textContent = data.data.tahun_ajaran;
            if (elSem)  elSem.textContent  = data.data.semester;
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan.' });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal terhubung ke server.' });
    });
}

let adminWaPollInterval = null;

function pollStatusBotWa(attempt = 1, maxAttempts = 6) {
    const badge = document.getElementById('wa-bot-status-badge');
    const textEl = document.getElementById('wa-bot-status-text');
    const qrBox = document.getElementById('wa-bot-qr-box');
    const qrImg = document.getElementById('wa-bot-qr-img');
    const accInfo = document.getElementById('wa-bot-account-info');
    const accPhone = document.getElementById('wa-bot-account-phone');

    const btnPutuskan = document.getElementById('btn-putuskan-bot-pengaturan');
    const btnTambah = document.getElementById('btn-tambah-bot-pengaturan');

    if (badge) {
        badge.className = 'badge badge-info';
        badge.style.cssText = 'font-size:11.5px;padding:4px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd';
        badge.innerHTML = '<span style="width:7px;height:7px;background:#0284c7;border-radius:50%;display:inline-block"></span> <span>Memeriksa...</span>';
    }

    fetch('/pengaturan/qr-wa', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'connected') {
            Swal.close();
            if (adminWaPollInterval) {
                clearInterval(adminWaPollInterval);
                adminWaPollInterval = null;
            }
            if (badge) {
                badge.className = 'badge badge-success';
                badge.style.cssText = 'font-size:11.5px;padding:4px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block"></span> <span>Online</span>';
            }
            if (accInfo && accPhone) {
                accPhone.textContent = data.user ? '+' + data.user : 'Terhubung';
                accInfo.style.display = 'flex';
            }
            // Saat WA sudah terhubung, sembunyikan barcode scan
            if (qrBox) qrBox.style.display = 'none';
            // Tampilkan tombol Putuskan, sembunyikan tombol Hubungkan
            if (btnPutuskan) btnPutuskan.style.display = 'inline-flex';
            if (btnTambah) btnTambah.style.display = 'none';

            if (attempt === 1 || attempt === maxAttempts) {
                Swal.fire({
                    icon: 'success',
                    title: 'Bot WhatsApp Terhubung!',
                    text: 'Bot online & siap digunakan' + (data.user ? ' (Nomor: +' + data.user + ')' : ''),
                    timer: 2200,
                    showConfirmButton: false
                });
            }
        } else if (data.status === 'connecting') {
            if (badge) {
                badge.className = 'badge badge-info';
                badge.style.cssText = 'font-size:11.5px;padding:4px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#0284c7;border-radius:50%;display:inline-block"></span> <span>Menghubungkan Sesi...</span>';
            }
            if (accInfo && accPhone && data.user) {
                accPhone.textContent = '+' + data.user;
                accInfo.style.display = 'flex';
            }
            if (qrBox) qrBox.style.display = 'none';
            if (btnPutuskan) btnPutuskan.style.display = 'inline-flex';
            if (btnTambah) btnTambah.style.display = 'none';

            if (!adminWaPollInterval) {
                adminWaPollInterval = setInterval(() => {
                    pollStatusBotWa(2, 2);
                }, 2000);
            }
        } else if (data.status === 'waiting_qr' && data.qr_image) {
            Swal.close();
            if (badge) {
                badge.className = 'badge badge-warning';
                badge.style.cssText = 'font-size:11.5px;padding:4px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#fef3c7;color:#92400e;border:1px solid #fde68a';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#f59e0b;border-radius:50%;display:inline-block"></span> <span>Menunggu Scan QR</span>';
            }
            if (accInfo) accInfo.style.display = 'none';
            // Munculkan barcode baru untuk di-scan jika terputus
            if (qrBox && qrImg) {
                qrImg.src = data.qr_image;
                qrBox.style.display = 'block';
            }
            // Sembunyikan Putuskan, tampilkan Hubungkan / Refresh
            if (btnPutuskan) btnPutuskan.style.display = 'none';
            if (btnTambah) btnTambah.style.display = 'inline-flex';

            // Auto polling jika sedang menunggu scan QR
            if (!adminWaPollInterval) {
                adminWaPollInterval = setInterval(() => {
                    pollStatusBotWa(2, 2);
                }, 3000);
            }
        } else {
            if (accInfo) accInfo.style.display = 'none';
            if (qrBox) qrBox.style.display = 'none';
            if (btnPutuskan) btnPutuskan.style.display = 'none';
            if (btnTambah) btnTambah.style.display = 'inline-flex';

            if (attempt < maxAttempts) {
                setTimeout(() => pollStatusBotWa(attempt + 1, maxAttempts), 1500);
            } else {
                Swal.close();
                if (badge) {
                    badge.className = 'badge badge-danger';
                    badge.style.cssText = 'font-size:11.5px;padding:4px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca';
                    badge.innerHTML = '<span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block"></span> <span>Offline</span>';
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Bot Belum Terhubung',
                    text: data.message || 'Server bot belum aktif. Klik "Hubungkan / Scan Bot".',
                    confirmButtonColor: '#059669'
                });
            }
        }
    })
    .catch(() => {
        if (accInfo) accInfo.style.display = 'none';
        if (qrBox) qrBox.style.display = 'none';
        if (btnPutuskan) btnPutuskan.style.display = 'none';
        if (btnTambah) btnTambah.style.display = 'inline-flex';

        if (attempt < maxAttempts) {
            setTimeout(() => pollStatusBotWa(attempt + 1, maxAttempts), 1500);
        } else {
            Swal.close();
            if (badge) {
                badge.className = 'badge badge-danger';
                badge.style.cssText = 'font-size:11.5px;padding:4px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca';
                badge.innerHTML = '<span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block"></span> <span>Offline</span>';
            }
            Swal.fire({
                icon: 'warning',
                title: 'Bot Belum Aktif',
                text: 'Server bot belum berjalan. Klik tombol "Hubungkan / Scan Bot" untuk mengaktifkan.',
                confirmButtonColor: '#059669'
            });
        }
    });
}

function updateLinkPreviewPengaturan() {
    const input = document.getElementById('set-wa-public-url');
    const preview = document.getElementById('set-preview-link-wa');
    const labelMode = document.getElementById('set-label-url-mode');
    if (!preview) return;

    let val = (input?.value || '').trim();
    if (val) {
        if (!val.startsWith('http://') && !val.startsWith('https://')) {
            val = 'https://' + val;
        }
        val = val.replace(/\/+$/, '');
        preview.textContent = val + '/persetujuan-izin-guru/1/kepsek?signature=xxxx';
        if (labelMode) {
            if (val.includes('trycloudflare') || val.includes('cloudflare')) {
                labelMode.textContent = 'Cloudflare Tunnel Aktif';
            } else if (val.includes('ngrok')) {
                labelMode.textContent = 'Ngrok Tunnel Aktif';
            } else {
                labelMode.textContent = 'Custom Domain Aktif';
            }
        }
    } else {
        const origin = window.location.origin;
        preview.textContent = origin + '/persetujuan-izin-guru/1/kepsek?signature=xxxx';
        if (labelMode) labelMode.textContent = 'Otomatis Menyesuaikan Host/IP';
    }
}

function isiUrlOtomatisPengaturan(type) {
    const input = document.getElementById('set-wa-public-url');
    if (!input) return;

    if (type === 'current') {
        const origin = window.location.origin;
        input.value = origin;
        Swal.fire({
            icon: 'info',
            title: 'Domain Terdeteksi',
            text: 'Menggunakan domain browser saat ini: ' + origin,
            timer: 1800,
            showConfirmButton: false
        });
    } else {
        input.value = '';
        Swal.fire({
            icon: 'info',
            title: 'Mode IP Otomatis',
            text: 'Link akan otomatis menggunakan IP LAN atau host aktif saat surat dibuat.',
            timer: 1800,
            showConfirmButton: false
        });
    }
    updateLinkPreviewPengaturan();
}

function cekStatusBotWa() {
    updateLinkPreviewPengaturan();
    Swal.fire({
        title: 'Memeriksa Status...',
        text: 'Menghubungi server bot WhatsApp...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    pollStatusBotWa(1, 3);
}

function startAtauRestartBotPengaturan() {
    Swal.fire({
        title: 'Menyiapkan Bot WhatsApp...',
        text: 'Memulai server bot dan menyiapkan sesi QR Code...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('/pengaturan/restart-wa', {
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
            pollStatusBotWa(1, 4);
        }, 1500);
    })
    .catch(() => {
        setTimeout(() => {
            pollStatusBotWa(1, 4);
        }, 1500);
    });
}

function putuskanBotWaPengaturan() {
    Swal.fire({
        title: 'Putuskan WhatsApp Bot?',
        text: 'Sesi bot aktif akan di-logout dan koneksi diputuskan. Anda harus scan QR code baru untuk menghubungkan kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Putuskan Bot',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memutuskan Bot...',
                text: 'Menghapus sesi auth dan me-reset WhatsApp bot...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('/pengaturan/disconnect-wa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bot Berhasil Diputuskan',
                        text: 'Sesi WhatsApp telah dihapus. Silakan scan QR code baru jika ingin menghubungkan kembali.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                    pollStatusBotWa(1, 3);
                }, 1500);
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memutuskan',
                    text: err.message || 'Terjadi kesalahan saat memutuskan bot.'
                });
            });
        }
    });
}

function modalTestKirimWa() {
    const defaultNomor = document.getElementById('set-wa-waka-kesiswaan')?.value?.trim()
        || document.getElementById('set-wa-kepsek')?.value?.trim()
        || document.getElementById('set-wa-waka-sdm')?.value?.trim()
        || '';

    Swal.fire({
        title: 'Uji Coba Kirim Pesan WA',
        html: `
            <div style="text-align:left;display:grid;gap:10px;margin-top:10px">
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Nomor WhatsApp Tujuan:</label>
                    <input type="text" id="swal-test-phone" class="swal2-input" style="margin:4px 0 0 0;width:100%" placeholder="Contoh: 081234567890" value="${defaultNomor}">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#475569">Isi Pesan Uji Coba:</label>
                    <textarea id="swal-test-pesan" class="swal2-textarea" style="margin:4px 0 0 0;width:100%;height:80px" placeholder="Pesan tes...">Halo! Ini adalah pesan uji coba integrasi WhatsApp Bot PresensiKita.</textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim Pesan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16a34a',
        preConfirm: () => {
            const phone = document.getElementById('swal-test-phone').value.trim();
            const msg = document.getElementById('swal-test-pesan').value.trim();
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

            fetch('/pengaturan/test-wa', {
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

function tambahGuruModal() {
    Swal.fire({
        title: 'Tambah Guru Baru',
        html: `
            <div class="swal-form-container">
                <div class="swal-form-group">
                    <label for="swal-gname">Nama Lengkap & Gelar</label>
                    <input id="swal-gname" class="swal-form-input" placeholder="Contoh: Budi Santoso, S.Pd.">
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-gnip">NIP (Opsional)</label>
                        <input id="swal-gnip" class="swal-form-input" placeholder="19850101...">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-gperan">Peran</label>
                        <select id="swal-gperan" class="swal-form-select">
                            ${(typeof daftarPeran !== 'undefined' ? daftarPeran : []).map(r => `<option value="${r.nama}">${r.nama}</option>`).join('')}
                        </select>
                    </div>
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-ghp">No. HP (Opsional)</label>
                        <input id="swal-ghp" class="swal-form-input" placeholder="08123456789">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-gadmin">Hak Akses</label>
                        <select id="swal-gadmin" class="swal-form-select">
                            <option value="0">Guru Biasa</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="swal-form-row">
                    <div class="swal-form-group">
                        <label for="swal-gusername">Username Login <span style="font-weight:400;color:#94a3b8;font-size:11px">(auto dari nama depan)</span></label>
                        <input id="swal-gusername" class="swal-form-input" placeholder="contoh: budi">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-gpass">Password <span style="font-weight:400;color:#94a3b8;font-size:11px">(auto: username+123)</span></label>
                        <input type="text" id="swal-gpass" class="swal-form-input" placeholder="Minimal 4 karakter">
                    </div>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Data Guru',
        cancelButtonText: 'Batal',
        didOpen: () => {
            const TITLES = ['dra','drs','dr','prof','ir','pdt','hj','h','st','spd','se','sh','ssi','sos','sag','skom','si','mt','mm','msi','mpd','mba','mhum','msn','mkes'];
            function getFirstName(fullName) {
                const beforeComma = fullName.split(',')[0].trim();
                const parts = beforeComma.split(/\s+/);
                for (const part of parts) {
                    const clean = part.replace(/[^a-zA-Z]/g, '').toLowerCase();
                    if (clean && clean.length > 1 && !TITLES.includes(clean)) {
                        return clean;
                    }
                }
                return parts[0] ? parts[0].replace(/[^a-z0-9]/gi, '').toLowerCase() : '';
            }

            const namaEl = document.getElementById('swal-gname');
            const usnEl  = document.getElementById('swal-gusername');
            const pwEl   = document.getElementById('swal-gpass');

            let usnManual = false;
            let pwManual  = false;

            usnEl.addEventListener('input', () => { usnManual = true; });
            pwEl.addEventListener('input',  () => { pwManual  = true; });

            namaEl.addEventListener('input', () => {
                const firstName = getFirstName(namaEl.value.trim());
                if (!usnManual && firstName) usnEl.value = firstName;
                if (!pwManual  && firstName) pwEl.value  = firstName + '123';
            });
        },
        preConfirm: () => {
            const nama     = document.getElementById('swal-gname').value.trim();
            const username = document.getElementById('swal-gusername').value.trim();
            const pass     = document.getElementById('swal-gpass').value.trim();

            if (!nama) { Swal.showValidationMessage('Nama guru tidak boleh kosong'); return false; }
            if (!username) { Swal.showValidationMessage('Username tidak boleh kosong'); return false; }
            if (!pass || pass.length < 4) { Swal.showValidationMessage('Password minimal 4 karakter'); return false; }

            return {
                nama_guru: nama,
                nip:       document.getElementById('swal-gnip').value.trim(),
                peran:     document.getElementById('swal-gperan').value,
                no_hp:     document.getElementById('swal-ghp').value.trim(),
                is_admin:  parseInt(document.getElementById('swal-gadmin').value),
                username:  username,
                password:  pass
            };
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            fetch('/guru/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message })
                    .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan saat menyimpan data.' });
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Username mungkin sudah terpakai atau terjadi masalah server.', 'error');
            });
        }
    });
}

const chartTooltip = {
    backgroundColor: '#1e293b',
    titleColor: '#f1f5f9',
    bodyColor: '#cbd5e1',
    borderColor: '#334155',
    borderWidth: 1,
    padding: 10,
    cornerRadius: 8
};

let laporanCharts = { donut: null, line: null, bar: null };
let laporanPage = 1;
let laporanChartsReady = false;

function getChartGridColor() {
    return document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255,255,255,0.08)' : '#f1f5f9';
}

function initChart(){
    const ctx = document.getElementById('lineChart');
    if (!ctx) return;
    const tren = (window.dashboardTren && !Array.isArray(window.dashboardTren))
        ? window.dashboardTren
        : { labels: [], hadir: [], sakit: [], izin: [], alpa: [] };
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: tren.labels || [],
            datasets: [
                { label: 'Hadir', data: tren.hadir || [], borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.08)', tension: 0.4, fill: true, pointBackgroundColor: '#22c55e', pointRadius: 4, borderWidth: 2.5 },
                { label: 'Sakit', data: tren.sakit || [], borderColor: '#f59e0b', backgroundColor: 'transparent', tension: 0.4, pointBackgroundColor: '#f59e0b', pointRadius: 4, borderWidth: 2 },
                { label: 'Izin', data: tren.izin || [], borderColor: '#3b82f6', backgroundColor: 'transparent', tension: 0.4, pointBackgroundColor: '#3b82f6', pointRadius: 4, borderWidth: 2 },
                { label: 'Alpa', data: tren.alpa || [], borderColor: '#ef4444', backgroundColor: 'transparent', tension: 0.4, pointBackgroundColor: '#ef4444', pointRadius: 4, borderWidth: 2 },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false, ...chartTooltip }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11, family: 'Inter' } } },
                y: { beginAtZero: true, grid: { color: getChartGridColor() }, ticks: { color: '#94a3b8', font: { size: 11, family: 'Inter' } } }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
}

function destroyLaporanCharts() {
    Object.keys(laporanCharts).forEach(key => {
        if (laporanCharts[key]) {
            laporanCharts[key].destroy();
            laporanCharts[key] = null;
        }
    });
    laporanChartsReady = false;
}

function renderLaporanCharts(rekap) {
    const donutEl = document.getElementById('laporanDonutChart');
    const lineEl = document.getElementById('laporanLineChart');
    const barEl = document.getElementById('laporanBarChart');
    if (!donutEl || !lineEl || !barEl) return;

    destroyLaporanCharts();

    const hadir = Number(rekap.hadir || 0);
    const sakit = Number(rekap.sakit || 0);
    const izin = Number(rekap.izin || 0);
    const dispen = Number(rekap.dispen || 0);
    const alpa = Number(rekap.alpa || 0);
    const donutValues = [hadir, sakit, izin, dispen, alpa];
    const donutSum = donutValues.reduce((a, b) => a + b, 0);

    laporanCharts.donut = new Chart(donutEl, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Dispensasi', 'Alpa'],
            datasets: [{
                data: donutSum > 0 ? donutValues : [0, 0, 0, 0, 0],
                backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#7c3aed', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: { legend: { display: false }, tooltip: chartTooltip }
        }
    });

    const tren = rekap.tren || { labels: [], pct: [] };
    laporanCharts.line = new Chart(lineEl, {
        type: 'line',
        data: {
            labels: tren.labels || [],
            datasets: [{
                label: 'Kehadiran %',
                data: tren.pct || [],
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#22c55e',
                pointRadius: 3,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: chartTooltip },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10, family: 'Inter' } } },
                y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 10, family: 'Inter' } } }
            }
        }
    });

    const hari = rekap.rekap_hari || { labels: ['Sen','Sel','Rab','Kam','Jum','Sab'], hadir: [0,0,0,0,0,0], sakit: [0,0,0,0,0,0], izin: [0,0,0,0,0,0], dispen: [0,0,0,0,0,0], alpa: [0,0,0,0,0,0] };
    laporanCharts.bar = new Chart(barEl, {
        type: 'bar',
        data: {
            labels: hari.labels,
            datasets: [
                { label: 'Hadir', data: hari.hadir, backgroundColor: '#22c55e', stack: 'absensi', borderRadius: 3 },
                { label: 'Sakit', data: hari.sakit, backgroundColor: '#f59e0b', stack: 'absensi', borderRadius: 3 },
                { label: 'Izin', data: hari.izin, backgroundColor: '#3b82f6', stack: 'absensi', borderRadius: 3 },
                { label: 'Dispensasi', data: hari.dispen, backgroundColor: '#7c3aed', stack: 'absensi', borderRadius: 3 },
                { label: 'Alpa', data: hari.alpa, backgroundColor: '#ef4444', stack: 'absensi', borderRadius: 3 },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { ...chartTooltip, mode: 'index', intersect: false }
            },
            scales: {
                x: { stacked: true, grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11, family: 'Inter' } } },
                y: { stacked: true, beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11, family: 'Inter' } } }
            }
        }
    });

    laporanChartsReady = true;
}

function initLaporanCharts() {
    if (laporanChartsReady) return;
    const rekap = window.laporanInitial || {
        hadir: 0, sakit: 0, izin: 0, alpa: 0,
        tren: { labels: [], pct: [] },
        rekap_hari: null
    };
    renderLaporanCharts(rekap);
    initLaporanPagination();
}

function updateLaporanKelasLabel(select) {
    const sub = document.getElementById('laporan-subtitle');
    if (sub && select && select.selectedIndex >= 0) {
        sub.textContent = 'Kelas ' + select.options[select.selectedIndex].text.trim();
    }
}

function formatNumber(n) {
    return Number(n || 0).toLocaleString('id-ID');
}

function laporanBadgeStyle(pct) {
    if (pct >= 85) return 'background:#dcfce7;color:#15803d;';
    if (pct >= 75) return 'background:#fef3c7;color:#b45309;';
    return 'background:#fee2e2;color:#b91c1c;';
}

function applyLaporanRekap(rekap) {
    const page = document.getElementById('page-laporan');
    if (!page || !rekap) return;

    page.dataset.hadir = rekap.hadir;
    page.dataset.sakit = rekap.sakit;
    page.dataset.izin = rekap.izin;
    page.dataset.alpa = rekap.alpa;
    page.dataset.pctHadir = rekap.pct_hadir;
    page.dataset.pctSakit = rekap.pct_sakit;
    page.dataset.pctIzin = rekap.pct_izin;
    page.dataset.pctAlpa = rekap.pct_alpa;

    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    setText('laporan-stat-siswa', formatNumber(rekap.total_siswa));
    setText('laporan-stat-hadir', formatNumber(rekap.hadir));
    setText('laporan-stat-sakit', formatNumber(rekap.sakit));
    setText('laporan-stat-izin', formatNumber(rekap.izin));
    setText('laporan-stat-alpa', formatNumber(rekap.alpa));
    setText('laporan-ring-pct', `${rekap.pct_hadir}%`);
    setText('laporan-ring-label', rekap.pct_label || '-');
    setText('laporan-leg-hadir', `${rekap.pct_hadir}%`);
    setText('laporan-leg-sakit', `${rekap.pct_sakit}%`);
    setText('laporan-leg-izin', `${rekap.pct_izin}%`);
    setText('laporan-leg-alpa', `${rekap.pct_alpa}%`);

    const ringFg = document.getElementById('laporan-ring-fg');
    if (ringFg) ringFg.setAttribute('stroke-dasharray', `${rekap.pct_hadir}, 100`);

    if (rekap.nama_kelas) {
        const sub = document.getElementById('laporan-subtitle');
        if (sub) sub.textContent = 'Kelas ' + rekap.nama_kelas;
    }

    const tbody = document.getElementById('laporan-tbody');
    const siswa = rekap.siswa || [];
    if (tbody) {
        if (siswa.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#94a3b8">Belum ada data siswa untuk ditampilkan.</td></tr>';
        } else {
            tbody.innerHTML = siswa.map((s, idx) => `
                <tr class="laporan-row" data-page="${Math.floor(idx / 5) + 1}">
                    <td style="color:#94a3b8;font-weight:600">${idx + 1}</td>
                    <td style="font-weight:600;color:#1e293b">${s.nama_siswa}</td>
                    <td style="text-align:center;color:#16a34a;font-weight:600">${s.hadir}</td>
                    <td style="text-align:center;color:#d97706;font-weight:600">${s.sakit}</td>
                    <td style="text-align:center;color:#2563eb;font-weight:600">${s.izin}</td>
                    <td style="text-align:center;color:#dc2626;font-weight:600">${s.alpa}</td>
                    <td style="text-align:center"><span style="padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;display:inline-block;${laporanBadgeStyle(s.persentase)}">${s.persentase}%</span></td>
                    <td style="color:#475569;font-weight:500">${s.keterangan}</td>
                </tr>
            `).join('');
        }
    }

    const pag = document.getElementById('laporan-pagination');
    if (pag) {
        pag.dataset.total = String(siswa.length);
        pag.dataset.perPage = '5';
    }
    laporanPage = 1;
    window.laporanInitial = rekap;
    renderLaporanCharts(rekap);
    initLaporanPagination();
}

function tampilkanLaporan() {
    const kelasId = document.getElementById('laporan-kelas')?.value;
    const bulan = document.getElementById('laporan-bulan')?.value;
    const dataFilter = document.getElementById('laporan-data')?.value || 'semua';
    const tahun = new Date().getFullYear();

    if (!kelasId) return;

    const btn = document.querySelector('.laporan-btn.btn-primary');
    if (btn) {
        btn.disabled = true;
        btn.style.opacity = '0.7';
    }

    const params = new URLSearchParams({
        kelas_id: kelasId,
        bulan: bulan,
        tahun: String(tahun),
        data: dataFilter
    });

    fetch(`/laporan/data?${params.toString()}`)
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success') {
                Swal.fire('Gagal', res.message || 'Tidak dapat memuat laporan.', 'error');
                return;
            }
            applyLaporanRekap(res.data);
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat laporan.', 'error');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '';
            }
        });
}

function exportLaporanPdf() {
    const kelasId = document.getElementById('laporan-kelas')?.value || '';
    const bulan = document.getElementById('laporan-bulan')?.value || '';
    const dataFilter = document.getElementById('laporan-data')?.value || 'semua';
    const tahun = new Date().getFullYear();

    const params = new URLSearchParams({
        kelas_id: kelasId,
        bulan: bulan,
        tahun: String(tahun),
        data: dataFilter
    });

    window.location.href = `/laporan/export-pdf?${params.toString()}`;
}

function exportRiwayatPdf() {
    const bulanSelect = document.getElementById('riwayat-filter-bulan');
    const kelasSelect = document.getElementById('riwayat-filter-kelas');

    const bulan = bulanSelect ? parseInt(bulanSelect.value, 10) : '';
    const kelas = kelasSelect?.value || '';
    const tahun = new Date().getFullYear();

    const params = new URLSearchParams({
        kelas_id: kelas,
        bulan: String(bulan),
        tahun: String(tahun)
    });

    window.location.href = `/riwayat/export-pdf?${params.toString()}`;
}

function initLaporanPagination() {
    const pag = document.getElementById('laporan-pagination');
    if (!pag) return;
    const total = Number(pag.dataset.total || 0);
    const perPage = Number(pag.dataset.perPage || 5);
    const pages = Math.max(1, Math.ceil(total / perPage) || 1);
    if (laporanPage > pages) laporanPage = pages;

    let html = `<button type="button" class="laporan-page-btn" ${laporanPage <= 1 ? 'disabled' : ''} data-goto="${laporanPage - 1}">Prev</button>`;
    for (let i = 1; i <= pages; i++) {
        html += `<button type="button" class="laporan-page-btn ${i === laporanPage ? 'active' : ''}" data-goto="${i}">${i}</button>`;
    }
    html += `<button type="button" class="laporan-page-btn" ${laporanPage >= pages ? 'disabled' : ''} data-goto="${laporanPage + 1}">Next</button>`;
    pag.innerHTML = html;

    pag.querySelectorAll('.laporan-page-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const goto = Number(btn.dataset.goto);
            if (!goto || goto === laporanPage) return;
            laporanPage = goto;
            applyLaporanPage();
            initLaporanPagination();
        });
    });
    applyLaporanPage();
}

function applyLaporanPage() {
    const rows = document.querySelectorAll('#laporan-tbody .laporan-row');
    const pag = document.getElementById('laporan-pagination');
    const info = document.getElementById('laporan-page-info');
    const total = Number(pag?.dataset.total || rows.length);
    const perPage = Number(pag?.dataset.perPage || 5);
    rows.forEach(row => {
        const page = Number(row.dataset.page);
        row.style.display = page === laporanPage ? '' : 'none';
    });
    if (info) {
        if (total === 0) {
            info.textContent = 'Menampilkan 0 - 0 dari 0 data';
        } else {
            const start = (laporanPage - 1) * perPage + 1;
            const end = Math.min(laporanPage * perPage, total);
            info.textContent = `Menampilkan ${start} - ${end} dari ${total} data`;
        }
    }
}

let jamScheduleCache = null;

function applyPeriodFromData(data) {
    const periodEl = document.getElementById('header-period');
    if (!periodEl || !data) return;

    const now = new Date();
    const day = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][now.getDay()];
    const totalSeconds = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();
    const toSeconds = value => {
        if (!value) return 0;
        const parts = String(value).split(':').map(Number);
        return (parts[0] || 0) * 3600 + (parts[1] || 0) * 60 + (parts[2] || 0);
    };

    const istirahatList = data.istirahat_semua?.[day] || [];
    const istirahat = istirahatList.find(item => {
        const m = toSeconds(item.mulai);
        const s = toSeconds(item.selesai);
        return totalSeconds >= m && totalSeconds < s;
    });

    if (istirahat) {
        periodEl.textContent = `Istirahat ${istirahat.nomor}`;
        periodEl.title = `Istirahat ${istirahat.nomor} (${String(istirahat.mulai).slice(0, 5)} - ${String(istirahat.selesai).slice(0, 5)})`;
        return;
    }

    const jamList = data.jam_semua?.[day] || [];
    const jam = jamList.find(item => {
        const m = toSeconds(item.jam_mulai);
        const s = toSeconds(item.jam_selesai);
        return totalSeconds >= m && totalSeconds < s;
    });

    if (jam) {
        periodEl.textContent = `Jam ke-${jam.jam_ke}`;
        periodEl.title = `Jam ke-${jam.jam_ke} (${String(jam.jam_mulai).slice(0, 5)} - ${String(jam.jam_selesai).slice(0, 5)})`;
    } else {
        periodEl.textContent = 'Di luar jam';
        periodEl.title = 'Di luar jam pelajaran';
    }
}

function bukaJurnalKelas(kelasId) {
    showPage('jurnal-absensi');
    const page = document.getElementById('page-jurnal-absensi');
    if (!page) return;
    const select = page.querySelector('#pilih-kelas');
    if (select && kelasId) {
        select.value = String(kelasId);
        loadSiswaByKelas(kelasId);
    }
}
window.bukaJurnalKelas = bukaJurnalKelas;

function updateJadwalGuruRows() {
    const rows = document.querySelectorAll('.jadwal-row-item');
    if (!rows.length) return;

    const now = new Date();
    const totalSeconds = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();
    const toSeconds = value => {
        if (!value) return 0;
        const parts = String(value).split(':').map(Number);
        return (parts[0] || 0) * 3600 + (parts[1] || 0) * 60 + (parts[2] || 0);
    };

    rows.forEach(row => {
        const mulai = toSeconds(row.dataset.mulai);
        const selesai = toSeconds(row.dataset.selesai);
        const kelasId = row.dataset.kelas;
        const hasJurnal = row.dataset.hasJurnal === '1';
        const isSedang = totalSeconds >= mulai && totalSeconds < selesai;
        const isBelum = totalSeconds < mulai;
        const isSelesai = totalSeconds >= selesai;

        const statusCell = row.querySelector('.status-cell');
        const actionCell = row.querySelector('.action-cell');

        row.style.background = isSedang ? '#f0fdf4' : '';

        if (statusCell) {
            if (isSedang) {
                if (hasJurnal) {
                    statusCell.innerHTML = `<span class="badge badge-success" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:700;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0"><span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>Sudah Diisi</span>`;
                } else {
                    statusCell.innerHTML = `<span class="badge badge-success" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:700"><span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite"></span>Sedang Berlangsung</span>`;
                }
            } else if (isBelum) {
                statusCell.innerHTML = `<span class="badge badge-info" style="padding:5px 10px;font-weight:600;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">Belum Dimulai</span>`;
            } else {
                if (hasJurnal) {
                    statusCell.innerHTML = `<span class="badge" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;font-weight:600;background:#f8fafc;color:#15803d;border:1px solid #e2e8f0"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Sudah Diisi</span>`;
                } else {
                    statusCell.innerHTML = `<span class="badge" style="background:#fef2f2;color:#b91c1c;padding:5px 10px;font-weight:600;border:1px solid #fecaca">Tidak Diisi</span>`;
                }
            }
        }

        if (actionCell) {
            if (isSedang) {
                if (hasJurnal) {
                    actionCell.innerHTML = `<button type="button" class="btn-warning btn-isi-jurnal" onclick="bukaJurnalKelas('${kelasId}')" style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#f59e0b;color:#fff;border:none;outline:none;cursor:pointer;box-shadow:0 2px 6px rgba(245,158,11,0.25)" title="Edit jurnal yang sedang aktif"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Jurnal</button>`;
                } else {
                    actionCell.innerHTML = `<button type="button" class="btn-primary btn-isi-jurnal" onclick="bukaJurnalKelas('${kelasId}')" style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#16a34a;color:#fff;border:none;outline:none;cursor:pointer;box-shadow:0 2px 6px rgba(22,163,74,0.25)" title="Isi jurnal sekarang"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg> Isi Jurnal</button>`;
                }
            } else if (isBelum) {
                actionCell.innerHTML = `<button type="button" class="btn-disabled-jurnal" disabled style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;cursor:not-allowed;box-shadow:none" title="Belum waktunya, jam pelajaran belum dimulai"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Belum Dimulai</button>`;
            } else {
                actionCell.innerHTML = `<button type="button" class="btn-disabled-jurnal" disabled style="padding:7px 14px;font-size:12px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;cursor:not-allowed;box-shadow:none" title="Jam pelajaran telah selesai">${hasJurnal ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Selesai' : '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Terlewat'}</button>`;
            }
        }
    });
}

function updateDate(){
    const now=new Date();
    const days=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const dateEl=document.getElementById('header-date');
    const timeEl=document.getElementById('header-time');
    if(dateEl) dateEl.textContent=`${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    if(timeEl) timeEl.textContent=now.toLocaleTimeString('id-ID', { hour12: false });
    if(jamScheduleCache) applyPeriodFromData(jamScheduleCache);
    updateJadwalGuruRows();
}

window.bukaImportGuruModal = function(){
    Swal.fire({
        title: 'Upload CSV Guru',
        customClass: {
            popup: 'custom-swal-popup',
            title: 'custom-swal-title',
            confirmButton: 'custom-swal-confirm',
            cancelButton: 'custom-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div style="text-align:left;padding:4px 0">
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:14px">
                    <div style="font-size:13px;font-weight:700;color:#16a34a;margin-bottom:4px">Keterangan File</div>
                    <div style="font-size:12px;color:#334155;line-height:1.6">
                        Upload data guru menggunakan file <strong>CSV (.csv), Excel (.xlsx/.xls), atau TXT</strong> (maksimal 25MB).
                        Guru dengan NIP yang sudah terdaftar akan diperbarui datanya, sedangkan NIP baru otomatis ditambahkan sebagai guru aktif.
                    </div>
                </div>
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:14px">
                    <div style="font-size:13px;font-weight:700;color:#2563eb;margin-bottom:6px">Ketentuan Format Data (kolom wajib)</div>
                    <table style="width:100%;border-collapse:collapse;font-size:12px">
                        <thead>
                            <tr style="background:#dbeafe;color:#1e40af;text-align:left">
                                <th style="padding:6px 10px;border:1px solid #bfdbfe;width:44px">No</th>
                                <th style="padding:6px 10px;border:1px solid #bfdbfe">Nama Guru</th>
                                <th style="padding:6px 10px;border:1px solid #bfdbfe">NIP</th>
                                <th style="padding:6px 10px;border:1px solid #bfdbfe">Kode Mapel</th>
                                <th style="padding:6px 10px;border:1px solid #bfdbfe;width:90px">Total Jam Mengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background:#fff;color:#334155">
                                <td style="padding:6px 10px;border:1px solid #bfdbfe;color:#94a3b8">1</td>
                                <td style="padding:6px 10px;border:1px solid #bfdbfe">Martiin, S.Pd</td>
                                <td style="padding:6px 10px;border:1px solid #bfdbfe;font-family:monospace">19670604 198903 2 009</td>
                                <td style="padding:6px 10px;border:1px solid #bfdbfe;font-family:monospace">K.MP</td>
                                <td style="padding:6px 10px;border:1px solid #bfdbfe">26</td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="font-size:11px;color:#64748b;margin-top:6px">Baris pertama file wajib berisi judul kolom seperti tabel di atas.</div>
                </div>
                <div style="position:relative;border:2px dashed #cbd5e1;border-radius:10px;padding:24px 16px;text-align:center;cursor:pointer;transition:all 0.2s;background:#f8fafc" id="guru-csv-dropzone"
                     onclick="document.getElementById('guru-csv-file-input').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#22c55e';this.style.background='#f0fdf4'"
                     ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc'"
                     ondrop="event.preventDefault();this.style.borderColor='#cbd5e1';this.style.background='#f8fafc';handleGuruCsvFile(event.dataTransfer.files[0])">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 8px;display:block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <div style="font-size:13.5px;color:#64748b;font-weight:600">Klik atau seret file di sini</div>
                    <div style="font-size:11.5px;color:#94a3b8;margin-top:4px">Format .csv, .txt, .xlsx, .xls (Maks 25MB)</div>
                    <div id="guru-csv-filename" style="font-size:12.5px;color:#22c55e;font-weight:600;margin-top:8px;display:none"></div>
                </div>
                <input type="file" id="guru-csv-file-input" accept=".csv,.txt,.xlsx,.xls" style="display:none" onchange="handleGuruCsvFile(this.files[0])">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:6px;vertical-align:-2px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Upload',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
            if (!window._guruCsvFile) {
                Swal.showValidationMessage('Pilih file CSV/Excel/TXT terlebih dahulu');
                return false;
            }
            return window._guruCsvFile;
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            const formData = new FormData();
            formData.append('file_guru', result.value);

            Swal.fire({
                title: 'Mengupload...',
                html: '<div style="color:#64748b;font-size:13.5px">Sedang memproses data guru...</div>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title' },
                buttonsStyling: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('/guru/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok || data.status === 'error') throw new Error(data.message || 'Import gagal.');
                return data;
            })
            .then(data => {
                let detailHtml = `<div style="text-align:left;font-size:13.5px;color:#475569;line-height:1.8">`;
                detailHtml += `<div style="font-size:20px;font-weight:800;color:#16a34a;margin-bottom:4px">${data.message}</div>`;
                if (data.skipped && data.skipped.length > 0) {
                    detailHtml += `<div style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0">`;
                    detailHtml += `<div style="font-weight:700;color:#e11d48;margin-bottom:4px">${data.skipped.length} baris dilewati:</div>`;
                    detailHtml += `<div style="max-height:120px;overflow-y:auto;font-size:12px;color:#64748b;background:#fff1f2;padding:8px 10px;border-radius:6px">`;
                    data.skipped.forEach(e => { detailHtml += `<div style="margin-bottom:2px">• ${e}</div>`; });
                    detailHtml += `</div></div>`;
                }
                detailHtml += `</div>`;

                Swal.fire({
                    icon: 'success',
                    title: 'Upload Selesai!',
                    html: detailHtml,
                    customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                    buttonsStyling: false,
                    confirmButtonText: 'OK'
                }).then(() => reloadCurrentPage());
            })
            .catch(error => Swal.fire({
                icon: 'error',
                title: 'Import Gagal',
                text: error.message || 'Terjadi kesalahan sistem.',
                customClass: { popup: 'custom-swal-popup', title: 'custom-swal-title', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false
            }));
        }
    });
};

window._guruCsvFile = null;
window.handleGuruCsvFile = function(file) {
    if (!file) return;
    window._guruCsvFile = file;
    const nameEl = document.getElementById('guru-csv-filename');
    if (nameEl) {
        nameEl.textContent = file.name;
        nameEl.style.display = 'block';
    }
};

function refreshJamPelajaran(){
    const periodEl = document.getElementById('header-period');
    if (!periodEl) return;

    fetch('/jam-pelajaran/sekarang', { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') return;
            jamScheduleCache = data;
            applyPeriodFromData(data);
        })
        .catch(err => console.error('Error mengecek jam pelajaran:', err));
}

document.addEventListener('DOMContentLoaded',function(){
    updateDate();
    window.setInterval(updateDate, 1000);
    refreshJamPelajaran();
    window.setInterval(refreshJamPelajaran, 10000);
    refreshJadwalGuru();
    window.setInterval(refreshJadwalGuru, 10000);
    initChart();
    initLaporanPagination();
    renderTable(currentSiswaList);
    window.addEventListener('resize', function(){
        if (window.innerWidth > 992) closeSidebarMobile();
    });

    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('page-' + hash)) {
        showPage(hash);
    }
});

/* ── Custom Delete Confirmation Helper ── */
function confirmDeleteData({ title, itemName, warningText, onConfirm }) {
    Swal.fire({
        html: `
            <div style="padding: 4px 0;">
                <div style="
                    width: 60px; height: 60px;
                    background: #fef2f2;
                    border-radius: 50%;
                    display: flex; align-items: center; justify-content: center;
                    margin: 0 auto 18px;
                    border: 6px solid #fee2e2;
                ">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        <line x1="10" y1="11" x2="10" y2="17"/>
                        <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                </div>
                <div style="font-size: 19px; font-weight: 800; color: #1e293b; margin-bottom: 8px; font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
                    ${title}
                </div>
                <div style="font-size: 13.5px; color: #64748b; line-height: 1.55; font-family: 'Inter', sans-serif;">
                    Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?<br>
                    ${warningText ? `<small style="color:#94a3b8;font-size:12px;">${warningText}</small>` : ''}
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            popup:          'swal-delete-popup',
            confirmButton:  'swal-delete-confirm',
            cancelButton:   'swal-delete-cancel',
            actions:        'swal-delete-actions',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
}

/* ── Soft Delete Handlers ── */
function hapusGuru(id, nama, jadwalCount = 0) {
    if (jadwalCount > 0 && typeof tampilkanPeringatanJadwalGuru === 'function') {
        tampilkanPeringatanJadwalGuru(id, nama, jadwalCount, 'dihapus');
        return;
    }

    confirmDeleteData({
        title: 'Hapus Data Guru?',
        itemName: `guru ${nama}`,
        onConfirm: () => {
            fetch(`/guru/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'has_jadwal' && typeof tampilkanPeringatanJadwalGuru === 'function') {
                    tampilkanPeringatanJadwalGuru(id, nama, res.jadwal_count || jadwalCount, 'dihapus');
                    return;
                }
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.' }));
        }
    });
}

function hapusSiswa(id, nama) {
    confirmDeleteData({
        title: 'Hapus Data Siswa?',
        itemName: `siswa ${nama}`,
        onConfirm: () => {
            fetch(`/siswa/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.' }));
        }
    });
}

function hapusSemuaGuru() {
    confirmDeleteData({
        title: 'Hapus Semua Data Guru?',
        itemName: 'semua data guru',
        warningText: 'Semua akun guru akan dihapus PERMANEN (tidak bisa dikembalikan). Relasi guru (jadwal, kelas, izin, dll) ikut terhapus/hilang. Akun admin yang sedang login tidak ikut dihapus.',
        onConfirm: () => {
            fetch('/guru/hapus-semua', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false })
                        .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.' }));
        }
    });
}

function hapusSemuaSiswa() {
    confirmDeleteData({
        title: 'Hapus Semua Data Siswa?',
        itemName: 'semua data siswa',
        warningText: 'Semua data siswa akan dihapus permanen secara soft delete dari aplikasi.',
        onConfirm: () => {
            fetch('/siswa/hapus-semua', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.' }));
        }
    });
}

function hapusKelas(id, nama) {
    confirmDeleteData({
        title: 'Hapus Data Kelas?',
        itemName: `kelas ${nama}`,
        onConfirm: () => {
            fetch(`/kelas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.' }));
        }
    });
}

function hapusMapel(id, nama, jadwalCount) {
    if (jadwalCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat dihapus',
            html: `Mapel <strong>${nama}</strong> masih dipakai di <strong>${jadwalCount}</strong> jadwal mengajar.`,
        });
        return;
    }

    confirmDeleteData({
        title: 'Hapus Mata Pelajaran?',
        itemName: `mapel ${nama}`,
        warningText: 'Data mata pelajaran ini akan dihapus.',
        onConfirm: () => {
            fetch(`/mapel/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => reloadCurrentPage());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan sistem.' }));
        }
    });
}



/* ── Modal Notifikasi Header ───────────────────────────── */
function tampilkanNotifikasi() {
    // Ambil notifikasi dari server (table notifikasi di DB)
    fetch(window.notifUrl || '/admin/notifikasi')
        .then(r => r.json())
        .then(data => {
            const items = data.notifikasi || [];
            const jumlahBaru = items.filter(n => !n.is_read).length;

            let htmlIsi = '';
            if (items.length === 0) {
                htmlIsi = `
                    <div style="text-align:center;padding:32px 16px;color:#94a3b8">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:0.4"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <div style="font-size:13.5px;font-weight:600;color:#475569">Belum ada pemberitahuan</div>
                        <div style="font-size:12px;margin-top:4px">Notifikasi sistem akan muncul di sini.</div>
                    </div>`;
            } else {
                htmlIsi = '<div style="text-align:left;display:flex;flex-direction:column;gap:10px;margin-top:10px;max-height:340px;overflow-y:auto">';
                const iconMap = {
                    info:    { bg:'#eff6ff', color:'#2563eb', svg:'<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>' },
                    success: { bg:'#f0fdf4', color:'#16a34a', svg:'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>' },
                    warning: { bg:'#fffbeb', color:'#d97706', svg:'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>' },
                    error:   { bg:'#fef2f2', color:'#ef4444', svg:'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="16" x2="12.01" y2="16"/>' },
                };
                items.forEach(n => {
                    const ic = iconMap[n.tipe] || iconMap.info;
                    const bg = n.is_read ? '#f8fafc' : '#eff6ff';
                    const isTerlambat = (n.judul || '').toLowerCase().includes('terlambat');
                    htmlIsi += `
                        <div style="display:flex;gap:12px;padding:12px;background:${bg};border-radius:10px;border:1px solid #e2e8f0;align-items:flex-start;${isTerlambat ? 'cursor:pointer;' : ''}" ${isTerlambat ? `onclick="Swal.close();tampilkanModalSiswaTerlambat(${n.id_kelas ? `'${n.id_kelas}'` : 'null'});"` : ''}>
                            <div style="width:34px;height:34px;background:${ic.bg};border-radius:8px;display:flex;align-items:center;justify-content:center;color:${ic.color};flex-shrink:0;margin-top:2px">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${ic.svg}</svg>
                            </div>
                            <div style="flex:1">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                                    <div style="font-size:13px;font-weight:700;color:#1e293b">${n.judul}</div>
                                    ${isTerlambat ? '<span style="font-size:10.5px;color:#ea580c;background:#fff7ed;padding:2px 6px;border-radius:4px;font-weight:700;border:1px solid #fed7aa">Lihat Detail &rarr;</span>' : ''}
                                </div>
                                <div style="font-size:12px;color:#64748b;margin-top:2px">${n.pesan}</div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:4px">${n.waktu_relatif || ''}</div>
                            </div>
                        </div>`;
                });
                htmlIsi += '</div>';
            }

            Swal.fire({
                title: `<div style="display:flex;align-items:center;justify-content:space-between;width:100%;padding-bottom:10px;border-bottom:1px solid #e2e8f0;font-size:16px;font-weight:800;color:#0f172a">
                    <span>Pemberitahuan Sistem</span>
                    ${jumlahBaru > 0 ? `<span style="font-size:11.5px;font-weight:600;background:#eff6ff;color:#2563eb;padding:3px 10px;border-radius:99px">${jumlahBaru} Baru</span>` : ''}
                </div>`,
                customClass: { popup: 'custom-swal-popup', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false,
                confirmButtonText: 'Tutup',
                html: htmlIsi,
            }).then(() => {
                const badge = document.getElementById('notif-badge-count');
                if (badge) badge.style.display = 'none';

                // Tandai notifikasi sudah dibaca
                if (jumlahBaru > 0) {
                    fetch(window.notifUrl ? window.notifUrl.replace(/\/$/, '') + '/read' : '/admin/notifikasi/read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: [] })
                    }).catch(() => {});
                }
            });
        })
        .catch(() => {
            // Jika endpoint belum ada / error — tampilkan kosong
            Swal.fire({
                title: '<div style="display:flex;align-items:center;width:100%;padding-bottom:10px;border-bottom:1px solid #e2e8f0;font-size:16px;font-weight:800;color:#0f172a">Pemberitahuan Sistem</div>',
                customClass: { popup: 'custom-swal-popup', confirmButton: 'custom-swal-confirm' },
                buttonsStyling: false,
                confirmButtonText: 'Tutup',
                html: `<div style="text-align:center;padding:32px 16px;color:#94a3b8">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:0.4"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <div style="font-size:13.5px;font-weight:600;color:#475569">Belum ada pemberitahuan</div>
                    <div style="font-size:12px;margin-top:4px">Notifikasi sistem akan muncul di sini.</div>
                </div>`,
            });
        });
}

window.tampilkanModalSiswaTerlambat = function(kelasId, namaKelas) {
    let url = '/guru/siswa-terlambat';
    const params = [];
    if (kelasId) {
        params.push('kelas_id=' + encodeURIComponent(kelasId));
    }
    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    fetch(url)
        .then(r => r.json())
        .then(data => {
            const list = data.data || [];
            const headerTitle = namaKelas ? `Siswa Terlambat - ${namaKelas}` : 'Daftar Siswa Terlambat Hari Ini';
            if (list.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Siswa Terlambat',
                    text: namaKelas 
                        ? `Tidak ada siswa yang tercatat terlambat di kelas ${namaKelas} untuk hari ini.` 
                        : 'Belum ada data siswa yang tercatat terlambat di kelas Anda untuk hari ini.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            let html = '<div class="siswa-terlambat-modal-list">';
            list.forEach(s => {
                html += `
                    <div class="siswa-terlambat-modal-item">
                        <div class="siswa-terlambat-modal-item-top">
                            <div class="siswa-terlambat-modal-name">${s.nama_siswa}</div>
                            <span class="badge" style="background:#ffedd5;color:#c2410c;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;white-space:nowrap">Mulai Jam ke-${s.jam_ke}</span>
                        </div>
                        <div class="siswa-terlambat-modal-grid">
                            <div><span style="color:#64748b">Kelas:</span> <strong style="color:#1e293b">${s.nama_kelas}</strong></div>
                            <div><span style="color:#64748b">Waktu Datang:</span> <strong style="color:#1e293b">${s.jam_masuk} WIB</strong></div>
                            <div style="grid-column: 1 / -1;"><span style="color:#64748b">Guru Piket:</span> <strong style="color:#1e293b">${s.guru_piket}</strong></div>
                        </div>
                        <div class="siswa-terlambat-modal-alasan">
                            <strong>Alasan:</strong> ${s.alasan || 'Tanpa keterangan'}
                        </div>
                        ${s.foto_surat_url ? `
                            <div style="margin-top:6px">
                                <a href="${s.foto_surat_url}" target="_blank" rel="noopener" class="siswa-terlambat-modal-link">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:-2px;margin-right:3px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    Lihat Foto Surat / Bukti
                                </a>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            html += '</div>';

            Swal.fire({
                title: `<div class="siswa-terlambat-modal-title">
                    <span>${headerTitle}</span>
                    <span class="siswa-terlambat-modal-count">${list.length} Siswa</span>
                </div>`,
                html: html,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb',
                customClass: { 
                    popup: 'custom-swal-popup siswa-terlambat-popup'
                }
            });
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memuat data siswa terlambat.',
                confirmButtonColor: '#dc2626'
        });
    });
};

/* ── Checklist Feature (Admin Data Tables) ── */
(function(){
    const CHECKLIST_KEY = 'admin_checklist_';

    window.toggleChecklistMode = function(page) {
        const toggleBtn = document.getElementById('btn-toggle-checklist-' + page);
        const bar = document.getElementById('checklist-bar-' + page);
        const selectAll = document.querySelector('.checklist-select-all');
        const isActive = toggleBtn && toggleBtn.dataset.active === '1';
        if (!isActive) {
            if (toggleBtn) {
                toggleBtn.dataset.active = '1';
                toggleBtn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Batal Pilih';
                toggleBtn.style.background = '#dc2626';
                toggleBtn.style.borderColor = '#dc2626';
            }
            if (bar) bar.style.display = 'flex';
            if (selectAll) selectAll.disabled = false;
            document.querySelectorAll('.checklist-item[data-page="' + page + '"]').forEach(cb => cb.disabled = false);
            loadChecklist(page);
        } else {
            if (toggleBtn) {
                toggleBtn.dataset.active = '0';
                toggleBtn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>Edit Pilih';
                toggleBtn.style.background = '#6366f1';
                toggleBtn.style.borderColor = '#6366f1';
            }
            if (bar) bar.style.display = 'none';
            document.querySelectorAll('.checklist-item[data-page="' + page + '"]').forEach(cb => cb.disabled = true);
            if (selectAll) selectAll.disabled = true;
        }
    };

    const escapeChecklistHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[character]);

    const getSelectedGuruData = selectedIds => selectedIds.map(id => {
        const checkbox = document.querySelector('.checklist-item[data-page="guru"][data-id="' + id + '"]');
        const row = checkbox ? checkbox.closest('tr') : null;
        const nameCell = row ? row.querySelector('td:nth-child(4)') : null;

        return {
            id: Number(id),
            nama: nameCell ? nameCell.textContent.trim() : 'Guru ' + id,
            jadwalCount: Math.max(0, Number(row ? row.dataset.jadwalCount || 0 : 0)),
        };
    });

    window.hapusGuruTerpilih = async function(ids) {
        const selected = getSelectedGuruData(ids);
        const withSchedules = selected.filter(guru => guru.jadwalCount > 0);
        const totalSchedules = withSchedules.reduce((total, guru) => total + guru.jadwalCount, 0);
        const affectedList = withSchedules.map(guru => (
            '<div style="padding:7px 0;border-bottom:1px solid #e2e8f0">' +
            '<span style="font-weight:600;color:#334155">' + escapeChecklistHtml(guru.nama) + '</span>' +
            '<span style="float:right;color:#b45309;font-weight:700">' + guru.jadwalCount + ' jadwal</span>' +
            '</div>'
        )).join('');
        const warningHtml = withSchedules.length > 0
            ? '<div style="margin-top:12px;padding:12px;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;color:#92400e;line-height:1.5">' +
                '<strong>Peringatan:</strong> ' + withSchedules.length + ' guru masih memiliki total ' + totalSchedules + ' jadwal mengajar. Jadwal tidak dihapus, tetapi akan diubah menjadi <strong>Kosong (Belum Ada Guru Pengampu)</strong>.' +
                (affectedList ? '<div style="margin-top:8px;max-height:180px;overflow:auto">' + affectedList + '</div>' : '') +
              '</div>'
            : '<p style="margin-top:10px;color:#64748b;font-size:12.5px">Tidak ada jadwal mengajar yang perlu dilepas.</p>';
        const result = await Swal.fire({
            title: 'Hapus ' + selected.length + ' data guru?',
            icon: withSchedules.length > 0 ? 'warning' : 'question',
            html: '<div style="text-align:left;font-size:13.5px;color:#334155;line-height:1.55">' +
                '<p> Data guru yang dipilih akan dihapus. ' +
                (withSchedules.length > 0
                    ? 'Relasi guru pada jadwal mengajar akan diputus terlebih dahulu.'
                    : 'Semua guru terpilih tidak memiliki jadwal mengajar.') +
                '</p>' + warningHtml + '</div>',
            showCancelButton: true,
            confirmButtonText: withSchedules.length > 0 ? 'Ya, putuskan jadwal dan hapus' : 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                confirmButton: 'custom-swal-confirm',
                cancelButton: 'custom-swal-cancel',
            },
            buttonsStyling: false,
        });

        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Menghapus data...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
        });

        try {
            const response = await fetch('/guru/hapus-terpilih', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids: selected.map(guru => guru.id) }),
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'Gagal menghapus data guru terpilih.');
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1800,
                showConfirmButton: false,
            }).then(() => location.reload());
            cancelChecklist('guru');
        } catch (error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message || 'Gagal menghapus data guru terpilih.',
                customClass: {
                    popup: 'custom-swal-popup',
                    title: 'custom-swal-title',
                    confirmButton: 'custom-swal-confirm',
                },
                buttonsStyling: false,
            });
        }
    };

    window.hapusTerpilih = function(page) {
        const ids = Array.from(document.querySelectorAll('.checklist-item[data-page="' + page + '"]:checked')).map(cb => cb.dataset.id);
        if (ids.length === 0) return;
        if (page === 'guru') {
            window.hapusGuruTerpilih(ids);
            return;
        }

        let label = page === 'siswa' ? 'siswa' : 'kelas';
        confirmDeleteData({
            title: 'Hapus ' + ids.length + ' Data ' + label + '?',
            itemName: ids.length + ' ' + label + ' terpilih',
            onConfirm: async function() {
                try {
                    let successCount = 0;
                    let lastError = '';
                    Swal.fire({
                        title: 'Menghapus Data...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    for (let i = 0; i < ids.length; i++) {
                        const res = await fetch('/' + page + '/' + ids[i], {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            successCount++;
                        } else {
                            const errData = await res.text();
                            lastError = errData.substring(0, 50);
                        }
                    }
                    
                    if (successCount === ids.length) {
                        location.reload();
                    } else {
                        throw new Error('Gagal menghapus sebagian atau semua data. ' + (ids.length - successCount) + ' gagal. Hint: ' + lastError);
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, buttonsStyling: false });
                }
            }
        });
    };

    window.cancelChecklist = function(page) {
        document.querySelectorAll('.checklist-item[data-page="' + page + '"]').forEach(cb => cb.checked = false);
        const selectAll = document.querySelector('.checklist-select-all');
        if (selectAll) selectAll.checked = false;
        localStorage.removeItem(CHECKLIST_KEY + page);
        updateChecklistCount(page);
    };

    window.loadChecklist = function(page) {
        const saved = JSON.parse(localStorage.getItem(CHECKLIST_KEY + page) || '[]');
        document.querySelectorAll('.checklist-item[data-page="' + page + '"]').forEach(cb => {
            cb.checked = saved.includes(cb.dataset.id);
        });
        var items = document.querySelectorAll('.checklist-item[data-page="' + page + '"]');
        var allChecked = items.length > 0 && Array.from(items).every(cb => saved.includes(cb.dataset.id));
        var selectAll = document.querySelector('.checklist-select-all');
        if (selectAll) selectAll.checked = allChecked;
        updateChecklistCount(page);
    }

    window.updateChecklistCount = function(page) {
        var count = document.querySelectorAll('.checklist-item[data-page="' + page + '"]:checked').length;
        var countEl = document.getElementById('checklist-count-' + page);
        if (countEl) countEl.textContent = count;
        var bar = document.getElementById('checklist-bar-' + page);
        if (bar) bar.style.display = count > 0 ? 'flex' : 'none';
    };

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('checklist-item')) {
            var page = e.target.dataset.page;
            var checked = [];
            document.querySelectorAll('.checklist-item[data-page="' + page + '"]:checked').forEach(function(cb) { checked.push(cb.dataset.id); });
            localStorage.setItem(CHECKLIST_KEY + page, JSON.stringify(checked));
            updateChecklistCount(page);
            var items = document.querySelectorAll('.checklist-item[data-page="' + page + '"]');
            var allChecked = items.length > 0 && Array.from(items).every(cb => cb.checked);
            var selectAll = document.querySelector('.checklist-select-all');
            if (selectAll && !selectAll.disabled) selectAll.checked = allChecked;
        }
        if (e.target && e.target.classList.contains('checklist-select-all')) {
            var table = e.target.closest('table');
            if (table) {
                var itemInTable = table.querySelector('.checklist-item');
                if (itemInTable) {
                    var page = itemInTable.dataset.page;
                    var checkboxes = document.querySelectorAll('.checklist-item[data-page="' + page + '"]');
                    checkboxes.forEach(function(cb) { cb.checked = e.target.checked; });
                    var checked = [];
                    checkboxes.forEach(function(cb) { if (cb.checked) checked.push(cb.dataset.id); });
                    localStorage.setItem(CHECKLIST_KEY + page, JSON.stringify(checked));
                    updateChecklistCount(page);
                }
            }
        }
    });

    ['siswa', 'kelas', 'guru'].forEach(function(page) {
        var saved = JSON.parse(localStorage.getItem(CHECKLIST_KEY + page) || '[]');
        if (saved.length > 0) {
            document.querySelectorAll('.checklist-item[data-page="' + page + '"]').forEach(function(cb) {
                if (saved.includes(cb.dataset.id)) cb.checked = true;
            });
            var items = document.querySelectorAll('.checklist-item[data-page="' + page + '"]');
            var allChecked = items.length > 0 && Array.from(items).every(cb => saved.includes(cb.dataset.id));
            var selectAll = document.querySelector('.checklist-select-all');
            if (selectAll) selectAll.checked = allChecked;
        }
    });
})();


function perbaruiBadgeNotifikasi() {
    fetch(window.notifUrl || '/admin/notifikasi')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge-count');
            const jumlahBaru = data.total_baru || (data.notifikasi ? data.notifikasi.filter(n => !n.is_read).length : 0);
            if (badge) {
                if (jumlahBaru > 0) {
                    badge.textContent = jumlahBaru > 99 ? '99+' : jumlahBaru;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', function() {
    perbaruiBadgeNotifikasi();
    setInterval(perbaruiBadgeNotifikasi, 30000);
});

window.showSelfiePopup = function(url, title) {
    Swal.fire({
        title: 'Foto Selfie Mengajar',
        html: `
            ${title ? `<div style="font-size:13.5px;font-weight:600;color:#64748b;margin-top:-6px;margin-bottom:14px">${title}</div>` : ''}
            <div style="border-radius:12px;overflow:hidden;background:#f1f5f9;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;min-height:220px;position:relative">
                <img src="${url}" 
                     alt="Foto Selfie Mengajar" 
                     style="width:100%;max-height:420px;object-fit:cover;display:block;border-radius:11px" 
                     onerror="this.parentElement.innerHTML='<div style=\\'padding:36px 20px;color:#ef4444;font-size:13px;font-weight:600;display:flex;flex-direction:column;align-items:center;gap:8px\\'><svg width=\\'28\\' height=\\'28\\' viewBox=\\'0 0 24 24\\' fill=\\'none\\' stroke=\\'currentColor\\' stroke-width=\\'2\\'><circle cx=\\'12\\' cy=\\'12\\' r=\\'10\\'/><line x1=\\'12\\' y1=\\'8\\' x2=\\'12\\' y2=\\'12\\'/><line x1=\\'12\\' y1=\\'16\\' x2=\\'12.01\\' y2=\\'16\\'/></svg>Foto selfie tidak ditemukan atau gagal dimuat.</div>'">
            </div>
            <div style="margin-top:14px;display:flex;justify-content:center;gap:8px">
                <a href="${url}" target="_blank" rel="noopener" class="custom-swal-confirm" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;padding:8px 16px;border-radius:8px;background:#2563eb;color:#ffffff">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Buka Ukuran Penuh
                </a>
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'custom-swal-popup'
        },
        width: 480
    });
};


