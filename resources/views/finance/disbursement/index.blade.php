
@extends('layouts.finance')

@section('title', 'Disbursement & Slip Gaji')
@section('page-title', 'Disbursement & Slip Gaji')
@section('page-desc', 'Tandai pencairan dana selesai dan distribusikan slip gaji digital ke karyawan.')

@section('content')
<div x-data="{
    activeIndex: 0,
    periodDropdownOpen: false,
    batches: {{ Js::from($batches->items()) }},
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
        <div class="bg-white rounded-md border border-gray-100 shadow-sm p-12 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                </div>
            <h3 class="text-sm font-medium text-gray-800">Belum ada batch yang siap didisbursement.</h3>
            <p class="text-xs text-gray-500 mt-1">Batch akan muncul di sini setelah file export bank digenerate di menu Export Bank Transfer.</p>
        </div>
    @else


    <div class="mb-8 relative max-w-md" @click.outside="periodDropdownOpen = false">
        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-widest mb-2">Pilih Periode Payroll</p>

        <button type="button" @click="periodDropdownOpen = !periodDropdownOpen"
                class="w-full flex items-center justify-between gap-3 bg-white border border-gray-200 rounded-md px-5 py-4 hover:border-[#0B3D2E]/30 hover:bg-gray-50/50 transition shadow-sm">
            <div class="flex items-center gap-4 min-w-0">
                <span class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm border"
                      :class="statusOf(activeIndex) === 'pending' ? 'bg-gray-50 text-gray-700 border-gray-200' : 'bg-gray-50 text-[#0B3D2E] border-gray-200'">
                    <span class="material-symbols-outlined text-[20px]" x-text="statusIcon(activeIndex)"></span>
                </span>
                <div class="text-left min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="batches[activeIndex].period"></p>
                    <p class="text-[11px] text-gray-500 truncate mt-0.5" x-text="statusLabel(activeIndex)"></p>
                </div>
            </div>
            </button>

        <div x-show="periodDropdownOpen" x-cloak
             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
             class="absolute mt-2 w-full bg-white rounded-md shadow-sm border border-gray-100 z-30 max-h-80 overflow-y-auto p-2">
            <template x-for="(b, i) in batches" :key="b.id">
                <button type="button" @click="activeIndex = i; periodDropdownOpen = false; search = ''"
                        class="w-full flex items-center justify-between gap-3 p-4 rounded-md text-left transition hover:bg-gray-50"
                        :class="activeIndex === i ? 'bg-gray-50' : ''">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                              :class="statusOf(i) === 'pending' ? 'bg-gray-50 text-gray-700' : 'bg-gray-50 text-[#0B3D2E]'">
                            <span class="material-symbols-outlined text-[18px]" x-text="statusIcon(i)"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" x-text="b.period"></p>
                            <p class="text-[11px] text-gray-500 truncate mt-0.5" x-text="'Diexport ' + b.exported_at"></p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] font-medium px-2 py-0.5 rounded-md inline-block border"
                           :class="statusOf(i) === 'pending' ? 'bg-gray-50 text-gray-700 border-gray-200' : 'bg-gray-50 text-[#0B3D2E] border-gray-200'"
                           x-text="statusOf(i) === 'done' ? 'Selesai' : (statusOf(i) === 'partial' ? 'Dicairkan' : 'Pending')"></p>
                        <p class="text-[11px]  text-gray-500 mt-1.5 font-medium" x-text="formatRupiah(b.grand_nett)"></p>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <template x-for="(b, i) in batches" :key="b.id">
    <div x-show="activeIndex === i" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">


        <div class="rounded-md p-6 mb-8 flex items-center justify-between gap-6 flex-wrap border shadow-sm transition-colors"
             :class="statusOf(i) === 'pending' ? 'bg-gray-50 border-gray-200' : 'bg-gray-50 border-gray-200'">
            <div class="flex items-center gap-4 min-w-0">
                <span class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 text-white"
                      :class="statusOf(i) === 'pending' ? 'bg-gray-50' : (statusOf(i) === 'partial' ? 'bg-emerald-600' : 'bg-[#0B3D2E]')">
                    </span>
                <div class="min-w-0">
                    <p class="text-base font-semibold" :class="statusOf(i) === 'pending' ? 'text-gray-700' : 'text-emerald-900'"
                       x-text="statusOf(i) === 'done' ? 'Batch ini sudah selesai diproses' : (statusOf(i) === 'partial' ? 'Tinggal 1 langkah lagi: publish slip gaji' : 'Konfirmasi Pencairan Dana Gaji')"></p>
                    <p class="text-xs mt-1" :class="statusOf(i) === 'pending' ? 'text-gray-700' : 'text-[#0B3D2E]'"
                       x-text="statusOf(i) === 'done' ? 'Karyawan sudah bisa melihat slip gajinya masing-masing.' : (statusOf(i) === 'partial' ? 'Dana sudah ditransfer. Publish slip supaya karyawan bisa mengaksesnya.' : 'Pastikan seluruh transfer bank ke karyawan sudah sukses, baru tandai batch ini.')"></p>
                </div>
            </div>
            <button type="button" x-show="!b.disbursed" @click="showDisburseModal = true"
                    class="shrink-0 inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white text-sm font-medium px-5 py-2.5 rounded-md hover:bg-[#043927] shadow-sm transition">
                <span class="material-symbols-outlined text-[18px]">done_all</span>
                Mark as Disbursed
            </button>
            <button type="button" x-show="b.disbursed && !b.published" @click="showPublishModal = true"
                    class="shrink-0 inline-flex items-center gap-1.5 bg-[#0B3D2E] text-white text-sm font-medium px-5 py-2.5 rounded-md hover:bg-[#043927] shadow-sm transition">
                <span class="material-symbols-outlined text-[18px]">mark_email_read</span>
                Publish Sekarang
            </button>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">


            <div class="xl:col-span-3 bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Daftar Penerima Periode <span x-text="b.period"></span></h3>
                        <p class="text-[11px] text-gray-500 mt-1">Klik ikon slip untuk pratinjau desain PDF</p>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Cari karyawan…"
                               class="w-56 pl-10 pr-4 py-2 border border-gray-200 bg-white rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition shadow-sm">
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 z-10 bg-gray-50 border-b border-gray-100 shadow-sm">
                            <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                                <th class="px-8 py-4">Karyawan</th>
                                <th class="px-6 py-4">Bank & Rekening</th>
                                <th class="px-8 py-4 text-center">Slip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-gray-700">
                            <template x-for="emp in b.employees.filter(e => search === '' || e.name.toLowerCase().includes(search.toLowerCase()) || e.nip.toLowerCase().includes(search.toLowerCase()))" :key="emp.payroll_id">
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <img :src="'https://i.pravatar.cc/36?img=' + emp.avatar" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="">
                                            <div>
                                                <p class="font-medium text-gray-800 group-hover:text-[#0B3D2E] transition-colors leading-tight" x-text="emp.name"></p>
                                                <p class="text-[11px]  text-gray-500 mt-0.5" x-text="emp.nip + ' · ' + emp.dept"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-[11px] px-2 py-0.5 rounded bg-gray-50 border border-gray-200 text-[#0B3D2E]" x-text="emp.bank"></span>
                                        <p class=" text-gray-600 mt-1 text-xs" x-text="emp.rekening"></p>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        <button type="button" @click="openSlip(emp)"
                                                class="p-2 rounded-md border border-gray-200 bg-white text-gray-500 hover:text-[#0B3D2E] hover:bg-gray-50 hover:border-gray-200 transition shadow-sm"
                                                title="Preview Slip Gaji">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div x-show="search !== '' && matchingCount() === 0" x-cloak class="px-8 py-16 text-center bg-white">
                        <p class="text-sm font-medium text-gray-500">
                            <template x-if="search">
                                <span>Tidak ada karyawan yang sesuai dengan pencarian "<span x-text="search" class="font-bold text-gray-700"></span>".</span>
                            </template>
                        </p>
                    </div>
                </div>

                <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">Total <span class="font-medium text-gray-800" x-text="b.total_emp"></span> karyawan</p>
                    <p class="text-sm font-semibold  text-[#0B3D2E]" x-text="'Grand Total: ' + formatRupiah(b.grand_nett)"></p>
                </div>
            </div>


            <div class="xl:col-span-2 bg-white rounded-md border border-gray-100 shadow-sm p-8 space-y-6">
                <div class="pb-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800">Progres Pencairan</h3>
                    <p class="text-[11px] text-gray-500 mt-1"
                       x-text="((b.disbursed ? 1 : 0) + (b.published ? 1 : 0)) + ' dari 2 langkah selesai'"></p>
                </div>

                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden shadow-sm">
                    <div class="h-full bg-[#0B3D2E] rounded-full transition-all duration-500"
                         :style="'width: ' + (((b.disbursed ? 1 : 0) + (b.published ? 1 : 0)) / 2 * 100) + '%'"></div>
                </div>

                <div class="flex items-start gap-4">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-semibold shrink-0 mt-0.5 transition-colors"
                          :class="b.disbursed ? 'bg-[#0B3D2E] text-white shadow-sm' : 'bg-gray-50 text-gray-400 border border-gray-200'">
                        <span x-show="b.disbursed" class="material-symbols-outlined text-[16px]">check</span>
                        <span x-show="!b.disbursed">1</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium" :class="b.disbursed ? 'text-[#0B3D2E]' : 'text-gray-800'">Dana ditransfer bank</p>
                        <p class="text-[11px] text-gray-500 mt-1" x-text="b.disbursed ? 'Transfer bank telah dikonfirmasi selesai.' : 'Corporate Internet Banking belum ditandai selesai.'"></p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-semibold shrink-0 mt-0.5 transition-colors"
                          :class="b.published ? 'bg-[#0B3D2E] text-white shadow-sm' : (b.disbursed ? 'bg-gray-50 text-gray-500 border border-gray-200' : 'bg-gray-50 text-gray-300 border border-gray-100')">
                        <span x-show="b.published" class="material-symbols-outlined text-[16px]">check</span>
                        <span x-show="!b.published">2</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium" :class="b.published ? 'text-[#0B3D2E]' : (b.disbursed ? 'text-gray-800' : 'text-gray-400')">Slip gaji digital dipublish</p>
                        <p class="text-[11px] mt-1" :class="b.disbursed ? 'text-gray-500' : 'text-gray-400'"
                           x-text="b.published ? 'Karyawan dapat mengakses slip via dashboard/aplikasi.' : (b.disbursed ? 'Siap dipublish — pakai tombol di panel atas.' : 'Aktif setelah langkah 1 selesai.')"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </template>


    <div x-show="showDisburseModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         @click.self="showDisburseModal = false">
        <div class="bg-white rounded-md max-w-sm w-full p-8 shadow-sm space-y-6"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                    </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-800">Konfirmasi Disbursement</h3>
                    <p class="text-xs text-gray-500 mt-1">Tandai gaji telah berhasil ditransfer</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-md border border-gray-100 text-center">
                Pastikan seluruh transfer bank telah berhasil diproses sebelum menandai batch ini sebagai <strong class="text-[#0B3D2E]">Disbursed</strong>. Tindakan ini akan membuka tahapan Publish Digital Payslip.
            </p>
            <form method="POST" :action="'{{ url('finance/disbursement') }}/' + batches[activeIndex].id + '/mark-disbursed'" class="flex gap-3 pt-4 border-t border-gray-100">
                @csrf
                <button type="button" @click="showDisburseModal = false"
                        class="flex-1 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                    Ya, Sudah Cair
                </button>
            </form>
        </div>
    </div>


    <div x-show="showPublishModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         @click.self="showPublishModal = false">
        <div class="bg-white rounded-md max-w-sm w-full p-8 shadow-sm space-y-6"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                    </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-800">Publish Slip Gaji</h3>
                    <p class="text-xs text-gray-500 mt-1">Buka akses slip ke dashboard karyawan</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-md border border-gray-100 text-center">
                Slip gaji digital akan dapat diakses oleh seluruh karyawan pada batch ini melalui dashboard/aplikasi masing-masing.
            </p>
            <form method="POST" :action="'{{ url('finance/disbursement') }}/' + batches[activeIndex].id + '/mark-published'" class="flex gap-3 pt-4 border-t border-gray-100">
                @csrf
                <button type="button" @click="showPublishModal = false"
                        class="flex-1 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Publish
                </button>
            </form>
        </div>
    </div>

    @include('finance._slip-preview-modal')

    <div class="mt-8">
        {{ $batches->links() }}
    </div>

    @endif
</div>
@endsection
