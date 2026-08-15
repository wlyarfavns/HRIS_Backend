<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil dengan data asli (User login + Employee terkait).
     */
    public function index(Request $request)
    {
        $user     = $request->user();
        $employee = Employee::with(['department', 'position'])
            ->where('user_id', $user->id)
            ->first();

        // FIX: gunakan null-safe operator (?->) di setiap akses relasi/atribut
        // employee, supaya tidak throw "Attempt to read property on null"
        // saat user (mis. supervisor "Anton") belum punya record Employee.
        $userData = [
            'name'         => $user->name,
            'email'        => $user->email,
            'phone'        => $employee?->phone ?? '-',
            'role'         => $user->getRoleNames()->first() ?? 'Supervisor',
            'department'   => $employee?->department?->name ?? '-',
            'position'     => $employee?->position?->name ?? '-',
            'nip'          => $employee?->employee_id ?? ($user->nip ?? '-'),
            'avatar'       => 'https://i.pravatar.cc/150?img=' . (($user->id % 70) ?: 1),
            'join_date'    => $employee?->join_date
                                ? Carbon::parse($employee->join_date)->translatedFormat('d F Y')
                                : '-',
            // FIX: default harus '' (bukan '-'), karena <select> di form hanya
            // punya opsi Laki-laki/Perempuan. Kalau default '-' tidak cocok
            // opsi manapun, browser menampilkan opsi pertama secara visual
            // tapi x-model tetap '-' → lolos ke request → gagal validasi
            // "in:Laki-laki,Perempuan" walau tampilan terlihat sudah terisi.
            'gender'       => match ($employee?->gender) {
                                'P' => 'Perempuan',
                                'L' => 'Laki-laki',
                                default => '',
                            },
            'birth_place'  => $employee?->birth_place ?? '-',
            // format Y-m-d supaya kompatibel dengan <input type="date">
            'birth_date'   => $employee?->birth_date ? Carbon::parse($employee->birth_date)->format('Y-m-d') : '',
            'address'      => $employee?->address ?? '-',
            'bank_name'    => $employee?->bank_name ?? '-',
            'bank_account' => $employee?->bank_account_number ?? '-',
            'npwp'         => $employee?->npwp ?? '-',
        ];

        return view('supervisor.profile', compact('userData'));
    }

    /**
     * Update data diri & kontak. Menyentuh tabel users (name, email)
     * dan employees (kalau supervisor punya profil kepegawaian).
     */
    public function updateProfile(Request $request)
    {
        $user     = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'       => 'nullable|string|max:20',
            'gender'      => 'nullable|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:255',
            'birth_date'  => 'nullable|date',
            'address'     => 'nullable|string|max:1000',
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if ($employee) {
            $employee->update([
                'full_name'   => $data['name'],
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? $employee->phone,
                'gender'      => isset($data['gender']) ? ($data['gender'] === 'Perempuan' ? 'P' : 'L') : $employee->gender,
                'birth_place' => $data['birth_place'] ?? $employee->birth_place,
                'birth_date'  => $data['birth_date'] ?? $employee->birth_date,
                'address'     => $data['address'] ?? $employee->address,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diperbarui!',
        ]);
    }

    /**
     * Update password akun, wajib verifikasi password lama dulu.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini salah.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui!',
        ]);
    }
}