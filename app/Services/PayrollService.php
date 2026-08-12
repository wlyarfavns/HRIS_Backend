<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSummary;
use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\PayrollBankExport;
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

            $totalPresent = $attendancesCount;
            $totalAbsent = 0;
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
                        'status' => Payroll::STATUS_DRAFT,
                    ]
                );

                $payroll->details()->delete();
                $payroll->details()->createMany($detailsToCreate);

                $payrolls[] = $payroll;
            }
        });

        return $payrolls;
    }

    /**
     * Setujui semua payroll draft pada periode tsb sebagai HR Operations.
     * Return jumlah payroll yang berhasil diubah statusnya.
     */
    public function approveByHr($companyId, $startDate, $endDate): int
    {
        return Payroll::where('company_id', $companyId)
            ->where('period_start', $startDate)
            ->where('period_end', $endDate)
            ->where('status', Payroll::STATUS_DRAFT)
            ->update(['status' => Payroll::STATUS_APPROVED_HR]);
    }

    /**
     * Setujui semua payroll approved_hr pada periode tsb sebagai Finance.
     * Return jumlah payroll yang berhasil diubah statusnya.
     */
    public function approveByFinance($companyId, $startDate, $endDate): int
    {
        return Payroll::where('company_id', $companyId)
            ->where('period_start', $startDate)
            ->where('period_end', $endDate)
            ->where('status', Payroll::STATUS_APPROVED_HR)
            ->update(['status' => Payroll::STATUS_APPROVED_FINANCE]);
    }

    public function submitBatchToFinance($companyId, $startDate, $endDate, $userId): PayrollBatch
    {
        $batch = PayrollBatch::firstOrCreate(
            ['company_id' => $companyId, 'period_start' => $startDate, 'period_end' => $endDate],
            ['status' => PayrollBatch::STATUS_DRAFT]
        );

        $batch->update([
            'status' => PayrollBatch::STATUS_PENDING_FINANCE,
            'submitted_by' => $userId,
            'submitted_at' => now(),
        ]);

        Payroll::where('company_id', $companyId)
            ->where('period_start', $startDate)
            ->where('period_end', $endDate)
            ->update(['payroll_batch_id' => $batch->id]);

        return $batch;
    }

    public function approveBatchByFinance(PayrollBatch $batch, $userId): PayrollBatch
    {
        $batch->update([
            'status' => PayrollBatch::STATUS_APPROVED_FINANCE,
            'approved_finance_by' => $userId,
            'approved_finance_at' => now(),
        ]);

        $batch->payrolls()->where('status', Payroll::STATUS_APPROVED_HR)
            ->update(['status' => Payroll::STATUS_APPROVED_FINANCE]);

        return $batch;
    }

    public function exportBatchBankFiles(PayrollBatch $batch, $userId)
    {
        $groups = $batch->payrolls()->with('employee')->get()
            ->groupBy(fn($p) => $p->employee->bank_name ?? 'LAINNYA');

        $exports = collect();
        foreach ($groups as $bankCode => $group) {
            $exports->push(PayrollBankExport::updateOrCreate(
                ['payroll_batch_id' => $batch->id, 'bank_code' => $bankCode],
                [
                    'format' => 'CSV Mass Transfer',
                    'filename' => "{$bankCode}_PAYROLL_{$batch->period_start->format('MY')}.csv",
                    'accounts_count' => $group->count(),
                    'total_amount' => $group->sum('net_salary'),
                    'exported_by' => $userId,
                    'exported_at' => now(),
                ]
            ));
        }

        $batch->update(['status' => PayrollBatch::STATUS_EXPORTED, 'exported_at' => now()]);
        return $exports;
    }

    public function markBatchDisbursed(PayrollBatch $batch): PayrollBatch
    {
        $batch->update(['status' => PayrollBatch::STATUS_DISBURSED, 'disbursed_at' => now()]);
        return $batch;
    }

    public function buildBankCsvContent(PayrollBatch $batch, string $bankCode): string
    {
        $payrolls = $batch->payrolls()->with('employee')
            ->whereHas('employee', fn($q) => $q->where('bank_name', $bankCode))
            ->get();

        $rows = ['Employee ID,Name,Bank Name,Account Number,Account Holder,Amount,Description'];
        foreach ($payrolls as $p) {
            $e = $p->employee;
            $desc = "Gaji Periode {$batch->period_start->toDateString()} to {$batch->period_end->toDateString()}";
            $rows[] = implode(',', [
                '"' . $e->employee_id . '"',
                '"' . $e->full_name . '"',
                '"' . $e->bank_name . '"',
                '"' . $e->bank_account_number . '"',
                '"' . $e->bank_account_holder . '"',
                $p->net_salary,
                '"' . $desc . '"',
            ]);
        }
        return implode("\n", $rows);
    }

    public function markBatchPublished(PayrollBatch $batch, $userId): PayrollBatch
    {
        $batch->update([
            'published_by' => $userId,
            'published_at' => now(),
        ]);
        return $batch;
    }
}