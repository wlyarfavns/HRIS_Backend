<?php

namespace App\Http\Controllers\Web\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    private array $roleLabels = [
        'company' => 'Super Admin',
        'hr' => 'HR Admin',
        'supervisor' => 'Supervisor',
        'finance' => 'Finance Staff',
        'employee' => 'Karyawan',
    ];

    public function index(Request $request)
    {
        $user = $request->user();

        $roleKey = $user->roles->first() ? $user->roles->first()->name : 'company';

        $userData = [
            'name' => $user->name,
            'email' => $user->email ?? '-',
            'nip' => $user->nip ?? '-',
            'role' => $this->roleLabels[$roleKey] ?? ucfirst($roleKey),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0B3D2E&color=fff',
        ];

        $viewFolder = $roleKey === 'company' ? 'admin' : $roleKey;
        return view("{$viewFolder}.profile", compact('userData'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Data profil berhasil diperbarui!']);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Kata sandi saat ini salah.',
                'errors' => ['current_password' => ['Kata sandi saat ini salah.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Kata sandi berhasil diperbarui!']);
    }
}