@extends('layouts.hr')

@section('title', 'Komponen & Master Gaji')
@section('page-title', 'Komponen & Master Gaji')
@section('page-desc', 'Ketentuan kalkulasi dan komponen default yang fleksibel.')

@section('page-action')
    <button type="button" class="border border-primary/30 text-primary text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 hover:bg-primary/5 transition">
        <span class="material-symbols-outlined text-[16px]">rule_settings</span>
        Payroll Rules
    </button>
@endsection

@php
    $categoryColor = [
        'Pendapatan Tetap' => 'bg-primary/10 text-primary',
        'Pendapatan Variabel' => 'bg-amber-500/10 text-amber-700',
        'Potongan' => 'bg-error/10 text-error',
    ];

    $components = [
        [
            'name' => 'Gaji Pokok', 'category' => 'Pendapatan Tetap', 'type' => 'Fixed',
            'rule' => 'Sesuai Kontrak Kerja', 'note' => 'Dasar perhitungan BPJS & Lembur.', 'active' => true,
        ],
        [
            'name' => 'Tunj. Jabatan', 'category' => 'Pendapatan Tetap', 'type' => 'Fixed',
            'rule' => 'Sesuai Level Job Grade', 'note' => 'Melekat pada posisi/level.', 'active' => true,
        ],
        [
            'name' => 'Tunj. Makan & Transport', 'category' => 'Pendapatan Variabel', 'type' => 'Harian',
            'rule' => 'Rp35.000 / Hari Hadir', 'note' => 'Dihitung dari total presensi sah.', 'active' => true,
        ],
        [
            'name' => 'Upah Lembur (Overtime)', 'category' => 'Pendapatan Variabel', 'type' => 'Rumus Depnaker',
            'rule' => '1/173 × Gaji Pokok × Jam', 'note' => 'Diperhitungkan jika SPL diapprove.', 'active' => true,
        ],
        [
            'name' => 'BPJS Kesehatan', 'category' => 'Potongan', 'type' => 'Persentase',
            'rule' => '1% dari Gaji Pokok', 'note' => 'Potongan wajib karyawan.', 'active' => true,
        ],
        [
            'name' => 'BPJS Ketenagakerjaan (JHT)', 'category' => 'Potongan', 'type' => 'Persentase',
            'rule' => '2% dari Gaji Pokok', 'note' => 'Potongan wajib karyawan.', 'active' => true,
        ],
        [
            'name' => 'PPh 21', 'category' => 'Potongan', 'type' => 'TER (Tarif Efektif)',
            'rule' => 'Mengikuti tabel TER Depkeu', 'note' => 'Dihitung otomatis oleh Payroll Engine.', 'active' => true,
        ],
        [
            'name' => 'Reimbursement', 'category' => 'Pendapatan Variabel', 'type' => 'Klaim',
            'rule' => 'Sesuai Nominal Bukti', 'note' => 'Ditambahkan jika klaim diapprove Finance.', 'active' => false,
        ],
    ];
@endphp

@section('content')

    <div class="grid grid-cols-4 gap-5">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ count($components) }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Komponen</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ collect($components)->where('category', 'Pendapatan Tetap')->count() }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Pendapatan Tetap</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-amber-700">{{ collect($components)->where('category', 'Pendapatan Variabel')->count() }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Pendapatan Variabel</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-error">{{ collect($components)->where('category', 'Potongan')->count() }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Potongan</p>
        </div>
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Komponen &amp; Master Gaji</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Ketentuan kalkulasi dan komponen default yang fleksibel.</p>
            </div>
            <button type="button" class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tambah Komponen
            </button>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3">Komponen</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Tipe</th>
                    <th class="px-6 py-3">Nominal / Rumus</th>
                    <th class="px-6 py-3">Keterangan</th>
                    <th class="px-6 py-3 text-center">Aktif</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($components as $c)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1.5 rounded {{ $categoryColor[$c['category']] }}">
                                {{ $c['name'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $c['category'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $c['type'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data text-xs text-on-surface font-semibold">{{ $c['rule'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">{{ $c['note'] }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <button type="button"
                                class="relative w-10 h-5.5 rounded-full transition {{ $c['active'] ? 'bg-primary' : 'bg-outline-variant' }}"
                                style="height:22px;">
                                <span class="absolute top-0.5 {{ $c['active'] ? 'right-0.5' : 'left-0.5' }} w-4 h-4 bg-white rounded-full shadow transition"></span>
                            </button>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <button type="button" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CATATAN ENGINE --}}
    <div class="card-flat rounded-2xl p-6 flex items-start gap-4">
        <span class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[20px]">info</span>
        </span>
        <div>
            <p class="text-sm font-bold text-on-surface">Bagaimana komponen ini dipakai Payroll Engine?</p>
            <p class="text-xs text-on-surface-variant/60 mt-1 leading-relaxed">
                Setiap periode payroll, engine mengambil komponen aktif di atas lalu menghitung
                <span class="font-semibold text-on-surface">Pendapatan Tetap + Pendapatan Variabel − Potongan</span>
                per karyawan berdasarkan data presensi, SPL yang sudah dikunci HR, dan klaim reimbursement yang disetujui Finance.
                Hasil akhirnya tampil di
                <a href="{{ route('hr.payroll.index') }}" class="text-primary font-bold hover:underline">Proses Payroll</a>.
            </p>
        </div>
    </div>

@endsection