<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = ['id'];

    public const STATUS_PRESENT = 'hadir';
    public const STATUS_LATE    = 'terlambat';
    public const STATUS_ABSENT  = 'alpha';
    public const STATUS_PERMIT  = 'izin';
    public const STATUS_SAKIT   = 'sakit';

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'is_mock_location' => 'boolean',
        'is_mock_location_out' => 'boolean',
        'latitude_in' => 'decimal:7',
        'longitude_in' => 'decimal:7',
        'latitude_out' => 'decimal:7',
        'longitude_out' => 'decimal:7',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'Tepat Waktu',
            self::STATUS_LATE    => 'Terlambat',
            self::STATUS_PERMIT  => 'Izin',
            self::STATUS_SAKIT   => 'Izin / Sakit',
            self::STATUS_ABSENT  => 'Tidak Hadir',
            default              => $this->time_in && !$this->time_out ? 'Sedang Bekerja' : '-',
        };
    }


    public function getEffectiveHoursAttribute(): string
    {
        if (!$this->time_in) {
            return '-';
        }
        if (!$this->time_out) {
            return 'Sedang berjalan';
        }

        $in = \Carbon\Carbon::parse($this->time_in);
        $out = \Carbon\Carbon::parse($this->time_out);
        $diff = $in->diff($out);

        return sprintf('%dj %02dm', $diff->h + ($diff->days * 24), $diff->i);
    }
}