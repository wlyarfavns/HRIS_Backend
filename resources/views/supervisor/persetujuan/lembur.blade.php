@extends('layouts.supervisor')

@section('title', 'Persetujuan Lembur (SPL) Tim')
@section('page-title', 'Persetujuan Lembur (SPL) Tim')
@section('page-desc', 'Verifikasi dan setujui Surat Perintah Lembur anggota tim Anda sebelum diteruskan ke HR.')

@php
    $statsView = [
        ['label' => 'SPL Pending Review', 'value' => $stats['pending_review'] . ' Pengajuan', 'icon' => 'timelapse', 'color' => 'text-gray-700', 'bg' => 'bg-gray-50'],
        ['label' => 'Sedang Lembur Hari Ini', 'value' => $stats['today_overtime'] . ' Pegawai', 'icon' => 'schedule', 'color' => 'text-gray-700', 'bg' => 'bg-gray-50'],
        ['label' => 'Total Jam Lembur Tim', 'value' => number_format($stats['total_hours'], 1, ',', '.') . ' Jam', 'icon' => 'query_stats', 'color' => 'text-[#0B3D2E]', 'bg' => 'bg-gray-50'],
    ];

    $badge = [
        'approved_spv' => 'bg-gray-50 text-[#0B3D2E] border border-gray-200',
        'locked'       => 'bg-gray-100 text-gray-600 border border-gray-200',
        'rejected'     => 'bg-gray-50 text-gray-700 border border-gray-200',
    ];

    $statusLabel = [
        'approved_spv' => 'Approved SPV',
        'locked'       => 'Locked HR',
        'rejected'     => 'Ditolak',
    ];
@endphp

