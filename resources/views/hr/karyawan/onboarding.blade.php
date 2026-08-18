@extends('layouts.hr')

@section('title', 'Onboarding Karyawan Baru')
@section('page-title', 'Onboarding Karyawan Baru')
@section('page-desc', 'Master data karyawan baru, generate NIP otomatis, dan enkripsi dokumen PII.')

@section('content')


    @if (session('success_data'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-gray-900/40 flex items-center justify-center z-50 p-4 ">

            <div x-show="show" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                @click.outside="show = false" class="bg-white rounded-md p-8 max-w-sm w-full text-center shadow-sm border border-gray-100">

                <div class="w-16 h-16 rounded-full bg-gray-50 text-[#0B3D2E] flex items-center justify-center mx-auto mb-4">
                    </div>

                <h3 class="text-lg font-medium text-gray-800 mb-2">Karyawan Berhasil Ditambahkan</h3>
                <p class="text-sm text-gray-500 mb-4">
                    <strong>{{ session('success_data')['name'] }}</strong> telah terdaftar dan akunnya sudah aktif.
                </p>

                <div class="bg-gray-50 rounded-md p-4 mb-2 text-left space-y-3 border border-gray-100">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-medium tracking-wide mb-0.5">
                            NIP (Username Login)
                        </p>
                        <div class="flex items-center justify-between gap-2" x-data="{ copied: false }">
                            <p class=" font-semibold text-[#0B3D2E] text-xl">
                                {{ session('success_data')['nip'] }}
                            </p>
                            <button type="button"
                                @click="navigator.clipboard.writeText('{{ session('success_data')['nip'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="w-8 h-8 rounded-lg flex items-center justify-center cursor-pointer
                                                               bg-gray-50 hover:bg-[#0B3D2E] hover:text-white text-[#0B3D2E] transition"
                                :title="copied ? 'Tersalin!' : 'Salin NIP'">
                                <span class="material-symbols-outlined text-[16px]"
                                    x-text="copied ? 'check' : 'content_copy'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-200">
                        <p class="text-[10px] text-gray-400 uppercase font-medium tracking-wide mb-0.5">
                            Password Awal
                        </p>
                        <p class=" font-semibold text-gray-800 text-xl">
                            {{ session('success_data')['nip'] }}
                        </p>
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 mb-6">
                    Sampaikan NIP ini ke karyawan untuk login pertama kali. Password wajib diganti saat aktivasi akun.
                </p>

                <button @click="show = false" class="w-full py-3 rounded-md bg-[#0B3D2E] text-white text-sm font-medium
                                                   hover:bg-[#043927] transition cursor-pointer">
                    Mengerti
                </button>
            </div>
        </div>
    @endif


    <div x-data="{
                        contractType: 'PKWT',
                        autoNip: '{{ $predictedNip }}',
                        expiryDate: '',
                        h30Reminder: true,
                        confirmOpen: false
                    }">


        <a href="{{ route('hr.employees.index') }}"
            class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-[#0B3D2E] transition -mt-2 mb-2">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Karyawan
        </a>


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


        <div class="bg-white rounded-md p-5 mb-6 border border-gray-200 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <span class="w-10 h-10 rounded-md bg-gray-50 text-gray-500 flex items-center justify-center font-medium">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                </span>
                <div>
                    <p class="text-sm font-medium text-gray-800">Onboarding Karyawan Baru</p>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                        Generate NIP otomatis · Tracking masa berlaku kontrak · Enkripsi PII
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-medium px-2.5 py-1 rounded bg-gray-100 text-gray-600 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">lock</span> PII Encrypted
                </span>
                <span
                    class="text-[10px] font-medium px-2.5 py-1 rounded bg-gray-50 text-gray-700 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">notifications_active</span> Auto H-30 Alert
                </span>
            </div>
        </div>


        <div
            class="rounded-md bg-gray-50 border border-gray-200 text-gray-700 text-xs p-4 mb-6 flex items-start gap-3">
            <span class="material-symbols-outlined text-[18px] text-gray-700 mt-0.5">info</span>
            <p class="leading-relaxed">NIP yang di-generate otomatis akan menjadi <strong>username sekaligus password awal</strong> untuk login
                karyawan. Karyawan wajib mengganti password saat
                login pertama kali.</p>
        </div>


        <form x-ref="formOnboarding" method="POST" action="{{ route('hr.employees.storeWeb') }}"
            enctype="multipart/form-data" class="space-y-6">
            @csrf


            <div class="bg-white rounded-md p-8 border border-gray-200">
                <div class="flex items-center justify-between gap-3 mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">1</span>
                        <div>
                            <h2 class="text-lg font-medium text-gray-800">Data Identitas Pribadi</h2>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Nama Lengkap
                        </label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}"
                            placeholder="Sesuai KTP" class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition">
                    </div>
                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            NIK (KTP) — 16 Digit
                        </label>
                        <input type="text" name="nik" maxlength="16" required value="{{ old('nik') }}"
                            placeholder="3171xxxxxxxxxxxx" class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                    </div>
                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="081234567890"
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                    </div>
                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            NPWP (15/16 Digit)
                        </label>
                        <input type="text" name="npwp" value="{{ old('npwp') }}" placeholder="Opsional jika ada" 
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                    </div>
                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            No. BPJS Kesehatan/TK
                        </label>
                        <input type="text" name="bpjs_number" value="{{ old('bpjs_number') }}"
                            placeholder="Opsional" class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-md p-8 border border-gray-200">
                <div class="flex items-center justify-between gap-3 mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">2</span>
                        <div>
                            <h2 class="text-lg font-medium text-gray-800">Data Penempatan &amp; Kontrak</h2>
                        </div>
                    </div>


                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-md px-4 py-3"
                        x-data="{ copied: false }">
                        <div class="text-right">
                            <p class="text-[9px] font-medium uppercase tracking-wider text-gray-400 mb-0.5">
                                NIP &amp; Password Awal
                            </p>
                            <p class=" font-semibold text-xl text-[#0B3D2E] leading-none" x-text="autoNip">
                                {{ $predictedNip }}
                            </p>
                        </div>
                        <button type="button"
                            @click="navigator.clipboard.writeText(autoNip); copied = true; setTimeout(() => copied = false, 2000)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition cursor-pointer
                                               bg-white border border-gray-200 hover:bg-[#0B3D2E] hover:text-white hover:border-[#0B3D2E] text-gray-500"
                            :title="copied ? 'Tersalin!' : 'Salin NIP'">
                            <span class="material-symbols-outlined text-[16px]"
                                x-text="copied ? 'check' : 'content_copy'"></span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Departemen
                        </label>
                        <div class="relative">
                            <select name="department_id" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>Pilih Departemen
                                </option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Posisi / Jabatan
                        </label>
                        <div class="relative">
                            <select name="position_id" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="" disabled {{ old('position_id') ? '' : 'selected' }}>Pilih Posisi</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                        {{ $pos->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Atasan Langsung (Supervisor)
                        </label>
                        <div class="relative">
                            <select name="supervisor_id"
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="" {{ old('supervisor_id') ? '' : 'selected' }}>-- Tidak ada (Level Tertinggi) --</option>
                                @foreach ($supervisors as $spv)
                                    <option value="{{ $spv->id }}" {{ old('supervisor_id') == $spv->id ? 'selected' : '' }}>
                                        {{ $spv->name }} — Supervisor
                                    </option>
                                @endforeach
                            </select>
                            </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Tanggal Bergabung (Join Date)
                        </label>
                        <input type="date" name="join_date" required value="{{ old('join_date', now()->format('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Tipe Kontrak Kerja
                        </label>
                        <div class="relative">
                            <select name="contract_type" x-model="contractType" required
                                class="appearance-none w-full pl-4 pr-10 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                                <option value="PKWT">PKWT (Kontrak Waktu Tertentu)</option>
                                <option value="PKWTT">PKWTT (Karyawan Tetap)</option>
                                <option value="Probation">Probation (3 Bulan)</option>
                                <option value="Internship">Internship / Magang</option>
                            </select>
                            </div>
                    </div>

                    <div x-show="contractType !== 'PKWTT'">
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Tanggal Berakhir Kontrak
                        </label>
                        <input type="date" name="contract_end_date" x-model="expiryDate" class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition cursor-pointer">
                    </div>

                    <div>
                        <label class="text-[11px] font-medium text-gray-500 uppercase tracking-wide block mb-1.5">
                            Gaji Pokok Kesepakatan
                        </label>
                        <input type="number" name="basic_salary" required value="{{ old('basic_salary') }}"
                            placeholder="Contoh: 6500000" class="w-full px-4 py-2.5 bg-gray-50 rounded-md text-sm border border-gray-200 text-gray-800 placeholder-gray-400 hover:border-gray-300 focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition ">
                    </div>
                </div>


                <div x-show="contractType !== 'PKWTT'" class="mt-6 p-4 rounded-md bg-gray-50 border border-gray-200 text-gray-700
                                       flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[18px] text-gray-700">alarm</span>
                        <span>Sistem akan mengirimkan notifikasi otomatis ke HR &amp; Atasan pada
                            <strong class="">H-30</strong> sebelum kontrak berakhir.</span>
                    </div>
                    <span class="font-medium text-gray-700 bg-gray-50 px-2 py-1 rounded">Reminder Aktif</span>
                </div>
            </div>


            <div class="bg-white rounded-md p-8 border border-gray-200">
                <div class="flex items-center justify-between gap-3 mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">3</span>
                        <div>
                            <h2 class="text-lg font-medium text-gray-800">Upload Dokumen Pendukung</h2>
                        </div>
                    </div>
                    <span class="text-[11px] text-gray-400">Maks. 5MB (PDF/JPG/PNG)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div x-data="{ fileName: '' }">
                        <label class="border border-dashed border-gray-300 rounded-md p-6 text-center
                                                  hover:border-[#0B3D2E] hover:bg-gray-50 transition cursor-pointer
                                                  flex flex-col items-center justify-center h-full">
                            <p class="font-medium text-sm text-gray-700" x-text="fileName || 'KTP Asli / e-KTP'"></p>
                            <p class="text-xs text-gray-400 mt-1">Klik untuk pilih file</p>
                            <input type="file" name="ktp_file" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                @change="fileName = $event.target.files[0]?.name ?? ''">
                        </label>
                    </div>

                    <div x-data="{ fileName: '' }">
                        <label class="border border-dashed border-gray-300 rounded-md p-6 text-center
                                                  hover:border-[#0B3D2E] hover:bg-gray-50 transition cursor-pointer
                                                  flex flex-col items-center justify-center h-full">
                            <p class="font-medium text-sm text-gray-700" x-text="fileName || 'Kartu NPWP'"></p>
                            <p class="text-xs text-gray-400 mt-1">Klik untuk pilih file</p>
                            <input type="file" name="npwp_file" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                @change="fileName = $event.target.files[0]?.name ?? ''">
                        </label>
                    </div>

                    <div x-data="{ fileName: '' }">
                        <label class="border border-dashed border-gray-300 rounded-md p-6 text-center
                                                  hover:border-[#0B3D2E] hover:bg-gray-50 transition cursor-pointer
                                                  flex flex-col items-center justify-center h-full">
                            <p class="font-medium text-sm text-gray-700" x-text="fileName || 'Kartu BPJS'"></p>
                            <p class="text-xs text-gray-400 mt-1">Klik untuk pilih file</p>
                            <input type="file" name="bpjs_file" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                                @change="fileName = $event.target.files[0]?.name ?? ''">
                        </label>
                    </div>
                </div>
            </div>


            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('hr.employees.index') }}" class="px-6 py-2.5 rounded-md text-sm font-medium
                                       text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition cursor-pointer">
                    Batal
                </a>


                <button type="button" @click="
                    if ($refs.formOnboarding.checkValidity()) {
                        confirmOpen = true
                    } else {
                        $refs.formOnboarding.reportValidity()
                    }
                " class="px-8 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium
                       hover:bg-[#043927] shadow-sm flex items-center gap-2 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    Simpan &amp; Terbitkan NIP Karyawan
                </button>
            </div>

        </form>


        <div x-show="confirmOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 flex items-center justify-center z-50 p-4 ">

            <div x-show="confirmOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" @click.outside="confirmOpen = false"
                class="bg-white rounded-md p-8 max-w-sm w-full text-center shadow-sm border border-gray-100">


                <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-700 border border-gray-200
                                        flex items-center justify-center mx-auto mb-4">
                    </div>

                <h3 class="text-lg font-medium text-gray-800 mb-2">Konfirmasi Penyimpanan</h3>
                <p class="text-sm text-gray-600 mb-1">
                    Apakah Anda yakin ingin menyimpan data karyawan ini?
                </p>
                <p class="text-xs text-gray-500 mb-2 mt-2">
                    NIP <span class=" font-medium text-[#0B3D2E]" x-text="autoNip"></span>
                    akan diterbitkan dan akun karyawan langsung aktif.
                </p>


                <div class="flex gap-3 mt-6">
                    <button type="button" @click="confirmOpen = false"
                        class="flex-1 py-2.5 rounded-md bg-gray-100 text-sm font-medium
                                           text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="confirmOpen = false; $nextTick(() => $refs.formOnboarding.submit())"
                        class="flex-1 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium
                                           hover:bg-[#043927] shadow-sm transition cursor-pointer flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>

    </div>

@endsection
