/* ── Global SweetAlert2 Modern Mixin ───────────────────── */
const SwalModern = Swal.mixin({
    buttonsStyling: true,
    customClass: {
        popup:         '',
        confirmButton: '',
        cancelButton:  '',
    }
});

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
    closeSidebarMobile();
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

    fetch(`/absensi/siswa/${idKelas}`)
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                currentSiswaList = res.data;
                renderTable(currentSiswaList);
                muatAbsensiTersimpan();
            }
        })
        .catch(err => console.error('Error fetching siswa:', err));
}

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

            if (data.jurnal) {
                const map = {};
                (data.siswa || []).forEach(s => { map[s.id_siswa] = s; });
                currentSiswaList.forEach(s => {
                    const rec = map[s.id_siswa];
                    if (rec && rec.status && rec.status !== 'H') {
                        const radio = qs(`input[name="st-${s.id_siswa}"][value="${rec.status}"]`, root);
                        if (radio) radio.checked = true;
                    }
                    if (rec && rec.keterangan) {
                        const ket = qs(`#ket-${s.id_siswa}`, root);
                        if (ket) ket.value = rec.keterangan;
                    }
                });
            }

            updateRekap();
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
    const radioAttr = isAdmin ? 'onclick="return false;" tabindex="-1"' : 'onchange="updateRekap()"';
    const ketAttr = isAdmin 
        ? 'readonly placeholder="Diisi oleh Guru..." style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:12px;width:100%;outline:none;background:#f8fafc;color:#475569;cursor:default;"' 
        : 'placeholder="Keterangan (opsional)..." style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:12px;width:100%;outline:none;"';

    data.forEach((s, idx)=>{
        const id = s.id_siswa;
        const nisn = s.nisn || '-';
        const nama = s.nama_siswa;
        const r=document.createElement('tr');
        r.innerHTML=`
            <td style="color:#94a3b8;font-weight:600">${idx+1}</td>
            <td style="font-family:monospace;font-size:13px;color:#64748b">${nisn}</td>
            <td style="font-weight:600">${nama}</td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="H" checked ${radioAttr} style="accent-color:#22c55e;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="S" ${radioAttr} style="accent-color:#f59e0b;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="I" ${radioAttr} style="accent-color:#3b82f6;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td class="td-status"><div class="radio-wrapper"><input type="radio" name="st-${id}" value="A" ${radioAttr} style="accent-color:#ef4444;${isAdmin ? 'pointer-events:none;cursor:default;' : ''}"></div></td>
            <td><input type="text" id="ket-${id}" ${ketAttr}></td>
        `;
        tbody.appendChild(r);
    });
    updateRekap();
}

function updateRekap(){
    const root = absensiRoot();
    let h=0,s=0,i=0,a=0;
    currentSiswaList.forEach(d=>{
        const c=root ? qs(`input[name="st-${d.id_siswa}"]:checked`, root) : document.querySelector(`input[name="st-${d.id_siswa}"]:checked`);
        if(c){if(c.value==='H')h++;else if(c.value==='S')s++;else if(c.value==='I')i++;else if(c.value==='A')a++;}
    });
    const set=(id,val)=>{const el= root ? qs('#'+id, root) : document.getElementById(id); if(el) el.textContent=val;};
    set('rekap-hadir',h);
    set('rekap-sakit',s);
    set('rekap-izin',i);
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
}

function filterSiswa(q){
    q=q.toLowerCase();
    const filtered = currentSiswaList.filter(s=>s.nama_siswa.toLowerCase().includes(q)||(s.nisn && s.nisn.includes(q)));
    renderTable(filtered);
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
            absensi: absensiData
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Absensi berhasil disimpan! (Hadir: ' + data.rekap.hadir + ', Sakit: ' + data.rekap.sakit + ', Izin: ' + data.rekap.izin + ', Alpa: ' + data.rekap.alpa + ')',
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
                .then(() => location.reload());
            });
        }
    });
}

