@extends('layouts.supervisor')

@section('title', 'Dashboard Supervisor')
@section('page-title', 'Dashboard Supervisor')
@section('page-desc', 'Pantau kehadiran tim, performansi pengajuan, dan persetujuan yang menunggu kamu.')

@php
    $stats = [
        ['label' => 'Anggota Tim', 'value' => '9 Org', 'icon' => 'groups', 'trend' => 'Divisi Eng'],
        ['label' => 'Hadir Hari Ini', 'value' => '7 / 9', 'icon' => 'check_circle', 'trend' => '77,8% Rate'],
        ['label' => 'Menunggu Persetujuan', 'value' => '5', 'icon' => 'fact_check', 'trend' => 'Pending SPV'],
        ['label' => 'Sedang Cuti / Izin', 'value' => '2', 'icon' => 'event_busy', 'trend' => 'Tercatat'],
    ];

    $team = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'status' => 'Hadir', 'time' => '07:55'],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'status' => 'Izin / Sakit', 'time' => '-'],
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'status' => 'Hadir', 'time' => '08:02'],
        ['name' => 'Kevin Malone', 'avatar' => 55, 'status' => 'Terlambat', 'time' => '08:20'],
        ['name' => 'Toby Flenderson', 'avatar' => 61, 'status' => 'Cuti Tahunan', 'time' => '-'],
    ];
    $teamBadge = [
        'Hadir' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
        'Terlambat' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'Izin / Sakit' => 'bg-violet-50 text-violet-800 border border-violet-200',
        'Cuti Tahunan' => 'bg-violet-50 text-violet-800 border border-violet-200',
        'Belum Presensi' => 'bg-rose-50 text-rose-800 border border-rose-200',
    ];

    $pending = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'type' => 'Cuti Tahunan', 'detail' => '2 hari (12–13 Agu)', 'route' => 'supervisor.approvals.leave'],
        ['name' => 'Kevin Malone', 'avatar' => 55, 'type' => 'Lembur (SPL)', 'detail' => '2 jam · Closing Laporan Bulanan', 'route' => 'supervisor.approvals.overtime'],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'type' => 'Reimbursement', 'detail' => 'Rp350.000 · Bensin & Parkir Client', 'route' => 'supervisor.approvals.reimbursement'],
    ];
@endphp

@section('content')

