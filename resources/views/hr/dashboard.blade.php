@extends('layouts.hr')

@section('title', 'Dashboard HR')
@section('page-title', 'Dashboard HR')
@section('page-desc', 'Ringkasan performansi tim, tingkat kehadiran, dan persetujuan pengajuan hari ini.')

@section('content')
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- STAT ROW --}}
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-4 rounded-2xl p-6 text-white flex flex-col justify-between animate-dash-card dash-delay-1 shadow-sm hover:shadow-md transition" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between">
                <p class="text-emerald-100/70 text-[10px] font-bold uppercase tracking-widest">Total Karyawan Aktif</p>
                <span class="flex items-center gap-1 text-[10px] font-bold bg-white/10 text-emerald-200 px-2.5 py-1 rounded-full backdrop-blur-xs">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +4% Q3
                </span>
            </div>
            <div class="my-4">
                <p class="text-5xl font-extrabold font-mono-data tracking-tight">1.284</p>
                <p class="text-emerald-100/70 text-xs mt-1">Orang terdaftar — PT Talenta Digital Nusantara</p>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-white/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-[11px] text-emerald-200 font-medium">96,2% presensi tercatat hari ini</span>
            </div>
        </div>

        <div class="col-span-8 card-flat rounded-2xl p-6 grid grid-cols-2 divide-x divide-black/5 animate-dash-card dash-delay-2">
            <div class="pr-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest">Kehadiran Hari Ini</p>
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/50">142 / 150 Staf</span>
                    </div>
                    <p class="text-3xl font-extrabold font-mono-data text-primary mb-1">96,2%</p>
                    <p class="text-xs text-on-surface-variant/60">Tingkat kehadiran shift pagi &amp; reguler</p>
                </div>
                <a href="{{ route('hr.attendance.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-emerald-700 transition mt-3">
                    Lihat rekap presensi lengkap <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

            <div class="pl-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest">Permintaan Tertunda</p>
                        <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/60">12 Pengajuan</span>
                    </div>
                    <p class="text-3xl font-extrabold font-mono-data text-primary mb-1">12</p>
                    <p class="text-xs text-on-surface-variant/60">Cuti · Lembur (SPL) · Klaim Reimbursement</p>
                </div>
                <a href="{{ route('hr.approvals.leave') }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-emerald-700 transition mt-3">
                    Kelola persetujuan tertunda <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    {{-- DASHBOARD VISUAL GRAPHIC GRID (EXACTLY MATCHING USER SPEC & IMAGES) --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- 1. TEAM PERFORMANCE LINE CHART CARD (IMAGE 2 TOP LEFT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-3 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Performansi Tim</h3>
                    <p class="text-xs text-on-surface-variant/50">Tren produktivitas &amp; penyelesaian kerja</p>
                </div>
                <select class="text-xs bg-surface-container border border-black/5 rounded-lg px-2.5 py-1 text-on-surface font-semibold focus:outline-none cursor-pointer">
                    <option>6 Bulan Terakhir</option>
                    <option>Tahun Ini</option>
                </select>
            </div>

            <div class="flex items-baseline gap-3 my-2">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">89,52%</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span> +3.84% vs minggu lalu
                </span>
            </div>

            {{-- SVG Smooth Line Curve Chart with Active Tooltip Point --}}
            <div class="relative mt-2 mb-1">
                <svg viewBox="0 0 300 100" class="w-full h-28 overflow-visible">
                    <defs>
                        <linearGradient id="hrLineGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M 10 65 C 50 55, 80 60, 120 38 C 160 32, 190 50, 230 18 C 260 40, 280 28, 290 32 L 290 95 L 10 95 Z" fill="url(#hrLineGrad)"/>
                    <path class="animate-line-draw" d="M 10 65 C 50 55, 80 60, 120 38 C 160 32, 190 50, 230 18 C 260 40, 280 28, 290 32" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="230" cy="18" r="5" fill="#10b981" stroke="#ffffff" stroke-width="2.5"/>
                </svg>
                <div class="absolute top-0 left-[72%] -translate-x-1/2 bg-white border border-black/10 shadow-md rounded-lg px-2.5 py-1 text-center font-mono-data">
                    <p class="text-[9px] text-on-surface-variant/60 uppercase">Mei 2026</p>
                    <p class="text-xs font-bold text-emerald-600">95.2%</p>
                </div>
            </div>

            <div class="flex justify-between text-[11px] font-mono-data text-on-surface-variant/50 border-t border-black/5 pt-2">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
            </div>
        </div>

        {{-- 2. ATTENDANCE RATE STACKED DAILY BAR CHART (IMAGE 2 TOP RIGHT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold font-mono-data text-on-surface">92%</span>
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50 inline-flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[13px]">arrow_upward</span> +1.54%
                        </span>
                    </div>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Tingkat Kehadiran Harian (Attendance Rate)</p>
                </div>
            </div>

            {{-- Metric Breakdown Header --}}
            <div class="grid grid-cols-3 gap-2 my-2 bg-surface-container/70 p-2.5 rounded-xl border border-black/5">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-6 bg-[#0B3D2E] rounded-sm"></div>
                    <div>
                        <p class="text-[10px] text-on-surface-variant/50 uppercase font-bold">On Time</p>
                        <p class="text-sm font-extrabold font-mono-data text-on-surface">220</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-6 bg-amber-400 rounded-sm"></div>
                    <div>
                        <p class="text-[10px] text-on-surface-variant/50 uppercase font-bold">Late</p>
                        <p class="text-sm font-extrabold font-mono-data text-on-surface">15</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-6 bg-slate-300 rounded-sm"></div>
                    <div>
                        <p class="text-[10px] text-on-surface-variant/50 uppercase font-bold">Absent</p>
                        <p class="text-sm font-extrabold font-mono-data text-on-surface">15</p>
                    </div>
                </div>
            </div>

            {{-- Daily Vertical Bars (01-20) --}}
            <div class="flex items-end justify-between h-20 pt-2 gap-1 overflow-hidden">
                @php
                    $days = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20'];
                @endphp
                @foreach ($days as $idx => $d)
                    @php
                        $onTimeH = rand(70, 90);
                        $lateH = rand(5, 15);
                        $absentH = 100 - $onTimeH - $lateH;
                    @endphp
                    <div class="flex flex-col items-center gap-1 flex-1">
                        <div class="w-full bg-surface-container rounded-t flex flex-col justify-end h-16 overflow-hidden relative">
                            <div class="bg-[#0B3D2E] w-full animate-bar-vertical" style="height: {{ $onTimeH }}%;"></div>
                            <div class="bg-amber-400 w-full animate-bar-vertical" style="height: {{ $lateH }}%;"></div>
                            <div class="bg-slate-300 w-full animate-bar-vertical" style="height: {{ $absentH }}%;"></div>
                        </div>
                        <span class="text-[9px] font-mono-data text-on-surface-variant/40">{{ $d }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. EMPLOYMENT STATUS HORIZONTAL BAR (IMAGE 2 BOTTOM LEFT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Status Kepegawaian</h3>
                    <p class="text-xs text-on-surface-variant/50">Komposisi tipe kontrak seluruh staf</p>
                </div>
                <button class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">more_horiz</span>
                </button>
            </div>

            <div class="flex items-baseline gap-2 my-2">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">537</span>
                <span class="text-xs font-semibold text-on-surface-variant/50">Karyawan Terdaftar</span>
            </div>

            {{-- Multi-Color Stacked Horizontal Progress Bar --}}
            <div class="w-full h-7 bg-surface-container rounded-full overflow-hidden flex my-2 p-0.5 border border-black/5 shadow-inner">
                <div class="h-full bg-[#0B3D2E] rounded-l-full animate-bar-grow" style="width: 65%;" title="Full-Time: 65%"></div>
                <div class="h-full bg-emerald-500 animate-bar-grow" style="width: 25%;" title="Part-Time: 25%"></div>
                <div class="h-full bg-emerald-200 rounded-r-full animate-bar-grow" style="width: 10%;" title="Magang/Probation: 10%"></div>
            </div>

            <div class="flex items-center justify-between text-xs pt-2 border-t border-black/5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#0B3D2E]"></span>
                    <span class="font-bold text-on-surface">Full-Time</span>
                    <span class="text-on-surface-variant/50 font-mono-data">(65%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-on-surface">Part-Time / Kontrak</span>
                    <span class="text-on-surface-variant/50 font-mono-data">(25%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-200"></span>
                    <span class="font-bold text-on-surface">Magang</span>
                    <span class="text-on-surface-variant/50 font-mono-data">(10%)</span>
                </div>
            </div>
        </div>

        {{-- 4. EMPLOYEE SATISFACTION GAUGE CHART (IMAGE 1 & IMAGE 2 BOTTOM RIGHT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-6 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Indeks Kepuasan Karyawan</h3>
                    <p class="text-xs text-on-surface-variant/50">Survei kepuasan kerja &amp; lingkungan</p>
                </div>
                <button class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">more_horiz</span>
                </button>
            </div>

            <div class="grid grid-cols-12 gap-4 items-center my-1">
                {{-- SVG Semi-Circle Gauge Arc --}}
                <div class="col-span-6 flex flex-col items-center relative">
                    <svg viewBox="0 0 100 60" class="w-40 h-24 overflow-visible">
                        {{-- Background Arc Track --}}
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#e2e8f0" stroke-width="11" stroke-linecap="round"/>
                        {{-- Very Satisfied Arc --}}
                        <path d="M 10 50 A 40 40 0 0 1 70 18" fill="none" stroke="#10b981" stroke-width="11" stroke-linecap="round"/>
                        {{-- Somewhat Satisfied Arc --}}
                        <path d="M 70 18 A 40 40 0 0 1 86 38" fill="none" stroke="#a7f3d0" stroke-width="11" stroke-linecap="round"/>
                        {{-- Dissatisfied Arc --}}
                        <path d="M 86 38 A 40 40 0 0 1 90 50" fill="none" stroke="#fca5a5" stroke-width="11" stroke-linecap="round"/>
                        {{-- Needle Indicator Dial --}}
                        <g class="animate-gauge-needle" style="--gauge-deg: 34deg;">
                            <line x1="50" y1="50" x2="50" y2="16" stroke="#191c1d" stroke-width="3.5" stroke-linecap="round"/>
                            <circle cx="50" cy="50" r="5" fill="#191c1d"/>
                        </g>
                    </svg>
                    <div class="text-center -mt-3">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface block">73%</span>
                        <span class="text-[11px] text-on-surface-variant/50 font-medium">avg. satisfaction</span>
                    </div>
                </div>

                {{-- Legend Stats --}}
                <div class="col-span-6 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Very Satisfied</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">421</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Somewhat</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">103</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Dissatisfied</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">13</span>
                    </div>
                </div>
            </div>

            {{-- Green Pill Notification Container (Exact Image 1 & 2) --}}
            <div class="mt-3 bg-emerald-50/90 text-emerald-900 border border-emerald-200/70 rounded-full px-4 py-2 text-xs font-medium flex items-center justify-between">
                <span>That's an <strong class="font-bold text-emerald-950">increase of 6%</strong> from last year</span>
                <span class="material-symbols-outlined text-[16px] text-emerald-600">trending_up</span>
            </div>
        </div>

    </div>

    {{-- SHORTCUT MODUL HR --}}
    <div class="grid grid-cols-3 gap-5 animate-dash-card dash-delay-5">
        <a href="{{ route('hr.approvals.leave') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #FFD700;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Cuti &amp; Izin Karyawan</p>
                <span class="text-[11px] font-mono-data text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">5 pending</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Pengajuan cuti tahunan, sakit, dan izin karyawan yang menunggu persetujuan HR.</p>
        </a>

        <a href="{{ route('hr.approvals.overtime') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #0B3D2E;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Lembur (SPL)</p>
                <span class="text-[11px] font-mono-data text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">4 pending</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Surat perintah lembur karyawan yang telah disetujui Supervisor.</p>
        </a>

        <a href="{{ route('hr.payroll.index') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #10b981;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Penggajian (Payroll)</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/70 bg-surface-container px-2 py-0.5 rounded-full">Periode Agustus</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Proses kalkulasi payroll periode berjalan, BPJS, PPh21, dan slip gaji.</p>
        </a>
    </div>

    {{-- PERSETUJUAN TERTUNDA + DISTRIBUSI STAF --}}
    <div class="grid grid-cols-3 gap-5 animate-dash-card dash-delay-6">

        {{-- TABLE PERSETUJUAN TERTUNDA --}}
        <div class="col-span-2 card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Persetujuan Tertunda Hari Ini</h2>
                    <p class="text-xs text-on-surface-variant/50">Memerlukan persetujuan dari jajaran HR</p>
                </div>
                <a href="{{ route('hr.approvals.leave') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua Persetujuan &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-on-surface-variant/40 border-b border-black/5">
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Karyawan</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Jenis Permintaan</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Tanggal / Period</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    <tr>
                        <td class="py-3.5 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=12" class="w-8 h-8 rounded-full border border-black/10 object-cover" alt="">
                            <div>
                                <p class="font-bold text-on-surface text-xs">Jim Halpert</p>
                                <p class="text-[10px] text-on-surface-variant/50">Sales Department</p>
                            </div>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">CUTI TAHUNAN</span>
                        </td>
                        <td class="text-on-surface-variant/70 font-mono-data text-xs">24–28 Okt</td>
                        <td>
                            <div class="flex gap-2">
                                <form action="{{ route('hr.approvals.dummy-approve') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition" title="Setujui">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                </form>
                                <form action="{{ route('hr.approvals.dummy-reject') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition" title="Tolak">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3.5 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=33" class="w-8 h-8 rounded-full border border-black/10 object-cover" alt="">
                            <div>
                                <p class="font-bold text-on-surface text-xs">Angela Martin</p>
                                <p class="text-[10px] text-on-surface-variant/50">Finance Department</p>
                            </div>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">REIMBURSEMENT</span>
                        </td>
                        <td class="text-on-surface-variant/70 font-mono-data text-xs">21 Okt</td>
                        <td>
                            <div class="flex gap-2">
                                <form action="{{ route('hr.approvals.dummy-approve') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition" title="Setujui">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                </form>
                                <form action="{{ route('hr.approvals.dummy-reject') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition" title="Tolak">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- DISTRIBUSI STAF PER DEPARTEMEN --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface mb-1">Distribusi Staf</h2>
                <p class="text-xs text-on-surface-variant/50 mb-5">Persentase alokasi karyawan per divisi</p>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-on-surface-variant/70">Engineering &amp; IT</span>
                            <span class="font-bold font-mono-data text-on-surface">42% (539 Org)</span>
                        </div>
                        <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-2 rounded-full bg-[#0B3D2E] animate-bar-grow" style="width: 42%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-on-surface-variant/70">Marketing &amp; Product</span>
                            <span class="font-bold font-mono-data text-on-surface">28% (359 Org)</span>
                        </div>
                        <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-2 rounded-full bg-emerald-600 animate-bar-grow" style="width: 28%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-on-surface-variant/70">Sales &amp; Business</span>
                            <span class="font-bold font-mono-data text-on-surface">18% (231 Org)</span>
                        </div>
                        <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-2 rounded-full bg-emerald-400 animate-bar-grow" style="width: 18%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5 font-medium">
                            <span class="text-on-surface-variant/70">Operations &amp; HR</span>
                            <span class="font-bold font-mono-data text-on-surface">12% (155 Org)</span>
                        </div>
                        <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-2 rounded-full bg-amber-400 animate-bar-grow" style="width: 12%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="w-full mt-6 border border-black/10 rounded-xl py-2.5 text-xs font-bold text-primary hover:bg-emerald-50 transition flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                Lihat Analitik Departemen Lengkap
            </button>
        </div>
    </div>

</div>

@endsection