<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayrollService;
use App\Models\PayrollBatch;
use App\Models\Payroll;
class PayrollController extends Controller
{
    public function __construct(protected PayrollService $payrollService)
    {
    }

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $pendingBatches = PayrollBatch::with('submittedBy')
            ->where('company_id', $companyId)
            ->where('status', PayrollBatch::STATUS_PENDING_FINANCE)
            ->withCount('payrolls')->withSum('payrolls', 'net_salary')
            ->latest('submitted_at')->get();

        $readyBatches = PayrollBatch::with('approvedBy')
            ->where('company_id', $companyId)
            ->where('status', PayrollBatch::STATUS_APPROVED_FINANCE)
            ->withCount('payrolls')->withSum('payrolls', 'net_salary')
            ->latest('approved_finance_at')->get();

        $completedBatches = PayrollBatch::with('bankExports')
            ->where('company_id', $companyId)
            ->where('status', PayrollBatch::STATUS_DISBURSED)
            ->withCount('payrolls')->withSum('payrolls', 'net_salary')
            ->latest('disbursed_at')->get();

        return view('finance.payroll.index', compact('pendingBatches', 'readyBatches', 'completedBatches'));
    }

    public function show(Request $request, PayrollBatch $batch)
    {
        abort_unless($batch->company_id === $request->user()->company_id, 403);
        $payrolls = $batch->payrolls()->with(['employee.department', 'details.salaryComponent'])->get();
        return view('finance.payroll.show', compact('batch', 'payrolls'));
    }

    public function approve(Request $request, PayrollBatch $batch)
    {
        abort_unless($batch->company_id === $request->user()->company_id, 403);
        abort_unless($batch->status === PayrollBatch::STATUS_PENDING_FINANCE, 400);

        $this->payrollService->approveBatchByFinance($batch, $request->user()->id);

        return redirect()->route('finance.export.index')
            ->with('success', "Payroll {$batch->period_start->translatedFormat('F Y')} disetujui & dikunci.");
    }

    public function slip(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $payroll = Payroll::with(['employee.department', 'employee.position', 'details.salaryComponent', 'payrollBatch'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $batch = $payroll->payrollBatch;

        if ($batch?->published_at) {
            $status = 'Slip Sudah Dipublish';
            $statusTime = $batch->published_at->translatedFormat('d M Y, H.i');
        } elseif ($batch?->disbursed_at) {
            $status = 'Dana Sudah Dicairkan';
            $statusTime = $batch->disbursed_at->translatedFormat('d M Y, H.i');
        } else {
            $status = 'Menunggu Pencairan';
            $statusTime = '-';
        }

        $slip = [
            'nip' => $payroll->employee->employee_id ?? '-',
            'name' => $payroll->employee->full_name ?? '-',
            'avatar' => ($payroll->employee->id % 70) + 1,
            'position' => $payroll->employee->position->name ?? '-',
            'department' => $payroll->employee->department->name ?? '-',
            'period' => $payroll->period_start->translatedFormat('F Y'),
            'earnings' => $payroll->details->where('type', 'earning')
                ->map(fn($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])->values(),
            'deductions' => $payroll->details->where('type', 'deduction')
                ->map(fn($d) => ['label' => $d->salaryComponent->name ?? '-', 'amount' => (float) $d->amount])->values(),
            'status' => $status,
            'status_time' => $statusTime,
            // Belum ada tabel log akses slip di DB — kosongkan dulu, bukan data fiktif.
            // Kalau butuh, tambahkan tabel payroll_slip_views (payroll_id, employee_id, action, viewed_at).
            'access_log' => [],
        ];

        return view('finance.disbursement.slip', compact('slip'));
    }
}
