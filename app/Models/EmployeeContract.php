<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    protected $fillable = [

        'employee_id',

        'contract_number',

        'contract_type',

        'start_date',

        'end_date',

        'basic_salary',

        'status',

        'notes',

        'created_by',
    ];

    protected $casts = [

        'start_date'=>'date',

        'end_date'=>'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}