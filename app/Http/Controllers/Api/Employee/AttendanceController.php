<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\AttendanceStatusService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function today(Request $request)
    {
        $employee = $request->user()->employee;
        $today = now()->toDateString();
        $company = $employee->company;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();


        $assignment = \App\Models\ShiftAssignment::with('shiftType')
            ->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();


        $shiftData = null;
        if ($assignment && $assignment->shiftType) {
            $shiftData = [
                'name' => $assignment->shiftType->name,
                'start_time' => substr($assignment->shiftType->start_time, 0, 5),
                'end_time' => substr($assignment->shiftType->end_time, 0, 5),
                'is_off' => (bool) $assignment->shiftType->is_off,
            ];
        } else {
            $shiftData = [
                'name' => 'Jam Kerja Standar',
                'start_time' => substr($company->standard_in_time, 0, 5),
                'end_time' => substr($company->standard_out_time, 0, 5),
                'is_off' => false,
            ];
        }

        $leave = null;
        $isPastTimeLimit = false;

        if (!$attendance) {
            $leave = LeaveRequest::with('leaveType')
                ->where('employee_id', $employee->id)
                ->coveringDate($today)
                ->where('status', 'approved')
                ->first();


            if (!$leave && !($shiftData['is_off'] ?? false)) {
                $standardInTimeStr = $assignment
                    ? substr($assignment->shiftType->start_time, 0, 8)
                    : $company->standard_in_time;

                $standardStartTime = Carbon::createFromTimeString($today . ' ' . $standardInTimeStr);
                $maxTolerableTime = $standardStartTime->copy()->addMinutes($company->late_tolerance_minutes);

                if (now()->greaterThan($maxTolerableTime)) {
                    $isPastTimeLimit = true;


                    $attendance = Attendance::create([
                        'company_id' => $company->id,
                        'employee_id' => $employee->id,
                        'shift_assignment_id' => $assignment?->id,
                        'date' => $today,
                        'time_in' => null,
                        'time_out' => null,
                        'status' => Attendance::STATUS_ABSENT, 
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status absensi hari ini.',
            'data' => $attendance,
            'leave' => $leave,
            'is_past_time_limit' => $isPastTimeLimit,
            'shift' => $shiftData,
        ]);
    }
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

        $isMockLocation = filter_var($request->is_mock_location, FILTER_VALIDATE_BOOLEAN);

        if ($isMockLocation) {
            return response()->json(['success' => false, 'message' => 'Fake GPS terdeteksi.'], 403);
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereNull('time_out')
            ->whereIn('date', [now()->toDateString(), now()->subDay()->toDateString()])
            ->latest('date')
            ->first();

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Anda belum melakukan absen masuk.'], 400);
        }

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
        $company = $employee->company;
        $today = now()->toDateString();

        $isMockLocation = filter_var($request->is_mock_location, FILTER_VALIDATE_BOOLEAN);

        if ($isMockLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Fake GPS terdeteksi. Sistem menolak absensi Anda.'
            ], 403);
        }

        $assignment = \App\Models\ShiftAssignment::with('shiftType')
            ->where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($assignment && $assignment->shiftType->is_off) {
            return response()->json([
                'success' => false,
                'message' => 'Hari ini Anda dijadwalkan libur, tidak perlu absen.'
            ], 403);
        }


        $standardInTimeStr = $assignment
            ? substr($assignment->shiftType->start_time, 0, 8)
            : $company->standard_in_time;

        $standardStartTime = Carbon::createFromTimeString($today . ' ' . $standardInTimeStr);
        $maxTolerableTime = $standardStartTime->copy()->addMinutes($company->late_tolerance_minutes);
        $currentTime = Carbon::now();

        if ($currentTime->greaterThan($maxTolerableTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda terlambat. Batas maksimal adalah ' . $maxTolerableTime->format('H:i') . '. Anda dianggap tidak masuk hari ini.'
            ], 403);
        }

        $isLate = $currentTime->greaterThan($standardStartTime);
        $status = $isLate ? Attendance::STATUS_LATE : Attendance::STATUS_PRESENT;
        $lateMinutes = $isLate ? $standardStartTime->diffInMinutes($currentTime) : 0;

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

        if (Attendance::where('employee_id', $employee->id)->where('date', $today)->exists()) {
            return response()->json(['success' => false, 'message' => 'Anda sudah absen hari ini.'], 400);
        }

        $photoPath = $request->file('photo')->store('attendances', 'public');

        $attendance = Attendance::create([
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'shift_assignment_id' => $assignment?->id, 
            'date' => $today,
            'time_in' => now()->toTimeString(),
            'photo_in' => $photoPath,
            'latitude_in' => $request->latitude,
            'longitude_in' => $request->longitude,
            'distance_in_meters' => round($distance, 2),
            'is_mock_location' => $isMockLocation,
            'status' => $status,
            'late_minutes' => $lateMinutes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data' => $attendance
        ], 201);
    }

    public function submitLeave(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $employee = $request->user()->employee;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin/sakit berhasil dikirim, menunggu persetujuan HR.',
            'data' => $leave,
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


    public function summary(Request $request)
    {
        $employee = $request->user()->employee;


        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        $weeklyCalendar = (new \App\Services\AttendanceService())->generateAttendanceCalendar($employee, $startOfWeek, $endOfWeek);


        $currentYear = now()->year;
        $leaveBalances = \App\Models\LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->get();

        $leaveQuota = $leaveBalances->sum(fn($b) => $b->initial_quota + $b->carried_forward_quota);
        $leaveUsed = $leaveBalances->sum('used_quota');
        $leaveBalance = max($leaveQuota - $leaveUsed, 0);


        $overtimeHours = 0;
        if (class_exists(\App\Models\OvertimeRequest::class)) {
            $overtimeHours = \App\Models\OvertimeRequest::where('employee_id', $employee->id)
                ->whereIn('status', ['approved_spv', 'locked']) 
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('hours');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'present' => $weeklyCalendar->whereIn('status', ['Hadir', 'Terlambat'])->count(),
                'late' => $weeklyCalendar->where('status', 'Terlambat')->count(),
                'permit' => $weeklyCalendar->where('status', 'Izin Pribadi')->count(),
                'sick' => $weeklyCalendar->where('status', 'Sakit')->count(),
                'alpha' => $weeklyCalendar->where('status', 'Alpha')->count(),
                'leave_balance' => $leaveBalance,
                'overtime_hours' => $overtimeHours,
            ]
        ], 200);
    }


    public function checkAttendanceStatus(Request $request, AttendanceStatusService $service)
    {
        $request->validate([
            'shift_id' => 'nullable|exists:shifts,id',
            'type' => 'nullable|in:clock_in,clock_out',
            'actual_clock_in' => 'nullable|date_format:Y-m-d H:i:s',
            'actual_clock_out' => 'nullable|date_format:Y-m-d H:i:s',
            'expected_clock_in' => 'nullable|date_format:Y-m-d H:i:s',
            'expected_clock_out' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status presensi berhasil dihitung',
            'data' => $service->resolve($request->all()),
        ]);
    }

    public function history(Request $request)
    {
        $employee = $request->user()->employee;

        $month = $request->filled('month')
            ? Carbon::parse($request->month . '-01')
            : Carbon::now();

        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $history = (new \App\Services\AttendanceService())->generateAttendanceCalendar($employee, $start, $end);

        return response()->json([
            'success' => true,
            'data' => $history->toArray(),
            'meta' => [
                'month' => $month->format('Y-m'),
                'days_in_month' => $month->daysInMonth,
            ],
        ]);
    }

    public function statistics(Request $request)
    {
        $employee = $request->user()->employee;
        $currentYear = now()->year;
        $currentMonth = now()->month;


        $leaveBalances = \App\Models\LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->get();
        $leaveQuota = $leaveBalances->sum(fn($b) => $b->initial_quota + $b->carried_forward_quota);
        $leaveUsed = $leaveBalances->sum('used_quota');
        $leaveBalance = max($leaveQuota - $leaveUsed, 0);


        $overtimeThisMonth = 0;
        if (class_exists(\App\Models\OvertimeRequest::class)) {
            $overtimeThisMonth = \App\Models\OvertimeRequest::where('employee_id', $employee->id)
                ->whereIn('status', ['approved_spv', 'locked'])
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $currentMonth)
                ->sum('hours');
        }


        $startYear = Carbon::create($currentYear, 1, 1)->toDateString();
        $endYear = Carbon::create($currentYear, 12, 31)->toDateString();
        $yearlyCalendar = (new \App\Services\AttendanceService())->generateAttendanceCalendar($employee, $startYear, $endYear);

        $thisMonthCalendar = $yearlyCalendar->filter(fn($c) => Carbon::parse($c['date'])->month == $currentMonth);
        $lateThisMonth = $thisMonthCalendar->where('status', 'Terlambat')->count();
        $presentThisMonth = $thisMonthCalendar->whereIn('status', ['Hadir', 'Terlambat'])->count();


        $monthlyStats = [];
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyAttendances = $yearlyCalendar->filter(fn($c) => Carbon::parse($c['date'])->month == $i);
            $hadir = $monthlyAttendances->whereIn('status', ['Hadir', 'Terlambat'])->count();
            $telat = $monthlyAttendances->where('status', 'Terlambat')->count();
            $izin = $monthlyAttendances->where('status', 'Izin Pribadi')->count();
            $sakit = $monthlyAttendances->where('status', 'Sakit')->count();

            $cuti = $monthlyAttendances->whereNotIn('status', ['Hadir', 'Terlambat', 'Alpha', 'Libur', 'Izin Pribadi', 'Sakit'])->count();

            $monthlyStats[] = [
                'month' => $months[$i - 1],
                'hadir' => $hadir,
                'terlambat' => $telat,
                'cuti' => $cuti + $izin + $sakit, 
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'dashboard' => [
                    'sisa_cuti' => $leaveBalance,
                    'lembur_jam' => $overtimeThisMonth,
                    'terlambat_kali' => $lateThisMonth,
                ],
                'detail' => [
                    'kehadiran_hari' => $presentThisMonth,
                    'terlambat_kali' => $lateThisMonth,
                    'sisa_cuti' => $leaveBalance,
                    'lembur_jam' => $overtimeThisMonth,
                    'bulanan' => $monthlyStats,
                ]
            ]
        ], 200);
    }
}
