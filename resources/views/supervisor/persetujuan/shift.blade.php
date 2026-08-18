@extends('layouts.supervisor')

@section('title', 'Persetujuan Tukar Shift Tim')
@section('page-title', 'Persetujuan Tukar Shift Tim')
@section('page-desc', 'Verifikasi dan setujui pengajuan tukar shift anggota tim Anda sebelum diteruskan ke HR.')

@php
    $statsView = [
        ['label' => 'Tukar Shift Pending', 'value' => $stats['pending_review'] . ' Pengajuan', 'icon' => 'sync', 'color' => 'text-gray-700', 'bg' => 'bg-gray-50'],
    ];

    $badge = [
        'pending_spv'  => 'bg-gray-50 text-gray-700 border border-gray-200',
        'pending_hr'   => 'bg-gray-50 text-gray-700 border border-gray-200',
        'approved'     => 'bg-gray-50 text-[#0B3D2E] border border-gray-200',
        'rejected'     => 'bg-gray-50 text-gray-700 border border-gray-200',
    ];

    $statusLabel = [
        'pending_spv'  => 'Pending SPV',
        'pending_hr'   => 'Pending HR',
        'approved'     => 'Disetujui HR',
        'rejected'     => 'Ditolak',
    ];
@endphp

@section('content')
<div x-data="{ showModal: false, selectedReq: null }">

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 px-4 py-3 bg-gray-50 text-emerald-800 border border-gray-200 rounded-md text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 px-4 py-3 bg-gray-50 text-gray-700 border border-gray-200 rounded-md text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach ($statsView as $s)
            <div class="bg-white border border-gray-100 rounded-md p-6 shadow-sm relative overflow-hidden group hover:border-gray-200 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</span>
                </div>
                <p class="text-3xl font-semibold  text-gray-800 leading-none mb-2">{{ $s['value'] }}</p>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 {{ $s['bg'] }} rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
            </div>
        @endforeach
    </div>


    <div class="mt-8 mb-8">
        <h2 class="text-lg font-medium text-gray-800 mb-6 flex items-center gap-2">
            Menunggu Persetujuan Anda
        </h2>

        @if ($pending->isEmpty())
            <div class="bg-white rounded-md shadow-sm border border-gray-100 p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    </div>
                <h3 class="text-sm font-medium text-gray-800">Tidak ada tugas</h3>
                <p class="text-xs text-gray-500 mt-1">Belum ada pengajuan tukar shift yang perlu ditinjau.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($pending as $r)
                    <div class="bg-white rounded-md shadow-sm p-6 hover:shadow-sm transition group border border-gray-100 hover:border-[#0B3D2E]/30">
                        <div class="flex items-start gap-4 mb-5">
                            <div class="w-12 h-12 rounded-md bg-gray-50 flex items-center justify-center shrink-0">
                                </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-800 group-hover:text-[#0B3D2E] transition-colors">{{ $r->fromEmployee->full_name ?? $r->fromEmployee->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Tukar dengan: <span class="font-medium text-gray-800">{{ $r->toEmployee->full_name ?? $r->toEmployee->name }}</span></p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4 text-xs mb-5 space-y-3  border border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-sans">Shift Awal:</span>
                                <span class="font-medium text-gray-800">{{ $r->fromAssignment->shiftType->name }} ({{ \Carbon\Carbon::parse($r->fromAssignment->date)->translatedFormat('d M') }})</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-sans">Shift Tujuan:</span>
                                <span class="font-medium text-[#0B3D2E]">{{ $r->toAssignment->shiftType->name }} ({{ \Carbon\Carbon::parse($r->toAssignment->date)->translatedFormat('d M') }})</span>
                            </div>
                            <div class="flex flex-col gap-2 border-t border-gray-200 pt-3 mt-3">
                                <span class="text-gray-500 font-sans">Alasan:</span>
                                <span class="text-gray-700 bg-white p-3 rounded-md border border-gray-200">{{ $r->reason ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <form action="{{ route('supervisor.approvals.shift.reject', $r->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full h-11 rounded-md font-medium text-sm bg-gray-50 text-gray-700 hover:bg-gray-50 transition border border-gray-200" onclick="return confirm('Tolak pengajuan tukar shift ini?')">Tolak</button>
                            </form>
                            <form action="{{ route('supervisor.approvals.shift.approve', $r->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full h-11 rounded-md font-medium text-sm bg-[#0B3D2E] text-white hover:bg-[#043927] transition shadow-sm" onclick="return confirm('Setujui pengajuan tukar shift ini?')">Setujui</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6">
            {{ $pending->links() }}
        </div>
    </div>


    <div class="bg-white rounded-md shadow-sm border border-gray-100 overflow-hidden mt-10">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-medium text-gray-800 flex items-center gap-2">
                Riwayat Persetujuan
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-medium text-[11px] uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-4">Pihak 1 (Pengaju)</th>
                        <th class="px-6 py-4">Pihak 2 (Ditukar)</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Waktu Persetujuan (SPV)</th>
                        <th class="px-8 py-4">Status Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($history as $h)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <p class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $h->fromEmployee->full_name ?? $h->fromEmployee->name }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5 ">{{ $h->fromAssignment->shiftType->name }} ({{ \Carbon\Carbon::parse($h->fromAssignment->date)->translatedFormat('d M') }})</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800 text-sm">{{ $h->toEmployee->full_name ?? $h->toEmployee->name }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5 ">{{ $h->toAssignment->shiftType->name }} ({{ \Carbon\Carbon::parse($h->toAssignment->date)->translatedFormat('d M') }})</p>
                            </td>
                            <td class="px-6 py-4 truncate max-w-[200px] text-sm text-gray-600">{{ $h->reason ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs  text-gray-600">{{ $h->approved_at ? $h->approved_at->translatedFormat('d M Y, H:i') : '-' }}</td>
                            <td class="px-8 py-4">
                                @php
                                    $statusClass = $badge[$h->status] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
                                    $statusText = $statusLabel[$h->status] ?? $h->status;
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-[11px] font-medium {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-500 text-sm">Belum ada riwayat persetujuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $history->links() }}
        </div>
    </div>
</div>

<x-auto-refresh />
@endsection

