@extends('layouts.finance')

@section('title', 'Verifikasi Reimbursement')
@section('page-title', 'Verifikasi Reimbursement')
@section('page-desc', 'Klaim yang sudah disetujui atasan/HR, menunggu verifikasi finance sebelum dicairkan.')

@php
    $claims = [
        ['name' => 'Siti Aminah', 'avatar' => 44, 'category' => 'Bensin & Parkir Client', 'amount' => 350000, 'approver' => 'Disetujui SPV & HR', 'status' => 'Pending Finance'],
        ['name' => 'Eko Prasetyo', 'avatar' => 51, 'category' => 'Akomodasi Dinas Luar Kota', 'amount' => 875000, 'approver' => 'Disetujui SPV & HR', 'status' => 'Pending Finance'],
        ['name' => 'Oscar Martinez', 'avatar' => 27, 'category' => 'Makan Lembur Proyek', 'amount' => 120000, 'approver' => 'Disetujui SPV & HR', 'status' => 'Pending Finance'],
        ['name' => 'Angela Martin', 'avatar' => 33, 'category' => 'Alat Tulis Kantor', 'amount' => 275000, 'approver' => 'Diverifikasi 2 Nov', 'status' => 'Approved'],
        ['name' => 'Toby Flenderson', 'avatar' => 61, 'category' => 'Cetak Dokumen Legal', 'amount' => 95000, 'approver' => 'Diverifikasi 1 Nov', 'status' => 'Approved'],
    ];
    $badge = [
        'Pending Finance' => 'bg-amber-500/10 text-amber-700',
        'Approved'        => 'bg-primary/10 text-primary',
        'Ditolak'         => 'bg-error/10 text-error',
    ];
@endphp

@section('content')

    <div class="grid grid-cols-3 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp14.250.000</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Klaim Pending Finance</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">3</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Jumlah Pengajuan Menunggu</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp448.333</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Rata-rata per Klaim</p>
        </div>
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Klaim Reimbursement</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Klaim tampil di sini setelah disetujui Supervisor &amp; HR.</p>
            </div>
            <button class="text-xs font-bold text-primary/70 hover:text-primary flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">filter_list</span> Filter Status
            </button>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Nominal</th>
                    <th class="px-6 py-3">Bukti</th>
                    <th class="px-6 py-3">Riwayat Approval</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($claims as $c)
                    <tr class="hover:bg-surface-container/60">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $c['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $c['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $c['category'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data font-bold text-on-surface">Rp{{ number_format($c['amount'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5">
                            <a href="#" class="text-xs font-bold text-primary underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">receipt_long</span> Lihat struk
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">{{ $c['approver'] }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$c['status']] }}">{{ $c['status'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            @if ($c['status'] === 'Pending Finance')
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                    <button class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center justify-center">
                                    <button class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/5 transition">
                                        <span class="material-symbols-outlined text-[18px]">description</span>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection