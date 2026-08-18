@extends('layouts.admin')

@section('title', 'Pengguna & Hak Akses')
@section('page-title', 'Pengguna & Hak Akses')
@section('page-desc', 'Kelola akun pengguna dan role/permission di sistem.')

@section('content')
<div class="space-y-8">


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($roles as $role)
            <div class="bg-white rounded-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 font-medium text-sm">{{ $role['name'] }}</h3>
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                        <span class="material-symbols-outlined text-[18px]">{{ $role['icon'] }}</span>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-medium  text-gray-800">{{ $role['users'] }}</span>
                    <span class="text-gray-500 text-xs mb-1">Pengguna Aktif</span>
                </div>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md p-6 border border-gray-200">

        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <h3 class="text-base font-medium text-gray-800">Daftar Pengguna</h3>
                <p class="text-xs text-gray-500 mt-1">{{ count($users) }} pengguna terdaftar</p>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-3">

                <div class="relative">
                    <select name="role" onchange="this.form.submit()"
                        class="appearance-none text-xs font-medium border border-gray-200 rounded-lg pl-3 pr-8 py-2 bg-gray-50 text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 transition cursor-pointer">
                        <option value="Semua Role" {{ request('role') == 'Semua Role' ? 'selected' : '' }}>Semua Role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r['name'] }}" {{ request('role') == $r['name'] ? 'selected' : '' }}>
                                {{ $r['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>


                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                        class="w-64 pl-9 pr-3 py-2 bg-gray-50 rounded-lg text-xs font-medium border border-gray-200 text-gray-700 placeholder-gray-400 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 transition"
                        onkeydown="if(event.keyCode==13){this.form.submit();}">
                </div>

                <a href="{{ route('admin.users.create') }}"
                    class="bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium px-4 py-2 rounded-lg flex items-center gap-2 whitespace-nowrap transition">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Pengguna
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-[11px] text-gray-500 bg-gray-50 border-y border-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-medium">Pengguna</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($users as $user)
                        @php
                            $roleDbName = $user->roles->first() ? $user->roles->first()->name : 'employee';
                            $roleDisplayName = $displayRoleMap[$roleDbName] ?? 'Pegawai';

                            $status = $user->status ?? 'Aktif';
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 text-sm">{{ $user->name }}</p>
                                <p class="text-[11px] text-gray-500">{{ $user->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">
                                    {{ $roleDisplayName }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'Aktif' ? 'bg-gray-500' : 'bg-gray-50' }}"></span> 
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" title="Edit"
                                        class="text-gray-400 hover:text-gray-700 transition">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form action="{{ route('admin.users.destroyWeb', $user->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus akun pengguna ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Pengguna"
                                            class="text-gray-400 hover:text-gray-700 transition">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
