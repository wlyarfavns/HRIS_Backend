@extends('layouts.hr')
    @php
        /** @var \App\Models\Employee $employee */
        /** @var \Illuminate\Support\Collection $contracts */
        /** @var \Illuminate\Support\Collection $recentActivity */
        /** @var int $leaveBalance */
        /** @var int $leaveQuota */
        /** @var array<string,bool> $documents */
    @endphp

@section('title', 'Detail Karyawan — ' . $employee->full_name)
@section('page-title', 'Detail Karyawan')
@section('page-desc', 'Profil lengkap, riwayat kontrak, dan ringkasan kepegawaian.')

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('hr.employees.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
                   hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>

    {{-- FLASH SUCCESS --}}
    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm p-3.5 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── HEADER PROFIL ──────────────────────────────────────────────────── --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-5 mb-5">

        {{-- Avatar inisial (tidak pakai gambar eksternal) --}}
        <div class="w-[72px] h-[72px] rounded-full bg-primary/10 text-primary
                        flex items-center justify-center text-2xl font-extrabold shrink-0 uppercase">
            {{ substr($employee->full_name, 0, 1) }}
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2.5">
                <p class="text-lg font-bold text-on-surface">{{ $employee->full_name }}</p>

                {{-- Badge tipe kontrak --}}
                <span class="text-[11px] font-bold px-2.5 py-1 rounded
                        {{ $employee->employment_status === 'PKWTT'
        ? 'bg-primary/10 text-primary'
        : 'bg-amber-500/10 text-amber-700' }}">
                    {{ $employee->employment_status }}
                </span>

                {{-- Badge status --}}
                <span class="text-[11px] font-bold px-2.5 py-1 rounded
                        {{ $employee->status === 'active'
        ? 'bg-green-500/10 text-green-700'
        : 'bg-red-500/10 text-red-700' }}">
                    {{ $employee->status === 'active' ? 'Aktif' : ucfirst($employee->status) }}
                </span>
            </div>

            <p class="text-sm text-on-surface-variant/60 mt-1">
                {{ $employee->position->name ?? '-' }}
                &bull;
                {{ $employee->department->name ?? '-' }}
            </p>
            <p class="text-xs text-on-surface-variant/40 font-mono-data mt-1">
                {{ $employee->employee_id }}
                &bull;
                Bergabung {{ \Carbon\Carbon::parse($employee->join_date)->translatedFormat('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('hr.employees.documents', $employee->employee_id) }}" class="border border-black/10 text-on-surface-variant/70 text-xs font-bold px-4 py-2.5
                           rounded-lg flex items-center gap-1.5 hover:bg-surface-container transition cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">folder_open</span>
                Dokumen
            </a>
            <a href="{{ route('hr.employees.edit', $employee->employee_id) }}" class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5
                           rounded-lg flex items-center gap-1.5 transition cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Data
            </a>
        </div>
    </div>

    {{-- ── GRID UTAMA ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-5">

        {{-- KOLOM KIRI: DATA UTAMA --}}
        <div class="col-span-2 space-y-5">

            {{-- DATA PRIBADI --}}
            <div class="card-flat rounded-2xl p-6">
                <h2 class="text-base font-bold text-on-surface mb-5">Data Pribadi</h2>
                <div class="grid grid-cols-2 gap-5">

                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">NIK (KTP)</p>
                        <p class="text-sm font-semibold text-on-surface font-mono-data mt-1">
                            {{ $employee->nik ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">Email</p>
                        <p class="text-sm font-semibold text-on-surface mt-1 break-all">
                            {{-- Sembunyikan email dummy @internal.local --}}
                            @if (str_ends_with($employee->email ?? '', '@internal.local'))
                                <span class="text-on-surface-variant/40 italic text-xs">Belum diaktivasi</span>
                            @else
                                {{ $employee->email ?? '—' }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">No. Telepon</p>
                        <p class="text-sm font-semibold text-on-surface font-mono-data mt-1">
                            {{ $employee->phone ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">Posisi / Grade
                        </p>
                        <p class="text-sm font-semibold text-on-surface mt-1">
                            {{ $employee->position->name ?? '—' }}
                            @if ($employee->position->grade ?? null)
                                <span class="text-on-surface-variant/50">· {{ $employee->position->grade }}</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">NPWP</p>
                        <p class="text-sm font-semibold text-on-surface font-mono-data mt-1">
                            {{ $employee->npwp ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">No. BPJS</p>
                        <p class="text-sm font-semibold text-on-surface font-mono-data mt-1">
                            {{ $employee->bpjs_number ?? '—' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- RIWAYAT KONTRAK --}}
            <div class="card-flat rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5">
                    <h2 class="text-base font-bold text-on-surface">Riwayat Masa Berlaku Kontrak</h2>
                </div>

                @if ($contracts->isEmpty())
                    <div class="px-6 py-8 text-center text-sm text-on-surface-variant/50">
                        <span
                            class="material-symbols-outlined text-[32px] block mb-2 text-on-surface-variant/20">assignment_late</span>
                        Belum ada riwayat kontrak tercatat.
                    </div>
                @else
                    <div class="divide-y divide-black/5">
                        @foreach ($contracts as $c)
                                <div class="px-6 py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                                        {{ $c['status'] === 'Berjalan'
                            ? 'bg-primary/10 text-primary'
                            : 'bg-surface-container text-on-surface-variant/40' }}">
                                            <span class="material-symbols-outlined text-[18px]">assignment</span>
                                        </span>
                                        <div>
                                            <p class="text-sm font-bold text-on-surface">{{ $c['type'] }}</p>
                                            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $c['range'] }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded
                                                    {{ $c['status'] === 'Berjalan'
                            ? 'bg-primary/10 text-primary'
                            : 'bg-surface-container text-on-surface-variant/50' }}">
                                        {{ $c['status'] }}
                                    </span>
                                </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- AKTIVITAS TERBARU --}}
            <div class="card-flat rounded-2xl p-6">
                <h2 class="text-base font-bold text-on-surface mb-5">Aktivitas Terbaru</h2>

                @if ($recentActivity->isEmpty())
                    <p class="text-sm text-on-surface-variant/50 italic">Belum ada aktivitas tercatat.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($recentActivity as $a)
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary
                                                         flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">{{ $a['icon'] }}</span>
                                </span>
                                <p class="text-sm text-on-surface-variant/80 flex-1">{{ $a['label'] }}</p>
                                <p class="text-xs text-on-surface-variant/40 font-mono-data whitespace-nowrap">
                                    {{ $a['time'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- KOLOM KANAN: RINGKASAN --}}
        <div class="space-y-5">

            {{-- SISA CUTI --}}
            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">
                    Sisa Kuota Cuti {{ now()->year }}
                </p>
                <p class="text-3xl font-extrabold font-mono-data text-primary">
                    {{ $leaveBalance }}
                    <span class="text-sm font-bold text-on-surface-variant/40">/ {{ $leaveQuota }} hari</span>
                </p>
                <div class="w-full h-1.5 rounded-full bg-surface-container mt-3 overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all"
                        style="width: {{ $leaveQuota > 0 ? round($leaveBalance / $leaveQuota * 100) : 0 }}%">
                    </div>
                </div>
                <p class="text-[11px] text-on-surface-variant/40 mt-2">
                    Terpakai: {{ $leaveQuota - $leaveBalance }} hari
                </p>
            </div>

            {{-- GAJI POKOK --}}
            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">
                    Gaji Pokok
                </p>
                <p class="text-2xl font-extrabold font-mono-data text-on-surface">
                    Rp{{ number_format($employee->basic_salary ?? 0, 0, ',', '.') }}
                </p>
                <a href="{{ route('hr.payroll.slip', $employee->employee_id) }}"
                    class="text-xs font-bold text-primary/70 hover:text-primary transition mt-2 inline-block">
                    Lihat slip gaji terakhir →
                </a>
            </div>

            {{-- DOKUMEN TERUNGGAH --}}
            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">
                    Dokumen Terunggah
                </p>
                <div class="space-y-2.5">
                    @foreach ($documents as $doc => $done)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-on-surface-variant/70">{{ $doc }}</span>
                            <span class="material-symbols-outlined text-[18px]
                                        {{ $done ? 'text-primary' : 'text-on-surface-variant/25' }}">
                                {{ $done ? 'check_circle' : 'radio_button_unchecked' }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('hr.employees.documents', $employee->employee_id) }}"
                    class="text-xs font-bold text-primary/70 hover:text-primary transition mt-3 inline-block">
                    Kelola dokumen →
                </a>
            </div>

            {{-- INFO NIP / AKUN --}}
            <div class="card-flat rounded-2xl p-6">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">
                    Info Akun Karyawan
                </p>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] text-on-surface-variant/40 uppercase font-bold tracking-wide mb-0.5">
                            NIP (Username)
                        </p>
                        <p class="font-mono-data font-extrabold text-primary text-base">
                            {{ $employee->employee_id }}
                        </p>
                    </div>
                    <div class="pt-2 border-t border-black/5">
                        <p class="text-[10px] text-on-surface-variant/40 uppercase font-bold tracking-wide mb-0.5">
                            Status Aktivasi
                        </p>
                        @php
                            $isActivated = $employee->email && !str_ends_with($employee->email, '@internal.local');
                        @endphp
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded inline-flex items-center gap-1
                                {{ $isActivated ? 'bg-green-500/10 text-green-700' : 'bg-amber-500/10 text-amber-700' }}">
                            <span class="material-symbols-outlined text-[13px]">
                                {{ $isActivated ? 'verified' : 'pending' }}
                            </span>
                            {{ $isActivated ? 'Sudah Aktivasi' : 'Belum Aktivasi' }}
                        </span>
                        @if (!$isActivated)
                            <p class="text-[10px] text-on-surface-variant/40 mt-1.5">
                                Karyawan belum mengisi email &amp; password baru di aplikasi mobile.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection