{{-- admin/modul/finance.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modul Finance')
@section('page-title', 'Modul Finance')
@section('page-desc', 'Aktifkan/nonaktifkan fitur Finance untuk seluruh perusahaan. Operasional harian dikelola oleh role Finance.')

@php
    $features = [
        ['label' => 'Approval Payroll 2 Tahap (HR + Finance)', 'desc' => 'Nonaktifkan untuk approval payroll cukup 1 tahap (HR saja).', 'active' => true],
        ['label' => 'Verifikasi Reimbursement oleh Finance', 'desc' => 'Nonaktifkan agar approval HR sudah cukup untuk reimbursement.', 'active' => true],
        ['label' => 'Export Otomatis Bank Transfer setelah Payroll Approved', 'desc' => 'File transfer bank otomatis dibuat begitu Finance menyetujui payroll.', 'active' => false],
        ['label' => 'Slip Gaji Digital dengan Log Akses', 'desc' => 'Catat setiap kali karyawan membuka/mengunduh slip gaji di aplikasi mobile.', 'active' => true],
    ];
@endphp

@section('content')
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Fitur Modul Finance</h2>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">Perubahan di sini memengaruhi jumlah tahap approval yang tampil di halaman HR &amp; Finance terkait.</p>
        </div>
        <div class="divide-y divide-black/5">
            @foreach ($features as $f)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-on-surface">{{ $f['label'] }}</p>
                        <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ $f['desc'] }}</p>
                    </div>
                    <button type="button"
                        class="relative w-11 h-6 rounded-full transition {{ $f['active'] ? 'bg-primary' : 'bg-outline-variant' }}">
                        <span class="absolute top-0.5 {{ $f['active'] ? 'right-0.5' : 'left-0.5' }} w-5 h-5 bg-white rounded-full shadow transition"></span>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endsection
