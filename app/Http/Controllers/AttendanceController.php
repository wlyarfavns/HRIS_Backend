<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * LOGIC NO 27: Menentukan Status Presensi (On-Time, Late, Early Leave)
     */
    public function checkAttendanceStatus(Request $request)
    {
        // Validasi input request dari Flutter
        $request->validate([
            'actual_clock_in' => 'required|date_format:Y-m-d H:i:s',
            'actual_clock_out' => 'required|date_format:Y-m-d H:i:s',
            'expected_clock_in' => 'required|date_format:Y-m-d H:i:s',
            'expected_clock_out' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $actualClockIn = Carbon::parse($request->actual_clock_in);
        $actualClockOut = Carbon::parse($request->actual_clock_out);
        
        $expectedClockIn = Carbon::parse($request->expected_clock_in);
        $expectedClockOut = Carbon::parse($request->expected_clock_out);

        // 1. Cek Status Clock-In (On-Time / Late) dengan toleransi 15 menit
        $toleranceMinutes = 15;
        $lateThreshold = (clone $expectedClockIn)->addMinutes($toleranceMinutes);

        $clockInStatus = $actualClockIn->greaterThan($lateThreshold) ? 'Late' : 'On-Time';

        // 2. Cek Status Clock-Out (On-Time / Early Leave)
        $clockOutStatus = $actualClockOut->lessThan($expectedClockOut) ? 'Early Leave' : 'On-Time';

        return response()->json([
            'success' => true,
            'message' => 'Status presensi berhasil dihitung',
            'data' => [
                'clock_in_status' => $clockInStatus,   // 'On-Time' atau 'Late'
                'clock_out_status' => $clockOutStatus, // 'On-Time' atau 'Early Leave'
            ]
        ]);
    }
}