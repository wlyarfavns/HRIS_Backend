@extends('layouts.hr')

@section('title', 'Run Payroll Engine')
@section('page-title', 'Run Payroll Engine')
@section('page-desc', 'Konfigurasi periode, pilih batch karyawan, dan jalankan kalkulasi otomatis.')

@php

    $periodOptions = [];
    for ($i = 0; $i < 6; $i++) {
        $dt = now()->subMonths($i);
        $periodOptions[$dt->format('Y-m')] = $dt->translatedFormat('F Y');
    }
@endphp

@section('content')
<div x-data="{
    allChecked: false,
    checkedEmployees: [],
    employees: {{ Js::from($employees->pluck('id')) }},


    period: '{{ now()->format('Y-m') }}',
    startDate: '{{ now()->subMonth()->format('Y-m-26') }}',
    endDate: '{{ now()->format('Y-m-25') }}',


    changePeriod(newPeriod) {
        this.period = newPeriod;
        document.getElementById('formPeriod').value = newPeriod;


        let [year, month] = newPeriod.split('-');
        let currentMonth = new Date(year, parseInt(month) - 1, 1);


        let prevMonth = new Date(currentMonth);
        prevMonth.setMonth(prevMonth.getMonth() - 1);

        let py = prevMonth.getFullYear();
        let pm = String(prevMonth.getMonth() + 1).padStart(2, '0');


        this.startDate = `${py}-${pm}-26`;
        this.endDate = `${year}-${month}-25`;
    },

    toggleAll() {
        if (this.allChecked) {
            this.checkedEmployees = [...this.employees];
        } else {
            this.checkedEmployees = [];
        }
    },
    updateAllChecked() {
        this.allChecked = this.checkedEmployees.length === this.employees.length;
    },

    loading: false,
    loadingStep: 0,
    loadingDone: false,
    loadingSteps: [
        { label: 'Menarik Data Presensi', icon: 'fingerprint', desc: 'Mengunci rekap absensi karyawan' },
        { label: 'Mengkalkulasi Lembur', icon: 'more_time', desc: 'Sesuai komponen rumus lembur (SPL)' },
        { label: 'Menghitung PPh21 & BPJS', icon: 'account_balance', desc: 'Tabel TER PP 58/2023 & potongan BPJS' },
        { label: 'Menyusun Payroll Draft', icon: 'task_alt', desc: 'Merangkum pendapatan & potongan' },
    ],

    startEngine() {
        if (this.checkedEmployees.length === 0) return;
        this.loading = true;


        document.getElementById('selectedEmployeesInput').value = JSON.stringify(this.checkedEmployees);
        document.getElementById('formPeriod').value = this.period;
        document.getElementById('formStartDate').value = this.startDate;
        document.getElementById('formEndDate').value = this.endDate;

        const advance = (step) => {
            this.loadingStep = step;
            if (step < this.loadingSteps.length) {
                setTimeout(() => advance(step + 1), 1000);
            } else {
                this.loadingDone = true;
                setTimeout(() => document.getElementById('runEngineForm').submit(), 1500);
            }
        };
        setTimeout(() => advance(1), 400);
    }
}" x-init="checkedEmployees = []">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3 bg-gray-50/50">
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-800">Setup Periode</h3>
                        <p class="text-[11px] text-gray-500 mt-0.5">Atur rentang cut-off payroll</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2">Periode Penggajian</label>

                        <select x-model="period" @change="changePeriod($event.target.value)" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition cursor-pointer outline-none shadow-sm">
                            @foreach($periodOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label class="block text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2">
                            Cut-Off Absensi
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="date" x-model="startDate"
                                   class="flex-1 border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition cursor-pointer outline-none shadow-sm font-medium">
                            <span class="text-gray-400 text-xs font-medium">–</span>
                            <input type="date" x-model="endDate"
                                   class="flex-1 border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition cursor-pointer outline-none shadow-sm font-medium">
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-md border border-gray-200 p-6 shadow-sm">
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-4">Ringkasan Konfigurasi</p>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-md bg-gray-50 border border-gray-200">
                        <span class="text-xs font-medium text-gray-600">Karyawan Dipilih</span>
                        <span class="font-medium text-[#0B3D2E]  text-sm" x-text="checkedEmployees.length + ' / {{ $employees->count() }}'"></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-md bg-gray-50 border border-gray-100">
                        <span class="text-xs font-medium text-gray-600">Rentang Cut-Off</span>
                        <span class="font-medium text-gray-800  text-xs" x-text="startDate + ' s/d ' + endDate"></span>
                    </div>
                </div>
            </div>
        </div>


        <div class="lg:col-span-2">
            <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-medium text-gray-800">Pilih Karyawan</h3>
                        <p class="text-xs text-gray-500 mt-1">Pilih karyawan yang akan diproses payroll-nya</p>
                    </div>
                </div>
                <div class="overflow-x-auto flex-1 max-h-[32rem] custom-scrollbar">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 bg-gray-50 z-10 shadow-sm">
                            <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-200">
                                <th class="px-6 py-4 w-12">
                                    <input type="checkbox" x-model="allChecked" @change="toggleAll()" class="w-4 h-4 rounded border-gray-300 text-[#0B3D2E] focus:ring-[#0B3D2E]/20 cursor-pointer transition">
                                </th>
                                <th class="px-6 py-4">Karyawan</th>
                                <th class="px-6 py-4">Departemen</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach ($employees as $e)
                                @php $initials = strtoupper(substr($e->full_name, 0, 2)); @endphp
                                <tr class="hover:bg-gray-50 transition" :class="checkedEmployees.includes({{ $e->id }}) && 'bg-gray-50/50'">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="{{ $e->id }}" x-model="checkedEmployees" @change="updateAllChecked()" class="w-4 h-4 rounded border-gray-300 text-[#0B3D2E] focus:ring-[#0B3D2E]/20 cursor-pointer transition">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center font-medium text-xs text-[#0B3D2E] shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-sm leading-tight">{{ $e->full_name }}</p>
                                                <p class="text-[11px]  text-gray-500 mt-0.5">{{ $e->employee_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $e->department->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[10px] font-medium px-2.5 py-1 rounded-md bg-gray-50 text-[#0B3D2E] uppercase tracking-wider">Aktif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-5 border-t border-gray-100 bg-white flex items-center justify-between">
                    <p class="text-xs text-gray-500 font-medium">Siap diproses jika karyawan sudah dipilih</p>
                    <button type="button" @click="startEngine()" :disabled="checkedEmployees.length === 0"
                            :class="checkedEmployees.length === 0 ? 'opacity-50 cursor-not-allowed bg-gray-300' : 'bg-[#0B3D2E] hover:bg-[#043927] shadow-sm cursor-pointer'"
                            class="text-white text-sm font-medium px-6 py-2.5 rounded-md flex items-center gap-2 transition">
                        <span class="material-symbols-outlined text-[18px]">bolt</span> Kalkulasi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>


   <form id="runEngineForm" action="{{ route('hr.payroll.run') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="period" id="formPeriod" value="{{ now()->format('Y-m') }}">
        <input type="hidden" name="start_date" id="formStartDate">
        <input type="hidden" name="end_date" id="formEndDate">
        <input type="hidden" name="employee_ids" id="selectedEmployeesInput">
    </form>


    <div x-show="loading" x-cloak class="fixed inset-0 bg-[#043927]/90  z-[100] flex items-center justify-center p-4">
        <div class="max-w-lg w-full text-center space-y-8 animate-in fade-in zoom-in duration-300">
            <div class="relative flex items-center justify-center">
                <div class="w-28 h-28 rounded-md flex items-center justify-center shadow-sm transition-all duration-500" :class="loadingDone ? 'bg-emerald-500/30 ring-8 ring-emerald-500/40' : 'bg-white/10 ring-4 ring-white/10'">
                    <span class="material-symbols-outlined text-[64px] text-white transition-all duration-500" :class="loadingDone ? '' : 'animate-pulse'" x-text="loadingDone ? 'task_alt' : (loadingStep > 0 && loadingSteps[loadingStep-1] ? loadingSteps[loadingStep-1].icon : 'sync')"></span>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-white mb-2" x-text="loadingDone ? 'Kalkulasi Selesai!' : 'Payroll Engine Berjalan...'"></h2>
                <p class="text-emerald-100/70 text-sm font-medium" x-text="loadingDone ? 'Menyimpan draft dan mengalihkan ke halaman Review...' : (loadingStep > 0 && loadingSteps[loadingStep-1] ? loadingSteps[loadingStep-1].label : 'Memulai...')"></p>
            </div>
            <div class="max-w-sm mx-auto space-y-2">
                <div class="h-2 bg-black/20 rounded-full overflow-hidden shadow-sm">
                    <div class="h-full bg-white rounded-full transition-all duration-700 ease-out"
                         :style="'width: ' + (loadingDone ? 100 : (loadingStep / 4 * 100)) + '%'"></div>
                </div>
                <p class="text-xs text-emerald-100/50  text-right" x-text="(loadingDone ? 100 : Math.round(loadingStep / 4 * 100)) + '%'"></p>
            </div>
        </div>
    </div>
</div>
@endsection
