@extends('layouts.hr')

@section('title', 'Presensi & Live Attendance')
@section('page-title', 'Presensi & Live Attendance')
@section('page-desc', 'Pencatatan jam masuk & keluar via GPS + selfie.')

@php
    $stats = [
        ['label' => 'Tepat Waktu', 'value' => '128', 'icon' => 'check_circle'],
        ['label' => 'Terlambat', 'value' => '14', 'icon' => 'schedule'],
        ['label' => 'Izin / Sakit', 'value' => '6', 'icon' => 'medical_information'],
        ['label' => 'Tidak Hadir', 'value' => '8', 'icon' => 'person_off'],
    ];

    // status, keterlambatan (menit), lokasi & waktu mengikuti data yang sama seperti dicatat di aplikasi mobile karyawan
    $logs = [
        ['name' => 'Michael Scott', 'avatar' => 14, 'in' => '07:58', 'out' => '17:05', 'loc' => 'Kantor Pusat, Jakarta Selatan', 'status' => 'Tepat Waktu', 'late_minutes' => null, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026'],
        ['name' => 'Pam Beesly', 'avatar' => 47, 'in' => '08:14', 'out' => '17:02', 'loc' => 'Kantor Pusat, Jakarta Selatan', 'status' => 'Terlambat', 'late_minutes' => 14, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026'],
        ['name' => 'Jim Halpert', 'avatar' => 12, 'in' => '07:50', 'out' => '-', 'loc' => 'Kantor Pusat, Jakarta Selatan', 'status' => 'Sedang Bekerja', 'late_minutes' => null, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026'],
        ['name' => 'Dwight Schrute', 'avatar' => 51, 'in' => '-', 'out' => '-', 'loc' => '-', 'status' => 'Izin / Sakit', 'late_minutes' => null, 'note' => 'Surat dokter terlampir', 'attachment' => 'surat_dokter_dwight.pdf', 'date' => '05 Agustus 2026'],
        ['name' => 'Angela Martin', 'avatar' => 33, 'in' => '-', 'out' => '-', 'loc' => '-', 'status' => 'Tidak Hadir', 'late_minutes' => null, 'note' => null, 'attachment' => null, 'date' => '05 Agustus 2026'],
    ];

    $badge = [
        'Tepat Waktu' => 'bg-primary/10 text-primary',
        'Terlambat' => 'bg-amber-500/10 text-amber-700',
        'Sedang Bekerja' => 'bg-purple-500/10 text-purple-700',
        'Izin / Sakit' => 'bg-violet-500/10 text-violet-700',
        'Tidak Hadir' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-[20px]">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $s['value'] }}</p>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- LOG TABLE --}}
    <div class="card-flat rounded-2xl overflow-hidden" x-data="{ activePreview: null }">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Log Presensi Hari Ini</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($logs) }} karyawan tercatat · klik foto untuk lihat bukti selfie & lokasi</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" placeholder="Cari nama karyawan..."
                           class="w-52 pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition">
                </div>
                <input type="date" value="2026-08-05"
                       class="text-xs font-bold border border-black/10 rounded-lg px-3 py-2.5 bg-white
                              hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">Karyawan</th>
                    <th class="px-6 py-3.5">Bukti Selfie</th>
                    <th class="px-6 py-3.5">Jam Kerja</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($logs as $l)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/32?img={{ $l['avatar'] }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $l['name'] }}">
                                <div>
                                    <p class="font-bold text-on-surface">{{ $l['name'] }}</p>
                                    <p class="text-xs text-on-surface-variant/50">{{ $l['loc'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            @if ($l['in'] !== '-')
                                <button type="button" @click="activePreview = {{ $loop->index }}"
                                        class="block w-9 h-9 rounded-lg overflow-hidden ring-2 ring-transparent hover:ring-primary/40 transition">
                                    <img src="https://i.pravatar.cc/64?img={{ $l['avatar'] + 1 }}" class="w-full h-full object-cover" alt="selfie {{ $l['name'] }}">
                                </button>
                            @else
                                <span class="text-on-surface-variant/30">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">
                            {{ $l['in'] }} <span class="text-on-surface-variant/30">–</span> {{ $l['out'] }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] }}">{{ $l['status'] }}</span>
                            @if ($l['status'] === 'Terlambat' && $l['late_minutes'])
                                <p class="text-[11px] text-on-surface-variant/40 mt-1">Telat {{ $l['late_minutes'] }} menit</p>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            @if ($l['attachment'])
                                <button type="button" title="{{ $l['note'] }}"
                                        class="text-[11px] font-bold text-primary flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[15px]">description</span>
                                    Lihat Surat
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

        {{-- PREVIEW SELFIE + GEOTAG (overlay ala watermark foto bukti kurir) --}}
        @foreach ($logs as $l)
            @if ($l['in'] !== '-')
                <div x-show="activePreview === {{ $loop->index }}" x-cloak
                     class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-6"
                     @click.self="activePreview = null">
                    <div class="bg-white rounded-2xl overflow-hidden max-w-sm w-full shadow-xl">
                        <div class="relative">
                            <img src="https://i.pravatar.cc/480?img={{ $l['avatar'] + 1 }}" class="w-full h-80 object-cover" alt="selfie {{ $l['name'] }}">
                            <button type="button" @click="activePreview = null"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                            {{-- watermark keterangan lokasi & waktu di atas foto --}}
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-4 pt-10 pb-3.5">
                                <p class="text-white text-sm font-bold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                                    {{ $l['loc'] }}
                                </p>
                                <div class="flex items-center gap-3 mt-1">
                                    <p class="text-white/80 text-[11px] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                        {{ $l['date'] }}
                                    </p>
                                    <p class="text-white/80 text-[11px] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        Clock in {{ $l['in'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $l['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <p class="text-sm font-bold text-on-surface">{{ $l['name'] }}</p>
                            </div>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] }}">{{ $l['status'] }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

@endsection