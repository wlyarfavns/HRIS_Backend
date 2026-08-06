@extends('layouts.admin')

@section('title', 'Profil Perusahaan')
@section('page-title', 'Profil Perusahaan')
@section('page-desc', 'Kelola data identitas perusahaan yang terdaftar di sistem.')

@php
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
        'departments' => 4,
        'status' => 'Aktif',
    ];
@endphp

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">domain</span>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $company['status'] }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Status Perusahaan</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">groups</span>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ number_format($company['employees']) }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Total Karyawan</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">account_tree</span>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $company['departments'] }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Departemen</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary text-[20px]">badge</span>
            </div>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $company['code'] }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">Kode Perusahaan</p>
        </div>
    </div>

    {{-- PROFIL CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Data Perusahaan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Identitas resmi perusahaan yang terdaftar di sistem</p>
            </div>

            <a href="{{ route('admin.companies.edit') }}"
               class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg
                      flex items-center gap-1.5 whitespace-nowrap transition">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Profil Perusahaan
            </a>
        </div>

        <div class="p-6 grid grid-cols-3 gap-x-6 gap-y-5">
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Nama Perusahaan</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $company['name'] }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Bidang Usaha</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $company['industry'] }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">NPWP</p>
                <p class="text-sm font-semibold text-on-surface mt-1 font-mono-data">{{ $company['npwp'] }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Alamat Lengkap</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $company['address'] }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">PIC Perusahaan</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $company['pic'] }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">No. Telepon</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $company['phone'] }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Email Perusahaan</p>
                <p class="text-sm font-semibold text-on-surface mt-1">{{ $company['email'] }}</p>
            </div>
        </div>
    </div>

@endsection