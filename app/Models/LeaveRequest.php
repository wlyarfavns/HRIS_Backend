<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property \Carbon\Carbon|null $approved_at
 */
class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'attachment',      
        'approved_by',     
        'approved_at',     
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Ambil pengajuan izin/cuti/sakit yang meliputi tanggal tertentu.
     */
    public function scopeCoveringDate($query, string $date)
    {
        return $query->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }

    /**
     * Filter berdasarkan company milik employee (karena leave_requests tidak simpan company_id langsung).
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

    /**
     * Label untuk ditampilkan di tabel presensi HR, mis. "Sakit", "Cuti Tahunan".
     * Fallback ke "Izin" kalau relasi leaveType belum di-load / null.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->leaveType->name ?? 'Izin';
    }
}