<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\LeaveType;
use Carbon\Carbon;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string|max:500',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',


        ];
    }


    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $leaveTypeId = $this->input('leave_type_id');
            $startDate   = $this->input('start_date');
            $endDate     = $this->input('end_date');

            if (!$leaveTypeId || !$startDate || !$endDate) return;


            try {
                $start     = Carbon::parse($startDate);
                $end       = Carbon::parse($endDate);
                $totalDays = $start->diffInDays($end) + 1;
            } catch (\Exception $e) {
                return;
            }

            $leaveType = LeaveType::find($leaveTypeId);
            if (!$leaveType) return;

            if ($totalDays < $leaveType->min_days_per_request) {
                $validator->errors()->add(
                    'end_date',
                    "Minimal pengajuan {$leaveType->min_days_per_request} hari untuk tipe cuti ini."
                );
            }

            if ($leaveType->max_days_per_request !== null && $totalDays > $leaveType->max_days_per_request) {
                $validator->errors()->add(
                    'end_date',
                    "Maksimal pengajuan {$leaveType->max_days_per_request} hari untuk tipe cuti ini."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'leave_type_id.required' => 'Tipe cuti wajib dipilih.',
            'leave_type_id.exists'   => 'Tipe cuti tidak valid.',
            'start_date.required'    => 'Tanggal mulai wajib diisi.',
            'start_date.date'        => 'Format tanggal mulai tidak valid.',
            'end_date.required'      => 'Tanggal selesai wajib diisi.',
            'end_date.date'          => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal'=> 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}