<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    /**
     * Karyawan ajukan cuti dari mobile → langsung pending_spv
     * (menunggu persetujuan Supervisor terlebih dahulu).
     */
    public function store(StoreLeaveRequest $request)
    {
        $validated = $request->validated();

        $employee = Auth::user()->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Profil karyawan tidak ditemukan untuk akun ini.'
            ], 404);
        }

        // Hitung total_days di server — jangan percaya nilai dari client
        $start     = Carbon::parse($validated['start_date']);
        $end       = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $leaveRequest = LeaveRequest::create([
            'employee_id'   => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['end_date'],
            'total_days'    => $totalDays,
            'reason'        => $validated['reason'] ?? null,
            'status'        => 'pending_spv', // ← Masuk ke antrian Supervisor dulu
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan Supervisor.',
            'data'    => $leaveRequest,
        ], 201);
    }

    /**
     * Index — HR lihat semua, karyawan lihat miliknya sendiri.
     */
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

    /**
     * API approve — dipakai HR via API (mobile HR / postman).
     * Hanya bisa approve jika sudah di tahap pending_hr.
     */
    public function approve(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasRole('hr')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $companyId    = $user->company_id;
        $leaveRequest = LeaveRequest::whereHas('employee', fn($q) => $q->where('company_id', $companyId))
            ->findOrFail($id);

        if ($leaveRequest->status !== 'pending_hr') {
            return response()->json([
                'message' => 'Pengajuan belum disetujui Supervisor atau sudah diproses.'
            ], 400);
        }

        $leaveType = $leaveRequest->leaveType;

        if ($leaveType && $leaveType->is_quota_based) {
            $year    = Carbon::parse($leaveRequest->start_date)->year;
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
            'status'      => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti disetujui.',
            'data'    => $leaveRequest,
        ]);
    }
}