function tambahKelasModal(){
    const guruOptions = (window.daftarGuru || []).map(g =>
        `<option value="${g.id_guru}">${g.nama_guru}</option>`
    ).join('');
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
                        <input id="swal-kjurusan" class="swal-form-input" placeholder="Contoh: RPL, AK, DKV">
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
                .then(() => location.reload());
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
                .then(() => location.reload());
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
    const namaSekolah    = document.getElementById('set-nama-sekolah')?.value?.trim();
    const npsn           = document.getElementById('set-npsn')?.value?.trim();
    const kepsek         = document.getElementById('set-kepsek')?.value?.trim();
    const alamat         = document.getElementById('set-alamat')?.value?.trim();
    const emailSekolah   = document.getElementById('set-email')?.value?.trim();
    const teleponSekolah = document.getElementById('set-telepon')?.value?.trim();
    const tahunAjaran    = document.getElementById('set-tahun-ajaran')?.value?.trim();
    const semester       = document.getElementById('set-semester')?.value;
    const sistemAbsensi  = document.getElementById('set-sistem-absensi')?.value;
    const batasWaktu     = document.getElementById('set-batas-waktu')?.value;
    const izinEdit       = document.getElementById('set-izin-edit')?.checked ? '1' : '0';

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
            nama_sekolah:       namaSekolah,
            npsn:               npsn,
            kepsek:             kepsek,
            alamat:             alamat,
            email_sekolah:      emailSekolah,
            telepon_sekolah:    teleponSekolah,
            tahun_ajaran:       tahunAjaran,
            semester:           semester,
            sistem_absensi:     sistemAbsensi,
            batas_waktu_jurnal: batasWaktu,
            izin_edit_jurnal:   izinEdit,
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
                            <option value="Guru">Guru</option>
                            <option value="Wali Kelas">Wali Kelas</option>
                            <option value="Guru Piket">Guru Piket</option>
                            <option value="Waka">Waka</option>
                            <option value="Kepsek">Kepsek</option>
                            <option value="Satpam">Satpam</option>
                            <option value="Admin">Admin</option>
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
                        <label for="swal-gusername">Username Login</label>
                        <input id="swal-gusername" class="swal-form-input" placeholder="contoh: budi">
                    </div>
                    <div class="swal-form-group">
                        <label for="swal-gpass">Password</label>
                        <input type="password" id="swal-gpass" class="swal-form-input" placeholder="Minimal 4 karakter">
                    </div>
                </div>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Data Guru',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const nama = document.getElementById('swal-gname').value.trim();
            const username = document.getElementById('swal-gusername').value.trim();
            const pass = document.getElementById('swal-gpass').value.trim();

            if (!nama) {
                Swal.showValidationMessage('Nama guru tidak boleh kosong');
                return false;
            }
            if (!username) {
                Swal.showValidationMessage('Username tidak boleh kosong');
                return false;
            }
            if (!pass || pass.length < 4) {
                Swal.showValidationMessage('Password minimal 4 karakter');
                return false;
            }

            return {
                nama_guru: nama,
                nip: document.getElementById('swal-gnip').value.trim(),
                peran: document.getElementById('swal-gperan').value,
                no_hp: document.getElementById('swal-ghp').value.trim(),
                is_admin: parseInt(document.getElementById('swal-gadmin').value),
                username: username,
                password: pass
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
                    .then(() => location.reload());
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
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11, family: 'Inter' } } }
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
    const alpa = Number(rekap.alpa || 0);
    const donutValues = [hadir, sakit, izin, alpa];
    const donutSum = donutValues.reduce((a, b) => a + b, 0);

    laporanCharts.donut = new Chart(donutEl, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
            datasets: [{
                data: donutSum > 0 ? donutValues : [0, 0, 0, 0],
                backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
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

    const hari = rekap.rekap_hari || { labels: ['Sen','Sel','Rab','Kam','Jum','Sab'], hadir: [0,0,0,0,0,0], sakit: [0,0,0,0,0,0], izin: [0,0,0,0,0,0], alpa: [0,0,0,0,0,0] };
    laporanCharts.bar = new Chart(barEl, {
        type: 'bar',
        data: {
            labels: hari.labels,
            datasets: [
                { label: 'Hadir', data: hari.hadir, backgroundColor: '#22c55e', stack: 'absensi', borderRadius: 3 },
                { label: 'Sakit', data: hari.sakit, backgroundColor: '#f59e0b', stack: 'absensi', borderRadius: 3 },
                { label: 'Izin', data: hari.izin, backgroundColor: '#3b82f6', stack: 'absensi', borderRadius: 3 },
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

function updateDate(){
    const now=new Date();
    const days=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    const el=document.getElementById('header-date');
    if(el) el.textContent=`${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
}

document.addEventListener('DOMContentLoaded',function(){
    updateDate();
    initChart();
    initLaporanPagination();
    renderTable(currentSiswaList);
    window.addEventListener('resize', function(){
        if (window.innerWidth > 992) closeSidebarMobile();
    });
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
                    <small style="color:#94a3b8;font-size:12px;">${warningText || 'Data akan dihapus secara soft-delete.'}</small>
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
function hapusGuru(id, nama) {
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
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
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
                        .then(() => location.reload());
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
                        .then(() => location.reload());
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
                        .then(() => location.reload());
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
                    htmlIsi += `
                        <div style="display:flex;gap:12px;padding:12px;background:${bg};border-radius:10px;border:1px solid #e2e8f0;align-items:flex-start">
                            <div style="width:34px;height:34px;background:${ic.bg};border-radius:8px;display:flex;align-items:center;justify-content:center;color:${ic.color};flex-shrink:0;margin-top:2px">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${ic.svg}</svg>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1e293b">${n.judul}</div>
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
