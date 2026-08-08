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

    $components = [
        [
            'name' => 'Gaji Pokok', 'category' => 'Pendapatan Tetap', 'type' => 'Fixed',
            'rule' => 'Sesuai Kontrak Kerja', 'note' => 'Dasar perhitungan BPJS & Lembur.',
        ],
        [
            'name' => 'Tunj. Jabatan', 'category' => 'Pendapatan Tetap', 'type' => 'Fixed',
            'rule' => 'Sesuai Level Job Grade', 'note' => 'Melekat pada posisi/level.',
        ],
        [
            'name' => 'Tunj. Makan & Transport', 'category' => 'Pendapatan Variabel', 'type' => 'Harian',
            'rule' => 'Rp35.000 / Hari Hadir', 'note' => 'Dihitung dari total presensi sah.',
        ],
        [
            'name' => 'Upah Lembur (Overtime)', 'category' => 'Pendapatan Variabel', 'type' => 'Rumus Depnaker',
            'rule' => '1/173 × Gaji Pokok × Jam', 'note' => 'Diperhitungkan jika SPL diapprove.',
        ],
        [
            'name' => 'BPJS Kesehatan', 'category' => 'Potongan', 'type' => 'Persentase',
            'rule' => '1% dari Gaji Pokok', 'note' => 'Potongan wajib jaminan kesehatan karyawan.',
        ],
        [
            'name' => 'BPJS Ketenagakerjaan (JHT)', 'category' => 'Potongan', 'type' => 'Persentase',
            'rule' => '2% dari Gaji Pokok', 'note' => 'Potongan jaminan hari tua karyawan.',
        ],
        [
            'name' => 'PPh 21 (TER)', 'category' => 'Potongan', 'type' => 'Tarif Efektif',
            'rule' => 'Mengikuti Tabel TER PP 58/2023', 'note' => 'Dihitung otomatis oleh Payroll Engine.',
        ],
        [
            'name' => 'Reimbursement Klaim', 'category' => 'Pendapatan Variabel', 'type' => 'Klaim Bukti',
            'rule' => 'Sesuai Nominal Bukti Struk', 'note' => 'Ditambahkan jika klaim diapprove Finance.',
        ],
    ];
@endphp

@section('content')
<div x-data="{
    showAddModal: false
}">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Total Komponen Aktif</p>
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ count($components) }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Master komponen sistem</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Pendapatan Tetap</p>
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ collect($components)->where('category', 'Pendapatan Tetap')->count() }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Fixed basis kontrak</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Pendapatan Variabel</p>
            <p class="text-2xl font-extrabold font-mono-data text-amber-700">{{ collect($components)->where('category', 'Pendapatan Variabel')->count() }}</p>
            <p class="text-[11px] text-on-surface-variant/40 mt-1">Harian, SPL, &amp; klaim</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mb-1">Potongan Wajib</p>
            <p class="text-2xl font-extrabold font-mono-data text-error">{{ collect($components)->where('category', 'Potongan')->count() }}</p>
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

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest border-b border-black/5">
                        <th class="px-6 py-3.5">Komponen</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5">Tipe</th>
                        <th class="px-4 py-3.5">Nominal / Rumus</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($components as $c)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5 font-bold text-on-surface text-xs">
                                {{ $c['name'] }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $categoryColor[$c['category']] }}">
                                    {{ $c['category'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-on-surface-variant/70 text-xs font-mono-data">{{ $c['type'] }}</td>
                            <td class="px-4 py-3.5 font-mono-data text-xs text-on-surface font-extrabold">{{ $c['rule'] }}</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">{{ $c['note'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH KOMPONEN --}}
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showAddModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_circle</span>
                    <h3 class="text-base font-bold text-on-surface">Tambah Komponen Gaji</h3>
                </div>
                <button type="button" @click="showAddModal = false" class="text-on-surface-variant/40 hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nama Komponen</label>
                    <input type="text" placeholder="Contoh: Tunjangan Komunikasi" class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Kategori</label>
                        <select class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <option>Pendapatan Tetap</option>
                            <option>Pendapatan Variabel</option>
                            <option>Potongan</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Tipe Rumus</label>
                        <select class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                            <option>Fixed Nominal</option>
                            <option>Harian (Presensi)</option>
                            <option>Rumus SPL</option>
                            <option>Persentase</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Rumus / Nominal Default</label>
                    <input type="text" placeholder="Contoh: Rp250.000 / Bulan" class="w-full border border-black/10 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                <button type="button" @click="showAddModal = false"
                        class="px-4 py-2.5 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                    Batal
                </button>
                <button type="button" @click="showAddModal = false"
                        class="px-5 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm transition">
                    Simpan Komponen
                </button>
            </div>
        </div>
    </div>

</div>
@endsection