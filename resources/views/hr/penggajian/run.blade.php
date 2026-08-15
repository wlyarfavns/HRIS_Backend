@extends('layouts.hr')

@section('title', 'Run Payroll Engine')
@section('page-title', 'Run Payroll Engine')
@section('page-desc', 'Konfigurasi periode, pilih batch karyawan, dan jalankan kalkulasi otomatis.')

@php
    // Generate opsi periode dinamis (Bulan ini + 5 bulan ke belakang)
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
    
    // State dinamis untuk Periode dan Tanggal
    period: '{{ now()->format('Y-m') }}',
    startDate: '{{ now()->subMonth()->format('Y-m-26') }}',
    endDate: '{{ now()->format('Y-m-25') }}',

    // Method saat dropdown periode diubah (Tanggal otomatis menyesuaikan)
    changePeriod(newPeriod) {
        this.period = newPeriod;
        document.getElementById('formPeriod').value = newPeriod;

        // Ambil tahun dan bulan dari periode baru (Contoh: '2026-07')
        let [year, month] = newPeriod.split('-');
        let currentMonth = new Date(year, parseInt(month) - 1, 1);
        
        // Cari bulan sebelumnya untuk startDate
        let prevMonth = new Date(currentMonth);
        prevMonth.setMonth(prevMonth.getMonth() - 1);

        let py = prevMonth.getFullYear();
        let pm = String(prevMonth.getMonth() + 1).padStart(2, '0');
        
        // Update input tanggal secara otomatis
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
        
        // Masukkan state ke input hidden untuk dikirim ke Controller Laravel
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

    <div class="grid grid-cols-3 gap-6">
        {{-- KIRI: FORM SETUP PERIODE --}}
        <div class="col-span-1 space-y-5">
            <div class="card-flat rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-black/5 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-[17px]">tune</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-on-surface">Setup Periode</h3>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wide mb-1.5">Periode Penggajian</label>
                        <!-- Menggunakan Dropdown Dinamis -->
                        <select x-model="period" @change="changePeriod($event.target.value)" class="w-full border border-black/10 rounded-xl px-3.5 py-2.5 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary/20 focus:outline-none transition cursor-pointer">
                            @foreach($periodOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Tanggal Cut-Off yang terhubung dengan Alpine x-model -->
                    <div>
                        <label class="block text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wide mb-1.5">
                            Cut-Off Absensi
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="date" x-model="startDate"
                                   class="flex-1 border border-black/10 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none transition cursor-pointer">
                            <span class="text-on-surface-variant/40 text-xs font-bold">–</span>
                            <input type="date" x-model="endDate"
                                   class="flex-1 border border-black/10 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none transition cursor-pointer">
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="card-flat rounded-2xl p-5">
                <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wide mb-3">Ringkasan Konfigurasi</p>
                <div class="space-y-2.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-on-surface-variant/60">Karyawan Dipilih</span>
                        <span class="font-bold text-primary font-mono-data" x-text="checkedEmployees.length + ' / {{ $employees->count() }}'"></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-on-surface-variant/60">Rentang Cut-Off</span>
                        <span class="font-bold text-on-surface font-mono-data" x-text="startDate + ' s/d ' + endDate"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: TABEL KARYAWAN --}}
        <div class="col-span-2">
            <div class="card-flat rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-on-surface">Pilih Karyawan</h3>
                    </div>
                </div>
                <div class="overflow-x-auto h-[26rem]">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-surface-container z-10">
                            <tr class="text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                                <th class="px-5 py-3.5">
                                    <input type="checkbox" x-model="allChecked" @change="toggleAll()" class="w-3.5 h-3.5 rounded accent-primary cursor-pointer">
                                </th>
                                <th class="px-4 py-3.5">Karyawan</th>
                                <th class="px-4 py-3.5">Departemen</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach ($employees as $e)
                                @php $initials = strtoupper(substr($e->full_name, 0, 2)); @endphp
                                <tr class="hover:bg-primary/4 transition" :class="checkedEmployees.includes({{ $e->id }}) && 'bg-primary/5'">
                                    <td class="px-5 py-3">
                                        <input type="checkbox" value="{{ $e->id }}" x-model="checkedEmployees" @change="updateAllChecked()" class="w-3.5 h-3.5 rounded accent-primary cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center font-bold text-xs text-primary shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-on-surface text-xs leading-tight">{{ $e->full_name }}</p>
                                                <p class="text-[10px] font-mono-data text-on-surface-variant/40">{{ $e->employee_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-on-surface-variant/70">{{ $e->department->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-primary/10 text-primary">Aktif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-black/5 bg-surface-container/40 flex items-center justify-between">
                    <p class="text-xs text-on-surface-variant/50">Pilih karyawan yang akan dikalkulasi gajinya</p>
                    <button type="button" @click="startEngine()" :disabled="checkedEmployees.length === 0"
                            :class="checkedEmployees.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:brightness-110 shadow-md'"
                            class="bg-primary text-white text-xs font-bold px-6 py-3 rounded-xl flex items-center gap-2 transition">
                        <span class="material-symbols-outlined text-[17px]">bolt</span> Kalkulasi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Tersembunyi untuk Dikirim ke Backend Laravel --}}
   <form id="runEngineForm" action="{{ route('hr.payroll.run') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="period" id="formPeriod" value="{{ now()->format('Y-m') }}">
        <input type="hidden" name="start_date" id="formStartDate">
        <input type="hidden" name="end_date" id="formEndDate">
        <input type="hidden" name="employee_ids" id="selectedEmployeesInput">
    </form>

    {{-- LOADING OVERLAY: ENGINE RUNNING --}}
    <div x-show="loading" x-cloak class="fixed inset-0 bg-[#0B3D2E]/95 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="max-w-lg w-full text-center space-y-8">
            <div class="relative flex items-center justify-center">
                <div class="w-24 h-24 rounded-3xl flex items-center justify-center" :class="loadingDone ? 'bg-emerald-400/20 ring-4 ring-emerald-400/30' : 'bg-white/10 ring-4 ring-white/10'">
                    <span class="material-symbols-outlined text-white text-[48px]" :class="!loadingDone && 'animate-spin'" style="animation-duration: 2s;" x-text="loadingDone ? 'check_circle' : 'settings'"></span>
                </div>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-white mb-2" x-text="loadingDone ? 'Kalkulasi Selesai!' : 'Payroll Engine Berjalan...'"></h2>
                <p class="text-white/60 text-sm">Menyimpan draft dan mengalihkan ke halaman Review...</p>
            </div>
            <div class="max-w-sm mx-auto">
                <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-400 to-primary rounded-full transition-all duration-700"
                         :style="'width: ' + (loadingDone ? 100 : (loadingStep / 4 * 100)) + '%'"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection