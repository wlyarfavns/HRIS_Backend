@extends('layouts.hr')

@section('title', 'Shift & Roster Kerja')
@section('page-title', 'Shift & Roster Kerja')
@section('page-desc', 'Manajemen kalender roster, bulk assign shift, pengajuan tukar shift, dan geofencing office.')

{{--
    Dikirim dari App\Http\Controllers\Web\HR\ShiftController@index:
    $shiftTypes, $roster, $mapBadge, $mapName, $labels, $swapRequests,
    $pendingSpvCount, $weekStart, $weekEnd, $departments
--}}

@section('content')
<div x-data="{
    showBulkModal: false,
    selectedDept: 'Semua',
    selectedShift: {{ $shiftTypes[0]['id'] ?? 'null' }},
    searchQuery: '',
    selectAll: false,
    selectedEmployees: [],
    allEmployeeIds: {{ $roster->pluck('id')->toJson() }},

    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },

    toggleSelectAll() {
        this.selectAll = !this.selectAll;
        this.selectedEmployees = this.selectAll ? [...this.allEmployeeIds] : [];
    },

    async assignShift(employeeId, shiftTypeId, date) {
        try {
            const res = await fetch('{{ route('hr.shift.update-cell') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ employee_id: employeeId, shift_type_id: shiftTypeId, date: date }),
            });
            if (!res.ok) throw new Error('Gagal menyimpan');
            this.triggerToast('Shift berhasil diubah!');
            setTimeout(() => window.location.reload(), 600);
        } catch (e) {
            this.triggerToast('Gagal mengubah shift, coba lagi.', 'error');
        }
    }
}" class="space-y-6">

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- MASTER SHIFT CARDS HEADER ROW -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse ($shiftTypes as $t)
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
        @empty
            <div class="col-span-4 p-6 rounded-2xl border border-dashed border-black/10 text-center text-xs text-on-surface-variant/50">
                Belum ada jenis shift. Tambahkan dulu di Pengaturan &rarr; Jenis Shift.
            </div>
        @endforelse
    </div>

    <!-- MAIN ROSTER CALENDAR & TOOLBAR -->
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
        <!-- Toolbar Header -->
        <div class="p-6 border-b border-black/5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-lg font-bold text-on-surface">Kalender Roster Kerja Tim</h2>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Periode Aktif</span>
                    </div>
                    <p class="text-xs text-on-surface-variant/60 mt-0.5">
                        {{ $weekStart->translatedFormat('d') }} – {{ $weekEnd->translatedFormat('d F Y') }} · Mengatur rotasi shift mingguan &amp; toleransi absensi
                    </p>
                </div>

                {{-- Tombol utama dipisah dari filter, selalu di kanan & full-visible --}}
                <button type="button" @click="showBulkModal = true"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                               bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800
                               text-white font-semibold text-xs transition shadow-sm shadow-emerald-600/20
                               shrink-0 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">group_add</span>
                    Bulk Assign Shift
                </button>
            </div>

            {{-- Baris filter terpisah supaya tidak berebut tempat dengan tombol aksi utama --}}
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari karyawan / NIP..." class="pl-9 pr-3 py-2 text-xs border border-black/10 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary w-44">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[16px]">search</span>
                </div>

                <select x-model="selectedDept" class="text-xs border border-black/10 rounded-xl pl-3 pr-8 py-2 bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer font-medium">
                    <option value="Semua">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>

                <div class="flex items-center border border-black/10 rounded-xl p-0.5 bg-surface-variant/10">
                    <a href="{{ route('hr.shift.index', ['week_start' => $weekStart->copy()->subWeek()->toDateString()]) }}"
                       class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-white text-on-surface-variant/70 transition">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                    <span class="text-xs font-bold px-2 text-on-surface">{{ $weekStart->translatedFormat('d M') }} - {{ $weekEnd->translatedFormat('d M') }}</span>
                    <a href="{{ route('hr.shift.index', ['week_start' => $weekStart->copy()->addWeek()->toDateString()]) }}"
                       class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-white text-on-surface-variant/70 transition">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                </div>

                {{-- Lompat langsung ke bulan & tahun tertentu --}}
                <div class="relative">
                    <input type="month" value="{{ $weekStart->format('Y-m') }}"
                           onchange="if(this.value){ window.location.href = '{{ route('hr.shift.index') }}?week_start=' + this.value + '-01'; }"
                           class="text-xs font-bold border border-black/10 rounded-xl pl-9 pr-3 py-2 bg-white
                                  hover:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[16px] pointer-events-none">calendar_month</span>
                </div>

                <button type="button" onclick="window.location.href = '{{ route('hr.shift.index') }}'"
                        class="text-[11px] font-bold text-primary hover:underline px-1">
                    Minggu Ini
                </button>
            </div>
        </div>

        <!-- ROSTER TABLE -->
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
                    @forelse ($roster as $r)
                        <tr class="hover:bg-primary/5 transition" x-show="(selectedDept === 'Semua' || selectedDept === '{{ $r['dept'] }}') && (searchQuery === '' || '{{ strtolower($r['name']) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($r['nip']) }}'.includes(searchQuery.toLowerCase()))">
                            <td class="px-6 py-3.5">
                                <p class="font-bold text-on-surface text-xs leading-tight">{{ $r['name'] }}</p>
                                <p class="text-[11px] text-on-surface-variant/60 font-mono mt-0.5">ID: {{ $r['nip'] }}</p>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-on-surface text-xs">{{ $r['dept'] }}</p>
                                <p class="text-[11px] text-on-surface-variant/60">{{ $r['pos'] }}</p>
                            </td>
                            @foreach ($r['days'] as $i => $d)
                                <td class="px-2 py-3.5 text-center border-l border-black/5">
                                    <div x-data="{ open: false }" class="relative inline-block">
                                        <button type="button" @click="open = !open"
                                                class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold shadow-2xs transition hover:scale-105 cursor-pointer {{ $d ? ($mapBadge[$d] ?? 'bg-gray-100 text-gray-700 border border-gray-300') : 'w-7 h-7 text-[10px] text-on-surface-variant/30 border border-dashed border-black/10' }}"
                                                title="{{ $d ? ($mapName[$d] ?? $d) : 'Klik untuk atur shift' }}">
                                            {{ $d ?: '-' }}
                                        </button>

                                        <div x-show="open" @click.outside="open = false" x-cloak
                                             class="absolute z-30 mt-1 left-1/2 -translate-x-1/2 w-36 bg-white border border-black/10 rounded-xl shadow-xl p-1.5 space-y-0.5">
                                            @foreach ($shiftTypes as $st)
                                                <button type="button"
                                                        @click="open = false; assignShift({{ $r['id'] }}, {{ $st['id'] }}, '{{ $labels[$i]['iso'] }}')"
                                                        class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-[11px] font-semibold text-left hover:bg-surface-variant/20 transition">
                                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color:{{ $st['color'] }}"></span>
                                                    {{ $st['name'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($labels) }}" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Belum ada karyawan aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- TABLE FOOTER WITH LEGEND -->
        <div class="px-6 py-3.5 bg-surface-variant/15 border-t border-black/5 flex items-center justify-between text-xs text-on-surface-variant/70 flex-wrap gap-4">
            <div class="flex items-center gap-4 flex-wrap">
                @foreach ($shiftTypes as $t)
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded {{ $t['bg'] }}"></span> <strong>{{ $t['code'] }}</strong> = {{ $t['name'] }}</span>
                @endforeach
            </div>
            <div class="flex items-center gap-1.5 text-xs font-bold text-primary">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                Blueprint Validation: 1 Karyawan Max 1 Shift Aktif / Hari
            </div>
        </div>
    </div>

    <!-- LOWER SECTION: TUKAR SHIFT & GEOFENCING -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- CARD 1: PENGAJUAN TUKAR SHIFT -->
        <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-black/5 pb-4">
                <div>
                    <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">sync_alt</span>
                        Pengajuan Tukar Shift Antar Pegawai
                    </h2>
                    <p class="text-xs text-on-surface-variant/60 mt-0.5">Persetujuan 2 belah pihak & atasan (Supervisor/HR)</p>
                </div>
                @if ($pendingSpvCount > 0)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">{{ $pendingSpvCount }} Menunggu SPV</span>
                @endif
            </div>

            <div class="space-y-3.5">
                @forelse ($swapRequests as $req)
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
                            @if ($req['status_raw'] === 'pending_spv')
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Menunggu SPV</span>
                            @elseif ($req['status_raw'] === 'approved')
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">Disetujui</span>
                            @else
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">Ditolak</span>
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

                        @if ($req['status_raw'] === 'pending_spv')
                            <div class="pt-2 flex justify-end gap-2">
                                <form method="POST" action="{{ route('hr.shift.swap.reject', $req['db_id']) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-black/10 text-xs font-semibold text-on-surface-variant hover:bg-black/5 transition">
                                        Tolak
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('hr.shift.swap.approve', $req['db_id']) }}">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm">
                                        Setujui
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-on-surface-variant/50 text-center py-6">Belum ada pengajuan tukar shift.</p>
                @endforelse
            </div>
        </div>

        <!-- CARD 2: PENGATURAN RADIUS GEOFENCING OFFICE -->
        <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-5 flex flex-col justify-between"
             x-data="{ radiusMeter: {{ $company->geofence_radius_meters ?? 100 }}, toleranceMin: {{ $company->late_tolerance_minutes ?? 15 }} }">
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
                        <p class="font-bold text-on-surface text-sm">{{ $company->name ?? 'Kantor Pusat' }}</p>
                        <p class="text-on-surface-variant/60 font-mono mt-0.5">
                            Lat: {{ $company->office_latitude ?? '-' }} | Long: {{ $company->office_longitude ?? '-' }}
                        </p>
                        <p class="text-[11px] text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">security</span>
                            Anti-Mock Location Engine: Aktif (Mendeteksi Fake GPS)
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('hr.shift.geofencing.update') }}"
                      class="space-y-4"
                      x-data="{
                          lat: '{{ $company->office_latitude ?? '' }}',
                          lng: '{{ $company->office_longitude ?? '' }}',
                          detecting: false,
                          detectLocation() {
                              if (!navigator.geolocation) { triggerToast('Browser tidak mendukung GPS', 'error'); return; }
                              this.detecting = true;
                              navigator.geolocation.getCurrentPosition(
                                  (pos) => {
                                      this.lat = pos.coords.latitude.toFixed(7);
                                      this.lng = pos.coords.longitude.toFixed(7);
                                      this.detecting = false;
                                  },
                                  () => { this.detecting = false; triggerToast('Gagal mengambil lokasi. Izinkan akses GPS di browser.', 'error'); },
                                  { enableHighAccuracy: true, timeout: 8000 }
                              );
                          }
                      }">
                    @csrf

                    <!-- Koordinat Kantor (manual / deteksi otomatis) -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-on-surface-variant/80 uppercase tracking-wide">Titik Koordinat Kantor</label>
                            <button type="button" @click="detectLocation()" :disabled="detecting"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-primary hover:underline disabled:opacity-50">
                                <span class="material-symbols-outlined text-[15px]" :class="detecting && 'animate-spin'">my_location</span>
                                <span x-text="detecting ? 'Mendeteksi...' : 'Deteksi Lokasi Saya'"></span>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="office_latitude" x-model="lat" placeholder="Latitude, mis. -6.2088000"
                                   class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs font-mono focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <input type="text" name="office_longitude" x-model="lng" placeholder="Longitude, mis. 106.8456000"
                                   class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs font-mono focus:ring-2 focus:ring-primary/20 focus:outline-none">
                        </div>
                        <p class="text-[10px] text-on-surface-variant/50">
                            "Deteksi Lokasi Saya" memakai GPS perangkat kamu saat ini — pastikan kamu sedang berada tepat di lokasi kantor saat menekannya. Atau isi manual dari Google Maps.
                        </p>
                    </div>

                    <!-- Radius Slider -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-on-surface-variant/80 uppercase tracking-wide">Radius Absensi Kantor</label>
                            <span class="font-mono font-extrabold text-primary text-base" x-text="radiusMeter + ' meter'">100 meter</span>
                        </div>
                        <input type="range" name="geofence_radius_meters" min="25" max="500" step="25" x-model="radiusMeter" class="w-full accent-primary h-2 bg-black/10 rounded-lg cursor-pointer">
                        <div class="flex justify-between text-[10px] text-on-surface-variant/60 font-mono">
                            <span>25m (Sangat Ketat)</span>
                            <span>100m (Rekomendasi)</span>
                            <span>500m (Fleksibel)</span>
                        </div>
                    </div>

                    <!-- Tolerance & Cross-Day Rules -->
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-black/5">
                        <div class="p-3 rounded-xl border border-black/5 bg-surface-variant/10">
                            <label class="text-[10px] font-bold text-on-surface-variant/60 uppercase block mb-1.5">Toleransi Keterlambatan</label>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-amber-600">timer</span>
                                <input type="number" name="late_tolerance_minutes" x-model="toleranceMin" min="0" max="120"
                                       class="w-16 font-mono font-bold text-sm text-on-surface border border-black/10 rounded-lg px-2 py-1 focus:ring-2 focus:ring-primary/20 focus:outline-none">
                                <span class="text-xs text-on-surface-variant/60">menit</span>
                            </div>
                            <p class="text-[10px] text-on-surface-variant/50 mt-1">
                                Clock in &le; <span x-text="toleranceMin"></span> menit dari jam standar terhitung Tepat Waktu
                            </p>
                        </div>

                        <div class="p-3 rounded-xl border border-black/5 bg-surface-variant/10">
                            <span class="text-[10px] font-bold text-on-surface-variant/60 uppercase">Logika Cross-Day (Malam)</span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="material-symbols-outlined text-[18px] text-purple-600">nightlight</span>
                                <span class="font-mono font-bold text-sm text-on-surface">H+1 Clock-out</span>
                            </div>
                            <p class="text-[10px] text-on-surface-variant/50 mt-0.5">Diatur otomatis via kolom is_cross_day di jenis shift</p>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs transition shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan Pengaturan Geofencing
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL BULK ASSIGN SHIFT -->
    <div x-show="showBulkModal" x-cloak class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" @click.self="showBulkModal = false">
        <form method="POST" action="{{ route('hr.shift.bulk-assign') }}"
              class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto">
            @csrf
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
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

            <div class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide block mb-1.5">1. Pilih Jenis Shift</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach ($shiftTypes as $st)
                            <label class="border rounded-xl p-2.5 flex flex-col items-center gap-1 cursor-pointer transition text-center"
                                   :class="selectedShift == {{ $st['id'] }} ? 'border-emerald-600 bg-emerald-50 text-emerald-700 font-bold' : 'border-black/10 hover:border-black/20'">
                                <input type="radio" name="shift_type_id" value="{{ $st['id'] }}" x-model="selectedShift" class="hidden">
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
                        <input type="date" name="date_start" value="{{ $weekStart->toDateString() }}" required
                               class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide block mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_end" value="{{ $weekEnd->toDateString() }}" required
                               class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-bold text-on-surface-variant/80 uppercase tracking-wide">3. Pilih Karyawan Penerima</label>
                        <button type="button" @click="toggleSelectAll()" class="text-emerald-700 font-bold hover:underline"
                                x-text="selectAll ? 'Batal Pilih Semua' : `Pilih Semua (${allEmployeeIds.length} Karyawan)`">Pilih Semua</button>
                    </div>

                    <div class="max-h-36 overflow-y-auto border border-black/10 rounded-xl p-2 divide-y divide-black/5 bg-surface-variant/10">
                        @foreach ($roster as $r)
                            <label class="flex items-center justify-between p-2 hover:bg-white rounded-lg cursor-pointer transition">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $r['id'] }}" x-model="selectedEmployees" class="rounded border-black/20 text-emerald-600 focus:ring-emerald-500/20">
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

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showBulkModal = false" class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Terapkan Jadwal Shift
                </button>
            </div>
        </form>
    </div>

    <!-- TOAST NOTIFICATION -->
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