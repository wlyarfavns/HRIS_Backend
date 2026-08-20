@extends('layouts.hr')

@section('title', 'Karyawan')
@section('page-title', 'Karyawan')
@section('page-desc', 'Master data karyawan, kontrak, dan status kepegawaian.')

@section('content')


    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="bg-white border border-gray-200 rounded-md p-5">
                <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center mb-4 text-gray-500">
                    <span class="material-symbols-outlined text-[20px]">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-semibold  text-gray-800 leading-none">{{ $s['value'] }}</p>
                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide mt-2">{{ $s['label'] }}</p>
                <p class="text-[11px] text-gray-400 mt-1">{{ $s['note'] }}</p>
            </div>
        @endforeach
    </div>


    @php
        $alpineItems = collect($employees->items())->map(function($e) {
            return [
                'name' => strtolower($e->full_name ?? ''),
                'nip' => strtolower($e->employee_id ?? ''),
                'dept' => strtolower($e->department_id ?? '')
            ];
        })->toJson();
    @endphp
    <div class="bg-white border border-gray-200 rounded-md overflow-hidden mt-6"
        x-data="{
            searchQuery: '{{ request('search') }}',
            selectedDept: '{{ request('department', 'Semua Departemen') }}',
            items: {{ $alpineItems }},
            get hasVisibleRows() {
                return this.items.some(i => 
                    (this.selectedDept === 'Semua Departemen' || i.dept === this.selectedDept) &&
                    (this.searchQuery === '' || i.name.includes(this.searchQuery.toLowerCase()) || i.nip.includes(this.searchQuery.toLowerCase()))
                );
            }
        }">

        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-medium text-gray-800">Daftar Karyawan</h2>
                <p class="text-xs text-gray-500 mt-1">{{ count($employees) }} karyawan ditampilkan</p>
            </div>

            <div class="flex items-center gap-3">


                <div class="relative">
                    <select x-model="selectedDept"
                        class="appearance-none text-xs font-medium border border-gray-200 rounded-lg pl-3 pr-8 py-2 bg-gray-50 text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 transition cursor-pointer">
                        <option value="Semua Departemen">Semua Departemen</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>


                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari NIP atau nama..."
                        class="w-56 pl-9 pr-3 py-2 bg-gray-50 rounded-lg text-xs font-medium border border-gray-200 text-gray-700 placeholder-gray-400 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 transition">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[18px]">search</span>
                </div>

                <a href="{{ route('hr.employees.onboarding') }}"
                    class="bg-[#0B3D2E] hover:bg-[#043927] shadow-sm text-white text-xs font-medium px-4 py-2 rounded-lg flex items-center gap-1.5 whitespace-nowrap transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    Onboarding Karyawan
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-[11px] text-gray-500 bg-gray-50 border-b border-gray-100 uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 font-medium">NIP</th>
                        <th class="px-6 py-3 font-medium">Karyawan</th>
                        <th class="px-6 py-3 font-medium">Departemen / Posisi</th>
                        <th class="px-6 py-3 font-medium">Tipe Kontrak</th>
                        <th class="px-6 py-3 font-medium">Tgl Bergabung</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 text-sm">
                    @forelse ($employees as $e)
                        <tr class="hover:bg-gray-50 transition"
                            x-show="(selectedDept === 'Semua Departemen' || selectedDept === '{{ $e->department_id }}') && (searchQuery === '' || '{{ strtolower(addslashes($e->full_name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($e->employee_id)) }}'.includes(searchQuery.toLowerCase()))">
                            <td class="px-6 py-4  font-medium">{{ $e->employee_id }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('hr.employees.show', $e->employee_id) }}"
                                    class="flex items-center gap-3 group w-fit">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-medium text-gray-600 border border-gray-300">
                                        {{ strtoupper(substr($e->full_name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#0B3D2E] transition">{{ $e->full_name }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $e->department?->name ?? '-' }} <span class="text-gray-300 mx-1">·</span>
                                {{ $e->position->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-medium px-2 py-1 rounded {{ $e->employment_status === 'PKWTT' ? 'bg-gray-50 text-[#0B3D2E]' : 'bg-gray-50 text-gray-700' }}">
                                    {{ $e->employment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4  text-gray-600 text-[13px]">
                                {{ \Carbon\Carbon::parse($e->join_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match ($e->status) {
                                        'active' => 'text-[#0B3D2E] bg-gray-50',
                                        'pending' => 'text-gray-700 bg-gray-50',
                                        'inactive', 'resigned' => 'text-gray-700 bg-gray-50',
                                        default => 'text-gray-500 bg-gray-100'
                                    };
                                    $statusDot = match ($e->status) {
                                        'active' => 'bg-gray-500',
                                        'pending' => 'bg-gray-50',
                                        'inactive', 'resigned' => 'bg-gray-50',
                                        default => 'bg-gray-400'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-medium {{ $statusColor }} capitalize">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ $e->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('hr.employees.show', $e->employee_id) }}" title="Lihat Detail"
                                        class="text-gray-400 hover:text-[#0B3D2E] transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="{{ route('hr.employees.edit', $e->id) }}" title="Edit Karyawan"
                                        class="text-gray-400 hover:text-gray-700 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <a href="{{ route('hr.employees.documents', $e->id) }}" title="Dokumen"
                                        class="text-gray-400 hover:text-gray-700 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">folder_open</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada data karyawan.</td>
                        </tr>
                    @endforelse
                    
                    @if($employees->count() > 0)
                        <tr x-show="!hasVisibleRows" style="display: none;" x-transition>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                <template x-if="searchQuery">
                                    <span>Tidak ada karyawan yang sesuai dengan pencarian "<span x-text="searchQuery" class="font-medium text-gray-700"></span>".</span>
                                </template>
                                <template x-if="!searchQuery">
                                    <span>Tidak ada karyawan di departemen ini.</span>
                                </template>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $employees->links() }}
        </div>
    </div>

@endsection
