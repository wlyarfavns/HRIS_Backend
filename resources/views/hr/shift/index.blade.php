@extends('layouts.hr')

@section('title', 'Shift & Roster Kerja')
@section('page-title', 'Shift & Roster Kerja')
@section('page-desc', 'Manajemen kalender roster, bulk assign shift, pengajuan tukar shift, dan geofencing office.')

@php
    $shiftTypes = [
        ['code' => 'P', 'name' => 'Shift Pagi', 'time' => '08:00 - 17:00', 'color' => '#059669', 'bg' => 'bg-emerald-600', 'count' => 112, 'desc' => 'Jam operasional standar kantor & sales'],
        ['code' => 'S', 'name' => 'Shift Siang', 'time' => '13:00 - 22:00', 'color' => '#d97706', 'bg' => 'bg-amber-600', 'count' => 24, 'desc' => 'Dukungan operasional & customer service'],
        ['code' => 'M', 'name' => 'Shift Malam', 'time' => '22:00 - 07:00 (+1)', 'color' => '#7c3aed', 'bg' => 'bg-purple-600', 'count' => 8, 'desc' => 'Cross-day shift logistik & IT monitoring'],
        ['code' => 'L', 'name' => 'Libur (Off)', 'time' => 'Off Duty', 'color' => '#64748b', 'bg' => 'bg-slate-500', 'count' => 6, 'desc' => 'Hari istirahat mingguan / roster off'],
    ];

    $roster = [
        ['nip' => 'EMP-00231', 'name' => 'Michael Scott', 'dept' => 'Sales', 'pos' => 'Regional Manager', 'avatar' => 14, 'days' => ['P','P','P','P','P','L','L']],
        ['nip' => 'EMP-00567', 'name' => 'Pam Beesly', 'dept' => 'Front Office', 'pos' => 'Receptionist', 'avatar' => 47, 'days' => ['P','P','S','S','P','L','L']],
        ['nip' => 'EMP-00812', 'name' => 'Jim Halpert', 'dept' => 'Sales', 'pos' => 'Sales Executive', 'avatar' => 12, 'days' => ['S','S','S','P','P','L','L']],
        ['nip' => 'EMP-00933', 'name' => 'Dwight Schrute', 'dept' => 'Sales', 'pos' => 'Assistant Manager', 'avatar' => 51, 'days' => ['M','M','M','L','P','P','L']],
        ['nip' => 'EMP-01044', 'name' => 'Angela Martin', 'dept' => 'Finance', 'pos' => 'Accounting Staff', 'avatar' => 33, 'days' => ['P','P','P','P','P','L','L']],
        ['nip' => 'EMP-01120', 'name' => 'Kevin Malone', 'dept' => 'Finance', 'pos' => 'Junior Accountant', 'avatar' => 55, 'days' => ['P','P','P','P','P','L','L']],
    ];

    $mapBadge = [
        'P' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
        'S' => 'bg-amber-100 text-amber-800 border border-amber-300',
        'M' => 'bg-purple-100 text-purple-800 border border-purple-300',
        'L' => 'bg-slate-100 text-slate-600 border border-slate-300'
    ];

    $mapName = [
        'P' => 'Shift Pagi (08:00 – 17:00)',
        'S' => 'Shift Siang (13:00 – 22:00)',
        'M' => 'Shift Malam (22:00 – 07:00 H+1)',
        'L' => 'Libur / Off Duty'
    ];

    $labels = [
        ['day' => 'Senin', 'date' => '21 Okt'],
        ['day' => 'Selasa', 'date' => '22 Okt'],
        ['day' => 'Rabu', 'date' => '23 Okt'],
        ['day' => 'Kamis', 'date' => '24 Okt'],
        ['day' => 'Jumat', 'date' => '25 Okt'],
        ['day' => 'Sabtu', 'date' => '26 Okt'],
        ['day' => 'Minggu', 'date' => '27 Okt'],
    ];

    $swapRequests = [
        [
            'id' => 'SWP-101',
            'from' => 'Jim Halpert', 'from_avatar' => 12, 'from_shift' => 'Shift Siang (24 Okt)',
            'to' => 'Dwight Schrute', 'to_avatar' => 51, 'to_shift' => 'Shift Malam (24 Okt)',
            'reason' => 'Keperluan keluarga mendesak',
            'peer_approved' => true,
            'status' => 'Pending SPV',
            'created' => 'Hari ini, 08:30',
        ],
        [
            'id' => 'SWP-102',
            'from' => 'Pam Beesly', 'from_avatar' => 47, 'from_shift' => 'Shift Pagi (26 Okt)',
            'to' => 'Angela Martin', 'to_avatar' => 33, 'to_shift' => 'Libur (26 Okt)',
            'reason' => 'Tukar shift piket akhir pekan',
            'peer_approved' => true,
            'status' => 'Approved',
            'created' => 'Kemarin, 14:15',
        ],
    ];
