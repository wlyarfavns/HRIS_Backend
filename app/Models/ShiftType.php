<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftType extends Model
{
    protected $fillable = [
        'company_id', 'code', 'name', 'start_time', 'end_time',
        'is_cross_day', 'is_off', 'color', 'description',
    ];

    protected $casts = [
        'is_cross_day' => 'boolean',
        'is_off' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    /**
     * Label jam kerja siap tampil, mis. "08:00 - 17:00" atau "Off Duty".
     */
    public function getTimeLabelAttribute(): string
    {
        if ($this->is_off) {
            return 'Off Duty';
        }
        $start = $this->start_time ? substr($this->start_time, 0, 5) : '-';
        $end = $this->end_time ? substr($this->end_time, 0, 5) : '-';
        return $start . ' - ' . $end . ($this->is_cross_day ? ' (+1)' : '');
    }

    /** Tailwind bg class solid, dipakai untuk dot/legend */
    public function getBgClassAttribute(): string
    {
        return match ($this->code) {
            'P' => 'bg-emerald-600',
            'S' => 'bg-amber-600',
            'M' => 'bg-purple-600',
            'L' => 'bg-slate-500',
            default => 'bg-primary',
        };
    }

    /** Tailwind badge class (soft), dipakai untuk chip di tabel roster */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->code) {
            'P' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            'S' => 'bg-amber-100 text-amber-800 border border-amber-300',
            'M' => 'bg-purple-100 text-purple-800 border border-purple-300',
            'L' => 'bg-slate-100 text-slate-600 border border-slate-300',
            default => 'bg-gray-100 text-gray-700 border border-gray-300',
        };
    }
}