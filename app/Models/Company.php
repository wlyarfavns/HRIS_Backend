<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'city', 'province', 'postal_code', 
        'phone', 'email', 'office_latitude', 'office_longitude', 
        'geofence_radius_meters', 'is_active',
        'standard_in_time', 'late_tolerance_minutes', 'max_overtime_hours', 'overtime_formula'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}