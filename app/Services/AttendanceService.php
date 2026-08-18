<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceService
{

    public function generateAttendanceCalendar($employee, $startDateStr, $endDateStr)
    {
        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();
        $today = Carbon::now()->startOfDay();

        $attendances = \App\Models\Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()->keyBy('date');

        $leaves = \App\Models\LeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'approved_hr', 'approved_spv'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->get();

        $shifts = \App\Models\ShiftAssignment::with('shiftType')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()->keyBy('date');

        $calendar = collect();

        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            $dateStr = $d->toDateString();

            if ($attendances->has($dateStr)) {
                $att = $attendances->get($dateStr);
                $status = strtolower($att->status) == 'present' ? 'Hadir' : (strtolower($att->status) == 'late' ? 'Terlambat' : ucfirst($att->status));
                $calendar->push([
                    'date' => $dateStr,
                    'time_in' => $att->time_in,
                    'time_out' => $att->time_out ?? null,
                    'status' => $status,
                ]);
                continue;
            }

            $leaveDay = $leaves->first(function ($leave) use ($d) {
                return $d->between(Carbon::parse($leave->start_date)->startOfDay(), Carbon::parse($leave->end_date)->startOfDay());
            });

            if ($leaveDay) {
                $calendar->push([
                    'date' => $dateStr,
                    'time_in' => null,
                    'time_out' => null,
                    'status' => $leaveDay->leaveType->name ?? 'Cuti',
                ]);
                continue;
            }

            $isOff = false;
            if ($shifts->has($dateStr)) {
                $isOff = $shifts->get($dateStr)->shiftType->is_off;
            } else {
                $isOff = $d->isWeekend();
            }

            if ($isOff) {
                if ($d->lte($today)) {
                    $calendar->push([
                        'date' => $dateStr,
                        'time_in' => null,
                        'time_out' => null,
                        'status' => 'Libur',
                    ]);
                }
                continue;
            }

            if ($d->lt($today)) {
                $calendar->push([
                    'date' => $dateStr,
                    'time_in' => null,
                    'time_out' => null,
                    'status' => 'Alpha',
                ]);
            } elseif ($d->isSameDay($today)) {
                $outTimeStr = $shifts->has($dateStr) 
                    ? substr($shifts->get($dateStr)->shiftType->end_time, 0, 8) 
                    : ($employee->company->standard_out_time ?? '17:00:00');

                $outTimeStr = $outTimeStr ?: '17:00:00';
                $outTime = Carbon::createFromTimeString($dateStr . ' ' . $outTimeStr);

                if (now()->greaterThan($outTime)) {
                    $calendar->push([
                        'date' => $dateStr,
                        'time_in' => null,
                        'time_out' => null,
                        'status' => 'Alpha',
                    ]);
                }
            }
        }

        return $calendar->sortByDesc('date')->values();
    }
}
