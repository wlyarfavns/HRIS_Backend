@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')
@section('page-desc', 'Perbarui data akun, role, dan status pengguna.')

@section('content')


    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-[#0B3D2E] transition -mt-2 mb-6">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Pengguna
    </a>


    <div class="bg-white rounded-md p-6 flex items-center gap-4 mt-2 border border-gray-200">
        <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center text-xl font-medium text-[#0B3D2E] border border-gray-200">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="flex-1">
            <p class="text-lg font-medium text-gray-800">{{ $user->name }}</p>
            <p class="text-xs text-gray-500 mt-0.5">ID: {{ $user->id }} · {{ $user->email }}</p>
        </div>
        <span
            class="text-[11px] font-medium px-3 py-1.5 rounded-lg {{ $user->display_role === 'Super Admin' ? 'bg-gray-50 text-gray-700' : 'bg-gray-50 text-[#0B3D2E]' }}">
            {{ $user->display_role }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.users.updateWeb', $user->id) }}" class="space-y-6 mt-6"
        onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan data pengguna ini?');">
        @csrf
        @method('PUT')


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">1</span>
                <h2 class="text-lg font-medium text-gray-800">Data Akun</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="{{ $user->name }}"
                        class="mt-2 w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required value="{{ $user->email }}"
                        class="mt-2 w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tak diubah"
                        class="mt-2 w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
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
                            @foreach (['Super Admin', 'HR Admin', 'Finance', 'Supervisor'] as $r)
                                <option {{ $user->display_role === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
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
                class="bg-[#0B3D2E] text-white px-8 py-2.5 rounded-md text-sm font-medium hover:bg-[#043927] transition flex items-center gap-2 shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection
