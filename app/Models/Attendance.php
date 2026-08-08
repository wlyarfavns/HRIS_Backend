<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = ['id'];

    public const STATUS_PRESENT = 'hadir';
    public const STATUS_LATE    = 'terlambat';
    public const STATUS_ABSENT  = 'Tidak Hadir';
    public const STATUS_PERMIT  = 'izin';
    public const STATUS_SAKIT   = 'sakit';
    // ------------------------------------------

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i', 
        'time_out' => 'datetime:H:i',
        'is_mock_location' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}