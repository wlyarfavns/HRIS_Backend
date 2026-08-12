<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    /**
     * Daftar SPL untuk HR — SPL yang sudah disetujui Supervisor (approved_spv)
     * bisa dikunci di sini agar masuk ke rekap Payroll.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $query = OvertimeRequest::with('employee.department')
            ->where('company_id', $companyId);

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('employee', fn ($e) => $e->where('full_name', 'like', "%{$q}%"))
                    ->orWhere('project', 'like', "%{$q}%");
            });
        }

        $requests = $query->latest('date')->paginate(10)->withQueryString();

        $stats = [
            'today_overtime' => OvertimeRequest::where('company_id', $companyId)
                ->whereDate('date', today())
                ->count(),
            'pending' => OvertimeRequest::where('company_id', $companyId)
                ->whereIn('status', ['pending_spv', 'approved_spv'])
                ->count(),
            'approved_hours' => OvertimeRequest::where('company_id', $companyId)
                ->whereIn('status', ['approved_spv', 'locked'])
                ->whereMonth('date', now()->month)
                ->sum('hours'),
            'estimated_cost' => OvertimeRequest::where('company_id', $companyId)
                ->whereIn('status', ['approved_spv', 'locked'])
                ->whereMonth('date', now()->month)
                ->sum('overtime_pay'),
        ];

        return view('hr.persetujuan.lembur', compact('requests', 'stats'));
    }

    /**
     * HR mengunci SPL yang sudah disetujui Supervisor.
     * Hanya boleh mengunci SPL yang statusnya approved_spv.
     */
    public function lock(Request $request, OvertimeRequest $overtime)
    {
        $companyId = $request->user()->company_id;

        if ($overtime->company_id !== $companyId) {
            return response()->json(['message' => 'Anda tidak berwenang memproses SPL ini.'], 403);
        }

        if ($overtime->status !== 'approved_spv') {
            return response()->json([
                'message' => 'SPL ini belum disetujui Supervisor atau sudah diproses sebelumnya.',
            ], 400);
        }

        // Pastikan upah lembur sudah/kembali terhitung sebelum dikunci
        $overtime->overtime_pay = OvertimeRequest::calculateOvertimePay(
            $overtime->salary_snapshot,
            $overtime->hours
        );

        $overtime->update([
            'status'       => 'locked',
            'overtime_pay' => $overtime->overtime_pay,
            'locked_by'    => Auth::id(),
            'locked_at'    => now(),
        ]);

        return response()->json([
            'message' => "SPL {$overtime->employee->full_name} berhasil dikunci dan masuk ke rekap Payroll.",
            'data'    => $overtime,
        ]);
    }

    /**
     * HR menolak SPL (misalnya setelah direview ulang, sebelum dikunci).
     */
    public function reject(Request $request, OvertimeRequest $overtime)
    {
        $companyId = $request->user()->company_id;

        if ($overtime->company_id !== $companyId) {
            return response()->json(['message' => 'Anda tidak berwenang memproses SPL ini.'], 403);
        }

        if (!in_array($overtime->status, ['pending_spv', 'approved_spv'])) {
            return response()->json(['message' => 'SPL ini sudah diproses sebelumnya.'], 400);
        }

        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $overtime->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $data['reason'] ?? 'Ditolak oleh HR',
        ]);

        return response()->json([
            'message' => "SPL {$overtime->employee->full_name} berhasil ditolak.",
            'data'    => $overtime,
        ]);
    }
}