@extends('layouts.supervisor')

@section('title', 'Persetujuan Cuti & Izin Tim')
@section('page-title', 'Persetujuan Cuti & Izin Tim')
@section('page-desc', 'Review pengajuan cuti, sakit, dan izin anggota tim Anda sebelum diteruskan ke HR.')

@php
    $stats = [
        ['label' => 'Pending Review Kamu', 'value' => '3 Pengajuan', 'icon' => 'pending_actions', 'color' => 'text-amber-700'],
        ['label' => 'Anggota Tim Hadir', 'value' => '7 / 9 Pegawai', 'icon' => 'groups', 'color' => 'text-primary'],
        ['label' => 'Sedang Cuti / Izin', 'value' => '2 Orang', 'icon' => 'event_busy', 'color' => 'text-purple-700'],
        ['label' => 'Disetujui Bulan Ini', 'value' => '14 Pengajuan', 'icon' => 'task_alt', 'color' => 'text-primary'],
    ];

    $pending = [
        [
            'nip' => 'EMP-00231', 'name' => 'Budi Santoso', 'avatar' => 22, 'pos' => 'Sales Executive',
            'type' => 'Cuti Tahunan', 'range' => '12 – 13 Agu 2026', 'quota' => '6 hari tersisa',
            'attach' => null, 'reason' => 'Keperluan keluarga di luar kota', 'days' => 2,
        ],
        [
            'nip' => 'EMP-00567', 'name' => 'Dewi Lestari', 'avatar' => 33, 'pos' => 'Staff Administrasi',
            'type' => 'Cuti Sakit', 'range' => '15 – 17 Agu 2026', 'quota' => '10 hari tersisa',
            'attach' => 'surat_dokter_dewi.pdf', 'reason' => 'Sakit demam & istirahat dokter', 'days' => 3,
        ],
    ];

    $history = [
        ['nip' => 'EMP-01044', 'name' => 'Siti Aminah', 'avatar' => 44, 'type' => 'Sakit', 'range' => '5 Agu', 'status' => 'Pending HR', 'decided' => 'Disetujui Anda, 4 Agu'],
        ['nip' => 'EMP-00812', 'name' => 'Eko Prasetyo', 'avatar' => 19, 'type' => 'Izin Pribadi', 'range' => '7 Agu', 'status' => 'Approved', 'decided' => 'Disetujui Anda, 3 Agu'],
    ];

    $badge = [
        'Pending HR' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'Approved' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
        'Rejected' => 'bg-rose-50 text-rose-800 border border-rose-200',
    ];
@endphp

@section('content')
<div x-data="{
    showReviewModal: false,
    selectedReq: null,
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },
    openReview(req) {
        this.selectedReq = req;
        this.showReviewModal = true;
    }
}">

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- PENDING APPROVAL TABLE --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Menunggu Persetujuan Anda (Supervisor)</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Setelah disetujui, pengajuan akan otomatis diteruskan ke HR Operations untuk verifikasi kuota.</p>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">{{ count($pending) }} Menunggu Review</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5 min-w-[200px]">Anggota Tim</th>
                        <th class="px-4 py-3.5 min-w-[140px]">Jenis Pengajuan</th>
                        <th class="px-4 py-3.5 min-w-[160px]">Rentang Tanggal</th>
                        <th class="px-4 py-3.5 min-w-[160px]">Sisa Kuota / Berkas</th>
                        <th class="px-6 py-3.5 text-center min-w-[170px]">Aksi Supervisor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($pending as $r)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/36?img={{ $r['avatar'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $r['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $r['name'] }}</p>
                                        <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">{{ $r['nip'] }} · {{ $r['pos'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ $r['type'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs text-on-surface font-semibold">{{ $r['range'] }}</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface-variant/70">
                                <p class="font-medium text-on-surface">{{ $r['quota'] }}</p>
                                @if ($r['attach'])
                                    <span class="text-primary font-bold inline-flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[14px]">attach_file</span> Lampiran Ada
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" @click="openReview({{ json_encode($r) }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Review Pengajuan
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIWAYAT KEPUTUSAN --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface">Riwayat Persetujuan Tim Kamu</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Audit trail persetujuan internal tim</p>
            </div>
            <span class="text-[10px] font-mono text-on-surface-variant/60 uppercase">Audit Trail</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Tipe</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">Keputusan Anda</th>
                        <th class="px-4 py-3.5">Status di HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($history as $r)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img={{ $r['avatar'] }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-black/10" alt="">
                                    <span class="font-bold text-on-surface text-xs">{{ $r['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-on-surface">{{ $r['type'] }}</td>
                            <td class="px-4 py-3.5 font-mono text-xs text-on-surface-variant/70">{{ $r['range'] }}</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface-variant/80 font-medium">{{ $r['decided'] }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $badge[$r['status']] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $r['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL REVIEW SPV --}}
    <div x-show="showReviewModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showReviewModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150" x-show="selectedReq">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-3">
                    <img :src="'https://i.pravatar.cc/40?img=' + (selectedReq ? selectedReq.avatar : 1)" class="w-10 h-10 rounded-full object-cover border border-black/10" alt="">
                    <div>
                        <h3 class="text-base font-bold text-on-surface" x-text="selectedReq ? selectedReq.name : ''"></h3>
                        <p class="text-xs text-on-surface-variant/60" x-text="selectedReq ? selectedReq.pos : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showReviewModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 space-y-2 font-mono">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Jenis Pengajuan:</span>
                        <span class="font-bold text-on-surface" x-text="selectedReq ? selectedReq.type : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Rentang Tanggal:</span>
                        <span class="font-bold text-primary" x-text="selectedReq ? selectedReq.range : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Sisa Kuota:</span>
                        <span class="font-bold text-on-surface" x-text="selectedReq ? selectedReq.quota : ''"></span>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Catatan Pekerjaan / Alasan</label>
                    <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface leading-relaxed" x-text="selectedReq ? selectedReq.reason : ''"></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showReviewModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showReviewModal = false; triggerToast('Pengajuan cuti ditolak', 'error')"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition">
                    Tolak
                </button>
                <button type="button" @click="showReviewModal = false; triggerToast('Pengajuan cuti berhasil disetujui & diteruskan ke HR!')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui &amp; Teruskan ke HR
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION (THEME-MATCHED DEEP EMERALD) -->
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-white font-medium text-xs border border-emerald-500/30 backdrop-blur-md"
         :class="{
             'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
             'bg-rose-950 border-rose-500/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-xs font-semibold"></span>
    </div>

</div>
@endsection