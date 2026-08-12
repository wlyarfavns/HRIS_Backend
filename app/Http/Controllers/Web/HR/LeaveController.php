<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        // Pending = hanya yang sudah disetujui SPV, menunggu HR
        $pending = LeaveRequest::forCompany($companyId)
            ->where('status', 'pending_hr')
            ->count();

        $onLeaveToday = LeaveRequest::forCompany($companyId)
            ->where('status', 'approved')
            ->coveringDate(now()->toDateString())
            ->count();

        $annualTypeIds = LeaveType::where('company_id', $companyId)
            ->where('name', 'like', '%Tahunan%')->pluck('id');

        $sickTypeIds = LeaveType::where('company_id', $companyId)
            ->where(function ($q) {
                $q->where('name', 'like', '%Sakit%')
                    ->orWhere('name', 'like', '%Izin%');
            })->pluck('id');

        $stats = [
            ['label' => 'PENDING APPROVAL HR',  'value' => $pending . ' Pengajuan',      'icon' => 'assignment_late', 'color' => 'text-amber-800'],
            ['label' => 'SEDANG CUTI HARI INI', 'value' => $onLeaveToday . ' Karyawan',  'icon' => 'event_busy',      'color' => 'text-primary'],
            ['label' => 'CUTI TAHUNAN',          'value' => LeaveRequest::forCompany($companyId)->whereIn('leave_type_id', $annualTypeIds)->count() . ' Pengajuan', 'icon' => 'calendar_month',   'color' => 'text-purple-700'],
            ['label' => 'CUTI SAKIT & IZIN',    'value' => LeaveRequest::forCompany($companyId)->whereIn('leave_type_id', $sickTypeIds)->count() . ' Pengajuan',   'icon' => 'medical_services', 'color' => 'text-primary'],
        ];

        $leaveRequests = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType', 'approver'])
            ->forCompany($companyId)
            ->latest()
            ->get();

        $leaveTypes = LeaveType::where('company_id', $companyId)->get();

        return view('hr.persetujuan.cuti', compact('stats', 'leaveRequests', 'leaveTypes'));
    }

    /**
     * HR approve — hanya bisa jika status pending_hr
     * (sudah melewati Supervisor).
     */
    public function approve(Request $request, $id)
    {
        $companyId    = $request->user()->company_id;
        $leaveRequest = LeaveRequest::forCompany($companyId)->findOrFail($id);

        if ($leaveRequest->status !== 'pending_hr') {
            $msg = match($leaveRequest->status) {
                'pending_spv' => 'Pengajuan ini belum disetujui Supervisor.',
                'approved'    => 'Pengajuan ini sudah disetujui sebelumnya.',
                'rejected'    => 'Pengajuan ini sudah ditolak.',
                default       => 'Status pengajuan tidak valid untuk disetujui.',
            };
            return back()->with('error', $msg);
        }

        $leaveType = $leaveRequest->leaveType;

        if ($leaveType && $leaveType->is_quota_based) {
            $year    = Carbon::parse($leaveRequest->start_date)->year;
            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $year)->first();

            if ($balance) {
                $available = ($balance->initial_quota + $balance->carried_forward_quota) - $balance->used_quota;
                if ($available < $leaveRequest->total_days) {
                    return back()->with('error',
                        "Kuota cuti tidak mencukupi. Tersisa {$available} hari, dibutuhkan {$leaveRequest->total_days} hari."
                    );
                }
                $balance->increment('used_quota', $leaveRequest->total_days);
            } else {
                session()->flash('warning',
                    'Persetujuan berhasil, namun saldo cuti karyawan untuk tahun ' . $year .
                    ' belum di-setup. Silakan buat saldo cuti di menu Pengaturan.'
                );
            }
        }

        $leaveRequest->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success',
            "Pengajuan cuti {$leaveRequest->employee->full_name} berhasil disetujui oleh HR."
        );
    }

    /**
     * HR tolak — bisa tolak di tahap pending_hr.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $companyId    = $request->user()->company_id;
        $leaveRequest = LeaveRequest::forCompany($companyId)->findOrFail($id);

        if (!in_array($leaveRequest->status, ['pending_hr', 'pending_spv'])) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $leaveRequest->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success',
            "Pengajuan cuti {$leaveRequest->employee->full_name} berhasil ditolak."
        );
    }
}