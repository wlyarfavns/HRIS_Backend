@extends('layouts.hr')

@section('title', 'Shift & Roster Kerja')
@section('page-title', 'Shift & Roster Kerja')
@section('page-desc', 'Manajemen kalender roster, bulk assign shift, pengajuan tukar shift, dan geofencing office.')

@section('content')
<div id="shift-wrapper">
    @php
        $alpineItems = collect($roster)->map(function($r) {
            return [
                'name' => strtolower($r['name'] ?? ''),
                'nip' => strtolower($r['nip'] ?? ''),
                'dept' => strtolower($r['dept'] ?? '')
            ];
        })->toJson();
    @endphp
    <div x-data="{
        showBulkModal: false,
        selectedDept: 'Semua',
        selectedShift: {{ $shiftTypes[0]['id'] ?? 'null' }},
        searchQuery: '',
        items: {{ $alpineItems }},
        get hasVisibleRows() {
            return this.items.some(i => 
                (this.selectedDept === 'Semua' || i.dept === this.selectedDept.toLowerCase()) &&
                (this.searchQuery === '' || i.name.includes(this.searchQuery.toLowerCase()) || i.nip.includes(this.searchQuery.toLowerCase()))
            );
        },
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
                class="p-4 rounded-md bg-gray-50 border border-gray-200 text-emerald-800 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($shiftTypes as $t)
                <div
                    class="bg-white rounded-md p-6 border border-gray-200 shadow-sm transition space-y-3 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm border border-black/10"
                                style="background-color:{{ $t['color'] }}"></span>
                            <h2 class="text-sm font-medium text-gray-800">{{ $t['name'] }}</h2>
                        </div>
                        <span
                            class="text-[10px] font-medium px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 uppercase tracking-wider">{{ $t['count'] }}
                            Karyawan</span>
                    </div>
                    <p class="text-sm  font-medium text-[#0B3D2E]">{{ $t['time'] }}</p>
                    <p class="text-[11px] text-gray-500 line-clamp-1 leading-relaxed">{{ $t['desc'] }}</p>
                </div>
            @empty
                <div
                    class="col-span-4 p-8 rounded-md border border-dashed border-gray-300 text-center text-sm text-gray-500 bg-gray-50">
                    Belum ada jenis shift. Tambahkan dulu di Pengaturan  Jenis Shift.
                </div>
            @endforelse
        </div>


        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">

            <div class="p-6 border-b border-gray-100 space-y-4 bg-gray-50/50">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-medium text-gray-800">Kalender Roster Kerja Tim</h2>
                            <span
                                class="text-[10px] font-medium px-2.5 py-1 rounded-full bg-gray-50 text-[#0B3D2E] border border-gray-200 uppercase tracking-wider">Periode
                                Aktif</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $weekStart->translatedFormat('d') }} – {{ $weekEnd->translatedFormat('d F Y') }} · Mengatur
                            rotasi shift mingguan &amp; toleransi absensi
                        </p>
                    </div>


                    <button type="button" @click="showBulkModal = true" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-md
                                   bg-[#0B3D2E] hover:bg-[#043927] text-white font-medium text-sm transition shadow-sm
                                   shrink-0 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">group_add</span>
                        Bulk Assign Shift
                    </button>
                </div>


                <form method="GET" action="{{ route('hr.shift.index') }}" class="flex items-center gap-4 flex-wrap"
                    @submit.prevent="
                        let form = $event.target;
                        let url = new URL(form.action);
                        let formData = new FormData(form);
                        for(let [k,v] of formData.entries()) url.searchParams.set(k,v);
                        
                        // Add opacity to show loading state
                        document.getElementById('shift-wrapper').style.opacity = '0.5';
                        document.getElementById('shift-wrapper').style.pointerEvents = 'none';

                        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(res => res.text())
                            .then(html => {
                                let doc = new DOMParser().parseFromString(html, 'text/html');
                                let newWrapper = doc.getElementById('shift-wrapper');
                                if (newWrapper) {
                                    document.getElementById('shift-wrapper').outerHTML = newWrapper.outerHTML;
                                } else {
                                    window.location.href = url.toString();
                                }
                            }).catch(() => {
                                window.location.href = url.toString();
                            });
                    ">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[18px]">search</span>
                        <input type="text" x-model="searchQuery" placeholder="Cari karyawan / NIP..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] w-64 bg-white transition">
                    </div>

                    <select x-model="selectedDept"
                        class="text-sm border border-gray-200 rounded-md pl-4 pr-10 py-2 bg-white focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] cursor-pointer transition">
                        <option value="Semua">Semua Departemen</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>

                    <div class="h-6 w-px bg-gray-200 mx-1"></div>

                    <div class="flex items-center border border-gray-200 rounded-md p-1 bg-white shadow-sm">
                        <button type="button" onclick="const dateInput = this.nextElementSibling.querySelector('input'); dateInput.value = '{{ $weekStart->copy()->subWeek()->toDateString() }}'; dateInput.dispatchEvent(new Event('change'));"
                            class="w-7 h-7 rounded flex items-center justify-center hover:bg-gray-100 text-gray-500 transition cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        
                        <div class="relative flex items-center px-3 group">
                            <span class="material-symbols-outlined text-[16px] text-[#0B3D2E] mr-2">calendar_month</span>
                            <input type="date" id="week_start_input" name="week_start" value="{{ $weekStart->format('Y-m-d') }}" onchange="this.form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}))"
                                class="text-sm font-semibold text-gray-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer text-center" title="Pilih Tanggal Mulai" style="width: 135px;">
                        </div>

                        <button type="button" onclick="const dateInput = this.previousElementSibling.querySelector('input'); dateInput.value = '{{ $weekStart->copy()->addWeek()->toDateString() }}'; dateInput.dispatchEvent(new Event('change'));"
                            class="w-7 h-7 rounded flex items-center justify-center hover:bg-gray-100 text-gray-500 transition cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>

                    <button type="button" onclick="const dateInput = document.getElementById('week_start_input'); dateInput.value = '{{ now()->startOfWeek()->toDateString() }}'; dateInput.dispatchEvent(new Event('change'));"
                        class="text-xs font-semibold text-gray-600 hover:text-[#0B3D2E] transition px-3 py-2 flex items-center gap-1.5 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-[14px]">today</span>
                        Minggu Ini
                    </button>
                </form>
            </div>


            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-widest">
                            <th class="px-6 py-4 min-w-[220px]">Karyawan</th>
                            <th class="px-6 py-4 min-w-[150px]">Departemen / Jabatan</th>
                            @foreach ($labels as $l)
                                <th class="px-3 py-4 text-center min-w-[85px] border-l border-gray-100">
                                    <span class="block text-xs font-medium text-gray-700">{{ $l['day'] }}</span>
                                    <span
                                        class="block text-[10px] font-medium text-gray-400 mt-0.5">{{ $l['date'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($roster as $r)
                            <tr class="hover:bg-gray-50 transition"
                                x-show="(selectedDept === 'Semua' || selectedDept === '{{ $r['dept'] }}') && (searchQuery === '' || '{{ strtolower($r['name']) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($r['nip']) }}'.includes(searchQuery.toLowerCase()))">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800 text-sm leading-tight">{{ $r['name'] }}</p>
                                    <p class="text-[11px] text-gray-500  mt-0.5">ID: {{ $r['nip'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800 text-sm">{{ $r['dept'] }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $r['pos'] }}</p>
                                </td>
                                @foreach ($r['days'] as $i => $d)
                                    <td class="px-3 py-4 text-center border-l border-gray-100">
                                        <div x-data="{ open: false }" class="relative inline-block">
                                            <button type="button" @click="open = !open"
                                                class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm transition hover:scale-105 cursor-pointer {{ $d ? ($mapBadge[$d] ?? 'bg-gray-100 text-gray-700 border border-gray-200') : 'w-8 h-8 text-[11px] text-gray-300 border border-dashed border-gray-300 bg-white' }}"
                                                title="{{ $d ? ($mapName[$d] ?? $d) : 'Klik untuk atur shift' }}">
                                                {{ $d ?: '-' }}
                                            </button>

                                            <div x-show="open" @click.outside="open = false" x-cloak
                                                class="absolute z-30 mt-2 left-1/2 -translate-x-1/2 w-40 bg-white border border-gray-100 rounded-md shadow-sm p-2 space-y-1">
                                                @foreach ($shiftTypes as $st)
                                                    <button type="button"
                                                        @click="open = false; assignShift({{ $r['id'] }}, {{ $st['id'] }}, '{{ $labels[$i]['iso'] }}')"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-left hover:bg-gray-50 text-gray-700 transition">
                                                        <span class="w-3 h-3 rounded-full shrink-0 border border-black/10"
                                                            style="background-color:{{ $st['color'] }}"></span>
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
                                <td colspan="{{ 2 + count($labels) }}"
                                    class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada karyawan aktif.
                                </td>
                            </tr>
                        @endforelse
                        
                        @if(count($roster) > 0)
                            <tr x-show="!hasVisibleRows" style="display: none;" x-transition>
                                <td colspan="{{ 2 + count($labels) }}" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <template x-if="searchQuery">
                                        <span>Tidak ada karyawan yang sesuai dengan pencarian "<span x-text="searchQuery" class="font-medium text-gray-700"></span>" di departemen ini.</span>
                                    </template>
                                    <template x-if="!searchQuery">
                                        <span>Tidak ada data roster untuk departemen ini.</span>
                                    </template>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-6 pb-6">
                {{ $employees->links() }}
            </div>


            <div
                class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 flex-wrap gap-4">
                <div class="flex items-center gap-5 flex-wrap">
                    @foreach ($shiftTypes as $t)
                        <span class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-sm border border-black/10 {{ $t['bg'] }}"></span>
                            <span class="font-medium text-gray-700">{{ $t['code'] }}</span> = {{ $t['name'] }}</span>
                    @endforeach
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-medium text-gray-700 uppercase tracking-wide">
                    <span class="material-symbols-outlined text-[16px]">verified_user</span>
                    Blueprint Validation: 1 Karyawan Max 1 Shift Aktif / Hari
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


            <div class="bg-white rounded-md border border-gray-200 p-8 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 flex items-center gap-2">
                            Pengajuan Tukar Shift Antar Pegawai
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">Menunggu persetujuan HR</p>
                    </div>
                    @if ($pendingHrCount > 0)
                        <span
                            class="text-[10px] font-medium px-3 py-1.5 rounded-full bg-gray-50 text-gray-700 uppercase tracking-wider">{{ $pendingHrCount }}
                            Menunggu HR</span>
                    @endif
                </div>

                <div class="space-y-4">
                    @forelse ($swapRequests as $req)
                        <div class="p-5 rounded-md border border-gray-200 bg-gray-50 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <img src="https://i.pravatar.cc/24?img={{ $req['from_avatar'] }}"
                                            class="w-7 h-7 rounded-full border border-gray-200" alt="">
                                        <span class="text-sm font-medium text-gray-800">{{ $req['from'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <img src="https://i.pravatar.cc/24?img={{ $req['to_avatar'] }}"
                                            class="w-7 h-7 rounded-full border border-gray-200" alt="">
                                        <span class="text-sm font-medium text-gray-800">{{ $req['to'] }}</span>
                                    </div>
                                </div>
                                @if ($req['status_raw'] === 'pending_spv')
                                    <span
                                        class="text-[10px] font-medium px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 uppercase tracking-wider">Menunggu
                                        SPV</span>
                                @elseif ($req['status_raw'] === 'pending_hr')
                                    <span
                                        class="text-[10px] font-medium px-2.5 py-1 rounded-full bg-gray-50 text-gray-700 uppercase tracking-wider">Menunggu
                                        HR</span>
                                @elseif ($req['status_raw'] === 'approved')
                                    <span
                                        class="text-[10px] font-medium px-2.5 py-1 rounded-full bg-gray-50 text-[#0B3D2E] uppercase tracking-wider">Disetujui</span>
                                @else
                                    <span
                                        class="text-[10px] font-medium px-2.5 py-1 rounded-full bg-gray-50 text-gray-700 uppercase tracking-wider">Ditolak</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="p-3 rounded-md bg-white border border-gray-200 shadow-sm">
                                    <span
                                        class="text-[10px] text-gray-400 font-medium uppercase tracking-wide block mb-1">Shift
                                        Semula</span>
                                    <span class="font-medium text-gray-800">{{ $req['from_shift'] }}</span>
                                </div>
                                <div class="p-3 rounded-md bg-white border border-gray-200 shadow-sm">
                                    <span
                                        class="text-[10px] text-gray-400 font-medium uppercase tracking-wide block mb-1">Shift
                                        Pengganti</span>
                                    <span class="font-medium text-gray-800">{{ $req['to_shift'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-2 border-t border-gray-200">
                                <p class="text-gray-600 italic leading-relaxed">"{{ $req['reason'] }}"</p>
                                <span class="text-[10px]  text-gray-400">{{ $req['created'] }}</span>
                            </div>

                            @if ($req['status_raw'] === 'pending_hr')
                                <div class="pt-4 mt-2 border-t border-gray-200 flex justify-end gap-3">
                                    <form method="POST" action="{{ route('hr.shift.swap.reject', $req['db_id']) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-4 py-2 rounded-md border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                                            Tolak
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('hr.shift.swap.approve', $req['db_id']) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-5 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium transition shadow-sm">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Belum ada pengajuan tukar shift.</p>
                    @endforelse
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-200 p-8 shadow-sm space-y-6 flex flex-col justify-between"
                x-data="{ radiusMeter: {{ $company->geofence_radius_meters ?? 100 }}, toleranceMin: {{ $company->late_tolerance_minutes ?? 15 }} }">
                <div class="space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-medium text-gray-800 flex items-center gap-2">
                                Pengaturan Radius Geofencing Office
                            </h2>
                            <p class="text-xs text-gray-500 mt-1">Validasi absensi via GPS Haversine &
                                toleransi jam hadir</p>
                        </div>
                        <span
                            class="text-[10px] font-medium px-3 py-1.5 rounded-full bg-gray-50 text-[#0B3D2E] border border-gray-200 uppercase tracking-wider">GPS
                            Geotag Aktif</span>
                    </div>


                    <div class="p-5 rounded-md bg-gray-50 border border-gray-200 flex items-center gap-4">
                        <div
                            class="w-16 h-16 rounded-md bg-white border border-gray-200 flex items-center justify-center shrink-0 text-gray-400">
                            </div>
                        <div class="min-w-0 flex-1 text-sm">
                            <p class="font-medium text-gray-800">{{ $company->name ?? 'Kantor Pusat' }}</p>
                            <p class="text-gray-500  mt-0.5 text-xs">
                                Lat: {{ $company->office_latitude ?? '-' }} | Long: {{ $company->office_longitude ?? '-' }}
                            </p>
                            <p class="text-[11px] text-[#0B3D2E] font-medium mt-2 flex items-center gap-1.5 uppercase tracking-wide">
                                <span class="material-symbols-outlined text-[16px]">security</span>
                                Anti-Mock Location Engine: Aktif (Mendeteksi Fake GPS)
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('hr.shift.geofencing.update') }}" class="space-y-5" x-data="{
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


                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Titik
                                    Koordinat Kantor</label>
                                <button type="button" @click="detectLocation()" :disabled="detecting"
                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-700 hover:text-gray-700 hover:underline disabled:opacity-50 transition">
                                    <span class="material-symbols-outlined text-[14px]"
                                        :class="detecting && 'animate-spin'">my_location</span>
                                    <span x-text="detecting ? 'Mendeteksi...' : 'Deteksi Lokasi Saya'"></span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="office_latitude" x-model="lat"
                                    placeholder="Latitude, mis. -6.2088000"
                                    class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm  focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition focus:outline-none">
                                <input type="text" name="office_longitude" x-model="lng"
                                    placeholder="Longitude, mis. 106.8456000"
                                    class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm  focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition focus:outline-none">
                            </div>
                            <p class="text-[10px] text-gray-400 leading-relaxed">
                                "Deteksi Lokasi Saya" memakai GPS perangkat kamu saat ini — pastikan kamu sedang berada
                                tepat di lokasi kantor saat menekannya. Atau isi manual dari Google Maps.
                            </p>
                        </div>


                        <div class="space-y-4 pt-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Radius
                                    Absensi Kantor</label>
                                <span class=" font-semibold text-[#0B3D2E] text-lg bg-gray-50 px-2 py-1 rounded-lg"
                                    x-text="radiusMeter + ' meter'">100 meter</span>
                            </div>
                            <input type="range" name="geofence_radius_meters" min="25" max="500" step="25"
                                x-model="radiusMeter"
                                class="w-full accent-[#0B3D2E] h-2 bg-gray-200 rounded-lg cursor-pointer">
                            <div class="flex justify-between text-[10px] text-gray-400  font-medium">
                                <span>25m (Sangat Ketat)</span>
                                <span>100m (Rekomendasi)</span>
                                <span>500m (Fleksibel)</span>
                            </div>
                        </div>


                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                            <div class="p-4 rounded-md border border-gray-200 bg-gray-50">
                                <label
                                    class="text-[10px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Toleransi
                                    Keterlambatan</label>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-gray-700">timer</span>
                                    <input type="number" name="late_tolerance_minutes" x-model="toleranceMin" min="0"
                                        max="120"
                                        class="w-20  font-medium text-base text-gray-800 border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                                    <span class="text-xs font-medium text-gray-500">menit</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2 leading-relaxed">
                                    Clock in &le; <span x-text="toleranceMin"></span> menit dari jam standar terhitung Tepat
                                    Waktu
                                </p>
                            </div>

                            <div class="p-4 rounded-md border border-gray-200 bg-gray-50 flex flex-col justify-center">
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Logika Cross-Day
                                    (Malam)</span>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="material-symbols-outlined text-[20px] text-gray-700">nightlight</span>
                                    <span class=" font-medium text-base text-gray-800">H+1 Clock-out</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2 leading-relaxed">Diatur otomatis via kolom
                                    is_cross_day di jenis shift</p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white font-medium text-sm transition shadow-sm flex items-center justify-center gap-2 mt-2">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan Pengaturan Geofencing
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <div x-show="showBulkModal" x-cloak class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
            @click.self="showBulkModal = false">
            <form method="POST" action="{{ route('hr.shift.bulk-assign') }}"
                class="bg-white rounded-md max-w-xl w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto border border-gray-100">
                @csrf
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E] border border-gray-200">
                            </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">Bulk Assign Shift ke Tim</h3>
                            <p class="text-xs text-gray-500 mt-1">Terapkan jadwal shift serentak ke banyak karyawan
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showBulkModal = false"
                        class="text-gray-400 hover:text-gray-800 transition w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="space-y-6 text-sm">
                    <div>
                        <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">1. Pilih
                            Jenis Shift</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($shiftTypes as $st)
                                <label
                                    class="border rounded-md p-3 flex flex-col items-center gap-1.5 cursor-pointer transition text-center bg-gray-50"
                                    :class="selectedShift == {{ $st['id'] }} ? 'border-gray-200 bg-gray-50 text-[#0B3D2E] font-medium ring-1 ring-emerald-500' : 'border-gray-200 hover:border-gray-300 text-gray-700 hover:bg-gray-100'">
                                    <input type="radio" name="shift_type_id" value="{{ $st['id'] }}" x-model="selectedShift"
                                        class="hidden">
                                    <span class="w-3.5 h-3.5 rounded-full border border-black/10" style="background-color:{{ $st['color'] }}"></span>
                                    <span class="text-xs leading-tight font-semibold mt-1">{{ $st['name'] }}</span>
                                    <span class="text-[10px]  font-medium opacity-70">{{ $st['time'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">2. Dari
                                Tanggal</label>
                            <input type="date" name="date_start" value="{{ $weekStart->toDateString() }}" required
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition focus:outline-none">
                        </div>
                        <div>
                            <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">Sampai
                                Tanggal</label>
                            <input type="date" name="date_end" value="{{ $weekEnd->toDateString() }}" required
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px]">3. Pilih Karyawan
                                Penerima</label>
                            <button type="button" @click="toggleSelectAll()"
                                class="text-gray-700 font-medium hover:text-gray-700 text-xs transition"
                                x-text="selectAll ? 'Batal Pilih Semua' : `Pilih Semua (${allEmployeeIds.length} Karyawan)`">Pilih
                                Semua</button>
                        </div>

                        <div
                            class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-2 divide-y divide-gray-100 bg-gray-50 custom-scrollbar">
                            @foreach ($roster as $r)
                                <label
                                    class="flex items-center justify-between p-3 hover:bg-white rounded-lg cursor-pointer transition">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="employee_ids[]" value="{{ $r['id'] }}"
                                            x-model="selectedEmployees"
                                            class="w-4 h-4 rounded border-gray-300 text-[#0B3D2E] focus:ring-[#0B3D2E]/20 transition">
                                        <span class="font-medium text-gray-700 text-sm">{{ $r['name'] }}</span>
                                    </div>
                                    <span class="text-[11px]  text-gray-500 bg-gray-100 px-2 py-1 rounded-md">{{ $r['dept'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div
                        class="p-4 rounded-md bg-gray-50 border border-gray-200 text-gray-700 flex items-start gap-3">
                        <span class="material-symbols-outlined text-[20px] text-gray-700 shrink-0 mt-0.5">info</span>
                        <p class="text-xs leading-relaxed font-medium">
                            <span class="font-medium text-gray-700">Overlap Shift Guard:</span> Sistem akan menimpa jadwal sebelumnya pada
                            rentang tanggal yang dipilih. Satu karyawan hanya akan memiliki 1 jadwal shift aktif per hari.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showBulkModal = false"
                        class="px-5 py-2.5 rounded-md border border-gray-200 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium shadow-sm flex items-center gap-2 transition cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Terapkan Jadwal Shift
                    </button>
                </div>
            </form>
        </div>


        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-md shadow-sm text-white font-medium text-sm border border-gray-200/30 "
            :class="{
                 'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
                 'bg-gray-50 border-gray-200/30 text-white': toast.type === 'error'
             }" style="display: none;">
            <span class="material-symbols-outlined text-[20px]"
                :class="toast.type === 'error' ? 'text-white' : 'text-emerald-100'"
                x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
            <span x-text="toast.message" class="text-sm font-medium"></span>
        </div>

    </div>
</div>
@endsection
