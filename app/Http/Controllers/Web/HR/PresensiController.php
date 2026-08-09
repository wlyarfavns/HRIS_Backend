<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $search = $request->input('search');
        $companyId = $this->resolveCompanyId($request);

        // 1. Ambil presensi (clock-in/out) hari itu
        $attendanceQuery = Attendance::with('employee')
            ->where('company_id', $companyId)
            ->where('date', $date);

        if ($search) {
            $attendanceQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $attendances = $attendanceQuery->get();

        // 2. Ambil izin/sakit/cuti yang approved dan meliputi tanggal ini
        $leaveQuery = LeaveRequest::with(['employee', 'leaveType'])
            ->forCompany($companyId)
            ->where('status', 'approved')
            ->coveringDate($date);

        if ($search) {
            $leaveQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $leaves = $leaveQuery->get();

        // 3. Bentuk baris log presensi
        $attendanceLogs = $attendances->map(function (Attendance $a) {
            return [
                'nip'             => $a->employee->nip ?? '-',
                'name'            => $a->employee->name ?? '-',
                'avatar'          => $a->employee->id ?? 1,
                'in'              => $a->time_in ? $a->time_in->format('H:i') : '-',
                'out'             => $a->time_out ? $a->time_out->format('H:i') : '-',
                'loc'             => optional($a->company)->name ?? 'Kantor Pusat',
                'status'          => $a->status_label,
                'late_minutes'    => $a->late_minutes,
                'effective_hours' => $a->effective_hours,
                'distance'        => $a->distance_in_meters !== null
                                        ? round($a->distance_in_meters) . 'm (Valid)'
                                        : '-',
                'mock_gps'        => (bool) $a->is_mock_location,
                'note'            => $a->note,
                'attachment'      => null,
                'date'            => optional($a->date)->translatedFormat('d F Y'),
                'photo_in'        => $a->photo_in,
                'photo_out'       => $a->photo_out,
                'lat_in'          => $a->latitude_in,
                'lng_in'          => $a->longitude_in,
            ];
        });

        // 4. Bentuk baris log izin/sakit/cuti
        $leaveLogs = $leaves->map(function (LeaveRequest $l) {
            return [
                'nip'             => $l->employee->nip ?? '-',
                'name'            => $l->employee->name ?? '-',
                'avatar'          => $l->employee->id ?? 1,
                'in'              => '-',
                'out'             => '-',
                'loc'             => '-',
                'status'          => $l->type_label, // mis. "Sakit", "Cuti Tahunan" — dari relasi leaveType
                'late_minutes'    => null,
                'effective_hours' => '-',
                'distance'        => '-',
                'mock_gps'        => false,
                'note'            => $l->reason ?: ($l->type_label . ' (' . $l->start_date->translatedFormat('d M') . ' - ' . $l->end_date->translatedFormat('d M Y') . ')'),
                'attachment'      => $l->attachment,
                'date'            => $l->start_date->translatedFormat('d F Y'),
                'photo_in'        => null,
                'photo_out'       => null,
                'lat_in'          => null,
                'lng_in'          => null,
            ];
        });

        $logs = $attendanceLogs->merge($leaveLogs)->sortBy('name')->values();
        $total = $logs->count();

        $tepatWaktuCount = $attendanceLogs->where('status', 'Tepat Waktu')->count();
        $terlambatCount  = $attendanceLogs->where('status', 'Terlambat')->count();
        $izinCount       = $leaveLogs->count(); // semua jenis: sakit, cuti, izin — apa pun nama LeaveType-nya
        $tidakHadirCount = $attendanceLogs->where('status', 'Tidak Hadir')->count();

        // 5. Statistik ringkas
        $stats = [
            [
                'label' => 'Tepat Waktu',
                'value' => (string) $tepatWaktuCount,
                'icon'  => 'check_circle',
                'color' => 'text-primary',
                'note'  => $total ? round($tepatWaktuCount / $total * 100, 1) . '% kehadiran' : '-',
            ],
            [
                'label' => 'Terlambat',
                'value' => (string) $terlambatCount,
                'icon'  => 'schedule',
                'color' => 'text-amber-700',
                'note'  => $total ? round($terlambatCount / $total * 100, 1) . '% dari presensi' : '-',
            ],
            [
                'label' => 'Izin / Sakit',
                'value' => (string) $izinCount,
                'icon'  => 'medical_information',
                'color' => 'text-purple-700',
                'note'  => 'Termasuk cuti & lampiran dokumen',
            ],
            [
                'label' => 'Tidak Hadir',
                'value' => (string) $tidakHadirCount,
                'icon'  => 'person_off',
                'color' => 'text-error',
                'note'  => 'Alpha / tanpa kabar',
            ],
        ];

        // Badge warna: status presensi tetap (Tepat Waktu/Terlambat/dst), sedangkan nama LeaveType
        // (Sakit, Cuti Tahunan, dll) otomatis jatuh ke warna default abu-abu di blade — tambahkan
        // di sini kalau mau warna ungu konsisten untuk semua jenis cuti:
        $leaveTypeNames = $leaveLogs->pluck('status')->unique();
        $badge = [
            'Tepat Waktu'    => 'bg-primary/10 text-primary',
            'Terlambat'      => 'bg-amber-500/10 text-amber-700',
            'Sedang Bekerja' => 'bg-blue-500/10 text-blue-700',
            'Tidak Hadir'    => 'bg-error/10 text-error',
        ];
        foreach ($leaveTypeNames as $name) {
            $badge[$name] = 'bg-purple-500/10 text-purple-700';
        }

        return view('hr.presensi.index', compact('stats', 'logs', 'badge', 'date', 'search'));
    }

    /**
     * Export rekap presensi harian ke CSV.
     */
    public function export(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $companyId = $this->resolveCompanyId($request);

        $attendances = Attendance::with('employee')
            ->where('company_id', $companyId)
            ->where('date', $date)
            ->get();

        $filename = 'rekap-presensi-' . $date . '.csv';

        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['NIP', 'Nama', 'Jam Masuk', 'Jam Keluar', 'Status', 'Jam Kerja Efektif', 'Jarak GPS (m)', 'Fake GPS']);

            foreach ($attendances as $a) {
                fputcsv($handle, [
                    $a->employee->nip ?? '-',
                    $a->employee->name ?? '-',
                    $a->time_in ? $a->time_in->format('H:i') : '-',
                    $a->time_out ? $a->time_out->format('H:i') : '-',
                    $a->status_label,
                    $a->effective_hours,
                    $a->distance_in_meters ?? '-',
                    $a->is_mock_location ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function resolveCompanyId(Request $request): int
    {
        $user = $request->user();

        // Sesuaikan dengan struktur relasi user->employee->company_id di project kamu
        return $user->employee->company_id
            ?? $user->company_id
            ?? abort(403, 'Company tidak ditemukan untuk user ini.');
    }
}