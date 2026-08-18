<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\NumberFormat;

class PayrollExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithColumnFormatting,
    WithTitle
{
    protected $payrolls;

    public function __construct($payrolls)
    {
        $this->payrolls = $payrolls;
    }

    public function collection()
    {
        return $this->payrolls;
    }

    public function title(): string
    {
        return 'Rekap Payroll';
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
            'Gaji Bersih',
        ];
    }

    public function map($p): array
    {
        return [
            $p->employee->employee_id ?? '-',
            $p->employee->full_name ?? '-',
            $p->employee->department->name ?? '-',
            $p->basic_salary,
            $p->total_allowances,
            $p->total_deductions,
            $p->net_salary,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 28,
            'C' => 22,
            'D' => 16,
            'E' => 16,
            'F' => 16,
            'G' => 16,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0',
            'E' => '#,##0',
            'F' => '#,##0',
            'G' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->payrolls->count() + 1;


        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1F6F4A');
        $sheet->getStyle('A1:G1')->getFont()->getColor()
            ->setRGB('FFFFFF');
        $sheet->getStyle('A1:G1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $sheet->getStyle("A1:G{$lastRow}")->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


        $sheet->freezePane('A2');

        return [];
    }
}