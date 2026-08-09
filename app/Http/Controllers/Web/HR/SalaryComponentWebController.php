<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalaryComponent;

class SalaryComponentWebController extends Controller
{
    public function index()
    {
        // For demo, assume company_id = 1
        $companyId = 1;
        $components = SalaryComponent::where('company_id', $companyId)->get();

        $totalComponents = $components->count();
        $totalEarning = $components->where('type', 'earning')->count();
        $totalDeduction = $components->where('type', 'deduction')->count();

        return view('hr.penggajian.komponen', compact('components', 'totalComponents', 'totalEarning', 'totalDeduction'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'is_taxable' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0'
        ]);

        // Hardcode company_id for this demo
        $validated['company_id'] = 1;
        if (!isset($validated['is_taxable'])) {
            $validated['is_taxable'] = false;
        }

        SalaryComponent::create($validated);

        return redirect()->route('hr.payroll.components')->with('success', 'Komponen Gaji berhasil ditambahkan!');
    }
    public function update(Request $request, $id)
    {
        $component = SalaryComponent::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'is_taxable' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0'
        ]);

        if (!isset($validated['is_taxable'])) {
            $validated['is_taxable'] = false;
        }

        $component->update($validated);

        return redirect()->route('hr.payroll.components')->with('success', 'Komponen Gaji berhasil diupdate!');
    }

    public function destroy($id)
    {
        $component = SalaryComponent::findOrFail($id);
        $component->delete();

        return redirect()->route('hr.payroll.components')->with('success', 'Komponen Gaji berhasil dihapus!');
    }
}
