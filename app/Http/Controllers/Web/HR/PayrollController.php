<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollRecapExport;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Halaman utama Penggajian.
     * Semua data (pipeline steps, rekap komponen gaji) diambil dari DB,
     * tidak ada lagi $steps / $components hardcode di view.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        // Periode aktif: dari query string ?period=2026-08, default bulan berjalan
        $period = $request->filled('period')
            ? Carbon::parse($request->input('period') . '-01')
            : now();

        $start = $period->copy()->startOfMonth();
        $end = $period->copy()->endOfMonth();

        $payrolls = Payroll::with(['employee.department', 'details.salaryComponent'])
            ->where('company_id', $companyId)
            ->whereDate('period_start', $start->toDateString())
            ->whereDate('period_end', $end->toDateString())
            ->latest()
            ->get();

        $totalKaryawan = $payrolls->count();
        $totalGajiBersih = $payrolls->sum('net_salary');
        $totalGajiBersihFormatted = 'Rp' . number_format($totalGajiBersih, 0, ',', '.');

        $steps = $this->buildPipelineSteps($payrolls, $start, $end);

        return view('hr.penggajian.index', compact(
            'payrolls',
            'totalKaryawan',
            'totalGajiBersihFormatted',
            'steps',
            'start',
            'end'
        ));
    }

    /**
     * Bangun status 5-tahap pipeline berdasarkan data payroll asli,
     * bukan status statis seperti pada versi dummy sebelumnya.
     */
    private function buildPipelineSteps($payrolls, Carbon $start, Carbon $end): array
    {
        $hasData = $payrolls->isNotEmpty();
        $allApprovedHr = $hasData && $payrolls->every(
            fn($p) => in_array($p->status, [Payroll::STATUS_APPROVED_HR, Payroll::STATUS_APPROVED_FINANCE])
        );
        $allApprovedFinance = $hasData && $payrolls->every(
            fn($p) => $p->status === Payroll::STATUS_APPROVED_FINANCE
        );

        return [
            [
                'step' => 1,
                'label' => 'Cut-off Rekap Absensi',
                'state' => $hasData ? 'completed' : 'upcoming',
                'status' => $hasData ? 'Selesai' : 'Belum Diproses',
                'date' => $hasData ? $start->translatedFormat('d M Y') : '-',
                'icon' => 'check',
                'desc' => $hasData ? "{$payrolls->count()} data presensi terkunci" : 'Menunggu proses kalkulasi',
            ],
            [
                'step' => 2,
                'label' => 'Engine Payroll (PPh21 & BPJS)',
                'state' => $hasData ? 'completed' : 'upcoming',
                'status' => $hasData ? 'Selesai' : 'Belum Diproses',
                'date' => $hasData ? $end->translatedFormat('d M Y') : '-',
                'icon' => 'check',
                'desc' => 'Kalkulasi TER PP 58/2023',
            ],
            [
                'step' => 3,
                'label' => 'Approval HR Operations',
                'state' => $allApprovedHr ? 'completed' : ($hasData ? 'active' : 'upcoming'),
                'status' => $allApprovedHr ? 'Selesai' : ($hasData ? 'Sedang Proses' : 'Belum Diproses'),
                'date' => $allApprovedHr ? 'Disetujui' : 'Pending Review',
                'icon' => $allApprovedHr ? 'check' : 'pending_actions',
                'desc' => $allApprovedHr ? 'Disetujui HR Lead' : 'Menunggu review HR',
            ],
            [
                'step' => 4,
                'label' => 'Approval Finance',
                'state' => $allApprovedFinance ? 'completed' : ($allApprovedHr ? 'active' : 'upcoming'),
                'status' => $allApprovedFinance ? 'Selesai' : ($allApprovedHr ? 'Sedang Proses' : 'Menunggu HR'),
                'date' => $allApprovedFinance ? 'Disetujui' : 'Pending Review',
                'icon' => $allApprovedFinance ? 'check' : 'pending_actions',
                'desc' => $allApprovedFinance ? 'Disetujui Finance Manager' : 'Review Finance Manager',
            ],
            [
                'step' => 5,
                'label' => 'Export Bank Transfer',
                'state' => $allApprovedFinance ? 'active' : 'upcoming',
                'status' => $allApprovedFinance ? 'Siap Export' : 'Terjadwal',
                'date' => $allApprovedFinance ? now()->translatedFormat('d M Y') : '-',
                'icon' => 'schedule',
                'desc' => 'CSV BCA, Mandiri & BNI',
            ],
        ];
    }

    /**
     * Jalankan engine payroll (cut-off absensi + kalkulasi gaji).
     * Tidak ada lagi hardcode company_id / tanggal Juli 2026.
     */
    public function runPayroll(Request $request)
    {
        $request->validate([
            'period' => 'required|date_format:Y-m',
        ]);

        $companyId = $request->user()->company_id;
        $period = Carbon::parse($request->input('period') . '-01');
        $startDate = $period->copy()->startOfMonth()->toDateString();
        $endDate = $period->copy()->endOfMonth()->toDateString();

        $this->payrollService->generateAttendanceSummary($companyId, $startDate, $endDate);
        $this->payrollService->calculateSalary($companyId, $startDate, $endDate);

        return redirect()
            ->route('hr.payroll.index', ['period' => $period->format('Y-m')])
            ->with('success', "Payroll periode {$period->translatedFormat('F Y')} berhasil dikalkulasi oleh sistem.");
    }

    /**
     * Approval oleh HR Operations. Hanya boleh dari status draft.
     */
    public function approveHr(Request $request)
    {
        $request->validate(['period' => 'required|date_format:Y-m']);
        $companyId = $request->user()->company_id;
        $period = Carbon::parse($request->input('period') . '-01');
        $start = $period->copy()->startOfMonth()->toDateString();
        $end = $period->copy()->endOfMonth()->toDateString();

        $count = $this->payrollService->approveByHr($companyId, $start, $end);
        $this->payrollService->submitBatchToFinance($companyId, $start, $end, $request->user()->id); // BARU

        return redirect()->route('hr.payroll.index', ['period' => $period->format('Y-m')])
            ->with('success', "{$count} payroll disetujui HR & dikirim ke Finance.");
    }

    /**
     * Approval oleh Finance. Hanya boleh dari status approved_hr.
     */
    public function approveFinance(Request $request)
    {
        $request->validate(['period' => 'required|date_format:Y-m']);

        $companyId = $request->user()->company_id;
        $period = Carbon::parse($request->input('period') . '-01');

        $count = $this->payrollService->approveByFinance(
            $companyId,
            $period->copy()->startOfMonth()->toDateString(),
            $period->copy()->endOfMonth()->toDateString()
        );

        return redirect()
            ->route('hr.payroll.index', ['period' => $period->format('Y-m')])
            ->with('success', "{$count} payroll disetujui oleh Finance.");
    }

    /**
     * Export rekap payroll periode berjalan ke XLSX (rekap internal HR,
     * berbeda dari exportBankCsv di PayrollApiController yang khusus
     * format upload bank).
     */
    public function exportXlsx(Request $request)
    {
        $request->validate(['period' => 'required|date_format:Y-m']);

        $companyId = $request->user()->company_id;
        $period = Carbon::parse($request->input('period') . '-01');

        return Excel::download(
            new PayrollRecapExport(
                $companyId,
                $period->copy()->startOfMonth()->toDateString(),
                $period->copy()->endOfMonth()->toDateString()
            ),
            "Rekap_Payroll_{$period->format('M_Y')}.xlsx"
        );
    }

    /**
     * Slip gaji individual (dibuka di HR web, tombol print/PDF ada di view).
     */
    public function slip(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $payroll = Payroll::with(['employee', 'details.salaryComponent'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return view('hr.penggajian.slip', compact('payroll'));
    }


}