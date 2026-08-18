<?php

namespace App\Http\Controllers\Web\company;

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

    public function indexWeb(Request $request)
    {
        $companyId = $request->user()->company_id;
        $departments = Department::where('company_id', $companyId)
            ->withCount('employees')
            ->with(['employees' => function($q) {
                $q->select('id', 'department_id', 'full_name', 'employee_id');
            }])
            ->paginate(10);


        $defaultDepartments = [
            'Information Technology (IT)',
            'Human Resources (HR)',
            'Finance & Accounting',
            'Marketing & Sales',
            'Operations',
            'Customer Service',
            'Research & Development',
            'Legal'
        ];


        $existingDeptNames = Department::where('company_id', $companyId)->pluck('name')->toArray();
        $availableDepartments = array_diff($defaultDepartments, $existingDeptNames);

        return view('admin.perusahaan.struktur-organisasi', [
            'departments' => $departments,
            'defaultDepartments' => $availableDepartments
        ]);
    }

    public function storeWeb(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $companyId = $request->user()->company_id;


        $exists = Department::where('company_id', $companyId)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Departemen tersebut sudah ada.');
        }

        Department::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'code' => strtoupper(substr(str_replace(' ', '', $request->name), 0, 4)),
        ]);

        return redirect()->route('admin.org-structure.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function destroyWeb(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        abort_if(
            $department->company_id != $request->user()->company_id,
            403
        );

        if ($department->employees()->count() > 0) {
            return redirect()->back()->with('error', 'Departemen tidak dapat dihapus karena masih memiliki anggota.');
        }

        $department->delete();

        return redirect()->route('admin.org-structure.index')->with('success', 'Departemen berhasil dihapus.');
    }
}