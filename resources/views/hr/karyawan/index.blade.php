@extends('layouts.hr')

@section('title', 'Karyawan')
@section('page-title', 'Karyawan')
@section('page-desc', 'Master data karyawan, kontrak, dan status kepegawaian.')

@php
    $stats = [
        ['label' => 'Total Karyawan', 'value' => '1.284', 'icon' => 'groups', 'note' => 'Aktif di seluruh perusahaan'],
        ['label' => 'Kontrak Akan Habis', 'value' => '18', 'icon' => 'event_upcoming', 'note' => 'H-30 dari sekarang'],
        ['label' => 'Karyawan Baru', 'value' => '7', 'icon' => 'person_add', 'note' => '30 hari terakhir'],
        ['label' => 'PKWT / PKWTT', 'value' => '312 / 972', 'icon' => 'assignment', 'note' => 'Rasio tipe kontrak'],
    ];

    $employees = [
        ['nip' => 'EMP-00231', 'name' => 'Michael Scott', 'dept' => 'Sales', 'pos' => 'Regional Manager', 'type' => 'PKWTT', 'join' => '12 Jan 2019', 'status' => 'Aktif', 'avatar' => 14],
        ['nip' => 'EMP-00567', 'name' => 'Pam Beesly', 'dept' => 'Front Office', 'pos' => 'Receptionist', 'type' => 'PKWTT', 'join' => '03 Mar 2021', 'status' => 'Aktif', 'avatar' => 47],
        ['nip' => 'EMP-00812', 'name' => 'Jim Halpert', 'dept' => 'Sales', 'pos' => 'Sales Executive', 'type' => 'PKWT', 'join' => '18 Agu 2024', 'status' => 'Kontrak Habis 12 Sep', 'avatar' => 12],
        ['nip' => 'EMP-00933', 'name' => 'Dwight Schrute', 'dept' => 'Sales', 'pos' => 'Assistant Manager', 'type' => 'PKWTT', 'join' => '05 Feb 2020', 'status' => 'Aktif', 'avatar' => 51],
        ['nip' => 'EMP-01044', 'name' => 'Angela Martin', 'dept' => 'Finance', 'pos' => 'Accounting Staff', 'type' => 'PKWT', 'join' => '01 Okt 2024', 'status' => 'Aktif', 'avatar' => 33],
    ];
@endphp

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-[20px]">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data text-on-surface leading-none">{{ $s['value'] }}</p>
                <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-2">{{ $s['label'] }}</p>
                <p class="text-[11px] text-on-surface-variant/40 mt-1">{{ $s['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- TABLE CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Karyawan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($employees) }} dari 1.284 karyawan ditampilkan</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <select class="appearance-none text-xs font-bold border border-black/10 rounded-lg pl-3 pr-8 py-2.5 bg-white
                                   hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition cursor-pointer">
                        <option>Semua Departemen</option>
                        <option>Sales</option>
                        <option>Finance</option>
                        <option>Front Office</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                </div>

                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                    <input type="text" placeholder="Cari NIP atau nama..."
                           class="w-56 pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition">
                </div>

                <div class="w-px h-6 bg-black/10 mx-0.5"></div>

                <a href="{{ route('hr.employees.onboarding') }}"
                   class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg
                          flex items-center gap-1.5 whitespace-nowrap transition">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Onboarding Karyawan
                </a>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">NIP</th>
                    <th class="px-6 py-3.5">Karyawan</th>
                    <th class="px-6 py-3.5">Departemen / Posisi</th>
                    <th class="px-6 py-3.5">Tipe Kontrak</th>
                    <th class="px-6 py-3.5">Tgl Bergabung</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($employees as $e)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $e['nip'] }}</td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('hr.employees.show', $e['nip']) }}" class="flex items-center gap-3 group w-fit">
                                <img src="https://i.pravatar.cc/32?img={{ $e['avatar'] }}" class="w-8 h-8 rounded-full object-cover" alt="{{ $e['name'] }}">
                                <span class="font-bold text-on-surface group-hover:text-primary transition">{{ $e['name'] }}</span>
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70">
                            {{ $e['dept'] }} <span class="text-on-surface-variant/40">·</span> {{ $e['pos'] }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $e['type'] === 'PKWTT' ? 'bg-primary/10 text-primary' : 'bg-amber-500/10 text-amber-700' }}">
                                {{ $e['type'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $e['join'] }}</td>
                        <td class="px-6 py-3.5">
                            <span class="text-xs font-semibold {{ str_contains($e['status'], 'Habis') ? 'text-error' : 'text-primary' }}">
                                {{ $e['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('hr.employees.show', $e['nip']) }}" title="Lihat Detail"
                                   class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                                <a href="{{ route('hr.employees.edit', $e['nip']) }}" title="Edit"
                                   class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <a href="{{ route('hr.employees.documents', $e['nip']) }}" title="Dokumen"
                                    class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection