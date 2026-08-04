<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'user_id', 'department_id', 'position_id',
        'nip', 'full_name', 'email', 'phone', 'address',
        'join_date', 'contract_type', 'contract_end_date',
        'basic_salary', 'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'contract_end_date' => 'date',
        'basic_salary' => 'decimal:2',
    ];

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
}
