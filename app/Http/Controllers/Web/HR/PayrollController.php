<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use App\Models\User;
use App\Notifications\GeneralNotification;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
        Carbon::setLocale('id');
    }
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $period = $request->filled('period') ? Carbon::parse($request->input('period') . '-01') : now();


        $payrolls = Payroll::with(['employee.department', 'details.salaryComponent'])
            ->where('company_id', $companyId)
            ->whereYear('period_end', $period->year)
            ->whereMonth('period_end', $period->month)
            ->latest()
            ->get()
            ->unique('employee_id') // Ambil draf terbaru jika sempat di-run berkali-kali
            ->values();

        // 2. Ambil tanggal mulai/akhir dari database langsung agar UI ikut dinamis
        $start = $payrolls->first() ? $payrolls->first()->period_start : $period->copy()->startOfMonth();
        $end = $payrolls->first() ? $payrolls->first()->period_end : $period->copy()->endOfMonth();

        $totalGross = $payrolls->sum('basic_salary') + $payrolls->sum('total_allowances');
        $totalDeduct = $payrolls->sum('total_deductions');
        $totalNet = $payrolls->sum('net_salary');

        $components = \App\Models\SalaryComponent::forCompany($companyId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('hr.penggajian.index', compact(
            'payrolls',
            'components',
            'start',
            'end',
            'period',
            'totalGross',
            'totalDeduct',
            'totalNet'
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

    public function runPayroll(Request $request)
    {
        // 1. Validasi input yang masuk dari JS
        $request->validate([
            'period' => 'required|date_format:Y-m',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_ids' => 'required|string' // Format JSON array dari Alpine
        ]);

        $companyId = $request->user()->company_id;
        $period = Carbon::parse($request->input('period') . '-01');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 2. Decode string JSON menjadi Array PHP
        $employeeIds = json_decode($request->input('employee_ids'), true);

        if (empty($employeeIds)) {
            return back()->with('error', 'Tidak ada karyawan yang dipilih.');
        }

        // 3. Passing parameter ID dan Tanggal Custom ke Service
        $this->payrollService->generateAttendanceSummary($companyId, $startDate, $endDate, $employeeIds);
        $this->payrollService->calculateSalary($companyId, $startDate, $endDate, $employeeIds);

        return redirect()
            ->route('hr.payroll.index', ['period' => $period->format('Y-m')])
            ->with('success', "Payroll periode {$period->translatedFormat('F Y')} untuk " . count($employeeIds) . " karyawan berhasil dikalkulasi.");
    }

    public function showRunPage(Request $request)
    {
        $companyId = $request->user()->company_id;

        // Ambil data karyawan aktif untuk ditampilkan di tabel (Pilih Batch Karyawan)
        $employees = \App\Models\Employee::with('department')
            ->where('company_id', $companyId)
            ->whereIn('status', ['active', 'probation', 'PKWT', 'PKWTT']) // Pastikan status sesuai di database Anda
            ->get();

        return view('hr.penggajian.run', compact('employees'));
    }

    public function approveHr(Request $request)
    {
        $companyId = $request->user()->company_id;

        // 1. Cari data payroll yang masih DRAFT (Sistem akan mengabaikan URL dan mencari draf asli di database)
        $payroll = \App\Models\Payroll::where('company_id', $companyId)
            ->where('status', \App\Models\Payroll::STATUS_DRAFT)
            ->orderBy('period_start', 'asc')
            ->first();

        // Jika benar-benar tidak ada draf, kembalikan error
        if (!$payroll) {
            return back()->with('error', 'Tidak ada draft payroll yang tersedia untuk dikirim ke Finance.');
        }

        $start = $payroll->period_start->toDateString();
        $end = $payroll->period_end->toDateString();

        // Ambil nama bulan langsung dari database (Pasti akurat, contoh: "Maret 2026")
        $bulanPayroll = $payroll->period_start->translatedFormat('F Y');

        // 2. Eksekusi penguncian data dan pembuatan Batch untuk Finance
        $count = $this->payrollService->approveByHr($companyId, $start, $end);
        $this->payrollService->submitBatchToFinance($companyId, $start, $end, $request->user()->id);

        // 3. Trigger Notifikasi ke Finance
        $financeUsers = User::role('finance')->where('company_id', $companyId)->get();
        foreach ($financeUsers as $financeUser) {
            $financeUser->notify(new GeneralNotification(
                'Review Payroll Baru',
                "HR telah mengirimkan data payroll periode {$bulanPayroll} untuk di-review & disetujui.",
                '/finance/payroll'
            ));
        }

        // 4. Redirect kembali dengan URL yang sudah dikoreksi ke bulan yang tepat
        return redirect()->route('hr.payroll.index', ['period' => $payroll->period_start->format('Y-m')])
            ->with('success', "{$count} data payroll periode {$bulanPayroll} berhasil dikirim ke Finance.");
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

    public function exportXlsx(Request $request)
    {
        $request->validate(['period' => 'required|date_format:Y-m']);
        $companyId = $request->user()->company_id;
        $period = Carbon::parse($request->input('period') . '-01');

        $payrolls = Payroll::with(['employee.department', 'details.salaryComponent'])
            ->where('company_id', $companyId)
            ->whereYear('period_end', $period->year)
            ->whereMonth('period_end', $period->month)
            ->latest()
            ->get()
            ->unique('employee_id')
            ->values();

        $fileName = "Rekap_Payroll_{$period->format('M_Y')}.xlsx";

        return Excel::download(new PayrollExport($payrolls), $fileName);
    }
    public function slip(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $payroll = Payroll::with(['employee.department', 'employee.position', 'company', 'details.salaryComponent'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return view('hr.penggajian.slip', compact('payroll'));
    }


}