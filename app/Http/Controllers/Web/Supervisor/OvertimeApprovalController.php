<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\SystemNotification;

class OvertimeApprovalController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();
        $companyId  = $supervisor->company_id;

        $pending = OvertimeRequest::with('employee')
            ->where('company_id', $companyId)
            ->where('status', 'pending_spv')
            ->whereHas('employee', fn ($q) => $q
                ->where('company_id', $companyId)
                ->where('supervisor_id', $supervisor->id)
            )
            ->latest('date')
            ->paginate(10)->withQueryString();

        $history = OvertimeRequest::with('employee')
            ->where('company_id', $companyId)
            ->whereIn('status', ['approved_spv', 'locked', 'rejected'])
            ->whereHas('employee', fn ($q) => $q
                ->where('company_id', $companyId)
                ->where('supervisor_id', $supervisor->id)
            )
            ->where('approved_by', $supervisor->id)
            ->latest('approved_at')
            ->paginate(10)->withQueryString();

        $stats = [
            'pending_review' => $pending->total(),
            'today_overtime' => OvertimeRequest::where('company_id', $companyId)
                ->whereHas('employee', fn ($q) => $q->where('supervisor_id', $supervisor->id))
                ->whereDate('date', today())->count(),
            'total_hours' => OvertimeRequest::where('company_id', $companyId)
                ->whereHas('employee', fn ($q) => $q->where('supervisor_id', $supervisor->id))
                ->where('status', '!=', 'rejected')
                ->whereMonth('date', now()->month)
                ->sum('hours'),
        ];

        return view('supervisor.persetujuan.lembur', compact('pending', 'history', 'stats'));
    }

    public function approve(Request $request, OvertimeRequest $overtime)
    {
        $this->authorizeOwnership($request, $overtime);
        abort_unless($overtime->status === 'pending_spv', 422, 'SPL sudah diproses.');

        $overtime->update([
            'status'       => 'approved_spv',
            'approved_by'  => $request->user()->id,
            'approved_at'  => now(),
            'overtime_pay' => OvertimeRequest::calculateOvertimePay(
                $overtime->salary_snapshot,
                $overtime->hours
            ),
        ]);

        $hrUsers = User::role('hr')->where('company_id', $request->user()->company_id)->get();
        foreach ($hrUsers as $hrUser) {
            $hrUser->notify(new GeneralNotification(
                'Persetujuan Lembur (Menunggu HR)',
                "Supervisor telah menyetujui pengajuan lembur {$overtime->employee->full_name}. Silakan tinjau dan proses penggajian lembur.",
                route('hr.approvals.overtime') 
            ));
        }

        if ($overtime->employee && $overtime->employee->user) {
            $overtime->employee->user->notify(new SystemNotification(
                'Lembur Disetujui SPV',
                'Pengajuan lembur Anda telah disetujui Supervisor dan diteruskan ke HR.',
                'info'
            ));
        }

        return redirect()->route('supervisor.approvals.overtime')
            ->with('success', 'SPL berhasil disetujui & diteruskan ke HR Operations.');
    }

    public function reject(Request $request, OvertimeRequest $overtime)
    {
        $this->authorizeOwnership($request, $overtime);
        abort_unless($overtime->status === 'pending_spv', 422, 'SPL sudah diproses.');

        $data = $request->validate(['reason' => 'nullable|string|max:255']);

        $overtime->update([
            'status'           => 'rejected',
            'approved_by'      => $request->user()->id,
            'approved_at'      => now(),
            'rejection_reason' => $data['reason'] ?? 'Ditolak oleh Supervisor',
        ]);

        if ($overtime->employee && $overtime->employee->user) {
            $overtime->employee->user->notify(new SystemNotification(
                'Lembur Ditolak',
                'Pengajuan lembur Anda telah ditolak oleh Supervisor. Alasan: ' . ($data['reason'] ?? 'Tidak ada alasan.'),
                'error'
            ));
        }

        return redirect()->route('supervisor.approvals.overtime')
            ->with('success', 'SPL ditolak.');
    }


    private function authorizeOwnership(Request $request, OvertimeRequest $overtime): void
    {
        $supervisor = $request->user();

        abort_unless(
            $overtime->company_id === $supervisor->company_id
            && $overtime->employee?->supervisor_id === $supervisor->id,
            403,
            'Anda tidak berwenang memproses SPL ini.'
        );
    }
}