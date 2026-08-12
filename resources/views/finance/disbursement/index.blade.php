@extends('layouts.finance')

@section('title', 'Disbursement & Slip Gaji')
@section('page-title', 'Disbursement & Slip Gaji')
@section('page-desc', 'Tandai pencairan dana selesai dan distribusikan slip gaji digital ke karyawan.')

@section('content')
<div x-data="{
    activeIndex: 0,
    periodDropdownOpen: false,
    batches: {{ Js::from($batches) }},
    showDisburseModal: false,
    showPublishModal: false,
    showSlipModal: false,
    slipEmployee: null,
    search: '',
    openSlip(emp) { this.slipEmployee = emp; this.showSlipModal = true; },
    statusOf(i) { if (this.batches[i].published) return 'done'; if (this.batches[i].disbursed) return 'partial'; return 'pending'; },
    statusLabel(i) { const s = this.statusOf(i); return s === 'done' ? 'Selesai · Dipublish' : s === 'partial' ? 'Menunggu Publish' : 'Menunggu Diproses'; },
    statusIcon(i) { const s = this.statusOf(i); return s === 'done' ? 'done_all' : s === 'partial' ? 'done_all' : 'schedule'; },
    formatRupiah(n) { return 'Rp' + Number(n).toLocaleString('id-ID'); },
    matchingCount() {
        if (this.search === '') return this.batches[this.activeIndex]?.employees.length ?? 0;
        const q = this.search.toLowerCase();
        return (this.batches[this.activeIndex]?.employees ?? []).filter(e => e.name.toLowerCase().includes(q) || e.nip.toLowerCase().includes(q)).length;
    },
    toast: { show: false, message: '{{ session('success') }}' },
}" x-init="if (toast.message) { toast.show = true; setTimeout(() => toast.show = false, 3000); }">

    @if ($batches->isEmpty())
        <div class="card-flat rounded-2xl p-10 text-center">
            <span class="material-symbols-outlined text-[32px] text-on-surface-variant/30 mb-2 block">inbox</span>
            <p class="text-sm font-bold text-on-surface-variant/60">Belum ada batch yang siap didisbursement.</p>
            <p class="text-xs text-on-surface-variant/40 mt-1">Batch akan muncul di sini setelah file export bank digenerate di menu Export Bank Transfer.</p>
        </div>
    @else

    {{-- ===== PERIOD SELECTOR ===== --}}
    <div class="mb-6 relative max-w-md" @click.outside="periodDropdownOpen = false">
        <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wider mb-2">Pilih Periode Payroll</p>

        <button type="button" @click="periodDropdownOpen = !periodDropdownOpen"
                class="w-full flex items-center justify-between gap-3 bg-white border border-black/10 rounded-2xl px-4 py-3 hover:border-primary/30 transition shadow-sm">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                      :class="statusOf(activeIndex) === 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                    <span class="material-symbols-outlined text-[18px]" x-text="statusIcon(activeIndex)"></span>
                </span>
                <div class="text-left min-w-0">
                    <p class="text-sm font-extrabold text-on-surface truncate" x-text="batches[activeIndex].period"></p>
                    <p class="text-[11px] text-on-surface-variant/50 truncate" x-text="statusLabel(activeIndex)"></p>
                </div>
            </div>
            <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40 shrink-0 transition-transform"
                  :class="periodDropdownOpen ? 'rotate-180' : ''">expand_more</span>
        </button>

        <div x-show="periodDropdownOpen" x-cloak
             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
             class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-black/5 z-30 max-h-80 overflow-y-auto p-2">
            <template x-for="(b, i) in batches" :key="b.id">
                <button type="button" @click="activeIndex = i; periodDropdownOpen = false; search = ''"
                        class="w-full flex items-center justify-between gap-3 p-3 rounded-xl text-left transition hover:bg-primary/5"
                        :class="activeIndex === i ? 'bg-primary/10' : ''">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                              :class="statusOf(i) === 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                            <span class="material-symbols-outlined text-[16px]" x-text="statusIcon(i)"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-on-surface truncate" x-text="b.period"></p>
                            <p class="text-[10px] text-on-surface-variant/40 truncate" x-text="'Diexport ' + b.exported_at"></p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] font-bold px-2 py-0.5 rounded-full inline-block"
                           :class="statusOf(i) === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                           x-text="statusOf(i) === 'done' ? 'Selesai' : (statusOf(i) === 'partial' ? 'Dicairkan' : 'Pending')"></p>
                        <p class="text-[10px] font-mono-data text-on-surface-variant/50 mt-1" x-text="formatRupiah(b.grand_nett)"></p>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <template x-for="(b, i) in batches" :key="b.id">
    <div x-show="activeIndex === i" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- ===== BANNER PANDUAN ===== --}}
        <div class="rounded-2xl p-4 mb-5 flex items-center justify-between gap-4 flex-wrap border transition-colors"
             :class="statusOf(i) === 'pending' ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200'">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white"
                      :class="statusOf(i) === 'pending' ? 'bg-amber-500' : (statusOf(i) === 'partial' ? 'bg-emerald-600' : 'bg-emerald-500')">
                    <span class="material-symbols-outlined text-[20px]"
                          x-text="statusOf(i) === 'done' ? 'task_alt' : (statusOf(i) === 'partial' ? 'mark_email_read' : 'priority_high')"></span>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-extrabold" :class="statusOf(i) === 'pending' ? 'text-amber-800' : 'text-emerald-800'"
                       x-text="statusOf(i) === 'done' ? 'Batch ini sudah selesai diproses' : (statusOf(i) === 'partial' ? 'Tinggal 1 langkah lagi: publish slip gaji' : 'Konfirmasi Pencairan Dana Gaji')"></p>
                    <p class="text-xs mt-0.5" :class="statusOf(i) === 'pending' ? 'text-amber-700/70' : 'text-emerald-700/70'"
                       x-text="statusOf(i) === 'done' ? 'Karyawan sudah bisa melihat slip gajinya masing-masing.' : (statusOf(i) === 'partial' ? 'Dana sudah ditransfer. Publish slip supaya karyawan bisa mengaksesnya.' : 'Pastikan seluruh transfer bank ke karyawan sudah sukses, baru tandai batch ini.')"></p>
                </div>
            </div>
            <button type="button" x-show="!b.disbursed" @click="showDisburseModal = true"
                    class="shrink-0 inline-flex items-center gap-1.5 bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:brightness-110 shadow-sm transition">
                <span class="material-symbols-outlined text-[16px]">done_all</span>
                Mark as Disbursed
            </button>
            <button type="button" x-show="b.disbursed && !b.published" @click="showPublishModal = true"
                    class="shrink-0 inline-flex items-center gap-1.5 bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:brightness-110 shadow-sm transition">
                <span class="material-symbols-outlined text-[16px]">mark_email_read</span>
                Publish Sekarang
            </button>
        </div>

        <div class="grid grid-cols-5 gap-5 items-start">

            {{-- ===== KIRI: TABEL KARYAWAN ===== --}}
            <div class="col-span-3 card-flat rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-black/5 flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="text-sm font-bold text-on-surface">Daftar Penerima Periode <span x-text="b.period"></span></h3>
                        <p class="text-[11px] text-on-surface-variant/50 mt-0.5">Klik ikon slip untuk pratinjau desain PDF</p>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[15px] text-on-surface-variant/40 pointer-events-none">search</span>
                        <input type="text" x-model="search" placeholder="Cari karyawan…"
                               class="pl-9 pr-3 py-1.5 border border-black/10 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 w-44 transition">
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[360px] overflow-y-auto">
                    <table class="w-full text-xs">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-surface-container text-[10px] font-bold text-on-surface-variant/50 uppercase tracking-wider border-b border-black/5 text-left">
                                <th class="px-5 py-3">Karyawan</th>
                                <th class="px-4 py-3">Bank & Rekening</th>
                                <th class="px-4 py-3 text-center">Slip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <template x-for="emp in b.employees.filter(e => search === '' || e.name.toLowerCase().includes(search.toLowerCase()) || e.nip.toLowerCase().includes(search.toLowerCase()))" :key="emp.payroll_id">
                                <tr class="hover:bg-primary/4 transition">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <img :src="'https://i.pravatar.cc/32?img=' + emp.avatar" class="w-8 h-8 rounded-full object-cover shrink-0 border border-black/10" alt="">
                                            <div>
                                                <p class="font-bold text-on-surface" x-text="emp.name"></p>
                                                <p class="text-[10px] font-mono-data text-on-surface-variant/50" x-text="emp.nip + ' · ' + emp.dept"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-bold text-[10px] px-1.5 py-0.5 rounded bg-primary/10 text-primary" x-text="emp.bank"></span>
                                        <p class="font-mono-data text-on-surface-variant/60 mt-0.5 text-[10px]" x-text="emp.rekening"></p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="openSlip(emp)"
                                                class="p-1.5 rounded-lg text-on-surface-variant/40 hover:text-primary hover:bg-primary/10 transition"
                                                title="Preview Slip Gaji">
                                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div x-show="search !== '' && matchingCount() === 0" x-cloak class="px-5 py-10 text-center">
                        <span class="material-symbols-outlined text-[28px] text-on-surface-variant/25 mb-2 block">search_off</span>
                        <p class="text-xs font-bold text-on-surface-variant/50">Karyawan tidak ditemukan</p>
                        <p class="text-[11px] text-on-surface-variant/35 mt-0.5">Coba kata kunci nama atau NIP lain.</p>
                    </div>
                </div>

                <div class="px-5 py-3 bg-surface-container/60 border-t border-black/5 flex items-center justify-between">
                    <p class="text-[11px] text-on-surface-variant/60">Total <span class="font-bold text-on-surface" x-text="b.total_emp"></span> karyawan</p>
                    <p class="text-[11px] font-extrabold font-mono-data text-primary" x-text="'Grand Total: ' + formatRupiah(b.grand_nett)"></p>
                </div>
            </div>

            {{-- ===== KANAN: PROGRES ===== --}}
            <div class="col-span-2 card-flat rounded-2xl p-5 space-y-4">
                <div class="pb-3 border-b border-black/5">
                    <h3 class="text-sm font-bold text-on-surface">Progres Pencairan</h3>
                    <p class="text-[11px] text-on-surface-variant/50 mt-0.5"
                       x-text="((b.disbursed ? 1 : 0) + (b.published ? 1 : 0)) + ' dari 2 langkah selesai'"></p>
                </div>

                <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all duration-500"
                         :style="'width: ' + (((b.disbursed ? 1 : 0) + (b.published ? 1 : 0)) / 2 * 100) + '%'"></div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-extrabold shrink-0 mt-0.5"
                          :class="b.disbursed ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant/50 border border-black/10'">
                        <span x-show="b.disbursed" class="material-symbols-outlined text-[14px]">check</span>
                        <span x-show="!b.disbursed">1</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold" :class="b.disbursed ? 'text-primary' : 'text-on-surface'">Dana ditransfer bank</p>
                        <p class="text-[11px] text-on-surface-variant/50 mt-0.5" x-text="b.disbursed ? 'Transfer bank telah dikonfirmasi selesai.' : 'Corporate Internet Banking belum ditandai selesai.'"></p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-extrabold shrink-0 mt-0.5"
                          :class="b.published ? 'bg-primary text-white' : (b.disbursed ? 'bg-surface-container text-on-surface-variant/50 border border-black/10' : 'bg-surface-container text-on-surface-variant/25 border border-black/5')">
                        <span x-show="b.published" class="material-symbols-outlined text-[14px]">check</span>
                        <span x-show="!b.published">2</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold" :class="b.published ? 'text-primary' : (b.disbursed ? 'text-on-surface' : 'text-on-surface-variant/40')">Slip gaji digital dipublish</p>
                        <p class="text-[11px] mt-0.5" :class="b.disbursed ? 'text-on-surface-variant/50' : 'text-on-surface-variant/30'"
                           x-text="b.published ? 'Karyawan dapat mengakses slip via dashboard/aplikasi.' : (b.disbursed ? 'Siap dipublish — pakai tombol di panel atas.' : 'Aktif setelah langkah 1 selesai.')"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </template>

    {{-- ====================== MODAL: MARK AS DISBURSED — form sungguhan ====================== --}}
    <div x-show="showDisburseModal" x-cloak
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         @click.self="showDisburseModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px] text-primary">done_all</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-on-surface">Konfirmasi Disbursement</h3>
                    <p class="text-xs text-on-surface-variant/60">Tandai gaji telah berhasil ditransfer</p>
                </div>
            </div>
            <p class="text-xs text-on-surface-variant/70 leading-relaxed">
                Pastikan seluruh transfer bank telah berhasil diproses sebelum menandai batch ini sebagai <strong class="text-primary">Disbursed</strong>. Tindakan ini akan membuka tahapan Publish Digital Payslip.
            </p>
            <form method="POST" :action="'{{ url('finance/disbursement') }}/' + batches[activeIndex].id + '/mark-disbursed'" class="flex gap-2 pt-2 border-t border-black/5">
                @csrf
                <button type="button" @click="showDisburseModal = false"
                        class="flex-1 py-2.5 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[15px]">done_all</span>
                    Ya, Sudah Cair
                </button>
            </form>
        </div>
    </div>

    {{-- ====================== MODAL: PUBLISH PAYSLIP — form sungguhan ====================== --}}
    <div x-show="showPublishModal" x-cloak
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         @click.self="showPublishModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px] text-primary">mark_email_read</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-on-surface">Publish Slip Gaji Digital</h3>
                    <p class="text-xs text-on-surface-variant/60">Buka akses slip ke dashboard karyawan</p>
                </div>
            </div>
            <p class="text-xs text-on-surface-variant/70 leading-relaxed">
                Slip gaji digital akan dapat diakses oleh seluruh karyawan pada batch ini melalui dashboard/aplikasi masing-masing.
            </p>
            <form method="POST" :action="'{{ url('finance/disbursement') }}/' + batches[activeIndex].id + '/mark-published'" class="flex gap-2 pt-2 border-t border-black/5">
                @csrf
                <button type="button" @click="showPublishModal = false"
                        class="flex-1 py-2.5 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[15px]">send</span>
                    Publish Sekarang
                </button>
            </form>
        </div>
    </div>

    @include('finance._slip-preview-modal')

    @endif
</div>
@endsection