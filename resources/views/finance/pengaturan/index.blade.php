@extends('layouts.finance')

@section('title', 'Pengaturan Finance')
@section('page-title', 'Pengaturan')
@section('page-desc', 'Konfigurasi parameter keuangan, format bank, dan pajak untuk proses payroll.')

@php
    $bankFormats = [
        ['bank' => 'BCA', 'format' => 'CSV', 'columns' => 'no_rekening, nama_penerima, nominal, berita'],
        ['bank' => 'Mandiri', 'format' => 'CSV', 'columns' => 'account_no, name, amount, reference'],
        ['bank' => 'BNI', 'format' => 'CSV', 'columns' => 'rekening, nama, jumlah, keterangan'],
    ];
@endphp

@section('content')

    <div class="space-y-8">


        <div class="bg-white rounded-md border border-gray-100 shadow-sm p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center">
                        </div>
                    <h2 class="text-lg font-medium text-gray-800">Rekening &amp; Profil Bank Perusahaan</h2>
                </div>
                <a href="{{ route('admin.company.index') }}" class="text-xs font-medium text-gray-500 hover:text-[#0B3D2E] hover:bg-gray-50 px-4 py-2 rounded-md transition">
                    Dikelola di Profil Perusahaan <span class="material-symbols-outlined text-[14px] align-middle ml-1">arrow_forward</span>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-2">Nama Bank</p>
                    <p class="text-sm font-medium text-gray-800">Bank Central Asia (BCA)</p>
                </div>
                <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-2">No. Rekening</p>
                    <p class="text-sm font-medium text-gray-800 ">206-0891-234</p>
                </div>
                <div class="bg-gray-50 p-5 rounded-md border border-gray-100">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-widest mb-2">Atas Nama</p>
                    <p class="text-sm font-medium text-gray-800">PT Talenta Digital Nusantara</p>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h2 class="text-lg font-medium text-gray-800">Format Bank untuk Export</h2>
                    <p class="text-xs text-gray-500 mt-1">Template kolom CSV per bank tujuan transfer.</p>
                </div>
                <button type="button" class="bg-[#0B3D2E] hover:bg-[#043927] shadow-sm text-white text-xs font-medium px-5 py-2.5 rounded-md flex items-center gap-2 transition">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Format
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                            <th class="px-8 py-4">Bank</th>
                            <th class="px-6 py-4">Format File</th>
                            <th class="px-6 py-4">Template Kolom</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($bankFormats as $b)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-8 py-4 font-medium text-gray-800">{{ $b['bank'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-[#0B3D2E] border border-gray-200">{{ $b['format'] }}</span>
                                </td>
                                <td class="px-6 py-4  text-xs text-gray-500">{{ $b['columns'] }}</td>
                                <td class="px-8 py-4 text-center">
                                    <button type="button" class="p-2 rounded-md text-gray-400 hover:text-[#0B3D2E] hover:bg-gray-50 transition shadow-sm border border-transparent hover:border-gray-200">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div class="bg-white rounded-md border border-gray-100 shadow-sm p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center">
                    </div>
                <h2 class="text-lg font-medium text-gray-800">Parameter Pajak &amp; BPJS</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-widest block mb-2">Skema PPh21</label>
                    <input type="text" value="Tarif Efektif Rata-rata (TER)" disabled
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-widest block mb-2">BPJS Kesehatan (Perusahaan)</label>
                    <div class="relative">
                        <input type="number" step="0.1" value="4.0"
                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-md text-sm  focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-widest block mb-2">BPJS Ketenagakerjaan (Perusahaan)</label>
                    <div class="relative">
                        <input type="number" step="0.1" value="3.7"
                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-md text-sm  focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md flex items-center gap-3">
                <span class="material-symbols-outlined text-[#0B3D2E] text-[20px]">info</span>
                <p class="text-xs text-emerald-800">Tabel tarif PPh21 TER dan batas PTKP mengikuti regulasi terbaru — dikonfigurasi oleh Payroll Engine di sisi backend.</p>
            </div>
        </div>


        <div class="bg-white rounded-md border border-gray-100 shadow-sm p-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px] text-gray-700">admin_panel_settings</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Approval Payroll 2 Tahap (HR + Finance)</p>
                    <p class="text-xs text-gray-500 mt-0.5">Dikendalikan Super Admin di Modul Finance — ditampilkan di sini sebagai referensi (read-only).</p>
                </div>
            </div>
            <span class="text-[11px] font-medium px-3 py-1.5 rounded-md bg-gray-50 text-gray-700 border border-gray-200 shadow-sm">Aktif</span>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <button type="reset" class="px-6 py-2.5 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                Batal
            </button>
            <button type="submit" class="bg-[#0B3D2E] text-white px-6 py-2.5 rounded-md text-sm font-medium hover:bg-[#043927] shadow-sm transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Pengaturan
            </button>
        </div>
    </div>

@endsection
