<?php

namespace App\Http\Controllers\Web\company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function indexWeb(Request $request)
    {
        $companyId = $request->user()->company_id ?? 1;

        $company = Company::findOrFail($companyId);

        $pic = User::where('company_id', $companyId)->role('company')->first();

        $totalEmployees = User::where('company_id', $companyId)->count();

        $companyData = (object) [
            'id' => $company->id,
            'code' => 'CMP-' . str_pad($company->id, 3, '0', STR_PAD_LEFT),
            'name' => $company->name,
            'city' => $company->city,
            'postal_code' => $company->postal_code,
            'address' => $company->city . ', ' . $company->province . ' - ' . $company->postal_code,
            'pic' => $pic ? $pic->name : 'Belum Ada PIC',
            'email' => $pic ? $pic->email : 'Belum Ada Email',
            'employees' => $totalEmployees,
            'status' => 'Aktif',
        ];

        return view('admin.perusahaan.manajemen-perusahaan', compact('companyData'));
    }

    public function editWeb(Request $request)
    {
        $companyId = $request->user()->company_id ?? 1;
        $company = Company::findOrFail($companyId);


        $stats = [
            ['label' => 'Total Karyawan Terdaftar', 'value' => User::where('company_id', $companyId)->count(), 'icon' => 'groups'],
            ['label' => 'Total Departemen', 'value' => '0', 'icon' => 'account_tree'],
            ['label' => 'Status Perusahaan', 'value' => 'Aktif', 'icon' => 'verified'],
        ];

        return view('admin.perusahaan.edit', compact('company', 'stats'));
    }

    public function updateWeb(Request $request)
    {
        $companyId = $request->user()->company_id ?? 1;
        $company = Company::findOrFail($companyId);

        $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        $company->update([
            'name' => $request->name,
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ]);

        return redirect()->route('admin.company.index')->with('success', 'Profil perusahaan berhasil diperbarui!');
    }
}