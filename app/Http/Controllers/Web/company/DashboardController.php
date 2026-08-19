<?php

namespace App\Http\Controllers\Web\company; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;


        $totalEmployees = Employee::where('company_id', $companyId)->where('status', 'active')->count();
        $totalDepartments = Department::where('company_id', $companyId)->count();
        $totalUsers = User::where('company_id', $companyId)->role(['hr', 'finance', 'supervisor'])->count();


        $recentEmployees = Employee::with('department')
            ->where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalDepartments',
            'totalUsers',
            'recentEmployees'
        )); 
    }
}