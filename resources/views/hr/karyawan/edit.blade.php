@extends('layouts.hr')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')
@section('page-desc', 'Perbarui data pribadi, pekerjaan, rekening bank, komponen gaji, dan dokumen karyawan.')


@section('content')

    {{-- LINK KEMBALI --}}
    <a href="{{ route('hr.employees.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-on-surface-variant/60
              hover:text-primary transition -mt-2 mb-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>

    {{-- IDENTITAS RINGKAS --}}
    <div class="card-flat rounded-2xl p-6 flex items-center gap-4">
        <img src="https://i.pravatar.cc/56?u={{ $employee->id }}" class="w-14 h-14 rounded-full object-cover" alt="{{ $employee->full_name }}">
        <div class="flex-1">
            <p class="text-base font-bold text-on-surface">{{ $employee->full_name }}</p>
            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $employee->employee_id }} · {{ $employee->department->name ?? 'Tanpa Departemen' }}</p>
        </div>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $employee->employment_status === 'PKWTT' ? 'bg-primary/10 text-primary' : 'bg-amber-500/10 text-amber-700' }}">
            {{ $employee->employment_status }}
        </span>
    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('hr.employees.updateWeb', $employee->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ERROR VALIDASI JIKA ADA --}}
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

        {{-- 1. DATA PRIBADI --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Pribadi</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $employee->full_name) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NIK (KTP)</label>
                    <input type="text" name="nik" maxlength="16" required value="{{ old('nik', $employee->nik) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required value="{{ old('email', $employee->email) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="phone" required value="{{ old('phone', $employee->phone) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $employee->npwp) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. BPJS Kesehatan / TK</label>
                    <input type="text" name="bpjs_number" value="{{ old('bpjs_number', $employee->bpjs_number) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>
        </div>

        {{-- 2. DATA PEKERJAAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                <h2 class="text-base font-bold text-on-surface">Data Pekerjaan</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NIP</label>
                    <input type="text" value="{{ $employee->employee_id }}" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Departemen</label>
                    <div class="relative mt-1.5">
                        <select name="department_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
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
                            @foreach ($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                {{-- DROPDOWN ATASAN (SUPERVISOR) --}}
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Atasan Langsung (Supervisor)</label>
                    <div class="relative mt-1.5">
                        <select name="supervisor_id"
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="">-- Tidak ada (Level Tertinggi) --</option>
                            @foreach ($supervisors as $spv)
                                <option value="{{ $spv->id }}" {{ old('supervisor_id', $employee->supervisor_id) == $spv->id ? 'selected' : '' }}>
                                    {{ $spv->name }} — Supervisor
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Tanggal Bergabung</label>
                    <input type="date" name="join_date" required value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Tipe Kontrak</label>
                    <div class="relative mt-1.5">
                        <select name="employment_status" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="PKWT" {{ old('employment_status', $employee->employment_status) === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak)</option>
                            <option value="PKWTT" {{ old('employment_status', $employee->employment_status) === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                            <option value="Probation" {{ old('employment_status', $employee->employment_status) === 'Probation' ? 'selected' : '' }}>Probation (3 Bulan)</option>
                            <option value="Internship" {{ old('employment_status', $employee->employment_status) === 'Internship' ? 'selected' : '' }}>Internship / Magang</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Gaji Pokok (Rp)</label>
                    <input type="number" name="basic_salary" required value="{{ old('basic_salary', $employee->basic_salary) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>
        </div>

        {{-- 3. STATUS KEPEGAWAIAN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">3</span>
                <h2 class="text-base font-bold text-on-surface">Status Kepegawaian</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Status Saat Ini</label>
                    <div class="relative mt-1.5">
                        <select name="status" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="resigned" {{ old('status', $employee->status) === 'resigned' ? 'selected' : '' }}>Resigned (Keluar)</option>
                            <option value="pending" {{ old('status', $employee->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="col-span-2 flex items-end">
                    <p class="text-xs text-on-surface-variant/40">Mengubah status ke <span class="font-bold text-error">Non-Aktif</span> atau <span class="font-bold text-error">Resigned</span> akan menghentikan akses karyawan ke sistem presensi & payroll.</p>
                </div>
            </div>
        </div>

        {{-- 4. DATA REKENING BANK --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">4</span>
                <h2 class="text-base font-bold text-on-surface">Data Rekening Bank</h2>
            </div>
            <p class="text-xs text-on-surface-variant/40 mb-6 ml-11">
                Dipakai untuk transfer gaji & export mass transfer bank oleh Finance.
                Pilihan bank dibatasi agar konsisten dan tidak salah kelompok saat export CSV.
            </p>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Bank</label>
                    <div class="relative mt-1.5">
                        @php $currentBank = old('bank_name', $employee->bank_name); @endphp
                        <select name="bank_name"
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="" {{ !$currentBank ? 'selected' : '' }}>-- Pilih Bank --</option>
                            <option value="BCA" {{ $currentBank === 'BCA' ? 'selected' : '' }}>BCA</option>
                            <option value="MANDIRI" {{ $currentBank === 'MANDIRI' ? 'selected' : '' }}>Mandiri</option>
                            <option value="BNI" {{ $currentBank === 'BNI' ? 'selected' : '' }}>BNI</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $employee->bank_account_holder) }}"
                           placeholder="Sesuai buku tabungan"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
        </div>

        {{-- 6. DOKUMEN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">6</span>
                <h2 class="text-base font-bold text-on-surface">Dokumen</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                @php
                    $documents = [
                        ['name' => 'ktp_file', 'label' => 'Scan KTP', 'uploaded' => !empty($employee->ktp_file_path)],
                        ['name' => 'npwp_file', 'label' => 'Scan NPWP', 'uploaded' => !empty($employee->npwp)],
                        ['name' => 'bpjs_file', 'label' => 'Kartu BPJS', 'uploaded' => !empty($employee->bpjs_number)]
                    ];
                @endphp
                @foreach ($documents as $doc)
                    <label class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition group
                                  {{ $doc['uploaded'] ? 'border-primary/30 bg-primary/5' : 'border-black/10 hover:border-primary/40 hover:bg-primary/5' }}">
                        <input type="file" name="{{ $doc['name'] }}" class="hidden">
                        @if ($doc['uploaded'])
                            <span class="material-symbols-outlined text-primary text-[32px]">task</span>
                            <p class="text-sm font-bold text-primary mt-2">{{ $doc['label'] }}</p>
                            <p class="text-[11px] text-on-surface-variant/40 mt-0.5">Terisi / Terunggah · klik untuk ganti</p>
                        @else
                            <span class="material-symbols-outlined text-on-surface-variant/30 group-hover:text-primary text-[32px] transition">upload_file</span>
                            <p class="text-sm font-bold text-on-surface mt-2 group-hover:text-primary transition">{{ $doc['label'] }}</p>
                            <p class="text-[11px] text-on-surface-variant/40 mt-0.5">Belum diisi · Klik untuk upload</p>
                        @endif
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
                Simpan Perubahan
            </button>
        </div>
    </form>

@endsection