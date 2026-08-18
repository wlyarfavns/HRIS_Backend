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
        'Super Admin' => 'bg-gray-50 text-gray-700',
        'HR Admin' => 'bg-gray-50 text-[#0B3D2E]',
        'Finance' => 'bg-gray-50 text-[#0B3D2E]',
        'Supervisor' => 'bg-gray-50 text-[#0B3D2E]',
    ];
@endphp

@section('content')


    <div class="bg-white rounded-md shadow-sm p-6 mb-6 flex items-center gap-4 flex-wrap border border-[#043927]/5">
        <div class="relative flex-1 min-w-[220px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#043927]/40 text-[18px]">search</span>
            <input type="text" placeholder="Cari riwayat aktivitas..."
                   class="w-full pl-10 pr-4 py-2.5 bg-[#FDFBF7] rounded-md text-sm border border-[#043927]/10 text-[#043927] placeholder-[#043927]/40 hover:border-[#043927]/20 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition">
        </div>

        <div class="relative">
            <select class="appearance-none text-xs font-medium border border-[#043927]/10 rounded-md pl-4 pr-9 py-2.5 bg-[#FDFBF7] text-[#043927] hover:border-[#043927]/20 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition cursor-pointer">
                <option>Semua Role</option>
                <option>Super Admin</option>
                <option>HR Admin</option>
                <option>Finance</option>
                <option>Supervisor</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#043927]/50 text-[18px] pointer-events-none">expand_more</span>
        </div>

        <div class="relative">
            <select class="appearance-none text-xs font-medium border border-[#043927]/10 rounded-md pl-4 pr-9 py-2.5 bg-[#FDFBF7] text-[#043927] hover:border-[#043927]/20 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition cursor-pointer">
                <option>Semua Jenis Aksi</option>
                <option>Login</option>
                <option>Perubahan Data</option>
                <option>Persetujuan</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[#043927]/50 text-[18px] pointer-events-none">expand_more</span>
        </div>

        <input type="date"
               class="text-xs font-medium border border-[#043927]/10 rounded-md px-4 py-2.5 bg-[#FDFBF7] text-[#043927] hover:border-[#043927]/20 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition">
    </div>


    <div class="bg-white rounded-md shadow-sm p-8">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#043927]/5">
            <div>
                <h2 class="text-lg font-medium text-[#043927]">Riwayat Aktivitas</h2>
                <p class="text-xs text-[#043927]/50 mt-0.5">{{ count($logs) }} entri ditampilkan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-xs text-[#043927]/50 border-b border-[#043927]/5">
                    <tr>
                        <th class="px-4 py-4 font-semibold">WAKTU</th>
                        <th class="px-4 py-4 font-semibold">PENGGUNA</th>
                        <th class="px-4 py-4 font-semibold">AKSI</th>
                        <th class="px-4 py-4 font-semibold">MODUL</th>
                        <th class="px-4 py-4 font-semibold">DETAIL</th>
                    </tr>
                </thead>
                <tbody class="text-[#043927] text-sm" x-data="{ open: null }">
                    @foreach ($logs as $i => $log)
                        <tr class="border-b border-[#043927]/5 hover:bg-[#FDFBF7] transition cursor-pointer" @click="open = open === {{ $i }} ? null : {{ $i }}">
                            <td class="px-4 py-4  text-xs text-[#043927]/60">{{ $log['time'] }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img={{ $log['avatar'] }}" class="w-8 h-8 rounded-full" alt="">
                                    <div>
                                        <p class="font-medium text-xs">{{ $log['user'] }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-medium px-1.5 py-0.5 rounded-md {{ $roleBadge[$log['role']] }}">{{ $log['role'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-semibold text-xs">{{ $log['action'] }}</td>
                            <td class="px-4 py-4 text-[#043927]/60 text-xs">{{ $log['module'] }}</td>
                            <td class="px-4 py-4 text-xs text-[#043927]/60 max-w-xs truncate" x-bind:class="open === {{ $i }} ? '' : 'truncate'">
                                {{ $log['detail'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

