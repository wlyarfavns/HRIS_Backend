<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReimbursementSpvActionRequest;
use App\Models\Employee;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\SystemNotification;

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
            ->paginate(10)->withQueryString();

        $history = Reimbursement::with('employee')
            ->whereIn('employee_id', $subordinateIds)
            ->where('spv_id', $user->id)
            ->where(function($q) {
                $q->where('status', '!=', Reimbursement::STATUS_REJECTED)
                  ->orWhereNull('hr_reviewed_by');
            })
            ->latest('spv_approved_at')
            ->paginate(10)->withQueryString();

        $stats = [
            ['label' => 'Klaim Pending Review', 'value' => $pending->total() . ' Pengajuan', 'icon' => 'receipt_long', 'color' => 'text-amber-700'],
            ['label' => 'Total Nominal Pending', 'value' => 'Rp' . number_format(Reimbursement::whereIn('employee_id', $subordinateIds)->pendingSpv()->sum('amount'), 0, ',', '.'), 'icon' => 'payments', 'color' => 'text-primary'],
            ['label' => 'Klaim Tim Bulan Ini', 'value' => Reimbursement::whereIn('employee_id', $subordinateIds)->whereMonth('claim_date', now()->month)->count() . ' Pengajuan', 'icon' => 'fact_check', 'color' => 'text-purple-700'],
            ['label' => 'Total Riwayat', 'value' => $history->total() . ' Pengajuan', 'icon' => 'verified', 'color' => 'text-primary'],
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

            $hrUsers = User::role('hr')->where('company_id', $user->company_id)->get();
            foreach ($hrUsers as $hrUser) {
                $hrUser->notify(new GeneralNotification(
                    'Persetujuan Reimbursement (Menunggu HR)',
                    "Supervisor telah menyetujui reimbursement dari {$reimbursement->employee->full_name}. Silakan tinjau dan proses pencairan dana.",
                    route('hr.approvals.reimbursement') 
                ));
            }

            if ($reimbursement->employee && $reimbursement->employee->user) {
                $reimbursement->employee->user->notify(new SystemNotification(
                    'Reimbursement Disetujui SPV',
                    'Klaim reimbursement Anda telah disetujui Supervisor dan sedang menunggu proses HR/Finance.',
                    'info'
                ));
            }

            return response()->json(['message' => 'Klaim berhasil disetujui & diteruskan ke HR Operations.', 'status' => $reimbursement->status]);
        }

        $reimbursement->update([
            'status' => Reimbursement::STATUS_REJECTED,
            'spv_id' => $user->id,
            'spv_approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($reimbursement->employee && $reimbursement->employee->user) {
            $reimbursement->employee->user->notify(new SystemNotification(
                'Reimbursement Ditolak',
                'Klaim reimbursement Anda telah ditolak oleh Supervisor. Alasan: ' . ($request->rejection_reason ?? 'Tidak ada alasan.'),
                'error'
            ));
        }

        return response()->json(['message' => 'Klaim tim ditolak.', 'status' => $reimbursement->status]);
    }
}