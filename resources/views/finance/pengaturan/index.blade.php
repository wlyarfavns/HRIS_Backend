@extends('layouts.finance')

@section('title', 'Pengaturan Finance')
@section('page-title', 'Pengaturan')
@section('page-desc', 'Konfigurasi parameter keuangan, format bank, dan pajak untuk proses payroll.')

@php
    $bankFormats = [
        ['bank' => 'BCA', 'format' => 'CSV', 'columns' => 'no_rekening, nama_penerima, nominal, berita'],
        ['bank' => 'Mandiri', 'format' => 'CSV', 'columns' => 'account_no, name, amount, reference'],
        ['bank' => 'BNI', 'format' => 'CSV', 'columns' => 'rekening, nama, jumlah, keterangan'],
    ];
@endphp

@section('content')

    <div class="space-y-6">

        {{-- REKENING PERUSAHAAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                    </span>
                    <h2 class="text-base font-bold text-on-surface">Rekening &amp; Profil Bank Perusahaan</h2>
                </div>
                <a href="{{ route('admin.company.index') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Dikelola di Profil Perusahaan →</a>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1.5">Nama Bank</p>
                    <p class="text-sm font-bold text-on-surface">Bank Central Asia (BCA)</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1.5">No. Rekening</p>
                    <p class="text-sm font-bold text-on-surface font-mono-data">206-0891-234</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1.5">Atas Nama</p>
                    <p class="text-sm font-bold text-on-surface">PT Talenta Digital Nusantara</p>
                </div>
            </div>
        </div>

        {{-- FORMAT BANK DIDUKUNG --}}
        <div class="card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Format Bank untuk Export</h2>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Template kolom CSV per bank tujuan transfer.</p>
                </div>
                <button type="button" class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Format
                </button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Bank</th>
                        <th class="px-6 py-3">Format File</th>
                        <th class="px-6 py-3">Template Kolom</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($bankFormats as $b)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5 font-bold text-on-surface">{{ $b['bank'] }}</td>
                            <td class="px-6 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">{{ $b['format'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-mono-data text-xs text-on-surface-variant/60">{{ $b['columns'] }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PARAMETER PAJAK & BPJS --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                </span>
                <h2 class="text-base font-bold text-on-surface">Parameter Pajak &amp; BPJS</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Skema PPh21</label>
                    <input type="text" value="Tarif Efektif Rata-rata (TER)" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/50 cursor-not-allowed">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">BPJS Kesehatan (Perusahaan)</label>
                    <div class="relative mt-1.5">
                        <input type="number" step="0.1" value="4.0"
                               class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                      hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant/40">%</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">BPJS Ketenagakerjaan (Perusahaan)</label>
                    <div class="relative mt-1.5">
                        <input type="number" step="0.1" value="3.7"
                               class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                      hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant/40">%</span>
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-on-surface-variant/40 mt-4">Tabel tarif PPh21 TER dan batas PTKP mengikuti regulasi terbaru — dikonfigurasi oleh Payroll Engine di sisi backend.</p>
        </div>

        {{-- APPROVAL 2 TAHAP (READ-ONLY REFERENCE) --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-on-surface">Approval Payroll 2 Tahap (HR + Finance)</p>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Dikendalikan Super Admin di Modul Finance — ditampilkan di sini sebagai referensi (read-only).</p>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">Aktif</span>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="reset" class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant/70 hover:bg-primary/5 hover:text-primary transition">
                Batal
            </button>
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Pengaturan
            </button>
        </div>
    </div>

@endsection
