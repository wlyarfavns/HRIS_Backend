<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['company_id', 'department_id', 'job_grade_id', 'title'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobGrade()
    {
        return $this->belongsTo(JobGrade::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
