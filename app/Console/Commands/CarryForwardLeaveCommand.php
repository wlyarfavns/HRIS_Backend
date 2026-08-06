<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CarryForwardLeaveCommand extends Command
{
    protected $signature = 'leave:carry-forward';

    protected $description = 'Carry forward remaining leave quota to the next year';

    public function handle()
    {
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        $balances = \App\Models\LeaveBalance::where('year', $lastYear)->get();

        foreach ($balances as $balance) {
            $leaveType = $balance->leaveType;

            if ($leaveType && $leaveType->allow_carry_forward) {
                $available = ($balance->initial_quota + $balance->carried_forward_quota) - $balance->used_quota;

                if ($available > 0) {
                    $carryForwardAmount = min($available, $leaveType->max_carry_forward_days);

                    \App\Models\LeaveBalance::updateOrCreate(
                        [
                            'employee_id' => $balance->employee_id,
                            'leave_type_id' => $balance->leave_type_id,
                            'year' => $currentYear,
                        ],
                        [
                            'carried_forward_quota' => $carryForwardAmount
                        ]
                    );
                }
            }
        }

        $this->info("Carry forward completed for year {$currentYear}");
    }
}
