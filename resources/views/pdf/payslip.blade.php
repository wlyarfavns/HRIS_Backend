<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $payroll->employee->full_name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; margin-top: 10px; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details-table th { background-color: #f2f2f2; }
        .amount { text-align: right !important; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 40px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; width: 200px; }
        .signature-line { border-bottom: 1px solid #333; height: 60px; margin-bottom: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">{{ $payroll->company->name ?? 'Perusahaan' }}</div>
        <div class="title">Slip Gaji Karyawan</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>ID Karyawan</strong></td>
            <td width="30%">: {{ $payroll->employee->employee_id }}</td>
            <td width="20%"><strong>Periode</strong></td>
            <td width="30%">: {{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong>Nama</strong></td>
            <td>: {{ $payroll->employee->full_name }}</td>
            <td><strong>Status</strong></td>
            <td>: {{ ucfirst(str_replace('_', ' ', $payroll->status)) }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="amount">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="amount">{{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
            </tr>
            @if($payroll->total_allowances > 0)
            <tr>
                <td>Tunjangan</td>
                <td class="amount">{{ number_format($payroll->total_allowances, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payroll->total_deductions > 0)
            <tr>
                <td>Potongan</td>
                <td class="amount">- {{ number_format($payroll->total_deductions, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Gaji Bersih (Net Salary)</td>
                <td class="amount">{{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div>HR / Finance Manager</div>
            <div class="signature-line"></div>
            <div>Tanda Tangan</div>
        </div>
    </div>

</body>
</html>
