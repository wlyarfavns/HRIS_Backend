@extends('layouts.finance')

@section('title', 'Dashboard Finance')
@section('page-title', 'Dashboard Finance')
@section('page-desc', 'Ringkasan beban penggajian, pencairan reimbursement, dan SLA verifikasi.')

@section('content')
<div class="space-y-8">


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <a href="{{ route('finance.payroll.index') }}" class="block bg-[#0B3D2E] rounded-xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 border border-transparent">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100 mb-1">Total Payroll Pending</p>
            <p class="text-4xl font-bold text-white truncate">{{ $netPayrollFormatted ?? 'Rp 0' }}</p>
            <p class="text-xs text-emerald-200 mt-2">Kelola Penggajian </p>
        </a>

        <a href="{{ route('finance.reimbursement.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Total Reimburse</p>
            <p class="text-4xl font-bold text-gray-800 truncate">{{ $totalReimburseFormatted ?? 'Rp 0' }}</p>
            <p class="text-xs text-emerald-600 mt-2">Kelola Pencairan </p>
        </a>

        <a href="{{ route('finance.reimbursement.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">SLA Verifikasi</p>
            <p class="text-4xl font-bold text-gray-800">{{ $slaOnTimePercent ?? 0 }}%</p>
            <p class="text-xs text-emerald-600 mt-2">Penyelesaian tepat waktu </p>
        </a>

        <a href="{{ route('finance.reimbursement.index') }}" class="block bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Menunggu Pencairan</p>
            <p class="text-4xl font-bold text-gray-800">{{ $pendingCountLabel ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-2">Lihat daftar antrean </p>
        </a>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">


        <div class="xl:col-span-2 space-y-8">


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-4">
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Tren Pencairan Reimbursement</h3>
                        <p class="text-xs text-gray-500 mt-1">6 Bulan Terakhir</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        @if(isset($trendData) && count($trendData) > 0)
                            @foreach($trendData as $idx => $val)
                                <div class="flex justify-between items-center py-4 px-5 border border-gray-100 rounded-md hover:border-gray-200 shadow-sm transition group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-gray-50 transition">
                                            </div>
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#0B3D2E] transition">{{ $trendLabels[$idx] ?? '' }}</span>
                                    </div>
                                    <span class="text-base font-semibold  text-[#0B3D2E] bg-gray-50 px-4 py-2 rounded-md">{{ $val }} <span class="text-xs text-[#0B3D2E] font-sans font-medium">Juta</span></span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-sm font-medium text-gray-500">Data tren belum tersedia.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-4">
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Beban Reimbursement per Departemen</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if(isset($deptLabels) && count($deptLabels) > 0)
                        @foreach($deptLabels as $idx => $label)
                        <div class="flex justify-between items-center py-4 px-5 bg-white border border-gray-100 rounded-md hover:border-gray-300 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </div>
                            <span class="text-sm font-semibold  text-gray-800">{{ $deptData[$idx] }} <span class="text-xs text-gray-500 font-sans font-medium">Juta</span></span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <p class="text-sm font-medium text-gray-500">Data departemen tidak tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>


        <div class="xl:col-span-1 space-y-8">


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-4">
                    
                    <h3 class="text-base font-medium text-gray-800">Status Verifikasi Pencairan</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-md border border-gray-200">
                            <span class="font-medium text-sm text-[#0B3D2E]">Telah Dicairkan</span>
                            <span class="font-semibold  text-base text-[#0B3D2E]">{{ $verifiedCount ?? 0 }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-md border border-gray-200">
                            <span class="font-medium text-sm text-gray-700">Menunggu (Pending)</span>
                            <span class="font-semibold  text-base text-gray-700">{{ $pendingCount ?? 0 }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-md border border-gray-200">
                            <span class="font-medium text-sm text-gray-700">Ditolak</span>
                            <span class="font-semibold  text-base text-gray-700">{{ $rejectedCount ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-widest">Total Pengajuan</span>
                            <span class="font-semibold text-gray-800 text-lg ">{{ $totalSla ?? 0 }}</span>
                        </div>
                        <a href="{{ route('finance.reimbursement.index') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-[#0B3D2E] text-white font-medium text-sm rounded-md hover:bg-[#043927] transition shadow-sm">
                            Lihat Data Pending
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-medium text-gray-800">Akses Cepat</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('finance.payroll.index') }}" class="flex items-center gap-4 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#0B3D2E] transition">Proses Payroll</span>
                    </a>

                    <a href="#" class="flex items-center gap-4 p-4 rounded-md bg-white border border-gray-200 hover:border-[#0B3D2E] shadow-sm transition group">
                        
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#0B3D2E] transition">Laporan Keuangan</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
