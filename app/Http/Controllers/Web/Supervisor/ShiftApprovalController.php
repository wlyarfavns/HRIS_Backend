<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ShiftSwapRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class ShiftApprovalController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();
        $companyId  = $supervisor->company_id;



        $pending = ShiftSwapRequest::with(['fromEmployee', 'toEmployee', 'fromAssignment.shiftType', 'toAssignment.shiftType'])
            ->where('company_id', $companyId)
            ->where('status', 'pending_spv')
            ->where(function ($query) use ($supervisor) {
                $query->whereHas('fromEmployee', fn($q) => $q->where('supervisor_id', $supervisor->id))
                      ->orWhereHas('toEmployee', fn($q) => $q->where('supervisor_id', $supervisor->id));
            })
            ->latest()
            ->paginate(10)->withQueryString();

        $history = ShiftSwapRequest::with(['fromEmployee', 'toEmployee', 'fromAssignment.shiftType', 'toAssignment.shiftType'])
            ->where('company_id', $companyId)
            ->where(fn($q) => $q->whereIn('status', ['pending_hr', 'approved'])->orWhere(fn($sub) => $sub->where('status', 'rejected')->where('approved_by', $supervisor->id)))
            ->where('approved_by', $supervisor->id) 
            ->latest('approved_at')
            ->paginate(10)->withQueryString();

        $stats = [
            'pending_review' => $pending->total(),
        ];

        return view('supervisor.persetujuan.shift', compact('pending', 'history', 'stats'));
    }

    public function approve(Request $request, ShiftSwapRequest $swap)
    {
        $supervisor = $request->user();

        $this->authorizeOwnership($supervisor, $swap);
        abort_unless($swap->status === 'pending_spv', 422, 'Pengajuan sudah diproses.');

        $swap->update([
            'status'       => 'pending_hr',
            'approved_by'  => $supervisor->id,
            'approved_at'  => now(),
        ]);


        $hrUsers = User::role('hr')->where('company_id', $supervisor->company_id)->get();
        foreach ($hrUsers as $hrUser) {
            $hrUser->notify(new GeneralNotification(
                'Persetujuan Tukar Shift (Menunggu HR)',
                "Supervisor telah menyetujui pengajuan tukar shift {$swap->fromEmployee->full_name}. Silakan tinjau dan proses persetujuan akhir.",
                route('hr.shift.index') 
            ));
        }


        if ($swap->fromEmployee && $swap->fromEmployee->user) {
            $swap->fromEmployee->user->notify(new SystemNotification(
                'Tukar Shift Disetujui SPV',
                'Pengajuan tukar shift Anda telah disetujui Supervisor dan sedang menunggu persetujuan HR.',
                'info'
            ));
        }

        return redirect()->route('supervisor.approvals.shift')
            ->with('success', 'Pengajuan tukar shift disetujui & diteruskan ke HR.');
    }

    public function reject(Request $request, ShiftSwapRequest $swap)
    {
        $supervisor = $request->user();

        $this->authorizeOwnership($supervisor, $swap);
        abort_unless($swap->status === 'pending_spv', 422, 'Pengajuan sudah diproses.');

        $swap->update([
            'status'           => 'rejected',
            'approved_by'      => $supervisor->id,
            'approved_at'      => now(),
        ]);


        if ($swap->fromEmployee && $swap->fromEmployee->user) {
            $swap->fromEmployee->user->notify(new SystemNotification(
                'Tukar Shift Ditolak SPV',
                'Pengajuan tukar shift Anda telah ditolak oleh Supervisor.',
                'error'
            ));
        }

        return redirect()->route('supervisor.approvals.shift')
            ->with('success', 'Pengajuan tukar shift ditolak.');
    }

    private function authorizeOwnership($supervisor, ShiftSwapRequest $swap): void
    {
        $isFromSpv = $swap->fromEmployee?->supervisor_id === $supervisor->id;
        $isToSpv = $swap->toEmployee?->supervisor_id === $supervisor->id;

        abort_unless(
            $swap->company_id === $supervisor->company_id && ($isFromSpv || $isToSpv),
            403, 
            'Anda tidak memiliki akses ke pengajuan ini.'
        );
    }
}
