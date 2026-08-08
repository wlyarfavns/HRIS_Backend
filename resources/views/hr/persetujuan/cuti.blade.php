@extends('layouts.hr')

@section('title', 'Persetujuan Cuti & Izin')
@section('page-title', 'Persetujuan Cuti & Izin')
@section('page-desc', 'Board persetujuan terpadu untuk pengajuan cuti tahunan, sakit, dan izin karyawan.')

@php
    $stats = [
        ['label' => 'PENDING APPROVAL', 'value' => '8 Pengajuan', 'icon' => 'assignment_late', 'color' => 'text-amber-800'],
        ['label' => 'SEDANG CUTI HARI INI', 'value' => '5 Karyawan', 'icon' => 'event_busy', 'color' => 'text-primary'],
        ['label' => 'CUTI TAHUNAN', 'value' => '12 Pengajuan', 'icon' => 'calendar_month', 'color' => 'text-purple-700'],
        ['label' => 'CUTI SAKIT & IZIN', 'value' => '6 Pengajuan', 'icon' => 'medical_services', 'color' => 'text-primary'],
    ];

    $pendingList = [
        [
            'nip' => 'EMP-00231', 'name' => 'Budi Santoso', 'avatar' => 22,
            'type' => 'Cuti Tahunan', 'detail' => '2 Hari (12–13 Aug 2026)',
            'status' => 'Pending Spv', 'badge' => 'bg-amber-500/15 text-amber-900',
            'attach' => false, 'action' => 'Review Cuti',
            'reason' => 'Keperluan keluarga di luar kota', 'quota' => '6 Hari tersisa',
        ],
        [
            'nip' => 'EMP-00567', 'name' => 'Dewi Lestari', 'avatar' => 33,
            'type' => 'Cuti Sakit', 'detail' => '3 Hari (15–17 Aug 2026)',
            'status' => 'Pending HR', 'badge' => 'bg-purple-500/15 text-purple-900',
            'attach' => true, 'action' => 'Review Cuti',
            'reason' => 'Sakit demam tinggi & istirahat dokter', 'quota' => 'Surat dokter terlampir',
        ],
        [
            'nip' => 'EMP-01044', 'name' => 'Siti Aminah', 'avatar' => 44,
            'type' => 'Izin Pribadi', 'detail' => '1 Hari (20 Aug 2026)',
            'status' => 'Pending HR', 'badge' => 'bg-amber-50 text-amber-800 border border-amber-200',
            'attach' => false, 'action' => 'Review Izin',
            'reason' => 'Urusan administrasi keluarga', 'quota' => 'Potong kuota izin',
        ],
        [
            'nip' => 'EMP-00812', 'name' => 'Eko Prasetyo', 'avatar' => 19,
            'type' => 'Cuti Melahirkan', 'detail' => '90 Hari (01 Sep–30 Nov 2026)',
            'status' => 'Approved Spv', 'badge' => 'bg-sky-50 text-sky-800 border border-sky-200',
            'attach' => true, 'action' => 'Review Cuti',
            'reason' => 'Persalinan & pendampingan keluarga', 'quota' => 'Hak cuti melahirkan 3 bulan',
        ],
    ];
@endphp

