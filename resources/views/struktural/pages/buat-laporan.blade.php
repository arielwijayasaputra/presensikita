@php
    $sessionRole = session('auth_role');
    $roleMap = [
        'admin' => 'Admin',
        'guru' => 'Guru',
        'guru_piket' => 'Guru Piket',
        'walikelas' => 'Wali Kelas',
        'waka' => 'Waka Kesiswaan',
        'kepsek' => 'Kepala Sekolah',
        'satpam' => 'Satpam'
    ];
    $userRole = $roleMap[$sessionRole] ?? 'Struktural';
    $userName = session('auth_nama_guru') ?? ($guru->nama_guru ?? 'User');
@endphp

<div class="page-content page-anim" id="page-buat-laporan" style="display:none">
    <div class="page-header" style="margin-bottom:20px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px;color:#1e293b">Buat Laporan / Pengaduan</div>
            <div class="page-subtitle">Laporkan kendala sistem, absensi, atau keluhan lainnya langsung kepada Admin.</div>
        </div>
    </div>

    <div class="card" style="padding:28px; background:#fff; border-radius:18px; border:1px solid #e2e8f0; max-width:680px">
        <form id="struktural-laporan-form" onsubmit="submitLaporanStruktural(event)">
            @csrf

            <!-- Pelapor & Peran (Readonly) -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px">
                <div class="form-field">
                    <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px">Nama Pelapor</label>
                    <input type="text" name="nama_pelapor" value="{{ $userName }}" readonly class="form-input" style="background:#f8fafc; color:#64748b; font-weight:600; cursor:not-allowed; width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px">
                </div>
                <div class="form-field">
                    <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px">Peran / Role</label>
                    <input type="text" name="role_pelapor" value="{{ $userRole }}" readonly class="form-input" style="background:#f8fafc; color:#64748b; font-weight:600; cursor:not-allowed; width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px">
                </div>
            </div>

            <!-- Judul Laporan -->
            <div class="form-field" style="margin-bottom:18px">
                <label for="judul-laporan-struktural" style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px">Judul / Subjek Laporan <span style="color:#ef4444">*</span></label>
                <input type="text" id="judul-laporan-struktural" name="judul" required class="form-input" placeholder="Masukkan judul laporan Anda..." style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; outline:none">
            </div>

            <!-- Rincian Laporan -->
            <div class="form-field" style="margin-bottom:20px">
                <label for="isi-laporan-struktural" style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px">Rincian Laporan <span style="color:#ef4444">*</span></label>
                <textarea id="isi-laporan-struktural" name="isi_laporan" required class="form-textarea" placeholder="Jelaskan secara detail rincian kendala atau laporan Anda..." style="width:100%; min-height:120px; padding:10px 14px; border-radius:10px; border:1px solid #cbd5e1; outline:none; font-family:inherit"></textarea>
            </div>

            <div>
                <button type="submit" class="btn-primary" id="btn-submit-laporan-struktural" style="padding:10px 24px; font-size:13.5px; font-weight:700; border-radius:10px; background:#2563eb; color:#fff; border:none; cursor:pointer">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function submitLaporanStruktural(e) {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('btn-submit-laporan-struktural');
    const originalText = btn.textContent;

    btn.textContent = 'Mengirim...';
    btn.disabled = true;

    const formData = new FormData(form);

    fetch('{{ route("laporan.public.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                confirmButtonColor: '#2563eb'
            }).then(() => {
                form.reset();
                if (typeof showPage === 'function') {
                    showPage('profil');
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: res.message
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan sistem. Silakan coba lagi.'
        });
    })
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}
</script>
