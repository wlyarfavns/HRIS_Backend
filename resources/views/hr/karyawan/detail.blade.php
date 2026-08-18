@extends('layouts.hr')
    @php






    @endphp

@section('title', 'Detail Karyawan — ' . $employee->full_name)
@section('page-title', 'Detail Karyawan')
@section('page-desc', 'Profil lengkap, riwayat kontrak, dan ringkasan kepegawaian.')

@section('content')


    <a href="{{ route('hr.employees.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500
                   hover:text-[#0B3D2E] transition -mt-2 mb-4">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>


    @if (session('success'))
        <div class="rounded-md bg-gray-50 border border-gray-200 text-emerald-800 text-sm p-4 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif


    <div class="bg-white rounded-md p-8 flex items-center gap-5 mb-6 border border-gray-200 shadow-sm">


        <div class="w-20 h-20 rounded-full bg-gray-50 text-[#0B3D2E] border border-gray-200
                        flex items-center justify-center text-3xl font-semibold shrink-0 uppercase">
            {{ substr($employee->full_name, 0, 1) }}
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <p class="text-xl font-medium text-gray-800">{{ $employee->full_name }}</p>


                <span class="text-[11px] font-medium px-3 py-1.5 rounded-lg
                        {{ $employee->employment_status === 'PKWTT'
        ? 'bg-gray-50 text-[#0B3D2E]'
        : 'bg-gray-50 text-gray-700' }}">
                    {{ $employee->employment_status }}
                </span>


                <span class="text-[11px] font-medium px-3 py-1.5 rounded-lg
                        {{ $employee->status === 'active'
        ? 'bg-gray-50 text-[#0B3D2E]'
        : 'bg-gray-50 text-gray-700' }}">
                    {{ $employee->status === 'active' ? 'Aktif' : ucfirst($employee->status) }}
                </span>
            </div>

            <p class="text-sm font-medium text-gray-600 mt-1.5">
                {{ $employee->position->name ?? '-' }}
                <span class="text-gray-300 mx-1">&bull;</span>
                {{ $employee->department->name ?? '-' }}
            </p>
            <p class="text-xs text-gray-400  mt-1">
                {{ $employee->employee_id }}
                <span class="text-gray-300 mx-1">&bull;</span>
                Bergabung {{ \Carbon\Carbon::parse($employee->join_date)->translatedFormat('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('hr.employees.documents', $employee->employee_id) }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-5 py-2.5
                           rounded-md flex items-center gap-1.5 hover:bg-gray-50 transition cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">folder_open</span>
                Dokumen
            </a>
            <a href="{{ route('hr.employees.edit', $employee->employee_id) }}" class="bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium px-5 py-2.5
                           rounded-md flex items-center gap-1.5 transition cursor-pointer shadow-sm">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Data
            </a>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


        <div class="md:col-span-2 space-y-6">


            <div class="bg-white rounded-md p-8 border border-gray-200">
                <h2 class="text-lg font-medium text-gray-800 mb-6 pb-4 border-b border-gray-100">Data Pribadi</h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-8">

                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">NIK (KTP)</p>
                        <p class="text-sm font-medium text-gray-800  mt-1.5">
                            {{ $employee->nik ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Email</p>
                        <p class="text-sm font-medium text-gray-800 mt-1.5 break-all">

                            @if (str_ends_with($employee->email ?? '', '@internal.local'))
                                <span class="text-gray-400 italic text-xs font-normal">Belum diaktivasi</span>
                            @else
                                {{ $employee->email ?? '—' }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">No. Telepon</p>
                        <p class="text-sm font-medium text-gray-800  mt-1.5">
                            {{ $employee->phone ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Posisi / Grade
                        </p>
                        <p class="text-sm font-medium text-gray-800 mt-1.5">
                            {{ $employee->position->name ?? '—' }}
                            @if ($employee->position->grade ?? null)
                                <span class="text-gray-400 font-normal">· {{ $employee->position->grade }}</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">NPWP</p>
                        <p class="text-sm font-medium text-gray-800  mt-1.5">
                            {{ $employee->npwp ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">No. BPJS</p>
                        <p class="text-sm font-medium text-gray-800  mt-1.5">
                            {{ $employee->bpjs_number ?? '—' }}
                        </p>
                    </div>

                </div>
            </div>


            <div class="bg-white rounded-md overflow-hidden border border-gray-200">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-medium text-gray-800">Riwayat Masa Berlaku Kontrak</h2>
                </div>

                @if ($contracts->isEmpty())
                    <div class="px-8 py-10 text-center text-sm text-gray-500">
                        Belum ada riwayat kontrak tercatat.
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($contracts as $c)
                                <div class="px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-4">
                                        <span class="w-10 h-10 rounded-md flex items-center justify-center shrink-0
                                                        {{ $c['status'] === 'Berjalan'
                            ? 'bg-gray-50 text-[#0B3D2E]'
                            : 'bg-gray-100 text-gray-400' }}">
                                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $c['type'] }}</p>
                                            <p class="text-xs text-gray-500  mt-0.5">{{ $c['range'] }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-medium px-3 py-1 rounded-lg uppercase tracking-wider
                                                    {{ $c['status'] === 'Berjalan'
                            ? 'bg-gray-50 text-[#0B3D2E]'
                            : 'bg-gray-100 text-gray-500' }}">
                                        {{ $c['status'] }}
                                    </span>
                                </div>
                        @endforeach
                    </div>
                @endif
            </div>


            <div class="bg-white rounded-md p-8 border border-gray-200">
                <h2 class="text-base font-medium text-gray-800 mb-6">Aktivitas Terbaru</h2>

                @if ($recentActivity->isEmpty())
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded-md text-center border border-dashed border-gray-200">Belum ada aktivitas tercatat.</p>
                @else
                    <div class="space-y-5">
                        @foreach ($recentActivity as $a)
                            <div class="flex items-center gap-4">
                                <span class="w-9 h-9 rounded-md bg-gray-50 text-gray-500
                                                         flex items-center justify-center shrink-0 border border-gray-100">
                                    <span class="material-symbols-outlined text-[18px]">{{ $a['icon'] }}</span>
                                </span>
                                <p class="text-sm text-gray-700 font-medium flex-1">{{ $a['label'] }}</p>
                                <p class="text-xs text-gray-400  whitespace-nowrap bg-gray-50 px-2 py-1 rounded-md">
                                    {{ $a['time'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>


        <div class="space-y-6">


            <div class="bg-white rounded-md p-6 border border-gray-200">
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-3">
                    Sisa Kuota Cuti {{ now()->year }}
                </p>
                <p class="text-3xl font-semibold  text-gray-800">
                    {{ $leaveBalance }}
                    <span class="text-sm font-medium text-gray-400">/ {{ $leaveQuota }} hari</span>
                </p>
                <div class="w-full h-1.5 rounded-full bg-gray-100 mt-4 overflow-hidden">
                    <div class="h-full bg-[#0B3D2E] rounded-full transition-all"
                        style="width: {{ $leaveQuota > 0 ? round($leaveBalance / $leaveQuota * 100) : 0 }}%">
                    </div>
                </div>
                <p class="text-[11px] text-gray-500 mt-2.5 font-medium">
                    Terpakai: {{ $leaveQuota - $leaveBalance }} hari
                </p>
            </div>


            <div class="bg-white rounded-md p-6 border border-gray-200">
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-3">
                    Gaji Pokok
                </p>
                <p class="text-2xl font-semibold  text-[#0B3D2E]">
                    Rp{{ number_format($employee->basic_salary ?? 0, 0, ',', '.') }}
                </p>
                <a href="{{ route('hr.payroll.slip', $employee->employee_id) }}"
                    class="text-[11px] font-medium text-gray-700 hover:text-gray-700 transition mt-3 inline-block">
                    Lihat slip gaji terakhir →
                </a>
            </div>


            <div class="bg-white rounded-md p-6 border border-gray-200">
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-4">
                    Dokumen Terunggah
                </p>
                <div class="space-y-3">
                    @foreach ($documents as $doc => $done)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 font-medium">{{ $doc }}</span>
                            </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('hr.employees.documents', $employee->employee_id) }}"
                        class="text-[11px] font-medium text-gray-700 hover:text-gray-700 transition inline-block">
                        Kelola dokumen →
                    </a>
                </div>
            </div>


            <div class="bg-white rounded-md p-6 border border-gray-200">
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-4">
                    Info Akun Karyawan
                </p>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-medium tracking-wide mb-1">
                            NIP (Username)
                        </p>
                        <p class=" font-semibold text-gray-800 text-lg bg-gray-50 p-2 rounded-lg border border-gray-100">
                            {{ $employee->employee_id }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
