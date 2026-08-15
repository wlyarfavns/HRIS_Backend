<?php

namespace App\Http\Controllers\Mobile\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();


        $employee = $user->employee()
            ->with(['company', 'department', 'position'])
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Data profil karyawan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data' => [
                'employee_id' => $employee->employee_id,
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'birth_place' => $employee->birth_place,
                'birth_date' => optional($employee->birth_date)->format('Y-m-d'),
                'gender' => $employee->gender,
                'agama' => $employee->agama,
                'address' => $employee->address,

                'department' => optional($employee->department)->name,
                'position' => optional($employee->position)->name,
                'join_date' => optional($employee->join_date)->format('Y-m-d'),
                'company_name' => optional($employee->company)->name,

            ]
        ], 200);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        // Validasi input dari Flutter
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_place' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:L,P',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:50',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Update tabel users
            $user->update([
                'name' => $request->name,
            ]);

            // 2. Update tabel employees
            if ($employee) {
                $employee->update([
                    'full_name' => $request->name,
                    'birth_place' => $request->birth_place,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'agama' => $request->agama,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // 1. Validasi input yang masuk
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        // 2. CEK KECOCOKAN PASSWORD LAMA
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini tidak cocok.'
            ], 400); // Mengembalikan status 400 Bad Request
        }

        // 3. JIKA COCOK, UPDATE PASSWORD BARU
        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}