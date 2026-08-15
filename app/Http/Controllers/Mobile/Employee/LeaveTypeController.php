<?php

namespace App\Http\Controllers\Mobile\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $types = LeaveType::where('company_id', $companyId)->get();

        return response()->json(['data' => $types]);
    }
}