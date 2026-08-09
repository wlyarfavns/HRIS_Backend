<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return response()->json([
            'success' => true,
            'data' => $shifts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $start = Carbon::createFromFormat('H:i', $validated['start_time']);
        $end = Carbon::createFromFormat('H:i', $validated['end_time']);
        $validated['is_cross_day'] = $end->lessThan($start);

        $shift = Shift::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shift created successfully',
            'data' => $shift
        ], 201);
    }

    public function show($id)
    {
        $shift = Shift::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $shift
        ]);
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
        ]);

        $startTime = $validated['start_time'] ?? $shift->start_time;
        $endTime = $validated['end_time'] ?? $shift->end_time;
        
        if (strlen($startTime) > 5) $startTime = substr($startTime, 0, 5);
        if (strlen($endTime) > 5) $endTime = substr($endTime, 0, 5);

        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);
        $validated['is_cross_day'] = $end->lessThan($start);

        $shift->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shift updated successfully',
            'data' => $shift
        ]);
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shift deleted successfully'
        ]);
    }
}