@section('content')
<div x-data="{
    showModal: false,
    selectedReq: null,
}">

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 px-4 py-3 bg-gray-50 text-emerald-800 border border-gray-200 rounded-md text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach ($statsView as $s)
            <div class="bg-white border border-gray-100 rounded-md p-6 shadow-sm relative overflow-hidden group hover:border-{{ explode('-', $s['color'])[1] }}-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</span>
                </div>
                <p class="text-3xl font-semibold  text-gray-800 leading-none mb-2">{{ $s['value'] }}</p>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 {{ $s['bg'] }} rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Surat Perintah Lembur Menunggu Persetujuan</h2>
                <p class="text-xs text-gray-500 mt-1">Setelah Anda setujui, HR Operations akan mengunci data untuk perhitungan payroll</p>
            </div>
            @if ($pending->count() > 0)
                <span class="text-[11px] font-medium px-4 py-1.5 rounded-full bg-gray-50 text-gray-700 border border-gray-200">Wajib Review SPV</span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-wider">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Keperluan &amp; Proyek</th>
                        <th class="px-6 py-4">Durasi Waktu</th>
                        <th class="px-6 py-4 text-right">Estimasi Upah</th>
                        <th class="px-8 py-4 text-center">Aksi Supervisor</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($pending as $r)
                        @php $upah = \App\Models\OvertimeRequest::calculateOvertimePay($r->salary_snapshot, $r->hours); @endphp
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($r->employee->full_name) }}&background=random"
                                         class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="{{ $r->employee->full_name }}">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $r->employee->full_name }}</p>
                                        <p class="text-[11px]  text-gray-500 mt-0.5">
                                            {{ $r->employee->employee_id }} · {{ $r->employee->department->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-800">{{ $r->project ?? '-' }}</p>
                                <p class="text-[11px] text-gray-500  mt-0.5">
                                    {{ $r->date->translatedFormat('d M Y') }}
                                    ({{ \Carbon\Carbon::parse($r->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($r->end_time)->format('H:i') }})
                                </p>
                            </td>
                            <td class="px-6 py-4  font-medium text-sm text-[#0B3D2E]">
                                {{ rtrim(rtrim(number_format($r->hours, 1, ',', '.'), '0'), ',') }} Jam
                            </td>
                            <td class="px-6 py-4 text-right  font-semibold text-sm text-gray-800">
                                Rp{{ number_format($upah, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-4 text-center">
                                <button type="button"
                                    @click="showModal = true; selectedReq = @js([
                                        'id'      => $r->id,
                                        'name'    => $r->employee->full_name,
                                        'project' => $r->project,
                                        'hours'   => $r->hours,
                                        'start'   => \Carbon\Carbon::parse($r->start_time)->format('H:i'),
                                        'end'     => \Carbon\Carbon::parse($r->end_time)->format('H:i'),
                                        'notes'   => $r->notes,
                                        'upah'    => $upah,
                                    ])"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium transition shadow-sm whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Review SPL Tim
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Tidak ada pengajuan lembur yang menunggu review Anda.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-6 pb-6">
            {{ $pending->links() }}
        </div>
    </div>


    <div class="bg-white rounded-md shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-medium text-gray-800">Riwayat Keputusan SPL Tim</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-wider">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Durasi</th>
                        <th class="px-6 py-4">Keperluan Proyek</th>
                        <th class="px-6 py-4">Keputusan Anda</th>
                        <th class="px-8 py-4">Status di HR</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($history as $r)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($r->employee->full_name) }}&background=random"
                                         class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="">
                                    <span class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $r->employee->full_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4  text-sm font-medium text-gray-800">{{ $r->hours }} Jam</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $r->project ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                {{ $r->status === 'rejected' ? 'Ditolak' : 'Disetujui' }} Anda, {{ $r->approved_at?->translatedFormat('d M') ?? '-' }}
                            </td>
                            <td class="px-8 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-medium whitespace-nowrap {{ $badge[$r->status] ?? '' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $statusLabel[$r->status] ?? $r->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada riwayat keputusan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-6 pb-6">
            {{ $history->links() }}
        </div>
    </div>


    <div x-show="showModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showModal = false">
        <div class="bg-white rounded-md max-w-lg w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in duration-200 border border-gray-100" x-show="selectedReq">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 text-[#0B3D2E] flex items-center justify-center">
                        </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Persetujuan SPL Tim</h3>
                        <p class="text-xs text-gray-500" x-text="selectedReq ? selectedReq.name : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div class="p-5 rounded-md bg-gray-50 border border-gray-200 space-y-3  text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-sans text-xs">Karyawan:</span>
                        <span class="font-medium text-gray-800" x-text="selectedReq ? selectedReq.name : ''"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-sans text-xs">Keperluan Proyek:</span>
                        <span class="font-medium text-gray-800" x-text="selectedReq ? selectedReq.project : ''"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-sans text-xs">Durasi &amp; Jam:</span>
                        <span class="font-medium text-[#0B3D2E]" x-text="selectedReq ? selectedReq.hours + ' Jam (' + selectedReq.start + '–' + selectedReq.end + ')' : ''"></span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-gray-700 font-sans font-medium text-xs">Estimasi Upah Lembur:</span>
                        <span class="font-semibold text-base text-[#0B3D2E]" x-text="selectedReq ? 'Rp' + Number(selectedReq.upah).toLocaleString('id-ID') : ''"></span>
                    </div>
                </div>

                <div>
                    <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">Catatan Pekerjaan</label>
                    <p class="p-4 rounded-md border border-gray-200 bg-gray-50 text-gray-700 leading-relaxed" x-text="selectedReq ? selectedReq.notes : ''"></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="showModal = false"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Batal
                </button>

                <form method="POST" :action="selectedReq ? '{{ url('supervisor/persetujuan/lembur') }}/' + selectedReq.id + '/reject' : '#'">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 rounded-md bg-gray-50 text-gray-700 hover:bg-gray-50 text-sm font-medium transition shadow-sm">
                        Tolak SPL
                    </button>
                </form>

                <form method="POST" :action="selectedReq ? '{{ url('supervisor/persetujuan/lembur') }}/' + selectedReq.id + '/approve' : '#'">
                    @csrf
                    <button type="submit"
                            class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center gap-2 transition">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Setujui SPL Tim
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
