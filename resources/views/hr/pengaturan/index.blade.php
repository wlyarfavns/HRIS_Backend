@extends('layouts.hr')

@section('title', 'Pengaturan HR')
@section('page-title', 'Pengaturan')
@section('page-desc', 'Konfigurasi kebijakan operasional HR: cuti, lembur, presensi, dan jenis izin.')



@section('content')

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-error/10 border border-error flex flex-col gap-1">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-error">error</span>
                <p class="text-sm font-bold text-error">Terdapat Kesalahan Input!</p>
            </div>
            <ul class="text-xs text-error mt-1 ml-9 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.settings.updateMain') }}" class="space-y-6">
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
                        <input type="number" name="default_quota" value="{{ $cutiTahunan ? $cutiTahunan->default_quota : 12 }}"
                               class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                      hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant/40">hari/tahun</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Min. Hari per Pengajuan</label>
                    <input type="number" name="min_days_per_request" value="{{ $cutiTahunan ? $cutiTahunan->min_days_per_request : 1 }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Maks. Hari per Pengajuan</label>
                    <input type="number" name="max_days_per_request" value="{{ $cutiTahunan ? $cutiTahunan->max_days_per_request : 12 }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
            </div>
            <div class="flex items-center justify-between mt-5 pt-5 border-t border-black/5">
                <div>
                    <p class="text-sm font-bold text-on-surface">Carry-forward Kuota Akhir Tahun</p>
                    <p class="text-xs text-on-surface-variant/50 mt-0.5">Sisa kuota cuti tahun berjalan otomatis dibawa ke tahun berikutnya.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="allow_carry_forward" class="sr-only peer" {{ ($cutiTahunan && $cutiTahunan->allow_carry_forward) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
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
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Rumus Perhitungan</label>
                    <input type="text" name="overtime_formula" value="{{ $company->overtime_formula ?? '1/173 × Gaji Pokok × Jam' }}"
                           class="mt-1.5 w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                  hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Batas Maks. Jam Lembur / Bulan</label>
                    <input type="number" name="max_overtime_hours" value="{{ $company->max_overtime_hours }}"
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
                        <input type="number" name="late_tolerance_minutes" value="{{ $company->late_tolerance_minutes }}"
                               class="w-full px-3.5 py-2.5 bg-surface-container rounded-lg text-sm font-mono-data border border-transparent
                                      hover:border-primary/20 focus:border-primary/40 focus:ring-2 focus:ring-primary/20 focus:bg-white focus:outline-none transition">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant/40">menit</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Jam Kerja Standar (Datang)</label>
                    <input type="time" name="standard_in_time" value="{{ \Carbon\Carbon::parse($company->standard_in_time)->format('H:i') }}"
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

        <div class="flex justify-end gap-3 mb-6">
            <button type="reset" class="px-5 py-2.5 rounded-lg text-sm font-bold text-on-surface-variant/70 hover:bg-primary/5 hover:text-primary transition">
                Batal
            </button>
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Simpan Pengaturan
            </button>
        </div>
    </form>

    {{-- JENIS CUTI / IZIN (Di luar form utama karena punya logic CRUD terpisah) --}}
    <div class="card-flat rounded-2xl overflow-hidden mt-6 mb-10">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-on-surface">Jenis Izin / Cuti</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Kelola kategori cuti/izin beserta kebutuhan lampiran.</p>
            </div>
        </div>
        
        <div class="p-4 bg-surface-container/30 border-b border-black/5">
            <form action="{{ route('hr.settings.leave-types.store') }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Nama Cuti/Izin</label>
                    <input type="text" name="name" required placeholder="Contoh: Cuti Menikah" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                </div>
                <div class="w-40">
                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Tipe</label>
                    <select name="is_quota_based" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                        <option value="1">Berkuota</option>
                        <option value="0">Tidak Berkuota</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Kuota (Hari)</label>
                    <input type="number" name="default_quota" value="0" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                </div>
                <div class="w-32">
                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Wajib Lampiran</label>
                    <select name="requires_attachment" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <button type="submit" class="bg-primary hover:brightness-110 text-white font-bold h-[38px] px-5 rounded-lg text-sm transition">
                    Tambah
                </button>
            </form>
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
                    <tr class="hover:bg-primary/5 transition group" id="row-{{ $lt->id }}">
                        <td class="px-6 py-3.5 font-bold text-on-surface">{{ $lt->name }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70 text-xs">
                            {{ $lt->is_quota_based ? $lt->default_quota . ' hari/tahun' : 'Sesuai kebutuhan' }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded {{ $lt->requires_attachment ? 'bg-amber-500/10 text-amber-700' : 'bg-surface-container text-on-surface-variant/50' }}">
                                {{ $lt->requires_attachment ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center flex justify-center gap-2">
                            <button type="button" onclick="document.getElementById('edit-form-{{ $lt->id }}').classList.toggle('hidden'); document.getElementById('row-{{ $lt->id }}').classList.toggle('bg-primary/5');" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                        </td>
                    </tr>
                    <tr id="edit-form-{{ $lt->id }}" class="hidden bg-primary/5 border-b border-black/5">
                        <td colspan="4" class="px-6 py-4">
                            <form action="{{ route('hr.settings.leave-types.update', $lt->id) }}" method="POST" class="flex items-end gap-4">
                                @csrf
                                @method('PUT')
                                <div class="flex-1">
                                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Nama Cuti/Izin</label>
                                    <input type="text" name="name" value="{{ $lt->name }}" required class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                </div>
                                <div class="w-40">
                                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Tipe</label>
                                    <select name="is_quota_based" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                        <option value="1" {{ $lt->is_quota_based ? 'selected' : '' }}>Berkuota</option>
                                        <option value="0" {{ !$lt->is_quota_based ? 'selected' : '' }}>Tidak Berkuota</option>
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Kuota (Hari)</label>
                                    <input type="number" name="default_quota" value="{{ $lt->default_quota }}" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                </div>
                                <div class="w-32">
                                    <label class="text-xs font-bold text-on-surface-variant/70 mb-1 block">Wajib Lampiran</label>
                                    <select name="requires_attachment" class="w-full bg-white border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                        <option value="0" {{ !$lt->requires_attachment ? 'selected' : '' }}>Tidak</option>
                                        <option value="1" {{ $lt->requires_attachment ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <button type="submit" class="bg-primary hover:brightness-110 text-white font-bold h-[38px] px-4 rounded-lg text-sm transition">
                                    Simpan
                                </button>
                                <button type="button" onclick="document.getElementById('edit-form-{{ $lt->id }}').classList.add('hidden'); document.getElementById('row-{{ $lt->id }}').classList.remove('bg-primary/5');" class="bg-surface-container hover:bg-black/10 text-on-surface font-bold h-[38px] px-4 rounded-lg text-sm transition">
                                    Batal
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
