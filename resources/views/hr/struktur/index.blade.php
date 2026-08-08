@extends('layouts.hr')

@section('title', 'Struktur Organisasi')
@section('page-title', 'Struktur Organisasi')
@section('page-desc', 'Kelola departemen, posisi, dan job grade perusahaan.')

@section('page-action')
    <button @click="$dispatch('open-dept-modal')" x-data class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
        <span class="material-symbols-outlined text-[16px]">add</span>
        Tambah Departemen
    </button>
@endsection

@php
    // Data is now passed from StructureController
@endphp

@section('content')

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Departemen</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ count($departments) }}</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Posisi</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">-</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Jumlah Job Grade</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ count($jobGrades) }}</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-2">Total Karyawan</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ number_format($departments->sum('employees_count')) }}</p>
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
                            <p class="text-sm font-bold text-on-surface">{{ $dept->name }}</p>
                            <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ $dept->description ?? '-' }}</p>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition class="mt-2 w-full bg-surface-container rounded-lg p-3 text-xs text-on-surface-variant/60 space-y-1">
                            <p>- posisi terdaftar</p>
                            <p>{{ $dept->employees_count }} karyawan aktif</p>
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
                            <td class="px-6 py-3.5 font-bold text-on-surface">{{ $dept->name }}</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/70">{{ $dept->description ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-right font-mono-data text-on-surface">- <span class="text-on-surface-variant/40">({{ $dept->employees_count }} org)</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- JOB GRADES TABLE --}}
        <div class="card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                <h2 class="text-base font-bold text-on-surface">Job Grade &amp; Range Gaji</h2>
                <button type="button" @click="$dispatch('open-grade-modal')" x-data class="text-xs font-bold text-primary hover:underline">+ Tambah Grade</button>
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
                                <span class="text-[11px] font-mono-data font-bold text-primary bg-primary/10 px-2 py-1 rounded">JG-{{ $grade->level }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-bold text-on-surface">{{ $grade->name }}</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/70 font-mono-data text-xs">-</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH DEPARTEMEN --}}
    <div x-data="{ open: false }" @open-dept-modal.window="open = true" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;" x-transition>
        <div @click.away="open = false" class="bg-surface rounded-2xl w-full max-w-md p-6 shadow-xl">
            <h3 class="text-lg font-bold text-on-surface mb-4">Tambah Departemen Baru</h3>
            <form action="{{ route('hr.structure.dummy-dept') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/60 uppercase">Nama Departemen</label>
                        <input type="text" name="name" required class="w-full mt-1.5 px-3 py-2 bg-surface-container rounded-lg border border-transparent focus:border-primary/40 focus:ring-2 focus:ring-primary/20 outline-none transition text-sm" placeholder="Misal: IT Operations">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/60 uppercase">Kepala Departemen</label>
                        <input type="text" name="head" class="w-full mt-1.5 px-3 py-2 bg-surface-container rounded-lg border border-transparent focus:border-primary/40 focus:ring-2 focus:ring-primary/20 outline-none transition text-sm" placeholder="Opsional">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-bold text-on-surface-variant/70 hover:bg-surface-container rounded-lg transition">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 text-sm font-bold rounded-lg hover:brightness-110 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH GRADE --}}
    <div x-data="{ open: false }" @open-grade-modal.window="open = true" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;" x-transition>
        <div @click.away="open = false" class="bg-surface rounded-2xl w-full max-w-md p-6 shadow-xl">
            <h3 class="text-lg font-bold text-on-surface mb-4">Tambah Job Grade</h3>
            <form action="{{ route('hr.structure.dummy-grade') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/60 uppercase">Grade (Kode)</label>
                        <input type="text" name="grade" required class="w-full mt-1.5 px-3 py-2 bg-surface-container rounded-lg border border-transparent focus:border-primary/40 focus:ring-2 focus:ring-primary/20 outline-none transition text-sm" placeholder="Misal: JG-5">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/60 uppercase">Level / Title</label>
                        <input type="text" name="title" required class="w-full mt-1.5 px-3 py-2 bg-surface-container rounded-lg border border-transparent focus:border-primary/40 focus:ring-2 focus:ring-primary/20 outline-none transition text-sm" placeholder="Misal: Vice President">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/60 uppercase">Range Gaji</label>
                        <input type="text" name="range" required class="w-full mt-1.5 px-3 py-2 bg-surface-container rounded-lg border border-transparent focus:border-primary/40 focus:ring-2 focus:ring-primary/20 outline-none transition text-sm" placeholder="Misal: Rp30.000.000 - Rp50.000.000">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-bold text-on-surface-variant/70 hover:bg-surface-container rounded-lg transition">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 text-sm font-bold rounded-lg hover:brightness-110 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection
