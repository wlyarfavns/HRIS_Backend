<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollApiController extends Controller
{
    protected $payrollService;

    public function __construct(\App\Services\PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Endpoint to automatically generate attendance cut-off summary.
     */
    public function generateCutoff(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $summaries = $this->payrollService->generateAttendanceSummary(
            $validated['company_id'],
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json([
            'message' => 'Attendance cut-off generated successfully.',
            'data' => $summaries
        ], 200);
    }

    /**
     * Endpoint to calculate net salary.
     */
    public function calculateSalary(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $payrolls = $this->payrollService->calculateSalary(
            $validated['company_id'],
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json([
            'message' => 'Salary calculation completed successfully.',
            'data' => $payrolls
        ], 200);
    }

    /**
     * Endpoint to approve payroll by HR.
     */
    public function approveHr(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== Payroll::STATUS_DRAFT) {
            return response()->json([
                'message' => 'Payroll cannot be approved by HR because its current status is ' . $payroll->status
            ], 400);
        }

        $payroll->update([
            'status' => Payroll::STATUS_APPROVED_HR
        ]);

        return response()->json([
            'message' => 'Payroll approved by HR successfully.',
            'data' => $payroll
        ], 200);
    }

    /**
     * Endpoint to approve payroll by Finance.
     */
    public function approveFinance(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== Payroll::STATUS_APPROVED_HR) {
            return response()->json([
                'message' => 'Payroll cannot be approved by Finance because its current status is ' . $payroll->status
            ], 400);
        }

        $payroll->update([
            'status' => Payroll::STATUS_APPROVED_FINANCE
        ]);

        return response()->json([
            'message' => 'Payroll approved by Finance successfully.',
            'data' => $payroll
        ], 200);
    }

    /**
     * Export Bank Transfer CSV.
     */
    public function exportBankCsv(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $payrolls = Payroll::with('employee')
            ->where('company_id', $validated['company_id'])
            ->where('period_start', $validated['start_date'])
            ->where('period_end', $validated['end_date'])
            ->where('status', Payroll::STATUS_APPROVED_FINANCE)
            ->get();

        $csvHeader = ['Employee ID', 'Name', 'Bank Name', 'Account Number', 'Account Holder', 'Amount', 'Description'];
        $csvData = [];
        $csvData[] = implode(',', $csvHeader);

        foreach ($payrolls as $payroll) {
            $employee = $payroll->employee;
            $description = "Gaji Periode " . $validated['start_date'] . " to " . $validated['end_date'];
            
            $row = [
                '"' . ($employee->employee_id ?? '') . '"',
                '"' . ($employee->full_name ?? '') . '"',
                '"' . ($employee->bank_name ?? '') . '"',
                '"' . ($employee->bank_account_number ?? '') . '"',
                '"' . ($employee->bank_account_holder ?? '') . '"',
                $payroll->net_salary,
                '"' . $description . '"',
            ];
            $csvData[] = implode(',', $row);
        }

        $csvString = implode("\n", $csvData);

        return response($csvString, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bank_transfer_'.$validated['start_date'].'_'.$validated['end_date'].'.csv"',
        ]);
    }

    /**
     * Generate PDF Payslip.
     */
    public function generateSlip($id)
    {
        $payroll = Payroll::with(['employee', 'company'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payslip', [
            'payroll' => $payroll
        ]);

        return $pdf->download('Slip_Gaji_' . ($payroll->employee->employee_id ?? 'Karyawan') . '_' . $payroll->period_start->format('M_Y') . '.pdf');
    }
}