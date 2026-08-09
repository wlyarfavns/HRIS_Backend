@extends('layouts.hr')

@section('title', 'Karyawan')
@section('page-title', 'Karyawan')
@section('page-desc', 'Master data karyawan, kontrak, dan status kepegawaian.')

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
    <div class="card-flat rounded-2xl overflow-hidden mt-6">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Karyawan</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">{{ count($employees) }} karyawan ditampilkan</p>
            </div>

            {{-- BUNGKUS DENGAN FORM AGAR BISA SUBMIT --}}
            <form method="GET" action="{{ route('hr.employees.index') }}" class="flex items-center gap-2.5">

                {{-- DROPDOWN DEPARTEMEN --}}
                <div class="relative">
                    <select name="department" onchange="this.form.submit()"
                        class="appearance-none text-xs font-bold border border-black/10 rounded-lg pl-3 pr-8 py-2.5 bg-white hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition cursor-pointer">
                        <option value="Semua Departemen" {{ request('department') == 'Semua Departemen' ? 'selected' : '' }}>
                            Semua Departemen</option>

                        {{-- Looping dari database departemen --}}
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                </div>

                {{-- KOLOM PENCARIAN --}}
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>

                    {{-- Tambahkan name="search", onkeydown, dan set value dari request --}}
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP atau nama..."
                        onkeydown="if(event.keyCode==13){this.form.submit();}"
                        class="w-56 pl-9 pr-3 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent hover:border-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 focus:bg-white transition">
                </div>

                <div class="w-px h-6 bg-black/10 mx-0.5"></div>

                <a href="{{ route('hr.employees.onboarding') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 shadow-sm text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 whitespace-nowrap transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Onboarding Karyawan
                </a>
            </form>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr
                    class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
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
                @forelse ($employees as $e)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $e->employee_id }}</td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('hr.employees.show', $e->employee_id) }}"
                                class="flex items-center gap-3 group w-fit">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($e->full_name) }}&background=random"
                                    class="w-8 h-8 rounded-full object-cover" alt="{{ $e->full_name }}">
                                <span
                                    class="font-bold text-on-surface group-hover:text-primary transition">{{ $e->full_name }}</span>
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70">
                            {{ $e->department->name ?? '-' }} <span class="text-on-surface-variant/40">·</span>
                            {{ $e->position->name ?? '-' }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span
                                class="text-[11px] font-bold px-2.5 py-1 rounded {{ $e->employment_status === 'PKWTT' ? 'bg-primary/10 text-primary' : 'bg-amber-500/10 text-amber-700' }}">
                                {{ $e->employment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">
                            {{ \Carbon\Carbon::parse($e->join_date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-3.5">
                            @php
                                $statusColor = match ($e->status) {
                                    'active' => 'text-primary',
                                    'pending' => 'text-amber-600',
                                    'inactive', 'resigned' => 'text-error',
                                    default => 'text-on-surface-variant/50'
                                };
                            @endphp
                            <span class="text-xs font-semibold {{ $statusColor }} capitalize">
                                {{ $e->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('hr.employees.show', $e->employee_id) }}" title="Lihat Detail"
                                    class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                                <a href="{{ route('hr.employees.edit', $e->employee_id) }}" title="Edit"
                                    class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <a href="{{ route('hr.employees.documents', $e->employee_id) }}" title="Dokumen"
                                    class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">folder_open</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-on-surface-variant/50">Belum ada data karyawan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    

@endsection