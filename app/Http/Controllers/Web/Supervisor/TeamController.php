<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();

        $team = Employee::with(['department', 'position'])
            ->where('company_id', $supervisor->company_id)
            ->where('supervisor_id', $supervisor->id)
            ->paginate(10);

        return view('supervisor.tim.index', compact('team'));
    }
}
