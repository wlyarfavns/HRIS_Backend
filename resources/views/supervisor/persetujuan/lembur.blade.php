@extends('layouts.supervisor')

@section('title', 'Persetujuan Lembur Tim')
@section('page-title', 'Persetujuan Lembur (SPL) Tim')
@section('page-desc', 'Setujui SPL sebelum HR mengunci untuk perhitungan payroll.')

@php
    $pending = [
        ['name' => 'Kevin Malone', 'avatar' => 55, 'hours' => 2, 'project' => 'Closing Laporan Bulanan'],
    ];
    $history = [
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'hours' => 3, 'project' => 'Migrasi Server', 'status' => 'Approved SPV', 'decided' => 'Disetujui kamu, 4 Agu'],
    ];
    $badge = ['Approved SPV' => 'bg-primary/10 text-primary', 'Locked' => 'bg-gray-200 text-gray-600'];
@endphp

@section('content')

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Menunggu Persetujuan Kamu</h2>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">Setelah disetujui, HR akan mengunci SPL untuk masuk perhitungan payroll.</p>
        </div>
        @if (count($pending) === 0)
            <div class="px-6 py-10 text-center text-sm text-on-surface-variant/40">Tidak ada pengajuan lembur yang menunggu review kamu.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Durasi</th>
                        <th class="px-6 py-3">Keperluan</th>
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
                            <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $r['hours'] }} Jam</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $r['project'] }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-black/10 hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                        Setujui SPL
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
                    <th class="px-6 py-3">Durasi</th>
                    <th class="px-6 py-3">Keperluan</th>
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
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $r['hours'] }} Jam</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $r['project'] }}</td>
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