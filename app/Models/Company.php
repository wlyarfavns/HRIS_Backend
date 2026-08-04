<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'subdomain', 'address', 'city', 'province', 'postal_code', 
        'phone', 'email', 'office_latitude', 'office_longitude', 
        'geofence_radius_meters', 'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}