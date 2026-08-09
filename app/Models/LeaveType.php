<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'is_quota_based',
        'min_days_per_request',
        'max_days_per_request',
        'allow_carry_forward',
        'max_carry_forward_days',
        'default_quota',
        'requires_attachment',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
