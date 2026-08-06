@extends('layouts.admin')

@section('title', 'Pengguna & Hak Akses')
@section('page-title', 'Pengguna & Hak Akses')
@section('page-desc', 'Kelola akun pengguna dan role/permission di sistem.')

@php
    $roles = [
        ['name' => 'Super Admin', 'users' => 2, 'icon' => 'shield_person'],
        ['name' => 'HR Admin', 'users' => 6, 'icon' => 'badge'],
        ['name' => 'Finance', 'users' => 5, 'icon' => 'payments'],
        ['name' => 'Supervisor', 'users' => 11, 'icon' => 'supervisor_account'],
    ];

    $users = [
        ['nip' => 'USR-0001', 'name' => 'Andi Wijaya', 'email' => 'andi.wijaya@talentahr.co.id', 'role' => 'Super Admin', 'status' => 'Aktif', 'avatar' => 15],
        ['nip' => 'USR-0002', 'name' => 'Rina Kartika', 'email' => 'rina.kartika@talentahr.co.id', 'role' => 'HR Admin', 'status' => 'Aktif', 'avatar' => 32],
        ['nip' => 'USR-0003', 'name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@talentahr.co.id', 'role' => 'Finance', 'status' => 'Aktif', 'avatar' => 8],
        ['nip' => 'USR-0004', 'name' => 'Dwight Schrute', 'email' => 'dwight.schrute@talentahr.co.id', 'role' => 'Supervisor', 'status' => 'Menunggu Aktivasi', 'avatar' => 51],
        ['nip' => 'USR-0005', 'name' => 'Angela Martin', 'email' => 'angela.martin@talentahr.co.id', 'role' => 'Finance', 'status' => 'Aktif', 'avatar' => 47],
    ];
@endphp

@section('content')

    {{-- ROLE SUMMARY --}}
    <div class="grid grid-cols-4 gap-5">
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
    <div class="card-flat rounded-2xl overflow-hidden">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Pengguna</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($users) }} pengguna terdaftar</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <select class="appearance-none text-xs font-bold border border-black/10 rounded-lg pl-3 pr-8 py-2.5 bg-white
                                   hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition cursor-pointer">
                        <option>Semua Role</option>
                        @foreach ($roles as $r)
                            <option>{{ $r['name'] }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                </div>

                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" placeholder="Cari nama atau email..."
                           class="w-56 pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition">
                </div>

                <div class="w-px h-6 bg-black/10 mx-0.5"></div>

                <a href="{{ route('admin.users.create') }}"
                   class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg
                          flex items-center gap-1.5 whitespace-nowrap transition">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Tambah Pengguna
                </a>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">Pengguna</th>
                    <th class="px-6 py-3.5">Role</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($users as $user)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/32?img={{ $user['avatar'] }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $user['name'] }}">
                                <div>
                                    <p class="font-bold text-on-surface">{{ $user['name'] }}</p>
                                    <p class="text-xs text-on-surface-variant/50">{{ $user['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $user['role'] === 'Super Admin' ? 'bg-on-surface-variant/10 text-on-surface-variant' : 'bg-primary/10 text-primary' }}">
                                {{ $user['role'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-xs font-semibold {{ $user['status'] === 'Aktif' ? 'text-primary' : 'text-amber-700' }}">
                                {{ $user['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.users.edit', $user['nip']) }}" title="Edit"
                                   class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <button type="button" title="Nonaktifkan"
                                        class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-error hover:bg-error/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">person_off</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection