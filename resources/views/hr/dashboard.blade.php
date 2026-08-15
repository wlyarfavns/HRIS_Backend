@extends('layouts.hr')

@section('title', 'Dashboard HR')
@section('page-title', 'Dashboard HR')
@section('page-desc', 'Ringkasan performansi tim, tingkat kehadiran, dan persetujuan pengajuan hari ini.')

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════
         KPI CARDS — 4 kolom
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="card-flat rounded-2xl p-5 animate-dash-card dash-delay-1 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-on-surface-variant/50 tracking-widest uppercase mb-1">Total Karyawan Aktif</p>
                    <p class="text-3xl font-extrabold font-mono-data text-on-surface">{{ $totalEmployees }}</p>
                    <p class="text-xs text-on-surface-variant/50 mt-1">Terdaftar di sistem</p>
                </div>
                <span class="material-symbols-outlined text-[28px] text-primary/30 mt-1">groups</span>
            </div>
        </div>

        <div class="card-flat rounded-2xl p-5 animate-dash-card dash-delay-2 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-on-surface-variant/50 tracking-widest uppercase mb-1">Karyawan Baru</p>
                    <p class="text-3xl font-extrabold font-mono-data text-on-surface">{{ $newEmployees }}</p>
                    <p class="text-xs text-on-surface-variant/50 mt-1">30 hari terakhir</p>
                </div>
                <span class="material-symbols-outlined text-[28px] text-emerald-400/50 mt-1">person_add</span>
            </div>
        </div>

        <div class="card-flat rounded-2xl p-5 animate-dash-card dash-delay-3 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-on-surface-variant/50 tracking-widest uppercase mb-1">Pengajuan Cuti Pending</p>
                    <p class="text-3xl font-extrabold font-mono-data {{ $pendingLeave > 0 ? 'text-amber-600' : 'text-on-surface' }}">
                        {{ $pendingLeave }}
                    </p>
                    <p class="text-xs text-on-surface-variant/50 mt-1">Menunggu approval HR</p>
                </div>
                <span class="material-symbols-outlined text-[28px] text-amber-400/50 mt-1">assignment_late</span>
            </div>
        </div>

        <div class="card-flat rounded-2xl p-5 animate-dash-card dash-delay-4 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-on-surface-variant/50 tracking-widest uppercase mb-1">Kontrak Akan Habis</p>
                    <p class="text-3xl font-extrabold font-mono-data {{ $expiringContracts > 0 ? 'text-rose-600' : 'text-on-surface' }}">
                        {{ $expiringContracts }}
                    </p>
                    <p class="text-xs text-on-surface-variant/50 mt-1">H-30 dari sekarang</p>
                </div>
                <span class="material-symbols-outlined text-[28px] text-rose-400/50 mt-1">event_upcoming</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         CHART GRID — 2x2
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- CARD 1: Line Chart — Team Performance --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-1 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Performansi Tim</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Tren kehadiran tepat waktu 6 bulan terakhir</p>
                </div>
                @php
                    $perfBadgeColor = $perfChange >= 0
                        ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60'
                        : 'text-rose-700 bg-rose-50 border-rose-200/60';
                    $perfArrow = $perfChange >= 0 ? 'arrow_upward' : 'arrow_downward';
                @endphp
                <span class="inline-flex items-center gap-1 text-[11px] font-bold {{ $perfBadgeColor }} border px-2.5 py-1 rounded-full whitespace-nowrap">
                    <span class="material-symbols-outlined text-[13px]">{{ $perfArrow }}</span>
                    {{ $perfChange > 0 ? '+' : '' }}{{ $perfChange }}%
                </span>
            </div>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">{{ $avgProductivity }}%</span>
                <span class="text-xs text-on-surface-variant/50">avg. kehadiran tepat waktu</span>
            </div>
            <div class="flex-1 relative" style="min-height:190px;">
                <canvas id="hrLineChart"></canvas>
            </div>
        </div>

        {{-- CARD 2: Bar Chart — Daily Attendance --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-2 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Kehadiran Harian</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Distribusi status kehadiran staf bulan ini</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-full whitespace-nowrap">
                    <span class="material-symbols-outlined text-[13px]">arrow_upward</span>{{ $attendanceRate }}%
                </span>
            </div>
            <div class="flex items-center gap-4 mb-3">
                <div class="flex items-center gap-1.5 text-xs"><span class="w-2.5 h-2.5 rounded-sm bg-[#0B3D2E] shrink-0"></span><span class="text-on-surface-variant/70 font-medium">Tepat Waktu</span></div>
                <div class="flex items-center gap-1.5 text-xs"><span class="w-2.5 h-2.5 rounded-sm bg-amber-400 shrink-0"></span><span class="text-on-surface-variant/70 font-medium">Terlambat</span></div>
                <div class="flex items-center gap-1.5 text-xs"><span class="w-2.5 h-2.5 rounded-sm bg-slate-300 shrink-0"></span><span class="text-on-surface-variant/70 font-medium">Alpa</span></div>
            </div>
            <div class="flex-1 relative" style="min-height:190px;">
                <canvas id="hrBarChart"></canvas>
            </div>
        </div>

        {{-- CARD 3: Doughnut — Leave & Absence Breakdown --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-3 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Kategori Pengajuan Cuti &amp; Izin</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Distribusi jenis permohonan periode berjalan</p>
                </div>
            </div>

            @if($leaveTotalAll === 0)
                {{-- Empty state --}}
                <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                    <span class="material-symbols-outlined text-[40px] text-on-surface-variant/20 mb-2">event_busy</span>
                    <p class="text-sm text-on-surface-variant/50">Belum ada pengajuan cuti</p>
                </div>
            @else
                <div class="flex items-center gap-6 flex-1 mt-1">
                    <div class="relative shrink-0 flex items-center justify-center" style="width:160px;height:160px;">
                        <canvas id="hrDonutChart" style="width:160px;height:160px;"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $leaveTotalAll }}</span>
                            <span class="text-[10px] text-on-surface-variant/50 mt-0.5">Pengajuan</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2.5">
                        @php
                            $donutColors = ['bg-[#0B3D2E]', 'bg-emerald-500', 'bg-emerald-300', 'bg-emerald-100'];
                            $donutBarColors = ['bg-[#0B3D2E]', 'bg-emerald-500', 'bg-emerald-300', 'bg-emerald-100'];
                        @endphp
                        @foreach($leaveLabels as $i => $label)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $donutColors[$i] ?? 'bg-slate-300' }} shrink-0"></span>
                                    <span class="font-semibold text-on-surface">{{ $label }}</span>
                                </div>
                                <span class="font-bold font-mono-data">{{ $leavePercents[$i] ?? 0 }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $donutBarColors[$i] ?? 'bg-slate-300' }} rounded-full animate-bar-grow"
                                     style="width:{{ $leavePercents[$i] ?? 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- CARD 4: Horizontal Bar — Department Distribution --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-4 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Distribusi Karyawan per Divisi</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Alokasi staf di departemen aktif perusahaan</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-full whitespace-nowrap">
                    {{ $totalEmployees }} Total Staf
                </span>
            </div>

            @if($deptCounts->sum() === 0)
                <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                    <span class="material-symbols-outlined text-[40px] text-on-surface-variant/20 mb-2">corporate_fare</span>
                    <p class="text-sm text-on-surface-variant/50">Belum ada data departemen</p>
                </div>
            @else
                <div class="flex-1 relative mt-3" style="min-height:190px;">
                    <canvas id="hrDeptChart"></canvas>
                </div>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         BARIS BAWAH: Pending Approvals + Aktivitas Terkini
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-5">

        {{-- Pending Approvals Summary --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-1 hover:shadow-md transition">
            <h3 class="text-base font-bold text-on-surface mb-4">Menunggu Persetujuan</h3>
            <div class="space-y-3">
                <a href="{{ route('hr.approvals.leave') }}" class="flex items-center justify-between p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-[20px]">event_busy</span>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Cuti & Izin</p>
                            <p class="text-[10px] text-on-surface-variant/50">Pending approval HR</p>
                        </div>
                    </div>
                    @if($pendingLeave > 0)
                        <span class="text-xs font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full">{{ $pendingLeave }}</span>
                    @else
                        <span class="text-xs text-on-surface-variant/40">Bersih</span>
                    @endif
                </a>

                <a href="{{ route('hr.approvals.overtime') }}" class="flex items-center justify-between p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-600 text-[20px]">schedule</span>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Lembur (SPL)</p>
                            <p class="text-[10px] text-on-surface-variant/50">Siap dikunci HR</p>
                        </div>
                    </div>
                    @if($pendingOvertimeHr > 0)
                        <span class="text-xs font-bold bg-blue-500 text-white px-2 py-0.5 rounded-full">{{ $pendingOvertimeHr }}</span>
                    @else
                        <span class="text-xs text-on-surface-variant/40">Bersih</span>
                    @endif
                </a>

                <a href="{{ route('hr.approvals.reimbursement') }}" class="flex items-center justify-between p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition group">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-purple-600 text-[20px]">receipt_long</span>
                        <div>
                            <p class="text-sm font-semibold text-on-surface">Reimbursement</p>
                            <p class="text-[10px] text-on-surface-variant/50">Pending review HR</p>
                        </div>
                    </div>
                    @if($pendingReimbursement > 0)
                        <span class="text-xs font-bold bg-purple-500 text-white px-2 py-0.5 rounded-full">{{ $pendingReimbursement }}</span>
                    @else
                        <span class="text-xs text-on-surface-variant/40">Bersih</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Aktivitas Terkini — span 2 kolom --}}
        <div class="col-span-2 card-flat rounded-2xl p-6 animate-dash-card dash-delay-2 hover:shadow-md transition">
            <h3 class="text-base font-bold text-on-surface mb-4">Aktivitas Terkini</h3>

            @if($recentActivity->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="material-symbols-outlined text-[40px] text-on-surface-variant/20 mb-2">inbox</span>
                    <p class="text-sm text-on-surface-variant/50">Belum ada aktivitas terbaru</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentActivity as $act)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl {{ $act['bg'] }} flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[16px] {{ $act['color'] }}">{{ $act['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-on-surface truncate">{{ $act['label'] }}</p>
                            <p class="text-[11px] text-on-surface-variant/50">{{ $act['sub'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>

{{-- ══════════════════════════════════════════
     Chart.js — semua data dari PHP/Blade (bukan hardcode)
     ══════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // ── Data dari PHP ─────────────────────────────────────────────────────────
    const lineLabels    = @json($performanceLabels);
    const lineData      = @json($performanceData);
    const barLabels     = @json($attendanceDays);
    const onTimeData    = @json($onTimeByDay);
    const lateData      = @json($lateByDay);
    const absentData    = @json($absentByDay);
    const leaveLabels   = @json($leaveLabels);
    const leaveCounts   = @json($leaveCounts);
    const deptLabels    = @json($deptLabels);
    const deptCounts    = @json($deptCounts);

    // ── 1. Line Chart — Performansi ───────────────────────────────────────────
    const hrLine = document.getElementById('hrLineChart');
    if (hrLine) {
        const g = hrLine.getContext('2d').createLinearGradient(0, 0, 0, 200);
        g.addColorStop(0, 'rgba(16,185,129,0.22)');
        g.addColorStop(1, 'rgba(16,185,129,0)');
        new Chart(hrLine, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: 'Kehadiran Tepat Waktu (%)',
                    data: lineData,
                    fill: true,
                    backgroundColor: g,
                    borderColor: '#10b981',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#10b981',
                    tension: 0.42,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 1300, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E', titleColor: '#a7f3d0', bodyColor: '#fff',
                        borderColor: '#10b981', borderWidth: 1, padding: 10, cornerRadius: 10,
                        callbacks: { label: c => ` ${c.parsed.y}% tepat waktu` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        min: 0, max: 100,
                        grid: { color: '#f1f5f9' }, border: { display: false },
                        ticks: { callback: v => v+'%', font: { size: 10 }, stepSize: 20 }
                    }
                }
            }
        });
    }

    // ── 2. Stacked Bar — Kehadiran Harian ────────────────────────────────────
    const hrBar = document.getElementById('hrBarChart');
    if (hrBar && barLabels.length > 0) {
        new Chart(hrBar, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [
                    { label: 'Tepat Waktu', data: onTimeData, backgroundColor: '#0B3D2E', borderRadius: 2, stack: 'a' },
                    { label: 'Terlambat', data: lateData, backgroundColor: '#fbbf24', stack: 'a' },
                    { label: 'Alpa', data: absentData, backgroundColor: '#cbd5e1', borderRadius: { topLeft: 2, topRight: 2 }, stack: 'a' },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E', titleColor: '#a7f3d0', bodyColor: '#fff',
                        borderColor: '#10b981', borderWidth: 1, padding: 10, cornerRadius: 10,
                        mode: 'index'
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, border: { display: false }, ticks: { font: { size: 9 } } },
                    y: { stacked: true, min: 0, max: 100, grid: { color: '#f1f5f9' }, border: { display: false },
                         ticks: { callback: v => v+'%', font: { size: 10 } } }
                }
            }
        });
    }

    // ── 3. Doughnut — Cuti & Izin ─────────────────────────────────────────────
    const hrDonut = document.getElementById('hrDonutChart');
    if (hrDonut && leaveLabels.length > 0) {
        new Chart(hrDonut, {
            type: 'doughnut',
            data: {
                labels: leaveLabels,
                datasets: [{
                    data: leaveCounts,
                    backgroundColor: ['#0B3D2E','#10b981','#6ee7b7','#a7f3d0'],
                    hoverBackgroundColor: ['#0a3328','#059669','#34d399','#86efac'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                cutout: '70%', responsive: false,
                animation: { animateRotate: true, duration: 1400, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E', titleColor: '#a7f3d0', bodyColor: '#fff',
                        borderColor: '#10b981', borderWidth: 1, padding: 10, cornerRadius: 10,
                        callbacks: { label: c => ` ${c.label}: ${c.parsed} pengajuan` }
                    }
                }
            }
        });
    }

    // ── 4. Horizontal Bar — Distribusi Departemen ─────────────────────────────
    const hrDept = document.getElementById('hrDeptChart');
    if (hrDept && deptLabels.length > 0) {
        new Chart(hrDept, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: deptCounts,
                    backgroundColor: ['#0B3D2E', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 1300, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E', titleColor: '#a7f3d0', bodyColor: '#fff',
                        borderColor: '#10b981', borderWidth: 1, padding: 10, cornerRadius: 10,
                        callbacks: {
                            label: c => {
                                const total = deptCounts.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round(c.parsed.x / total * 100) : 0;
                                return ` ${c.parsed.x} Karyawan (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { font: { size: 10 } } },
                    y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endsection