<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}