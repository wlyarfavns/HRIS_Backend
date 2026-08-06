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
    /**
     * Memproses aktivasi akun karyawan dari Postman atau Form Web
     */
    public function activate(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Cari karyawan berdasarkan token
        $employee = Employee::where('activation_token', $request->token)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Token aktivasi tidak valid atau sudah digunakan.'
            ], 400);
        }

        // 3. Cek apakah token sudah kedaluwarsa
        if ($employee->activation_expired_at && Carbon::now()->greaterThan($employee->activation_expired_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Token aktivasi sudah kedaluwarsa. Silakan minta HR untuk mengirim ulang undangan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // 4. Buat Akun User untuk Login
            $user = User::create([
                'company_id' => $employee->company_id, 
                'name'       => $employee->full_name,
                'email'      => $employee->email,
                'password'   => Hash::make($request->password),
            ]);

            // 5. Berikan Hak Akses (Role) Karyawan
            $user->assignRole('employee');

            // 6. Update Data Karyawan (Tandai Aktif & Hapus Token)
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