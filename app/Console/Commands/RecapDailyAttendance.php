<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class RecapDailyAttendance extends Command
{

    protected $signature = 'attendance:recap-daily';


    protected $description = 'Merekap absensi harian dan menandai karyawan yang tidak absen sebagai Alpa (Tidak Hadir)';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $this->info("Memulai rekap absensi untuk tanggal: {$today}");


        $activeEmployees = Employee::whereIn('status', ['active', 'pending_activation'])->get();

        $alpaCount = 0;


        foreach ($activeEmployees as $employee) {

            $hasAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->exists();


            if (!$hasAttendance) {
                Attendance::create([
                    'company_id'  => $employee->company_id,
                    'employee_id' => $employee->id,
                    'date'        => $today,
                    'status'      => Attendance::STATUS_ABSENT, 
                ]);
                $alpaCount++;
            }
        }

        $this->info("Rekap selesai! Sebanyak {$alpaCount} karyawan ditandai sebagai Alpa.");
    }
}