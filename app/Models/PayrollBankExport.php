<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBankExport extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['exported_at' => 'datetime'];
    public function payrollBatch()
    {
        return $this->belongsTo(PayrollBatch::class);
    }
}