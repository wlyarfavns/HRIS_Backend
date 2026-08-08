<?php

namespace App\Http\Controllers\Mobile\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Mengecek status absensi karyawan hari ini
     */
    public function today(Request $request)
    {
        $employee = $request->user()->employee;
        $today = now()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Status absensi hari ini.',
            'data' => $attendance 
        ]);
    }

    /**
     * Proses Absen Pulang
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'is_mock_location' => 'required|in:true,false,1,0',
        ]);

        $employee = $request->user()->employee;
        $company = $employee->company;
        $today = now()->toDateString();

        $isMockLocation = filter_var($request->is_mock_location, FILTER_VALIDATE_BOOLEAN);

        // 1. CEK FAKE GPS
        if ($isMockLocation) {
            return response()->json(['success' => false, 'message' => 'Fake GPS terdeteksi.'], 403);
        }

        // 2. CARI DATA ABSEN MASUK HARI INI
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Anda belum melakukan absen masuk hari ini.'], 400);
        }

        if ($attendance->time_out !== null) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen pulang hari ini.'], 400);
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

        // 4. SIMPAN DATA ABSEN PULANG
        $photoPath = $request->file('photo')->store('attendances/checkout', 'public');

        $attendance->update([
            'time_out' => now()->toTimeString(),
            'photo_out' => $photoPath,
            'latitude_out' => $request->latitude,
            'longitude_out' => $request->longitude,
            'distance_out_meters' => round($distance, 2),
            'is_mock_location_out' => $isMockLocation, 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil dicatat.',
            'data' => $attendance
        ], 200);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => now()->toTimeString(),
            'photo_in' => $photoPath,
            'latitude_in' => $request->latitude,
            'longitude_in' => $request->longitude,
            'distance_in_meters' => round($distance, 2),
            'is_mock_location' => $isMockLocation,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data' => $attendance
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

    /**
     * Mengambil ringkasan absensi minggu ini (Senin - Minggu)
     */
    public function summary(Request $request)
    {
        $employee = $request->user()->employee;
        
        // Ambil awal dan akhir minggu ini menggunakan Carbon
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        // Tarik data absen dalam rentang waktu tersebut
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'present' => $attendances->where('status', Attendance::STATUS_PRESENT)->count(),
                'late'    => $attendances->where('status', Attendance::STATUS_LATE)->count(),
                'permit'  => $attendances->where('status', Attendance::STATUS_PERMIT)->count(),
                'sick'    => $attendances->where('status', Attendance::STATUS_SAKIT)->count(),
            ]
        ], 200);
    }

    /**
     * Mengambil riwayat absensi terbaru (Limit 10)
     */
    public function history(Request $request)
    {
        $employee = $request->user()->employee;

        $history = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get(['date', 'time_in', 'time_out', 'status']); 

        return response()->json([
            'success' => true,
            'data' => $history
        ], 200);
    }
}