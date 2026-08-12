<?php

namespace App\Http\Controllers\Web\HR;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{

    public function checkAttendanceStatus(Request $request)
    {
        $request->validate([
            'shift_id' => 'nullable|exists:shifts,id',
            'type' => 'nullable|in:clock_in,clock_out',
            'actual_clock_in' => 'nullable|date_format:Y-m-d H:i:s',
            'actual_clock_out' => 'nullable|date_format:Y-m-d H:i:s',
            'expected_clock_in' => 'nullable|date_format:Y-m-d H:i:s',
            'expected_clock_out' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $now = now();
        $clockInStatus = null;
        $clockOutStatus = null;

        if ($request->has('shift_id') && $request->has('type')) {
            $shift = \App\Models\Shift::findOrFail($request->shift_id);
            $expectedTimeStr = $request->type === 'clock_in' ? $shift->start_time : $shift->end_time;
            $expectedTime = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $expectedTimeStr);
            if ($request->type === 'clock_out' && $shift->is_cross_day) {
                $expectedTime->addDay();
            }

            if ($request->type === 'clock_in') {
                $lateThreshold = (clone $expectedTime)->addMinutes(15);
                $clockInStatus = $now->greaterThan($lateThreshold) ? 'Late' : 'On-Time';
            } else {
                $clockOutStatus = $now->lessThan($expectedTime) ? 'Early Leave' : 'On-Time';
            }
        } else {
            $actualClockIn = $request->actual_clock_in ? Carbon::parse($request->actual_clock_in) : null;
            $actualClockOut = $request->actual_clock_out ? Carbon::parse($request->actual_clock_out) : null;
            
            $expectedClockIn = $request->expected_clock_in ? Carbon::parse($request->expected_clock_in) : null;
            $expectedClockOut = $request->expected_clock_out ? Carbon::parse($request->expected_clock_out) : null;

            if ($actualClockIn && $expectedClockIn) {
                $lateThreshold = (clone $expectedClockIn)->addMinutes(15);
                $clockInStatus = $actualClockIn->greaterThan($lateThreshold) ? 'Late' : 'On-Time';
            }

            if ($actualClockOut && $expectedClockOut) {
                $clockOutStatus = $actualClockOut->lessThan($expectedClockOut) ? 'Early Leave' : 'On-Time';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status presensi berhasil dihitung',
            'data' => [
                'clock_in_status' => $clockInStatus ?? 'N/A',
                'clock_out_status' => $clockOutStatus ?? 'N/A',
                'server_time' => $now->format('Y-m-d H:i:s')
            ]
        ]);
    }
}