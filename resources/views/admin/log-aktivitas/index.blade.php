@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('page-desc', 'Audit trail seluruh aktivitas penting pengguna di sistem.')

@php
    $logs = [
        ['time' => '07 Agu 2026, 09.12', 'user' => 'Andi Wijaya', 'role' => 'Super Admin', 'avatar' => 15, 'action' => 'Mengubah hak akses', 'module' => 'Pengguna', 'detail' => 'Role Angela Martin diubah dari Staff menjadi Finance Admin.'],
        ['time' => '07 Agu 2026, 08.40', 'user' => 'Rina Kartika', 'role' => 'HR Admin', 'avatar' => 32, 'action' => 'Mengunci SPL', 'module' => 'Persetujuan Lembur', 'detail' => 'Mengunci 3 pengajuan SPL untuk periode Agustus 2026.'],
        ['time' => '06 Agu 2026, 16.55', 'user' => 'Fajar Nugroho', 'role' => 'Finance', 'avatar' => 8, 'action' => 'Menyetujui payroll', 'module' => 'Approval Payroll', 'detail' => 'Payroll periode Juli 2026 disetujui, diteruskan ke Export Bank Transfer.'],
        ['time' => '06 Agu 2026, 14.20', 'user' => 'Andy Bernard', 'role' => 'Supervisor', 'avatar' => 51, 'action' => 'Menyetujui cuti tim', 'module' => 'Persetujuan Tim', 'detail' => 'Cuti tahunan Budi Santoso (12–13 Agu) diteruskan ke HR.'],
        ['time' => '06 Agu 2026, 10.05', 'user' => 'Andi Wijaya', 'role' => 'Super Admin', 'avatar' => 15, 'action' => 'Login', 'module' => 'Sistem', 'detail' => 'Login berhasil dari 118.99.xx.xx (Jakarta).'],
        ['time' => '05 Agu 2026, 17.30', 'user' => 'Rina Kartika', 'role' => 'HR Admin', 'avatar' => 32, 'action' => 'Menambahkan karyawan baru', 'module' => 'Karyawan', 'detail' => 'Onboarding EMP-01288 — Grace Miller, Finance & Accounting.'],
        ['time' => '05 Agu 2026, 11.02', 'user' => 'Andi Wijaya', 'role' => 'Super Admin', 'avatar' => 15, 'action' => 'Mengubah modul', 'module' => 'Modul Finance', 'detail' => 'Fitur "Verifikasi Reimbursement oleh Finance" diaktifkan.'],
    ];
    $roleBadge = [
        'Super Admin' => 'bg-on-surface-variant/10 text-on-surface-variant',
        'HR Admin' => 'bg-primary/10 text-primary',
        'Finance' => 'bg-primary/10 text-primary',
        'Supervisor' => 'bg-primary/10 text-primary',
    ];
@endphp

@section('content')

    {{-- FILTER BAR --}}
    <div class="card-flat rounded-2xl p-5 flex items-center gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[220px]">
            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
            <input type="text" placeholder="Cari nama pengguna..."
                   class="w-full pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                          hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition">
        </div>

        <div class="relative">
            <select class="appearance-none text-xs font-bold border border-black/10 rounded-lg pl-3 pr-8 py-2.5 bg-white
                           hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition cursor-pointer">
                <option>Semua Role</option>
                <option>Super Admin</option>
                <option>HR Admin</option>
                <option>Finance</option>
                <option>Supervisor</option>
            </select>
            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
        </div>

        <div class="relative">
            <select class="appearance-none text-xs font-bold border border-black/10 rounded-lg pl-3 pr-8 py-2.5 bg-white
                           hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition cursor-pointer">
                <option>Semua Jenis Aksi</option>
                <option>Login</option>
                <option>Perubahan Data</option>
                <option>Persetujuan</option>
            </select>
            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
        </div>

        <input type="date"
               class="text-xs font-bold border border-black/10 rounded-lg px-3 py-2.5 bg-white text-on-surface-variant/70
                      hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition">
    </div>

    {{-- TABLE CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Riwayat Aktivitas</h2>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($logs) }} entri ditampilkan</p>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">Waktu</th>
                    <th class="px-6 py-3.5">Pengguna</th>
                    <th class="px-6 py-3.5">Aksi</th>
                    <th class="px-6 py-3.5">Modul</th>
                    <th class="px-6 py-3.5">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5" x-data="{ open: null }">
                @foreach ($logs as $i => $log)
                    <tr class="hover:bg-primary/5 transition cursor-pointer" @click="open = open === {{ $i }} ? null : {{ $i }}">
                        <td class="px-6 py-3.5 font-mono-data text-xs text-on-surface-variant/60 whitespace-nowrap">{{ $log['time'] }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $log['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <div>
                                    <p class="font-bold text-on-surface text-xs">{{ $log['user'] }}</p>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $roleBadge[$log['role']] }}">{{ $log['role'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface font-semibold text-xs">{{ $log['action'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">{{ $log['module'] }}</td>
                        <td class="px-6 py-3.5 text-xs text-on-surface-variant/50 max-w-xs truncate" x-bind:class="open === {{ $i }} ? '' : 'truncate'">
                            {{ $log['detail'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
