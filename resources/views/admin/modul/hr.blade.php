
@extends('layouts.admin')

@section('title', 'Modul HR')
@section('page-title', 'Modul HR')
@section('page-desc', 'Aktifkan/nonaktifkan fitur HR untuk seluruh perusahaan. Operasional harian dikelola oleh role HR Admin.')

@php
    $features = [
        ['label' => 'Presensi GPS & Selfie', 'desc' => 'Clock-in/out via geofencing.', 'active' => true],
        ['label' => 'Approval Cuti 2 Tahap (SPV + HR)', 'desc' => 'Nonaktifkan untuk approval 1 tahap (SPV saja).', 'active' => true],
        ['label' => 'Kunci SPL oleh HR', 'desc' => 'SPL wajib dikunci HR sebelum masuk payroll.', 'active' => true],
        ['label' => 'Integrasi Mesin Fingerprint', 'desc' => 'Sinkronisasi data absensi dari mesin fisik.', 'active' => false],
    ];
@endphp

@section('content')
    <div class="card-flat rounded-md overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-medium text-on-surface">Fitur Modul HR</h2>
        </div>
        <div class="divide-y divide-black/5">
            @foreach ($features as $f)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-on-surface">{{ $f['label'] }}</p>
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
