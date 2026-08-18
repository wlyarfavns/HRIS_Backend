<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\JobGrade;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    public function index(Request $request)
    {

        $companyId = $request->user() ? $request->user()->company_id : 1;


        $departments = Department::where('company_id', $companyId)
            ->withCount('employees')
            ->get();

        $jobGrades = JobGrade::where('company_id', $companyId)->get();

        return view('hr.struktur.index', compact('departments', 'jobGrades'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $companyId = $request->user() ? $request->user()->company_id : 1;

        Department::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'code' => strtoupper(substr($request->name, 0, 3)),
            'description' => $request->head ?? null, 
        ]);

        return redirect()->route('hr.structure.index')->with('success', 'Departemen baru berhasil ditambahkan!');
    }

    public function storeJobGrade(Request $request)
    {
        $request->validate([
            'grade' => 'required|string|max:50',
            'title' => 'required|string|max:255',
        ]);

        $companyId = $request->user() ? $request->user()->company_id : 1;


        preg_match('/\d+/', $request->grade, $matches);
        $level = isset($matches[0]) ? (int) $matches[0] : 1;

        JobGrade::create([
            'company_id' => $companyId,
            'name' => $request->title,
            'level' => $level,
            'default_allowance' => 0, 
        ]);

        return redirect()->route('hr.structure.index')->with('success', 'Job Grade baru berhasil ditambahkan!');
    }
}
