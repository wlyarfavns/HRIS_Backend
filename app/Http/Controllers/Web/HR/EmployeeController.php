<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Menampilkan seluruh karyawan di company tersebut.
     */
    public function index(Request $request)
    {
        $employees = Employee::with(['department', 'position'])
            ->where('company_id', $request->user()->company_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    /**
     * HR Mendaftarkan Karyawan Baru (Pre-Onboarding).
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:PKWT,PKWTT', // DIPERBAIKI
        ]);

        $companyId = $request->user()->company_id;

        // ---------------------------------------------------------
        // LOGIKA GENERATE NIP 18 DIGIT
        // ---------------------------------------------------------
        $birthDate = Carbon::parse($request->birth_date)->format('Ymd');
        $joinDate = Carbon::parse($request->join_date)->format('Ym');
        $genderCode = $request->gender === 'L' ? '1' : '2';

        $baseNip = $birthDate . $joinDate . $genderCode;

        $lastEmployee = Employee::where('employee_id', 'like', $baseNip . '%')
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            // Ambil 3 digit terakhir dari NIP sebelumnya, jadikan integer, lalu tambah 1
            $lastSequence = (int) substr($lastEmployee->employee_id, -3);
            $newSequence = $lastSequence + 1;
        } else {
            // Jika belum ada, mulai dari 1
            $newSequence = 1;
        }

        $sequence = str_pad($newSequence, 3, '0', STR_PAD_LEFT);
        $nip = $baseNip . $sequence;

        $employee = Employee::create([
            'company_id' => $companyId,
            'employee_id' => $nip,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'address' => $request->address,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'join_date' => $request->join_date,
            'employment_status' => $request->employment_status,
            'status' => 'pending',
            'activation_token' => Str::random(60),
            'activation_expired_at' => Carbon::now()->addDays(3),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Karyawan berhasil dibuat. Menunggu aktivasi.',
            'data' => $employee
        ], 201);
    }

    /**
     * Menampilkan detail satu karyawan.
     */
    public function show(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $employee->load(['department', 'position', 'activeContract'])
        ]);
    }

    /**
     * HR mengubah data administratif karyawan.
     */
    public function update(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:PKWT,PKWTT', // DIPERBAIKI
            'status' => 'required|in:pending,active,inactive,resigned', // DIPERBAIKI
        ]);

        $employee->update($request->only([
            'full_name',
            'phone',
            'gender',
            'birth_place',
            'birth_date',
            'address',
            'department_id',
            'position_id',
            'join_date',
            'employment_status',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data Karyawan berhasil diperbarui.',
            'data' => $employee
        ]);
    }

    /**
     * HR menghapus data Karyawan.
     */
    public function destroy(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Karyawan berhasil dihapus.'
        ]);
    }
}