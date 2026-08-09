<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'start_time',
        'end_time',
        'is_cross_day'
    ];
}
