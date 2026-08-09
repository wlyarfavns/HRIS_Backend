@extends('layouts.supervisor')

@section('title', 'Persetujuan Lembur (SPL) Tim')
@section('page-title', 'Persetujuan Lembur (SPL) Tim')
@section('page-desc', 'Verifikasi dan setujui Surat Perintah Lembur anggota tim Anda sebelum diteruskan ke HR.')

@php
    $stats = [
        ['label' => 'SPL Pending Review', 'value' => '1 Pengajuan', 'icon' => 'timelapse', 'color' => 'text-amber-700'],
        ['label' => 'Sedang Lembur Hari Ini', 'value' => '3 Pegawai', 'icon' => 'schedule', 'color' => 'text-primary'],
        ['label' => 'Total Jam Lembur Tim', 'value' => '14 Jam', 'icon' => 'query_stats', 'color' => 'text-purple-700'],
        ['label' => 'Status SPL Tim', 'value' => '94% Disetujui', 'icon' => 'verified', 'color' => 'text-primary'],
    ];

    $pending = [
        [
            'nip' => 'EMP-01120', 'name' => 'Kevin Malone', 'avatar' => 55, 'dept' => 'Finance Staff',
            'hours' => 2, 'project' => 'Closing Laporan Bulanan Q3', 'salary' => 5200000,
            'date' => '25 Okt 2026', 'start' => '17:30', 'end' => '19:30',
            'notes' => 'Rekonsiliasi mutasi rekening giro BCA & Mandiri untuk persiapan audit',
        ],
    ];

    $history = [
        ['nip' => 'EMP-00812', 'name' => 'Eko Prasetyo', 'avatar' => 19, 'hours' => 3, 'project' => 'Migrasi Server Cloud', 'status' => 'Approved SPV', 'decided' => 'Disetujui Anda, 24 Agu'],
        ['nip' => 'EMP-00933', 'name' => 'Dwight Schrute', 'avatar' => 51, 'hours' => 4, 'project' => 'Client Emergency Tender', 'status' => 'Locked HR', 'decided' => 'Disetujui Anda, 22 Agu'],
    ];

    $badge = [
        'Approved SPV' => 'bg-sky-50 text-sky-800 border border-sky-200',
        'Locked HR' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
    ];
@endphp

@section('content')
<div x-data="{
    showModal: false,
    selectedReq: null,
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },
    openReview(r) {
        this.selectedReq = r;
        this.showModal = true;
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
                <h2 class="text-base font-bold text-on-surface">Surat Perintah Lembur Menunggu Persetujuan Anda</h2>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Setelah Anda setujui, HR Operations akan mengunci data untuk masuk perhitungan payroll</p>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">Wajib Review SPV</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5 min-w-[200px]">Karyawan</th>
                        <th class="px-4 py-3.5 min-w-[220px]">Keperluan &amp; Proyek</th>
                        <th class="px-4 py-3.5 min-w-[120px]">Durasi Waktu</th>
                        <th class="px-4 py-3.5 text-right min-w-[140px]">Estimasi Upah Lembur</th>
                        <th class="px-6 py-3.5 text-center min-w-[170px]">Aksi Supervisor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @foreach ($pending as $r)
                        @php $upah = round((1/173) * $r['salary'] * $r['hours']); @endphp
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/36?img={{ $r['avatar'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $r['name'] }}">
                                    <div>
                                        <p class="font-bold text-on-surface text-xs leading-tight">{{ $r['name'] }}</p>
                                        <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">{{ $r['nip'] }} · {{ $r['dept'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-xs font-bold text-on-surface">{{ $r['project'] }}</p>
                                <p class="text-[10px] text-on-surface-variant/60 font-mono mt-0.5">{{ $r['date'] }} ({{ $r['start'] }}–{{ $r['end'] }})</p>
                            </td>
                            <td class="px-4 py-3.5 font-mono font-bold text-xs text-on-surface">
                                {{ $r['hours'] }} Jam
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-extrabold text-xs text-primary">
                                Rp{{ number_format($upah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <button type="button" @click="openReview({{ json_encode(array_merge($r, ['upah' => $upah])) }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Review SPL Tim
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIWAYAT SPL TIM --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5">
            <h2 class="text-base font-bold text-on-surface">Riwayat Keputusan SPL Tim</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5">Karyawan</th>
                        <th class="px-4 py-3.5">Durasi</th>
                        <th class="px-4 py-3.5">Keperluan Proyek</th>
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
                            <td class="px-4 py-3.5 font-mono text-xs text-on-surface-variant/80 font-bold">{{ $r['hours'] }} Jam</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface font-medium">{{ $r['project'] }}</td>
                            <td class="px-4 py-3.5 text-xs font-medium text-on-surface-variant/80">{{ $r['decided'] }}</td>
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
    <div x-show="showModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150" x-show="selectedReq">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">timelapse</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Persetujuan SPL Tim</h3>
                        <p class="text-xs text-on-surface-variant/60" x-text="selectedReq ? selectedReq.name : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 space-y-2 font-mono">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Karyawan:</span>
                        <span class="font-bold text-on-surface" x-text="selectedReq ? selectedReq.name : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Keperluan Proyek:</span>
                        <span class="font-bold text-on-surface" x-text="selectedReq ? selectedReq.project : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Durasi &amp; Jam:</span>
                        <span class="font-bold text-primary" x-text="selectedReq ? selectedReq.hours + ' Jam (' + selectedReq.start + '–' + selectedReq.end + ')' : ''"></span>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Catatan Pekerjaan</label>
                    <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface leading-relaxed" x-text="selectedReq ? selectedReq.notes : ''"></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showModal = false; triggerToast('SPL tim ditolak', 'error')"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition">
                    Tolak SPL
                </button>
                <button type="button" @click="showModal = false; triggerToast('SPL tim berhasil disetujui & diteruskan ke HR Operations!')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui SPL Tim
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