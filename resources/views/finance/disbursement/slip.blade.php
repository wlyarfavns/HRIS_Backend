@extends('layouts.finance')

@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')
@section('page-desc', 'Preview slip gaji digital yang telah didistribusikan ke karyawan.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $slip = [
        'nip' => $id ?? 'EMP-00812',
        'name' => 'Jim Halpert',
        'avatar' => 12,
        'position' => 'Sales Executive',
        'department' => 'Sales',
        'period' => 'Juli 2026',
        'earnings' => [
            ['label' => 'Gaji Pokok', 'amount' => 6500000],
            ['label' => 'Tunj. Jabatan', 'amount' => 850000],
            ['label' => 'Tunj. Makan & Transport', 'amount' => 735000],
            ['label' => 'Upah Lembur (SPL)', 'amount' => 210000],
        ],
        'deductions' => [
            ['label' => 'BPJS Kesehatan', 'amount' => 65000],
            ['label' => 'BPJS Ketenagakerjaan (JHT)', 'amount' => 130000],
            ['label' => 'PPh 21 (TER)', 'amount' => 118000],
        ],
        'status' => 'Dana Sudah Dicairkan',
        'status_time' => '1 Agu 2026, 07.45',
        'access_log' => [
            ['action' => 'Karyawan membuka slip gaji', 'time' => '2 Agu, 09.12'],
            ['action' => 'Karyawan mengunduh PDF', 'time' => '2 Agu, 09.12'],
        ],
    ];
@endphp

@section('content')
    @include('shared._slip-gaji-content', ['slip' => $slip, 'backRoute' => 'finance.disbursement.index'])
@endsection
