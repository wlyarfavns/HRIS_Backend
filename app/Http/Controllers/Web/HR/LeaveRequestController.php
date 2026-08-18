<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;

class LeaveRequestController extends Controller
{

    public function store(StoreLeaveRequest $request)
    {
        $validated = $request->validated();

        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Profil karyawan tidak ditemukan untuk akun ini.'
            ], 404);
        }


        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'] ?? null,
            'attachment' => $attachmentPath,
            'status' => 'pending_spv', 
        ]);

        if ($employee->supervisor) {
            $employee->supervisor->notify(new GeneralNotification(
                'Pengajuan Cuti Baru',
                $employee->full_name . ' mengajukan cuti.',
                '/supervisor/persetujuan/cuti'
            ));
        }

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan Supervisor.',
            'data' => $leaveRequest,
        ], 201);
    }


    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('hr')) {
            $companyId = $user->company_id;

            $requests = LeaveRequest::with('leaveType', 'employee')
                ->whereHas('employee', fn($q) => $q->where('company_id', $companyId))
                ->latest()->get();

            $balances = LeaveBalance::with('leaveType', 'employee')
                ->whereHas('employee', fn($q) => $q->where('company_id', $companyId))
                ->get();

            return response()->json(['balances' => $balances, 'requests' => $requests]);
        }

        $employee = $user->employee;
        if (!$employee) {
            return response()->json([
                'message' => 'Profil karyawan tidak ditemukan untuk akun ini.'
            ], 404);
        }

        $requests = LeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id)
            ->latest()->get();

        $balances = LeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)->get();

        return response()->json(['balances' => $balances, 'requests' => $requests]);
    }


    public function approve(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasRole('hr')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $companyId = $user->company_id;
        $leaveRequest = LeaveRequest::whereHas('employee', fn($q) => $q->where('company_id', $companyId))
            ->findOrFail($id);

        if ($leaveRequest->status !== 'pending_hr') {
            return response()->json([
                'message' => 'Pengajuan belum disetujui Supervisor atau sudah diproses.'
            ], 400);
        }

        $leaveType = $leaveRequest->leaveType;

        if ($leaveType && $leaveType->is_quota_based) {
            $year = Carbon::parse($leaveRequest->start_date)->year;
            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $year)->first();

            if (!$balance) {
                return response()->json(['message' => 'Saldo cuti tidak ditemukan untuk tahun ini.'], 400);
            }

            $available = ($balance->initial_quota + $balance->carried_forward_quota) - $balance->used_quota;

            if ($available < $leaveRequest->total_days) {
                return response()->json(['message' => "Kuota cuti tidak mencukupi. Tersisa {$available} hari."], 400);
            }

            $balance->increment('used_quota', $leaveRequest->total_days);
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti disetujui.',
            'data' => $leaveRequest,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Data karyawan tidak ditemukan.');

        $leaveRequest = LeaveRequest::where('employee_id', $employee->id)->find($id);

        if (!$leaveRequest) {
            return response()->json([
                'message' => 'Pengajuan cuti tidak ditemukan atau sudah dibatalkan.'
            ], 404);
        }

        if ($leaveRequest->status !== 'pending_spv') {
            return response()->json([
                'message' => 'Pengajuan yang sudah diproses tidak bisa dibatalkan.'
            ], 422);
        }

        $leaveRequest->delete();

        return response()->json(['message' => 'Pengajuan cuti dibatalkan.']);
    }
}