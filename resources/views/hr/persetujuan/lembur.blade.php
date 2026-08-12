@extends('layouts.hr')

@section('title', 'Persetujuan Lembur (SPL)')
@section('page-title', 'Persetujuan Lembur (SPL)')
@section('page-desc', 'Surat Perintah Lembur (SPL), kalkulasi rumus Depnaker, dan penguncian data lembur.')

@php
    $statsView = [
        ['label' => 'Sedang Lembur Hari Ini', 'value' => $stats['today_overtime'] . ' Pegawai', 'icon' => 'timelapse', 'color' => 'text-purple-700'],
        ['label' => 'SPL Pending Approval', 'value' => $stats['pending'] . ' Pengajuan', 'icon' => 'pending_actions', 'color' => 'text-amber-700'],
        ['label' => 'Total Jam Lembur Disetujui', 'value' => number_format($stats['approved_hours'], 1, ',', '.') . ' Jam', 'icon' => 'schedule', 'color' => 'text-primary'],
        ['label' => 'Estimasi Biaya Lembur', 'value' => 'Rp' . number_format($stats['estimated_cost'], 0, ',', '.'), 'icon' => 'payments', 'color' => 'text-primary'],
    ];

    $badge = [
        'pending_spv'  => 'bg-amber-50 text-amber-800 border border-amber-200',
        'approved_spv' => 'bg-sky-50 text-sky-800 border border-sky-200',
        'locked'       => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
        'rejected'     => 'bg-rose-50 text-rose-800 border border-rose-200',
    ];
@endphp

