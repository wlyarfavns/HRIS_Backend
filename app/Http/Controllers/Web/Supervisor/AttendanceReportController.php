<?php

namespace App\Http\Controllers\Web\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data akun (User) yang sedang login (yaitu Anton)
        $user = $request->user();

        // KODE ABORT(403) SUDAH DIHAPUS DARI SINI

        // 2. Ambil parameter bulan dari URL, jika kosong gunakan bulan saat ini
        $filterMonth = $request->input('month', now()->format('Y-m'));
        $parsedDate = Carbon::parse($filterMonth);
        $year = $parsedDate->year;
        $month = $parsedDate->month;

        // 3. Tarik data bawahan (seperti Santosi) yang supervisor_id-nya sama dengan ID Anton di tabel users
        $team = Employee::where('supervisor_id', $user->id)
            ->with(['attendances' => function ($query) use ($year, $month) {
                $query->whereYear('date', $year)
                      ->whereMonth('date', $month);
            }])
            ->get();

        // 4. Format data untuk ditampilkan di view
        $teamRecap = $team->map(function ($member) {
            $attendances = $member->attendances;

            $tepatWaktu = $attendances->where('status', Attendance::STATUS_PRESENT)->count();
            $terlambat = $attendances->where('status', Attendance::STATUS_LATE)->count();
            $izinSakit = $attendances->whereIn('status', [Attendance::STATUS_PERMIT, Attendance::STATUS_SAKIT])->count();
            
            // Hadir total adalah gabungan tepat waktu dan terlambat
            $hadirTotal = $tepatWaktu + $terlambat;

            // Asumsi standar hari kerja efektif dalam sebulan
            $totalWorkingDays = 22; 
            
            // Hitung persentase
            $persentase = $totalWorkingDays > 0 ? round(($hadirTotal / $totalWorkingDays) * 100) : 0;
            $persentase = $persentase > 100 ? 100 : $persentase;

            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'avatar' => $member->id, 
                'hadir' => $hadirTotal,
                'terlambat' => $terlambat,
                'izin' => $izinSakit,
                'persentase' => $persentase
            ];
        });

        return view('supervisor.laporan.index', compact('teamRecap', 'filterMonth'));
    }
}