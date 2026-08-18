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


    <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-md p-1.5 w-fit shadow-sm mb-6">
        <button @click="tab = 'pending'"
            :class="tab === 'pending' ? 'bg-[#0B3D2E] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0B3D2E]'"
            class="px-4 py-2 rounded-md text-xs font-medium transition-all duration-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">pending_actions</span>
            Pending Approval
            <span :class="tab === 'pending' ? 'bg-white/20 text-white' : 'bg-gray-50 text-gray-700'"
                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full transition">
                {{ $pendingBatches->count() }}
            </span>
        </button>
        <button @click="tab = 'ready'"
            :class="tab === 'ready' ? 'bg-[#0B3D2E] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0B3D2E]'"
            class="px-4 py-2 rounded-md text-xs font-medium transition-all duration-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">task_alt</span>
            Ready to Disburse
            <span :class="tab === 'ready' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-[#0B3D2E]'"
                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full transition">
                {{ $readyBatches->count() }}
            </span>
        </button>
        <button @click="tab = 'completed'"
            :class="tab === 'completed' ? 'bg-[#0B3D2E] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-[#0B3D2E]'"
            class="px-4 py-2 rounded-md text-xs font-medium transition-all duration-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            Completed
        </button>
    </div>


    <div x-show="tab === 'pending'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3.5 flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[20px] text-gray-700">info</span>
            <p class="text-xs text-gray-700 font-medium">
                Batch berikut telah disubmit HR dan menunggu review serta persetujuan Finance sebelum dapat dicairkan.
            </p>
        </div>

        <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-medium text-gray-800">Batch Gaji Menunggu Persetujuan</h2>
                <p class="text-xs text-gray-500 mt-1">Review kalkulasi PPh21 TER & BPJS sebelum menyetujui disbursement</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-8 py-4">Periode Gaji</th>
                            <th class="px-6 py-4">Dikirim Oleh</th>
                            <th class="px-6 py-4">Waktu Submit</th>
                            <th class="px-6 py-4 text-right">Total Pegawai</th>
                            <th class="px-6 py-4 text-right">Grand Total Nett</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($pendingBatches as $b)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    
                                    <span class="font-medium text-sm text-gray-800 group-hover:text-[#0B3D2E] transition-colors">{{ $b->period_start->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $b->submittedBy->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs  text-gray-500">{{ optional($b->submitted_at)->translatedFormat('d M Y, H.i') ?? '-' }} WIB</td>
                            <td class="px-6 py-4 text-right  text-sm font-medium">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                            <td class="px-6 py-4 text-right  text-sm font-semibold text-[#0B3D2E]">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-gray-700 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-50 animate-pulse"></span>
                                    Pending Finance
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <a href="{{ route('finance.payroll.show', $b) }}"
                                   class="inline-flex items-center bg-[#0B3D2E] text-white text-xs font-medium px-4 py-2 rounded-md hover:bg-[#043927] shadow-sm transition">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Tidak ada batch payroll yang menunggu persetujuan Finance saat ini.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-6 pb-6">
                {{ $pendingBatches->links() }}
            </div>
        </div>
    </div>


    <div x-show="tab === 'ready'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none;">

        <div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3.5 flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[20px] text-[#0B3D2E]">check_circle</span>
            <p class="text-xs text-emerald-800 font-medium">
                Batch berikut telah disetujui Finance dan siap untuk proses export & disbursement bank.
            </p>
        </div>

        <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-medium text-gray-800">Batch Siap Dicairkan</h2>
                <p class="text-xs text-gray-500 mt-1">Lanjutkan ke menu Export Bank Transfer</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-8 py-4">Periode Gaji</th>
                            <th class="px-6 py-4">Disetujui Oleh</th>
                            <th class="px-6 py-4">Waktu Approve</th>
                            <th class="px-6 py-4 text-right">Total Pegawai</th>
                            <th class="px-6 py-4 text-right">Grand Total Nett</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($readyBatches as $b)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    
                                    <span class="font-medium text-sm text-gray-800 group-hover:text-[#0B3D2E] transition-colors">{{ $b->period_start->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $b->approvedBy->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs  text-gray-500">{{ optional($b->approved_finance_at)->translatedFormat('d M Y, H.i') ?? '-' }} WIB</td>
                            <td class="px-6 py-4 text-right  text-sm font-medium">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                            <td class="px-6 py-4 text-right  text-sm font-semibold text-[#0B3D2E]">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-[#0B3D2E] border border-gray-200">
                                    <span class="material-symbols-outlined text-[14px]">verified</span>
                                    Approved by Finance
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <a href="{{ route('finance.export.index') }}"
                                   class="inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white text-xs font-medium px-4 py-2 rounded-md hover:bg-[#043927] shadow-sm transition">
                                    <span class="material-symbols-outlined text-[16px]">upload_file</span>
                                    Export Bank Transfer
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada batch yang siap dicairkan.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-6 pb-6">
                {{ $readyBatches->links() }}
            </div>
        </div>
    </div>


    <div x-show="tab === 'completed'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none;">

        <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-medium text-gray-800">Riwayat Payroll Selesai</h2>
                <p class="text-xs text-gray-500 mt-1">Batch yang sudah dicairkan dan slip gaji telah dipublish ke karyawan</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-8 py-4">Periode Gaji</th>
                            <th class="px-6 py-4">Tanggal Cair</th>
                            <th class="px-6 py-4 text-right">Total Pegawai</th>
                            <th class="px-6 py-4 text-right">Grand Total Nett</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Arsip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($completedBatches as $b)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    
                                    <span class="font-medium text-sm text-gray-800">{{ $b->period_start->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs  text-gray-600">{{ optional($b->disbursed_at)->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right  text-sm font-medium text-gray-700">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                            <td class="px-6 py-4 text-right  text-sm font-semibold text-gray-800">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="material-symbols-outlined text-[14px]">done_all</span>
                                    Disbursed
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
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
                                        class="p-2 rounded-lg text-gray-400 hover:text-[#0B3D2E] hover:bg-gray-50 transition">
                                    <span class="material-symbols-outlined text-[20px]">folder_open</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada riwayat payroll yang sudah dicairkan.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-6 pb-6">
                {{ $completedBatches->links() }}
            </div>
        </div>
    </div>


    <div x-show="showArchiveModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showArchiveModal = false">
        <div class="bg-white rounded-md max-w-xl w-full p-8 shadow-sm space-y-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-show="archiveBatch">

            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                        </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Arsip Payroll — <span x-text="archiveBatch?.period"></span></h3>
                        <p class="text-xs text-gray-500 mt-1">Dicairkan pada <span x-text="archiveBatch?.disbursed_at"></span></p>
                    </div>
                </div>
                <button type="button" @click="showArchiveModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-4 bg-gray-50 rounded-md p-5 text-sm border border-gray-100">
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-widest text-gray-500">Tanggal Cair</p>
                    <p class="font-medium  text-gray-800 mt-1.5" x-text="archiveBatch?.disbursed_at"></p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-widest text-gray-500">Total Pegawai</p>
                    <p class="font-medium  text-gray-800 mt-1.5" x-text="(archiveBatch?.total_emp || 0).toLocaleString('id-ID') + ' org'"></p>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-widest text-gray-500">Grand Total Nett</p>
                    <p class="font-semibold  text-[#0B3D2E] mt-1.5" x-text="'Rp' + (archiveBatch?.grand_nett || 0).toLocaleString('id-ID')"></p>
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-xs font-medium text-gray-800 uppercase tracking-widest">File Transfer Bank yang Sudah Diexport</p>
                <div class="space-y-3">
                    <template x-for="bank in (archiveBatch?.exported_files || [])" :key="bank.filename">
                        <div class="flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-[#0B3D2E]/30 transition group bg-white shadow-sm">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-12 h-12 rounded-md bg-white border border-gray-100 flex items-center justify-center shrink-0 overflow-hidden p-1.5 shadow-sm">
                                    <template x-if="bankLogos[bank.code]">
                                        <img :src="bankLogos[bank.code]" :alt="bank.name" class="w-full h-full object-contain">
                                    </template>
                                    <template x-if="!bankLogos[bank.code]">
                                        <span class="text-[11px] font-black text-gray-500" x-text="bank.code"></span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="bank.name"></p>
                                    <p class="text-[11px]  text-gray-500 truncate mt-1" x-text="bank.format + ' · ' + bank.accounts + ' rekening · Rp' + bank.total.toLocaleString('id-ID')"></p>
                                </div>
                            </div>
                            <a :href="archiveBatch.download_url.replace('__BANK__', bank.code)"
                               class="shrink-0 ml-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-[#0B3D2E] hover:bg-[#0B3D2E] hover:text-white text-xs font-medium transition shadow-sm border border-gray-200">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                Unduh
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="showArchiveModal = false"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <div x-show="showToast" x-transition
         class="fixed bottom-6 right-6 z-[70] bg-[#0B3D2E] text-white text-sm font-medium px-5 py-3 rounded-md flex items-center gap-3 shadow-sm border border-gray-200/30">
        <span class="material-symbols-outlined text-[20px] text-emerald-100">check_circle</span>
        <span x-text="toastMsg" class="font-medium"></span>
    </div>

</div>
@endsection
