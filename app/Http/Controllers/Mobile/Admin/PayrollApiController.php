<?php

namespace App\Http\Controllers\Mobile\Admin;

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

    public function generateCutoff(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->authorizeCompany($request, $validated['company_id']);

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

    public function calculateSalary(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->authorizeCompany($request, $validated['company_id']);

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

    public function approveHr(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        $this->authorizeCompany($request, $payroll->company_id);

        if ($payroll->status !== Payroll::STATUS_DRAFT) {
            return response()->json([
                'message' => 'Payroll cannot be approved by HR because its current status is ' . $payroll->status
            ], 400);
        }

        $payroll->update(['status' => Payroll::STATUS_APPROVED_HR]);

        return response()->json([
            'message' => 'Payroll approved by HR successfully.',
            'data' => $payroll
        ], 200);
    }

    public function approveFinance(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        $this->authorizeCompany($request, $payroll->company_id);

        if ($payroll->status !== Payroll::STATUS_APPROVED_HR) {
            return response()->json([
                'message' => 'Payroll cannot be approved by Finance because its current status is ' . $payroll->status
            ], 400);
        }

        $payroll->update(['status' => Payroll::STATUS_APPROVED_FINANCE]);

        return response()->json([
            'message' => 'Payroll approved by Finance successfully.',
            'data' => $payroll
        ], 200);
    }

    public function exportBankCsv(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->authorizeCompany($request, $validated['company_id']);

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
     * Pastikan user hanya bisa mengakses data company miliknya sendiri.
     */
    private function authorizeCompany(Request $request, $companyId): void
    {
        abort_unless(
            $request->user()->company_id === (int) $companyId,
            403,
            'Unauthorized: data milik company lain.'
        );
    }

}