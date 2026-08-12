<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalaryComponent;

class SalaryComponentWebController extends Controller
{
    private const CATEGORIES = ['Pendapatan Tetap', 'Pendapatan Variabel', 'Potongan'];

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $components = SalaryComponent::forCompany($companyId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $totalComponents = $components->count();
        $totalTetap       = $components->where('category', 'Pendapatan Tetap')->count();
        $totalVariabel     = $components->where('category', 'Pendapatan Variabel')->count();
        $totalPotongan     = $components->where('category', 'Potongan')->count();

        return view('hr.penggajian.komponen', compact(
            'components', 'totalComponents', 'totalTetap', 'totalVariabel', 'totalPotongan'
        ));
    }

    public function store(Request $request)
    {
        $companyId = $request->user()->company_id;

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:earning,deduction',
            'category'         => 'required|in:' . implode(',', self::CATEGORIES),
            'calculation_type' => 'nullable|string|max:100',
            'formula_note'     => 'nullable|string|max:255',
            'is_taxable'       => 'boolean',
            'default_amount'   => 'nullable|numeric|min:0',
        ]);

        $validated['company_id']  = $companyId;
        $validated['is_taxable']  = $validated['is_taxable'] ?? false;

        SalaryComponent::create($validated);

        return redirect()->route('hr.payroll.components')->with('success', 'Komponen Gaji berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $component = SalaryComponent::forCompany($companyId)->findOrFail($id);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:earning,deduction',
            'category'         => 'required|in:' . implode(',', self::CATEGORIES),
            'calculation_type' => 'nullable|string|max:100',
            'formula_note'     => 'nullable|string|max:255',
            'is_taxable'       => 'boolean',
            'default_amount'   => 'nullable|numeric|min:0',
        ]);

        $validated['is_taxable'] = $validated['is_taxable'] ?? false;

        $component->update($validated);

        return redirect()->route('hr.payroll.components')->with('success', 'Komponen Gaji berhasil diupdate!');
    }

    public function destroy(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $component = SalaryComponent::forCompany($companyId)->findOrFail($id);
        $component->delete();

        return redirect()->route('hr.payroll.components')->with('success', 'Komponen Gaji berhasil dihapus!');
    }
}