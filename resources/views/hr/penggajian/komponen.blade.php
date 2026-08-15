@extends('layouts.hr')

@section('title', 'Komponen & Master Gaji')
@section('page-title', 'Komponen & Master Gaji')
@section('page-desc', 'Ketentuan kalkulasi dan komponen default yang fleksibel.')

@php
    $categoryColor = [
        'Pendapatan Tetap' => 'bg-primary/10 text-primary',
        'Pendapatan Variabel' => 'bg-amber-500/10 text-amber-800',
        'Potongan' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    editing: null,
    openEdit(c) { this.editing = c; this.showEditModal = true; },
    toast: { show: false, message: {{ Js::from(session('success')) }} },
}" x-init="if (toast.message) { toast.show = true; setTimeout(() => toast.show = false, 3000); }">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Total Komponen Aktif</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ $totalComponents }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Master komponen sistem</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Pendapatan Tetap</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ $totalTetap }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Fixed basis kontrak</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Pendapatan Variabel</p>
            <p class="text-2xl font-extrabold font-mono-data text-amber-700">{{ $totalVariabel }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Harian, SPL, &amp; klaim</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Potongan Wajib</p>
            <p class="text-2xl font-extrabold font-mono-data text-error">{{ $totalPotongan }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">BPJS &amp; PPh21 TER</p>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Master Komponen &amp; Rumus Gaji</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Ketentuan kalkulasi default yang fleksibel untuk seluruh job grade dan tipe kontrak</p>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" @click="showAddModal = true"
                        class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Komponen
                </button>
            </div>
        </div>

        {{-- Kolom mengikuti tampilan baru: Komponen (+ badge Kena Pajak inline), Kategori, Tipe, Nominal/Rumus, Keterangan.
             Kolom Aksi tetap dipertahankan karena edit/hapus adalah fungsi CRUD nyata yang tidak ada di versi contoh dummy. --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Komponen</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5">Tipe</th>
                        <th class="px-4 py-3.5">Nominal / Rumus</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse ($components as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5">
                                <p class="font-bold text-on-surface text-xs">{{ $c->name }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-mono-data text-on-surface-variant/40">
                                        {{ $c->type === 'earning' ? 'Earning' : 'Deduction' }}
                                    </span>
                                    @if ($c->is_taxable)
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-error/10 text-error">Kena Pajak</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $categoryColor[$c->category] ?? 'bg-surface-container text-on-surface-variant/60' }}">
                                    {{ $c->category ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-on-surface-variant/70 text-xs font-mono-data">{{ $c->calculation_type ?? '-' }}</td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface font-extrabold">
                                {{ $c->formula_note ?? ($c->default_amount ? 'Rp' . number_format($c->default_amount, 0, ',', '.') : '-') }}
                            </td>
                            {{-- TODO: field 'description' belum ada di skema lama (yg ada cuma formula_note). Tambahkan kolom ini di migration bila ingin diisi. --}}
                            <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">
                                {{ $c->description ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" @click="openEdit({{ Js::from($c) }})"
        class="p-1.5 rounded-lg text-on-surface-variant/40 hover:text-primary hover:bg-primary/10 transition" title="Edit">
    <span class="material-symbols-outlined text-[16px]">edit</span>
</button>
                                    <form method="POST" action="{{ route('hr.payroll.components.destroy', $c->id) }}"
                                          onsubmit="return confirm('Hapus komponen {{ $c->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg text-on-surface-variant/40 hover:text-error hover:bg-error/10 transition" title="Hapus">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-xs text-on-surface-variant/50">
                                Belum ada komponen gaji. Klik "Tambah Komponen" untuk membuat yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH KOMPONEN --}}
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showAddModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_circle</span>
                    <h3 class="text-base font-bold text-on-surface">Tambah Komponen Gaji</h3>
                </div>
                <button type="button" @click="showAddModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
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
                        <select name="category" required class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <option value="Pendapatan Tetap">Pendapatan Tetap</option>
                            <option value="Pendapatan Variabel">Pendapatan Variabel</option>
                            <option value="Potongan">Potongan</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe (untuk kalkulasi)</label>
                        <select name="type" required class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <option value="earning">Earning (Penambah)</option>
                            <option value="deduction">Deduction (Pengurang)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe Kalkulasi (tampilan)</label>
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
                {{-- TODO: field baru sesuai tampilan baru — tambahkan kolom 'description' di migration jika ingin disimpan --}}
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Keterangan (opsional)</label>
                    <input type="text" name="description" placeholder="Contoh: Diperhitungkan jika SPL diapprove"
                           class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                    <button type="button" @click="showAddModal = false"
                            class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm transition">
                        Simpan Komponen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT KOMPONEN --}}
    <div x-show="showEditModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showEditModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" x-show="editing">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">edit</span>
                    <h3 class="text-base font-bold text-on-surface">Edit Komponen Gaji</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="editing ? '{{ url('hr/penggajian/komponen') }}/' + editing.id : '#'" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nama Komponen</label>
                    <input type="text" name="name" required x-bind:value="editing?.name"
                           class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Kategori</label>
                        <select name="category" required class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <template x-for="opt in ['Pendapatan Tetap','Pendapatan Variabel','Potongan']" :key="opt">
                                <option :value="opt" :selected="editing?.category === opt" x-text="opt"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe</label>
                        <select name="type" required class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <option value="earning" :selected="editing?.type === 'earning'">Earning</option>
                            <option value="deduction" :selected="editing?.type === 'deduction'">Deduction</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe Kalkulasi</label>
                    <input type="text" name="calculation_type" x-bind:value="editing?.calculation_type"
                           class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nominal Default</label>
                        <input type="number" step="0.01" name="default_amount" x-bind:value="editing?.default_amount"
                               class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                    <div class="flex items-end pb-2.5">
                        <label class="flex items-center gap-2 text-xs font-bold text-on-surface-variant/70">
                            <input type="checkbox" name="is_taxable" value="1" x-bind:checked="editing?.is_taxable" class="rounded border-black/20">
                            Kena Pajak (PPh21)
                        </label>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Catatan Rumus</label>
                    <input type="text" name="formula_note" x-bind:value="editing?.formula_note"
                           class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Keterangan (opsional)</label>
                    <input type="text" name="description" x-bind:value="editing?.description"
                           class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="toast.show" x-transition
         class="fixed bottom-6 right-6 z-50 bg-[#0B3D2E] text-white text-xs font-medium px-4 py-2.5 rounded-2xl flex items-center gap-2 shadow-lg">
        <span class="material-symbols-outlined text-[16px] text-emerald-400">check_circle</span>
        <span x-text="toast.message"></span>
    </div>

</div>
@endsection