<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayrollService;
use App\Models\PayrollBatch;
class ExportController extends Controller
{
    public function __construct(protected PayrollService $payrollService)
    {
    }

    public function index(Request $request)
    {
        $batches = PayrollBatch::where('company_id', $request->user()->company_id)
            ->whereIn('status', [PayrollBatch::STATUS_APPROVED_FINANCE, PayrollBatch::STATUS_EXPORTED])
            ->withCount('payrolls')->withSum('payrolls', 'net_salary')
            ->with(['bankExports', 'approvedBy'])->get();

        return view('finance.export.index', compact('batches'));
    }

    public function generate(Request $request, PayrollBatch $batch)
    {
        abort_unless($batch->company_id === $request->user()->company_id, 403);
        $this->payrollService->exportBatchBankFiles($batch, $request->user()->id);
        return back()->with('success', 'File export bank berhasil digenerate.');
    }

    public function download(Request $request, PayrollBatch $batch, string $bankCode)
    {
        abort_unless($batch->company_id === $request->user()->company_id, 403);
        $csv = $this->payrollService->buildBankCsvContent($batch, $bankCode);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$bankCode}_PAYROLL_{$batch->period_start->format('MY')}.csv\"",
        ]);
    }
}
