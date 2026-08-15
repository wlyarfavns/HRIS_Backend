@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard Super Admin')
@section('page-desc', 'Ringkasan arsitektur sistem, ERD database model, dan manajemen pengguna perusahaan.')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════════
         CHART GRID — 2 kolom
         ═══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- CARD 1: Line Chart — System Activity Trend --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-1 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Tren Log Aktivitas Sistem</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Volume transaksi API &amp; audit trail 6 bulan terakhir</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-full whitespace-nowrap">
                    <span class="material-symbols-outlined text-[13px]">arrow_upward</span> +2.4% Stabilitas
                </span>
            </div>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">99.94%</span>
                <span class="text-xs text-on-surface-variant/50">avg. uptime SLA</span>
            </div>
            <div class="flex-1 relative" style="min-height:200px;">
                <canvas id="adminLineChart"></canvas>
            </div>
            <div class="flex justify-between text-[11px] font-mono-data text-on-surface-variant/40 mt-2 border-t border-black/5 pt-2">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
            </div>
        </div>

        {{-- CARD 2: Doughnut — System Performance SLA --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-2 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">System Performance SLA</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Distribusi tingkat ketersediaan sistem periode berjalan</p>
                </div>
                <button class="text-on-surface-variant/40 hover:text-on-surface transition">
                    <span class="material-symbols-outlined text-[20px]">more_horiz</span>
                </button>
            </div>

            <div class="flex items-center gap-6 flex-1 mt-2">
                {{-- Doughnut Canvas --}}
                <div class="relative shrink-0 flex items-center justify-center" style="width:180px; height:180px;">
                    <canvas id="adminDonutChart" style="width:180px;height:180px;"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">98.4%</span>
                        <span class="text-[10px] text-on-surface-variant/50 font-medium mt-0.5">Optimal SLA</span>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex-1 space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="font-semibold text-on-surface">Optimal SLA</span>
                            </div>
                            <span class="font-bold font-mono-data text-on-surface">98.4%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width:98.4%; animation: barGrow 1.2s 0.5s ease-out backwards;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-200 shrink-0"></span>
                                <span class="font-semibold text-on-surface">Degradation</span>
                            </div>
                            <span class="font-bold font-mono-data text-on-surface">1.2%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-200 rounded-full" style="width:1.2%; animation: barGrow 1.2s 0.7s ease-out backwards;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400 shrink-0"></span>
                                <span class="font-semibold text-on-surface">Offline / Maint.</span>
                            </div>
                            <span class="font-bold font-mono-data text-on-surface">0.4%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-400 rounded-full" style="width:0.4%; animation: barGrow 1.2s 0.9s ease-out backwards;"></div>
                        </div>
                    </div>
                    <div class="mt-4 bg-emerald-50 text-emerald-900 border border-emerald-200/70 rounded-xl px-3 py-2 text-[11px] font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-[15px] text-emerald-600">trending_up</span>
                        <span>Naik <strong>2.4%</strong> stabilitas vs bulan lalu</span>
                    </div>
                </div>
            </div>
        </div>

    </div>





{{-- ─── Chart.js ─── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Shared defaults ──────────────────────────────────────────────
    Chart.defaults.font.family = "'Inter', 'IBM Plex Mono', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // ── 1. Line Chart — System Activity Trend ───────────────────────
    const lineCtx = document.getElementById('adminLineChart');
    if (lineCtx) {
        const lineGrad = lineCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        lineGrad.addColorStop(0, 'rgba(16,185,129,0.22)');
        lineGrad.addColorStop(1, 'rgba(16,185,129,0.0)');

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'SLA Uptime (%)',
                    data: [97.0, 98.2, 96.4, 99.1, 100.0, 99.4],
                    fill: true,
                    backgroundColor: lineGrad,
                    borderColor: '#10b981',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#10b981',
                    tension: 0.42,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E',
                        titleColor: '#a7f3d0',
                        bodyColor: '#ffffff',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y.toFixed(1)}% SLA`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11, family: 'IBM Plex Mono' } }
                    },
                    y: {
                        min: 94,
                        max: 101,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        border: { display: false, dash: [4,4] },
                        ticks: {
                            font: { size: 10, family: 'IBM Plex Mono' },
                            callback: v => v + '%',
                            stepSize: 2
                        }
                    }
                }
            }
        });
    }

    // ── 2. Doughnut Chart — SLA Breakdown ────────────────────────────
    const donutCtx = document.getElementById('adminDonutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Optimal SLA', 'Degradation', 'Offline/Maint.'],
                datasets: [{
                    data: [98.4, 1.2, 0.4],
                    backgroundColor: ['#10b981', '#a7f3d0', '#fca5a5'],
                    hoverBackgroundColor: ['#059669', '#6ee7b7', '#f87171'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderColor: '#ffffff',
                }]
            },
            options: {
                cutout: '72%',
                responsive: false,
                animation: { animateRotate: true, duration: 1400, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E',
                        titleColor: '#a7f3d0',
                        bodyColor: '#ffffff',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed.toFixed(1)}%`
                        }
                    }
                }
            }
        });
    }

    // ── Bar grow animation ────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `
        @keyframes barGrow {
            from { width: 0; }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection