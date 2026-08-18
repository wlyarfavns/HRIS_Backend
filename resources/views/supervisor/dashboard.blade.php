@extends('layouts.supervisor')

@section('title', 'Dashboard Supervisor')
@section('page-title', 'Dashboard Supervisor')
@section('page-desc', 'Ringkasan performa tim, persetujuan, dan kehadiran hari ini.')

@section('content')
<div class="space-y-8">


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <a href="{{ route('supervisor.team.index') }}" class="block bg-[#0B3D2E] rounded-xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 border border-transparent">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100 mb-1">Anggota Tim</p>
            <p class="text-4xl font-bold text-white">{{ $stats[0]['value'] }}</p>
            <p class="text-xs text-emerald-200 mt-2">Lihat tim </p>
        </a>

        <a href="{{ route('supervisor.attendance.report') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Hadir Hari Ini</p>
            <p class="text-4xl font-bold text-gray-800">{{ $stats[1]['value'] }}</p>
            <p class="text-xs text-emerald-600 mt-2">Laporan Kehadiran </p>
        </a>

        <a href="{{ route('supervisor.approvals.leave') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Menunggu Persetujuan</p>
            <p class="text-4xl font-bold text-gray-800">{{ $stats[2]['value'] }}</p>
            <p class="text-xs text-emerald-600 mt-2">Cuti & Lembur </p>
        </a>

        <a href="{{ route('supervisor.attendance.report') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Sedang Cuti / Izin</p>
            <p class="text-4xl font-bold text-gray-800">{{ $stats[3]['value'] }}</p>
            <p class="text-xs text-emerald-600 mt-2">Lihat Kehadiran </p>
        </a>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">


        <div class="xl:col-span-2 space-y-8">
            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-4">
                    
                    <h3 class="text-lg font-medium text-gray-800">Tugas Persetujuan (Menunggu)</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                                <th class="px-8 py-4">Karyawan</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Detail</th>
                                <th class="px-8 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @if(isset($pending) && $pending->count() > 0)
                                @foreach($pending as $p)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <img :src="'https://i.pravatar.cc/36?img=' + {{ $p['avatar'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="">
                                            <span class="font-medium text-sm text-gray-800 group-hover:text-[#0B3D2E] transition-colors">{{ $p['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ $p['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs  text-gray-500">{{ $p['detail'] }}</td>
                                    <td class="px-8 py-4 text-center">
                                        <a href="{{ route($p['route'] ?? 'supervisor.dashboard') }}" class="inline-flex items-center gap-1 bg-gray-50 text-[#0B3D2E] text-xs font-medium px-4 py-2 rounded-md hover:bg-[#0B3D2E] hover:text-white transition shadow-sm border border-gray-200">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center gap-2">
                                            Tidak ada pengajuan yang perlu direview
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>


            @if(isset($slaStats))
            <div class="bg-white rounded-md border border-gray-100 shadow-sm p-8">
                <div class="flex items-center gap-4 mb-6">
                    
                    <h3 class="text-lg font-medium text-gray-800">Tingkat Respons Persetujuan (SLA)</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center text-sm font-medium mb-3">
                            <span class="text-gray-500">Tingkat Persetujuan Tepat Waktu</span>
                            <span class="text-[#0B3D2E] font-semibold text-lg">{{ $slaStats['rate'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 shadow-sm overflow-hidden">
                            <div class="bg-[#0B3D2E] h-full rounded-full transition-all duration-500" style="width: {{ $slaStats['rate'] }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 p-5 bg-gray-50 rounded-md border border-gray-100">
                        <div>
                            <span class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Disetujui / Ditolak</span>
                            <span class="font-semibold  text-gray-800 text-lg">{{ $slaStats['approved'] + $slaStats['rejected'] }} <span class="text-xs text-gray-500 font-sans font-medium">Pengajuan</span></span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-1.5">Masih Tertunda</span>
                            <span class="font-semibold  text-gray-700 text-lg">{{ $slaStats['pending'] }} <span class="text-xs text-gray-500 font-sans font-medium">Pengajuan</span></span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>


        <div class="xl:col-span-1 space-y-8">
            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[500px]">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 shrink-0">
                    <h3 class="text-base font-medium text-gray-800">Kehadiran Tim Hari Ini</h3>
                    <p class="text-xs text-gray-500 mt-1">Status presensi anggota tim</p>
                </div>

                <div class="flex-1 overflow-y-auto p-2 custom-scrollbar">
                    @if(isset($team) && $team->count() > 0)
                        <div class="space-y-1">
                        @foreach($team as $m)
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-md transition">
                            <div class="flex items-center gap-3">
                                <img :src="'https://i.pravatar.cc/32?img=' + {{ $m['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-gray-100" alt="">
                                <div>
                                    <p class="font-medium text-sm text-gray-800 leading-tight">{{ $m['name'] }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5 ">{{ $m['time'] }}</p>
                                </div>
                            </div>
                            <div>
                                @php
                                    $bClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                    if ($m['status'] === 'Hadir') $bClass = 'bg-gray-50 text-[#0B3D2E] border-gray-200';
                                    elseif ($m['status'] === 'Terlambat') $bClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                    elseif (str_contains($m['status'], 'Izin') || str_contains($m['status'], 'Cuti')) $bClass = 'bg-violet-50 text-violet-700 border-violet-100';
                                    elseif ($m['status'] === 'Belum Presensi') $bClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                @endphp
                                <span class="text-[10px] font-medium px-2.5 py-1 rounded-md border {{ $bClass }}">
                                    {{ $m['status'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-full">
                            <p class="text-sm font-medium text-gray-500">Belum ada anggota tim.</p>
                        </div>
                    @endif
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-medium text-gray-800">Pintasan Supervisor</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('supervisor.attendance.report') }}" class="flex items-center gap-3 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[16px] text-[#0B3D2E]">description</span>
                        </div>
                        <span class="font-medium text-sm text-gray-700 group-hover:text-[#0B3D2E]">Laporan Kehadiran Tim</span>
                    </a>
                    <a href="{{ route('supervisor.approvals.shift') }}" class="flex items-center gap-3 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[16px] text-gray-700">swap_horiz</span>
                        </div>
                        <span class="font-medium text-sm text-gray-700 group-hover:text-[#0B3D2E]">Persetujuan Tukar Shift</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>
@endsection
