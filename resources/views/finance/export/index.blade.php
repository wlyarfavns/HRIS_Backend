@extends('layouts.finance')

@section('title', 'Export Bank Transfer')
@section('page-title', 'Export Bank Transfer')
@section('page-desc', 'Buat file transfer massal sesuai format standar bank (BCA, Mandiri, BNI) untuk proses disbursement.')

@php
    $banks = [
        ['code' => 'BCA', 'name' => 'Bank Central Asia (BCA)', 'format' => 'CSV / AutoPay', 'accounts' => 812, 'total' => 792400000, 'filename' => 'BCA_PAYROLL_AUG2026.csv', 'status' => 'Siap Download'],
        ['code' => 'MANDIRI', 'name' => 'Bank Mandiri (MCM)', 'format' => 'CSV / MCM Format', 'accounts' => 341, 'total' => 328600000, 'filename' => 'MANDIRI_PAYROLL_AUG2026.csv', 'status' => 'Siap Download'],
        ['code' => 'BNI', 'name' => 'Bank Negara Indonesia (BNI)', 'format' => 'CSV / Direct Debit', 'accounts' => 131, 'total' => 119500000, 'filename' => 'BNI_PAYROLL_AUG2026.csv', 'status' => 'Siap Download'],
    ];
@endphp

@section('content')
<div x-data="{
    selectedPeriod: 'Agustus 2026',
    selectedBank: 'BCA',
    generatedCount: 1284,
    showSuccessAlert: false
}">

    {{-- TOP SUMMARY ROW --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Total Rekening Siap Transfer</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">1.284 Rek</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">100% tervalidasi bank</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Total Nominal Disbursement</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">Rp1,24 M</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Rp1.240.500.000</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Format Standar Didukung</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">3 Bank</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">BCA, Mandiri, BNI</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Status Persetujuan</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">Approved</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Payroll disetujui Finance</p>
        </div>
    </div>

    {{-- GENERATOR FORM CARD --}}
    <div class="card-flat rounded-2xl p-6 mt-6">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-4">
            <div>
                <h2 class="text-base font-bold text-on-surface">Generator File Export Transfer Massal</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Pilih periode payroll yang telah disetujui finance &amp; format bank tujuan transfer</p>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded bg-primary/10 text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">verified</span>
                Payroll Siap Transfer
            </span>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="border border-black/10 rounded-xl p-4 bg-surface-container/30">
                <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide mb-1.5 block">1. Periode Payroll</label>
                <select x-model="selectedPeriod" class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 bg-white">
                    <option value="Agustus 2026">Agustus 2026 (Aktif)</option>
                    <option value="Juli 2026">Juli 2026 (Arsip)</option>
                    <option value="Juni 2026">Juni 2026 (Arsip)</option>
                </select>
            </div>
            <div class="border border-black/10 rounded-xl p-4 bg-surface-container/30">
                <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide mb-1.5 block">2. Format Bank Tujuan</label>
                <select x-model="selectedBank" class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 bg-white">
                    <option value="BCA">BCA (AutoPay CSV)</option>
                    <option value="MANDIRI">Mandiri (MCM Format CSV)</option>
                    <option value="BNI">BNI (Direct Debit CSV)</option>
                    <option value="ALL">Download Semua Format (ZIP)</option>
                </select>
            </div>
            <div class="border border-black/10 rounded-xl p-4 bg-surface-container/30 flex flex-col justify-between">
                <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">3. Verifikasi Checksum</label>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold font-mono-data text-primary">SHA-256: 8f4a...29b1</p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-primary/10 text-primary">Match</span>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between pt-4 border-t border-black/5 flex-wrap gap-3">
            <p class="text-xs text-on-surface-variant/60">
                File CSV yang dihasilkan dapat langsung diunggah ke portal internet banking korporasi (BCA KlikBisnis, Mandiri MCM, BNI Direct).
            </p>
            <button type="button" @click="showSuccessAlert = true; setTimeout(() => showSuccessAlert = false, 3500)"
                    class="bg-primary hover:brightness-110 text-white font-bold px-5 py-2.5 rounded-lg text-xs shadow-sm transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[17px]">download</span>
                Generate &amp; Download File CSV
            </button>
        </div>

        <div x-show="showSuccessAlert" x-cloak x-transition
             class="mt-4 p-3.5 rounded-xl bg-primary/10 border border-primary/30 text-primary text-xs font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            File export bank transfer berhasil digenerate dan diunduh ke komputer Anda.
        </div>
    </div>

    {{-- RINGKASAN PER BANK CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface">Rincian File per Bank Mitra Perusahaan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Pemetaan otomatis nomor rekening karyawan berdasarkan bank penerima</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Nama Bank Mitra</th>
                        <th class="px-4 py-3.5">Format Standar</th>
                        <th class="px-4 py-3.5 text-right">Jumlah Rekening</th>
                        <th class="px-4 py-3.5 text-right">Total Nominal Transfer</th>
                        <th class="px-4 py-3.5">Nama File</th>
                        <th class="px-6 py-3.5 text-center">Unduh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($banks as $b)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5 font-bold text-on-surface text-xs">
                                {{ $b['name'] }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-mono-data font-bold px-2.5 py-1 rounded bg-primary/10 text-primary">{{ $b['format'] }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80">{{ number_format($b['accounts'], 0, ',', '.') }} Rek</td>
                            <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-xs text-primary">Rp{{ number_format($b['total'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface-variant/60">{{ $b['filename'] }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" title="Unduh File {{ $b['code'] }}"
                                        class="p-2 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection