@extends('layouts.hr')

@section('title', 'Penggajian')
@section('page-title', 'Penggajian')
@section('page-desc', 'Proses payroll periode berjalan: rekap absensi → kalkulasi → approval → disbursement.')

{{--
    Docblock ini murni bantuan untuk IDE (Intelephense) agar mengenali
    $start & $end sebagai instance Carbon, bukan string — menghilangkan
    warning "Call to a member function translatedFormat()/format() on
    a non-object of type string" yang muncul di tab Problems.
    Tidak berpengaruh ke runtime.
--}}
@php
    /** @var \App\Models\Payroll[]|\Illuminate\Support\Collection $payrolls */
    /** @var array $steps */
    /** @var \Carbon\Carbon $start */
    /** @var \Carbon\Carbon $end */
@endphp

@section('content')
<div x-data="{
    showProcessModal: false,
    toast: { show: false, message: '{{ session('success') }}', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    }
}" x-init="if ('{{ session('success') }}') triggerToast('{{ session('success') }}')">

    {{-- STATS & PERIODE BERJALAN --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="col-span-2 card-flat rounded-2xl p-6 relative overflow-hidden" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between">
                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Periode Payroll Berjalan</p>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded bg-white/10 text-white flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-brand-gold">bolt</span> Engine V1.0
                </span>
            </div>
            <p class="text-2xl font-extrabold text-white mt-3 mb-1">
                {{ $start->translatedFormat('d') }} – {{ $end->translatedFormat('d M Y') }}
            </p>
            <p class="text-white/70 text-xs">
                Status:
                <span class="font-bold text-brand-gold">
                    {{ $payrolls->isEmpty() ? 'Belum Diproses' : ($steps[3]['status'] ?? '-') }}
                </span>
            </p>
        </div>

        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Karyawan Diproses</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ $totalKaryawan }} Org</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">
                {{ $payrolls->isEmpty() ? 'Belum ada data periode ini' : 'Cut-off presensi selesai' }}
            </p>
        </div>

        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Estimasi Net Payroll</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ $totalGajiBersihFormatted }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Termasuk PPh21 TER &amp; BPJS</p>
        </div>
    </div>

    {{-- PIPELINE PROSES PAYROLL (dari data asli, bukan hardcode) --}}
    <div class="card-flat rounded-2xl p-6 mt-6">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <h2 class="text-base font-bold text-on-surface">Proses &amp; Alur Workflow Payroll</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">5 tahap otomatisasi dari rekap data absensi hingga digital payslip &amp; export bank</p>
            </div>

            <div class="flex items-center gap-2.5">
                @if ($payrolls->isNotEmpty() && $steps[2]['state'] === 'active')
                    <form method="POST" action="{{ route('hr.payroll.approveHr') }}">
                        @csrf
                        <input type="hidden" name="period" value="{{ $start->format('Y-m') }}">
                        <button type="submit" class="border border-primary/30 text-primary hover:bg-primary/5 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                            Approve HR
                        </button>
                    </form>
                @endif

                @if ($payrolls->isNotEmpty() && $steps[3]['state'] === 'active')
                    <form method="POST" action="{{ route('hr.payroll.approveFinance') }}">
                        @csrf
                        <input type="hidden" name="period" value="{{ $start->format('Y-m') }}">
                        <button type="submit" class="border border-amber-500/40 text-amber-800 hover:bg-amber-500/10 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                            Approve Finance
                        </button>
                    </form>
                @endif

                <button type="button" @click="showProcessModal = true"
                        class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                    <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                    Kalkulasi Ulang Payroll
                </button>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-3.5 relative">
            @foreach ($steps as $s)
                <div class="rounded-xl p-4 border flex flex-col justify-between transition relative
                    {{ $s['state'] === 'completed' ? 'border-primary/30 bg-primary/5' : '' }}
                    {{ $s['state'] === 'active' ? 'border-amber-500/50 bg-amber-500/10 ring-2 ring-amber-500/20' : '' }}
                    {{ $s['state'] === 'upcoming' ? 'border-black/10 bg-surface-container/30' : '' }}">

                    <div>
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

                            <span class="text-[10px] font-bold px-2 py-0.5 rounded font-mono-data
                                {{ $s['state'] === 'completed' ? 'bg-primary/15 text-primary' : '' }}
                                {{ $s['state'] === 'active' ? 'bg-amber-500/20 text-amber-900 font-extrabold' : '' }}
                                {{ $s['state'] === 'upcoming' ? 'bg-black/5 text-on-surface-variant/60' : '' }}">
                                {{ $s['status'] }}
                            </span>
                        </div>

                        <h3 class="text-xs font-bold text-on-surface leading-tight mb-1">{{ $s['label'] }}</h3>
                        <p class="text-[10px] text-on-surface-variant/60 leading-tight">{{ $s['desc'] }}</p>
                    </div>

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

    {{-- KOMPONEN GAJI PER KARYAWAN (dari Payroll model, bukan array dummy) --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Rincian Komponen Gaji per Karyawan</h2>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('hr.payroll.export', ['period' => $start->format('Y-m')]) }}"
                   class="border border-black/10 hover:bg-surface-container px-3.5 py-2 rounded-xl text-xs font-bold text-on-surface flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px] text-primary">download</span> Unduh Rekap (XLSX)
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5 text-right">Gaji Pokok</th>
                        <th class="px-4 py-3.5 text-right">Tunjangan</th>
                        <th class="px-4 py-3.5 text-right">Potongan</th>
                        <th class="px-4 py-3.5 text-right">Gaji Bersih (Net)</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Slip Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse ($payrolls as $p)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-[11px] font-bold text-primary shrink-0">
                                        {{ strtoupper(substr($p->employee->full_name ?? '-', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $p->employee->full_name ?? '-' }}</p>
                                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">
                                            {{ $p->employee->employee_id ?? '-' }} · {{ $p->employee->department->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">
                                {{ number_format($p->basic_salary, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">
                                {{ number_format($p->total_allowances, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-error">
                                -{{ number_format($p->total_deductions, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-xs text-primary">
                                Rp{{ number_format($p->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded
                                    {{ $p->status === 'approved_finance' ? 'bg-primary/15 text-primary' : '' }}
                                    {{ $p->status === 'approved_hr' ? 'bg-amber-500/15 text-amber-800' : '' }}
                                    {{ $p->status === 'draft' ? 'bg-black/5 text-on-surface-variant/60' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <a href="{{ route('hr.payroll.slip', $p->id) }}"
                                   class="text-xs font-bold text-primary hover:underline flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">description</span> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Belum ada data payroll untuk periode ini. Klik "Kalkulasi Ulang Payroll" untuk memproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PROSES PAYROLL ENGINE --}}
    <div x-show="showProcessModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showProcessModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
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
                Kalkulasi ulang akan memuat ulang data presensi terkunci, jam lembur SPL terverifikasi, serta tabel TER PPh21
                untuk periode <strong class="text-on-surface font-mono-data">{{ $start->translatedFormat('F Y') }}</strong>.
            </p>

            <form method="POST" action="{{ route('hr.payroll.run') }}" class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                @csrf
                <input type="hidden" name="period" value="{{ $start->format('Y-m') }}">
                <button type="button" @click="showProcessModal = false"
                        class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">sync</span>
                    Jalankan Engine Payroll
                </button>
            </form>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
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