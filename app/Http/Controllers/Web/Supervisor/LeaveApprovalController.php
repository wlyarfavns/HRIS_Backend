<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveApprovalController extends Controller
{

    public function index(Request $request)
    {
        $supervisor = $request->user();
        $companyId = $supervisor->company_id;


        $pending = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType'])
            ->whereHas(
                'employee',
                fn($q) => $q
                    ->where('company_id', $companyId)
                    ->where('supervisor_id', $supervisor->id)
            )
            ->where('status', 'pending_spv')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($r) => $this->formatRow($r));


        $history = LeaveRequest::with(['employee.department', 'leaveType'])
            ->whereHas(
                'employee',
                fn($q) => $q
                    ->where('company_id', $companyId)
                    ->where('supervisor_id', $supervisor->id)
            )
            ->where(fn($q) => $q->whereIn('status', ['pending_hr', 'approved'])->orWhere(fn($sub) => $sub->where('status', 'rejected')->where('approved_by', $supervisor->id)))
            ->latest('approved_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn($r) => $this->formatHistory($r));


        $stats = [
            ['label' => 'MENUNGGU REVIEW', 'value' => $pending->total() . ' Pengajuan', 'icon' => 'assignment_late', 'color' => 'text-amber-800'],
            ['label' => 'DISETUJUI SPV', 'value' => LeaveRequest::whereHas('employee', fn($q) => $q->where('supervisor_id', $supervisor->id))->whereIn('status', ['pending_hr', 'approved'])->count() . ' Total', 'icon' => 'check_circle', 'color' => 'text-green-700'],
            ['label' => 'SEDANG CUTI', 'value' => LeaveRequest::whereHas('employee', fn($q) => $q->where('supervisor_id', $supervisor->id))->where('status', 'approved')->coveringDate(now()->toDateString())->count() . ' Orang', 'icon' => 'event_busy', 'color' => 'text-primary'],
            ['label' => 'DITOLAK', 'value' => LeaveRequest::whereHas('employee', fn($q) => $q->where('supervisor_id', $supervisor->id))->where('status', 'rejected')->where('approved_by', $supervisor->id)->count() . ' Total', 'icon' => 'cancel', 'color' => 'text-red-700'],
        ];

        return view('supervisor.persetujuan.cuti', compact('pending', 'history', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $supervisor = $request->user();
        $leaveRequest = LeaveRequest::whereHas(
            'employee',
            fn($q) => $q
                ->where('company_id', $supervisor->company_id)
                ->where('supervisor_id', $supervisor->id)
        )
            ->where('status', 'pending_spv')
            ->findOrFail($id);

        $leaveRequest->update([
            'status' => 'pending_hr',
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
        ]);




        $hrUsers = User::role('hr')->where('company_id', $supervisor->company_id)->get();
        foreach ($hrUsers as $hrUser) {
            $hrUser->notify(new GeneralNotification(
                'Persetujuan Cuti (Menunggu HR)',
                "Supervisor telah menyetujui pengajuan cuti {$leaveRequest->employee->full_name}. Silakan validasi saldo dan setujui pencairan.",
                route('hr.approvals.leave') 
            ));
        }


        if ($leaveRequest->employee && $leaveRequest->employee->user) {
            $leaveRequest->employee->user->notify(new SystemNotification(
                'Cuti Disetujui SPV',
                'Pengajuan cuti Anda telah disetujui Supervisor dan sedang menunggu persetujuan HR.',
                'info'
            ));
        }

        return back()->with('success', "Pengajuan cuti {$leaveRequest->employee->full_name} disetujui dan diteruskan ke HR.");
    }


    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $supervisor = $request->user();
        $leaveRequest = LeaveRequest::whereHas(
            'employee',
            fn($q) => $q
                ->where('company_id', $supervisor->company_id)
                ->where('supervisor_id', $supervisor->id)
        )
            ->where('status', 'pending_spv')
            ->findOrFail($id);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason ?? 'Ditolak oleh Supervisor',
        ]);

        if ($leaveRequest->employee && $leaveRequest->employee->user) {
            $leaveRequest->employee->user->notify(new SystemNotification(
                'Pengajuan Cuti Ditolak',
                'Pengajuan cuti Anda telah ditolak oleh Supervisor.',
                'error'
            ));
        }

        return back()->with(
            'success',
            "Pengajuan cuti {$leaveRequest->employee->full_name} berhasil ditolak."
        );
    }


    private function formatRow(LeaveRequest $r): array
    {
        $start = Carbon::parse($r->start_date)->translatedFormat('d M Y');
        $end = Carbon::parse($r->end_date)->translatedFormat('d M Y');

        return [
            'id' => $r->id,
            'name' => $r->employee->full_name ?? '-',
            'nip' => $r->employee->employee_id ?? '-',
            'pos' => $r->employee->position->name ?? '-',
            'dept' => $r->employee->department->name ?? '-',
            'type' => $r->leaveType->name ?? 'Izin',
            'range' => $start === $end ? $start : "{$start} – {$end}",
            'total_days' => $r->total_days,
            'reason' => $r->reason ?? '-',
            'attach' => (bool) $r->attachment,
            'attach_url' => $r->attachment ? asset('storage/' . $r->attachment) : null,
            'quota' => $r->leaveType?->is_quota_based
                ? 'Berbasis kuota — memotong saldo cuti'
                : 'Tidak memotong kuota',
            'avatar' => $r->employee->employee_id ?? $r->id,
            'initials' => strtoupper(substr($r->employee->full_name ?? '?', 0, 1)),
        ];
    }

    private function formatHistory(LeaveRequest $r): array
    {
        $start = Carbon::parse($r->start_date)->translatedFormat('d M Y');
        $end = Carbon::parse($r->end_date)->translatedFormat('d M Y');

        $statusMap = [
            'pending_hr' => ['label' => 'Menunggu HR', 'badge' => 'bg-blue-50 text-blue-800 border border-blue-200'],
            'approved' => ['label' => 'Disetujui HR', 'badge' => 'bg-green-50 text-green-800 border border-green-200'],
            'rejected' => ['label' => 'Ditolak', 'badge' => 'bg-red-50 text-red-800 border border-red-200'],
        ];
        $s = $statusMap[$r->status] ?? ['label' => $r->status, 'badge' => 'bg-surface-container text-on-surface-variant'];

        return [
            'name' => $r->employee->full_name ?? '-',
            'avatar' => $r->employee->employee_id ?? $r->id,
            'type' => $r->leaveType->name ?? 'Izin',
            'range' => $start === $end ? $start : "{$start} – {$end}",
            'decided' => $r->approved_at
                ? Carbon::parse($r->approved_at)->translatedFormat('d M Y, H:i')
                : '-',
            'status' => $s['label'],
            'status_badge' => $s['badge'],
        ];
    }
}