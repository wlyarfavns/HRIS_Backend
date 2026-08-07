@extends('layouts.hr')

@section('title', 'Penggajian')
@section('page-title', 'Penggajian')
@section('page-desc', 'Proses payroll periode berjalan: rekap absensi → kalkulasi → approval → disbursement.')

@section('page-action')
    <form action="{{ route('hr.payroll.run') }}" method="POST">
        @csrf
        <button type="submit" class="bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 hover:brightness-110 transition cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">bolt</span>
            Jalankan Payroll
        </button>
    </form>
@endsection

@php
    $steps = [
        ['label' => 'Cut-off Rekap Absensi', 'done' => true],
        ['label' => 'Engine Payroll', 'done' => true],
        ['label' => 'Approval HR', 'done' => true],
        ['label' => 'Approval Finance', 'done' => false],
        ['label' => 'Export Bank Transfer', 'done' => false],
    ];
@endphp

@section('content')
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-4 gap-5">
        <div class="col-span-2 card-flat rounded-2xl p-6" style="background-color:#0B3D2E;">
            <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Periode Berjalan</p>
            <p class="text-2xl font-extrabold text-white mt-2">1 - 31 Juli 2026</p>
            <p class="text-white/70 text-sm mt-1">Status: <span class="font-bold text-brand-gold">Menunggu Approval Finance</span></p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ $totalKaryawan }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Karyawan Diproses</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ $totalGajiBersihFormatted }}</p>
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

    {{-- PREVIEW SLIP --}}
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <h2 class="text-base font-bold text-on-surface">Preview Komponen Gaji</h2>
            <button class="text-xs font-bold text-primary/70 hover:text-primary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">download</span> Export Bank Transfer (CSV)
            </button>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Karyawan</th>
                    <th class="px-6 py-3 text-right">Gaji Pokok</th>
                    <th class="px-6 py-3 text-right">Tunjangan</th>
                    <th class="px-6 py-3 text-right">Lembur</th>
                    <th class="px-6 py-3 text-right">Potongan</th>
                    <th class="px-6 py-3 text-right">Gaji Bersih</th>
                    <th class="px-6 py-3 text-center">Slip</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($payrolls as $p)
                    <tr class="hover:bg-surface-container/60">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="https://i.pravatar.cc/28?u={{ $p->employee->email }}" class="w-7 h-7 rounded-full" alt="">
                                <span class="font-bold text-on-surface text-xs">{{ $p->employee->full_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($p->basic_salary, 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($p->total_allowances, 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">0</td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-error">-{{ number_format($p->total_deductions, 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data font-bold text-primary">Rp{{ number_format($p->net_salary, 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <a href="{{ route('hr.payroll.slip', $p->id) }}" target="_blank" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/5 transition inline-block">
                                <span class="material-symbols-outlined text-[18px]">description</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-on-surface-variant/50">
                            Belum ada data payroll untuk periode ini. Silakan klik "Jalankan Payroll".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection