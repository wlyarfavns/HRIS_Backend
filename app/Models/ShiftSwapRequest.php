<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSwapRequest extends Model
{
    protected $fillable = [
        'company_id', 'from_employee_id', 'from_shift_assignment_id',
        'to_employee_id', 'to_shift_assignment_id',
        'reason', 'peer_approved', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'peer_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function fromEmployee()
    {
        return $this->belongsTo(Employee::class, 'from_employee_id');
    }

    public function toEmployee()
    {
        return $this->belongsTo(Employee::class, 'to_employee_id');
    }

    public function fromAssignment()
    {
        return $this->belongsTo(ShiftAssignment::class, 'from_shift_assignment_id');
    }

    public function toAssignment()
    {
        return $this->belongsTo(ShiftAssignment::class, 'to_shift_assignment_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_peer' => 'Menunggu Rekan',
            'pending_spv'  => 'Menunggu SPV',
            'approved'     => 'Disetujui',
            'rejected'     => 'Ditolak',
            default        => $this->status,
        };
    }
}