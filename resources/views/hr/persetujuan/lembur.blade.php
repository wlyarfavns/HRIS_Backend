@extends('layouts.hr')

@section('title', 'Persetujuan Lembur (SPL)')
@section('page-title', 'Persetujuan Lembur (SPL)')
@section('page-desc', 'Surat Perintah Lembur menunggu persetujuan & kunci sebelum payroll.')

@php
    $requests = [
        ['name' => 'Eko Prasetyo', 'avatar' => 19, 'hours' => 3, 'project' => 'Migrasi Server', 'status' => 'Approved Spv', 'salary' => 6500000],
        ['name' => 'Kevin Malone', 'avatar' => 55, 'hours' => 2, 'project' => 'Closing Laporan Bulanan', 'status' => 'Pending Spv', 'salary' => 5200000],
    ];
    $badge = ['Pending Spv' => 'bg-amber-500/10 text-amber-700', 'Approved Spv' => 'bg-primary/10 text-primary', 'Locked' => 'bg-gray-200 text-gray-600'];
@endphp

@section('content')

    <div class="grid grid-cols-3 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">12</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Sedang Lembur Hari Ini</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">6</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">SPL Pending Approval</p>
        </div>
        <div class="card-flat rounded-2xl p-5 border-l-[3px]" style="border-color:#FFD700">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">Rumus Upah Lembur (Depnaker)</p>
            <p class="text-sm font-bold text-on-surface mt-2 font-mono-data">1/173 × Gaji Pokok × Jam</p>
        </div>
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Daftar Pengajuan SPL</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Durasi</th>
                    <th class="px-6 py-3">Keperluan</th>
                    <th class="px-6 py-3">Estimasi Upah Lembur</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($requests as $r)
                    @php $upah = round((1/173) * $r['salary'] * $r['hours']); @endphp
                    <tr class="hover:bg-surface-container/60">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $r['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $r['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $r['hours'] }} Jam</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $r['project'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data font-bold text-primary">Rp{{ number_format($upah, 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$r['status']] }}">{{ $r['status'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-2">
                                <button class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-black/10 hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                    Lock SPL
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