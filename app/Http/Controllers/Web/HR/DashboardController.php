<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Payroll;
use App\Models\EmployeeContract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $now       = now();
        $today     = $now->toDateString();


        $totalEmployees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $newEmployees = Employee::where('company_id', $companyId)
            ->where('join_date', '>=', $now->copy()->subDays(30))
            ->count();

        $pendingLeave = LeaveRequest::forCompany($companyId)
            ->where('status', 'pending_hr')
            ->count();

        $expiringContracts = EmployeeContract::whereHas('employee', fn ($q) =>
                $q->where('company_id', $companyId))
            ->where('contract_type', 'PKWT')
            ->where('status', 'Active')
            ->whereBetween('end_date', [$now, $now->copy()->addDays(30)])
            ->count();




        $performanceData = collect();
        $performanceLabels = collect();

        for ($i = 5; $i >= 0; $i--) {
            $month     = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth()->toDateString();
            $monthEnd   = $month->copy()->endOfMonth()->toDateString();

            $total = Attendance::where('company_id', $companyId)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->count();



            $onTime = Attendance::where('company_id', $companyId)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where(fn ($q) => $q->where('late_minutes', 0)->orWhereNull('late_minutes'))
                ->count();

            $pct = $total > 0 ? round($onTime / $total * 100, 2) : 0;

            $performanceLabels->push($month->translatedFormat('M'));
            $performanceData->push($pct);
        }


        $avgProductivity = $performanceData->avg()
            ? number_format($performanceData->avg(), 2)
            : '0.00';


        $perfChange = $performanceData->count() >= 2
            ? round($performanceData->last() - $performanceData->slice(-2, 1)->first(), 2)
            : 0;


        $attendanceDays    = collect();
        $onTimeByDay       = collect();
        $lateByDay         = collect();
        $absentByDay       = collect();


        $activeCount = $totalEmployees ?: 1;

        for ($i = 19; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);

            if ($day->isWeekend()) {
                continue;
            }

            $dayStr = $day->toDateString();

            $dayTotal = Attendance::where('company_id', $companyId)
                ->where('date', $dayStr)
                ->count();

            $dayOnTime = Attendance::where('company_id', $companyId)
                ->where('date', $dayStr)
                ->where(fn ($q) => $q->where('late_minutes', 0)->orWhereNull('late_minutes'))
                ->count();

            $dayLate = Attendance::where('company_id', $companyId)
                ->where('date', $dayStr)
                ->where('late_minutes', '>', 0)
                ->count();


            $attendanceDays->push($day->format('d'));
            $onTimeByDay->push($activeCount > 0 ? round($dayOnTime / $activeCount * 100) : 0);
            $lateByDay->push($activeCount > 0 ? round($dayLate / $activeCount * 100) : 0);

            $absentByDay->push($activeCount > 0 ? max(0, 100 - round($dayTotal / $activeCount * 100)) : 0);
        }


        $currentMonthStart = $now->copy()->startOfMonth()->toDateString();
        $currentMonthEnd   = $now->copy()->endOfMonth()->toDateString();

        $totalExpected = Attendance::where('company_id', $companyId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $totalPresent  = Attendance::where('company_id', $companyId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->whereNotNull('time_in')
            ->count();
        $attendanceRate = $totalExpected > 0
            ? round($totalPresent / $totalExpected * 100)
            : 0;



        $leaveBreakdown = LeaveRequest::forCompany($companyId)
            ->join('leave_types', 'leave_requests.leave_type_id', '=', 'leave_types.id')
            ->select('leave_types.name as type_name', DB::raw('COUNT(*) as total'))
            ->groupBy('leave_types.name')
            ->orderByDesc('total')
            ->limit(4) 
            ->get();

        $leaveTotal    = $leaveBreakdown->sum('total') ?: 1;
        $leaveLabels   = $leaveBreakdown->pluck('type_name');
        $leaveCounts   = $leaveBreakdown->pluck('total');
        $leavePercents = $leaveCounts->map(fn ($c) => round($c / $leaveTotal * 100));
        $leaveTotalAll = LeaveRequest::forCompany($companyId)->count();


        $deptDistribution = Department::where('company_id', $companyId)
            ->withCount(['employees' => fn ($q) => $q->where('status', 'active')])
            ->orderByDesc('employees_count')
            ->limit(5) 
            ->get();

        $deptLabels = $deptDistribution->pluck('name');
        $deptCounts = $deptDistribution->pluck('employees_count');


        $pendingOvertimeHr = OvertimeRequest::where('company_id', $companyId)
            ->where('status', 'approved_spv')
            ->count();

        $pendingReimbursement = \App\Models\Reimbursement::where('company_id', $companyId)
            ->where('status', 'pending_hr')
            ->count();


        $recentLeaves = LeaveRequest::with(['employee', 'leaveType'])
            ->forCompany($companyId)
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($l) => [
                'icon'  => 'event_busy',
                'color' => 'text-amber-600',
                'bg'    => 'bg-amber-50',
                'label' => "{$l->employee->full_name} — Cuti {$l->leaveType->name}",
                'sub'   => ucfirst($l->status) . ' • ' . Carbon::parse($l->updated_at)->diffForHumans(),
            ]);

        $recentPayrolls = Payroll::with('employee')
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->limit(3)
            ->get()
            ->map(fn ($p) => [
                'icon'  => 'payments',
                'color' => 'text-primary',
                'bg'    => 'bg-emerald-50',
                'label' => "Payroll {$p->employee->full_name}",
                'sub'   => Carbon::parse($p->period_start)->translatedFormat('M Y') . ' • ' . ucfirst($p->status),
            ]);

        $recentActivity = $recentLeaves->merge($recentPayrolls)
            ->sortByDesc('sub')
            ->take(6)
            ->values();

        return view('hr.dashboard', compact(

            'totalEmployees',
            'newEmployees',
            'pendingLeave',
            'expiringContracts',

            'performanceLabels',
            'performanceData',
            'avgProductivity',
            'perfChange',

            'attendanceDays',
            'onTimeByDay',
            'lateByDay',
            'absentByDay',
            'attendanceRate',

            'leaveLabels',
            'leaveCounts',
            'leavePercents',
            'leaveTotalAll',

            'deptLabels',
            'deptCounts',
            'totalEmployees',

            'pendingOvertimeHr',
            'pendingReimbursement',

            'recentActivity',
        ));
    }
}