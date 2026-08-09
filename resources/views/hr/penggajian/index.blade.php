@extends('layouts.hr')

@section('title', 'Penggajian')
@section('page-title', 'Penggajian')
@section('page-desc', 'Proses payroll periode berjalan: rekap absensi → kalkulasi → approval → disbursement.')

@php
    $steps = [
        ['step' => 1, 'label' => 'Cut-off Rekap Absensi', 'status' => 'Selesai', 'date' => '25 Agu 2026', 'state' => 'completed', 'icon' => 'check', 'desc' => '1.284 presensi terkunci'],
        ['step' => 2, 'label' => 'Engine Payroll (PPh21 & BPJS)', 'status' => 'Selesai', 'date' => '26 Agu 2026', 'state' => 'completed', 'icon' => 'check', 'desc' => 'Kalkulasi TER PP 58/2023'],
        ['step' => 3, 'label' => 'Approval HR Operations', 'status' => 'Selesai', 'date' => '27 Agu 2026', 'state' => 'completed', 'icon' => 'check', 'desc' => 'Disetujui HR Lead'],
        ['step' => 4, 'label' => 'Approval Finance', 'status' => 'Sedang Proses', 'date' => 'Pending Review', 'state' => 'active', 'icon' => 'pending_actions', 'desc' => 'Review Finance Manager'],
        ['step' => 5, 'label' => 'Export Bank Transfer', 'status' => 'Terjadwal', 'date' => '01 Sep 2026', 'state' => 'upcoming', 'icon' => 'schedule', 'desc' => 'CSV BCA, Mandiri & BNI'],
    ];

    $components = [
        ['nip' => 'EMP-00231', 'name' => 'Budi Santoso', 'avatar' => 22, 'dept' => 'Sales', 'basic' => 6500000, 'allowance' => 850000, 'overtime' => 375723, 'bpjs' => 260000, 'pph21' => 180000, 'deduction' => 440000, 'net' => 7285723],
        ['nip' => 'EMP-01044', 'name' => 'Siti Aminah', 'avatar' => 44, 'dept' => 'Front Office', 'basic' => 5800000, 'allowance' => 700000, 'overtime' => 0, 'bpjs' => 232000, 'pph21' => 95000, 'deduction' => 327000, 'net' => 6173000],
        ['nip' => 'EMP-00812', 'name' => 'Eko Prasetyo', 'avatar' => 19, 'dept' => 'Sales & Migration', 'basic' => 7200000, 'allowance' => 900000, 'overtime' => 512000, 'bpjs' => 288000, 'pph21' => 265000, 'deduction' => 553000, 'net' => 8059000],
        ['nip' => 'EMP-00567', 'name' => 'Pam Beesly', 'avatar' => 47, 'dept' => 'Front Office', 'basic' => 5500000, 'allowance' => 650000, 'overtime' => 0, 'bpjs' => 220000, 'pph21' => 75000, 'deduction' => 295000, 'net' => 5855000],
    ];
@endphp

