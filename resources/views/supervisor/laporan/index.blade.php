@extends('layouts.supervisor')

@section('title', 'Laporan Kehadiran Tim')
@section('page-title', 'Laporan Kehadiran Tim')
@section('page-desc', 'Rekap kehadiran bulanan anggota tim kamu.')

@php
    $filterParts = explode('-', $filterMonth);
    $selYear = (int) ($filterParts[0] ?? date('Y'));
    $selMonth = $filterParts[1] ?? date('m');

    // 1. Ambil tahun berjalan secara otomatis dari server (misal: 2026, 2030, 2045, dll)
    $currentYear = (int) date('Y');

    // 2. Rentang Dinamis: 10 tahun ke belakang & 5 tahun ke depan dari tahun saat ini.
    //    min() dan max() memastikan tahun yang sedang dipilih ($selYear) PASTI ada di opsi.
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

    <div class="card-flat rounded-2xl overflow-hidden">
        
        <form x-data="{ year: '{{ $selYear }}', month: '{{ $selMonth }}' }" 
              x-ref="filterForm" 
              method="GET" action="{{ route('supervisor.attendance.report') }}" 
              class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
              
            <div>
                <h2 class="text-base font-bold text-on-surface">
                    Rekap Kehadiran — {{ \Carbon\Carbon::parse($filterMonth)->locale('id')->translatedFormat('F Y') }}
                </h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($teamRecap) }} anggota tim</p>
            </div>
            
            <div class="flex items-center gap-2">
                
                {{-- Hidden input yang menampung nilai gabungan "YYYY-MM" --}}
                <input type="hidden" name="month" x-ref="hiddenInput" :value="year + '-' + month">

                {{-- Dropdown Pilih Bulan --}}
                <div class="relative">
                    <select x-model="month" @change="$nextTick(() => $refs.filterForm.submit())"
                            class="appearance-none pl-3.5 pr-8 py-2.5 bg-surface-container rounded-lg text-xs font-bold border border-transparent
                                   hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 
                                   focus:bg-white focus:outline-none transition cursor-pointer">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[16px] pointer-events-none">expand_more</span>
                </div>

                {{-- Dropdown Pilih Tahun (DINAMIS DARI $startYear SAMPAI $endYear) --}}
                <div class="relative">
                    <select x-model="year" @change="$nextTick(() => $refs.filterForm.submit())"
                            class="appearance-none pl-3.5 pr-8 py-2.5 bg-surface-container rounded-lg text-xs font-bold border border-transparent
                                   hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 
                                   focus:bg-white focus:outline-none transition cursor-pointer">
                        @for ($i = $startYear; $i <= $endYear; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[16px] pointer-events-none">expand_more</span>
                </div>
            </div>
        </form>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Hadir</th>
                    <th class="px-6 py-3">Terlambat</th>
                    <th class="px-6 py-3">Izin / Sakit</th>
                    <th class="px-6 py-3">Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($teamRecap as $t)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="font-bold text-on-surface text-xs">{{ $t['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $t['hadir'] }} hari</td>
                        <td class="px-6 py-3.5 font-mono-data text-amber-700">{{ $t['terlambat'] }}x</td>
                        <td class="px-6 py-3.5 font-mono-data text-violet-700">{{ $t['izin'] }} hari</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-1.5 rounded-full bg-surface-container overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $t['persentase'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-on-surface-variant/60">{{ $t['persentase'] }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                            Belum ada data anggota tim untuk bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection