<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeActivationController extends Controller
{
    
    public function activate(Request $request)
    {
        
        $request->validate([
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

$employee = Employee::where('activation_token', $request->token)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Token aktivasi tidak valid atau sudah digunakan.'
            ], 400);
        }

if ($employee->activation_expired_at && Carbon::now()->greaterThan($employee->activation_expired_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Token aktivasi sudah kedaluwarsa. Silakan minta HR untuk mengirim ulang undangan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

$user = User::create([
                'company_id' => $employee->company_id, 
                'name'       => $employee->full_name,
                'email'      => $employee->email,
                'password'   => Hash::make($request->password),
            ]);

$user->assignRole('employee');

$employee->update([
                'user_id'               => $user->id,
                'status'                => 'active',
                'activation_token'      => null,
                'activation_expired_at' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil diaktifkan. Silakan login menggunakan email dan password baru Anda.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengaktifkan akun.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}