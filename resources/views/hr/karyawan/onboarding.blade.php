@extends('layouts.hr')

@section('title', 'Onboarding Karyawan Baru')
@section('page-title', 'Onboarding Karyawan Baru')
@section('page-desc', 'Master data karyawan baru, generate NIP otomatis, dan enkripsi dokumen PII.')

@section('content')
<div x-data="{
    contractType: 'PKWT',
    autoNip: 'EMP-01285',
    expiryDate: '2027-08-08',
    h30Reminder: true
}">

    {{-- LINK KEMBALI --}}
    <a href="{{ route('hr.employees.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60 hover:text-primary transition -mt-2 mb-2">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>

    {{-- BLUEPRINT BADGES CALLOUT --}}
    <div class="card-flat rounded-2xl p-4 mb-6 bg-gradient-to-r from-primary/5 via-white to-surface-container border border-primary/20 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold shadow-sm">
                <span class="material-symbols-outlined text-[20px]">badge</span>
            </span>
            <div>
                <p class="text-xs font-bold text-on-surface">Core HR Engine V1.0 · Blueprint 01 Onboarding</p>
                <p class="text-[11px] text-on-surface-variant/60">Generate NIP otomatis · Tracking masa berlaku kontrak H-30 · Enkripsi PII Data Protection</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold px-2.5 py-1 rounded bg-primary/10 text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-[13px]">lock</span> PII Encrypted
            </span>
            <span class="text-[10px] font-bold px-2.5 py-1 rounded bg-amber-500/10 text-amber-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[13px]">notifications_active</span> Auto H-30 Alert
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('hr.employees.index') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- 1. DATA PRIBADI & PII --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-6 pb-3 border-b border-black/5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Data Identitas Pribadi</h2>
                        <p class="text-xs text-on-surface-variant/50">Data identitas dilindungi dengan enkripsi standar GDPR &amp; PDP</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded bg-surface-container text-on-surface-variant/60">Wajib Lengkap</span>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" required placeholder="Contoh: Budi Santoso"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">NIK (KTP) — 16 Digit</label>
                    <input type="text" name="nik" maxlength="16" required placeholder="3171xxxxxxxxxxxx"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Email Perusahaan</label>
                    <input type="email" name="email" required placeholder="budi.santoso@talentahr.co.id"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" required placeholder="081234567890"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">NPWP (15/16 Digit)</label>
                    <input type="text" name="npwp" placeholder="Opsional jika ada"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">No. BPJS Kesehatan &amp; Ketenagakerjaan</label>
                    <input type="text" name="bpjs_number" placeholder="Contoh: 000123456789"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>
        </div>

        {{-- 2. DATA PEKERJAAN & STATUS KONTRAK --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-6 pb-3 border-b border-black/5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Data Penempatan &amp; Status Kontrak</h2>
                        <p class="text-xs text-on-surface-variant/50">Pengaturan departemen, job grade, dan tracking masa berlaku kontrak</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-on-surface-variant/60 font-bold">Generated NIP:</span>
                    <span class="font-mono-data font-extrabold text-sm text-primary px-2.5 py-0.5 rounded bg-primary/10" x-text="autoNip">EMP-01285</span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Departemen</label>
                    <div class="relative">
                        <select name="department_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option>Sales &amp; Marketing</option>
                            <option>Finance &amp; Accounting</option>
                            <option>Human Resources</option>
                            <option>Engineering &amp; IT</option>
                            <option>Front Office</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Posisi / Jabatan</label>
                    <div class="relative">
                        <select name="position_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option>Staff (JG-1)</option>
                            <option>Supervisor (JG-2)</option>
                            <option>Manager (JG-3)</option>
                            <option>Director (JG-4)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Tanggal Bergabung (Join Date)</label>
                    <input type="date" name="join_date" required value="2026-08-08"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Tipe Kontrak Kerja</label>
                    <div class="relative">
                        <select name="contract_type" x-model="contractType" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="PKWT">PKWT (Kontrak Waktu Tertentu)</option>
                            <option value="PKWTT">PKWTT (Karyawan Tetap)</option>
                            <option value="Probation">Probation (3 Bulan)</option>
                            <option value="Internship">Internship / Magang</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div x-show="contractType !== 'PKWTT'">
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Tanggal Berakhir Kontrak</label>
                    <input type="date" name="contract_end_date" x-model="expiryDate"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/70 uppercase tracking-wide block mb-1">Gaji Pokok Kesepakatan</label>
                    <input type="number" name="basic_salary" required placeholder="Contoh: 6500000"
                           class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>

            {{-- H-30 REMINDER ALERT BOX --}}
            <div x-show="contractType !== 'PKWTT'" class="mt-4 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-900 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-700">alarm</span>
                    <span>Sistem akan mengirimkan notifikasi otomatis ke HR &amp; Atasan pada <strong class="font-mono-data">H-30</strong> sebelum kontrak berakhir.</span>
                </div>
                <span class="font-bold text-amber-800 font-mono-data">Reminder Aktif</span>
            </div>
        </div>

        {{-- 3. UPLOAD DOKUMEN (KTP, NPWP, BPJS, REKENING BANK) --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-6 pb-3 border-b border-black/5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">3</span>
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Upload Dokumen Pendukung</h2>
                        <p class="text-xs text-on-surface-variant/50">Upload berkas legalitas untuk verifikasi BPJS &amp; transfer gaji bank</p>
                    </div>
                </div>
                <span class="text-[11px] font-mono-data text-on-surface-variant/40">Maks. 5MB per file (PDF/JPG/PNG)</span>
            </div>

            <div class="grid grid-cols-4 gap-4">
                @foreach (['KTP Asli / e-KTP', 'NPWP Pajak', 'Kartu BPJS Kesehatan', 'Buku Tabungan / Rekening'] as $doc)
                    <div class="border border-dashed border-black/20 rounded-xl p-4 text-center hover:border-primary/40 hover:bg-primary/5 transition cursor-pointer flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-[32px] text-primary/60 mb-2">upload_file</span>
                        <p class="font-bold text-xs text-on-surface">{{ $doc }}</p>
                        <p class="text-[10px] text-on-surface-variant/40 mt-0.5">Klik untuk pilih file</p>
                        <input type="file" class="hidden">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- FORM SUBMIT BAR --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('hr.employees.index') }}"
               class="px-5 py-3 rounded-lg border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-surface-container transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 rounded-lg bg-primary text-white text-xs font-bold hover:brightness-110 shadow-sm flex items-center gap-2 transition">
                <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                Simpan &amp; Terbitkan NIP Karyawan
            </button>
        </div>
    </form>
</div>
@endsection