<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveBalance;

class CarryForwardLeaveCommand extends Command
{
    protected $signature = 'leave:carry-forward';

    protected $description = 'Generate new leave quota and carry forward remaining quota to the next year';

    public function handle()
    {
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        $balances = LeaveBalance::with('leaveType')->where('year', $lastYear)->get();

        foreach ($balances as $balance) {
            $leaveType = $balance->leaveType;
            $carryForwardAmount = 0;


            if ($leaveType && $leaveType->allow_carry_forward) {
                $available = ($balance->initial_quota + $balance->carried_forward_quota) - $balance->used_quota;

                if ($available > 0) {
                    $carryForwardAmount = min($available, $leaveType->max_carry_forward_days);
                }
            }

            LeaveBalance::updateOrCreate(
                [
                    'employee_id' => $balance->employee_id,
                    'leave_type_id' => $balance->leave_type_id,
                    'year' => $currentYear,
                ],
                [

                    'initial_quota' => $leaveType->default_quota ?? 12,
                    'carried_forward_quota' => $carryForwardAmount,
                ]
            );
        }

        $this->info("Generate quota & carry forward completed for year {$currentYear}");
    }
}