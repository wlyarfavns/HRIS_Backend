<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceStatusController extends Controller
{
    /**
     * LOGIC NO 27: Menentukan Status Presensi (On-Time, Late, Early Leave)
     */
    public function determineAttendanceStatus(Request $request)
    {
        // Contoh data yang dikirim dari aplikasi Flutter / request
        $actualClockIn = Carbon::parse($request->actual_clock_in);   // Waktu aktual karyawan scan masuk
        $actualClockOut = Carbon::parse($request->actual_clock_out); // Waktu aktual karyawan scan keluar
        
        $expectedClockIn = Carbon::parse($request->expected_clock_in);   // Jadwal masuk shift
        $expectedClockOut = Carbon::parse($request->expected_clock_out); // Jadwal keluar shift

        // 1. Cek Status Clock-In (On-Time atau Late)
        // Toleransi keterlambatan misalnya 15 menit (bisa disesuaikan)
        $toleranceMinutes = 15;
        $lateThreshold = (clone $expectedClockIn)->addMinutes($toleranceMinutes);

        if ($actualClockIn->greaterThan($lateThreshold)) {
            $clockInStatus = 'Late';
        } else {
            $clockInStatus = 'On-Time';
        }

        // 2. Cek Status Clock-Out (On-Time atau Early Leave)
        if ($actualClockOut->lessThan($expectedClockOut)) {
            $clockOutStatus = 'Early Leave';
        } else {
            $clockOutStatus = 'On-Time';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'clock_in_status' => $clockInStatus,   // Nilai: On-Time / Late
                'clock_out_status' => $clockOutStatus, // Nilai: On-Time / Early Leave
            ]
        ]);
    }
}