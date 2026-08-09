<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JobGrade;
use Illuminate\Http\Request;

class JobGradeController extends Controller
{
    public function index()
    {
        $jobGrades = JobGrade::all();
        return response()->json([
            'success' => true,
            'data' => $jobGrades
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'default_allowance' => 'nullable|numeric|min:0',
        ]);

        $jobGrade = JobGrade::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job Grade created successfully',
            'data' => $jobGrade
        ], 201);
    }

    public function show($id)
    {
        $jobGrade = JobGrade::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $jobGrade
        ]);
    }

    public function update(Request $request, $id)
    {
        $jobGrade = JobGrade::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'level' => 'sometimes|integer|min:1',
            'default_allowance' => 'nullable|numeric|min:0',
        ]);

        $jobGrade->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job Grade updated successfully',
            'data' => $jobGrade
        ]);
    }

    public function destroy($id)
    {
        $jobGrade = JobGrade::findOrFail($id);

        if ($jobGrade->positions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete job grade with assigned positions'
            ], 400);
        }

        $jobGrade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job Grade deleted successfully'
        ]);
    }
}
