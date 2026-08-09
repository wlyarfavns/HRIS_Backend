@extends('layouts.hr')

@section('title', 'Persetujuan Reimbursement')
@section('page-title', 'Persetujuan Reimbursement')
@section('page-desc', 'Verifikasi klaim pengeluaran karyawan sebelum diteruskan ke Tim Finance.')

@php
    $stats = [
        ['label' => 'Total Klaim Pending', 'value' => 'Rp14.250.000', 'icon' => 'receipt_long', 'color' => 'text-amber-700'],
        ['label' => 'Jumlah Pengajuan', 'value' => '23 Klaim', 'icon' => 'fact_check', 'color' => 'text-primary'],
        ['label' => 'Klaim Siap ke Finance', 'value' => '8 Pengajuan', 'icon' => 'account_balance_wallet', 'color' => 'text-primary'],
        ['label' => 'Rata-rata Nominal Klaim', 'value' => 'Rp619.500', 'icon' => 'query_stats', 'color' => 'text-on-surface'],
    ];

    $claims = [
        [
            'nip' => 'EMP-01044', 'name' => 'Siti Aminah', 'avatar' => 44, 'dept' => 'Sales',
            'category' => 'Bensin & Parkir Client', 'amount' => 350000, 'status' => 'Pending Finance',
            'receipt' => 'struk_bensin_pertamina_0508.pdf', 'date' => '05 Agu 2026', 'spv' => 'Andy Bernard (Disetujui)',
            'desc' => 'Perjalanan dinas meeting dengan client PT Bank Mega Kuningan',
        ],
        [
            'nip' => 'EMP-00567', 'name' => 'Toby Flenderson', 'avatar' => 61, 'dept' => 'HR Operations',
            'category' => 'Alat Tulis Kantor & Meterai', 'amount' => 275000, 'status' => 'Pending HR',
            'receipt' => 'struk_gramedia_meterai.pdf', 'date' => '06 Agu 2026', 'spv' => 'Sarah Johnson (Disetujui)',
            'desc' => 'Pembelian 10 lembar e-meterai & perlengkapan onboarding karyawan baru',
        ],
        [
            'nip' => 'EMP-00933', 'name' => 'Oscar Martinez', 'avatar' => 27, 'dept' => 'Finance',
            'category' => 'Makan Lembur Tim', 'amount' => 120000, 'status' => 'Approved',
            'receipt' => 'struk_grabfood_makan.pdf', 'date' => '02 Agu 2026', 'spv' => 'Fajar Nugroho (Disetujui)',
            'desc' => 'Makan malam lembur rekonsiliasi audit pajak semester 1',
        ],
        [
            'nip' => 'EMP-00231', 'name' => 'Michael Scott', 'avatar' => 14, 'dept' => 'Management',
            'category' => 'Entertainment & Jamuan Klien', 'amount' => 1850000, 'status' => 'Pending HR',
            'receipt' => 'invoice_dinner_dharmawangsa.pdf', 'date' => '07 Agu 2026', 'spv' => 'Direksi (Disetujui)',
            'desc' => 'Jamuan makan malam prospek enterprise ERP dengan VP Dentsu',
        ],
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
    openReceipt(claim) {
        this.selectedClaim = claim;
        this.showReceiptModal = true;
    }
}">

    {{-- METRIC CARDS --}}
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

    {{-- TABLE REIMBURSEMENT --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Verifikasi Klaim Reimbursement</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Alur verifikasi: Pengajuan &rarr; SPV &rarr; HR Review &rarr; Finance Verification &rarr; Disbursement</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" placeholder="Cari nama atau kategori..."
                           class="w-56 pl-9 pr-3 py-2 bg-surface-variant/10 rounded-xl text-xs border border-black/10
                                  focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5 min-w-[200px]">Karyawan</th>
                        <th class="px-4 py-3.5 min-w-[220px]">Kategori Klaim</th>
                        <th class="px-4 py-3.5 text-right min-w-[120px]">Nominal</th>
                        <th class="px-4 py-3.5 min-w-[130px]">Bukti Struk</th>
                        <th class="px-4 py-3.5 min-w-[180px]">Persetujuan SPV</th>
                        <th class="px-4 py-3.5 min-w-[140px]">Status</th>
                        <th class="px-6 py-3.5 text-center min-w-[170px]">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($claims as $c)
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
                                <p class="text-[11px] text-on-surface-variant/60 line-clamp-1 mt-0.5">{{ $c['desc'] }}</p>
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
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-on-surface-variant/80">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                                    {{ $c['spv'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $badge[$c['status']] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $c['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if ($c['status'] === 'Pending HR')
                                    <button type="button" @click="openReceipt({{ json_encode($c) }})"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition shadow-xs whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">verified</span>
                                        Verifikasi Klaim
                                    </button>
                                @elseif ($c['status'] === 'Pending Finance')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px] text-amber-600">hourglass_top</span>
                                        Di Finance
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                                        Disetujui
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL STRUK & VERIFIKASI REIMBURSEMENT --}}
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
                        <h3 class="text-base font-bold text-on-surface">Detail Bukti Pengeluaran &amp; Verifikasi</h3>
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
                        <p class="text-on-surface-variant/60">Total Klaim Diajukan</p>
                        <p class="text-xl font-extrabold font-mono text-primary mt-0.5"
                           x-text="selectedClaim ? 'Rp' + Number(selectedClaim.amount).toLocaleString('id-ID') : ''"></p>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200"
                          x-text="selectedClaim ? selectedClaim.status : ''"></span>
                </div>

                {{-- STRUK SIMULATION PREVIEW --}}
                <div class="border border-dashed border-black/20 rounded-xl p-4 bg-white flex flex-col items-center justify-center gap-2 text-center">
                    <span class="material-symbols-outlined text-[36px] text-primary/60">receipt_long</span>
                    <div>
                        <p class="font-bold text-on-surface" x-text="selectedClaim ? selectedClaim.receipt : ''"></p>
                        <p class="text-[11px] text-on-surface-variant/60 mt-0.5">Format file PDF/JPG terenkripsi &amp; terverifikasi digital</p>
                    </div>
                    <button type="button" class="mt-1 text-xs font-bold text-primary hover:underline">Unduh Berkas Asli (PDF)</button>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Keperluan Pengeluaran</label>
                    <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface" x-text="selectedClaim ? selectedClaim.desc : ''"></p>
                </div>
            </div>

            {{-- ACTION BUTTONS INSIDE MODAL --}}
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showReceiptModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showReceiptModal = false; triggerToast('Klaim berhasil ditolak', 'error')"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition">
                    Tolak Klaim
                </button>
                <button type="button" @click="showReceiptModal = false; triggerToast('Klaim berhasil diverifikasi & diteruskan ke Finance!')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Teruskan ke Finance
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