<?php

namespace App\Http\Controllers\Mobile\Employee;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Notifications\GeneralNotification;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Data karyawan tidak ditemukan.');

        $overtimes = OvertimeRequest::where('employee_id', $employee->id)
            ->latest('date')
            ->paginate(10);

        return response()->json($overtimes);
    }

    public function store(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Data karyawan tidak ditemukan.');

        $data = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'project' => 'nullable|string|max:255',
            'notes' => 'required|string|max:500',
        ]);




        $start = Carbon::createFromFormat('H:i', $data['start_time']);
        $end = Carbon::createFromFormat('H:i', $data['end_time']);


        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        $minutes = $end->diffInMinutes($start);
        $hours = round(abs($minutes) / 60, 1);

        $overtime = OvertimeRequest::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'hours' => $hours,
            'project' => $data['project'] ?? null,
            'notes' => $data['notes'],
            'salary_snapshot' => $employee->basic_salary,
            'status' => 'pending_spv',
        ]);

        if ($employee->supervisor) {
            $employee->supervisor->notify(new GeneralNotification(
                'Pengajuan Lembur Baru',
                $employee->full_name . ' mengajukan lembur.',
                '/supervisor/persetujuan/lembur' 
            ));
        }

        return response()->json([
            'message' => 'Pengajuan lembur berhasil dikirim, menunggu approval Supervisor.',
            'data' => $overtime,
        ], 201);
    }

    public function show(Request $request, OvertimeRequest $overtime)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Data karyawan tidak ditemukan.');
        abort_unless($overtime->employee_id === $employee->id, 403);

        return response()->json($overtime);
    }

    public function destroy(Request $request, OvertimeRequest $overtime)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 404, 'Data karyawan tidak ditemukan.');
        abort_unless($overtime->employee_id === $employee->id, 403);
        abort_unless($overtime->status === 'pending_spv', 422, 'Pengajuan yang sudah diproses tidak bisa dibatalkan.');

        $overtime->delete();
        return response()->json(['message' => 'Pengajuan lembur dibatalkan.']);
    }
}