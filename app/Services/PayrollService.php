<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSummary;
use App\Models\Employee;
use App\Models\EmployeeSalaryComponent;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate Cut-off Attendance for a company in a specific period.
     */
    public function generateAttendanceSummary($companyId, $startDate, $endDate)
    {
        $employees = Employee::where('company_id', $companyId)->get();
        $summaries = [];

        foreach ($employees as $employee) {
            $attendancesCount = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->count();

            // Total Present is simply the number of check-ins for now.
            // A more advanced logic would check workdays vs check-ins to find absences.
            $totalPresent = $attendancesCount;
            $totalAbsent = 0; // Simplified for this engine demo
            $totalLate = 0;

            $summary = AttendanceSummary::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'employee_id' => $employee->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                [
                    'total_present' => $totalPresent,
                    'total_absent' => $totalAbsent,
                    'total_late' => $totalLate,
                    'total_early_leave' => 0,
                ]
            );

            $summaries[] = $summary;
        }

        return $summaries;
    }

    /**
     * Calculate Salary based on attendance summaries and salary components.
     */
    public function calculateSalary($companyId, $startDate, $endDate)
    {
        $summaries = AttendanceSummary::where('company_id', $companyId)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get();

        $payrolls = [];

        DB::transaction(function () use ($summaries, $companyId, $startDate, $endDate, &$payrolls) {
            foreach ($summaries as $summary) {
                $employeeId = $summary->employee_id;
                
                $employeeComponents = EmployeeSalaryComponent::with('salaryComponent')
                    ->where('employee_id', $employeeId)
                    ->get();

                $totalAllowances = 0;
                $totalDeductions = 0;
                $basicSalary = 0;

                $detailsToCreate = [];

                foreach ($employeeComponents as $empComp) {
                    $component = $empComp->salaryComponent;
                    $amount = $empComp->amount;

                    // Standardize finding basic salary
                    if (strtolower(trim($component->name)) === 'gaji pokok') {
                        $basicSalary = $amount;
                    } elseif ($component->type === 'earning') {
                        $totalAllowances += $amount;
                    } elseif ($component->type === 'deduction') {
                        $totalDeductions += $amount;
                    }

                    $detailsToCreate[] = [
                        'salary_component_id' => $component->id,
                        'amount' => $amount,
                        'type' => $component->type,
                    ];
                }

                $netSalary = $basicSalary + $totalAllowances - $totalDeductions;

                $payroll = Payroll::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'employee_id' => $employeeId,
                        'period_start' => $startDate,
                        'period_end' => $endDate,
                    ],
                    [
                        'basic_salary' => $basicSalary,
                        'total_allowances' => $totalAllowances,
                        'total_deductions' => $totalDeductions,
                        'net_salary' => $netSalary,
                        'status' => 'draft',
                    ]
                );

                $payroll->details()->delete();
                $payroll->details()->createMany($detailsToCreate);

                $payrolls[] = $payroll;
            }
        });

        return $payrolls;
    }
}
