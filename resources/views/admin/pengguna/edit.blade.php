@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')
@section('page-desc', 'Perbarui data akun, role, dan status pengguna.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $user = [
        'nip' => 'USR-0004',
        'full_name' => 'Dwight Schrute',
        'email' => 'dwight.schrute@talentahr.co.id',
        'phone' => '081298765432',
        'role' => 'Supervisor',
        'department' => 'Sales',
        'status' => 'Menunggu Aktivasi',
        'avatar' => 51,
    ];
@endphp

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Pengguna
    </a>

    {{-- IDENTITAS RINGKAS --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-4">
        <img src="https://i.pravatar.cc/56?img={{ $user['avatar'] }}" class="w-14 h-14 rounded-full object-cover" alt="{{ $user['full_name'] }}">
        <div class="flex-1">
            <p class="text-base font-bold text-on-surface">{{ $user['full_name'] }}</p>
            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $user['nip'] }} · {{ $user['email'] }}</p>
        </div>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $user['role'] === 'Super Admin' ? 'bg-on-surface-variant/10 text-on-surface-variant' : 'bg-primary/10 text-primary' }}">
            {{ $user['role'] }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.users.updateWeb', $user['id']) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- DATA AKUN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Akun</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="{{ $user['full_name'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required value="{{ $user['email'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="phone" required value="{{ $user['phone'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">ID Pengguna</label>
                    <input type="text" value="{{ $user['nip'] }}" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>
            </div>
        </div>

        {{-- ROLE & AKSES --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                <h2 class="text-base font-bold text-on-surface">Role &amp; Akses</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Role</label>
                    <div class="relative mt-1.5">
                        <select name="role" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Super Admin', 'HR Admin', 'Finance', 'Supervisor'] as $r)
                                <option {{ $user['role'] === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Departemen</label>
                    <div class="relative mt-1.5">
                        <select name="department" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Human Resources', 'Finance & Accounting', 'Engineering', 'Sales & Marketing'] as $d)
                                <option {{ $user['department'] === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Status</label>
                    <div class="relative mt-1.5">
                        <select name="status" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Aktif', 'Menunggu Aktivasi', 'Nonaktif'] as $s)
                                <option {{ $user['status'] === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="col-span-3 flex items-end">
                    <p class="text-xs text-on-surface-variant/40">Mengubah status ke <span class="font-bold text-error">Nonaktif</span> akan langsung mencabut akses login pengguna ini.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
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