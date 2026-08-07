@extends('layouts.admin')

@section('title', 'Profil Perusahaan')
@section('page-title', 'Profil Perusahaan')
@section('page-desc', 'Kelola data identitas perusahaan Anda')

@php
    $company = [
        'name' => 'PT Talenta Digital Nusantara',
        'npwp' => '01.234.567.8-901.000',
        'address' => 'Jl. Jenderal Sudirman No. 88, Kebayoran Baru',
        'city' => 'Jakarta Selatan, DKI Jakarta',
        'pic' => 'Andi Wijaya',
        'lat' => '-6.224227',
        'lng' => '106.802544',
        'bank_name' => 'Bank Central Asia (BCA)',
        'bank_account' => '206-0891-234',
        'bank_holder' => 'PT Talenta Digital Nusantara',
    ];
    $stats = [
        ['label' => 'Total Karyawan Terdaftar', 'value' => '1.284', 'icon' => 'groups'],
        ['label' => 'Total Departemen', 'value' => '4', 'icon' => 'account_tree'],
        ['label' => 'Status Perusahaan', 'value' => 'Aktif', 'icon' => 'verified'],
    ];
@endphp

@section('content')

    {{-- STAT ROW --}}
    <div class="grid grid-cols-3 gap-5">
        @foreach ($stats as $s)
            <div class="card-flat rounded-2xl p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[22px]">{{ $s['icon'] }}</span>
                </div>
                <div>
                    <p class="text-xl font-extrabold font-mono-data text-on-surface leading-none">{{ $s['value'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant/50 uppercase tracking-wide mt-1.5">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.company.index') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- IDENTITAS PERUSAHAAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Identitas Perusahaan</h2>
            </div>

            <div class="flex items-center gap-5 mb-6 pb-6 border-b border-black/5">
                <div class="w-20 h-20 rounded-2xl bg-surface-container border border-black/5 flex items-center justify-center overflow-hidden shrink-0">
                    <span class="material-symbols-outlined text-on-surface-variant/30 text-[32px]">domain</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-on-surface mb-1">Logo Perusahaan</p>
                    <div class="flex items-center gap-2">
                        <label class="cursor-pointer text-xs font-bold text-primary border border-primary/30 rounded-lg px-3 py-2 hover:bg-primary/5 transition flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">upload</span>
                            Ubah Logo
                            <input type="file" name="logo" class="hidden">
                        </label>
                        <p class="text-[11px] text-on-surface-variant/40">PNG/JPG, maks 2MB</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Perusahaan</label>
                    <input type="text" name="name" required value="{{ $company['name'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NPWP Perusahaan</label>
                    <input type="text" name="npwp" required value="{{ $company['npwp'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">PIC / Penanggung Jawab</label>
                    <div class="relative mt-1.5">
                        <select name="pic" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Andi Wijaya', 'Rina Kartika'] as $p)
                                <option {{ $company['pic'] === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                    <p class="text-[11px] text-on-surface-variant/40 mt-1">Dipilih dari daftar pengguna Super Admin/HR.</p>
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Alamat Lengkap Kantor</label>
                    <textarea name="address" required rows="2"
                              class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                     hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition resize-none">{{ $company['address'] }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Kota / Wilayah</label>
                    <input type="text" name="city" required value="{{ $company['city'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
        </div>

        {{-- KOORDINAT LOKASI KANTOR --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                <h2 class="text-base font-bold text-on-surface">Koordinat Lokasi Kantor</h2>
            </div>
            <p class="text-xs text-on-surface-variant/50 mb-5 ml-11">Titik pusat radius geofencing presensi yang dipakai di Shift &amp; Roster Kerja (HR).</p>

            <div class="grid grid-cols-3 gap-5 items-end">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Latitude</label>
                    <input type="text" name="lat" required value="{{ $company['lat'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Longitude</label>
                    <input type="text" name="lng" required value="{{ $company['lng'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <button type="button" class="px-4 py-2.5 rounded-lg text-sm font-bold border border-black/10 text-on-surface-variant/70 hover:bg-surface-container transition flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">pin_drop</span>
                    Pilih di Peta
                </button>
            </div>

            <div class="mt-5 rounded-xl overflow-hidden border border-black/5 h-48 bg-surface-container flex items-center justify-center relative">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 50% 50%, rgba(11,61,46,0.12) 0%, transparent 60%);"></div>
                <div class="text-center relative">
                    <span class="material-symbols-outlined text-primary text-[36px]">location_on</span>
                    <p class="text-xs font-bold text-on-surface-variant/60 mt-1">Map picker — titik pusat geofencing kantor</p>
                </div>
            </div>
        </div>

        {{-- REKENING BANK PERUSAHAAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">3</span>
                <h2 class="text-base font-bold text-on-surface">Rekening Bank Perusahaan</h2>
            </div>
            <p class="text-xs text-on-surface-variant/50 mb-5 ml-11">Dasar header file export transfer — referensi bersama dengan Pengaturan Finance.</p>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Bank</label>
                    <input type="text" name="bank_name" required value="{{ $company['bank_name'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Rekening</label>
                    <input type="text" name="bank_account" required value="{{ $company['bank_account'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Atas Nama</label>
                    <input type="text" name="bank_holder" required value="{{ $company['bank_holder'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="reset"
                    class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant/70
                           hover:bg-primary/5 hover:text-primary transition">
                Batal
            </button>
            <button type="submit"
                    class="bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Perubahan Profil
            </button>
        </div>
    </form>

@endsection
