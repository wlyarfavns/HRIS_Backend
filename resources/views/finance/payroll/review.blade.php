@extends('layouts.finance')

@section('title', 'Review Payroll — ' . $batch->period_start->translatedFormat('F Y'))
@section('page-title', 'Review Payroll — ' . $batch->period_start->translatedFormat('F Y'))
@section('page-desc', 'Data bersifat read-only. Validasi angka akhir sebelum mengunci dan menyetujui disbursement.')

@php
    // Turunkan gross/bpjs/pph21/net per karyawan dari relasi details (source of truth: DB).
    $rows = $payrolls->map(function ($p) {
        $earnings   = $p->details->where('type', 'earning');
        $deductions = $p->details->where('type', 'deduction');

        $bpjs  = $deductions->filter(fn ($d) => str_starts_with($d->salaryComponent->name ?? '', 'BPJS'))->sum('amount');
        $pph21 = $deductions->filter(fn ($d) => str_starts_with($d->salaryComponent->name ?? '', 'PPh'))->sum('amount');

        return [
            'id'       => $p->id,
            'nip'      => $p->employee->employee_id ?? '-',
            'name'     => $p->employee->full_name ?? '-',
            'dept'     => $p->employee->department->name ?? '-',
            'bank'     => $p->employee->bank_name ?? '-',
            'rekening' => $p->employee->bank_account_number ?? '-',
            'gross'    => $p->basic_salary + $p->total_allowances,
            'bpjs'     => $bpjs,
            'pph21'    => $pph21,
            'net'      => $p->net_salary,
            'earnings' => $earnings->map(fn ($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => $d->amount])->values(),
            'deductions' => $deductions->map(fn ($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => $d->amount])->values(),
        ];
    });

    $totalGross   = $rows->sum('gross');
    $totalBpjs    = $rows->sum('bpjs');
    $totalPph21   = $rows->sum('pph21');
    $totalNet     = $rows->sum('net');
    $totalTitipan = $totalBpjs + $totalPph21;
@endphp

@section('page-action')
    <a href="{{ route('finance.payroll.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60 hover:text-primary transition px-3 py-2 rounded-lg hover:bg-primary/5">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali
    </a>
@endsection

