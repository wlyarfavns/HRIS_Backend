<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use HasFactory;

    public const STATUS_PENDING_SPV = 'pending_spv';
    public const STATUS_PENDING_HR = 'pending_hr';
    public const STATUS_PENDING_FINANCE = 'pending_finance';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'company_id',
        'employee_id',
        'category',
        'description',
        'amount',
        'receipt_path',
        'claim_date',
        'status',
        'spv_id',
        'spv_approved_at',
        'hr_reviewed_by',
        'hr_reviewed_at',
        'finance_reviewed_by',
        'finance_reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'amount' => 'decimal:2',
        'spv_approved_at' => 'datetime',
        'hr_reviewed_at' => 'datetime',
        'finance_reviewed_at' => 'datetime',
    ];

    protected $appends = ['status_label', 'receipt_url'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function spv()
    {
        return $this->belongsTo(User::class, 'spv_id');
    }

    public function hrReviewer()
    {
        return $this->belongsTo(User::class, 'hr_reviewed_by');
    }

    public function financeReviewer()
    {
        return $this->belongsTo(User::class, 'finance_reviewed_by');
    }

    public function scopePendingSpv($query)
    {
        return $query->where('status', self::STATUS_PENDING_SPV);
    }

    public function scopePendingHr($query)
    {
        return $query->where('status', self::STATUS_PENDING_HR);
    }

    public function scopePendingFinance($query)
    {
        return $query->where('status', self::STATUS_PENDING_FINANCE);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_SPV => 'Menunggu SPV',
            self::STATUS_PENDING_HR => 'Pending HR',
            self::STATUS_PENDING_FINANCE => 'Pending Finance',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if ($this->receipt_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->receipt_path)) {
            return asset('storage/' . $this->receipt_path);
        }
        return null;
    }
}