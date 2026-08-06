@extends('layouts.hr')

@section('title', 'Presensi & Live Attendance')
@section('page-title', 'Presensi & Live Attendance')
@section('page-desc', 'Pencatatan jam masuk & keluar via GPS + selfie.')

@php
    $stats = [
        ['label' => 'Tepat Waktu', 'value' => '128', 'color' => '#0B3D2E'],
        ['label' => 'Terlambat', 'value' => '14', 'color' => '#FFD700'],
        ['label' => 'Tidak Hadir', 'value' => '8', 'color' => '#ba1a1a'],
        ['label' => 'Sedang Lembur', 'value' => '12', 'color' => '#7c3aed'],
    ];

    $logs = [
        ['name' => 'Michael Scott', 'avatar' => 14, 'in' => '07:58', 'out' => '17:05', 'loc' => 'Kantor Pusat', 'status' => 'Tepat Waktu'],
        ['name' => 'Pam Beesly', 'avatar' => 47, 'in' => '08:14', 'out' => '17:02', 'loc' => 'Kantor Pusat', 'status' => 'Terlambat'],
        ['name' => 'Jim Halpert', 'avatar' => 12, 'in' => '07:50', 'out' => '-', 'loc' => 'Kantor Cabang B', 'status' => 'Sedang Bekerja'],
        ['name' => 'Angela Martin', 'avatar' => 33, 'in' => '-', 'out' => '-', 'loc' => '-', 'status' => 'Tidak Hadir'],
    ];

    $badge = [
        'Tepat Waktu' => 'bg-primary/10 text-primary',
        'Terlambat' => 'bg-amber-500/10 text-amber-700',
        'Sedang Bekerja' => 'bg-purple-500/10 text-purple-700',
        'Tidak Hadir' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')

    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5 border-l-[3px]" style="border-color:{{ $s['color'] }}">
                <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ $s['value'] }}</p>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-3 gap-5">
        {{-- LIVE MAP --}}
        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-4">Peta Lokasi Absen Hari Ini</h2>
            <div class="rounded-xl h-64 bg-surface-container flex items-center justify-center relative overflow-hidden">
                <span class="material-symbols-outlined text-on-surface-variant/20 text-[48px]">map</span>
                <span class="absolute top-8 left-10 w-3 h-3 rounded-full bg-primary ring-4 ring-primary/20"></span>
                <span class="absolute bottom-12 right-16 w-3 h-3 rounded-full bg-amber-500 ring-4 ring-amber-500/20"></span>
                <span class="absolute top-20 right-24 w-3 h-3 rounded-full bg-primary ring-4 ring-primary/20"></span>
            </div>
            <p class="text-[11px] text-on-surface-variant/40 mt-3">Menampilkan titik clock-in real-time berdasarkan koordinat GPS karyawan.</p>
        </div>

        {{-- LOG TABLE --}}
        <div class="col-span-2 card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                <h2 class="text-base font-bold text-on-surface">Log Presensi Hari Ini</h2>
                <input type="date" value="2026-08-05" class="text-xs font-bold border border-black/10 rounded-lg px-3 py-2">
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Selfie</th>
                        <th class="px-6 py-3">Clock In</th>
                        <th class="px-6 py-3">Clock Out</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($logs as $l)
                        <tr class="hover:bg-surface-container/60">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2.5">
                                    <img src="https://i.pravatar.cc/28?img={{ $l['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                    <span class="font-bold text-on-surface text-xs">{{ $l['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                @if ($l['in'] !== '-')
                                    <img src="https://i.pravatar.cc/24?img={{ $l['avatar'] + 1 }}" class="w-6 h-6 rounded object-cover" alt="selfie">
                                @else
                                    <span class="text-on-surface-variant/30">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 font-mono-data text-on-surface-variant/70">{{ $l['in'] }}</td>
                            <td class="px-6 py-3 font-mono-data text-on-surface-variant/70">{{ $l['out'] }}</td>
                            <td class="px-6 py-3 text-on-surface-variant/60 text-xs">{{ $l['loc'] }}</td>
                            <td class="px-6 py-3">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] }}">{{ $l['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection