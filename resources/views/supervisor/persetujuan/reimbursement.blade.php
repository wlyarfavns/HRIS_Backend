@extends('layouts.supervisor')

@section('title', 'Persetujuan Reimbursement Tim')
@section('page-title', 'Persetujuan Reimbursement Tim')
@section('page-desc', 'Verifikasi klaim pengeluaran anggota tim Anda sebelum diteruskan ke HR & Finance.')

@section('content')
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
    openReceipt(c) {
        this.selectedClaim = c;
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
            const res = await fetch(`/supervisor/persetujuan/reimbursement/${this.selectedClaim.id}/action`, {
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
    }
}">


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach ($stats as $s)
            <div class="bg-white border border-gray-100 rounded-md p-6 shadow-sm relative overflow-hidden group hover:border-[#0B3D2E]/30 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</span>
                </div>
                <p class="text-3xl font-semibold  text-gray-800 leading-none mb-2">{{ $s['value'] }}</p>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Klaim Pengeluaran Menunggu Verifikasi Anda</h2>
                <p class="text-xs text-gray-500 mt-1">Klaim yang disetujui akan diteruskan ke HR Operations dan Finance untuk pencairan dana.</p>
            </div>
            <span class="text-[11px] font-medium px-4 py-1.5 rounded-full bg-gray-50 text-gray-700 border border-gray-200">{{ $pending->count() }} Menunggu Review</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-wider">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Kategori Pengeluaran</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4">Bukti Struk</th>
                        <th class="px-8 py-4 text-center">Aksi Supervisor</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($pending as $c)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?background=E9F3EF&color=0B3D2E&name={{ urlencode($c->employee->full_name ?? '-') }}"
                                         class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="{{ $c->employee->full_name ?? '-' }}">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $c->employee->full_name ?? '-' }}</p>
                                        <p class="text-[11px]  text-gray-500 mt-0.5">
                                            {{ $c->employee->employee_id ?? '-' }} · {{ $c->employee->department->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-800">{{ $c->category }}</p>
                                <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5 max-w-[200px] truncate">{{ $c->description }}</p>
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
                            <td class="px-8 py-4 text-center">
                                <button type="button" @click="openReceipt(@js($c))"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium transition shadow-sm whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Setujui &amp; Teruskan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada klaim yang menunggu review.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-6 pb-6">
            {{ $pending->links() }}
        </div>
    </div>


    <div class="bg-white rounded-md shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-medium text-gray-800">Riwayat Persetujuan Reimbursement Tim</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-wider">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4">Keputusan Anda</th>
                        <th class="px-8 py-4">Status Terkini</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($history as $c)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?background=E9F3EF&color=0B3D2E&name={{ urlencode($c->employee->full_name ?? '-') }}"
                                         class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="">
                                    <span class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $c->employee->full_name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $c->category }}</td>
                            <td class="px-6 py-4 text-right  font-semibold text-[#0B3D2E] text-sm">Rp{{ number_format($c->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                {{ $c->status === 'rejected' ? 'Ditolak' : 'Disetujui' }} Anda, {{ $c->spv_approved_at?->translatedFormat('d M') ?? '-' }}
                            </td>
                            <td class="px-8 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-medium whitespace-nowrap
                                    {{ $c->status === 'rejected' ? 'bg-gray-50 text-gray-700 border border-gray-200' : ($c->status === 'approved' ? 'bg-gray-50 text-[#0B3D2E] border border-gray-200' : 'bg-gray-50 text-gray-700 border border-gray-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $c->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada riwayat.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-6 pb-6">
            {{ $history->links() }}
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
                        <h3 class="text-lg font-medium text-gray-800">Persetujuan Klaim Tim</h3>
                        <p class="text-xs text-gray-500" x-text="selectedClaim ? (selectedClaim.employee ? selectedClaim.employee.full_name : '') + ' · ' + selectedClaim.category : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showReceiptModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div class="p-5 rounded-md bg-gray-50 border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 font-medium text-xs">Nominal Klaim:</p>
                        <p class="text-2xl font-semibold  text-[#0B3D2E] mt-1"
                           x-text="selectedClaim ? 'Rp' + Number(selectedClaim.amount).toLocaleString('id-ID') : ''"></p>
                    </div>
                    <span class="text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-gray-700 border border-gray-200"
                          x-text="selectedClaim ? selectedClaim.claim_date : ''"></span>
                </div>

                <div class="border-2 border-dashed border-gray-200 rounded-md p-6 bg-white flex flex-col items-center justify-center gap-3 text-center">
                    <div>
                        <p class="font-medium text-gray-800" x-text="selectedClaim ? (selectedClaim.receipt_path ? selectedClaim.receipt_path.split('/').pop() : 'Tidak ada berkas') : ''"></p>
                        <p class="text-[11px] text-gray-500 mt-1">Format file PDF/JPG terenkripsi &amp; terverifikasi digital</p>
                    </div>
                    <a :href="selectedClaim ? selectedClaim.receipt_url : '#'" target="_blank"
                       x-show="selectedClaim && selectedClaim.receipt_url"
                       class="mt-2 text-xs font-medium text-[#0B3D2E] hover:underline bg-gray-50 px-4 py-2 rounded-md border border-gray-200 transition">Cek Berkas</a>
                </div>

                <div>
                    <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">Keterangan Pengeluaran</label>
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
                    Batal
                </button>
                <button type="button" @click="submitAction('reject')" :disabled="processing"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-50 text-sm font-medium transition shadow-sm disabled:opacity-50">
                    <span x-text="rejecting ? 'Kirim Penolakan' : 'Tolak Klaim'"></span>
                </button>
                <button type="button" @click="submitAction('approve')" :disabled="processing" x-show="!rejecting"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center gap-2 transition disabled:opacity-50">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui &amp; Teruskan
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
