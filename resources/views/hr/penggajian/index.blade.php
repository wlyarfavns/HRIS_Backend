@extends('layouts.hr')

@section('title', 'Payroll')
@section('page-title', 'Payroll')
@section('page-desc', 'Komponen gaji, eksekusi engine, dan tinjau hasil kalkulasi periode berjalan.')

@php
    $categoryColor = [
        'Pendapatan Tetap' => ['pill' => 'bg-primary/10 text-primary border border-primary/20', 'dot' => 'bg-primary'],
        'Pendapatan Variabel' => ['pill' => 'bg-amber-500/10 text-amber-800 border border-amber-500/20', 'dot' => 'bg-amber-500'],
        'Potongan' => ['pill' => 'bg-error/10 text-error border border-error/20', 'dot' => 'bg-error'],
    ];
@endphp

@section('content')
    <div x-data="{
                    /* ── VIEW STATE ─────────────────────── */
                    view: 'rules',

                    /* ── ADD & EDIT KOMPONEN MODAL ──────── */
                    showAddModal: false,
                    showEditModal: false,
                    editItem: {},
                    openEdit(item) { 
                        this.editItem = { ...item }; 
                        this.showEditModal = true; 
                    },

                    /* ── DETAIL SIDE PANEL (SLIP REVIEW) ── */
                    showDetail: false,
                    selectedEmp: null,
                    openDetail(item) { 
                        this.selectedEmp = {
                            name: item.employee?.full_name || '-',
                            nip: item.employee?.employee_id || '-',
                            dept: item.employee?.department?.name || '-',
                            basic: parseFloat(item.basic_salary) || 0,
                            allowance: parseFloat(item.total_allowances) || 0,
                            deduction: parseFloat(item.total_deductions) || 0,
                            net: parseFloat(item.net_salary) || 0,
                            status: item.status
                        };
                        this.showDetail = true; 
                    },

                    /* ── SUBMIT CONFIRM ──────────────────── */
                    showSubmitConfirm: false,
                    submitted: false,
                    submitToFinance() { 
                        this.showSubmitConfirm = false; 
                        document.getElementById('approveHrForm').submit();
                    },

                    /* ── TOAST ───────────────────────────── */
                    toast: { show: false, message: '', type: 'success' },
                    showToast(msg, type = 'success') { 
                        this.toast.message = msg; 
                        this.toast.type = type;
                        this.toast.show = true; 
                        setTimeout(() => this.toast.show = false, 3000); 
                    }
                }" x-init="let sessionMsg = @json(session('success')); if (sessionMsg) showToast(sessionMsg)">

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- VIEW 1 — KOMPONEN & MASTER GAJI (PAYROLL RULES) --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div x-show="view === 'rules'">
            <div class="card-flat rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Komponen & Master Gaji</h2>
                        <p class="text-xs text-on-surface-variant/50 mt-0.5">Ketentuan kalkulasi dan komponen default yang
                            fleksibel</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" @click="showAddModal = true"
                            class="border border-black/10 hover:bg-surface-container px-3.5 py-2.5 rounded-xl text-xs font-bold text-on-surface flex items-center gap-1.5 transition">
                            <span class="material-symbols-outlined text-[16px] text-primary">add_circle</span>
                            Tambah Aturan
                        </button>
                        @if($payrolls->count() > 0)
                            <button type="button" @click="view = 'review'"
                                class="bg-amber-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl flex items-center gap-2 hover:brightness-110 shadow-md transition">
                                <span class="material-symbols-outlined text-[17px]">fact_check</span>
                                Lihat Draft Payroll Saat Ini
                            </button>
                        @endif
                        <a href="{{ route('hr.payroll.showRun') }}"
                            class="bg-primary text-white text-xs font-bold px-5 py-2.5 rounded-xl flex items-center gap-2 hover:brightness-110 shadow-md transition">
                            <span class="material-symbols-outlined text-[17px]">play_circle</span>
                            Mulai Proses Payroll
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                                <th class="px-6 py-3.5">Komponen</th>
                                <th class="px-4 py-3.5">Kategori</th>
                                <th class="px-4 py-3.5">Tipe</th>
                                <th class="px-4 py-3.5">Nominal / Rumus</th>
                                <th class="px-6 py-3.5">Keterangan</th>
                                <th class="px-4 py-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach ($components as $c)
                                <tr class="hover:bg-primary/4 transition">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $categoryColor[$c->category]['dot'] ?? 'bg-black/20' }}"></span>
                                            <span class="font-bold text-on-surface text-xs">{{ $c->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span
                                            class="text-[11px] font-semibold px-2.5 py-1 rounded-lg {{ $categoryColor[$c->category]['pill'] ?? 'bg-surface-container' }}">
                                            {{ $c->category }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-on-surface-variant/70 text-xs font-mono-data">
                                        {{ $c->calculation_type ?? $c->type }}
                                    </td>
                                    <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface font-extrabold">
                                        {{ $c->formula_note ?? 'Rp ' . number_format($c->default_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">{{ $c->description ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click='openEdit(@json($c))'
                                                class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/8 transition"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                            </button>
                                            <form method="POST" action="{{ route('hr.payroll.components.destroy', $c->id) }}"
                                                onsubmit="return confirm('Hapus komponen {{ $c->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-on-surface-variant/50 hover:text-error hover:bg-error/10 transition"
                                                    title="Hapus">
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- VIEW 2 — REVIEW HASIL KALKULASI --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div x-show="view === 'review'" x-cloak>
            <button type="button" @click="view = 'rules'"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-on-surface-variant/60 hover:text-primary transition mb-4">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Komponen Gaji
            </button>

            <div class="card-flat rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Pratinjau Payroll
                            {{ $start->translatedFormat('F Y') }}
                        </h2>
                        <p class="text-xs text-on-surface-variant/50 mt-0.5">Klik ikon mata untuk melihat detail per
                            karyawan</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                                <th class="px-6 py-3.5">Karyawan</th>
                                <th class="px-4 py-3.5">Departemen</th>
                                <th class="px-4 py-3.5 text-right">Pendapatan</th>
                                <th class="px-4 py-3.5 text-right">Potongan</th>
                                <th class="px-4 py-3.5 text-right">Take Home Pay</th>
                                <th class="px-4 py-3.5 text-center w-20">Slip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach ($payrolls as $p)
                                @php
                                    $gross = $p->basic_salary + $p->total_allowances;
                                    $deduct = $p->total_deductions;
                                    $initials = strtoupper(substr($p->employee->full_name ?? 'U', 0, 2));
                                @endphp
                                <tr class="hover:bg-primary/4 transition">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center font-bold text-xs text-primary shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-on-surface text-xs leading-tight">
                                                    {{ $p->employee->full_name }}
                                                </p>
                                                <p class="text-[10px] font-mono-data text-on-surface-variant/40">
                                                    {{ $p->employee->employee_id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-on-surface-variant/60">
                                        {{ $p->employee->department->name ?? '-' }}
                                    </td>
                                    <td
                                        class="px-4 py-3.5 text-right font-mono-data text-xs text-on-surface-variant/80 font-semibold">
                                        Rp{{ number_format($gross, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-mono-data text-xs text-error font-semibold">
                                        -Rp{{ number_format($deduct, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-xs text-primary">
                                        Rp{{ number_format($p->net_salary, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <button type="button" @click='openDetail(@json($p))'
                                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/8 transition">
                                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($payrolls->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">Belum ada
                                        data kalkulasi payroll.</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-surface-container/60 border-t-2 border-black/8">
                                <td class="px-6 py-3.5 text-xs font-extrabold text-on-surface">TOTAL
                                    ({{ $payrolls->count() }} Karyawan)</td>
                                <td></td>
                                <td class="px-4 py-3.5 text-right font-mono-data text-xs font-extrabold text-on-surface">
                                    Rp{{ number_format($totalGross, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-right font-mono-data text-xs font-extrabold text-error">
                                    -Rp{{ number_format($totalDeduct, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-right font-mono-data font-extrabold text-primary">
                                    Rp{{ number_format($totalNet, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div
                    class="px-6 py-4 border-t border-black/5 bg-surface-container/30 flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-[11px] text-on-surface-variant/50">
                        Data yang disubmit akan di-lock dan diteruskan ke Finance untuk Persetujuan & Pencairan Dana.
                    </p>
                    <div class="flex items-center gap-2.5">
                        <form method="GET" action="{{ route('hr.payroll.export') }}" target="_blank">
                            <input type="hidden" name="period" value="{{ $end->format('Y-m') }}">

                            <button type="submit"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 transition bg-white hover:bg-surface-container text-on-surface border border-black/10 shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-primary">download</span>
                                Unduh XLSX
                            </button>
                        </form>

                        <form id="approveHrForm" method="POST" action="{{ route('hr.payroll.approveHr') }}">
                            @csrf
                            <input type="hidden" name="period" value="{{ $start->format('Y-m') }}">
                        </form>

                        <button type="button" @click="showSubmitConfirm = true" {{ $payrolls->isEmpty() ? 'disabled' : '' }}
                            class="text-xs font-bold px-5 py-2.5 rounded-xl flex items-center gap-2 transition {{ $payrolls->isEmpty() ? 'opacity-50 cursor-not-allowed bg-surface-container border border-black/10' : 'bg-primary hover:brightness-110 text-white shadow-md' }}">
                            <span class="material-symbols-outlined text-[16px]">send</span>
                            Kirim ke Finance
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- MODAL TAMBAH KOMPONEN --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
            @click.self="showAddModal = false">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-black/5 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">add_circle</span>
                        <h3 class="text-base font-bold text-on-surface">Tambah Komponen Gaji</h3>
                    </div>
                    <button type="button" @click="showAddModal = false"
                        class="text-on-surface-variant/40 hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('hr.payroll.components.store') }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nama Komponen</label>
                        <input type="text" name="name" required placeholder="Contoh: Tunjangan Komunikasi"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Kategori</label>
                            <select name="category" required
                                class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                                <option value="Pendapatan Tetap">Pendapatan Tetap</option>
                                <option value="Pendapatan Variabel">Pendapatan Variabel</option>
                                <option value="Potongan">Potongan</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe
                                (kalkulasi)</label>
                            <select name="type" required
                                class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                                <option value="earning">Earning (Penambah)</option>
                                <option value="deduction">Deduction (Pengurang)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe Kalkulasi
                            (tampilan)</label>
                        <input type="text" name="calculation_type" placeholder="Contoh: Fixed Nominal, Harian, Persentase"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nominal Default</label>
                            <input type="number" step="0.01" name="default_amount" placeholder="250000"
                                class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                        </div>
                        <div class="flex items-end pb-2.5">
                            <label class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/70">
                                <input type="checkbox" name="is_taxable" value="1" class="rounded border-black/20">
                                Kena Pajak (PPh21)
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Catatan Rumus</label>
                        <input type="text" name="formula_note" placeholder="Contoh: 1/173 × Gaji Pokok × Jam"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Keterangan
                            (opsional)</label>
                        <input type="text" name="description" placeholder="Contoh: Diperhitungkan jika SPL diapprove"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                        <button type="button" @click="showAddModal = false"
                            class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm transition">Simpan
                            Komponen</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- MODAL EDIT KOMPONEN --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
            @click.self="showEditModal = false">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" x-show="editItem?.id">
                <div class="flex items-center justify-between border-b border-black/5 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">edit</span>
                        <h3 class="text-base font-bold text-on-surface">Edit Komponen Gaji</h3>
                    </div>
                    <button type="button" @click="showEditModal = false"
                        class="text-on-surface-variant/40 hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editItem?.id ? '{{ url('hr/penggajian/komponen') }}/' + editItem.id : '#'" method="POST"
                    class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nama Komponen</label>
                        <input type="text" name="name" required x-bind:value="editItem?.name"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Kategori</label>
                            <select name="category" required
                                class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                                <template x-for="opt in ['Pendapatan Tetap','Pendapatan Variabel','Potongan']" :key="opt">
                                    <option :value="opt" :selected="editItem?.category === opt" x-text="opt"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe</label>
                            <select name="type" required
                                class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                                <option value="earning" :selected="editItem?.type === 'earning'">Earning (Penambah)</option>
                                <option value="deduction" :selected="editItem?.type === 'deduction'">Deduction (Pengurang)
                                </option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe Kalkulasi</label>
                        <input type="text" name="calculation_type" x-bind:value="editItem?.calculation_type"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nominal Default</label>
                            <input type="number" step="0.01" name="default_amount" x-bind:value="editItem?.default_amount"
                                class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                        </div>
                        <div class="flex items-end pb-2.5">
                            <label class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/70">
                                <input type="checkbox" name="is_taxable" value="1" x-bind:checked="editItem?.is_taxable"
                                    class="rounded border-black/20">
                                Kena Pajak (PPh21)
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Catatan Rumus</label>
                        <input type="text" name="formula_note" x-bind:value="editItem?.formula_note"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Keterangan
                            (opsional)</label>
                        <input type="text" name="description" x-bind:value="editItem?.description"
                            class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm transition">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SIDE PANEL — SLIP GAJI DETAIL --}}
        <div x-show="showDetail" x-cloak class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"
            @click="showDetail = false"></div>
        <div x-show="showDetail" x-cloak
            class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col"
            x-transition:enter="transition ease-out duration-300 translate-x-full"
            x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-full">

            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3" x-show="selectedEmp">
                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center font-bold text-primary">
                        <span x-text="selectedEmp?.name.substring(0, 2).toUpperCase()"></span>
                    </div>
                    <div>
                        <p class="font-bold text-on-surface text-sm" x-text="selectedEmp?.name"></p>
                        <p class="text-xs text-on-surface-variant/50">
                            <span x-text="selectedEmp?.nip"></span> · <span x-text="selectedEmp?.dept"></span>
                        </p>
                    </div>
                </div>
                <button @click="showDetail = false"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant/30 hover:bg-surface-container">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto" x-show="selectedEmp">
                <div class="px-6 pt-5 pb-3">
                    <p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-3">Pendapatan
                    </p>
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="text-sm text-on-surface">Gaji Pokok & Tunjangan Tetap</span>
                        <span class="font-mono-data text-sm font-semibold text-on-surface"
                            x-text="'Rp' + (selectedEmp?.basic || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="text-sm text-on-surface">Tunjangan Lainnya / Lembur</span>
                        <span class="font-mono-data text-sm font-semibold text-on-surface"
                            x-text="'Rp' + (selectedEmp?.allowance || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>
                <div class="px-6 py-3 flex justify-between items-baseline bg-surface-container/40">
                    <span class="text-sm font-bold text-on-surface">Total Pendapatan</span>
                    <span class="font-mono-data text-sm font-bold text-on-surface"
                        x-text="'Rp' + (selectedEmp?.basic + selectedEmp?.allowance).toLocaleString('id-ID')"></span>
                </div>

                <div class="px-6 pt-4 pb-3">
                    <p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-3">Potongan</p>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm text-on-surface">Total Potongan (BPJS/PPh21)</span>
                        <span class="font-mono-data text-sm font-semibold text-error"
                            x-text="'-Rp' + (selectedEmp?.deduction).toLocaleString('id-ID')"></span>
                    </div>
                </div>
                <div class="px-6 py-3 flex justify-between items-baseline bg-surface-container/40">
                    <span class="text-sm font-bold text-on-surface">Total Potongan</span>
                    <span class="font-mono-data text-sm font-bold text-error"
                        x-text="'-Rp' + (selectedEmp?.deduction).toLocaleString('id-ID')"></span>
                </div>

                <div class="mx-6 my-4 rounded-xl border border-primary/15 bg-primary/5 px-5 py-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-bold text-on-surface">Take Home Pay</p>
                        <p class="font-mono-data text-lg font-extrabold text-primary"
                            x-text="'Rp' + (selectedEmp?.net).toLocaleString('id-ID')"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL KONFIRMASI SUBMIT --}}
        <div x-show="showSubmitConfirm" x-cloak
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl overflow-hidden p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-primary text-[28px]">send</span>
                </div>
                <h3 class="text-base font-bold text-on-surface mb-2">Konfirmasi Kirim ke Finance</h3>
                <p class="text-xs text-on-surface-variant/60 leading-relaxed mb-4">
                    Data payroll ini akan di-<strong>lock</strong> dan diteruskan ke Finance untuk Persetujuan & Pencairan
                    Dana.
                </p>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="showSubmitConfirm = false"
                        class="flex-1 px-4 py-3 rounded-xl border border-black/10 text-xs font-bold hover:bg-surface-container">Batal</button>
                    <button type="button" @click="submitToFinance()"
                        class="flex-1 px-4 py-3 rounded-xl bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">send</span> Kirim
                    </button>
                </div>
            </div>
        </div>

        {{-- TOAST NOTIFICATION --}}
        <div x-show="toast.show" x-cloak
            class="fixed bottom-6 right-6 z-[70] flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-white text-xs backdrop-blur-md"
            :class="toast.type === 'error' ? 'bg-rose-950 border border-rose-500/30' : 'bg-[#0B3D2E] border border-emerald-500/30'">
            <span class="material-symbols-outlined text-[20px]"
                :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'"
                x-text="toast.type === 'error' ? 'error' : 'check_circle'"></span>
            <span x-text="toast.message" class="font-semibold"></span>
        </div>

    </div>
@endsection