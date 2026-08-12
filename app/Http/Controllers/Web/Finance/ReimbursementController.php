<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReimbursementFinanceActionRequest;
use App\Models\Reimbursement;
use Illuminate\Http\Request;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $claims = Reimbursement::with(['employee.department'])
            ->where('company_id', $companyId)
            ->whereIn('status', [Reimbursement::STATUS_PENDING_FINANCE, Reimbursement::STATUS_APPROVED])
            ->latest('claim_date')
            ->get();

        $stats = [
            ['label' => 'Total Klaim Menunggu Finance', 'value' => 'Rp' . number_format($claims->where('status', Reimbursement::STATUS_PENDING_FINANCE)->sum('amount'), 0, ',', '.'), 'icon' => 'receipt_long', 'color' => 'text-amber-700'],
            ['label' => 'Jumlah Pengajuan Menunggu', 'value' => $claims->where('status', Reimbursement::STATUS_PENDING_FINANCE)->count() . ' Klaim', 'icon' => 'fact_check', 'color' => 'text-primary'],
            ['label' => 'Klaim Siap Disburse', 'value' => 'Rp' . number_format($claims->where('status', Reimbursement::STATUS_APPROVED)->sum('amount'), 0, ',', '.'), 'icon' => 'payments', 'color' => 'text-primary'],
            ['label' => 'Rata-rata per Klaim', 'value' => 'Rp' . number_format($claims->avg('amount') ?? 0, 0, ',', '.'), 'icon' => 'query_stats', 'color' => 'text-on-surface'],
        ];

        return view('finance.reimbursement.index', compact('claims', 'stats'));
    }

    public function action(ReimbursementFinanceActionRequest $request, Reimbursement $reimbursement)
    {
        abort_unless($reimbursement->status === Reimbursement::STATUS_PENDING_FINANCE, 400, 'Klaim tidak dalam status Pending Finance.');

        if ($request->action === 'approve') {
            $reimbursement->update([
                'status' => Reimbursement::STATUS_APPROVED,
                'finance_reviewed_by' => $request->user()->id,
                'finance_reviewed_at' => now(),
            ]);

            return response()->json(['message' => 'Klaim berhasil diverifikasi & masuk Disbursement.', 'status' => $reimbursement->status]);
        }

        $reimbursement->update([
            'status' => Reimbursement::STATUS_REJECTED,
            'finance_reviewed_by' => $request->user()->id,
            'finance_reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['message' => 'Klaim berhasil ditolak.', 'status' => $reimbursement->status]);
    }
}