@section('content')
<div x-data="{
    showApproveModal: false,
    search: '',
    showSlipModal: false,
    slipEmployee: null,
    openSlip(emp) { this.slipEmployee = emp; this.showSlipModal = true; }
}">

    <div class="flex items-center gap-2 mb-5">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
            <span class="material-symbols-outlined text-[13px]">lock</span>
            Mode Read-Only — Finance hanya memvalidasi, tidak mengubah kalkulasi
        </span>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full
            {{ $batch->status === \App\Models\PayrollBatch::STATUS_APPROVED_FINANCE ? 'bg-primary/10 text-primary' : 'bg-amber-100 text-amber-700' }}">
            {{ ucwords(str_replace('_', ' ', $batch->status)) }}
        </span>
    </div>

    @if ($batch->status === \App\Models\PayrollBatch::STATUS_APPROVED_FINANCE)
    <div class="bg-primary/10 border border-primary/20 rounded-2xl px-5 py-4 flex items-center justify-between gap-4 mb-5">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[22px] text-primary">verified</span>
            <div>
                <p class="text-sm font-bold text-primary">Payroll {{ $batch->period_start->translatedFormat('F Y') }} Telah Disetujui & Dikunci</p>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Status "Approved by Finance". Lanjutkan ke Export Bank Transfer.</p>
            </div>
        </div>
        <a href="{{ route('finance.export.index') }}"
           class="shrink-0 inline-flex items-center gap-1.5 bg-primary text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-primary/90 shadow-sm transition">
            <span class="material-symbols-outlined text-[15px]">upload_file</span>
            Buka Export Bank Transfer
        </a>
    </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/50">Total Gross Salary</p>
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[17px] text-blue-500">account_balance_wallet</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">Rp{{ number_format($totalGross, 0, ',', '.') }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1.5">Sebelum potongan pajak & BPJS</p>
        </div>

        <div class="bg-white border border-black/5 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/50">Titipan Pajak & BPJS</p>
                <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[17px] text-rose-500">account_balance</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-rose-600 leading-none">Rp{{ number_format($totalTitipan, 0, ',', '.') }}</p>
            <div class="mt-2 space-y-0.5">
                <p class="text-[11px] text-on-surface-variant/50">PPh21 TER: <span class="font-bold text-on-surface-variant/70">Rp{{ number_format($totalPph21, 0, ',', '.') }}</span></p>
                <p class="text-[11px] text-on-surface-variant/50">BPJS: <span class="font-bold text-on-surface-variant/70">Rp{{ number_format($totalBpjs, 0, ',', '.') }}</span></p>
            </div>
        </div>

        <div class="bg-[#0B3D2E] border border-[#0B3D2E] rounded-2xl p-5 shadow-sm relative overflow-hidden text-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/50">Grand Total Nett Salary</p>
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[17px] text-emerald-300">payments</span>
                </div>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-white leading-none">Rp{{ number_format($totalNet, 0, ',', '.') }}</p>
            <p class="text-[11px] text-white/50 mt-1.5">Cash yang harus disiapkan Finance untuk transfer ke {{ $rows->count() }} karyawan</p>
        </div>
    </div>

    {{-- TABEL KARYAWAN --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-sm font-bold text-on-surface">Rincian Per Karyawan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Nama, nomor rekening bank, dan net salary siap transfer</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant/40">search</span>
                    <input type="text" x-model="search" placeholder="Cari karyawan…"
                           class="pl-8 pr-3 py-2 border border-black/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 w-48">
                </div>

                @if ($batch->status === \App\Models\PayrollBatch::STATUS_PENDING_FINANCE)
                    <button type="button" @click="showApproveModal = true"
                            class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-primary/90 shadow-sm transition">
                        <span class="material-symbols-outlined text-[16px]">lock</span>
                        Approve & Lock Payroll
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-primary/10 text-primary text-xs font-bold px-4 py-2.5 rounded-xl">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Approved & Locked
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-variant/20 text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wider border-b border-black/5">
                        <th class="px-6 py-3.5 min-w-[220px]">Karyawan</th>
                        <th class="px-4 py-3.5">Bank & Rekening</th>
                        <th class="px-4 py-3.5 text-right">Gross</th>
                        <th class="px-4 py-3.5 text-right">BPJS</th>
                        <th class="px-4 py-3.5 text-right">PPh21</th>
                        <th class="px-4 py-3.5 text-right min-w-[130px]">Net Salary</th>
                        <th class="px-6 py-3.5 text-center">Slip Preview</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($rows as $emp)
                    <tr class="hover:bg-primary/5 transition"
                        x-show="search === '' || '{{ strtolower($emp['name']) }}'.includes(search.toLowerCase()) || '{{ strtolower($emp['nip']) }}'.includes(search.toLowerCase()) || '{{ strtolower($emp['dept']) }}'.includes(search.toLowerCase())">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-[11px] font-bold text-primary shrink-0">
                                    {{ strtoupper(substr($emp['name'], 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface leading-tight">{{ $emp['name'] }}</p>
                                    <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">{{ $emp['nip'] }} · {{ $emp['dept'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div>
                                <span class="font-bold text-on-surface text-[11px] px-1.5 py-0.5 rounded bg-primary/10 text-primary">{{ $emp['bank'] }}</span>
                                <p class="font-mono text-on-surface-variant/60 mt-0.5 text-[11px]">{{ $emp['rekening'] }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($emp['gross'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-rose-600 font-bold">-{{ number_format($emp['bpjs'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-rose-600 font-bold">-{{ number_format($emp['pph21'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-primary text-sm">Rp{{ number_format($emp['net'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <button type="button"
                                    @click="openSlip({{ json_encode(array_merge($emp, ['period' => $batch->period_start->translatedFormat('F Y')])) }})"
                                    class="p-2 rounded-lg text-on-surface-variant/40 hover:text-primary hover:bg-primary/10 transition"
                                    title="Preview Slip Gaji">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-surface-variant/10 border-t-2 border-black/10 text-xs font-bold">
                        <td class="px-6 py-3.5 text-on-surface" colspan="2">TOTAL — {{ $rows->count() }} Karyawan</td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-on-surface">{{ number_format($totalGross, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-rose-600">-{{ number_format($totalBpjs, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-rose-600">-{{ number_format($totalPph21, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-mono-data text-primary text-sm">Rp{{ number_format($totalNet, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- MODAL KONFIRMASI APPROVE — form sungguhan ke backend --}}
    <div x-show="showApproveModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showApproveModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-3 pb-4 border-b border-black/5">
                <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px] text-primary">lock</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-on-surface">Approve & Lock Payroll</h3>
                    <p class="text-xs text-on-surface-variant/60 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="bg-surface-variant/20 rounded-xl p-4 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60">Periode</span>
                        <span class="font-bold text-on-surface font-mono">{{ $batch->period_start->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60">Total Karyawan</span>
                        <span class="font-bold text-on-surface font-mono">{{ $rows->count() }} org</span>
                    </div>
                    <div class="flex justify-between border-t border-black/5 pt-2 mt-2">
                        <span class="text-on-surface-variant/60 font-bold">Grand Total Nett</span>
                        <span class="font-extrabold text-primary font-mono">Rp{{ number_format($totalNet, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-xs text-on-surface-variant/60 leading-relaxed">
                    Data akan <strong class="text-on-surface">dikunci</strong> dan status berubah menjadi <strong class="text-primary">"Approved by Finance"</strong>.
                </p>
            </div>

            <form method="POST" action="{{ route('finance.payroll.approve', $batch) }}" class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                @csrf
                <button type="button" @click="showApproveModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/90 shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[15px]">lock</span>
                    Ya, Approve & Lock
                </button>
            </form>
        </div>
    </div>

    @include('finance._slip-preview-modal')

</div>
@endsection