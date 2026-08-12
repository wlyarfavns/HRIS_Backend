@extends('layouts.supervisor')

@section('title', 'Persetujuan Cuti & Izin Tim')
@section('page-title', 'Persetujuan Cuti & Izin Tim')
@section('page-desc', 'Review pengajuan cuti, sakit, dan izin anggota tim Anda sebelum diteruskan ke HR.')

{{-- DATA DUMMY DIHAPUS, DIGANTI DARI CONTROLLER --}}

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

    {{-- ALERT SUCCESS DARI BACKEND --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

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
                    @forelse ($pending as $r)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/36?u={{ $r['avatar'] }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10" alt="{{ $r['name'] }}">
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
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-on-surface-variant/50">Belum ada pengajuan.</td></tr>
                    @endforelse
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
                    @forelse ($history as $r)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?u={{ $r['avatar'] }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-black/10" alt="">
                                    <span class="font-bold text-on-surface text-xs">{{ $r['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-on-surface">{{ $r['type'] }}</td>
                            <td class="px-4 py-3.5 font-mono text-xs text-on-surface-variant/70">{{ $r['range'] }}</td>
                            <td class="px-4 py-3.5 text-xs text-on-surface-variant/80 font-medium">{{ $r['decided'] }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $r['status_badge'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $r['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-on-surface-variant/50">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL REVIEW SPV --}}
    <div x-show="showReviewModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showReviewModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5" x-show="selectedReq">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-3">
                    <img :src="'https://i.pravatar.cc/40?u=' + (selectedReq ? selectedReq.avatar : 1)" class="w-10 h-10 rounded-full object-cover" alt="">
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
                </div>

                <div>
                    <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Catatan / Alasan</label>
                    <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface" x-text="selectedReq ? selectedReq.reason : ''"></p>
                </div>

                <div x-show="selectedReq && selectedReq.attach">
                    <a :href="selectedReq ? selectedReq.attach_url : '#'" target="_blank" class="text-primary font-bold flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-[16px]">attach_file</span> Lihat Lampiran
                    </a>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                <button type="button" @click="showReviewModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="submitDecision('reject')"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition">
                    Tolak
                </button>
                <button type="button" @click="submitDecision('approve')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    Setujui &amp; Teruskan ke HR
                </button>
            </div>
        </div>
    </div>

    {{-- HIDDEN FORMS UNTUK SUBMIT ACTION --}}
    <form id="form-approve" method="POST" style="display:none">
        @csrf
    </form>
    <form id="form-reject" method="POST" style="display:none">
        @csrf
        <input type="hidden" name="rejection_reason" value="Ditolak oleh Supervisor">
    </form>
</div>
@endsection