@extends('layouts.hr')

@section('title', 'Dokumen Karyawan')
@section('page-title', 'Dokumen Karyawan')
@section('page-desc', 'Lihat status dan riwayat dokumen kepegawaian.')

@php 
    $emp = \App\Models\Employee::with('department')->where('company_id', auth()->user()->company_id)
        ->where(function ($q) use ($id) {
            $q->where('employee_id', $id)->orWhere('id', $id);
        })->firstOrFail();
 
    $employee = [ 
        'id' => $emp->id,
        'nip' => $emp->employee_id, 
        'full_name' => $emp->full_name, 
        'department' => $emp->department ? $emp->department?->name : '-', 
        'contract_type' => $emp->contract_type ?? 'PKWT', 
        'avatar' => $emp->id % 70, 
    ]; 
 
    $documents = [ 
        ['label' => 'Scan KTP', 'icon' => 'badge', 'uploaded' => !empty($emp->ktp_file_path), 'file' => basename($emp->ktp_file_path ?? ''), 'url' => $emp->ktp_file_path ? asset('storage/' . $emp->ktp_file_path) : '#', 'size' => '-', 'date' => '-'], 
        ['label' => 'Scan NPWP', 'icon' => 'receipt_long', 'uploaded' => !empty($emp->npwp_file_path), 'file' => basename($emp->npwp_file_path ?? ''), 'url' => $emp->npwp_file_path ? asset('storage/' . $emp->npwp_file_path) : '#', 'size' => '-', 'date' => '-'], 
        ['label' => 'Kartu BPJS', 'icon' => 'health_and_safety', 'uploaded' => !empty($emp->bpjs_file_path), 'file' => basename($emp->bpjs_file_path ?? ''), 'url' => $emp->bpjs_file_path ? asset('storage/' . $emp->bpjs_file_path) : '#', 'size' => '-', 'date' => '-'], 
        ['label' => 'Ijazah Terakhir', 'icon' => 'school', 'uploaded' => false, 'file' => null, 'url' => '#', 'size' => null, 'date' => null], 
        ['label' => 'Kontrak Kerja', 'icon' => 'contract_edit', 'uploaded' => false, 'file' => null, 'url' => '#', 'size' => null, 'date' => null], 
        ['label' => 'CV / Resume', 'icon' => 'description', 'uploaded' => false, 'file' => null, 'url' => '#', 'size' => null, 'date' => null], 
    ];

    $uploadedCount = collect($documents)->where('uploaded', true)->count();

    $history = [
        ['file' => 'ktp_jim_halpert.pdf', 'type' => 'Scan KTP', 'size' => '412 KB', 'date' => '18 Agu 2024, 09:02', 'by' => 'Jim Halpert'],
        ['file' => 'npwp_jim_halpert.pdf', 'type' => 'Scan NPWP', 'size' => '298 KB', 'date' => '18 Agu 2024, 09:05', 'by' => 'Jim Halpert'],
    ];
@endphp

@section('content')


    <a href="{{ route('hr.employees.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500
              hover:text-[#0B3D2E] transition -mt-2 mb-6">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Kembali ke Daftar Karyawan
    </a>


    <div class="bg-white rounded-md p-6 flex items-center gap-4 border border-gray-200">
        <img src="https://i.pravatar.cc/56?img={{ $employee['avatar'] }}" class="w-14 h-14 rounded-full object-cover border border-gray-200" alt="{{ $employee['full_name'] }}">
        <div class="flex-1">
            <p class="text-lg font-medium text-gray-800">{{ $employee['full_name'] }}</p>
            <p class="text-xs text-gray-500  mt-0.5">{{ $employee['nip'] }} &bull; {{ $employee['department'] }}</p>
        </div>
        <span class="text-[11px] font-medium px-3 py-1.5 rounded-lg {{ $employee['contract_type'] === 'PKWTT' ? 'bg-gray-50 text-[#0B3D2E]' : 'bg-gray-50 text-gray-700' }}">
            {{ $employee['contract_type'] }}
        </span>
        <a href="{{ route('hr.employees.edit', $employee['id']) }}"
           class="text-xs font-medium text-gray-600 hover:text-[#0B3D2E] border border-gray-200 hover:border-[#0B3D2E] bg-gray-50 hover:bg-gray-50 rounded-md px-4 py-2.5 transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">edit</span>
            Kelola di Edit Karyawan
        </a>
    </div>


    <div class="bg-white rounded-md p-6 flex items-center gap-4 border border-gray-200 mt-6">
        <div class="w-10 h-10 rounded-md bg-gray-50 text-gray-500 flex items-center justify-center shrink-0 border border-gray-100">
            <span class="material-symbols-outlined text-[20px]">folder_open</span>
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-800">{{ $uploadedCount }} dari {{ count($documents) }} dokumen terunggah</p>
            <p class="text-xs text-gray-500 mt-1">Untuk mengunggah atau mengganti berkas, buka halaman Edit Karyawan.</p>
        </div>
        <div class="w-32 h-2 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full bg-[#0B3D2E] rounded-full" style="width: {{ round($uploadedCount / count($documents) * 100) }}%"></div>
        </div>
    </div>


    <div class="bg-white rounded-md p-8 border border-gray-200 mt-6">
        <div class="flex items-center gap-3 mb-8 pb-4 border-b border-gray-100">
            <span class="w-8 h-8 rounded-full bg-gray-50 text-gray-500 flex items-center justify-center font-medium text-sm">1</span>
            <div>
                <h2 class="text-lg font-medium text-gray-800">Dokumen Kepegawaian</h2>
                <p class="text-xs text-gray-500 mt-1">Klik "Lihat" untuk membuka berkas yang sudah diunggah.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($documents as $doc)
                <div class="border-2 rounded-md p-6 text-center transition
                            {{ $doc['uploaded'] ? 'border-gray-200 bg-gray-50' : 'border-dashed border-gray-200 bg-gray-50' }}">

                    @if ($doc['uploaded'])
                        <p class="text-sm font-medium text-gray-800 mt-3">{{ $doc['label'] }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 truncate">{{ $doc['file'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $doc['size'] }} · diunggah {{ $doc['date'] }}</p>

                        <div class="flex items-center justify-center gap-2 mt-4 pt-4 border-t border-gray-200"> 
                            <a href="{{ $doc['url'] }}" target="_blank" title="Lihat" 
                                    class="text-[11px] font-medium text-[#0B3D2E] flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-[#0B3D2E] hover:text-white transition"> 
                                <span class="material-symbols-outlined text-[16px]">visibility</span> 
                                Lihat 
                            </a> 
                            <a href="{{ $doc['url'] }}" download="{{ $doc['file'] }}" title="Unduh" 
                                    class="text-[11px] font-medium text-gray-500 flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition"> 
                                <span class="material-symbols-outlined text-[16px]">download</span> 
                                Unduh 
                            </a> 
                        </div>
                    @else
                        <p class="text-sm font-medium text-gray-500 mt-3">{{ $doc['label'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">Belum diunggah</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>


    <div class="bg-white rounded-md overflow-hidden border border-gray-200 mt-6">
        <div class="px-8 py-5 flex items-center gap-3 border-b border-gray-100 bg-gray-50/50">
            <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-medium text-sm">2</span>
            <h2 class="text-base font-medium text-gray-800">Riwayat Unggahan</h2>
        </div>
        <div class="px-8 py-12 text-center"> 
            <span class="material-symbols-outlined text-gray-300 text-5xl mb-3 block">history_toggle_off</span> 
            <p class="text-sm text-gray-500">Belum ada riwayat unggahan dokumen saat ini.</p> 
        </div>
    </div>

@endsection
