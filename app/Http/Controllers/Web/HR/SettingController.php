<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\LeaveType;

class SettingController extends Controller
{
    public function index()
    {

        $companyId = 1;
        $company = Company::findOrFail($companyId);

        $leaveTypes = LeaveType::where('company_id', $companyId)->get();

        if ($leaveTypes->isEmpty()) {
            LeaveType::create(['company_id' => $companyId, 'name' => 'Cuti Tahunan', 'is_quota_based' => true, 'default_quota' => 12, 'min_days_per_request' => 1, 'max_days_per_request' => 12, 'allow_carry_forward' => true, 'requires_attachment' => false]);
            LeaveType::create(['company_id' => $companyId, 'name' => 'Sakit', 'is_quota_based' => false, 'requires_attachment' => true]);
            LeaveType::create(['company_id' => $companyId, 'name' => 'Izin Pribadi', 'is_quota_based' => true, 'default_quota' => 3, 'requires_attachment' => false]);
            LeaveType::create(['company_id' => $companyId, 'name' => 'Cuti Melahirkan', 'is_quota_based' => false, 'requires_attachment' => true]);

            $leaveTypes = LeaveType::where('company_id', $companyId)->get();
        }

        $cutiTahunan = $leaveTypes->where('name', 'Cuti Tahunan')->first();

        return view('hr.pengaturan.index', compact('company', 'leaveTypes', 'cutiTahunan'));
    }

    public function updateMainSettings(Request $request)
    {
        $companyId = 1;
        $company = Company::findOrFail($companyId);

        $validated = $request->validate([
            'late_tolerance_minutes' => 'required|integer|min:0',
            'standard_in_time' => 'required|date_format:H:i',
            'max_overtime_hours' => 'required|integer|min:0',
            'overtime_formula' => 'required|string|max:255',
            'default_quota' => 'required|integer|min:1',
            'min_days_per_request' => 'required|integer|min:1',
            'max_days_per_request' => 'required|integer|min:1',
            'allow_carry_forward' => 'boolean'
        ]);

        $company->update([
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'],
            'standard_in_time' => $validated['standard_in_time'] . ':00',
            'max_overtime_hours' => $validated['max_overtime_hours'],
            'overtime_formula' => $validated['overtime_formula'],
        ]);

        $cutiTahunan = LeaveType::where('company_id', $companyId)->where('name', 'Cuti Tahunan')->first();
        if ($cutiTahunan) {
            $cutiTahunan->update([
                'default_quota' => $validated['default_quota'],
                'min_days_per_request' => $validated['min_days_per_request'],
                'max_days_per_request' => $validated['max_days_per_request'],
                'allow_carry_forward' => $request->has('allow_carry_forward') ? true : false,
            ]);
        }

        return redirect()->route('hr.settings.index')->with('success', 'Pengaturan HR berhasil diperbarui!');
    }

    public function storeLeaveType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_quota_based' => 'boolean',
            'default_quota' => 'nullable|integer|min:0',
            'requires_attachment' => 'boolean',
        ]);

        $validated['company_id'] = 1;
        if (!isset($validated['is_quota_based'])) $validated['is_quota_based'] = false;
        if (!isset($validated['requires_attachment'])) $validated['requires_attachment'] = false;

        LeaveType::create($validated);

        return redirect()->route('hr.settings.index')->with('success', 'Jenis Cuti/Izin berhasil ditambahkan!');
    }

    public function updateLeaveType(Request $request, $id)
    {
        $leaveType = LeaveType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_quota_based' => 'boolean',
            'default_quota' => 'nullable|integer|min:0',
            'requires_attachment' => 'boolean',
        ]);

        if (!isset($validated['is_quota_based'])) $validated['is_quota_based'] = false;
        if (!isset($validated['requires_attachment'])) $validated['requires_attachment'] = false;

        $leaveType->update($validated);

        return redirect()->route('hr.settings.index')->with('success', 'Jenis Cuti/Izin berhasil diupdate!');
    }

    public function destroyLeaveType($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->delete();

        return redirect()->route('hr.settings.index')->with('success', 'Jenis Cuti/Izin berhasil dihapus!');
    }
}
