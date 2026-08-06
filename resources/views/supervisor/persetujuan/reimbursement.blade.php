@extends('layouts.supervisor')

@section('title', 'Persetujuan Reimbursement Tim')
@section('page-title', 'Persetujuan Reimbursement Tim')
@section('page-desc', 'Setujui klaim tim sebelum diteruskan ke HR & Finance untuk verifikasi.')

@php
    $pending = [
        ['name' => 'Siti Aminah', 'avatar' => 44, 'category' => 'Bensin & Parkir Client', 'amount' => 350000],
    ];
    $history = [
        ['name' => 'Toby Flenderson', 'avatar' => 61, 'category' => 'Alat Tulis Kantor', 'amount' => 275000, 'status' => 'Pending HR', 'decided' => 'Disetujui kamu, 4 Agu'],
        ['name' => 'Oscar Martinez', 'avatar' => 27, 'category' => 'Makan Lembur', 'amount' => 120000, 'status' => 'Approved', 'decided' => 'Disetujui kamu, 2 Agu'],
    ];
    $badge = [
        'Pending HR' => 'bg-purple-500/10 text-purple-700',
        'Pending Finance' => 'bg-amber-500/10 text-amber-700',
        'Approved' => 'bg-primary/10 text-primary',
    ];
@endphp

@section('content')

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Menunggu Persetujuan Kamu</h2>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">Klaim akan diteruskan ke HR, lalu Finance untuk verifikasi & pencairan.</p>
        </div>
        @if (count($pending) === 0)
            <div class="px-6 py-10 text-center text-sm text-on-surface-variant/40">Tidak ada klaim reimbursement yang menunggu review kamu.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Nominal</th>
                        <th class="px-6 py-3">Bukti</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($pending as $c)
                        <tr class="hover:bg-primary/5 transition">
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
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-black/10 hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                        Setujui &amp; Teruskan
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
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Nominal</th>
                    <th class="px-6 py-3">Keputusan Kamu</th>
                    <th class="px-6 py-3">Status Terkini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($history as $c)
                    <tr>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $c['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $c['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $c['category'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data font-bold text-on-surface">Rp{{ number_format($c['amount'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-xs text-on-surface-variant/50">{{ $c['decided'] }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge[$c['status']] }}">{{ $c['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection