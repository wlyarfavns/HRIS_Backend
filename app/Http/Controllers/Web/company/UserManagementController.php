<?php

namespace App\Http\Controllers\Web\company;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:hr,supervisor,finance',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'company_id' => $request->user()->company_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'company_id' => $request->user()->company_id, 
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
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

    public function destroyWeb($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('company')) {
            return redirect()->back()->withErrors(['error' => 'Akun Super Admin tidak boleh dihapus.']);
        }

        // Hapus pengguna
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
    }


    public function indexWeb(Request $request)
    {
        $companyId = $request->user()->company_id ?? 1;

        $query = User::where('company_id', $companyId)
            ->role(['hr', 'finance', 'supervisor']);

        // 2. FITUR PENCARIAN (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'Semua Role') {
            $dbRoleMap = [
                'HR Admin' => 'hr',
                'Finance' => 'finance',
                'Supervisor' => 'supervisor'
            ];

            $selectedDbRole = $dbRoleMap[$request->role] ?? null;
            if ($selectedDbRole) {
                $query->role($selectedDbRole);
            }
        }

        $users = $query->get();

        $roles = [
            ['name' => 'HR Admin', 'users' => User::role('hr')->where('company_id', $companyId)->count(), 'icon' => 'badge'],
            ['name' => 'Finance', 'users' => User::role('finance')->where('company_id', $companyId)->count(), 'icon' => 'payments'],
            ['name' => 'Supervisor', 'users' => User::role('supervisor')->where('company_id', $companyId)->count(), 'icon' => 'supervisor_account'],
        ];

        $displayRoleMap = [
            'hr' => 'HR Admin',
            'finance' => 'Finance',
            'supervisor' => 'Supervisor',
        ];

        return view('admin.pengguna.index', compact('users', 'roles', 'displayRoleMap'));
    }

    public function createWeb()
    {
        return view('admin.pengguna.create');
    }

    public function editWeb($id)
    {
        $user = User::findOrFail($id);

        // Ambil role pertama yang dimiliki user ini
        $userRole = $user->roles->first() ? $user->roles->first()->name : 'employee';

        $displayRoleMap = [
            'company' => 'Super Admin',
            'hr' => 'HR Admin',
            'finance' => 'Finance',
            'supervisor' => 'Supervisor',
            'employee' => 'Pegawai'
        ];

        $user->display_role = $displayRoleMap[$userRole] ?? 'Pegawai';

        return view('admin.pengguna.edit', compact('user'));
    }
}

