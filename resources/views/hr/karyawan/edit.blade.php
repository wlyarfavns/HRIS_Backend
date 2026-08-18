@extends('layouts.hr')

@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan')
@section('page-desc', 'Perbarui data pribadi, pekerjaan, rekening bank, komponen gaji, dan dokumen karyawan.')

@section('content')


    <a href="{{ route('hr.employees.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500
              hover:text-[#0B3D2E] transition -mt-2 mb-6">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>


    <div class="bg-white rounded-md p-6 flex items-center gap-4 border border-gray-200">
        <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center text-xl font-medium text-[#0B3D2E] border border-gray-200">
            {{ strtoupper(substr($employee->full_name, 0, 1)) }}
        </div>
        <div class="flex-1">
            <p class="text-lg font-medium text-gray-800">{{ $employee->full_name }}</p>
            <p class="text-xs text-gray-500  mt-0.5">{{ $employee->employee_id }} · {{ $employee->department->name ?? 'Tanpa Departemen' }}</p>
        </div>
        <span class="text-[11px] font-medium px-3 py-1.5 rounded-lg {{ $employee->employment_status === 'PKWTT' ? 'bg-gray-50 text-[#0B3D2E]' : 'bg-gray-50 text-gray-700' }}">
            {{ $employee->employment_status }}
        </span>
    </div>


    <form method="POST" action="{{ route('hr.employees.updateWeb', $employee->id) }}" enctype="multipart/form-data" class="space-y-6 mt-6">
        @csrf
        @method('PUT')


        @if ($errors->any())
            <div class="rounded-md bg-gray-50 border border-gray-200 text-gray-700 text-sm p-4 mb-4">
                <p class="font-medium mb-1">Periksa kembali isian berikut:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">1</span>
                <h2 class="text-lg font-medium text-gray-800">Data Pribadi</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="{{ old('full_name', $employee->full_name) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">NIK (KTP)</label>
                    <input type="text" name="nik" maxlength="16" required value="{{ old('nik', $employee->nik) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Email</label>
                    <input type="email" name="email" required value="{{ old('email', $employee->email) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">No. Telepon</label>
                    <input type="text" name="phone" required value="{{ old('phone', $employee->phone) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $employee->npwp) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">No. BPJS Kesehatan / TK</label>
                    <input type="text" name="bpjs_number" value="{{ old('bpjs_number', $employee->bpjs_number) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">2</span>
                <h2 class="text-lg font-medium text-gray-800">Data Pekerjaan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">NIP</label>
                    <input type="text" value="{{ $employee->employee_id }}" disabled
                           class="w-full px-4 py-2.5 bg-gray-100 rounded-md text-sm border border-gray-200 text-gray-500  cursor-not-allowed">
                </div>

                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Departemen</label>
                    <div class="relative">
                        <select name="department_id" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>
                </div>

                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Posisi / Jabatan</label>
                    <div class="relative">
                        <select name="position_id" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            @foreach ($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>
                </div>


                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Atasan Langsung (Supervisor)</label>
                    <div class="relative">
                        <select name="supervisor_id"
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="">-- Tidak ada (Level Tertinggi) --</option>
                            @foreach ($supervisors as $spv)
                                <option value="{{ $spv->id }}" {{ old('supervisor_id', $employee->supervisor_id) == $spv->id ? 'selected' : '' }}>
                                    {{ $spv->name }} — Supervisor
                                </option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>
                </div>

                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Tanggal Bergabung</label>
                    <input type="date" name="join_date" required value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                </div>

                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Tipe Kontrak</label>
                    <div class="relative">
                        <select name="employment_status" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="PKWT" {{ old('employment_status', $employee->employment_status) === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak)</option>
                            <option value="PKWTT" {{ old('employment_status', $employee->employment_status) === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                            <option value="Probation" {{ old('employment_status', $employee->employment_status) === 'Probation' ? 'selected' : '' }}>Probation (3 Bulan)</option>
                            <option value="Internship" {{ old('employment_status', $employee->employment_status) === 'Internship' ? 'selected' : '' }}>Internship / Magang</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>
                </div>

                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Gaji Pokok (Rp)</label>
                    <input type="number" name="basic_salary" required value="{{ old('basic_salary', $employee->basic_salary) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">3</span>
                <h2 class="text-lg font-medium text-gray-800">Status Kepegawaian</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Status Saat Ini</label>
                    <div class="relative">
                        <select name="status" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="resigned" {{ old('status', $employee->status) === 'resigned' ? 'selected' : '' }}>Resigned (Keluar)</option>
                            <option value="pending" {{ old('status', $employee->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>
                </div>
                <div class="col-span-2 flex items-end">
                    <p class="text-xs text-gray-500">Mengubah status ke <span class="font-medium text-gray-700">Non-Aktif</span> atau <span class="font-medium text-gray-700">Resigned</span> akan menghentikan akses karyawan ke sistem presensi & payroll.</p>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center justify-between gap-3 mb-8 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">4</span>
                    <h2 class="text-lg font-medium text-gray-800">Data Rekening Bank</h2>
                </div>
            </div>
            <p class="text-xs text-gray-500 mb-6">
                Dipakai untuk transfer gaji & export mass transfer bank oleh Finance.
                Pilihan bank dibatasi agar konsisten dan tidak salah kelompok saat export CSV.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Nama Bank</label>
                    <div class="relative">
                        @php $currentBank = old('bank_name', $employee->bank_name); @endphp
                        <select name="bank_name"
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                            <option value="" {{ !$currentBank ? 'selected' : '' }}>-- Pilih Bank --</option>
                            <option value="BCA" {{ $currentBank === 'BCA' ? 'selected' : '' }}>BCA</option>
                            <option value="MANDIRI" {{ $currentBank === 'MANDIRI' ? 'selected' : '' }}>Mandiri</option>
                            <option value="BNI" {{ $currentBank === 'BNI' ? 'selected' : '' }}>BNI</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
</div>
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $employee->bank_account_holder) }}"
                           placeholder="Sesuai buku tabungan"
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
                <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">6</span>
                <h2 class="text-lg font-medium text-gray-800">Dokumen</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $documents = [
                        ['name' => 'ktp_file', 'label' => 'Scan KTP', 'uploaded' => !empty($employee->ktp_file_path)],
                        ['name' => 'npwp_file', 'label' => 'Scan NPWP', 'uploaded' => !empty($employee->npwp)],
                        ['name' => 'bpjs_file', 'label' => 'Kartu BPJS', 'uploaded' => !empty($employee->bpjs_number)]
                    ];
                @endphp
                @foreach ($documents as $doc)
                    <label class="border-2 border-dashed rounded-md p-8 text-center cursor-pointer transition group
                                  {{ $doc['uploaded'] ? 'border-gray-200 bg-gray-50' : 'border-gray-200 hover:border-[#0B3D2E] hover:bg-gray-50' }}">
                        <input type="file" name="{{ $doc['name'] }}" class="hidden">
                        @if ($doc['uploaded'])
                            <p class="text-sm font-medium text-[#0B3D2E] mt-2">{{ $doc['label'] }}</p>
                            <p class="text-[11px] text-[#0B3D2E]/70 mt-1">Terisi / Terunggah · klik untuk ganti</p>
                        @else
                            <p class="text-sm font-medium text-gray-700 mt-2 group-hover:text-[#0B3D2E] transition">{{ $doc['label'] }}</p>
                            <p class="text-[11px] text-gray-400 mt-1">Belum diisi · Klik untuk upload</p>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('hr.employees.index') }}"
               class="px-6 py-2.5 rounded-md text-sm font-medium text-gray-500
                      hover:bg-gray-100 hover:text-gray-800 transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-[#0B3D2E] text-white px-8 py-2.5 rounded-md text-sm font-medium hover:bg-[#043927] transition shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Perubahan
            </button>
        </div>
    </form>

@endsection
