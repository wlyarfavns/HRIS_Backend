@extends('layouts.finance')

@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')
@section('page-desc', 'Ringkasan klaim, payroll, dan pencairan dana hari ini.')

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-4 rounded-2xl p-6 text-white flex flex-col justify-between" style="background-color:#0B3D2E;">
            <div class="flex items-center justify-between gap-2">
                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Total Gaji Bersih</p>
                <span class="flex items-center gap-1 text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded whitespace-nowrap shrink-0">
                    <span class="material-symbols-outlined text-[14px]">payments</span> Agu 2026
                </span>
            </div>
            <div class="mt-6 mb-1">
                <p class="text-3xl font-extrabold font-mono-data whitespace-nowrap">Rp1,24 M</p>
                <p class="text-white/50 text-[11px] font-mono-data mt-1">Rp1.240.500.000</p>
            </div>
            <p class="text-white/60 text-xs">1.284 karyawan · menunggu approval finance</p>
        </div>

        <div class="col-span-8 card-flat rounded-2xl p-6 grid grid-cols-2 divide-x divide-black/5">
            <div class="pr-6">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Klaim Pending</p>
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-500/10 px-2 py-0.5 rounded whitespace-nowrap shrink-0">Verifikasi</span>
                </div>
                <p class="text-3xl font-bold font-mono-data text-primary mb-1">Rp14,25 Jt</p>
                <a href="{{ route('finance.reimbursement.index') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Verifikasi klaim →</a>
            </div>
            <div class="pl-6">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Payroll Menunggu</p>
                    <span class="text-[11px] font-bold text-on-surface-variant/60 bg-surface-container px-2 py-0.5 rounded whitespace-nowrap shrink-0">HR Approved</span>
                </div>
                <p class="text-3xl font-bold font-mono-data text-primary mb-1">1</p>
                <a href="{{ route('finance.payroll.index') }}" class="text-xs font-bold text-primary/70 hover:text-primary transition">Tinjau payroll →</a>
            </div>
        </div>
    </div>

    {{-- SHORTCUT MODUL --}}
    <div class="grid grid-cols-4 gap-5">
        <a href="{{ route('finance.reimbursement.index') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #FFD700;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Reimbursement</p>
                <span class="text-[11px] font-mono-data text-primary bg-primary/5 px-2 py-0.5 rounded whitespace-nowrap shrink-0">23 klaim</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Verifikasi klaim pengeluaran karyawan sebelum cair.</p>
        </a>

        <a href="{{ route('finance.payroll.index') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #0B3D2E;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Approval Payroll</p>
                <span class="text-[11px] font-mono-data text-primary bg-primary/5 px-2 py-0.5 rounded whitespace-nowrap shrink-0">1 pending</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Cek PPh21, BPJS, &amp; komponen gaji sebelum disetujui.</p>
        </a>

        <a href="{{ route('finance.export.index') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #B9C2BD;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Export Bank</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/60 bg-surface-container px-2 py-0.5 rounded whitespace-nowrap shrink-0">1.284 rek.</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Buat file transfer massal sesuai format bank tujuan.</p>
        </a>

        <a href="{{ route('finance.disbursement.index') }}" class="card-flat rounded-xl p-5 block" style="border-left: 3px solid #B9C2BD;">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="font-bold text-on-surface text-sm whitespace-nowrap">Disbursement</p>
                <span class="text-[11px] font-mono-data text-on-surface-variant/60 bg-surface-container px-2 py-0.5 rounded whitespace-nowrap shrink-0">Riwayat</span>
            </div>
            <p class="text-sm text-on-surface-variant/60">Riwayat pencairan dana &amp; distribusi slip gaji digital.</p>
        </a>
    </div>

    {{-- ALUR PROSES + AUDIT TRAIL --}}
    <div class="grid grid-cols-3 gap-5">

        {{-- ALUR PERSETUJUAN PENDING --}}
        <div class="col-span-2 card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-on-surface">Pengajuan Menunggu Finance</h2>
                <a href="{{ route('finance.reimbursement.index') }}" class="text-xs font-bold text-primary/60 uppercase tracking-widest hover:text-primary transition">Lihat Semua</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-on-surface-variant/40 border-b border-black/5">
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Karyawan</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Tipe</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Nominal</th>
                        <th class="pb-3 font-bold text-[11px] uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-black/5">
                        <td class="py-4 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=44" class="w-8 h-8 rounded-full" alt="">
                            <span class="font-bold text-on-surface">Siti Aminah</span>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-amber-500/10 text-amber-700 whitespace-nowrap">REIMBURSEMENT</span>
                        </td>
                        <td class="text-on-surface-variant/70 font-mono-data whitespace-nowrap">Rp350.000</td>
                        <td>
                            <div class="flex gap-2">
                                <button class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-primary/5 hover:border-primary/40 text-primary transition">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </button>
                                <button class="w-7 h-7 rounded-full border border-black/10 flex items-center justify-center hover:bg-error/5 hover:border-error/40 text-error transition">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-4 flex items-center gap-3">
                            <img src="https://i.pravatar.cc/32?img=68" class="w-8 h-8 rounded-full" alt="">
                            <span class="font-bold text-on-surface">Payroll Agustus 2026</span>
                        </td>
                        <td>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary whitespace-nowrap">PAYROLL RUN</span>
                        </td>
                        <td class="text-on-surface-variant/70 font-mono-data whitespace-nowrap">Rp1,24 M</td>
                        <td>
                            <a href="{{ route('finance.payroll.index') }}" class="text-xs font-bold text-primary hover:underline">Tinjau →</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- AUDIT TRAIL --}}
        <div class="card-flat rounded-2xl p-6">
            <h2 class="text-base font-bold text-on-surface mb-5">Audit Trail Terbaru</h2>
            <div class="space-y-5">
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></span>
                    <div>
                        <p class="text-xs font-mono-data text-on-surface-variant/40">10.01</p>
                        <p class="text-sm font-bold text-on-surface leading-snug">Reimbursement Siti Aminah diverifikasi finance.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-brand-gold mt-1.5 shrink-0"></span>
                    <div>
                        <p class="text-xs font-mono-data text-on-surface-variant/40">09.40</p>
                        <p class="text-sm font-bold text-on-surface leading-snug">Payroll Agustus 2026 diterima dari HR, menunggu approval finance.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></span>
                    <div>
                        <p class="text-xs font-mono-data text-on-surface-variant/40">Kmrn</p>
                        <p class="text-sm font-bold text-on-surface leading-snug">Export CSV BCA payroll Juli 2026 berhasil diunduh.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></span>
                    <div>
                        <p class="text-xs font-mono-data text-on-surface-variant/40">Kmrn</p>
                        <p class="text-sm font-bold text-on-surface leading-snug">1.276 slip gaji digital berhasil didistribusikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection