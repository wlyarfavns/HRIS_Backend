@extends('layouts.admin')

@section('title', 'Tambah Cabang')
@section('page-title', 'Tambah Cabang')
@section('page-desc', 'Daftarkan perusahaan atau cabang baru ke dalam sistem.')

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('admin.companies.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Cabang
    </a>

    <form method="POST" action="{{ route('admin.companies.index') }}" class="space-y-6">
        @csrf

        {{-- DATA CABANG --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Cabang</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Cabang</label>
                    <input type="text" name="name" required placeholder="Contoh: Cabang Yogyakarta"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Kode Cabang</label>
                    <input type="text" value="Otomatis oleh sistem" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Alamat Lengkap</label>
                    <textarea name="address" required rows="2" placeholder="Jalan, nomor, kota, provinsi"
                              class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                     hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition resize-none"></textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">PIC Cabang</label>
                    <input type="text" name="pic" required placeholder="Nama penanggung jawab"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon Cabang</label>
                    <input type="text" name="phone" required
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Status Awal</label>
                    <div class="relative mt-1.5">
                        <select name="status" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option>Persiapan</option>
                            <option>Aktif</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.companies.index') }}"
               class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant/70
                      hover:bg-primary/5 hover:text-primary transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Cabang
            </button>
        </div>
    </form>

@endsection