@endphp

@section('content')
<div x-data="{
    showBulkModal: false,
    showAddShiftModal: false,
    selectedDept: 'Semua',
    selectedShift: 'P',
    searchQuery: '',
    radiusMeter: 100,
    toleranceMin: 15,
    crossDayActive: true,
    selectAll: false,
    selectedEmployees: ['EMP-00231', 'EMP-00567'],
    
    // Quick toast alert
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },

    toggleSelectAll() {
        this.selectAll = !this.selectAll;
        this.selectedEmployees = this.selectAll ? ['EMP-00231', 'EMP-00567', 'EMP-00812', 'EMP-00933', 'EMP-01044', 'EMP-01120'] : [];
    }
}" class="space-y-6">

    <!-- MASTER SHIFT CARDS HEADER ROW -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($shiftTypes as $t)
            <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm hover:shadow-md transition space-y-2 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full shrink-0 shadow-sm" style="background-color:{{ $t['color'] }}"></span>
                        <h2 class="text-sm font-bold text-on-surface">{{ $t['name'] }}</h2>
                    </div>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-black/5 text-on-surface-variant/70">{{ $t['count'] }} Karyawan</span>
                </div>
                <p class="text-xs font-mono font-bold text-primary">{{ $t['time'] }}</p>
                <p class="text-[11px] text-on-surface-variant/60 line-clamp-1">{{ $t['desc'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- MAIN ROSTER CALENDAR & TOOLBAR -->
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
        <!-- Toolbar Header -->
        <div class="p-6 border-b border-black/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-lg font-bold text-on-surface">Kalender Roster Kerja Tim</h2>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Periode Aktif</span>
                </div>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">21 – 27 Oktober 2026 · Mengatur rotasi shift mingguan & toleransi absensi</p>
            </div>

            <!-- Action Toolbar & Filters -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Search Input -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari karyawan / NIP..." class="pl-9 pr-3 py-2 text-xs border border-black/10 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary w-44">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[16px]">search</span>
                </div>

                <!-- Dept Filter -->
                <div class="relative">
                    <select x-model="selectedDept" class="text-xs border border-black/10 rounded-xl pl-3 pr-8 py-2 bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer font-medium">
                        <option value="Semua">Semua Departemen</option>
                        <option value="Sales">Sales &amp; Marketing</option>
                        <option value="Front Office">Front Office</option>
                        <option value="Finance">Finance &amp; Accounting</option>
                    </select>
                </div>

                <!-- Week Navigation -->
                <div class="flex items-center border border-black/10 rounded-xl p-0.5 bg-surface-variant/10">
                    <button class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-white text-on-surface-variant/70 transition">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <span class="text-xs font-bold px-2 text-on-surface">Minggu Ini</span>
                    <button class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-white text-on-surface-variant/70 transition">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>

                <!-- Bulk Assign Button -->
                <button type="button" @click="showBulkModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold text-xs transition shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">group_add</span>
                    Bulk Assign Shift
                </button>
            </div>
        </div>

        <!-- ROSTER TABLE (CRISP, STRUCTURED, NON-BLURRY) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5 min-w-[220px]">Karyawan</th>
                        <th class="px-4 py-3.5 min-w-[150px]">Departemen / Jabatan</th>
                        @foreach ($labels as $l)
                            <th class="px-2 py-3.5 text-center min-w-[85px] border-l border-black/5">
                                <span class="block text-xs font-bold text-on-surface">{{ $l['day'] }}</span>
                                <span class="block text-[10px] font-normal text-on-surface-variant/60">{{ $l['date'] }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($roster as $r)
                        <tr class="hover:bg-primary/5 transition" x-show="(selectedDept === 'Semua' || selectedDept === '{{ $r['dept'] }}') && (searchQuery === '' || '{{ strtolower($r['name']) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($r['nip']) }}'.includes(searchQuery.toLowerCase()))">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/40?img={{ $r['avatar'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $r['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $r['name'] }}</p>
                                        <p class="text-[11px] text-on-surface-variant/60 font-mono mt-0.5">{{ $r['nip'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-on-surface text-xs">{{ $r['dept'] }}</p>
                                <p class="text-[11px] text-on-surface-variant/60">{{ $r['pos'] }}</p>
                            </td>
                            @foreach ($r['days'] as $d)
                                <td class="px-2 py-3.5 text-center border-l border-black/5">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold shadow-2xs transition hover:scale-105 cursor-pointer {{ $mapBadge[$d] }}"
                                          title="{{ $mapName[$d] }}"
                                          @click="triggerToast('Shift {{ $r['name'] }} diubah ke {{ $d }}')">
                                        {{ $d }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- TABLE FOOTER WITH LEGEND & VALIDATION RULES -->
        <div class="px-6 py-3.5 bg-surface-variant/15 border-t border-black/5 flex items-center justify-between text-xs text-on-surface-variant/70 flex-wrap gap-4">
            <div class="flex items-center gap-4 flex-wrap">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-600"></span> <strong>P</strong> = Pagi (08:00–17:00)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-600"></span> <strong>S</strong> = Siang (13:00–22:00)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-purple-600"></span> <strong>M</strong> = Malam (22:00–07:00 H+1)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-500"></span> <strong>L</strong> = Libur (Off)</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-bold text-primary">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                Blueprint Validation: 1 Karyawan Max 1 Shift Aktif / Hari
            </div>
        </div>
    </div>

    <!-- LOWER SECTION: PENGAJUAN TUKAR SHIFT & GEOFENCING OFFICE (2 COLUMNS) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CARD 1: PENGAJUAN TUKAR SHIFT ANTA PEGAWAI -->
        <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-black/5 pb-4">
                <div>
                    <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">sync_alt</span>
                        Pengajuan Tukar Shift Antar Pegawai
                    </h2>
                    <p class="text-xs text-on-surface-variant/60 mt-0.5">Persetujuan 2 belah pihak & atasan (Supervisor/HR)</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">1 Menunggu SPV</span>
            </div>

            <div class="space-y-3.5">
                @foreach ($swapRequests as $req)
                    <div class="p-4 rounded-xl border border-black/10 bg-surface-variant/5 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center gap-1.5">
                                    <img src="https://i.pravatar.cc/24?img={{ $req['from_avatar'] }}" class="w-6 h-6 rounded-full" alt="">
                                    <span class="text-xs font-bold text-on-surface">{{ $req['from'] }}</span>
                                </div>
                                <span class="material-symbols-outlined text-[16px] text-primary">sync_alt</span>
                                <div class="flex items-center gap-1.5">
                                    <img src="https://i.pravatar.cc/24?img={{ $req['to_avatar'] }}" class="w-6 h-6 rounded-full" alt="">
                                    <span class="text-xs font-bold text-on-surface">{{ $req['to'] }}</span>
                                </div>
                            </div>
                            @if ($req['status'] === 'Pending SPV')
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Menunggu SPV</span>
                            @else
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">Disetujui</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-2.5 rounded-xl bg-white border border-black/5">
                                <span class="text-[10px] text-on-surface-variant/60 font-semibold uppercase block mb-0.5">Shift Semula</span>
                                <span class="font-bold text-on-surface">{{ $req['from_shift'] }}</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white border border-black/5">
                                <span class="text-[10px] text-on-surface-variant/60 font-semibold uppercase block mb-0.5">Shift Pengganti</span>
                                <span class="font-bold text-on-surface">{{ $req['to_shift'] }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1 border-t border-black/5">
                            <p class="text-on-surface-variant/70 italic text-[11px]">"{{ $req['reason'] }}"</p>
                            <span class="text-[10px] font-mono text-on-surface-variant/50">{{ $req['created'] }}</span>
                        </div>

                        @if ($req['status'] === 'Pending SPV')
                            <div class="pt-2 flex justify-end gap-2">
                                <button type="button" @click="triggerToast('Pengajuan tukar shift ditolak', 'error')" class="px-3 py-1.5 rounded-lg border border-black/10 text-xs font-semibold text-on-surface-variant hover:bg-black/5 transition">
                                    Tolak
                                </button>
                                <button type="button" @click="triggerToast('Pengajuan tukar shift disetujui!'); {{ $req['status'] = 'Approved' }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm">
                                    Setujui
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CARD 2: PENGATURAN RADIUS GEOFENCING OFFICE -->
        <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-5 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-black/5 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">share_location</span>
                            Pengaturan Radius Geofencing Office
                        </h2>
                        <p class="text-xs text-on-surface-variant/60 mt-0.5">Validasi absensi via GPS Haversine & toleransi jam hadir</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">GPS Geotag Aktif</span>
                </div>

                <!-- Location Status Card -->
                <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center shrink-0 text-primary">
                        <span class="material-symbols-outlined text-[28px]">apartment</span>
                    </div>
                    <div class="min-w-0 flex-1 text-xs">
                        <p class="font-bold text-on-surface text-sm">Kantor Pusat — Jakarta Selatan</p>
                        <p class="text-on-surface-variant/60 font-mono mt-0.5">Lat: -6.2088 | Long: 106.8456</p>
                        <p class="text-[11px] text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">security</span>
                            Anti-Mock Location Engine: Aktif (Mendeteksi Fake GPS)
                        </p>
                    </div>
                </div>

                <!-- Radius Slider -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-on-surface-variant/80 uppercase tracking-wide">Radius Absensi Kantor</label>
                        <span class="font-mono font-extrabold text-primary text-base" x-text="radiusMeter + ' meter'">100 meter</span>
                    </div>
                    <input type="range" min="25" max="500" step="25" x-model="radiusMeter" class="w-full accent-primary h-2 bg-black/10 rounded-lg cursor-pointer">
                    <div class="flex justify-between text-[10px] text-on-surface-variant/60 font-mono">
                        <span>25m (Sangat Ketat)</span>
                        <span>100m (Rekomendasi)</span>
                        <span>500m (Fleksibel)</span>
                    </div>
                </div>

                <!-- Tolerance & Cross-Day Rules -->
                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-black/5">
                    <div class="p-3 rounded-xl border border-black/5 bg-surface-variant/10">
                        <span class="text-[10px] font-bold text-on-surface-variant/60 uppercase">Toleransi Keterlambatan</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="material-symbols-outlined text-[18px] text-amber-600">timer</span>
                            <span class="font-mono font-bold text-sm text-on-surface">15 Menit</span>
                        </div>
                        <p class="text-[10px] text-on-surface-variant/50 mt-0.5">Clock in &le; 08:15 terhitung Tepat Waktu</p>
                    </div>

                    <div class="p-3 rounded-xl border border-black/5 bg-surface-variant/10">
                        <span class="text-[10px] font-bold text-on-surface-variant/60 uppercase">Logika Cross-Day (Malam)</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="material-symbols-outlined text-[18px] text-purple-600">nightlight</span>
                            <span class="font-mono font-bold text-sm text-on-surface">H+1 Clock-out</span>
                        </div>
                        <p class="text-[10px] text-on-surface-variant/50 mt-0.5">Tidak memotong kuota absen 2 hari</p>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="button" @click="triggerToast('Pengaturan Geofencing berhasil disimpan!')" class="w-full py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-medium text-xs transition shadow-sm">
                    Simpan Pengaturan Geofencing
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL BULK ASSIGN SHIFT KE TIM -->
    <div x-show="showBulkModal" x-cloak class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" @click.self="showBulkModal = false">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[20px]">group_add</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Bulk Assign Shift ke Tim</h3>
                        <p class="text-xs text-on-surface-variant/60">Terapkan jadwal shift serentak ke banyak karyawan</p>
                    </div>
                </div>
                <button type="button" @click="showBulkModal = false" class="text-on-surface-variant/40 hover:text-on-surface transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Form -->
            <div class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide block mb-1.5">1. Pilih Jenis Shift</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach ($shiftTypes as $st)
                            <label class="border rounded-xl p-2.5 flex flex-col items-center gap-1 cursor-pointer transition text-center"
                                   :class="selectedShift === '{{ $st['code'] }}' ? 'border-primary bg-primary/5 text-primary font-bold' : 'border-black/10 hover:border-black/20'">
                                <input type="radio" name="shift_preset" value="{{ $st['code'] }}" x-model="selectedShift" class="hidden">
                                <span class="w-3 h-3 rounded-full" style="background-color:{{ $st['color'] }}"></span>
                                <span class="text-xs leading-tight">{{ $st['name'] }}</span>
                                <span class="text-[10px] font-mono text-on-surface-variant/60">{{ $st['time'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide block mb-1">2. Dari Tanggal</label>
                        <input type="date" value="2026-10-21" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide block mb-1">Sampai Tanggal</label>
                        <input type="date" value="2026-10-27" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide">3. Pilih Karyawan Penerima</label>
                        <button type="button" @click="toggleSelectAll()" class="text-primary font-bold hover:underline"
                                x-text="selectAll ? 'Batal Pilih Semua' : 'Pilih Semua (6 Karyawan)'">Pilih Semua</button>
                    </div>

                    <div class="max-h-36 overflow-y-auto border border-black/10 rounded-xl p-2 divide-y divide-black/5 bg-surface-variant/10">
                        @foreach ($roster as $r)
                            <label class="flex items-center justify-between p-2 hover:bg-white rounded-lg cursor-pointer transition">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" value="{{ $r['nip'] }}" x-model="selectedEmployees" class="rounded border-black/20 text-primary focus:ring-primary/20">
                                    <img src="https://i.pravatar.cc/24?img={{ $r['avatar'] }}" class="w-5 h-5 rounded-full" alt="">
                                    <span class="font-bold text-on-surface">{{ $r['name'] }}</span>
                                </div>
                                <span class="text-[11px] font-mono text-on-surface-variant/60">{{ $r['dept'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-900 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-[18px] text-amber-700 shrink-0 mt-0.5">info</span>
                    <p class="text-[11px] leading-relaxed">
                        <span class="font-bold">Overlap Shift Guard:</span> Sistem akan menimpa jadwal sebelumnya pada rentang tanggal yang dipilih. Satu karyawan hanya akan memiliki 1 jadwal shift aktif per hari.
                    </p>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showBulkModal = false" class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showBulkModal = false; triggerToast('Jadwal shift berhasil diterapkan ke tim!')" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Terapkan Jadwal Shift
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION (THEME-MATCHED DEEP EMERALD) -->
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-white font-medium text-xs border border-emerald-500/30 backdrop-blur-md"
         :class="{
             'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
             'bg-rose-950 border-rose-500/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-xs font-semibold"></span>
    </div>

</div>
@endsection