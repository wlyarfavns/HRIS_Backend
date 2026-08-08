@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard Super Admin')
@section('page-desc', 'Ringkasan arsitektur sistem, ERD database model, dan manajemen pengguna perusahaan.')

@php
    $masterTables = ['users', 'roles', 'permissions', 'employees', 'departments', 'positions', 'job_grades', 'shifts', 'leave_types', 'pension_rules'];
    $transactionTables = ['employee_shifts', 'attendances', 'leave_requests', 'overtime_requests', 'reimbursements', 'payroll_runs', 'payroll_details', 'payslips'];
    $auditTables = ['activity_logs', 'approval_histories', 'leave_balances', 'notifications', 'payslip_access_logs'];

    $schemaPreviews = [
        ['table' => 'employees', 'fields' => 'id, nip, full_name, email, department_id, position_id, join_date, basic_salary', 'constraints' => 'nip & email unique; foreign key ke dept & position'],
        ['table' => 'attendances', 'fields' => 'id, employee_id, date, clock_in, clock_out, lat_in, long_in, photo_in, status', 'constraints' => 'unique(employee_id, date); status enum'],
        ['table' => 'leave_requests', 'fields' => 'id, employee_id, leave_type_id, start_date, end_date, reason, status', 'constraints' => 'status: pending_spv, pending_hr, approved, rejected'],
        ['table' => 'payroll_runs', 'fields' => 'id, period_name, start_date, end_date, total_gross, total_net, status', 'constraints' => 'status: draft, hr_approved, finance_approved, disbursed'],
    ];
@endphp

