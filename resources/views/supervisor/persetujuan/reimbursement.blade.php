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

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- TABLE PENDING REVIEW --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Klaim Pengeluaran Menunggu Verifikasi Anda</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Klaim yang disetujui akan diteruskan ke HR Operations dan Finance untuk pencairan dana.</p>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">{{ $pending->count() }} Menunggu Review</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5 min-w-[200px]">Karyawan</th>
                        <th class="px-4 py-3.5 min-w-[220px]">Kategori Pengeluaran</th>
                        <th class="px-4 py-3.5 text-right min-w-[120px]">Nominal</th>
                        <th class="px-4 py-3.5 min-w-[130px]">Bukti Struk</th>
                        <th class="px-6 py-3.5 text-center min-w-[170px]">Aksi Supervisor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @forelse ($pending as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?background=E9F3EF&color=0B3D2E&name={{ urlencode($c->employee->full_name ?? '-') }}"
                                         class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $c->employee->full_name ?? '-' }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $c->employee->full_name ?? '-' }}</p>
                                        <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">
                                            {{ $c->employee->employee_id ?? '-' }} · {{ $c->employee->department->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-xs font-bold text-on-surface">{{ $c->category }}</p>
                                <p class="text-[11px] text-on-surface-variant/60 line-clamp-1 mt-0.5">{{ $c->description }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-extrabold text-xs text-primary">
                                Rp{{ number_format($c->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <button type="button" @click="openReceipt(@js($c))"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-black/10 bg-surface-variant/10 hover:bg-primary/10 hover:border-primary/30 text-xs font-semibold text-primary transition whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                    Lihat Struk
                                </button>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" @click="openReceipt(@js($c))"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Setujui &amp; Teruskan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant/50">Belum ada klaim yang menunggu review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIWAYAT KEPUTUSAN --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Riwayat Persetujuan Reimbursement Tim</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5 text-right">Nominal</th>
                        <th class="px-4 py-3.5">Keputusan Anda</th>
                        <th class="px-4 py-3.5">Status Terkini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @forelse ($history as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?background=E9F3EF&color=0B3D2E&name={{ urlencode($c->employee->full_name ?? '-') }}"
                                         class="w-7 h-7 rounded-full object-cover shrink-0 border border-black/10" alt="">
                                    <span class="font-bold text-on-surface text-xs">{{ $c->employee->full_name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-on-surface">{{ $c->category }}</td>
                            <td class="px-4 py-3.5 text-right font-mono font-extrabold text-primary text-xs">Rp{{ number_format($c->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface-variant/80 font-medium">
                                {{ $c->status === 'rejected' ? 'Ditolak' : 'Disetujui' }} Anda, {{ $c->spv_approved_at?->translatedFormat('d M') ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                                    {{ $c->status === 'rejected' ? 'bg-rose-50 text-rose-800 border border-rose-200' : ($c->status === 'approved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $c->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant/50">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL REVIEW STRUK --}}
    <div x-show="showReceiptModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showReceiptModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150" x-show="selectedClaim">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">receipt</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Persetujuan Klaim Tim</h3>
                        <p class="text-xs text-on-surface-variant/60" x-text="selectedClaim ? selectedClaim.employee.full_name + ' · ' + selectedClaim.category : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showReceiptModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 flex items-center justify-between">
                    <div>
                        <p class="text-on-surface-variant/60">Nominal Klaim:</p>
                        <p class="text-xl font-extrabold font-mono text-primary mt-0.5"
                           x-text="selectedClaim ? 'Rp' + Number(selectedClaim.amount).toLocaleString('id-ID') : ''"></p>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200"
                          x-text="selectedClaim ? selectedClaim.claim_date : ''"></span>
                </div>

                <div class="border border-dashed border-black/20 rounded-xl p-4 bg-white flex flex-col items-center justify-center gap-2 text-center">
                    <span class="material-symbols-outlined text-[36px] text-primary/60">receipt_long</span>
                    <div>
                        <p class="font-bold text-on-surface" x-text="selectedClaim ? (selectedClaim.receipt_path ? selectedClaim.receipt_path.split('/').pop() : 'Tidak ada berkas') : ''"></p>
                        <p class="text-[11px] text-on-surface-variant/60 mt-0.5">Format file PDF/JPG terenkripsi &amp; terverifikasi digital</p>
                    </div>
                    <a :href="selectedClaim ? selectedClaim.receipt_url : '#'" target="_blank"
                       x-show="selectedClaim && selectedClaim.receipt_url"
                       class="mt-1 text-xs font-bold text-primary hover:underline">Unduh Berkas Asli</a>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Keterangan Pengeluaran</label>
                    <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface leading-relaxed" x-text="selectedClaim ? selectedClaim.description : ''"></p>
                </div>

                {{-- FORM ALASAN PENOLAKAN --}}
                <div x-show="rejecting" x-cloak>
                    <label class="font-bold text-rose-700 uppercase text-[10px] block mb-1">Alasan Penolakan</label>
                    <textarea x-model="rejectReason" rows="2"
                              class="w-full p-3 rounded-xl border border-rose-200 bg-rose-50 text-on-surface text-xs focus:outline-none focus:ring-2 focus:ring-rose-200"
                              placeholder="Jelaskan alasan penolakan klaim ini..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showReceiptModal = false" :disabled="processing"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="submitAction('reject')" :disabled="processing"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition disabled:opacity-50">
                    <span x-text="rejecting ? 'Kirim Penolakan' : 'Tolak Klaim'"></span>
                </button>
                <button type="button" @click="submitAction('approve')" :disabled="processing" x-show="!rejecting"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition disabled:opacity-50">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui &amp; Teruskan ke HR
                </button>
            </div>
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