<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [

        'company_id',

        'user_id',

        'employee_id',

        'full_name',

        'email',

        'phone',

        'gender',
        
        'agama',

        'birth_place',

        'birth_date',

        'address',

        'department_id',

        'position_id',

        'join_date',

        'employment_status',

        'status',

        'activation_token',

        'activation_expired_at',

        'bank_name',

        'bank_account_number',

        'bank_account_holder',
    ];
    

    protected $casts = [

        'birth_date'=>'date',

        'join_date'=>'date',

        'activation_expired_at'=>'datetime',
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
            ->where('status','active');
    }
}