<?php

namespace App\Http\Controllers\Web\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\SalaryComponent;
use App\Models\EmployeeSalaryComponent;
use App\Models\User;
use App\Models\EmployeeContract;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $employees = Employee::with(['department', 'position'])
            ->where('company_id', $request->user()->company_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    public function show(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $employee->load(['department', 'position', 'activeContract'])
        ]);
    }

    public function showWeb(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $employee = Employee::with(['department', 'position', 'contracts'])
            ->where('company_id', $companyId)
            ->where(function ($q) use ($id) {
                $q->where('employee_id', $id)->orWhere('id', $id);
            })
            ->firstOrFail();
        $currentYear = now()->year;

        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->get();

        $leaveQuota = $leaveBalances->sum(fn($b) => $b->initial_quota + $b->carried_forward_quota);
        $leaveUsed = $leaveBalances->sum('used_quota');
        $leaveBalance = max($leaveQuota - $leaveUsed, 0);


        $contracts = $employee->contracts()
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn($c) => [
                'type' => $c->contract_type . ($c->contract_number ? ' — Kontrak ' . $c->contract_number : ''),
                'range' => Carbon::parse($c->start_date)->translatedFormat('d M Y')
                    . ' – '
                    . Carbon::parse($c->end_date)->translatedFormat('d M Y'),
                'status' => $c->status === 'Active' ? 'Berjalan' : 'Selesai',
            ]);


        $recentActivity = collect();

        $employee->leaveRequests()
            ->with('leaveType')
            ->where('status', 'approved')
            ->latest('updated_at')->limit(3)->get()
            ->each(fn($l) => $recentActivity->push([
                'label' => 'Cuti ' . ($l->leaveType->name ?? '-') . ' disetujui',
                'time' => Carbon::parse($l->updated_at)->translatedFormat('d M Y'),
                'icon' => 'event_available',
            ]));

        $employee->payrolls()
            ->latest('created_at')->limit(2)->get()
            ->each(fn($p) => $recentActivity->push([
                'label' => 'Slip gaji ' . Carbon::parse($p->period_start)->translatedFormat('M Y') . ' diunduh',
                'time' => Carbon::parse($p->created_at)->translatedFormat('d M Y'),
                'icon' => 'description',
            ]));

        $recentActivity = $recentActivity
            ->sortByDesc('time')
            ->take(5)
            ->values();


        $documents = [
            'Scan KTP' => (bool) $employee->ktp_file_path,
            'Scan NPWP' => (bool) $employee->npwp_file_path,
            'Kartu BPJS' => (bool) $employee->bpjs_file_path,
        ];

        return view('hr.karyawan.detail', compact(
            'employee',
            'contracts',
            'recentActivity',
            'leaveBalance',
            'leaveQuota',
            'documents',
        ));
    }

    public function editWeb(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $employee = Employee::with(['department', 'position'])
            ->where('company_id', $companyId)
            ->where(function ($q) use ($id) {
                $q->where('employee_id', $id)->orWhere('id', $id);
            })
            ->firstOrFail();

        $departments = Department::where('company_id', $companyId)->get();
        $positions = Position::where('company_id', $companyId)->get();

        $supervisors = User::where('company_id', $companyId)
            ->role('supervisor')
            ->orderBy('name')
            ->get();

        return view('hr.karyawan.edit', compact(
            'employee',
            'departments',
            'positions',
            'supervisors',
            'salaryComponents',
            'employeeComponentAmounts'
        ));
    }
    public function update(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:PKWT,PKWTT',
            'status' => 'required|in:pending,active,inactive,resigned',
        ]);

        $employee->update($request->only([
            'full_name',
            'phone',
            'gender',
            'birth_place',
            'birth_date',
            'address',
            'department_id',
            'position_id',
            'join_date',
            'employment_status',
            'status',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data Karyawan berhasil diperbarui.',
            'data' => $employee
        ]);
    }

    public function destroy(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Karyawan berhasil dihapus.'
        ]);
    }




    public function indexWeb(Request $request)
    {
        $companyId = $request->user()->company_id;

        $query = Employee::with(['department', 'position'])
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department') && $request->department !== 'Semua Departemen') {
            $query->where('department_id', $request->department);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $departments = Department::where('company_id', $companyId)->get();

        $totalEmployees = Employee::where('company_id', $companyId)->count();
        $newEmployees = Employee::where('company_id', $companyId)
            ->where('join_date', '>=', Carbon::now()->subDays(30))->count();
        $pkwt = Employee::where('company_id', $companyId)->where('employment_status', 'PKWT')->count();
        $pkwtt = Employee::where('company_id', $companyId)->where('employment_status', 'PKWTT')->count();

        $expiringContracts = EmployeeContract::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->where('contract_type', 'PKWT')
            ->where('status', 'Active')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();

        $stats = [
            ['label' => 'Total Karyawan', 'value' => $totalEmployees, 'icon' => 'groups', 'note' => 'Terdaftar di sistem'],
            ['label' => 'Kontrak Akan Habis', 'value' => $expiringContracts, 'icon' => 'event_upcoming', 'note' => 'H-30 dari sekarang'],
            ['label' => 'Karyawan Baru', 'value' => $newEmployees, 'icon' => 'person_add', 'note' => '30 hari terakhir'],
            ['label' => 'PKWT / PKWTT', 'value' => "$pkwt / $pkwtt", 'icon' => 'assignment', 'note' => 'Rasio tipe kontrak'],
        ];

        return view('hr.karyawan.index', compact('employees', 'stats', 'departments'));
    }

    public function createWeb(Request $request)
    {
        $companyId = $request->user()->company_id;

        $departments = Department::where('company_id', $companyId)->get();
        $positions = Position::where('company_id', $companyId)->get();
        $supervisors = User::where('company_id', $companyId)
            ->role('supervisor')
            ->orderBy('name')
            ->get();

        $joinYearMonth = Carbon::now()->format('Ym');
        $lastEmployee = Employee::where('company_id', $companyId)
            ->where('employee_id', 'like', $joinYearMonth . '%')
            ->orderBy('employee_id', 'desc')->first();
        $newSequence = $lastEmployee ? ((int) substr($lastEmployee->employee_id, -3)) + 1 : 1;
        $predictedNip = $joinYearMonth . str_pad($newSequence, 3, '0', STR_PAD_LEFT);

        return view('hr.karyawan.onboarding', compact('departments', 'positions', 'predictedNip', 'supervisors'));
    }

    public function storeWeb(Request $request)
    {
        $companyId = $request->user()->company_id;

        $request->validate([
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            'phone' => 'required|string|max:20',
            'npwp' => 'nullable|string|max:25',
            'bpjs_number' => 'nullable|string|max:20',
            'department_id' => [
                'required',
                Rule::exists('departments', 'id')->where('company_id', $companyId),
            ],
            'position_id' => [
                'required',
                Rule::exists('positions', 'id')->where('company_id', $companyId),
            ],
            'supervisor_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('company_id', $companyId),
            ],
            'join_date' => 'required|date',
            'contract_type' => 'required|in:PKWT,PKWTT,Probation,Internship',
            'contract_end_date' => 'nullable|date|after:join_date',
            'basic_salary' => 'required|numeric|min:0',
            'ktp_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'npwp_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bpjs_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);


        $joinYearMonth = Carbon::parse($request->join_date)->format('Ym');
        $lastEmployee = Employee::where('company_id', $companyId)
            ->where('employee_id', 'like', $joinYearMonth . '%')
            ->orderBy('employee_id', 'desc')->first();

        $newSequence = $lastEmployee ? ((int) substr($lastEmployee->employee_id, -3)) + 1 : 1;
        $nip = $joinYearMonth . str_pad($newSequence, 3, '0', STR_PAD_LEFT);



        $dummyEmail = strtolower($nip) . '@internal.local';

        $ktpPath = null;
        if ($request->hasFile('ktp_file')) {
            $ktpPath = $request->file('ktp_file')->store('documents/ktp', 'public');
        }

        $npwpPath = null;
        if ($request->hasFile('npwp_file')) {
            $npwpPath = $request->file('npwp_file')->store('documents/npwp', 'public');
        }

        $bpjsPath = null;
        if ($request->hasFile('bpjs_file')) {
            $bpjsPath = $request->file('bpjs_file')->store('documents/bpjs', 'public');
        }

        $employee = null;

        \DB::transaction(function () use ($request, $companyId, $nip, $dummyEmail, $ktpPath, $npwpPath, $bpjsPath, &$employee) {

            $user = User::create([
                'company_id' => $companyId,
                'nip' => $nip,
                'name' => $request->full_name,
                'email' => $dummyEmail,
                'password' => Hash::make($nip),
            ]);
            $user->assignRole('employee');


            $employee = Employee::create([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'supervisor_id' => $request->supervisor_id,
                'employee_id' => $nip,
                'full_name' => $request->full_name,
                'nik' => $request->nik,
                'email' => $dummyEmail,
                'phone' => $request->phone,
                'npwp' => $request->npwp,
                'bpjs_number' => $request->bpjs_number,
                'basic_salary' => $request->basic_salary,
                'ktp_file_path' => $ktpPath,
                'npwp_file_path' => $npwpPath,
                'bpjs_file_path' => $bpjsPath,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'join_date' => $request->join_date,
                'employment_status' => $request->contract_type,
                'status' => 'active',
            ]);


            if ($request->contract_type !== 'PKWTT' && $request->filled('contract_end_date')) {
                EmployeeContract::create([
                    'employee_id' => $employee->id,
                    'contract_type' => $request->contract_type,
                    'start_date' => $request->join_date,
                    'end_date' => $request->contract_end_date,
                    'status' => 'Active',
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('hr.employees.onboarding')->with('success_data', [
            'name' => $request->full_name,
            'nip' => $nip,
        ]);
    }

    public function updateWeb(Request $request, $id)
    {
        $companyId = $request->user()->company_id;

        $employee = Employee::where('company_id', $companyId)
            ->where(function ($q) use ($id) {
                $q->where('employee_id', $id)->orWhere('id', $id);
            })
            ->first();

        if (!$employee) {
            return redirect()->route('hr.employees.index')->with('error', 'Karyawan tidak ditemukan.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'npwp' => 'nullable|string|max:25',
            'bpjs_number' => 'nullable|string|max:20',
            'department_id' => [
                'required',
                Rule::exists('departments', 'id')->where('company_id', $companyId),
            ],
            'position_id' => [
                'required',
                Rule::exists('positions', 'id')->where('company_id', $companyId),
            ],
            'supervisor_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('company_id', $companyId),
            ],
            'join_date' => 'required|date',
            'employment_status' => 'required|in:PKWT,PKWTT,Probation,Internship',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:pending,active,inactive,resigned',
            'ktp_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',


            'bank_name' => 'nullable|in:BCA,MANDIRI,BNI',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'full_name',
            'nik',
            'email',
            'phone',
            'npwp',
            'bpjs_number',
            'department_id',
            'position_id',
            'supervisor_id',
            'join_date',
            'employment_status',
            'basic_salary',
            'status',
            'bank_name',
            'bank_account_number',
            'bank_account_holder',
        ]);

        if ($request->hasFile('ktp_file')) {
            $data['ktp_file_path'] = $request->file('ktp_file')->store('documents/ktp', 'public');
        }

        $employee->update($data);


        foreach ($request->input('components', []) as $componentId => $amount) {

            if ($amount === null || $amount === '') {
                continue;
            }


            $validComponent = SalaryComponent::where('id', $componentId)
                ->where('company_id', $companyId)
                ->exists();

            if (!$validComponent) {
                continue;
            }

            EmployeeSalaryComponent::updateOrCreate(
                ['employee_id' => $employee->id, 'salary_component_id' => $componentId],
                ['amount' => $amount]
            );
        }

        return redirect()->route('hr.employees.index')->with('success', 'Perubahan data karyawan berhasil disimpan!');
    }
}