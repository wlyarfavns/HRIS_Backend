@extends('layouts.hr')

@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')
@section('page-desc', 'Preview slip gaji digital karyawan untuk periode berjalan.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $slip = [
        'nip' => $id ?? 'EMP-00231',
        'name' => 'Budi Santoso',
        'avatar' => 22,
        'position' => 'Sales Executive',
        'department' => 'Sales',
        'period' => 'Agustus 2026',
        'earnings' => [
            ['label' => 'Gaji Pokok', 'amount' => 6500000],
            ['label' => 'Tunj. Jabatan', 'amount' => 850000],
            ['label' => 'Tunj. Makan & Transport', 'amount' => 770000],
            ['label' => 'Upah Lembur (SPL)', 'amount' => 375723],
        ],
        'deductions' => [
            ['label' => 'BPJS Kesehatan', 'amount' => 65000],
            ['label' => 'BPJS Ketenagakerjaan (JHT)', 'amount' => 130000],
            ['label' => 'PPh 21 (TER)', 'amount' => 130000],
        ],
        'status' => 'Terdistribusi ke Karyawan',
        'status_time' => '1 Sep 2026, 08.00',
        'access_log' => [
            ['action' => 'Karyawan membuka slip gaji', 'time' => '2 Sep, 09.12'],
            ['action' => 'Karyawan mengunduh PDF', 'time' => '2 Sep, 09.13'],
        ],
    ];
@endphp

@section('content')
    @include('shared._slip-gaji-content', ['slip' => $slip, 'backRoute' => 'hr.payroll.index'])
@endsection
