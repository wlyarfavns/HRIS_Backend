@extends('layouts.hr')

@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')
@section('page-desc', 'Profil lengkap, riwayat kontrak, dan ringkasan kepegawaian.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $employee = [
        'nip' => $id ?? 'EMP-00812',
        'full_name' => 'Jim Halpert',
        'nik' => '3374012345670003',
        'email' => 'jim.halpert@talentahr.co.id',
        'phone' => '081234567890',
        'department' => 'Sales',
        'position' => 'Sales Executive',
        'job_grade' => 'JG-1 · Staff',
        'join_date' => '18 Agu 2024',
        'contract_type' => 'PKWT',
        'basic_salary' => 6500000,
        'status' => 'Aktif',
        'avatar' => 12,
        'leave_balance' => 6,
        'leave_quota' => 12,
    ];

    $contracts = [
        ['type' => 'PKWT — Kontrak I', 'range' => '18 Agu 2024 – 17 Agu 2025', 'status' => 'Selesai'],
        ['type' => 'PKWT — Kontrak II (Perpanjangan)', 'range' => '18 Agu 2025 – 17 Sep 2026', 'status' => 'Berjalan'],
    ];

    $recentActivity = [
        ['label' => 'Cuti Tahunan disetujui', 'time' => '3 Agu 2026', 'icon' => 'event_available'],
        ['label' => 'Slip gaji Juli 2026 diunduh', 'time' => '2 Agu 2026', 'icon' => 'description'],
        ['label' => 'Lembur (SPL) 3 jam disetujui', 'time' => '29 Jul 2026', 'icon' => 'schedule'],
    ];
@endphp

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('hr.employees.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>

    {{-- HEADER PROFIL --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-5">
        <img src="https://i.pravatar.cc/72?img={{ $employee['avatar'] }}" class="w-[72px] h-[72px] rounded-full object-cover" alt="{{ $employee['full_name'] }}">
        <div class="flex-1">
            <div class="flex items-center gap-2.5">
                <p class="text-lg font-bold text-on-surface">{{ $employee['full_name'] }}</p>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $employee['contract_type'] === 'PKWTT' ? 'bg-primary/10 text-primary' : 'bg-amber-500/10 text-amber-700' }}">
                    {{ $employee['contract_type'] }}
                </span>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">{{ $employee['status'] }}</span>
            </div>
            <p class="text-sm text-on-surface-variant/60 mt-1">{{ $employee['position'] }} · {{ $employee['department'] }}</p>
            <p class="text-xs text-on-surface-variant/40 font-mono-data mt-1">{{ $employee['nip'] }} · Bergabung {{ $employee['join_date'] }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('hr.employees.documents', $employee['nip']) }}"
               class="border border-black/10 text-on-surface-variant/70 text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 hover:bg-surface-container transition">
                <span class="material-symbols-outlined text-[16px]">folder_open</span>
                Dokumen
            </a>
            <a href="{{ route('hr.employees.edit', $employee['nip']) }}"
               class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- KOLOM KIRI: DATA UTAMA --}}
        <div class="col-span-2 space-y-5">

            {{-- DATA PRIBADI --}}
            <div class="card-flat rounded-2xl p-6">
                <h2 class="text-base font-bold text-on-surface mb-5">Data Pribadi</h2>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">NIK (KTP)</p>
                        <p class="text-sm font-semibold text-on-surface font-mono-data mt-1">{{ $employee['nik'] }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">Email</p>
                        <p class="text-sm font-semibold text-on-surface mt-1">{{ $employee['email'] }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">No. Telepon</p>
                        <p class="text-sm font-semibold text-on-surface font-mono-data mt-1">{{ $employee['phone'] }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">Job Grade</p>
                        <p class="text-sm font-semibold text-on-surface mt-1">{{ $employee['job_grade'] }}</p>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT KONTRAK --}}
            <div class="card-flat rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5">
                    <h2 class="text-base font-bold text-on-surface">Riwayat Masa Berlaku Kontrak</h2>
                </div>
                <div class="divide-y divide-black/5">
                    @foreach ($contracts as $c)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                             {{ $c['status'] === 'Berjalan' ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant/40' }}">
                                    <span class="material-symbols-outlined text-[18px]">assignment</span>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-on-surface">{{ $c['type'] }}</p>
                                    <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $c['range'] }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $c['status'] === 'Berjalan' ? 'bg-primary/10 text-primary' : 'bg-surface-container text-on-surface-variant/50' }}">
                                {{ $c['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- AKTIVITAS TERBARU --}}
            <div class="card-flat rounded-2xl p-6">
                <h2 class="text-base font-bold text-on-surface mb-5">Aktivitas Terbaru</h2>
                <div class="space-y-4">
                    @foreach ($recentActivity as $a)
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">{{ $a['icon'] }}</span>
                            </span>
                            <p class="text-sm text-on-surface-variant/80 flex-1">{{ $a['label'] }}</p>
                            <p class="text-xs text-on-surface-variant/40 font-mono-data">{{ $a['time'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: RINGKASAN --}}
        <div class="space-y-5">
            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">Sisa Kuota Cuti</p>
                <p class="text-3xl font-extrabold font-mono-data text-primary">{{ $employee['leave_balance'] }} <span class="text-sm font-bold text-on-surface-variant/40">/ {{ $employee['leave_quota'] }} hari</span></p>
                <div class="w-full h-1.5 rounded-full bg-surface-container mt-3 overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width: {{ round($employee['leave_balance'] / $employee['leave_quota'] * 100) }}%"></div>
                </div>
            </div>

            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">Gaji Pokok</p>
                <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp{{ number_format($employee['basic_salary'], 0, ',', '.') }}</p>
                <a href="{{ route('hr.payroll.slip', $employee['nip']) }}" class="text-xs font-bold text-primary/70 hover:text-primary transition mt-2 inline-block">Lihat slip gaji terakhir →</a>
            </div>

            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">Dokumen Terunggah</p>
                <div class="space-y-2.5">
                    @foreach (['Scan KTP' => true, 'Scan NPWP' => true, 'Kartu BPJS' => false] as $doc => $done)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-on-surface-variant/70">{{ $doc }}</span>
                            <span class="material-symbols-outlined text-[18px] {{ $done ? 'text-primary' : 'text-on-surface-variant/25' }}">
                                {{ $done ? 'check_circle' : 'radio_button_unchecked' }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('hr.employees.documents', $employee['nip']) }}" class="text-xs font-bold text-primary/70 hover:text-primary transition mt-3 inline-block">Kelola dokumen →</a>
            </div>
        </div>
    </div>

@endsection
