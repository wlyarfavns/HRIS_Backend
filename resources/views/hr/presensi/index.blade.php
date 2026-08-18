@extends('layouts.hr')

@section('title', 'Presensi & Live Attendance')
@section('page-title', 'Presensi & Live Attendance')
@section('page-desc', 'Pencatatan jam masuk & keluar via GPS Haversine + live selfie capture.')

@section('content')
    <div x-data="{
        activePreview: null,
        showClockWidget: false,
    }">


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            @foreach ($stats as $s)
                @php
                    $colorMap = [
                        'text-[#0B3D2E]' => 'text-[#0B3D2E]',
                        'text-gray-700' => 'text-gray-700',
                        'text-rose-700' => 'text-gray-700',
                        'text-gray-700' => 'text-gray-700',
                    ];
                    $cleanColor = $colorMap[$s['color']] ?? 'text-gray-800';
                @endphp
                <div class="bg-white rounded-md p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center {{ $cleanColor }}">
                            <span class="material-symbols-outlined text-[18px]">{{ $s['icon'] }}</span>
                        </div>
                    </div>
                    <p class="text-3xl font-semibold  {{ $cleanColor }} leading-none">{{ $s['value'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-2 ">{{ $s['note'] }}</p>
                </div>
            @endforeach
        </div>


        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <form method="GET" action="{{ route('hr.attendance.index') }}"
                class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
                <div>
                    <h2 class="text-base font-medium text-gray-800">Log Presensi &amp; Live Attendance</h2>
                    <p class="text-xs text-gray-500 mt-1">{{ count($logs) }} karyawan tercatat · klik foto
                        selfie untuk melihat geotag lokasi & waktu</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama karyawan..."
                            class="w-64 pl-10 pr-4 py-2.5 bg-white rounded-md text-sm border border-gray-200
                                      focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition">
                    </div>
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                        class="text-sm border border-gray-200 rounded-md px-4 py-2.5 bg-white text-gray-700
                                  focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition cursor-pointer">
                    <a href="{{ route('hr.attendance.export', ['date' => $date]) }}"
                        class="border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2.5 rounded-md text-sm font-medium text-gray-700 flex items-center gap-2 transition shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">download</span> Export Rekap
                    </a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr
                            class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4">Bukti Selfie</th>
                            <th class="px-6 py-4">Jam Masuk / Keluar</th>
                            <th class="px-6 py-4">Total Jam Kerja Efektif</th>
                            <th class="px-6 py-4">Jarak GPS (Haversine)</th>
                            <th class="px-6 py-4">Status Presensi</th>
                            <th class="px-6 py-4">Keterangan / Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($logs as $l)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <p class="font-medium text-gray-800 text-sm leading-tight">{{ $l['name'] }}</p>
                                        <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">
                                            <span
                                                class=" border-r border-gray-300 pr-2 mr-2">{{ $l['nip'] }}</span>
                                            {{ $l['loc'] }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($l['in'] !== '-')
                                        <button type="button" @click="activePreview = {{ $loop->index }}"
                                            class="block w-10 h-10 rounded-md overflow-hidden ring-2 ring-transparent hover:ring-[#0B3D2E]/40 shadow-sm transition"
                                            title="Klik untuk pratinjau watermark geotag">
                                            <img src="{{ $l['photo_in'] ? asset('storage/' . $l['photo_in']) : 'https://i.pravatar.cc/64?img=' . ($l['avatar'] + 1) }}"
                                                class="w-full h-full object-cover" alt="selfie {{ $l['name'] }}">
                                        </button>
                                    @else
                                        <span class="text-gray-300 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4  text-sm text-gray-800">
                                    <span class="font-medium">{{ $l['in'] }}</span> <span
                                        class="text-gray-400 mx-1">–</span> <span
                                        class="font-medium">{{ $l['out'] }}</span>
                                </td>
                                <td class="px-6 py-4  text-sm font-medium text-[#0B3D2E]">
                                    {{ $l['effective_hours'] }}
                                </td>
                                <td class="px-6 py-4  text-sm text-gray-600">
                                    @if ($l['distance'] !== '-')
                                        <span class="flex items-center gap-1.5 text-xs text-gray-700 font-medium bg-gray-50 px-2 py-1 rounded-md w-fit">
                                            <span class="material-symbols-outlined text-[14px]">near_me</span>
                                            {{ $l['distance'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-[10px] font-medium px-2.5 py-1.5 rounded-lg uppercase tracking-wider {{ $badge[$l['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $l['status'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($l['attachment'])
                                        <a href="{{ asset('storage/' . $l['attachment']) }}" target="_blank"
                                            title="{{ $l['note'] }}"
                                            class="text-[11px] font-medium text-[#0B3D2E] flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-emerald-100 transition w-fit">
                                            <span class="material-symbols-outlined text-[16px]">description</span>
                                            Lihat Dokumen
                                        </a>
                                    @elseif ($l['note'])
                                        <span class="text-xs text-gray-500">{{ $l['note'] }}</span>
                                    @else
                                        <span class="text-gray-300 text-sm">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Tidak ada data presensi untuk tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-6 pb-6">
                {{ $logs->links() }}
            </div>
        </div>


        @foreach ($logs as $l)
            @if ($l['in'] !== '-')
                <div x-show="activePreview === {{ $loop->index }}" x-cloak
                    class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 " @click.self="activePreview = null">
                    <div
                        class="bg-white rounded-md overflow-hidden max-w-sm w-full shadow-sm animate-in fade-in zoom-in-95 duration-150 border border-gray-100">
                        <div class="relative">
                            <img src="{{ $l['photo_in'] ? asset('storage/' . $l['photo_in']) : 'https://i.pravatar.cc/480?img=' . ($l['avatar'] + 1) }}"
                                class="w-full h-80 object-cover" alt="selfie {{ $l['name'] }}">
                            <button type="button" @click="activePreview = null"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>


                            <div
                                class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent px-5 pt-12 pb-4 text-white">
                                <p class="text-sm font-medium flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-gray-700">location_on</span>
                                    {{ $l['loc'] }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-white/80 mt-1.5 ">
                                    <span>{{ $l['date'] }} · {{ $l['in'] }} WIB</span>
                                    @if ($l['lat_in'] && $l['lng_in'])
                                        <span class="text-gray-700 font-medium">GPS: {{ $l['lat_in'] }}, {{ $l['lng_in'] }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-2 text-[10px] text-white/90 font-medium">
                                    <span class="px-2 py-1 rounded-md bg-white/20 ">Haversine: {{ $l['distance'] }}</span>
                                    <span
                                        class="px-2 py-1 rounded-md  {{ $l['mock_gps'] ? 'bg-gray-50/80' : 'bg-[#0B3D2E]/80' }} text-white">
                                        Anti-Mock GPS: {{ $l['mock_gps'] ? 'Terdeteksi' : 'Lolos' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="px-5 py-4 flex items-center justify-between bg-gray-50 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <p class="text-sm font-medium text-gray-800">{{ $l['name'] }}</p>
                            </div>
                            <span
                                class="text-[10px] font-medium px-3 py-1.5 rounded-lg uppercase tracking-wider {{ $badge[$l['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $l['status'] }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    </div>
@endsection
