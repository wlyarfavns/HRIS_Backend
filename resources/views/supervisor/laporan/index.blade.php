@extends('layouts.supervisor')

@section('title', 'Laporan Kehadiran Tim')
@section('page-title', 'Laporan Kehadiran Tim')
@section('page-desc', 'Rekap kehadiran bulanan anggota tim kamu.')

@php
    $filterParts = explode('-', $filterMonth);
    $selYear = (int) ($filterParts[0] ?? date('Y'));
    $selMonth = $filterParts[1] ?? date('m');

    $currentYear = (int) date('Y');

    $startYear = min($currentYear - 10, $selYear);
    $endYear   = max($currentYear + 5, $selYear);

    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
@endphp

@section('content')

    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">

        <form x-data="{ year: '{{ $selYear }}', month: '{{ $selMonth }}' }" 
              x-ref="filterForm" 
              method="GET" action="{{ route('supervisor.attendance.report') }}" 
              class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">

            <div>
                <h2 class="text-lg font-medium text-gray-800">
                    Rekap Kehadiran — {{ \Carbon\Carbon::parse($filterMonth)->locale('id')->translatedFormat('F Y') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">{{ count($teamRecap) }} anggota tim</p>
            </div>

            <div class="flex items-center gap-3">


                <input type="hidden" name="month" x-ref="hiddenInput" :value="year + '-' + month">


                <div class="relative">
                    <select x-model="month" @change="$nextTick(() => $refs.filterForm.submit())"
                            class="appearance-none pl-4 pr-10 py-2.5 bg-white rounded-md text-sm font-medium text-gray-700 border border-gray-200 hover:bg-gray-50 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:outline-none transition cursor-pointer shadow-sm">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>


                <div class="relative">
                    <select x-model="year" @change="$nextTick(() => $refs.filterForm.submit())"
                            class="appearance-none pl-4 pr-10 py-2.5 bg-white rounded-md text-sm font-medium text-gray-700 border border-gray-200 hover:bg-gray-50 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:outline-none transition cursor-pointer shadow-sm">
                        @for ($i = $startYear; $i <= $endYear; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm whitespace-nowrap text-left">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Hadir</th>
                        <th class="px-6 py-4">Terlambat</th>
                        <th class="px-6 py-4">Izin</th>
                        <th class="px-6 py-4">Sakit</th>
                        <th class="px-6 py-4">Cuti</th>
                        <th class="px-6 py-4">Alpha</th>
                        <th class="px-8 py-4">Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($teamRecap as $t)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <span class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $t['name'] }}</span>
                            </td>
                            <td class="px-6 py-4  text-[#0B3D2E] font-semibold text-sm">{{ $t['hadir'] }} hari</td>
                            <td class="px-6 py-4  text-gray-700 font-semibold text-sm">{{ $t['terlambat'] }}x</td>
                            <td class="px-6 py-4  text-violet-600 font-semibold text-sm">{{ $t['izin'] }} hari</td>
                            <td class="px-6 py-4  text-gray-700 font-semibold text-sm">{{ $t['sakit'] }} hari</td>
                            <td class="px-6 py-4  text-gray-700 font-semibold text-sm">{{ $t['cuti'] }} hari</td>
                            <td class="px-6 py-4  text-gray-700 font-semibold text-sm">{{ $t['alpha'] }} hari</td>
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-32 h-2.5 rounded-full bg-gray-100 overflow-hidden shadow-sm border border-gray-200">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $t['persentase'] >= 90 ? 'bg-[#0B3D2E]' : ($t['persentase'] >= 70 ? 'bg-gray-50' : 'bg-gray-50') }}" style="width: {{ $t['persentase'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-800 ">{{ $t['persentase'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada data anggota tim untuk bulan ini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $teamRecap->links() }}
        </div>
    </div>

@endsection
