<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Models\PayrollBatch;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class DisbursementController extends Controller
{
    public function __construct(protected PayrollService $payrollService) {}

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $batches = PayrollBatch::with([
                'payrolls.employee.department',
                'payrolls.employee.position',
                'payrolls.details.salaryComponent',
            ])
            ->where('company_id', $companyId)
            ->whereIn('status', [PayrollBatch::STATUS_EXPORTED, PayrollBatch::STATUS_DISBURSED])
            ->orderByDesc('period_start')
            ->paginate(10)->withQueryString()
            ->through(function ($batch) {
                return [
                    'id'          => $batch->id,
                    'period'      => $batch->period_start->translatedFormat('F Y'),
                    'exported_at' => $batch->exported_at
                        ? $batch->exported_at->translatedFormat('d M Y, H.i') . ' WIB'
                        : '-',
                    'total_emp'   => $batch->payrolls->count(),
                    'grand_nett'  => $batch->payrolls->sum('net_salary'),
                    'disbursed'   => $batch->status === PayrollBatch::STATUS_DISBURSED,
                    'published'   => (bool) $batch->published_at,
                    'employees'   => $batch->payrolls->map(function ($p) use ($batch) {
                        $earnings   = $p->details->where('type', 'earning');
                        $deductions = $p->details->where('type', 'deduction');

                        return [
                            'payroll_id' => $p->id,
                            'nip'        => $p->employee->employee_id ?? '-',
                            'name'       => $p->employee->full_name ?? '-',
                            'avatar'     => ($p->employee->id % 70) + 1,
                            'dept'       => $p->employee->department->name ?? '-',
                            'position'   => $p->employee->position->name ?? '-',
                            'bank'       => $p->employee->bank_name ?? '-',
                            'rekening'   => $p->employee->bank_account_number ?? '-',
                            'period'     => $batch->period_start->translatedFormat('F Y'),
                            'earnings'   => collect([
                                ['label' => 'Gaji Pokok', 'amount' => (float) $p->basic_salary],
                            ])->concat(
                                $earnings->map(fn ($d) => [
                                    'label'  => $d->salaryComponent->name ?? '-',
                                    'amount' => (float) $d->amount,
                                ])
                            )->values(),
                            'deductions' => $deductions->map(fn ($d) => [
                                'label'  => $d->salaryComponent->name ?? '-',
                                'amount' => (float) $d->amount,
                            ])->values(),
                            'net'        => (float) $p->net_salary,
                        ];
                    })->values(),
                ];
            });

        return view('finance.disbursement.index', compact('batches'));
    }

    public function markDisbursed(Request $request, PayrollBatch $batch)
    {
        abort_unless($batch->company_id === $request->user()->company_id, 403);
        abort_unless($batch->status === PayrollBatch::STATUS_EXPORTED, 400);

        $this->payrollService->markBatchDisbursed($batch);

        return back()->with('success', "Batch {$batch->period_start->translatedFormat('F Y')} berhasil ditandai sebagai sudah dicairkan.");
    }

    public function markPublished(Request $request, PayrollBatch $batch)
    {
        abort_unless($batch->company_id === $request->user()->company_id, 403);
        abort_unless($batch->status === PayrollBatch::STATUS_DISBURSED, 400);
        abort_if($batch->published_at, 400, 'Batch sudah dipublish sebelumnya.');

        $this->payrollService->markBatchPublished($batch, $request->user()->id);

        return back()->with('success', "Slip gaji periode {$batch->period_start->translatedFormat('F Y')} berhasil dipublish ke karyawan.");
    }
}