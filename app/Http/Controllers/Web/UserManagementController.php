<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    
    public function index(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->role(['hr', 'supervisor', 'finance'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'required|in:hr,supervisor,finance',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'company_id' => $request->user()->company_id,
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dibuat.',
                'data' => [
                    'user' => $user,
                    'role' => $request->role
                ]
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeWeb(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'company_id' => 1, // Defaulting to 1 since admin is global or company 1
                'name'       => $request->full_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
            ]);

            // Map roles
            $roleMap = [
                'Super Admin' => 'company',
                'HR Admin' => 'hr',
                'Finance' => 'finance',
                'Supervisor' => 'supervisor'
            ];
            
            $assignedRole = $roleMap[$request->role] ?? 'employee';
            $user->assignRole($assignedRole);

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan pengguna: ' . $e->getMessage()])->withInput();
        }
    }

public function update(Request $request, User $user)
    {
        if ($user->company_id != $request->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        if ($user->hasRole('company')) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Company tidak dapat diubah.'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data' => $user
        ]);
    }

    public function updateWeb(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'name' => $request->full_name ?? $user->name,
                'email' => $request->email ?? $user->email,
            ]);
            
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($request->filled('role')) {
                $roleMap = [
                    'Super Admin' => 'company',
                    'HR Admin' => 'hr',
                    'Finance' => 'finance',
                    'Supervisor' => 'supervisor'
                ];
                $assignedRole = $roleMap[$request->role] ?? 'employee';
                $user->syncRoles([$assignedRole]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Perubahan pengguna berhasil disimpan!');
    }

public function destroy(Request $request, User $user)
    {
        if ($user->company_id != $request->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        if ($user->hasRole('company')) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Company tidak dapat dihapus.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    }
}