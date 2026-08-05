<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::where(
            'company_id',
            $request->user()->company_id
        )->get();

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|max:100',
            'code'=>'nullable|max:20',
            'description'=>'nullable'
        ]);

        $department = Department::create([
            'company_id'=>$request->user()->company_id,
            'name'=>$request->name,
            'code'=>$request->code,
            'description'=>$request->description
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Department berhasil dibuat.',
            'data'=>$department
        ],201);
    }

    public function show(Request $request, Department $department)
    {
        abort_if(
            $department->company_id != $request->user()->company_id,
            403
        );

        return response()->json([
            'success'=>true,
            'data'=>$department
        ]);
    }

    public function update(Request $request, Department $department)
    {
        abort_if(
            $department->company_id != $request->user()->company_id,
            403
        );

        $request->validate([
            'name'=>'required|max:100',
            'code'=>'nullable|max:20',
            'description'=>'nullable'
        ]);

        $department->update([
            'name'=>$request->name,
            'code'=>$request->code,
            'description'=>$request->description
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Department berhasil diupdate.',
            'data'=>$department
        ]);
    }

    public function destroy(Request $request, Department $department)
    {
        abort_if(
            $department->company_id != $request->user()->company_id,
            403
        );

        $department->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Department berhasil dihapus.'
        ]);
    }
}