<div class="space-y-6">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $idx => $s)
            <div class="card-flat rounded-2xl p-5 flex flex-col justify-between animate-dash-card dash-delay-{{ $idx + 1 }} hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">{{ $s['icon'] }}</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant/60 font-mono-data">
                        {{ $s['trend'] }}
                    </span>
                </div>
                <div>
                    <p class="text-3xl font-extrabold font-mono-data text-on-surface leading-tight">{{ $s['value'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- VISUAL CHARTS GRID (IMAGE 1 & 2 SPEC) --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- 1. TEAM PRODUCTIVITY & ATTENDANCE LINE CHART (IMAGE 2 TOP LEFT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-3 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Kehadiran &amp; Produktivitas Tim</h3>
                    <p class="text-xs text-on-surface-variant/50">Tren konsistensi kehadiran tim kamu</p>
                </div>
                <select class="text-xs bg-surface-container border border-black/5 rounded-lg px-2.5 py-1 text-on-surface font-semibold focus:outline-none cursor-pointer">
                    <option>Bulan Ini</option>
                    <option>3 Bulan Terakhir</option>
                </select>
            </div>

            <div class="flex items-baseline gap-3 my-2">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">91,20%</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span> +2,1% vs minggu lalu
                </span>
            </div>

            <div class="relative mt-2 mb-1">
                <svg viewBox="0 0 300 100" class="w-full h-28 overflow-visible">
                    <defs>
                        <linearGradient id="spvLineGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M 10 60 C 50 48, 80 55, 120 30 C 160 25, 190 40, 230 14 C 260 32, 280 22, 290 25 L 290 95 L 10 95 Z" fill="url(#spvLineGrad)"/>
                    <path class="animate-line-draw" d="M 10 60 C 50 48, 80 55, 120 30 C 160 25, 190 40, 230 14 C 260 32, 280 22, 290 25" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="230" cy="14" r="5" fill="#10b981" stroke="#ffffff" stroke-width="2.5"/>
                </svg>
                <div class="absolute top-0 left-[72%] -translate-x-1/2 bg-white border border-black/10 shadow-md rounded-lg px-2.5 py-1 text-center font-mono-data">
                    <p class="text-[9px] text-on-surface-variant/60 uppercase">Minggu 3</p>
                    <p class="text-xs font-bold text-emerald-600">96.8%</p>
                </div>
            </div>

            <div class="flex justify-between text-[11px] font-mono-data text-on-surface-variant/50 border-t border-black/5 pt-2">
                <span>Mgg 1</span><span>Mgg 2</span><span>Mgg 3</span><span>Mgg 4</span>
            </div>
        </div>

        {{-- 2. APPROVAL SLA & TEAM SATISFACTION GAUGE (IMAGE 1 & IMAGE 2 BOTTOM RIGHT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">SLA Respon Persetujuan</h3>
                    <p class="text-xs text-on-surface-variant/50">Tingkat kecepatan approval SPV</p>
                </div>
                <button class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">more_horiz</span>
                </button>
            </div>

            <div class="grid grid-cols-12 gap-4 items-center my-1">
                {{-- SVG Semi-Circle Gauge --}}
                <div class="col-span-6 flex flex-col items-center relative">
                    <svg viewBox="0 0 100 60" class="w-40 h-24 overflow-visible">
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#e2e8f0" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 10 50 A 40 40 0 0 1 74 16" fill="none" stroke="#10b981" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 74 16 A 40 40 0 0 1 86 38" fill="none" stroke="#a7f3d0" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 86 38 A 40 40 0 0 1 90 50" fill="none" stroke="#fca5a5" stroke-width="11" stroke-linecap="round"/>
                        <g class="animate-gauge-needle" style="--gauge-deg: 38deg;">
                            <line x1="50" y1="50" x2="50" y2="16" stroke="#191c1d" stroke-width="3.5" stroke-linecap="round"/>
                            <circle cx="50" cy="50" r="5" fill="#191c1d"/>
                        </g>
                    </svg>
                    <div class="text-center -mt-3">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface block">92%</span>
                        <span class="text-[11px] text-on-surface-variant/50 font-medium">kecepatan respon</span>
                    </div>
                </div>

                {{-- Legend Stats --}}
                <div class="col-span-6 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Disetujui</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">45</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Pending SPV</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">5</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Ditolak</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">2</span>
                    </div>
                </div>
            </div>

            {{-- Green Pill Notification (Image 1) --}}
            <div class="mt-3 bg-emerald-50/90 text-emerald-900 border border-emerald-200/70 rounded-full px-4 py-2 text-xs font-medium flex items-center justify-between">
                <span>Respon approval <strong class="font-bold text-emerald-950">1.2 jam lebih cepat</strong> minggu ini</span>
                <span class="material-symbols-outlined text-[16px] text-emerald-600">bolt</span>
            </div>
        </div>

    </div>

    {{-- TIM HARI INI & PERSETUJUAN --}}
    <div class="grid grid-cols-3 gap-5 animate-dash-card dash-delay-5">

        {{-- STATUS TIM HARI INI --}}
        <div class="col-span-2 card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Status Tim Hari Ini</h2>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Ringkasan kehadiran anggota tim kamu</p>
                </div>
                <span class="text-xs font-mono-data font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                    7 / 9 Hadir
                </span>
            </div>

            {{-- Horizontal Multi Progress Bar --}}
            <div class="px-6 pt-4 pb-2">
                <div class="w-full h-3 bg-surface-container rounded-full overflow-hidden flex p-0.5 border border-black/5">
                    <div class="h-full bg-[#0B3D2E] rounded-l-full animate-bar-grow" style="width: 78%;" title="Hadir: 78%"></div>
                    <div class="h-full bg-amber-400 animate-bar-grow" style="width: 11%;" title="Terlambat: 11%"></div>
                    <div class="h-full bg-violet-400 rounded-r-full animate-bar-grow" style="width: 11%;" title="Cuti/Izin: 11%"></div>
                </div>
            </div>

            <div class="divide-y divide-black/5">
                @foreach ($team as $t)
                    <div class="px-6 py-3.5 flex items-center justify-between hover:bg-surface-container/40 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img={{ $t['avatar'] }}" class="w-8 h-8 rounded-full border border-black/10 object-cover" alt="{{ $t['name'] }}">
                            <p class="font-bold text-on-surface text-xs">{{ $t['name'] }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono-data text-on-surface-variant/50">{{ $t['time'] }}</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $teamBadge[$t['status']] }}">{{ $t['status'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- MENUNGGU PERSETUJUAN --}}
        <div class="card-flat rounded-2xl overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Menunggu Persetujuan</h2>
                        <p class="text-xs text-on-surface-variant/50 mt-0.5">Status "Pending SPV"</p>
                    </div>
                    <span class="w-6 h-6 rounded-full bg-amber-500 text-white font-mono-data text-xs font-bold flex items-center justify-center">
                        {{ count($pending) }}
                    </span>
                </div>

                <div class="divide-y divide-black/5">
                    @foreach ($pending as $p)
                        <a href="{{ route($p['route']) }}" class="px-6 py-3.5 flex items-center gap-3 hover:bg-emerald-50/50 transition block">
                            <img src="https://i.pravatar.cc/28?img={{ $p['avatar'] }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $p['name'] }}">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-on-surface truncate">{{ $p['name'] }}</p>
                                <p class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md inline-block my-0.5 border border-emerald-200/60">{{ $p['type'] }}</p>
                                <p class="text-[11px] text-on-surface-variant/50 truncate">{{ $p['detail'] }}</p>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant/40 text-[18px] shrink-0">chevron_right</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-3.5 bg-surface-container border-t border-black/5 text-center">
                <a href="{{ route('supervisor.approvals.leave') }}" class="text-xs font-bold text-primary hover:text-emerald-700 transition inline-flex items-center gap-1">
                    Lihat semua pengajuan tim &rarr;
                </a>
            </div>
        </div>
    </div>

</div>

@endsection