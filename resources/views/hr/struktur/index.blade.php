@extends('layouts.hr')

@section('title', 'Struktur Organisasi')
@section('page-title', 'Struktur Organisasi')
@section('page-desc', 'Kelola departemen, posisi, dan job grade perusahaan.')

@section('page-action')
    <button class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
        <span class="material-symbols-outlined text-[16px]">add</span>
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
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Departemen</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ count($departments) }}</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Posisi</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ collect($departments)->sum('positions') }}</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Jumlah Job Grade</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ count($jobGrades) }}</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Karyawan</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ number_format(collect($departments)->sum('employees')) }}</p>
        </div>
    </div>

    {{-- ORG CHART VIEWER --}}
    <div class="card-flat rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-base font-bold text-on-surface">Org Chart Viewer</h2>
            <span class="text-[11px] font-bold text-on-surface-variant/60 bg-surface-container px-2.5 py-1 rounded">Klik departemen untuk detail</span>
        </div>

        <div class="flex flex-col items-center gap-6" x-data="{ open: null }">
            <div class="rounded-xl px-5 py-3 text-white text-sm font-bold" style="background-color:#0B3D2E;">
                CEO — Andi Wijaya
            </div>
            <div class="w-px h-6 bg-black/10"></div>
            <div class="grid grid-cols-4 gap-5 w-full">
                @foreach ($departments as $i => $dept)
                    <div class="flex flex-col items-center">
                        <div class="w-px h-6 bg-black/10"></div>
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full text-center card-flat rounded-lg px-3 py-3 border-t-[3px]" style="border-color:#FFD700;">
                            <p class="text-sm font-bold text-on-surface">{{ $dept['name'] }}</p>
                            <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ $dept['head'] }}</p>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition class="mt-2 w-full bg-surface-container rounded-lg p-3 text-xs text-on-surface-variant/60 space-y-1">
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
        <div class="card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5">
                <h2 class="text-base font-bold text-on-surface">Daftar Departemen</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Departemen</th>
                        <th class="px-6 py-3">Kepala Departemen</th>
                        <th class="px-6 py-3 text-right">Posisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($departments as $dept)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5 font-bold text-on-surface">{{ $dept['name'] }}</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/70">{{ $dept['head'] }}</td>
                            <td class="px-6 py-3.5 text-right font-mono-data text-on-surface">{{ $dept['positions'] }} <span class="text-on-surface-variant/40">({{ $dept['employees'] }} org)</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- JOB GRADES TABLE --}}
        <div class="card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                <h2 class="text-base font-bold text-on-surface">Job Grade &amp; Range Gaji</h2>
                <button type="button" class="text-xs font-bold text-primary hover:underline">+ Tambah Grade</button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Grade</th>
                        <th class="px-6 py-3">Level / Title</th>
                        <th class="px-6 py-3">Range Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($jobGrades as $grade)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <span class="text-[11px] font-mono-data font-bold text-primary bg-primary/10 px-2 py-1 rounded">{{ $grade['grade'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-bold text-on-surface">{{ $grade['title'] }}</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/70 font-mono-data text-xs">{{ $grade['range'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
