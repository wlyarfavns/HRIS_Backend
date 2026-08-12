@extends('layouts.hr')

@section('title', 'Dashboard HR')
@section('page-title', 'Dashboard HR')
@section('page-desc', 'Ringkasan performansi tim, tingkat kehadiran, dan persetujuan pengajuan hari ini.')

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════
         CHART GRID — 2x2
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- CARD 1: Line Chart — Team Performance --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-1 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Performansi Tim</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Tren produktivitas dan penyelesaian kerja 6 bulan terakhir</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-full whitespace-nowrap">
                    <span class="material-symbols-outlined text-[13px]">arrow_upward</span>+3.84%
                </span>
            </div>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">89.52%</span>
                <span class="text-xs text-on-surface-variant/50">avg. produktivitas</span>
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
                    <span class="material-symbols-outlined text-[13px]">arrow_upward</span>92%
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
            <div class="flex items-center gap-6 flex-1 mt-1">
                <div class="relative shrink-0 flex items-center justify-center" style="width:160px;height:160px;">
                    <canvas id="hrDonutChart" style="width:160px;height:160px;"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">244</span>
                        <span class="text-[10px] text-on-surface-variant/50 mt-0.5">Pengajuan</span>
                    </div>
                </div>
                <div class="flex-1 space-y-2.5">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-[#0B3D2E]"></span><span class="font-semibold text-on-surface">Cuti Tahunan</span></div>
                            <span class="font-bold font-mono-data">55%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-[#0B3D2E] rounded-full animate-bar-grow" style="width:55%"></div></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="font-semibold text-on-surface">Izin Sakit</span></div>
                            <span class="font-bold font-mono-data">25%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full animate-bar-grow" style="width:25%"></div></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-300"></span><span class="font-semibold text-on-surface">Izin Penting</span></div>
                            <span class="font-bold font-mono-data">12%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-300 rounded-full animate-bar-grow" style="width:12%"></div></div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-100"></span><span class="font-semibold text-on-surface">Izin Khusus</span></div>
                            <span class="font-bold font-mono-data">8%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-100 rounded-full animate-bar-grow" style="width:8%"></div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 4: Horizontal Bar — Department Distribution --}}
        <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-4 hover:shadow-md transition flex flex-col">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Distribusi Karyawan per Divisi</h3>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Alokasi staf di 4 departemen utama perusahaan</p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-full whitespace-nowrap">
                    537 Total Staf
                </span>
            </div>
            <div class="flex-1 relative mt-3" style="min-height:190px;">
                <canvas id="hrDeptChart"></canvas>
            </div>
        </div>

    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // ── 1. Line Chart — Team Performance ──────────────────────────────
    const hrLine = document.getElementById('hrLineChart');
    if (hrLine) {
        const g = hrLine.getContext('2d').createLinearGradient(0, 0, 0, 200);
        g.addColorStop(0, 'rgba(16,185,129,0.22)');
        g.addColorStop(1, 'rgba(16,185,129,0)');
        new Chart(hrLine, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],
                datasets: [{
                    label: 'Performansi (%)',
                    data: [76, 81, 79, 85, 95, 90],
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
                        callbacks: { label: c => ` ${c.parsed.y}% produktivitas` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        min: 65, max: 100,
                        grid: { color: '#f1f5f9' }, border: { display: false },
                        ticks: { callback: v => v+'%', font: { size: 10 }, stepSize: 10 }
                    }
                }
            }
        });
    }

    // ── 2. Stacked Bar — Daily Attendance ─────────────────────────────
    const hrBar = document.getElementById('hrBarChart');
    if (hrBar) {
        const days = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20'];
        const onTime  = [88,82,90,85,78,92,87,80,91,86,76,89,93,84,88,81,90,87,79,94];
        const late    = [7,10,6,9,12,5,8,11,5,9,14,7,4,10,8,12,6,9,13,4];
        const absent  = days.map((d,i) => 100 - onTime[i] - late[i]);
        new Chart(hrBar, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [
                    { label: 'Tepat Waktu', data: onTime, backgroundColor: '#0B3D2E', borderRadius: 2, stack: 'a' },
                    { label: 'Terlambat', data: late, backgroundColor: '#fbbf24', stack: 'a' },
                    { label: 'Alpa', data: absent, backgroundColor: '#cbd5e1', borderRadius: { topLeft: 2, topRight: 2 }, stack: 'a' },
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
                    y: { stacked: true, grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { callback: v => v+'%', font: { size: 10 } } }
                }
            }
        });
    }

    // ── 3. Doughnut — Leave & Absence Breakdown ──────────────────────
    const hrDonut = document.getElementById('hrDonutChart');
    if (hrDonut) {
        new Chart(hrDonut, {
            type: 'doughnut',
            data: {
                labels: ['Cuti Tahunan','Izin Sakit','Izin Penting','Izin Khusus'],
                datasets: [{ data: [55,25,12,8], backgroundColor: ['#0B3D2E','#10b981','#6ee7b7','#a7f3d0'],
                    hoverBackgroundColor: ['#0a3328','#059669','#34d399','#86efac'], borderWidth: 3, borderColor: '#fff' }]
            },
            options: {
                cutout: '70%', responsive: false,
                animation: { animateRotate: true, duration: 1400, easing: 'easeInOutQuart' },
                plugins: { legend: { display: false },
                    tooltip: { backgroundColor: '#0B3D2E', titleColor: '#a7f3d0', bodyColor: '#fff', borderColor: '#10b981', borderWidth: 1, padding: 10, cornerRadius: 10,
                        callbacks: { label: c => ` ${c.label}: ${c.parsed}%` }
                    }
                }
            }
        });
    }

    // ── 4. Horizontal Bar — Department Distribution ───────────────────
    const hrDept = document.getElementById('hrDeptChart');
    if (hrDept) {
        new Chart(hrDept, {
            type: 'bar',
            data: {
                labels: ['Engineering & IT', 'Marketing & Product', 'Sales & Business', 'Operations & HR'],
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: [225, 150, 97, 65],
                    backgroundColor: ['#0B3D2E', '#10b981', '#34d399', '#a7f3d0'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1300, easing: 'easeInOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B3D2E',
                        titleColor: '#a7f3d0',
                        bodyColor: '#fff',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: c => ` ${c.parsed.x} Karyawan (${Math.round(c.parsed.x / 537 * 100)}%)`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endsection