@extends('layouts.supervisor')

@section('title', 'Dashboard Supervisor')
@section('page-title', 'Dashboard Supervisor')
@section('page-desc', 'Pantau kehadiran tim, performansi pengajuan, dan persetujuan yang menunggu kamu.')

@php
    // $stats, $team, $teamBadge, $pending, $weeklyAttendance, $slaStats
    // semuanya dikirim dari SupervisorDashboardController@index — tidak ada data dummy lagi.
    // Fallback ke array kosong kalau variabel entah kenapa tidak terkirim (mis. route salah/cache lama),
    // supaya blade tidak crash dan lebih mudah ketahuan datanya kosong daripada error 500.
    $stats = $stats ?? [];
    $team = $team ?? [];
    $teamBadge = $teamBadge ?? [];
    $pending = $pending ?? [];
    $weeklyAttendance = $weeklyAttendance ?? [];
    $slaStats = $slaStats ?? ['rate' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];

    $totalTeam = count($team);
    $hadirCount = collect($team)->whereIn('status', ['Hadir', 'Terlambat'])->count();

    // Titik-titik untuk polyline SVG chart mingguan, dari data asli $weeklyAttendance (persen 0-100)
    $chartPoints = collect($weeklyAttendance)->values();
    $svgCoords = $chartPoints->map(function ($pct, $i) use ($chartPoints) {
        $x = $chartPoints->count() > 1 ? 10 + ($i * (280 / max($chartPoints->count() - 1, 1))) : 10;
        $y = 90 - (($pct / 100) * 75); // 90 = baseline, 75 = tinggi area chart
        return round($x, 1) . ' ' . round($y, 1);
    });
    $areaPath = 'M ' . $svgCoords->implode(' L ') . " L 290 95 L 10 95 Z";
    $linePath = 'M ' . $svgCoords->implode(' L ');
    $lastPoint = $svgCoords->last();
    $lastPct = $chartPoints->last();

    // Sudut jarum gauge SLA: 0% -> 0deg, 100% -> 180deg (semi-circle), lalu offset -90deg utk transform CSS
    $gaugeDeg = (($slaStats['rate'] ?? 0) / 100) * 180 - 90;
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

    {{-- VISUAL CHARTS GRID --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- 1. TEAM PRODUCTIVITY & ATTENDANCE LINE CHART --}}
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
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">{{ number_format($lastPct ?? 0, 1, ',', '.') }}%</span>
            </div>

            <div class="relative mt-2 mb-1">
                @if ($chartPoints->count() > 1)
                    <svg viewBox="0 0 300 100" class="w-full h-28 overflow-visible">
                        <defs>
                            <linearGradient id="spvLineGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <path d="{{ $areaPath }}" fill="url(#spvLineGrad)"/>
                        <path class="animate-line-draw" d="{{ $linePath }}" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="{{ explode(' ', $lastPoint)[0] }}" cy="{{ explode(' ', $lastPoint)[1] }}" r="5" fill="#10b981" stroke="#ffffff" stroke-width="2.5"/>
                    </svg>
                @else
                    <p class="text-xs text-on-surface-variant/50 py-8 text-center">Belum cukup data presensi bulan ini.</p>
                @endif
            </div>

            <div class="flex justify-between text-[11px] font-mono-data text-on-surface-variant/50 border-t border-black/5 pt-2">
                @for ($w = 1; $w <= max($chartPoints->count(), 1); $w++)
                    <span>Mgg {{ $w }}</span>
                @endfor
            </div>
        </div>

        {{-- 2. APPROVAL SLA GAUGE --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">SLA Respon Persetujuan</h3>
                    <p class="text-xs text-on-surface-variant/50">Tingkat kecepatan approval SPV, 30 hari terakhir</p>
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
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#10b981" stroke-width="11" stroke-linecap="round"
                              stroke-dasharray="{{ round(($slaStats['rate'] ?? 0) / 100 * 125.6, 1) }} 125.6"/>
                        <g style="transform: rotate({{ $gaugeDeg }}deg); transform-origin: 50px 50px;">
                            <line x1="50" y1="50" x2="50" y2="16" stroke="#191c1d" stroke-width="3.5" stroke-linecap="round"/>
                            <circle cx="50" cy="50" r="5" fill="#191c1d"/>
                        </g>
                    </svg>
                    <div class="text-center -mt-3">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface block">{{ $slaStats['rate'] ?? 0 }}%</span>
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
                        <span class="font-bold font-mono-data text-on-surface">{{ $slaStats['approved'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Pending SPV</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">{{ $slaStats['pending'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Ditolak</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">{{ $slaStats['rejected'] ?? 0 }}</span>
                    </div>
                </div>
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
                    {{ $hadirCount }} / {{ $totalTeam }} Hadir
                </span>
            </div>

            <div class="divide-y divide-black/5">
                @forelse ($team as $t)
                    <div class="px-6 py-3.5 flex items-center justify-between hover:bg-surface-container/40 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img={{ $t['avatar'] }}" class="w-8 h-8 rounded-full border border-black/10 object-cover" alt="{{ $t['name'] }}">
                            <p class="font-bold text-on-surface text-xs">{{ $t['name'] }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono-data text-on-surface-variant/50">{{ $t['time'] }}</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $teamBadge[$t['status']] ?? 'bg-surface-container text-on-surface-variant' }}">{{ $t['status'] }}</span>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-6 text-xs text-on-surface-variant/50 text-center">Belum ada anggota tim terdaftar.</p>
                @endforelse
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
                    @forelse ($pending as $p)
                        <a href="{{ route($p['route']) }}" class="px-6 py-3.5 flex items-center gap-3 hover:bg-emerald-50/50 transition block">
                            <img src="https://i.pravatar.cc/28?img={{ $p['avatar'] }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $p['name'] }}">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-on-surface truncate">{{ $p['name'] }}</p>
                                <p class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md inline-block my-0.5 border border-emerald-200/60">{{ $p['type'] }}</p>
                                <p class="text-[11px] text-on-surface-variant/50 truncate">{{ $p['detail'] }}</p>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant/40 text-[18px] shrink-0">chevron_right</span>
                        </a>
                    @empty
                        <p class="px-6 py-6 text-xs text-on-surface-variant/50 text-center">Tidak ada pengajuan yang menunggu.</p>
                    @endforelse
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