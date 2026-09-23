@php
    $monthsIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $currentPiketMonthName = $monthsIndo[$piketBulan] ?? 'Bulan ' . $piketBulan;
    $totalDaysCount = 0;
    foreach($guruPiketWeeks as $w) {
        $totalDaysCount += count($w['days']);
    }
@endphp

<div class="page-content page-anim" id="page-guru-piket" style="display:none">
    <div class="page-header" style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div class="page-title" style="font-size:22px;font-weight:800;margin-top:2px">Guru Piket 1 Bulan</div>
            <div class="page-subtitle">Atur penugasan 6 guru piket (Sesi 1: 07.00–11.00 &amp; Sesi 2: 11.00–Pulang) untuk 1 bulan penuh (Senin–Jumat) periode <strong>{{ $currentPiketMonthName }} {{ $piketTahun }}</strong>.</div>
        </div>
        <div>
            <button type="button" class="btn-primary" onclick="bukaModalPengaturanWaPiket()" style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:9px;font-size:13px;font-weight:600;background:#059669;border-color:#059669">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Pengaturan WA Bot
            </button>
        </div>
    </div>

    {{-- Filter Bulan & Toolbar --}}
    <div class="card" style="padding:14px 18px;margin-bottom:20px;max-width:1200px">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            {{-- Pilihan Dropdown Bulan, Tahun & Minggu --}}
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <div style="display:inline-flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;padding:4px 10px;border-radius:9px">
                    <select id="select-piket-bulan" class="filter-select" onchange="ubahBulanPiket(this.value, document.getElementById('select-piket-tahun').value)" style="border:none;background:transparent;font-weight:700;font-size:13.5px;color:#0f172a;padding:4px 6px;cursor:pointer">
                        @foreach($monthsIndo as $mNum => $mLabel)
                            <option value="{{ $mNum }}" {{ $piketBulan == $mNum ? 'selected' : '' }}>{{ $mLabel }}</option>
                        @endforeach
                    </select>
                    <select id="select-piket-tahun" class="filter-select" onchange="ubahBulanPiket(document.getElementById('select-piket-bulan').value, this.value)" style="border:none;background:transparent;font-weight:700;font-size:13.5px;color:#0f172a;padding:4px 6px;cursor:pointer">
                        @for($y = (int)date('Y') - 1; $y <= (int)date('Y') + 3; $y++)
                            <option value="{{ $y }}" {{ $piketTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Filter Pilihan Minggu --}}
                <div style="display:inline-flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;padding:4px 10px;border-radius:9px">
                    <select id="select-piket-minggu" class="filter-select" onchange="filterMingguPiket(this.value)" style="border:none;background:transparent;font-weight:700;font-size:13.5px;color:#0f172a;padding:4px 6px;cursor:pointer">
                        <option value="all">Semua Minggu (1 Bulan Penuh)</option>
                        @foreach($guruPiketWeeks as $w)
                            <option value="{{ $w['week_num'] }}">Minggu {{ $w['week_num'] }} ({{ \Carbon\Carbon::parse($w['days'][0]['tanggal'])->format('d M') }} – {{ \Carbon\Carbon::parse(end($w['days'])['tanggal'])->format('d M') }})</option>
                        @endforeach
                    </select>
                </div>

                <span style="font-size:12px;color:#64748b;background:#f1f5f9;padding:6px 12px;border-radius:20px;font-weight:600">
                    📅 {{ $totalDaysCount }} Hari Kerja · {{ count($allGuruPiket) }} Guru Tersedia
                </span>
            </div>

            {{-- Tombol Kosongkan --}}
            <div>
                <button type="button" class="btn-secondary" onclick="kosongkanSemuaSlotPiket()" style="font-size:12px;padding:7px 14px;border-radius:8px;color:#dc2626;border-color:#fecaca;background:#fef2f2;display:inline-flex;align-items:center;gap:6px;font-weight:600">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    <span id="btn-kosongkan-text">Kosongkan Bulan Ini</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Form Kalender 1 Bulan --}}
    <div class="card" style="padding:22px 24px;max-width:1200px">
        <form id="guru-piket-form" onsubmit="simpanGuruPiketBulk(event)">
            @csrf

            <div style="display:grid;gap:28px;margin-bottom:24px">
                @forelse($guruPiketWeeks as $wIdx => $week)
                    <div class="gp-week-block" data-week-index="{{ $wIdx + 1 }}" style="border:1px solid #e2e8f0;border-radius:14px;padding:18px 20px;background:#ffffff;box-shadow:0 2px 6px rgba(0,0,0,0.02)">
                        {{-- Header Minggu --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1.5px dashed #e2e8f0">
                            <div style="display:flex;align-items:center;gap:10px">
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#fff7ed;color:#ea580c;border-radius:8px;font-weight:800;font-size:13px;border:1px solid #ffedd5">
                                    {{ $week['week_num'] }}
                                </span>
                                <div>
                                    <h4 style="font-size:15px;font-weight:800;color:#0f172a;margin:0">Minggu ke-{{ $week['week_num'] }}</h4>
                                    <div style="font-size:11.5px;color:#64748b">{{ $week['label'] }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Grid Hari dalam Minggu Ini --}}
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:16px">
                            @foreach($week['days'] as $day)
                                @php($isToday = ($day['is_today'] ?? false))
                                <div class="gp-day-card" data-date="{{ $day['tanggal'] }}" data-hari-iso="{{ $day['hari_iso'] }}" style="border:1.5px solid {{ $isToday ? '#f97316' : '#e2e8f0' }};border-radius:12px;padding:14px;background:{{ $isToday ? '#fffaf5' : '#fafbfc' }};position:relative;box-shadow:{{ $isToday ? '0 4px 14px rgba(249,115,22,0.12)' : 'none' }}">
                                    {{-- Header Hari --}}
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid {{ $isToday ? '#fed7aa' : '#f1f5f9' }}">
                                        <div style="display:flex;align-items:center;gap:7px">
                                            <span style="font-size:13.5px;font-weight:800;color:{{ $isToday ? '#ea580c' : '#1e293b' }}">{{ $day['hari'] }}</span>
                                            <span style="font-size:12px;color:#64748b;font-weight:600">{{ \Carbon\Carbon::parse($day['tanggal'])->format('d M Y') }}</span>
                                            @if($isToday)
                                                <span style="background:#ea580c;color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:10px;text-transform:uppercase;letter-spacing:0.5px">Hari Ini</span>
                                            @endif
                                        </div>
                                        <button type="button" onclick="kosongkanHari('{{ $day['tanggal'] }}')" title="Kosongkan hari ini" style="border:none;background:transparent;cursor:pointer;color:#94a3b8;padding:2px;display:flex;align-items:center;border-radius:4px" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>

                                    {{-- Sesi 1: 07.00 - 11.00 (3 Guru) --}}
                                    <div style="margin-bottom:12px;background:#ffffff;border:1px solid {{ $isToday ? '#fed7aa' : '#e2e8f0' }};border-radius:10px;padding:10px">
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                            <span style="font-size:11.5px;font-weight:700;color:#0369a1;background:#e0f2fe;padding:2px 8px;border-radius:6px;display:inline-flex;align-items:center;gap:5px">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                Sesi 1 (07.00 – 11.00)
                                            </span>
                                            <span style="font-size:10.5px;color:#64748b;font-weight:600">3 Guru</span>
                                        </div>
                                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px">
                                            @for($slot = 0; $slot < 3; $slot++)
                                                @php($selectedId = $guruPiketAssignments[$day['tanggal']][$slot] ?? '')
                                                <div class="gp-sd" data-slot data-slot-index="{{ $slot }}">
                                                    <input type="hidden" name="assignments[{{ $day['tanggal'] }}][]" value="{{ $selectedId }}">
                                                    <label style="font-size:10px;color:#64748b;display:block;margin-bottom:2px;font-weight:600">Guru {{ $slot + 1 }}</label>
                                                    <button type="button" class="gp-sd-trigger filter-input" style="padding:5px 6px;font-size:11.5px;" data-trigger>
                                                        <span class="gp-sd-value {{ $selectedId === '' ? 'empty' : '' }}">{{ $selectedId !== '' && isset($guruNameMap[$selectedId]) ? $guruNameMap[$selectedId] : '— Pilih —' }}</span>
                                                        @if($selectedId !== '')
                                                            <span class="gp-sd-clear" role="button" tabindex="-1" data-clear>
                                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                            </span>
                                                        @endif
                                                        <svg class="gp-sd-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
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

                                    {{-- Sesi 2: 11.00 - Pulang (3 Guru) --}}
                                    <div style="background:#ffffff;border:1px solid {{ $isToday ? '#fed7aa' : '#e2e8f0' }};border-radius:10px;padding:10px">
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                            <span style="font-size:11.5px;font-weight:700;color:#c2410c;background:#ffedd5;padding:2px 8px;border-radius:6px;display:inline-flex;align-items:center;gap:5px">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                Sesi 2 (11.00 – Pulang)
                                            </span>
                                            <span style="font-size:10.5px;color:#64748b;font-weight:600">3 Guru</span>
                                        </div>
                                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px">
                                            @for($slot = 3; $slot < 6; $slot++)
                                                @php($selectedId = $guruPiketAssignments[$day['tanggal']][$slot] ?? '')
                                                <div class="gp-sd" data-slot data-slot-index="{{ $slot }}">
                                                    <input type="hidden" name="assignments[{{ $day['tanggal'] }}][]" value="{{ $selectedId }}">
                                                    <label style="font-size:10px;color:#64748b;display:block;margin-bottom:2px;font-weight:600">Guru {{ $slot + 1 }}</label>
                                                    <button type="button" class="gp-sd-trigger filter-input" style="padding:5px 6px;font-size:11.5px;" data-trigger>
                                                        <span class="gp-sd-value {{ $selectedId === '' ? 'empty' : '' }}">{{ $selectedId !== '' && isset($guruNameMap[$selectedId]) ? $guruNameMap[$selectedId] : '— Pilih —' }}</span>
                                                        @if($selectedId !== '')
                                                            <span class="gp-sd-clear" role="button" tabindex="-1" data-clear>
                                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                            </span>
                                                        @endif
                                                        <svg class="gp-sd-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
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
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:40px;color:#64748b;background:#f8fafc;border-radius:12px;border:1px dashed #cbd5e1">
                        Tidak ada hari kerja pada bulan dan tahun yang dipilih.
                    </div>
                @endforelse
            </div>

            {{-- Footer Simpan --}}
            <div style="display:flex;justify-content:flex-end;align-items:center;gap:12px;padding-top:16px;border-top:1px solid #e2e8f0">
                <button type="submit" class="btn-primary" style="padding:11px 32px;border-radius:10px;font-size:14px;font-weight:700;display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#f97316,#ea580c);border:none;box-shadow:0 4px 14px rgba(234,88,12,0.35)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Penugasan ({{ $currentPiketMonthName }} {{ $piketTahun }})
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pengaturan Nomor WhatsApp Bot / Guru Piket (Di luar .page-content agar fixed backdrop tidak ikut ter-scroll) --}}
<div id="modal-wa-guru-piket" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;width:100vw;height:100vh;background:rgba(15,23,42,.65);z-index:1040;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(4px);box-sizing:border-box" onclick="if(event.target===this) tutupModalPengaturanWaPiket()">
    <div class="card" style="width:100%;max-width:580px;max-height:calc(100vh - 40px);background:#fff;border-radius:16px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);border:1px solid #e2e8f0;display:flex;flex-direction:column;overflow:hidden;animation:modalEnter 0.25s ease-out">
        {{-- Header Modal --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 24px;border-bottom:1px solid #f1f5f9;background:#fafbfc">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:42px;height:42px;background:#ecfdf5;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#059669;flex-shrink:0;box-shadow:0 2px 6px rgba(5,150,105,0.15)">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div>
                    <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0">Pengaturan WhatsApp Bot Notifikasi</h3>
                    <div style="font-size:12px;color:#64748b;margin-top:2px">Integrasi bot WhatsApp &amp; nomor penerima perizinan</div>
                </div>
            </div>
            <button type="button" onclick="tutupModalPengaturanWaPiket()" aria-label="Tutup" style="border:0;background:#f1f5f9;width:32px;height:32px;border-radius:8px;font-size:20px;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s" onmouseover="this.style.background='#e2e8f0';this.style.color='#0f172a'" onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b'">&times;</button>
        </div>

        {{-- Body Modal (Scrollable) --}}
        <div style="overflow-y:auto;padding:22px 24px;flex:1;display:grid;gap:18px">
            {{-- Status & Kontrol Server Bot --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px;display:grid;gap:12px">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
                    <div>
                        <div style="font-size:12.5px;font-weight:700;color:#1e293b">Status Server Bot WhatsApp:</div>
                        <div id="modal-bot-account-info" style="font-size:11.5px;color:#059669;font-weight:600;margin-top:2px;display:none">
                            Nomor Bot: <span id="modal-bot-account-phone">-</span>
                        </div>
                    </div>
                    <span class="badge {{ ($waBotStatus['online'] ?? false) ? 'badge-success' : 'badge-danger' }}" id="modal-bot-status-badge" style="font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px">
                        <span style="width:8px;height:8px;background:{{ ($waBotStatus['online'] ?? false) ? '#22c55e' : '#ef4444' }};border-radius:50%;display:inline-block"></span>
                        <span id="modal-bot-status-text">{{ ($waBotStatus['online'] ?? false) ? 'Online' : 'Offline' }}</span>
                    </span>
                </div>

                {{-- Box QR Code jika butuh scan login / tambah bot --}}
                <div id="modal-bot-qr-box" style="display:none;background:#fff;border:1.5px dashed #059669;border-radius:12px;padding:16px;text-align:center;box-shadow:0 4px 12px rgba(5,150,105,0.08)">
                    <div style="font-size:13px;font-weight:700;color:#065f46;margin-bottom:4px">Scan QR Code dengan WhatsApp di HP Anda</div>
                    <div style="font-size:11.5px;color:#047857;margin-bottom:10px">Buka WhatsApp &gt; Perangkat Tertaut &gt; Tautkan Perangkat</div>
                    <div id="modal-bot-qr-img-wrap" style="display:flex;justify-content:center;margin-bottom:10px">
                        <img id="modal-bot-qr-img" src="" alt="Scan QR Code WhatsApp" style="width:200px;height:200px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;padding:6px">
                    </div>
                    <div style="font-size:11px;color:#64748b;display:flex;align-items:center;justify-content:center;gap:6px">
                        <span style="display:inline-block;width:6px;height:6px;background:#059669;border-radius:50%;animation:pulse 1.5s infinite"></span>
                        Menunggu scan... Status akan otomatis terhubung setelah discan.
                    </div>
                </div>

                {{-- Tombol Aksi Kontrol Bot --}}
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button type="button" class="btn-secondary" id="btn-tambah-bot" onclick="startAtauRestartBot()" style="flex:1;min-width:140px;border-radius:8px;padding:8px 12px;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:6px;background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        Hubungkan / Scan Bot
                    </button>
                    <button type="button" class="btn-secondary" id="btn-putuskan-bot-modal" onclick="putuskanBotWaModal()" style="flex:1;min-width:130px;border-radius:8px;padding:8px 12px;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:6px;background:#fef2f2;border-color:#fecaca;color:#dc2626">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Putuskan WA Bot
                    </button>
                    <button type="button" class="btn-secondary" onclick="cekStatusBotWaModal(true)" style="min-width:95px;border-radius:8px;padding:8px 10px;font-size:12px;display:flex;align-items:center;justify-content:center;gap:5px">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Cek Status
                    </button>
                    <button type="button" class="btn-secondary" onclick="modalTestKirimWa()" style="min-width:105px;border-radius:8px;padding:8px 10px;font-size:12px;display:flex;align-items:center;justify-content:center;gap:5px;background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Test Kirim WA
                    </button>
                </div>
            </div>

            {{-- Form Nomor Tujuan --}}
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

                <div style="background:#f8fafc;border:1px solid #e2e8f0;padding:12px 14px;border-radius:10px;display:grid;gap:8px">
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
                        <label style="font-size:12px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:6px">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            URL Domain / Link Akses WhatsApp
                        </label>
                        <span style="font-size:11px;color:#059669;font-weight:600" id="modal-label-url-mode">Cloudflare / Ngrok / IP Otomatis</span>
                    </div>
                    <input type="text" class="filter-input" id="modal-set-wa-public-url" value="{{ $waPublicUrl ?? '' }}" placeholder="Contoh: https://xxxx.trycloudflare.com atau https://xxxx.ngrok-free.app" style="width:100%" oninput="updateLinkPreviewModal()">
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <button type="button" class="btn-secondary" onclick="isiUrlOtomatisModal('current')" style="padding:5px 10px;font-size:11.5px;border-radius:6px;display:inline-flex;align-items:center;gap:4px;background:#ecfdf5;color:#059669;border-color:#a7f3d0;font-weight:600">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            Gunakan Domain Browser Saat Ini
                        </button>
                        <button type="button" class="btn-secondary" onclick="isiUrlOtomatisModal('lan')" style="padding:5px 10px;font-size:11.5px;border-radius:6px;display:inline-flex;align-items:center;gap:4px">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Reset ke IP Otomatis
                        </button>
                    </div>
                    <div style="font-size:11px;color:#475569;background:#fff;padding:8px 10px;border-radius:6px;border:1px solid #e2e8f0;word-break:break-all">
                        <strong style="color:#0f172a">Preview Link di WA:</strong> <span id="modal-preview-link-wa" style="color:#2563eb;font-family:monospace">Memuat...</span>
                    </div>
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

                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;padding-top:14px;border-top:1px solid #f1f5f9">
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
let waModalPollInterval = null;

function updateLinkPreviewModal() {
    const input = document.getElementById('modal-set-wa-public-url');
    const preview = document.getElementById('modal-preview-link-wa');
    const labelMode = document.getElementById('modal-label-url-mode');
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

function isiUrlOtomatisModal(type) {
    const input = document.getElementById('modal-set-wa-public-url');
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
    updateLinkPreviewModal();
}

function bukaModalPengaturanWaPiket() {
    const modal = document.getElementById('modal-wa-guru-piket');
    if (modal) {
        modal.style.display = 'flex';
        updateLinkPreviewModal();
        cekStatusBotWaModal(false);
    }
}

function tutupModalPengaturanWaPiket() {
    const modal = document.getElementById('modal-wa-guru-piket');
    if (modal) {
        modal.style.display = 'none';
    }
    if (waModalPollInterval) {
        clearInterval(waModalPollInterval);
        waModalPollInterval = null;
    }
}

function cekStatusBotWaModal(showAlert = true) {
    const badge = document.getElementById('modal-bot-status-badge');
    const textEl = document.getElementById('modal-bot-status-text');
    const qrBox = document.getElementById('modal-bot-qr-box');
    const qrImg = document.getElementById('modal-bot-qr-img');
    const accInfo = document.getElementById('modal-bot-account-info');
    const accPhone = document.getElementById('modal-bot-account-phone');

    const btnPutuskan = document.getElementById('btn-putuskan-bot-modal');
    const btnTambah = document.getElementById('btn-tambah-bot');

    if (badge) {
        badge.className = 'badge badge-info';
        badge.style.cssText = 'font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd';
        badge.innerHTML = '<span style="width:8px;height:8px;background:#0284c7;border-radius:50%;display:inline-block"></span> <span>Memeriksa...</span>';
    }

    fetch(@json(route('pengaturan.qr-wa')), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'connected') {
            if (waModalPollInterval) {
                clearInterval(waModalPollInterval);
                waModalPollInterval = null;
            }
            if (badge) {
                badge.className = 'badge badge-success';
                badge.style.cssText = 'font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block"></span> <span>Online</span>';
            }
            if (accInfo && accPhone) {
                accPhone.textContent = data.user ? '+' + data.user : 'Terhubung';
                accInfo.style.display = 'block';
            }
            // Sembunyikan barcode scan saat WA sudah terhubung
            if (qrBox) qrBox.style.display = 'none';
            // Tampilkan tombol Putuskan WA, sembunyikan tombol Hubungkan
            if (btnPutuskan) btnPutuskan.style.display = 'inline-flex';
            if (btnTambah) btnTambah.style.display = 'none';

            if (showAlert) {
                Swal.fire({ icon: 'success', title: 'Bot WhatsApp Online!', text: data.message || 'Bot terhubung dan siap mengirim notifikasi.', timer: 2000, showConfirmButton: false });
            }
        } else if (data.status === 'connecting') {
            if (badge) {
                badge.className = 'badge badge-info';
                badge.style.cssText = 'font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#0284c7;border-radius:50%;display:inline-block"></span> <span>Menghubungkan Sesi...</span>';
            }
            if (accInfo && accPhone && data.user) {
                accPhone.textContent = '+' + data.user;
                accInfo.style.display = 'block';
            }
            if (qrBox) qrBox.style.display = 'none';
            if (btnPutuskan) btnPutuskan.style.display = 'inline-flex';
            if (btnTambah) btnTambah.style.display = 'none';

            if (!waModalPollInterval) {
                waModalPollInterval = setInterval(() => {
                    const modal = document.getElementById('modal-wa-guru-piket');
                    if (modal && modal.style.display !== 'none') {
                        cekStatusBotWaModal(false);
                    } else {
                        clearInterval(waModalPollInterval);
                        waModalPollInterval = null;
                    }
                }, 2000);
            }
        } else if (data.status === 'waiting_qr' && data.qr_image) {
            if (badge) {
                badge.className = 'badge badge-warning';
                badge.style.cssText = 'font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#fef3c7;color:#92400e;border:1px solid #fde68a';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block"></span> <span>Menunggu Scan QR</span>';
            }
            if (accInfo) accInfo.style.display = 'none';
            // Munculkan barcode baru untuk di-scan jika terputus
            if (qrBox && qrImg) {
                qrImg.src = data.qr_image;
                qrBox.style.display = 'block';
            }
            // Sembunyikan Putuskan, tampilkan tombol Hubungkan / Refresh
            if (btnPutuskan) btnPutuskan.style.display = 'none';
            if (btnTambah) btnTambah.style.display = 'inline-flex';

            // Auto poll tiap 3 detik saat menunggu QR
            if (!waModalPollInterval) {
                waModalPollInterval = setInterval(() => {
                    const modal = document.getElementById('modal-wa-guru-piket');
                    if (modal && modal.style.display !== 'none') {
                        cekStatusBotWaModal(false);
                    } else {
                        clearInterval(waModalPollInterval);
                        waModalPollInterval = null;
                    }
                }, 3000);
            }
            if (showAlert) {
                Swal.fire({ icon: 'info', title: 'Scan QR Code', text: 'Silakan scan QR Code yang muncul dengan WhatsApp di HP Anda.' });
            }
        } else {
            if (badge) {
                badge.className = 'badge badge-danger';
                badge.style.cssText = 'font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca';
                badge.innerHTML = '<span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block"></span> <span>Offline</span>';
            }
            if (accInfo) accInfo.style.display = 'none';
            if (btnPutuskan) btnPutuskan.style.display = 'none';
            if (btnTambah) btnTambah.style.display = 'inline-flex';

            // Jika offline, coba trigger start WhatsApp otomatis agar generate QR
            if (qrBox) qrBox.style.display = 'none';
            if (showAlert) {
                Swal.fire({ icon: 'warning', title: 'Bot Offline', text: data.message || 'Server bot WhatsApp belum aktif. Klik "Hubungkan / Scan Bot".', confirmButtonColor: '#059669' });
            }
        }
    })
    .catch(err => {
        if (badge) {
            badge.className = 'badge badge-danger';
            badge.style.cssText = 'font-size:11.5px;padding:5px 12px;display:inline-flex;align-items:center;gap:6px;border-radius:20px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca';
            badge.innerHTML = '<span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block"></span> <span>Offline</span>';
        }
        if (accInfo) accInfo.style.display = 'none';
        if (qrBox) qrBox.style.display = 'none';
        if (btnPutuskan) btnPutuskan.style.display = 'none';
        if (btnTambah) btnTambah.style.display = 'inline-flex';
        if (showAlert) {
            Swal.fire({ icon: 'warning', title: 'Bot Belum Aktif', text: 'Server bot belum berjalan. Klik tombol "Hubungkan / Scan Bot" untuk mengaktifkan.', confirmButtonColor: '#059669' });
        }
    });
}

function startAtauRestartBot() {
    const btn = document.getElementById('btn-tambah-bot');
    if (btn) btn.disabled = true;

    Swal.fire({
        title: 'Menyiapkan Bot WhatsApp...',
        text: 'Memulai server bot dan menyiapkan sesi QR Code...',
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
            cekStatusBotWaModal(false);
        }, 2000);
    })
    .catch(() => {
        setTimeout(() => {
            Swal.close();
            cekStatusBotWaModal(false);
        }, 2000);
    })
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

function putuskanBotWaModal() {
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

            fetch(@json(route('pengaturan.disconnect-wa')), {
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
                    cekStatusBotWaModal(false);
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

function simpanPengaturanWaPiket(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-simpan-wa-piket');
    if (btn) btn.disabled = true;

    const kepsek = document.getElementById('modal-set-wa-kepsek')?.value?.trim() ?? '';
    const wakaSdm = document.getElementById('modal-set-wa-waka-sdm')?.value?.trim() ?? '';
    const wakaKesiswaan = document.getElementById('modal-set-wa-waka-kesiswaan')?.value?.trim() ?? '';
    const publicUrl = document.getElementById('modal-set-wa-public-url')?.value?.trim() ?? '';
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
            wa_public_url: publicUrl,
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
        const setPublicUrl = document.getElementById('set-wa-public-url');
        const setGwAktif = document.getElementById('set-wa-gateway-aktif');
        if (setKepsek) setKepsek.value = kepsek;
        if (setWakaSdm) setWakaSdm.value = wakaSdm;
        if (setWakaKesiswaan) setWakaKesiswaan.value = wakaKesiswaan;
        if (setPublicUrl) setPublicUrl.value = publicUrl;
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

function ubahBulanPiket(bulan, tahun) {
    const url = new URL(window.location.href);
    url.searchParams.set('piket_bulan', bulan);
    url.searchParams.set('piket_tahun', tahun);
    url.hash = 'guru-piket';
    window.location.href = url.toString();
}

function filterMingguPiket(weekVal) {
    const weekBlocks = document.querySelectorAll('#guru-piket-form .gp-week-block');
    weekBlocks.forEach(block => {
        if (weekVal === 'all' || block.dataset.weekIndex === String(weekVal)) {
            block.style.display = '';
        } else {
            block.style.display = 'none';
        }
    });

    const btnKosongkanText = document.getElementById('btn-kosongkan-text');
    if (btnKosongkanText) {
        btnKosongkanText.textContent = weekVal === 'all' ? 'Kosongkan Bulan Ini' : 'Kosongkan Minggu ' + weekVal;
    }
}

function kosongkanSemuaSlotPiket() {
    const selectedWeek = document.getElementById('select-piket-minggu')?.value || 'all';
    const isSingleWeek = selectedWeek !== 'all';
    const confirmTitle = isSingleWeek ? 'Kosongkan Slot Minggu ' + selectedWeek + '?' : 'Kosongkan Semua Slot Bulan Ini?';
    const confirmText = isSingleWeek ? 'Semua pilihan guru piket pada Minggu ' + selectedWeek + ' akan dikosongkan.' : 'Seluruh pilihan guru piket di bulan ini pada formulir akan dikosongkan.';

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal'
    }).then(res => {
        if (res.isConfirmed) {
            const selector = isSingleWeek 
                ? '#guru-piket-form .gp-week-block[data-week-index="' + selectedWeek + '"] .gp-sd'
                : '#guru-piket-form .gp-sd';
            document.querySelectorAll(selector).forEach(sd => {
                if (window.gpSetValue) window.gpSetValue(sd, '', '');
            });
            Swal.fire({
                icon: 'info',
                title: 'Dikosongkan',
                text: 'Slot guru piket telah dikosongkan. Klik "Simpan Penugasan" di bawah untuk menyimpan perubahan ini ke database.',
                timer: 2200,
                showConfirmButton: false
            });
        }
    });
}

function kosongkanHari(tanggal) {
    const card = document.querySelector('#guru-piket-form .gp-day-card[data-date="' + tanggal + '"]');
    if (!card) return;
    card.querySelectorAll('.gp-sd').forEach(sd => {
        if (window.gpSetValue) window.gpSetValue(sd, '', '');
    });
}

(function () {
    const form = document.getElementById('guru-piket-form');

    function closeAll(except) {
        document.querySelectorAll('.gp-sd.open').forEach(sd => {
            if (except && sd === except) return;
            closeSlot(sd);
        });
    }

    function openSlot(sd) {
        const search = sd.querySelector('[data-search]');
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
        if (hidden) hidden.value = value || '';
        if (valueEl) {
            valueEl.textContent = value ? guruName : '— Pilih Guru —';
            valueEl.classList.toggle('empty', !value);
        }
        let clear = sd.querySelector('.gp-sd-clear');
        if (!clear && value) {
            const caret = sd.querySelector('.gp-sd-caret');
            const btn = document.createElement('span');
            btn.className = 'gp-sd-clear';
            btn.setAttribute('role', 'button');
            btn.setAttribute('tabindex', '-1');
            btn.setAttribute('data-clear', '');
            btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
            if (caret) caret.parentNode.insertBefore(btn, caret);
            clear = btn;
        }
        if (clear) clear.style.display = value ? '' : 'none';

        // Update selected option style
        sd.querySelectorAll('.gp-sd-option').forEach(o => {
            o.classList.toggle('selected', value && o.dataset.value == value);
        });
    }

    // Expose globally for clear functions
    window.gpSetValue = setValue;

    function filterOptions(sd, keyword) {
        const normalized = keyword.trim().toLowerCase();
        const list = sd.querySelector('[data-list]');
        if (!list) return;
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
    document.addEventListener('click', e => {
        const trigger = e.target.closest('[data-trigger]');
        if (trigger) {
            const sd = trigger.closest('.gp-sd');
            const wasOpen = sd.classList.contains('open');
            closeAll(sd);
            if (!wasOpen) openSlot(sd);
            return;
        }
        // Ignore clicks inside panel search
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

    // Option selection
    document.addEventListener('click', e => {
        const opt = e.target.closest('.gp-sd-option');
        if (!opt) return;
        const sd = opt.closest('.gp-sd');
        const value = opt.dataset.value;
        const guruName = opt.dataset.guruName;
        setValue(sd, value, guruName);
        closeSlot(sd);
    });

    // Search input
    document.addEventListener('input', e => {
        const search = e.target.closest('[data-search]');
        if (search) {
            const sd = search.closest('.gp-sd');
            filterOptions(sd, search.value);
        }
    });

    // Keyboard navigation (Escape to close)
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeAll();
        }
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
