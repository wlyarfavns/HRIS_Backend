<?php

namespace App\Http\Controllers;

use App\Models\EmployeeContract;
use Illuminate\Http\Request;

class EmployeeContractController extends Controller
{
    public function index()
    {
        $contracts = EmployeeContract::with('employee')->get();
        return response()->json([
            'success' => true,
            'data' => $contracts
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:PKWT,PKWTT',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'document_file' => 'nullable|string',
            'status' => 'required|in:Active,Expired,Terminated',
        ]);

        $contract = EmployeeContract::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contract created successfully',
            'data' => $contract
        ], 201);
    }

    public function show($id)
    {
        $contract = EmployeeContract::with('employee')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $contract
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $contract = EmployeeContract::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'contract_type' => 'sometimes|in:PKWT,PKWTT',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'document_file' => 'nullable|string',
            'status' => 'sometimes|in:Active,Expired,Terminated',
        ]);

        $contract->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contract updated successfully',
            'data' => $contract
        ], 200);
    }

    public function destroy($id)
    {
        $contract = EmployeeContract::findOrFail($id);
        $contract->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contract deleted successfully'
        ], 200);
    }

    public function reminderH30()
    {
        $today = now();
        $targetDate = now()->addDays(30);

        $expiringContracts = EmployeeContract::with('employee')
            ->where('contract_type', 'PKWT')
            ->where('status', 'Active')
            ->whereBetween('end_date', [$today, $targetDate])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Contracts expiring in the next 30 days',
            'data' => $expiringContracts
        ], 200);
    }
}