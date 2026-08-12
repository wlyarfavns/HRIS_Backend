<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export rekap payroll periode berjalan ke XLSX.
 * Dipakai oleh PayrollController@exportXlsx.
 *
 * Butuh package: composer require maatwebsite/excel
 */
class PayrollRecapExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected int $companyId;
    protected string $startDate;
    protected string $endDate;

    public function __construct(int $companyId, string $startDate, string $endDate)
    {
        $this->companyId = $companyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Ambil data payroll periode ini beserta relasi yang dibutuhkan.
     */
    public function collection()
    {
        return Payroll::with(['employee.department'])
            ->where('company_id', $this->companyId)
            ->where('period_start', $this->startDate)
            ->where('period_end', $this->endDate)
            ->orderBy('employee_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Karyawan',
            'Departemen',
            'Gaji Pokok',
            'Total Tunjangan',
            'Total Potongan',
            'Gaji Bersih (Net)',
            'Status',
        ];
    }

    /**
     * @param Payroll $payroll
     */
    public function map($payroll): array
    {
        return [
            $payroll->employee->employee_id ?? '-',
            $payroll->employee->full_name ?? '-',
            $payroll->employee->department->name ?? '-',
            (float) $payroll->basic_salary,
            (float) $payroll->total_allowances,
            (float) $payroll->total_deductions,
            (float) $payroll->net_salary,
            ucfirst(str_replace('_', ' ', $payroll->status)),
        ];
    }

    /**
     * Bold header row.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}