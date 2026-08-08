@extends('layouts.finance')

@section('title', 'Disbursement & Slip Gaji')
@section('page-title', 'Disbursement & Slip Gaji')
@section('page-desc', 'Riwayat pencairan dana transfer bank serta monitoring akses karyawan terhadap slip gaji digital.')

@php
    $stats = [
        ['label' => 'Total Slip Terdistribusi', 'value' => '1.284 Slip', 'icon' => 'mark_email_read', 'color' => 'text-primary'],
        ['label' => 'Total Dicairkan Bulan Ini', 'value' => 'Rp1,24 M', 'icon' => 'payments', 'color' => 'text-primary'],
        ['label' => 'Tingkat Unduh Karyawan', 'value' => '96,4%', 'icon' => 'download_done', 'color' => 'text-on-surface'],
        ['label' => 'Akses Terenkripsi', 'value' => '100% Aman', 'icon' => 'lock', 'color' => 'text-primary'],
    ];

    $history = [
        ['date' => '01 Agu 2026', 'type' => 'Payroll Massal — Juli 2026', 'count' => 1280, 'total' => 1198400000, 'status' => 'Selesai Transfer', 'bank' => 'BCA, Mandiri, BNI'],
        ['date' => '28 Jul 2026', 'type' => 'Reimbursement Batch #12', 'count' => 34, 'total' => 8750000, 'status' => 'Selesai Transfer', 'bank' => 'BCA Transfer'],
        ['date' => '01 Jul 2026', 'type' => 'Payroll Massal — Juni 2026', 'count' => 1276, 'total' => 1185900000, 'status' => 'Selesai Transfer', 'bank' => 'BCA, Mandiri, BNI'],
    ];

    $accessLogs = [
        ['nip' => 'EMP-00812', 'name' => 'Jim Halpert', 'avatar' => 12, 'action' => 'Mengunduh Slip Gaji Digital (PDF)', 'time' => 'Hari ini, 09.12 WIB', 'ip' => '182.1.22.45'],
        ['nip' => 'EMP-01044', 'name' => 'Angela Martin', 'avatar' => 33, 'action' => 'Membuka pratinjau Slip Gaji Digital', 'time' => 'Hari ini, 08.40 WIB', 'ip' => '182.1.22.88'],
        ['nip' => 'EMP-00933', 'name' => 'Oscar Martinez', 'avatar' => 27, 'action' => 'Mengunduh Slip Gaji Digital (PDF)', 'time' => 'Kemarin, 16.05 WIB', 'ip' => '114.122.10.12'],
        ['nip' => 'EMP-00231', 'name' => 'Michael Scott', 'avatar' => 14, 'action' => 'Mengunduh Slip Gaji Digital (PDF)', 'time' => 'Kemarin, 14.30 WIB', 'ip' => '182.1.22.10'],
    ];
@endphp

@section('content')
<div>

    {{-- STATS ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] {{ $s['color'] }}">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- RIWAYAT DISBURSEMENT CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Riwayat Pencairan Dana &amp; Disbursement</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Catatan transfer dana payroll dan klaim reimbursement ke rekening bank karyawan</p>
            </div>

            <button type="button" class="border border-black/10 hover:bg-surface-container px-3.5 py-2 rounded-lg text-xs font-bold text-on-surface flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">download</span>
                Unduh Rekap Disbursement (XLSX)
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Tanggal Transfer</th>
                        <th class="px-4 py-3.5">Jenis Disbursement</th>
                        <th class="px-4 py-3.5">Kanal Bank Mitra</th>
                        <th class="px-4 py-3.5 text-right">Jumlah Penerima</th>
                        <th class="px-4 py-3.5 text-right">Total Nominal</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($history as $h)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5 text-on-surface-variant/70 font-mono-data text-xs">{{ $h['date'] }}</td>
                            <td class="px-4 py-3.5 font-bold text-on-surface text-xs">{{ $h['type'] }}</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface-variant/70 font-mono-data">{{ $h['bank'] }}</td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">{{ number_format($h['count'], 0, ',', '.') }} Org</td>
                            <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-xs text-primary">Rp{{ number_format($h['total'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">{{ $h['status'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" title="Unduh Berkas Bukti Transfer"
                                        class="p-2 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- LOG AKSES SLIP GAJI DIGITAL --}}
    <div class="card-flat rounded-2xl p-6 mt-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-on-surface">Audit Trail Akses Slip Gaji Digital</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Tracking waktu dan alamat IP saat karyawan membuka atau mengunduh dokumen slip gaji</p>
            </div>
            <span class="text-[10px] font-mono-data px-2 py-0.5 rounded bg-surface-container text-on-surface-variant/70">Encrypted Log</span>
        </div>

        <div class="space-y-3 divide-y divide-black/5">
            @foreach ($accessLogs as $log)
                <div class="pt-3 first:pt-0 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <img src="https://i.pravatar.cc/32?img={{ $log['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                        <div>
                            <p class="text-xs font-bold text-on-surface">{{ $log['name'] }} <span class="font-normal font-mono-data text-on-surface-variant/40">({{ $log['nip'] }})</span></p>
                            <p class="text-[11px] text-on-surface-variant/60">{{ $log['action'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-mono-data text-on-surface-variant/60">
                        <span class="text-[11px] bg-surface-container px-2 py-0.5 rounded">IP: {{ $log['ip'] }}</span>
                        <span class="text-on-surface-variant/40">{{ $log['time'] }}</span>
                        <a href="{{ route('finance.disbursement.slip', $log['nip']) }}"
                           class="text-primary font-bold hover:underline text-xs flex items-center gap-0.5">
                            Lihat Slip &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection