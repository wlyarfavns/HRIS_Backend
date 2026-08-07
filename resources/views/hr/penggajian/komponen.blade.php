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
        'earning' => 'bg-primary/10 text-primary',
        'deduction' => 'bg-error/10 text-error',
    ];
@endphp

@section('content')
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-error/10 border border-error flex flex-col gap-1">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-error">error</span>
                <p class="text-sm font-bold text-error">Terdapat Kesalahan Input!</p>
            </div>
            <ul class="text-xs text-error mt-1 ml-9 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-on-surface">{{ $totalComponents }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Komponen</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-primary">{{ $totalEarning }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Pendapatan</p>
        </div>
        <div class="card-flat rounded-2xl p-5">
            <p class="text-2xl font-extrabold font-mono-data text-error">{{ $totalDeduction }}</p>
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1">Total Potongan</p>
        </div>
        <div class="card-flat rounded-2xl p-5 bg-surface-container/50">
            <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1 mb-2">Aksi Cepat</p>
            <button type="button" onclick="document.getElementById('nama-komponen-input').focus()" class="w-full bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2 rounded-lg flex items-center justify-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tambah Komponen
            </button>
        </div>
    </div>

    <!-- ADD COMPONENT FORM -->
    <div id="tambah-komponen" class="card-flat rounded-2xl p-6 mb-6 border border-primary/20 bg-primary/5">
        <h3 class="font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined">add_circle</span> Tambah Komponen Baru</h3>
        <form action="{{ route('hr.payroll.components.store') }}" method="POST" class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Nama Komponen</label>
                <input type="text" id="nama-komponen-input" name="name" required placeholder="Contoh: Tunjangan Makan" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
            </div>
            <div class="w-48">
                <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Tipe</label>
                <select name="type" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    <option value="earning">Pendapatan</option>
                    <option value="deduction">Potongan</option>
                </select>
            </div>
            <div class="w-48">
                <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Pajak</label>
                <select name="is_taxable" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    <option value="1">Kena Pajak</option>
                    <option value="0">Bebas Pajak</option>
                </select>
            </div>
            <div class="w-48">
                <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Nominal Default (Rp)</label>
                <input type="number" name="default_amount" placeholder="Opsional" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
            </div>
            <button type="submit" class="bg-primary hover:brightness-110 text-white font-bold px-5 py-2.5 rounded-lg transition shadow-md">Simpan</button>
        </form>
    </div>

    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-on-surface">Master Komponen Gaji</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Daftar komponen yang aktif di database.</p>
            </div>
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
                            <span class="text-[11px] font-bold px-2.5 py-1.5 rounded {{ $categoryColor[$c->type] ?? '' }}">
                                {{ $c->name }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $c->type == 'earning' ? 'Pendapatan' : 'Potongan' }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">-</td>
                        <td class="px-6 py-3.5 font-mono-data text-xs text-on-surface font-semibold">
                            Rp{{ number_format($c->default_amount ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3.5 text-on-surface-variant/60 text-xs">{{ $c->is_taxable ? 'Kena Pajak' : 'Bebas Pajak' }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <button type="button"
                                class="relative w-10 h-5.5 rounded-full transition bg-primary"
                                style="height:22px;">
                                <span class="absolute top-0.5 right-0.5 w-4 h-4 bg-white rounded-full shadow transition"></span>
                            </button>
                        </td>
                        <td class="px-6 py-3.5 text-center flex justify-center gap-2">
                            <!-- Edit Button -->
                            <button type="button" onclick="document.getElementById('edit-form-{{ $c->id }}').classList.toggle('hidden')" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition" title="Edit">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <!-- Delete Button -->
                            <form action="{{ route('hr.payroll.components.destroy', $c->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus komponen {{ $c->name }}?')" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-error hover:bg-error/10 transition" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit Form Row (Hidden by default) -->
                    <tr id="edit-form-{{ $c->id }}" class="hidden bg-primary/5">
                        <td colspan="7" class="px-6 py-4 border-b border-black/5 shadow-inner">
                            <form action="{{ route('hr.payroll.components.update', $c->id) }}" method="POST" class="flex items-end gap-4">
                                @csrf
                                @method('PUT')
                                <div class="flex-1">
                                    <label class="text-[10px] font-bold text-on-surface-variant/70 mb-1 block uppercase tracking-wider">Nama Komponen</label>
                                    <input type="text" name="name" value="{{ $c->name }}" required class="w-full bg-white border border-black/10 rounded px-2 py-1.5 text-xs focus:outline-none focus:border-primary">
                                </div>
                                <div class="w-32">
                                    <label class="text-[10px] font-bold text-on-surface-variant/70 mb-1 block uppercase tracking-wider">Tipe</label>
                                    <select name="type" class="w-full bg-white border border-black/10 rounded px-2 py-1.5 text-xs focus:outline-none focus:border-primary">
                                        <option value="earning" {{ $c->type == 'earning' ? 'selected' : '' }}>Pendapatan</option>
                                        <option value="deduction" {{ $c->type == 'deduction' ? 'selected' : '' }}>Potongan</option>
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="text-[10px] font-bold text-on-surface-variant/70 mb-1 block uppercase tracking-wider">Pajak</label>
                                    <select name="is_taxable" class="w-full bg-white border border-black/10 rounded px-2 py-1.5 text-xs focus:outline-none focus:border-primary">
                                        <option value="1" {{ $c->is_taxable ? 'selected' : '' }}>Kena Pajak</option>
                                        <option value="0" {{ !$c->is_taxable ? 'selected' : '' }}>Bebas Pajak</option>
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="text-[10px] font-bold text-on-surface-variant/70 mb-1 block uppercase tracking-wider">Nominal (Rp)</label>
                                    <input type="number" name="default_amount" value="{{ $c->default_amount }}" class="w-full bg-white border border-black/10 rounded px-2 py-1.5 text-xs focus:outline-none focus:border-primary">
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="document.getElementById('edit-form-{{ $c->id }}').classList.add('hidden')" class="px-4 py-2 rounded bg-black/5 hover:bg-black/10 text-xs font-bold transition">Batal</button>
                                    <button type="submit" class="bg-primary hover:brightness-110 text-white font-bold px-4 py-2 rounded text-xs transition shadow-sm">Simpan Perubahan</button>
                                </div>
                            </form>
                        </td>
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