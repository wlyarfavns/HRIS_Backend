@extends('layouts.admin')

@section('title', 'Profil Perusahaan')
@section('page-title', 'Profil Perusahaan')
@section('page-desc', 'Kelola data identitas perusahaan yang terdaftar di sistem.')

@section('content')

    {{-- STAT ROW --}}
    {{-- Ubah menjadi grid-cols-3 agar 3 kartu tersisa membagi ruang dengan rata --}}
    <div class="grid grid-cols-3 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">domain</span>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $companyData->status }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Status Perusahaan</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">groups</span>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ number_format($companyData->employees) }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Total Karyawan</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">location_city</span>
            </div>
            <p class="text-xl font-extrabold text-on-surface leading-tight truncate">{{ $companyData->city }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Lokasi Utama</p>
        </div>
    </div>

    {{-- PROFIL CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Data Perusahaan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Identitas resmi perusahaan yang terdaftar di sistem</p>
            </div>

            <a href="{{ route('admin.company.edit', $companyData->id) }}"
               class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 whitespace-nowrap transition">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Profil
            </a>
        </div>

        {{-- Tata letak form diseimbangkan ke dalam grid 2 kolom yang rapi --}}
        <div class="p-6 grid grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Nama Perusahaan</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $companyData->name }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Email Perusahaan</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $companyData->email }}</p>
            </div>
            
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Alamat Lengkap & Provinsi</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $companyData->address }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Kode Pos</p>
                <p class="text-sm font-semibold text-on-surface mt-1 font-mono-data">{{ $companyData->postal_code }}</p>
            </div>
        </div>
    </div>

@endsection