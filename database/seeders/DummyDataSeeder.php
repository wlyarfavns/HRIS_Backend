<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) {
            $company = Company::create(['name' => 'PT Maju Sejahtera', 'email' => 'admin@maju.com', 'is_active' => true]);
        }

        $deptIT = Department::firstOrCreate(['company_id' => $company->id, 'name' => 'Information Technology', 'code' => 'IT']);
        $deptHR = Department::firstOrCreate(['company_id' => $company->id, 'name' => 'Human Resources', 'code' => 'HR']);
        $deptFin = Department::firstOrCreate(['company_id' => $company->id, 'name' => 'Finance', 'code' => 'FIN']);

        $spvUser = User::firstOrCreate(['email' => 'supervisor@example.com'], [
            'name' => 'Anton D (Supervisor)',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
        ]);
        $spvUser->assignRole('supervisor');

        $spvEmp = Employee::firstOrCreate(['email' => 'supervisor@example.com'], [
            'company_id' => $company->id,
            'user_id' => $spvUser->id,
            'employee_id' => 'EMP-SPV-001',
            'full_name' => 'Anton D',
            'department_id' => $deptIT->id,
            'join_date' => Carbon::now()->subYears(2),
            'employment_status' => 'PKWTT',
            'status' => 'active'
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $emp = Employee::firstOrCreate(['employee_id' => 'EMP-00' . $i], [
                'company_id' => $company->id,
                'full_name' => 'Pegawai Dummy ' . $i,
                'email' => 'pegawai' . $i . '@example.com',
                'department_id' => $i % 2 == 0 ? $deptHR->id : $deptIT->id,
                'supervisor_id' => $spvEmp->id,
                'join_date' => Carbon::now()->subMonths(rand(1, 10)),
                'employment_status' => 'PKWT',
                'status' => 'active'
            ]);


            for ($d = 0; $d < 3; $d++) {
                $date = Carbon::now()->subDays($d)->toDateString();
                Attendance::firstOrCreate([
                    'company_id' => $company->id,
                    'employee_id' => $emp->id,
                    'date' => $date
                ], [
                    'time_in' => rand(7, 9) . ':' . rand(10, 59) . ':00',
                    'status' => rand(1, 10) > 2 ? 'hadir' : 'terlambat'
                ]);
            }


            if ($i == 1 || $i == 2) {
                $type = LeaveType::firstOrCreate(['company_id' => $company->id, 'name' => 'Cuti Tahunan']);
                LeaveRequest::firstOrCreate([
                    'employee_id' => $emp->id,
                    'status' => 'pending_spv'
                ], [
                    'leave_type_id' => $type->id,
                    'start_date' => Carbon::now()->addDays(2),
                    'end_date' => Carbon::now()->addDays(4),
                    'total_days' => 3,
                    'reason' => 'Acara keluarga',
                ]);
            }
        }
    }
}
