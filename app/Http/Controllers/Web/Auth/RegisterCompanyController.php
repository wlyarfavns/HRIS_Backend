<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterCompanyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'postal_code'  => 'required|string|max:10',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $company = Company::create([
                'name'        => $request->company_name,
                'city'        => $request->city,
                'province'    => $request->province,
                'postal_code' => $request->postal_code,
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'name'       => $request->company_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
            ]);

            $user->assignRole('company');

            DB::commit();

Auth::login($user);
            $request->session()->regenerate();

if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Perusahaan berhasil didaftarkan.',
                    'data'    => [
                        'company' => $company,
                        'user'    => $user,
                    ]
                ], 201);
            }

return redirect()->route('register.success')->with([
                'company_name'  => $request->company_name,
                'email'         => $request->email,
                'registered_at' => now()->translatedFormat('d M Y')
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan.',
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
        }
    }

    public function create()
    {
        return view('auth.register');
    }

public function success()
    {
        
        if (!session('company_name')) {
            return redirect()->route('register');
        }

        return view('auth.register-success');
    }
}