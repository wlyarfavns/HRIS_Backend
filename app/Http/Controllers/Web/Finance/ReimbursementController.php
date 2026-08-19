<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReimbursementFinanceActionRequest;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use App\Notifications\SystemNotification;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $query = Reimbursement::with(['employee.department'])
            ->where('company_id', $companyId)
            ->whereIn('status', [Reimbursement::STATUS_PENDING_FINANCE, Reimbursement::STATUS_APPROVED]);

        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%');
            });
        }

        $pendingAmount = (clone $query)->where('status', Reimbursement::STATUS_PENDING_FINANCE)->sum('amount');
        $pendingCount = (clone $query)->where('status', Reimbursement::STATUS_PENDING_FINANCE)->count();
        $approvedAmount = (clone $query)->where('status', Reimbursement::STATUS_APPROVED)->sum('amount');
        $avgAmount = (clone $query)->avg('amount') ?? 0;

        $perPage = $request->input('per_page', 10);
        $claims = $query->latest('claim_date')->paginate($perPage)->withQueryString();

        $stats = [
            ['label' => 'Total Klaim Menunggu Finance', 'value' => 'Rp' . number_format($pendingAmount, 0, ',', '.'), 'icon' => 'receipt_long', 'color' => 'text-amber-700'],
            ['label' => 'Jumlah Pengajuan Menunggu', 'value' => $pendingCount . ' Klaim', 'icon' => 'fact_check', 'color' => 'text-primary'],
            ['label' => 'Klaim Siap Disburse', 'value' => 'Rp' . number_format($approvedAmount, 0, ',', '.'), 'icon' => 'payments', 'color' => 'text-primary'],
            ['label' => 'Rata-rata per Klaim', 'value' => 'Rp' . number_format($avgAmount, 0, ',', '.'), 'icon' => 'query_stats', 'color' => 'text-on-surface'],
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

            if ($reimbursement->employee && $reimbursement->employee->user) {
                $reimbursement->employee->user->notify(new SystemNotification(
                    'Reimbursement Berhasil Dicairkan',
                    'Klaim reimbursement Anda telah disetujui sepenuhnya oleh Finance dan dana akan segera dicairkan.',
                    'success'
                ));
            }

            return response()->json(['message' => 'Klaim berhasil diverifikasi & masuk Disbursement.', 'status' => $reimbursement->status]);
        }

        $reimbursement->update([
            'status' => Reimbursement::STATUS_REJECTED,
            'finance_reviewed_by' => $request->user()->id,
            'finance_reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($reimbursement->employee && $reimbursement->employee->user) {
            $reimbursement->employee->user->notify(new SystemNotification(
                'Reimbursement Ditolak Finance',
                'Klaim reimbursement Anda ditolak pada tahap akhir oleh tim Finance. Alasan: ' . ($request->rejection_reason ?? 'Tidak ada alasan.'),
                'error'
            ));
        }

        return response()->json(['message' => 'Klaim berhasil ditolak.', 'status' => $reimbursement->status]);
    }
}