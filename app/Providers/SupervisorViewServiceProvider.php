<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SupervisorViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Composer ini jalan setiap kali layouts.supervisor dirender —
        // jadi badge notifikasi & info user di sidebar/topbar selalu up-to-date
        // di SEMUA halaman supervisor, bukan cuma di dashboard.
        View::composer('layouts.supervisor', function ($view) {
            $user = Auth::user();

            if (! $user) {
                $view->with([
                    'sidebarLeaveBadge'         => 0,
                    'sidebarOvertimeBadge'      => 0,
                    'sidebarReimbursementBadge' => 0,
                    'sidebarPendingTotal'       => 0,
                ]);
                return;
            }

            $teamIds = Employee::where('company_id', $user->company_id)
                ->where('supervisor_id', $user->id)
                ->pluck('id');

            $leaveCount = LeaveRequest::whereIn('employee_id', $teamIds)
                ->where('status', 'pending_spv')
                ->count();

            $overtimeCount = OvertimeRequest::where('company_id', $user->company_id)
                ->whereIn('employee_id', $teamIds)
                ->where('status', 'pending_spv')
                ->count();

            $reimbursementCount = Reimbursement::whereIn('employee_id', $teamIds)
                ->pendingSpv()
                ->count();

            $view->with([
                'sidebarLeaveBadge'         => $leaveCount,
                'sidebarOvertimeBadge'      => $overtimeCount,
                'sidebarReimbursementBadge' => $reimbursementCount,
                'sidebarPendingTotal'       => $leaveCount + $overtimeCount + $reimbursementCount,
            ]);
        });
    }
}