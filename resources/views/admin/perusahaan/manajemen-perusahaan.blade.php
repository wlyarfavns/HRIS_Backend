@extends('layouts.admin')

@section('title', 'Profil Perusahaan')
@section('page-title', 'Profil Perusahaan')
@section('page-desc', 'Kelola data identitas perusahaan yang terdaftar di sistem.')

@section('content')


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-md p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 font-medium text-sm">Status Perusahaan</h3>
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">domain</span>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-medium text-gray-800 capitalize">{{ $companyData->status }}</span>
                <span class="text-emerald-500 text-xs font-medium mb-1 flex items-center gap-1">
                    Aktif
                </span>
            </div>
        </div>


        <div class="bg-white rounded-md p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 font-medium text-sm">Total Karyawan</h3>
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">groups</span>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-medium  text-gray-800">{{ number_format($companyData->employees) }}</span>
                <span class="text-gray-400 text-xs font-medium mb-1 flex items-center gap-1">
                    Terdaftar
                </span>
            </div>
        </div>


        <div class="bg-white rounded-md p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 font-medium text-sm">Lokasi Utama</h3>
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">location_city</span>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-medium text-gray-800 truncate">{{ $companyData->city }}</span>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-md p-8 mt-8 border border-gray-200">

        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100 flex-wrap gap-4">
            <div>
                <h3 class="text-base font-medium text-gray-800">Data Perusahaan</h3>
                <p class="text-xs text-gray-500 mt-1">Identitas resmi perusahaan yang terdaftar di sistem</p>
            </div>

            <a href="{{ route('admin.company.edit', $companyData->id) }}"
               class="bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium px-5 py-2.5 rounded-lg flex items-center gap-1.5 whitespace-nowrap transition shadow-sm">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit Profil
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Nama Perusahaan</p>
                <div class="mt-2 p-4 bg-gray-50 rounded-md border border-gray-100">
                    <p class="text-sm font-medium text-gray-800">{{ $companyData->name }}</p>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Email Perusahaan</p>
                <div class="mt-2 p-4 bg-gray-50 rounded-md border border-gray-100">
                    <p class="text-sm font-medium text-gray-800">{{ $companyData->email }}</p>
                </div>
            </div>

            <div>
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Alamat Lengkap & Provinsi</p>
                <div class="mt-2 p-4 bg-gray-50 rounded-md border border-gray-100 min-h-[60px]">
                    <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ $companyData->address }}</p>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Kode Pos</p>
                <div class="mt-2 p-4 bg-gray-50 rounded-md border border-gray-100">
                    <p class="text-sm font-medium text-gray-800 ">{{ $companyData->postal_code }}</p>
                </div>
            </div>
        </div>
    </div>

@endsection