@section('content')
<div x-data="{
    showProcessModal: false,
    selectedDept: 'Semua',
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    }
}">

    {{-- STATS & PERIODE BERJALAN --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="col-span-2 card-flat rounded-2xl p-6 relative overflow-hidden" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between">
                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Periode Payroll Berjalan</p>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded bg-white/10 text-white flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-brand-gold">bolt</span> Engine V1.0
                </span>
            </div>
            <p class="text-2xl font-extrabold text-white mt-3 mb-1">1 – 31 Agustus 2026</p>
            <p class="text-white/70 text-xs">Status: <span class="font-bold text-brand-gold">Menunggu Approval Tim Finance</span></p>
        </div>

        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Karyawan Diproses</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">1.284 Org</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">100% presensi cut-off selesai</p>
        </div>

        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Estimasi Net Payroll</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">Rp9,84 M</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Termasuk PPh21 TER &amp; BPJS</p>
        </div>
    </div>

    {{-- USER-FRIENDLY STEPPER PIPELINE PROSES PAYROLL --}}
    <div class="card-flat rounded-2xl p-6 mt-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <h2 class="text-base font-bold text-on-surface">Proses &amp; Alur Workflow Payroll</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">5 tahap otomatisasi dari rekap data absensi hingga digital payslip &amp; export bank</p>
            </div>

            <button type="button" @click="showProcessModal = true"
                    class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                Kalkulasi Ulang Payroll
            </button>
        </div>

        {{-- GRID OF CONNECTED STEP CARDS (HIGH CONTRAST & USER FRIENDLY) --}}
        <div class="grid grid-cols-5 gap-3.5 relative">
            @foreach ($steps as $s)
                <div class="rounded-xl p-4 border flex flex-col justify-between transition relative
                    {{ $s['state'] === 'completed' ? 'border-primary/30 bg-primary/5' : '' }}
                    {{ $s['state'] === 'active' ? 'border-amber-500/50 bg-amber-500/10 ring-2 ring-amber-500/20' : '' }}
                    {{ $s['state'] === 'upcoming' ? 'border-black/10 bg-surface-container/30' : '' }}">

                    <div>
                        {{-- CARD HEADER: STEP NUMBER & ICON BADGE --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs shadow-xs
                                {{ $s['state'] === 'completed' ? 'bg-primary text-white' : '' }}
                                {{ $s['state'] === 'active' ? 'bg-amber-500 text-white' : '' }}
                                {{ $s['state'] === 'upcoming' ? 'bg-surface-container text-on-surface-variant/60 border border-black/10' : '' }}">
                                @if ($s['state'] === 'completed')
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                @elseif($s['state'] === 'active')
                                    <span class="material-symbols-outlined text-[16px] animate-pulse">hourglass_top</span>
                                @else
                                    {{ $s['step'] }}
                                @endif
                            </div>

                            {{-- STATUS PILL --}}
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded font-mono-data
                                {{ $s['state'] === 'completed' ? 'bg-primary/15 text-primary' : '' }}
                                {{ $s['state'] === 'active' ? 'bg-amber-500/20 text-amber-900 font-extrabold' : '' }}
                                {{ $s['state'] === 'upcoming' ? 'bg-black/5 text-on-surface-variant/60' : '' }}">
                                {{ $s['status'] }}
                            </span>
                        </div>

                        {{-- TITLE --}}
                        <h3 class="text-xs font-bold text-on-surface leading-tight mb-1">
                            {{ $s['label'] }}
                        </h3>
                        <p class="text-[10px] text-on-surface-variant/60 leading-tight">
                            {{ $s['desc'] }}
                        </p>
                    </div>

                    {{-- FOOTER DATE --}}
                    <div class="mt-4 pt-2 border-t border-black/5 flex items-center justify-between text-[10px] font-mono-data">
                        <span class="text-on-surface-variant/40">Tanggal:</span>
                        <span class="font-bold {{ $s['state'] === 'active' ? 'text-amber-800 font-extrabold' : 'text-on-surface-variant/70' }}">
                            {{ $s['date'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- PREVIEW KOMPONEN GAJI PER KARYAWAN --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Rincian Komponen Gaji per Karyawan</h2>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="triggerToast('Mengunduh Laporan Rekapitulasi Payroll Periode 1–31 Agustus 2026 (XLSX)...', 'info')"
                        class="border border-black/10 hover:bg-surface-container px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px] text-primary">download</span> Unduh Rekap (XLSX)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5 text-right">Gaji Pokok</th>
                        <th class="px-4 py-3.5 text-right">Tunj. Jabatan</th>
                        <th class="px-4 py-3.5 text-right">Upah Lembur</th>
                        <th class="px-4 py-3.5 text-right">BPJS (1%+2%)</th>
                        <th class="px-4 py-3.5 text-right">PPh21 TER</th>
                        <th class="px-4 py-3.5 text-right">Gaji Bersih (Net)</th>
                        <th class="px-6 py-3.5 text-center">Slip Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($components as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="https://i.pravatar.cc/32?img={{ $c['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="{{ $c['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $c['name'] }}</p>
                                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">{{ $c['nip'] }} · {{ $c['dept'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">{{ number_format($c['basic'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">{{ number_format($c['allowance'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">{{ number_format($c['overtime'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-error">-{{ number_format($c['bpjs'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-error">-{{ number_format($c['pph21'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-xs text-primary">Rp{{ number_format($c['net'], 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <a href="{{ route('hr.payroll.slip', $c['nip']) }}"
                                   class="text-xs font-bold text-primary hover:underline flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">description</span> Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PROSES PAYROLL ENGINE --}}
    <div x-show="showProcessModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showProcessModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">restart_alt</span>
                    <h3 class="text-base font-bold text-on-surface">Kalkulasi Ulang Payroll Engine</h3>
                </div>
                <button type="button" @click="showProcessModal = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <p class="text-xs text-on-surface-variant/70 leading-relaxed">
                Kalkulasi ulang akan memuat ulang data presensi terkunci, jam lembur SPL terverifikasi, serta tabel TER PPh21 Depkeu PP 58/2023 untuk <strong class="text-on-surface font-mono-data">1.284 karyawan</strong>.
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                <button type="button" @click="showProcessModal = false"
                        class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                    Batal
                </button>
                <button type="button" @click="showProcessModal = false"
                        class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">sync</span>
                    Jalankan Engine Payroll
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION (THEME-MATCHED DEEP EMERALD) -->
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-white font-medium text-xs border border-emerald-500/30 backdrop-blur-md"
         :class="{
             'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
             'bg-rose-950 border-rose-500/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-xs font-semibold"></span>
    </div>

</div>
@endsection