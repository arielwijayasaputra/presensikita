<div class="page-content page-anim" id="page-absensi-siswa" style="display:none">
    <div class="page-header" style="margin-bottom:20px"><div><div class="page-title" style="font-size:22px;font-weight:800">Absensi Harian Siswa</div><div class="page-subtitle">Catat siswa sakit atau izin lewat surat keterangan ke jurnal kelas (termasuk saat hari event).</div></div></div>
    <div class="card" style="padding:22px 24px;max-width:900px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Catat Sakit atau Izin</div></div>
        <form id="absensi-siswa-form" onsubmit="simpanAbsensiSiswa(event)">@csrf
            <div style="display:grid;grid-template-columns:minmax(0, 1.2fr) minmax(110px, 130px) minmax(130px, 150px) minmax(130px, 150px);gap:14px;margin-bottom:14px;align-items:flex-start">
                <div style="min-width:0">
                    <label for="absensi-siswa">Siswa</label>
                    <input type="hidden" id="absensi-siswa" name="id_siswa" required>
                    <div style="position:relative;margin-top:4px">
                        <input type="text" id="absensi-siswa-search" class="filter-input" placeholder="Ketik nama atau kelas siswa..." autocomplete="off" required style="width:100%;box-sizing:border-box"
                            onfocus="filterAbsensiSiswaDropdown(this.value)"
                            onclick="filterAbsensiSiswaDropdown(this.value)"
                            oninput="filterAbsensiSiswaDropdown(this.value)"
                            onkeyup="filterAbsensiSiswaDropdown(this.value)">
                        <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        <div id="absensi-siswa-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:var(--card-bg, #ffffff);color:var(--text, #0f172a);border:1px solid var(--border, #cbd5e1);border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:999;box-shadow:0 10px 25px rgba(0,0,0,0.25)">
                            @foreach($siswaAktif as $siswa)
                                <div class="absensi-siswa-dropdown-item" data-id="{{ $siswa->id_siswa }}" data-search="{{ strtolower($siswa->nama_siswa . ' ' . ($siswa->kelas->nama_kelas ?? '') . ' ' . ($siswa->nisn ?? '')) }}"
                                    onclick="pilihAbsensiSiswaDropdown('{{ $siswa->id_siswa }}', '{{ addslashes($siswa->nama_siswa) }} - {{ addslashes($siswa->kelas->nama_kelas ?? '') }}')"
                                    style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border, #f1f5f9);transition:background 0.15s;color:inherit"
                                    onmouseenter="this.style.background='var(--hover-bg, rgba(255,255,255,0.06))'" onmouseleave="this.style.background='transparent'">
                                    <div style="font-weight:600;color:inherit">{{ $siswa->nama_siswa }}</div>
                                    <div style="font-size:11.5px;color:#94a3b8">{{ $siswa->kelas->nama_kelas ?? '-' }} • NISN: {{ $siswa->nisn ?? '-' }}</div>
                                </div>
                            @endforeach
                            <div id="absensi-siswa-empty" style="display:none;padding:14px;text-align:center;color:#94a3b8;font-size:13px">Siswa tidak ditemukan</div>
                        </div>
                    </div>
                </div>
                <div style="min-width:0"><label for="absensi-jenis">Jenis absensi</label><select id="absensi-jenis" name="jenis_absen" class="filter-select" required style="width:100%;margin-top:4px;box-sizing:border-box"><option value="S">Sakit</option><option value="I">Izin</option></select></div>
                <div style="min-width:0"><label for="absensi-tanggal-mulai">Tanggal Mulai</label><input id="absensi-tanggal-mulai" type="date" name="tanggal_dispen" value="{{ now()->toDateString() }}" class="filter-input" required style="width:100%;margin-top:4px;box-sizing:border-box" onchange="var s=document.getElementById('absensi-tanggal-selesai'); if(s){ s.min=this.value; if(s.value < this.value) s.value=this.value; }"></div>
                <div style="min-width:0"><label for="absensi-tanggal-selesai">Sampai Dengan</label><input id="absensi-tanggal-selesai" type="date" name="tanggal_selesai" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" class="filter-input" required style="width:100%;margin-top:4px;box-sizing:border-box"></div>
            </div>
            <div style="margin-bottom:14px"><label for="absensi-foto">Foto surat keterangan <small style="color:#94a3b8">(opsional)</small></label><input id="absensi-foto" type="file" name="foto_surat" accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;margin-top:6px;font-size:13px"><small style="display:block;color:#64748b;margin-top:5px">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</small></div>
            <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 16px;font-size:13px">Simpan &amp; Absen ke Jurnal</button>
        </form>
    </div>
    <div class="card" style="padding:22px 24px;margin-top:20px">
        <div class="card-header" style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div class="card-title">Riwayat Absensi Siswa</div>
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-absensi-siswa-tbl" oninput="filterTable('search-absensi-siswa-tbl','table-absensi-siswa-tbl')" onkeyup="filterTable('search-absensi-siswa-tbl','table-absensi-siswa-tbl')" placeholder="Cari nama atau kelas..." class="filter-input" style="padding:7px 12px 7px 32px;font-size:12.5px;border-radius:8px;border:1px solid #cbd5e1;width:210px">
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" id="table-absensi-siswa-tbl" style="min-width:900px">
                <thead><tr><th>Siswa</th><th>Kelas</th><th>Jenis</th><th>Tanggal</th><th>Surat</th><th>Jurnal</th></tr></thead>
                <tbody>
                    @forelse($absensiSiswaTerbaru as $item)
                        <tr>
                            <td><strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong></td>
                            <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $item->jenis_absen === 'S' ? 'Sakit' : 'Izin' }}</td>
                            <td>
                                @if($item->tanggal_selesai && $item->tanggal_selesai->gt($item->tanggal_dispen))
                                    {{ $item->tanggal_dispen->format('d-m-Y') }} <span style="color:#64748b;font-size:11.5px">s/d</span> {{ $item->tanggal_selesai->format('d-m-Y') }}
                                @else
                                    {{ $item->tanggal_dispen->format('d-m-Y') }}
                                @endif
                            </td>
                            <td>
                                @if($item->foto_surat)
                                    <a href="#" onclick="showSuratPopup('{{ Storage::disk('public')->url($item->foto_surat) }}'); return false;" style="font-size:12.5px;font-weight:600;color:#2563eb;display:inline-flex;align-items:center;gap:4px">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat foto
                                    </a>
                                @else
                                    <span style="color:#94a3b8">-</span>
                                @endif
                            </td>
                            <td><span class="badge badge-success">Tersimpan</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:#64748b;padding:22px">Belum ada absensi siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function simpanAbsensiSiswa(event){event.preventDefault();fetch(@json(route('absensi-siswa.store')),{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:new FormData(document.getElementById('absensi-siswa-form'))}).then(async r=>{const d=await r.json();if(!r.ok)throw new Error(d.message||'Gagal menyimpan absensi siswa.');return d}).then(d=>{Swal.fire({icon:'success',title:'Absensi tersimpan',text:d.message,confirmButtonColor:'#2563eb'}).then(()=>window.location.reload())}).catch(e=>Swal.fire({icon:'error',title:'Gagal',text:e.message,confirmButtonColor:'#dc2626'}));}