@section('content')
    <div x-data="overtimeApprovalPage()">

        {{-- METRIC CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($statsView as $s)
                <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">{{ $s['label'] }}</p>
                        <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">{{ $s['icon'] }}</span>
                    </div>
                    <p class="text-2xl font-extrabold font-mono {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- DAFTAR SPL TABLE CARD --}}
        <div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Daftar Surat Perintah Lembur (SPL)</h2>
                    <p class="text-xs text-on-surface-variant/60 mt-0.5">Kunci SPL yang sudah disetujui Supervisor sebelum
                        dimasukkan ke rekap Payroll</p>
                </div>

                <div class="flex items-center gap-2.5">
                    <form method="GET" action="{{ route('hr.approvals.overtime') }}" class="relative">
                        <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-[18px]">search</span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau proyek..."
                            class="w-56 pl-9 pr-3 py-2 bg-surface-variant/10 rounded-xl text-xs border border-black/10
                                      focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-variant/20 border-b border-black/5 text-on-surface-variant/70 text-[11px] font-bold uppercase tracking-wider">
                            <th class="px-6 py-3.5 min-w-[200px]">Karyawan</th>
                            <th class="px-4 py-3.5 min-w-[220px]">Keperluan &amp; Proyek</th>
                            <th class="px-4 py-3.5 min-w-[120px]">Durasi Jam</th>
                            <th class="px-4 py-3.5 text-right min-w-[140px]">Estimasi Upah Lembur</th>
                            <th class="px-4 py-3.5 min-w-[140px]">Status</th>
                            <th class="px-6 py-3.5 text-center min-w-[170px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 text-xs">
                        @forelse ($requests as $r)
                            @php
                                // FIX: sebelumnya pakai $r->overtime_pay ?? 0 — kolom ini
                                // baru terisi setelah Supervisor approve, jadi SPL yang masih
                                // pending_spv selalu tampil Rp0. Sekarang dihitung on-the-fly
                                // (sama seperti di halaman Supervisor) supaya HR bisa lihat
                                // estimasi sejak awal, dan tetap akurat setelah dikunci.
                                $upah = $r->overtime_pay
                                    ?? \App\Models\OvertimeRequest::calculateOvertimePay($r->salary_snapshot, $r->hours);
                            @endphp
                            <tr class="hover:bg-primary/5 transition" id="spl-row-{{ $r->id }}">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($r->employee->full_name) }}&background=random"
                                            class="w-9 h-9 rounded-full object-cover shrink-0 border border-black/10"
                                            alt="{{ $r->employee->full_name }}">
                                        <div>
                                            <p class="font-bold text-on-surface text-xs leading-tight">{{ $r->employee->full_name }}</p>
                                            <p class="text-[10px] font-mono text-on-surface-variant/50 mt-0.5">
                                                {{ $r->employee->employee_id }} · {{ $r->employee->department->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-xs font-bold text-on-surface">{{ $r->project ?? '-' }}</p>
                                    <p class="text-[10px] text-on-surface-variant/60 font-mono mt-0.5">
                                        {{ $r->date->translatedFormat('d M Y') }}
                                        ({{ \Carbon\Carbon::parse($r->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($r->end_time)->format('H:i') }})
                                    </p>
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-xs text-on-surface">
                                    {{ rtrim(rtrim(number_format($r->hours, 1, ',', '.'), '0'), ',') }} Jam
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-extrabold text-xs text-primary">
                                    Rp{{ number_format($upah, 0, ',', '.') }}
                                    @if (is_null($r->overtime_pay))
                                        <span class="block text-[9px] font-sans font-normal normal-case text-on-surface-variant/50">estimasi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $badge[$r->status] ?? '' }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @if ($r->status === 'approved_spv')
                                        {{-- FIX: pakai @js() bukan {{ json_encode(...) }} —
                                             json_encode menghasilkan tanda kutip ganda yang
                                             bentrok dengan atribut @click yang juga dibungkus
                                             kutip ganda, sehingga HTML tombol rusak/hilang. --}}
                                        <button type="button"
                                            @click="openModal({{ $r->id }}, @js([
                                                'name'    => $r->employee->full_name,
                                                'project' => $r->project,
                                                'hours'   => $r->hours,
                                                'start'   => \Carbon\Carbon::parse($r->start_time)->format('H:i'),
                                                'end'     => \Carbon\Carbon::parse($r->end_time)->format('H:i'),
                                                'upah'    => $upah,
                                                'notes'   => $r->notes,
                                            ]))"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
                                            Kunci SPL
                                        </button>
                                    @elseif ($r->status === 'locked')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[16px] text-emerald-600">lock</span>
                                            Terkunci
                                        </span>
                                    @elseif ($r->status === 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 text-xs font-semibold whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[16px] text-rose-600">block</span>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold whitespace-nowrap">
                                            <span class="material-symbols-outlined text-[16px] text-amber-600">hourglass_top</span>
                                            Menunggu SPV
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-on-surface-variant/50 text-xs">
                                    Belum ada pengajuan lembur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="px-6 py-4 border-t border-black/5">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>

        {{-- MODAL REVIEW & LOCK SPL --}}
        <div x-show="showSplModal" x-cloak class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
            @click.self="closeModal()">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-150"
                x-show="selectedSpl">
                <div class="flex items-center justify-between border-b border-black/5 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-on-surface">Konfirmasi &amp; Kunci SPL</h3>
                            <p class="text-xs text-on-surface-variant/60">Kunci data lembur agar masuk ke perhitungan
                                Payroll berjalan</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-on-surface-variant/40 hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant/60">Nama Karyawan:</span>
                            <span class="font-bold text-on-surface" x-text="selectedSpl ? selectedSpl.name : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant/60">Proyek/Pekerjaan:</span>
                            <span class="font-bold text-on-surface" x-text="selectedSpl ? selectedSpl.project : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant/60">Durasi Lembur:</span>
                            <span class="font-bold font-mono text-primary"
                                x-text="selectedSpl ? selectedSpl.hours + ' Jam (' + selectedSpl.start + '–' + selectedSpl.end + ')' : ''"></span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-black/5">
                            <span class="text-on-surface-variant/70 font-bold">Kalkulasi Upah Lembur (Depnaker):</span>
                            <span class="font-extrabold font-mono text-sm text-primary"
                                x-text="selectedSpl ? 'Rp' + Number(selectedSpl.upah).toLocaleString('id-ID') : ''"></span>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-on-surface-variant/60 uppercase text-[10px] block mb-1">Catatan Pekerjaan</label>
                        <p class="p-3 rounded-xl border border-black/5 bg-surface-variant/10 text-on-surface"
                            x-text="selectedSpl ? selectedSpl.notes : ''"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                    <button type="button" @click="closeModal()" :disabled="loading"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition disabled:opacity-50">
                        Batal
                    </button>
                    <button type="button" @click="rejectSpl()" :disabled="loading"
                        class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-semibold transition disabled:opacity-50">
                        <span x-show="!loading">Tolak SPL</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button" @click="lockSpl()" :disabled="loading"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm flex items-center gap-1.5 transition disabled:opacity-50">
                        <span class="material-symbols-outlined text-[16px]" x-show="!loading">lock</span>
                        <span x-show="!loading">Kunci SPL &amp; Teruskan ke Payroll</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TOAST NOTIFICATION -->
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
             }" style="display: none;">
            <span class="material-symbols-outlined text-[20px]"
                :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'"
                x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
            <span x-text="toast.message" class="text-xs font-semibold"></span>
        </div>

    </div>
@endsection

@push('scripts')
<script>
function overtimeApprovalPage() {
    return {
        showSplModal: false,
        selectedSpl: null,
        loading: false,
        toast: { show: false, message: '', type: 'success' },

        triggerToast(msg, type = 'success') {
            this.toast.message = msg;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },

        openModal(id, item) {
            this.selectedSpl = { id, ...item };
            this.showSplModal = true;
        },

        closeModal() {
            if (this.loading) return;
            this.showSplModal = false;
            this.selectedSpl = null;
        },

        async lockSpl() {
            if (!this.selectedSpl) return;
            this.loading = true;
            try {
                const res = await fetch(`{{ url('/hr/persetujuan/lembur') }}/${this.selectedSpl.id}/lock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal mengunci SPL');

                this.showSplModal = false;
                this.triggerToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } catch (e) {
                this.triggerToast(e.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        async rejectSpl() {
            if (!this.selectedSpl) return;
            this.loading = true;
            try {
                const res = await fetch(`{{ url('/hr/persetujuan/lembur') }}/${this.selectedSpl.id}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ reason: '' }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menolak SPL');

                this.showSplModal = false;
                this.triggerToast(data.message, 'error');
                setTimeout(() => window.location.reload(), 800);
            } catch (e) {
                this.triggerToast(e.message, 'error');
            } finally {
                this.loading = false;
            }
        },
    }
}
</script>
@endpush