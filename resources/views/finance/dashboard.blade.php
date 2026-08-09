@extends('layouts.finance')

@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')
@section('page-desc', 'Ringkasan klaim reimbursement, approval payroll, dan pencairan dana hari ini.')

@section('content')

<div class="space-y-6">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-4 rounded-2xl p-6 text-white flex flex-col justify-between animate-dash-card dash-delay-1 shadow-sm hover:shadow-md transition" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between gap-2">
                <p class="text-emerald-100/70 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Total Gaji Bersih (Net)</p>
                <span class="flex items-center gap-1 text-[10px] font-bold bg-white/10 text-emerald-200 px-2.5 py-1 rounded-full whitespace-nowrap shrink-0 backdrop-blur-xs">
                    <span class="material-symbols-outlined text-[14px]">payments</span> Agu 2026
                </span>
            </div>
            <div class="my-4">
                <p class="text-4xl font-extrabold font-mono-data tracking-tight whitespace-nowrap">Rp1,24 M</p>
                <p class="text-emerald-100/70 text-xs font-mono-data mt-1">Rp1.240.500.000 (1.284 Karyawan)</p>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-white/10">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                <span class="text-[11px] text-emerald-200 font-medium">Status: Menunggu Approval Finance</span>
            </div>
        </div>

        <div class="col-span-8 card-flat rounded-2xl p-6 grid grid-cols-2 divide-x divide-black/5 animate-dash-card dash-delay-2">
            <div class="pr-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Klaim Pending</p>
                        <span class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200 whitespace-nowrap shrink-0">Verifikasi</span>
                    </div>
                    <p class="text-3xl font-extrabold font-mono-data text-primary mb-1">Rp14,25 Jt</p>
                    <p class="text-xs text-on-surface-variant/60">23 klaim reimbursement baru</p>
                </div>
                <a href="{{ route('finance.reimbursement.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-emerald-700 transition mt-3">
                    Verifikasi klaim reimbursement <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

            <div class="pl-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Payroll Menunggu</p>
                        <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 whitespace-nowrap shrink-0">HR Approved</span>
                    </div>
                    <p class="text-3xl font-extrabold font-mono-data text-primary mb-1">1 Batch</p>
                    <p class="text-xs text-on-surface-variant/60">Payroll Run Periode Agustus 2026</p>
                </div>
                <a href="{{ route('finance.payroll.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-emerald-700 transition mt-3">
                    Tinjau komponen &amp; persetujuan <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    {{-- DASHBOARD VISUAL GRAPHIC GRID (EXACTLY MATCHING USER SPEC & IMAGES) --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- 1. REIMBURSEMENT TREND LINE CHART (IMAGE 2 TOP LEFT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-3 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Tren Klaim Reimbursement</h3>
                    <p class="text-xs text-on-surface-variant/50">Volume pengeluaran operasional bulanan</p>
                </div>
                <select class="text-xs bg-surface-container border border-black/5 rounded-lg px-2.5 py-1 text-on-surface font-semibold focus:outline-none cursor-pointer">
                    <option>6 Bulan Terakhir</option>
                    <option>Tahun Ini</option>
                </select>
            </div>

            <div class="flex items-baseline gap-3 my-2">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">Rp14,25 Jt</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">arrow_downward</span> -4.2% vs bulan lalu
                </span>
            </div>

            <div class="relative mt-2 mb-1">
                <svg viewBox="0 0 300 100" class="w-full h-28 overflow-visible">
                    <defs>
                        <linearGradient id="finLineGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M 10 50 C 50 65, 80 40, 120 55 C 160 30, 190 45, 230 20 C 260 35, 280 25, 290 30 L 290 95 L 10 95 Z" fill="url(#finLineGrad)"/>
                    <path class="animate-line-draw" d="M 10 50 C 50 65, 80 40, 120 55 C 160 30, 190 45, 230 20 C 260 35, 280 25, 290 30" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="230" cy="20" r="5" fill="#10b981" stroke="#ffffff" stroke-width="2.5"/>
                </svg>
                <div class="absolute top-0 left-[72%] -translate-x-1/2 bg-white border border-black/10 shadow-md rounded-lg px-2.5 py-1 text-center font-mono-data">
                    <p class="text-[9px] text-on-surface-variant/60 uppercase">Mei 2026</p>
                    <p class="text-xs font-bold text-emerald-600">Rp18,4 Jt</p>
                </div>
            </div>

            <div class="flex justify-between text-[11px] font-mono-data text-on-surface-variant/50 border-t border-black/5 pt-2">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
            </div>
        </div>

        {{-- 2. DISBURSEMENT SLA & REIMBURSEMENT GAUGE (IMAGE 1 & IMAGE 2 BOTTOM RIGHT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">SLA Verifikasi &amp; Pencairan</h3>
                    <p class="text-xs text-on-surface-variant/50">Tingkat ketepatan waktu pembayaran</p>
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
                        <path d="M 10 50 A 40 40 0 0 1 75 16" fill="none" stroke="#10b981" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 75 16 A 40 40 0 0 1 86 38" fill="none" stroke="#a7f3d0" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 86 38 A 40 40 0 0 1 90 50" fill="none" stroke="#fca5a5" stroke-width="11" stroke-linecap="round"/>
                        <g class="animate-gauge-needle" style="--gauge-deg: 40deg;">
                            <line x1="50" y1="50" x2="50" y2="16" stroke="#191c1d" stroke-width="3.5" stroke-linecap="round"/>
                            <circle cx="50" cy="50" r="5" fill="#191c1d"/>
                        </g>
                    </svg>
                    <div class="text-center -mt-3">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface block">94%</span>
                        <span class="text-[11px] text-on-surface-variant/50 font-medium">pencairan tepat waktu</span>
                    </div>
                </div>

                {{-- Legend Stats --}}
                <div class="col-span-6 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Terverifikasi</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">180</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Pending Fin</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">23</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Ditolak</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">4</span>
                    </div>
                </div>
            </div>

            {{-- Green Pill Notification Container (Image 1) --}}
            <div class="mt-3 bg-emerald-50/90 text-emerald-900 border border-emerald-200/70 rounded-full px-4 py-2 text-xs font-medium flex items-center justify-between">
                <span>Pencairan reimbursement <strong class="font-bold text-emerald-950">+15% lebih cepat</strong> bulan ini</span>
                <span class="material-symbols-outlined text-[16px] text-emerald-600">trending_up</span>
            </div>
        </div>

    </div>

    {{-- SHORTCUT MODUL FINANCE --}}
    <div class="grid grid-cols-4 gap-5 animate-dash-card dash-delay-5">
        <a href="{{ route('finance.reimbursement.index') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #FFD700;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Reimbursement</p>
                <span class="text-[11px] font-mono-data text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 whitespace-nowrap shrink-0">23 klaim</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Verifikasi klaim pengeluaran karyawan sebelum dana dicairkan.</p>
        </a>

        <a href="{{ route('finance.payroll.index') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #0B3D2E;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Approval Payroll</p>
                <span class="text-[11px] font-mono-data text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 whitespace-nowrap shrink-0">1 pending</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Cek PPh21, BPJS, &amp; komponen gaji sebelum disetujui.</p>
        </a>

        <a href="{{ route('finance.export.index') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #10b981;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Export Bank</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/70 bg-surface-container px-2 py-0.5 rounded-full whitespace-nowrap shrink-0">1.284 rek.</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Buat file transfer massal sesuai format BCA, Mandiri, BNI, BRI.</p>
        </a>

        <a href="{{ route('finance.disbursement.index') }}" class="card-flat rounded-xl p-5 block hover:shadow-md transition" style="border-left: 4px solid #34d399;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Disbursement</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/70 bg-surface-container px-2 py-0.5 rounded-full whitespace-nowrap shrink-0">Riwayat</span>
            </div>
            <p class="text-xs text-on-surface-variant/60">Riwayat pencairan dana &amp; distribusi slip gaji digital.</p>
        </a>
    </div>

    {{-- PENGAJUAN MENUNGGU + AUDIT TRAIL --}}
    <div class="grid grid-cols-3 gap-5 animate-dash-card dash-delay-6">

        {{-- PENGAJUAN MENUNGGU FINANCE --}}
        <div class="col-span-2 card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Pengajuan Menunggu Finance</h2>
                    <p class="text-xs text-on-surface-variant/50">Memerlukan verifikasi &amp; persetujuan pencairan</p>
                </div>
                <a href="{{ route('finance.reimbursement.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua Pengajuan &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-on-surface-variant/40 border-b border-black/5">
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Karyawan / Batch</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Tipe</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Nominal</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    <tr>
                        <td class="py-3.5 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=44" class="w-8 h-8 rounded-full border border-black/10 object-cover" alt="">
                            <div>
                                <p class="font-bold text-on-surface text-xs">Siti Aminah</p>
                                <p class="text-[10px] text-on-surface-variant/50">Bensin &amp; Parkir Client</p>
                            </div>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 whitespace-nowrap">REIMBURSEMENT</span>
                        </td>
                        <td class="text-on-surface-variant/80 font-mono-data text-xs font-bold whitespace-nowrap">Rp350.000</td>
                        <td>
                            <div class="flex gap-1.5">
                                <button title="Setujui" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </button>
                                <button title="Tolak" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border border-rose-200 flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3.5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                PR
                            </div>
                            <div>
                                <p class="font-bold text-on-surface text-xs">Payroll Agustus 2026</p>
                                <p class="text-[10px] text-on-surface-variant/50">1.284 Rekening Karyawan</p>
                            </div>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap">PAYROLL RUN</span>
                        </td>
                        <td class="text-on-surface-variant/80 font-mono-data text-xs font-bold whitespace-nowrap">Rp1,24 M</td>
                        <td>
                            <a href="{{ route('finance.payroll.index') }}" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-0.5">
                                Tinjau <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- AUDIT TRAIL TIMELINE --}}
        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-1">Audit Trail Terbaru</h2>
            <p class="text-xs text-on-surface-variant/50 mb-5">Jejak log aktivitas keuangan</p>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mt-1 shrink-0"></span>
                    <div>
                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">10.01 Hari Ini</p>
                        <p class="text-xs font-bold text-on-surface leading-snug">Reimbursement Siti Aminah (Rp350k) diverifikasi finance.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 mt-1 shrink-0"></span>
                    <div>
                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">09.40 Hari Ini</p>
                        <p class="text-xs font-bold text-on-surface leading-snug">Payroll Agustus 2026 diterima dari HR, menunggu approval finance.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mt-1 shrink-0"></span>
                    <div>
                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">Kemarin</p>
                        <p class="text-xs font-bold text-on-surface leading-snug">Export CSV BCA payroll Juli 2026 berhasil diunduh.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mt-1 shrink-0"></span>
                    <div>
                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">Kemarin</p>
                        <p class="text-xs font-bold text-on-surface leading-snug">1.276 slip gaji digital berhasil didistribusikan ke aplikasi karyawan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection