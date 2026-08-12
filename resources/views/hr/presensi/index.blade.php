@extends('layouts.hr')

@section('title', 'Presensi & Live Attendance')
@section('page-title', 'Presensi & Live Attendance')
@section('page-desc', 'Pencatatan jam masuk & keluar via GPS Haversine + live selfie capture.')


@section('content')
<div x-data="{
    activePreview: null,
    showClockWidget: false,
}">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] {{ $s['color'] }}">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
                <p class="text-[11px] text-on-surface-variant/40 mt-1 font-mono-data">{{ $s['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- LOG TABLE --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <form method="GET" action="{{ route('hr.attendance.index') }}"
              class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Log Presensi &amp; Live Attendance</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($logs) }} karyawan tercatat · klik foto selfie untuk melihat geotag lokasi & waktu</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama karyawan..."
                           class="w-52 pl-9 pr-3 py-2 bg-surface-container rounded-lg text-xs border border-transparent
                                  hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition">
                </div>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                       class="text-xs font-bold border border-black/10 rounded-lg px-3 py-2 bg-white
                              hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 transition">
                <a href="{{ route('hr.attendance.export', ['date' => $date]) }}"
                   class="border border-black/10 hover:bg-surface-container px-3 py-2 rounded-lg text-xs font-bold text-on-surface flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">download</span> Export Rekap
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Bukti Selfie</th>
                        <th class="px-4 py-3.5">Jam Masuk / Keluar</th>
                        <th class="px-4 py-3.5">Total Jam Kerja Efektif</th>
                        <th class="px-4 py-3.5">Jarak GPS (Haversine)</th>
                        <th class="px-4 py-3.5">Status Presensi</th>
                        <th class="px-6 py-3.5">Keterangan / Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse ($logs as $l)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img={{ $l['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="{{ $l['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $l['name'] }}</p>
                                        <p class="text-[10px] text-on-surface-variant/50 line-clamp-1">{{ $l['loc'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($l['in'] !== '-')
                                    <button type="button" @click="activePreview = {{ $loop->index }}"
                                            class="block w-9 h-9 rounded-lg overflow-hidden ring-2 ring-transparent hover:ring-primary/40 shadow-sm transition"
                                            title="Klik untuk pratinjau watermark geotag">
                                        <img src="{{ $l['photo_in'] ? asset('storage/' . $l['photo_in']) : 'https://i.pravatar.cc/64?img=' . ($l['avatar'] + 1) }}"
                                             class="w-full h-full object-cover" alt="selfie {{ $l['name'] }}">
                                    </button>
                                @else
                                    <span class="text-on-surface-variant/30 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface">
                                <span class="font-bold">{{ $l['in'] }}</span> <span class="text-on-surface-variant/30">–</span> <span class="font-bold">{{ $l['out'] }}</span>
                            </td>
                            <td class="px-4 py-3.5 font-mono-data text-xs font-semibold text-primary">
                                {{ $l['effective_hours'] }}
                            </td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface-variant/70">
                                @if ($l['distance'] !== '-')
                                    <span class="flex items-center gap-1 text-[11px] text-primary font-bold">
                                        <span class="material-symbols-outlined text-[14px]">near_me</span>
                                        {{ $l['distance'] }}
                                    </span>
                                @else
                                    <span class="text-on-surface-variant/30">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] ?? 'bg-gray-200 text-gray-700' }}">{{ $l['status'] }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                @if ($l['attachment'])
                                    <a href="{{ asset('storage/' . $l['attachment']) }}" target="_blank" title="{{ $l['note'] }}"
                                       class="text-[11px] font-bold text-primary flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary/10 hover:bg-primary/20 transition w-fit">
                                        <span class="material-symbols-outlined text-[15px]">description</span>
                                        Lihat Dokumen
                                    </a>
                                @elseif ($l['note'])
                                    <span class="text-xs text-on-surface-variant/60">{{ $l['note'] }}</span>
                                @else
                                    <span class="text-on-surface-variant/30 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Tidak ada data presensi untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PREVIEW SELFIE + GEOTAG WATERMARK --}}
    @foreach ($logs as $l)
        @if ($l['in'] !== '-')
            <div x-show="activePreview === {{ $loop->index }}" x-cloak
                 class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
                 @click.self="activePreview = null">
                <div class="bg-white rounded-2xl overflow-hidden max-w-sm w-full shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                    <div class="relative">
                        <img src="{{ $l['photo_in'] ? asset('storage/' . $l['photo_in']) : 'https://i.pravatar.cc/480?img=' . ($l['avatar'] + 1) }}"
                             class="w-full h-80 object-cover" alt="selfie {{ $l['name'] }}">
                        <button type="button" @click="activePreview = null"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center transition">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>

                        {{-- WATERMARK GEOTAG & HAVERSINE LOG --}}
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent px-4 pt-10 pb-3.5 text-white">
                            <p class="text-sm font-bold flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-brand-gold">location_on</span>
                                {{ $l['loc'] }}
                            </p>
                            <div class="flex items-center justify-between text-[11px] text-white/80 mt-1 font-mono-data">
                                <span>{{ $l['date'] }} · {{ $l['in'] }} WIB</span>
                                @if ($l['lat_in'] && $l['lng_in'])
                                    <span class="text-brand-gold font-bold">GPS: {{ $l['lat_in'] }}, {{ $l['lng_in'] }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-white/70">
                                <span class="px-1.5 py-0.5 rounded bg-white/20">Haversine: {{ $l['distance'] }}</span>
                                <span class="px-1.5 py-0.5 rounded {{ $l['mock_gps'] ? 'bg-error/80' : 'bg-primary/80' }} text-white">
                                    Anti-Mock GPS: {{ $l['mock_gps'] ? 'Terdeteksi' : 'Lolos' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between bg-surface-container/40">
                        <div class="flex items-center gap-2.5">
                            <img src="https://i.pravatar.cc/28?img={{ $l['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                            <p class="text-xs font-bold text-on-surface">{{ $l['name'] }}</p>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$l['status']] ?? 'bg-gray-200 text-gray-700' }}">{{ $l['status'] }}</span>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>
@endsection