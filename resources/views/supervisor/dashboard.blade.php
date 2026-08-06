@extends('layouts.supervisor')

@section('title', 'Dashboard Supervisor')
@section('page-title', 'Dashboard Supervisor')
@section('page-desc', 'Pantau kehadiran tim dan pengajuan yang menunggu persetujuan kamu.')

@php
    $stats = [
        ['label' => 'Anggota Tim', 'value' => '9', 'icon' => 'groups'],
        ['label' => 'Hadir Hari Ini', 'value' => '7 / 9', 'icon' => 'check_circle'],
        ['label' => 'Menunggu Persetujuan', 'value' => '5', 'icon' => 'fact_check'],
        ['label' => 'Sedang Cuti / Izin', 'value' => '2', 'icon' => 'event_busy'],
    ];

    $team = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'status' => 'Hadir', 'time' => '07:55'],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'status' => 'Izin / Sakit', 'time' => '-'],
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'status' => 'Hadir', 'time' => '08:02'],
        ['name' => 'Kevin Malone', 'avatar' => 55, 'status' => 'Terlambat', 'time' => '08:20'],
        ['name' => 'Toby Flenderson', 'avatar' => 61, 'status' => 'Cuti Tahunan', 'time' => '-'],
    ];
    $teamBadge = [
        'Hadir' => 'bg-primary/10 text-primary',
        'Terlambat' => 'bg-amber-500/10 text-amber-700',
        'Izin / Sakit' => 'bg-violet-500/10 text-violet-700',
        'Cuti Tahunan' => 'bg-violet-500/10 text-violet-700',
        'Belum Presensi' => 'bg-error/10 text-error',
    ];

    $pending = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'type' => 'Cuti Tahunan', 'detail' => '2 hari (12–13 Agu)', 'route' => 'supervisor.approvals.leave'],
        ['name' => 'Kevin Malone', 'avatar' => 55, 'type' => 'Lembur (SPL)', 'detail' => '2 jam · Closing Laporan Bulanan', 'route' => 'supervisor.approvals.overtime'],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'type' => 'Reimbursement', 'detail' => 'Rp350.000 · Bensin & Parkir Client', 'route' => 'supervisor.approvals.reimbursement'],
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

    <div class="grid grid-cols-3 gap-5">
        {{-- TIM HARI INI --}}
        <div class="col-span-2 card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5">
                <h2 class="text-base font-bold text-on-surface">Status Tim Hari Ini</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Ringkasan kehadiran anggota tim kamu</p>
            </div>
            <div class="divide-y divide-black/5">
                @foreach ($team as $t)
                    <div class="px-6 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img={{ $t['avatar'] }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $t['name'] }}">
                            <p class="font-bold text-on-surface text-sm">{{ $t['name'] }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono-data text-on-surface-variant/50">{{ $t['time'] }}</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $teamBadge[$t['status']] }}">{{ $t['status'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- MENUNGGU PERSETUJUAN --}}
        <div class="card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5">
                <h2 class="text-base font-bold text-on-surface">Menunggu Persetujuan Kamu</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Pengajuan dari tim, status "Pending SPV"</p>
            </div>
            <div class="divide-y divide-black/5">
                @foreach ($pending as $p)
                    <a href="{{ route($p['route']) }}" class="px-6 py-3.5 flex items-center gap-3 hover:bg-primary/5 transition">
                        <img src="https://i.pravatar.cc/28?img={{ $p['avatar'] }}" class="w-7 h-7 rounded-full object-cover shrink-0" alt="{{ $p['name'] }}">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-on-surface truncate">{{ $p['name'] }} <span class="font-normal text-on-surface-variant/50">· {{ $p['type'] }}</span></p>
                            <p class="text-[11px] text-on-surface-variant/50 truncate">{{ $p['detail'] }}</p>
                        </div>
                        <span class="material-symbols-outlined text-on-surface-variant/30 text-[18px] ml-auto shrink-0">chevron_right</span>
                    </a>
                @endforeach
            </div>
            <div class="px-6 py-3 bg-surface-container">
                <a href="{{ route('supervisor.approvals.leave') }}" class="text-xs font-bold text-primary hover:underline">Lihat semua pengajuan &rarr;</a>
            </div>
        </div>
    </div>

@endsection