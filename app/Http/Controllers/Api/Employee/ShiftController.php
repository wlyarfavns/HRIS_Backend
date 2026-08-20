<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ShiftAssignment;
use App\Models\ShiftSwapRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\GeneralNotification;
use App\Models\User;

class ShiftController extends Controller
{
    public function eligiblePeers(Request $request)
    {
        $employee = $request->user()->employee;

        $peers = Employee::where('company_id', $employee->company_id)
            ->where('supervisor_id', $employee->supervisor_id)
            ->where('id', '!=', $employee->id)
            ->select('id', 'full_name as name')
            ->orderBy('full_name')
            ->get();

        return response()->json(['success' => true, 'data' => $peers]);
    }


    public function index(Request $request)
    {
        $employee = $request->user()->employee;

        $swaps = ShiftSwapRequest::with(['fromEmployee', 'toEmployee', 'fromAssignment.shiftType', 'toAssignment.shiftType'])
            ->where(function ($q) use ($employee) {
                $q->where('from_employee_id', $employee->id)
                    ->orWhere('to_employee_id', $employee->id);
            })
            ->latest()
            ->get()
            ->map(fn(ShiftSwapRequest $r) => [
                'id' => $r->id,
                'from' => $r->fromEmployee->full_name,
                'to' => $r->toEmployee->full_name,
                'from_shift' => $r->fromAssignment->shiftType->name . ' (' . $r->fromAssignment->date->translatedFormat('d M') . ')',
                'to_shift' => $r->toAssignment->shiftType->name . ' (' . $r->toAssignment->date->translatedFormat('d M') . ')',
                'reason' => $r->reason,
                'status' => $r->status,
                'peer_approved' => (bool) $r->peer_approved,
                'is_incoming' => $r->to_employee_id === $employee->id,
                'created_at' => $r->created_at->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => $swaps]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_assignment_id' => 'required|exists:shift_assignments,id',
            'to_employee_id' => 'required|exists:employees,id',
            'to_assignment_id' => 'required|exists:shift_assignments,id',
            'reason' => 'required|string|max:500',
        ]);

        $employee = $request->user()->employee;
        $fromAssignment = ShiftAssignment::findOrFail($request->from_assignment_id);

        if ($fromAssignment->employee_id !== $employee->id) {
            return response()->json(['success' => false, 'message' => 'Jadwal ini bukan milik Anda.'], 403);
        }

        $toAssignment = ShiftAssignment::findOrFail($request->to_assignment_id);
        if ((int) $toAssignment->employee_id !== (int) $request->to_employee_id) {
            return response()->json(['success' => false, 'message' => 'Jadwal tujuan tidak sesuai karyawan yang dipilih.'], 422);
        }

        $conflictForMe = ShiftAssignment::where('employee_id', $employee->id)
            ->where('date', $toAssignment->date)
            ->where('id', '!=', $fromAssignment->id)
            ->exists();

        $conflictForPeer = ShiftAssignment::where('employee_id', $toAssignment->employee_id)
            ->where('date', $fromAssignment->date)
            ->where('id', '!=', $toAssignment->id)
            ->exists();

        if ($conflictForMe) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki jadwal lain di tanggal ' . $toAssignment->date->translatedFormat('d M Y') . ', tidak bisa menukar ke tanggal tersebut.'
            ], 422);
        }

        if ($conflictForPeer) {
            return response()->json([
                'success' => false,
                'message' => 'Rekan yang dipilih sudah memiliki jadwal lain di tanggal ' . $fromAssignment->date->translatedFormat('d M Y') . ', tidak bisa menukar ke tanggal tersebut.'
            ], 422);
        }

        $swap = ShiftSwapRequest::create([
            'company_id' => $employee->company_id,
            'from_employee_id' => $employee->id,
            'to_employee_id' => $request->to_employee_id,
            'from_shift_assignment_id' => $fromAssignment->id,
            'to_shift_assignment_id' => $toAssignment->id,
            'reason' => $request->reason,
            'peer_approved' => true,
            'status' => 'pending_spv',
        ]);

        if ($employee->supervisor) {
            $employee->supervisor->notify(new GeneralNotification(
                'Pengajuan Tukar Shift Baru',
                $employee->full_name . ' mengajukan pertukaran shift dengan ' . ShiftAssignment::find($request->to_assignment_id)->employee->full_name . '.',
                route('supervisor.approvals.shift') 
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan tukar shift terkirim, menunggu persetujuan HR.',
            'data' => $swap,
        ]);
    }























    public function mySchedule(Request $request)
    {
        $me = $request->user()->employee;
        if (!$me) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        if ($request->filled('employee_id')) {
            $employee = Employee::where('id', $request->employee_id)
                ->where('company_id', $me->company_id) 
                ->first();
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan.'], 404);
            }
        } else {
            $employee = $me;
        }

        $start = $request->filled('start') ? Carbon::parse($request->start) : now()->startOfMonth();
        $end = $request->filled('end') ? Carbon::parse($request->end) : now()->endOfMonth();

        $assignments = ShiftAssignment::with('shiftType')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get()
            ->map(fn(ShiftAssignment $a) => [
                'id' => $a->id,
                'date' => $a->date->toDateString(),
                'shift_code' => $a->shiftType->code,
                'shift_name' => $a->shiftType->name,
                'time_label' => $a->shiftType->time_label,
                'color' => $a->shiftType->color,
            ]);

        return response()->json(['success' => true, 'data' => $assignments]);
    }
}
