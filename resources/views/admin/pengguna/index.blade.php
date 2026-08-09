@extends('layouts.admin')

@section('title', 'Pengguna & Hak Akses')
@section('page-title', 'Pengguna & Hak Akses')
@section('page-desc', 'Kelola akun pengguna dan role/permission di sistem.')

@section('content')

    {{-- ROLE SUMMARY --}}
    {{-- Ubah grid-cols-4 menjadi grid-cols-3 karena Super Admin dihapus --}}
    <div class="grid grid-cols-3 gap-5">
        @foreach ($roles as $role)
            <div class="card-flat rounded-2xl p-5">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-[20px]">{{ $role['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $role['users'] }}</p>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">{{ $role['name'] }}</p>
                <p class="text-[11px] text-on-surface-variant/40 mt-1">pengguna aktif</p>
            </div>
        @endforeach
    </div>

    {{-- TABLE CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Pengguna</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($users) }} pengguna terdaftar</p>
            </div>

            {{-- BUNGKUS DENGAN FORM AGAR BISA MELAKUKAN REQUEST GET --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2.5">

                {{-- Dropdown Role --}}
                <div class="relative">
                    <select name="role" onchange="this.form.submit()"
                        class="appearance-none text-xs font-bold border border-black/10 rounded-lg pl-3 pr-8 py-2.5 bg-white hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition cursor-pointer">
                        <option value="Semua Role" {{ request('role') == 'Semua Role' ? 'selected' : '' }}>Semua Role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r['name'] }}" {{ request('role') == $r['name'] ? 'selected' : '' }}>
                                {{ $r['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- Input Pencarian --}}
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>

                    {{-- Tambahkan name="search", value dari request(), dan event onblur untuk otomatis submit saat
                    enter/pindah fokus --}}
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                        class="w-56 pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition"
                        onkeydown="if(event.keyCode==13){this.form.submit();}">
                </div>

                <div class="w-px h-6 bg-black/10 mx-0.5"></div>

                <a href="{{ route('admin.users.create') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 whitespace-nowrap transition shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Tambah Pengguna
                </a>
            </form>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr
                    class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">Pengguna</th>
                    <th class="px-6 py-3.5">Role</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($users as $user)
                    @php
                        // Deteksi nama role
                        $roleDbName = $user->roles->first() ? $user->roles->first()->name : 'employee';
                        $roleDisplayName = $displayRoleMap[$roleDbName] ?? 'Pegawai';

                        // Menentukan warna badge role
                        $roleColorClass = $roleDbName === 'company'
                            ? 'bg-on-surface-variant/10 text-on-surface-variant'
                            : 'bg-primary/10 text-primary';

                        // Status (Asumsi default Aktif jika belum ada kolom status di database)
                        $status = $user->status ?? 'Aktif';
                        $statusColor = $status === 'Aktif' ? 'text-primary' : 'text-amber-700';
                    @endphp

                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                    class="w-8 h-8 rounded-full object-cover" alt="{{ $user->name }}">
                                <div>
                                    <p class="font-bold text-on-surface">{{ $user->name }}</p>
                                    <p class="text-xs text-on-surface-variant/50">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $roleColorClass }}">
                                {{ $roleDisplayName }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-xs font-semibold {{ $statusColor }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.users.edit', $user->id) }}" title="Edit"
                                    class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <form action="{{ route('admin.users.destroyWeb', $user->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus akun pengguna ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Pengguna"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                        <span class="material-symbols-outlined text-[18px]">person_off</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection