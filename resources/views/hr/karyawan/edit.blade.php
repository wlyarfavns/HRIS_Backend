@extends('layouts.hr')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')
@section('page-desc', 'Perbarui data pribadi, pekerjaan, dan dokumen karyawan.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $employee = [
        'nip' => 'EMP-00812',
        'full_name' => 'Jim Halpert',
        'nik' => '3374012345670003',
        'email' => 'jim.halpert@talentahr.co.id',
        'phone' => '081234567890',
        'npwp' => '09.876.543.2-123.000',
        'bpjs_number' => '0001234567890',
        'department' => 'Sales',
        'position' => 'Staff',
        'join_date' => '2024-08-18',
        'contract_type' => 'PKWT',
        'basic_salary' => 6500000,
        'status' => 'Aktif',
        'avatar' => 12,
    ];
@endphp

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
        <img src="https://i.pravatar.cc/56?img={{ $employee['avatar'] }}" class="w-14 h-14 rounded-full object-cover" alt="{{ $employee['full_name'] }}">
        <div class="flex-1">
            <p class="text-base font-bold text-on-surface">{{ $employee['full_name'] }}</p>
            <p class="text-xs text-on-surface-variant/50 font-mono-data mt-0.5">{{ $employee['nip'] }} · {{ $employee['department'] }}</p>
        </div>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $employee['contract_type'] === 'PKWTT' ? 'bg-primary/10 text-primary' : 'bg-amber-500/10 text-amber-700' }}">
            {{ $employee['contract_type'] }}
        </span>
    </div>

    <form method="POST" action="{{ route('hr.employees.updateWeb', $employee['nip']) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- DATA PRIBADI --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                <h2 class="text-base font-bold text-on-surface">Data Pribadi</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="{{ $employee['full_name'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NIK (KTP)</label>
                    <input type="text" name="nik" maxlength="16" required value="{{ $employee['nik'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" required value="{{ $employee['email'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="phone" required value="{{ $employee['phone'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">NPWP</label>
                    <input type="text" name="npwp" value="{{ $employee['npwp'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">No. BPJS Kesehatan / TK</label>
                    <input type="text" name="bpjs_number" value="{{ $employee['bpjs_number'] }}"
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
                    <input type="text" value="{{ $employee['nip'] }}" disabled
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container/60 rounded-lg text-sm text-on-surface-variant/40 font-mono-data cursor-not-allowed">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Departemen</label>
                    <div class="relative mt-1.5">
                        <select name="department_id" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach (['Sales', 'Finance', 'Engineering', 'Front Office'] as $dept)
                                <option {{ $employee['department'] === $dept ? 'selected' : '' }}>{{ $dept }}</option>
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
                            @foreach (['Staff', 'Supervisor', 'Manager'] as $pos)
                                <option {{ $employee['position'] === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Tanggal Bergabung</label>
                    <input type="date" name="join_date" required value="{{ $employee['join_date'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Tipe Kontrak</label>
                    <div class="relative mt-1.5">
                        <select name="contract_type" required
                                class="appearance-none w-full pl-3.5 pr-9 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                       hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="PKWT" {{ $employee['contract_type'] === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak)</option>
                            <option value="PKWTT" {{ $employee['contract_type'] === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Gaji Pokok (Rp)</label>
                    <input type="number" name="basic_salary" required value="{{ $employee['basic_salary'] }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition font-mono-data">
                </div>
            </div>
        </div>

        {{-- STATUS KEPEGAWAIAN --}}
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
                            @foreach (['Aktif', 'Cuti Panjang', 'Non-Aktif'] as $st)
                                <option {{ $employee['status'] === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[18px] pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div class="col-span-2 flex items-end">
                    <p class="text-xs text-on-surface-variant/40">Mengubah status ke <span class="font-bold text-error">Non-Aktif</span> akan menghentikan akses karyawan ke sistem presensi & payroll.</p>
                </div>
            </div>
        </div>

        {{-- DOKUMEN --}}
        <div class="card-flat rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">4</span>
                <h2 class="text-base font-bold text-on-surface">Dokumen</h2>
            </div>
            <div class="grid grid-cols-3 gap-5">
                @foreach ([['name' => 'ktp_file', 'label' => 'Scan KTP', 'uploaded' => true], ['name' => 'npwp_file', 'label' => 'Scan NPWP', 'uploaded' => true], ['name' => 'bpjs_file', 'label' => 'Kartu BPJS', 'uploaded' => false]] as $doc)
                    <label class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition group
                                  {{ $doc['uploaded'] ? 'border-primary/30 bg-primary/5' : 'border-black/10 hover:border-primary/40 hover:bg-primary/5' }}">
                        <input type="file" name="{{ $doc['name'] }}" class="hidden">
                        @if ($doc['uploaded'])
                            <span class="material-symbols-outlined text-primary text-[32px]">task</span>
                            <p class="text-sm font-bold text-primary mt-2">{{ $doc['label'] }}</p>
                            <p class="text-[11px] text-on-surface-variant/40 mt-0.5">Terunggah · klik untuk ganti</p>
                        @else
                            <span class="material-symbols-outlined text-on-surface-variant/30 group-hover:text-primary text-[32px] transition">upload_file</span>
                            <p class="text-sm font-bold text-on-surface mt-2 group-hover:text-primary transition">{{ $doc['label'] }}</p>
                            <p class="text-[11px] text-on-surface-variant/40 mt-0.5">Belum diunggah · PDF/JPG maks 2MB</p>
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