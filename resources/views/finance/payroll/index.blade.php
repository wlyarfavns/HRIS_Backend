@extends('layouts.finance')

@section('title', 'Approval Payroll')
@section('page-title', 'Approval Payroll')
@section('page-desc', 'Tinjau kalkulasi PPh21 TER, BPJS, & komponen gaji sebelum disetujui untuk disbursement bank.')

@php
    $steps = [
        ['label' => 'Cut-off Rekap Absensi', 'done' => true],
        ['label' => 'Engine Payroll (PPh21 & BPJS)', 'done' => true],
        ['label' => 'Approval HR', 'done' => true],
        ['label' => 'Approval Finance', 'done' => false],
        ['label' => 'Export Bank Transfer', 'done' => false],
    ];

    $components = [
        ['nip' => 'EMP-00231', 'name' => 'Budi Santoso', 'avatar' => 22, 'dept' => 'Sales', 'basic' => 6500000, 'allowance' => 850000, 'overtime' => 375723, 'bpjs' => 260000, 'pph21' => 180000, 'net' => 7285723],
        ['nip' => 'EMP-01044', 'name' => 'Siti Aminah', 'avatar' => 44, 'dept' => 'Front Office', 'basic' => 5800000, 'allowance' => 700000, 'overtime' => 0, 'bpjs' => 232000, 'pph21' => 95000, 'net' => 6173000],
        ['nip' => 'EMP-00812', 'name' => 'Eko Prasetyo', 'avatar' => 19, 'dept' => 'Sales & Migration', 'basic' => 7200000, 'allowance' => 900000, 'overtime' => 512000, 'bpjs' => 288000, 'pph21' => 265000, 'net' => 8059000],
        ['nip' => 'EMP-00567', 'name' => 'Pam Beesly', 'avatar' => 47, 'dept' => 'Front Office', 'basic' => 5500000, 'allowance' => 650000, 'overtime' => 0, 'bpjs' => 220000, 'pph21' => 75000, 'net' => 5855000],
    ];
@endphp

@section('content')
<div x-data="{
    showApproveModal: false,
    payrollApproved: false,
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    }
}">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="col-span-2 bg-[#0B3D2E] rounded-2xl p-6 text-white flex flex-col justify-between shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Periode Payroll Berjalan</p>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white/10 text-white flex items-center gap-1 border border-white/10">
                    <span class="material-symbols-outlined text-[14px] text-amber-400">verified</span> HR Approved
                </span>
            </div>
            <p class="text-2xl font-extrabold text-white mt-3 mb-1">1 – 31 Agustus 2026</p>
            <p class="text-white/70 text-xs">Status: <span class="font-bold text-amber-400">Menunggu Review &amp; Approval Finance</span></p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Total Karyawan</p>
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">groups</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-on-surface leading-none">1.284 Org</p>
            <p class="text-[11px] text-on-surface-variant/60 mt-1">Presensi &amp; SPL tervalidasi</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Estimasi Net Payroll</p>
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">payments</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-primary leading-none">Rp1,24 M</p>
            <p class="text-[11px] text-on-surface-variant/60 mt-1">Termasuk TER &amp; BPJS</p>
        </div>
    </div>

    {{-- STEPPER ALUR PAYROLL DENGAN TOMBOL APPROVE TERPADU --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm p-6 mt-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <h2 class="text-base font-bold text-on-surface">Alur Persetujuan Payroll</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Langkah 4: Finance melakukan verifikasi akhir sebelum membuka jalur export transfer</p>
            </div>

            <button type="button" @click="showApproveModal = true"
                    class="bg-primary hover:bg-primary-dark text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm transition">
                <span class="material-symbols-outlined text-[17px]">check_circle</span>
                Setujui Payroll &amp; Lanjut ke Export
            </button>
        </div>

        <div class="flex items-center">
            @foreach ($steps as $i => $s)
                <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center gap-1.5 shrink-0">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold shadow-xs
                            {{ $s['done'] ? 'bg-primary text-white' : 'bg-surface-variant/20 text-on-surface-variant/40 border border-black/10' }}">
                            @if ($s['done'])
                                <span class="material-symbols-outlined text-[17px]">check</span>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p class="text-[11px] font-bold text-center w-28 {{ $s['done'] ? 'text-primary' : 'text-on-surface-variant/40' }}">{{ $s['label'] }}</p>
                    </div>
                    @if (!$loop->last)
                        <div class="flex-1 h-0.5 mx-2 {{ $s['done'] ? 'bg-primary' : 'bg-black/10' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- RINCIAN KALKULASI TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Rincian Kalkulasi PPh21 TER &amp; Potongan BPJS</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Verifikasi kepatuhan pajak penghasilan &amp; jaminan sosial karyawan</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="triggerToast('Mengunduh Laporan Rekapitulasi Payroll (XLSX)...', 'info')"
                        class="border border-black/10 hover:bg-black/5 px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px] text-primary">download</span> Unduh Rekap (XLSX)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5 min-w-[200px]">Karyawan</th>
                        <th class="px-4 py-3.5 text-right min-w-[120px]">Gaji Pokok</th>
                        <th class="px-4 py-3.5 text-right min-w-[120px]">Tunj. Jabatan</th>
                        <th class="px-4 py-3.5 text-right min-w-[120px]">Upah Lembur</th>
                        <th class="px-4 py-3.5 text-right min-w-[130px]">BPJS (1%+2%)</th>
                        <th class="px-4 py-3.5 text-right min-w-[130px]">PPh21 (TER)</th>
                        <th class="px-4 py-3.5 text-right min-w-[140px]">Gaji Bersih (Net)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($components as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/36?img={{ $c['avatar'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $c['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $c['name'] }}</p>
                                        <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">{{ $c['nip'] }} · {{ $c['dept'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-on-surface-variant/80">{{ number_format($c['basic'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-on-surface-variant/80">{{ number_format($c['allowance'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-on-surface-variant/80">{{ number_format($c['overtime'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-rose-700 font-bold">-{{ number_format($c['bpjs'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-rose-700 font-bold">-{{ number_format($c['pph21'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono font-extrabold text-xs text-primary">Rp{{ number_format($c['net'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL APPROVAL FINANCE --}}
    <div x-show="showApproveModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showApproveModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Konfirmasi Approval Payroll Finance</h3>
                        <p class="text-xs text-on-surface-variant/60">Persetujuan final sebelum generasi file transfer bank</p>
                    </div>
                </div>
                <button type="button" @click="showApproveModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <p class="text-xs text-on-surface-variant/70 leading-relaxed">
                Dengan menyetujui payroll periode <strong class="text-on-surface font-mono">Agustus 2026</strong> sebesar <strong class="text-primary font-mono">Rp1.240.500.000</strong>, data akan dikunci dan siap di-generate ke file transfer bank (BCA, Mandiri, BNI).
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showApproveModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <a href="{{ route('finance.export.index') }}"
                   class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">task_alt</span>
                    Setujui &amp; Lanjut ke Export Bank
                </a>
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