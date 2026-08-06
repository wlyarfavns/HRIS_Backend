@extends('layouts.finance')

@section('title', 'Export Bank Transfer')
@section('page-title', 'Export Bank Transfer')
@section('page-desc', 'Buat file transfer massal sesuai format bank untuk proses disbursement.')

@php
    $banks = [
        ['name' => 'BCA', 'format' => 'CSV', 'accounts' => 812, 'total' => 792400000],
        ['name' => 'Mandiri', 'format' => 'CSV', 'accounts' => 341, 'total' => 328600000],
        ['name' => 'BNI', 'format' => 'CSV', 'accounts' => 131, 'total' => 119500000],
    ];
@endphp

@section('content')

    <div class="card-flat rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-on-surface">Buat File Export Baru</h2>
                <p class="text-sm text-on-surface-variant/60 mt-0.5">Pilih periode payroll yang telah disetujui finance &amp; format bank tujuan.</p>
            </div>
            <span class="text-[11px] font-bold px-3 py-1.5 rounded bg-primary/10 text-primary">Payroll Approved</span>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="border border-black/10 rounded-xl p-4">
                <label class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2 block">Periode</label>
                <select class="w-full border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option>Agustus 2026</option>
                    <option>Juli 2026</option>
                </select>
            </div>
            <div class="border border-black/10 rounded-xl p-4">
                <label class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2 block">Format Bank</label>
                <select class="w-full border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option>BCA (CSV)</option>
                    <option>Mandiri (CSV)</option>
                    <option>BNI (CSV)</option>
                </select>
            </div>
            <div class="border border-black/10 rounded-xl p-4 flex flex-col justify-between">
                <label class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1 block">Total Rekening</label>
                <p class="text-lg font-bold font-mono-data text-on-surface">1.284 rekening</p>
            </div>
        </div>

        <button class="mt-5 bg-primary text-white font-bold px-5 py-3 rounded-lg text-sm hover:brightness-110 transition flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            Export CSV
        </button>
    </div>

    {{-- RINGKASAN PER BANK --}}
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Ringkasan per Bank Tujuan</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Bank</th>
                    <th class="px-6 py-3">Format File</th>
                    <th class="px-6 py-3 text-right">Jumlah Rekening</th>
                    <th class="px-6 py-3 text-right">Total Nominal</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($banks as $b)
                    <tr class="hover:bg-surface-container/60">
                        <td class="px-6 py-3.5 font-bold text-on-surface text-xs">{{ $b['name'] }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">{{ $b['format'] }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-right font-mono-data text-on-surface-variant/70">{{ number_format($b['accounts'], 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-right font-mono-data font-bold text-on-surface">Rp{{ number_format($b['total'], 0, ',', '.') }}</td>
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

@endsection