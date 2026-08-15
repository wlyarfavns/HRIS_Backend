<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $userData = [
            'name'        => $user->name,
            'email'       => $employee->email ?? $user->email ?? '-',
            'phone'       => $employee->phone ?? '-',
            'role'        => $user->getRoleNames()->first() === 'finance' ? 'Finance Staff' : ucfirst($user->getRoleNames()->first() ?? '-'),
            'department'  => $employee->department->name ?? '-',
            'position'    => $employee->position->name ?? '-',
            'nip'         => $employee->employee_id ?? '-',
            'address'     => $employee->address ?? '-',
        ];

        return view('finance.profile', compact('userData'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        abort_unless($employee, 404, 'Profil karyawan tidak ditemukan.');

        $user->update(['name' => $request->name]);

        $employee->update([
            'email'       => $request->email,
            'phone'       => $request->phone,
            'gender'      => $request->gender === 'Laki-laki' ? 'L' : 'P',
            'birth_place' => $request->birth_place,
            'birth_date'  => $request->birth_date,
            'address'     => $request->address,
        ]);

        return response()->json(['message' => 'Data profil berhasil diperbarui!']);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Kata sandi saat ini salah.',
                'errors'  => ['current_password' => ['Kata sandi saat ini salah.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Kata sandi berhasil diperbarui!']);
    }
}