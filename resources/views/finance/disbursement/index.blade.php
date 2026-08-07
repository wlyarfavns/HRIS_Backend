@extends('layouts.finance')

@section('title', 'Disbursement & Slip Gaji')
@section('page-title', 'Disbursement & Slip Gaji')
@section('page-desc', 'Riwayat pencairan dana serta akses karyawan terhadap slip gaji digital.')

@php
    $history = [
        ['date' => '1 Agu 2026', 'type' => 'Payroll Juli 2026', 'count' => 1280, 'total' => 1198400000, 'status' => 'Selesai'],
        ['date' => '28 Jul 2026', 'type' => 'Reimbursement Batch #12', 'count' => 34, 'total' => 8750000, 'status' => 'Selesai'],
        ['date' => '1 Jul 2026', 'type' => 'Payroll Juni 2026', 'count' => 1276, 'total' => 1185900000, 'status' => 'Selesai'],
    ];
    $accessLogs = [
        ['nip' => 'EMP-00812', 'name' => 'Jim Halpert', 'avatar' => 12, 'action' => 'Mengunduh slip gaji Juli 2026', 'time' => '2 Agu, 09.12'],
        ['nip' => 'EMP-01044', 'name' => 'Angela Martin', 'avatar' => 33, 'action' => 'Membuka slip gaji Juli 2026', 'time' => '1 Agu, 16.40'],
        ['nip' => 'EMP-00934', 'name' => 'Oscar Martinez', 'avatar' => 27, 'action' => 'Mengunduh slip gaji Juni 2026', 'time' => '30 Jul, 11.05'],
    ];
@endphp

@section('content')

    <div class="grid grid-cols-3 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">1.284</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Slip Gaji Terdistribusi</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp1,19 M</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Dicairkan (Periode Terakhir)</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">96%</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Slip Sudah Diakses Karyawan</p>
        </div>
    </div>

    {{-- RIWAYAT DISBURSEMENT --}}
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <h2 class="text-base font-bold text-on-surface">Riwayat Disbursement</h2>
            <button class="text-xs font-bold text-primary/70 hover:text-primary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">download</span> Unduh Semua
            </button>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Jenis</th>
                    <th class="px-6 py-3 text-right">Jumlah Penerima</th>
                    <th class="px-6 py-3 text-right">Total Nominal</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">File</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($history as $h)
                    <tr class="hover:bg-surface-container/60">
                        <td class="px-6 py-3.5 text-on-surface-variant/70 font-mono-data text-xs">{{ $h['date'] }}</td>
                        <td class="px-6 py-3.5 font-bold text-on-surface text-xs">{{ $h['type'] }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($h['count'], 0, ',', '.') }} orang</td>
                        <td class="px-6 py-3.5 text-right font-mono-data font-bold text-on-surface">Rp{{ number_format($h['total'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">{{ strtoupper($h['status']) }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <button class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/5 transition">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- LOG AKSES SLIP GAJI --}}
    <div class="card-flat rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-on-surface">Log Akses Slip Gaji Digital</h2>
        </div>
        <div class="space-y-4">
            @foreach ($accessLogs as $log)
                <a href="{{ route('finance.disbursement.slip', $log['nip']) }}"
                   class="flex items-center justify-between border-b border-black/5 pb-4 last:border-0 last:pb-0 hover:bg-surface-container/60 -mx-2 px-2 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <img src="https://i.pravatar.cc/32?img={{ $log['avatar'] }}" class="w-8 h-8 rounded-full" alt="">
                        <div>
                            <p class="text-sm font-bold text-on-surface">{{ $log['name'] }}</p>
                            <p class="text-xs text-on-surface-variant/60">{{ $log['action'] }}</p>
                        </div>
                    </div>
                    <p class="text-xs font-mono-data text-on-surface-variant/40">{{ $log['time'] }}</p>
                </a>
            @endforeach
        </div>
    </div>

@endsection