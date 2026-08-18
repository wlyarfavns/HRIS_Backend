@extends('layouts.hr')

@section('title', 'Persetujuan Cuti & Izin')
@section('page-title', 'Persetujuan Cuti & Izin')
@section('page-desc', 'Board persetujuan terpadu untuk pengajuan cuti tahunan, sakit, dan izin karyawan.')

@section('content')
<div x-data="{
        activeTab: 'Semua',
        showDetailModal: false,
        showRejectModal: false,
        selectedRequest: null,
        openReview(req) {
            this.selectedRequest = req;
            this.showDetailModal = true;
        },
        openReject(req) {
            this.selectedRequest = req;
            this.showDetailModal = false;
            this.showRejectModal = true;
        }
    }">


    @foreach (['success' => 'bg-gray-50 border-gray-200 text-emerald-800 check_circle',
               'error'   => 'bg-gray-50 border-gray-200 text-gray-700 error',
               'warning' => 'bg-gray-50 border-gray-200 text-gray-700 warning'] as $type => $cfg)
        @if (session($type))
            @php [$bg, $border, $color, $icon] = explode(' ', $cfg) @endphp
            <div class="rounded-md {{ $bg }} border {{ $border }} {{ $color }} text-sm p-4 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                {{ session($type) }}
            </div>
        @endif
    @endforeach


    <div class="grid grid-cols-4 gap-6 mb-6">
        @foreach ($stats as $s)
            @php

                $colorMap = [
                    'text-gray-700' => 'text-gray-700',
                    'text-orange-600' => 'text-orange-500',
                    'text-green-600' => 'text-emerald-500',
                    'text-gray-700' => 'text-gray-700',
                ];
                $cleanColor = $colorMap[$s['color']] ?? 'text-gray-800';
            @endphp
            <div class="bg-white rounded-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                        <span class="material-symbols-outlined text-[18px]">{{ $s['icon'] }}</span>
                    </div>
                </div>
                <p class="text-3xl font-semibold  {{ $cleanColor }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md overflow-hidden border border-gray-200">

        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-medium text-gray-800">Daftar Pengajuan Cuti &amp; Izin</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Total {{ $leaveRequests->count() }} pengajuan ·
                    <span class="text-gray-700 font-semibold">
                        {{ $leaveRequests->where('status', 'pending_hr')->count() }} menunggu persetujuan HR
                    </span>
                </p>
            </div>


            <div class="flex items-center gap-1.5 p-1.5 rounded-md bg-gray-50 border border-gray-200 text-xs font-medium flex-wrap">
                <button type="button" @click="activeTab = 'Semua'"
                    class="px-4 py-2 rounded-lg transition cursor-pointer"
                    :class="activeTab === 'Semua' ? 'bg-white text-[#0B3D2E] shadow-sm font-medium' : 'text-gray-500 hover:text-gray-800'">
                    Semua
                </button>
                @foreach ($leaveTypes as $lt)
                    <button type="button" @click="activeTab = '{{ addslashes($lt->name) }}'"
                        class="px-4 py-2 rounded-lg transition cursor-pointer"
                        :class="activeTab === '{{ addslashes($lt->name) }}' ? 'bg-white text-[#0B3D2E] shadow-sm font-medium' : 'text-gray-500 hover:text-gray-800'">
                        {{ $lt->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Detail</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($leaveRequests as $item)
                        @php
                            $typeName  = $item->leaveType->name ?? 'Izin';


                            $statusMap = [
                                'pending_spv' => ['label' => 'Menunggu SPV', 'class' => 'bg-orange-50 text-orange-600'],
                                'pending_hr'  => ['label' => 'Menunggu HR',  'class' => 'bg-gray-50 text-gray-700'],
                                'approved'    => ['label' => 'Disetujui',    'class' => 'bg-gray-50 text-[#0B3D2E]'],
                                'rejected'    => ['label' => 'Ditolak',      'class' => 'bg-gray-50 text-gray-700'],
                            ];
                            $badge    = $statusMap[$item->status] ?? ['label' => ucfirst($item->status), 'class' => 'bg-gray-100 text-gray-500'];
                            $start    = \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M Y');
                            $end      = \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y');
                            $range    = $start === $end ? $start : "{$start} – {$end}";
                            $initials = strtoupper(substr($item->employee->full_name ?? '?', 0, 1));


                            $canActHR = $item->status === 'pending_hr';

                            $modalData = [
                                'id'          => $item->id,
                                'name'        => $item->employee->full_name ?? '-',
                                'nip'         => $item->employee->employee_id ?? '-',
                                'dept'        => $item->employee->department->name ?? '-',
                                'type'        => $typeName,
                                'detail'      => $item->total_days . ' Hari (' . $range . ')',
                                'status'      => $badge['label'],
                                'reason'      => $item->reason ?? '-',
                                'attach'      => (bool) $item->attachment,
                                'attach_url'  => $item->attachment ? asset('storage/' . $item->attachment) : null,
                                'quota'       => ($item->leaveType && $item->leaveType->is_quota_based)
                                                    ? 'Berbasis kuota — memotong saldo cuti'
                                                    : 'Tidak memotong kuota',
                                'approved_by' => $item->approver->name ?? null,
                                'approved_at' => $item->approved_at
                                                    ? \Carbon\Carbon::parse($item->approved_at)->translatedFormat('d M Y, H:i')
                                                    : null,
                                'is_pending'  => $canActHR,   
                                'can_cancel'  => $item->status !== 'rejected',
                                'initials'    => $initials,
                            ];
                        @endphp

                        <tr class="hover:bg-gray-50 transition"
                            x-show="activeTab === 'Semua' || '{{ addslashes($typeName) }}' === activeTab">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 border border-gray-300
                                                flex items-center justify-center text-xs font-medium shrink-0 uppercase">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm leading-tight">
                                            {{ $item->employee->full_name ?? '-' }}
                                        </p>
                                        <p class="text-[11px]  text-gray-500 mt-0.5">
                                            {{ $item->employee->employee_id ?? '-' }}
                                            @if ($item->employee?->department)
                                                · {{ $item->employee->department->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-800">{{ $typeName }}</span>
                                @if ($item->leaveType?->requires_attachment)
                                    <p class="text-[10px] text-gray-700 font-medium mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        Wajib lampiran
                                    </p>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-800 ">
                                    {{ $item->total_days }} Hari <span class="text-gray-400">({{ $range }})</span>
                                </p>
                                @if ($item->attachment)
                                    <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank"
                                        class="text-[11px] text-[#0B3D2E] font-medium flex items-center gap-1 mt-1 hover:underline w-fit">
                                        <span class="material-symbols-outlined text-[14px]">attach_file</span>
                                        Lihat Lampiran
                                    </a>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-[10px] font-medium px-2.5 py-1.5 rounded-lg uppercase tracking-wider {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                                @if ($item->status === 'pending_spv')
                                    <p class="text-[10px] text-orange-500 mt-1.5 font-medium">Menunggu Supervisor</p>
                                @elseif ($item->approved_at)
                                    <p class="text-[11px] text-gray-400 mt-1.5 ">
                                        {{ \Carbon\Carbon::parse($item->approved_at)->translatedFormat('d M Y') }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if ($canActHR)
                                    <button type="button"
                                        @click="openReview({{ json_encode($modalData) }})"
                                        class="px-4 py-2 rounded-md text-xs font-medium transition cursor-pointer
                                               bg-[#0B3D2E] text-white hover:bg-[#043927] shadow-sm">
                                        Review &amp; Setujui
                                    </button>
                                @else
                                    <button type="button"
                                        @click="openReview({{ json_encode($modalData) }})"
                                        class="px-4 py-2 rounded-md text-xs font-medium transition cursor-pointer
                                               border border-gray-200 hover:bg-gray-100 text-gray-600">
                                        Detail
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                Belum ada pengajuan cuti atau izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $leaveRequests->links() }}
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 text-[11px]
                    text-gray-500 flex items-center justify-between">
            <span class="flex items-center gap-3">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span> Menunggu SPV</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-50 inline-block"></span> Menunggu HR</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-500 inline-block"></span> Disetujui</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-50 inline-block"></span> Ditolak</span>
            </span>
            <span class="font-medium text-[#0B3D2E]">HR hanya bisa aksi saat status "Menunggu HR".</span>
        </div>
    </div>


    <div x-show="showDetailModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
        @click.self="showDetailModal = false">

        <div x-show="showDetailModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-md max-w-lg w-full p-8 shadow-sm space-y-6 border border-gray-100">

            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-600 flex items-center
                                justify-center text-lg font-medium uppercase border border-gray-200"
                         x-text="selectedRequest?.initials ?? '?'"></div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800"
                            x-text="selectedRequest?.name ?? ''"></h3>
                        <p class="text-xs text-gray-500  mt-0.5"
                           x-text="selectedRequest ? selectedRequest.type + ' · ' + selectedRequest.nip : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showDetailModal = false"
                    class="text-gray-400 hover:text-gray-800 cursor-pointer w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div class="p-4 rounded-md bg-gray-50 border border-gray-100 space-y-3 ">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-sans text-xs">Departemen:</span>
                        <span class="font-medium text-gray-800" x-text="selectedRequest?.dept ?? '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-sans text-xs">Detail:</span>
                        <span class="font-medium text-gray-800" x-text="selectedRequest?.detail ?? '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-sans text-xs">Status:</span>
                        <span class="font-medium text-[#0B3D2E]" x-text="selectedRequest?.status ?? '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-sans text-xs">Kuota:</span>
                        <span class="font-medium text-gray-800" x-text="selectedRequest?.quota ?? '-'"></span>
                    </div>
                    <div class="flex justify-between" x-show="selectedRequest?.approved_by">
                        <span class="text-gray-500 font-sans text-xs">Diproses:</span>
                        <span class="font-medium text-gray-800"
                              x-text="(selectedRequest?.approved_by ?? '') + ' · ' + (selectedRequest?.approved_at ?? '')"></span>
                    </div>
                </div>

                <div>
                    <label class="font-medium text-gray-400 uppercase text-[10px] tracking-wide block mb-1.5">
                        Alasan Karyawan
                    </label>
                    <p class="p-4 rounded-md border border-gray-100 bg-gray-50 text-gray-700 leading-relaxed min-h-[80px]"
                        x-text="selectedRequest?.reason ?? '-'"></p>
                </div>

                <div x-show="selectedRequest?.attach">
                    <a :href="selectedRequest?.attach_url ?? '#'" target="_blank"
                        class="flex items-center gap-1.5 text-[#0B3D2E] font-medium text-xs hover:underline cursor-pointer w-fit">
                        <span class="material-symbols-outlined text-[16px]">attach_file</span>
                        Lihat Lampiran / Surat Dokter
                    </a>
                </div>
            </div>


            <div x-show="selectedRequest?.can_cancel || selectedRequest?.is_pending"
                 class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="button"
                    x-show="selectedRequest?.can_cancel"
                    @click="showDetailModal = false; showRejectModal = true"
                    class="flex-1 py-3 rounded-md border border-gray-200 text-sm font-medium
                           text-gray-700 hover:bg-gray-50 transition cursor-pointer
                           flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">block</span>
                    Batalkan / Tolak
                </button>
                <button type="button"
                    x-show="selectedRequest?.is_pending"
                    @click="document.getElementById('approve-id').value = selectedRequest.id;
                            document.getElementById('form-approve').requestSubmit();"
                    class="flex-1 py-3 rounded-md bg-[#0B3D2E] text-white text-sm font-medium
                           hover:bg-[#043927] shadow-sm transition cursor-pointer
                           flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Setujui Cuti
                </button>
            </div>


            <div x-show="!selectedRequest?.can_cancel && !selectedRequest?.is_pending"
                 class="pt-4 border-t border-gray-100">
                <button type="button" @click="showDetailModal = false"
                    class="w-full py-3 rounded-md bg-gray-100 text-sm font-medium
                           text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <form id="form-approve" method="POST" style="display:none">
        @csrf
        <input type="hidden" id="approve-id" name="_id">
    </form>


    <div x-show="showRejectModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
        @click.self="showRejectModal = false">

        <div x-show="showRejectModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-md max-w-md w-full p-8 shadow-sm space-y-6 border border-gray-100">

            <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                <div class="w-12 h-12 rounded-full bg-gray-50 text-gray-700 flex items-center justify-center border border-gray-200">
                    </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-800">Tolak Pengajuan</h3>
                    <p class="text-xs text-gray-500 mt-1"
                       x-text="selectedRequest ? selectedRequest.name + ' — ' + selectedRequest.type : ''"></p>
                </div>
            </div>

            <form id="form-reject" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" id="reject-id" name="_id">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                        Alasan Penolakan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="rejection_reason" rows="3"
                        placeholder="Contoh: Kuota cuti habis / bentrok jadwal tim..."
                        class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm border border-gray-200
                               hover:border-gray-200 focus:border-gray-200 focus:ring-2 focus:ring-red-500/20
                               focus:outline-none transition resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showRejectModal = false"
                        class="flex-1 py-3 rounded-md bg-gray-100 text-sm font-medium
                               text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                        @click="document.getElementById('reject-id').value = selectedRequest.id;
                                document.getElementById('form-reject').requestSubmit();"
                        class="flex-1 py-3 rounded-md bg-gray-50 text-white text-sm font-medium
                               hover:bg-gray-50 transition cursor-pointer shadow-sm
                               flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    document.getElementById('form-approve').addEventListener('submit', function () {
        this.action = '/hr/persetujuan/cuti/' + document.getElementById('approve-id').value + '/approve';
    });
    document.getElementById('form-reject').addEventListener('submit', function () {
        this.action = '/hr/persetujuan/cuti/' + document.getElementById('reject-id').value + '/reject';
    });
</script>

<x-auto-refresh />
@endsection
