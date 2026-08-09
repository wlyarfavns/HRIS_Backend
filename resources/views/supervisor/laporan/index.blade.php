@extends('layouts.supervisor')

@section('title', 'Laporan Kehadiran Tim')
@section('page-title', 'Laporan Kehadiran Tim')
@section('page-desc', 'Rekap kehadiran bulanan anggota tim kamu.')

@php
    $team = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'hadir' => 21, 'terlambat' => 1, 'izin' => 1, 'total' => 22],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'hadir' => 19, 'terlambat' => 0, 'izin' => 3, 'total' => 22],
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'hadir' => 22, 'terlambat' => 2, 'izin' => 0, 'total' => 22],
        ['name' => 'Kevin Malone', 'avatar' => 55, 'hadir' => 20, 'terlambat' => 3, 'izin' => 2, 'total' => 22],
    ];
@endphp

@section('content')

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface">Rekap Kehadiran — Agustus 2026</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($team) }} anggota tim</p>
            </div>
            <input type="month" value="2026-08"
                   class="text-xs font-bold border border-black/10 rounded-lg px-3 py-2.5 bg-white
                          hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Hadir</th>
                    <th class="px-6 py-3">Terlambat</th>
                    <th class="px-6 py-3">Izin / Sakit</th>
                    <th class="px-6 py-3">Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($team as $t)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $t['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $t['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $t['hadir'] }} hari</td>
                        <td class="px-6 py-3.5 font-mono-data text-amber-700">{{ $t['terlambat'] }}x</td>
                        <td class="px-6 py-3.5 font-mono-data text-violet-700">{{ $t['izin'] }} hari</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-1.5 rounded-full bg-surface-container overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ round($t['hadir'] / $t['total'] * 100) }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-on-surface-variant/60">{{ round($t['hadir'] / $t['total'] * 100) }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection