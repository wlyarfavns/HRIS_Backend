<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReimbursementSpvActionRequest;
use App\Models\Employee;
use App\Models\Reimbursement;
use Illuminate\Http\Request;

class ReimbursementApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $subordinateIds = Employee::where('supervisor_id', $user->id)->pluck('id');

        $pending = Reimbursement::with('employee.department')
            ->whereIn('employee_id', $subordinateIds)
            ->pendingSpv()
            ->latest('claim_date')
            ->get();

        $history = Reimbursement::with('employee')
            ->whereIn('employee_id', $subordinateIds)
            ->where('spv_id', $user->id)
            ->latest('spv_approved_at')
            ->limit(20)
            ->get();

        $stats = [
            ['label' => 'Klaim Pending Review', 'value' => $pending->count() . ' Pengajuan', 'icon' => 'receipt_long', 'color' => 'text-amber-700'],
            ['label' => 'Total Nominal Pending', 'value' => 'Rp' . number_format($pending->sum('amount'), 0, ',', '.'), 'icon' => 'payments', 'color' => 'text-primary'],
            ['label' => 'Klaim Tim Bulan Ini', 'value' => Reimbursement::whereIn('employee_id', $subordinateIds)->whereMonth('claim_date', now()->month)->count() . ' Pengajuan', 'icon' => 'fact_check', 'color' => 'text-purple-700'],
            ['label' => 'Total Riwayat', 'value' => $history->count() . ' Pengajuan', 'icon' => 'verified', 'color' => 'text-primary'],
        ];

        return view('supervisor.persetujuan.reimbursement', compact('pending', 'history', 'stats'));
    }

    public function action(ReimbursementSpvActionRequest $request, Reimbursement $reimbursement)
    {
        $user = $request->user();
        $subordinateIds = Employee::where('supervisor_id', $user->id)->pluck('id');

        abort_unless($subordinateIds->contains($reimbursement->employee_id), 404);
        abort_unless($reimbursement->status === Reimbursement::STATUS_PENDING_SPV, 400, 'Klaim tidak dalam status Pending SPV.');

        if ($request->action === 'approve') {
            $reimbursement->update([
                'status' => Reimbursement::STATUS_PENDING_HR,
                'spv_id' => $user->id,
                'spv_approved_at' => now(),
            ]);

            return response()->json(['message' => 'Klaim berhasil disetujui & diteruskan ke HR Operations.', 'status' => $reimbursement->status]);
        }

        $reimbursement->update([
            'status' => Reimbursement::STATUS_REJECTED,
            'spv_id' => $user->id,
            'spv_approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['message' => 'Klaim tim ditolak.', 'status' => $reimbursement->status]);
    }
}