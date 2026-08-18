@extends('layouts.finance')

@section('title', 'Review Payroll — ' . $batch->period_start->translatedFormat('F Y'))
@section('page-title', 'Review Payroll — ' . $batch->period_start->translatedFormat('F Y'))
@section('page-desc', 'Data bersifat read-only. Validasi angka akhir sebelum mengunci dan menyetujui disbursement.')

@php

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



            'earnings' => collect([
                    ['label' => 'Gaji Pokok', 'amount' => (float) $p->basic_salary],
                ])
                ->concat($earnings->map(fn ($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount]))
                ->values(),
            'deductions' => $deductions->map(fn ($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])->values(),
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
       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-[#0B3D2E] transition px-4 py-2.5 rounded-md border border-gray-200 hover:border-[#0B3D2E]/30 hover:bg-gray-50 bg-white shadow-sm">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
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

    <div class="flex items-center gap-3 mb-6 flex-wrap">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-medium px-3 py-1.5 rounded-full bg-gray-50 text-gray-700 border border-gray-200">
            <span class="material-symbols-outlined text-[14px]">lock</span>
            Mode Read-Only — Finance hanya memvalidasi, tidak mengubah kalkulasi
        </span>
        <span class="text-[11px] font-medium px-3 py-1.5 rounded-full border
            {{ $batch->status === \App\Models\PayrollBatch::STATUS_APPROVED_FINANCE ? 'bg-gray-50 text-[#0B3D2E] border-gray-200' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
            {{ ucwords(str_replace('_', ' ', $batch->status)) }}
        </span>
    </div>

    @if ($batch->status === \App\Models\PayrollBatch::STATUS_APPROVED_FINANCE)
    <div class="bg-gray-50 border border-gray-200 rounded-md px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <p class="text-sm font-medium text-emerald-900">Payroll {{ $batch->period_start->translatedFormat('F Y') }} Telah Disetujui & Dikunci</p>
                <p class="text-xs text-[#0B3D2E] mt-1">Status "Approved by Finance". Lanjutkan ke Export Bank Transfer.</p>
            </div>
        </div>
        <a href="{{ route('finance.export.index') }}"
           class="shrink-0 inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white text-sm font-medium px-5 py-2.5 rounded-md hover:bg-[#043927] shadow-sm transition">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            Buka Export Bank Transfer
        </a>
    </div>
    @endif


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-gray-100 rounded-md p-6 shadow-sm relative overflow-hidden group hover:border-gray-200 transition-colors">
            <div class="flex items-center justify-between mb-4">
                
                <p class="text-[11px] font-medium uppercase tracking-widest text-gray-400">Total Gross Salary</p>
            </div>
            <p class="text-3xl font-semibold  text-gray-800 leading-none">Rp{{ number_format($totalGross, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2">Sebelum potongan pajak & BPJS</p>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
        </div>

        <div class="bg-white border border-gray-100 rounded-md p-6 shadow-sm relative overflow-hidden group hover:border-gray-200 transition-colors">
            <div class="flex items-center justify-between mb-4">
                
                <p class="text-[11px] font-medium uppercase tracking-widest text-gray-400">Titipan Pajak & BPJS</p>
            </div>
            <p class="text-3xl font-semibold  text-gray-700 leading-none">Rp{{ number_format($totalTitipan, 0, ',', '.') }}</p>
            <div class="mt-3 space-y-1">
                <p class="text-xs text-gray-500 flex justify-between"><span>PPh21 TER:</span> <span class="font-medium text-gray-700">Rp{{ number_format($totalPph21, 0, ',', '.') }}</span></p>
                <p class="text-xs text-gray-500 flex justify-between"><span>BPJS:</span> <span class="font-medium text-gray-700">Rp{{ number_format($totalBpjs, 0, ',', '.') }}</span></p>
            </div>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
        </div>

        <div class="bg-[#0B3D2E] border border-[#0B3D2E] rounded-md p-6 shadow-sm relative overflow-hidden text-white group hover:border-[#043927] transition-colors">
            <div class="flex items-center justify-between mb-4">
                
                <p class="text-[11px] font-medium uppercase tracking-widest text-emerald-100">Grand Total Nett Salary</p>
            </div>
            <p class="text-3xl font-semibold  text-white leading-none">Rp{{ number_format($totalNet, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-100 mt-2">Cash yang harus disiapkan Finance untuk transfer ke {{ $rows->count() }} karyawan</p>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
        </div>
    </div>


    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Rincian Per Karyawan</h2>
                <p class="text-xs text-gray-500 mt-1">Nama, nomor rekening bank, dan net salary siap transfer</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Cari karyawan…"
                           class="w-64 pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] shadow-sm transition">
                </div>

                @if ($batch->status === \App\Models\PayrollBatch::STATUS_PENDING_FINANCE)
                    <button type="button" @click="showApproveModal = true"
                            class="inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white text-sm font-medium px-5 py-2.5 rounded-md hover:bg-[#043927] shadow-sm transition">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        Approve & Lock Payroll
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-50 text-[#0B3D2E] border border-gray-200 text-sm font-medium px-5 py-2.5 rounded-md shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        Approved & Locked
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Bank & Rekening</th>
                        <th class="px-6 py-4 text-right">Gross</th>
                        <th class="px-6 py-4 text-right">BPJS</th>
                        <th class="px-6 py-4 text-right">PPh21</th>
                        <th class="px-6 py-4 text-right">Net Salary</th>
                        <th class="px-8 py-4 text-center">Slip Preview</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach ($rows as $emp)
                    <tr class="hover:bg-gray-50 transition group"
                        x-show="search === '' || '{{ strtolower($emp['name']) }}'.includes(search.toLowerCase()) || '{{ strtolower($emp['nip']) }}'.includes(search.toLowerCase()) || '{{ strtolower($emp['dept']) }}'.includes(search.toLowerCase())">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-xs font-medium text-[#0B3D2E] shrink-0">
                                    {{ strtoupper(substr($emp['name'], 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors leading-tight">{{ $emp['name'] }}</p>
                                    <p class="text-[11px]  text-gray-500 mt-0.5">{{ $emp['nip'] }} · {{ $emp['dept'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-medium text-[#0B3D2E] text-[11px] px-2 py-0.5 rounded bg-gray-50 border border-gray-200">{{ $emp['bank'] }}</span>
                                <p class=" text-gray-600 mt-1 text-xs">{{ $emp['rekening'] }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right  text-sm">{{ number_format($emp['gross'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right  text-sm text-gray-700 font-medium">-{{ number_format($emp['bpjs'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right  text-sm text-gray-700 font-medium">-{{ number_format($emp['pph21'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right  font-semibold text-[#0B3D2E] text-sm">Rp{{ number_format($emp['net'], 0, ',', '.') }}</td>
                        <td class="px-8 py-4 text-center">
                            <button type="button"
                                    @click="openSlip({{ json_encode(array_merge($emp, ['period' => $batch->period_start->translatedFormat('F Y')])) }})"
                                    class="p-2.5 rounded-md border border-gray-200 bg-white text-gray-500 hover:text-[#0B3D2E] hover:bg-gray-50 hover:border-gray-200 transition shadow-sm"
                                    title="Preview Slip Gaji">
                                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 border-t border-gray-200 text-sm font-medium text-gray-800">
                        <td class="px-8 py-5" colspan="2">TOTAL — {{ $rows->count() }} Karyawan</td>
                        <td class="px-6 py-5 text-right ">{{ number_format($totalGross, 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-right  text-gray-700">-{{ number_format($totalBpjs, 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-right  text-gray-700">-{{ number_format($totalPph21, 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-right  text-[#0B3D2E]">Rp{{ number_format($totalNet, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-4 px-6 pb-6">
            {{ $payrolls->links() }}
        </div>
    </div>


    <div x-show="showApproveModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showApproveModal = false">
        <div class="bg-white rounded-md max-w-md w-full p-8 shadow-sm space-y-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                    </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-800">Approve & Lock Payroll</h3>
                    <p class="text-xs text-gray-500 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 rounded-md p-5 space-y-3 text-sm border border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Periode</span>
                        <span class="font-medium text-gray-800 ">{{ $batch->period_start->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Total Karyawan</span>
                        <span class="font-medium text-gray-800 ">{{ $rows->count() }} org</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3 mt-3">
                        <span class="text-gray-700 font-medium">Grand Total Nett</span>
                        <span class="font-semibold text-[#0B3D2E]  text-base">Rp{{ number_format($totalNet, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed text-center">
                    Data akan <strong class="text-gray-800">dikunci</strong> dan status berubah menjadi <strong class="text-[#0B3D2E]">"Approved by Finance"</strong>.
                </p>
            </div>

            <form method="POST" action="{{ route('finance.payroll.approve', $batch) }}" class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                @csrf
                <button type="button" @click="showApproveModal = false"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center gap-2 transition">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    Ya, Approve & Lock
                </button>
            </form>
        </div>
    </div>

    @include('finance._slip-preview-modal')

</div>
@endsection
