<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSummary;
use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\SalaryComponent;
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
        $query = Employee::where('company_id', $companyId);
        if (!empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }
        $employees = $query->get();

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

    private function resolveComponentAmount(SalaryComponent $component, float $basicSalary): float
    {
        $value = (float) ($component->default_amount ?? 0);
        $calcType = strtolower($component->calculation_type ?? '');
        $isPercentage = str_contains($calcType, 'persentase') || str_contains($calcType, '%');

        if ($isPercentage) {
            return round($basicSalary * ($value / 100), 2);
        }

        return $value;
    }

    public function calculateSalary($companyId, $startDate, $endDate)
    {
        $query = AttendanceSummary::where('company_id', $companyId)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate);

        if (!empty($employeeIds)) {
            $query->whereIn('employee_id', $employeeIds);
        }

        $summaries = $query->get();
        $components = SalaryComponent::where('company_id', $companyId)->get();

        $gajiPokokComponent = $components->first(
            fn($c) => strtolower(trim($c->name)) === 'gaji pokok'
        );
        $basicSalary = (float) ($gajiPokokComponent->default_amount ?? 0);

        $payrolls = [];

        DB::transaction(function () use ($summaries, $companyId, $startDate, $endDate, $components, $basicSalary, &$payrolls) {
            foreach ($summaries as $summary) {
                $employeeId = $summary->employee_id;

                $totalAllowances = 0;
                $totalDeductions = 0;
                $detailsToCreate = [];

                foreach ($components as $component) {
                    // Gaji Pokok sudah dihitung terpisah sebagai $basicSalary, skip di loop.
                    if (strtolower(trim($component->name)) === 'gaji pokok') {
                        continue;
                    }

                    $amount = $this->resolveComponentAmount($component, $basicSalary);

                    if ($component->type === 'earning') {
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

    /**
     * FIX: guard di level Service (bukan cuma Controller) supaya batch yang sudah
     * lanjut ke tahap exported/disbursed/published TIDAK BISA di-approve ulang.
     * Approve ulang sebelumnya bisa menimpa status batch balik ke 'approved_finance'
     * walau exported_at/disbursed_at/published_at sudah terisi — batch jadi "hilang"
     * dari halaman Disbursement meski datanya sebenarnya sudah lengkap.
     */
    public function approveBatchByFinance(PayrollBatch $batch, $userId): PayrollBatch
    {
        abort_unless(
            $batch->status === PayrollBatch::STATUS_PENDING_FINANCE,
            409,
            "Batch periode {$batch->period_start->translatedFormat('F Y')} berstatus \"{$batch->status}\" dan tidak bisa di-approve ulang."
        );

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
        // FIX: guard supaya batch yang sudah exported/disbursed/published tidak bisa
        // di-generate ulang secara tidak sengaja dan menimpa status maju-mundur.
        abort_unless(
            $batch->status === PayrollBatch::STATUS_APPROVED_FINANCE,
            409,
            "Batch periode {$batch->period_start->translatedFormat('F Y')} berstatus \"{$batch->status}\" dan tidak bisa diexport ulang."
        );

        $exports = collect();

        DB::transaction(function () use ($batch, $userId, &$exports) {
            $groups = $batch->payrolls()->with('employee')->get()
                ->groupBy(function ($p) {
                    $bank = trim((string) ($p->employee->bank_name ?? ''));
                    return $bank === '' ? 'LAINNYA' : $bank;
                });

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
        });

        return $exports;
    }


    public function markBatchDisbursed(PayrollBatch $batch): PayrollBatch
    {
        // FIX: guard tambahan di Service, konsisten dengan guard yang sudah ada di
        // DisbursementController::markDisbursed() — mencegah re-run yang menimpa
        // disbursed_at kalau batch sudah lewat dari status 'exported'.
        abort_unless(
            $batch->status === PayrollBatch::STATUS_EXPORTED,
            409,
            "Batch periode {$batch->period_start->translatedFormat('F Y')} berstatus \"{$batch->status}\" dan tidak bisa ditandai disbursed."
        );

        $batch->update(['status' => PayrollBatch::STATUS_DISBURSED, 'disbursed_at' => now()]);
        return $batch;
    }

    public function buildBankCsvContent(PayrollBatch $batch, string $bankCode): string
    {
        $payrolls = $batch->payrolls()->with('employee')
            ->whereHas('employee', function ($q) use ($bankCode) {
                if ($bankCode === 'LAINNYA') {
                    $q->where(function ($sub) {
                        $sub->whereNull('bank_name')->orWhere('bank_name', '');
                    });
                } else {
                    $q->where('bank_name', $bankCode);
                }
            })
            ->get();

        $rows = ['Employee ID,Name,Bank Name,Account Number,Account Holder,Amount,Description'];
        foreach ($payrolls as $p) {
            $e = $p->employee;
            $desc = "Gaji Periode {$batch->period_start->toDateString()} to {$batch->period_end->toDateString()}";
            $rows[] = implode(',', [
                '"' . ($e->employee_id ?? '') . '"',
                '"' . ($e->full_name ?? '') . '"',
                '"' . ($e->bank_name ?? 'LAINNYA') . '"',
                '"' . ($e->bank_account_number ?? '-') . '"',
                '"' . ($e->bank_account_holder ?? '-') . '"',
                $p->net_salary,
                '"' . $desc . '"',
            ]);
        }
        return implode("\n", $rows);
    }

    public function markBatchPublished(PayrollBatch $batch, $userId): PayrollBatch
    {
        // FIX: guard tambahan di Service, konsisten dengan guard di
        // DisbursementController::markPublished() — batch harus sudah disbursed
        // dan belum pernah dipublish sebelumnya.
        abort_unless(
            $batch->status === PayrollBatch::STATUS_DISBURSED,
            409,
            "Batch periode {$batch->period_start->translatedFormat('F Y')} berstatus \"{$batch->status}\" dan belum bisa dipublish."
        );
        abort_if(
            $batch->published_at,
            409,
            "Batch periode {$batch->period_start->translatedFormat('F Y')} sudah dipublish sebelumnya."
        );

        $batch->update([
            'published_by' => $userId,
            'published_at' => now(),
        ]);
        return $batch;
    }
}