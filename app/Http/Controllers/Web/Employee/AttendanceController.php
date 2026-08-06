<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'photo'            => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'is_mock_location' => 'required|in:true,false,1,0',
        ]);

        $employee = $request->user()->employee;
        
        // Tarik data Company beserta aturan absensinya
        $company = $employee->company;

        $isMockLocation = filter_var($request->is_mock_location, FILTER_VALIDATE_BOOLEAN);

        // 1. CEK FAKE GPS
        if ($isMockLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Fake GPS terdeteksi. Sistem menolak absensi Anda.'
            ], 403);
        }

        // 2. CEK WAKTU BERDASARKAN ATURAN COMPANY
        $standardStartTime = Carbon::createFromTimeString($company->standard_in_time); 
        $maxTolerableTime = $standardStartTime->copy()->addMinutes($company->late_tolerance_minutes);
        $currentTime = Carbon::now();

        if ($currentTime->greaterThan($maxTolerableTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda terlambat. Batas maksimal adalah ' . $maxTolerableTime->format('H:i') . '. Anda dianggap tidak masuk hari ini.'
            ], 403);
        }

        // 3. KALKULASI JARAK (HAVERSINE)
        $distance = $this->calculateHaversineDistance(
            $company->office_latitude,
            $company->office_longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > $company->geofence_radius_meters) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada ' . round($distance) . 'm dari kantor. Batas radius adalah ' . $company->geofence_radius_meters . 'm.'
            ], 403);
        }

        // 4. CEK ABSEN GANDA HARI INI
        if (Attendance::where('employee_id', $employee->id)->where('date', now()->toDateString())->exists()) {
            return response()->json(['success' => false, 'message' => 'Anda sudah absen hari ini.'], 400);
        }

        // 5. SIMPAN DATA
        $photoPath = $request->file('photo')->store('attendances', 'public');

        $attendance = Attendance::create([
            'company_id'         => $company->id,
            'employee_id'        => $employee->id,
            'date'               => now()->toDateString(),
            'time_in'            => now()->toTimeString(),
            'photo_in'           => $photoPath,
            'latitude_in'        => $request->latitude,
            'longitude_in'       => $request->longitude,
            'distance_in_meters' => round($distance, 2),
            'is_mock_location'   => $isMockLocation,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data'    => $attendance
        ], 201);
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}