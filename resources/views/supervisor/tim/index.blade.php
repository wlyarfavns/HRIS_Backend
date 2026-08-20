@extends('layouts.supervisor')

@section('title', 'Anggota Tim')
@section('page-title', 'Anggota Tim')
@section('page-desc', 'Daftar karyawan yang berada di bawah pengawasan Anda.')

@section('content')
<div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-medium text-gray-800">Daftar Anggota Tim</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                    <th class="px-6 py-4">Karyawan</th>
                    <th class="px-6 py-4">Departemen</th>
                    <th class="px-6 py-4">Jabatan</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse ($team as $member)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $initials = strtoupper(substr($member->full_name ?? '?', 0, 1));
                                @endphp
                                <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center font-semibold text-xs border border-emerald-100">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $member->full_name }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $member->employee_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ $member->department?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $member->position->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium 
                                {{ $member->status === 'active' || $member->status === 'PKWTT' || $member->status === 'PKWT' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                </div>
                            <h4 class="text-gray-800 font-medium mb-2">Belum Ada Anggota Tim</h4>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto">
                                Saat ini tidak ada karyawan yang tercatat di bawah pengawasan Anda.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($team->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $team->links() }}
        </div>
    @endif
</div>
@endsection