@section('content')
<div x-data="{
    activeTab: 'Semua',
    showDetailModal: false,
    selectedRequest: null,
    openReview(req) {
        this.selectedRequest = req;
        this.showDetailModal = true;
    }
}">

    {{-- TOP SUMMARY STATS KHUSUS CUTI & IZIN (SESUAI SS EXACT MATCH USER) --}}
    <div class="grid grid-cols-4 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide">{{ $s['label'] }}</p>
                    <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">{{ $s['icon'] }}</span>
                </div>
                <p class="text-2xl font-extrabold font-mono-data {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- DAFTAR PENGAJUAN PENDING (FULL-WIDTH) --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6 flex flex-col justify-between">
        <div>
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Daftar Pengajuan Cuti &amp; Izin Pending</h2>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Board verifikasi pengajuan cuti tahunan, sakit, dan izin karyawan</p>
                </div>

                {{-- FILTER TABS --}}
                <div class="flex items-center gap-1.5 p-1 rounded-xl bg-surface-container border border-black/5 text-xs font-bold">
                    <button type="button" @click="activeTab = 'Semua'"
                            class="px-3.5 py-1.5 rounded-lg transition"
                            :class="activeTab === 'Semua' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant/60 hover:text-on-surface'">
                        Semua
                    </button>
                    <button type="button" @click="activeTab = 'Cuti Tahunan'"
                            class="px-3.5 py-1.5 rounded-lg transition"
                            :class="activeTab === 'Cuti Tahunan' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant/60 hover:text-on-surface'">
                        Cuti Tahunan
                    </button>
                    <button type="button" @click="activeTab = 'Sakit'"
                            class="px-3.5 py-1.5 rounded-lg transition"
                            :class="activeTab === 'Sakit' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant/60 hover:text-on-surface'">
                        Cuti Sakit
                    </button>
                    <button type="button" @click="activeTab = 'Izin'"
                            class="px-3.5 py-1.5 rounded-lg transition"
                            :class="activeTab === 'Izin' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant/60 hover:text-on-surface'">
                        Izin Pribadi
                    </button>
                </div>
            </div>

            {{-- TABLE PENGAJUAN PENDING --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                            <th class="px-6 py-3.5">Karyawan</th>
                            <th class="px-4 py-3.5">Tipe Pengajuan</th>
                            <th class="px-4 py-3.5">Detail &amp; Berkas</th>
                            <th class="px-4 py-3.5">Status Approver</th>
                            <th class="px-6 py-3.5 text-center">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @foreach ($pendingList as $item)
                            <tr class="hover:bg-primary/5 transition"
                                x-show="activeTab === 'Semua' || '{{ $item['type'] }}'.includes(activeTab)">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <img src="https://i.pravatar.cc/32?img={{ $item['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="{{ $item['name'] }}">
                                        <div>
                                            <p class="font-bold text-on-surface text-xs leading-tight">{{ $item['name'] }}</p>
                                            <p class="text-[10px] font-mono-data text-on-surface-variant/40">{{ $item['nip'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-xs font-bold text-on-surface">{{ $item['type'] }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-xs text-on-surface font-mono-data">{{ $item['detail'] }}</p>
                                    @if ($item['attach'])
                                        <span class="text-[10px] text-primary font-bold flex items-center gap-0.5 mt-0.5">
                                            <span class="material-symbols-outlined text-[13px]">attach_file</span> Surat Dokter / Lampiran
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $item['badge'] }}">{{ $item['status'] }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <button type="button" @click="openReview({{ json_encode($item) }})"
                                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition border border-black/10 hover:bg-primary hover:text-white hover:border-primary shadow-xs">
                                        {{ $item['action'] }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ATURAN CUTI & IZIN NOTE --}}
        <div class="px-6 py-3.5 bg-surface-container border-t border-black/5 text-[11px] text-on-surface-variant/60 flex items-center justify-between">
            <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px] text-primary">rule</span> Cuti sakit &gt; 1 hari wajib melampirkan surat dokter.</span>
            <span class="font-semibold text-primary">Sistem menolak otomatis jika sisa kuota cuti habis.</span>
        </div>
    </div>

    {{-- MODAL DETAIL / REVIEW PENGAJUAN --}}
    <div x-show="showDetailModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showDetailModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150" x-show="selectedRequest">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2.5">
                    <img :src="'https://i.pravatar.cc/36?img=' + (selectedRequest ? selectedRequest.avatar : 1)" class="w-9 h-9 rounded-full" alt="">
                    <div>
                        <h3 class="text-base font-bold text-on-surface" x-text="selectedRequest ? selectedRequest.name : ''"></h3>
                        <p class="text-xs text-on-surface-variant/50" x-text="selectedRequest ? selectedRequest.type + ' · ' + selectedRequest.nip : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showDetailModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="p-3.5 rounded-xl bg-surface-container border border-black/5 space-y-1.5 font-mono-data">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Detail Rincian:</span>
                        <span class="font-bold text-on-surface" x-text="selectedRequest ? selectedRequest.detail : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Status Approver:</span>
                        <span class="font-bold text-primary" x-text="selectedRequest ? selectedRequest.status : ''"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant/60 font-sans">Informasi Tambahan:</span>
                        <span class="font-bold text-on-surface" x-text="selectedRequest ? selectedRequest.quota : ''"></span>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Alasan / Catatan Karyawan</label>
                    <p class="p-3 rounded-lg border border-black/5 bg-white text-on-surface leading-relaxed" x-text="selectedRequest ? selectedRequest.reason : ''"></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                <button type="button" @click="showDetailModal = false"
                        class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-error hover:bg-error/5 transition">
                    Tolak Pengajuan
                </button>
                <button type="button" @click="showDetailModal = false"
                        class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui Cuti &amp; Izin
                </button>
            </div>
        </div>
    </div>

</div>
@endsection