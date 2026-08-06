@extends('layouts.finance')

@section('title', 'Approval Payroll')
@section('page-title', 'Approval Payroll')
@section('page-desc', 'Tinjau kalkulasi PPh21, BPJS, & komponen gaji sebelum dicairkan ke bank.')

@section('page-action')
    <button class="bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 hover:brightness-110 transition">
        <span class="material-symbols-outlined text-[16px]">check_circle</span>
        Setujui Payroll
    </button>
@endsection

@php
    $steps = [
        ['label' => 'Cut-off Rekap Absensi', 'done' => true],
        ['label' => 'Engine Payroll (PPh21 & BPJS)', 'done' => true],
        ['label' => 'Approval HR', 'done' => true],
        ['label' => 'Approval Finance', 'done' => false],
        ['label' => 'Export Bank Transfer', 'done' => false],
    ];

    $components = [
        ['name' => 'Budi Santoso', 'avatar' => 22, 'basic' => 6500000, 'allowance' => 850000, 'overtime' => 375723, 'bpjs' => 260000, 'pph21' => 180000, 'net' => 7285723],
        ['name' => 'Siti Aminah', 'avatar' => 44, 'basic' => 5800000, 'allowance' => 700000, 'overtime' => 0, 'bpjs' => 232000, 'pph21' => 95000, 'net' => 5973000],
        ['name' => 'Eko Prasetyo', 'avatar' => 51, 'basic' => 7200000, 'allowance' => 900000, 'overtime' => 512000, 'bpjs' => 288000, 'pph21' => 265000, 'net' => 8059000],
    ];
@endphp

@section('content')

    <div class="grid grid-cols-4 gap-5">
        <div class="col-span-2 card-flat rounded-2xl p-6" style="background-color:#0B3D2E;">
            <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Periode Berjalan</p>
            <p class="text-2xl font-extrabold text-white mt-2">1 - 31 Agustus 2026</p>
            <p class="text-white/70 text-sm mt-1">Status: <span class="font-bold text-brand-gold">Menunggu Approval Finance</span></p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">1.284</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Karyawan Diproses</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">Rp1,24 M</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Gaji Bersih</p>
        </div>
    </div>

    {{-- STEPPER --}}
    <div class="card-flat rounded-2xl p-6">
        <h2 class="text-base font-bold text-on-surface mb-6">Alur Proses Payroll</h2>
        <div class="flex items-center">
            @foreach ($steps as $i => $s)
                <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center gap-2 shrink-0">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $s['done'] ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant/40 border border-black/10' }}">
                            @if ($s['done'])
                                <span class="material-symbols-outlined text-[18px]">check</span>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p class="text-[11px] font-bold text-center w-24 {{ $s['done'] ? 'text-primary' : 'text-on-surface-variant/40' }}">{{ $s['label'] }}</p>
                    </div>
                    @if (!$loop->last)
                        <div class="flex-1 h-0.5 mx-1 {{ $s['done'] ? 'bg-primary' : 'bg-black/10' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- RINCIAN KALKULASI --}}
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface">Rincian Kalkulasi Payroll</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Verifikasi PPh21 &amp; BPJS sesuai aturan sebelum disetujui.</p>
            </div>
            <button class="text-xs font-bold text-primary/70 hover:text-primary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">download</span> Unduh Rekap (XLSX)
            </button>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3 text-right">Gaji Pokok</th>
                    <th class="px-6 py-3 text-right">Tunjangan</th>
                    <th class="px-6 py-3 text-right">Lembur</th>
                    <th class="px-6 py-3 text-right">BPJS</th>
                    <th class="px-6 py-3 text-right">PPh21 (TER)</th>
                    <th class="px-6 py-3 text-right">Gaji Bersih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($components as $c)
                    <tr class="hover:bg-surface-container/60">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?img={{ $c['avatar'] }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $c['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($c['basic'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($c['allowance'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($c['overtime'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-error">-{{ number_format($c['bpjs'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-error">-{{ number_format($c['pph21'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data font-bold text-primary">Rp{{ number_format($c['net'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- AKSI PERSETUJUAN --}}
    <div class="card-flat rounded-2xl p-6 flex items-center justify-between">
        <div>
            <p class="font-bold text-on-surface text-sm">Setujui payroll periode ini?</p>
            <p class="text-xs text-on-surface-variant/60 mt-0.5">Setelah disetujui, data akan diteruskan ke tahap Export Bank Transfer.</p>
        </div>
        <div class="flex gap-3">
            <button class="border border-black/10 text-on-surface-variant/80 font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-surface-container transition">
                Tolak &amp; Kembalikan ke HR
            </button>
            <button class="bg-primary text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Setujui Payroll
            </button>
        </div>
    </div>

@endsection