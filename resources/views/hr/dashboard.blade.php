@extends('layouts.hr')

@section('title', 'Dashboard HR')
@section('page-title', 'Dashboard')
@section('page-desc', 'Ringkasan kondisi tim dan pengajuan hari ini.')

@section('content')
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

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
            <p class="text-white/60 text-xs">Seluruh sistem — PT Talenta Digital Nusantara</p>
        </div>

        <div class="col-span-8 card-flat rounded-2xl p-6 grid grid-cols-2 divide-x divide-black/5">
            <div class="pr-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest">Kehadiran Hari Ini</p>
                    <span class="text-[11px] font-bold text-primary bg-primary/5 px-2 py-0.5 rounded">142/150</span>
                </div>
                <p class="text-3xl font-bold font-mono-data text-primary mb-1">96,2%</p>
                <a href="{{ route('hr.attendance.index') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Lihat presensi →</a>
            </div>
            <div class="pl-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest">Permintaan Tertunda</p>
                    <span class="text-[11px] font-bold text-on-surface-variant/60 bg-surface-container px-2 py-0.5 rounded">Cuti · Lembur · Klaim</span>
                </div>
                <p class="text-3xl font-bold font-mono-data text-primary mb-1">12</p>
                <a href="{{ route('hr.approvals.leave') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Kelola persetujuan →</a>
            </div>
        </div>
    </div>

    {{-- SHORTCUT MODUL --}}
    <div class="grid grid-cols-3 gap-5">
        <a href="{{ route('hr.approvals.leave') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #FFD700;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Cuti &amp; Izin</p>
                <span class="text-[11px] font-mono-data text-primary bg-primary/5 px-2 py-0.5 rounded">5 pending</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Pengajuan cuti tahunan, sakit, dan izin karyawan.</p>
        </a>

        <a href="{{ route('hr.approvals.overtime') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #0B3D2E;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Lembur (SPL)</p>
                <span class="text-[11px] font-mono-data text-primary bg-primary/5 px-2 py-0.5 rounded">4 pending</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Surat perintah lembur menunggu persetujuan.</p>
        </a>

        <a href="{{ route('hr.payroll.index') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #B9C2BD;">
            <div class="flex items-center justify-between mb-2">
                <p class="font-bold text-on-surface text-sm">Penggajian</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/60 bg-surface-container px-2 py-0.5 rounded">Terjadwal</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Proses payroll periode berjalan dan slip gaji.</p>
        </a>
    </div>

    {{-- PERSETUJUAN TERTUNDA + DISTRIBUSI STAF --}}
    <div class="grid grid-cols-3 gap-5">

        {{-- PERSETUJUAN TERTUNDA --}}
        <div class="col-span-2 card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-on-surface">Persetujuan Tertunda</h2>
                <a href="" class="text-xs font-bold text-primary/60 uppercase tracking-widest hover:text-primary transition">Lihat Semua</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-on-surface-variant/40 border-b border-black/5">
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Karyawan</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Jenis Permintaan</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Tanggal</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-black/5">
                        <td class="py-4 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=12" class="w-8 h-8 rounded-full" alt="">
                            <span class="font-bold text-on-surface">Jim Halpert</span>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded" style="background-color:rgba(255,215,0,0.15); color:#8a7300;">CUTI TAHUNAN</span>
                        </td>
                        <td class="text-on-surface-variant/70 font-mono-data">24–28 Okt</td>
                        <td>
                            <div class="flex gap-2">
                                <form action="{{ route('hr.approvals.dummy-approve') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition" title="Setujui">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                </form>
                                <form action="{{ route('hr.approvals.dummy-reject') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition" title="Tolak">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-4 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=33" class="w-8 h-8 rounded-full" alt="">
                            <span class="font-bold text-on-surface">Angela Martin</span>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">REIMBURSEMENT</span>
                        </td>
                        <td class="text-on-surface-variant/70 font-mono-data">21 Okt</td>
                        <td>
                            <div class="flex gap-2">
                                <form action="{{ route('hr.approvals.dummy-approve') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition" title="Setujui">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                </form>
                                <form action="{{ route('hr.approvals.dummy-reject') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition" title="Tolak">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- DISTRIBUSI STAF --}}
        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-5">Distribusi Staf</h2>

            <div class="space-y-3.5">
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-on-surface-variant/70">Engineering</span>
                        <span class="font-bold font-mono-data text-on-surface">42%</span>
                    </div>
                    <div class="w-full h-1.5 bg-surface-container rounded-full">
                        <div class="h-1.5 rounded-full" style="width: 42%; background-color:#0B3D2E;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-on-surface-variant/70">Marketing</span>
                        <span class="font-bold font-mono-data text-on-surface">28%</span>
                    </div>
                    <div class="w-full h-1.5 bg-surface-container rounded-full">
                        <div class="h-1.5 rounded-full" style="width: 28%; background-color:#0B3D2E;"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-on-surface-variant/70">Sales</span>
                        <span class="font-bold font-mono-data text-on-surface">15%</span>
                    </div>
                    <div class="w-full h-1.5 bg-surface-container rounded-full">
                        <div class="h-1.5 rounded-full" style="width: 15%; background-color:#FFD700;"></div>
                    </div>
                </div>
            </div>

            <button class="w-full mt-6 border border-black/10 rounded-lg py-2.5 text-sm font-bold text-on-surface-variant/80 hover:bg-surface-container transition">
                Analitik Departemen Lengkap
            </button>
        </div>
    </div>

@endsection