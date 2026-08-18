@extends('layouts.supervisor')

@section('title', 'Persetujuan Cuti & Izin Tim')
@section('page-title', 'Persetujuan Cuti & Izin Tim')
@section('page-desc', 'Review pengajuan cuti, sakit, dan izin anggota tim Anda sebelum diteruskan ke HR.')

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
    },
    submitDecision(action) {
        if (!this.selectedReq || !this.selectedReq.id) {
            this.triggerToast('ID pengajuan tidak ditemukan.', 'error');
            return;
        }
        const form = document.getElementById('form-' + action);
        form.action = '/supervisor/persetujuan/cuti/' + this.selectedReq.id + '/' + action;
        form.submit();
    }
}">


    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-gray-50 text-emerald-800 border border-gray-200 rounded-md text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach ($stats as $s)
            <div class="bg-white border border-gray-100 rounded-md p-6 shadow-sm relative overflow-hidden group hover:border-[#0B3D2E]/30 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</span>
                </div>
                <p class="text-3xl font-semibold  text-gray-800 leading-none mb-2">{{ $s['value'] }}</p>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
            </div>
        @endforeach
    </div>


    <div class="bg-white rounded-md shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Menunggu Persetujuan Anda</h2>
                <p class="text-xs text-gray-500 mt-1">Setelah disetujui, pengajuan akan otomatis diteruskan ke HR Operations.</p>
            </div>
            <span class="text-[11px] font-medium px-4 py-1.5 rounded-full bg-gray-50 text-gray-700 border border-gray-200">{{ count($pending) }} Menunggu Review</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-wider">
                        <th class="px-8 py-4">Anggota Tim</th>
                        <th class="px-6 py-4">Jenis Pengajuan</th>
                        <th class="px-6 py-4">Rentang Tanggal</th>
                        <th class="px-6 py-4">Sisa Kuota / Berkas</th>
                        <th class="px-8 py-4 text-center">Aksi Supervisor</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($pending as $r)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/36?u={{ $r['avatar'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="{{ $r['name'] }}">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $r['name'] }}</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $r['nip'] }} · {{ $r['pos'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-50"></span>
                                    {{ $r['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4  text-sm font-semibold text-gray-800">{{ $r['range'] }}</td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <p class="font-medium text-gray-800">{{ $r['quota'] }}</p>
                                @if ($r['attach'])
                                    <span class="text-[#0B3D2E] font-medium inline-flex items-center gap-1 mt-1 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">
                                        <span class="material-symbols-outlined text-[14px]">attach_file</span> Lampiran Ada
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-center">
                                <button type="button" @click="openReview({{ json_encode($r) }})"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium transition shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Review Pengajuan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada pengajuan.
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
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Riwayat Persetujuan Tim Kamu</h2>
                <p class="text-xs text-gray-500 mt-1">Audit trail persetujuan internal tim</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[11px] font-medium uppercase tracking-wider">
                        <th class="px-8 py-4">Karyawan</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Keputusan Anda</th>
                        <th class="px-8 py-4">Status di HR</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse ($history as $r)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?u={{ $r['avatar'] }}" class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-gray-50" alt="">
                                    <span class="font-medium text-gray-800 text-sm group-hover:text-[#0B3D2E] transition-colors">{{ $r['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-600">{{ $r['type'] }}</td>
                            <td class="px-6 py-4  text-xs text-gray-600">{{ $r['range'] }}</td>
                            <td class="px-6 py-4 text-xs text-gray-800 font-medium">{{ $r['decided'] }}</td>
                            <td class="px-8 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-medium whitespace-nowrap {{ str_contains($r['status_badge'], 'emerald') ? 'bg-gray-50 text-[#0B3D2E] border border-gray-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $r['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    Belum ada riwayat.
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


    <div x-show="showReviewModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showReviewModal = false">
        <div class="bg-white rounded-md max-w-lg w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in duration-200 border border-gray-100" x-show="selectedReq">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-4">
                    <img :src="'https://i.pravatar.cc/40?u=' + (selectedReq ? selectedReq.avatar : 1)" class="w-12 h-12 rounded-full object-cover shadow-sm ring-2 ring-gray-50" alt="">
                    <div>
                        <h3 class="text-lg font-medium text-gray-800" x-text="selectedReq ? selectedReq.name : ''"></h3>
                        <p class="text-xs text-gray-500" x-text="selectedReq ? selectedReq.pos : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showReviewModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div class="p-5 rounded-md bg-gray-50 border border-gray-200 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium text-xs">Jenis Pengajuan:</span>
                        <span class="font-medium text-gray-800" x-text="selectedReq ? selectedReq.type : ''"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium text-xs">Rentang Tanggal:</span>
                        <span class="font-medium text-[#0B3D2E] " x-text="selectedReq ? selectedReq.range : ''"></span>
                    </div>
                </div>

                <div>
                    <label class="font-medium text-gray-400 uppercase tracking-wide text-[11px] block mb-2">Catatan / Alasan</label>
                    <p class="p-4 rounded-md border border-gray-200 bg-gray-50 text-gray-700 leading-relaxed" x-text="selectedReq ? selectedReq.reason : ''"></p>
                </div>

                <div x-show="selectedReq && selectedReq.attach">
                    <a :href="selectedReq ? selectedReq.attach_url : '#'" target="_blank" class="text-[#0B3D2E] font-medium flex items-center gap-2 hover:underline bg-gray-50 w-fit px-4 py-2 rounded-md border border-gray-200 transition">
                        <span class="material-symbols-outlined text-[18px]">attach_file</span> Lihat Lampiran
                    </a>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="showReviewModal = false"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Batal
                </button>
                <button type="button" @click="submitDecision('reject')"
                        class="px-5 py-2.5 rounded-md bg-gray-50 text-gray-700 hover:bg-gray-50 text-sm font-medium transition shadow-sm">
                    Tolak
                </button>
                <button type="button" @click="submitDecision('approve')"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm flex items-center gap-2 transition">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    Setujui &amp; Teruskan
                </button>
            </div>
        </div>
    </div>


    <form id="form-approve" method="POST" style="display:none">
        @csrf
    </form>
    <form id="form-reject" method="POST" style="display:none">
        @csrf
        <input type="hidden" name="rejection_reason" value="Ditolak oleh Supervisor">
    </form>
</div>

<x-auto-refresh />
@endsection
