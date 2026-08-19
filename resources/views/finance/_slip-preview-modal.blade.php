<div x-show="showSlipModal" x-cloak
    class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" @click.self="showSlipModal = false">
    <div class="bg-white rounded-md w-full max-w-lg shadow-sm overflow-hidden max-h-[90vh] flex flex-col border border-gray-100"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-show="slipEmployee">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0 bg-gray-50/50">
            <div class="flex items-center gap-3">
                
                <p class="text-base font-medium text-gray-800">Preview Slip Gaji Digital</p>
            </div>
            <button type="button" @click="showSlipModal = false"
                class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="overflow-y-auto">

            <div class="px-8 py-6 text-white bg-[#0B3D2E]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-medium uppercase tracking-widest text-emerald-100/70">Slip Gaji Digital</p>
                        <p class="text-xl font-semibold mt-1" x-text="slipEmployee?.period"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">{{ $company->name ?? '-' }}</p>
                        <p class="text-[11px] text-emerald-100/70 mt-1">
                            {{ collect([$company->address ?? null, $company->city ?? null])->filter()->implode(', ') ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>


            <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-4 flex-wrap bg-gray-50">
                <img :src="`https://i.pravatar.cc/44?img=${slipEmployee?.avatar}`"
                    class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800" x-text="slipEmployee?.name"></p>
                    <p class="text-[11px]  text-gray-500 mt-0.5"
                        x-text="slipEmployee?.nip + ' · ' + slipEmployee?.dept"></p>
                </div>
                <span class="text-[11px] font-medium px-3 py-1.5 rounded-md bg-white border border-gray-200 text-[#0B3D2E] shadow-sm"
                    x-text="slipEmployee?.bank + ' ' + slipEmployee?.rekening"></span>
            </div>


            <div class="px-8 py-6 grid grid-cols-2 gap-8 bg-white">
                <div>
                    <p class="text-[11px] font-medium text-[#0B3D2E] uppercase tracking-widest mb-4">Pendapatan</p>
                    <div class="space-y-3">
                        <template x-for="e in (slipEmployee?.earnings || [])" :key="e.label">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500" x-text="e.label"></span>
                                <span class=" text-gray-800 font-medium"
                                    x-text="Number(e.amount).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-gray-700 uppercase tracking-widest mb-4">Potongan</p>
                    <div class="space-y-3">
                        <template x-for="d in (slipEmployee?.deductions || [])" :key="d.label">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500" x-text="d.label"></span>
                                <span class=" text-gray-700 font-medium"
                                    x-text="'-' + Number(d.amount).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>


            <div class="px-8 py-5 bg-gray-50/50 flex items-center justify-between border-t border-gray-200/50">
                <div>
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">Total Diterima
                        (Net)</p>
                    <p class="text-[11px] text-gray-400 mt-1">Ditransfer ke rekening terdaftar</p>
                </div>
                <p class="text-2xl font-semibold  text-[#0B3D2E]"
                    x-text="'Rp' + Number(slipEmployee?.net || 0).toLocaleString('id-ID')">
                </p>
            </div>

            <p class="text-[11px] text-gray-400 text-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                Dokumen ini adalah pratinjau internal Finance sebelum/sesudah dipublish ke karyawan.
            </p>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between shrink-0 bg-white">
            <a :href="slipEmployee ? '{{ url('finance/disbursement') }}/' + slipEmployee.payroll_id + '/slip' : '#'"
               class="inline-flex items-center gap-1.5 text-xs font-medium text-[#0B3D2E] hover:bg-gray-50 px-3 py-1.5 rounded-md transition">
                Lihat Detail Lengkap <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
            <button type="button" @click="showSlipModal = false"
                class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
