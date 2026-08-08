@extends('layouts.hr')

@section('title', 'Presensi & Live Attendance')
@section('page-title', 'Presensi & Live Attendance')
@section('page-desc', 'Pencatatan jam masuk & keluar via GPS Haversine + live selfie capture.')

@php
    $stats = [
        ['label' => 'Tepat Waktu', 'value' => '128', 'icon' => 'check_circle', 'color' => 'text-primary', 'note' => '91,4% kehadiran'],
        ['label' => 'Terlambat', 'value' => '14', 'icon' => 'schedule', 'color' => 'text-amber-700', 'note' => '10% dari presensi'],
        ['label' => 'Izin / Sakit', 'value' => '6', 'icon' => 'medical_information', 'color' => 'text-purple-700', 'note' => 'Ada surat dokter'],
        ['label' => 'Tidak Hadir', 'value' => '2', 'icon' => 'person_off', 'color' => 'text-error', 'note' => 'Alpha / tanpa kabar'],
    ];

    $logs = [
        [
            'nip' => 'EMP-00231', 'name' => 'Michael Scott', 'avatar' => 14,
            'in' => '07:58', 'out' => '17:05', 'loc' => 'Kantor Pusat, Jakarta Selatan',
            'status' => 'Tepat Waktu', 'late_minutes' => null, 'effective_hours' => '9j 07m',
            'distance' => '12m (Valid)', 'mock_gps' => false, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026',
        ],
        [
            'nip' => 'EMP-00567', 'name' => 'Pam Beesly', 'avatar' => 47,
            'in' => '08:14', 'out' => '17:02', 'loc' => 'Kantor Pusat, Jakarta Selatan',
            'status' => 'Terlambat', 'late_minutes' => 14, 'effective_hours' => '8j 48m',
            'distance' => '28m (Valid)', 'mock_gps' => false, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026',
        ],
        [
            'nip' => 'EMP-00812', 'name' => 'Jim Halpert', 'avatar' => 12,
            'in' => '07:50', 'out' => '-', 'loc' => 'Kantor Pusat, Jakarta Selatan',
            'status' => 'Sedang Bekerja', 'late_minutes' => null, 'effective_hours' => 'Sedang berjalan',
            'distance' => '8m (Valid)', 'mock_gps' => false, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026',
        ],
        [
            'nip' => 'EMP-00933', 'name' => 'Dwight Schrute', 'avatar' => 51,
            'in' => '-', 'out' => '-', 'loc' => '-',
            'status' => 'Izin / Sakit', 'late_minutes' => null, 'effective_hours' => '-',
            'distance' => '-', 'mock_gps' => false, 'note' => 'Surat dokter terlampir (>1 hari)', 'attachment' => 'surat_dokter_dwight.pdf', 'date' => '05 Agustus 2026',
        ],
        [
            'nip' => 'EMP-01044', 'name' => 'Angela Martin', 'avatar' => 33,
            'in' => '07:55', 'out' => '17:10', 'loc' => 'Kantor Pusat, Jakarta Selatan',
            'status' => 'Tepat Waktu', 'late_minutes' => null, 'effective_hours' => '9j 15m',
            'distance' => '15m (Valid)', 'mock_gps' => false, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026',
        ],
    ];

    $badge = [
        'Tepat Waktu' => 'bg-primary/10 text-primary',
        'Terlambat' => 'bg-amber-500/10 text-amber-700',
        'Sedang Bekerja' => 'bg-blue-500/10 text-blue-700',
        'Izin / Sakit' => 'bg-purple-500/10 text-purple-700',
        'Tidak Hadir' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')
<div x-data="{
    activePreview: null,
    showClockWidget: false,
    clockInTime: '08:00',
    currentSeconds: new Date().toLocaleTimeString('id-ID'),
    clockedIn: true,
    gpsDistance: '14 meter',
    haversineStatus: 'Valid (Dalam Radius 100m)',
    mockGpsDetected: false
}">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] {{ $s['color'] }}">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
                <p class="text-[11px] text-on-surface-variant/40 mt-1 font-mono-data">{{ $s['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- LOG TABLE --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Log Presensi &amp; Live Attendance Hari Ini</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($logs) }} karyawan tercatat · klik foto selfie untuk melihat geotag lokasi & waktu</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" placeholder="Cari nama karyawan..."
                           class="w-52 pl-9 pr-3 py-2 bg-surface-container rounded-lg text-xs border border-transparent
                                  hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition">
                </div>
                <input type="date" value="2026-08-05"
                       class="text-xs font-bold border border-black/10 rounded-lg px-3 py-2 bg-white
                              hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                <button class="border border-black/10 hover:bg-surface-container px-3 py-2 rounded-lg text-xs font-bold text-on-surface flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">download</span> Export Rekap
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Bukti Selfie</th>
                        <th class="px-4 py-3.5">Jam Masuk / Keluar</th>
                        <th class="px-4 py-3.5">Total Jam Kerja Efektif</th>
                        <th class="px-4 py-3.5">Jarak GPS (Haversine)</th>
                        <th class="px-4 py-3.5">Status Presensi</th>
                        <th class="px-6 py-3.5">Keterangan / Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($logs as $l)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img={{ $l['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="{{ $l['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $l['name'] }}</p>
                                        <p class="text-[10px] text-on-surface-variant/50 line-clamp-1">{{ $l['loc'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($l['in'] !== '-')
                                    <button type="button" @click="activePreview = {{ $loop->index }}"
                                            class="block w-9 h-9 rounded-lg overflow-hidden ring-2 ring-transparent hover:ring-primary/40 shadow-sm transition"
                                            title="Klik untuk pratinjau watermark geotag">
                                        <img src="https://i.pravatar.cc/64?img={{ $l['avatar'] + 1 }}" class="w-full h-full object-cover" alt="selfie {{ $l['name'] }}">
                                    </button>
                                @else
                                    <span class="text-on-surface-variant/30 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface">
                                <span class="font-bold">{{ $l['in'] }}</span> <span class="text-on-surface-variant/30">–</span> <span class="font-bold">{{ $l['out'] }}</span>
                            </td>
                            <td class="px-4 py-3.5 font-mono-data text-xs font-semibold text-primary">
                                {{ $l['effective_hours'] }}
                            </td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface-variant/70">
                                @if ($l['distance'] !== '-')
                                    <span class="flex items-center gap-1 text-[11px] text-primary font-bold">
                                        <span class="material-symbols-outlined text-[14px]">near_me</span>
                                        {{ $l['distance'] }}
                                    </span>
                                @else
                                    <span class="text-on-surface-variant/30">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] }}">{{ $l['status'] }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                @if ($l['attachment'])
                                    <button type="button" title="{{ $l['note'] }}"
                                            class="text-[11px] font-bold text-primary flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary/10 hover:bg-primary/20 transition">
                                        <span class="material-symbols-outlined text-[15px]">description</span>
                                        Surat Dokter
                                    </button>
                                @elseif ($l['note'])
                                    <span class="text-xs text-on-surface-variant/60">{{ $l['note'] }}</span>
                                @else
                                    <span class="text-on-surface-variant/30 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PREVIEW SELFIE + GEOTAG WATERMARK --}}
    @foreach ($logs as $l)
        @if ($l['in'] !== '-')
            <div x-show="activePreview === {{ $loop->index }}" x-cloak
                 class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
                 @click.self="activePreview = null">
                <div class="bg-white rounded-2xl overflow-hidden max-w-sm w-full shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                    <div class="relative">
                        <img src="https://i.pravatar.cc/480?img={{ $l['avatar'] + 1 }}" class="w-full h-80 object-cover" alt="selfie {{ $l['name'] }}">
                        <button type="button" @click="activePreview = null"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center transition">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>

                        {{-- WATERMARK GEOTAG & HAIVERSINE LOG --}}
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent px-4 pt-10 pb-3.5 text-white">
                            <p class="text-sm font-bold flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-brand-gold">location_on</span>
                                {{ $l['loc'] }}
                            </p>
                            <div class="flex items-center justify-between text-[11px] text-white/80 mt-1 font-mono-data">
                                <span>{{ $l['date'] }} · {{ $l['in'] }} WIB</span>
                                <span class="text-brand-gold font-bold">GPS: -6.2088, 106.8456</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-white/70">
                                <span class="px-1.5 py-0.5 rounded bg-white/20">Haversine: {{ $l['distance'] }}</span>
                                <span class="px-1.5 py-0.5 rounded bg-primary/80 text-white">Anti-Mock GPS: Lolos</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between bg-surface-container/40">
                        <div class="flex items-center gap-2.5">
                            <img src="https://i.pravatar.cc/28?img={{ $l['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                            <p class="text-xs font-bold text-on-surface">{{ $l['name'] }}</p>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] }}">{{ $l['status'] }}</span>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- MODAL SIMULASI CLOCK-IN / OUT VIA KAMERA & GPS --}}
    <div x-show="showClockWidget" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showClockWidget = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">photo_camera</span>
                    <h3 class="text-base font-bold text-on-surface">Simulasi Live Attendance</h3>
                </div>
                <button type="button" @click="showClockWidget = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- LIVE CAMERA SIMULATION BOX --}}
            <div class="relative rounded-2xl overflow-hidden bg-black/90 aspect-video flex flex-col items-center justify-center text-white text-center p-4">
                <div class="w-20 h-20 rounded-full border-2 border-dashed border-white/60 flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-[36px] text-white/80">face</span>
                </div>
                <p class="text-xs font-bold">Posisikan Wajah di Tengah Bingkai</p>
                <p class="text-[10px] text-white/60 mt-0.5">Deteksi wajah otomatis &amp; watermark timestamp</p>
                <div class="absolute top-2 left-2 px-2 py-0.5 rounded bg-primary text-[10px] font-bold">GPS: Valid (14m)</div>
            </div>

            <div class="p-3 rounded-xl bg-surface-container border border-black/5 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-on-surface-variant/60">Lokasi Terdeteksi:</span>
                    <span class="font-bold text-on-surface">Kantor Pusat Jakarta</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant/60">Status Fake GPS:</span>
                    <span class="font-bold text-primary">Tidak Terdeteksi (Aman)</span>
                </div>
            </div>

            <div class="flex gap-2.5 pt-2">
                <button type="button" @click="showClockWidget = false"
                        class="flex-1 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                    Batal
                </button>
                <button type="button" @click="showClockWidget = false"
                        class="flex-1 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center justify-center gap-1 transition">
                    <span class="material-symbols-outlined text-[16px]">fingerprint</span>
                    Clock-In Sekarang
                </button>
            </div>
        </div>
    </div>

</div>
@endsection