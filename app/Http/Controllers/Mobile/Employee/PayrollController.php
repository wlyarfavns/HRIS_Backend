<?php

namespace App\Http\Controllers\Mobile\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    /**
     * Riwayat slip gaji milik karyawan yang sedang login.
     * GET /mobile/payroll/history
     */
    public function history(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Profil karyawan tidak ditemukan untuk akun ini.');

        // PERBAIKAN: Hanya ambil payroll yang Batch-nya sudah memiliki tanggal pencairan (Completed/Disbursed)
        $payrolls = Payroll::where('employee_id', $employee->id)
            ->whereHas('payrollBatch', function ($query) {
                $query->whereNotNull('disbursed_at'); 
            })
            ->orderByDesc('period_start')
            ->get();

        $data = $payrolls->map(fn($p) => [
            'id' => $p->id,
            'period_label' => $p->period_start->translatedFormat('F Y'),
            'period_start' => $p->period_start->toDateString(),
            'period_end' => $p->period_end->toDateString(),
            'net_salary' => (float) $p->net_salary,
            'status' => 'Disbursed', // Kita paksa labelnya jadi disbursed karena sudah difilter
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Slip gaji paling baru (untuk kartu ringkasan di halaman utama Payroll).
     * GET /mobile/payroll/latest
     */
    public function latest(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Profil karyawan tidak ditemukan untuk akun ini.');

        // PERBAIKAN: Hanya ambil slip terbaru yang sudah benar-benar dicairkan
        $payroll = Payroll::with('payrollBatch')
            ->where('employee_id', $employee->id)
            ->whereHas('payrollBatch', function ($query) {
                $query->whereNotNull('disbursed_at');
            })
            ->orderByDesc('period_start')
            ->first();

        if (!$payroll) {
            return response()->json(['success' => true, 'data' => null]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payroll->id,
                'period_label' => $payroll->period_start->translatedFormat('F Y'),
                'net_salary' => (float) $payroll->net_salary,
                'status' => 'Disbursed',
                'paid_at' => optional($payroll->payrollBatch?->disbursed_at)?->translatedFormat('d F Y'),
                'is_disbursed' => true,
            ],
        ]);
    }

    /**
     * Detail satu slip gaji — hanya milik karyawan yang login
     * GET /mobile/payroll/{id}
     */
    public function show(Request $request, $id)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Profil karyawan tidak ditemukan untuk akun ini.');

        // PERBAIKAN: Pastikan ID yang diminta memang sudah dicairkan
        $payroll = Payroll::with(['employee.department', 'employee.position', 'details.salaryComponent'])
            ->where('employee_id', $employee->id)
            ->whereHas('payrollBatch', function ($query) {
                $query->whereNotNull('disbursed_at');
            })
            ->findOrFail($id);

        $earnings = $payroll->details->where('type', 'earning')
            ->map(fn($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])
            ->values();

        $deductions = $payroll->details->where('type', 'deduction')
            ->map(fn($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payroll->id,
                'period_label' => $payroll->period_start->translatedFormat('F Y'),
                'employee_name' => $payroll->employee->full_name,
                'employee_id' => $payroll->employee->employee_id,
                'position' => $payroll->employee->position->name ?? '-',
                'department' => $payroll->employee->department->name ?? '-',
                'basic_salary' => (float) $payroll->basic_salary,
                'earnings' => $earnings,
                'deductions' => $deductions,
                'total_earnings' => (float) ($payroll->basic_salary + $payroll->total_allowances),
                'total_deductions' => (float) $payroll->total_deductions,
                'net_salary' => (float) $payroll->net_salary,
                'status' => 'Disbursed',
            ],
        ]);
    }

    /**
     * Unduh PDF Slip Gaji
     */
    public function downloadSlip(Request $request, $id)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Profil karyawan tidak ditemukan untuk akun ini.');

        // PERBAIKAN: Pastikan hanya bisa download jika sudah dicairkan
        $payroll = Payroll::with(['employee.department', 'employee.position', 'details.salaryComponent', 'company'])
            ->where('employee_id', $employee->id)
            ->whereHas('payrollBatch', function ($query) {
                $query->whereNotNull('disbursed_at');
            })
            ->findOrFail($id);

        $earnings = $payroll->details->where('type', 'earning')
            ->map(fn($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])
            ->values();

        $deductions = $payroll->details->where('type', 'deduction')
            ->map(fn($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])
            ->values();

        $totalEarnings = (float) ($payroll->basic_salary + $payroll->total_allowances);
        $totalDeductions = (float) $payroll->total_deductions;

        $pdf = Pdf::loadView('mobile.payroll.slip-pdf', [
            'payroll' => $payroll,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'totalEarnings' => $totalEarnings,
            'totalDeductions' => $totalDeductions,
        ])->setPaper('a4', 'portrait');

        $filename = 'Slip-Gaji-' . $payroll->period_start->format('F-Y') . '.pdf';

        return $pdf->download($filename);
    }
}