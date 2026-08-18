<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'supervisor_id',
        'employee_id',
        'full_name',
        'nik',
        'email',
        'phone',
        'npwp',
        'bpjs_number',
        'gender',
        'agama',
        'birth_place',
        'birth_date',
        'address',
        'department_id',
        'position_id',
        'join_date',
        'employment_status',
        'basic_salary',
        'ktp_file_path',
        'npwp_file_path',
        'bpjs_file_path',
        'status',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
    ];

    protected $casts = [

        'birth_date' => 'date',

        'join_date' => 'date',

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(EmployeeContract::class)
            ->where('status', 'Active');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }
    public function reimbursements()
    {
        return $this->hasMany(Reimbursement::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}