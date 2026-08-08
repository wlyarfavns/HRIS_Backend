@extends('layouts.supervisor')

@section('title', 'Persetujuan Reimbursement Tim')
@section('page-title', 'Persetujuan Reimbursement Tim')
@section('page-desc', 'Verifikasi klaim pengeluaran anggota tim Anda sebelum diteruskan ke HR & Finance.')

@php
    $stats = [
        ['label' => 'Klaim Pending Review', 'value' => '1 Pengajuan', 'icon' => 'receipt_long', 'color' => 'text-amber-700'],
        ['label' => 'Total Nominal Pending', 'value' => 'Rp350.000', 'icon' => 'payments', 'color' => 'text-primary'],
        ['label' => 'Klaim Tim Bulan Ini', 'value' => '8 Pengajuan', 'icon' => 'fact_check', 'color' => 'text-purple-700'],
        ['label' => 'Tingkat Validitas Struk', 'value' => '100% Lolos', 'icon' => 'verified', 'color' => 'text-primary'],
    ];

    $pending = [
        [
            'nip' => 'EMP-01044', 'name' => 'Siti Aminah', 'avatar' => 44, 'dept' => 'Sales Staff',
            'category' => 'Bensin & Parkir Client', 'amount' => 350000, 'date' => '05 Agu 2026',
            'receipt' => 'struk_bensin_pertamina_0508.pdf', 'notes' => 'Kunjungan meeting prospek ERP ke PT Bank Mega Kuningan',
        ],
    ];

    $history = [
        ['nip' => 'EMP-00567', 'name' => 'Toby Flenderson', 'avatar' => 61, 'category' => 'Alat Tulis Kantor', 'amount' => 275000, 'status' => 'Pending HR', 'decided' => 'Disetujui Anda, 4 Agu'],
        ['nip' => 'EMP-00933', 'name' => 'Oscar Martinez', 'avatar' => 27, 'category' => 'Makan Lembur Tim', 'amount' => 120000, 'status' => 'Approved', 'decided' => 'Disetujui Anda, 2 Agu'],
    ];

    $badge = [
        'Pending HR' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'Pending Finance' => 'bg-sky-50 text-sky-800 border border-sky-200',
        'Approved' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
    ];
@endphp

@section('content')
<div x-data="{
    showReceiptModal: false,
    selectedClaim: null,
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },
    openReceipt(c) {
        this.selectedClaim = c;
        this.showReceiptModal = true;
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
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">1 Menunggu Review</span>
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
                    @foreach ($pending as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/36?img={{ $c['avatar'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $c['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $c['name'] }}</p>
                                        <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">{{ $c['nip'] }} · {{ $c['dept'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-xs font-bold text-on-surface">{{ $c['category'] }}</p>
                                <p class="text-[11px] text-on-surface-variant/60 line-clamp-1 mt-0.5">{{ $c['notes'] }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-extrabold text-xs text-primary">
                                Rp{{ number_format($c['amount'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <button type="button" @click="openReceipt({{ json_encode($c) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-black/10 bg-surface-variant/10 hover:bg-primary/10 hover:border-primary/30 text-xs font-semibold text-primary transition whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                    Lihat Struk
                                </button>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" @click="openReceipt({{ json_encode($c) }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Setujui &amp; Teruskan
                                </button>
                            </td>
                        </tr>
                    @endforeach
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
                    @foreach ($history as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img={{ $c['avatar'] }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-black/10" alt="">
                                    <span class="font-bold text-on-surface text-xs">{{ $c['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-on-surface">{{ $c['category'] }}</td>
                            <td class="px-4 py-3.5 text-right font-mono font-extrabold text-primary text-xs">Rp{{ number_format($c['amount'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface-variant/80 font-medium">{{ $c['decided'] }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $badge[$c['status']] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $c['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
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
                        <p class="text-xs text-on-surface-variant/60" x-text="selectedClaim ? selectedClaim.name + ' · ' + selectedClaim.category : ''"></p>
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
                          x-text="selectedClaim ? selectedClaim.date : ''"></span>
                </div>

                <div class="border border-dashed border-black/20 rounded-xl p-4 bg-white flex flex-col items-center justify-center gap-2 text-center">
                    <span class="material-symbols-outlined text-[36px] text-primary/60">receipt_long</span>
                    <div>
                        <p class="font-bold text-on-surface" x-text="selectedClaim ? selectedClaim.receipt : ''"></p>
                        <p class="text-[11px] text-on-surface-variant/60 mt-0.5">File bukti tersimpan di S3 Cloud Storage</p>
                    </div>
                    <button type="button" class="mt-1 text-xs font-bold text-primary hover:underline">Unduh Berkas Asli (PDF)</button>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Keterangan Pengeluaran</label>
                    <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface leading-relaxed" x-text="selectedClaim ? selectedClaim.notes : ''"></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showReceiptModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showReceiptModal = false; triggerToast('Klaim tim ditolak', 'error')"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition">
                    Tolak Klaim
                </button>
                <button type="button" @click="showReceiptModal = false; triggerToast('Klaim tim berhasil disetujui & diteruskan ke HR Operations!')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui &amp; Teruskan ke HR
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION (THEME-MATCHED DEEP EMERALD) -->
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