@extends('layouts.admin')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')
@section('page-desc', 'Buat akun pengguna baru beserta role dan hak aksesnya.')

@section('content')


    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-[#0B3D2E] transition -mt-2 mb-6">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Pengguna
    </a>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6"
        onsubmit="return confirm('Apakah Anda yakin ingin menambahkan pengguna baru ini?');">
        @csrf


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">1</span>
                <h2 class="text-lg font-medium text-gray-800">Data Akun</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required placeholder="Nama sesuai identitas"
                        class="mt-2 w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required placeholder="nama@talentahr.co.id"
                        class="mt-2 w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Password Sementara</label>
                    <input type="password" name="password" required placeholder="Min. 8 karakter"
                        class="mt-2 w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
                <div class="col-span-1 md:col-span-3">
                    <p class="text-xs text-gray-500 flex items-center gap-1.5 mt-2">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        Pengguna akan diminta mengganti password saat login pertama kali.
                    </p>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">2</span>
                <h2 class="text-lg font-medium text-gray-800">Role &amp; Akses</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Pilih Role</label>
                    <div class="relative mt-2">
                        <select name="role" required
                            class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="">-- Pilih Akses --</option>
                            <option>HR Admin</option>
                            <option>Finance</option>
                            <option>Supervisor</option>
                        </select>
                        </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="{{ route('admin.users.index') }}"
                class="px-6 py-2.5 rounded-md text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition">
                Batal
            </a>
            <button type="submit"
                class="bg-[#0B3D2E] text-white px-8 py-2.5 rounded-md text-sm font-medium hover:bg-[#043927] transition flex items-center gap-2 cursor-pointer shadow-sm">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Pengguna
            </button>
        </div>
    </form>
@endsection
