@extends('layouts.finance')

@section('title', 'Payroll Approval & Disbursement')
@section('page-title', 'Payroll Approval & Disbursement')
@section('page-desc', 'Review batch gaji dari HR, validasi angka akhir, dan setujui untuk proses disbursement bank.')

@section('content')
<div x-data="{
    tab: 'pending',
    showArchiveModal: false,
    archiveBatch: null,
    bankLogos: {
        BCA: 'https://i.pinimg.com/736x/27/71/54/2771540fa7259e0bd0cdfae464385480.jpg',
        MANDIRI: 'https://i.pinimg.com/736x/0b/ed/5c/0bed5c44c43dc1efd1cbf6acf3aa1d89.jpg',
        BNI: 'https://i.pinimg.com/1200x/7a/ca/e2/7acae2a6ac351b72a5c89e2fbc545758.jpg'
    },
    toastMsg: '',
    showToast: false,
    openArchive(batch) { this.archiveBatch = batch; this.showArchiveModal = true; },
    downloadFile(fileName) {
        this.toastMsg = 'Mengunduh ' + fileName + '...';
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 3000);
    }
}" x-init="if ('{{ session('success') }}') { toastMsg = '{{ session('success') }}'; showToast = true; setTimeout(() => showToast = false, 3000); }">

    {{-- TAB HEADER --}}
    <div class="flex items-center gap-1 bg-white border border-black/5 rounded-2xl p-1.5 w-fit shadow-sm mb-6">
        <button @click="tab = 'pending'"
            :class="tab === 'pending' ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant/60 hover:bg-primary/5 hover:text-primary'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-[15px]">pending_actions</span>
            Pending Approval
            <span :class="tab === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700'"
                class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full transition">
                {{ $pendingBatches->count() }}
            </span>
        </button>
        <button @click="tab = 'ready'"
            :class="tab === 'ready' ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant/60 hover:bg-primary/5 hover:text-primary'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-[15px]">task_alt</span>
            Ready to Disburse
            <span :class="tab === 'ready' ? 'bg-white/20 text-white' : 'bg-primary/10 text-primary'"
                class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full transition">
                {{ $readyBatches->count() }}
            </span>
        </button>
        <button @click="tab = 'completed'"
            :class="tab === 'completed' ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant/60 hover:bg-primary/5 hover:text-primary'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-[15px]">check_circle</span>
            Completed
        </button>
    </div>

    {{-- ======================== TAB: PENDING APPROVAL ======================== --}}
    <div x-show="tab === 'pending'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3.5 flex items-center gap-3 mb-5">
            <span class="material-symbols-outlined text-[20px] text-amber-600">info</span>
            <p class="text-xs text-amber-800 font-medium">
                Batch berikut telah disubmit HR dan menunggu review serta persetujuan Finance sebelum dapat dicairkan.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5">
                <h2 class="text-sm font-bold text-on-surface">Batch Gaji Menunggu Persetujuan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Review kalkulasi PPh21 TER & BPJS sebelum menyetujui disbursement</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-variant/20 text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wider border-b border-black/5">
                            <th class="px-6 py-3.5">Periode Gaji</th>
                            <th class="px-4 py-3.5">Dikirim Oleh</th>
                            <th class="px-4 py-3.5">Waktu Submit</th>
                            <th class="px-4 py-3.5 text-right">Total Pegawai</th>
                            <th class="px-4 py-3.5 text-right">Grand Total Nett</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse ($pendingBatches as $b)
                        <tr class="hover:bg-primary/5 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[16px] text-amber-600">calendar_month</span>
                                    </div>
                                    <span class="font-bold text-sm text-on-surface">{{ $b->period_start->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs text-on-surface-variant/70">{{ $b->submittedBy->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-xs font-mono text-on-surface-variant/60">{{ optional($b->submitted_at)->translatedFormat('d M Y, H.i') ?? '-' }} WIB</td>
                            <td class="px-4 py-4 text-right font-mono-data text-xs font-bold text-on-surface">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                            <td class="px-4 py-4 text-right font-mono-data text-sm font-extrabold text-primary">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending Finance
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('finance.payroll.show', $b) }}"
                                   class="inline-flex items-center bg-primary text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-primary/90 shadow-sm transition">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Tidak ada batch payroll yang menunggu persetujuan Finance saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ======================== TAB: READY TO DISBURSE ======================== --}}
    <div x-show="tab === 'ready'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none;">

        <div class="bg-primary/5 border border-primary/15 rounded-2xl px-5 py-3.5 flex items-center gap-3 mb-5">
            <span class="material-symbols-outlined text-[20px] text-primary">check_circle</span>
            <p class="text-xs text-primary font-medium">
                Batch berikut telah disetujui Finance dan siap untuk proses export & disbursement bank.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5">
                <h2 class="text-sm font-bold text-on-surface">Batch Siap Dicairkan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Lanjutkan ke menu Export Bank Transfer</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-variant/20 text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wider border-b border-black/5">
                            <th class="px-6 py-3.5">Periode Gaji</th>
                            <th class="px-4 py-3.5">Disetujui Oleh</th>
                            <th class="px-4 py-3.5">Waktu Approve</th>
                            <th class="px-4 py-3.5 text-right">Total Pegawai</th>
                            <th class="px-4 py-3.5 text-right">Grand Total Nett</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse ($readyBatches as $b)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[16px] text-primary">calendar_month</span>
                                    </div>
                                    <span class="font-bold text-sm text-on-surface">{{ $b->period_start->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs text-on-surface-variant/70">{{ $b->approvedBy->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-xs font-mono text-on-surface-variant/60">{{ optional($b->approved_finance_at)->translatedFormat('d M Y, H.i') ?? '-' }} WIB</td>
                            <td class="px-4 py-4 text-right font-mono-data text-xs font-bold text-on-surface">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                            <td class="px-4 py-4 text-right font-mono-data text-sm font-extrabold text-primary">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-primary/10 text-primary">
                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                    Approved by Finance
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('finance.export.index') }}"
                                   class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-primary/90 shadow-sm transition">
                                    <span class="material-symbols-outlined text-[15px]">upload_file</span>
                                    Export Bank Transfer
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Belum ada batch yang siap dicairkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ======================== TAB: COMPLETED ======================== --}}
    <div x-show="tab === 'completed'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none;">

        <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5">
                <h2 class="text-sm font-bold text-on-surface">Riwayat Payroll Selesai</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Batch yang sudah dicairkan dan slip gaji telah dipublish ke karyawan</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-variant/20 text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wider border-b border-black/5">
                            <th class="px-6 py-3.5">Periode Gaji</th>
                            <th class="px-4 py-3.5">Tanggal Cair</th>
                            <th class="px-4 py-3.5 text-right">Total Pegawai</th>
                            <th class="px-4 py-3.5 text-right">Grand Total Nett</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-center">Arsip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse ($completedBatches as $b)
                        <tr class="hover:bg-primary/5 transition text-on-surface-variant/70">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-surface-variant/30 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant/40">calendar_month</span>
                                    </div>
                                    <span class="font-bold text-sm text-on-surface">{{ $b->period_start->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs font-mono">{{ optional($b->disbursed_at)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-4 text-right font-mono-data text-xs font-bold">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                            <td class="px-4 py-4 text-right font-mono-data text-sm font-extrabold text-on-surface">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-surface-variant/40 text-on-surface-variant/60">
                                    <span class="material-symbols-outlined text-[12px]">done_all</span>
                                    Disbursed
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" title="Lihat Arsip"
                                        @click="openArchive({{ json_encode([
                                            'period' => $b->period_start->translatedFormat('F Y'),
                                            'period_slug' => $b->period_start->format('M-Y'),
                                            'disbursed_at' => optional($b->disbursed_at)->translatedFormat('d M Y'),
                                            'total_emp' => $b->payrolls_count,
                                            'grand_nett' => $b->payrolls_sum_net_salary ?? 0,
                                            'exported_files' => $b->bankExports->map(fn($e) => [
                                                'code' => $e->bank_code, 'name' => $e->bank_code,
                                                'format' => $e->format, 'accounts' => $e->accounts_count,
                                                'total' => $e->total_amount, 'filename' => $e->filename,
                                            ]),
                                            'download_url' => route('finance.export.download', [$b, '__BANK__']),
                                        ]) }})"
                                        class="p-2 rounded-lg text-on-surface-variant/40 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Belum ada riwayat payroll yang sudah dicairkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL ARSIP PAYROLL --}}
    <div x-show="showArchiveModal" x-cloak
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         @click.self="showArchiveModal = false">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-show="archiveBatch">

            <div class="flex items-center justify-between pb-4 border-b border-black/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px] text-primary">folder_open</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Arsip Payroll — <span x-text="archiveBatch?.period"></span></h3>
                        <p class="text-xs text-on-surface-variant/60 mt-0.5">Dicairkan pada <span x-text="archiveBatch?.disbursed_at"></span></p>
                    </div>
                </div>
                <button type="button" @click="showArchiveModal = false" class="text-on-surface-variant/40 hover:text-on-surface transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-3 bg-surface-variant/20 rounded-xl p-4 text-xs">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant/50">Tanggal Cair</p>
                    <p class="font-bold font-mono text-on-surface mt-1" x-text="archiveBatch?.disbursed_at"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant/50">Total Pegawai</p>
                    <p class="font-bold font-mono text-on-surface mt-1" x-text="(archiveBatch?.total_emp || 0).toLocaleString('id-ID') + ' org'"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant/50">Grand Total Nett</p>
                    <p class="font-extrabold font-mono text-primary mt-1" x-text="'Rp' + (archiveBatch?.grand_nett || 0).toLocaleString('id-ID')"></p>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-xs font-bold text-on-surface uppercase tracking-wider">File Transfer Bank yang Sudah Diexport</p>
                <div class="space-y-2.5">
                    <template x-for="bank in (archiveBatch?.exported_files || [])" :key="bank.filename">
                        <div class="flex items-center justify-between p-3.5 rounded-xl border border-black/8 hover:border-primary/25 transition group bg-white">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-11 h-11 rounded-xl bg-white border border-black/10 flex items-center justify-center shrink-0 overflow-hidden p-1 shadow-2xs">
                                    <template x-if="bankLogos[bank.code]">
                                        <img :src="bankLogos[bank.code]" :alt="bank.name" class="w-full h-full object-contain">
                                    </template>
                                    <template x-if="!bankLogos[bank.code]">
                                        <span class="text-[10px] font-black text-on-surface-variant/70" x-text="bank.code"></span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-on-surface truncate" x-text="bank.name"></p>
                                    <p class="text-[10px] font-mono-data text-on-surface-variant/50 truncate mt-0.5" x-text="bank.format + ' · ' + bank.accounts + ' rekening · Rp' + bank.total.toLocaleString('id-ID')"></p>
                                </div>
                            </div>
                            <a :href="archiveBatch.download_url.replace('__BANK__', bank.code)"
                               class="shrink-0 ml-3 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white text-xs font-bold transition shadow-2xs">
                                <span class="material-symbols-outlined text-[15px]">download</span>
                                Unduh
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-black/5">
                <button type="button" @click="showArchiveModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="showToast" x-transition
         class="fixed bottom-6 right-6 z-[70] bg-emerald-800 text-white text-xs font-medium px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-lg">
        <span class="material-symbols-outlined text-[16px] text-emerald-300">check_circle</span>
        <span x-text="toastMsg"></span>
    </div>

</div>
@endsection