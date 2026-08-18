<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {

        $user = $request->user();



        $filterMonth = $request->input('month', now()->format('Y-m'));
        $parsedDate = Carbon::parse($filterMonth);
        $year = $parsedDate->year;
        $month = $parsedDate->month;


        $team = Employee::where('supervisor_id', $user->id)
            ->paginate(10)
            ->withQueryString();


        $teamRecap = $team->through(function ($member) use ($year, $month) {

            $startOfMonth = Carbon::create($year, $month, 1)->toDateString();
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

            $attendances = (new \App\Services\AttendanceService())->generateAttendanceCalendar($member, $startOfMonth, $endOfMonth);

            $tepatWaktu = $attendances->where('status', 'Hadir')->count();
            $terlambat = $attendances->where('status', 'Terlambat')->count();


            $izin = $attendances->filter(fn($a) => stripos($a['status'], 'izin') !== false)->count();
            $sakit = $attendances->filter(fn($a) => stripos($a['status'], 'sakit') !== false || stripos($a['status'], 'sick') !== false)->count();
            $cuti = $attendances->filter(function($a) {
                $s = strtolower($a['status']);
                return !in_array($s, ['hadir', 'terlambat', 'alpha', 'libur']) && strpos($s, 'izin') === false && strpos($s, 'sakit') === false;
            })->count();

            $alpha = $attendances->where('status', 'Alpha')->count();
            $libur = $attendances->where('status', 'Libur')->count();


            $hadirTotal = $tepatWaktu + $terlambat;


            $totalDays = Carbon::create($year, $month, 1)->daysInMonth;
            $totalWorkingDays = max($totalDays - $libur, 1); 


            $persentase = round(($hadirTotal / $totalWorkingDays) * 100);
            $persentase = $persentase > 100 ? 100 : $persentase;

            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'avatar' => $member->id, 
                'hadir' => $hadirTotal,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'cuti' => $cuti,
                'alpha' => $alpha,
                'persentase' => $persentase
            ];
        });

        return view('supervisor.laporan.index', compact('teamRecap', 'filterMonth'));
    }
}