<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class RecapDailyAttendance extends Command
{
    // Nama command yang akan dipanggil oleh sistem
    protected $signature = 'attendance:recap-daily';

    // Deskripsi command
    protected $description = 'Merekap absensi harian dan menandai karyawan yang tidak absen sebagai Alpa (Tidak Hadir)';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        
        $this->info("Memulai rekap absensi untuk tanggal: {$today}");

        // 1. Ambil SEMUA karyawan yang statusnya masih aktif bekerja
        $activeEmployees = Employee::whereIn('status', ['active', 'pending_activation'])->get();

        $alpaCount = 0;

        // 2. Cek satu per satu karyawan
        foreach ($activeEmployees as $employee) {
            // Apakah karyawan ini punya data absensi hari ini? (Hadir/Terlambat/Izin/Sakit)
            $hasAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $today)
                ->exists();

            // 3. Jika TIDAK ADA data, buatkan data "Alpa" secara otomatis
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