<div class="page-content page-anim" id="page-dispen-siswa" style="display:none">
    <div class="page-header" style="margin-bottom:20px"><div><div class="page-title" style="font-size:22px;font-weight:800">Dispensasi Siswa</div><div class="page-subtitle">Buat dan pantau izin dispensasi siswa dengan persetujuan Waka dan Guru Piket.</div></div></div>
    <div class="card" style="padding:22px 24px;max-width:900px">
        <div class="card-header" style="margin-bottom:16px"><div class="card-title">Buat Permintaan Dispensasi</div></div>
        <form id="dispen-form" onsubmit="buatDispen(event)">@csrf
            <div style="display:grid;grid-template-columns:1fr 180px;gap:14px;margin-bottom:14px">
                <div>
                    <label for="dispen-siswa">Siswa</label>
                    <input type="hidden" id="dispen-siswa" name="id_siswa" required>
                    <div style="position:relative;margin-top:4px">
                        <input type="text" id="dispen-siswa-search" class="filter-input" placeholder="Ketik nama siswa..." autocomplete="off" required style="width:100%"
                            onfocus="toggleDispenSiswaDropdown(true)"
                            oninput="filterDispenSiswaDropdown(this.value)">
                        <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        <div id="dispen-siswa-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;margin-top:4px;max-height:220px;overflow-y:auto;z-index:50;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
                            @foreach($siswaAktif as $siswa)
                                <div class="dispen-siswa-dropdown-item" data-id="{{ $siswa->id_siswa }}" data-search="{{ strtolower($siswa->nama_siswa . ' ' . ($siswa->kelas->nama_kelas ?? '')) }}"
                                    onclick="pilihDispenSiswaDropdown('{{ $siswa->id_siswa }}', '{{ addslashes($siswa->nama_siswa) }} - {{ addslashes($siswa->kelas->nama_kelas ?? '') }}')"
                                    style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9;transition:background 0.15s"
                                    onmouseenter="this.style.background='#f1f5f9'" onmouseleave="this.style.background='#fff'">
                                    {{ $siswa->nama_siswa }} - {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            @endforeach
                            <div id="dispen-siswa-empty" style="display:none;padding:14px;text-align:center;color:#94a3b8;font-size:13px">Siswa tidak ditemukan</div>
                        </div>
                    </div>
                </div>
                <div><label for="dispen-tanggal">Tanggal</label><input id="dispen-tanggal" type="date" name="tanggal_dispen" value="{{ now()->toDateString() }}" class="filter-input" required style="width:100%;margin-top:4px"></div>
            </div>
            <input type="hidden" name="jenis_absen" value="D">
            <div style="margin-bottom:14px"><label for="dispen-alasan">Alasan dispensasi</label><textarea id="dispen-alasan" name="alasan" class="filter-input" rows="3" maxlength="2000" required placeholder="Contoh: mengikuti lomba, kegiatan sekolah, atau keperluan resmi lainnya..." style="width:100%;margin-top:4px;resize:vertical"></textarea></div>
            <div style="margin-bottom:14px"><label for="dispen-foto">Foto surat keterangan <small style="color:#94a3b8">(opsional)</small></label><input id="dispen-foto" type="file" name="foto_surat" accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;margin-top:6px;font-size:13px"><small style="display:block;color:#64748b;margin-top:5px">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</small></div>
            <button type="submit" class="btn-primary" style="border-radius:8px;padding:10px 16px;font-size:13px">Simpan &amp; Absen ke Jurnal</button>
        </form>
        <div id="dispen-links" style="display:none;margin-top:16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:14px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <strong style="font-size:12px;color:#1d4ed8">Link persetujuan Waka Kesiswaan</strong>
                <div id="dispen-wa-status-badge"></div>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                <input id="dispen-waka-link" class="filter-input" readonly style="flex:1;font-size:12px">
                <button type="button" class="btn-secondary" onclick="salinDispen('dispen-waka-link')">Salin</button>
                <a id="dispen-waka-wa-btn" href="#" target="_blank" class="btn-secondary" style="font-size:12px;display:inline-flex;align-items:center;gap:4px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;text-decoration:none">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Kirim WA
                </a>
            </div>
        </div>
    </div>
    <div class="card" style="padding:22px 24px;margin-top:20px">
        <div class="card-header" style="margin-bottom:16px">
            <div class="card-title">Status Dispensasi</div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table" style="min-width:950px">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Surat</th>
                        <th>Status Waka</th>
                        <th>Status Keluar-Masuk</th>
                        <th>Link Waka</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispenTerbaru as $item)
                        <tr>
                            <td><strong>{{ $item->siswa->nama_siswa ?? '-' }}</strong></td>
                            <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $item->tanggal_dispen->format('d-m-Y') }}</td>
                            <td>{{ $item->alasan }}</td>
                            <td>
                                @if($item->foto_surat)
                                    <a href="{{ Storage::disk('public')->url($item->foto_surat) }}" target="_blank" rel="noopener">Lihat foto</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ ucfirst($item->status_waka) }}</td>
                            <td>
                                @if($item->waktu_masuk)
                                    <span class="badge badge-success">Sudah Kembali</span>
                                @elseif($item->waktu_keluar)
                                    <span class="badge badge-warning">Di Luar Sekolah</span>
                                @else
                                    <span class="badge badge-info">Di Sekolah</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ URL::temporarySignedRoute('dispen-siswa.public', now()->addDays(2), ['dispen' => $item->id_dispen_siswa, 'role' => 'waka']) }}" target="_blank">
                                    Buka Waka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:#64748b;padding:22px">
                                Belum ada permintaan dispensasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function buatDispen(event){
    event.preventDefault();
    fetch(@json(route('dispen-siswa.store')),{
        method:'POST',
        headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},
        body:new FormData(document.getElementById('dispen-form'))
    })
    .then(async r=>{
        const d=await r.json();
        if(!r.ok) throw new Error(d.message||'Gagal membuat dispensasi.');
        return d;
    })
    .then(d=>{
        document.getElementById('dispen-waka-link').value=d.waka_link;

        const waUrl = d.wa_notification?.wa_me_link || ('https://wa.me/?text=' + encodeURIComponent(d.waka_link));
        const btnWa = document.getElementById('dispen-waka-wa-btn');
        if (btnWa) btnWa.href = waUrl;

        const statusBadge = document.getElementById('dispen-wa-status-badge');
        if (statusBadge) {
            if (d.wa_notification?.sent) {
                statusBadge.innerHTML = '<span class="badge badge-success" style="font-size:11px;padding:3px 8px">● WA Terkirim ke Waka Kesiswaan</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge badge-secondary" style="font-size:11px;padding:3px 8px">WA Belum Terkirim Otomatis</span>';
            }
        }

        document.getElementById('dispen-links').style.display='block';
        Swal.fire({icon:'success',title:'Berhasil Dibuat!',text:d.message,confirmButtonColor:'#ea580c'});
    })
    .catch(e=>Swal.fire({icon:'error',title:'Gagal',text:e.message,confirmButtonColor:'#dc2626'}));
}
function salinDispen(id){navigator.clipboard.writeText(document.getElementById(id).value).then(()=>Swal.fire({icon:'success',title:'Link disalin',timer:1200,showConfirmButton:false}));}

function toggleDispenSiswaDropdown(show){var dd=document.getElementById('dispen-siswa-dropdown');if(dd)dd.style.display=show?'block':'none';}
function filterDispenSiswaDropdown(keyword){var items=document.querySelectorAll('.dispen-siswa-dropdown-item');var empty=document.getElementById('dispen-siswa-empty');var q=keyword.toLowerCase().trim();var found=0;items.forEach(function(item){var match=item.dataset.search.includes(q);item.style.display=match?'':'none';if(match)found++;});if(empty)empty.style.display=found===0?'block':'none';toggleDispenSiswaDropdown(true);}
function pilihDispenSiswaDropdown(id,nama){document.getElementById('dispen-siswa').value=id;document.getElementById('dispen-siswa-search').value=nama;toggleDispenSiswaDropdown(false);}
document.addEventListener('click',function(e){var search=document.getElementById('dispen-siswa-search');if(search&&search.parentElement&&!search.parentElement.contains(e.target)){toggleDispenSiswaDropdown(false);}});
</script>
