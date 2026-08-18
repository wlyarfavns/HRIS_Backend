@extends('layouts.hr')

@section('title', 'Persetujuan Reimbursement')
@section('page-title', 'Persetujuan Reimbursement')
@section('page-desc', 'Verifikasi klaim pengeluaran karyawan sebelum diteruskan ke Tim Finance.')

@php
    $badge = [
        'pending_spv'     => 'bg-gray-100 text-gray-500',
        'pending_hr'      => 'bg-gray-50 text-gray-700',
        'pending_finance' => 'bg-gray-50 text-gray-700',
        'approved'        => 'bg-gray-50 text-[#0B3D2E]',
        'rejected'        => 'bg-gray-50 text-gray-700',
    ];
@endphp

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
            const res = await fetch(`/hr/persetujuan/reimbursement/${this.selectedClaim.id}/action`, {
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


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @foreach ($stats as $s)
            @php
                $colorMap = [
                    'text-gray-700' => 'text-gray-700',
                    'text-sky-700' => 'text-gray-700',
                    'text-[#0B3D2E]' => 'text-[#0B3D2E]',
                    'text-rose-700' => 'text-gray-700',
                ];
                $cleanColor = $colorMap[$s['color']] ?? 'text-gray-800';
            @endphp
            <div class="bg-white rounded-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                        <span class="material-symbols-outlined text-[18px]">{{ $s['icon'] }}</span>
                    </div>
                </div>
                <p class="text-3xl font-semibold  {{ $cleanColor }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
            <div>
                <h2 class="text-base font-medium text-gray-800">Daftar Verifikasi Klaim Reimbursement</h2>
                <p class="text-xs text-gray-500 mt-1">Alur verifikasi: Pengajuan  SPV  HR Review  Finance Verification  Disbursement</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" id="searchClaim" placeholder="Cari nama atau kategori..."
                           class="w-64 pl-10 pr-4 py-2.5 bg-white rounded-md text-sm border border-gray-200
                                  focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition"
                           onkeydown="if(event.key==='Enter'){window.location.href='{{ route('hr.approvals.reimbursement') }}?q='+encodeURIComponent(this.value)}">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4 min-w-[200px]">Karyawan</th>
                        <th class="px-6 py-4 min-w-[220px]">Kategori Klaim</th>
                        <th class="px-6 py-4 text-right min-w-[120px]">Nominal</th>
                        <th class="px-6 py-4 min-w-[130px]">Bukti Struk</th>
                        <th class="px-6 py-4 min-w-[180px]">Persetujuan SPV</th>
                        <th class="px-6 py-4 min-w-[140px]">Status</th>
                        <th class="px-6 py-4 text-center min-w-[170px]">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($claims as $c)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $initials = strtoupper(substr($c->employee->full_name ?? '?', 0, 1));
                                    @endphp
                                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 border border-gray-300
                                                flex items-center justify-center text-xs font-medium shrink-0 uppercase">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm leading-tight">{{ $c->employee->full_name ?? '-' }}</p>
                                        <p class="text-[11px]  text-gray-500 mt-0.5">
                                            {{ $c->employee->employee_id ?? '-' }} · {{ $c->employee->department->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-800">{{ $c->category }}</p>
                                <p class="text-[11px] text-gray-500 line-clamp-1 mt-1">{{ $c->description }}</p>
                            </td>
                            <td class="px-6 py-4 text-right  font-semibold text-sm text-[#0B3D2E]">
                                Rp{{ number_format($c->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" @click="openReceipt(@js($c))"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-200 hover:text-[#0B3D2E] text-[11px] font-semibold text-gray-600 transition whitespace-nowrap shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                    Lihat Struk
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-700">
                                    @if ($c->spv)
                                        <span class="material-symbols-outlined text-[18px] text-[#0B3D2E]">check_circle</span>
                                        {{ $c->spv->name }} (Disetujui)
                                    @else
                                        <span class="text-gray-500">Menunggu SPV</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-medium uppercase tracking-wider {{ $badge[$c->status] ?? '' }}">
                                    {{ $c->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($c->status === 'pending_hr')
                                    <button type="button" @click="openReceipt(@js($c))"
                                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium transition shadow-sm w-full whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">verified</span>
                                        Verifikasi Klaim
                                    </button>
                                @elseif ($c->status === 'pending_finance')
                                    <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-gray-700 text-xs font-medium w-full whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">hourglass_top</span>
                                        Di Finance
                                    </span>
                                @elseif ($c->status === 'approved')
                                    <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-[#0B3D2E] text-xs font-medium w-full whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        Disetujui
                                    </span>
                                @elseif ($c->status === 'rejected')
                                    <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-gray-700 text-xs font-medium w-full whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-100 text-gray-500 text-xs font-medium w-full whitespace-nowrap">
                                        Menunggu SPV
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">
                                Belum ada pengajuan reimbursement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div x-show="showReceiptModal" x-cloak
         class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
         @click.self="showReceiptModal = false">
        <div class="bg-white rounded-md max-w-lg w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in-95 duration-150 border border-gray-100" x-show="selectedClaim">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 text-[#0B3D2E] flex items-center justify-center border border-gray-200">
                        </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Detail Bukti Pengeluaran &amp; Verifikasi</h3>
                        <p class="text-xs text-gray-500 mt-1" x-text="selectedClaim ? selectedClaim.employee.full_name + ' · ' + selectedClaim.category : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showReceiptModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div class="p-5 rounded-md bg-gray-50 border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-medium">Total Klaim Diajukan</p>
                        <p class="text-2xl font-semibold  text-[#0B3D2E] mt-1"
                           x-text="selectedClaim ? 'Rp' + Number(selectedClaim.amount).toLocaleString('id-ID') : ''"></p>
                    </div>
                    <span class="text-[10px] font-medium px-3 py-1.5 rounded-lg uppercase tracking-wider bg-gray-50 text-gray-700"
                          x-text="selectedClaim ? selectedClaim.status_label : ''"></span>
                </div>


                <div class="border-2 border-dashed border-gray-200 rounded-md p-6 bg-white flex flex-col items-center justify-center gap-3 text-center">
                    <div>
                        <p class="font-medium text-gray-800 text-sm" x-text="selectedClaim ? (selectedClaim.receipt_path ? selectedClaim.receipt_path.split('/').pop() : 'Tidak ada berkas') : ''"></p>
                        <p class="text-[11px] text-gray-500 mt-1">Format file PDF/JPG terenkripsi &amp; terverifikasi digital</p>
                    </div>
                    <a :href="selectedClaim ? selectedClaim.receipt_url : '#'" target="_blank"
                       x-show="selectedClaim && selectedClaim.receipt_url"
                       class="mt-2 text-xs font-medium text-gray-700 hover:text-gray-700 hover:underline">Cek Berkas</a>
                </div>

                <div>
                    <label class="font-medium text-gray-400 uppercase text-[10px] tracking-wide block mb-1.5">Keperluan Pengeluaran</label>
                    <p class="p-4 rounded-md border border-gray-100 bg-gray-50 text-gray-700 min-h-[80px]" x-text="selectedClaim ? selectedClaim.description : ''"></p>
                </div>


                <div x-show="rejecting" x-cloak>
                    <label class="font-medium text-gray-700 uppercase text-[10px] tracking-wide block mb-1.5">Alasan Penolakan</label>
                    <textarea x-model="rejectReason" rows="3"
                              class="w-full p-4 rounded-md border border-gray-200 bg-gray-50 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-gray-200 transition resize-none"
                              placeholder="Jelaskan alasan penolakan klaim ini..."></textarea>
                </div>
            </div>


            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="showReceiptModal = false"
                        class="px-5 py-2.5 rounded-md bg-gray-100 text-sm font-medium text-gray-700 hover:bg-gray-200 transition"
                        :disabled="processing">
                    Batal
                </button>
                <button type="button" @click="submitAction('reject')" :disabled="processing"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-50 text-sm font-medium transition disabled:opacity-50">
                    <span x-text="rejecting ? 'Kirim Penolakan' : 'Tolak Klaim'"></span>
                </button>
                <button type="button" @click="submitAction('approve')" :disabled="processing"
                        x-show="!rejecting"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center gap-2 transition disabled:opacity-50">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Teruskan ke Finance
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
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-md shadow-sm text-white font-medium text-sm border border-gray-200/30 "
         :class="{
             'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
             'bg-gray-50 border-gray-200/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-white' : 'text-emerald-100'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-sm font-medium"></span>
    </div>

</div>
@endsection
