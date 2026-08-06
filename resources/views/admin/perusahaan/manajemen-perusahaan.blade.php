@extends('layouts.admin')

@section('title', 'Manajemen Perusahaan')
@section('page-title', 'Manajemen Perusahaan')
@section('page-desc', 'Kelola data perusahaan dan cabang yang terdaftar di sistem.')

@php
    $branches = [
        ['id' => 'BR-001', 'name' => 'Kantor Pusat - Jakarta', 'address' => 'Jl. Sudirman No. 88, Jakarta Selatan', 'pic' => 'Andi Wijaya', 'employees' => 512, 'status' => 'Aktif'],
        ['id' => 'BR-002', 'name' => 'Cabang Surabaya', 'address' => 'Jl. HR Muhammad No. 12, Surabaya', 'pic' => 'Rina Kartika', 'employees' => 341, 'status' => 'Aktif'],
        ['id' => 'BR-003', 'name' => 'Cabang Bandung', 'address' => 'Jl. Asia Afrika No. 45, Bandung', 'pic' => 'Fajar Nugroho', 'employees' => 288, 'status' => 'Aktif'],
        ['id' => 'BR-004', 'name' => 'Cabang Medan', 'address' => 'Jl. Gatot Subroto No. 21, Medan', 'pic' => 'Dewi Lestari', 'employees' => 143, 'status' => 'Persiapan'],
    ];

    $stats = [
        ['label' => 'Total Cabang', 'value' => count($branches), 'icon' => 'domain'],
        ['label' => 'Cabang Aktif', 'value' => collect($branches)->where('status', 'Aktif')->count(), 'icon' => 'check_circle'],
        ['label' => 'Total Karyawan', 'value' => number_format(collect($branches)->sum('employees')), 'icon' => 'groups'],
        ['label' => 'Dalam Persiapan', 'value' => collect($branches)->where('status', 'Persiapan')->count(), 'icon' => 'construction'],
    ];
@endphp

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-[20px]">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $s['value'] }}</p>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- TABLE CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Cabang</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($branches) }} cabang terdaftar di sistem</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" placeholder="Cari nama cabang..."
                           class="w-56 pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition">
                </div>

                <div class="w-px h-6 bg-black/10 mx-0.5"></div>

                <a href="{{ route('admin.companies.create') }}"
                   class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg
                          flex items-center gap-1.5 whitespace-nowrap transition">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Cabang
                </a>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">Nama Cabang</th>
                    <th class="px-6 py-3.5">Alamat</th>
                    <th class="px-6 py-3.5">PIC</th>
                    <th class="px-6 py-3.5">Karyawan</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($branches as $branch)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5 font-bold text-on-surface">{{ $branch['name'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70">{{ $branch['address'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70">{{ $branch['pic'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ number_format($branch['employees']) }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-xs font-semibold {{ $branch['status'] === 'Aktif' ? 'text-primary' : 'text-amber-700' }}">
                                {{ $branch['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.companies.edit', $branch['id']) }}" title="Edit"
                                   class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection