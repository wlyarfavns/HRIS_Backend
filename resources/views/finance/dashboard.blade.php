@extends('layouts.finance')

@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')
@section('page-desc', 'Ringkasan klaim penggantian biaya, persetujuan penggajian, dan pencairan dana hari ini.')

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════
         CHART GRID — 2 kolom
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- CARD 1: Line Chart — Reimbursement Trend --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-1 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Tren Klaim Reimbursement</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Volume pengeluaran operasional 6 bulan terakhir dalam juta Rupiah</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold {{ $trendPercent <= 0 ? 'text-emerald-700 bg-emerald-50 border-emerald-200/60' : 'text-rose-700 bg-rose-50 border-rose-200/60' }} border px-2.5 py-1 rounded-full whitespace-nowrap">
                    <span class="material-symbols-outlined text-[13px]">{{ $trendPercent <= 0 ? 'arrow_downward' : 'arrow_upward' }}</span>{{ $trendPercent }}%
                </span>
            </div>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">{{ $trendTotalFormatted }}</span>
                <span class="text-xs text-on-surface-variant/50">total bulan ini</span>
            </div>
            <div class="flex-1 relative" style="min-height:190px;">
                <canvas id="finLineChart"></canvas>
            </div>
        </div>

        {{-- CARD 2: Doughnut — Disbursement SLA --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-2 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">SLA Verifikasi &amp; Pencairan</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Tingkat ketepatan waktu pembayaran periode berjalan</p>
                </div>
                <button class="text-on-surface-variant/40 hover:text-on-surface transition">
                    <span class="material-symbols-outlined text-[20px]">more_horiz</span>
                </button>
            </div>

            <div class="flex items-center gap-6 flex-1 mt-2">
                <div class="relative shrink-0 flex items-center justify-center" style="width:170px;height:170px;">
                    <canvas id="finDonutChart" style="width:170px;height:170px;"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $slaOnTimePercent }}%</span>
                        <span class="text-[10px] text-on-surface-variant/50 mt-0.5">Tepat Waktu</span>
                    </div>
                </div>
                <div class="flex-1 space-y-3.5">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="font-semibold text-on-surface">Terverifikasi</span></div>
                            <span class="font-bold font-mono-data">{{ $verifiedCount }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full animate-bar-grow" style="width:{{ round($verifiedCount / $totalSla * 100, 1) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="font-semibold text-on-surface">Pending Finance</span></div>
                            <span class="font-bold font-mono-data">{{ $pendingCount }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-amber-400 rounded-full animate-bar-grow" style="width:{{ round($pendingCount / $totalSla * 100, 1) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-400"></span><span class="font-semibold text-on-surface">Ditolak</span></div>
                            <span class="font-bold font-mono-data">{{ $rejectedCount }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-rose-400 rounded-full animate-bar-grow" style="width:{{ round($rejectedCount / $totalSla * 100, 1) }}%"></div></div>
                    </div>

                    @if ($verifiedCount > 0)
                    <div class="mt-1 bg-emerald-50 text-emerald-900 border border-emerald-200/70 rounded-xl px-3 py-2 text-[11px] font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-[15px] text-emerald-600">trending_up</span>
                        <span>{{ $verifiedCount }} klaim telah diverifikasi bulan ini</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         PAYROLL STATUS + REIMBURSEMENT BY DEPT
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-5 animate-dash-card dash-delay-3">

        {{-- Reimbursement per Departemen --}}
        <div class="col-span-2 card-flat rounded-2xl p-6 flex flex-col">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Reimbursement per Departemen</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Perbandingan total klaim pengeluaran per divisi bulan ini</p>
                </div>
            </div>
            <div class="flex-1 relative" style="min-height:160px;">
                @if ($deptLabels->isEmpty())
                    <div class="absolute inset-0 flex items-center justify-center text-xs text-on-surface-variant/40">
                        Belum ada klaim terverifikasi pada periode ini.
                    </div>
                @else
                    <canvas id="finDeptChart"></canvas>
                @endif
            </div>
        </div>

        {{-- Quick Finance Stats --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-on-surface mb-1">Ringkasan Keuangan</h3>
                <p class="text-xs text-on-surface-variant/50 mb-5">Periode {{ $periodLabel }}</p>
                <div class="space-y-4">
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200/50">
                        <p class="text-[10px] text-emerald-700/70 font-bold uppercase tracking-wider mb-1">Gaji Bersih Bulan Ini</p>
                        <p class="text-xl font-extrabold font-mono-data text-emerald-900">{{ $netPayrollFormatted }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container border border-black/5">
                        <p class="text-[10px] text-on-surface-variant/50 font-bold uppercase tracking-wider mb-1">Total Klaim Penggantian Biaya</p>
                        <p class="text-xl font-extrabold font-mono-data text-on-surface">{{ $totalReimburseFormatted }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/50">
                        <p class="text-[10px] text-amber-700/70 font-bold uppercase tracking-wider mb-1">Menunggu Pencairan</p>
                        <p class="text-xl font-extrabold font-mono-data text-amber-900">{{ $pendingCountLabel }} Klaim</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('finance.reimbursement.index') }}" class="w-full mt-4 bg-primary text-white text-xs font-bold py-2.5 rounded-xl hover:brightness-110 transition flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">payments</span>Kelola Pencairan
            </a>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    const trendLabels = @json($trendLabels);
    const trendData = @json($trendData);
    const slaData = [{{ $verifiedCount }}, {{ $pendingCount }}, {{ $rejectedCount }}];
    const deptLabels = @json($deptLabels);
    const deptData = @json($deptData);

    // ── 1. Line Chart — Reimbursement Trend ──────────────────────────
    const finLine = document.getElementById('finLineChart');
    if (finLine) {
        const g = finLine.getContext('2d').createLinearGradient(0, 0, 0, 200);
        g.addColorStop(0, 'rgba(16,185,129,0.22)');
        g.addColorStop(1, 'rgba(16,185,129,0)');
        new Chart(finLine, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Klaim (Rp Juta)',
                    data: trendData,
                    fill: true, backgroundColor: g,
                    borderColor: '#10b981', borderWidth: 2.5,
                    pointBackgroundColor: '#fff', pointBorderColor: '#10b981',
                    pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 7,
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
                        callbacks: { label: c => ` Rp${c.parsed.y} Jt` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        grid: { color: '#f1f5f9' }, border: { display: false },
                        ticks: { callback: v => 'Rp' + v + 'Jt', font: { size: 10 } },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ── 2. Doughnut — SLA Breakdown ──────────────────────────────────
    const finDonut = document.getElementById('finDonutChart');
    if (finDonut) {
        new Chart(finDonut, {
            type: 'doughnut',
            data: {
                labels: ['Terverifikasi', 'Pending', 'Ditolak'],
                datasets: [{
                    data: slaData,
                    backgroundColor: ['#10b981', '#fbbf24', '#fca5a5'],
                    hoverBackgroundColor: ['#059669', '#f59e0b', '#f87171'],
                    borderWidth: 3, borderColor: '#fff'
                }]
            },
            options: {
                cutout: '72%', responsive: false,
                animation: { animateRotate: true, duration: 1400, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E', titleColor: '#a7f3d0', bodyColor: '#fff',
                        borderColor: '#10b981', borderWidth: 1, padding: 10, cornerRadius: 10
                    }
                }
            }
        });
    }

    // ── 3. Horizontal Bar — Reimbursement by Dept ────────────────────
    const finDept = document.getElementById('finDeptChart');
    if (finDept && deptLabels.length > 0) {
        new Chart(finDept, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Total Klaim (Rp Juta)',
                    data: deptData,
                    backgroundColor: ['#0B3D2E', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'],
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
                        callbacks: { label: c => ` Rp${c.parsed.x} Jt` }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9' }, border: { display: false },
                        ticks: { callback: v => 'Rp' + v + 'Jt', font: { size: 10 } },
                        beginAtZero: true
                    },
                    y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endsection