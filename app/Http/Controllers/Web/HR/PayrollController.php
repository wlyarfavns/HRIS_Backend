<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index()
    {
        // Fetch payrolls to display in the frontend
        $payrolls = Payroll::with('employee')->latest()->get();

        $totalKaryawan = $payrolls->count();
        $totalGajiBersih = $payrolls->sum('net_salary');

        // Format to nice string (e.g. Rp9.8 M or Rp9.500.000)
        $totalGajiBersihFormatted = 'Rp' . number_format($totalGajiBersih, 0, ',', '.');

        return view('hr.penggajian.index', compact('payrolls', 'totalKaryawan', 'totalGajiBersihFormatted'));
    }

    public function runPayroll(Request $request)
    {
        // In real app, these would come from the form inputs (month/year selector).
        // Since our dummy data in the DB is strictly for July 2026, we hardcode it for this demo.
        $companyId = 1; 
        $startDate = '2026-07-01';
        $endDate = '2026-07-31';

        // 1. Generate Attendance Summary
        $this->payrollService->generateAttendanceSummary($companyId, $startDate, $endDate);

        // 2. Calculate Salary
        $this->payrollService->calculateSalary($companyId, $startDate, $endDate);

        return redirect()->route('hr.payroll.index')->with('success', 'Berhasil! Payroll periode Juli 2026 telah selesai dikalkulasi oleh sistem.');
    }

    public function slip($id)
    {
        $payroll = Payroll::with(['employee', 'details.salaryComponent'])->findOrFail($id);
        
        return view('hr.penggajian.slip', compact('payroll'));
    }
}