@section('content')
<div x-data="{
    activeTab: 'Overview',
    showErdModal: false
}" class="space-y-6">

    {{-- TOP STAT ROW --}}
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-4 rounded-2xl p-6 text-white flex flex-col justify-between animate-dash-card dash-delay-1 shadow-sm hover:shadow-md transition" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between">
                <p class="text-emerald-100/70 text-[10px] font-bold uppercase tracking-widest">Total Karyawan Aktif</p>
                <span class="flex items-center gap-1 text-[10px] font-bold bg-white/10 text-emerald-200 px-2.5 py-1 rounded-full backdrop-blur-xs">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +4% Q3
                </span>
            </div>
            <div class="my-4">
                <p class="text-5xl font-extrabold font-mono-data tracking-tight">1.284 Org</p>
                <p class="text-emerald-100/70 text-xs mt-1">Seluruh sistem — PT Talenta Digital Nusantara</p>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-white/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-[11px] text-emerald-200 font-medium">Sistem Server 99.9% Uptime Compliance</span>
            </div>
        </div>

        <div class="col-span-8 card-flat rounded-2xl p-6 grid grid-cols-3 divide-x divide-black/5 animate-dash-card dash-delay-2">
            <div class="pr-4 flex flex-col justify-between">
                <div>
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest mb-2">Departemen</p>
                    <p class="text-3xl font-extrabold font-mono-data text-primary mb-1">4 Divisi</p>
                    <p class="text-xs text-on-surface-variant/60">Struktur organisasi HRIS</p>
                </div>
                <a href="{{ route('admin.company.index') }}" class="text-xs font-bold text-primary hover:text-emerald-700 transition inline-flex items-center gap-1 mt-2">
                    Profil Perusahaan &rarr;
                </a>
            </div>
            <div class="px-4 flex flex-col justify-between">
                <div>
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest mb-2">Pengguna Sistem</p>
                    <p class="text-3xl font-extrabold font-mono-data text-primary mb-1">24 Akun</p>
                    <p class="text-xs text-on-surface-variant/60">Admin, HR, SPV &amp; Finance</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-primary hover:text-emerald-700 transition inline-flex items-center gap-1 mt-2">
                    Kelola Akses Role &rarr;
                </a>
            </div>
            <div class="pl-4 flex flex-col justify-between">
                <div>
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest mb-2">Database Model</p>
                    <p class="text-3xl font-extrabold font-mono-data text-on-surface mb-1">23 Tabel</p>
                    <p class="text-xs text-on-surface-variant/60">Normalized ERD Relational</p>
                </div>
                <button type="button" @click="showErdModal = true" class="text-xs font-bold text-primary hover:text-emerald-700 transition inline-flex items-center gap-1 mt-2 text-left">
                    Lihat ERD Blueprint &rarr;
                </button>
            </div>
        </div>
    </div>

    {{-- DASHBOARD VISUAL GRAPHIC GRID (EXACTLY MATCHING USER SPEC & IMAGES) --}}
    <div class="grid grid-cols-2 gap-5">

        {{-- 1. SYSTEM ACTIVITY TREND LINE CHART (IMAGE 2 TOP LEFT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-3 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">Tren Log Aktivitas Sistem</h3>
                    <p class="text-xs text-on-surface-variant/50">Volume transaksi API &amp; audit trail harian</p>
                </div>
                <select class="text-xs bg-surface-container border border-black/5 rounded-lg px-2.5 py-1 text-on-surface font-semibold focus:outline-none cursor-pointer">
                    <option>6 Bulan Terakhir</option>
                    <option>Tahun Ini</option>
                </select>
            </div>

            <div class="flex items-baseline gap-3 my-2">
                <span class="text-3xl font-extrabold font-mono-data text-on-surface">99,94%</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 rounded-full">
                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span> +2,4% Stabilitas
                </span>
            </div>

            <div class="relative mt-2 mb-1">
                <svg viewBox="0 0 300 100" class="w-full h-28 overflow-visible">
                    <defs>
                        <linearGradient id="adminLineGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M 10 70 C 50 60, 80 65, 120 35 C 160 30, 190 48, 230 16 C 260 38, 280 25, 290 28 L 290 95 L 10 95 Z" fill="url(#adminLineGrad)"/>
                    <path class="animate-line-draw" d="M 10 70 C 50 60, 80 65, 120 35 C 160 30, 190 48, 230 16 C 260 38, 280 25, 290 28" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="230" cy="16" r="5" fill="#10b981" stroke="#ffffff" stroke-width="2.5"/>
                </svg>
                <div class="absolute top-0 left-[72%] -translate-x-1/2 bg-white border border-black/10 shadow-md rounded-lg px-2.5 py-1 text-center font-mono-data">
                    <p class="text-[9px] text-on-surface-variant/60 uppercase">Mei 2026</p>
                    <p class="text-xs font-bold text-emerald-600">99.8% SLA</p>
                </div>
            </div>

            <div class="flex justify-between text-[11px] font-mono-data text-on-surface-variant/50 border-t border-black/5 pt-2">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
            </div>
        </div>

        {{-- 2. SYSTEM HEALTH & SLA GAUGE CHART (IMAGE 1 & IMAGE 2 BOTTOM RIGHT) --}}
        <div class="card-flat rounded-2xl p-6 flex flex-col justify-between animate-dash-card dash-delay-4 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-base font-bold text-on-surface">System Performance SLA</h3>
                    <p class="text-xs text-on-surface-variant/50">Ketersediaan &amp; respon waktu database</p>
                </div>
                <button class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">more_horiz</span>
                </button>
            </div>

            <div class="grid grid-cols-12 gap-4 items-center my-1">
                {{-- SVG Semi-Circle Gauge Arc --}}
                <div class="col-span-6 flex flex-col items-center relative">
                    <svg viewBox="0 0 100 60" class="w-40 h-24 overflow-visible">
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="#e2e8f0" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 10 50 A 40 40 0 0 1 76 14" fill="none" stroke="#10b981" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 76 14 A 40 40 0 0 1 87 38" fill="none" stroke="#a7f3d0" stroke-width="11" stroke-linecap="round"/>
                        <path d="M 87 38 A 40 40 0 0 1 90 50" fill="none" stroke="#fca5a5" stroke-width="11" stroke-linecap="round"/>
                        <g class="animate-gauge-needle" style="--gauge-deg: 42deg;">
                            <line x1="50" y1="50" x2="50" y2="16" stroke="#191c1d" stroke-width="3.5" stroke-linecap="round"/>
                            <circle cx="50" cy="50" r="5" fill="#191c1d"/>
                        </g>
                    </svg>
                    <div class="text-center -mt-3">
                        <span class="text-2xl font-extrabold font-mono-data text-on-surface block">98.4%</span>
                        <span class="text-[11px] text-on-surface-variant/50 font-medium">avg. uptime SLA</span>
                    </div>
                </div>

                {{-- Legend Stats --}}
                <div class="col-span-6 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Optimal SLA</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">98.4%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Degradation</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">1.2%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></span>
                            <span class="text-on-surface-variant/70">Offline/Maint</span>
                        </div>
                        <span class="font-bold font-mono-data text-on-surface">0.4%</span>
                    </div>
                </div>
            </div>

            {{-- Green Pill Notification Container (Image 1) --}}
            <div class="mt-3 bg-emerald-50/90 text-emerald-900 border border-emerald-200/70 rounded-full px-4 py-2 text-xs font-medium flex items-center justify-between">
                <span>That's an <strong class="font-bold text-emerald-950">increase of 2.4%</strong> stability from last month</span>
                <span class="material-symbols-outlined text-[16px] text-emerald-600">trending_up</span>
            </div>
        </div>

    </div>

    {{-- DATABASE & ERD HRIS SYSTEM BLUEPRINT SECTION --}}
    <div class="card-flat rounded-2xl p-6 animate-dash-card dash-delay-5">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-on-surface">Database &amp; ERD HRIS System (Blueprint V1.0)</h2>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">Data Model</span>
                </div>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Kelompok tabel master data HR, transaksi presensi/payroll, dan audit trail log</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="showErdModal = true"
                        class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm transition">
                    <span class="material-symbols-outlined text-[16px]">account_tree</span>
                    Buka Full Data Model
                </button>
            </div>
        </div>

        {{-- 3 TABLE GROUPS --}}
        <div class="grid grid-cols-3 gap-5">
            {{-- 1. MASTER --}}
            <div class="p-4 rounded-xl bg-surface-container/60 border border-black/5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-on-surface uppercase tracking-wide">Master Data</h3>
                    <span class="text-[10px] font-mono-data text-on-surface-variant/50">{{ count($masterTables) }} Tabel</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($masterTables as $t)
                        <span class="text-[11px] font-mono-data px-2.5 py-1 rounded-lg bg-white border border-black/5 text-primary font-bold shadow-xs">
                            {{ $t }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- 2. TRANSACTIONS --}}
            <div class="p-4 rounded-xl bg-surface-container/60 border border-black/5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-on-surface uppercase tracking-wide">Transactions</h3>
                    <span class="text-[10px] font-mono-data text-on-surface-variant/50">{{ count($transactionTables) }} Tabel</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($transactionTables as $t)
                        <span class="text-[11px] font-mono-data px-2.5 py-1 rounded-lg bg-white border border-black/5 text-on-surface font-semibold shadow-xs">
                            {{ $t }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- 3. AUDIT & SYSTEM --}}
            <div class="p-4 rounded-xl bg-surface-container/60 border border-black/5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-on-surface uppercase tracking-wide">Audit &amp; System</h3>
                    <span class="text-[10px] font-mono-data text-on-surface-variant/50">{{ count($auditTables) }} Tabel</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($auditTables as $t)
                        <span class="text-[11px] font-mono-data px-2.5 py-1 rounded-lg bg-white border border-black/5 text-purple-700 font-semibold shadow-xs">
                            {{ $t }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ERD ASCII / CODE RELATIONS VIEWER --}}
        <div class="mt-5 p-4 rounded-xl bg-slate-950 text-white font-mono-data text-xs space-y-1 overflow-x-auto shadow-inner border border-slate-800">
            <p class="text-brand-gold font-bold mb-2">// Relasi Entitas Database HRIS &amp; ERP Mekari Talenta</p>
            <p class="text-emerald-300 font-medium">departments &mdash;&lt; positions &mdash;&lt; employees &mdash;&gt; shifts</p>
            <p class="text-slate-300 pl-24">|&mdash;&mdash;&mdash;&mdash; attendances</p>
            <p class="text-slate-300 pl-24">|&mdash;&mdash;&mdash;&mdash; leave_requests &mdash;&lt; approval_histories</p>
            <p class="text-slate-300 pl-24">|&mdash;&mdash;&mdash;&mdash; overtime_requests</p>
            <p class="text-slate-300 pl-24">|&mdash;&mdash;&mdash;&mdash; reimbursements</p>
            <p class="text-emerald-300 pt-1">payroll_runs &mdash;&lt; payroll_details &mdash;&lt; payslips</p>
            <p class="text-slate-300">employees &mdash;&lt; leave_balances</p>
            <p class="text-slate-300">users &mdash;&lt; activity_logs / payslip_access_logs</p>
        </div>

        {{-- SCHEMA FIELD INTI & CONSTRAINT PREVIEW --}}
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-4 py-2.5">Tabel</th>
                        <th class="px-4 py-2.5">Field Inti (Columns)</th>
                        <th class="px-4 py-2.5">Constraint Penting</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($schemaPreviews as $sp)
                        <tr>
                            <td class="px-4 py-2.5 font-mono-data font-bold text-primary">{{ $sp['table'] }}</td>
                            <td class="px-4 py-2.5 font-mono-data text-on-surface-variant/80">{{ $sp['fields'] }}</td>
                            <td class="px-4 py-2.5 text-on-surface-variant/60 font-mono-data text-[11px]">{{ $sp['constraints'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL FULL ERD DETAILS --}}
    <div x-show="showErdModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-xs"
         @click.self="showErdModal = false">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">database</span>
                    <h3 class="text-base font-bold text-on-surface">Data Model &amp; Architecture Spec V1.0</h3>
                </div>
                <button type="button" @click="showErdModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3 text-xs leading-relaxed text-on-surface-variant/70">
                <p>
                    Arsitektur HRIS didesain dengan prinsip integritas referensial ketat:
                    Setiap transaksi cuti dan lembur memiliki jejak audit otomatis pada tabel <strong class="font-mono-data text-on-surface">approval_histories</strong>. Rekap penggajian mengunci data presensi dan SPL yang valid untuk menghasilkan <strong class="font-mono-data text-on-surface">payroll_details</strong> dan <strong class="font-mono-data text-on-surface">payslips</strong> digital.
                </p>
                <div class="p-3.5 rounded-xl bg-surface-container border border-black/5 font-mono-data text-[11px] space-y-1">
                    <p class="font-bold text-primary">// Engine Compliance:</p>
                    <p>&bull; Haversine Formula for Geolocation radius verification</p>
                    <p>&bull; TER Depkeu PP 58/2023 for real-time PPh 21 calculation</p>
                    <p>&bull; Depnaker overtime formula: 1/173 &times; Basic Salary &times; Hours</p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-2 border-t border-black/5">
                <button type="button" @click="showErdModal = false"
                        class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm transition">
                    Tutup Dokumentasi
                </button>
            </div>
        </div>
    </div>

</div>
@endsection