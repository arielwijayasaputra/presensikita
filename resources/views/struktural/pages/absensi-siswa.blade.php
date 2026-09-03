<div class="page-content page-anim" id="page-absensi-siswa" style="display:none">
    <div class="page-header" style="margin-bottom:20px"><div><div class="page-title" style="font-size:22px;font-weight:800">Absensi Siswa</div><div class="page-subtitle">Catat siswa sakit atau izin ke jurnal kelas dan simpan surat keterangannya.</div></div></div>
    <div class="card" style="padding:22px 24px;max-width:900px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Catat Sakit atau Izin</div></div>
        <form id="absensi-siswa-form" onsubmit="simpanAbsensiSiswa(event)">@csrf
            <div style="display:grid;grid-template-columns:1fr 180px 180px;gap:14px;margin-bottom:14px">
                <div>
                    <label for="absensi-siswa">Siswa</label>
                    <input type="hidden" id="absensi-siswa" name="id_siswa" required>
                    <div style="position:relative;margin-top:4px">
                        <input type="text" id="absensi-siswa-search" class="filter-input" placeholder="Ketik nama siswa..." autocomplete="off" required style="width:100%"
                            onfocus="toggleAbsensiSiswaDropdown(true)"
                            oninput="filterAbsensiSiswaDropdown(this.value)">
                        <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        <div id="absensi-siswa-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:50;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                            @foreach($siswaAktif as $siswa)
                                <div class="absensi-siswa-dropdown-item" data-id="{{ $siswa->id_siswa }}" data-search="{{ strtolower($siswa->nama_siswa . ' ' . ($siswa->kelas->nama_kelas ?? '')) }}"
                                    onclick="pilihAbsensiSiswaDropdown('{{ $siswa->id_siswa }}', '{{ addslashes($siswa->nama_siswa) }} - {{ addslashes($siswa->kelas->nama_kelas ?? '') }}')"
                                    style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9;transition:background 0.15s"
                                    onmouseenter="this.style.background='#f1f5f9'" onmouseleave="this.style.background='#fff'">
                                    {{ $siswa->nama_siswa }} - {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            @endforeach
                            <div id="absensi-siswa-empty" style="display:none;padding:14px;text-align:center;color:#94a3b8;font-size:13px">Siswa tidak ditemukan</div>
                        </div>
                    </div>
                </div>
                <div><label for="absensi-jenis">Jenis absensi</label><select id="absensi-jenis" name="jenis_absen" class="filter-select" required style="width:100%;margin-top:4px"><option value="S">Sakit</option><option value="I">Izin</option></select></div>
                <div><label for="absensi-tanggal">Tanggal</label><input id="absensi-tanggal" type="date" name="tanggal_dispen" value="{{ now()->toDateString() }}" class="filter-input" required style="width:100%;margin-top:4px"></div>
            </div>
            <div style="margin-bottom:14px"><label for="absensi-foto">Foto surat keterangan <small style="color:#94a3b8">(opsional)</small></label><input id="absensi-foto" type="file" name="foto_surat" accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;margin-top:6px;font-size:13px"><small style="display:block;color:#64748b;margin-top:5px">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</small></div>
            <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 16px;font-size:13px">Simpan &amp; Absen ke Jurnal</button>
        </form>
    </div>
    <div class="card" style="padding:22px 24px;margin-top:20px"><div class="card-header" style="margin-bottom:16px"><div class="card-title">Riwayat Absensi Siswa</div></div><div style="overflow-x:auto"><table class="data-table" style="min-width:900px"><thead><tr><th>Siswa</th><th>Kelas</th><th>Jenis</th><th>Tanggal</th><th>Surat</th><th>Jurnal</th></tr></thead><tbody>@forelse($absensiSiswaTerbaru as $item)<tr><td><strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong></td><td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td><td>{{ $item->jenis_absen === 'S' ? 'Sakit' : 'Izin' }}</td><td>{{ $item->tanggal_dispen->format('d-m-Y') }}</td><td>@if($item->foto_surat)<a href="{{ Storage::disk('public')->url($item->foto_surat) }}" target="_blank" rel="noopener">Lihat foto</a>@else-@endif</td><td><span class="badge badge-success">Tersimpan</span></td></tr>@empty<tr><td colspan="6" style="text-align:center;color:#64748b;padding:22px">Belum ada absensi siswa.</td></tr>@endforelse</tbody></table></div></div>
</div>
<script>
function simpanAbsensiSiswa(event){event.preventDefault();fetch(@json(route('absensi-siswa.store')),{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:new FormData(document.getElementById('absensi-siswa-form'))}).then(async r=>{const d=await r.json();if(!r.ok)throw new Error(d.message||'Gagal menyimpan absensi siswa.');return d}).then(d=>{Swal.fire({icon:'success',title:'Absensi tersimpan',text:d.message,confirmButtonColor:'#2563eb'}).then(()=>window.location.reload())}).catch(e=>Swal.fire({icon:'error',title:'Gagal',text:e.message,confirmButtonColor:'#dc2626'}));}

function toggleAbsensiSiswaDropdown(show){var dd=document.getElementById('absensi-siswa-dropdown');if(dd)dd.style.display=show?'block':'none';}
function filterAbsensiSiswaDropdown(keyword){var items=document.querySelectorAll('.absensi-siswa-dropdown-item');var empty=document.getElementById('absensi-siswa-empty');var q=keyword.toLowerCase().trim();var found=0;items.forEach(function(item){var match=item.dataset.search.includes(q);item.style.display=match?'':'none';if(match)found++;});if(empty)empty.style.display=found===0?'block':'none';toggleAbsensiSiswaDropdown(true);}
function pilihAbsensiSiswaDropdown(id,nama){document.getElementById('absensi-siswa').value=id;document.getElementById('absensi-siswa-search').value=nama;toggleAbsensiSiswaDropdown(false);}
document.addEventListener('click',function(e){var search=document.getElementById('absensi-siswa-search');if(search&&search.parentElement&&!search.parentElement.contains(e.target)){toggleAbsensiSiswaDropdown(false);}});
</script>
