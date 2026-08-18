<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SupervisorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();
        $companyId  = $supervisor->company_id;
        $today      = today();


        $team = Employee::with(['department', 'position'])
            ->where('company_id', $companyId)
            ->where('supervisor_id', $supervisor->id)
            ->get();

        $teamIds    = $team->pluck('id');
        $totalTeam  = $team->count();


        $attendanceToday = Attendance::whereIn('employee_id', $teamIds)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('employee_id');



        $leaveTodayByEmployee = LeaveRequest::whereIn('employee_id', $teamIds)
            ->where('status', 'approved')
            ->coveringDate($today->toDateString())
            ->get()
            ->keyBy('employee_id');


        $team = $team->map(function ($member) use ($attendanceToday, $leaveTodayByEmployee) {
            $att   = $attendanceToday->get($member->id);
            $leave = $leaveTodayByEmployee->get($member->id);

            if ($leave) {
                $status = $leave->leaveType->name ?? 'Cuti / Izin';
                $time   = '-';
            } elseif ($att) {
                $status = match ($att->status) {
                    Attendance::STATUS_PRESENT => 'Hadir',
                    Attendance::STATUS_LATE    => 'Terlambat',
                    Attendance::STATUS_PERMIT, Attendance::STATUS_SAKIT => 'Izin / Sakit',
                    default => 'Hadir',
                };
                $time = $att->check_in ? Carbon::parse($att->check_in)->format('H:i') : '-';
            } else {
                $status = 'Belum Presensi';
                $time   = '-';
            }

            return [
                'name'   => $member->full_name,
                'avatar' => $member->id,
                'status' => $status,
                'time'   => $time,
            ];
        });

        $teamBadge = [
            'Hadir'           => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'Terlambat'       => 'bg-amber-50 text-amber-800 border border-amber-200',
            'Izin / Sakit'    => 'bg-violet-50 text-violet-800 border border-violet-200',
            'Cuti Tahunan'    => 'bg-violet-50 text-violet-800 border border-violet-200',
            'Belum Presensi'  => 'bg-rose-50 text-rose-800 border border-rose-200',
        ];


        $hadirHariIni = collect($team)->whereIn('status', ['Hadir', 'Terlambat'])->count();
        $cutiIzin     = collect($team)->whereIn('status', ['Izin / Sakit', 'Cuti Tahunan'])->count();


        $pendingLeave = LeaveRequest::with(['employee', 'leaveType'])
            ->whereIn('employee_id', $teamIds)
            ->where('status', 'pending_spv')
            ->get()
            ->map(fn ($r) => [
                'name'     => $r->employee->full_name ?? '-',
                'avatar'   => $r->employee->id ?? 0,
                'type'     => $r->leaveType->name ?? 'Cuti',
                'detail'   => $r->total_days . ' hari (' . Carbon::parse($r->start_date)->translatedFormat('d M') . '–' . Carbon::parse($r->end_date)->translatedFormat('d M') . ')',
                'route'    => 'supervisor.approvals.leave',
                'sort'     => $r->created_at,
            ]);

        $pendingOvertime = OvertimeRequest::with('employee')
            ->where('company_id', $companyId)
            ->whereIn('employee_id', $teamIds)
            ->where('status', 'pending_spv')
            ->get()
            ->map(fn ($r) => [
                'name'   => $r->employee->full_name ?? '-',
                'avatar' => $r->employee->id ?? 0,
                'type'   => 'Lembur (SPL)',
                'detail' => $r->hours . ' jam · ' . ($r->description ?? Carbon::parse($r->date)->translatedFormat('d M Y')),
                'route'  => 'supervisor.approvals.overtime',
                'sort'   => $r->created_at,
            ]);

        $pendingReimbursement = Reimbursement::with('employee')
            ->whereIn('employee_id', $teamIds)
            ->pendingSpv()
            ->get()
            ->map(fn ($r) => [
                'name'   => $r->employee->full_name ?? '-',
                'avatar' => $r->employee->id ?? 0,
                'type'   => 'Reimbursement',
                'detail' => 'Rp' . number_format($r->amount, 0, ',', '.') . ' · ' . ($r->description ?? '-'),
                'route'  => 'supervisor.approvals.reimbursement',
                'sort'   => $r->created_at,
            ]);

        $pending = $pendingLeave
            ->concat($pendingOvertime)
            ->concat($pendingReimbursement)
            ->sortByDesc('sort')
            ->take(5)
            ->values();

        $totalPending = $pendingLeave->count() + $pendingOvertime->count() + $pendingReimbursement->count();


        $stats = [
            [
                'label' => 'Anggota Tim',
                'value' => $totalTeam . ' Org',
                'icon'  => 'groups',
                'trend' => $team->first()['department'] ?? 'Tim Anda',
            ],
            [
                'label' => 'Hadir Hari Ini',
                'value' => "{$hadirHariIni} / {$totalTeam}",
                'icon'  => 'check_circle',
                'trend' => $totalTeam > 0 ? round(($hadirHariIni / $totalTeam) * 100, 1) . '% Rate' : '0% Rate',
            ],
            [
                'label' => 'Menunggu Persetujuan',
                'value' => (string) $totalPending,
                'icon'  => 'fact_check',
                'trend' => 'Pending SPV',
            ],
            [
                'label' => 'Sedang Cuti / Izin',
                'value' => (string) $cutiIzin,
                'icon'  => 'event_busy',
                'trend' => 'Tercatat',
            ],
        ];


        $weeklyAttendance = $this->buildWeeklyAttendanceTrend($teamIds);


        $slaStats = $this->buildApprovalSla($companyId, $teamIds, $supervisor->id);

        return view('supervisor.dashboard', compact(
            'stats',
            'team',
            'teamBadge',
            'pending',
            'weeklyAttendance',
            'slaStats'
        ));
    }


    private function buildWeeklyAttendanceTrend($teamIds): array
    {
        $start = now()->startOfMonth();
        $weeks = [];

        for ($i = 0; $i < 4; $i++) {
            $weekStart = (clone $start)->addWeeks($i);
            $weekEnd   = (clone $weekStart)->endOfWeek();

            if ($weekStart->greaterThan(now())) {
                break;
            }

            $records = Attendance::whereIn('employee_id', $teamIds)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get();

            $total  = $records->count();
            $hadir  = $records->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count();

            $weeks[] = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
        }

        return $weeks;
    }


    private function buildApprovalSla($companyId, $teamIds, $supervisorId): array
    {
        $since = now()->subDays(30);

        $leaveApproved = LeaveRequest::whereIn('employee_id', $teamIds)
            ->whereIn('status', ['pending_hr', 'approved'])
            ->where('approved_by', $supervisorId)
            ->where('approved_at', '>=', $since)
            ->count();
        $leaveRejected = LeaveRequest::whereIn('employee_id', $teamIds)
            ->where('status', 'rejected')
            ->where('approved_by', $supervisorId)
            ->where('approved_at', '>=', $since)
            ->count();

        $overtimeApproved = OvertimeRequest::where('company_id', $companyId)
            ->whereIn('employee_id', $teamIds)
            ->whereIn('status', ['approved_spv', 'locked'])
            ->where('approved_by', $supervisorId)
            ->where('approved_at', '>=', $since)
            ->count();
        $overtimeRejected = OvertimeRequest::where('company_id', $companyId)
            ->whereIn('employee_id', $teamIds)
            ->where('status', 'rejected')
            ->where('approved_by', $supervisorId)
            ->where('approved_at', '>=', $since)
            ->count();

        $reimbApproved = Reimbursement::whereIn('employee_id', $teamIds)
            ->where('status', Reimbursement::STATUS_PENDING_HR)
            ->where('spv_id', $supervisorId)
            ->where('spv_approved_at', '>=', $since)
            ->count();
        $reimbRejected = Reimbursement::whereIn('employee_id', $teamIds)
            ->where('status', Reimbursement::STATUS_REJECTED)
            ->where('spv_id', $supervisorId)
            ->where('spv_approved_at', '>=', $since)
            ->count();

        $approved = $leaveApproved + $overtimeApproved + $reimbApproved;
        $rejected = $leaveRejected + $overtimeRejected + $reimbRejected;

        $pendingLeave     = LeaveRequest::whereIn('employee_id', $teamIds)->where('status', 'pending_spv')->count();
        $pendingOvertime  = OvertimeRequest::where('company_id', $companyId)->whereIn('employee_id', $teamIds)->where('status', 'pending_spv')->count();
        $pendingReimb     = Reimbursement::whereIn('employee_id', $teamIds)->pendingSpv()->count();
        $pending = $pendingLeave + $pendingOvertime + $pendingReimb;

        $totalDecided = $approved + $rejected;
        $rate = $totalDecided > 0 ? round(($approved / $totalDecided) * 100) : ($pending > 0 ? 0 : 100);

        return [
            'rate'     => $rate,
            'approved' => $approved,
            'pending'  => $pending,
            'rejected' => $rejected,
        ];
    }
}