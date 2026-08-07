@extends('layouts.hr')

@section('title', 'Pengaturan HR')
@section('page-title', 'Pengaturan')
@section('page-desc', 'Konfigurasi kebijakan operasional HR: cuti, lembur, presensi, dan jenis izin.')

@php
    $leaveTypes = [
        ['name' => 'Cuti Tahunan', 'attachment' => false, 'quota' => '12 hari/tahun'],
        ['name' => 'Sakit', 'attachment' => true, 'quota' => 'Sesuai kebutuhan (>1 hari wajib surat)'],
        ['name' => 'Izin Pribadi', 'attachment' => false, 'quota' => 'Maks. 3 hari/tahun'],
        ['name' => 'Cuti Melahirkan', 'attachment' => true, 'quota' => '3 bulan'],
    ];
@endphp

@section('content')

    <form method="POST" action="{{ route('hr.settings.index') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- KEBIJAKAN CUTI --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">event_available</span>
                </span>
                <h2 class="text-base font-bold text-on-surface">Kebijakan Cuti</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Kuota Cuti Tahunan Default</label>
                    <div class="relative mt-1.5">
                        <input type="number" value="12"
                               class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                      hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant/40">hari/tahun</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Min. Hari per Pengajuan</label>
                    <input type="number" value="1"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Maks. Hari per Pengajuan</label>
                    <input type="number" value="12"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-between mt-5 pt-5 border-t border-black/5">
                <div>
                    <p class="text-sm font-bold text-on-surface">Carry-forward Kuota Akhir Tahun</p>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Sisa kuota cuti tahun berjalan otomatis dibawa ke tahun berikutnya.</p>
                </div>
                <button type="button" class="relative w-11 h-6 rounded-full bg-primary transition">
                    <span class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition"></span>
                </button>
            </div>
        </div>

        {{-- KEBIJAKAN LEMBUR --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                </span>
                <h2 class="text-base font-bold text-on-surface">Kebijakan Lembur</h2>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Rumus Perhitungan (Default Depnaker)</label>
                    <input type="text" value="1/173 × Gaji Pokok × Jam" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm font-mono-data text-on-surface-variant/50 cursor-not-allowed">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Batas Maks. Jam Lembur / Bulan</label>
                    <input type="number" value="40"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
        </div>

        {{-- KEBIJAKAN PRESENSI --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">fingerprint</span>
                </span>
                <h2 class="text-base font-bold text-on-surface">Kebijakan Presensi</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Toleransi Keterlambatan</label>
                    <div class="relative mt-1.5">
                        <input type="number" value="15"
                               class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                      hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant/40">menit</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Jam Kerja Standar</label>
                    <input type="text" value="08:00 - 17:00"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Hari Libur Nasional/Cuti Bersama</label>
                    <button type="button" class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm text-left text-on-surface-variant/70 hover:bg-primary/5 transition flex items-center justify-between">
                        Kelola daftar hari libur
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- JENIS CUTI / IZIN --}}
        <div class="card-flat rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-on-surface">Jenis Izin / Cuti</h2>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Kelola kategori cuti/izin beserta kebutuhan lampiran.</p>
                </div>
                <button type="button" class="bg-primary hover:brightness-110 text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Jenis
                </button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Kuota / Ketentuan</th>
                        <th class="px-6 py-3">Wajib Lampiran</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($leaveTypes as $lt)
                        <tr class="hover:bg-primary/5 transition">
                            <td class="px-6 py-3.5 font-bold text-on-surface">{{ $lt['name'] }}</td>
                            <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">{{ $lt['quota'] }}</td>
                            <td class="px-6 py-3.5">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $lt['attachment'] ? 'bg-amber-500/10 text-amber-700' : 'bg-surface-container text-on-surface-variant/50' }}">
                                    {{ $lt['attachment'] ? 'Ya' : 'Tidak' }}
                                </span>
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

        <div class="flex justify-end gap-3">
            <button type="reset" class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant/70 hover:bg-primary/5 hover:text-primary transition">
                Batal
            </button>
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Pengaturan
            </button>
        </div>
    </form>

@endsection
