<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReimbursementRequest;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use App\Notifications\GeneralNotification;

class ReimbursementController extends Controller
{
    public function store(StoreReimbursementRequest $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'Profil karyawan tidak ditemukan untuk akun ini.'], 404);
        }

        $receiptPath = $request->file('receipt')->store('reimbursements', 'public');

        $reimbursement = Reimbursement::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'claim_date' => $request->claim_date,
            'receipt_path' => $receiptPath,
            'status' => Reimbursement::STATUS_PENDING_SPV,
        ]);

        if ($employee->supervisor) {
            $employee->supervisor->notify(new GeneralNotification(
                'Pengajuan Reimbursement Baru',
                $employee->full_name . ' mengajukan reimbursement.',
                '/supervisor/persetujuan/reimbursement'
            ));
        }

        return response()->json([
            'message' => 'Klaim reimbursement berhasil diajukan.',
            'data' => $reimbursement,
        ], 201);
    }

    public function index(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'Profil karyawan tidak ditemukan untuk akun ini.'], 404);
        }

        $claims = Reimbursement::where('employee_id', $employee->id)
            ->latest('claim_date')
            ->get();

        return response()->json(['data' => $claims]);
    }
}
