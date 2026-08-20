@extends('layouts.hr')

@section('title', 'Persetujuan Lembur (SPL)')
@section('page-title', 'Persetujuan Lembur (SPL)')
@section('page-desc', 'Surat Perintah Lembur (SPL), kalkulasi rumus Depnaker, dan penguncian data lembur.')

@php
    $statsView = [
        ['label' => 'Sedang Lembur Hari Ini', 'value' => $stats['today_overtime'] . ' Pegawai', 'icon' => 'timelapse', 'color' => 'text-gray-700'],
        ['label' => 'SPL Pending Approval', 'value' => $stats['pending'] . ' Pengajuan', 'icon' => 'pending_actions', 'color' => 'text-gray-700'],
        ['label' => 'Total Jam Lembur Disetujui', 'value' => number_format($stats['approved_hours'], 1, ',', '.') . ' Jam', 'icon' => 'schedule', 'color' => 'text-[#0B3D2E]'],
        ['label' => 'Estimasi Biaya Lembur', 'value' => 'Rp' . number_format($stats['estimated_cost'], 0, ',', '.'), 'icon' => 'payments', 'color' => 'text-[#0B3D2E]'],
    ];

    $badge = [
        'pending_spv'  => 'bg-gray-50 text-gray-700',
        'approved_spv' => 'bg-gray-50 text-gray-700',
        'locked'       => 'bg-gray-50 text-[#0B3D2E]',
        'rejected'     => 'bg-gray-50 text-gray-700',
    ];
@endphp

