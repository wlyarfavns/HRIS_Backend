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

    {{-- FLASH MESSAGES --}}
    @foreach (['success' => 'bg-green-50 border-green-200 text-green-800 check_circle',
               'error'   => 'bg-red-50 border-red-200 text-red-800 error',
               'warning' => 'bg-amber-50 border-amber-200 text-amber-800 warning'] as $type => $cfg)
        @if (session($type))
            @php [$bg, $border, $color, $icon] = explode(' ', $cfg) @endphp
            <div class="rounded-xl {{ $bg }} border {{ $border }} {{ $color }} text-sm p-3.5 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    {{-- STATS --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- DAFTAR PENGAJUAN --}}
    <div class="card-flat rounded-2xl overflow-hidden">

        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Daftar Pengajuan Cuti &amp; Izin</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">
                    Total {{ $leaveRequests->count() }} pengajuan ·
                    <span class="text-amber-700 font-semibold">
                        {{ $leaveRequests->where('status', 'pending_hr')->count() }} menunggu persetujuan HR
                    </span>
                </p>
            </div>

            {{-- FILTER TABS --}}
            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-surface-container border border-black/5 text-xs font-bold flex-wrap">
                <button type="button" @click="activeTab = 'Semua'"
                    class="px-3.5 py-1.5 rounded-lg transition cursor-pointer"
                    :class="activeTab === 'Semua' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant/60 hover:text-on-surface'">
                    Semua
                </button>
                @foreach ($leaveTypes as $lt)
                    <button type="button" @click="activeTab = '{{ addslashes($lt->name) }}'"
                        class="px-3.5 py-1.5 rounded-lg transition cursor-pointer"
                        :class="activeTab === '{{ addslashes($lt->name) }}' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant/60 hover:text-on-surface'">
                        {{ $lt->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold
                               text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Tipe</th>
                        <th class="px-4 py-3.5">Detail</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse ($leaveRequests as $item)
                        @php
                            $typeName  = $item->leaveType->name ?? 'Izin';

                            // ← Status map lengkap termasuk semua tahap workflow
                            $statusMap = [
                                'pending_spv' => ['label' => 'Menunggu SPV', 'class' => 'bg-orange-500/10 text-orange-800'],
                                'pending_hr'  => ['label' => 'Menunggu HR',  'class' => 'bg-amber-500/15 text-amber-900'],
                                'approved'    => ['label' => 'Disetujui',    'class' => 'bg-green-500/10 text-green-800'],
                                'rejected'    => ['label' => 'Ditolak',      'class' => 'bg-red-500/10 text-red-800'],
                            ];
                            $badge    = $statusMap[$item->status] ?? ['label' => ucfirst($item->status), 'class' => 'bg-surface-container text-on-surface-variant'];
                            $start    = \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M Y');
                            $end      = \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y');
                            $range    = $start === $end ? $start : "{$start} – {$end}";
                            $initials = strtoupper(substr($item->employee->full_name ?? '?', 0, 1));

                            // HR hanya bisa aksi jika status pending_hr
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
                                'is_pending'  => $canActHR,   // ← tombol approve/reject hanya muncul jika pending_hr
                                'initials'    => $initials,
                            ];
                        @endphp

                        <tr class="hover:bg-primary/5 transition"
                            x-show="activeTab === 'Semua' || '{{ addslashes($typeName) }}' === activeTab">

                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary
                                                flex items-center justify-center text-xs font-extrabold shrink-0 uppercase">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">
                                            {{ $item->employee->full_name ?? '-' }}
                                        </p>
                                        <p class="text-[10px] font-mono-data text-on-surface-variant/40">
                                            {{ $item->employee->employee_id ?? '-' }}
                                            @if ($item->employee?->department)
                                                · {{ $item->employee->department->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="text-xs font-bold text-on-surface">{{ $typeName }}</span>
                                @if ($item->leaveType?->requires_attachment)
                                    <p class="text-[10px] text-amber-700 font-bold mt-0.5 flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        Wajib lampiran
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-3.5">
                                <p class="text-xs text-on-surface font-mono-data">
                                    {{ $item->total_days }} Hari ({{ $range }})
                                </p>
                                @if ($item->attachment)
                                    <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank"
                                        class="text-[10px] text-primary font-bold flex items-center gap-0.5 mt-0.5 hover:underline">
                                        <span class="material-symbols-outlined text-[13px]">attach_file</span>
                                        Lihat Lampiran
                                    </a>
                                @endif
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                                @if ($item->status === 'pending_spv')
                                    <p class="text-[10px] text-orange-600 mt-0.5">Menunggu Supervisor</p>
                                @elseif ($item->approved_at)
                                    <p class="text-[10px] text-on-surface-variant/40 mt-0.5 font-mono-data">
                                        {{ \Carbon\Carbon::parse($item->approved_at)->translatedFormat('d M Y') }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-6 py-3.5 text-center">
                                @if ($canActHR)
                                    <button type="button"
                                        @click="openReview({{ json_encode($modalData) }})"
                                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer
                                               bg-primary text-white hover:brightness-110 shadow-sm">
                                        Review &amp; Setujui
                                    </button>
                                @else
                                    <button type="button"
                                        @click="openReview({{ json_encode($modalData) }})"
                                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer
                                               border border-black/10 hover:bg-surface-container text-on-surface-variant/60">
                                        Detail
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-on-surface-variant/50">
                                <span class="material-symbols-outlined text-[40px] block mb-2 text-on-surface-variant/20">
                                    event_available
                                </span>
                                Belum ada pengajuan cuti atau izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-3.5 bg-surface-container border-t border-black/5 text-[11px]
                    text-on-surface-variant/60 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span> Menunggu SPV
                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block ml-2"></span> Menunggu HR
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block ml-2"></span> Disetujui
                <span class="w-2 h-2 rounded-full bg-red-400 inline-block ml-2"></span> Ditolak
            </span>
            <span class="font-semibold text-primary">HR hanya bisa aksi saat status "Menunggu HR".</span>
        </div>
    </div>


    {{-- ── MODAL DETAIL / REVIEW ─────────────────────────────────────────── --}}
    <div x-show="showDetailModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        @click.self="showDetailModal = false">

        <div x-show="showDetailModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5">

            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center
                                justify-center text-sm font-extrabold uppercase"
                         x-text="selectedRequest?.initials ?? '?'"></div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface"
                            x-text="selectedRequest?.name ?? ''"></h3>
                        <p class="text-xs text-on-surface-variant/50"
                           x-text="selectedRequest ? selectedRequest.type + ' · ' + selectedRequest.nip : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showDetailModal = false"
                    class="text-on-surface-variant/40 hover:text-on-surface cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="p-3.5 rounded-xl bg-surface-container border border-black/5 space-y-2 font-mono-data">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Departemen:</span>
                        <span class="font-bold text-on-surface" x-text="selectedRequest?.dept ?? '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Detail:</span>
                        <span class="font-bold text-on-surface" x-text="selectedRequest?.detail ?? '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Status:</span>
                        <span class="font-bold text-primary" x-text="selectedRequest?.status ?? '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Kuota:</span>
                        <span class="font-bold text-on-surface" x-text="selectedRequest?.quota ?? '-'"></span>
                    </div>
                    <div class="flex justify-between" x-show="selectedRequest?.approved_by">
                        <span class="text-on-surface-variant/60 font-sans">Diproses:</span>
                        <span class="font-bold text-on-surface"
                              x-text="(selectedRequest?.approved_by ?? '') + ' · ' + (selectedRequest?.approved_at ?? '')"></span>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">
                        Alasan Karyawan
                    </label>
                    <p class="p-3 rounded-lg border border-black/5 bg-surface-container text-on-surface leading-relaxed"
                        x-text="selectedRequest?.reason ?? '-'"></p>
                </div>

                <div x-show="selectedRequest?.attach">
                    <a :href="selectedRequest?.attach_url ?? '#'" target="_blank"
                        class="flex items-center gap-2 text-primary font-bold text-xs hover:underline cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">attach_file</span>
                        Lihat Lampiran / Surat Dokter
                    </a>
                </div>
            </div>

            {{-- Tombol: bisa aksi (pending_hr) --}}
            <div x-show="selectedRequest?.is_pending"
                 class="flex items-center gap-3 pt-2 border-t border-black/5">
                <button type="button"
                    @click="showDetailModal = false; showRejectModal = true"
                    class="flex-1 py-3 rounded-xl border border-red-200 text-sm font-bold
                           text-red-700 hover:bg-red-50 transition cursor-pointer
                           flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">block</span>
                    Tolak
                </button>
                <button type="button"
                    @click="document.getElementById('approve-id').value = selectedRequest.id;
                            document.getElementById('form-approve').requestSubmit();"
                    class="flex-1 py-3 rounded-xl bg-primary text-white text-sm font-bold
                           hover:brightness-110 shadow-sm transition cursor-pointer
                           flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    Setujui Cuti
                </button>
            </div>

            {{-- Tombol: sudah diproses --}}
            <div x-show="!selectedRequest?.is_pending"
                 class="pt-2 border-t border-black/5">
                <button type="button" @click="showDetailModal = false"
                    class="w-full py-3 rounded-xl border border-black/10 text-sm font-bold
                           text-on-surface-variant/70 hover:bg-surface-container transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Form approve hidden --}}
    <form id="form-approve" method="POST" style="display:none">
        @csrf
        <input type="hidden" id="approve-id" name="_id">
    </form>

    {{-- ── MODAL TOLAK ──────────────────────────────────────────────────────── --}}
    <div x-show="showRejectModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        @click.self="showRejectModal = false">

        <div x-show="showRejectModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5">

            <div class="flex items-center gap-3 pb-3 border-b border-black/5">
                <div class="w-10 h-10 rounded-full bg-red-500/10 text-red-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">cancel</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-on-surface">Tolak Pengajuan</h3>
                    <p class="text-xs text-on-surface-variant/50"
                       x-text="selectedRequest ? selectedRequest.name + ' — ' + selectedRequest.type : ''"></p>
                </div>
            </div>

            <form id="form-reject" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" id="reject-id" name="_id">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide block mb-1.5">
                        Alasan Penolakan <span class="text-on-surface-variant/30">(opsional)</span>
                    </label>
                    <textarea name="rejection_reason" rows="3"
                        placeholder="Contoh: Kuota cuti habis / bentrok jadwal tim..."
                        class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                               hover:border-red-200 focus:border-red-300 focus:ring-2 focus:ring-red-100
                               focus:outline-none transition resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showRejectModal = false"
                        class="flex-1 py-2.5 rounded-lg border border-black/10 text-xs font-bold
                               text-on-surface-variant/70 hover:bg-surface-container transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                        @click="document.getElementById('reject-id').value = selectedRequest.id;
                                document.getElementById('form-reject').requestSubmit();"
                        class="flex-1 py-2.5 rounded-lg bg-red-600 text-white text-xs font-bold
                               hover:brightness-110 transition cursor-pointer
                               flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">block</span>
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
@endsection