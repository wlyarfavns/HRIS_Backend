<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true; 
    }

public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_days' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $leaveTypeId = $this->input('leave_type_id');
                    if ($leaveTypeId) {
                        $leaveType = \App\Models\LeaveType::find($leaveTypeId);
                        if ($leaveType) {
                            if ($value < $leaveType->min_days_per_request) {
                                $fail("Total days must be at least {$leaveType->min_days_per_request} for this leave type.");
                            }
                            if ($leaveType->max_days_per_request !== null && $value > $leaveType->max_days_per_request) {
                                $fail("Total days cannot exceed {$leaveType->max_days_per_request} for this leave type.");
                            }
                        }
                    }
                },
            ],
            'reason' => 'nullable|string',
        ];
    }
}
