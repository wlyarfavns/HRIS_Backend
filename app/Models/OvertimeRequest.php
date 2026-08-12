<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'employee_id', 'date', 'start_time', 'end_time',
        'hours', 'project', 'notes', 'salary_snapshot', 'overtime_pay',
        'status', 'approved_by', 'approved_at',
        'locked_by', 'locked_at', 'payroll_id',
        'rejected_by', 'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_spv'  => 'Pending Spv',
            'approved_spv' => 'Approved Spv',
            'locked'       => 'Locked',
            'rejected'     => 'Rejected',
            default        => $this->status,
        };
    }

    /**
     * Kalkulasi upah lembur sesuai formula Kepmenaker (1/173 x upah sebulan x jam lembur).
     * NOTE: rumus Depnaker sebenarnya berjenjang (jam ke-1 x1.5, jam berikutnya x2),
     * sesuaikan dgn kebijakan perusahaan.
     */
    public static function calculateOvertimePay(float $monthlySalary, float $hours): int
    {
        $hourlyRate = (1 / 173) * $monthlySalary;

        // Jam pertama x 1.5, jam ke-2 dst x 2 (standar Kepmenaker No. 102/2004)
        if ($hours <= 1) {
            $pay = $hourlyRate * 1.5 * $hours;
        } else {
            $pay = ($hourlyRate * 1.5) + ($hourlyRate * 2 * ($hours - 1));
        }

        return (int) round($pay);
    }
}