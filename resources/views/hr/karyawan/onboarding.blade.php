@extends('layouts.hr')

@section('title', 'Onboarding Karyawan Baru')
@section('page-title', 'Onboarding Karyawan Baru')
@section('page-desc', 'Master data karyawan baru, generate NIP otomatis, dan enkripsi dokumen PII.')

@section('content')

    {{-- ── MODAL SUKSES (muncul setelah redirect dari controller) ──────────── --}}
    @if (session('success_data'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

            <div x-show="show" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                @click.outside="show = false" class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-xl">

                <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px]">check_circle</span>
                </div>

                <h3 class="text-lg font-bold text-on-surface mb-2">Karyawan Berhasil Ditambahkan</h3>
                <p class="text-sm text-on-surface-variant/70 mb-4">
                    <strong>{{ session('success_data')['name'] }}</strong> telah terdaftar dan akunnya sudah aktif.
                </p>

                <div class="bg-surface-container rounded-xl p-4 mb-2 text-left space-y-3">
                    <div>
                        <p class="text-[10px] text-on-surface-variant/60 uppercase font-bold tracking-wide mb-0.5">
                            NIP (Username Login)
                        </p>
                        <div class="flex items-center justify-between gap-2" x-data="{ copied: false }">
                            <p class="font-mono-data font-extrabold text-primary text-xl">
                                {{ session('success_data')['nip'] }}
                            </p>
                            <button type="button"
                                @click="navigator.clipboard.writeText('{{ session('success_data')['nip'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="w-8 h-8 rounded-lg flex items-center justify-center cursor-pointer
                                               bg-primary/10 hover:bg-primary hover:text-white text-primary transition"
                                :title="copied ? 'Tersalin!' : 'Salin NIP'">
                                <span class="material-symbols-outlined text-[16px]"
                                    x-text="copied ? 'check' : 'content_copy'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-black/5">
                        <p class="text-[10px] text-on-surface-variant/60 uppercase font-bold tracking-wide mb-0.5">
                            Password Awal
                        </p>
                        <p class="font-mono-data font-extrabold text-on-surface text-xl">
                            {{ session('success_data')['nip'] }}
                        </p>
                    </div>
                </div>

                <p class="text-[11px] text-on-surface-variant/50 mb-6">
                    Sampaikan NIP ini ke karyawan untuk login pertama kali. Password wajib diganti saat aktivasi akun.
                </p>

                <button @click="show = false" class="w-full py-3 rounded-lg bg-primary text-white text-sm font-bold
                                   hover:brightness-110 transition cursor-pointer">
                    Mengerti
                </button>
            </div>
        </div>
    @endif

    {{-- ── KONTEN UTAMA ─────────────────────────────────────────────────────── --}}
    <div x-data="{
                contractType: 'PKWT',
                autoNip: '{{ $predictedNip }}',
                expiryDate: '',
                h30Reminder: true,
                confirmOpen: false
            }">

        {{-- LINK KEMBALI --}}
        <a href="{{ route('hr.employees.index') }}"
            class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60 hover:text-primary transition -mt-2 mb-2">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Karyawan
        </a>

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm p-4 mb-4">
                <p class="font-bold mb-1">Periksa kembali isian berikut:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- BLUEPRINT BADGES CALLOUT --}}
        <div class="card-flat rounded-2xl p-4 mb-6 bg-gradient-to-r from-primary/5 via-white to-surface-container
                        border border-primary/20 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                </span>
                <div>
                    <p class="text-xs font-bold text-on-surface">Core HR Engine V1.0 · Blueprint 01 Onboarding</p>
                    <p class="text-[11px] text-on-surface-variant/60">
                        Generate NIP otomatis · Tracking masa berlaku kontrak H-30 · Enkripsi PII Data Protection
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">lock</span> PII Encrypted
                </span>
                <span
                    class="text-[10px] font-bold px-2.5 py-1 rounded bg-amber-500/10 text-amber-800 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">notifications_active</span> Auto H-30 Alert
                </span>
            </div>
        </div>

        {{-- INFO: NIP = PASSWORD AWAL LOGIN --}}
        <div
            class="rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs p-3.5 mb-6 flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[18px] text-blue-600">info</span>
            <p>NIP yang di-generate otomatis akan menjadi <strong>username sekaligus password awal</strong> untuk login
                karyawan di aplikasi mobile. Karyawan wajib mengganti/aktivasi akun (isi email &amp; password baru) saat
                login pertama kali.</p>
        </div>

        {{-- ── FORM ─────────────────────────────────────────────────────────── --}}
        <form x-ref="formOnboarding" method="POST" action="{{ route('hr.employees.storeWeb') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- 1. DATA PRIBADI & PII --}}
            <div class="card-flat rounded-2xl p-6">
                <div class="flex items-center justify-between gap-3 mb-6 pb-3 border-b border-black/5">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                        <div>
                            <h2 class="text-base font-bold text-on-surface">Data Identitas Pribadi</h2>
                            <p class="text-xs text-on-surface-variant/50">Data identitas dilindungi dengan enkripsi standar
                                GDPR &amp; PDP</p>
                        </div>
                    </div>
                    <span
                        class="text-[11px] font-bold px-2.5 py-0.5 rounded bg-surface-container text-on-surface-variant/60">
                        Wajib Lengkap
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Nama Lengkap
                        </label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}"
                            placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            NIK (KTP) — 16 Digit
                        </label>
                        <input type="text" name="nik" maxlength="16" required value="{{ old('nik') }}"
                            placeholder="3171xxxxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition font-mono-data">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="081234567890"
                            class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition font-mono-data">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            NPWP (15/16 Digit)
                        </label>
                        <input type="text" name="npwp" value="{{ old('npwp') }}" placeholder="Opsional jika ada" class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition font-mono-data">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            No. BPJS Kesehatan &amp; Ketenagakerjaan
                        </label>
                        <input type="text" name="bpjs_number" value="{{ old('bpjs_number') }}"
                            placeholder="Contoh: 000123456789" class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition font-mono-data">
                    </div>
                </div>
            </div>

            {{-- 2. DATA PEKERJAAN & STATUS KONTRAK --}}
            <div class="card-flat rounded-2xl p-6">
                <div class="flex items-center justify-between gap-3 mb-6 pb-3 border-b border-black/5">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                        <div>
                            <h2 class="text-base font-bold text-on-surface">Data Penempatan &amp; Status Kontrak</h2>
                            <p class="text-xs text-on-surface-variant/50">Pengaturan departemen, job grade, dan tracking
                                masa berlaku kontrak</p>
                        </div>
                    </div>

                    {{-- NIP CARD --}}
                    <div class="flex items-center gap-3 bg-primary/5 border border-primary/25 rounded-xl px-4 py-2.5"
                        x-data="{ copied: false }">
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant/50 mb-0.5">
                                NIP &amp; Password Awal
                            </p>
                            <p class="font-mono-data font-extrabold text-2xl text-primary leading-none" x-text="autoNip">
                                {{ $predictedNip }}</p>
                        </div>
                        <button type="button"
                            @click="navigator.clipboard.writeText(autoNip); copied = true; setTimeout(() => copied = false, 2000)"
                            class="w-9 h-9 rounded-lg flex items-center justify-center transition cursor-pointer
                                       bg-primary/10 hover:bg-primary hover:text-white text-primary"
                            :title="copied ? 'Tersalin!' : 'Salin NIP'">
                            <span class="material-symbols-outlined text-[18px]"
                                x-text="copied ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Departemen
                        </label>
                        <div class="relative">
                            <select name="department_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm
                                           border border-transparent hover:border-primary/20 focus:border-primary/40
                                           focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>Pilih Departemen
                                </option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2
                                             text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Posisi / Jabatan
                        </label>
                        <div class="relative">
                            <select name="position_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm
                                           border border-transparent hover:border-primary/20 focus:border-primary/40
                                           focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="" disabled {{ old('position_id') ? '' : 'selected' }}>Pilih Posisi</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                        {{ $pos->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2
                                             text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Tanggal Bergabung (Join Date)
                        </label>
                        <input type="date" name="join_date" required value="{{ old('join_date', now()->format('Y-m-d')) }}"
                            class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition cursor-pointer">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Tipe Kontrak Kerja
                        </label>
                        <div class="relative">
                            <select name="contract_type" x-model="contractType" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm
                                           border border-transparent hover:border-primary/20 focus:border-primary/40
                                           focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="PKWT">PKWT (Kontrak Waktu Tertentu)</option>
                                <option value="PKWTT">PKWTT (Karyawan Tetap)</option>
                                <option value="Probation">Probation (3 Bulan)</option>
                                <option value="Internship">Internship / Magang</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2
                                             text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div x-show="contractType !== 'PKWTT'">
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Tanggal Berakhir Kontrak
                        </label>
                        <input type="date" name="contract_end_date" x-model="expiryDate" class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition cursor-pointer">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">
                            Gaji Pokok Kesepakatan
                        </label>
                        <input type="number" name="basic_salary" required value="{{ old('basic_salary') }}"
                            placeholder="Contoh: 6500000" class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20
                                       focus:bg-white focus:outline-none transition font-mono-data">
                    </div>
                </div>

                {{-- H-30 REMINDER --}}
                <div x-show="contractType !== 'PKWTT'" class="mt-4 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-900
                               flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-700">alarm</span>
                        <span>Sistem akan mengirimkan notifikasi otomatis ke HR &amp; Atasan pada
                            <strong class="font-mono-data">H-30</strong> sebelum kontrak berakhir.</span>
                    </div>
                    <span class="font-bold text-amber-800 font-mono-data">Reminder Aktif</span>
                </div>
            </div>

            {{-- 3. UPLOAD DOKUMEN --}}
            <div class="card-flat rounded-2xl p-6">
                <div class="flex items-center justify-between gap-3 mb-6 pb-3 border-b border-black/5">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">3</span>
                        <div>
                            <h2 class="text-base font-bold text-on-surface">Upload Dokumen Pendukung</h2>
                            <p class="text-xs text-on-surface-variant/50">Upload berkas KTP untuk verifikasi identitas
                                karyawan</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-mono-data text-on-surface-variant/40">Maks. 5MB (PDF/JPG/PNG)</span>
                </div>

                <div x-data="{ fileName: '' }" class="max-w-sm">
                    <label class="border border-dashed border-black/20 rounded-xl p-4 text-center
                                      hover:border-primary/40 hover:bg-primary/5 transition cursor-pointer
                                      flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-[32px] text-primary/60 mb-2">upload_file</span>
                        <p class="font-bold text-xs text-on-surface" x-text="fileName || 'KTP Asli / e-KTP'"></p>
                        <p class="text-[10px] text-on-surface-variant/40 mt-0.5">Klik untuk pilih file</p>
                        <input type="file" name="ktp_file" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                            @change="fileName = $event.target.files[0]?.name ?? ''">
                    </label>
                </div>
            </div>

            {{-- FORM SUBMIT BAR --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('hr.employees.index') }}" class="px-5 py-3 rounded-lg border border-black/10 text-xs font-bold
                               text-on-surface-variant/70 hover:bg-surface-container transition cursor-pointer">
                    Batal
                </a>

                {{-- Tombol ini TIDAK submit form, hanya buka modal konfirmasi --}}
                {{-- SESUDAH — validasi form dulu sebelum buka modal --}}
                <button type="button" @click="
            if ($refs.formOnboarding.checkValidity()) {
                confirmOpen = true
            } else {
                $refs.formOnboarding.reportValidity()
            }
        " class="px-6 py-3 rounded-lg bg-primary text-white text-xs font-bold
               hover:brightness-110 shadow-sm flex items-center gap-2 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    Simpan &amp; Terbitkan NIP Karyawan
                </button>
            </div>

        </form>
        {{-- ── /FORM ── --}}


        {{-- ── MODAL KONFIRMASI SEBELUM SUBMIT ─────────────────────────────── --}}
        <div x-show="confirmOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

            <div x-show="confirmOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" @click.outside="confirmOpen = false"
                class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-xl">

                {{-- Ikon --}}
                <div class="w-16 h-16 rounded-full bg-amber-500/10 text-amber-600
                                flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px]">help</span>
                </div>

                <h3 class="text-lg font-bold text-on-surface mb-2">Konfirmasi Penyimpanan</h3>
                <p class="text-sm text-on-surface-variant/70 mb-1">
                    Apakah Anda yakin ingin menyimpan data karyawan ini?
                </p>
                <p class="text-xs text-on-surface-variant/50 mb-2">
                    NIP <span class="font-mono-data font-bold text-primary" x-text="autoNip"></span>
                    akan diterbitkan dan akun karyawan langsung aktif.
                </p>
                <p class="text-[11px] text-on-surface-variant/40 mb-6">
                    Pastikan semua data sudah benar sebelum melanjutkan.
                </p>

                {{-- Tombol aksi --}}
                <div class="flex gap-3">
                    <button type="button" @click="confirmOpen = false" class="flex-1 py-3 rounded-lg border border-black/10 text-sm font-bold
                                   text-on-surface-variant/70 hover:bg-surface-container transition cursor-pointer">
                        Tidak, Batal
                    </button>
                    <button type="button" @click="confirmOpen = false; $nextTick(() => $refs.formOnboarding.submit())"
                        class="flex-1 py-3 rounded-lg bg-primary text-white text-sm font-bold
                                   hover:brightness-110 transition cursor-pointer flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>
        {{-- ── /MODAL KONFIRMASI ── --}}

    </div>{{-- ── /x-data utama ── --}}

@endsection