function toggleAbsensiSiswaDropdown(show){
    var dd = document.getElementById('absensi-siswa-dropdown');
    if (dd) dd.style.display = show ? 'block' : 'none';
}
function filterAbsensiSiswaDropdown(keyword){
    var items = document.querySelectorAll('.absensi-siswa-dropdown-item');
    var empty = document.getElementById('absensi-siswa-empty');
    var q = (keyword || '').toLowerCase().trim();
    var found = 0;
    items.forEach(function(item){
        var searchStr = (item.getAttribute('data-search') || item.dataset.search || '').toLowerCase();
        var match = !q || searchStr.includes(q);
        item.style.display = match ? 'block' : 'none';
        if(match) found++;
    });
    if(empty) empty.style.display = found === 0 ? 'block' : 'none';
    toggleAbsensiSiswaDropdown(true);
}
function pilihAbsensiSiswaDropdown(id,nama){
    var idInput = document.getElementById('absensi-siswa');
    var searchInput = document.getElementById('absensi-siswa-search');
    if (idInput) idInput.value = id;
    if (searchInput) searchInput.value = nama;
    toggleAbsensiSiswaDropdown(false);
}
document.addEventListener('click',function(e){
    var search = document.getElementById('absensi-siswa-search');
    if (search && search.parentElement && !search.parentElement.contains(e.target)){
        toggleAbsensiSiswaDropdown(false);
    }
});
if (typeof window.showSuratPopup !== 'function') {
    window.showSuratPopup = function(url) {
        Swal.fire({
            title: 'Foto Surat Keterangan',
            imageUrl: url,
            imageAlt: 'Foto Surat',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#475569',
            customClass: {
                image: 'swal-popup-image'
            }
        });
    };
}
</script>
