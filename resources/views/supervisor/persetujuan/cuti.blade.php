@extends('layouts.supervisor')

@section('title', 'Persetujuan Cuti & Izin Tim')
@section('page-title', 'Persetujuan Cuti & Izin Tim')
@section('page-desc', 'Review pengajuan cuti/izin anggota tim sebelum diteruskan ke HR.')

@php
    $pending = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'type' => 'Cuti Tahunan', 'range' => '12 - 13 Agu', 'quota' => '6 hari tersisa', 'attach' => false],
    ];

    $history = [
        ['name' => 'Siti Aminah', 'avatar' => 44, 'type' => 'Sakit', 'range' => '5 Agu', 'status' => 'Pending HR', 'decided' => 'Disetujui kamu, 4 Agu'],
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'type' => 'Izin Pribadi', 'range' => '7 Agu', 'status' => 'Approved', 'decided' => 'Disetujui kamu, 3 Agu'],
    ];
    $badge = [
        'Pending HR' => 'bg-purple-500/10 text-purple-700',
        'Approved' => 'bg-primary/10 text-primary',
        'Rejected' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Menunggu Persetujuan Kamu</h2>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">Setelah disetujui, pengajuan otomatis diteruskan ke HR untuk review final.</p>
        </div>
        @if (count($pending) === 0)
            <div class="px-6 py-10 text-center text-sm text-on-surface-variant/40">Tidak ada pengajuan yang menunggu review kamu saat ini.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($pending as $r)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="https://i.pravatar.cc/28?img={{ $r['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                    <span class="font-bold text-on-surface text-xs">{{ $r['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded" style="background-color:rgba(255,215,0,0.15); color:#8a7300;">{{ $r['type'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $r['range'] }}</td>
                            <td class="px-6 py-3.5 text-xs text-on-surface-variant/60">
                                {{ $r['quota'] }}
                                @if ($r['attach'])
                                    <a href="#" class="ml-1 text-primary font-bold underline">Lihat surat</a>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-black/10 hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                        Setujui &amp; Teruskan ke HR
                                    </button>
                                    <button type="button" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Riwayat Keputusan Kamu</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Tipe</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Keputusan Kamu</th>
                    <th class="px-6 py-3">Status Terkini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($history as $r)
                    <tr>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $r['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $r['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $r['type'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $r['range'] }}</td>
                        <td class="px-6 py-3.5 text-xs text-on-surface-variant/50">{{ $r['decided'] }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$r['status']] }}">{{ $r['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection