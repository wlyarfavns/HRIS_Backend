<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;

class LeaveRequestController extends Controller
{
    /**
     * Submit a new leave request (Task 32: Validasi Min/Max)
     */
    public function store(StoreLeaveRequest $request)
    {
        $validated = $request->validated();

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $validated['employee_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $validated['total_days'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Leave request submitted successfully.',
            'data' => $leaveRequest
        ], 201);
    }

    /**
     * Approve a leave request (Task 31: Kalkulasi Potong Kuota)
     */
    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status === 'approved') {
            return response()->json(['message' => 'Already approved.'], 400);
        }

        $leaveType = $leaveRequest->leaveType;
        
        // Calculate and deduct quota if quota based
        if ($leaveType->is_quota_based) {
            $year = date('Y', strtotime($leaveRequest->start_date));
            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $year)
                ->first();

            if (!$balance) {
                return response()->json(['message' => 'Leave balance not found for this year.'], 400);
            }

            $availableQuota = ($balance->initial_quota + $balance->carried_forward_quota) - $balance->used_quota;
            
            if ($availableQuota < $leaveRequest->total_days) {
                return response()->json(['message' => 'Insufficient leave quota.'], 400);
            }

            // Deduct quota
            $balance->used_quota += $leaveRequest->total_days;
            $balance->save();
        }

        $leaveRequest->status = 'approved';
        $leaveRequest->save();

        return response()->json([
            'message' => 'Leave request approved successfully. Quota deducted.',
            'data' => $leaveRequest
        ]);
    }

    /**
     * Get all leave balances and requests (for testing/checking)
     */
    public function index()
    {
        $balances = LeaveBalance::with('leaveType')->get();
        $requests = LeaveRequest::with('leaveType')->get();

        return response()->json([
            'balances' => $balances,
            'requests' => $requests
        ]);
    }
}
