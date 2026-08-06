@extends('layouts.admin')

@section('title', 'Struktur Organisasi')
@section('page-title', 'Struktur Organisasi')
@section('page-desc', 'Kelola departemen, posisi, dan job grade perusahaan.')

@section('page-action')
    <button class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Departemen
    </button>
@endsection

@php
    $departments = [
        ['name' => 'Human Resources', 'head' => 'Rina Kartika', 'positions' => 4, 'employees' => 18],
        ['name' => 'Finance & Accounting', 'head' => 'Fajar Nugroho', 'positions' => 5, 'employees' => 22],
        ['name' => 'Engineering', 'head' => 'Dewi Lestari', 'positions' => 7, 'employees' => 64],
        ['name' => 'Sales & Marketing', 'head' => 'Budi Santoso', 'positions' => 6, 'employees' => 41],
    ];

    $jobGrades = [
        ['grade' => 'JG-1', 'title' => 'Staff', 'range' => 'Rp5.000.000 - Rp7.500.000'],
        ['grade' => 'JG-2', 'title' => 'Supervisor', 'range' => 'Rp7.500.000 - Rp12.000.000'],
        ['grade' => 'JG-3', 'title' => 'Manager', 'range' => 'Rp12.000.000 - Rp20.000.000'],
        ['grade' => 'JG-4', 'title' => 'Director', 'range' => 'Rp20.000.000 - Rp35.000.000'],
    ];
@endphp

@section('content')
    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="bg-white rounded-xl p-5">
            <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-2">TOTAL DEPARTEMEN</p>
            <p class="text-2xl font-bold font-mono-data text-gray-900">{{ count($departments) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5">
            <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-2">TOTAL POSISI</p>
            <p class="text-2xl font-bold font-mono-data text-gray-900">{{ collect($departments)->sum('positions') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5">
            <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-2">JOB GRADE</p>
            <p class="text-2xl font-bold font-mono-data text-emerald-700">{{ count($jobGrades) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5">
            <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-2">TOTAL KARYAWAN</p>
            <p class="text-2xl font-bold font-mono-data text-gray-900">{{ number_format(collect($departments)->sum('employees')) }}</p>
        </div>
    </div>

    {{-- ORG CHART VIEWER (simple interactive) --}}
    <div class="bg-white rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-base font-bold text-gray-900">Org Chart Viewer</h2>
            <span class="text-[11px] font-mono-data text-gray-400 bg-gray-50 px-2 py-0.5 rounded">Interaktif</span>
        </div>

        <div class="flex flex-col items-center gap-6" x-data="{ open: null }">
            <div class="bg-[#0d3b2e] text-white text-sm font-semibold px-5 py-3 rounded-lg">
                CEO — Andi Wijaya
            </div>
            <div class="w-px h-6 bg-gray-200"></div>
            <div class="grid grid-cols-4 gap-5 w-full">
                @foreach ($departments as $i => $dept)
                    <div class="flex flex-col items-center">
                        <div class="w-px h-6 bg-gray-200"></div>
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full text-center bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-3 hover:border-emerald-300 transition">
                            <p class="text-sm font-semibold text-gray-900">{{ $dept['name'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $dept['head'] }}</p>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition class="mt-2 w-full bg-gray-50 rounded-lg p-3 text-xs text-gray-500 space-y-1">
                            <p>{{ $dept['positions'] }} posisi terdaftar</p>
                            <p>{{ $dept['employees'] }} karyawan aktif</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5">
        {{-- DEPARTMENTS TABLE --}}
        <div class="bg-white rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Daftar Departemen</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3">Departemen</th>
                        <th class="px-6 py-3">Kepala</th>
                        <th class="px-6 py-3 text-right">Posisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($departments as $dept)
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-6 py-3.5 font-semibold text-gray-900">{{ $dept['name'] }}</td>
                            <td class="px-6 py-3.5 text-gray-500">{{ $dept['head'] }}</td>
                            <td class="px-6 py-3.5 text-right font-mono-data text-gray-900">{{ $dept['positions'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- JOB GRADES TABLE --}}
        <div class="bg-white rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900">Job Grade & Range Gaji</h2>
                <button class="text-xs font-semibold text-[#0d3b2e] hover:underline">+ Tambah Grade</button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3">Grade</th>
                        <th class="px-6 py-3">Level</th>
                        <th class="px-6 py-3">Range</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($jobGrades as $grade)
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-6 py-3.5">
                                <span class="text-xs font-mono-data font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded">{{ $grade['grade'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-gray-900">{{ $grade['title'] }}</td>
                            <td class="px-6 py-3.5 text-gray-500 font-mono-data text-xs">{{ $grade['range'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection