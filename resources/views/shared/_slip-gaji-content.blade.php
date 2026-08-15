{{--
Partial: Slip Gaji Digital
Dipakai oleh hr/penggajian/slip.blade.php & finance/disbursement/slip.blade.php
Variabel yang dibutuhkan: $slip (array), $backRoute (nama route utk tombol kembali)
--}}

<a href="{{ route($backRoute) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
          hover:text-primary transition -mt-2 mb-1">
    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
    Kembali
</a>

<div class="grid grid-cols-3 gap-6 items-start">

    {{-- SLIP GAJI --}}
    <div class="col-span-2 card-flat rounded-2xl overflow-hidden">

        {{-- HEADER SLIP --}}
        <div class="px-8 py-6 text-white" style="background-color:#0B3D2E;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-white/60">Slip Gaji Digital</p>
                    <p class="text-xl font-extrabold mt-1">{{ $slip['period'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold">{{ $slip['company_name'] }}</p>
                    <p class="text-[11px] text-white/60 mt-0.5">{{ $slip['company_address'] }}</p>
                </div>
            </div>
        </div>

        {{-- IDENTITAS KARYAWAN --}}
        <div class="px-8 py-6 border-b border-black/5 grid grid-cols-3 gap-5">
            <div class="col-span-1">
                <p class="text-sm font-bold text-on-surface">{{ $slip['name'] }}</p>
                <p class="text-xs text-on-surface-variant/50 font-mono-data">{{ $slip['nip'] }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">Jabatan</p>
                <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $slip['position'] }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-on-surface-variant/40 uppercase tracking-wide">Departemen</p>
                <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $slip['department'] }}</p>
            </div>
        </div>

        {{-- KOMPONEN PENDAPATAN & POTONGAN --}}
        <div class="px-8 py-6 grid grid-cols-2 gap-8">
            <div>
                <p class="text-[11px] font-bold text-primary uppercase tracking-widest mb-3">Pendapatan</p>
                <div class="space-y-2.5">
                    @foreach ($slip['earnings'] as $e)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-on-surface-variant/70">{{ $e['label'] }}</span>
                            <span
                                class="font-mono-data text-on-surface">{{ number_format($e['amount'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between text-sm font-bold mt-3 pt-3 border-t border-black/5">
                    <span class="text-on-surface">Total Pendapatan</span>
                    <span
                        class="font-mono-data text-primary">{{ number_format(collect($slip['earnings'])->sum('amount'), 0, ',', '.') }}</span>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-bold text-error uppercase tracking-widest mb-3">Potongan</p>
                <div class="space-y-2.5">
                    @forelse ($slip['deductions'] as $d)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-on-surface-variant/70">{{ $d['label'] }}</span>
                            <span class="font-mono-data text-error">-{{ number_format($d['amount'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant/40">Tidak ada potongan.</p>
                    @endforelse
                </div>
                <div class="flex items-center justify-between text-sm font-bold mt-3 pt-3 border-t border-black/5">
                    <span class="text-on-surface">Total Potongan</span>
                    <span
                        class="font-mono-data text-error">-{{ number_format(collect($slip['deductions'])->sum('amount'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- GAJI BERSIH --}}
        <div class="px-8 py-6 bg-surface-container flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Gaji Bersih Diterima</p>
                <p class="text-[11px] text-on-surface-variant/40 mt-0.5">Ditransfer ke rekening terdaftar karyawan</p>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-primary">
                Rp{{ number_format(collect($slip['earnings'])->sum('amount') - collect($slip['deductions'])->sum('amount'), 0, ',', '.') }}
            </p>
        </div>

        <div class="px-8 py-5 flex items-center justify-end gap-3 border-t border-black/5">
            <button type="button"
                class="border border-black/10 text-on-surface-variant/70 text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 hover:bg-surface-container transition">
                <span class="material-symbols-outlined text-[16px]">print</span>
                Cetak
            </button>
            <button type="button"
                class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">download</span>
                Unduh PDF
            </button>
        </div>
    </div>

    {{-- SIDEBAR: STATUS --}}
    <div class="space-y-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">Status Distribusi
            </p>
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </span>
                <div>
                    <p class="text-sm font-bold text-on-surface">{{ $slip['status'] }}</p>
                    <p class="text-[11px] text-on-surface-variant/40">{{ $slip['status_time'] }}</p>
                </div>
            </div>
        </div>

        <div class="card-flat rounded-2xl p-5">
            <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Catatan</p>
            <p class="text-xs text-on-surface-variant/60 leading-relaxed">
                Slip ini dihasilkan otomatis oleh Payroll Engine dan hanya bisa diakses oleh karyawan bersangkutan
                melalui aplikasi mobile menggunakan autentikasi akun masing-masing.
            </p>
        </div>
    </div>
</div>