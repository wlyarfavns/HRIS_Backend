@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard Super Admin')
@section('page-desc', 'Ringkasan manajemen sistem, departemen, dan pengguna aktif.')

@section('content')
<div class="space-y-8">


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="{{ route('admin.users.index') }}" class="block bg-[#0B3D2E] rounded-xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 border border-transparent">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100 mb-1">Karyawan Aktif</p>
            <p class="text-4xl font-bold text-white">{{ $totalEmployees ?? 0 }}</p>
            <p class="text-xs text-emerald-200 mt-2">Lihat daftar karyawan </p>
        </a>

        <a href="{{ route('admin.org-structure.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Total Departemen</p>
            <p class="text-4xl font-bold text-gray-800">{{ $totalDepartments ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-2">Kelola departemen </p>
        </a>

        <a href="{{ route('admin.users.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pengguna Sistem</p>
            <p class="text-4xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-2">Kelola akses </p>
        </a>

    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">


        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-medium text-gray-800">Aksi Cepat</h3>
                </div>
                <div class="p-6 space-y-4">
                    <a href="{{ route('admin.users.create') }}" class="flex items-start gap-4 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 group-hover:text-[#0B3D2E]">Tambah Pengguna</h4>
                            <p class="text-xs text-gray-500 mt-1">Berikan akses sistem baru</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-start gap-4 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 group-hover:text-[#0B3D2E]">Kelola Role</h4>
                            <p class="text-xs text-gray-500 mt-1">Atur hak akses pengguna</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.company.index') }}" class="flex items-start gap-4 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 group-hover:text-[#0B3D2E]">Profil Perusahaan</h4>
                            <p class="text-xs text-gray-500 mt-1">Update data identitas HRIS</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <div class="lg:col-span-2 bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-4">
                    
                    <h3 class="text-lg font-medium text-gray-800">Karyawan Baru Terdaftar</h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                            <th class="px-8 py-4">Karyawan</th>
                            <th class="px-6 py-4">Departemen</th>
                            <th class="px-8 py-4 text-right">Join Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @if(isset($recentEmployees) && $recentEmployees->count() > 0)
                            @foreach($recentEmployees as $emp)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-xs font-medium text-[#0B3D2E] shrink-0">
                                                {{ strtoupper(substr($emp->full_name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-sm text-gray-800 group-hover:text-[#0B3D2E] transition-colors">{{ $emp->full_name }}</p>
                                                <p class="text-[11px]  text-gray-500 mt-0.5">{{ $emp->employee_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ $emp->department?->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 text-right text-xs text-gray-500 ">
                                        {{ $emp->join_date ? \Carbon\Carbon::parse($emp->join_date)->translatedFormat('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="px-8 py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        Belum ada karyawan terdaftar.
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<x-auto-refresh />
@endsection
