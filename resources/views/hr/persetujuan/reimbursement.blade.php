@extends('layouts.hr')

@section('title', 'Persetujuan Reimbursement')
@section('page-title', 'Persetujuan Reimbursement')
@section('page-desc', 'Verifikasi klaim yang sudah disetujui Supervisor, sebelum diteruskan ke Finance.')

@php
    $claims = [
        ['name' => 'Siti Aminah', 'avatar' => 44, 'category' => 'Bensin & Parkir Client', 'amount' => 350000, 'status' => 'Pending Finance'],
        ['name' => 'Toby Flenderson', 'avatar' => 61, 'category' => 'Alat Tulis Kantor', 'amount' => 275000, 'status' => 'Pending HR'],
        ['name' => 'Oscar Martinez', 'avatar' => 27, 'category' => 'Makan Lembur', 'amount' => 120000, 'status' => 'Approved'],
    ];
    $badge = ['Pending HR' => 'bg-purple-500/10 text-purple-700', 'Pending Finance' => 'bg-amber-500/10 text-amber-700', 'Approved' => 'bg-primary/10 text-primary'];
@endphp

@section('content')

    <div class="grid grid-cols-3 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp14.250.000</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Klaim Pending</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">23</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Jumlah Pengajuan</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp2.100.000</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Rata-rata per Klaim</p>
        </div>
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Daftar Klaim Reimbursement</h2>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">HR hanya bertindak pada klaim berstatus "Pending HR". Klaim "Pending Finance" sudah bukan wewenang HR.</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Nominal</th>
                    <th class="px-6 py-3">Bukti</th>
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
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$c['status']] }}">{{ $c['status'] }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            @if ($c['status'] === 'Pending HR')
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </button>
                                    <button type="button" class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                            @elseif ($c['status'] === 'Pending Finance')
                                <div class="flex items-center justify-center">
                                    <span title="Sudah diteruskan ke Finance" class="flex items-center gap-1 text-[11px] font-bold text-on-surface-variant/40">
                                        <span class="material-symbols-outlined text-[15px]">forward_to_inbox</span>
                                        Di Finance
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-center">
                                    <button type="button" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/5 transition">
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