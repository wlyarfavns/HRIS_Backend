<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveApprovalController extends Controller
{
    /**
     * Halaman persetujuan cuti milik Supervisor.
     * Supervisor hanya lihat karyawan yang supervisor_id = dirinya.
     */
    public function index(Request $request)
    {
        $supervisor   = $request->user();
        $companyId    = $supervisor->company_id;

        // Pengajuan yang menunggu persetujuan Supervisor
        $pending = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType'])
            ->whereHas('employee', fn($q) => $q
                ->where('company_id', $companyId)
                ->where('supervisor_id', $supervisor->id)
            )
            ->where('status', 'pending_spv')
            ->latest()
            ->get()
            ->map(fn($r) => $this->formatRow($r));

        // Riwayat yang sudah pernah diproses Supervisor ini
        $history = LeaveRequest::with(['employee.department', 'leaveType'])
            ->whereHas('employee', fn($q) => $q
                ->where('company_id', $companyId)
                ->where('supervisor_id', $supervisor->id)
            )
            ->whereIn('status', ['pending_hr', 'approved', 'rejected'])
            ->latest('approved_at')
            ->limit(30)
            ->get()
            ->map(fn($r) => $this->formatHistory($r));

        // Stats
        $stats = [
            ['label' => 'MENUNGGU REVIEW',    'value' => $pending->count() . ' Pengajuan', 'icon' => 'assignment_late', 'color' => 'text-amber-800'],
            ['label' => 'DISETUJUI SPV',      'value' => LeaveRequest::whereHas('employee', fn($q) => $q->where('supervisor_id', $supervisor->id))->whereIn('status', ['pending_hr', 'approved'])->count() . ' Total', 'icon' => 'check_circle', 'color' => 'text-green-700'],
            ['label' => 'SEDANG CUTI',        'value' => LeaveRequest::whereHas('employee', fn($q) => $q->where('supervisor_id', $supervisor->id))->where('status', 'approved')->coveringDate(now()->toDateString())->count() . ' Orang', 'icon' => 'event_busy', 'color' => 'text-primary'],
            ['label' => 'DITOLAK',            'value' => LeaveRequest::whereHas('employee', fn($q) => $q->where('supervisor_id', $supervisor->id))->where('status', 'rejected')->count() . ' Total', 'icon' => 'cancel', 'color' => 'text-red-700'],
        ];

        return view('supervisor.persetujuan.cuti', compact('pending', 'history', 'stats'));
    }

    /**
     * Supervisor setujui → status berubah ke pending_hr.
     */
    public function approve(Request $request, $id)
    {
        $supervisor   = $request->user();
        $leaveRequest = LeaveRequest::whereHas('employee', fn($q) => $q
                ->where('company_id', $supervisor->company_id)
                ->where('supervisor_id', $supervisor->id)
            )
            ->where('status', 'pending_spv')
            ->findOrFail($id);

        $leaveRequest->update([
            'status'      => 'pending_hr',  // ← lanjut ke antrian HR
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
        ]);

        return back()->with('success',
            "Pengajuan cuti {$leaveRequest->employee->full_name} disetujui dan diteruskan ke HR."
        );
    }

    /**
     * Supervisor tolak → status rejected, tidak naik ke HR.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $supervisor   = $request->user();
        $leaveRequest = LeaveRequest::whereHas('employee', fn($q) => $q
                ->where('company_id', $supervisor->company_id)
                ->where('supervisor_id', $supervisor->id)
            )
            ->where('status', 'pending_spv')
            ->findOrFail($id);

        $leaveRequest->update([
            'status'           => 'rejected',
            'approved_by'      => $supervisor->id,
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason ?? 'Ditolak oleh Supervisor',
        ]);

        return back()->with('success',
            "Pengajuan cuti {$leaveRequest->employee->full_name} berhasil ditolak."
        );
    }

    // ── Helper format ────────────────────────────────────────────────────────

    private function formatRow(LeaveRequest $r): array
    {
        $start = Carbon::parse($r->start_date)->translatedFormat('d M Y');
        $end   = Carbon::parse($r->end_date)->translatedFormat('d M Y');

        return [
            'id'         => $r->id,
            'name'       => $r->employee->full_name ?? '-',
            'nip'        => $r->employee->employee_id ?? '-',
            'pos'        => $r->employee->position->name ?? '-',
            'dept'       => $r->employee->department->name ?? '-',
            'type'       => $r->leaveType->name ?? 'Izin',
            'range'      => $start === $end ? $start : "{$start} – {$end}",
            'total_days' => $r->total_days,
            'reason'     => $r->reason ?? '-',
            'attach'     => (bool) $r->attachment,
            'attach_url' => $r->attachment ? asset('storage/' . $r->attachment) : null,
            'quota'      => $r->leaveType?->is_quota_based
                                ? 'Berbasis kuota — memotong saldo cuti'
                                : 'Tidak memotong kuota',
            'avatar'     => $r->employee->employee_id ?? $r->id,
            'initials'   => strtoupper(substr($r->employee->full_name ?? '?', 0, 1)),
        ];
    }

    private function formatHistory(LeaveRequest $r): array
    {
        $start = Carbon::parse($r->start_date)->translatedFormat('d M Y');
        $end   = Carbon::parse($r->end_date)->translatedFormat('d M Y');

        $statusMap = [
            'pending_hr' => ['label' => 'Menunggu HR',  'badge' => 'bg-blue-50 text-blue-800 border border-blue-200'],
            'approved'   => ['label' => 'Disetujui HR', 'badge' => 'bg-green-50 text-green-800 border border-green-200'],
            'rejected'   => ['label' => 'Ditolak',      'badge' => 'bg-red-50 text-red-800 border border-red-200'],
        ];
        $s = $statusMap[$r->status] ?? ['label' => $r->status, 'badge' => 'bg-surface-container text-on-surface-variant'];

        return [
            'name'         => $r->employee->full_name ?? '-',
            'avatar'       => $r->employee->employee_id ?? $r->id,
            'type'         => $r->leaveType->name ?? 'Izin',
            'range'        => $start === $end ? $start : "{$start} – {$end}",
            'decided'      => $r->approved_at
                                ? Carbon::parse($r->approved_at)->translatedFormat('d M Y, H:i')
                                : '-',
            'status'       => $s['label'],
            'status_badge' => $s['badge'],
        ];
    }
}