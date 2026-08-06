@extends('layouts.admin')

@section('title', 'Edit Cabang')
@section('page-title', 'Edit Cabang')
@section('page-desc', 'Perbarui data perusahaan atau cabang.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $branch = [
        'id' => 'BR-003',
        'name' => 'Cabang Bandung',
        'address' => 'Jl. Asia Afrika No. 45, Bandung',
        'pic' => 'Fajar Nugroho',
        'phone' => '022-4267890',
        'employees' => 288,
        'status' => 'Aktif',
    ];
@endphp

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('admin.companies.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Cabang
    </a>

    {{-- IDENTITAS RINGKAS --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-primary text-[26px]">domain</span>
        </div>
        <div class="flex-1">
            <p class="text-base font-bold text-on-surface">{{ $branch['name'] }}</p>
            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $branch['id'] }} · {{ number_format($branch['employees']) }} karyawan</p>
        </div>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $branch['status'] === 'Aktif' ? 'bg-primary/10 text-primary' : 'bg-amber-500/10 text-amber-700' }}">
            {{ $branch['status'] }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.companies.index') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- DATA CABANG --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Cabang</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Cabang</label>
                    <input type="text" name="name" required value="{{ $branch['name'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Kode Cabang</label>
                    <input type="text" value="{{ $branch['id'] }}" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Alamat Lengkap</label>
                    <textarea name="address" required rows="2"
                              class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                     hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition resize-none">{{ $branch['address'] }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">PIC Cabang</label>
                    <input type="text" name="pic" required value="{{ $branch['pic'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon Cabang</label>
                    <input type="text" name="phone" required value="{{ $branch['phone'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Status</label>
                    <div class="relative mt-1.5">
                        <select name="status" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Aktif', 'Persiapan', 'Nonaktif'] as $st)
                                <option {{ $branch['status'] === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="col-span-3 flex items-end">
                    <p class="text-xs text-on-surface-variant/40">Mengubah status ke <span class="font-bold text-error">Nonaktif</span> akan menyembunyikan cabang ini dari pilihan penempatan karyawan baru.</p>
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
                Simpan Perubahan
            </button>
        </div>
    </form>

@endsection