@section('content')
    <div x-data="overtimeApprovalPage()">


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            @foreach ($statsView as $s)
                <div class="bg-white rounded-md p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">{{ $s['label'] }}</p>
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                            <span class="material-symbols-outlined text-[18px]">{{ $s['icon'] }}</span>
                        </div>
                    </div>
                    <p class="text-3xl font-semibold  {{ $s['color'] }} leading-none">{{ $s['value'] }}</p>
                </div>
            @endforeach
        </div>


        @php
        $alpineItems = collect($requests->items())->map(function($r) {
            return [
                'name' => strtolower($r->employee->full_name ?? ''),
                'project' => strtolower($r->project ?? ''),
                'nip' => strtolower($r->employee->employee_id ?? '')
            ];
        })->toJson();
        @endphp
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden"
            x-data="{
                searchQuery: '{{ request('q') }}',
                items: {{ $alpineItems }},
                get hasVisibleRows() {
                    return this.items.some(i => 
                        this.searchQuery === '' || 
                        i.name.includes(this.searchQuery.toLowerCase()) || 
                        i.project.includes(this.searchQuery.toLowerCase()) ||
                        i.nip.includes(this.searchQuery.toLowerCase())
                    );
                }
            }">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
                <div>
                    <h2 class="text-base font-medium text-gray-800">Daftar Surat Perintah Lembur (SPL)</h2>
                    <p class="text-xs text-gray-500 mt-1">Kunci SPL yang sudah disetujui Supervisor sebelum dimasukkan ke rekap Payroll</p>
                </div>

                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('hr.approvals.overtime') }}" class="relative" @submit.prevent>
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[18px]">search</span>
                        <input type="text" name="q" x-model="searchQuery" placeholder="Cari nama atau proyek..." autocomplete="off"
                            class="w-64 pl-10 pr-4 py-2.5 bg-white rounded-md text-sm border border-gray-200
                                      focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4 min-w-[200px]">Karyawan</th>
                            <th class="px-6 py-4 min-w-[220px]">Keperluan &amp; Proyek</th>
                            <th class="px-6 py-4 min-w-[120px]">Durasi Jam</th>
                            <th class="px-6 py-4 text-right min-w-[140px]">Estimasi Upah Lembur</th>
                            <th class="px-6 py-4 min-w-[140px]">Status</th>
                            <th class="px-6 py-4 text-center min-w-[170px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($requests as $r)
                            @php
                                $upah = $r->overtime_pay
                                    ?? \App\Models\OvertimeRequest::calculateOvertimePay($r->salary_snapshot, $r->hours);
                                $initials = strtoupper(substr($r->employee->full_name ?? '?', 0, 1));
                            @endphp
                            <tr class="hover:bg-gray-50 transition" id="spl-row-{{ $r->id }}"
                                x-show="searchQuery === '' || '{{ strtolower(addslashes($r->employee->full_name ?? '')) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($r->project ?? '')) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($r->employee->employee_id ?? '')) }}'.includes(searchQuery.toLowerCase())">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 border border-gray-300
                                                    flex items-center justify-center text-xs font-medium shrink-0 uppercase">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 text-sm leading-tight">{{ $r->employee->full_name }}</p>
                                            <p class="text-[11px]  text-gray-500 mt-0.5">
                                                {{ $r->employee->employee_id }} · {{ $r->employee->department?->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ $r->project ?? '-' }}</p>
                                    <p class="text-[11px] text-gray-500  mt-1">
                                        {{ $r->date->translatedFormat('d M Y') }}
                                        <span class="text-gray-400 mx-1">|</span>
                                        {{ \Carbon\Carbon::parse($r->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($r->end_time)->format('H:i') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4  font-medium text-sm text-gray-800">
                                    {{ rtrim(rtrim(number_format($r->hours, 1, ',', '.'), '0'), ',') }} Jam
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class=" font-semibold text-sm text-[#0B3D2E]">
                                        Rp{{ number_format($upah, 0, ',', '.') }}
                                    </p>
                                    @if (is_null($r->overtime_pay))
                                        <span class="block text-[10px] font-sans font-medium text-gray-400 mt-0.5">estimasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-medium uppercase tracking-wider {{ $badge[$r->status] ?? '' }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($r->status === 'approved_spv')
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
                                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-xs font-medium transition shadow-sm w-full">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
                                            Kunci SPL
                                        </button>
                                    @elseif ($r->status === 'locked')
                                        <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-[#0B3D2E] text-xs font-medium w-full">
                                            <span class="material-symbols-outlined text-[16px]">lock</span>
                                            Terkunci
                                        </span>
                                    @elseif ($r->status === 'rejected')
                                        <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-gray-700 text-xs font-medium w-full">
                                            <span class="material-symbols-outlined text-[16px]">block</span>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-md bg-gray-50 text-gray-700 text-xs font-medium w-full">
                                            <span class="material-symbols-outlined text-[16px]">hourglass_top</span>
                                            Menunggu SPV
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                                    Belum ada pengajuan lembur.
                                </td>
                            </tr>
                        @endforelse
                        
                        @if($requests->count() > 0)
                            <tr x-show="!hasVisibleRows" style="display: none;" x-transition>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <template x-if="searchQuery">
                                        <span>Tidak ada lembur yang sesuai dengan pencarian "<span x-text="searchQuery" class="font-medium text-gray-700"></span>".</span>
                                    </template>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>


        <div x-show="showSplModal" x-cloak class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
            @click.self="closeModal()">
            <div class="bg-white rounded-md max-w-lg w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in-95 duration-150 border border-gray-100"
                x-show="selectedSpl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gray-50 text-[#0B3D2E] flex items-center justify-center border border-gray-200">
                            </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">Konfirmasi &amp; Kunci SPL</h3>
                            <p class="text-xs text-gray-500 mt-1">Kunci data lembur agar masuk ke perhitungan Payroll</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="space-y-4 text-sm">
                    <div class="p-4 rounded-md bg-gray-50 border border-gray-100 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Nama Karyawan:</span>
                            <span class="font-medium text-gray-800" x-text="selectedSpl ? selectedSpl.name : ''"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Proyek/Pekerjaan:</span>
                            <span class="font-medium text-gray-800" x-text="selectedSpl ? selectedSpl.project : ''"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Durasi Lembur:</span>
                            <span class="font-medium  text-gray-800"
                                x-text="selectedSpl ? selectedSpl.hours + ' Jam (' + selectedSpl.start + ' – ' + selectedSpl.end + ')' : ''"></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="text-gray-600 font-medium text-xs uppercase tracking-wide">Kalkulasi Upah (Depnaker):</span>
                            <span class="font-semibold  text-lg text-[#0B3D2E]"
                                x-text="selectedSpl ? 'Rp' + Number(selectedSpl.upah).toLocaleString('id-ID') : ''"></span>
                        </div>
                    </div>

                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[10px] tracking-wide block mb-1.5">Catatan Pekerjaan</label>
                        <p class="p-4 rounded-md border border-gray-100 bg-gray-50 text-gray-700 min-h-[80px]"
                            x-text="selectedSpl ? selectedSpl.notes : ''"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="closeModal()" :disabled="loading"
                        class="px-5 py-2.5 rounded-md bg-gray-100 text-sm font-medium text-gray-700 hover:bg-gray-200 transition disabled:opacity-50">
                        Batal
                    </button>
                    <button type="button" @click="rejectSpl()" :disabled="loading"
                        class="px-5 py-2.5 rounded-md border border-gray-200 text-gray-700 bg-gray-50 hover:bg-gray-50 text-sm font-medium transition disabled:opacity-50">
                        <span x-show="!loading">Tolak SPL</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button" @click="lockSpl()" :disabled="loading"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium shadow-sm flex items-center gap-2 transition disabled:opacity-50">
                        <span class="material-symbols-outlined text-[18px]" x-show="!loading">lock</span>
                        <span x-show="!loading">Kunci &amp; Teruskan</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>


        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-md shadow-sm text-white font-medium text-sm border border-gray-200/30 "
            :class="{
                 'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
                 'bg-gray-50 border-gray-200/30 text-white': toast.type === 'error'
             }" style="display: none;">
            <span class="material-symbols-outlined text-[20px]"
                :class="toast.type === 'error' ? 'text-white' : 'text-emerald-100'"
                x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
            <span x-text="toast.message" class="text-sm font-medium"></span>
        </div>

    </div>

<x-auto-refresh />
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
