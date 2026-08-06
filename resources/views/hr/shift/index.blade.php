@extends('layouts.hr')

@section('title', 'Shift & Roster Kerja')
@section('page-title', 'Shift & Roster Kerja')
@section('page-desc', 'Jadwal fleksibel, shift malam, dan toleransi keterlambatan.')

@section('page-action')
    <button class="bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 hover:brightness-110 transition">
        <span class="material-symbols-outlined text-[16px]">group_add</span>
        Bulk Assign Shift
    </button>
@endsection

@php
    $shiftTypes = [
        ['name' => 'Shift Pagi', 'time' => '08:00 - 17:00', 'color' => '#0B3D2E'],
        ['name' => 'Shift Siang', 'time' => '13:00 - 22:00', 'color' => '#FFD700'],
        ['name' => 'Shift Malam', 'time' => '22:00 - 07:00', 'color' => '#7c3aed'],
        ['name' => 'Libur', 'time' => '-', 'color' => '#B9C2BD'],
    ];

    $roster = [
        ['name' => 'Michael Scott', 'avatar' => 14, 'days' => ['P','P','P','P','P','L','L']],
        ['name' => 'Pam Beesly', 'avatar' => 47, 'days' => ['P','P','S','S','P','L','L']],
        ['name' => 'Jim Halpert', 'avatar' => 12, 'days' => ['S','S','S','P','P','L','L']],
        ['name' => 'Dwight Schrute', 'avatar' => 51, 'days' => ['M','M','M','L','P','P','L']],
    ];
    $map = ['P' => '#0B3D2E', 'S' => '#FFD700', 'M' => '#7c3aed', 'L' => '#B9C2BD'];
    $labels = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
@endphp

@section('content')

    <div class="grid grid-cols-4 gap-5">
        @foreach ($shiftTypes as $t)
            <div class="card-flat rounded-xl p-4 flex items-center gap-3">
                <span class="w-3 h-3 rounded-full shrink-0" style="background-color:{{ $t['color'] }}"></span>
                <div>
                    <p class="text-sm font-bold text-on-surface">{{ $t['name'] }}</p>
                    <p class="text-[11px] text-on-surface-variant/50 font-mono-data">{{ $t['time'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card-flat rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-bold text-on-surface">Roster Minggu Ini</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">21 - 27 Oktober 2026</p>
            </div>
            <div class="flex gap-1">
                <button class="w-8 h-8 rounded-lg hover:bg-surface-container flex items-center justify-center text-on-surface-variant/60">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </button>
                <button class="w-8 h-8 rounded-lg hover:bg-surface-container flex items-center justify-center text-on-surface-variant/60">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </button>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-widest">
                    <th class="pb-3">Karyawan</th>
                    @foreach ($labels as $l)
                        <th class="pb-3 text-center w-14">{{ $l }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($roster as $r)
                    <tr>
                        <td class="py-3">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $r['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $r['name'] }}</span>
                            </div>
                        </td>
                        @foreach ($r['days'] as $d)
                            <td class="text-center">
                                <span class="inline-flex w-7 h-7 rounded-lg items-center justify-center text-[11px] font-bold text-white"
                                      style="background-color:{{ $map[$d] }}{{ $d === 'L' ? '' : '' }}; opacity:{{ $d === 'L' ? '0.5' : '1' }}">
                                    {{ $d }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-2 gap-5">
        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-4">Pengajuan Tukar Shift</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container">
                    <div>
                        <p class="text-sm font-bold text-on-surface">Jim Halpert ⇄ Dwight Schrute</p>
                        <p class="text-xs text-on-surface-variant/50">Shift Malam, 24 Okt · Menunggu SPV</p>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-amber-500/10 text-amber-700">Pending</span>
                </div>
            </div>
        </div>

        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-4">Radius Geofencing Kantor</h2>
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Radius Absen (meter)</label>
                    <input type="range" min="20" max="300" value="100" class="w-full mt-2 accent-primary">
                </div>
                <span class="font-mono-data font-bold text-primary text-lg w-16 text-right">100 m</span>
            </div>
            <p class="text-xs text-on-surface-variant/40 mt-2">Karyawan hanya bisa clock-in di dalam radius ini dari titik kantor.</p>
        </div>
    </div>

@endsection