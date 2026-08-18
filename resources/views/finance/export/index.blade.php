@extends('layouts.finance')

@section('title', 'Export Bank Transfer')
@section('page-title', 'Export Bank Transfer')
@section('page-desc', 'Unduh format mass transfer batch payroll yang telah disetujui Finance untuk diunggah ke portal bank.')

@section('content')
<div x-data="{
    showExportModal: false,
    activeBatch: null,
    bankLogos: {
        BCA: 'https://i.pinimg.com/736x/27/71/54/2771540fa7259e0bd0cdfae464385480.jpg',
        MANDIRI: 'https://i.pinimg.com/736x/0b/ed/5c/0bed5c44c43dc1efd1cbf6acf3aa1d89.jpg',
        BNI: 'https://i.pinimg.com/1200x/7a/ca/e2/7acae2a6ac351b72a5c89e2fbc545758.jpg'
    },
    openExport(batch) { this.activeBatch = batch; this.showExportModal = true; },
    toast: { show: false, message: '' },
    showToast(msg) { this.toast.message = msg; this.toast.show = true; setTimeout(() => this.toast.show = false, 3000); }
}" x-init="if ('{{ session('success') }}') showToast('{{ session('success') }}')">

    <div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3.5 flex items-center gap-3 mb-6">
        <span class="material-symbols-outlined text-[20px] text-[#0B3D2E] shrink-0">info</span>
        <p class="text-xs text-emerald-800 font-medium leading-relaxed">
            Generate lalu unduh format mass transfer untuk diunggah manual ke portal Corporate Internet Banking (KlikBCA Bisnis, Mandiri Kopra, BNI Direct, dll).
        </p>
    </div>

    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-medium text-gray-800">Batch Approved by Finance</h2>
            <p class="text-xs text-gray-500 mt-1">Generate file export bank, lalu unduh per bank</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-8 py-4">Periode Gaji</th>
                        <th class="px-6 py-4">Disetujui Oleh</th>
                        <th class="px-6 py-4 text-right">Total Pegawai</th>
                        <th class="px-6 py-4 text-right">Grand Total Nett</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-8 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($batches as $b)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[20px] text-[#0B3D2E]">calendar_month</span>
                                </div>
                                <span class="font-medium text-sm text-gray-800 group-hover:text-[#0B3D2E] transition-colors">{{ $b->period_start->translatedFormat('F Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">{{ $b->approvedBy->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right  text-sm font-medium">{{ number_format($b->payrolls_count, 0, ',', '.') }} org</td>
                        <td class="px-6 py-4 text-right  text-sm font-semibold text-[#0B3D2E]">Rp{{ number_format($b->payrolls_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($b->bankExports->isNotEmpty())
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-[#0B3D2E] border border-gray-200">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Sudah Diexport
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-gray-700 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-50 animate-pulse"></span>
                                    Belum Diexport
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-4 text-center">
                            @if ($b->bankExports->isEmpty())
                                <form method="POST" action="{{ route('finance.export.generate', $b) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white hover:bg-[#043927] shadow-sm text-xs font-medium px-4 py-2 rounded-md transition">
                                        <span class="material-symbols-outlined text-[16px]">sync</span>
                                        Generate File
                                    </button>
                                </form>
                            @else
                                <button type="button" @click="openExport({{ json_encode([
                                        'period' => $b->period_start->translatedFormat('F Y'),
                                        'batch_id' => $b->id,
                                        'banks' => $b->bankExports->map(fn($e) => [
                                            'code' => $e->bank_code, 'name' => $e->bank_code,
                                            'format' => $e->format, 'accounts' => $e->accounts_count,
                                            'total' => $e->total_amount, 'filename' => $e->filename,
                                        ]),
                                        'download_url' => route('finance.export.download', [$b, '__BANK__']),
                                    ]) }})"
                                        class="inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white hover:bg-[#043927] shadow-sm text-xs font-medium px-4 py-2 rounded-md transition">
                                    <span class="material-symbols-outlined text-[16px]">upload_file</span>
                                    Lihat & Unduh
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-12 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                Tidak ada batch yang siap diexport saat ini.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $batches->links() }}
        </div>
    </div>


    <div x-show="showExportModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.self="showExportModal = false">

        <div class="bg-white rounded-md w-full max-w-lg shadow-sm overflow-hidden border border-gray-100"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-show="activeBatch">

            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center">
                        </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Export Bank Transfer</h3>
                        <p class="text-xs text-gray-500 mt-1" x-text="'Periode ' + activeBatch?.period"></p>
                    </div>
                </div>
                <button type="button" @click="showExportModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="px-8 py-6 space-y-4">
                <template x-for="bank in (activeBatch?.banks || [])" :key="bank.filename">
                    <div class="flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-[#0B3D2E]/30 hover:bg-gray-50 transition group shadow-sm">
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
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="bank.name"></p>
                                <p class="text-[11px]  text-gray-500 truncate mt-1" x-text="bank.format + ' · ' + bank.accounts + ' rekening · Rp' + bank.total.toLocaleString('id-ID')"></p>
                            </div>
                        </div>
                        <a :href="activeBatch.download_url.replace('__BANK__', bank.code)"
                           class="shrink-0 ml-4 w-10 h-10 rounded-md flex items-center justify-center bg-gray-50 text-[#0B3D2E] hover:bg-[#0B3D2E] hover:text-white transition shadow-sm border border-gray-200">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                        </a>
                    </div>
                </template>

                <p class="text-[11px] text-gray-500 leading-relaxed pt-2 text-center">
                    Unduh format mass transfer untuk diunggah secara manual ke portal Corporate Internet Banking.
                </p>
            </div>

            <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end bg-gray-50">
                <button type="button" @click="showExportModal = false"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-white transition shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 right-6 z-[70] flex items-center gap-3 px-5 py-3.5 rounded-md shadow-sm bg-[#0B3D2E] border border-gray-200/30 text-white text-sm  max-w-xs">
        <span class="material-symbols-outlined text-[20px] text-emerald-100">check_circle</span>
        <span x-text="toast.message" class="font-medium leading-tight"></span>
    </div>

</div>
@endsection
