@extends('layouts.admin')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')
@section('page-desc', 'Buat akun pengguna baru beserta role dan hak aksesnya.')

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60 hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Pengguna
    </a>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6"
        onsubmit="return confirm('Apakah Anda yakin ingin menambahkan pengguna baru ini?');">
        @csrf

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
                    <input type="text" name="full_name" required placeholder="Nama sesuai identitas"
                        class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required placeholder="nama@talentahr.co.id"
                        class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Password
                        Sementara</label>
                    <input type="password" name="password" required placeholder="Min. 8 karakter"
                        class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div class="col-span-3 flex items-end mt-2">
                    <p class="text-xs text-on-surface-variant/40">Pengguna akan diminta mengganti password saat login
                        pertama kali.</p>
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
                            <option value="">Pilih role</option>
                            <option>HR Admin</option>
                            <option>Finance</option>
                            <option>Supervisor</option>
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
                class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Pengguna
            </button>
        </div>
    </form>
@endsection