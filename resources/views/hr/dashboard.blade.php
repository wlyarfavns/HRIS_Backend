@extends('layouts.hr')

@section('title', 'Dashboard HR')
@section('page-title', 'Dashboard HR')
@section('page-desc', 'Ringkasan data operasional SDM, kehadiran, dan pengajuan administratif.')

@section('content')
<div class="space-y-8">


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <a href="{{ route('hr.employees.index') }}" class="block bg-[#0B3D2E] rounded-xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 border border-transparent">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100 mb-1">Total Pegawai</p>
            <p class="text-4xl font-bold text-white">{{ $totalEmployees ?? 0 }}</p>
            <p class="text-xs text-emerald-200 mt-2">Lihat daftar pegawai </p>
        </a>

        <a href="{{ route('hr.employees.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pegawai Baru</p>
            <p class="text-4xl font-bold text-gray-800">{{ $newEmployees ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-2">Dalam 30 hari terakhir </p>
        </a>

        <a href="{{ route('hr.approvals.leave') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Perlu Approval</p>
            <p class="text-4xl font-bold text-gray-800">{{ ($pendingLeave ?? 0) + ($pendingOvertimeHr ?? 0) }}</p>
            <p class="text-xs text-emerald-600 mt-2">Cuti & Lembur </p>
        </a>

        <a href="{{ route('hr.employees.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Kontrak Expire</p>
            <p class="text-4xl font-bold text-gray-800">{{ $expiringContracts ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-2">Dalam 30 hari ke depan </p>
        </a>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">


        <div class="xl:col-span-2 space-y-8">


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">Data Kehadiran</h3>
                            <p class="text-xs text-gray-500 mt-1">Bulan Berjalan</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="bg-gray-50 p-6 rounded-md border border-gray-100">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-2">Tingkat Kehadiran</p>
                        <div class="flex items-end gap-3">
                            <span class="text-3xl font-semibold  text-[#0B3D2E]">{{ $attendanceRate ?? 0 }}%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Dari total hari kerja</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-md border border-gray-100">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-2">Rata-rata Tepat Waktu</p>
                        <div class="flex items-end gap-3">
                            <span class="text-3xl font-semibold  text-[#0B3D2E]">{{ $avgProductivity ?? 0 }}%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Berdasarkan jam masuk</p>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        
                        <h3 class="text-lg font-medium text-gray-800">Kategori Cuti & Izin Terbanyak</h3>
                    </div>
                </div>
                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @if(isset($leaveLabels) && count($leaveLabels) > 0)
                        @foreach($leaveLabels as $idx => $label)
                        <div class="p-5 rounded-md border border-gray-100 flex items-center justify-between bg-white shadow-sm hover:border-gray-200 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E] font-medium text-sm">
                                    #{{ $idx + 1 }}
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 text-sm">{{ $label }}</h4>
                                    <p class="text-[11px] text-gray-500 mt-1">Kategori</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-semibold  text-[#0B3D2E]">{{ $leaveCounts[$idx] }}</span>
                                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Pengajuan</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-span-2 text-center py-8">
                            <p class="text-sm font-medium text-gray-500">Belum ada pengajuan cuti/izin.</p>
                        </div>
                    @endif
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        
                        <h3 class="text-lg font-medium text-gray-800">Distribusi Pegawai per Departemen</h3>
                    </div>
                </div>
                <div class="p-4">
                    @if(isset($deptLabels) && count($deptLabels) > 0)
                        <div class="divide-y divide-gray-100">
                        @foreach($deptLabels as $idx => $label)
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-md transition">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </div>
                            <span class="text-sm font-medium  text-[#0B3D2E] px-3 py-1 bg-gray-50 rounded-md border border-gray-200">{{ $deptCounts[$idx] }} Pegawai</span>
                        </div>
                        @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-sm font-medium text-gray-500">Data departemen tidak tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>


        <div class="xl:col-span-1 space-y-8">


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-medium text-gray-800">Perlu Tindakan (HR)</h3>
                    <p class="text-xs text-gray-500 mt-1">Pengajuan menunggu persetujuan</p>
                </div>
                <div class="p-6 space-y-4">
                    <a href="{{ route('hr.approvals.leave') }}" class="flex justify-between items-center p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[16px] text-gray-700">event_note</span>
                            </div>
                            <span class="font-medium text-sm text-gray-700 group-hover:text-[#0B3D2E]">Cuti & Izin</span>
                        </div>
                        <span class="text-gray-700  font-medium text-base px-2 py-1 bg-gray-50 rounded-md">{{ $pendingLeave ?? 0 }}</span>
                    </a>

                    <a href="{{ route('hr.approvals.overtime') }}" class="flex justify-between items-center p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[16px] text-gray-700">schedule</span>
                            </div>
                            <span class="font-medium text-sm text-gray-700 group-hover:text-[#0B3D2E]">Lembur (SPL)</span>
                        </div>
                        <span class="text-gray-700  font-medium text-base px-2 py-1 bg-gray-50 rounded-md">{{ $pendingOvertimeHr ?? 0 }}</span>
                    </a>

                    <a href="{{ route('hr.approvals.reimbursement') }}" class="flex justify-between items-center p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[16px] text-gray-700">receipt_long</span>
                            </div>
                            <span class="font-medium text-sm text-gray-700 group-hover:text-[#0B3D2E]">Reimbursement</span>
                        </div>
                        <span class="text-gray-700  font-medium text-base px-2 py-1 bg-gray-50 rounded-md">{{ $pendingReimbursement ?? 0 }}</span>
                    </a>
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-medium text-gray-800">Aktivitas Terkini</h3>
                    <p class="text-xs text-gray-500 mt-1">Log operasi sistem terbaru</p>
                </div>
                <div class="p-2">
                    @if(isset($recentActivity) && $recentActivity->count() > 0)
                        <div class="divide-y divide-gray-100">
                        @foreach($recentActivity as $act)
                            <div class="p-4 flex items-start gap-3 hover:bg-gray-50 rounded-md transition">
                                <div class="w-8 h-8 rounded-full {{ $act['bg'] }} flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[16px] {{ $act['color'] }}">{{ $act['icon'] }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 leading-tight">{{ $act['label'] }}</p>
                                    <p class="text-[11px] text-gray-500 mt-1 ">{{ $act['sub'] }}</p>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-sm font-medium text-gray-500">Tidak ada aktivitas terbaru.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
