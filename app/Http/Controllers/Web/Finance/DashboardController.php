<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Reimbursement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $now = now();


        $months = collect(range(5, 0))->map(fn ($i) => $now->copy()->subMonths($i));

        $trendRaw = Reimbursement::selectRaw("DATE_FORMAT(claim_date, '%Y-%m') as ym, SUM(amount) as total")
            ->where('company_id', $companyId)
            ->where('status', Reimbursement::STATUS_APPROVED)
            ->where('claim_date', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $trendLabels = $months->map(fn ($m) => $m->translatedFormat('M'));
        $trendData = $months->map(fn ($m) => round(($trendRaw[$m->format('Y-m')] ?? 0) / 1_000_000, 2));

        $totalThisMonth = $trendData->last();
        $totalLastMonth = $trendData->count() > 1 ? $trendData[$trendData->count() - 2] : 0;
        $trendPercent = $totalLastMonth > 0
            ? round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100, 1)
            : 0;


        $slaBase = Reimbursement::where('company_id', $companyId)
            ->whereMonth('claim_date', $now->month)
            ->whereYear('claim_date', $now->year);

        $verifiedCount = (clone $slaBase)->where('status', Reimbursement::STATUS_APPROVED)->count();
        $pendingCount  = (clone $slaBase)->where('status', Reimbursement::STATUS_PENDING_FINANCE)->count();
        $rejectedCount = (clone $slaBase)->where('status', Reimbursement::STATUS_REJECTED)->count();
        $totalSla = max($verifiedCount + $pendingCount + $rejectedCount, 1);
        $slaOnTimePercent = round(($verifiedCount / $totalSla) * 100);


        $byDept = Reimbursement::selectRaw('departments.name as dept_name, SUM(reimbursements.amount) as total')
            ->join('employees', 'employees.id', '=', 'reimbursements.employee_id')
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->where('reimbursements.company_id', $companyId)
            ->where('reimbursements.status', Reimbursement::STATUS_APPROVED)
            ->whereMonth('reimbursements.claim_date', $now->month)
            ->whereYear('reimbursements.claim_date', $now->year)
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();


        $netPayrollThisMonth = Payroll::where('company_id', $companyId)
            ->where('status', Payroll::STATUS_APPROVED_HR)
            ->sum('net_salary');

        $totalReimburseThisMonth = (clone $slaBase)->where('status', Reimbursement::STATUS_APPROVED)->sum('amount');

        return view('finance.dashboard', [
            'periodLabel'             => $now->translatedFormat('F Y'),
            'trendLabels'             => $trendLabels,
            'trendData'               => $trendData,
            'trendTotalFormatted'     => 'Rp' . number_format($totalThisMonth, 2, ',', '.') . ' Jt',
            'trendPercent'            => $trendPercent,
            'slaOnTimePercent'        => $slaOnTimePercent,
            'verifiedCount'           => $verifiedCount,
            'pendingCount'            => $pendingCount,
            'rejectedCount'           => $rejectedCount,
            'totalSla'                => $totalSla,
            'deptLabels'              => $byDept->pluck('dept_name'),
            'deptData'                => $byDept->map(fn ($d) => round($d->total / 1_000_000, 2)),
            'netPayrollFormatted'     => $this->formatMillion($netPayrollThisMonth),
            'totalReimburseFormatted' => 'Rp' . number_format($totalReimburseThisMonth / 1_000_000, 3, ',', '.') . ' Jt',
            'pendingCountLabel'       => $pendingCount,
        ]);
    }

    private function formatMillion($value): string
    {
        if ($value >= 1_000_000_000) {
            return 'Rp' . number_format($value / 1_000_000_000, 2, ',', '.') . ' M';
        }
        return 'Rp' . number_format($value / 1_000_000, 2, ',', '.') . ' Jt';
    }
}