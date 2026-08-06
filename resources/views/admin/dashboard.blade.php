@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard')
@section('page-desc', 'Ringkasan kondisi sistem dan perusahaan Anda hari ini.')

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-4 rounded-2xl p-6 text-white flex flex-col justify-between" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between">
                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Total Karyawan</p>
                <span class="flex items-center gap-1 text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +4%
                </span>
            </div>
            <p class="text-5xl font-extrabold font-mono-data mt-6 mb-1">1.284</p>
            <p class="text-white/60 text-xs">Tersebar di 3 cabang aktif</p>
        </div>

        <div class="col-span-8 card-flat rounded-2xl p-6 grid grid-cols-2 divide-x divide-black/5">
            <div class="pr-5">
                <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest mb-3">Cabang Aktif</p>
                <p class="text-3xl font-bold font-mono-data text-primary mb-1">3</p>
                <a href="{{ route('admin.companies.index') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Kelola cabang →</a>
            </div>
            <div class="pl-5">
                <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest mb-3">Pengguna Sistem</p>
                <p class="text-3xl font-bold font-mono-data text-primary mb-1">24</p>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Kelola akses →</a>
            </div>
        </div>
    </div>

    {{-- RINGKASAN MODUL --}}
    <div class="grid grid-cols-3 gap-5">
        <a href="{{ route('admin.modules.hr') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #FFD700;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Modul HR</p>
                <span class="text-[11px] font-mono-data text-primary bg-primary/5 px-2 py-0.5 rounded">12 pending</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Pengajuan cuti, onboarding, dan kinerja karyawan.</p>
        </a>

        <a href="{{ route('admin.modules.finance') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #0B3D2E;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Modul Finance</p>
                <span class="text-[11px] font-mono-data text-primary bg-primary/5 px-2 py-0.5 rounded">Lancar</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Penggajian, klaim, dan anggaran departemen.</p>
        </a>

        <a href="{{ route('admin.logs.index') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #B9C2BD;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Log Aktivitas</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/60 bg-surface-container px-2 py-0.5 rounded">48 entri</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Riwayat perubahan data dan akses pengguna.</p>
        </a>
    </div>

    {{-- LOG AKTIVITAS + DISTRIBUSI ROLE --}}
    <div class="grid grid-cols-3 gap-5">

        <div class="col-span-2 card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-base font-bold text-on-surface">Aktivitas Sistem Terbaru</h2>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-bold text-primary/60 uppercase tracking-widest hover:text-primary transition">Lihat Semua</a>
            </div>

            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="w-10 shrink-0 text-center">
                        <p class="text-[11px] font-mono-data text-on-surface-variant/40">1 jam</p>
                    </div>
                    <div class="flex-1 pb-1 border-l border-black/5 pl-4 -ml-1">
                        <p class="font-bold text-on-surface text-sm">Pengguna baru ditambahkan</p>
                        <p class="text-sm text-on-surface-variant/60 mt-0.5">Dwight Schrute ditambahkan sebagai Supervisor cabang Scranton.</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-10 shrink-0 text-center">
                        <p class="text-[11px] font-mono-data text-on-surface-variant/40">3 jam</p>
                    </div>
                    <div class="flex-1 pb-1 border-l border-black/5 pl-4 -ml-1">
                        <p class="font-bold text-on-surface text-sm">Hak akses diubah</p>
                        <p class="text-sm text-on-surface-variant/60 mt-0.5">Role Angela Martin diubah dari Staff menjadi Finance Admin.</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-10 shrink-0 text-center">
                        <p class="text-[11px] font-mono-data text-on-surface-variant/40">Kmrn</p>
                    </div>
                    <div class="flex-1 pb-1 border-l border-black/5 pl-4 -ml-1">
                        <p class="font-bold text-on-surface text-sm">Tagihan langganan lunas</p>
                        <p class="text-sm text-on-surface-variant/60 mt-0.5">Pembayaran paket Business bulan November telah diverifikasi.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-5">Distribusi Role Pengguna</h2>

            <div class="space-y-3.5">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-on-surface-variant/70 w-24 shrink-0">HR Admin</span>
                    <div class="flex-1 h-1.5 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: 25%; background-color:#0B3D2E;"></div>
                    </div>
                    <span class="font-mono-data text-sm font-bold text-on-surface w-5 text-right">6</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-on-surface-variant/70 w-24 shrink-0">Finance</span>
                    <div class="flex-1 h-1.5 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: 21%; background-color:#0B3D2E;"></div>
                    </div>
                    <span class="font-mono-data text-sm font-bold text-on-surface w-5 text-right">5</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-on-surface-variant/70 w-24 shrink-0">Supervisor</span>
                    <div class="flex-1 h-1.5 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: 46%; background-color:#FFD700;"></div>
                    </div>
                    <span class="font-mono-data text-sm font-bold text-on-surface w-5 text-right">11</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-on-surface-variant/70 w-24 shrink-0">Super Admin</span>
                    <div class="flex-1 h-1.5 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: 8%; background-color:#B9C2BD;"></div>
                    </div>
                    <span class="font-mono-data text-sm font-bold text-on-surface w-5 text-right">2</span>
                </div>
            </div>

            <div class="flex items-center justify-between mt-5 pt-5 border-t border-black/5">
                <p class="text-xs text-on-surface-variant/40">Total 24 pengguna</p>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-primary hover:underline">Kelola Semua →</a>
            </div>
        </div>
    </div>

@endsection