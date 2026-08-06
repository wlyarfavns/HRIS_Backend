@extends('layouts.hr')

@section('title', 'Dokumen Karyawan')
@section('page-title', 'Dokumen Karyawan')
@section('page-desc', 'Lihat status dan riwayat dokumen kepegawaian.')

@php
    // Dummy data — nantinya diganti hasil query berdasarkan $id dari route
    $employee = [
        'nip' => 'EMP-00812',
        'full_name' => 'Jim Halpert',
        'department' => 'Sales',
        'contract_type' => 'PKWT',
        'avatar' => 12,
    ];

    $documents = [
        ['label' => 'Scan KTP', 'icon' => 'badge', 'uploaded' => true, 'file' => 'ktp_jim_halpert.pdf', 'size' => '412 KB', 'date' => '18 Agu 2024'],
        ['label' => 'Scan NPWP', 'icon' => 'receipt_long', 'uploaded' => true, 'file' => 'npwp_jim_halpert.pdf', 'size' => '298 KB', 'date' => '18 Agu 2024'],
        ['label' => 'Kartu BPJS', 'icon' => 'health_and_safety', 'uploaded' => false, 'file' => null, 'size' => null, 'date' => null],
        ['label' => 'Ijazah Terakhir', 'icon' => 'school', 'uploaded' => true, 'file' => 'ijazah_jim_halpert.pdf', 'size' => '1.1 MB', 'date' => '19 Agu 2024'],
        ['label' => 'Kontrak Kerja', 'icon' => 'contract_edit', 'uploaded' => true, 'file' => 'kontrak_pkwt_jim.pdf', 'size' => '540 KB', 'date' => '20 Agu 2024'],
        ['label' => 'CV / Resume', 'icon' => 'description', 'uploaded' => false, 'file' => null, 'size' => null, 'date' => null],
    ];

    $uploadedCount = collect($documents)->where('uploaded', true)->count();

    $history = [
        ['file' => 'kontrak_pkwt_jim.pdf', 'type' => 'Kontrak Kerja', 'size' => '540 KB', 'date' => '20 Agu 2024, 10:15', 'by' => 'HR Admin'],
        ['file' => 'ijazah_jim_halpert.pdf', 'type' => 'Ijazah Terakhir', 'size' => '1.1 MB', 'date' => '19 Agu 2024, 14:32', 'by' => 'HR Admin'],
        ['file' => 'ktp_jim_halpert.pdf', 'type' => 'Scan KTP', 'size' => '412 KB', 'date' => '18 Agu 2024, 09:02', 'by' => 'Jim Halpert'],
        ['file' => 'npwp_jim_halpert.pdf', 'type' => 'Scan NPWP', 'size' => '298 KB', 'date' => '18 Agu 2024, 09:05', 'by' => 'Jim Halpert'],
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
        <a href="{{ route('hr.employees.edit', $employee['nip']) }}"
           class="text-xs font-bold text-on-surface-variant/60 hover:text-primary border border-black/10 hover:border-primary/30 rounded-lg px-3.5 py-2 transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">edit</span>
            Kelola di Edit Karyawan
        </a>
    </div>

    {{-- STATUS KELENGKAPAN --}}
    <div class="card-flat rounded-2xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-primary text-[20px]">folder_open</span>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-on-surface">{{ $uploadedCount }} dari {{ count($documents) }} dokumen terunggah</p>
            <p class="text-xs text-on-surface-variant/50 mt-0.5">Untuk mengunggah atau mengganti berkas, buka halaman Edit Karyawan.</p>
        </div>
        <div class="w-32 h-2 rounded-full bg-surface-container overflow-hidden">
            <div class="h-full bg-primary rounded-full" style="width: {{ round($uploadedCount / count($documents) * 100) }}%"></div>
        </div>
    </div>

    {{-- DOKUMEN (VIEW ONLY) --}}
    <div class="card-flat rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
            <div>
                <h2 class="text-base font-bold text-on-surface">Dokumen Kepegawaian</h2>
                <p class="text-xs text-on-surface-variant/50 mt-0.5">Klik "Lihat" untuk membuka berkas yang sudah diunggah.</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-5">
            @foreach ($documents as $doc)
                <div class="border-2 rounded-xl p-6 text-center transition
                            {{ $doc['uploaded'] ? 'border-primary/30 bg-primary/5' : 'border-dashed border-black/10' }}">

                    @if ($doc['uploaded'])
                        <span class="material-symbols-outlined text-primary text-[32px]">{{ $doc['icon'] }}</span>
                        <p class="text-sm font-bold text-primary mt-2">{{ $doc['label'] }}</p>
                        <p class="text-[11px] text-on-surface-variant/50 mt-0.5 truncate">{{ $doc['file'] }}</p>
                        <p class="text-[11px] text-on-surface-variant/40 mt-0.5">{{ $doc['size'] }} · diunggah {{ $doc['date'] }}</p>

                        <div class="flex items-center justify-center gap-1 mt-3 pt-3 border-t border-primary/10">
                            <button type="button" title="Lihat"
                                    class="text-[11px] font-bold text-primary flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-primary/10 transition">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                Lihat
                            </button>
                            <button type="button" title="Unduh"
                                    class="text-[11px] font-bold text-on-surface-variant/60 flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-primary/10 hover:text-primary transition">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                Unduh
                            </button>
                        </div>
                    @else
                        <span class="material-symbols-outlined text-on-surface-variant/30 text-[32px]">{{ $doc['icon'] }}</span>
                        <p class="text-sm font-bold text-on-surface-variant/50 mt-2">{{ $doc['label'] }}</p>
                        <p class="text-[11px] text-on-surface-variant/40 mt-0.5">Belum diunggah</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- RIWAYAT UNGGAHAN --}}
    <div class="card-flat rounded-2xl overflow-hidden">
        <div class="px-6 py-5 flex items-center gap-3 border-b border-black/5">
            <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
            <h2 class="text-base font-bold text-on-surface">Riwayat Unggahan</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container text-left text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest">
                    <th class="px-6 py-3.5">Nama Berkas</th>
                    <th class="px-6 py-3.5">Jenis Dokumen</th>
                    <th class="px-6 py-3.5">Ukuran</th>
                    <th class="px-6 py-3.5">Tanggal Unggah</th>
                    <th class="px-6 py-3.5">Diunggah Oleh</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @foreach ($history as $h)
                    <tr class="hover:bg-primary/5 transition">
                        <td class="px-6 py-3.5 font-semibold text-on-surface">{{ $h['file'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70">{{ $h['type'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $h['size'] }}</td>
                        <td class="px-6 py-3.5 font-mono-data text-on-surface-variant/70">{{ $h['date'] }}</td>
                        <td class="px-6 py-3.5 text-on-surface-variant/70">{{ $h['by'] }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" title="Lihat" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <button type="button" title="Unduh" class="p-1.5 rounded-lg text-on-surface-variant/50 hover:text-primary hover:bg-primary/10 transition">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection