<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 30px 35px;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        .header {
            background-color: #0B3D2E;
            color: #ffffff;
            padding: 18px 22px;
            border-radius: 4px;
        }

        .header table {
            width: 100%;
        }

        .header .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #cfe3da;
            letter-spacing: 1px;
        }

        .header .period {
            font-size: 18px;
            font-weight: bold;
            margin-top: 4px;
        }

        .header .company {
            font-size: 12px;
            font-weight: bold;
            text-align: right;
        }

        .header .address {
            font-size: 9px;
            color: #cfe3da;
            text-align: right;
            margin-top: 2px;
        }

        .identity {
            margin-top: 18px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .identity table {
            width: 100%;
        }

        .identity td {
            padding: 3px 0;
            font-size: 10.5px;
        }

        .identity .name {
            font-size: 13px;
            font-weight: bold;
        }

        .identity .label {
            color: #888888;
            font-size: 9px;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        .earning-title {
            color: #0B3D2E;
        }

        .deduction-title {
            color: #B3261E;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items td {
            padding: 4px 0;
            font-size: 10.5px;
        }

        table.items td.amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        table.items tr.total td {
            border-top: 1px solid #e5e5e5;
            padding-top: 6px;
            font-weight: bold;
        }

        .columns {
            width: 100%;
            margin-top: 10px;
        }

        .columns td {
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .net-box {
            margin-top: 22px;
            background-color: #f4f6f5;
            padding: 14px 18px;
            border-radius: 4px;
        }

        .net-box table {
            width: 100%;
        }

        .net-box .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #888888;
        }

        .net-box .sublabel {
            font-size: 8.5px;
            color: #aaaaaa;
            margin-top: 2px;
        }

        .net-box .amount {
            font-size: 18px;
            font-weight: bold;
            color: #0B3D2E;
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-size: 8.5px;
            color: #aaaaaa;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="label">Slip Gaji Digital</div>
                    <div class="period">{{ $payroll->period_start->translatedFormat('F Y') }}</div>
                </td>
                <td>
                    <div class="company">{{ $payroll->company->name ?? '-' }}</div>
                    <div class="address">{{ $payroll->company->address ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="identity">
        <table>
            <tr>
                <td class="name">{{ $payroll->employee->full_name ?? '-' }}</td>
                <td style="text-align:right;">{{ $payroll->employee->employee_id ?? '-' }}</td>
            </tr>
            <tr>
                <td>
                    <span class="label">Jabatan</span><br>
                    {{ $payroll->employee->position->name ?? '-' }}
                </td>
                <td style="text-align:right;">
                    <span class="label">Departemen</span><br>
                    {{ $payroll->employee->department->name ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    <table class="columns">
        <tr>
            <td>
                <div class="section-title earning-title">Pendapatan</div>
                <table class="items">
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="amount">{{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
                    </tr>
                    @foreach ($earnings as $e)
                        <tr>
                            <td>{{ $e['label'] }}</td>
                            <td class="amount">{{ number_format($e['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total">
                        <td>Total Pendapatan</td>
                        <td class="amount">{{ number_format($totalEarnings, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <div class="section-title deduction-title">Potongan</div>
                <table class="items">
                    @forelse ($deductions as $d)
                        <tr>
                            <td>{{ $d['label'] }}</td>
                            <td class="amount">-{{ number_format($d['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Tidak ada potongan</td>
                        </tr>
                    @endforelse
                    <tr class="total">
                        <td>Total Potongan</td>
                        <td class="amount">-{{ number_format($totalDeductions, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="net-box">
        <table>
            <tr>
                <td>
                    <div class="label">Gaji Bersih Diterima (Take Home Pay)</div>
                    <div class="sublabel">Ditransfer ke rekening terdaftar karyawan</div>
                </td>
                <td class="amount">Rp{{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem HRIS dan sah tanpa tanda tangan basah.<br>
        Diunduh pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

</body>

</html>
