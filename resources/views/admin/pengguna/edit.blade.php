@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')
@section('page-desc', 'Perbarui data akun, role, dan status pengguna.')

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60 hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Pengguna
    </a>

    {{-- IDENTITAS RINGKAS --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-4 mt-2">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
            class="w-14 h-14 rounded-full object-cover" alt="{{ $user->name }}">
        <div class="flex-1">
            <p class="text-base font-bold text-on-surface">{{ $user->name }}</p>
            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">ID: {{ $user->id }} · {{ $user->email }}</p>
        </div>
        <span
            class="text-[11px] font-bold px-2.5 py-1 rounded {{ $user->display_role === 'Super Admin' ? 'bg-on-surface-variant/10 text-on-surface-variant' : 'bg-primary/10 text-primary' }}">
            {{ $user->display_role }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.users.updateWeb', $user->id) }}" class="space-y-6 mt-6"
        onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan data pengguna ini?');">
        @csrf
        @method('PUT')

        {{-- DATA AKUN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span
                    class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Akun</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="{{ $user->name }}"
                        class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required value="{{ $user->email }}"
                        class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Password Baru
                        (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                        class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
        </div>

        {{-- ROLE & AKSES --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span
                    class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                <h2 class="text-base font-bold text-on-surface">Role &amp; Akses</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Role</label>
                    <div class="relative mt-1.5">
                        <select name="role" required
                            class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Super Admin', 'HR Admin', 'Finance', 'Supervisor'] as $r)
                                <option {{ $user->display_role === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                        <span
                            class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8">
            <a href="{{ route('admin.users.index') }}"
                class="px-5 py-2.5 rounded-lg text-sm font-bold text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
                Batal
            </a>
            <button type="submit"
                class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection