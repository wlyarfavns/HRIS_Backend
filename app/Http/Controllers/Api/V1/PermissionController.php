<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $permission
        ]);
    }
}
