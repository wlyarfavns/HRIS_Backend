<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_FINANCE = 'pending_finance';
    public const STATUS_APPROVED_FINANCE = 'approved_finance';
    public const STATUS_EXPORTED = 'exported';
    public const STATUS_DISBURSED = 'disbursed';

    protected $guarded = ['id'];
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'submitted_at' => 'datetime',
        'approved_finance_at' => 'datetime',
        'exported_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'published_at'        => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    public function bankExports()
    {
        return $this->hasMany(PayrollBankExport::class);
    }
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_finance_by');
    }
}