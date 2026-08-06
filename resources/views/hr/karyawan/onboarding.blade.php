@extends('layouts.hr')

@section('title', 'Onboarding Karyawan Baru')
@section('page-title', 'Onboarding Karyawan Baru')
@section('page-desc', 'Form pendaftaran & kelengkapan dokumen karyawan baru.')

@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('hr.employees.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>

    <form method="POST" action="{{ route('hr.employees.index') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- DATA PRIBADI --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Pribadi</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required placeholder="Sesuai KTP"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NIK (KTP)</label>
                    <input type="text" name="nik" maxlength="16" required placeholder="16 digit"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required placeholder="nama@talentahr.co.id"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="phone" required
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NPWP</label>
                    <input type="text" name="npwp" placeholder="Opsional"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. BPJS Kesehatan / TK</label>
                    <input type="text" name="bpjs_number"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>
        </div>

        {{-- DATA PEKERJAAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                <h2 class="text-base font-bold text-on-surface">Data Pekerjaan</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NIP</label>
                    <input type="text" value="Otomatis oleh sistem" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Departemen</label>
                    <div class="relative mt-1.5">
                        <select name="department_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option>Sales</option>
                            <option>Finance</option>
                            <option>Engineering</option>
                            <option>Front Office</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Posisi / Jabatan</label>
                    <div class="relative mt-1.5">
                        <select name="position_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option>Staff</option>
                            <option>Supervisor</option>
                            <option>Manager</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Tanggal Bergabung</label>
                    <input type="date" name="join_date" required
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Tipe Kontrak</label>
                    <div class="relative mt-1.5">
                        <select name="contract_type" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="PKWT">PKWT (Kontrak)</option>
                            <option value="PKWTT">PKWTT (Tetap)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Gaji Pokok (Rp)</label>
                    <input type="number" name="basic_salary" required placeholder="Dasar hitung BPJS & lembur"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>
        </div>

        {{-- UPLOAD DOKUMEN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">3</span>
                <h2 class="text-base font-bold text-on-surface">Upload Dokumen</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                @foreach ([['name' => 'ktp_file', 'label' => 'Scan KTP'], ['name' => 'npwp_file', 'label' => 'Scan NPWP'], ['name' => 'bpjs_file', 'label' => 'Kartu BPJS']] as $doc)
                    <label class="border-2 border-dashed border-black/10 rounded-xl p-6 text-center cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition group">
                        <input type="file" name="{{ $doc['name'] }}" class="hidden">
                        <span class="material-symbols-outlined text-on-surface-variant/30 group-hover:text-primary text-[32px] transition">upload_file</span>
                        <p class="text-sm font-bold text-on-surface mt-2 group-hover:text-primary transition">{{ $doc['label'] }}</p>
                        <p class="text-[11px] text-on-surface-variant/40 mt-0.5">PDF/JPG, maks 2MB</p>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('hr.employees.index') }}"
               class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant/70
                      hover:bg-primary/5 hover:text-primary transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan &amp; Onboard Karyawan
            </button>
        </div>
    </form>

@endsection