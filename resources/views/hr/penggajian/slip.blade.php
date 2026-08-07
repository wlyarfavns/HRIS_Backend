<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payroll->employee->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 flex justify-center">

    <div class="bg-white p-10 max-w-2xl w-full shadow-lg border border-gray-200">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">SLIP GAJI</h1>
                <p class="text-sm text-gray-500 mt-1">Periode: {{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-800">HRIS System</h2>
                <p class="text-sm text-gray-500">Divisi HR & Finance</p>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="grid grid-cols-2 gap-4 mb-8 text-sm">
            <div>
                <p class="text-gray-500">Nama Karyawan:</p>
                <p class="font-bold text-gray-800 text-base">{{ $payroll->employee->full_name }}</p>
            </div>
            <div>
                <p class="text-gray-500">Email:</p>
                <p class="font-bold text-gray-800">{{ $payroll->employee->email }}</p>
            </div>
            <div>
                <p class="text-gray-500">Status:</p>
                <p class="font-bold text-green-600 uppercase">{{ $payroll->status }}</p>
            </div>
        </div>

        <!-- Salary Details -->
        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="bg-gray-50 text-gray-700">
                    <th class="p-3 text-left font-bold border-b border-gray-200">Keterangan</th>
                    <th class="p-3 text-right font-bold border-b border-gray-200">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <!-- Basic Salary -->
                <tr>
                    <td class="p-3 text-gray-800 border-b border-gray-100">Gaji Pokok</td>
                    <td class="p-3 text-right text-gray-800 font-mono border-b border-gray-100">Rp{{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
                </tr>
                
                <!-- Earnings / Deductions from details -->
                @foreach($payroll->details as $detail)
                <tr>
                    <td class="p-3 text-gray-800 border-b border-gray-100">
                        {{ $detail->salaryComponent->name }}
                        @if($detail->salaryComponent->type == 'deduction')
                            <span class="text-xs text-red-500 ml-1">(Potongan)</span>
                        @else
                            <span class="text-xs text-green-500 ml-1">(Tunjangan)</span>
                        @endif
                    </td>
                    <td class="p-3 text-right font-mono border-b border-gray-100 {{ $detail->salaryComponent->type == 'deduction' ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $detail->salaryComponent->type == 'deduction' ? '-' : '' }}Rp{{ number_format($detail->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-green-50">
                    <td class="p-4 text-left font-extrabold text-green-900 text-base">TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                    <td class="p-4 text-right font-extrabold text-green-900 text-lg font-mono">Rp{{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="mt-12 pt-6 border-t border-gray-200 text-xs text-gray-400 text-center">
            Dokumen ini digenerate secara otomatis oleh sistem HRIS dan sah tanpa tanda tangan basah.<br>
            Dicetak pada: {{ now()->format('d M Y H:i:s') }}
        </div>

    </div>

    <!-- Floating Print Button (Hidden on Print) -->
    <div class="no-print fixed bottom-10 right-10 flex gap-3">
        <button onclick="window.close()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-full shadow-lg transition">
            Tutup
        </button>
        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg flex items-center gap-2 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak / Simpan PDF
        </button>
    </div>

</body>
</html>
=======
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
>>>>>>> 333bd8917d4b1b01b27ac40591b091529131919e
