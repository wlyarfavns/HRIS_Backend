@extends('layouts.admin')

@section('title', 'Struktur Organisasi')
@section('page-title', 'Struktur Organisasi')
@section('page-desc', 'Kelola departemen dan struktur organisasi perusahaan.')

@section('content')
<div class="space-y-6">


    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-md border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 p-4 rounded-md border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-700 p-4 rounded-md border border-rose-200">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="bg-white rounded-md border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Departemen Baru</h3>

        <form action="{{ route('admin.org-structure.store') }}" method="POST">
            @csrf
            <div class="max-w-2xl">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Pilih Departemen</label>
                <div class="flex gap-4 items-center mt-2">
                    <select name="name" id="name" required class="block w-full py-2.5 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-[#0B3D2E] focus:border-[#0B3D2E] sm:text-sm">
                        @if(empty($defaultDepartments))
                            <option value="" disabled selected>-- Semua departemen default sudah ditambahkan --</option>
                        @else
                            <option value="" disabled selected>-- Pilih Departemen --</option>
                            @foreach($defaultDepartments as $depName)
                                <option value="{{ $depName }}">{{ $depName }}</option>
                            @endforeach
                        @endif
                    </select>
                    <button type="submit" @if(empty($defaultDepartments)) disabled @endif class="shrink-0 bg-[#0B3D2E] hover:bg-[#065c3e] disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-md text-sm font-medium transition-colors">
                        Tambahkan
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Pilihan hanya memuat departemen yang belum ada. Departemen kustom dapat dibuat oleh role HR.</p>
            </div>
        </form>
    </div>


    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-semibold text-gray-800">Daftar Departemen Aktif</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="p-4">Kode</th>
                        <th class="p-4">Nama Departemen</th>
                        <th class="p-4 text-center">Total Anggota</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                @forelse($departments as $dep)
                    <tbody x-data="{ expanded: false }" class="divide-y divide-gray-100 border-b border-gray-100">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 align-middle">
                                <span class="bg-gray-100 text-gray-700 font-medium px-2 py-1 rounded text-xs">{{ $dep->code ?? '-' }}</span>
                            </td>
                            <td class="p-4 align-middle text-gray-800 font-medium">
                                {{ $dep->name }}
                            </td>
                            <td class="p-4 align-middle text-center">
                                <button type="button" @click="expanded = !expanded" class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors">
                                    {{ $dep->employees_count }} Orang
                                    <span class="material-symbols-outlined text-[14px]" x-text="expanded ? 'expand_less' : 'expand_more'"></span>
                                </button>
                            </td>
                            <td class="p-4 align-middle text-right">
                                <form action="{{ route('admin.org-structure.destroy', $dep->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:bg-rose-50 p-2 rounded-md transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr x-show="expanded" class="bg-gray-50/50">
                            <td colspan="4" class="px-4 pb-4 pt-2">
                                @if($dep->employees->isEmpty())
                                    <div class="text-center p-4 text-xs text-gray-500 border border-dashed border-gray-200 rounded-md bg-white">
                                        Belum ada anggota di departemen ini.
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($dep->employees as $emp)
                                        <div class="flex items-center gap-3 bg-white p-3 border border-gray-100 rounded-md shadow-sm">
                                            <div class="w-8 h-8 rounded-full bg-[#0B3D2E]/10 text-[#0B3D2E] flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($emp->full_name ?? 'U', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name ?? '-' }}</p>
                                                <p class="text-[11px] text-gray-500 truncate">{{ $emp->employee_id ?? '-' }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500">
                                    Belum ada departemen yang ditambahkan ke perusahaan ini.
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
            </table>
        </div>

        <div class="mt-4 px-6 pb-6">
            {{ $departments->links() }}
        </div>
    </div>

</div>
@endsection

