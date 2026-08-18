<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReimbursementActionRequest;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use App\Notifications\SystemNotification;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Reimbursement::with(['employee.department', 'spv'])
            ->where('company_id', $companyId)
            ->whereIn('status', [
                Reimbursement::STATUS_PENDING_HR,
                Reimbursement::STATUS_PENDING_FINANCE,
                Reimbursement::STATUS_APPROVED,
                Reimbursement::STATUS_REJECTED,
            ]);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', fn ($e) => $e->where('full_name', 'like', "%{$search}%"))
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $claims = $query->latest('claim_date')->paginate(10)->withQueryString();

        $stats = [
            [
                'label' => 'Total Klaim Pending',
                'value' => 'Rp' . number_format(
                    $claims->whereIn('status', ['pending_hr', 'pending_finance'])->sum('amount'), 0, ',', '.'
                ),
                'icon' => 'receipt_long', 'color' => 'text-amber-700',
            ],
            [
                'label' => 'Jumlah Pengajuan',
                'value' => $claims->count() . ' Klaim',
                'icon' => 'fact_check', 'color' => 'text-primary',
            ],
            [
                'label' => 'Klaim Siap ke Finance',
                'value' => $claims->where('status', 'pending_finance')->count() . ' Pengajuan',
                'icon' => 'account_balance_wallet', 'color' => 'text-primary',
            ],
            [
                'label' => 'Rata-rata Nominal Klaim',
                'value' => 'Rp' . number_format($claims->avg('amount') ?? 0, 0, ',', '.'),
                'icon' => 'query_stats', 'color' => 'text-on-surface',
            ],
        ];

        return view('hr.persetujuan.reimbursement', compact('claims', 'stats'));
    }

    public function verify(ReimbursementActionRequest $request, Reimbursement $reimbursement)
    {

        abort_unless($reimbursement->company_id === auth()->user()->company_id, 404);

        abort_unless($reimbursement->status === Reimbursement::STATUS_PENDING_HR, 400, 'Klaim tidak dalam status Pending HR.');

        if ($request->action === 'approve') {
            $reimbursement->update([
                'status' => Reimbursement::STATUS_PENDING_FINANCE,
                'hr_reviewed_by' => auth()->id(),
                'hr_reviewed_at' => now(),
            ]);

            if ($reimbursement->employee && $reimbursement->employee->user) {
                $reimbursement->employee->user->notify(new SystemNotification(
                    'Selamat Reimbursse Anda Berhasil',
                    'Klaim reimbursement Anda telah divalidasi HR dan sedang diteruskan ke tim Finance untuk pencairan.',
                    'info'
                ));
            }

            return response()->json([
                'message' => 'Klaim berhasil diverifikasi & diteruskan ke Finance!',
                'status' => $reimbursement->status,
            ]);
        }

        $reimbursement->update([
            'status' => Reimbursement::STATUS_REJECTED,
            'hr_reviewed_by' => auth()->id(),
            'hr_reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($reimbursement->employee && $reimbursement->employee->user) {
            $reimbursement->employee->user->notify(new SystemNotification(
                'Reimbursement Ditolak',
                'Klaim reimbursement Anda telah ditolak oleh HR. Alasan: ' . ($request->rejection_reason ?? 'Tidak ada alasan.'),
                'error'
            ));
        }

        return response()->json([
            'message' => 'Klaim berhasil ditolak',
            'status' => $reimbursement->status,
        ]);
    }
}