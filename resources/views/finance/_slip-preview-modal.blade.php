<div x-show="showSlipModal" x-cloak
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" @click.self="showSlipModal = false">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-show="slipEmployee">

        <div class="flex items-center justify-between px-5 py-4 border-b border-black/5 shrink-0">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-primary">receipt_long</span>
                <p class="text-sm font-bold text-on-surface">Preview Slip Gaji Digital</p>
            </div>
            <button type="button" @click="showSlipModal = false"
                class="text-on-surface-variant/40 hover:text-on-surface transition">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="overflow-y-auto">
            {{-- HEADER PERUSAHAAN --}}
            {{--
                FIX: nama & alamat perusahaan sebelumnya hardcoded "PT Talenta Digital
                Nusantara". Ini sama untuk semua karyawan dalam satu batch (tidak berubah
                per slipEmployee), jadi cukup di-render sekali lewat Blade dari $company
                (di-share otomatis dari review.blade.php lewat @include), bukan lewat Alpine.
            --}}
            <div class="px-6 py-5 text-white" style="background-color:#0B3D2E;">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-white/50">Slip Gaji Digital</p>
                        <p class="text-lg font-extrabold mt-0.5" x-text="slipEmployee?.period"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold">{{ $company->name ?? '-' }}</p>
                        <p class="text-[10px] text-white/50 mt-0.5">
                            {{ collect([$company->address ?? null, $company->city ?? null])->filter()->implode(', ') ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- IDENTITAS --}}
            <div class="px-6 py-4 border-b border-black/5 flex items-center gap-4 flex-wrap">
                <img :src="`https://i.pravatar.cc/44?img=${slipEmployee?.avatar}`"
                    class="w-11 h-11 rounded-full object-cover border border-black/10 shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-on-surface" x-text="slipEmployee?.name"></p>
                    <p class="text-[11px] font-mono-data text-on-surface-variant/50"
                        x-text="slipEmployee?.nip + ' · ' + slipEmployee?.dept"></p>
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-primary/10 text-primary"
                    x-text="slipEmployee?.bank + ' ' + slipEmployee?.rekening"></span>
            </div>

            {{-- PENDAPATAN & POTONGAN --}}
            <div class="px-6 py-5 grid grid-cols-2 gap-6">
                <div>
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-2.5">Pendapatan</p>
                    <div class="space-y-2">
                        <template x-for="e in (slipEmployee?.earnings || [])" :key="e.label">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant/60" x-text="e.label"></span>
                                <span class="font-mono-data text-on-surface"
                                    x-text="Number(e.amount).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-error uppercase tracking-widest mb-2.5">Potongan</p>
                    <div class="space-y-2">
                        <template x-for="d in (slipEmployee?.deductions || [])" :key="d.label">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-on-surface-variant/60" x-text="d.label"></span>
                                <span class="font-mono-data text-error"
                                    x-text="'-' + Number(d.amount).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- TOTAL --}}
            <div class="px-6 py-4 bg-surface-container flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-on-surface-variant/50 uppercase tracking-wide">Total Diterima
                        (Net)</p>
                    <p class="text-[10px] text-on-surface-variant/40 mt-0.5">Ditransfer ke rekening terdaftar</p>
                </div>
                <p class="text-xl font-extrabold font-mono-data text-primary"
                    x-text="'Rp' + Number(slipEmployee?.net || 0).toLocaleString('id-ID')">
                </p>
            </div>

            <p class="text-[10px] text-on-surface-variant/40 text-center px-6 py-3">
                Dokumen ini adalah pratinjau internal Finance sebelum/sesudah dipublish ke karyawan.
            </p>
        </div>

        <div class="px-5 py-3.5 border-t border-black/5 flex items-center justify-between shrink-0">
            <a :href="slipEmployee ? '{{ url('finance/disbursement') }}/' + slipEmployee.id + '/slip' : '#'"
               class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
                Lihat Detail Lengkap <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
            <button type="button" @click="showSlipModal = false"
                class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                Tutup
            </button>
        </div>
    </div>
</div>