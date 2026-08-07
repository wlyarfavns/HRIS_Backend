<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = $request->input('company_id');
        $query = SalaryComponent::query();
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'is_taxable' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0'
        ]);

        $component = SalaryComponent::create($validated);
        return response()->json($component, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $component = SalaryComponent::findOrFail($id);
        return response()->json($component);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $component = SalaryComponent::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:earning,deduction',
            'is_taxable' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0'
        ]);

        $component->update($validated);
        return response()->json($component);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $component = SalaryComponent::findOrFail($id);
        $component->delete();
        return response()->json(null, 204);
    }
}
