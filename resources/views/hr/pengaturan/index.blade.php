@extends('layouts.hr')

@section('title', 'Pengaturan HR')
@section('page-title', 'Pengaturan')
@section('page-desc', 'Konfigurasi kebijakan operasional HR: cuti, lembur, presensi, dan jenis izin.')

@section('content')

    @if (session('success'))
        <div class="mb-6 p-4 rounded-md bg-gray-50 border border-gray-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-[#0B3D2E]">check_circle</span>
            <p class="text-sm font-medium text-gray-800">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-md bg-gray-50 border border-gray-200 flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-gray-700">error</span>
                <p class="text-sm font-medium text-gray-700">Terdapat Kesalahan Input!</p>
            </div>
            <ul class="text-xs text-gray-700 mt-1 ml-9 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.settings.updateMain') }}" class="space-y-6">
        @csrf
        @method('PUT')


        <div class="bg-white rounded-md p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E]">
                    <span class="material-symbols-outlined text-[20px]">event_available</span>
                </div>
                <div>
                    <h2 class="text-lg font-medium text-gray-800">Kebijakan Cuti</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Atur kuota dan batas hari cuti tahunan</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Kuota Cuti Tahunan Default</label>
                    <div class="relative">
                        <input type="number" name="default_quota" value="{{ $cutiTahunan ? $cutiTahunan->default_quota : 12 }}"
                               class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                      focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-400">hari/tahun</span>
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Min. Hari per Pengajuan</label>
                    <input type="number" name="min_days_per_request" value="{{ $cutiTahunan ? $cutiTahunan->min_days_per_request : 1 }}"
                           class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                  focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition shadow-sm">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Maks. Hari per Pengajuan</label>
                    <input type="number" name="max_days_per_request" value="{{ $cutiTahunan ? $cutiTahunan->max_days_per_request : 12 }}"
                           class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                  focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 focus:bg-white focus:outline-none transition shadow-sm">
                </div>
            </div>
            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100 bg-gray-50/50 -mx-8 px-8 -mb-8 pb-8 rounded-b-[24px]">
                <div>
                    <p class="text-sm font-medium text-gray-800">Carry-forward Kuota Akhir Tahun</p>
                    <p class="text-xs text-gray-500 mt-1">Sisa kuota cuti tahun berjalan otomatis dibawa ke tahun berikutnya.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer group">
                    <input type="checkbox" name="allow_carry_forward" class="sr-only peer" {{ ($cutiTahunan && $cutiTahunan->allow_carry_forward) ? 'checked' : '' }}>
                    <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0B3D2E] group-hover:shadow-sm transition"></div>
                </label>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700">
                    <span class="material-symbols-outlined text-[20px]">schedule</span>
                </div>
                <div>
                    <h2 class="text-lg font-medium text-gray-800">Kebijakan Lembur</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Rumus dan batasan lembur bulanan</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Rumus Perhitungan</label>
                    <input type="text" name="overtime_formula" value="{{ $company->overtime_formula ?? '1/173 × Gaji Pokok × Jam' }}"
                           class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                  focus:border-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:bg-white focus:outline-none transition shadow-sm">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Batas Maks. Jam Lembur / Bulan</label>
                    <div class="relative">
                        <input type="number" name="max_overtime_hours" value="{{ $company->max_overtime_hours }}"
                               class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                      focus:border-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:bg-white focus:outline-none transition shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-400">jam</span>
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-md p-8 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700">
                    <span class="material-symbols-outlined text-[20px]">fingerprint</span>
                </div>
                <div>
                    <h2 class="text-lg font-medium text-gray-800">Kebijakan Presensi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Aturan jam kerja standar dan toleransi keterlambatan</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Toleransi Keterlambatan</label>
                    <div class="relative">
                        <input type="number" name="late_tolerance_minutes" value="{{ $company->late_tolerance_minutes }}"
                               class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                      focus:border-gray-200 focus:ring-2 focus:ring-amber-500/20 focus:bg-white focus:outline-none transition shadow-sm">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-400">menit</span>
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Jam Kerja Standar (Datang)</label>
                    <input type="time" name="standard_in_time" value="{{ \Carbon\Carbon::parse($company->standard_in_time)->format('H:i') }}"
                           class="w-full px-4 py-3 bg-gray-50 rounded-md text-sm font-medium text-gray-800 border border-gray-200
                                  focus:border-gray-200 focus:ring-2 focus:ring-amber-500/20 focus:bg-white focus:outline-none transition shadow-sm cursor-pointer">
                </div>
                <div>
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide block mb-2">Hari Libur Nasional/Cuti Bersama</label>
                    <button type="button" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition shadow-sm flex items-center justify-between group">
                        Kelola hari libur
                        </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mb-8">
            <button type="reset" class="px-6 py-3 rounded-md text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                Batal
            </button>
            <button type="submit" class="bg-[#0B3D2E] text-white px-6 py-3 rounded-md text-sm font-medium hover:bg-[#043927] transition shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Pengaturan
            </button>
        </div>
    </form>


    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden mb-12">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Jenis Izin / Cuti</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola kategori cuti/izin beserta kebutuhan lampiran.</p>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-b border-gray-100">
            <form action="{{ route('hr.settings.leave-types.store') }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Nama Cuti/Izin</label>
                    <input type="text" name="name" required placeholder="Contoh: Cuti Menikah" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm">
                </div>
                <div class="w-48">
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Tipe</label>
                    <select name="is_quota_based" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm cursor-pointer">
                        <option value="1">Berkuota</option>
                        <option value="0">Tidak Berkuota</option>
                    </select>
                </div>
                <div class="w-36">
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Kuota (Hari)</label>
                    <input type="number" name="default_quota" value="0" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm">
                </div>
                <div class="w-40">
                    <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Wajib Lampiran</label>
                    <select name="requires_attachment" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm cursor-pointer">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <button type="submit" class="bg-[#0B3D2E] hover:bg-[#043927] text-white font-medium h-[42px] px-6 rounded-md text-sm transition shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                        <th class="px-8 py-4">Jenis</th>
                        <th class="px-8 py-4">Kuota / Ketentuan</th>
                        <th class="px-8 py-4">Wajib Lampiran</th>
                        <th class="px-8 py-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach ($leaveTypes as $lt)
                        <tr class="hover:bg-gray-50 transition group" id="row-{{ $lt->id }}">
                            <td class="px-8 py-4 font-medium text-gray-800">{{ $lt->name }}</td>
                            <td class="px-8 py-4 text-gray-600 text-sm">
                                @if($lt->is_quota_based)
                                    <span class="bg-gray-50 text-[#0B3D2E] font-medium px-3 py-1 rounded-md text-xs border border-gray-200">{{ $lt->default_quota }} hari/tahun</span>
                                @else
                                    <span class="text-gray-500 italic">Sesuai kebutuhan</span>
                                @endif
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-[11px] font-medium px-3 py-1 rounded-md uppercase tracking-wide {{ $lt->requires_attachment ? 'bg-gray-50 text-gray-700 border border-gray-200' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $lt->requires_attachment ? 'Wajib' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                <button type="button" onclick="document.getElementById('edit-form-{{ $lt->id }}').classList.toggle('hidden'); document.getElementById('row-{{ $lt->id }}').classList.toggle('bg-gray-50/30');" class="w-8 h-8 rounded-full text-gray-400 hover:text-[#0B3D2E] hover:bg-gray-50 transition inline-flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </td>
                        </tr>
                        <tr id="edit-form-{{ $lt->id }}" class="hidden bg-gray-50/20 border-b-2 border-gray-200">
                            <td colspan="4" class="px-8 py-6">
                                <form action="{{ route('hr.settings.leave-types.update', $lt->id) }}" method="POST" class="flex items-end gap-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex-1">
                                        <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Nama Cuti/Izin</label>
                                        <input type="text" name="name" value="{{ $lt->name }}" required class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm">
                                    </div>
                                    <div class="w-48">
                                        <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Tipe</label>
                                        <select name="is_quota_based" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm cursor-pointer">
                                            <option value="1" {{ $lt->is_quota_based ? 'selected' : '' }}>Berkuota</option>
                                            <option value="0" {{ !$lt->is_quota_based ? 'selected' : '' }}>Tidak Berkuota</option>
                                        </select>
                                    </div>
                                    <div class="w-36">
                                        <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Kuota (Hari)</label>
                                        <input type="number" name="default_quota" value="{{ $lt->default_quota }}" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm">
                                    </div>
                                    <div class="w-40">
                                        <label class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-2 block">Wajib Lampiran</label>
                                        <select name="requires_attachment" class="w-full bg-white border border-gray-200 rounded-md px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-2 focus:ring-[#0B3D2E]/20 transition shadow-sm cursor-pointer">
                                            <option value="0" {{ !$lt->requires_attachment ? 'selected' : '' }}>Tidak</option>
                                            <option value="1" {{ $lt->requires_attachment ? 'selected' : '' }}>Ya</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="bg-[#0B3D2E] hover:bg-[#043927] text-white font-medium h-[42px] px-6 rounded-md text-sm transition shadow-sm">
                                        Simpan
                                    </button>
                                    <button type="button" onclick="document.getElementById('edit-form-{{ $lt->id }}').classList.add('hidden'); document.getElementById('row-{{ $lt->id }}').classList.remove('bg-gray-50/30');" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium h-[42px] px-6 rounded-md text-sm transition shadow-sm">
                                        Batal
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

