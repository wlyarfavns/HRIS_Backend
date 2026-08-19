@extends('layouts.admin')

@section('title', 'Edit Profil Perusahaan')
@section('page-title', 'Edit Profil Perusahaan')
@section('page-desc', 'Perbarui informasi data utama perusahaan Anda.')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.company.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
        <span class="material-symbols-outlined text-[18px] mr-1">arrow_back</span>
        Kembali ke Profil
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Bagian Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-md p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-lg bg-[#0B3D2E]/10 flex items-center justify-center text-[#0B3D2E]">
                    <span class="material-symbols-outlined">edit_document</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Data Perusahaan</h2>
                    <p class="text-sm text-gray-500">Ubah informasi nama dan lokasi perusahaan.</p>
                </div>
            </div>

            <form action="{{ route('admin.company.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#0B3D2E] focus:ring focus:ring-[#0B3D2E] focus:ring-opacity-20 text-sm px-4 py-2.5 border" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                            <input type="text" name="province" id="province" value="{{ old('province', $company->province) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#0B3D2E] focus:ring focus:ring-[#0B3D2E] focus:ring-opacity-20 text-sm px-4 py-2.5 border" required>
                            @error('province')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota / Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#0B3D2E] focus:ring focus:ring-[#0B3D2E] focus:ring-opacity-20 text-sm px-4 py-2.5 border" required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $company->postal_code) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#0B3D2E] focus:ring focus:ring-[#0B3D2E] focus:ring-opacity-20 text-sm px-4 py-2.5 border" required>
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('admin.company.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B3D2E]">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#0B3D2E] border border-transparent rounded-md hover:bg-[#065A3D] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B3D2E]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bagian Info Statistik -->
    <div class="lg:col-span-1 space-y-6">
        @foreach($stats as $stat)
        <div class="bg-white rounded-md p-5 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-gray-500 font-medium text-sm">{{ $stat['label'] }}</h3>
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500">
                    <span class="material-symbols-outlined text-[18px]">{{ $stat['icon'] }}</span>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-medium text-gray-800">{{ $stat['value'] }}</span>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection