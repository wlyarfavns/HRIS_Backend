@extends('layouts.hr')

@section('title', 'Persetujuan Cuti & Izin')
@section('page-title', 'Persetujuan Cuti & Izin')
@section('page-desc', 'Kelola pengajuan cuti, sakit, dan izin karyawan.')

@php
    $requests = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'type' => 'Cuti Tahunan', 'range' => '12 - 13 Agu', 'quota' => '6 hari tersisa', 'attach' => false, 'status' => 'Pending SPV'],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'type' => 'Sakit', 'range' => '5 Agu', 'quota' => 'Lampiran surat dokter', 'attach' => true, 'status' => 'Pending HR'],
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'type' => 'Izin Pribadi', 'range' => '7 Agu', 'quota' => '-', 'attach' => false, 'status' => 'Approved'],
    ];
    $badge = [
        'Pending SPV' => 'bg-amber-500/10 text-amber-700',
        'Pending HR' => 'bg-purple-500/10 text-purple-700',
        'Approved' => 'bg-primary/10 text-primary',
        'Rejected' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')

    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">18</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Pending Approval</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">142/150</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Hadir Hari Ini</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">4,2</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Rata-rata Sisa Kuota</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">3</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Sakit &gt; 1 Hari (butuh surat)</p>
        </div>
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center gap-2">
            @foreach (['Semua', 'Pending SPV', 'Pending HR', 'Approved', 'Rejected'] as $i => $tab)
                <button class="text-xs font-bold px-3.5 py-2 rounded-lg transition {{ $i === 0 ? 'bg-primary text-white' : 'text-on-surface-variant/60 hover:bg-surface-container' }}">
                    {{ $tab }}
                </button>
            @endforeach
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Tipe</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Keterangan</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($requests as $r)
                    <tr class="hover:bg-surface-container/60">
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
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$r['status']] }}">{{ $r['status'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                <button class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </button>
                                <button class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection