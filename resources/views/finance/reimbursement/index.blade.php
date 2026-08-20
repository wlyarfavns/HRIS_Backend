@extends('layouts.finance')

@section('title', 'Verifikasi Reimbursement')
@section('page-title', 'Verifikasi Reimbursement')
@section('page-desc', 'Klaim pengeluaran yang telah disetujui SPV & HR, menunggu verifikasi final Finance sebelum pencairan.')

@section('content')
@php
    $alpineItems = collect($claims->items())->map(function($c) {
        return [
            'name' => strtolower($c->employee->full_name ?? '')
        ];
    })->toJson();
@endphp
<div x-data="{
    showReceiptModal: false,
    selectedClaim: null,
    rejecting: false,
    rejectReason: '',
    processing: false,
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },
    openReceipt(claim) {
        this.selectedClaim = claim;
        this.rejecting = false;
        this.rejectReason = '';
        this.showReceiptModal = true;
    },
    async submitAction(action) {
        if (action === 'reject' && !this.rejecting) {
            this.rejecting = true;
            return;
        }
        if (action === 'reject' && !this.rejectReason.trim()) {
            this.triggerToast('Alasan penolakan wajib diisi', 'error');
            return;
        }

        this.processing = true;
        try {
            const res = await fetch(`/finance/reimbursement/${this.selectedClaim.id}/action`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ action, rejection_reason: this.rejectReason }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Gagal memproses klaim');

            this.showReceiptModal = false;
            this.triggerToast(data.message, action === 'approve' ? 'success' : 'error');
            setTimeout(() => window.location.reload(), 1200);
        } catch (e) {
            this.triggerToast(e.message, 'error');
        } finally {
            this.processing = false;
        }
    },
    searchQuery: new URLSearchParams(window.location.search).get('search') || '',
    items: {{ $alpineItems }},
    get hasVisibleRows() {
        return this.items.some(i => 
            this.searchQuery === '' || i.name.includes(this.searchQuery.toLowerCase())
        );
    }
}">


    <div id="stats-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach ($stats as $s)
            <div class="bg-white rounded-md p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-[#0B3D2E]/30 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px] text-gray-500">{{ $s['icon'] }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</span>
                </div>
                <p class="text-3xl font-semibold  {{ $s['color'] }} leading-none mb-2">{{ $s['value'] }}</p>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Daftar Verifikasi Klaim Reimbursement</h2>
                <p class="text-xs text-gray-500 mt-1">Klaim tampil di sini setelah diverifikasi Supervisor &amp; HR Operations</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari klaim berdasarkan nama..."
                           class="w-72 pl-10 pr-4 py-2.5 bg-white rounded-md text-sm border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition shadow-sm">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[20px]">search</span>
                </div>
            </div>
        </div>

        <div id="table-container" class="overflow-x-auto relative">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Kategori Pengeluaran</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4">Bukti Struk</th>
                        <th class="px-6 py-4">Riwayat Verifikasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-8 py-4 text-center">Aksi Finance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($claims as $c)
                        <tr class="hover:bg-gray-50 transition group"
                            x-show="searchQuery === '' || '{{ strtolower(addslashes($c->employee->full_name ?? '')) }}'.includes(searchQuery.toLowerCase())">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?background=E9F3EF&color=0B3D2E&name={{ urlencode($c->employee->full_name ?? '-') }}"
                                         class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-gray-50"
                                         alt="{{ $c->employee->full_name ?? '-' }}">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $c->employee->full_name ?? '-' }}</p>
                                        <p class="text-[11px]  text-gray-500 mt-0.5">
                                            {{ $c->employee->employee_id ?? '-' }} &bull; {{ $c->employee->department?->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-800">{{ $c->category }}</p>
                                <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">{{ $c->description }}</p>
                            </td>
                            <td class="px-6 py-4 text-right  font-semibold text-[#0B3D2E] text-sm">
                                Rp{{ number_format($c->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" @click="openReceipt(@js($c))"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-200 text-xs font-semibold text-gray-600 hover:text-[#0B3D2E] transition whitespace-nowrap shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                    Lihat Struk
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600">
                                    @if ($c->spv)
                                        <span class="material-symbols-outlined text-[16px] text-[#0B3D2E]">check_circle</span>
                                        Disetujui {{ $c->spv->name }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-medium whitespace-nowrap
                                    {{ $c->status === 'pending_finance' ? 'bg-gray-50 text-gray-700 border border-gray-200' : ($c->status === 'approved' ? 'bg-gray-50 text-[#0B3D2E] border border-gray-200' : 'bg-gray-50 text-gray-700 border border-gray-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $c->status_label }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                @if ($c->status === 'pending_finance')
                                    <button type="button" @click="openReceipt(@js($c))"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium transition shadow-sm whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">verified</span>
                                        Verifikasi &amp; Cairkan
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-emerald-800 border border-gray-200 text-xs font-medium whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px] text-[#0B3D2E]">check_circle</span>
                                        Disetujui
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada klaim yang menunggu verifikasi Finance.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    
                    @if($claims->count() > 0)
                        <tr x-show="!hasVisibleRows" style="display: none;" x-transition>
                            <td colspan="7" class="px-8 py-12 text-center text-gray-500 text-sm">
                                <template x-if="searchQuery">
                                    <span>Tidak ada klaim yang sesuai dengan pencarian "<span x-text="searchQuery" class="font-medium text-gray-700"></span>".</span>
                                </template>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-6 pb-6">
            {{ $claims->links() }}
        </div>
    </div>


    <div x-show="showReceiptModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showReceiptModal = false">
        <div class="bg-white rounded-md max-w-lg w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in duration-200 border border-gray-100" x-show="selectedClaim">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 text-[#0B3D2E] flex items-center justify-center">
                        </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Verifikasi Reimbursement Finance</h3>
                        <p class="text-xs text-gray-500" x-text="selectedClaim ? selectedClaim.employee.full_name + ' · ' + selectedClaim.category : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showReceiptModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div class="p-5 rounded-md bg-gray-50 border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 font-medium text-xs">Nominal Transfer</p>
                        <p class="text-2xl font-semibold  text-[#0B3D2E] mt-1"
                           x-text="selectedClaim ? 'Rp' + Number(selectedClaim.amount).toLocaleString('id-ID') : ''"></p>
                    </div>
                    <span class="text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-gray-700 border border-gray-200"
                          x-text="selectedClaim ? selectedClaim.status_label : ''"></span>
                </div>

                <div class="border-2 border-dashed border-gray-200 rounded-md p-6 bg-white flex flex-col items-center justify-center gap-3 text-center">
                    <div>
                        <p class="font-medium text-gray-800" x-text="selectedClaim ? (selectedClaim.receipt_url ? selectedClaim.receipt_path.split('/').pop() : 'Foto tidak ada') : ''"></p>
                        <p class="text-[11px] text-gray-500 mt-1" x-show="selectedClaim && selectedClaim.receipt_url">Format file PDF/JPG terenkripsi &amp; terverifikasi digital</p>
                    </div>
                    <a :href="selectedClaim ? selectedClaim.receipt_url : '#'" target="_blank"
                       x-show="selectedClaim && selectedClaim.receipt_url"
                       class="mt-2 text-xs font-medium text-[#0B3D2E] hover:underline bg-gray-50 px-4 py-2 rounded-md border border-gray-200 transition">Cek Berkas</a>
                </div>

                <div>
                    <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">Rincian Keperluan Pengeluaran</label>
                    <p class="p-4 rounded-md border border-gray-200 bg-gray-50 text-gray-700 leading-relaxed" x-text="selectedClaim ? selectedClaim.description : ''"></p>
                </div>


                <div x-show="rejecting" x-cloak>
                    <label class="font-medium text-gray-700 uppercase tracking-wide text-[11px] block mb-2">Alasan Penolakan</label>
                    <textarea x-model="rejectReason" rows="2"
                              class="w-full p-4 rounded-md border border-gray-200 bg-gray-50 text-gray-800 text-sm focus:outline-none focus:border-gray-200 focus:ring-2 focus:ring-red-200/50 transition"
                              placeholder="Jelaskan alasan penolakan klaim ini..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="showReceiptModal = false" :disabled="processing"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm disabled:opacity-50">
                    <span x-text="(selectedClaim && selectedClaim.status === 'pending_finance') ? 'Batal' : 'Tutup'"></span>
                </button>
                <button type="button" @click="submitAction('reject')" :disabled="processing" x-show="selectedClaim && selectedClaim.status === 'pending_finance'"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-50 text-sm font-medium transition shadow-sm disabled:opacity-50">
                    <span x-text="rejecting ? 'Kirim Penolakan' : 'Tolak Klaim'"></span>
                </button>
                <button type="button" @click="submitAction('approve')" :disabled="processing" x-show="selectedClaim && selectedClaim.status === 'pending_finance' && !rejecting"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center gap-2 transition disabled:opacity-50">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Verifikasi &amp; Masukkan Disbursement
                </button>
            </div>
        </div>
    </div>


    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-4 rounded-md shadow-sm font-medium text-sm border "
         :class="{
             'bg-[#0B3D2E] border-gray-200/30 text-white': toast.type === 'success' || toast.type === 'info',
             'bg-gray-50 border-gray-200/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-white' : 'text-emerald-100'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="font-medium"></span>
    </div>

</div>
@endsection
