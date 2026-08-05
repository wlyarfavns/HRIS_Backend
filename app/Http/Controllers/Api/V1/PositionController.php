<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with(['department', 'jobGrade'])->get();
        return response()->json([
            'success' => true,
            'data' => $positions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'job_grade_id' => 'required|exists:job_grades,id',
            'title' => 'required|string|max:255',
        ]);

        $position = Position::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Position created successfully',
            'data' => $position->load(['department', 'jobGrade'])
        ], 201);
    }

    public function show($id)
    {
        $position = Position::with(['department', 'jobGrade'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $position
        ]);
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'sometimes|exists:departments,id',
            'job_grade_id' => 'sometimes|exists:job_grades,id',
            'title' => 'sometimes|string|max:255',
        ]);

        $position->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Position updated successfully',
            'data' => $position->load(['department', 'jobGrade'])
        ]);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);

        if ($position->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete position with assigned employees'
            ], 400);
        }

        $position->delete();

        return response()->json([
            'success' => true,
            'message' => 'Position deleted successfully'
        ]);
    }
}
