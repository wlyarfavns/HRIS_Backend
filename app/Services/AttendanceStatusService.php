<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Shift;

class AttendanceStatusService
{
    public function resolve(array $data): array
    {
        $now = now();
        $clockInStatus = null;
        $clockOutStatus = null;

        if (!empty($data['shift_id']) && !empty($data['type'])) {
            $shift = Shift::findOrFail($data['shift_id']);
            $expectedTimeStr = $data['type'] === 'clock_in' ? $shift->start_time : $shift->end_time;
            $expectedTime = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $expectedTimeStr);

            if ($data['type'] === 'clock_out' && $shift->is_cross_day) {
                $expectedTime->addDay();
            }

            if ($data['type'] === 'clock_in') {
                $lateThreshold = (clone $expectedTime)->addMinutes(15);
                $clockInStatus = $now->greaterThan($lateThreshold) ? 'Late' : 'On-Time';
            } else {
                $clockOutStatus = $now->lessThan($expectedTime) ? 'Early Leave' : 'On-Time';
            }
        } else {
            $actualClockIn  = !empty($data['actual_clock_in'])  ? Carbon::parse($data['actual_clock_in'])  : null;
            $actualClockOut = !empty($data['actual_clock_out']) ? Carbon::parse($data['actual_clock_out']) : null;
            $expectedClockIn  = !empty($data['expected_clock_in'])  ? Carbon::parse($data['expected_clock_in'])  : null;
            $expectedClockOut = !empty($data['expected_clock_out']) ? Carbon::parse($data['expected_clock_out']) : null;

            if ($actualClockIn && $expectedClockIn) {
                $lateThreshold = (clone $expectedClockIn)->addMinutes(15);
                $clockInStatus = $actualClockIn->greaterThan($lateThreshold) ? 'Late' : 'On-Time';
            }

            if ($actualClockOut && $expectedClockOut) {
                $clockOutStatus = $actualClockOut->lessThan($expectedClockOut) ? 'Early Leave' : 'On-Time';
            }
        }

        return [
            'clock_in_status'  => $clockInStatus ?? 'N/A',
            'clock_out_status' => $clockOutStatus ?? 'N/A',
            'server_time'      => $now->format('Y-m-d H:i:s'),
        ];
    }
}