<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ShiftAssignment;
use App\Models\ShiftSwapRequest;
use App\Models\ShiftType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\Constants\UnitValue;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $this->resolveCompanyId($request);

        // Minggu aktif: default minggu berjalan, bisa navigasi via ?week_start=YYYY-MM-DD
        $weekStart = $request->filled('week_start')
            ? Carbon::parse($request->week_start)->startOfWeek(UnitValue::MONDAY)
            : Carbon::now()->startOfWeek(UnitValue::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(UnitValue::SUNDAY);

        // 1. Master shift + jumlah karyawan yang assigned minggu ini per jenis shift
        $shiftTypes = ShiftType::where('company_id', $companyId)->orderBy('id')->get();
        $countsByType = ShiftAssignment::where('company_id', $companyId)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->select('shift_type_id', DB::raw('count(distinct employee_id) as total'))
            ->groupBy('shift_type_id')
            ->pluck('total', 'shift_type_id');

        $shiftTypesData = $shiftTypes->map(function (ShiftType $t) use ($countsByType) {
            return [
                'id' => $t->id,
                'code' => $t->code,
                'name' => $t->name,
                'time' => $t->time_label,
                'color' => $t->color,
                'bg' => $t->bg_class,
                'count' => $countsByType[$t->id] ?? 0,
                'desc' => $t->description,
            ];
        });

        // 2. Label hari untuk header tabel
        $labels = collect();
        for ($i = 0; $i < 7; $i++) {
            $d = $weekStart->copy()->addDays($i);
            $labels->push(['day' => $d->translatedFormat('l'), 'date' => $d->translatedFormat('d M'), 'iso' => $d->toDateString()]);
        }

        // 3. Roster: semua karyawan aktif + assignment mereka minggu ini
        // NOTE: kolom 'is_active' tidak ada di tabel employees kamu — filter dihapus.
        // Kalau kamu punya kolom status lain (mis. 'employment_status' = 'active'),
        // tambahkan lagi filternya di sini, contoh:
        // ->where('employment_status', 'active')
        $employees = Employee::with('department', 'position')
            ->where('company_id', $companyId)
            ->get();

        $assignments = ShiftAssignment::with('shiftType')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get()
            ->groupBy(fn($a) => $a->employee_id . '_' . $a->date->toDateString());

        $mapBadge = $shiftTypes->pluck('badge_class', 'code');
        $mapName = $shiftTypes->mapWithKeys(fn($t) => [$t->code => $t->name . ' (' . $t->time_label . ')']);

        $roster = $employees->map(function (Employee $e) use ($labels, $assignments) {
            $days = $labels->map(function ($l) use ($e, $assignments) {
                $a = $assignments->get($e->id . '_' . $l['iso']);
                return $a ? $a->first()->shiftType->code : null;
            })->all();

            return [
                'id' => $e->id,
                'nip' => $e->employee_id ?? $e->nip ?? $e->nik ?? '-',
                'name' => $this->resolveEmployeeName($e),
                'dept' => $e->department->name ?? '-',
                'pos' => $e->position->name ?? '-',
                'avatar' => $e->id,
                'days' => $days,
            ];
        });

        // 4. Pengajuan tukar shift (pending & yang baru diproses minggu ini)
        $swapRequests = ShiftSwapRequest::with(['fromEmployee', 'toEmployee', 'fromAssignment.shiftType', 'toAssignment.shiftType'])
            ->where('company_id', $companyId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (ShiftSwapRequest $r) {
                return [
                    'id' => 'SWP-' . str_pad($r->id, 3, '0', STR_PAD_LEFT),
                    'db_id' => $r->id,
                    'from' => $r->fromEmployee->name,
                    'from_avatar' => $r->fromEmployee->id,
                    'from_shift' => $r->fromAssignment->shiftType->name . ' (' . $r->fromAssignment->date->translatedFormat('d M') . ')',
                    'to' => $r->toEmployee->name,
                    'to_avatar' => $r->toEmployee->id,
                    'to_shift' => $r->toAssignment->shiftType->name . ' (' . $r->toAssignment->date->translatedFormat('d M') . ')',
                    'reason' => $r->reason,
                    'peer_approved' => $r->peer_approved,
                    'status' => $r->status_label,
                    'status_raw' => $r->status,
                    'created' => $r->created_at->diffForHumans(),
                ];
            });

        $pendingSpvCount = $swapRequests->where('status_raw', 'pending_spv')->count();

        $company = \App\Models\Company::find($companyId);

        return view('hr.shift.index', [
            'shiftTypes' => $shiftTypesData,
            'roster' => $roster,
            'mapBadge' => $mapBadge,
            'mapName' => $mapName,
            'labels' => $labels,
            'swapRequests' => $swapRequests,
            'pendingSpvCount' => $pendingSpvCount,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'departments' => \App\Models\Department::where('company_id', $companyId)->pluck('name'),
            'company' => $company,
        ]);
    }

    /**
     * Terapkan satu jenis shift ke banyak karyawan sekaligus untuk rentang tanggal.
     * Menimpa (overwrite) assignment yang sudah ada di rentang tersebut — sesuai "Overlap Shift Guard".
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'shift_type_id' => 'required|exists:shift_types,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $companyId = $this->resolveCompanyId($request);
        $start = Carbon::parse($request->date_start);
        $end = Carbon::parse($request->date_end);

        DB::transaction(function () use ($request, $companyId, $start, $end) {
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                foreach ($request->employee_ids as $employeeId) {
                    ShiftAssignment::updateOrCreate(
                        ['employee_id' => $employeeId, 'date' => $d->toDateString()],
                        ['company_id' => $companyId, 'shift_type_id' => $request->shift_type_id]
                    );
                }
            }
        });

        return redirect()->route('hr.shift.index')
            ->with('success', 'Jadwal shift berhasil diterapkan ke ' . count($request->employee_ids) . ' karyawan!');
    }

    /**
     * Ubah shift satu karyawan pada satu tanggal (klik cell di tabel roster).
     */
    public function updateCell(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_type_id' => 'required|exists:shift_types,id',
            'date' => 'required|date',
        ]);

        $companyId = $this->resolveCompanyId($request);

        $assignment = ShiftAssignment::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            ['company_id' => $companyId, 'shift_type_id' => $request->shift_type_id]
        );

        return response()->json(['success' => true, 'data' => $assignment]);
    }

    public function approveSwap(Request $request, ShiftSwapRequest $swap)
    {
        $fromAssignment = $swap->fromAssignment;
        $toAssignment = $swap->toAssignment;

        $fromEmployeeId = $fromAssignment->employee_id;
        $toEmployeeId = $toAssignment->employee_id;

        $conflictForTo = ShiftAssignment::where('employee_id', $toEmployeeId)
            ->where('date', $fromAssignment->date)
            ->where('id', '!=', $toAssignment->id)
            ->exists();

        $conflictForFrom = ShiftAssignment::where('employee_id', $fromEmployeeId)
            ->where('date', $toAssignment->date)
            ->where('id', '!=', $fromAssignment->id)
            ->exists();

        if ($conflictForTo || $conflictForFrom) {
            return back()->with('error', 'Tukar shift tidak bisa disetujui: salah satu karyawan sudah memiliki jadwal lain di tanggal tersebut.');
        }

        DB::transaction(function () use ($fromAssignment, $toAssignment, $fromEmployeeId, $toEmployeeId, $swap, $request) {
            $fromAssignment->update(['employee_id' => $toEmployeeId]);
            $toAssignment->update(['employee_id' => $fromEmployeeId]);

            $swap->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Pengajuan tukar shift disetujui & jadwal sudah ditukar.');
    }

    public function rejectSwap(Request $request, ShiftSwapRequest $swap)
    {
        $swap->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan tukar shift ditolak.');
    }

    /**
     * Ambil nama karyawan dengan aman, apa pun nama kolomnya di tabel `employees` kamu.
     * Cek berurutan: name -> full_name -> first_name+last_name -> fallback '-'.
     */
    private function resolveEmployeeName(Employee $e): string
    {
        if (!empty($e->name)) {
            return $e->name;
        }
        if (!empty($e->full_name)) {
            return $e->full_name;
        }
        $combined = trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? ''));
        return $combined !== '' ? $combined : ('Karyawan #' . $e->id);
    }

    /**
     * Update pengaturan radius geofencing & toleransi keterlambatan langsung dari halaman Shift.
     */
    public function updateGeofencing(Request $request)
    {
        $request->validate([
            'geofence_radius_meters' => 'required|integer|min:10|max:2000',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $companyId = $this->resolveCompanyId($request);

        $data = [
            'geofence_radius_meters' => $request->geofence_radius_meters,
            'late_tolerance_minutes' => $request->late_tolerance_minutes,
        ];

        if ($request->filled('office_latitude') && $request->filled('office_longitude')) {
            $data['office_latitude'] = $request->office_latitude;
            $data['office_longitude'] = $request->office_longitude;
        }

        \App\Models\Company::where('id', $companyId)->update($data);

        return back()->with('success', 'Pengaturan Geofencing berhasil disimpan!');
    }

    private function resolveCompanyId(Request $request): int
    {
        $user = $request->user();
        return $user->employee->company_id ?? $user->company_id ?? abort(403, 'Company tidak ditemukan.');
    }
}