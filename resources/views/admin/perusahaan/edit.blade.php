@extends('layouts.admin')

@section('title', 'Edit Profil Perusahaan')
@section('page-title', 'Edit Profil Perusahaan')
@section('page-desc', 'Perbarui data identitas perusahaan.')

@php
    // Dummy data — nantinya diganti hasil query data perusahaan (sistem single company)
    $company = [
        'code' => 'CMP-001',
        'name' => 'PT Talenta Meraki Indonesia',
        'address' => 'Jl. Sudirman No. 88, Jakarta Selatan',
        'pic' => 'Andi Wijaya',
        'phone' => '021-5789012',
        'email' => 'info@talentahr.co.id',
        'npwp' => '01.234.567.8-901.000',
        'industry' => 'Jasa Teknologi Informasi',
        'employees' => 1284,
        'status' => 'Aktif',
    ];
@endphp

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('admin.companies.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Profil Perusahaan
    </a>

    {{-- IDENTITAS RINGKAS --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-primary text-[26px]">domain</span>
        </div>
        <div class="flex-1">
            <p class="text-base font-bold text-on-surface">{{ $company['name'] }}</p>
            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $company['code'] }} · {{ number_format($company['employees']) }} karyawan</p>
        </div>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">
            {{ $company['status'] }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.companies.index') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- DATA PERUSAHAAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Perusahaan</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Perusahaan</label>
                    <input type="text" name="name" required value="{{ $company['name'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Kode Perusahaan</label>
                    <input type="text" value="{{ $company['code'] }}" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Alamat Lengkap</label>
                    <textarea name="address" required rows="2"
                              class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                     hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition resize-none">{{ $company['address'] }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Bidang Usaha</label>
                    <input type="text" name="industry" required value="{{ $company['industry'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NPWP</label>
                    <input type="text" name="npwp" required value="{{ $company['npwp'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">PIC Perusahaan</label>
                    <input type="text" name="pic" required value="{{ $company['pic'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="phone" required value="{{ $company['phone'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email Perusahaan</label>
                    <input type="email" name="email" required value="{{ $company['email'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
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