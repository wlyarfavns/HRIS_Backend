@extends('layouts.hr')

@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')
@section('page-desc', 'Slip gaji digital karyawan untuk periode berjalan.')

@php
    $earnings = collect([
        ['label' => 'Gaji Pokok', 'amount' => (float) $payroll->basic_salary],
    ])->concat(
        $payroll->details
            ->where('type', 'earning')
            ->map(fn ($d) => [
                'label'  => $d->salaryComponent->name ?? '-',
                'amount' => (float) $d->amount,
            ])
    )->values()->all();

    $deductions = $payroll->details
        ->where('type', 'deduction')
        ->map(fn ($d) => [
            'label'  => $d->salaryComponent->name ?? '-',
            'amount' => (float) $d->amount,
        ])
        ->values()->all();

    $statusLabel = match ($payroll->status) {
        \App\Models\Payroll::STATUS_DRAFT            => 'Draft (Belum Disetujui)',
        \App\Models\Payroll::STATUS_APPROVED_HR       => 'Disetujui HR — Menunggu Finance',
        \App\Models\Payroll::STATUS_APPROVED_FINANCE  => 'Disetujui Finance',
        default                                       => ucfirst($payroll->status),
    };

    $slip = [
        'nip'        => $payroll->employee->employee_id ?? '-',
        'name'       => $payroll->employee->full_name ?? '-',
        'position'   => $payroll->employee->position->name ?? '-',
        'department' => $payroll->employee->department?->name ?? '-',
        'period'     => $payroll->period_start->translatedFormat('F Y'),

        'company_name'    => $payroll->company->name ?? '-',
        'company_address' => $payroll->company->address ?? '-',

        'earnings'   => $earnings,
        'deductions' => $deductions,

        'status'      => $statusLabel,
        'status_time' => optional($payroll->updated_at)->translatedFormat('d M Y, H.i') ?? '-',
    ];
@endphp

@section('content')
    @include('shared._slip-gaji-content', ['slip' => $slip, 'backRoute' => 'hr.payroll.index'])
@endsection
